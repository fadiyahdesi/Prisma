<?php

namespace App\Services;

use App\Models\PpmHki;
use App\Models\PpmKlaimReward;
use App\Models\PpmPublikasiJurnal;
use App\Models\PpmRewardDistribusi;
use App\Models\RefTarifRewardSk;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RewardDistributionService
{
    /**
     * Get SK Rektor incentive rate matrix.
     */
    public function getTarif(string $kategori, string $subKategori): ?RefTarifRewardSk
    {
        return RefTarifRewardSk::where('kategori', $kategori)
            ->where('sub_kategori', $subKategori)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Submit a reward claim with multi-author distribution and anti-duplicate check.
     */
    public function submitClaim(User $user, array $data): PpmKlaimReward
    {
        return DB::transaction(function () use ($user, $data) {
            $jenisKlaim = $data['jenis_klaim'];
            $publikasi = null;
            $hki = null;
            $kategoriInsentif = $data['kategori_insentif'];

            // 1. Anti-Duplicate Claim Check & Asset Validation
            if ($jenisKlaim === 'Publikasi') {
                $publikasi = PpmPublikasiJurnal::findOrFail($data['id_publikasi']);

                if ($publikasi->is_claimed_reward) {
                    throw ValidationException::withMessages([
                        'id_publikasi' => ["Artikel publikasi dengan DOI '{$publikasi->doi}' sudah pernah diklaim insentifnya sebelumnya."],
                    ]);
                }
            } elseif ($jenisKlaim === 'HKI') {
                $hki = PpmHki::findOrFail($data['id_hki']);

                if ($hki->status_hki !== 'Terverifikasi HKI') {
                    throw ValidationException::withMessages([
                        'id_hki' => ["Sertifikat HKI belum terverifikasi oleh Sentra HKI UHN (Status: {$hki->status_hki})."],
                    ]);
                }

                if ($hki->is_claimed_reward) {
                    throw ValidationException::withMessages([
                        'id_hki' => ["Sertifikat HKI dengan nomor permohonan '{$hki->nomor_permohonan}' sudah pernah diklaim insentifnya sebelumnya."],
                    ]);
                }
            } else {
                throw ValidationException::withMessages([
                    'jenis_klaim' => ['Jenis klaim tidak valid.'],
                ]);
            }

            // 2. Fetch Rate from SK Rektor Matrix
            $tarifRecord = $this->getTarif($jenisKlaim, $kategoriInsentif);
            $tarifNominal = $tarifRecord ? (float) $tarifRecord->nominal_insentif : (float) ($data['total_reward'] ?? 0);

            if ($tarifNominal <= 0) {
                throw ValidationException::withMessages([
                    'kategori_insentif' => ['Tarif insentif untuk kategori ini tidak ditemukan atau bernilai 0.'],
                ]);
            }

            // 3. Strict 100% Multi-Author Distribution Validation
            $distribusiList = $data['distribusi'] ?? [];
            if (empty($distribusiList) || !is_array($distribusiList)) {
                throw ValidationException::withMessages([
                    'distribusi' => ['Daftar distribusi anggota penerima reward wajib diisi minimal 1 orang.'],
                ]);
            }

            $totalPersen = 0.0;
            foreach ($distribusiList as $item) {
                $p = (float) ($item['persentase'] ?? 0);
                if ($p <= 0) {
                    throw ValidationException::withMessages([
                        'distribusi' => ['Persentase pembagian setiap anggota harus lebih besar dari 0%.'],
                    ]);
                }
                $totalPersen += $p;
            }

            // Check if total is exactly 100% (allowing small float tolerance)
            if (abs($totalPersen - 100.0) > 0.01) {
                throw ValidationException::withMessages([
                    'distribusi' => ["Total persentase pembagian insentif multi-penulis harus tepat 100.00%. Total saat ini: {$totalPersen}%"],
                ]);
            }

            // 4. Generate Nomor Klaim
            $year = date('Y');
            $month = date('m');
            $randomCode = strtoupper(substr(uniqid(), -5));
            $nomorKlaim = "REW/{$year}/{$month}/{$randomCode}";

            // 5. Create Claim
            $klaim = PpmKlaimReward::create([
                'nomor_klaim' => $nomorKlaim,
                'user_id' => $user->id,
                'jenis_klaim' => $jenisKlaim,
                'id_publikasi' => $publikasi?->id,
                'id_hki' => $hki?->id,
                'kategori_insentif' => $kategoriInsentif,
                'tarif_dasar_sk' => $tarifNominal,
                'total_reward' => $tarifNominal,
                'file_surat_pernyataan' => $data['file_surat_pernyataan'] ?? null,
                'status_klaim' => 'Submitted',
            ]);

            // 6. Create Distribution Rows
            foreach ($distribusiList as $item) {
                $persen = (float) $item['persentase'];
                $nominalBagian = round(($persen / 100.0) * $tarifNominal, 2);

                PpmRewardDistribusi::create([
                    'id_klaim_reward' => $klaim->id,
                    'user_id' => !empty($item['user_id']) ? $item['user_id'] : null,
                    'nama_penulis' => $item['nama_penulis'],
                    'nidn_nim' => $item['nidn_nim'] ?? null,
                    'email' => $item['email'] ?? null,
                    'peran_penulis' => $item['peran_penulis'] ?? 'Penulis Pertama',
                    'persentase' => $persen,
                    'nominal_bagian' => $nominalBagian,
                    'nama_bank' => $item['nama_bank'],
                    'nomor_rekening' => $item['nomor_rekening'],
                    'nama_pemilik_rekening' => $item['nama_pemilik_rekening'],
                    'status_transfer' => 'Pending',
                ]);
            }

            // 7. Lock Asset (Anti-Duplicate Claim)
            if ($publikasi) {
                $publikasi->update(['is_claimed_reward' => true]);
            }
            if ($hki) {
                $hki->update(['is_claimed_reward' => true]);
            }

            // 8. Audit Log
            AuditLogService::log('claim_reward_submitted', null, [
                'nomor_klaim' => $nomorKlaim,
                'jenis_klaim' => $jenisKlaim,
                'total_reward' => $tarifNominal,
                'user_id' => $user->id,
            ], $user->id);

            return $klaim;
        });
    }

    /**
     * Approve claim by P3M Admin.
     */
    public function approveClaim(PpmKlaimReward $klaim, User $approver, ?string $catatan = null): PpmKlaimReward
    {
        $klaim->update([
            'status_klaim' => 'Approved_P3M',
            'approved_by_p3m' => $approver->id,
            'approved_at' => now(),
            'catatan_p3m' => $catatan,
        ]);

        AuditLogService::log('claim_reward_approved', null, [
            'nomor_klaim' => $klaim->nomor_klaim,
            'approver' => $approver->id,
        ], $approver->id);

        return $klaim;
    }

    /**
     * Reject claim by P3M Admin and unlock asset.
     */
    public function rejectClaim(PpmKlaimReward $klaim, User $reviewer, string $catatan): PpmKlaimReward
    {
        DB::transaction(function () use ($klaim, $reviewer, $catatan) {
            $klaim->update([
                'status_klaim' => 'Rejected',
                'catatan_p3m' => $catatan,
            ]);

            // Release anti-duplicate lock so user can correct and re-apply
            if ($klaim->publikasi) {
                $klaim->publikasi->update(['is_claimed_reward' => false]);
            }
            if ($klaim->hki) {
                $klaim->hki->update(['is_claimed_reward' => false]);
            }

            AuditLogService::log('claim_reward_rejected', null, [
                'nomor_klaim' => $klaim->nomor_klaim,
                'reviewer' => $reviewer->id,
                'catatan' => $catatan,
            ], $reviewer->id);
        });

        return $klaim;
    }

    /**
     * Disburse an individual distribution line by Keuangan.
     */
    public function disburseMember(PpmRewardDistribusi $distribusi, array $disbursementData): PpmRewardDistribusi
    {
        return DB::transaction(function () use ($distribusi, $disbursementData) {
            $distribusi->update([
                'status_transfer' => 'Disbursed',
                'tanggal_transfer' => $disbursementData['tanggal_transfer'] ?? now()->toDateString(),
                'nomor_referensi' => $disbursementData['nomor_referensi'] ?? null,
                'file_bukti_transfer' => $disbursementData['file_bukti_transfer'] ?? null,
            ]);

            // Check if all members of this claim are disbursed
            $klaim = $distribusi->klaimReward;
            $allDisbursed = $klaim->distribusi()->where('status_transfer', '!=', 'Disbursed')->count() === 0;

            if ($allDisbursed) {
                $klaim->update(['status_klaim' => 'Disbursed']);
            }

            AuditLogService::log('reward_distribution_disbursed', null, [
                'id_distribusi' => $distribusi->id,
                'nomor_klaim' => $klaim->nomor_klaim,
                'penerima' => $distribusi->nama_penulis,
                'nominal' => $distribusi->nominal_bagian,
            ]);

            return $distribusi;
        });
    }
}


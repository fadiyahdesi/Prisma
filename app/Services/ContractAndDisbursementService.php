<?php

namespace App\Services;

use App\Models\PpmKontrak;
use App\Models\PpmPencairanDana;
use App\Models\PpmUsulan;
use App\Notifications\GrantWinnerNotification;
use App\Notifications\DisbursementTermin1Notification;
use App\Services\AuditLogService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ContractAndDisbursementService
{
    /**
     * Mass assign winners, generate SK & SPK numbers, create contracts (US-09.1).
     */
    public static function penetapanPemenang(array $usulanIds, int $kepalaId): array
    {
        if (empty($usulanIds)) {
            throw new InvalidArgumentException('Pilih minimal satu usulan untuk ditetapkan sebagai pemenang.');
        }

        return DB::transaction(function () use ($usulanIds, $kepalaId) {
            $year = date('Y');
            $existingCount = PpmKontrak::whereYear('tanggal_sk', $year)->count();
            $skNumber = sprintf('%03d/SK-PEMENANG/P3M-UHN/%s', $existingCount + 1, $year);

            $contracts = [];

            foreach ($usulanIds as $idx => $id) {
                $usulan = PpmUsulan::with(['pengusul', 'skema'])->lockForUpdate()->findOrFail($id);

                // Ensure proposal is eligible to win
                if (!in_array($usulan->status, ['Reviewed', 'Approved', 'Submitted'], true)) {
                    continue;
                }

                $contractNumber = sprintf('%03d/SPK-BIMA/P3M-UHN/%s', $existingCount + $idx + 1, $year);
                $pagu = (float) $usulan->total_rab;
                $termin1 = round($pagu * 0.70, 2);
                $termin2 = round($pagu * 0.30, 2);
                $token = bin2hex(random_bytes(32));

                $kontrak = PpmKontrak::updateOrCreate(
                    ['id_usulan' => $usulan->id],
                    [
                        'nomor_sk' => $skNumber,
                        'nomor_kontrak' => $contractNumber,
                        'tanggal_sk' => now(),
                        'tanggal_kontrak' => now(),
                        'pagu_disetujui' => $pagu,
                        'dana_termin_1' => $termin1,
                        'dana_termin_2' => $termin2,
                        'signed_by_kepala' => true,
                        'signed_by_kepala_at' => now(),
                        'signed_by_pengusul' => false,
                        'signed_by_pengusul_at' => null,
                        'verification_token' => $token,
                        'status' => 'pending_signature',
                    ]
                );

                // Update usulan status to Contracted
                $usulan->update(['status' => 'Contracted']);

                // Send in-app notification for proposer
                if ($usulan->pengusul) {
                    $usulan->pengusul->notify(new GrantWinnerNotification($usulan, $kontrak));
                }

                AuditLogService::log('WINNER_ESTABLISHED', null, [
                    'usulan_id' => $usulan->id,
                    'kontrak_id' => $kontrak->id,
                    'nomor_sk' => $skNumber,
                    'nomor_kontrak' => $contractNumber,
                    'pagu_disetujui' => $pagu,
                ], $kepalaId);

                $contracts[] = $kontrak;
            }

            return $contracts;
        });
    }

    /**
     * Digital Signature by Dosen Pengusul on SPK (US-09.2).
     */
    public static function signContractByPengusul(PpmKontrak $kontrak, int $userId): PpmKontrak
    {
        $kontrak->loadMissing('usulan');

        if ($kontrak->usulan->id_pengusul !== $userId) {
            throw new InvalidArgumentException('Akses ditolak: Anda bukan ketua pengusul dari kontrak ini.');
        }

        if ($kontrak->signed_by_pengusul) {
            return $kontrak;
        }

        $now = now();
        $rawPayload = $kontrak->nomor_kontrak . '|' . $kontrak->verification_token . '|' . $userId . '|' . $now->toIso8601String();
        $docHash = hash('sha256', $rawPayload);

        $kontrak->update([
            'signed_by_pengusul' => true,
            'signed_by_pengusul_at' => $now,
            'document_hash' => $docHash,
            'status' => 'signed',
        ]);

        AuditLogService::log('SPK_DIGITALLY_SIGNED', null, [
            'kontrak_id' => $kontrak->id,
            'nomor_kontrak' => $kontrak->nomor_kontrak,
            'document_hash' => $docHash,
            'signed_at' => $now,
        ], $userId);

        return $kontrak;
    }

    /**
     * Proposer uploads active bank passbook & account details (US-09.3).
     */
    public static function updateRekeningPengusul(PpmKontrak $kontrak, int $userId, array $data, $fileBukuTabungan = null): PpmKontrak
    {
        $kontrak->loadMissing('usulan');

        if ($kontrak->usulan->id_pengusul !== $userId) {
            throw new InvalidArgumentException('Akses ditolak: Anda bukan ketua pengusul usulan ini.');
        }

        $updateData = [
            'nama_bank' => trim($data['nama_bank']),
            'nomor_rekening' => trim($data['nomor_rekening']),
            'nama_pemilik_rekening' => trim($data['nama_pemilik_rekening']),
            'rekening_verified_by' => null,
            'rekening_verified_at' => null,
        ];

        if ($fileBukuTabungan) {
            $path = $fileBukuTabungan->store('passbooks/' . $kontrak->id, 'public');
            $updateData['file_buku_tabungan'] = $path;
        }

        $kontrak->update($updateData);

        AuditLogService::log('BANK_ACCOUNT_UPDATED', null, [
            'kontrak_id' => $kontrak->id,
            'nama_bank' => $updateData['nama_bank'],
            'nomor_rekening' => $updateData['nomor_rekening'],
        ], $userId);

        return $kontrak;
    }

    /**
     * Divisi Keuangan verifies proposer bank passbook (US-09.3).
     */
    public static function verifyRekening(PpmKontrak $kontrak, int $keuanganId): PpmKontrak
    {
        if (empty($kontrak->nomor_rekening) || empty($kontrak->nama_bank)) {
            throw new InvalidArgumentException('Data rekening bank belum lengkap.');
        }

        $kontrak->update([
            'rekening_verified_by' => $keuanganId,
            'rekening_verified_at' => now(),
        ]);

        AuditLogService::log('BANK_ACCOUNT_VERIFIED', null, [
            'kontrak_id' => $kontrak->id,
            'verified_by' => $keuanganId,
            'nomor_rekening' => $kontrak->nomor_rekening,
        ], $keuanganId);

        return $kontrak;
    }

    /**
     * Divisi Keuangan executes Termin I (70%) disbursement (US-09.3).
     */
    public static function disburseTermin1(
        PpmKontrak $kontrak,
        array $data,
        $fileBuktiTransfer,
        int $keuanganId
    ): PpmPencairanDana {
        $kontrak->loadMissing('usulan');

        if (!$kontrak->isFullySigned()) {
            throw new InvalidArgumentException('Pencairan belum dapat diproses: Surat Perjanjian Kontrak (SPK) belum ditandatangani lengkap oleh kedua belah pihak.');
        }

        if (!$kontrak->hasVerifiedRekening()) {
            throw new InvalidArgumentException('Pencairan belum dapat diproses: Rekening bank belum divalidasi oleh Divisi Keuangan.');
        }

        if ($kontrak->isTermin1Disbursed()) {
            throw new InvalidArgumentException('Pencairan dana Termin I (70%) untuk usulan ini telah diproses sebelumnya.');
        }

        return DB::transaction(function () use ($kontrak, $data, $fileBuktiTransfer, $keuanganId) {
            $buktiPath = null;
            if ($fileBuktiTransfer) {
                $buktiPath = $fileBuktiTransfer->store('transfers/' . $kontrak->id, 'public');
            }

            $pencairan = PpmPencairanDana::create([
                'id_kontrak' => $kontrak->id,
                'termin' => 1,
                'persentase' => 70.00,
                'jumlah_dana' => $kontrak->dana_termin_1,
                'nomor_referensi' => trim($data['nomor_referensi']),
                'tanggal_transfer' => $data['tanggal_transfer'] ?? now(),
                'file_bukti_transfer' => $buktiPath,
                'status_pencairan' => 'transferred',
                'processed_by' => $keuanganId,
                'processed_at' => now(),
                'catatan' => $data['catatan'] ?? null,
            ]);

            // Update contract and proposal status to Ongoing
            $kontrak->update(['status' => 'active']);
            $kontrak->usulan->update(['status' => 'Ongoing']);

            // Notify proposer
            if ($kontrak->usulan && $kontrak->usulan->pengusul) {
                $kontrak->usulan->pengusul->notify(new DisbursementTermin1Notification($kontrak, $pencairan));
            }

            AuditLogService::log('DISBURSEMENT_TERMIN_1_TRANSFERRED', null, [
                'kontrak_id' => $kontrak->id,
                'pencairan_id' => $pencairan->id,
                'jumlah_dana' => $kontrak->dana_termin_1,
                'nomor_referensi' => $pencairan->nomor_referensi,
                'processed_by' => $keuanganId,
            ], $keuanganId);

            return $pencairan;
        });
    }

    /**
     * Generate inline QR Code SVG string for PDF or Web embedding (US-09.2).
     */
    public static function generateQrCodeSvg(string $content, int $size = 120): string
    {
        if (class_exists('SimpleSoftwareIO\QrCode\Facades\QrCode')) {
            return QrCode::size($size)->generate($content);
        }

        // Fallback placeholder SVG if package is still loading
        return '<svg width="' . $size . '" height="' . $size . '" xmlns="http://www.w3.org/2000/svg"><rect width="100%" height="100%" fill="#eee"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-size="10">QR Code</text></svg>';
    }

    /**
     * Generate official SPK PDF stream with embedded QR code (US-09.2).
     */
    public static function generateSpkPdf(PpmKontrak $kontrak)
    {
        $kontrak->loadMissing(['usulan.pengusul.fakultas', 'usulan.pengusul.prodi', 'usulan.skema', 'usulan.periode']);
        $verificationUrl = route('spk.verify', $kontrak->verification_token);
        $qrCodeSvg = self::generateQrCodeSvg($verificationUrl, 110);

        $pdf = Pdf::loadView('admin.kontrak.pdf-spk', compact('kontrak', 'verificationUrl', 'qrCodeSvg'))
                  ->setPaper('a4', 'portrait');

        return $pdf;
    }
}


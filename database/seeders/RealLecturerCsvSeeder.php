<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\RefProgramStudi;
use App\Models\RefFakultas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RealLecturerCsvSeeder extends Seeder
{
    private function cleanBaseName(string $name): string
    {
        $s = trim($name);
        $s = preg_replace('/^(Prof\.?\s*|Dr\.?\s*|Ir\.?\s*|apt\.?\s*|Bdn\.?\s*|Ns\.?\s*)+/i', '', $s);
        $parts = explode(',', $s);
        $base = trim($parts[0]);
        $base = preg_replace('/\b(S\.Pd|S\.T|S\.Kom|S\.ST\.Par|M\.Par|M\.T|M\.M|M\.Kom|SE|M\.Si|S\.E)\b.*$/i', '', $base);
        return preg_replace('/[^a-zA-Z0-9]+/', '', strtolower($base));
    }

    public function run(): void
    {
        $csv146Path = database_path('data/dosen_tetap_uhn.csv');
        if (!file_exists($csv146Path)) {
            $this->command->error("File CSV 146 dosen tetap tidak ditemukan di: {$csv146Path}");
            return;
        }

        // 1. Load optional SINTA enrichment map from dosen_real.csv
        $sintaByNidn = [];
        $sintaByCleanName = [];
        $sintaCsvPath = database_path('data/dosen_real.csv');
        if (file_exists($sintaCsvPath) && ($sh = fopen($sintaCsvPath, 'r')) !== false) {
            fgetcsv($sh); // header
            while (($srow = fgetcsv($sh)) !== false) {
                if (empty($srow) || count($srow) < 4) {
                    continue;
                }
                $sintaId = trim($srow[1] ?? '');
                $sNidn = trim($srow[2] ?? '');
                $sNama = trim($srow[3] ?? '');
                $sOverall = (float) ($srow[12] ?? $srow[10] ?? 0);
                $s3Yr = (float) ($srow[13] ?? $srow[11] ?? 0);
                $entry = [
                    'sinta_id' => $sintaId ?: null,
                    'sinta_score_overall' => $sOverall,
                    'sinta_score_3yr' => $s3Yr,
                ];
                if ($sNidn !== '') {
                    $sintaByNidn[$sNidn] = $entry;
                    $sintaByNidn[ltrim($sNidn, '0')] = $entry;
                }
                if ($sNama !== '') {
                    $sintaByCleanName[$this->cleanBaseName($sNama)] = $entry;
                }
            }
            fclose($sh);
        }

        // 2. Ensure Roles exist
        $dosenRole = Role::firstOrCreate(
            ['name' => 'Dosen / Pengusul'],
            ['guard_name' => 'web', 'description' => 'Dosen Peneliti & Pengabdi Universitas Harkat Negeri']
        );
        $kaprodiRole = Role::firstOrCreate(
            ['name' => 'Kaprodi'],
            ['guard_name' => 'web', 'description' => 'Ketua Program Studi Universitas Harkat Negeri']
        );
        $kplRole = Role::firstOrCreate(
            ['name' => 'Kepala P3M'],
            ['guard_name' => 'web', 'description' => 'Kepala P3M Universitas Harkat Negeri']
        );

        // 3. Ensure all 23 Prodis exist
        $vokasi = RefFakultas::where('nama_fakultas', 'like', '%Vokasi%')->first() ?? RefFakultas::first();
        if ($vokasi) {
            RefProgramStudi::updateOrCreate(
                ['kode_prodi' => 'SKT-D3'],
                ['id_fakultas' => $vokasi->id_fakultas, 'nama_prodi' => 'D-3 Kebidanan', 'jenjang' => 'D3']
            );
            RefProgramStudi::updateOrCreate(
                ['kode_prodi' => 'PBD-PRF'],
                ['id_fakultas' => $vokasi->id_fakultas, 'nama_prodi' => 'Pendidikan Profesi Bidan', 'jenjang' => 'D4']
            );
        }

        $allProdis = RefProgramStudi::with('fakultas')->get();

        $prodiMap = [
            'D4 Teknik Informatika' => 'TI-D4',
            'D4 Akuntansi Sektor Publik' => 'ASP-D4',
            'D4 Kebidanan' => 'KBD-D4',
            'Profesi Kebidanan' => 'PBD-PRF',
            'D3 Kebidanan' => 'SKT-D3',
            'D3 Farmasi' => 'FRM-D3',
            'D3 Keperawatan' => 'KPR-D3',
            'D3 Teknik Komputer' => 'TK-D3',
            'D3 Teknik Elektronika' => 'TE-D3',
            'D3 Teknik Mesin' => 'TM-D3',
            'D3 Akuntansi' => 'AKT-D3',
            'D3 Perhotelan' => 'PHT-D3',
            'D3 Desain Komunikasi Visual' => 'DKV-D3',
            'S1 Akuntansi' => 'AKT-S1',
            'S1 Manajemen' => 'MNJ-S1',
            'S1 Ilmu Hukum' => 'HKM-S1',
            'S1 Ilmu Komunikasi' => 'ILKOM-S1',
            'S1 Data Sains' => 'SD-S1',
            'S1 Teknik Mesin' => 'TM-S1',
            'S1 Sistem Informasi' => 'SI-S1',
            'S1 Teknik Informatika' => 'TI-S1',
            'S1 Psikologi' => 'PSI-S1',
            'S1 Pendidikan Guru Sekolah Dasar' => 'PGSD-S1',
        ];

        $handle = fopen($csv146Path, 'r');
        if ($handle === false) {
            return;
        }

        $keptUserIds = [];
        $importedCount = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $no = trim($row[1] ?? '');
            if (!is_numeric($no)) {
                continue;
            }

            $nama = trim($row[5] ?? '');
            $nipy = trim($row[6] ?? '');
            $nidnRaw = trim(preg_replace('/\s+/', ' ', $row[7] ?? ''));
            $jabatan = trim($row[8] ?? '') ?: 'Dosen';
            $jafungRaw = trim($row[9] ?? '') ?: 'Tenaga Pengajar';
            $prodiRaw = trim($row[10] ?? '');

            if ($nama === '') {
                continue;
            }

            $cleanName = $this->cleanBaseName($nama);

            // Extract primary NIDN/NUPTK (before '/') for login/matching
            $nidnParts = array_map('trim', explode('/', $nidnRaw));
            $primaryNidn = $nidnParts[0] ?? '';
            $altNidn = $nidnParts[1] ?? '';

            // SINTA enrichment
            $sinta = ($primaryNidn !== '' ? ($sintaByNidn[$primaryNidn] ?? $sintaByNidn['0' . $primaryNidn] ?? $sintaByNidn[ltrim($primaryNidn, '0')] ?? null) : null)
                ?? $sintaByCleanName[$cleanName]
                ?? ['sinta_id' => null, 'sinta_score_overall' => 0, 'sinta_score_3yr' => 0];

            // Resolve Prodi
            $kodeProdi = $prodiMap[$prodiRaw] ?? null;
            $prodiModel = $kodeProdi
                ? $allProdis->firstWhere('kode_prodi', $kodeProdi)
                : $allProdis->first(fn ($p) => Str::contains(strtolower($p->nama_prodi), strtolower($prodiRaw)));

            $fakultasId = $prodiModel ? $prodiModel->id_fakultas : null;
            $prodiId = $prodiModel ? $prodiModel->id_prodi : null;

            // Find existing unclaimed user in DB
            $user = null;
            if ($nidnRaw !== '') {
                $user = User::whereNotIn('id', $keptUserIds)
                    ->where(function ($q) use ($nidnRaw, $primaryNidn, $altNidn) {
                        $q->where('nidn_nim', $nidnRaw)
                          ->orWhere('nidn_nim', $primaryNidn)
                          ->orWhere('nidn_nim', '0' . $primaryNidn)
                          ->orWhere('nidn_nim', ltrim($primaryNidn, '0'));
                        if ($altNidn !== '') {
                            $q->orWhere('nidn_nim', $altNidn);
                        }
                    })
                    ->first();
            }
            if (!$user) {
                $candidates = User::whereNotIn('id', $keptUserIds)
                    ->where(function ($q) {
                        $q->whereNull('email')
                          ->orWhereNotIn('email', ['superadmin@harkatnegeri.ac.id', 'adminp3m@harkatnegeri.ac.id']);
                    })
                    ->get();
                foreach ($candidates as $cand) {
                    $cClean = $this->cleanBaseName($cand->name);
                    if ($cClean === $cleanName || levenshtein($cClean, $cleanName) <= 2) {
                        $user = $cand;
                        break;
                    }
                }
            }

            // Preserve user's real email only if they manually set a custom non-dummy email; otherwise null (as SDM sheet has no email)
            $existingEmail = $user?->email;
            $isGeneratedEmail = empty($existingEmail)
                || str_starts_with($existingEmail, 'dosen.')
                || str_starts_with($existingEmail, 'temp.')
                || $existingEmail === 'kepalap3m@harkatnegeri.ac.id';
            $emailToSave = $isGeneratedEmail ? null : $existingEmail;

            $defaultPasswordPlain = $primaryNidn !== '' ? $primaryNidn : ($nipy !== '' ? $nipy : 'password123');

            $userData = [
                'name' => $nama,
                'nipy' => $nipy !== '' ? $nipy : null,
                'nidn_nim' => $nidnRaw !== '' ? $nidnRaw : null,
                'jabatan' => $jabatan,
                'jabatan_fungsional' => $jafungRaw,
                'email' => $emailToSave,
                'sinta_id' => $sinta['sinta_id'] ?? ($user->sinta_id ?? null),
                'id_fakultas' => $fakultasId,
                'id_prodi' => $prodiId,
                'sinta_score_overall' => $sinta['sinta_score_overall'] ?: ($user->sinta_score_overall ?? 0),
                'sinta_score_3yr' => $sinta['sinta_score_3yr'] ?: ($user->sinta_score_3yr ?? 0),
                'is_otp_verified' => true,
                'email_verified_at' => now(),
                'password' => $user ? $user->password : Hash::make($defaultPasswordPlain),
            ];

            if ($user) {
                $user->update($userData);
            } else {
                $user = User::create($userData);
            }

            $keptUserIds[] = $user->id;

            // Assign roles
            $roleIds = [$dosenRole->id];
            if (Str::contains(strtolower($jabatan), 'ketua program studi')) {
                $roleIds[] = $kaprodiRole->id;
            }
            if ($primaryNidn === '0617029201') {
                $roleIds[] = $kplRole->id;
            }
            $user->roles()->sync($roleIds);

            $importedCount++;
        }

        fclose($handle);

        // 4. Delete any lecturer users not in the 146 official list (keep superadmin@ and adminp3m@)
        $extraIds = User::whereNotIn('id', $keptUserIds)
            ->where(function ($q) {
                $q->whereNull('email')
                  ->orWhereNotIn('email', ['superadmin@harkatnegeri.ac.id', 'adminp3m@harkatnegeri.ac.id']);
            })
            ->pluck('id')
            ->all();

        if (!empty($extraIds)) {
            DB::transaction(function () use ($extraIds) {
                $usulanIds = DB::table('ppm_usulan')->whereIn('id_pengusul', $extraIds)->pluck('id')->all();
                DB::table('ppm_usulan_anggota')->whereIn('user_id', $extraIds)->delete();
                if (!empty($usulanIds)) {
                    DB::table('ppm_usulan')->whereIn('id', $usulanIds)->delete();
                }
                DB::table('users')->whereIn('id', $extraIds)->delete();
            });
            $this->command->info('Menghapus ' . count($extraIds) . ' user dosen yang tidak ada di daftar 146 Dosen Tetap UHN.');
        }

        $this->command->info("Berhasil menyelaraskan {$importedCount} data Dosen Tetap UHN sesuai SK SDM.");
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\RefProgramStudi;
use App\Models\RefFakultas;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RealLecturerCsvSeeder extends Seeder
{
    public function run(): void
    {
        $csvPath = database_path('data/dosen_real.csv');
        if (!file_exists($csvPath)) {
            $this->command->error("File CSV tidak ditemukan di: {$csvPath}");
            return;
        }

        $dosenRole = Role::firstOrCreate(
            ['name' => 'Dosen / Pengusul'],
            ['guard_name' => 'web', 'description' => 'Dosen Peneliti & Pengabdi Universitas Harkat Negeri']
        );

        $allProdis = RefProgramStudi::with('fakultas')->get();

        $handle = fopen($csvPath, 'r');
        if ($handle === false) {
            return;
        }

        // Header row
        $header = fgetcsv($handle);
        $importedCount = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (empty($row) || count($row) < 5) {
                continue;
            }

            // CSV Columns:
            // 0: NO, 1: SINTAID, 2: NIDN, 3: NAMA, 4: AFILIASI, 5: PRODI, 6: PENDIDIKAN,
            // 7: JABATAN FUNGSIONAL, 8: GELAR DEPAN, 9: GELAR BELAKANG,
            // 10: SINTA OVERALL v2, 11: SINTA 3Yr v2, 12: SINTA OVERALL v3, 13: SINTA 3Yr v3,
            // 14: STATUS AKTIF, 15: STATUS VERIFIKASI

            $sintaId = trim($row[1] ?? '');
            $nidn = trim($row[2] ?? '');
            $nama = trim($row[3] ?? '');
            $prodiRaw = trim($row[5] ?? '');
            $jafung = trim($row[7] ?? '') ?: 'Tenaga Pengajar';
            $gelarDepan = trim($row[8] ?? '');
            $gelarBelakang = trim($row[9] ?? '');
            $sintaOverall = (float) ($row[12] ?? $row[10] ?? 0);
            $sinta3Yr = (float) ($row[13] ?? $row[11] ?? 0);

            if (empty($nama)) {
                continue;
            }

            // Resolve Prodi & Fakultas
            $prodiModel = null;
            if (!empty($prodiRaw)) {
                $normalizedProdi = str_replace(['D4 ', 'D3 ', 'S1 '], ['D-4 ', 'D-3 ', 'S-1 '], $prodiRaw);
                $prodiModel = $allProdis->first(function ($p) use ($normalizedProdi, $prodiRaw) {
                    return strcasecmp($p->nama_prodi, $normalizedProdi) === 0
                        || strcasecmp($p->nama_prodi, $prodiRaw) === 0
                        || Str::contains(strtolower($p->nama_prodi), strtolower($prodiRaw))
                        || Str::contains(strtolower($normalizedProdi), strtolower($p->nama_prodi));
                });
            }

            $fakultasId = $prodiModel ? $prodiModel->id_fakultas : null;
            $prodiId = $prodiModel ? $prodiModel->id_prodi : null;

            // Generate clean email from name / NIDN
            $slugName = Str::slug(Str::words($nama, 2, ''));
            $email = !empty($nidn) ? "dosen.{$nidn}@harkatnegeri.ac.id" : "{$slugName}@harkatnegeri.ac.id";
            if ($nidn === '0617029201') {
                $email = 'kepalap3m@harkatnegeri.ac.id';
                $nama = 'Sharfina Febbi Handayani, S.Kom., M.Kom.';
            }

            // Find existing user by NIDN or Email
            $user = null;
            if (!empty($nidn)) {
                $user = User::where('nidn_nim', $nidn)->first();
            }
            if (!$user) {
                $user = User::where('email', $email)->first();
            }

            $userData = [
                'name' => $nama,
                'nidn_nim' => $nidn ?: null,
                'sinta_id' => $sintaId ?: null,
                'id_fakultas' => $fakultasId,
                'id_prodi' => $prodiId,
                'jabatan_fungsional' => $jafung,
                'sinta_score_overall' => $sintaOverall,
                'sinta_score_3yr' => $sinta3Yr,
                'email_verified_at' => now(),
                'password' => Hash::make(!empty($nidn) ? $nidn : 'password123'),
            ];

            if ($user) {
                $user->update($userData);
            } else {
                $userData['email'] = $email;
                $user = User::create($userData);
            }

            // Assign Dosen role if not already assigned
            if (!$user->roles()->where('role_id', $dosenRole->id)->exists()) {
                $user->roles()->attach($dosenRole->id);
            }

            // Special role: Kepala P3M for Sharfina Febbi Handayani
            if ($nidn === '0617029201') {
                $kplRole = Role::where('name', 'Kepala P3M')->first();
                if ($kplRole && !$user->roles()->where('role_id', $kplRole->id)->exists()) {
                    $user->roles()->attach($kplRole->id);
                }
            }

            $importedCount++;
        }

        fclose($handle);
        $this->command->info("Berhasil mengimpor {$importedCount} data dosen riil dari CSV.");
    }
}

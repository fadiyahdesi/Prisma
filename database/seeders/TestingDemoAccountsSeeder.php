<?php

namespace Database\Seeders;

use App\Models\RefFakultas;
use App\Models\RefProgramStudi;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Fixture akun demo yang dipakai feature test.
 *
 * Seeder produksi sengaja hanya memuat dosen riil + akun sistem, sehingga
 * akun-akun berikut tidak tersedia lagi setelah `db:seed`. Seeder ini
 * mengembalikan akun-akun tersebut HANYA untuk keperluan pengujian.
 */
class TestingDemoAccountsSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan matriks izin role tersedia walaupun test hanya seed RbacSeeder.
        $this->call(PermissionSeeder::class);

        $fakultasIds = RefFakultas::pluck('id_fakultas', 'kode_fakultas');
        $prodiIds = RefProgramStudi::pluck('id_prodi', 'id_fakultas');

        $accounts = [
            [
                'email' => 'dosen@harkatnegeri.ac.id',
                'name' => 'Dr. Ir. Hendra Prasetya, M.T.',
                'nidn_nim' => '0615037801',
                'fakultas' => 'FST',
                'role' => 'Dosen / Pengusul',
                'sinta_score_3yr' => 185.50,
                'sinta_score_overall' => 320.00,
            ],
            [
                'email' => 'reviewer@harkatnegeri.ac.id',
                'name' => 'Prof. Dr. Ir. Budi Santoso, M.Sc.',
                'nidn_nim' => '0620087102',
                'fakultas' => 'FST',
                'role' => 'Reviewer',
                'rumpun_ilmu' => 'Teknik',
                'bidang_keahlian' => 'Sistem Sensor Cerdas dan Instrumentasi Pertanian',
                'sinta_score_3yr' => 310.00,
                'sinta_score_overall' => 540.00,
            ],
            [
                'email' => 'reviewer2@harkatnegeri.ac.id',
                'name' => 'Prof. Dr. Hj. Endang Rahayu, M.Si.',
                'nidn_nim' => '0611026901',
                'fakultas' => 'FSH',
                'role' => 'Reviewer',
                'rumpun_ilmu' => 'Teknik',
                'bidang_keahlian' => 'Sistem Sensor Cerdas dan AI untuk Pertanian Presisi',
                'sinta_score_3yr' => 295.00,
                'sinta_score_overall' => 480.00,
            ],
            [
                'email' => 'reviewer3@harkatnegeri.ac.id',
                'name' => 'Prof. Dr. Agus Suryanto, M.Pd.',
                'nidn_nim' => '0608057201',
                'fakultas' => 'FPP',
                'role' => 'Reviewer',
                'rumpun_ilmu' => 'Teknik',
                'bidang_keahlian' => 'Pertanian Presisi dan Sistem Sensor Cerdas',
                'sinta_score_3yr' => 330.00,
                'sinta_score_overall' => 560.00,
            ],
            [
                'email' => 'kaprodi@harkatnegeri.ac.id',
                'name' => 'Dr. Ratna Sari, S.E., M.Si.',
                'nidn_nim' => '0612058001',
                'fakultas' => 'FSH',
                'role' => 'Kaprodi',
            ],
            [
                'email' => 'dekan@harkatnegeri.ac.id',
                'name' => 'Drs. Bambang Sudiro, M.Kom.',
                'nidn_nim' => '0604087103',
                'fakultas' => 'SV',
                'role' => 'Dekanat',
            ],
            [
                'email' => 'adminp3m@harkatnegeri.ac.id',
                'name' => 'Admin P3M UHN',
                'nidn_nim' => 'ADM-P3M-01',
                'fakultas' => 'FST',
                'role' => 'Admin P3M',
            ],
            [
                'email' => 'kepalap3m@harkatnegeri.ac.id',
                'name' => 'Sharfina Febbi Handayani, S.Kom., M.Kom.',
                'nidn_nim' => '0617029201',
                'fakultas' => 'FST',
                'role' => 'Kepala P3M',
            ],
            [
                'email' => 'keuangan@harkatnegeri.ac.id',
                'name' => 'Bendahara P3M UHN',
                'nidn_nim' => 'KEU-P3M-01',
                'fakultas' => 'FST',
                'role' => 'Keuangan',
            ],
            [
                'email' => 'divisipublikasi@harkatnegeri.ac.id',
                'name' => 'Divisi Publikasi UHN',
                'nidn_nim' => 'DIV-PUB-01',
                'fakultas' => 'FST',
                'role' => 'Divisi Publikasi',
            ],
            [
                'email' => 'superadmin@harkatnegeri.ac.id',
                'name' => 'Super Administrator UHN',
                'nidn_nim' => 'ROOT-UHN-01',
                'fakultas' => 'FST',
                'role' => 'Superadmin',
            ],
            [
                'email' => 'dosen2@harkatnegeri.ac.id',
                'name' => 'Dr. Ahmad Fauzi, M.T.',
                'nidn_nim' => '0618048202',
                'fakultas' => 'FST',
                'role' => 'Dosen / Pengusul',
                'sinta_score_3yr' => 165.00,
                'sinta_score_overall' => 280.00,
            ],
            [
                'email' => 'dosen3@harkatnegeri.ac.id',
                'name' => 'Anita Wijaya, S.T., M.Eng.',
                'nidn_nim' => '0622098801',
                'fakultas' => 'FST',
                'role' => 'Dosen / Pengusul',
                'sinta_score_3yr' => 75.00,
                'sinta_score_overall' => 110.00,
            ],
            [
                'email' => 'mahasiswa1@harkatnegeri.ac.id',
                'name' => 'Rizky Pratama',
                'nidn_nim' => '220101001',
                'fakultas' => 'FST',
                'role' => 'Dosen / Mahasiswa Anggota',
            ],
            [
                'email' => 'mahasiswa2@harkatnegeri.ac.id',
                'name' => 'Siti Rahmawati',
                'nidn_nim' => '220101002',
                'fakultas' => 'FST',
                'role' => 'Dosen / Mahasiswa Anggota',
            ],
        ];

        foreach ($accounts as $account) {
            $fakultasId = $fakultasIds[$account['fakultas']] ?? $fakultasIds->first();
            $roleId = Role::where('name', $account['role'])->value('id');

            // Akun dengan NIDN yang sama sudah dibuat seeder dosen riil.
            // Pakai ulang baris tersebut agar tidak melanggar unique(nidn_nim).
            $user = User::where('email', $account['email'])->first()
                ?? User::where('nidn_nim', $account['nidn_nim'])->first();

            $attributes = [
                'email' => $account['email'],
                'name' => $account['name'],
                'nidn_nim' => $account['nidn_nim'],
                'password' => Hash::make('password'),
                'id_fakultas' => $fakultasId,
                'id_prodi' => $prodiIds[$fakultasId] ?? null,
                'sinta_score_3yr' => $account['sinta_score_3yr'] ?? 0,
                'sinta_score_overall' => $account['sinta_score_overall'] ?? 0,
                'is_otp_verified' => true,
                'email_verified_at' => now(),
            ];

            if ($user) {
                $user->update($attributes);
            } else {
                $user = User::create($attributes);
            }

            if ($roleId) {
                $user->roles()->sync([$roleId]);
            }

            if (!empty($account['rumpun_ilmu']) || !empty($account['bidang_keahlian'])) {
                $user->update([
                    'rumpun_ilmu'               => $account['rumpun_ilmu'] ?? $user->rumpun_ilmu,
                    'bidang_keahlian'           => $account['bidang_keahlian'] ?? $user->bidang_keahlian,
                    'reviewer_application_status' => 'approved',
                    'reviewer_applied_at'       => $user->reviewer_applied_at ?? now(),
                ]);
            }
        }
    }
}

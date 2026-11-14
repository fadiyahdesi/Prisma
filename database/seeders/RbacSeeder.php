<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RefFakultas;
use App\Models\RefProgramStudi;
use App\Models\PpmUsulan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RbacSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Faculties
        $fst = RefFakultas::updateOrCreate(
            ['kode_fakultas' => 'FST'],
            ['nama_fakultas' => 'Fakultas Sains & Teknologi', 'dekan_nama' => 'Dr. Ir. Hendra Prasetya, M.T.', 'dekan_nip' => '197803152005011002']
        );
        $fsh = RefFakultas::updateOrCreate(
            ['kode_fakultas' => 'FSH'],
            ['nama_fakultas' => 'Fakultas Sosial & Humaniora', 'dekan_nama' => 'Dr. Ratna Sari, S.E., M.Si.', 'dekan_nip' => '198005122006042001']
        );
        $fpp = RefFakultas::updateOrCreate(
            ['kode_fakultas' => 'FPP'],
            ['nama_fakultas' => 'Fakultas Psikologi & Pendidikan', 'dekan_nama' => 'Dr. Wahyu Hidayat, M.Psi.', 'dekan_nip' => '197511202003121001']
        );
        $sv = RefFakultas::updateOrCreate(
            ['kode_fakultas' => 'SV'],
            ['nama_fakultas' => 'Sekolah Vokasi', 'dekan_nama' => 'Drs. Bambang Sudiro, M.Kom.', 'dekan_nip' => '197108041998031003']
        );

        // 2. Seed Study Programs (23 Official Program Studi - UHN)
        $prodis = [
            // Fakultas Sains & Teknologi (4 Prodi)
            ['id_fakultas' => $fst->id_fakultas, 'kode_prodi' => 'SD-S1', 'nama_prodi' => 'S-1 Sains Data', 'jenjang' => 'S1', 'kaprodi_nama' => 'Hepatika Zidny Ilmadina, S.Pd., M.Kom.', 'kaprodi_nip' => '0618119101'],
            ['id_fakultas' => $fst->id_fakultas, 'kode_prodi' => 'TM-S1', 'nama_prodi' => 'S-1 Teknik Mesin', 'jenjang' => 'S1', 'kaprodi_nama' => 'Firman Lukman Sanjaya, S.T., M.T.', 'kaprodi_nip' => '0630069202'],
            ['id_fakultas' => $fst->id_fakultas, 'kode_prodi' => 'TI-S1', 'nama_prodi' => 'S-1 Teknik Informatika', 'jenjang' => 'S1', 'kaprodi_nama' => 'Aang Alim Murtopo, M.Kom.', 'kaprodi_nip' => '0613017701'],
            ['id_fakultas' => $fst->id_fakultas, 'kode_prodi' => 'SI-S1', 'nama_prodi' => 'S-1 Sistem Informasi', 'jenjang' => 'S1', 'kaprodi_nama' => 'Bangkit Indarmawan Nugroho, M.Kom.', 'kaprodi_nip' => '0026077701'],

            // Fakultas Sosial & Humaniora (4 Prodi)
            ['id_fakultas' => $fsh->id_fakultas, 'kode_prodi' => 'AKT-S1', 'nama_prodi' => 'S-1 Akuntansi', 'jenjang' => 'S1', 'kaprodi_nama' => 'Aryanto, S.E., M.Ak.', 'kaprodi_nip' => '0625059002'],
            ['id_fakultas' => $fsh->id_fakultas, 'kode_prodi' => 'HKM-S1', 'nama_prodi' => 'S-1 Hukum', 'jenjang' => 'S1', 'kaprodi_nama' => 'David Bani Adam, S.H., M.H.', 'kaprodi_nip' => '0614028402'],
            ['id_fakultas' => $fsh->id_fakultas, 'kode_prodi' => 'ILKOM-S1', 'nama_prodi' => 'S-1 Ilmu Komunikasi', 'jenjang' => 'S1', 'kaprodi_nama' => 'Himawan Ardhi Ristanto, S.I.Kom., M.I.Kom', 'kaprodi_nip' => '08.025.573'],
            ['id_fakultas' => $fsh->id_fakultas, 'kode_prodi' => 'MNJ-S1', 'nama_prodi' => 'S-1 Manajemen', 'jenjang' => 'S1', 'kaprodi_nama' => 'Bahri Kamal, S.E., M.M.', 'kaprodi_nip' => '0617078102'],

            // Fakultas Psikologi & Pendidikan (2 Prodi)
            ['id_fakultas' => $fpp->id_fakultas, 'kode_prodi' => 'PSI-S1', 'nama_prodi' => 'S-1 Psikologi', 'jenjang' => 'S1', 'kaprodi_nama' => 'Ahmad Fadil, S.Psi., M.Psi.', 'kaprodi_nip' => '08.025.583'],
            ['id_fakultas' => $fpp->id_fakultas, 'kode_prodi' => 'PGSD-S1', 'nama_prodi' => 'S-1 Pendidikan Guru Sekolah Dasar (PGSD)', 'jenjang' => 'S1', 'kaprodi_nama' => 'Ratri Wikaningtyas, M.Pd.', 'kaprodi_nip' => '0626029101'],

            // Sekolah Vokasi (13 Prodi)
            ['id_fakultas' => $sv->id_fakultas, 'kode_prodi' => 'ASP-D4', 'nama_prodi' => 'D-4 Akuntansi Sektor Publik', 'jenjang' => 'D4', 'kaprodi_nama' => 'Mohammad Alfian, S.E., M.Si., Ak.', 'kaprodi_nip' => '0610108904'],
            ['id_fakultas' => $sv->id_fakultas, 'kode_prodi' => 'KBD-D4', 'nama_prodi' => 'D-4 Kebidanan', 'jenjang' => 'D4', 'kaprodi_nama' => 'Bdn. Seventina Nurul H, S. SiT, M.Kes', 'kaprodi_nip' => '1117088601'],
            ['id_fakultas' => $sv->id_fakultas, 'kode_prodi' => 'PBD-PRF', 'nama_prodi' => 'Pendidikan Profesi Bidan', 'jenjang' => 'D4', 'kaprodi_nama' => 'Bdn. Nora Rahmanindar, S. SiT, M.Keb', 'kaprodi_nip' => '0613118506'],
            ['id_fakultas' => $sv->id_fakultas, 'kode_prodi' => 'TI-D4', 'nama_prodi' => 'D-4 Teknik Informatika', 'jenjang' => 'D4', 'kaprodi_nama' => 'Dyah Apriliani, S.T., M.Kom.', 'kaprodi_nip' => '0614049002'],
            ['id_fakultas' => $sv->id_fakultas, 'kode_prodi' => 'AKT-D3', 'nama_prodi' => 'D-3 Akuntansi', 'jenjang' => 'D3', 'kaprodi_nama' => 'Krisdiyawati, S.E., M.Ak., Ak.', 'kaprodi_nip' => '0616117404'],
            ['id_fakultas' => $sv->id_fakultas, 'kode_prodi' => 'DKV-D3', 'nama_prodi' => 'D-3 Desain Komunikasi Visual', 'jenjang' => 'D3', 'kaprodi_nama' => 'Robby Hardian, S.IP., M.Ds.', 'kaprodi_nip' => '0606098402'],
            ['id_fakultas' => $sv->id_fakultas, 'kode_prodi' => 'FRM-D3', 'nama_prodi' => 'D-3 Farmasi', 'jenjang' => 'D3', 'kaprodi_nama' => 'apt. Rizki Febriyanti, M. Farm', 'kaprodi_nip' => '0627028302'],
            ['id_fakultas' => $sv->id_fakultas, 'kode_prodi' => 'KPR-D3', 'nama_prodi' => 'D-3 Keperawatan', 'jenjang' => 'D3', 'kaprodi_nama' => 'Ns. Ardhi Henda Karmandika, M.Kep.', 'kaprodi_nip' => '0615029303'],
            ['id_fakultas' => $sv->id_fakultas, 'kode_prodi' => 'PHT-D3', 'nama_prodi' => 'D-3 Perhotelan', 'jenjang' => 'D3', 'kaprodi_nama' => 'Lourensius Hendra Putra, S.T., M.M.', 'kaprodi_nip' => '0612048207'],
            ['id_fakultas' => $sv->id_fakultas, 'kode_prodi' => 'TE-D3', 'nama_prodi' => 'D-3 Teknik Elektronika', 'jenjang' => 'D3', 'kaprodi_nama' => 'Rony Darpono, M.T.', 'kaprodi_nip' => '0606128001'],
            ['id_fakultas' => $sv->id_fakultas, 'kode_prodi' => 'TK-D3', 'nama_prodi' => 'D-3 Teknik Komputer', 'jenjang' => 'D3', 'kaprodi_nama' => 'Arif Rakhman, S.E., S.Pd, M.Kom', 'kaprodi_nip' => '0623118301'],
            ['id_fakultas' => $sv->id_fakultas, 'kode_prodi' => 'TM-D3', 'nama_prodi' => 'D-3 Teknik Mesin', 'jenjang' => 'D3', 'kaprodi_nama' => 'Mukhamad Khumaidi Usman, S.T., M.Eng.', 'kaprodi_nip' => '0608058601'],
            ['id_fakultas' => $sv->id_fakultas, 'kode_prodi' => 'SKT-D3', 'nama_prodi' => 'D-3 Kebidanan', 'jenjang' => 'D3', 'kaprodi_nama' => 'Adevia Maulidya C, S. ST, M.Kes', 'kaprodi_nip' => '0606118802'],
        ];

        foreach ($prodis as $p) {
            RefProgramStudi::updateOrCreate(['kode_prodi' => $p['kode_prodi']], $p);
        }

        // Clean up obsolete study programs to enforce strictly 22 prodis
        $officialCodes = array_column($prodis, 'kode_prodi');
        $obsoleteProdis = RefProgramStudi::whereNotIn('kode_prodi', $officialCodes)->get();
        foreach ($obsoleteProdis as $obs) {
            $fallbackProdi = RefProgramStudi::where('id_fakultas', $obs->id_fakultas)->whereIn('kode_prodi', $officialCodes)->first();
            if ($fallbackProdi) {
                User::where('id_prodi', $obs->id_prodi)->update(['id_prodi' => $fallbackProdi->id_prodi]);
            }
            $obs->delete();
        }

        // 3. Seed 8 RBAC Roles + Superadmin
        $rolesDef = [
            ['name' => 'Dosen / Pengusul', 'display_name' => 'Dosen / Pengusul Riset & Abmas', 'description' => 'Membuat usulan proposal via Wizard 6 langkah, mengunggah laporan monev & luaran.'],
            ['name' => 'Dosen / Mahasiswa Anggota', 'display_name' => 'Anggota Tim Riset / Abmas', 'description' => 'Menerima notifikasi undangan tim & mengeksekusi konfirmasi Member Consent.'],
            ['name' => 'Reviewer', 'display_name' => 'Reviewer Ilmiah Substantif', 'description' => 'Menilai proposal secara Double-Blind menggunakan rubrik BIMA berskala 1-7.'],
            ['name' => 'Kaprodi', 'display_name' => 'Ketua Program Studi', 'description' => 'Memantau usulan riset dosen homebase & verifikasi keselarasan roadmap prodi.'],
            ['name' => 'Dekanat', 'display_name' => 'Pimpinan Fakultas (Dekan/Wadek)', 'description' => 'Memantau dasbor capaian IKU & serapan pagu anggaran tingkat fakultas.'],
            ['name' => 'Admin P3M', 'display_name' => 'Operator / Admin P3M', 'description' => 'Mengonfigurasi periode Call for Proposals & penugasan reviewer matching.'],
            ['name' => 'Kepala P3M', 'display_name' => 'Kepala Unit P3M UHN', 'description' => 'Verifikasi kelembagaan LPPM Approval, penetapan pemenang, & penetapan SK.'],
            ['name' => 'Keuangan', 'display_name' => 'Divisi Keuangan LPPM', 'description' => 'Memverifikasi RAB SBM & mengeksekusi pencairan dana Termin I (70%) & II (30%).'],
            ['name' => 'Superadmin', 'display_name' => 'Super Administrator Sistem', 'description' => 'Akses penuh seluruh konfigurasi sistem & pemantauan Audit Trail Logging.'],
        ];

        $rolesObj = [];
        foreach ($rolesDef as $r) {
            $rolesObj[$r['name']] = Role::updateOrCreate(['name' => $r['name']], $r);
        }

        // 4. Seed Demo Users for Testing
        $firstProdi = RefProgramStudi::first();

        // Hanya akun sistem yang di-seed. Akun demo fiktif lainnya dinonaktifkan.
        $systemAccountEmails = [
            'adminp3m@harkatnegeri.ac.id',
            'kepalap3m@harkatnegeri.ac.id',
            'superadmin@harkatnegeri.ac.id',
        ];

        $demoUsers = [
            [
                'name' => 'Dr. Ir. Hendra Prasetya, M.T.',
                'email' => 'dosen@harkatnegeri.ac.id',
                'nidn_nim' => '0615037801',
                'jabatan_fungsional' => 'Lektor Kepala',
                'sinta_id' => '6012458',
                'sinta_score_3yr' => 185.50,
                'sinta_score_overall' => 320.00,
                'role' => 'Dosen / Pengusul'
            ],
            [
                'name' => 'Prof. Dr. Ir. Budi Santoso, M.Sc.',
                'email' => 'reviewer@harkatnegeri.ac.id',
                'nidn_nim' => '0620087102',
                'jabatan_fungsional' => 'Guru Besar',
                'sinta_id' => '5987123',
                'sinta_score_3yr' => 310.00,
                'sinta_score_overall' => 540.00,
                'role' => 'Reviewer'
            ],
            [
                'name' => 'Prof. Dr. Hj. Endang Rahayu, M.Si.',
                'email' => 'reviewer2@harkatnegeri.ac.id',
                'nidn_nim' => '0611026901',
                'jabatan_fungsional' => 'Guru Besar',
                'sinta_id' => '5987124',
                'sinta_score_3yr' => 295.00,
                'sinta_score_overall' => 480.00,
                'id_fakultas' => $fsh->id_fakultas,
                'role' => 'Reviewer'
            ],
            [
                'name' => 'Prof. Dr. Agus Suryanto, M.Pd.',
                'email' => 'reviewer3@harkatnegeri.ac.id',
                'nidn_nim' => '0608057201',
                'jabatan_fungsional' => 'Guru Besar',
                'sinta_id' => '5987125',
                'sinta_score_3yr' => 330.00,
                'sinta_score_overall' => 560.00,
                'id_fakultas' => $fpp->id_fakultas,
                'role' => 'Reviewer'
            ],
            [
                'name' => 'Dr. Ratna Sari, S.E., M.Si.',
                'email' => 'kaprodi@harkatnegeri.ac.id',
                'nidn_nim' => '0612058001',
                'jabatan_fungsional' => 'Lektor',
                'role' => 'Kaprodi'
            ],
            [
                'name' => 'Drs. Bambang Sudiro, M.Kom.',
                'email' => 'dekan@harkatnegeri.ac.id',
                'nidn_nim' => '0604087103',
                'jabatan_fungsional' => 'Lektor Kepala',
                'role' => 'Dekanat'
            ],
            [
                'name' => 'Admin P3M UHN',
                'email' => 'adminp3m@harkatnegeri.ac.id',
                'nidn_nim' => 'ADM-P3M-01',
                'role' => 'Admin P3M'
            ],
            [
                'name' => 'Sharfina Febbi Handayani, S.Kom., M.Kom.',
                'email' => 'kepalap3m@harkatnegeri.ac.id',
                'nidn_nim' => '0617029201',
                'role' => 'Kepala P3M'
            ],
            [
                'name' => 'Bendahara P3M UHN',
                'email' => 'keuangan@harkatnegeri.ac.id',
                'nidn_nim' => 'KEU-P3M-01',
                'role' => 'Keuangan'
            ],
            [
                'name' => 'Super Administrator UHN',
                'email' => 'superadmin@harkatnegeri.ac.id',
                'nidn_nim' => 'ROOT-UHN-01',
                'role' => 'Superadmin'
            ],
            [
                'name' => 'Dr. Ahmad Fauzi, M.T.',
                'email' => 'dosen2@harkatnegeri.ac.id',
                'nidn_nim' => '0618048202',
                'jabatan_fungsional' => 'Lektor',
                'sinta_id' => '6048202',
                'sinta_score_3yr' => 165.00,
                'sinta_score_overall' => 280.00,
                'role' => 'Dosen / Pengusul'
            ],
            [
                'name' => 'Anita Wijaya, S.T., M.Eng.',
                'email' => 'dosen3@harkatnegeri.ac.id',
                'nidn_nim' => '0622098801',
                'jabatan_fungsional' => 'Asisten Ahli',
                'sinta_id' => '6098801',
                'sinta_score_3yr' => 75.00,
                'sinta_score_overall' => 110.00,
                'role' => 'Dosen / Pengusul'
            ],
            [
                'name' => 'Rizky Pratama',
                'email' => 'mahasiswa1@harkatnegeri.ac.id',
                'nidn_nim' => '220101001',
                'role' => 'Dosen / Mahasiswa Anggota'
            ],
            [
                'name' => 'Siti Rahmawati',
                'email' => 'mahasiswa2@harkatnegeri.ac.id',
                'nidn_nim' => '220101002',
                'role' => 'Dosen / Mahasiswa Anggota'
            ],
        ];

        $demoUsers = array_filter($demoUsers, fn ($u) => in_array($u['email'], $systemAccountEmails, true));

        foreach ($demoUsers as $u) {
            $targetFakultasId = $u['id_fakultas'] ?? $fst->id_fakultas;
            $userProdi = RefProgramStudi::where('id_fakultas', $targetFakultasId)->first();

            $user = User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'password' => Hash::make('password'),
                    'nidn_nim' => $u['nidn_nim'] ?? null,
                    'jabatan_fungsional' => $u['jabatan_fungsional'] ?? null,
                    'sinta_id' => $u['sinta_id'] ?? null,
                    'sinta_score_3yr' => $u['sinta_score_3yr'] ?? 0,
                    'sinta_score_overall' => $u['sinta_score_overall'] ?? 0,
                    'id_fakultas' => $targetFakultasId,
                    'id_prodi' => $userProdi ? $userProdi->id_prodi : $firstProdi->id_prodi,
                    'is_otp_verified' => true,
                    'email_verified_at' => now(),
                ]
            );

            if (isset($rolesObj[$u['role']])) {
                $user->roles()->sync([$rolesObj[$u['role']]->id]);
            }
        }
    }
}


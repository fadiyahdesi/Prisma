<?php

namespace Database\Seeders;

use App\Models\PpmHki;
use App\Models\PpmKontrak;
use App\Models\PpmPencairanDana;
use App\Models\PpmPeriodeHibah;
use App\Models\PpmPublikasiJurnal;
use App\Models\PpmSkemaBima;
use App\Models\PpmUsulan;
use App\Models\PpmUsulanAnggota;
use App\Models\RefFakultas;
use App\Models\RefProgramStudi;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class Epic12DemoDataSeeder extends Seeder
{
    /**
     * Seed rich demo data across all 4 faculties for Epic 12 analytics and reports.
     */
    public function run(): void
    {
        $dosenRole = Role::where('name', 'Dosen / Pengusul')->first();
        $periode = PpmPeriodeHibah::first() ?? PpmPeriodeHibah::create([
            'nama_periode' => 'Call for Proposals BIMA 2026',
            'tahun_akademik' => '2025/2026',
            'semester' => 'Genap',
            'waktu_buka' => now()->subMonths(6),
            'waktu_tutup' => now()->addMonths(6),
            'is_active' => true,
        ]);

        $skema = PpmSkemaBima::first() ?? PpmSkemaBima::create([
            'kode_skema' => 'PD-2026',
            'nama_skema' => 'Penelitian Dasar Kemitraan',
            'kategori' => 'penelitian',
            'plafon_dana' => 50000000,
            'is_active' => true,
        ]);

        $fakultasRecords = RefFakultas::all();

        // Sample Dosen per Fakultas
        $facultyDosenMap = [
            'FST' => [
                'nama' => 'Dr. Ir. Hendra Prasetya, M.T.',
                'email' => 'hendra.fst@harkatnegeri.ac.id',
                'prodi_kode' => 'TI-S1',
                'tkt' => 5, // Terapan
                'judul' => 'Pengembangan Sistem Cerdas Pengenalan Pola Citra Medis Berbasis Edge AI',
                'pagu' => 45000000,
                'serapan' => 31500000,
            ],
            'FSH' => [
                'nama' => 'Dr. Ratna Sari, S.E., M.Si.',
                'email' => 'ratna.fsh@harkatnegeri.ac.id',
                'prodi_kode' => 'AKT-S1',
                'tkt' => 2, // Dasar
                'judul' => 'Model Tata Kelola Keuangan Digital untuk Penguatan Kinerja UMKM Pasca Pemulihan Ekonomi',
                'pagu' => 35000000,
                'serapan' => 24500000,
            ],
            'FPP' => [
                'nama' => 'Dr. Wahyu Hidayat, M.Psi.',
                'email' => 'wahyu.fpp@harkatnegeri.ac.id',
                'prodi_kode' => 'PSI-S1',
                'tkt' => 3, // Dasar
                'judul' => 'Pengaruh Intervensi Psikoedukasi Berbasis Komunitas terhadap Resiliensi Remaja',
                'pagu' => 30000000,
                'serapan' => 15000000,
            ],
            'SV' => [
                'nama' => 'Drs. Bambang Sudiro, M.Kom.',
                'email' => 'bambang.sv@harkatnegeri.ac.id',
                'prodi_kode' => 'RO-D4',
                'tkt' => 8, // Pengembangan
                'judul' => 'Rancang Bangun Prototipe Kendaraan Listrik Ringan untuk Transportasi Lingkungan Kampus Terpadu',
                'pagu' => 50000000,
                'serapan' => 50000000,
            ],
        ];

        foreach ($facultyDosenMap as $fakKode => $data) {
            $fak = $fakultasRecords->firstWhere('kode_fakultas', $fakKode);
            if (!$fak) continue;

            $prodi = RefProgramStudi::where('kode_prodi', $data['prodi_kode'])->first()
                ?? RefProgramStudi::where('id_fakultas', $fak->id_fakultas)->first();

            $dosen = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['nama'],
                    'password' => Hash::make('password'),
                    'nidn_nim' => '06' . rand(10000000, 99999999),
                    'id_fakultas' => $fak->id_fakultas,
                    'id_prodi' => $prodi?->id_prodi,
                    'is_otp_verified' => true,
                    'email_verified_at' => now(),
                ]
            );

            if ($dosenRole) {
                $dosen->roles()->syncWithoutDetaching([$dosenRole->id]);
            }

            // Create Proposal
            $usulan = PpmUsulan::updateOrCreate(
                [
                    'judul_usulan' => $data['judul'],
                    'id_pengusul' => $dosen->id,
                ],
                [
                    'id_periode_hibah' => $periode->id,
                    'id_skema_bima' => $skema->id,
                    'kode_usulan' => 'BIMA-' . $fakKode . '-' . date('Y') . '-' . Str::random(4),
                    'ringkasan_substansi' => 'Riset terpadu berorientasi IKU dan hilirisasi produk iptek perguruan tinggi.',
                    'target_tkt' => $data['tkt'],
                    'total_rab' => $data['pagu'],
                    'status' => 'Ongoing',
                    'submitted_at' => now()->subMonths(4),
                ]
            );

            // Student members for IKU-2
            PpmUsulanAnggota::updateOrCreate(
                [
                    'id_usulan' => $usulan->id,
                    'identifier' => '220101' . rand(100, 999),
                ],
                [
                    'nama' => 'Mahasiswa IKU ' . $fakKode,
                    'jenis_anggota' => 'mahasiswa',
                    'peran_anggota' => 'Mahasiswa IKU-2',
                    'status_persetujuan' => 'approved',
                    'approved_at' => now(),
                ]
            );

            // Contract & Disbursement
            $kontrak = PpmKontrak::updateOrCreate(
                ['id_usulan' => $usulan->id],
                [
                    'nomor_kontrak' => 'SPK/UHN/LPPM/' . $fakKode . '/' . date('Y') . '/001',
                    'nomor_sk' => 'SK-REKTOR-2026-' . $fakKode,
                    'tanggal_sk' => now()->subMonths(3)->toDateString(),
                    'tanggal_kontrak' => now()->subMonths(3)->toDateString(),
                    'verification_token' => Str::random(32),
                    'pagu_disetujui' => $data['pagu'],
                    'dana_termin_1' => $data['pagu'] * 0.7,
                    'dana_termin_2' => $data['pagu'] * 0.3,
                    'signed_by_pengusul' => true,
                    'signed_by_pengusul_at' => now(),
                    'signed_by_kepala' => true,
                    'signed_by_kepala_at' => now(),
                    'status' => 'Signed',
                    'nomor_rekening' => '1234567890',
                    'nama_bank' => 'Bank Mandiri',
                    'nama_pemilik_rekening' => $dosen->name,
                    'rekening_verified_at' => now(),
                ]
            );

            // Disbursed funds
            PpmPencairanDana::updateOrCreate(
                [
                    'id_kontrak' => $kontrak->id,
                    'termin' => 1,
                ],
                [
                    'jumlah_dana' => $data['serapan'],
                    'persentase' => 70,
                    'status_pencairan' => 'transferred',
                    'tanggal_transfer' => now()->toDateString(),
                    'nomor_referensi' => 'TRF-' . $fakKode . '-70',
                ]
            );

            // Publications (IKU-5 & Borang 3.b.3)
            PpmPublikasiJurnal::updateOrCreate(
                [
                    'user_id' => $dosen->id,
                    'judul_artikel' => 'Advances in ' . $data['judul'],
                ],
                [
                    'nama_jurnal' => 'Journal of Applied Research & Technology',
                    'kategori_peringkat' => ($fakKode === 'FST' || $fakKode === 'SV') ? 'Scopus Q1' : 'SINTA 2',
                    'tahun_terbit' => 2026,
                    'issn' => '2345-6789',
                    'volume_nomor' => 'Vol. 12 No. 2',
                    'doi' => '10.1016/j.prisma.2026.' . strtolower($fakKode),
                    'file_naskah' => 'publikasi/naskah_' . strtolower($fakKode) . '.pdf',
                    'metadata_source' => 'SINTA',
                ]
            );

            // HKI (IKU-5 & Borang 3.b.4)
            PpmHki::updateOrCreate(
                [
                    'user_id' => $dosen->id,
                    'judul_hki' => 'Invensi / Karya Cipta: ' . $data['judul'],
                ],
                [
                    'jenis_hki' => ($fakKode === 'FST' || $fakKode === 'SV') ? 'Paten Sederhana' : 'Hak Cipta',
                    'nomor_permohonan' => 'P0020260' . rand(1000, 9999),
                    'nomor_sertifikat' => 'IDS00000' . rand(100, 999),
                    'tanggal_permohonan' => now()->subMonths(3),
                    'tanggal_terbit' => now()->subMonth(),
                    'pemegang_hak' => 'Universitas Harkat Negeri',
                    'file_sertifikat' => 'hki/sertifikat_' . strtolower($fakKode) . '.pdf',
                    'status_hki' => 'Terverifikasi HKI',
                ]
            );
        }
    }
}

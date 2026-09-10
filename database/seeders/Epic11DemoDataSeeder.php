<?php

namespace Database\Seeders;

use App\Models\PpmHki;
use App\Models\PpmKlaimReward;
use App\Models\PpmPublikasiJurnal;
use App\Models\PpmRewardDistribusi;
use App\Models\RefTarifRewardSk;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class Epic11DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $dosen = User::where('email', 'dosen@harkatnegeri.ac.id')->first();
        $adminP3m = User::where('email', 'adminp3m@harkatnegeri.ac.id')->first();
        $keuangan = User::where('email', 'keuangan@harkatnegeri.ac.id')->first();

        if (!$dosen) {
            $this->command->error('User dosen@harkatnegeri.ac.id tidak ditemukan. Harap jalankan RbacSeeder terlebih dahulu.');
            return;
        }

        // Dummy PDF file for proofs
        Storage::disk('public')->makeDirectory('publikasi_naskah');
        Storage::disk('public')->makeDirectory('hki_sertifikat');
        Storage::disk('public')->makeDirectory('surat_pernyataan_reward');
        Storage::disk('public')->makeDirectory('bukti_transfer_reward');

        $samplePdf = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj 2 0 obj<</Type/Pages/Count 1/Kids[3 0 R]>>endobj 3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Parent 2 0 R/Resources<<>>>>endobj\nxref\n0 4\n0000000000 65535 f\n0000000009 00000 n\n0000000052 00000 n\n0000000101 00000 n\ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n178\n%%EOF";

        Storage::disk('public')->put('publikasi_naskah/sample_paper.pdf', $samplePdf);
        Storage::disk('public')->put('hki_sertifikat/sample_sertifikat.pdf', $samplePdf);
        Storage::disk('public')->put('surat_pernyataan_reward/sample_kesepakatan.pdf', $samplePdf);
        Storage::disk('public')->put('bukti_transfer_reward/sample_transfer.pdf', $samplePdf);

        // 1. Matriks Tarif SK Rektor UHN 2026
        $rates = [
            // Publikasi
            ['kategori' => 'Publikasi', 'sub_kategori' => 'Scopus Q1', 'nominal_insentif' => 35000000, 'nomor_sk_rektor' => 'SK-REKTOR/UHN/2026/015', 'keterangan' => 'Jurnal Internasional Bereputasi Q1 (SJR/Impact Factor Tinggi)'],
            ['kategori' => 'Publikasi', 'sub_kategori' => 'Scopus Q2', 'nominal_insentif' => 25000000, 'nomor_sk_rektor' => 'SK-REKTOR/UHN/2026/015', 'keterangan' => 'Jurnal Internasional Bereputasi Q2'],
            ['kategori' => 'Publikasi', 'sub_kategori' => 'Scopus Q3', 'nominal_insentif' => 15000000, 'nomor_sk_rektor' => 'SK-REKTOR/UHN/2026/015', 'keterangan' => 'Jurnal Internasional Bereputasi Q3'],
            ['kategori' => 'Publikasi', 'sub_kategori' => 'Scopus Q4', 'nominal_insentif' => 10000000, 'nomor_sk_rektor' => 'SK-REKTOR/UHN/2026/015', 'keterangan' => 'Jurnal Internasional Bereputasi Q4'],
            ['kategori' => 'Publikasi', 'sub_kategori' => 'SINTA 1', 'nominal_insentif' => 12000000, 'nomor_sk_rektor' => 'SK-REKTOR/UHN/2026/015', 'keterangan' => 'Jurnal Nasional Terakreditasi SINTA 1'],
            ['kategori' => 'Publikasi', 'sub_kategori' => 'SINTA 2', 'nominal_insentif' => 8000000, 'nomor_sk_rektor' => 'SK-REKTOR/UHN/2026/015', 'keterangan' => 'Jurnal Nasional Terakreditasi SINTA 2'],
            ['kategori' => 'Publikasi', 'sub_kategori' => 'SINTA 3', 'nominal_insentif' => 5000000, 'nomor_sk_rektor' => 'SK-REKTOR/UHN/2026/015', 'keterangan' => 'Jurnal Nasional Terakreditasi SINTA 3'],
            ['kategori' => 'Publikasi', 'sub_kategori' => 'SINTA 4', 'nominal_insentif' => 3000000, 'nomor_sk_rektor' => 'SK-REKTOR/UHN/2026/015', 'keterangan' => 'Jurnal Nasional Terakreditasi SINTA 4'],
            ['kategori' => 'Publikasi', 'sub_kategori' => 'Internasional Terindeks Lainnya', 'nominal_insentif' => 7500000, 'nomor_sk_rektor' => 'SK-REKTOR/UHN/2026/015', 'keterangan' => 'Jurnal Internasional Non-Q'],
            ['kategori' => 'Publikasi', 'sub_kategori' => 'Nasional Terakreditasi', 'nominal_insentif' => 2500000, 'nomor_sk_rektor' => 'SK-REKTOR/UHN/2026/015', 'keterangan' => 'Jurnal Nasional Terakreditasi Lainnya'],

            // HKI
            ['kategori' => 'HKI', 'sub_kategori' => 'Paten', 'nominal_insentif' => 40000000, 'nomor_sk_rektor' => 'SK-REKTOR/UHN/2026/015', 'keterangan' => 'Paten Biasa Granted'],
            ['kategori' => 'HKI', 'sub_kategori' => 'Paten Sederhana', 'nominal_insentif' => 20000000, 'nomor_sk_rektor' => 'SK-REKTOR/UHN/2026/015', 'keterangan' => 'Paten Sederhana Granted'],
            ['kategori' => 'HKI', 'sub_kategori' => 'Hak Cipta', 'nominal_insentif' => 5000000, 'nomor_sk_rektor' => 'SK-REKTOR/UHN/2026/015', 'keterangan' => 'Sertifikat Pencatatan Ciptaan DJKI'],
            ['kategori' => 'HKI', 'sub_kategori' => 'Desain Industri', 'nominal_insentif' => 10000000, 'nomor_sk_rektor' => 'SK-REKTOR/UHN/2026/015', 'keterangan' => 'Desain Industri Terdaftar'],
            ['kategori' => 'HKI', 'sub_kategori' => 'Merk Dagang', 'nominal_insentif' => 7500000, 'nomor_sk_rektor' => 'SK-REKTOR/UHN/2026/015', 'keterangan' => 'Merk Dagang Terdaftar'],
        ];

        foreach ($rates as $r) {
            RefTarifRewardSk::updateOrCreate(
                ['kategori' => $r['kategori'], 'sub_kategori' => $r['sub_kategori']],
                array_merge($r, ['is_active' => true])
            );
        }

        // 2. Bank Publikasi Jurnal (US-11.1)
        // Paper 1: Scopus Q1 (Unclaimed)
        $paper1 = PpmPublikasiJurnal::updateOrCreate(
            ['doi' => '10.1016/j.future.2026.01.001'],
            [
                'user_id' => $dosen->id,
                'judul_artikel' => 'Autonomous Edge-AI Orchestration for Decentralized IoT Sensing Networks in Smart Agriculture',
                'nama_jurnal' => 'Future Generation Computer Systems',
                'issn' => '0167-739X',
                'kategori_peringkat' => 'Scopus Q1',
                'tahun_terbit' => 2026,
                'volume_nomor' => 'Vol. 158, No. 2',
                'url_artikel' => 'https://doi.org/10.1016/j.future.2026.01.001',
                'file_naskah' => 'publikasi_naskah/sample_paper.pdf',
                'jumlah_penulis' => 3,
                'metadata_source' => 'crossref',
                'is_claimed_reward' => false,
            ]
        );

        // Paper 2: SINTA 2 (Already Claimed)
        $paper2 = PpmPublikasiJurnal::updateOrCreate(
            ['doi' => '10.22146/ijc.2025.99999'],
            [
                'user_id' => $dosen->id,
                'judul_artikel' => 'Rancang Bangun Sistem Monitoring Kualitas Air Tambak Berbasis Wireless Sensor Network',
                'nama_jurnal' => 'Indonesian Journal of Computing and Cybernetics',
                'issn' => '1410-8534',
                'kategori_peringkat' => 'SINTA 2',
                'tahun_terbit' => 2025,
                'volume_nomor' => 'Vol. 19, No. 3',
                'url_artikel' => 'https://doi.org/10.22146/ijc.2025.99999',
                'file_naskah' => 'publikasi_naskah/sample_paper.pdf',
                'jumlah_penulis' => 2,
                'metadata_source' => 'manual',
                'is_claimed_reward' => true,
            ]
        );

        // 3. Sentra HKI UHN (US-11.2)
        // HKI 1: Paten (Terverifikasi, Unclaimed)
        $hki1 = PpmHki::updateOrCreate(
            ['nomor_permohonan' => 'P00202600888'],
            [
                'user_id' => $dosen->id,
                'jenis_hki' => 'Paten',
                'judul_hki' => 'Metode dan Alat Pembangkit Energi Mikro Hidro Portabel Berbasis Turbin Vortex Sumbu Vertikal',
                'nomor_sertifikat' => 'IDP000098765',
                'tanggal_permohonan' => '2025-06-15',
                'tanggal_terbit' => '2026-02-10',
                'pemegang_hak' => 'Universitas Harkat Negeri',
                'file_sertifikat' => 'hki_sertifikat/sample_sertifikat.pdf',
                'status_hki' => 'Terverifikasi HKI',
                'verified_by_user_id' => $adminP3m?->id,
                'verified_at' => now()->subDays(10),
                'catatan_verifikasi' => 'Sertifikat Paten DJKI diverifikasi resmi dan sah oleh Sentra HKI UHN.',
                'is_claimed_reward' => false,
            ]
        );

        // HKI 2: Hak Cipta (Pending Verification in Admin Queue)
        $hki2 = PpmHki::updateOrCreate(
            ['nomor_permohonan' => 'EC00202611223'],
            [
                'user_id' => $dosen->id,
                'jenis_hki' => 'Hak Cipta',
                'judul_hki' => 'Perangkat Lunak Dashboard Real-Time Pemantauan Hasil Panen Hidroponik PRISMA-Agri v1.0',
                'nomor_sertifikat' => null,
                'tanggal_permohonan' => '2026-03-01',
                'tanggal_terbit' => null,
                'pemegang_hak' => 'Universitas Harkat Negeri',
                'file_sertifikat' => 'hki_sertifikat/sample_sertifikat.pdf',
                'status_hki' => 'Pending_verification',
                'verified_by_user_id' => null,
                'verified_at' => null,
                'catatan_verifikasi' => 'Menunggu verifikasi keaslian berkas oleh staf Sentra HKI UHN.',
                'is_claimed_reward' => false,
            ]
        );

        // HKI 3: Hak Cipta (Claimed)
        $hki3 = PpmHki::updateOrCreate(
            ['nomor_permohonan' => 'EC00202599887'],
            [
                'user_id' => $dosen->id,
                'jenis_hki' => 'Hak Cipta',
                'judul_hki' => 'Buku Monograf: Implementasi Sistem Tertanam Berbasis RISC-V untuk Otomasi Industri',
                'nomor_sertifikat' => '000456123',
                'tanggal_permohonan' => '2025-11-20',
                'tanggal_terbit' => '2025-12-05',
                'pemegang_hak' => 'Universitas Harkat Negeri',
                'file_sertifikat' => 'hki_sertifikat/sample_sertifikat.pdf',
                'status_hki' => 'Terverifikasi HKI',
                'verified_by_user_id' => $adminP3m?->id,
                'verified_at' => now()->subMonths(2),
                'catatan_verifikasi' => 'Terverifikasi otomatis pangkalan data DJKI.',
                'is_claimed_reward' => true,
            ]
        );

        // 4. Klaim Reward & Mesin Distribusi Multi-Penulis (US-11.3 & US-11.4)
        // Klaim 1: Paper 2 (SINTA 2 - Rp 8.000.000) -> Status: Approved_P3M, Siap Cair di Keuangan!
        $klaim1 = PpmKlaimReward::updateOrCreate(
            ['nomor_klaim' => 'REW/2026/09/DEMO1'],
            [
                'user_id' => $dosen->id,
                'jenis_klaim' => 'Publikasi',
                'id_publikasi' => $paper2->id,
                'id_hki' => null,
                'kategori_insentif' => 'SINTA 2',
                'tarif_dasar_sk' => 8000000,
                'total_reward' => 8000000,
                'file_surat_pernyataan' => 'surat_pernyataan_reward/sample_kesepakatan.pdf',
                'status_klaim' => 'Approved_P3M',
                'approved_by_p3m' => $adminP3m?->id,
                'approved_at' => now()->subDays(2),
                'catatan_p3m' => 'Klaim disetujui sesuai matriks SK Rektor SINTA 2. Diteruskan ke Divisi Keuangan.',
            ]
        );

        // Distribusi Klaim 1 (60% Dosen Utama, 40% Dosen Mitra) -> Total = 100%
        PpmRewardDistribusi::updateOrCreate(
            ['id_klaim_reward' => $klaim1->id, 'nama_penulis' => $dosen->name],
            [
                'user_id' => $dosen->id,
                'nidn_nim' => $dosen->nidn ?? '0011223344',
                'email' => $dosen->email,
                'peran_penulis' => 'Penulis Pertama & Korespondensi',
                'persentase' => 60.00,
                'nominal_bagian' => 4800000,
                'nama_bank' => 'BNI',
                'nomor_rekening' => '0834567890',
                'nama_pemilik_rekening' => $dosen->name,
                'status_transfer' => 'Disbursed',
                'tanggal_transfer' => now()->subDay()->toDateString(),
                'nomor_referensi' => 'TRX-BNI-20260910-001',
                'file_bukti_transfer' => 'bukti_transfer_reward/sample_transfer.pdf',
            ]
        );

        PpmRewardDistribusi::updateOrCreate(
            ['id_klaim_reward' => $klaim1->id, 'nama_penulis' => 'Dr. Budi Wicaksono, M.Kom.'],
            [
                'user_id' => null,
                'nidn_nim' => '0022334455',
                'email' => 'budi.wicaksono@harkatnegeri.ac.id',
                'peran_penulis' => 'Penulis Anggota',
                'persentase' => 40.00,
                'nominal_bagian' => 3200000,
                'nama_bank' => 'Mandiri',
                'nomor_rekening' => '1370019988776',
                'nama_pemilik_rekening' => 'Budi Wicaksono',
                'status_transfer' => 'Pending', // Ready to be disbursed in Keuangan UI!
            ]
        );

        // Klaim 2: HKI 3 (Hak Cipta - Rp 5.000.000) -> Status: Submitted (In P3M Queue)
        $klaim2 = PpmKlaimReward::updateOrCreate(
            ['nomor_klaim' => 'REW/2026/09/DEMO2'],
            [
                'user_id' => $dosen->id,
                'jenis_klaim' => 'HKI',
                'id_publikasi' => null,
                'id_hki' => $hki3->id,
                'kategori_insentif' => 'Hak Cipta',
                'tarif_dasar_sk' => 5000000,
                'total_reward' => 5000000,
                'file_surat_pernyataan' => 'surat_pernyataan_reward/sample_kesepakatan.pdf',
                'status_klaim' => 'Submitted',
                'approved_by_p3m' => null,
                'approved_at' => null,
                'catatan_p3m' => null,
            ]
        );

        PpmRewardDistribusi::updateOrCreate(
            ['id_klaim_reward' => $klaim2->id, 'nama_penulis' => $dosen->name],
            [
                'user_id' => $dosen->id,
                'nidn_nim' => $dosen->nidn ?? '0011223344',
                'email' => $dosen->email,
                'peran_penulis' => 'Ketua Inventor',
                'persentase' => 50.00,
                'nominal_bagian' => 2500000,
                'nama_bank' => 'BNI',
                'nomor_rekening' => '0834567890',
                'nama_pemilik_rekening' => $dosen->name,
                'status_transfer' => 'Pending',
            ]
        );

        PpmRewardDistribusi::updateOrCreate(
            ['id_klaim_reward' => $klaim2->id, 'nama_penulis' => 'Siti Rahmawati, S.T., M.Eng.'],
            [
                'user_id' => null,
                'nidn_nim' => '0033445566',
                'email' => 'siti.rahmawati@harkatnegeri.ac.id',
                'peran_penulis' => 'Anggota Inventor',
                'persentase' => 50.00,
                'nominal_bagian' => 2500000,
                'nama_bank' => 'BRI',
                'nomor_rekening' => '002101998877501',
                'nama_pemilik_rekening' => 'Siti Rahmawati',
                'status_transfer' => 'Pending',
            ]
        );

        $this->command->info('Epic 11 Demo Data Seeded Successfully!');
    }
}


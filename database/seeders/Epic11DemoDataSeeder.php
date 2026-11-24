<?php

namespace Database\Seeders;

use App\Models\PpmHki;
use App\Models\PpmKlaimReward;
use App\Models\PpmPublikasiJurnal;
use App\Models\PpmRewardDistribusi;
use App\Models\User;
use App\Support\PedomanKriteria;
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

        // Official PDF files for proofs
        $pdfService = app(\App\Services\DemoPdfGeneratorService::class);
        $pdfService->generatePaper([], 'publikasi_naskah/sample_paper.pdf');
        $pdfService->generateHkiCertificate([], 'hki_sertifikat/sample_sertifikat.pdf');
        $pdfService->generateRewardKesepakatan([], 'surat_pernyataan_reward/sample_kesepakatan.pdf');
        $pdfService->generateBuktiTransfer([], 'bukti_transfer_reward/sample_transfer.pdf');

        // 1. Matriks Tarif SK Rektor UHN 2026 (sumber: Dispo PPM No. 373.03/PPM.HN/X/2026)
        PedomanKriteria::seedTarif();

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
        // Klaim 1: Paper 2 (SINTA 2 - nominal lama Rp 8.000.000, sengaja tidak diubah) -> Approved_P3M.
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

        // Klaim 2: HKI 3 (Hak Cipta - arsip tarif lama Rp 5.000.000) -> Status: Submitted (In P3M Queue).
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


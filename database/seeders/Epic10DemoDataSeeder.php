<?php

namespace Database\Seeders;

use App\Models\PpmKontrak;
use App\Models\PpmLaporanAkhir;
use App\Models\PpmLogbook;
use App\Models\PpmMonevKemajuan;
use App\Models\PpmPencairanDana;
use App\Models\PpmPeriodeHibah;
use App\Models\PpmSeminarHasil;
use App\Models\PpmSkemaBima;
use App\Models\PpmUsulan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class Epic10DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $dosen = User::where('email', 'dosen@harkatnegeri.ac.id')->first();
        $reviewer = User::where('email', 'reviewer@harkatnegeri.ac.id')->first();
        $keuangan = User::where('email', 'keuangan@harkatnegeri.ac.id')->first();
        $adminP3m = User::where('email', 'adminp3m@harkatnegeri.ac.id')->first();

        if (!$dosen) {
            $this->command->error('User dosen@harkatnegeri.ac.id tidak ditemukan.');
            return;
        }

        $periode = PpmPeriodeHibah::where('is_active', true)->first() ?? PpmPeriodeHibah::first();
        $skemaFnd = PpmSkemaBima::where('kode_skema', 'Fundamental')->first() ?? PpmSkemaBima::first();
        $skemaPdp = PpmSkemaBima::where('kode_skema', 'PDP')->first() ?? PpmSkemaBima::skip(1)->first();

        // Sample PDF official documents
        $pdfService = app(\App\Services\DemoPdfGeneratorService::class);
        $pdfService->generateLaporanKemajuan([], 'monev/demo/laporan_kemajuan_sample.pdf');
        $pdfService->generateSptb(['persen' => 70, 'nomor_sptb' => 'SPTB-70/LPPM-UHN/2026'], 'monev/demo/sptb_70_sample.pdf');
        $pdfService->generateLaporanAkhir([], 'laporan-akhir/demo/laporan_akhir_sample.pdf');
        $pdfService->generateSptb(['persen' => 100, 'nomor_sptb' => 'SPTB-100/LPPM-UHN/2026'], 'laporan-akhir/demo/sptb_100_sample.pdf');
        $pdfService->generateBukuTabungan([], 'tabungan/sample.pdf');

        // =========================================================================
        // SKENARIO 1: Usulan Ongoing untuk Pengujian US-10.1 (Logbook) & US-10.2 (Monev)
        // Kode: USL-2026-FND-001 (atau baru)
        // =========================================================================
        $usulan1 = PpmUsulan::updateOrCreate(
            ['kode_usulan' => 'USL-2026-DEMO-LOGBOOK'],
            [
                'id_pengusul' => $dosen->id,
                'id_skema_bima' => $skemaFnd->id,
                'id_periode_hibah' => $periode->id,
                'judul_usulan' => 'Pengembangan Edge-AI dan Smart Sensor Node Berbasis LoRaWAN untuk Pertanian Presisi',
                'rumpun_ilmu_level_1' => 'Teknik & Rekayasa',
                'rumpun_ilmu_level_2' => 'Teknik Elektro & Informatika',
                'rumpun_ilmu_level_3' => 'Artificial Intelligence & IoT',
                'fokus_rirn' => 'Pangan & Pertanian',
                'target_tkt' => 4,
                'ringkasan_substansi' => 'Riset implementasi jaringan mikrokontroler hemat energi dan pemrosesan AI lokal pada perkebunan.',
                'total_rab' => 45000000.00,
                'skor_akhir' => 640.00,
                'status' => 'Ongoing',
                'submitted_at' => now()->subMonths(3),
            ]
        );

        $kontrak1 = PpmKontrak::updateOrCreate(
            ['id_usulan' => $usulan1->id],
            [
                'nomor_sk' => '045/SK-PEMENANG/P3M-UHN/' . date('Y'),
                'nomor_kontrak' => '088/SPK-BIMA/P3M-UHN/' . date('Y'),
                'tanggal_sk' => now()->subMonths(3),
                'tanggal_kontrak' => now()->subMonths(3),
                'pagu_disetujui' => 45000000.00,
                'dana_termin_1' => 31500000.00, // 70%
                'dana_termin_2' => 13500000.00, // 30%
                'nama_bank' => 'Bank Mandiri',
                'nomor_rekening' => '1090018892011',
                'nama_pemilik_rekening' => $dosen->name,
                'file_buku_tabungan' => 'tabungan/sample.pdf',
                'rekening_verified_at' => now()->subMonths(3),
                'rekening_verified_by' => $keuangan?->id,
                'signed_by_kepala' => true,
                'signed_by_kepala_at' => now()->subMonths(3),
                'signed_by_pengusul' => true,
                'signed_by_pengusul_at' => now()->subMonths(3),
                'status' => 'ongoing',
                'verification_token' => bin2hex(random_bytes(32)),
            ]
        );

        PpmPencairanDana::updateOrCreate(
            ['id_kontrak' => $kontrak1->id, 'termin' => 1],
            [
                'persentase' => 70.00,
                'jumlah_dana' => 31500000.00,
                'nomor_referensi' => 'TRF-MDR-2026-0811',
                'tanggal_transfer' => now()->subMonths(2)->subDays(15),
                'status_pencairan' => 'transferred',
                'processed_by' => $keuangan?->id,
                'processed_at' => now()->subMonths(2)->subDays(15),
                'catatan' => 'Pencairan Termin I 70% berhasil ditransfer.',
            ]
        );

        // Seed sample logbook entries
        PpmLogbook::updateOrCreate(
            ['id_usulan' => $usulan1->id, 'tanggal' => now()->subMonths(2)->toDateString()],
            [
                'aktivitas' => 'Koordinasi awal tim peneliti dan pengadaan modul sensor mikrokontroler STM32 dan modul transceiver LoRa SX1276.',
                'persentase_capaian' => 15.0,
                'created_by' => $dosen->id,
            ]
        );

        PpmLogbook::updateOrCreate(
            ['id_usulan' => $usulan1->id, 'tanggal' => now()->subMonth()->subDays(10)->toDateString()],
            [
                'aktivitas' => 'Perancangan arsitektur firmware edge-AI, implementasi kompresi model TensorFlow Lite, dan kalibrasi sensor kelembaban tanah.',
                'persentase_capaian' => 40.0,
                'created_by' => $dosen->id,
            ]
        );

        PpmLogbook::updateOrCreate(
            ['id_usulan' => $usulan1->id, 'tanggal' => now()->subDays(5)->toDateString()],
            [
                'aktivitas' => 'Uji coba komunikasi nirkabel gateway LoRaWAN di lahan uji coba pertanian dengan jarak pancar 2.5 km. Seluruh data telemetri berhasil terkirim ke server lokal.',
                'persentase_capaian' => 65.0,
                'created_by' => $dosen->id,
            ]
        );

        // =========================================================================
        // SKENARIO 2: Usulan Siap Dinilai Reviewer Monev (US-10.2)
        // Kode: USL-2026-DEMO-MONEV
        // =========================================================================
        $usulan2 = PpmUsulan::updateOrCreate(
            ['kode_usulan' => 'USL-2026-DEMO-MONEV'],
            [
                'id_pengusul' => $dosen->id,
                'id_skema_bima' => $skemaPdp->id,
                'id_periode_hibah' => $periode->id,
                'judul_usulan' => 'Penerapan Model Convolutional Neural Network (CNN) untuk Deteksi Dini Penyakit Tanaman Cabai',
                'rumpun_ilmu_level_1' => 'Sains & Matematika',
                'rumpun_ilmu_level_2' => 'Ilmu Komputer',
                'rumpun_ilmu_level_3' => 'Computer Vision',
                'fokus_rirn' => 'Pangan & Pertanian',
                'target_tkt' => 3,
                'ringkasan_substansi' => 'Klasifikasi citra daun tanaman menggunakan deep learning terkompresi pada perangkat mobile.',
                'total_rab' => 20000000.00,
                'skor_akhir' => 610.00,
                'status' => 'Ongoing',
                'submitted_at' => now()->subMonths(3),
            ]
        );

        $kontrak2 = PpmKontrak::updateOrCreate(
            ['id_usulan' => $usulan2->id],
            [
                'nomor_sk' => '046/SK-PEMENANG/P3M-UHN/' . date('Y'),
                'nomor_kontrak' => '089/SPK-BIMA/P3M-UHN/' . date('Y'),
                'tanggal_sk' => now()->subMonths(3),
                'tanggal_kontrak' => now()->subMonths(3),
                'pagu_disetujui' => 20000000.00,
                'dana_termin_1' => 14000000.00,
                'dana_termin_2' => 6000000.00,
                'nama_bank' => 'Bank BNI',
                'nomor_rekening' => '0398817263',
                'nama_pemilik_rekening' => $dosen->name,
                'file_buku_tabungan' => 'tabungan/sample.pdf',
                'rekening_verified_at' => now()->subMonths(3),
                'rekening_verified_by' => $keuangan?->id,
                'signed_by_kepala' => true,
                'signed_by_kepala_at' => now()->subMonths(3),
                'signed_by_pengusul' => true,
                'signed_by_pengusul_at' => now()->subMonths(3),
                'status' => 'ongoing',
                'verification_token' => bin2hex(random_bytes(32)),
            ]
        );

        PpmPencairanDana::updateOrCreate(
            ['id_kontrak' => $kontrak2->id, 'termin' => 1],
            [
                'persentase' => 70.00,
                'jumlah_dana' => 14000000.00,
                'nomor_referensi' => 'TRF-BNI-2026-0422',
                'tanggal_transfer' => now()->subMonths(2),
                'status_pencairan' => 'transferred',
                'processed_by' => $keuangan?->id,
                'processed_at' => now()->subMonths(2),
            ]
        );

        // Monev status 'submitted' - READY FOR REVIEWER TO EVALUATE!
        PpmMonevKemajuan::updateOrCreate(
            ['id_usulan' => $usulan2->id],
            [
                'file_laporan_kemajuan' => 'monev/demo/laporan_kemajuan_sample.pdf',
                'file_sptb_70' => 'monev/demo/sptb_70_sample.pdf',
                'ringkasan_kemajuan' => 'Telah mengumpulkan 1.200 sampel citra daun cabai di 3 sentra hortikultura. Akurasi pengujian model mencapai 91.4% dan naskah artikel jurnal telah mencapai draf 80%.',
                'persentase_kemajuan' => 72.0,
                'status' => 'submitted',
            ]
        );

        // =========================================================================
        // SKENARIO 3: Usulan Siap Semhas & Laporan Akhir (US-10.3) serta Pelunasan (US-10.4)
        // Kode: USL-2026-DEMO-SEMHAS-AKHIR
        // =========================================================================
        $usulan3 = PpmUsulan::updateOrCreate(
            ['kode_usulan' => 'USL-2026-DEMO-COMPLETE'],
            [
                'id_pengusul' => $dosen->id,
                'id_skema_bima' => $skemaPdp->id,
                'id_periode_hibah' => $periode->id,
                'judul_usulan' => 'Rancang Bangun Sistem Pendukung Keputusan Penentuan Dosis Pupuk Berdasarkan Analisis Citra Spektral',
                'rumpun_ilmu_level_1' => 'Teknik & Rekayasa',
                'rumpun_ilmu_level_2' => 'Teknik Informatika',
                'rumpun_ilmu_level_3' => 'Sistem Cerdas',
                'fokus_rirn' => 'Pangan & Pertanian',
                'target_tkt' => 4,
                'ringkasan_substansi' => 'Aplikasi rekomendasi nutrisi tanaman presisi dengan integrasi citra multispektral drone.',
                'total_rab' => 25000000.00,
                'skor_akhir' => 630.00,
                'status' => 'Ongoing',
                'submitted_at' => now()->subMonths(4),
            ]
        );

        $kontrak3 = PpmKontrak::updateOrCreate(
            ['id_usulan' => $usulan3->id],
            [
                'nomor_sk' => '047/SK-PEMENANG/P3M-UHN/' . date('Y'),
                'nomor_kontrak' => '090/SPK-BIMA/P3M-UHN/' . date('Y'),
                'tanggal_sk' => now()->subMonths(4),
                'tanggal_kontrak' => now()->subMonths(4),
                'pagu_disetujui' => 25000000.00,
                'dana_termin_1' => 17500000.00,
                'dana_termin_2' => 7500000.00,
                'nama_bank' => 'Bank Mandiri',
                'nomor_rekening' => '1090018892011',
                'nama_pemilik_rekening' => $dosen->name,
                'file_buku_tabungan' => 'tabungan/sample.pdf',
                'rekening_verified_at' => now()->subMonths(4),
                'rekening_verified_by' => $keuangan?->id,
                'signed_by_kepala' => true,
                'signed_by_kepala_at' => now()->subMonths(4),
                'signed_by_pengusul' => true,
                'signed_by_pengusul_at' => now()->subMonths(4),
                'status' => 'ongoing',
                'verification_token' => bin2hex(random_bytes(32)),
            ]
        );

        PpmPencairanDana::updateOrCreate(
            ['id_kontrak' => $kontrak3->id, 'termin' => 1],
            [
                'persentase' => 70.00,
                'jumlah_dana' => 17500000.00,
                'nomor_referensi' => 'TRF-MDR-2026-0910',
                'tanggal_transfer' => now()->subMonths(3),
                'status_pencairan' => 'transferred',
                'processed_by' => $keuangan?->id,
                'processed_at' => now()->subMonths(3),
            ]
        );

        // Monev already evaluated Lanjut!
        PpmMonevKemajuan::updateOrCreate(
            ['id_usulan' => $usulan3->id],
            [
                'file_laporan_kemajuan' => 'monev/demo/laporan_kemajuan_sample.pdf',
                'file_sptb_70' => 'monev/demo/sptb_70_sample.pdf',
                'ringkasan_kemajuan' => 'Progres 75% selesai dengan luaran prototype aplikasi berjalan stabil.',
                'persentase_kemajuan' => 75.0,
                'id_reviewer' => $reviewer?->id,
                'skor_monev' => 89.0,
                'catatan_evaluasi' => 'Sangat baik, luaran sesuai target dan laporan keuangan tertib.',
                'rekomendasi' => 'Lanjut',
                'status' => 'evaluated',
                'evaluated_at' => now()->subMonth(),
            ]
        );

        // Semhas scheduled & completed with grade 92.5
        PpmSeminarHasil::updateOrCreate(
            ['id_usulan' => $usulan3->id],
            [
                'jadwal_seminar' => now()->subDays(7),
                'ruangan_or_link' => 'Ruang Sidang Utama LPPM Gd. A Lt. 2',
                'id_penguji_1' => $reviewer?->id,
                'id_penguji_2' => $dosen?->id,
                'skor_seminar' => 92.5,
                'catatan_penguji' => 'Presentasi sangat memukau, luaran jurnal SINTA 2 sudah submitted.',
                'status_seminar' => 'completed',
            ]
        );

        // Laporan Akhir 100% & SPTB 100% uploaded -> Lembar Pengesahan is ACTIVE!
        $tokenAkhir = bin2hex(random_bytes(32));
        PpmLaporanAkhir::updateOrCreate(
            ['id_usulan' => $usulan3->id],
            [
                'file_laporan_akhir' => 'laporan-akhir/demo/laporan_akhir_sample.pdf',
                'file_sptb_100' => 'laporan-akhir/demo/sptb_100_sample.pdf',
                'ringkasan_hasil' => 'Seluruh tahapan riset 100% selesai. Luaran wajib artikel jurnal terindeks SINTA 2 berstatus Accepted dan hak cipta software terdaftar DJKI.',
                'verification_token' => $tokenAkhir,
                'is_approved_p3m' => true,
                'approved_by_p3m_at' => now()->subDays(2),
            ]
        );

        $this->command->info('Demo Data Epic 10 berhasil dibuat!');
    }
}

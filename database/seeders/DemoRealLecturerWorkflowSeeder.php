<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\PpmUsulan;
use App\Models\PpmSkemaBima;
use App\Models\PpmPeriodeHibah;
use App\Models\PpmKontrak;
use App\Models\PpmLogbook;
use App\Models\PpmMonevKemajuan;
use App\Models\PpmSeminarHasil;
use App\Models\PpmHki;
use App\Models\PpmPublikasiJurnal;
use App\Models\PpmKlaimReward;
use App\Models\PpmRewardDistribusi;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoRealLecturerWorkflowSeeder extends Seeder
{
    public function run(): void
    {
        $dosenRole = Role::firstOrCreate(['name' => 'Dosen / Pengusul']);
        $kplRole = Role::firstOrCreate(['name' => 'Kepala P3M']);

        // 1. Kepala P3M: Sharfina Febbi Handayani
        $sharfina = User::where('nidn_nim', '0617029201')->first()
            ?? User::where('email', 'kepalap3m@harkatnegeri.ac.id')->first();

        if (!$sharfina) {
            $sharfina = User::create([
                'name' => 'Sharfina Febbi Handayani, S.Kom., M.Kom.',
                'email' => 'kepalap3m@harkatnegeri.ac.id',
                'password' => Hash::make('0617029201'),
                'nidn_nim' => '0617029201',
                'sinta_id' => '6753189',
                'jabatan_fungsional' => 'Lektor',
                'sinta_score_3yr' => 235.75,
                'sinta_score_overall' => 500.50,
                'id_fakultas' => 4,
                'id_prodi' => 16,
                'is_otp_verified' => true,
                'email_verified_at' => now(),
            ]);
        } else {
            $sharfina->update([
                'name' => 'Sharfina Febbi Handayani, S.Kom., M.Kom.',
                'email' => 'kepalap3m@harkatnegeri.ac.id',
                'password' => Hash::make('0617029201'),
                'nidn_nim' => '0617029201',
                'sinta_id' => '6753189',
                'jabatan_fungsional' => 'Lektor',
                'sinta_score_3yr' => 235.75,
                'sinta_score_overall' => 500.50,
                'is_otp_verified' => true,
            ]);
        }

        // Assign both Kepala P3M and Dosen / Pengusul roles to Sharfina
        $sharfina->roles()->syncWithoutDetaching([$kplRole->id, $dosenRole->id]);

        // 2. Real Lecturer: Ginanjar Wiro Sasmito
        $ginanjar = User::where('nidn_nim', '0613028601')->first()
            ?? User::where('email', 'dosen.0613028601@harkatnegeri.ac.id')->first();
        if ($ginanjar) {
            $ginanjar->update(['password' => Hash::make('0613028601'), 'is_otp_verified' => true]);
            $ginanjar->roles()->syncWithoutDetaching([$dosenRole->id]);
        }

        // 3. Real Lecturer: Ida Farida
        $ida = User::where('nidn_nim', '0626017902')->first()
            ?? User::where('email', 'dosen.0626017902@harkatnegeri.ac.id')->first();
        if ($ida) {
            $ida->update(['password' => Hash::make('0626017902'), 'is_otp_verified' => true]);
            $ida->roles()->syncWithoutDetaching([$dosenRole->id]);
        }

        // 4. Real Lecturer: Slamet Wiyono
        $slamet = User::where('nidn_nim', '0626059001')->first()
            ?? User::where('email', 'dosen.0626059001@harkatnegeri.ac.id')->first();
        if ($slamet) {
            $slamet->update(['password' => Hash::make('0626059001'), 'is_otp_verified' => true]);
            $slamet->roles()->syncWithoutDetaching([$dosenRole->id]);
        }

        // Active period and schemes
        $periode = PpmPeriodeHibah::where('is_active', true)->first()
            ?? PpmPeriodeHibah::first();
        $skemaTerapan = PpmSkemaBima::where('nama_skema', 'like', '%Terapan%')->first()
            ?? PpmSkemaBima::first();
        $skemaFundamental = PpmSkemaBima::where('nama_skema', 'like', '%Fundamental%')->first()
            ?? PpmSkemaBima::first();

        // -------------------------------------------------------------
        // Proposal A: Ginanjar Wiro Sasmito -> Status SUBMITTED (Antrean LPPM)
        // -------------------------------------------------------------
        if ($ginanjar) {
            PpmUsulan::updateOrCreate(
                ['kode_usulan' => 'USL-2026-GWS-01'],
                [
                    'id_pengusul' => $ginanjar->id,
                    'id_skema_bima' => $skemaTerapan->id,
                    'id_periode_hibah' => $periode->id,
                    'judul_usulan' => 'Implementasi Deep Reinforcement Learning untuk Optimasi Rute Logistik Maritim Nusantara',
                    'rumpun_ilmu_level_1' => 'Teknologi & Rekayasa',
                    'rumpun_ilmu_level_2' => 'Ilmu Komputer',
                    'rumpun_ilmu_level_3' => 'Kecerdasan Buatan (AI)',
                    'fokus_rirn' => 'Kemaritiman & Kelautan',
                    'target_tkt' => 4,
                    'total_rab' => 24500000,
                    'status' => 'Submitted',
                    'submitted_at' => now()->subHours(4),
                    'ringkasan_substansi' => 'Penelitian ini mengkaji penerapan Deep Q-Networks (DQN) pada pemodelan navigasi armada pelayaran logistik perairan nusantara guna mereduksi konsumsi energi dan emisi karbon secara real-time. Prototipe terhubung dengan data AIS dan BMKG.',
                ]
            );
        }

        // -------------------------------------------------------------
        // Proposal B: Ida Farida -> Status SUBMITTED (Antrean LPPM)
        // -------------------------------------------------------------
        if ($ida) {
            PpmUsulan::updateOrCreate(
                ['kode_usulan' => 'USL-2026-IDF-02'],
                [
                    'id_pengusul' => $ida->id,
                    'id_skema_bima' => $skemaFundamental->id,
                    'id_periode_hibah' => $periode->id,
                    'judul_usulan' => 'Pengembangan Algoritma Deteksi Dini Kanker Payudara Berbasis Deep CNN pada Citra Mammogram Digital',
                    'rumpun_ilmu_level_1' => 'Ilmu Kesehatan',
                    'rumpun_ilmu_level_2' => 'Informatika Medis',
                    'rumpun_ilmu_level_3' => 'Pengolahan Citra Digital',
                    'fokus_rirn' => 'Kesehatan & Obat-obatan',
                    'target_tkt' => 3,
                    'total_rab' => 22000000,
                    'status' => 'Submitted',
                    'submitted_at' => now()->subHours(2),
                    'ringkasan_substansi' => 'Penelitian ini bertujuan mengembangkan model computer-aided detection (CAD) berbasis Vision Transformer dan Convolutional Neural Networks untuk segmentasi mikrokalsifikasi abnormal pada citra mammografi medis dengan akurasi target > 94%.',
                ]
            );
        }

        // -------------------------------------------------------------
        // Proposal C: Slamet Wiyono -> Status ONGOING (Siap ACC Semhas)
        // -------------------------------------------------------------
        if ($slamet) {
            $usulanC = PpmUsulan::updateOrCreate(
                ['kode_usulan' => 'USL-2026-SW-03'],
                [
                    'id_pengusul' => $slamet->id,
                    'id_skema_bima' => $skemaTerapan->id,
                    'id_periode_hibah' => $periode->id,
                    'judul_usulan' => 'Sistem Telemetri Kualitas Air Tambak Udang Berbasis Edge AI dan Transmisi LoRaWAN',
                    'rumpun_ilmu_level_1' => 'Teknologi & Rekayasa',
                    'rumpun_ilmu_level_2' => 'Teknik Elektro & Instrumentasi',
                    'rumpun_ilmu_level_3' => 'Internet of Things (IoT)',
                    'fokus_rirn' => 'Pangan & Pertanian',
                    'target_tkt' => 5,
                    'total_rab' => 25000000,
                    'status' => 'Ongoing',
                    'submitted_at' => now()->subMonths(5),
                    'ringkasan_substansi' => 'Riset implementatif pemantauan pH, oksigen terlarut, dan salinitas air tambak secara otonom dengan transmisi data jarak jauh nirkabel LoRaWAN berdaya ultra-rendah.',
                ]
            );

            // Kontrak SPK
            PpmKontrak::updateOrCreate(
                ['id_usulan' => $usulanC->id],
                [
                    'nomor_kontrak' => 'SPK/LPPM-UHN/2026/088',
                    'nomor_sk' => 'SK-REKTOR/UHN/2026/088',
                    'tanggal_sk' => now()->subMonths(4),
                    'tanggal_kontrak' => now()->subMonths(4),
                    'pagu_disetujui' => 25000000,
                    'dana_termin_1' => 17500000,
                    'dana_termin_2' => 7500000,
                    'signed_by_kepala' => true,
                    'signed_by_pengusul' => true,
                    'nama_bank' => 'Bank Mandiri',
                    'nomor_rekening' => '1370019283741',
                    'nama_pemilik_rekening' => 'Slamet Wiyono',
                    'status' => 'Signed',
                    'verification_token' => Str::random(32),
                ]
            );

            // Monev 70%
            PpmMonevKemajuan::updateOrCreate(
                ['id_usulan' => $usulanC->id],
                [
                    'file_laporan_kemajuan' => 'monev/demo/laporan_kemajuan_sample.pdf',
                    'file_sptb_70' => 'monev/demo/sptb_70_sample.pdf',
                    'ringkasan_kemajuan' => 'Pelaksanaan lapangan pengujian sensor tambak berjalan sangat baik dan sesuai target RAB. Siap melaksanakan seminar hasil riset.',
                    'persentase_kemajuan' => 75.0,
                    'status' => 'evaluated',
                    'evaluated_at' => now()->subWeeks(2),
                    'skor_monev' => 88.5,
                    'catatan_evaluasi' => 'Pelaksanaan lapangan berjalan sangat baik dan sesuai target RAB. Siap melaksanakan seminar hasil riset.',
                ]
            );

            // Seminar Hasil Menunggu ACC
            PpmSeminarHasil::updateOrCreate(
                ['id_usulan' => $usulanC->id],
                [
                    'jadwal_seminar' => now()->subDays(1),
                    'ruangan_or_link' => 'Ruang Sidang Utama P3M Gedung Rektorat Lt. 2',
                    'skor_seminar' => null,
                    'catatan_penguji' => null,
                    'status_seminar' => 'scheduled',
                ]
            );
        }

        // -------------------------------------------------------------
        // Proposal D: Sharfina Febbi Handayani -> Dosen Peneliti ONGOING
        // -------------------------------------------------------------
        $usulanD = PpmUsulan::updateOrCreate(
            ['kode_usulan' => 'USL-2026-SFH-04'],
            [
                'id_pengusul' => $sharfina->id,
                'id_skema_bima' => $skemaTerapan->id,
                'id_periode_hibah' => $periode->id,
                'judul_usulan' => 'Rancang Bangun Framework Keamanan Siber Zero Trust Architecture pada Infrastruktur Cloud Hybrid Perguruan Tinggi',
                'rumpun_ilmu_level_1' => 'Teknologi & Rekayasa',
                'rumpun_ilmu_level_2' => 'Ilmu Komputer',
                'rumpun_ilmu_level_3' => 'Keamanan Siber',
                'fokus_rirn' => 'Teknologi Informasi & Komunikasi',
                'target_tkt' => 5,
                'total_rab' => 25000000,
                'status' => 'Ongoing',
                'submitted_at' => now()->subMonths(3),
                'ringkasan_substansi' => 'Pengembangan framework Zero Trust Architecture (ZTA) yang memverifikasi identitas pengguna, perangkat, dan konteks akses secara berkelanjutan (continuous authentication) guna memitigasi serangan lateral movement pada data center kampus.',
            ]
        );

        // Kontrak SPK Sharfina
        PpmKontrak::updateOrCreate(
            ['id_usulan' => $usulanD->id],
            [
                'nomor_kontrak' => 'SPK/LPPM-UHN/2026/015',
                'nomor_sk' => 'SK-REKTOR/UHN/2026/015',
                'tanggal_sk' => now()->subMonths(3),
                'tanggal_kontrak' => now()->subMonths(3),
                'pagu_disetujui' => 25000000,
                'dana_termin_1' => 17500000,
                'dana_termin_2' => 7500000,
                'signed_by_kepala' => true,
                'signed_by_pengusul' => true,
                'nama_bank' => 'Bank BNI',
                'nomor_rekening' => '0821948271',
                'nama_pemilik_rekening' => 'Sharfina Febbi Handayani',
                'status' => 'Signed',
                'verification_token' => Str::random(32),
            ]
        );

        // Logbook Harian Sharfina (Total 65% - Diatas ambang batas 50% untuk unduh PDF!)
        PpmLogbook::where('id_usulan', $usulanD->id)->delete();
        $logbooks = [
            ['tanggal' => now()->subWeeks(8)->format('Y-m-d'), 'aktivitas' => 'Studi literatur standar NIST SP 800-207 Zero Trust Architecture dan pemetaan topologi jaringan cloud kampus.', 'persentase_capaian' => 15.0],
            ['tanggal' => now()->subWeeks(6)->format('Y-m-d'), 'aktivitas' => 'Konfigurasi Identity Provider (IdP) Keycloak dan Policy Decision Point (PDP) pada kluster server pengujian.', 'persentase_capaian' => 30.0],
            ['tanggal' => now()->subWeeks(4)->format('Y-m-d'), 'aktivitas' => 'Penyusunan aturan kontrol akses microsegmentation dan uji penetrasi serangan lateral movement simulatif.', 'persentase_capaian' => 50.0],
            ['tanggal' => now()->subWeeks(2)->format('Y-m-d'), 'aktivitas' => 'Pengukuran latency autentikasi TLS 1.3 mTLS dan evaluasi performa throughput data jaringan sebesar 10 Gbps.', 'persentase_capaian' => 65.0],
        ];
        foreach ($logbooks as $log) {
            PpmLogbook::create([
                'id_usulan' => $usulanD->id,
                'tanggal' => $log['tanggal'],
                'aktivitas' => $log['aktivitas'],
                'persentase_capaian' => $log['persentase_capaian'],
                'created_by' => $sharfina->id,
            ]);
        }

        // Publikasi Jurnal Sharfina
        $publikasi = PpmPublikasiJurnal::updateOrCreate(
            ['doi' => '10.1109/ACCESS.2026.3190821'],
            [
                'user_id' => $sharfina->id,
                'judul_artikel' => 'Adaptive Threat Hunting in Cloud Multi-Tenant Architecture Using Deep Attention Networks',
                'nama_jurnal' => 'IEEE Access',
                'issn' => '2169-3536',
                'kategori_peringkat' => 'Scopus Q1',
                'tahun_terbit' => 2026,
                'volume_nomor' => 'Vol. 14, No. 2, pp. 412-425',
                'url_artikel' => 'https://doi.org/10.1109/ACCESS.2026.3190821',
                'file_naskah' => 'publikasi_naskah/sample_paper.pdf',
                'metadata_source' => 'crossref',
                'jumlah_penulis' => 2,
                'peran_penulis' => 'Penulis Pertama',
                'is_claimed_reward' => true,
            ]
        );

        // Klaim Reward Sharfina
        $klaim = PpmKlaimReward::updateOrCreate(
            ['nomor_klaim' => 'REW-2026-SFH-001'],
            [
                'user_id' => $sharfina->id,
                'jenis_klaim' => 'Publikasi',
                'id_publikasi' => $publikasi->id,
                'kategori_insentif' => 'Jurnal Internasional Bereputasi (Scopus Q1)',
                'tarif_dasar_sk' => 10000000,
                'total_reward' => 10000000,
                'status_klaim' => 'Submitted',
            ]
        );

        // Distribusi 100% Utuh
        PpmRewardDistribusi::where('id_klaim_reward', $klaim->id)->delete();
        PpmRewardDistribusi::create([
            'id_klaim_reward' => $klaim->id,
            'user_id' => $sharfina->id,
            'nama_penulis' => 'Sharfina Febbi Handayani, S.Kom., M.Kom.',
            'nidn_nim' => '0617029201',
            'email' => 'kepalap3m@harkatnegeri.ac.id',
            'peran_penulis' => 'Penulis Pertama',
            'persentase' => 70.0,
            'nominal_bagian' => 7000000,
            'nama_bank' => 'Bank BNI',
            'nomor_rekening' => '0821948271',
            'nama_pemilik_rekening' => 'Sharfina Febbi Handayani',
            'status_transfer' => 'Pending',
        ]);
        PpmRewardDistribusi::create([
            'id_klaim_reward' => $klaim->id,
            'user_id' => $ginanjar ? $ginanjar->id : null,
            'nama_penulis' => 'Ginanjar Wiro Sasmito, M.Kom.',
            'nidn_nim' => '0613028601',
            'email' => 'dosen.0613028601@harkatnegeri.ac.id',
            'peran_penulis' => 'Anggota Penulis',
            'persentase' => 30.0,
            'nominal_bagian' => 3000000,
            'nama_bank' => 'Bank Mandiri',
            'nomor_rekening' => '1370018273641',
            'nama_pemilik_rekening' => 'Ginanjar Wiro Sasmito',
            'status_transfer' => 'Pending',
        ]);

        // Sentra HKI Ginanjar Wiro Sasmito
        if ($ginanjar) {
            PpmHki::updateOrCreate(
                ['nomor_permohonan' => 'EC00202619482'],
                [
                    'user_id' => $ginanjar->id,
                    'jenis_hki' => 'Hak Cipta',
                    'judul_hki' => 'Sistem Informasi Manajemen Presensi Berbasis Pengenalan Wajah dan Geofencing (SIMPRES UHN)',
                    'tanggal_permohonan' => now()->subWeeks(3),
                    'pemegang_hak' => 'Universitas Harkat Negeri',
                    'file_sertifikat' => 'hki_sertifikat/sample_sertifikat.pdf',
                    'status_hki' => 'Pending_verification',
                    'is_claimed_reward' => false,
                ]
            );
        }

        $this->command->info('Demo workflow with real lecturers and Sharfina Febbi Handayani seeded successfully!');
    }
}

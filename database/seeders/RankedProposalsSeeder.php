<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\PpmSkemaBima;
use App\Models\PpmPeriodeHibah;
use App\Models\PpmUsulan;
use App\Models\PpmPenugasanReviewer;
use App\Models\PpmPenilaianReviewer;

class RankedProposalsSeeder extends Seeder
{
    public function run(): void
    {
        $periode = PpmPeriodeHibah::where('is_active', true)->first() ?? PpmPeriodeHibah::first();
        if (!$periode) {
            return;
        }

        $skemaPtu = PpmSkemaBima::where('kode_skema', 'RIS-TERAPAN')->first() ?? PpmSkemaBima::first();
        $skemaFnd = PpmSkemaBima::where('kode_skema', 'FUNDAMENTAL')->first() ?? PpmSkemaBima::first();
        $skemaPdp = PpmSkemaBima::where('kode_skema', 'PDP')->first() ?? PpmSkemaBima::first();
        $skemaPkm = PpmSkemaBima::where('kode_skema', 'PKM')->first() ?? PpmSkemaBima::first();

        $dosen1 = User::where('email', 'dosen@harkatnegeri.ac.id')->first();
        $dosen2 = User::where('email', 'dosen2@harkatnegeri.ac.id')->first();
        $dosen3 = User::where('email', 'dosen3@harkatnegeri.ac.id')->first();
        $kaprodi = User::where('email', 'kaprodi@harkatnegeri.ac.id')->first();
        $dekan = User::where('email', 'dekan@harkatnegeri.ac.id')->first();

        $reviewer1 = User::where('email', 'reviewer@harkatnegeri.ac.id')->first();
        $reviewer2 = User::where('email', 'reviewer2@harkatnegeri.ac.id')->first();
        $reviewer3 = User::where('email', 'reviewer3@harkatnegeri.ac.id')->first();

        $proposalsData = [
            [
                'kode' => 'BIMA-2026-PTU-001',
                'judul' => 'Implementasi Platform Smart Precision Agriculture Berbasis IoT LoRaWAN dan Computer Vision Drone',
                'pengusul' => $dosen1,
                'skema' => $skemaPtu,
                'rab' => 65000000.0,
                'r1' => 672.0,
                'r2' => 668.0,
                'akhir' => 670.00,
                'tkt' => 5,
                'fokus' => 'Pangan & Pertanian',
            ],
            [
                'kode' => 'BIMA-2026-FND-002',
                'judul' => 'Sintesis Nanomaterial Karbon Aktif Berbasis Tempurung Kelapa Sawit untuk Superkapasitor Energi Terbarukan',
                'pengusul' => $dosen2,
                'skema' => $skemaFnd,
                'rab' => 48000000.0,
                'r1' => 655.0,
                'r2' => 645.0,
                'akhir' => 650.00,
                'tkt' => 3,
                'fokus' => 'Energi Baru & Terbarukan',
            ],
            [
                'kode' => 'BIMA-2026-PDP-003',
                'judul' => 'Pengembangan Aplikasi Mobile Edu-Health untuk Pemantauan Tumbuh Kembang Balita Stunting',
                'pengusul' => $dosen3,
                'skema' => $skemaPdp,
                'rab' => 24500000.0,
                'r1' => 638.0,
                'r2' => 630.0,
                'akhir' => 634.00,
                'tkt' => 3,
                'fokus' => 'Kesehatan & Obat',
            ],
            [
                'kode' => 'BIMA-2026-PKM-004',
                'judul' => 'Pemberdayaan Kelompok Tani Kopi Java Preanger Melalui Mesin Roasting Terkontrol Suhu Digital dan E-Commerce',
                'pengusul' => $kaprodi,
                'skema' => $skemaPkm,
                'rab' => 37500000.0,
                'r1' => 620.0,
                'r2' => 610.0,
                'akhir' => 615.00,
                'tkt' => 5,
                'fokus' => 'Ekonomi & Industri Kreatif',
            ],
            [
                'kode' => 'BIMA-2026-FND-005',
                'judul' => 'Model Ekonometrika Ketahanan Finansial UMKM Berbasis Fintech Lending dan Literasi Keuangan Digital',
                'pengusul' => $dekan,
                'skema' => $skemaFnd,
                'rab' => 47000000.0,
                'r1' => 595.0,
                'r2' => 591.0,
                'akhir' => 593.00,
                'tkt' => 3,
                'fokus' => 'Sosial Humaniora & Seni',
            ],
            [
                'kode' => 'BIMA-2026-PDP-006',
                'judul' => 'Rancang Bangun Alat Monitoring Kualitas Air Tambak Udang Otomatis Berbasis Sensor Multiparameter',
                'pengusul' => $dosen1,
                'skema' => $skemaPdp,
                'rab' => 23000000.0,
                'r1' => 585.0,
                'r2' => 579.0,
                'akhir' => 582.00,
                'tkt' => 3,
                'fokus' => 'Kemaritiman & Kelautan',
            ],
            [
                'kode' => 'BIMA-2026-PTU-007',
                'judul' => 'Prototipe Mini Turbin Angin Sumbu Vertikal (VAWT) Efisiensi Tinggi untuk Kawasan Pesisir Terpencil',
                'pengusul' => $dosen2,
                'skema' => $skemaPtu,
                'rab' => 65000000.0,
                'r1' => 575.0,
                'r2' => 565.0,
                'akhir' => 570.00,
                'tkt' => 6,
                'fokus' => 'Energi Baru & Terbarukan',
            ],
            [
                'kode' => 'BIMA-2026-PKM-008',
                'judul' => 'Optimalisasi Pengelolaan Limbah Padat Organik Pasar Tradisional Menjadi Pupuk Organik Cair Mikroba',
                'pengusul' => $dosen3,
                'skema' => $skemaPkm,
                'rab' => 36000000.0,
                'r1' => 540.0,
                'r2' => 536.0,
                'akhir' => 538.00,
                'tkt' => 4,
                'fokus' => 'Lingkungan Hidup',
            ],
        ];

        foreach ($proposalsData as $data) {
            $usulan = PpmUsulan::updateOrCreate(
                ['kode_usulan' => $data['kode']],
                [
                    'id_pengusul' => $data['pengusul']->id,
                    'id_skema_bima' => $data['skema']->id,
                    'id_periode_hibah' => $periode->id,
                    'judul_usulan' => $data['judul'],
                    'rumpun_ilmu_level_1' => 'Teknik & Rekayasa',
                    'fokus_rirn' => $data['fokus'],
                    'target_tkt' => $data['tkt'],
                    'ringkasan_substansi' => 'Ringkasan usulan riset kompetitif institusi UHN.',
                    'total_rab' => $data['rab'],
                    'skor_reviewer_1' => $data['r1'],
                    'skor_reviewer_2' => $data['r2'],
                    'skor_akhir' => $data['akhir'],
                    'status' => 'Reviewed',
                    'submitted_at' => now()->subDays(5),
                ]
            );

            // Seed sample completed review assignments
            if ($reviewer1 && $reviewer2) {
                $p1 = PpmPenugasanReviewer::updateOrCreate(
                    ['id_usulan' => $usulan->id, 'peran_reviewer' => 'reviewer_1'],
                    [
                        'id_reviewer' => $reviewer1->id,
                        'status_penugasan' => 'completed',
                        'assigned_at' => now()->subDays(4),
                        'completed_at' => now()->subDays(2),
                    ]
                );
                PpmPenilaianReviewer::updateOrCreate(
                    ['id_penugasan' => $p1->id],
                    [
                        'skor_kriteria' => ['k1' => 6.5, 'k2' => 6.8, 'k3' => 6.7],
                        'total_skor' => $data['r1'],
                        'komentar_kualitatif' => 'Usulan memiliki metodologi yang sangat solid dan relevansi tinggi dengan Renstra institusi.',
                        'rekomendasi' => 'layak',
                        'is_locked' => true,
                        'submitted_at' => now()->subDays(2),
                    ]
                );

                $p2 = PpmPenugasanReviewer::updateOrCreate(
                    ['id_usulan' => $usulan->id, 'peran_reviewer' => 'reviewer_2'],
                    [
                        'id_reviewer' => $reviewer2->id,
                        'status_penugasan' => 'completed',
                        'assigned_at' => now()->subDays(4),
                        'completed_at' => now()->subDays(1),
                    ]
                );
                PpmPenilaianReviewer::updateOrCreate(
                    ['id_penugasan' => $p2->id],
                    [
                        'skor_kriteria' => ['k1' => 6.4, 'k2' => 6.7, 'k3' => 6.6],
                        'total_skor' => $data['r2'],
                        'komentar_kualitatif' => 'RAB efisien dan terarah. Luaran wajib dan tambahan sangat terukur.',
                        'rekomendasi' => 'layak',
                        'is_locked' => true,
                        'submitted_at' => now()->subDays(1),
                    ]
                );
            }
        }
    }
}

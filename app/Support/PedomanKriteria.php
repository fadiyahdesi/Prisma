<?php

namespace App\Support;

use App\Models\RefTarifRewardSk;
use Illuminate\Support\Arr;

/**
 * Satu sumber kebenaran untuk kriteria yang berasal dari pedoman eksternal:
 *  - Matriks reward luaran penelitian/PkM (Dispo PPM No. 373.03/PPM.HN/X/2026).
 *  - Indikator monev internal pengabdian skema PKM (Borang Evaluasi + Indikator Monev PKM).
 *
 * Data ini sengaja dipisah dari logika bisnis agar bisa dipakai seeder, service,
 * dan tampilan tanpa saling duplikat.
 */
class PedomanKriteria
{
    public const NOMOR_SK = 'SK-REKTOR/UHN/2026/015';

    public const SUMBER_REWARD = 'Disposisi PPM UHN No. 373.03/PPM.HN/X/2026 — Penyesuaian Reward Luaran 2026';

    public const SUMBER_INDIKATOR = 'Borang Evaluasi Monev Internal PKM & Indikator Monev PKM, Universitas Harkat Negeri';

    public const URUTAN_INDEKS = ['Scopus', 'SINTA'];

    /**
     * Matriks tarif reward hasil penyesuaian PPM 2026.
     */
    public static function reward(): array
    {
        return [
            [
                'kategori' => 'Publikasi',
                'sub_kategori' => 'Scopus Q1',
                'nama' => 'Artikel Ilmiah Jurnal Internasional Bereputasi Scopus Q1',
                'nominal' => 15000000,
                'poin' => 24,
                'batas' => '2 klaim per semester',
                'batas_jumlah' => 2,
                'batas_periode' => 'semester',
                'syarat' => 'Penulis pertama dosen tetap UHN. Peringkat tertinggi yang diakui bila suatu artikel terindeks lebih dari satu database.',
                'urutan_indeks' => 1,
            ],
            [
                'kategori' => 'Publikasi',
                'sub_kategori' => 'Scopus Q2',
                'nama' => 'Artikel Ilmiah Jurnal Internasional Bereputasi Scopus Q2',
                'nominal' => 10000000,
                'batas' => '2 klaim per semester',
                'batas_jumlah' => 2,
                'batas_periode' => 'semester',
                'syarat' => 'Penulis pertama dosen tetap UHN. Peringkat tertinggi yang diakui bila suatu artikel terindeks lebih dari satu database.',
                'urutan_indeks' => 2,
            ],
            [
                'kategori' => 'Publikasi',
                'sub_kategori' => 'Scopus Q3',
                'nama' => 'Artikel Ilmiah Jurnal Internasional Bereputasi Scopus Q3',
                'nominal' => 7000000,
                'batas' => '2 klaim per semester',
                'batas_jumlah' => 2,
                'batas_periode' => 'semester',
                'syarat' => 'Penulis pertama dosen tetap UHN. Peringkat tertinggi yang diakui bila suatu artikel terindeks lebih dari satu database.',
                'urutan_indeks' => 3,
            ],
            [
                'kategori' => 'Publikasi',
                'sub_kategori' => 'Scopus Q4',
                'nama' => 'Artikel Ilmiah Jurnal Internasional Bereputasi Scopus Q4',
                'nominal' => 5000000,
                'batas' => '2 klaim per semester',
                'batas_jumlah' => 2,
                'batas_periode' => 'semester',
                'syarat' => 'Penulis pertama dosen tetap UHN. Peringkat tertinggi yang diakui bila suatu artikel terindeks lebih dari satu database.',
                'urutan_indeks' => 4,
            ],
            [
                'kategori' => 'Publikasi',
                'sub_kategori' => 'Prosiding Internasional (Scopus/DOAJ/CABI)',
                'nama' => 'Artikel Ilmiah pada Prosiding Internasional Bereputasi Sedang',
                'nominal' => 3500000,
                'batas' => '1 klaim per semester',
                'batas_jumlah' => 1,
                'batas_periode' => 'semester',
                'syarat' => 'Penulis pertama dosen tetap UHN. Berlaku juga untuk jurnal internasional terindeks DOAJ/CABI sesuai pedoman.',
                'urutan_indeks' => 5,
            ],
            [
                'kategori' => 'Publikasi',
                'sub_kategori' => 'SINTA 1',
                'nama' => 'Artikel Ilmiah Jurnal Nasional Terindeks SINTA Peringkat 1',
                'nominal' => 2500000,
                'batas' => '1 klaim per semester',
                'batas_jumlah' => 1,
                'batas_periode' => 'semester',
                'syarat' => 'Penulis pertama dosen tetap UHN. Scopus lebih diutamakan jika artikel terindeks keduanya.',
                'urutan_indeks' => 6,
            ],
            [
                'kategori' => 'Publikasi',
                'sub_kategori' => 'SINTA 2',
                'nama' => 'Artikel Ilmiah Jurnal Nasional Terindeks SINTA Peringkat 2',
                'nominal' => 2000000,
                'batas' => '1 klaim per semester',
                'batas_jumlah' => 1,
                'batas_periode' => 'semester',
                'syarat' => 'Penulis pertama dosen tetap UHN. Scopus lebih diutamakan jika artikel terindeks keduanya.',
                'urutan_indeks' => 7,
            ],
            [
                'kategori' => 'Publikasi',
                'sub_kategori' => 'SINTA 3',
                'nama' => 'Artikel Ilmiah Jurnal Nasional Terindeks SINTA Peringkat 3',
                'nominal' => 1500000,
                'batas' => '1 klaim per semester',
                'batas_jumlah' => 1,
                'batas_periode' => 'semester',
                'syarat' => 'Penulis pertama dosen tetap UHN. Merupakan peringkat terendah yang masih diakui dalam matriks 2026.',
                'urutan_indeks' => 8,
            ],
            [
                'kategori' => 'Publikasi',
                'sub_kategori' => 'Buku Ajar ISBN',
                'nama' => 'Buku Ajar ber-ISBN',
                'nominal' => 1000000,
                'poin' => 20,
                'batas' => '1 klaim per semester',
                'batas_jumlah' => 1,
                'batas_periode' => 'semester',
                'syarat' => 'Penulis pertama. Wajib melampirkan buku cetak/soft file dan buku terkait mata kuliah yang diajarkan di UHN.',
                'urutan_indeks' => 9,
            ],
            [
                'kategori' => 'Publikasi',
                'sub_kategori' => 'Buku Monograf ISBN',
                'nama' => 'Buku Monograf ber-ISBN',
                'nominal' => 1000000,
                'poin' => 20,
                'batas' => '1 klaim per semester',
                'batas_jumlah' => 1,
                'batas_periode' => 'semester',
                'syarat' => 'Penulis pertama. Wajib melampirkan buku cetak/soft file dan buku terkait mata kuliah yang diajarkan di UHN.',
                'urutan_indeks' => 10,
            ],
            [
                'kategori' => 'Publikasi',
                'sub_kategori' => 'Buku Referensi ISBN',
                'nama' => 'Buku Referensi ber-ISBN',
                'nominal' => 2000000,
                'poin' => 40,
                'batas' => '1 klaim per semester',
                'batas_jumlah' => 1,
                'batas_periode' => 'semester',
                'syarat' => 'Penulis pertama. Wajib melampirkan buku cetak/soft file dan buku terkait mata kuliah yang diajarkan di UHN.',
                'urutan_indeks' => 11,
            ],
            [
                'kategori' => 'HKI',
                'sub_kategori' => 'Paten Sederhana',
                'nama' => 'Paten Sederhana (status Diberi)',
                'nominal' => 5000000,
                'poin' => 28,
                'batas' => '1 klaim per tahun',
                'batas_jumlah' => 1,
                'batas_periode' => 'tahun',
                'syarat' => 'Status harus sudah Diberi dan sertifikat Paten Sederhana dari DJKI dilampirkan.',
                'urutan_indeks' => 12,
            ],
            [
                'kategori' => 'HKI',
                'sub_kategori' => 'Paten',
                'nama' => 'Paten (status Diberi)',
                'nominal' => 7500000,
                'poin' => 40,
                'batas' => '1 klaim per tahun',
                'batas_jumlah' => 1,
                'batas_periode' => 'tahun',
                'syarat' => 'Status harus sudah Diberi dan sertifikat Paten dari DJKI dilampirkan.',
                'urutan_indeks' => 13,
            ],
        ];
    }

    /**
     * Keterangan ringkas yang disimpan pada tabel tarif.
     */
    public static function keteranganTarif(array $row): string
    {
        $keterangan = sprintf('%s | Batas: %s | Syarat: %s', $row['nama'], $row['batas'], $row['syarat']);

        if (!empty($row['poin'])) {
            $keterangan .= " | Poin pendukung: {$row['poin']}";
        }

        return $keterangan;
    }

    /**
     * Cari baris pedoman setelah kategori lama dinormalisasi.
     */
    public static function rewardBySubKategori(string $kategori, string $subKategori): ?array
    {
        $canonical = self::aliasReward($subKategori);

        foreach (self::reward() as $row) {
            if ($row['kategori'] === $kategori && $row['sub_kategori'] === $canonical) {
                return $row;
            }
        }

        return null;
    }

    /**
     * Semua nama kategori (baru dan lama) yang mengacu ke satu sub-kategori pedoman.
     */
    public static function aliasKeluarga(string $subKategori): array
    {
        $canonical = self::aliasReward($subKategori);
        $keluarga = [$canonical];

        foreach (self::reward() as $row) {
            if (self::aliasReward($row['sub_kategori']) === $canonical) {
                $keluarga[] = $row['sub_kategori'];
            }
        }

        foreach (['internasional', 'jurnal internasional', 'internasional terindeks lainnya',
            'nasional terakreditasi', 'nasional tidak terakreditasi', 'jurnal nasional'] as $alias) {
            if (self::aliasReward($alias) === $canonical) {
                $keluarga[] = $alias;
            }
        }

        return array_values(array_unique($keluarga));
    }

    /**
     *Sinkronkan matriks pedoman ke tabel tarif reward.
     */
    public static function seedTarif(): void
    {
        $subKategoriAktif = [];

        foreach (self::reward() as $row) {
            RefTarifRewardSk::updateOrCreate(
                ['kategori' => $row['kategori'], 'sub_kategori' => $row['sub_kategori']],
                [
                    'nominal_insentif' => $row['nominal'],
                    'nomor_sk_rektor' => self::NOMOR_SK,
                    'keterangan' => self::keteranganTarif($row),
                    'is_active' => true,
                ]
            );

            $subKategoriAktif[] = $row['sub_kategori'];
        }

        // Kategori lama (SINTA 4, Internasional Terindeks Lainnya, dll.) tidak lagi
        // ditawarkan, tetapi barisnya sengaja dibiarkan untuk arsip klaim lama.
        RefTarifRewardSk::whereNotIn('sub_kategori', $subKategoriAktif)
            ->update(['is_active' => false]);
    }

    /**
     * Normalisasi kategori lama/hasil CSV ke sub-kategori pedoman 2026.
     */
    public static function aliasReward(string $subKategori): string
    {
        $aliases = [
            'internasional' => 'Prosiding Internasional (Scopus/DOAJ/CABI)',
            'jurnal internasional' => 'Prosiding Internasional (Scopus/DOAJ/CABI)',
            'internasional terindeks lainnya' => 'Prosiding Internasional (Scopus/DOAJ/CABI)',
            'scopus/ doaj/ cabi' => 'Prosiding Internasional (Scopus/DOAJ/CABI)',
            'prosiding internasional' => 'Prosiding Internasional (Scopus/DOAJ/CABI)',
            'nasional terakreditasi' => 'SINTA 3',
            'nasional tidak terakreditasi' => 'SINTA 3',
            'jurnal nasional' => 'SINTA 3',
            'buku ajar' => 'Buku Ajar ISBN',
            'buku monograf' => 'Buku Monograf ISBN',
            'buku referensi' => 'Buku Referensi ISBN',
            'paten biasa' => 'Paten',
            'paten granted' => 'Paten',
            'paten sederhana granted' => 'Paten Sederhana',
        ];

        return $aliases[strtolower(trim($subKategori))] ?? $subKategori;
    }

    /**
     * Indikator monev internal pengabdian. Saat ini yang diadopsi hanya skema PKM.
     * Skema lain sengaja dikembalikan ke input skor manual sampai pedoman resminya tersedia.
     *
     * Nilai item mengikuti "Indikator Monev PKM" (PDF). Skor komponen dihitung
     * sebagai jumlah nilai sub-indikator dibagi pembagi yang ditentukan borang.
     * Khusus video, PDF menuliskan pembagi 8 tetapi jumlah nilai maksimum item
     * adalah 100, sehingga pembagi 10 dipakai agar bobot maksimum tetap 10.
     */
    public static function monev(string $kodeSkema): array
    {
        $kode = strtoupper(trim($kodeSkema));

        if ($kode !== 'PKM') {
            return [
                'kode_skema' => $kode,
                'nama_skema' => null,
                'sumber' => null,
                'kelompok' => [],
                'fokus_tahun' => [],
                'komponen' => [],
            ];
        }

        $item = function (string $nama, array $penilaian): array {
            return ['nama' => $nama, 'penilaian' => $penilaian];
        };

        $komponen = function (string $nama, string $kelompok, int $maks, int $pembagi, string $deskripsi, array $items) use ($item): array {
            return [
                'nama' => $nama,
                'kelompok' => $kelompok,
                'bobot_maks' => $maks,
                'pembagi' => $pembagi,
                'deskripsi' => $deskripsi,
                'items' => $items,
            ];
        };

        return [
            'kode_skema' => 'PKM',
            'nama_skema' => 'Program Kemitraan Masyarakat (PKM)',
            'sumber' => self::SUMBER_INDIKATOR,
            'kelompok' => [
                'umum' => ['nama' => 'I. Penilaian Umum', 'maks' => 60],
                'khusus' => ['nama' => 'II. Penilaian Khusus PKM', 'maks' => 40],
            ],
            'fokus_tahun' => [
                'Tahun 1' => 'Pemetaan masalah dan implementasi solusi awal | Luaran: draft jurnal, baseline data mitra',
                'Tahun 2' => 'Penguatan dan validasi hasil solusi | Luaran: artikel SINTA/prosiding, data dampak terukur',
                'Tahun 3' => 'Hilirisasi hasil dan kemandirian mitra | Luaran: HKI, model kemitraan berkelanjutan',
            ],
            'komponen' => [
                'u1' => $komponen(
                    'Kehadiran dan Pelaksanaan Monev',
                    'umum',
                    10,
                    10,
                    'Kesiapan tim, kehadiran lengkap (tim, mahasiswa, mitra), ketepatan waktu, serta kesesuaian substansi usulan dengan implementasi lapangan.',
                    [
                        'i1' => $item('Kemampuan presentasi dan penguasaan materi usulan', [
                            'Presentasi kurang sistematis dan penyampaian materi kurang komunikatif' => 0,
                            'Presentasi sistematis atau penyampaian materi komunikatif' => 10,
                            'Presentasi sistematis dan penyampaian materi komunikatif' => 20,
                        ]),
                        'i2' => $item('Kehadiran Pelaksana', [
                            'Tim, mahasiswa, dan mitra tidak hadir lengkap' => 0,
                            'Hadir lengkap, tetapi ada stakeholder yang tidak dapat dikonfirmasi' => 10,
                            'Hadir lengkap dan dapat dikonfirmasi keseluruhannya' => 20,
                        ]),
                        'i3' => $item('Kesiapan dan Kelengkapan Pelaksana', [
                            'Tidak memiliki persiapan dalam pelaksanaan monev' => 0,
                            'Dokumen presentasi siap, persiapan mitra dan waktu kurang' => 10,
                            'Dokumen, mitra, dan waktu dipersiapkan dengan baik' => 20,
                        ]),
                        'i4' => $item('Ketepatan waktu', [
                            'Tim tidak hadir dalam monev' => 0,
                            'Terlambat hadir atau tidak mengikuti monev hingga selesai' => 10,
                            'Hadir tepat waktu dan mengikuti monev hingga selesai' => 20,
                        ]),
                        'i5' => $item('Kesesuaian substansi usulan dengan materi presentasi', [
                            'Substansi tidak sesuai' => 0,
                            'Substansi cukup sesuai' => 10,
                            'Substansi sesuai' => 20,
                        ]),
                    ]
                ),
                'u2' => $komponen(
                    'Artikel Publikasi Berita di Media Massa',
                    'umum',
                    10,
                    10,
                    'Artikel di media cetak/elektronik yang menyebutkan DPPM/LPPM, institusi, dan sumber pendanaan.',
                    [
                        'i1' => $item('Artikel publikasi berita pada media massa (cetak/elektronik)', [
                            'Tidak ada draft / tidak sesuai ketentuan / identitas tidak lengkap' => 0,
                            'Ada draft atau editing artikel sesuai ketentuan' => 10,
                            'Artikel sesuai ketentuan, hanya menunggu publish' => 20,
                            'Sudah terbit di media lokal, identitas lengkap' => 70,
                            'Sudah terbit di media nasional, identitas lengkap' => 100,
                        ]),
                    ]
                ),
                'u3' => $komponen(
                    'Keterlibatan Mahasiswa',
                    'umum',
                    10,
                    5,
                    'Mahasiswa terlibat aktif sesuai ketentuan dan memiliki pembagian tugas yang jelas.',
                    [
                        'i1' => $item('Keterlibatan mahasiswa sesuai ketentuan', [
                            'Tidak ada keterlibatan mahasiswa' => 0,
                            'Jumlah mahasiswa di bawah ketentuan' => 5,
                            'Sesuai ketentuan, pembagian tugas belum jelas' => 25,
                            'Sesuai ketentuan dan pembagian tugas jelas' => 50,
                        ]),
                    ]
                ),
                'u4' => $komponen(
                    'Karya Audio Visual (Video Kegiatan)',
                    'umum',
                    10,
                    10,
                    'Video kegiatan diunggah ke kanal YouTube resmi perguruan tinggi dengan kualitas baik, voice-over, subtitle, logo, dan informasi program.',
                    [
                        'i1' => $item('Channel Penayangan YouTube', [
                            'Tidak ada video / belum diunggah' => 0,
                            'YouTube bukan kanal resmi perguruan tinggi' => 10,
                            'YouTube resmi perguruan tinggi ketua tim' => 20,
                        ]),
                        'i2' => $item('Kualitas video', [
                            'Tidak ada video / belum diunggah di kanal resmi' => 0,
                            'Video di kanal resmi dengan resolusi di bawah 1080p' => 5,
                            'Video di kanal resmi dengan resolusi 1080p' => 15,
                        ]),
                        'i3' => $item('Bentuk Video', [
                            'Tidak ada video / belum diunggah di kanal resmi' => 0,
                            'Kumpulan foto/PowerPoint' => 1,
                            'Video pendek, bukan kumpulan foto/PowerPoint' => 10,
                        ]),
                        'i4' => $item('Voice Over dan Running Text/Text Title/Subtitle', [
                            'Tidak ada video / belum diunggah di kanal resmi' => 0,
                            'Tanpa voice over, subtitle, dan running text' => 5,
                            'Lengkap voice over, subtitle, dan running text' => 15,
                        ]),
                        'i5' => $item('Penyebutan Program dan Sumber Dana', [
                            'Tidak ada video / belum diunggah di kanal resmi' => 0,
                            'Nama program atau sumber dana belum benar / logo salah' => 1,
                            'Nama program dan sumber dana disebutkan benar' => 10,
                        ]),
                        'i6' => $item('Cerita dan Penggambaran', [
                            'Tidak ada video / belum diunggah di kanal resmi' => 0,
                            'Belum menggambarkan pelaksanaan dan hasil pengabdian' => 1,
                            'Menggambarkan pelaksanaan dan hasil pengabdian' => 10,
                        ]),
                        'i7' => $item('Daya Tarik, Transisi dan Stabilisasi', [
                            'Tidak ada video / belum diunggah di kanal resmi' => 0,
                            'Video kurang menarik' => 1,
                            'Video menarik dan bagus' => 10,
                        ]),
                        'i8' => $item('Jumlah viewers video', [
                            'Tidak ada viewers' => 0,
                            'Kurang dari 25 viewers' => 1,
                            '25–50 viewers' => 3,
                            '50–100 viewers' => 7,
                            'Lebih dari 100 viewers' => 10,
                        ]),
                    ]
                ),
                'u5' => $komponen(
                    'Karya Visual (Poster Kegiatan)',
                    'umum',
                    10,
                    10,
                    'Poster 160×60 cm portrait, mencantumkan sumber pendanaan dan logo Kemdiktisaintek, informatif serta menarik.',
                    [
                        'i1' => $item('Format Poster (ukuran, portrait, sumber dana, dan logo)', [
                            'Tidak ada poster / tidak sesuai ketentuan' => 0,
                            'Format sesuai, sumber dana dan logo belum dicantumkan' => 20,
                            'Format, sumber dana, dan logo sesuai ketentuan' => 40,
                        ]),
                        'i2' => $item('Isi/Substansi Poster', [
                            'Tidak ada poster / tidak sesuai ketentuan' => 0,
                            'Belum menggambarkan pelaksanaan dan hasil pengabdian' => 15,
                            'Menggambarkan pelaksanaan dan hasil pengabdian' => 30,
                        ]),
                        'i3' => $item('Daya Tarik, Warna dan Layout', [
                            'Tidak ada poster / tidak sesuai ketentuan' => 0,
                            'Warna monoton dan layout tidak rapi' => 10,
                            'Kurang menarik dari segi warna dan layout' => 15,
                            'Menarik dalam warna dan layout' => 30,
                        ]),
                    ]
                ),
                'u6' => $komponen(
                    'Kepatuhan terhadap Penggunaan Anggaran',
                    'umum',
                    10,
                    10,
                    'Penggunaan minimal 80% dana sesuai SBM 2024 dan panduan, dengan kwitansi/bukti dukung serta laporan lengkap.',
                    [
                        'i1' => $item('Dokumen Laporan Penggunaan Anggaran', [
                            'Tidak ada / belum diunggah / tanpa bukti dukung / bukti tidak sesuai' => 0,
                            'Laporan lengkap, sebagian bukti dukung tidak sesuai' => 4,
                            'Laporan lengkap dengan bukti dukung sesuai ketentuan' => 40,
                        ]),
                        'i2' => $item('Penggunaan Anggaran 80% dengan SBM 2024', [
                            'Tidak sesuai ketentuan SBM 2024' => 0,
                            'Sebagian komponen tidak sesuai SBM 2024' => 3,
                            'Sesuai ketentuan SBM 2024' => 30,
                        ]),
                        'i3' => $item('Penggunaan Anggaran 80% dengan Panduan dan Komponennya', [
                            'Tidak sesuai panduan dan komponen tidak pada tempatnya' => 0,
                            'Sebagian tidak sesuai panduan, komponen sudah pada tempatnya' => 6,
                            'Sesuai panduan dan komponen pada tempatnya' => 30,
                        ]),
                    ]
                ),
                'k1' => $komponen(
                    'Publikasi pada Jurnal Nasional Terakreditasi SINTA 1–6',
                    'khusus',
                    10,
                    10,
                    'Artikel pada jurnal nasional terakreditasi SINTA 1–6 sesuai ketentuan luaran.',
                    [
                        'i1' => $item('Publikasi pada jurnal nasional terakreditasi SINTA 1–6', [
                            'Tidak ada draft / terbit bukan SINTA / tidak sesuai ketentuan' => 0,
                            'Ada draft artikel sesuai ketentuan' => 10,
                            'Ada bukti terkirim sesuai ketentuan' => 20,
                            'Ada bukti diterima/direviu/revisi sesuai ketentuan' => 70,
                            'Terpublikasi nasional terindeks SINTA' => 100,
                        ]),
                    ]
                ),
                'k2' => $komponen(
                    'Pemberdayaan dan Keberdayaan Mitra',
                    'khusus',
                    30,
                    10,
                    'Peningkatan level keberdayaan mitra pada dua aspek kegiatan, disertai penerapan teknologi, kehadiran, dan peran tim.',
                    [
                        'i1' => $item('Peningkatan level keberdayaan aspek kegiatan ke-1', [
                            'Tidak ada peningkatan' => 0,
                            'Ada peningkatan, belum terkuantifikasi' => 25,
                            'Peningkatan terkuantifikasi disertai dasar dan hasil perhitungan' => 50,
                        ]),
                        'i2' => $item('Peningkatan level keberdayaan aspek kegiatan ke-2', [
                            'Tidak ada peningkatan' => 0,
                            'Ada peningkatan, belum terkuantifikasi' => 25,
                            'Peningkatan terkuantifikasi disertai dasar dan hasil perhitungan' => 50,
                        ]),
                        'i3' => $item('Persen peningkatan level keberdayaan bidang ke-1', [
                            'Tidak ada / tidak terkuantifikasi' => 0,
                            'Tidak memuaskan: peningkatan ≤20%' => 5,
                            'Cukup memuaskan: peningkatan 21–50%' => 12,
                            'Memuaskan: peningkatan 51–70%' => 20,
                            'Sangat memuaskan: peningkatan >70%' => 25,
                        ]),
                        'i4' => $item('Persen peningkatan level keberdayaan bidang ke-2', [
                            'Tidak ada / tidak terkuantifikasi' => 0,
                            'Tidak memuaskan: peningkatan ≤20%' => 5,
                            'Cukup memuaskan: peningkatan 21–50%' => 12,
                            'Memuaskan: peningkatan 51–70%' => 20,
                            'Sangat memuaskan: peningkatan >70%' => 25,
                        ]),
                        'i5' => $item('Penerapan teknologi dan inovasi', [
                            'Tidak ada / hanya satu bidang masalah' => 0,
                            'Dua bidang, tidak sesuai kebutuhan mitra' => 5,
                            'Sesuai kebutuhan, mulai digunakan mitra' => 15,
                            'Sesuai kebutuhan dan dioptimalkan mitra' => 35,
                            'Dioptimalkan mitra dan meningkatkan keberdayaan' => 50,
                        ]),
                        'i6' => $item('Kehadiran tim dan mahasiswa ke lokasi mitra', [
                            'Kurang dari 4 kali' => 0,
                            '4–7 kali' => 20,
                            'Lebih dari 7 kali' => 50,
                        ]),
                        'i7' => $item('Partisipasi dan peran tim serta mahasiswa', [
                            'Tidak semua memiliki peran / pembagian tugas tidak ada' => 0,
                            'Pembagian peran tidak sesuai kepakaran' => 20,
                            'Seluruh tim dan mahasiswa memiliki peran pemberdayaan' => 50,
                        ]),
                    ]
                ),
            ],
        ];
    }

    /**
     * Kerangka nilai awal untuk form penilaian terstruktur.
     */
    public static function defaultPenilaian(string $kodeSkema, ?array $existing = null): array
    {
        $penilaian = [];

        foreach (self::monev($kodeSkema)['komponen'] as $ck => $komponen) {
            $penilaian[$ck] = ['items' => []];

            foreach ($komponen['items'] as $ik => $item) {
                $penilaian[$ck]['items'][$ik] = Arr::get($existing, "{$ck}.items.{$ik}");
            }
        }

        return $penilaian;
    }

    /**
     * Hitung skor monev 0-100 dari nilai sub-indikator borang PKM.
     * Rumus tiap komponen: jumlah nilai item yang dipilih dibagi pembagi komponen.
     */
    public static function hitungSkorMonev(string $kodeSkema, array $penilaian): float
    {
        $total = 0.0;

        foreach (self::monev($kodeSkema)['komponen'] as $ck => $komponen) {
            $jumlah = 0.0;
            foreach ($komponen['items'] as $ik => $item) {
                $jumlah += (float) Arr::get($penilaian, "{$ck}.items.{$ik}", 0);
            }

            $skorKomponen = $jumlah / max(1, (int) $komponen['pembagi']);
            $total += min((float) $komponen['bobot_maks'], $skorKomponen);
        }

        return round(min(100, max(0, $total)), 2);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PpmSkemaBima;
use App\Models\PpmPeriodeHibah;

class BimaSkemaAndPeriodeSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed BIMA Schemes
        $schemes = [
            [
                'kode_skema' => 'PDP',
                'nama_skema' => 'Penelitian Dosen Pemula (PDP)',
                'kategori' => 'penelitian',
                'min_jafung' => ['Asisten Ahli', 'Lektor'],
                'min_sinta_3yr' => 50.0,
                'min_tkt' => 1,
                'max_tkt' => 3,
                'plafon_dana' => 25000000.00,
                'is_active' => true,
                'rubrik_penilaian' => [
                    [
                        'id' => 'c1',
                        'kriteria' => 'Kualifikasi & Rekam Jejak Tim Pengusul',
                        'bobot' => 20,
                        'deskripsi' => 'Kesesuaian kepakaran tim, publikasi SINTA 3 tahun terakhir, dan rekam jejak riset.',
                        'skala' => [
                            '1_2' => 'Sangat Kurang (Tanpa rekam jejak relevan)',
                            '3_4' => 'Cukup (Memiliki 1 artikel SINTA 3-6)',
                            '5_6' => 'Baik (Memiliki artikel SINTA 1-2 atau Scopus)',
                            '7' => 'Sangat Baik (Rekam jejak sangat unggul & relevan)'
                        ]
                    ],
                    [
                        'id' => 'c2',
                        'kriteria' => 'Urgensi & Kebaharuan Masalah (State of the Art)',
                        'bobot' => 25,
                        'deskripsi' => 'Kejelasan latar belakang, rumusan masalah, dan orisinalitas riset.',
                        'skala' => [
                            '1_2' => 'Sangat Kurang (Masalah tidak jelas & repetitif)',
                            '3_4' => 'Cukup (Kebaharuan terbatas)',
                            '5_6' => 'Baik (Kebaharuan jelas dengan pustaka acuan)',
                            '7' => 'Sangat Baik (State-of-the-art kuat & solutif)'
                        ]
                    ],
                    [
                        'id' => 'c3',
                        'kriteria' => 'Ketepatan Metode & Kesesuaian TKT',
                        'bobot' => 30,
                        'deskripsi' => 'Kelayakan rancangan penelitian, instrumen, dan pencapaian target TKT 1-3.',
                        'skala' => [
                            '1_2' => 'Sangat Kurang (Metode ambigu & tanpa tahapan)',
                            '3_4' => 'Cukup (Metode terstruktur tetapi kurang rinci)',
                            '5_6' => 'Baik (Rancangan tepat & sesuai TKT)',
                            '7' => 'Sangat Baik (Metodologi sangat presisi & teruji)'
                        ]
                    ],
                    [
                        'id' => 'c4',
                        'kriteria' => 'Target Luaran & Kelayakan RAB SBM',
                        'bobot' => 25,
                        'deskripsi' => 'Kesesuaian luaran wajib publikasi SINTA dan kewajaran rincian RAB SBM.',
                        'skala' => [
                            '1_2' => 'Sangat Kurang (RAB tidak efisien & luaran tidak jelas)',
                            '3_4' => 'Cukup (RAB sesuai SBM dengan luaran standar)',
                            '5_6' => 'Baik (Luaran menjanjikan & RAB terstruktur)',
                            '7' => 'Sangat Baik (RAB sangat efisien & luaran melebihi target)'
                        ]
                    ]
                ]
            ],
            [
                'kode_skema' => 'FUNDAMENTAL',
                'nama_skema' => 'Penelitian Fundamental (Hibah BIMA)',
                'kategori' => 'penelitian',
                'min_jafung' => ['Lektor', 'Lektor Kepala', 'Guru Besar / Profesor'],
                'min_sinta_3yr' => 150.0,
                'min_tkt' => 2,
                'max_tkt' => 6,
                'plafon_dana' => 150000000.00,
                'is_active' => true,
                'rubrik_penilaian' => [
                    [
                        'id' => 'c1',
                        'kriteria' => 'Rekam Jejak & Kualifikasi Tim Pengusul',
                        'bobot' => 25,
                        'deskripsi' => 'H-Index Scopus/Google Scholar dan publikasi bereputasi internasional.',
                        'skala' => [
                            '1_2' => 'Sangat Kurang',
                            '3_4' => 'Cukup',
                            '5_6' => 'Baik',
                            '7' => 'Sangat Baik (H-Index tinggi & bereputasi)'
                        ]
                    ],
                    [
                        'id' => 'c2',
                        'kriteria' => 'Keberlanjutan & State-of-the-Art Riset',
                        'bobot' => 25,
                        'deskripsi' => 'Kedalaman kajian pustaka ilmiah dan sumbangan pada keilmuan.',
                        'skala' => [
                            '1_2' => 'Sangat Kurang',
                            '3_4' => 'Cukup',
                            '5_6' => 'Baik',
                            '7' => 'Sangat Baik'
                        ]
                    ],
                    [
                        'id' => 'c3',
                        'kriteria' => 'Metodologi & Road Map Penelitian',
                        'bobot' => 30,
                        'deskripsi' => 'Peta jalan penelitian jangka panjang dan keterkaitan dengan fokus RIP UHN.',
                        'skala' => [
                            '1_2' => 'Sangat Kurang',
                            '3_4' => 'Cukup',
                            '5_6' => 'Baik',
                            '7' => 'Sangat Baik'
                        ]
                    ],
                    [
                        'id' => 'c4',
                        'kriteria' => 'Luaran Wajib Publikasi Scopus/SINTA',
                        'bobot' => 20,
                        'deskripsi' => 'Komitmen terbit pada Jurnal Berreputasi Scopus Q1-Q3 / SINTA 1-2.',
                        'skala' => [
                            '1_2' => 'Sangat Kurang',
                            '3_4' => 'Cukup',
                            '5_6' => 'Baik',
                            '7' => 'Sangat Baik'
                        ]
                    ]
                ]
            ],
            [
                'kode_skema' => 'HILIRISASI',
                'nama_skema' => 'Penelitian Hilirisasi & Terapan (BIMA Standard)',
                'kategori' => 'penelitian',
                'min_jafung' => ['Lektor Kepala', 'Guru Besar / Profesor'],
                'min_sinta_3yr' => 250.0,
                'min_tkt' => 4,
                'max_tkt' => 9,
                'plafon_dana' => 250000000.00,
                'is_active' => true,
                'rubrik_penilaian' => [
                    [
                        'id' => 'c1',
                        'kriteria' => 'Rekam Jejak & Kesiapan Mitra Industri',
                        'bobot' => 30,
                        'deskripsi' => 'Dukungan partisipasi & in-cash/in-kind dari mitra industri terverifikasi.',
                        'skala' => [
                            '1_2' => 'Sangat Kurang',
                            '3_4' => 'Cukup',
                            '5_6' => 'Baik',
                            '7' => 'Sangat Baik (Komitmen mitra sangat kuat)'
                        ]
                    ],
                    [
                        'id' => 'c2',
                        'kriteria' => 'Desain Produk & Pengujian TKT Terapan',
                        'bobot' => 30,
                        'deskripsi' => 'Kesiapan prototipe laik industri dan rencana validasi lingkungan relevan.',
                        'skala' => [
                            '1_2' => 'Sangat Kurang',
                            '3_4' => 'Cukup',
                            '5_6' => 'Baik',
                            '7' => 'Sangat Baik'
                        ]
                    ],
                    [
                        'id' => 'c3',
                        'kriteria' => 'Kelayakan Fasilitas & Peta Jalan Riset',
                        'bobot' => 20,
                        'deskripsi' => 'Ketersediaan laboratorium dan peralatan pendukung pendamping.',
                        'skala' => [
                            '1_2' => 'Sangat Kurang',
                            '3_4' => 'Cukup',
                            '5_6' => 'Baik',
                            '7' => 'Sangat Baik'
                        ]
                    ],
                    [
                        'id' => 'c4',
                        'kriteria' => 'Potensi Komersialisasi & Paten DJKI',
                        'bobot' => 20,
                        'deskripsi' => 'Prospek pendaftaran Paten/Hak Cipta dan bisnis komersial.',
                        'skala' => [
                            '1_2' => 'Sangat Kurang',
                            '3_4' => 'Cukup',
                            '5_6' => 'Baik',
                            '7' => 'Sangat Baik'
                        ]
                    ]
                ]
            ],
                        [
                'kode_skema' => 'PMP',
                'nama_skema' => 'Pemberdayaan Masyarakat Pemula (PMP)',
                'kategori' => 'pengabdian',
                'min_jafung' => json_encode(['Asisten Ahli', 'Lektor']),
                'min_sinta_3yr' => 0.0,
                'min_sinta_overall' => 0.0,
                'min_tkt' => null,
                'max_tkt' => null,
                'plafon_dana' => 25000000.00,
                'is_active' => true,
                'rubrik_penilaian' => [
                    [
                        'id' => 'c1',
                        'kriteria' => 'Analisis Situasi & Permasalahan Mitra',
                        'bobot' => 30,
                        'deskripsi' => 'Ketepatan identifikasi persoalan nyata di masyarakat/mitra sasaran.',
                        'skala' => [
                            '1_2' => 'Sangat Kurang',
                            '3_4' => 'Cukup',
                            '5_6' => 'Baik',
                            '7' => 'Sangat Baik'
                        ]
                    ],
                    [
                        'id' => 'c2',
                        'kriteria' => 'Solusi Teknologi & Metode Pelaksanaan',
                        'bobot' => 30,
                        'deskripsi' => 'Kepraktisan inovasi teknologi tepat guna yang ditransfer.',
                        'skala' => [
                            '1_2' => 'Sangat Kurang',
                            '3_4' => 'Cukup',
                            '5_6' => 'Baik',
                            '7' => 'Sangat Baik'
                        ]
                    ],
                    [
                        'id' => 'c3',
                        'kriteria' => 'Partisipasi Mitra & Keberlanjutan Program',
                        'bobot' => 20,
                        'deskripsi' => 'Keterlibatan aktif kelompok masyarakat dan rencana pasca-pengabdian.',
                        'skala' => [
                            '1_2' => 'Sangat Kurang',
                            '3_4' => 'Cukup',
                            '5_6' => 'Baik',
                            '7' => 'Sangat Baik'
                        ]
                    ],
                    [
                        'id' => 'c4',
                        'kriteria' => 'Target Luaran Media Massal & Video Kegiatan',
                        'bobot' => 20,
                        'deskripsi' => 'Publikasi media massa nasional, video YouTube, dan HKI modul.',
                        'skala' => [
                            '1_2' => 'Sangat Kurang',
                            '3_4' => 'Cukup',
                            '5_6' => 'Baik',
                            '7' => 'Sangat Baik'
                        ]
                    ]
                ]
            ],
            [
                'kode_skema' => 'PKM',
                'nama_skema' => 'Pemberdayaan Kemitraan Masyarakat (PKM)',
                'kategori' => 'pengabdian',
                'min_jafung' => json_encode(['Asisten Ahli', 'Lektor', 'Lektor Kepala', 'Guru Besar / Profesor']),
                'min_sinta_3yr' => 0.0,
                'min_sinta_overall' => 101.0,
                'min_tkt' => null,
                'max_tkt' => null,
                'plafon_dana' => 50000000.00,
                'is_active' => true,
                'rubrik_penilaian' => [
                    [
                        'id' => 'c1',
                        'kriteria' => 'Analisis Situasi & Permasalahan Mitra',
                        'bobot' => 30,
                        'deskripsi' => 'Ketepatan identifikasi persoalan nyata di masyarakat/mitra sasaran.',
                        'skala' => [
                            '1_2' => 'Sangat Kurang',
                            '3_4' => 'Cukup',
                            '5_6' => 'Baik',
                            '7' => 'Sangat Baik'
                        ]
                    ],
                    [
                        'id' => 'c2',
                        'kriteria' => 'Solusi Teknologi & Metode Pelaksanaan',
                        'bobot' => 30,
                        'deskripsi' => 'Kepraktisan inovasi teknologi tepat guna yang ditransfer.',
                        'skala' => [
                            '1_2' => 'Sangat Kurang',
                            '3_4' => 'Cukup',
                            '5_6' => 'Baik',
                            '7' => 'Sangat Baik'
                        ]
                    ],
                    [
                        'id' => 'c3',
                        'kriteria' => 'Partisipasi Mitra & Keberlanjutan Program',
                        'bobot' => 20,
                        'deskripsi' => 'Keterlibatan aktif kelompok masyarakat dan rencana pasca-pengabdian.',
                        'skala' => [
                            '1_2' => 'Sangat Kurang',
                            '3_4' => 'Cukup',
                            '5_6' => 'Baik',
                            '7' => 'Sangat Baik'
                        ]
                    ],
                    [
                        'id' => 'c4',
                        'kriteria' => 'Target Luaran Media Massal & Video Kegiatan',
                        'bobot' => 20,
                        'deskripsi' => 'Publikasi media massa nasional, video YouTube, dan HKI modul.',
                        'skala' => [
                            '1_2' => 'Sangat Kurang',
                            '3_4' => 'Cukup',
                            '5_6' => 'Baik',
                            '7' => 'Sangat Baik'
                        ]
                    ]
                ]
            ],
            [
                'kode_skema' => 'PMM',
                'nama_skema' => 'Pemberdayaan Masyarakat oleh Mahasiswa (PMM)',
                'kategori' => 'pengabdian',
                'min_jafung' => json_encode(['Asisten Ahli', 'Lektor', 'Lektor Kepala', 'Guru Besar / Profesor']),
                'min_sinta_3yr' => 0.0,
                'min_sinta_overall' => 101.0,
                'min_tkt' => null,
                'max_tkt' => null,
                'plafon_dana' => 80000000.00,
                'is_active' => true,
                'rubrik_penilaian' => [
                    [
                        'id' => 'c1',
                        'kriteria' => 'Analisis Situasi & Permasalahan Mitra',
                        'bobot' => 30,
                        'deskripsi' => 'Ketepatan identifikasi persoalan nyata di masyarakat/mitra sasaran.',
                        'skala' => [
                            '1_2' => 'Sangat Kurang',
                            '3_4' => 'Cukup',
                            '5_6' => 'Baik',
                            '7' => 'Sangat Baik'
                        ]
                    ],
                    [
                        'id' => 'c2',
                        'kriteria' => 'Solusi Teknologi & Metode Pelaksanaan',
                        'bobot' => 30,
                        'deskripsi' => 'Kepraktisan inovasi teknologi tepat guna yang ditransfer.',
                        'skala' => [
                            '1_2' => 'Sangat Kurang',
                            '3_4' => 'Cukup',
                            '5_6' => 'Baik',
                            '7' => 'Sangat Baik'
                        ]
                    ],
                    [
                        'id' => 'c3',
                        'kriteria' => 'Partisipasi Mitra & Keberlanjutan Program',
                        'bobot' => 20,
                        'deskripsi' => 'Keterlibatan aktif kelompok masyarakat dan rencana pasca-pengabdian.',
                        'skala' => [
                            '1_2' => 'Sangat Kurang',
                            '3_4' => 'Cukup',
                            '5_6' => 'Baik',
                            '7' => 'Sangat Baik'
                        ]
                    ],
                    [
                        'id' => 'c4',
                        'kriteria' => 'Target Luaran Media Massal & Video Kegiatan',
                        'bobot' => 20,
                        'deskripsi' => 'Publikasi media massa nasional, video YouTube, dan HKI modul.',
                        'skala' => [
                            '1_2' => 'Sangat Kurang',
                            '3_4' => 'Cukup',
                            '5_6' => 'Baik',
                            '7' => 'Sangat Baik'
                        ]
                    ]
                ]
            ],
            [
                'kode_skema' => 'PM-UPUD',
                'nama_skema' => 'Pemberdayaan Mitra Usaha Produk Unggulan Daerah (PM-UPUD)',
                'kategori' => 'pengabdian',
                'min_jafung' => json_encode(['Lektor', 'Lektor Kepala', 'Guru Besar / Profesor']),
                'min_sinta_3yr' => 0.0,
                'min_sinta_overall' => 200.0,
                'min_tkt' => null,
                'max_tkt' => null,
                'plafon_dana' => 150000000.00,
                'is_active' => true,
                'rubrik_penilaian' => [
                    [
                        'id' => 'c1',
                        'kriteria' => 'Analisis Situasi & Permasalahan Mitra',
                        'bobot' => 30,
                        'deskripsi' => 'Ketepatan identifikasi persoalan nyata di masyarakat/mitra sasaran.',
                        'skala' => [
                            '1_2' => 'Sangat Kurang',
                            '3_4' => 'Cukup',
                            '5_6' => 'Baik',
                            '7' => 'Sangat Baik'
                        ]
                    ],
                    [
                        'id' => 'c2',
                        'kriteria' => 'Solusi Teknologi & Metode Pelaksanaan',
                        'bobot' => 30,
                        'deskripsi' => 'Kepraktisan inovasi teknologi tepat guna yang ditransfer.',
                        'skala' => [
                            '1_2' => 'Sangat Kurang',
                            '3_4' => 'Cukup',
                            '5_6' => 'Baik',
                            '7' => 'Sangat Baik'
                        ]
                    ],
                    [
                        'id' => 'c3',
                        'kriteria' => 'Partisipasi Mitra & Keberlanjutan Program',
                        'bobot' => 20,
                        'deskripsi' => 'Keterlibatan aktif kelompok masyarakat dan rencana pasca-pengabdian.',
                        'skala' => [
                            '1_2' => 'Sangat Kurang',
                            '3_4' => 'Cukup',
                            '5_6' => 'Baik',
                            '7' => 'Sangat Baik'
                        ]
                    ],
                    [
                        'id' => 'c4',
                        'kriteria' => 'Target Luaran Media Massal & Video Kegiatan',
                        'bobot' => 20,
                        'deskripsi' => 'Publikasi media massa nasional, video YouTube, dan HKI modul.',
                        'skala' => [
                            '1_2' => 'Sangat Kurang',
                            '3_4' => 'Cukup',
                            '5_6' => 'Baik',
                            '7' => 'Sangat Baik'
                        ]
                    ]
                ]
            ],
            [
                'kode_skema' => 'PW',
                'nama_skema' => 'Pemberdayaan Wilayah (PW)',
                'kategori' => 'pengabdian',
                'min_jafung' => json_encode(['Lektor', 'Lektor Kepala', 'Guru Besar / Profesor']),
                'min_sinta_3yr' => 0.0,
                'min_sinta_overall' => 200.0,
                'min_tkt' => null,
                'max_tkt' => null,
                'plafon_dana' => 200000000.00,
                'is_active' => true,
                'rubrik_penilaian' => [
                    [
                        'id' => 'c1',
                        'kriteria' => 'Analisis Situasi & Permasalahan Mitra',
                        'bobot' => 30,
                        'deskripsi' => 'Ketepatan identifikasi persoalan nyata di masyarakat/mitra sasaran.',
                        'skala' => [
                            '1_2' => 'Sangat Kurang',
                            '3_4' => 'Cukup',
                            '5_6' => 'Baik',
                            '7' => 'Sangat Baik'
                        ]
                    ],
                    [
                        'id' => 'c2',
                        'kriteria' => 'Solusi Teknologi & Metode Pelaksanaan',
                        'bobot' => 30,
                        'deskripsi' => 'Kepraktisan inovasi teknologi tepat guna yang ditransfer.',
                        'skala' => [
                            '1_2' => 'Sangat Kurang',
                            '3_4' => 'Cukup',
                            '5_6' => 'Baik',
                            '7' => 'Sangat Baik'
                        ]
                    ],
                    [
                        'id' => 'c3',
                        'kriteria' => 'Partisipasi Mitra & Keberlanjutan Program',
                        'bobot' => 20,
                        'deskripsi' => 'Keterlibatan aktif kelompok masyarakat dan rencana pasca-pengabdian.',
                        'skala' => [
                            '1_2' => 'Sangat Kurang',
                            '3_4' => 'Cukup',
                            '5_6' => 'Baik',
                            '7' => 'Sangat Baik'
                        ]
                    ],
                    [
                        'id' => 'c4',
                        'kriteria' => 'Target Luaran Media Massal & Video Kegiatan',
                        'bobot' => 20,
                        'deskripsi' => 'Publikasi media massa nasional, video YouTube, dan HKI modul.',
                        'skala' => [
                            '1_2' => 'Sangat Kurang',
                            '3_4' => 'Cukup',
                            '5_6' => 'Baik',
                            '7' => 'Sangat Baik'
                        ]
                    ]
                ]
            ],
            [
                'kode_skema' => 'PDB',
                'nama_skema' => 'Pemberdayaan Desa Binaan (PDB)',
                'kategori' => 'pengabdian',
                'min_jafung' => json_encode(['Lektor', 'Lektor Kepala', 'Guru Besar / Profesor']),
                'min_sinta_3yr' => 0.0,
                'min_sinta_overall' => 200.0,
                'min_tkt' => null,
                'max_tkt' => null,
                'plafon_dana' => 150000000.00,
                'is_active' => true,
                'rubrik_penilaian' => [
                    [
                        'id' => 'c1',
                        'kriteria' => 'Analisis Situasi & Permasalahan Mitra',
                        'bobot' => 30,
                        'deskripsi' => 'Ketepatan identifikasi persoalan nyata di masyarakat/mitra sasaran.',
                        'skala' => [
                            '1_2' => 'Sangat Kurang',
                            '3_4' => 'Cukup',
                            '5_6' => 'Baik',
                            '7' => 'Sangat Baik'
                        ]
                    ],
                    [
                        'id' => 'c2',
                        'kriteria' => 'Solusi Teknologi & Metode Pelaksanaan',
                        'bobot' => 30,
                        'deskripsi' => 'Kepraktisan inovasi teknologi tepat guna yang ditransfer.',
                        'skala' => [
                            '1_2' => 'Sangat Kurang',
                            '3_4' => 'Cukup',
                            '5_6' => 'Baik',
                            '7' => 'Sangat Baik'
                        ]
                    ],
                    [
                        'id' => 'c3',
                        'kriteria' => 'Partisipasi Mitra & Keberlanjutan Program',
                        'bobot' => 20,
                        'deskripsi' => 'Keterlibatan aktif kelompok masyarakat dan rencana pasca-pengabdian.',
                        'skala' => [
                            '1_2' => 'Sangat Kurang',
                            '3_4' => 'Cukup',
                            '5_6' => 'Baik',
                            '7' => 'Sangat Baik'
                        ]
                    ],
                    [
                        'id' => 'c4',
                        'kriteria' => 'Target Luaran Media Massal & Video Kegiatan',
                        'bobot' => 20,
                        'deskripsi' => 'Publikasi media massa nasional, video YouTube, dan HKI modul.',
                        'skala' => [
                            '1_2' => 'Sangat Kurang',
                            '3_4' => 'Cukup',
                            '5_6' => 'Baik',
                            '7' => 'Sangat Baik'
                        ]
                    ]
                ]
            ]
        ];

        foreach ($schemes as $s) {
            PpmSkemaBima::updateOrCreate(['kode_skema' => $s['kode_skema']], $s);
        }

        // 2. Seed Call for Proposals Active Period
        PpmPeriodeHibah::updateOrCreate(
            ['tahun_akademik' => '2025/2026', 'semester' => 'Ganjil'],
            [
                'nama_periode' => 'Call for Proposals BIMA Hibah Riset & Abmas Batch 1 2025/2026',
                'waktu_buka' => now()->subDays(2),
                'waktu_tutup' => now()->addDays(28),
                'is_active' => true,
                'keterangan' => 'Periode penerimaan proposal riset standar BIMA Kemdiktisaintek tingkat Universitas Harkat Negeri.',
            ]
        );
    }
}


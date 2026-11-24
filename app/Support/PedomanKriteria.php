<?php

namespace App\Support;

use App\Models\RefTarifRewardSk;
use Illuminate\Support\Arr;

/**
 * Satu sumber kebenaran untuk kriteria yang berasal dari pedoman eksternal:
 *  - Matriks reward luaran penelitian/PkM (Dispo PPM No. 373.03/PPM.HN/X/2026).
 *  - Indikator Penilaian Evaluasi Keberlanjutan Penelitian (hal. 248-249).
 *
 * Data ini sengaja dipisah dari logika bisnis agar bisa dipakai seeder, service,
 * dan tampilan tanpa saling duplikat.
 */
class PedomanKriteria
{
    public const NOMOR_SK = 'SK-REKTOR/UHN/2026/015';

    public const SUMBER_REWARD = 'Disposisi PPM UHN No. 373.03/PPM.HN/X/2026 — Penyesuaian Reward Luaran 2026';

    public const SUMBER_INDIKATOR = 'Indikator Penilaian Evaluasi Keberlanjutan Penelitian, hal. 248-249';

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
     * Indikator evaluasi keberlanjutan berdasarkan kode skema.
     * PDP mengikuti pola Penelitian Dasar sesuai penegasan pengguna.
     */
    public static function monev(string $kodeSkema): array
    {
        $kode = strtoupper(trim($kodeSkema));

        $kelompok = in_array($kode, ['HILIRISASI', 'RIS-TERAPAN', 'PTU', 'TERAPAN'], true)
            ? 'terapan'
            : 'dasar';

        $itemDokumen = [
            'i1' => [
                'nama' => 'Kualitas dokumen luaran',
                'bobot' => 25,
                'penilaian' => [
                    'Sangat baik' => 100,
                    'Baik' => 75,
                    'Cukup baik' => 50,
                    'Kurang / tidak baik' => 25,
                ],
            ],
            'i2' => [
                'nama' => 'Kesesuaian isi dokumen dengan substansi penelitian',
                'bobot' => 25,
                'penilaian' => [
                    'Sangat sesuai' => 100,
                    'Sesuai' => 75,
                    'Cukup sesuai' => 50,
                    'Kurang / tidak sesuai' => 25,
                ],
            ],
            'i3' => [
                'nama' => 'Kesesuaian dengan periode pendanaan',
                'bobot' => 25,
                'penilaian' => [
                    'Sesuai' => 100,
                    'Tidak sesuai' => 0,
                ],
            ],
            'i4' => [
                'nama' => 'Kesesuaian pelaksanaan penelitian dengan usulan',
                'bobot' => 25,
                'penilaian' => [
                    'Sangat sesuai' => 100,
                    'Sesuai' => 75,
                    'Cukup sesuai' => 50,
                    'Kurang / tidak sesuai' => 25,
                ],
            ],
        ];

        $komponenDasar = [
            'c1' => [
                'nama' => 'Kemajuan ketercapaian luaran yang dijanjikan',
                'bobot_maks' => 80,
                'status' => [
                    'Telah tercapai / terlaksana' => 80,
                    'Berpotensi besar dapat tercapai' => 60,
                    'Berpotensi dapat tercapai' => 45,
                    'Kurang berpotensi dapat tercapai' => 25,
                    'Tidak tercapai' => 0,
                ],
                'items' => $itemDokumen,
            ],
            'c2' => [
                'nama' => 'Kesesuaian penelitian dengan usulan',
                'bobot_maks' => 20,
                'status' => [
                    'Penelitian sesuai usulan' => 20,
                    'Penelitian tidak sesuai usulan' => 0,
                ],
                'items' => [
                    'i1' => [
                        'nama' => 'Kesesuaian pelaksanaan dengan usulan',
                        'bobot' => 100,
                        'penilaian' => [
                            'Sangat sesuai' => 100,
                            'Sesuai' => 75,
                            'Cukup sesuai' => 50,
                            'Kurang / tidak sesuai' => 25,
                        ],
                    ],
                ],
            ],
        ];

        if ($kelompok === 'dasar') {
            return [
                'kode_skema' => $kode ?: 'PDP',
                'nama_skema' => 'Penelitian Dasar / Fundamental & Penelitian Dosen Pemula (PDP)',
                'sumber' => self::SUMBER_INDIKATOR . ' — Skema Penelitian Dasar',
                'komponen' => $komponenDasar,
            ];
        }

        return [
            'kode_skema' => $kode,
            'nama_skema' => 'Penelitian Terapan / Hilirisasi',
            'sumber' => self::SUMBER_INDIKATOR . ' — Skema Penelitian Terapan',
            'komponen' => [
                'c1' => [
                    'nama' => 'Kemajuan ketercapaian luaran yang dijanjikan',
                    'bobot_maks' => 80,
                    'status' => [
                        'Telah tercapai / terlaksana' => 80,
                        'Berpotensi besar dapat tercapai' => 55,
                        'Berpotensi dapat tercapai' => 40,
                        'Kurang berpotensi dapat tercapai' => 20,
                        'Tidak tercapai' => 0,
                    ],
                    'items' => $itemDokumen,
                ],
                'c2' => [
                    'nama' => 'Kesesuaian penelitian dengan usulan',
                    'bobot_maks' => 10,
                    'status' => [
                        'Penelitian sesuai usulan' => 10,
                        'Penelitian tidak sesuai usulan' => 0,
                    ],
                    'items' => [
                        'i1' => [
                            'nama' => 'Kesesuaian pelaksanaan dengan usulan',
                            'bobot' => 100,
                            'penilaian' => [
                                'Sangat sesuai' => 100,
                                'Sesuai' => 75,
                                'Cukup sesuai' => 50,
                                'Kurang / tidak sesuai' => 25,
                            ],
                        ],
                    ],
                ],
                'c3' => [
                    'nama' => 'Realisasi keterlibatan / kontribusi mitra (jika ada)',
                    'bobot_maks' => 10,
                    'status' => [
                        'Ada keterlibatan / kontribusi mitra' => 10,
                        'Tidak ada keterlibatan / kontribusi mitra' => 0,
                    ],
                    'items' => [
                        'i1' => [
                            'nama' => 'Kesesuaian janji keterlibatan mitra dengan pelaksanaan',
                            'bobot' => 50,
                            'penilaian' => [
                                'Sangat sesuai' => 100,
                                'Sesuai' => 75,
                                'Cukup sesuai' => 50,
                                'Kurang / tidak sesuai' => 25,
                            ],
                        ],
                        'i2' => [
                            'nama' => 'Realisasi kontribusi mitra',
                            'bobot' => 50,
                            'penilaian' => [
                                'Sesuai' => 100,
                                'Sesuai sebagian' => 50,
                                'Tidak sesuai' => 0,
                            ],
                        ],
                    ],
                ],
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
            $penilaian[$ck] = [
                'status' => Arr::get($existing, "{$ck}.status"),
                'items' => [],
            ];

            foreach ($komponen['items'] as $ik => $item) {
                $penilaian[$ck]['items'][$ik] = Arr::get($existing, "{$ck}.items.{$ik}");
            }
        }

        return $penilaian;
    }

    /**
     * Hitung skor monev 0-100 dari pilihan status dan nilai item.
     * Bobot item dalam pedoman dinyatakan dalam persen dan berjumlah 100 per komponen.
     */
    public static function hitungSkorMonev(string $kodeSkema, array $penilaian): float
    {
        $total = 0.0;

        foreach (self::monev($kodeSkema)['komponen'] as $ck => $komponen) {
            $status = (float) Arr::get($penilaian, "{$ck}.status", 0);
            if ($status <= 0) {
                continue;
            }

            $bobotTerkonversi = 0.0;
            foreach ($komponen['items'] as $ik => $item) {
                $faktor = (float) Arr::get($penilaian, "{$ck}.items.{$ik}", 0);
                $bobotTerkonversi += ($faktor / 100) * (float) $item['bobot'];
            }

            // $bobotTerkonversi maksimal 100 (semua item bernilai 100%).
            $total += $status * ($bobotTerkonversi / 100);
        }

        return round(min(100, max(0, $total)), 2);
    }
}

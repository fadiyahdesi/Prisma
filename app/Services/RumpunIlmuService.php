<?php

namespace App\Services;

class RumpunIlmuService
{
    /**
     * Get Rumpun Ilmu Hierarchy (Level 1 -> Level 2 -> Level 3)
     */
    public static function getRumpunIlmuHierarchy(): array
    {
        return [
            'Sains & Matematika' => [
                'Ilmu Komputer' => ['Rekayasa Perangkat Lunak', 'Kecerdasan Buatan (AI)', 'Sistem Informasi', 'Keamanan Siber'],
                'Fisika & Matematika' => ['Fisika Material', 'Matematika Terapan', 'Aktuaria'],
            ],
            'Teknologi & Rekayasa' => [
                'Teknik Elektro & Industri' => ['Sistem Tenaga Listrik', 'Robotika & Otomasi', 'Manufaktur Industri'],
                'Teknik Mesin & Sipil' => ['Konstruksi Bangunan', 'Energi Terbarukan', 'Otomotif & Transportasi'],
            ],
            'Sosial & Humaniora' => [
                'Ekonomi & Bisnis' => ['Manajemen Keuangan', 'Akuntansi Publik', 'Ekonomi Digital & UMKM'],
                'Ilmu Sosial & Hukum' => ['Hukum Bisnis', 'Ilmu Komunikasi', 'Hubungan Internasional'],
            ],
            'Kesehatan & Farmasi' => [
                'Ilmu Kesehatan' => ['Keperawatan Terpadu', 'Kebidanan & Kesehatan Ibu', 'Farmasi Klinis'],
            ],
        ];
    }

    /**
     * Get Bidang Fokus RIRN / PRN
     */
    public static function getFokusRirnList(): array
    {
        return [
            'Pangan & Pertanian',
            'Energi Baru & Terbarukan',
            'Kesehatan & Obat-obatan',
            'Teknologi Informasi & Komunikasi (TIK)',
            'Transportasi & Otomotif',
            'Bahan Baku & Material Maju',
            'Kebencanaan & Lingkungan Hidup',
            'Sosial Humaniora, Seni Budaya, & Pendidikan',
            'Pengembangan Ekonomi Digital & UMKM',
        ];
    }

    /**
     * Get Sustainable Development Goals indicators.
     */
    public static function getSdgList(): array
    {
        return [
            1 => 'Tanpa Kemiskinan',
            2 => 'Tanpa Kelaparan',
            3 => 'Kehidupan Sehat dan Sejahtera',
            4 => 'Pendidikan Bermutu',
            5 => 'Kesetaraan Gender',
            6 => 'Air Bersih dan Sanitasi Layak',
            7 => 'Energi Bersih dan Terjangkau',
            8 => 'Pekerjaan Layak dan Pertumbuhan Ekonomi',
            9 => 'Industri, Inovasi dan Infrastruktur',
            10 => 'Berkurangnya Kesenjangan',
            11 => 'Kota dan Permukiman yang Berkelanjutan',
            12 => 'Konsumsi dan Produksi yang Bertanggung Jawab',
            13 => 'Penanganan Perubahan Iklim',
            14 => 'Ekosistem Lautan',
            15 => 'Ekosistem Daratan',
            16 => 'Perdamaian, Keadilan dan Kelembagaan yang Tangguh',
            17 => 'Kemitraan untuk Mencapai Tujuan',
        ];
    }

    /**
     * Get TKT Indicator description for a specific TKT level (1-9)
     */
    public static function getTktIndicators(int $tkt): string
    {
        $indicators = [
            1 => 'Prinsip dasar teknologi telah diobservasi dan dilaporkan.',
            2 => 'Formulasi konsep dan/atau aplikasi teknologi telah dirumuskan.',
            3 => 'Pembuktian konsep (Proof of Concept) fungsi utama secara analitis dan eksperimental.',
            4 => 'Validasi komponen/sub-sistem dalam lingkungan laboratorium.',
            5 => 'Validasi komponen/sub-sistem dalam lingkungan yang relevan.',
            6 => 'Demonstrasi model/prototipe sistem dalam lingkungan yang relevan.',
            7 => 'Demonstrasi prototipe sistem dalam lingkungan operasional sebenarnya.',
            8 => 'Sistem telah lengkap dan memenuhi kualifikasi melalui pengujian dan evaluasi.',
            9 => 'Sistem telah teruji dan terbukti dalam lingkungan operasional sebenarnya (siap komersialisasi).',
        ];

        return $indicators[$tkt] ?? 'Prinsip dasar teknologi diobservasi.';
    }

    /**
     * Get all TKT indicators array (1-9)
     */
    public static function getAllTktIndicators(): array
    {
        return [
            1 => 'Prinsip dasar teknologi telah diobservasi dan dilaporkan.',
            2 => 'Formulasi konsep dan/atau aplikasi teknologi telah dirumuskan.',
            3 => 'Pembuktian konsep (Proof of Concept) fungsi utama secara analitis dan eksperimental.',
            4 => 'Validasi komponen/sub-sistem dalam lingkungan laboratorium.',
            5 => 'Validasi komponen/sub-sistem dalam lingkungan yang relevan.',
            6 => 'Demonstrasi model/prototipe sistem dalam lingkungan yang relevan.',
            7 => 'Demonstrasi prototipe sistem dalam lingkungan operasional sebenarnya.',
            8 => 'Sistem telah lengkap dan memenuhi kualifikasi melalui pengujian dan evaluasi.',
            9 => 'Sistem telah teruji dan terbukti dalam lingkungan operasional sebenarnya (siap komersialisasi).',
        ];
    }
}


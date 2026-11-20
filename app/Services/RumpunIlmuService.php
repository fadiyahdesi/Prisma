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

    /**
     * Resolve default Rumpun Ilmu Level 1 from a Program Studi name.
     */
    public static function resolveRumpunFromProdi(?string $prodiName): string
    {
        $lower = strtolower((string) $prodiName);
        if (str_contains($lower, 'farmasi') || str_contains($lower, 'bidan') || str_contains($lower, 'perawat') || str_contains($lower, 'kesehatan')) {
            return 'Kesehatan & Farmasi';
        }
        if (str_contains($lower, 'informatika') || str_contains($lower, 'komputer') || str_contains($lower, 'sains') || str_contains($lower, 'matematika')) {
            return 'Sains & Matematika';
        }
        if (str_contains($lower, 'akuntansi') || str_contains($lower, 'manajemen') || str_contains($lower, 'hotel') || str_contains($lower, 'hukum') || str_contains($lower, 'psikologi') || str_contains($lower, 'bahasa') || str_contains($lower, 'komunikasi') || str_contains($lower, 'desain') || str_contains($lower, 'dkv') || str_contains($lower, 'pgsd') || str_contains($lower, 'pendidikan')) {
            return 'Sosial & Humaniora';
        }

        return 'Teknologi & Rekayasa';
    }

    /**
     * Katalog lengkap TKT Meter (Permenristekdikti No. 42/2016 / PASINBIS UNAIR).
     */
    public static function getTktMeterCatalog(): array
    {
        return [
            'rekayasa' => [
                'label' => 'Umum & Rekayasa (Engineering)',
                'short' => 'Rekayasa',
                'levels' => [
                    1 => ['title' => 'Prinsip Dasar Teknologi Diteliti & Dilaporkan', 'phase' => 'Penelitian Dasar', 'indicators' => ['Asumsi dan hukum dasar teknologi telah dirumuskan secara teoretis.', 'Studi literatur awal mendukung kelayakan prinsip kerja sistem.', 'Survei fenomena fisis atau rekayasa dasar telah didokumentasikan.']],
                    2 => ['title' => 'Formulasi Konsep & Aplikasi Formulasi', 'phase' => 'Penelitian Dasar', 'indicators' => ['Konsep teknologi dan potensi aplikasi praktis telah didefinisikan.', 'Desain konseptual arsitektur atau skema kerja telah dibuat.', 'Analisis kelayakan awal secara matematis atau komputasi dilakukan.']],
                    3 => ['title' => 'Pembuktian Konsep (Proof of Concept)', 'phase' => 'Penelitian Dasar', 'indicators' => ['Eksperimen analitis dan uji coba skala laboratorium membuktikan konsep.', 'Fungsi kritis/karakteristik utama teknologi terverifikasi di lab.', 'Batasan parameter operasi dasar mulai teridentifikasi.']],
                    4 => ['title' => 'Validasi Komponen di Lingkungan Laboratorium', 'phase' => 'Pengembangan Teknologi', 'indicators' => ['Komponen-komponen terpisah dirakit menjadi modul terintegrasi skala lab.', 'Kinerja komponen diuji dalam skenario simulasi laboratorium terkontrol.', 'Kesalahan integrasi dasar telah dikalibrasi.']],
                    5 => ['title' => 'Validasi Komponen di Lingkungan Relevan', 'phase' => 'Pengembangan Teknologi', 'indicators' => ['Modul/subsistem diuji di lingkungan simulasi yang mendekati kondisi lapangan.', 'Integrasi perangkat keras/lunak atau material pendukung memenuhi target teknis.', 'Data keandalan awal dalam kondisi beban bervariasi tersedia.']],
                    6 => ['title' => 'Demonstrasi Model/Prototipe di Lingkungan Relevan', 'phase' => 'Pengembangan Teknologi', 'indicators' => ['Prototipe representatif skala penuh atau pilot didemonstrasikan.', 'Uji fungsionalitas sistem secara menyeluruh di lingkungan operasional relevan.', 'Desain teknik siap untuk uji coba pengguna nyata.']],
                    7 => ['title' => 'Demonstrasi Prototipe di Lingkungan Sebenarnya', 'phase' => 'Uji & Demonstrasi', 'indicators' => ['Prototipe beroperasi pada kondisi kerja lapangan/industri sesungguhnya.', 'Umpan balik dari mitra pengguna/industri telah dievaluasi.', 'Kestabilan sistem dalam jangka operasional uji tercapai.']],
                    8 => ['title' => 'Sistem Lengkap, Tersertifikasi & Andal', 'phase' => 'Uji & Demonstrasi', 'indicators' => ['Produk/sistem final selesai dibuat beserta SOP & dokumentasi mutu.', 'Uji kelayakan standar mutu/keselamatan/kepatuhan telah lulus.', 'Kesiapan proses manufaktur atau implementasi luas tervalidasi.']],
                    9 => ['title' => 'Teruji Penuh & Siap Komersialisasi', 'phase' => 'Komersialisasi', 'indicators' => ['Sistem/produk digunakan secara rutin di lingkungan komersial/masyarakat.', 'Klaim keandalan jangka panjang & dukungan purnajual tersedia.', 'Telah diadopsi oleh mitra industri atau pengguna luas.']],
                ],
            ],
            'tik' => [
                'label' => 'Perangkat Lunak & TIK (Software / AI)',
                'short' => 'Software & TIK',
                'levels' => [
                    1 => ['title' => 'Formulasi Algoritma & Arsitektur Dasar', 'phase' => 'Penelitian Dasar', 'indicators' => ['Model matematis, algoritma dasar, atau teori komputasi diidentifikasi.', 'Kajian pustaka arsitektur sistem/dataset relevan tersusun.', 'Kebutuhan masalah komputasi didefinisikan dengan jelas.']],
                    2 => ['title' => 'Rancangan Konsep & Spesifikasi Sistem', 'phase' => 'Penelitian Dasar', 'indicators' => ['Diagram alur, skema database/model AI, atau wireframe aplikasi dibuat.', 'Aplikasi target pengguna dan metrik keberhasilan dirumuskan.', 'Simulasi alur logika tanpa kode produksi penuh telah disusun.']],
                    3 => ['title' => 'Proof of Concept (PoC) Algoritma & Kode Awal', 'phase' => 'Penelitian Dasar', 'indicators' => ['Skrip/prototipe awal algoritma diuji menggunakan dataset uji terbatas.', 'Akurasi/latensi dasar terbukti layak secara eksperimental.', 'Komponen inti berjalan di lingkungan pengembangan lokal.']],
                    4 => ['title' => 'Validasi Modul Aplikasi di Lingkungan Lab', 'phase' => 'Pengembangan Teknologi', 'indicators' => ['Modul API, basis data, dan antarmuka inti terintegrasi pada server lab.', 'Unit testing dan integration testing dasar berjalan tanpa error fatal.', 'Alur data antar komponen tervalidasi dengan data simulasi.']],
                    5 => ['title' => 'Validasi Sistem pada Lingkungan Simulasi Nyata', 'phase' => 'Pengembangan Teknologi', 'indicators' => ['Aplikasi diuji pada server staging dengan ukuran dataset menyerupai skala riil.', 'Uji beban (load test) dan kerentanan keamanan dasar dilaksanakan.', 'Antarmuka siap dievaluasi oleh pengguna internal terbatas.']],
                    6 => ['title' => 'Demonstrasi Prototipe Beta di Lingkungan Relevan', 'phase' => 'Pengembangan Teknologi', 'indicators' => ['Rilis beta lengkap fitur utama didemonstrasikan kepada pemangku kepentingan.', 'Uji UAT (User Acceptance Test) awal di lingkungan relevan lulus.', 'Dokumentasi teknis API dan panduan pengguna awal tersusun.']],
                    7 => ['title' => 'Implementasi Pilot di Lingkungan Operasional Nyata', 'phase' => 'Uji & Demonstrasi', 'indicators' => ['Sistem di-deploy pada infrastruktur mitra/kampus dengan pengguna aktif.', 'Bug minor dan kendala integrasi operasional ditangani.', 'Log aktivitas dan performa operasional nyata terekam baik.']],
                    8 => ['title' => 'Sistem Stabil, Aman & Lulus Uji Kelayakan', 'phase' => 'Uji & Demonstrasi', 'indicators' => ['SLA uptime, keamanan data, dan HKI perangkat lunak telah terpenuhi.', 'Penetration testing / QA final disetujui pengelola sistem.', 'Migrasi dan pelatihan administrator operasional rampung.']],
                    9 => ['title' => 'Beroperasi Penuh & Adopsi Komersial / Institusi', 'phase' => 'Komersialisasi', 'indicators' => ['Perangkat lunak beroperasi penuh di produksi melayani pengguna skala luas.', 'Pemeliharaan berkala, lisensi, dan model bisnis/adopsi berjalan.', 'Terbukti memberi dampak efisiensi dan nilai tambah nyata.']],
                ],
            ],
            'kesehatan' => [
                'label' => 'Kesehatan, Farmasi & Alat Kesehatan',
                'short' => 'Kesehatan & Obat',
                'levels' => [
                    1 => ['title' => 'Identifikasi Target Biologis / Konsep Medis Dasar', 'phase' => 'Penelitian Dasar', 'indicators' => ['Hipotesis mekanisme aksi, senyawa aktif, atau kebutuhan klinis ditetapkan.', 'Kajian literatur ilmiah & etika biomedis awal rampung.', 'Karakterisasi target atau parameter fisiologis dasar dipetakan.']],
                    2 => ['title' => 'Desain Formulasi / Konsep Alat Medis', 'phase' => 'Penelitian Dasar', 'indicators' => ['Rancangan formulasi sediaan atau konsep desain alkes dirumuskan.', 'Kandidat bahan baku/biomaterial atau metode diagnostik diseleksi.', 'Aspek keamanan dan kepatuhan regulasi awal dipetakan.']],
                    3 => ['title' => 'Uji In-Vitro / Proof of Concept Laboratorium', 'phase' => 'Penelitian Dasar', 'indicators' => ['Uji aktivitas in-vitro, in-silico, atau uji fungsi sensor awal berhasil.', 'Keamanan awal (toksisitas sel/komponen) diuji di laboratorium.', 'Data pembuktian konsep farmakologis/teknis terdokumentasi.']],
                    4 => ['title' => 'Validasi Formulasi / Komponen Alat di Lab Terstandar', 'phase' => 'Pengembangan Teknologi', 'indicators' => ['Stabilitas sediaan awal atau integrasi modul alkes diuji di laboratorium.', 'Parameter kontrol kualitas dan pengulangan hasil (reproducibility) tercapai.', 'Protokol uji pra-klinik / kalibrasi alat tersusun.']],
                    5 => ['title' => 'Uji Pra-Klinik / Lingkungan Fisiologis Relevan', 'phase' => 'Pengembangan Teknologi', 'indicators' => ['Uji pra-klinik (hewan coba/model) atau uji phantom alat kesehatan dilakukan.', 'Efektivitas dan batas dosis/keamanan terukur memenuhi syarat.', 'Persetujuan komite etik dan standar GCP/GLP disiapkan.']],
                    6 => ['title' => 'Uji Klinik Fase I / Demonstrasi Prototipe Medis', 'phase' => 'Pengembangan Teknologi', 'indicators' => ['Uji keamanan pada subyek terbatas atau demonstrasi prototipe di klinik mitra.', 'Standar produksi CPOB/CPAKB skala pilot disiapkan.', 'Evaluasi efek samping dan keandalan diagnostik awal tercatat.']],
                    7 => ['title' => 'Uji Klinik Fase II/III Awal di Fasilitas Kesehatan Nyata', 'phase' => 'Uji & Demonstrasi', 'indicators' => ['Uji efikasi/kelayakan klinis dilakukan di rumah sakit atau klinik nyata.', 'Konsistensi mutu batch produksi atau unit alat teruji.', 'Proses pengurusan izin edar (BPOM / Kemenkes) diajukan.']],
                    8 => ['title' => 'Registrasi Regulasi, Sertifikasi & Validasi Final', 'phase' => 'Uji & Demonstrasi', 'indicators' => ['Izin edar / sertifikasi keamanan dan efektivitas resmi diperoleh.', 'Batch validasi komersial/klinis lulus pengujian kendali mutu penuh.', 'Prosedur farmakovigilans/kelayakan pasca-pasar siap dijalankan.']],
                    9 => ['title' => 'Digunakan Klinis & Tersedia di Fasilitas Kesehatan', 'phase' => 'Komersialisasi', 'indicators' => ['Produk kesehatan/farmasi diproduksi dan didistribusikan resmi.', 'Digunakan oleh tenaga medis atau masyarakat secara luas.', 'Pemantauan mutu dan manfaat klinis berkelanjutan berjalan.']],
                ],
            ],
            'pertanian' => [
                'label' => 'Pertanian, Pangan & Lingkungan',
                'short' => 'Pertanian & Pangan',
                'levels' => [
                    1 => ['title' => 'Observasi Plasma Nutfah / Prinsip Agronomi Dasar', 'phase' => 'Penelitian Dasar', 'indicators' => ['Karakteristik dasar komoditas, mikroba, atau sifat fisik-kimia diidentifikasi.', 'Studi literatur ekologi, nutrisi, atau proses pengolahan awal disusun.', 'Masalah ketahanan pangan/lingkungan yang dituju dirumuskan jelas.']],
                    2 => ['title' => 'Formulasi Konsep Teknologi Budidaya / Pangan', 'phase' => 'Penelitian Dasar', 'indicators' => ['Formula pakan/pupuk/pangan atau model budidaya mulai dirancang.', 'Potensi aplikasi dan nilai ekonomis awal bagi petani/UMKM dipetakan.', 'Rancangan eksperimen rumah kaca atau uji mutu ditetapkan.']],
                    3 => ['title' => 'Uji Skala Laboratorium / Rumah Kaca Awal', 'phase' => 'Penelitian Dasar', 'indicators' => ['Uji coba skala pot/lab mengonfirmasi respons pertumbuhan atau mutu gizi.', 'Uji cemaran/keamanan pangan dasar menunjukkan hasil memenuhi syarat.', 'Parameter efektivitas awal telah terdokumentasi.']],
                    4 => ['title' => 'Validasi Formula / Proses pada Kondisi Terkendali', 'phase' => 'Pengembangan Teknologi', 'indicators' => ['Formula/proses diulang di rumah kaca atau lini proses pangan skala lab.', 'Kombinasi nutrisi, daya simpan, atau rendemen terukur stabil.', 'Aspek kompatibilitas dengan kondisi tanah/iklim lokal diuji.']],
                    5 => ['title' => 'Uji Adaptasi Lahan Terbatas / Lingkungan Relevan', 'phase' => 'Pengembangan Teknologi', 'indicators' => ['Diujicobakan pada lahan percobaan atau mitra pengolah skala kecil.', 'Variabilitas kondisi cuaca/bahan baku telah teruji terhadap hasil.', 'Efisiensi biaya produksi dan mutu hasil mulai dievaluasi.']],
                    6 => ['title' => 'Demonstrasi Plot (Demplot) / Produksi Skala Pilot', 'phase' => 'Pengembangan Teknologi', 'indicators' => ['Demplot lapangan atau produksi pangan skala pilot berhasil dijalankan.', 'Standar operasional budidaya/pengolahan siap ditransfer ke petani/UMKM.', 'Analisis kelayakan usaha tani atau UMKM awal bernilai positif.']],
                    7 => ['title' => 'Uji Multi-Lokasi & Pendampingan Mitra Lapangan', 'phase' => 'Uji & Demonstrasi', 'indicators' => ['Teknologi diterapkan langsung oleh kelompok tani/mitra pada beberapa lokasi.', 'Sertifikasi pangan (PIRT/Halal/BPOM) atau pelepasan varietas diproses.', 'Hasil panen atau kualitas produk mitra terbukti meningkat nyata.']],
                    8 => ['title' => 'Produk / Teknologi Tepat Guna Tersertifikasi Penuh', 'phase' => 'Uji & Demonstrasi', 'indicators' => ['Izin edar/sertifikasi produk atau standar mutu agronomi tuntas.', 'Rantai pasok bahan baku dan pengemasan standar siap berjalan.', 'Mitra binaan mampu mengoperasikan sistem secara mandiri.']],
                    9 => ['title' => 'Adopsi Luas & Hilirisasi Produk Pertanian/Pangan', 'phase' => 'Komersialisasi', 'indicators' => ['Produk dipasarkan rutin atau teknologi diterapkan luas di wilayah binaan.', 'Kontribusi ekonomi dan ketahanan pangan bagi masyarakat terbukti.', 'Kemitraan berkelanjutan dengan industri/koperasi aktif berjalan.']],
                ],
            ],
            'soshum' => [
                'label' => 'Sosial Humaniora, Ekonomi & Pendidikan',
                'short' => 'Sosial & Pendidikan',
                'levels' => [
                    1 => ['title' => 'Kajian Teori & Identifikasi Fenomena Sosial/Kebijakan', 'phase' => 'Penelitian Dasar', 'indicators' => ['Fenomena sosial, ekonomi, hukum, atau pembelajaran diidentifikasi.', 'Kajian pustaka teori relevan dan pemetaan isu empiris dilakukan.', 'Kerangka konseptual awal disusun secara sistematis.']],
                    2 => ['title' => 'Perumusan Model Intervensi / Kurikulum / Kebijakan', 'phase' => 'Penelitian Dasar', 'indicators' => ['Model sosial, instrumen pembelajaran, atau rancangan kebijakan dirumuskan.', 'Subyek/sasaran pemangku kepentingan (sekolah/UMKM/pemda) dipetakan.', 'Indikator capaian perubahan perilaku atau kinerja didefinisikan.']],
                    3 => ['title' => 'Validasi Konsep, Instrumen & FGD Awal', 'phase' => 'Penelitian Dasar', 'indicators' => ['Instrumen penelitian/modul/naskah akademik diuji validitas & reliabilitasnya.', 'Focus Group Discussion (FGD) dengan pakar atau praktisi mengonfirmasi model.', 'Uji coba skala kecil membuktikan keterterapan konsep awal.']],
                    4 => ['title' => 'Pengembangan Modul / Sistem Sosial Skala Terbatas', 'phase' => 'Pengembangan Teknologi', 'indicators' => ['Komponen perangkat pembelajaran, model tata kelola, atau media edukasi dirakit.', 'Penilaian ahli (expert judgment) terhadap kelayakan bahan intervensi selesai.', 'Revisi berdasarkan uji coba kelompok kecil telah dilakukan.']],
                    5 => ['title' => 'Uji Coba Model dalam Lingkungan Komunitas Relevan', 'phase' => 'Pengembangan Teknologi', 'indicators' => ['Model diterapkan pada lingkungan relevan (kelas/komunitas/UMKM mitra).', 'Efektivitas metode, literasi, atau dampak sosio-ekonomi awal terukur.', 'Hambatan budaya dan adaptasi pengguna diidentifikasi.']],
                    6 => ['title' => 'Demonstrasi Model Terpadu Bersama Mitra Sasaran', 'phase' => 'Pengembangan Teknologi', 'indicators' => ['Model lengkap didemonstrasikan dalam siklus implementasi penuh bersama mitra.', 'Perubahan kinerja/kompetensi terukur secara kuantitatif maupun kualitatif.', 'Panduan penerapan atau rekomendasi kebijakan (policy brief) tersedia.']],
                    7 => ['title' => 'Penerapan Nyata pada Sekolah / UMKM / Instansi Mitra', 'phase' => 'Uji & Demonstrasi', 'indicators' => ['Model digunakan nyata pada kegiatan rutin instansi atau usaha mitra.', 'Evaluasi dampak sosial, ekonomi, atau kepuasan pengguna menunjukkan hasil baik.', 'Pencatatan Hak Cipta modul/panduan atau naskah kebijakan dilakukan.']],
                    8 => ['title' => 'Standarisasi Model & Rekomendasi Kebijakan Resmi', 'phase' => 'Uji & Demonstrasi', 'indicators' => ['Buku ajar, model tata kelola, atau policy brief disahkan untuk adopsi.', 'Bukti efektivitas replikasi pada konteks yang lebih luas terdokumentasi.', 'Mekanisme pendampingan mandiri mitra telah terbentuk.']],
                    9 => ['title' => 'Implementasi Berkelanjutan & Adopsi Kebijakan Luas', 'phase' => 'Komersialisasi', 'indicators' => ['Model sosial/pendidikan/bisnis diterapkan secara luas oleh institusi/masyarakat.', 'Regulasi daerah/instansi atau produk ekonomi kreatif berjalan mandiri.', 'Dampak sosial dan peningkatan kapasitas masyarakat terbukti berkelanjutan.']],
                ],
            ],
        ];
    }
}


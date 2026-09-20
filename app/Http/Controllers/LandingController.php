<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * Display the PRISMA Landing Page with structured domain data.
     */
    public function index()
    {
        $systemInfo = [
            'name' => 'PRISMA UHN',
            'fullName' => 'Portal Riset, Inovasi, Sistem Pengabdian Masyarakat, & Apresiasi',
            'institution' => 'Universitas Harkat Negeri',
            'domain' => 'prisma.harkatnegeri.ac.id',
            'tagline' => 'Membiaskan Inovasi, Mencerahkan Negeri',
            'bimaAligned' => true,
            'callForProposals' => [
                'isOpen' => true,
                'title' => 'Call for Proposals Hibah Internal UHN T.A. 2026/2027',
                'batch' => 'Gelombang 1',
                'startDate' => '15 Agustus 2026',
                'endDate' => '30 September 2026',
                'daysRemaining' => 27,
            ]
        ];

        $stats = [
            ['number' => '4', 'label' => 'Fakultas Utama', 'sub' => 'Saintek, Soshum, Psikopen, Vokasi'],
            ['number' => '22', 'label' => 'Program Studi', 'sub' => 'Jenjang S-1, D-4, & D-3'],
            ['number' => 'Rp 4.8 M', 'label' => 'Pagu Total Hibah', 'sub' => 'Tahun Anggaran 2026/2027'],
            ['number' => '100%', 'label' => 'BIMA Standard', 'sub' => 'Mirroring Kemdiktisaintek'],
            ['number' => '8', 'label' => 'Peran Hak Akses (RBAC)', 'sub' => 'Dosen s.d. Rektorat & Keuangan'],
            ['number' => '70/30', 'label' => 'Termin Dana', 'sub' => 'Monev & Pencairan Multi-Termin'],
        ];

        $pillars = [
            [
                'id' => 'riset-dasar',
                'badge' => 'Pilar 1',
                'title' => 'Riset Dasar & Keilmuan',
                'description' => 'Mendorong invensi dan penemuan keilmuan baru berbasis kepakaran rumpun ilmu dosen UHN.',
                'icon' => 'microscope',
                'schemes_count' => '3 Skema Utama',
                'focus' => 'TKT 1 - 3',
                'color' => 'blue'
            ],
            [
                'id' => 'riset-terapan',
                'badge' => 'Pilar 2',
                'title' => 'Riset Terapan & Hilirisasi',
                'description' => 'Pengujian model, prototipe laik industri, dan komersialisasi riset unggulan perguruan tinggi.',
                'icon' => 'cpu',
                'schemes_count' => '3 Skema Unggulan',
                'focus' => 'TKT 4 - 9',
                'color' => 'emerald'
            ],
            [
                'id' => 'pengabdian',
                'badge' => 'Pilar 3',
                'title' => 'Pengabdian Masyarakat',
                'description' => 'Pemberdayaan mitra produktif, UMKM, dan masyarakat wilayah pendampingan secara nyata.',
                'icon' => 'users',
                'schemes_count' => '4 Skema Kemitraan',
                'focus' => 'Dampak Sosial & Ekonomi',
                'color' => 'amber'
            ],
            [
                'id' => 'hki-insentif',
                'badge' => 'Pilar 4',
                'title' => 'Sentra HKI & Reward Insentif',
                'description' => 'Inventarisasi Jurnal Scopus/SINTA, Hak Cipta, Paten DJKI, serta klaim reward insentif dosen.',
                'icon' => 'award',
                'schemes_count' => 'Klaim Otomatis',
                'focus' => 'Capaian IKU-5',
                'color' => 'purple'
            ],
        ];

        $schemes = [
            [
                'id' => 'pdp',
                'category' => 'Riset Dasar',
                'code' => 'PDP',
                'name' => 'Penelitian Dosen Pemula',
                'pagu' => 'Rp 25.000.000',
                'max_pagu' => 25000000,
                'min_sinta' => 'Score 3Yr ≥ 50',
                'min_jafung' => 'Asisten Ahli / Tenaga Pengajar',
                'target_tkt' => 'TKT 1 - 3',
                'duration' => '1 Tahun',
                'description' => 'Membina kemampuan meneliti bagi dosen pemula dan memperluas jejaring publikasi ilmiah.',
                'luaran' => '1 Jurnal SINTA 3/4 atau 1 Hak Cipta'
            ],
            [
                'id' => 'pf',
                'category' => 'Riset Dasar',
                'code' => 'PF',
                'name' => 'Penelitian Fundamental UHN',
                'pagu' => 'Rp 65.000.000',
                'max_pagu' => 65000000,
                'min_sinta' => 'Score 3Yr ≥ 150',
                'min_jafung' => 'Lektor / Lektor Kepala',
                'target_tkt' => 'TKT 2 - 3',
                'duration' => '2 Tahun',
                'description' => 'Riset eksplorasi mendalam untuk menghasilkan prinsip dasar teknologi dan teori baru.',
                'luaran' => '1 Jurnal Scopus / WoS Q1-Q4'
            ],
            [
                'id' => 'hilirisasi',
                'category' => 'Riset Terapan',
                'code' => 'PISN',
                'name' => 'Hilirisasi Prototipe & Inovasi',
                'pagu' => 'Rp 120.000.000',
                'max_pagu' => 120000000,
                'min_sinta' => 'Score 3Yr ≥ 250',
                'min_jafung' => 'Lektor Kepala / Guru Besar',
                'target_tkt' => 'TKT 6 - 8',
                'duration' => '1-2 Tahun',
                'description' => 'Pengujian prototipe industri bersama mitra DU/DI (Dunia Usaha & Dunia Industri).',
                'luaran' => 'Paten Granted / Dokumen Feasibility Study'
            ],
            [
                'id' => 'pmp',
                'category' => 'Pengabdian',
                'code' => 'PMP',
                'name' => 'Pemberdayaan Masyarakat Pemula',
                'pagu' => 'Rp 30.000.000',
                'max_pagu' => 30000000,
                'min_sinta' => 'Score 3Yr ≥ 50',
                'min_jafung' => 'Asisten Ahli',
                'target_tkt' => 'Penerapan TTG',
                'duration' => '1 Tahun',
                'description' => 'Solusi kepakaran dosen UHN untuk menyelesaikan masalah prioritas pada masyarakat sasaran non-produktif.',
                'luaran' => 'Jurnal Pengabdian SINTA 3-5 & Video YouTube'
            ],
            [
                'id' => 'pkm',
                'category' => 'Pengabdian',
                'code' => 'PKM',
                'name' => 'Pemberdayaan Kemitraan Masyarakat',
                'pagu' => 'Rp 55.000.000',
                'max_pagu' => 55000000,
                'min_sinta' => 'Score 3Yr ≥ 100',
                'min_jafung' => 'Lektor',
                'target_tkt' => 'Mitra Produktif',
                'duration' => '1 Tahun',
                'description' => 'Kemitraan bersama UMKM atau kelompok usaha masyarakat untuk peningkatan omzet dan tata kelola.',
                'luaran' => '1 Hak Cipta / Paten Sederhana & Publikasi'
            ],
            [
                'id' => 'reward-pub',
                'category' => 'HKI & Insentif',
                'code' => 'INS-PUB',
                'name' => 'Insentif Publikasi Jurnal Scopus/SINTA',
                'pagu' => 's.d. Rp 25.000.000 / Artikel',
                'max_pagu' => 25000000,
                'min_sinta' => 'Afiliasi UHN Terverifikasi',
                'min_jafung' => 'Seluruh Dosen Tetap UHN',
                'target_tkt' => 'Scopus Q1-Q4 / SINTA 1-2',
                'duration' => 'Klaim Real-time',
                'description' => 'Reward finansial langsung dari Kampus bagi dosen yang menerbitkan artikel jurnal bereputasi.',
                'luaran' => 'Artikel terindeks & DOI Aktif'
            ],
        ];

        $faculties = [
            [
                'code' => 'FST',
                'name' => 'Fakultas Sains & Teknologi',
                'dean' => 'Dr. Ir. Hendra Prasetya, M.T.',
                'color' => 'blue',
                'programs' => [
                    'S-1 Teknik Informatika',
                    'S-1 Sistem Informasi',
                    'S-1 Teknik Elektro',
                    'S-1 Teknik Mesin',
                    'S-1 Bioteknologi',
                    'S-1 Rekayasa Perangkat Lunak'
                ]
            ],
            [
                'code' => 'FSH',
                'name' => 'Fakultas Sosial & Humaniora',
                'dean' => 'Dr. Ratna Sari, S.E., M.Si.',
                'color' => 'emerald',
                'programs' => [
                    'S-1 Akuntansi',
                    'S-1 Manajemen',
                    'S-1 Hukum',
                    'S-1 Ilmu Komunikasi',
                    'S-1 Hubungan Internasional'
                ]
            ],
            [
                'code' => 'FPP',
                'name' => 'Fakultas Psikologi & Pendidikan',
                'dean' => 'Dr. Wahyu Hidayat, M.Psi., Psikolog',
                'color' => 'amber',
                'programs' => [
                    'S-1 Psikologi',
                    'S-1 Pendidikan Bahasa Inggris',
                    'S-1 Pendidikan Matematika',
                    'S-1 Pendidikan Anak Usia Dini'
                ]
            ],
            [
                'code' => 'SV',
                'name' => 'Sekolah Vokasi',
                'dean' => 'Drs. Bambang Sudiro, M.Kom.',
                'color' => 'purple',
                'programs' => [
                    'D-4 Teknik Informatika',
                    'D-4 Rekayasa Otomotif',
                    'D-3 Akuntansi Terapan',
                    'D-3 Desain Grafis',
                    'D-3 Kebidanan',
                    'D-3 Farmasi',
                    'D-3 Keperawatan'
                ]
            ],
        ];

        $wizardSteps = [
            [
                'step' => 1,
                'title' => 'Identitas Usulan & TKT',
                'desc' => 'Pemilihan skema, rumpun ilmu Level 1-3, fokus RIRN, dan instrumen Self-Assessment TKT (1-9).'
            ],
            [
                'step' => 2,
                'title' => 'Tim Peneliti & Mitra',
                'desc' => 'Penambahan Dosen Anggota (via NIDN), Mahasiswa aktif (NIM untuk IKU-2), serta Profil Mitra Sasaran.'
            ],
            [
                'step' => 3,
                'title' => 'Substansi & Proposal PDF',
                'desc' => 'Ringkasan riset (max 500 kata) dan unggah draf berkas PDF/A terpadu (maksimal 5 MB).'
            ],
            [
                'step' => 4,
                'title' => 'RAB Berbasis SBM',
                'desc' => 'Kalkulator otomatis 5 pos belanja: Bahan, Data, Sewa Alat, Honorarium Pembantu, & Pelaporan.'
            ],
            [
                'step' => 5,
                'title' => 'Target Luaran Wajib/Tambahan',
                'desc' => 'Pemilihan luaran target: Jurnal SINTA/Scopus, Hak Cipta, Paten DJKI, atau Prototipe Laik Industri.'
            ],
            [
                'step' => 6,
                'title' => 'Konfirmasi & Member Consent',
                'desc' => 'Verifikasi persetujuan seluruh anggota tim (Member Consent) sebelum final submit ke LPPM.'
            ]
        ];

        $integrations = [
            [
                'name' => 'BIMA Kemdiktisaintek',
                'type' => 'Standar Nasional',
                'desc' => 'Mirroring 1:1 taksonomi hibah, Wizard 6 Langkah, dan format RAB SBM.',
                'icon' => 'layers'
            ],
            [
                'name' => 'SIAKAD Cloud UHN',
                'type' => 'Single Sign-On (SSO)',
                'desc' => 'Otentikasi tunggal akun dosen & mahasiswa terintegrasi dengan kode OTP Email Institusi.',
                'icon' => 'key'
            ],
            [
                'name' => 'SINTA Web Services',
                'type' => 'API Synchronizer',
                'desc' => 'Penarikan otomatis SINTA Score 3Yr, Overall, H-Index Scopus/Scholar, dan eligibilitas pengusul.',
                'icon' => 'refresh-cw'
            ],
            [
                'name' => 'DJKI Kemenkumham',
                'type' => 'HKI Validation',
                'desc' => 'Verifikasi nomor permohonan & sertifikat resmi Paten/Hak Cipta untuk kelayakan reward.',
                'icon' => 'shield-check'
            ],
            [
                'name' => 'MinIO S3 Storage',
                'type' => 'Encrypted Vault',
                'desc' => 'Penyimpanan berkas proposal, laporan monev, & SPK tersertifikasi AES-256.',
                'icon' => 'hard-drive'
            ],
        ];

        try {
            $recentPublications = \Illuminate\Support\Facades\Schema::hasTable('ppm_publikasi_jurnal')
                ? \App\Models\PpmPublikasiJurnal::with('user')->latest()->take(6)->get()
                : collect();
        } catch (\Throwable $e) {
            $recentPublications = collect();
        }

        return view('landing', compact(
            'systemInfo',
            'stats',
            'pillars',
            'schemes',
            'faculties',
            'wizardSteps',
            'integrations',
            'recentPublications'
        ));
    }

    /**
     * Katalog Publikasi Dosen Terbuka (Akses Tanpa Login).
     */
    public function publikasi(Request $request)
    {
        $search = $request->input('search');
        $kategori = $request->input('kategori_peringkat');

        $query = \App\Models\PpmPublikasiJurnal::with('user');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul_artikel', 'ilike', "%{$search}%")
                    ->orWhere('nama_jurnal', 'ilike', "%{$search}%")
                    ->orWhere('doi', 'ilike', "%{$search}%")
                    ->orWhereHas('user', fn($uq) => $uq->where('name', 'ilike', "%{$search}%"));
            });
        }

        if ($kategori) {
            $query->where('kategori_peringkat', $kategori);
        }

        $publikasiList = $query->latest()->paginate(12)->withQueryString();

        return view('publikasi.katalog-publik', compact('publikasiList', 'search', 'kategori'));
    }
}


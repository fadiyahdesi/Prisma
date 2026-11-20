<?php

namespace Database\Seeders;

use App\Models\PpmHki;
use App\Models\PpmKlaimReward;
use App\Models\PpmKontrak;
use App\Models\PpmLaporanAkhir;
use App\Models\PpmLogbook;
use App\Models\PpmMonevKemajuan;
use App\Models\PpmPencairanDana;
use App\Models\PpmPenilaianReviewer;
use App\Models\PpmPenugasanReviewer;
use App\Models\PpmPeriodeHibah;
use App\Models\PpmPublikasiJurnal;
use App\Models\PpmRewardDistribusi;
use App\Models\PpmSeminarHasil;
use App\Models\PpmSkemaBima;
use App\Models\PpmUsulan;
use App\Models\PpmUsulanAnggota;
use App\Models\PpmUsulanLuaran;
use App\Models\PpmUsulanRab;
use App\Models\RefFakultas;
use App\Models\RefTarifRewardSk;
use App\Models\Role;
use App\Models\User;
use App\Services\DemoPdfGeneratorService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class RealWorkflowSyncSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Pastikan seluruh Roles & Akun Operator Divisi/Keuangan/Dekanat + Kepala P3M tersinkronisasi
        $rolesByName = Role::all()->keyBy('name');
        $passwordHash = Hash::make('password');

        $fst = RefFakultas::where('kode_fakultas', 'FST')->first() ?? RefFakultas::first();
        $sv = RefFakultas::where('kode_fakultas', 'SV')->first() ?? $fst;
        $fsh = RefFakultas::where('kode_fakultas', 'FSH')->first() ?? $fst;
        $fpp = RefFakultas::where('kode_fakultas', 'FPP')->first() ?? $fst;

        $operatorDefs = [
            ['email' => 'adminp3m@harkatnegeri.ac.id', 'name' => 'Admin P3M UHN', 'nidn_nim' => 'ADM-P3M-01', 'role' => 'Admin P3M', 'fak' => $fst],
            ['email' => 'superadmin@harkatnegeri.ac.id', 'name' => 'Super Administrator UHN', 'nidn_nim' => 'ROOT-UHN-01', 'role' => 'Superadmin', 'fak' => $fst],
            ['email' => 'divisipenelitian@harkatnegeri.ac.id', 'name' => 'Divisi Penelitian P3M UHN', 'nidn_nim' => 'DIV-PEN-01', 'role' => 'Divisi Penelitian', 'fak' => $fst],
            ['email' => 'divisipengabdian@harkatnegeri.ac.id', 'name' => 'Divisi Pengabdian P3M UHN', 'nidn_nim' => 'DIV-PKM-01', 'role' => 'Divisi Pengabdian', 'fak' => $fsh],
            ['email' => 'divisipublikasi@harkatnegeri.ac.id', 'name' => 'Divisi Publikasi & HKI P3M UHN', 'nidn_nim' => 'DIV-PUB-01', 'role' => 'Divisi Publikasi', 'fak' => $sv],
            ['email' => 'keuangan@harkatnegeri.ac.id', 'name' => 'Divisi Keuangan P3M UHN', 'nidn_nim' => 'KEU-P3M-01', 'role' => 'Keuangan', 'fak' => $fst],
            ['email' => 'dekan@harkatnegeri.ac.id', 'name' => 'Dekanat Fakultas Sains & Teknologi UHN', 'nidn_nim' => 'DEK-FST-01', 'role' => 'Dekanat', 'fak' => $fst],
        ];

        $operators = [];
        foreach ($operatorDefs as $op) {
            $u = User::updateOrCreate(
                ['email' => $op['email']],
                [
                    'name' => $op['name'],
                    'nidn_nim' => $op['nidn_nim'],
                    'password' => $passwordHash,
                    'no_urut' => null,
                    'id_fakultas' => $op['fak']?->id_fakultas,
                    'email_verified_at' => now(),
                    'is_otp_verified' => true,
                ]
            );
            if (isset($rolesByName[$op['role']])) {
                $u->roles()->sync([$rolesByName[$op['role']]->id]);
            }
            $operators[$op['role']] = $u;
        }

        // Kepala P3M resmi: Sharfina Febbi Handayani, M.Kom. (dari 146 Dosen Tetap UHN)
        $sharfina = User::where('nidn_nim', '0617029201')->first()
            ?? User::where('nipy', '08.020.451')->first()
            ?? User::where('name', 'ilike', '%Sharfina%')->first();

        if ($sharfina && isset($rolesByName['Kepala P3M'], $rolesByName['Dosen / Pengusul'])) {
            $sharfina->update(['is_otp_verified' => true]);
            $sharfina->roles()->sync([
                $rolesByName['Kepala P3M']->id,
                $rolesByName['Dosen / Pengusul']->id,
            ]);
        }

        // 2. Ambil akun Dosen Tetap UHN real (no_urut NOT NULL) lintas fakultas untuk skenario sinkron end-to-end
        $pickLecturer = function (string $nidn, ?int $fakultasId = null, array $excludeIds = []): ?User {
            $exact = User::whereNotNull('no_urut')->where('nidn_nim', 'like', $nidn . '%')->first();
            if ($exact) {
                return $exact;
            }
            return User::whereNotNull('no_urut')
                ->when($fakultasId, fn ($q) => $q->where('id_fakultas', $fakultasId))
                ->when(!empty($excludeIds), fn ($q) => $q->whereNotIn('id', $excludeIds))
                ->orderByDesc('sinta_score_3yr')
                ->first();
        };

        $dairoh = $pickLecturer('0612108701', $sv?->id_fakultas);
        $fikri = $pickLecturer('0623108801', $sv?->id_fakultas, [$dairoh?->id ?? 0]);
        $ginanjar = $pickLecturer('0613028601', $sv?->id_fakultas, [$dairoh?->id ?? 0, $fikri?->id ?? 0]);
        $lukman = $pickLecturer('0629109301', $sv?->id_fakultas);
        $kusnadi = $pickLecturer('0616038701', $sv?->id_fakultas);
        $syarifudin = $pickLecturer('0627068803', $sv?->id_fakultas);
        $riska = $pickLecturer('0625039201', $sv?->id_fakultas);
        $arif = $pickLecturer('0623118301', $sv?->id_fakultas);
        $slamet = $pickLecturer('0626059001', $fst?->id_fakultas);
        $ida = $pickLecturer('0626017902', $fsh?->id_fakultas);
        $sobri = $pickLecturer('0608068801', $sv?->id_fakultas);

        // Reviewer non-Vokasi (FST, FSH, FPP) untuk menjaga CoI bebas benturan fakultas
        $revEko = User::whereNotNull('no_urut')->where('nidn_nim', 'like', '0605037304%')->first();
        $revArfan = User::whereNotNull('no_urut')->where('nidn_nim', 'like', '0623037704%')->first();
        $revRatri = User::whereNotNull('no_urut')->where('no_urut', 138)->first();
        $revInur = User::whereNotNull('no_urut')->where('no_urut', 146)->first();
        $revHeru = User::whereNotNull('no_urut')->where('nidn_nim', 'like', '0611058001%')->first();

        $allReviewers = collect([$revEko, $revArfan, $revRatri, $revInur, $revHeru])->filter();
        if (isset($rolesByName['Reviewer'])) {
            foreach ($allReviewers as $rev) {
                $rev->roles()->syncWithoutDetaching([$rolesByName['Reviewer']->id]);
                $rev->update([
                    'reviewer_application_status' => 'approved',
                    'reviewer_applied_at' => $rev->reviewer_applied_at ?? now()->subDays(10),
                ]);
            }
        }

        // Pastikan 1 dosen non-reviewer memiliki pengajuan diri Reviewer pending (untuk panel persetujuan Admin/Divisi)
        if ($sobri && !$sobri->hasRole('Reviewer')) {
            $sobri->update([
                'rumpun_ilmu' => 'Teknologi & Rekayasa',
                'bidang_keahlian' => 'Teknik Elektronika, Embedded System & Internet of Things',
                'reviewer_expertise_notes' => 'Aktif meneliti sistem kendali mikro dan instrumentasi industri terapan.',
                'reviewer_application_status' => 'pending',
                'reviewer_applied_at' => now()->subDays(1),
            ]);
        }

        // 3. Periode & Skema BIMA Aktif
        $periode = PpmPeriodeHibah::where('is_active', true)->orderByDesc('id')->first()
            ?? PpmPeriodeHibah::orderByDesc('id')->first();

        $skemaPdp = PpmSkemaBima::where('kode_skema', 'PDP')->first() ?? PpmSkemaBima::where('kategori', 'penelitian')->first();
        $skemaFnd = PpmSkemaBima::where('kode_skema', 'FUNDAMENTAL')->first() ?? $skemaPdp;
        $skemaTerapan = PpmSkemaBima::where('kode_skema', 'RIS-TERAPAN')->first() ?? $skemaPdp;
        $skemaPkm = PpmSkemaBima::where('kode_skema', 'PKM')->first() ?? PpmSkemaBima::where('kategori', 'pengabdian')->first();
        $skemaPmp = PpmSkemaBima::where('kode_skema', 'PMP')->first() ?? $skemaPkm;

        if (!$periode || !$skemaPdp || !$skemaPkm || !$dairoh) {
            return;
        }

        // 4. Generate Berkas PDF resmi agar seluruh tombol unduh berjalan mulus
        $pdfService = app(DemoPdfGeneratorService::class);
        $pdfService->generateProposal([
            'judul_usulan' => 'Sistem Pemantauan Kualitas Air Tambak Berbasis Edge-AI dan IoT LoRaWAN di Pesisir Pantura',
            'skema' => $skemaTerapan->nama_skema,
            'ketua_nama' => $dairoh->name,
            'ketua_nidn' => $dairoh->nidn_nim,
            'total_rab' => 50000000,
        ], 'proposals/uhn_pen_ongoing_01.pdf');
        $pdfService->generateSpkKontrak([], 'kontrak/uhn_spk_ongoing_01.pdf');
        $pdfService->generateBukuTabungan([], 'tabungan/uhn_rek_sample.pdf');
        $pdfService->generateBuktiTransfer([], 'bukti_transfer/uhn_termin1_sample.pdf');
        $pdfService->generateBuktiTransfer([
            'nominal' => 13500000,
            'terbilang' => 'Tiga Belas Juta Lima Ratus Ribu Rupiah',
            'keterangan' => 'Pelunasan Termin II (30%) Hibah Penelitian LPPM UHN 2026',
        ], 'bukti_transfer/uhn_termin2_sample.pdf');
        $pdfService->generateLaporanKemajuan([], 'monev/uhn_lap_kemajuan_sample.pdf');
        $pdfService->generateSptb(['persen' => 70, 'nomor_sptb' => 'SPTB-70/P3M-UHN/2026', 'nominal_termin' => 35000000], 'monev/uhn_sptb_70_sample.pdf');
        $pdfService->generateLaporanAkhir([], 'laporan-akhir/uhn_lap_akhir_sample.pdf');
        $pdfService->generateSptb(['persen' => 100, 'nomor_sptb' => 'SPTB-100/P3M-UHN/2026', 'nominal_termin' => 13500000], 'laporan-akhir/uhn_sptb_100_sample.pdf');
        $pdfService->generateHkiCertificate([], 'hki_sertifikat/uhn_hki_sertifikat_sample.pdf');
        $pdfService->generateHkiManualBook([], 'hki_dokumen/uhn_manual_book_sample.pdf');
        $pdfService->generateHkiPernyataan([], 'hki_dokumen/uhn_pernyataan_sample.pdf');
        $pdfService->generateHkiPengalihan([], 'hki_dokumen/uhn_pengalihan_sample.pdf');
        $pdfService->generatePaper([], 'publikasi_naskah/uhn_paper_sample.pdf');
        $pdfService->generateRewardKesepakatan([], 'surat_pernyataan_reward/uhn_reward_kesepakatan.pdf');
        $pdfService->generateBuktiTransfer([
            'nominal' => 5000000,
            'terbilang' => 'Lima Juta Rupiah',
            'keterangan' => 'Pencairan Insentif Publikasi / HKI Resmi LPPM UHN 2026',
        ], 'bukti_transfer_reward/uhn_reward_transfer.pdf');

        // Helper ringkas membuat RAB & Luaran pada usulan live
        $seedRabAndLuaran = function (PpmUsulan $usulan, float $honor, float $bahan, float $lapangan, float $pelaporan, string $luaranWajib): void {
            PpmUsulanRab::where('id_usulan', $usulan->id)->delete();
            PpmUsulanLuaran::where('id_usulan', $usulan->id)->delete();

            $items = [
                ['pos_belanja' => 'Honorarium', 'item_keterangan' => 'Honorarium Pengolahan Data & Asisten Lapangan', 'volume' => 1, 'satuan' => 'Paket', 'harga_satuan' => $honor],
                ['pos_belanja' => 'Bahan / Alat Habis Pakai', 'item_keterangan' => 'Komponen Riset, Sensor, & Bahan Habis Lab', 'volume' => 1, 'satuan' => 'Paket', 'harga_satuan' => $bahan],
                ['pos_belanja' => 'Pengumpulan Data / Lapangan', 'item_keterangan' => 'Survey Mitra, Uji Lapangan, & Transportasi', 'volume' => 1, 'satuan' => 'Kegiatan', 'harga_satuan' => $lapangan],
                ['pos_belanja' => 'Pelaporan & Publikasi', 'item_keterangan' => 'Biaya Publikasi Jurnal SINTA/Scopus & Pendaftaran HKI', 'volume' => 1, 'satuan' => 'Paket', 'harga_satuan' => $pelaporan],
            ];
            foreach ($items as $it) {
                PpmUsulanRab::create(array_merge($it, [
                    'id_usulan' => $usulan->id,
                    'total_harga' => $it['volume'] * $it['harga_satuan'],
                ]));
            }

            PpmUsulanLuaran::create([
                'id_usulan' => $usulan->id,
                'jenis_luaran' => 'wajib',
                'kategori_luaran' => $luaranWajib,
                'target_status' => 'Published / Granted',
            ]);
            PpmUsulanLuaran::create([
                'id_usulan' => $usulan->id,
                'jenis_luaran' => 'tambahan',
                'kategori_luaran' => 'Hak Cipta (HKI)',
                'target_status' => 'Terdaftar DJKI',
            ]);
        };

        $semesterLabel = trim($periode->tahun_akademik . ' ' . $periode->semester);

        // =========================================================================
        // 5A. SUBMITTED (ANTREAN VERIFIKASI DIVISI PENELITIAN & PENGABDIAN)
        // =========================================================================
        if ($lukman && $dairoh) {
            $uSubPen = PpmUsulan::updateOrCreate(
                ['kode_usulan' => 'USL-2026-PEN-SUB01'],
                [
                    'no_urut' => null,
                    'id_pengusul' => $lukman->id,
                    'id_skema_bima' => $skemaPdp->id,
                    'id_periode_hibah' => $periode->id,
                    'kategori_usulan' => 'penelitian',
                    'semester' => $semesterLabel,
                    'nama_ketua' => $lukman->name,
                    'nidn_ketua' => $lukman->nidn_nim,
                    'id_prodi' => $lukman->id_prodi,
                    'nama_prodi' => $lukman->prodi?->nama_prodi,
                    'nama_anggota' => $dairoh->name,
                    'judul_usulan' => 'Optimasi Klasifikasi Citra Penyakit Tanaman Padi Menggunakan Lightweight CNN pada Edge Device',
                    'rumpun_ilmu_level_1' => 'Sains & Matematika',
                    'rumpun_ilmu_level_2' => 'Ilmu Komputer',
                    'rumpun_ilmu_level_3' => 'Kecerdasan Buatan (AI)',
                    'fokus_rirn' => 'Teknologi Informasi & Komunikasi (TIK)',
                    'target_tkt' => 3,
                    'ringkasan_substansi' => 'Pengembangan model deep learning ringan yang dapat dioperasikan secara luring oleh penyuluh pertanian melalui perangkat mobile Android.',
                    'file_proposal_path' => 'proposals/uhn_pen_ongoing_01.pdf',
                    'total_honorarium' => 5000000,
                    'total_rab' => 24000000,
                    'status' => 'Submitted',
                    'submitted_at' => now()->subDays(2),
                ]
            );
            $seedRabAndLuaran($uSubPen, 5000000, 9000000, 5000000, 5000000, 'Jurnal Nasional Terakreditasi SINTA 2');
            PpmUsulanAnggota::updateOrCreate(
                ['id_usulan' => $uSubPen->id, 'identifier' => $dairoh->nidn_nim],
                [
                    'user_id' => $dairoh->id,
                    'jenis_anggota' => 'dosen',
                    'nama' => $dairoh->name,
                    'peran_anggota' => 'Anggota Peneliti 1',
                    'status_persetujuan' => 'pending',
                ]
            );
        }

        if ($ida) {
            $uSubPkm = PpmUsulan::updateOrCreate(
                ['kode_usulan' => 'USL-2026-PKM-SUB01'],
                [
                    'no_urut' => null,
                    'id_pengusul' => $ida->id,
                    'id_skema_bima' => $skemaPmp->id,
                    'id_periode_hibah' => $periode->id,
                    'kategori_usulan' => 'pengabdian',
                    'semester' => $semesterLabel,
                    'nama_ketua' => $ida->name,
                    'nidn_ketua' => $ida->nidn_nim,
                    'id_prodi' => $ida->id_prodi,
                    'nama_prodi' => $ida->prodi?->nama_prodi,
                    'judul_usulan' => 'Digitalisasi Pembukuan Keuangan dan Manajemen Pajak UMKM Batik Tegal Berbasis Aplikasi Kasir Cloud',
                    'rumpun_ilmu_level_1' => 'Sosial & Humaniora',
                    'rumpun_ilmu_level_2' => 'Ekonomi & Bisnis',
                    'rumpun_ilmu_level_3' => 'Akuntansi Publik',
                    'fokus_rirn' => 'Pengembangan Ekonomi Digital & UMKM',
                    'sdgs_indikator' => json_encode([1, 8, 9]),
                    'nama_mitra' => 'Paguyuban Pengrajin Batik Pesarean Tegal',
                    'mitra_jarak_km' => 6.4,
                    'target_tkt' => 4,
                    'ringkasan_substansi' => 'Pendampingan penyusunan laporan keuangan SAK EMKM dan integrasi sistem kasir digital bagi 20 pengrajin batik lokal.',
                    'file_proposal_path' => 'proposals/uhn_pen_ongoing_01.pdf',
                    'total_honorarium' => 5000000,
                    'total_rab' => 22500000,
                    'status' => 'Submitted',
                    'submitted_at' => now()->subDays(1),
                ]
            );
            $seedRabAndLuaran($uSubPkm, 5000000, 7500000, 6000000, 4000000, 'Jurnal Pengabdian Masyarakat Terakreditasi');
        }

        // =========================================================================
        // 5B. IN_REVIEW & ADJUDICATION (PLOTTING & PENILAIAN REVIEWER)
        // =========================================================================
        $divPenUser = $operators['Divisi Penelitian'] ?? $operators['Admin P3M'];
        $divPkmUser = $operators['Divisi Pengabdian'] ?? $operators['Admin P3M'];
        $kepalaUser = $sharfina ?? $operators['Admin P3M'];

        if ($fikri && $revEko && $revArfan) {
            $uRevPen = PpmUsulan::updateOrCreate(
                ['kode_usulan' => 'USL-2026-PEN-REV01'],
                [
                    'no_urut' => null,
                    'id_pengusul' => $fikri->id,
                    'id_skema_bima' => $skemaFnd->id,
                    'id_periode_hibah' => $periode->id,
                    'kategori_usulan' => 'penelitian',
                    'semester' => $semesterLabel,
                    'nama_ketua' => $fikri->name,
                    'nidn_ketua' => $fikri->nidn_nim,
                    'id_prodi' => $fikri->id_prodi,
                    'nama_prodi' => $fikri->prodi?->nama_prodi,
                    'judul_usulan' => 'Arsitektur Keamanan Data Akademik Terdesentralisasi Menggunakan Blockchain Hyperledger Fabric',
                    'rumpun_ilmu_level_1' => 'Sains & Matematika',
                    'rumpun_ilmu_level_2' => 'Ilmu Komputer',
                    'rumpun_ilmu_level_3' => 'Keamanan Siber',
                    'fokus_rirn' => 'Teknologi Informasi & Komunikasi (TIK)',
                    'target_tkt' => 3,
                    'ringkasan_substansi' => 'Rancang bangun penyimpanan ijazah dan transkrip akademik berbasis smart contract anti-pemalsuan.',
                    'file_proposal_path' => 'proposals/uhn_pen_ongoing_01.pdf',
                    'template_status' => 'sesuai',
                    'similarity_score' => 11.4,
                    'similarity_status' => 'lolos',
                    'verified_by' => $divPenUser?->id,
                    'verified_at' => now()->subDays(3),
                    'verification_notes' => 'Format BIMA sesuai dan similarity 11.4% memenuhi batas maksimal 20%.',
                    'kaprodi_alignment_status' => 'Aligned',
                    'kaprodi_recommendation' => 'Sesuai peta jalan riset D-4 Teknik Informatika.',
                    'total_honorarium' => 12000000,
                    'total_rab' => 65000000,
                    'status' => 'In_review',
                    'submitted_at' => now()->subDays(5),
                ]
            );
            $seedRabAndLuaran($uRevPen, 12000000, 28000000, 15000000, 10000000, 'Jurnal Internasional Terindeks Scopus');

            PpmPenugasanReviewer::updateOrCreate(
                ['id_usulan' => $uRevPen->id, 'peran_reviewer' => 'reviewer_1'],
                ['id_reviewer' => $revEko->id, 'status_penugasan' => 'assigned', 'assigned_by' => $divPenUser?->id, 'assigned_at' => now()->subDays(2)]
            );
            PpmPenugasanReviewer::updateOrCreate(
                ['id_usulan' => $uRevPen->id, 'peran_reviewer' => 'reviewer_2'],
                ['id_reviewer' => $revArfan->id, 'status_penugasan' => 'assigned', 'assigned_by' => $divPenUser?->id, 'assigned_at' => now()->subDays(2)]
            );
        }

        if ($kusnadi && $revRatri && $revInur) {
            $uRevPkm = PpmUsulan::updateOrCreate(
                ['kode_usulan' => 'USL-2026-PKM-REV01'],
                [
                    'no_urut' => null,
                    'id_pengusul' => $kusnadi->id,
                    'id_skema_bima' => $skemaPkm->id,
                    'id_periode_hibah' => $periode->id,
                    'kategori_usulan' => 'pengabdian',
                    'semester' => $semesterLabel,
                    'nama_ketua' => $kusnadi->name,
                    'nidn_ketua' => $kusnadi->nidn_nim,
                    'id_prodi' => $kusnadi->id_prodi,
                    'nama_prodi' => $kusnadi->prodi?->nama_prodi,
                    'judul_usulan' => 'Edukasi Swamedikasi Herbal Terstandar bagi Kader Posyandu Lansia Kecamatan Tegal Timur',
                    'rumpun_ilmu_level_1' => 'Kesehatan & Farmasi',
                    'rumpun_ilmu_level_2' => 'Ilmu Kesehatan',
                    'rumpun_ilmu_level_3' => 'Farmasi Klinis',
                    'fokus_rirn' => 'Kesehatan & Obat-obatan',
                    'sdgs_indikator' => json_encode([3, 4]),
                    'nama_mitra' => 'Puskesmas Slerok Kota Tegal',
                    'mitra_jarak_km' => 4.2,
                    'target_tkt' => 5,
                    'ringkasan_substansi' => 'Peningkatan kapasitas kader posyandu dalam pemanfaatan tanaman obat keluarga secara rasional dan aman.',
                    'file_proposal_path' => 'proposals/uhn_pen_ongoing_01.pdf',
                    'template_status' => 'sesuai',
                    'similarity_score' => 9.2,
                    'similarity_status' => 'lolos',
                    'verified_by' => $divPkmUser?->id,
                    'verified_at' => now()->subDays(3),
                    'total_honorarium' => 7000000,
                    'total_rab' => 38000000,
                    'status' => 'In_review',
                    'submitted_at' => now()->subDays(6),
                ]
            );
            $seedRabAndLuaran($uRevPkm, 7000000, 15000000, 11000000, 5000000, 'Prosiding / Jurnal Abmas Terakreditasi');

            PpmPenugasanReviewer::updateOrCreate(
                ['id_usulan' => $uRevPkm->id, 'peran_reviewer' => 'reviewer_1'],
                ['id_reviewer' => $revRatri->id, 'status_penugasan' => 'assigned', 'assigned_by' => $divPkmUser?->id, 'assigned_at' => now()->subDays(2)]
            );
            PpmPenugasanReviewer::updateOrCreate(
                ['id_usulan' => $uRevPkm->id, 'peran_reviewer' => 'reviewer_2'],
                ['id_reviewer' => $revInur->id, 'status_penugasan' => 'assigned', 'assigned_by' => $divPkmUser?->id, 'assigned_at' => now()->subDays(2)]
            );
        }

        // 1 Usulan Penelitian Disparitas -> Siap Adjudikasi Reviewer 3
        if ($slamet && $revRatri && $revHeru) {
            $uAdj = PpmUsulan::updateOrCreate(
                ['kode_usulan' => 'USL-2026-PEN-ADJ01'],
                [
                    'no_urut' => null,
                    'id_pengusul' => $slamet->id,
                    'id_skema_bima' => $skemaPdp->id,
                    'id_periode_hibah' => $periode->id,
                    'kategori_usulan' => 'penelitian',
                    'semester' => $semesterLabel,
                    'nama_ketua' => $slamet->name,
                    'nidn_ketua' => $slamet->nidn_nim,
                    'id_prodi' => $slamet->id_prodi,
                    'nama_prodi' => $slamet->prodi?->nama_prodi,
                    'judul_usulan' => 'Prediksi Dini Kegagalan Mesin CNC Menggunakan Analisis Getaran Berbasis Machine Learning',
                    'rumpun_ilmu_level_1' => 'Teknologi & Rekayasa',
                    'rumpun_ilmu_level_2' => 'Teknik Mesin & Sipil',
                    'rumpun_ilmu_level_3' => 'Manufaktur Industri',
                    'fokus_rirn' => 'Transportasi & Otomotif',
                    'target_tkt' => 3,
                    'ringkasan_substansi' => 'Pemodelan sinyal akselerometer multi-sumbu guna mendeteksi keausan pahat mesin CNC secara prediktif.',
                    'file_proposal_path' => 'proposals/uhn_pen_ongoing_01.pdf',
                    'template_status' => 'sesuai',
                    'similarity_score' => 14.0,
                    'similarity_status' => 'lolos',
                    'verified_by' => $divPenUser?->id,
                    'verified_at' => now()->subDays(7),
                    'skor_reviewer_1' => 625.00,
                    'skor_reviewer_2' => 410.00,
                    'is_disparity' => true,
                    'total_honorarium' => 5500000,
                    'total_rab' => 23500000,
                    'status' => 'Adjudication',
                    'submitted_at' => now()->subDays(10),
                ]
            );
            $seedRabAndLuaran($uAdj, 5500000, 10000000, 4000000, 4000000, 'Jurnal Nasional Terakreditasi SINTA 3');
            $p1 = PpmPenugasanReviewer::updateOrCreate(
                ['id_usulan' => $uAdj->id, 'peran_reviewer' => 'reviewer_1'],
                ['id_reviewer' => $revRatri->id, 'status_penugasan' => 'completed', 'assigned_by' => $divPenUser?->id, 'assigned_at' => now()->subDays(6), 'completed_at' => now()->subDays(4)]
            );
            PpmPenilaianReviewer::updateOrCreate(
                ['id_penugasan' => $p1->id],
                ['skor_kriteria' => ['C1' => 6, 'C2' => 7, 'C3' => 6, 'C4' => 6], 'total_skor' => 625.00, 'komentar_kualitatif' => 'Metodologi eksperimen terukur dengan target TKT 3 yang jelas.', 'rekomendasi' => 'layak', 'is_locked' => true, 'submitted_at' => now()->subDays(4)]
            );
            $p2 = PpmPenugasanReviewer::updateOrCreate(
                ['id_usulan' => $uAdj->id, 'peran_reviewer' => 'reviewer_2'],
                ['id_reviewer' => $revHeru->id, 'status_penugasan' => 'completed', 'assigned_by' => $divPenUser?->id, 'assigned_at' => now()->subDays(6), 'completed_at' => now()->subDays(3)]
            );
            PpmPenilaianReviewer::updateOrCreate(
                ['id_penugasan' => $p2->id],
                ['skor_kriteria' => ['C1' => 4, 'C2' => 4, 'C3' => 4, 'C4' => 5], 'total_skor' => 410.00, 'komentar_kualitatif' => 'Perlu kejelasan spesifikasi dataset pengujian mesin CNC industri.', 'rekomendasi' => 'revisi', 'is_locked' => true, 'submitted_at' => now()->subDays(3)]
            );
        }

        // =========================================================================
        // 5C. REVIEWED & RANKED (PEMERINGKATAN & PENETAPAN PEMENANG KEPALA P3M)
        // =========================================================================
        $rankedSeeds = [
            [
                'kode' => 'USL-2026-PEN-RNK01',
                'user' => $ginanjar,
                'skema' => $skemaFnd,
                'kat' => 'penelitian',
                'judul' => 'Model Rekomendasi Pengendalian Stunting Balita Terintegrasi Data Posyandu Kabupaten Tegal',
                'rumpun' => 'Sains & Matematika',
                'rab' => 72000000,
                's1' => 660.00,
                's2' => 645.00,
                'final' => 652.50,
            ],
            [
                'kode' => 'USL-2026-PEN-RNK02',
                'user' => $syarifudin,
                'skema' => $skemaTerapan,
                'kat' => 'penelitian',
                'judul' => 'Rancang Bangun Mesin Pengering Gabah Hemat Energi Berbasis Kolektor Surya Hibrida',
                'rumpun' => 'Teknologi & Rekayasa',
                'rab' => 58000000,
                's1' => 620.00,
                's2' => 616.00,
                'final' => 618.00,
            ],
            [
                'kode' => 'USL-2026-PKM-RNK01',
                'user' => $lukman,
                'skema' => $skemaPkm,
                'kat' => 'pengabdian',
                'judul' => 'Pelatihan Pemanfaatan AI Generatif untuk Pengembangan Bahan Ajar Guru SD Kabupaten Brebes',
                'rumpun' => 'Sains & Matematika',
                'rab' => 36000000,
                's1' => 598.00,
                's2' => 590.00,
                'final' => 594.00,
            ],
        ];

        foreach ($rankedSeeds as $rs) {
            if (!$rs['user'] || !$revRatri || !$revInur) {
                continue;
            }
            $uRnk = PpmUsulan::updateOrCreate(
                ['kode_usulan' => $rs['kode']],
                [
                    'no_urut' => null,
                    'id_pengusul' => $rs['user']->id,
                    'id_skema_bima' => $rs['skema']->id,
                    'id_periode_hibah' => $periode->id,
                    'kategori_usulan' => $rs['kat'],
                    'semester' => $semesterLabel,
                    'nama_ketua' => $rs['user']->name,
                    'nidn_ketua' => $rs['user']->nidn_nim,
                    'id_prodi' => $rs['user']->id_prodi,
                    'nama_prodi' => $rs['user']->prodi?->nama_prodi,
                    'judul_usulan' => $rs['judul'],
                    'rumpun_ilmu_level_1' => $rs['rumpun'],
                    'rumpun_ilmu_level_2' => 'Ilmu Terapan',
                    'rumpun_ilmu_level_3' => 'Inovasi Riset UHN',
                    'fokus_rirn' => 'Teknologi Informasi & Komunikasi (TIK)',
                    'target_tkt' => 4,
                    'ringkasan_substansi' => 'Usulan telah melalui telaah substansi double-blind dan siap ditetapkan dalam rapat pleno Kepala P3M.',
                    'file_proposal_path' => 'proposals/uhn_pen_ongoing_01.pdf',
                    'template_status' => 'sesuai',
                    'similarity_score' => 10.5,
                    'similarity_status' => 'lolos',
                    'verified_by' => $divPenUser?->id,
                    'verified_at' => now()->subDays(12),
                    'skor_reviewer_1' => $rs['s1'],
                    'skor_reviewer_2' => $rs['s2'],
                    'skor_akhir' => $rs['final'],
                    'is_disparity' => false,
                    'total_honorarium' => round($rs['rab'] * 0.25),
                    'total_rab' => $rs['rab'],
                    'status' => 'Reviewed',
                    'submitted_at' => now()->subDays(15),
                ]
            );
            $seedRabAndLuaran($uRnk, round($rs['rab'] * 0.25), round($rs['rab'] * 0.40), round($rs['rab'] * 0.20), round($rs['rab'] * 0.15), 'Jurnal Nasional SINTA 2');

            $asg1 = PpmPenugasanReviewer::updateOrCreate(
                ['id_usulan' => $uRnk->id, 'peran_reviewer' => 'reviewer_1'],
                ['id_reviewer' => $revEko?->id ?? $revRatri->id, 'status_penugasan' => 'completed', 'assigned_by' => $divPenUser?->id, 'assigned_at' => now()->subDays(10), 'completed_at' => now()->subDays(8)]
            );
            PpmPenilaianReviewer::updateOrCreate(
                ['id_penugasan' => $asg1->id],
                ['skor_kriteria' => ['C1' => 6, 'C2' => 7, 'C3' => 6, 'C4' => 7], 'total_skor' => $rs['s1'], 'komentar_kualitatif' => 'Proposal sangat matang dan berdampak tinggi bagi institusi.', 'rekomendasi' => 'layak', 'is_locked' => true, 'submitted_at' => now()->subDays(8)]
            );
            $asg2 = PpmPenugasanReviewer::updateOrCreate(
                ['id_usulan' => $uRnk->id, 'peran_reviewer' => 'reviewer_2'],
                ['id_reviewer' => $revRatri->id, 'status_penugasan' => 'completed', 'assigned_by' => $divPenUser?->id, 'assigned_at' => now()->subDays(10), 'completed_at' => now()->subDays(7)]
            );
            PpmPenilaianReviewer::updateOrCreate(
                ['id_penugasan' => $asg2->id],
                ['skor_kriteria' => ['C1' => 6, 'C2' => 6, 'C3' => 6, 'C4' => 7], 'total_skor' => $rs['s2'], 'komentar_kualitatif' => 'RAB wajar dan target luaran tercapai dalam 1 tahun anggaran.', 'rekomendasi' => 'layak', 'is_locked' => true, 'submitted_at' => now()->subDays(7)]
            );
        }

        // =========================================================================
        // 5D. ONGOING, CONTRACTED, COMPLETED -> KONTRAK, LOGBOOK, MONEV, SEMHAS, KEUANGAN
        // =========================================================================
        $keuanganUser = $operators['Keuangan'];

        // Project 1: ONGOING (Termin I transferred + 3 Logbooks + Monev submitted awaiting Reviewer!)
        $uOng = PpmUsulan::updateOrCreate(
            ['kode_usulan' => 'USL-2026-PEN-ONG01'],
            [
                'no_urut' => null,
                'id_pengusul' => $dairoh->id,
                'id_skema_bima' => $skemaTerapan->id,
                'id_periode_hibah' => $periode->id,
                'kategori_usulan' => 'penelitian',
                'semester' => $semesterLabel,
                'nama_ketua' => $dairoh->name,
                'nidn_ketua' => $dairoh->nidn_nim,
                'id_prodi' => $dairoh->id_prodi,
                'nama_prodi' => $dairoh->prodi?->nama_prodi,
                'judul_usulan' => 'Sistem Pemantauan Kualitas Air Tambak Berbasis Edge-AI dan IoT LoRaWAN di Pesisir Pantura',
                'rumpun_ilmu_level_1' => 'Sains & Matematika',
                'rumpun_ilmu_level_2' => 'Ilmu Komputer',
                'rumpun_ilmu_level_3' => 'Artificial Intelligence & IoT',
                'fokus_rirn' => 'Pangan & Pertanian',
                'target_tkt' => 5,
                'ringkasan_substansi' => 'Implementasi jaringan sensor multiparameter pH, DO, dan salinitas bertenaga surya untuk petambak pesisir Tegal.',
                'file_proposal_path' => 'proposals/uhn_pen_ongoing_01.pdf',
                'template_status' => 'sesuai',
                'similarity_score' => 8.4,
                'similarity_status' => 'lolos',
                'skor_reviewer_1' => 670.00,
                'skor_reviewer_2' => 660.00,
                'skor_akhir' => 665.00,
                'total_honorarium' => 12000000,
                'total_rab' => 50000000,
                'status' => 'Ongoing',
                'submitted_at' => now()->subMonths(2),
            ]
        );
        $seedRabAndLuaran($uOng, 12000000, 20000000, 10000000, 8000000, 'Jurnal Internasional Terindeks Scopus');

        $kOng = PpmKontrak::updateOrCreate(
            ['id_usulan' => $uOng->id],
            [
                'nomor_sk' => '012/SK-PEMENANG/P3M-UHN/2026',
                'nomor_kontrak' => '012/SPK-BIMA/P3M-UHN/2026',
                'tanggal_sk' => now()->subMonths(2),
                'tanggal_kontrak' => now()->subMonths(2),
                'pagu_disetujui' => 50000000,
                'dana_termin_1' => 35000000,
                'dana_termin_2' => 15000000,
                'nama_bank' => 'Bank Mandiri',
                'nomor_rekening' => '1390022481902',
                'nama_pemilik_rekening' => $dairoh->name,
                'file_buku_tabungan' => 'tabungan/uhn_rek_sample.pdf',
                'rekening_verified_at' => now()->subDays(45),
                'rekening_verified_by' => $keuanganUser?->id,
                'signed_by_kepala' => true,
                'signed_by_kepala_at' => now()->subDays(50),
                'signed_by_pengusul' => true,
                'signed_by_pengusul_at' => now()->subDays(48),
                'status' => 'ongoing',
                'verification_token' => hash('sha256', 'SPK-UHN-ONG01'),
            ]
        );

        PpmPencairanDana::updateOrCreate(
            ['id_kontrak' => $kOng->id, 'termin' => 1],
            [
                'persentase' => 70.00,
                'jumlah_dana' => 35000000,
                'nomor_referensi' => 'SP2D-UHN-2026-0112',
                'tanggal_transfer' => now()->subDays(40)->toDateString(),
                'file_bukti_transfer' => 'bukti_transfer/uhn_termin1_sample.pdf',
                'status_pencairan' => 'transferred',
                'processed_by' => $keuanganUser?->id,
                'processed_at' => now()->subDays(40),
                'catatan' => 'Pencairan Termin I 70% disalurkan ke rekening ketua peneliti.',
            ]
        );

        $logEntries = [
            ['hari' => 35, 'pct' => 25, 'akt' => 'Pengadaan node sensor kualitas air (pH, DO, salinitas), gateway LoRaWAN, dan panel surya 50Wp.'],
            ['hari' => 20, 'pct' => 50, 'akt' => 'Integrasi firmware edge inference untuk prediksi penurunan kualitas air serta kalibrasi sensor di lab Vokasi.'],
            ['hari' => 5, 'pct' => 75, 'akt' => 'Pemasangan stasiun pemantauan tahap pilot pada tambak mitra Kecamatan Margadana Kota Tegal dan pengambilan data awal.'],
        ];
        foreach ($logEntries as $le) {
            PpmLogbook::updateOrCreate(
                ['id_usulan' => $uOng->id, 'tanggal' => now()->subDays($le['hari'])->toDateString()],
                [
                    'aktivitas' => $le['akt'],
                    'persentase_capaian' => $le['pct'],
                    'created_by' => $dairoh->id,
                ]
            );
        }

        PpmMonevKemajuan::updateOrCreate(
            ['id_usulan' => $uOng->id],
            [
                'file_laporan_kemajuan' => 'monev/uhn_lap_kemajuan_sample.pdf',
                'file_sptb_70' => 'monev/uhn_sptb_70_sample.pdf',
                'ringkasan_kemajuan' => 'Capaian teknis mencapai 75% meliputi instalasi hardware di lokasi tambak dan draf naskah publikasi Q2 siap submit.',
                'persentase_kemajuan' => 75.00,
                'status' => 'submitted',
            ]
        );

        // Project 2: CONTRACTED (Menunggu TTD Peneliti & Verifikasi Rekening Keuangan)
        if ($ginanjar) {
            $uCtr = PpmUsulan::updateOrCreate(
                ['kode_usulan' => 'USL-2026-PKM-CTR01'],
                [
                    'no_urut' => null,
                    'id_pengusul' => $ginanjar->id,
                    'id_skema_bima' => $skemaPkm->id,
                    'id_periode_hibah' => $periode->id,
                    'kategori_usulan' => 'pengabdian',
                    'semester' => $semesterLabel,
                    'nama_ketua' => $ginanjar->name,
                    'nidn_ketua' => $ginanjar->nidn_nim,
                    'id_prodi' => $ginanjar->id_prodi,
                    'nama_prodi' => $ginanjar->prodi?->nama_prodi,
                    'judul_usulan' => 'Penerapan Sistem Informasi Pelayanan Surat Desa Terpadu di Kecamatan Slawi Kabupaten Tegal',
                    'rumpun_ilmu_level_1' => 'Sains & Matematika',
                    'rumpun_ilmu_level_2' => 'Ilmu Komputer',
                    'rumpun_ilmu_level_3' => 'Sistem Informasi',
                    'fokus_rirn' => 'Pengembangan Ekonomi Digital & UMKM',
                    'sdgs_indikator' => json_encode([9, 11, 16]),
                    'nama_mitra' => 'Pemerintah Desa Kalisapu Slawi',
                    'mitra_jarak_km' => 11.2,
                    'target_tkt' => 5,
                    'ringkasan_substansi' => 'Implementasi portal administrasi desa mandiri dan pelatihan operator pelayanan masyarakat.',
                    'file_proposal_path' => 'proposals/uhn_pen_ongoing_01.pdf',
                    'template_status' => 'sesuai',
                    'similarity_score' => 7.6,
                    'similarity_status' => 'lolos',
                    'skor_reviewer_1' => 648.00,
                    'skor_reviewer_2' => 636.00,
                    'skor_akhir' => 642.00,
                    'total_honorarium' => 8000000,
                    'total_rab' => 35000000,
                    'status' => 'Contracted',
                    'submitted_at' => now()->subDays(20),
                ]
            );
            $seedRabAndLuaran($uCtr, 8000000, 12000000, 10000000, 5000000, 'Jurnal Pengabdian SINTA 3');

            PpmKontrak::updateOrCreate(
                ['id_usulan' => $uCtr->id],
                [
                    'nomor_sk' => '015/SK-PEMENANG/P3M-UHN/2026',
                    'nomor_kontrak' => '015/SPK-BIMA/P3M-UHN/2026',
                    'tanggal_sk' => now()->subDays(4),
                    'tanggal_kontrak' => now()->subDays(4),
                    'pagu_disetujui' => 35000000,
                    'dana_termin_1' => 24500000,
                    'dana_termin_2' => 10500000,
                    'nama_bank' => 'BNI',
                    'nomor_rekening' => '0819945120',
                    'nama_pemilik_rekening' => $ginanjar->name,
                    'file_buku_tabungan' => 'tabungan/uhn_rek_sample.pdf',
                    'rekening_verified_at' => null,
                    'signed_by_kepala' => true,
                    'signed_by_kepala_at' => now()->subDays(4),
                    'signed_by_pengusul' => true,
                    'signed_by_pengusul_at' => now()->subDays(2),
                    'status' => 'signed_both',
                    'verification_token' => hash('sha256', 'SPK-UHN-CTR01'),
                ]
            );
        }

        // Project 3: ONGOING IN SEMHAS & READY FOR TERMIN II (30%)!
        if ($riska && $revEko && $revArfan) {
            $uSmh = PpmUsulan::updateOrCreate(
                ['kode_usulan' => 'USL-2026-PKM-SMH01'],
                [
                    'no_urut' => null,
                    'id_pengusul' => $riska->id,
                    'id_skema_bima' => $skemaPkm->id,
                    'id_periode_hibah' => $periode->id,
                    'kategori_usulan' => 'pengabdian',
                    'semester' => $semesterLabel,
                    'nama_ketua' => $riska->name,
                    'nidn_ketua' => $riska->nidn_nim,
                    'id_prodi' => $riska->id_prodi,
                    'nama_prodi' => $riska->prodi?->nama_prodi,
                    'judul_usulan' => 'Pendampingan Gizi Ibu Hamil Risiko KEK dengan Modul Asuhan Kebidanan Terpadu di Puskesmas Tarub',
                    'rumpun_ilmu_level_1' => 'Kesehatan & Farmasi',
                    'rumpun_ilmu_level_2' => 'Ilmu Kesehatan',
                    'rumpun_ilmu_level_3' => 'Kebidanan & Kesehatan Ibu',
                    'fokus_rirn' => 'Kesehatan & Obat-obatan',
                    'sdgs_indikator' => json_encode([2, 3, 5]),
                    'nama_mitra' => 'Puskesmas Tarub Kab. Tegal',
                    'mitra_jarak_km' => 14.0,
                    'target_tkt' => 5,
                    'ringkasan_substansi' => 'Edukasi intervensi gizi terstruktur dan pemantauan antropometri berkala bersama bidan desa mitra.',
                    'file_proposal_path' => 'proposals/uhn_pen_ongoing_01.pdf',
                    'template_status' => 'sesuai',
                    'similarity_score' => 6.8,
                    'similarity_status' => 'lolos',
                    'skor_reviewer_1' => 655.00,
                    'skor_reviewer_2' => 649.00,
                    'skor_akhir' => 652.00,
                    'total_honorarium' => 7500000,
                    'total_rab' => 30000000,
                    'status' => 'Ongoing',
                    'submitted_at' => now()->subMonths(4),
                ]
            );
            $seedRabAndLuaran($uSmh, 7500000, 10500000, 7000000, 5000000, 'Jurnal Abmas SINTA 3');

            $kSmh = PpmKontrak::updateOrCreate(
                ['id_usulan' => $uSmh->id],
                [
                    'nomor_sk' => '008/SK-PEMENANG/P3M-UHN/2026',
                    'nomor_kontrak' => '008/SPK-BIMA/P3M-UHN/2026',
                    'tanggal_sk' => now()->subMonths(4),
                    'tanggal_kontrak' => now()->subMonths(4),
                    'pagu_disetujui' => 30000000,
                    'dana_termin_1' => 21000000,
                    'dana_termin_2' => 9000000,
                    'nama_bank' => 'BRI',
                    'nomor_rekening' => '010101882100501',
                    'nama_pemilik_rekening' => $riska->name,
                    'file_buku_tabungan' => 'tabungan/uhn_rek_sample.pdf',
                    'rekening_verified_at' => now()->subMonths(3)->subDays(20),
                    'rekening_verified_by' => $keuanganUser?->id,
                    'signed_by_kepala' => true,
                    'signed_by_kepala_at' => now()->subMonths(3)->subDays(25),
                    'signed_by_pengusul' => true,
                    'signed_by_pengusul_at' => now()->subMonths(3)->subDays(23),
                    'status' => 'ongoing',
                    'verification_token' => hash('sha256', 'SPK-UHN-SMH01'),
                ]
            );

            PpmPencairanDana::updateOrCreate(
                ['id_kontrak' => $kSmh->id, 'termin' => 1],
                [
                    'persentase' => 70.00,
                    'jumlah_dana' => 21000000,
                    'nomor_referensi' => 'SP2D-UHN-2026-0098',
                    'tanggal_transfer' => now()->subMonths(3)->toDateString(),
                    'file_bukti_transfer' => 'bukti_transfer/uhn_termin1_sample.pdf',
                    'status_pencairan' => 'transferred',
                    'processed_by' => $keuanganUser?->id,
                    'processed_at' => now()->subMonths(3),
                    'catatan' => 'Pencairan Termin I 70% terlaksana.',
                ]
            );

            PpmMonevKemajuan::updateOrCreate(
                ['id_usulan' => $uSmh->id],
                [
                    'id_reviewer' => $revRatri?->id,
                    'file_laporan_kemajuan' => 'monev/uhn_lap_kemajuan_sample.pdf',
                    'file_sptb_70' => 'monev/uhn_sptb_70_sample.pdf',
                    'ringkasan_kemajuan' => 'Pelaksanaan pendampingan gizi kepada 40 ibu hamil berjalan sesuai jadwal.',
                    'persentase_kemajuan' => 85.00,
                    'skor_monev' => 88.50,
                    'catatan_evaluasi' => 'Dokumentasi mitra lengkap dan SPTB 70% tertib.',
                    'rekomendasi' => 'Lanjut',
                    'status' => 'evaluated',
                    'evaluated_at' => now()->subWeeks(3),
                ]
            );

            PpmSeminarHasil::updateOrCreate(
                ['id_usulan' => $uSmh->id],
                [
                    'jadwal_seminar' => now()->addDays(5)->setTime(10, 0),
                    'ruangan_or_link' => 'Ruang Seminar LPPM Gd. A Lt. 2 / Zoom Meeting',
                    'id_penguji_1' => $revEko->id,
                    'id_penguji_2' => $revArfan->id,
                    'status_seminar' => 'scheduled',
                ]
            );

            PpmLaporanAkhir::updateOrCreate(
                ['id_usulan' => $uSmh->id],
                [
                    'file_laporan_akhir' => 'laporan-akhir/uhn_lap_akhir_sample.pdf',
                    'file_sptb_100' => 'laporan-akhir/uhn_sptb_100_sample.pdf',
                    'ringkasan_hasil' => 'Program pendampingan selesai 100% dan menghasilkan 1 modul ber-HKI serta artikel terpublikasi.',
                    'is_approved_p3m' => true,
                    'approved_by_p3m_at' => now()->subDays(2),
                    'verification_token' => hash('sha256', 'LAP-UHN-SMH01'),
                ]
            );
        }

        // Project 4: COMPLETED (Termin I & II 100% Lunas, Semhas Graded, Laporan Akhir Approved!)
        if ($arif && $revRatri && $revEko) {
            $uCmp = PpmUsulan::updateOrCreate(
                ['kode_usulan' => 'USL-2026-PEN-CMP01'],
                [
                    'no_urut' => null,
                    'id_pengusul' => $arif->id,
                    'id_skema_bima' => $skemaTerapan->id,
                    'id_periode_hibah' => $periode->id,
                    'kategori_usulan' => 'penelitian',
                    'semester' => $semesterLabel,
                    'nama_ketua' => $arif->name,
                    'nidn_ketua' => $arif->nidn_nim,
                    'id_prodi' => $arif->id_prodi,
                    'nama_prodi' => $arif->prodi?->nama_prodi,
                    'judul_usulan' => 'Rancang Bangun Robot Autonomous Indoor Berbasis LiDAR untuk Distribusi Logistik Kampus',
                    'rumpun_ilmu_level_1' => 'Teknologi & Rekayasa',
                    'rumpun_ilmu_level_2' => 'Teknik Elektro & Industri',
                    'rumpun_ilmu_level_3' => 'Robotika & Otomasi',
                    'fokus_rirn' => 'Transportasi & Otomotif',
                    'target_tkt' => 7,
                    'ringkasan_substansi' => 'Prototipe robot navigasi SLAM 2D telah diuji di gedung akademik dengan akurasi penghindaran halangan 96%.',
                    'file_proposal_path' => 'proposals/uhn_pen_ongoing_01.pdf',
                    'template_status' => 'sesuai',
                    'similarity_score' => 9.1,
                    'similarity_status' => 'lolos',
                    'skor_reviewer_1' => 682.00,
                    'skor_reviewer_2' => 674.00,
                    'skor_akhir' => 678.00,
                    'total_honorarium' => 10000000,
                    'total_rab' => 45000000,
                    'status' => 'Completed',
                    'submitted_at' => now()->subMonths(6),
                ]
            );
            $seedRabAndLuaran($uCmp, 10000000, 19000000, 8000000, 8000000, 'Jurnal Internasional & Paten Sederhana');

            $kCmp = PpmKontrak::updateOrCreate(
                ['id_usulan' => $uCmp->id],
                [
                    'nomor_sk' => '003/SK-PEMENANG/P3M-UHN/2026',
                    'nomor_kontrak' => '003/SPK-BIMA/P3M-UHN/2026',
                    'tanggal_sk' => now()->subMonths(6),
                    'tanggal_kontrak' => now()->subMonths(6),
                    'pagu_disetujui' => 45000000,
                    'dana_termin_1' => 31500000,
                    'dana_termin_2' => 13500000,
                    'nama_bank' => 'Bank Mandiri',
                    'nomor_rekening' => '1390011294821',
                    'nama_pemilik_rekening' => $arif->name,
                    'file_buku_tabungan' => 'tabungan/uhn_rek_sample.pdf',
                    'rekening_verified_at' => now()->subMonths(5)->subDays(20),
                    'rekening_verified_by' => $keuanganUser?->id,
                    'signed_by_kepala' => true,
                    'signed_by_kepala_at' => now()->subMonths(5)->subDays(25),
                    'signed_by_pengusul' => true,
                    'signed_by_pengusul_at' => now()->subMonths(5)->subDays(24),
                    'status' => 'completed',
                    'verification_token' => hash('sha256', 'SPK-UHN-CMP01'),
                ]
            );

            PpmPencairanDana::updateOrCreate(
                ['id_kontrak' => $kCmp->id, 'termin' => 1],
                [
                    'persentase' => 70.00,
                    'jumlah_dana' => 31500000,
                    'nomor_referensi' => 'SP2D-UHN-2026-0021',
                    'tanggal_transfer' => now()->subMonths(5)->toDateString(),
                    'file_bukti_transfer' => 'bukti_transfer/uhn_termin1_sample.pdf',
                    'status_pencairan' => 'transferred',
                    'processed_by' => $keuanganUser?->id,
                    'processed_at' => now()->subMonths(5),
                    'catatan' => 'Termin I 70% cair.',
                ]
            );

            PpmPencairanDana::updateOrCreate(
                ['id_kontrak' => $kCmp->id, 'termin' => 2],
                [
                    'persentase' => 30.00,
                    'jumlah_dana' => 13500000,
                    'nomor_referensi' => 'SP2D-UHN-2026-0144',
                    'tanggal_transfer' => now()->subWeeks(1)->toDateString(),
                    'file_bukti_transfer' => 'bukti_transfer/uhn_termin2_sample.pdf',
                    'status_pencairan' => 'transferred',
                    'processed_by' => $keuanganUser?->id,
                    'processed_at' => now()->subWeeks(1),
                    'catatan' => 'Pelunasan Termin II 30% selesai (100% tersalurkan).',
                ]
            );

            PpmMonevKemajuan::updateOrCreate(
                ['id_usulan' => $uCmp->id],
                [
                    'id_reviewer' => $revRatri->id,
                    'file_laporan_kemajuan' => 'monev/uhn_lap_kemajuan_sample.pdf',
                    'file_sptb_70' => 'monev/uhn_sptb_70_sample.pdf',
                    'ringkasan_kemajuan' => 'Uji navigasi autonomous selesai dan dokumen HKI telah didaftarkan.',
                    'persentase_kemajuan' => 100.00,
                    'skor_monev' => 92.00,
                    'catatan_evaluasi' => 'Sangat memuaskan dan layak pelunasan.',
                    'rekomendasi' => 'Lanjut',
                    'status' => 'evaluated',
                    'evaluated_at' => now()->subMonths(1),
                ]
            );

            PpmSeminarHasil::updateOrCreate(
                ['id_usulan' => $uCmp->id],
                [
                    'jadwal_seminar' => now()->subWeeks(2)->setTime(13, 30),
                    'ruangan_or_link' => 'Aula Gedung C UHN',
                    'id_penguji_1' => $revEko->id,
                    'id_penguji_2' => $revRatri->id,
                    'skor_seminar' => 91.50,
                    'catatan_penguji' => 'Luaran prototipe tervalidasi serta artikel dan HKI terpenuhi.',
                    'status_seminar' => 'completed',
                ]
            );

            PpmLaporanAkhir::updateOrCreate(
                ['id_usulan' => $uCmp->id],
                [
                    'file_laporan_akhir' => 'laporan-akhir/uhn_lap_akhir_sample.pdf',
                    'file_sptb_100' => 'laporan-akhir/uhn_sptb_100_sample.pdf',
                    'ringkasan_hasil' => 'Seluruh target luaran wajib dan tambahan tercapai 100%.',
                    'is_approved_p3m' => true,
                    'approved_by_p3m_at' => now()->subWeeks(2),
                    'verification_token' => hash('sha256', 'LAP-UHN-CMP01'),
                ]
            );
        }

        // =========================================================================
        // 6. SENTRA HKI RESMI DOSEN UHN (TERVERIFIKASI & ANTREAN VERIFIKASI)
        // =========================================================================
        $divPubUser = $operators['Divisi Publikasi'] ?? $operators['Admin P3M'];
        $hkiItems = [
            [
                'user' => $sharfina,
                'jenis_hki' => 'Hak Cipta',
                'judul_hki' => 'Framework Arsitektur Keamanan Siber Zero Trust pada Infrastruktur Hybrid Cloud Kampus',
                'nomor_permohonan' => 'EC00202688001',
                'nomor_sertifikat' => '000512891',
                'tanggal_permohonan' => now()->subMonths(2)->toDateString(),
                'tanggal_terbit' => now()->subMonth()->toDateString(),
                'status_hki' => 'Terverifikasi HKI',
                'is_claimed_reward' => true,
                'catatan' => 'Diverifikasi resmi pada database DJKI Kemenkumham oleh Divisi Publikasi & Sentra HKI P3M UHN.',
            ],
            [
                'user' => $ginanjar,
                'jenis_hki' => 'Hak Cipta',
                'judul_hki' => 'Sistem Informasi Manajemen Presensi Berbasis Pengenalan Wajah dan Geofencing (SIMPRES UHN)',
                'nomor_permohonan' => 'EC00202619482',
                'nomor_sertifikat' => '000456123',
                'tanggal_permohonan' => now()->subWeeks(6)->toDateString(),
                'tanggal_terbit' => now()->subWeeks(3)->toDateString(),
                'status_hki' => 'Terverifikasi HKI',
                'is_claimed_reward' => false,
                'catatan' => 'Sertifikat pencatatan hak cipta program komputer DJKI valid.',
            ],
            [
                'user' => $slamet,
                'jenis_hki' => 'Paten Sederhana',
                'judul_hki' => 'Alat Telemetri Kualitas Air Tambak Udang Berdaya Ultra Rendah Berbasis LoRaWAN',
                'nomor_permohonan' => 'S00202600124',
                'nomor_sertifikat' => 'IDS000009871',
                'tanggal_permohonan' => now()->subMonths(5)->toDateString(),
                'tanggal_terbit' => now()->subWeeks(4)->toDateString(),
                'status_hki' => 'Terverifikasi HKI',
                'is_claimed_reward' => false,
                'catatan' => 'Sertifikat paten sederhana terdaftar dan aktif.',
            ],
            [
                'user' => $revHeru,
                'jenis_hki' => 'Paten',
                'judul_hki' => 'Formulasi Nanopartikel Ekstrak Daun Kelor Terstandar sebagai Agen Antioksidan Alami',
                'nomor_permohonan' => 'P00202500892',
                'nomor_sertifikat' => 'IDP000088192',
                'tanggal_permohonan' => now()->subMonths(7)->toDateString(),
                'tanggal_terbit' => now()->subWeeks(5)->toDateString(),
                'status_hki' => 'Terverifikasi HKI',
                'is_claimed_reward' => false,
                'catatan' => 'Sertifikat paten granted DJKI valid.',
            ],
            [
                'user' => $dairoh,
                'jenis_hki' => 'Hak Cipta',
                'judul_hki' => 'Aplikasi Mobile Pemantauan Salinitas Air Tambak Pantura (AQUASENSE UHN)',
                'nomor_permohonan' => 'EC00202690411',
                'nomor_sertifikat' => null,
                'tanggal_permohonan' => now()->subDays(5)->toDateString(),
                'tanggal_terbit' => null,
                'status_hki' => 'Pending_verification',
                'is_claimed_reward' => false,
                'catatan' => 'Menunggu pemeriksaan kesesuaian template dokumen dan verifikasi DJKI oleh Divisi Publikasi.',
            ],
            [
                'user' => $lukman,
                'jenis_hki' => 'Desain Industri',
                'judul_hki' => 'Desain Casing Cerdas Sensor Lingkungan Tahan Air Berbasis Energi Surya',
                'nomor_permohonan' => 'A00202601105',
                'nomor_sertifikat' => null,
                'tanggal_permohonan' => now()->subDays(3)->toDateString(),
                'tanggal_terbit' => null,
                'status_hki' => 'Pending_verification',
                'is_claimed_reward' => false,
                'catatan' => 'Menunggu verifikasi berkas pengalihan hak oleh Sentra HKI.',
            ],
        ];

        $hkiClaimTarget = null;
        foreach ($hkiItems as $item) {
            if (!$item['user']) {
                continue;
            }
            $isVerified = $item['status_hki'] === 'Terverifikasi HKI';
            $hki = PpmHki::updateOrCreate(
                ['nomor_permohonan' => $item['nomor_permohonan']],
                [
                    'user_id' => $item['user']->id,
                    'jenis_hki' => $item['jenis_hki'],
                    'judul_hki' => $item['judul_hki'],
                    'nomor_sertifikat' => $item['nomor_sertifikat'],
                    'tanggal_permohonan' => $item['tanggal_permohonan'],
                    'tanggal_terbit' => $item['tanggal_terbit'],
                    'pemegang_hak' => 'Universitas Harkat Negeri',
                    'file_sertifikat' => $isVerified ? 'hki_sertifikat/uhn_hki_sertifikat_sample.pdf' : null,
                    'file_manual_book' => 'hki_dokumen/uhn_manual_book_sample.pdf',
                    'file_surat_pernyataan' => 'hki_dokumen/uhn_pernyataan_sample.pdf',
                    'file_surat_pengalihan' => 'hki_dokumen/uhn_pengalihan_sample.pdf',
                    'status_hki' => $item['status_hki'],
                    'template_status' => $isVerified ? 'sesuai' : null,
                    'similarity_score' => $isVerified ? 5.2 : null,
                    'verified_by_user_id' => $isVerified ? $divPubUser?->id : null,
                    'verified_at' => $isVerified ? now()->subWeeks(2) : null,
                    'catatan_verifikasi' => $item['catatan'],
                    'is_claimed_reward' => $item['is_claimed_reward'],
                ]
            );
            if ($item['nomor_permohonan'] === 'EC00202688001') {
                $hkiClaimTarget = $hki;
            }
        }

        // =========================================================================
        // 7. KLAIM REWARD INSENTIF & DISTRIBUSI MULTI-PENULIS (DARI PUBLIKASI CSV REAL & HKI REAL)
        // =========================================================================
        // Pastikan tarif dasar lengkap untuk artikel CSV Internasional & Nasional
        RefTarifRewardSk::updateOrCreate(
            ['kategori' => 'Publikasi', 'sub_kategori' => 'Internasional'],
            ['nominal_insentif' => 7500000, 'nomor_sk_rektor' => 'SK-REKTOR/UHN/2026/015', 'keterangan' => 'Jurnal Internasional Terindeks (CSV Resmi)', 'is_active' => true]
        );
        RefTarifRewardSk::updateOrCreate(
            ['kategori' => 'Publikasi', 'sub_kategori' => 'Nasional Tidak Terakreditasi'],
            ['nominal_insentif' => 1500000, 'nomor_sk_rektor' => 'SK-REKTOR/UHN/2026/015', 'keterangan' => 'Jurnal Nasional ISSN', 'is_active' => true]
        );

        // Klaim 1 (Submitted -> antrean verifikasi Divisi Publikasi), terkait publikasi CSV nyata!
        $pubRealSubmitted = PpmPublikasiJurnal::whereNotNull('user_id')
            ->where('kategori_peringkat', 'Nasional Terakreditasi')
            ->orderBy('no_urut')
            ->first();
        if ($pubRealSubmitted && $pubRealSubmitted->user) {
            $pubRealSubmitted->update([
                'is_claimed_reward' => true,
                'template_status' => 'sesuai',
                'similarity_score' => 9.5,
                'status_verifikasi' => 'Verified',
                'verified_by' => $divPubUser?->id,
                'verified_at' => now()->subDays(5),
            ]);
            $k1 = PpmKlaimReward::updateOrCreate(
                ['nomor_klaim' => 'RWD-2026-UHN-001'],
                [
                    'user_id' => $pubRealSubmitted->user_id,
                    'jenis_klaim' => 'Publikasi',
                    'id_publikasi' => $pubRealSubmitted->id,
                    'kategori_insentif' => 'Nasional Terakreditasi',
                    'tarif_dasar_sk' => 2500000,
                    'total_reward' => 2500000,
                    'file_surat_pernyataan' => 'surat_pernyataan_reward/uhn_reward_kesepakatan.pdf',
                    'status_klaim' => 'Submitted',
                ]
            );
            PpmRewardDistribusi::updateOrCreate(
                ['id_klaim_reward' => $k1->id, 'nama_penulis' => $pubRealSubmitted->user->name],
                [
                    'user_id' => $pubRealSubmitted->user->id,
                    'nidn_nim' => $pubRealSubmitted->user->nidn_nim,
                    'email' => $pubRealSubmitted->user->email,
                    'peran_penulis' => 'Penulis Pertama & Korespondensi',
                    'persentase' => 100.00,
                    'nominal_bagian' => 2500000,
                    'nama_bank' => 'BNI',
                    'nomor_rekening' => '0771829401',
                    'nama_pemilik_rekening' => $pubRealSubmitted->user->name,
                    'status_transfer' => 'Pending',
                ]
            );
        }

        // Klaim 2 (Approved_P3M -> siap dicairkan Divisi Keuangan ke 2 penulis multi-author!)
        if ($hkiClaimTarget && $sharfina && $ginanjar) {
            $k2 = PpmKlaimReward::updateOrCreate(
                ['nomor_klaim' => 'RWD-2026-UHN-002'],
                [
                    'user_id' => $sharfina->id,
                    'jenis_klaim' => 'HKI',
                    'id_hki' => $hkiClaimTarget->id,
                    'kategori_insentif' => 'Hak Cipta',
                    'tarif_dasar_sk' => 5000000,
                    'total_reward' => 5000000,
                    'file_surat_pernyataan' => 'surat_pernyataan_reward/uhn_reward_kesepakatan.pdf',
                    'status_klaim' => 'Approved_P3M',
                    'approved_by_p3m' => $divPubUser?->id,
                    'approved_at' => now()->subDays(2),
                    'catatan_p3m' => 'Berkas sertifikat DJKI & surat pembagian proporsi 60:40 telah divalidasi Divisi Publikasi.',
                ]
            );
            PpmRewardDistribusi::updateOrCreate(
                ['id_klaim_reward' => $k2->id, 'nama_penulis' => $sharfina->name],
                [
                    'user_id' => $sharfina->id,
                    'nidn_nim' => $sharfina->nidn_nim,
                    'email' => $sharfina->email,
                    'peran_penulis' => 'Inventor Utama',
                    'persentase' => 60.00,
                    'nominal_bagian' => 3000000,
                    'nama_bank' => 'Bank Mandiri',
                    'nomor_rekening' => '1390098871234',
                    'nama_pemilik_rekening' => $sharfina->name,
                    'status_transfer' => 'Pending',
                ]
            );
            PpmRewardDistribusi::updateOrCreate(
                ['id_klaim_reward' => $k2->id, 'nama_penulis' => $ginanjar->name],
                [
                    'user_id' => $ginanjar->id,
                    'nidn_nim' => $ginanjar->nidn_nim,
                    'email' => $ginanjar->email,
                    'peran_penulis' => 'Inventor Anggota',
                    'persentase' => 40.00,
                    'nominal_bagian' => 2000000,
                    'nama_bank' => 'BNI',
                    'nomor_rekening' => '0552198341',
                    'nama_pemilik_rekening' => $ginanjar->name,
                    'status_transfer' => 'Pending',
                ]
            );
        }

        // Klaim 3 (Disbursed -> sudah selesai ditransfer Divisi Keuangan untuk publikasi Internasional CSV real!)
        $pubRealDisbursed = PpmPublikasiJurnal::whereNotNull('user_id')
            ->where('kategori_peringkat', 'Internasional')
            ->orderBy('no_urut')
            ->first();
        if ($pubRealDisbursed && $pubRealDisbursed->user) {
            $pubRealDisbursed->update([
                'is_claimed_reward' => true,
                'template_status' => 'sesuai',
                'similarity_score' => 7.2,
                'status_verifikasi' => 'Verified',
                'verified_by' => $divPubUser?->id,
                'verified_at' => now()->subWeeks(2),
            ]);
            $k3 = PpmKlaimReward::updateOrCreate(
                ['nomor_klaim' => 'RWD-2026-UHN-003'],
                [
                    'user_id' => $pubRealDisbursed->user_id,
                    'jenis_klaim' => 'Publikasi',
                    'id_publikasi' => $pubRealDisbursed->id,
                    'kategori_insentif' => 'Internasional',
                    'tarif_dasar_sk' => 7500000,
                    'total_reward' => 7500000,
                    'file_surat_pernyataan' => 'surat_pernyataan_reward/uhn_reward_kesepakatan.pdf',
                    'status_klaim' => 'Disbursed',
                    'approved_by_p3m' => $divPubUser?->id,
                    'approved_at' => now()->subWeeks(2),
                    'catatan_p3m' => 'Insentif publikasi internasional disetujui dan disalurkan.',
                ]
            );
            PpmRewardDistribusi::updateOrCreate(
                ['id_klaim_reward' => $k3->id, 'nama_penulis' => $pubRealDisbursed->user->name],
                [
                    'user_id' => $pubRealDisbursed->user->id,
                    'nidn_nim' => $pubRealDisbursed->user->nidn_nim,
                    'email' => $pubRealDisbursed->user->email,
                    'peran_penulis' => 'Penulis Utama',
                    'persentase' => 100.00,
                    'nominal_bagian' => 7500000,
                    'nama_bank' => 'BRI',
                    'nomor_rekening' => '010101552341502',
                    'nama_pemilik_rekening' => $pubRealDisbursed->user->name,
                    'status_transfer' => 'Disbursed',
                    'tanggal_transfer' => now()->subWeek()->toDateString(),
                    'nomor_referensi' => 'TRF-RWD-UHN-2026-003',
                    'file_bukti_transfer' => 'bukti_transfer_reward/uhn_reward_transfer.pdf',
                ]
            );
        }

        // Bersihkan cache analitik agar angka Eksekutif & Fakultas langsung terbarui
        Cache::forget('executive_analytics_summary_all');
        if ($periode) {
            Cache::forget('executive_analytics_summary_' . $periode->id);
        }
    }
}

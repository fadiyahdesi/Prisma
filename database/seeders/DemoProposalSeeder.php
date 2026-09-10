<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\PpmSkemaBima;
use App\Models\PpmPeriodeHibah;
use App\Models\PpmUsulan;
use App\Models\PpmUsulanAnggota;
use App\Models\PpmUsulanRab;
use App\Models\PpmUsulanLuaran;
use Illuminate\Support\Facades\Storage;

class DemoProposalSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure storage directory exists and sample dummy files are created
        Storage::disk('public')->makeDirectory('proposals');
        Storage::disk('public')->makeDirectory('mitra');

        $samplePdfContent = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj 2 0 obj<</Type/Pages/Count 1/Kids[3 0 R]>>endobj 3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Parent 2 0 R/Resources<<>>>>endobj\nxref\n0 4\n0000000000 65535 f\n0000000009 00000 n\n0000000052 00000 n\n0000000101 00000 n\ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n178\n%%EOF";
        
        Storage::disk('public')->put('proposals/sample_proposal_fnd.pdf', $samplePdfContent);
        Storage::disk('public')->put('proposals/sample_proposal_pdp.pdf', $samplePdfContent);
        Storage::disk('public')->put('mitra/sample_surat_kesediaan.pdf', $samplePdfContent);

        // Fetch Periode & Skema
        $periode = PpmPeriodeHibah::where('is_active', true)->first() ?? PpmPeriodeHibah::first();
        $skemaFnd = PpmSkemaBima::where('kode_skema', 'Fundamental')->first();
        $skemaPdp = PpmSkemaBima::where('kode_skema', 'PDP')->first();
        $skemaPkm = PpmSkemaBima::where('kode_skema', 'PKM')->first();

        // Fetch Users
        $dosen1 = User::where('email', 'dosen@harkatnegeri.ac.id')->first();
        $dosen2 = User::where('email', 'dosen2@harkatnegeri.ac.id')->first();
        $dosen3 = User::where('email', 'dosen3@harkatnegeri.ac.id')->first();
        $mhs1   = User::where('email', 'mahasiswa1@harkatnegeri.ac.id')->first();
        $mhs2   = User::where('email', 'mahasiswa2@harkatnegeri.ac.id')->first();

        if (!$periode || !$dosen1) {
            return;
        }

        // ==========================================
        // PROPOSAL 1: Fundamental (Status: Draft, Pending Consent from Dosen 2)
        // ==========================================
        if ($skemaFnd) {
            $usulan1 = PpmUsulan::updateOrCreate(
                ['kode_usulan' => 'USL-2026-FND-001'],
                [
                    'id_pengusul' => $dosen1->id,
                    'id_skema_bima' => $skemaFnd->id,
                    'id_periode_hibah' => $periode->id,
                    'judul_usulan' => 'Pengembangan Edge-AI dan Smart Sensor Node Berbasis LoRaWAN untuk Sistem Presisi Pertanian Berkelanjutan',
                    'rumpun_ilmu_level_1' => 'Teknik & Rekayasa',
                    'rumpun_ilmu_level_2' => 'Teknik Elektro & Informatika',
                    'rumpun_ilmu_level_3' => 'Artificial Intelligence & IoT',
                    'fokus_rirn' => 'Pangan & Pertanian',
                    'target_tkt' => 4,
                    'jawaban_instrumen_tkt' => ['q1' => 'ya', 'q2' => 'ya', 'q3' => 'ya'],
                    'nama_mitra' => 'Kelompok Tani Makmur Sejahtera',
                    'mitra_lat' => -7.250445,
                    'mitra_long' => 112.768845,
                    'mitra_jarak_km' => 15.5,
                    'mitra_surat_kesediaan_path' => 'mitra/sample_surat_kesediaan.pdf',
                    'ringkasan_substansi' => 'Penelitian ini bertujuan mengembangkan arsitektur Edge-AI hemat daya untuk analisis kondisi hara tanah dan mikroiklim secara real-time pada lahan pertanian. Sistem memanfaatkan protokol komunikasi wireless LoRaWAN yang efisien daya untuk menjangkau area pertanian terpencil tanpa ketergantungan koneksi seluler konvensional.',
                    'file_proposal_path' => 'proposals/sample_proposal_fnd.pdf',
                    'total_rab' => 45000000.00,
                    'total_honorarium' => 12000000.00,
                    'status' => 'Draft',
                ]
            );

            // Anggota Usulan 1
            PpmUsulanAnggota::updateOrCreate(
                ['id_usulan' => $usulan1->id, 'identifier' => $dosen2->nidn_nim ?? '0618048202'],
                [
                    'user_id' => $dosen2->id,
                    'jenis_anggota' => 'dosen',
                    'nama' => $dosen2->name,
                    'peran_anggota' => 'Anggota Peneliti (Pakar Embedded System)',
                    'status_persetujuan' => 'pending', // Pending consent for testing EPIC 06!
                    'approved_at' => null,
                ]
            );

            PpmUsulanAnggota::updateOrCreate(
                ['id_usulan' => $usulan1->id, 'identifier' => $mhs1->nidn_nim ?? '220101001'],
                [
                    'user_id' => $mhs1 ? $mhs1->id : null,
                    'jenis_anggota' => 'mahasiswa',
                    'nama' => $mhs1 ? $mhs1->name : 'Rizky Pratama',
                    'peran_anggota' => 'Anggota Mahasiswa (IKU-2)',
                    'status_persetujuan' => 'approved',
                    'approved_at' => now(),
                ]
            );

            // RAB Usulan 1
            $rabItems1 = [
                ['pos_belanja' => 'Honorarium', 'item_keterangan' => 'Honorarium Pembantu Lapangan & Surveyor (2 Orang x 4 Bulan)', 'volume' => 8, 'satuan' => 'OJ', 'harga_satuan' => 1500000, 'total_harga' => 12000000],
                ['pos_belanja' => 'Bahan / Alat Habis Pakai', 'item_keterangan' => 'Modul LoRaWAN SX1276, Transduser NPK Tanah & Microcontroller ESP32', 'volume' => 10, 'satuan' => 'Paket', 'harga_satuan' => 1800000, 'total_harga' => 18000000],
                ['pos_belanja' => 'Pengumpulan Data / Lapangan', 'item_keterangan' => 'Transportasi & Operasional Survei Lapangan Pertanian', 'volume' => 4, 'satuan' => 'Kunjungan', 'harga_satuan' => 2000000, 'total_harga' => 8000000],
                ['pos_belanja' => 'Sewa Peralatan / Laboratorium', 'item_keterangan' => 'Sewa Spectrum Analyzer & Testing Chamber RF', 'volume' => 1, 'satuan' => 'Paket', 'harga_satuan' => 4000000, 'total_harga' => 4000000],
                ['pos_belanja' => 'Pelaporan & Publikasi', 'item_keterangan' => 'Biaya Publikasi Jurnal Scopus & Pengurusan Hak Cipta', 'volume' => 1, 'satuan' => 'Paket', 'harga_satuan' => 3000000, 'total_harga' => 3000000],
            ];

            PpmUsulanRab::where('id_usulan', $usulan1->id)->delete();
            foreach ($rabItems1 as $r) {
                $r['id_usulan'] = $usulan1->id;
                PpmUsulanRab::create($r);
            }

            // Luaran Usulan 1
            PpmUsulanLuaran::where('id_usulan', $usulan1->id)->delete();
            PpmUsulanLuaran::create([
                'id_usulan' => $usulan1->id,
                'jenis_luaran' => 'wajib',
                'kategori_luaran' => 'Jurnal Internasional Bereputasi Scopus',
                'target_status' => 'Accepted / Published',
                'keterangan' => 'Target publikasi pada IEEE Internet of Things Journal atau Journal of Agricultural Engineering (Q2)',
            ]);
            PpmUsulanLuaran::create([
                'id_usulan' => $usulan1->id,
                'jenis_luaran' => 'tambahan',
                'kategori_luaran' => 'Hak Cipta / HKI',
                'target_status' => 'Granted / Registered',
                'keterangan' => 'Hak Cipta Firmware Edge-AI LoRaWAN',
            ]);
        }

        // ==========================================
        // PROPOSAL 2: PDP (Status: Submitted)
        // ==========================================
        if ($skemaPdp && $dosen3) {
            $usulan2 = PpmUsulan::updateOrCreate(
                ['kode_usulan' => 'USL-2026-PDP-002'],
                [
                    'id_pengusul' => $dosen3->id,
                    'id_skema_bima' => $skemaPdp->id,
                    'id_periode_hibah' => $periode->id,
                    'judul_usulan' => 'Penerapan Model Convolutional Neural Network (CNN) untuk Deteksi Dini Penyakit Daun Padi Berbasis Mobile App',
                    'rumpun_ilmu_level_1' => 'Sains & Matematika',
                    'rumpun_ilmu_level_2' => 'Ilmu Komputer',
                    'rumpun_ilmu_level_3' => 'Computer Vision & Deep Learning',
                    'fokus_rirn' => 'Pangan & Pertanian',
                    'target_tkt' => 3,
                    'jawaban_instrumen_tkt' => ['q1' => 'ya', 'q2' => 'ya'],
                    'ringkasan_substansi' => 'Riset ini merancang arsitektur model MobileNetV3 yang dikompresi menggunakan kuantisasi 8-bit untuk dijalankan secara offline di smartphone petani guna mendeteksi penyakit hawar daun bakteri dan blast padi secara presisi.',
                    'file_proposal_path' => 'proposals/sample_proposal_pdp.pdf',
                    'total_rab' => 22500000.00,
                    'total_honorarium' => 6000000.00,
                    'status' => 'Submitted',
                    'submitted_at' => now()->subDays(1),
                ]
            );

            PpmUsulanAnggota::updateOrCreate(
                ['id_usulan' => $usulan2->id, 'identifier' => $dosen1->nidn_nim],
                [
                    'user_id' => $dosen1->id,
                    'jenis_anggota' => 'dosen',
                    'nama' => $dosen1->name,
                    'peran_anggota' => 'Anggota Peneliti (Pakar Machine Learning)',
                    'status_persetujuan' => 'approved',
                    'approved_at' => now()->subDays(2),
                ]
            );

            PpmUsulanAnggota::updateOrCreate(
                ['id_usulan' => $usulan2->id, 'identifier' => $mhs2->nidn_nim ?? '220101002'],
                [
                    'user_id' => $mhs2 ? $mhs2->id : null,
                    'jenis_anggota' => 'mahasiswa',
                    'nama' => $mhs2 ? $mhs2->name : 'Siti Rahmawati',
                    'peran_anggota' => 'Anggota Mahasiswa (IKU-2)',
                    'status_persetujuan' => 'approved',
                    'approved_at' => now()->subDays(2),
                ]
            );

            // RAB Usulan 2
            $rabItems2 = [
                ['pos_belanja' => 'Honorarium', 'item_keterangan' => 'Honorarium Pengumpul Data Dataset Citra Daun (2 orang)', 'volume' => 4, 'satuan' => 'OJ', 'harga_satuan' => 1500000, 'total_harga' => 6000000],
                ['pos_belanja' => 'Bahan / Alat Habis Pakai', 'item_keterangan' => 'Sewa Cloud GPU RunPod / Colab Pro & Storage Dataset', 'volume' => 1, 'satuan' => 'Paket', 'harga_satuan' => 8500000, 'total_harga' => 8500000],
                ['pos_belanja' => 'Pengumpulan Data / Lapangan', 'item_keterangan' => 'Pengambilan Sampel Foto Daun di Sawah Subang & Karawang', 'volume' => 2, 'satuan' => 'Kunjungan', 'harga_satuan' => 2000000, 'total_harga' => 4000000],
                ['pos_belanja' => 'Pelaporan & Publikasi', 'item_keterangan' => 'Publikasi Jurnal Nasional Terakreditasi SINTA 2', 'volume' => 1, 'satuan' => 'Paket', 'harga_satuan' => 4000000, 'total_harga' => 4000000],
            ];

            PpmUsulanRab::where('id_usulan', $usulan2->id)->delete();
            foreach ($rabItems2 as $r) {
                $r['id_usulan'] = $usulan2->id;
                PpmUsulanRab::create($r);
            }

            PpmUsulanLuaran::where('id_usulan', $usulan2->id)->delete();
            PpmUsulanLuaran::create([
                'id_usulan' => $usulan2->id,
                'jenis_luaran' => 'wajib',
                'kategori_luaran' => 'Jurnal Nasional Terakreditasi SINTA 2',
                'target_status' => 'Submitted / Under Review',
                'keterangan' => 'Jurnal Telematika / Jurnal Ilmiah Ketenagalistrikan & Informatika',
            ]);
        }

        // ==========================================
        // PROPOSAL 3: PKM (Status: Draft)
        // ==========================================
        if ($skemaPkm && $dosen2) {
            $usulan3 = PpmUsulan::updateOrCreate(
                ['kode_usulan' => 'USL-2026-PKM-003'],
                [
                    'id_pengusul' => $dosen2->id,
                    'id_skema_bima' => $skemaPkm->id,
                    'id_periode_hibah' => $periode->id,
                    'judul_usulan' => 'Pemberdayaan UMKM Kopi Lokal Melalui Digitalisasi Pemasaran E-Commerce dan Pengolahan Limbah Kulit Kopi menjadi Bio-Pelet',
                    'rumpun_ilmu_level_1' => 'Sosial & Humaniora',
                    'rumpun_ilmu_level_2' => 'Manajemen & Kewirausahaan',
                    'rumpun_ilmu_level_3' => 'Pemberdayaan Masyarakat & Ekonomi Kreatif',
                    'fokus_rirn' => 'Ekonomi & Industri Kreatif',
                    'target_tkt' => 5,
                    'jawaban_instrumen_tkt' => ['q1' => 'ya', 'q2' => 'ya'],
                    'nama_mitra' => 'Koperasi Tani Kopi Java Preanger',
                    'mitra_lat' => -6.914744,
                    'mitra_long' => 107.609810,
                    'mitra_jarak_km' => 28.0,
                    'ringkasan_substansi' => 'Program Pengabdian kepada Masyarakat ini menargetkan penguatan kapasitas pemasaran digital UMKM Kopi lokal melalui integrasi marketplace dan diversifikasi produk limbah kulit kopi menjadi energi terbarukan bio-pelet.',
                    'total_rab' => 30000000.00,
                    'total_honorarium' => 8000000.00,
                    'status' => 'Draft',
                ]
            );

            PpmUsulanAnggota::updateOrCreate(
                ['id_usulan' => $usulan3->id, 'identifier' => $dosen3->nidn_nim],
                [
                    'user_id' => $dosen3->id,
                    'jenis_anggota' => 'dosen',
                    'nama' => $dosen3->name,
                    'peran_anggota' => 'Anggota Pelaksana Field Specialist',
                    'status_persetujuan' => 'approved',
                    'approved_at' => now()->subHours(5),
                ]
            );
        }
    }
}

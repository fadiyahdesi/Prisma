<?php

namespace App\Services;

use App\Models\PpmHki;
use App\Models\PpmKlaimReward;
use App\Models\PpmKontrak;
use App\Models\PpmLaporanAkhir;
use App\Models\PpmMonevKemajuan;
use App\Models\PpmPencairanDana;
use App\Models\PpmPublikasiJurnal;
use App\Models\PpmRewardDistribusi;
use App\Models\PpmUsulan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DemoPdfGeneratorService
{
    /**
     * Helper to render Blade view to PDF string and save to public disk.
     */
    public function renderAndSave(string $view, array $data, string $path, string $paper = 'a4', string $orientation = 'portrait'): string
    {
        $pdf = Pdf::loadView($view, $data)->setPaper($paper, $orientation);
        $content = $pdf->output();

        // Ensure directory exists
        $dir = dirname($path);
        if ($dir && !Storage::disk('public')->exists($dir)) {
            Storage::disk('public')->makeDirectory($dir);
        }

        Storage::disk('public')->put($path, $content);

        return $path;
    }

    public function generatePaper(array $data = [], string $path = 'publikasi_naskah/sample_paper.pdf'): string
    {
        return $this->renderAndSave('pdf.templates.paper', $data, $path, 'a4', 'portrait');
    }

    public function generateHkiCertificate(array $data = [], string $path = 'hki_sertifikat/sample_sertifikat.pdf'): string
    {
        return $this->renderAndSave('pdf.templates.hki_sertifikat', $data, $path, 'a4', 'portrait');
    }

    public function generateHkiManualBook(array $data = [], string $path = 'hki_dokumen/sample_manual_book.pdf'): string
    {
        return $this->renderAndSave('pdf.templates.hki_manual_book', $data, $path, 'a4', 'portrait');
    }

    public function generateHkiPernyataan(array $data = [], string $path = 'hki_dokumen/sample_surat_pernyataan.pdf'): string
    {
        return $this->renderAndSave('pdf.templates.hki_surat_pernyataan', $data, $path, 'a4', 'portrait');
    }

    public function generateHkiPengalihan(array $data = [], string $path = 'hki_dokumen/sample_surat_pengalihan.pdf'): string
    {
        return $this->renderAndSave('pdf.templates.hki_surat_pengalihan', $data, $path, 'a4', 'portrait');
    }

    public function generateProposal(array $data = [], string $path = 'proposals/sample_proposal_pdp.pdf'): string
    {
        return $this->renderAndSave('pdf.templates.proposal', $data, $path, 'a4', 'portrait');
    }

    public function generateMitraSurat(array $data = [], string $path = 'mitra/sample_surat_kesediaan.pdf'): string
    {
        return $this->renderAndSave('pdf.templates.mitra_surat', $data, $path, 'a4', 'portrait');
    }

    public function generateLaporanKemajuan(array $data = [], string $path = 'monev/demo/laporan_kemajuan_sample.pdf'): string
    {
        return $this->renderAndSave('pdf.templates.laporan_kemajuan', $data, $path, 'a4', 'portrait');
    }

    public function generateLaporanAkhir(array $data = [], string $path = 'laporan-akhir/demo/laporan_akhir_sample.pdf'): string
    {
        return $this->renderAndSave('pdf.templates.laporan_akhir', $data, $path, 'a4', 'portrait');
    }

    public function generateSptb(array $data = [], string $path = 'monev/demo/sptb_70_sample.pdf'): string
    {
        return $this->renderAndSave('pdf.templates.sptb', $data, $path, 'a4', 'portrait');
    }

    public function generateBukuTabungan(array $data = [], string $path = 'tabungan/sample.pdf'): string
    {
        return $this->renderAndSave('pdf.templates.buku_tabungan', $data, $path, 'a4', 'portrait');
    }

    public function generateSpkKontrak(array $data = [], string $path = 'kontrak/sample_spk.pdf'): string
    {
        return $this->renderAndSave('pdf.templates.spk_kontrak', $data, $path, 'a4', 'portrait');
    }

    public function generateBuktiTransfer(array $data = [], string $path = 'bukti_transfer/sample_transfer.pdf'): string
    {
        return $this->renderAndSave('pdf.templates.bukti_transfer', $data, $path, 'a4', 'portrait');
    }

    public function generateRewardKesepakatan(array $data = [], string $path = 'surat_pernyataan_reward/sample_kesepakatan.pdf'): string
    {
        return $this->renderAndSave('pdf.templates.reward_kesepakatan', $data, $path, 'a4', 'portrait');
    }

    /**
     * Generate all standard sample files and normalize database records.
     */
    public function generateAllDemoPdfs(bool $force = true, ?\Closure $logger = null): array
    {
        $log = $logger ?: function (string $msg) {};
        $stats = [
            'standard_files' => 0,
            'publications' => 0,
            'hki' => 0,
            'proposals' => 0,
            'monev' => 0,
            'contracts' => 0,
            'rewards' => 0,
        ];

        // 1. GENERATE BASE DEMO / SAMPLE FILES
        $log('Menghasilkan berkas contoh standar institusi UHN...');

        // Standard Publication Paper
        $this->generatePaper([
            'title' => 'Autonomous Edge-AI Orchestration for Decentralized IoT Networks',
            'journal_name' => 'Jurnal Rekayasa & Teknologi Informasi UHN',
            'volume' => '12',
            'issue' => '2',
            'year' => '2026',
            'doi' => '10.24912/jrti.v12i2.2026.892',
            'authors' => 'Lintang Patria, Budi Wicaksono, Siti Rahmawati',
            'affiliation' => 'Fakultas Sains dan Teknologi, Universitas Harkat Negeri, Indonesia',
            'email' => 'lintang@uhn.ac.id',
        ], 'publikasi_naskah/sample_paper.pdf');
        $stats['standard_files']++;

        // Standard HKI Certificate
        $this->generateHkiCertificate([
            'judul_hki' => 'Sistem Informasi Manajemen Presensi Berbasis Pengenalan Wajah dan Geofencing (SIMPRES UHN)',
            'jenis_ciptaan' => 'Program Komputer',
            'pencipta' => 'Lintang Patria, S.Kom., M.Cs., Dr. Budi Wicaksono, M.Kom.',
            'pemegang_hak' => 'UNIVERSITAS HARKAT NEGERI',
            'nomor_sertifikat' => '000582914',
            'nomor_permohonan' => 'EC00202618902',
            'tanggal_permohonan' => '15 Januari 2026',
            'tanggal_terbit' => '28 Februari 2026',
        ], 'hki_sertifikat/sample_sertifikat.pdf');
        $stats['standard_files']++;

        // Supporting HKI documents
        $this->generateHkiManualBook([], 'hki_dokumen/sample_manual_book.pdf');
        $this->generateHkiPernyataan([], 'hki_dokumen/sample_surat_pernyataan.pdf');
        $this->generateHkiPengalihan([], 'hki_dokumen/sample_surat_pengalihan.pdf');
        $stats['standard_files'] += 3;

        // Proposals
        $this->generateProposal([
            'judul_usulan' => 'Penerapan Model Convolutional Neural Network (CNN) untuk Deteksi Dini Penyakit Daun Padi Berbasis Mobile App',
            'skema' => 'Penelitian Dosen Pemula (PDP)',
            'ketua_nama' => 'Lintang Patria, S.Kom., M.Cs.',
            'ketua_nidn' => '0412088901',
            'total_rab' => 25000000,
        ], 'proposals/sample_proposal_pdp.pdf');

        $this->generateProposal([
            'judul_usulan' => 'Usulan Proposal Penelitian Terapan Unggulan UHN',
            'skema' => 'Penelitian Terapan Unggulan (PTU)',
            'ketua_nama' => 'Dr. Budi Wicaksono, M.Kom.',
            'ketua_nidn' => '0418048201',
            'total_rab' => 50000000,
        ], 'proposals/sample_proposal_fnd.pdf');
        $stats['standard_files'] += 2;

        // Mitra
        $this->generateMitraSurat([], 'mitra/sample_surat_kesediaan.pdf');
        $stats['standard_files']++;

        // Monev & Laporan Akhir
        $this->generateLaporanKemajuan([], 'monev/demo/laporan_kemajuan_sample.pdf');
        $this->generateSptb(['persen' => 70, 'nomor_sptb' => 'SPTB-70/LPPM-UHN/2026', 'nominal_termin' => 17500000], 'monev/demo/sptb_70_sample.pdf');
        $this->generateLaporanAkhir([], 'laporan-akhir/demo/laporan_akhir_sample.pdf');
        $this->generateSptb(['persen' => 100, 'nomor_sptb' => 'SPTB-100/LPPM-UHN/2026', 'nominal_termin' => 7500000], 'laporan-akhir/demo/sptb_100_sample.pdf');
        $stats['standard_files'] += 4;

        // Keuangan & Kontrak
        $this->generateBukuTabungan([], 'tabungan/sample.pdf');
        $this->generateBukuTabungan([], 'tabungan/sample_tabungan.pdf');
        $this->generateSpkKontrak([], 'kontrak/sample_spk.pdf');
        $this->generateBuktiTransfer([], 'bukti_transfer/sample_transfer.pdf');
        $this->generateBuktiTransfer([], 'pencairan/demo/bukti_transfer_sample.pdf');
        $stats['standard_files'] += 5;

        // Reward Insentif
        $this->generateRewardKesepakatan([], 'surat_pernyataan_reward/sample_kesepakatan.pdf');
        $this->generateBuktiTransfer([
            'nominal' => 5000000,
            'terbilang' => 'Lima Juta Rupiah',
            'keterangan' => 'Pembayaran Insentif Reward Publikasi Ilmiah SINTA 2 LPPM UHN 2026',
        ], 'bukti_transfer_reward/sample_transfer.pdf');
        $stats['standard_files'] += 2;

        // 2. ITERATE OVER ALL DB RECORDS IN PpmPublikasiJurnal
        $log('Sinkronisasi dan pembuatan PDF untuk seluruh Publikasi Jurnal...');
        $pubs = PpmPublikasiJurnal::with('user')->get();
        foreach ($pubs as $pub) {
            $path = $pub->file_naskah;
            if (!$path || $path === 'publikasi_naskah/sample_paper.pdf') {
                $slug = Str::slug(substr($pub->judul_artikel, 0, 40));
                $path = "publikasi/naskah_{$pub->id}_{$slug}.pdf";
            }

            $authorsStr = $pub->user ? $pub->user->name : 'Tim Peneliti UHN';
            $affilStr = 'Universitas Harkat Negeri, Indonesia';

            $this->generatePaper([
                'title' => $pub->judul_artikel,
                'journal_name' => $pub->nama_jurnal ?: 'Jurnal Rekayasa & Teknologi Informasi UHN',
                'volume' => $pub->volume ?: '12',
                'issue' => $pub->nomor ?: '2',
                'year' => $pub->tahun_publikasi ?: '2026',
                'doi' => $pub->doi ?: '10.24912/jrti.v12i2.' . ($pub->id + 100),
                'authors' => $authorsStr,
                'affiliation' => $affilStr,
                'email' => $pub->user && $pub->user->email ? $pub->user->email : 'peneliti@uhn.ac.id',
            ], $path);

            if ($pub->file_naskah !== $path) {
                $pub->file_naskah = $path;
                $pub->save();
            }
            $stats['publications']++;
        }

        // 3. ITERATE OVER ALL DB RECORDS IN PpmHki
        $log('Sinkronisasi dan pembuatan PDF untuk seluruh Kekayaan Intelektual (HKI)...');
        $hkis = PpmHki::with('user')->get();
        foreach ($hkis as $hki) {
            $certPath = $hki->file_sertifikat;
            if (!$certPath || $certPath === 'hki_sertifikat/sample_sertifikat.pdf') {
                $slug = Str::slug(substr($hki->judul_hki, 0, 40));
                $certPath = "hki/sertifikat_{$hki->id}_{$slug}.pdf";
            }

            $manualPath = $hki->file_manual_book ?: "hki/manual_{$hki->id}.pdf";
            $pernyataanPath = $hki->file_surat_pernyataan ?: "hki/pernyataan_{$hki->id}.pdf";
            $pengalihanPath = $hki->file_surat_pengalihan ?: "hki/pengalihan_{$hki->id}.pdf";

            $penciptaStr = $hki->user ? $hki->user->name : 'Tim Inventor UHN';

            $this->generateHkiCertificate([
                'judul_hki' => $hki->judul_hki,
                'jenis_ciptaan' => $hki->jenis_hki ?: 'Program Komputer',
                'pencipta' => $penciptaStr,
                'pemegang_hak' => $hki->pemegang_hak ?: 'UNIVERSITAS HARKAT NEGERI',
                'nomor_sertifikat' => $hki->nomor_sertifikat ?: ('0005' . str_pad($hki->id, 5, '0', STR_PAD_LEFT)),
                'nomor_permohonan' => $hki->nomor_permohonan ?: ('EC002026' . str_pad($hki->id, 5, '0', STR_PAD_LEFT)),
                'tanggal_permohonan' => $hki->tanggal_permohonan ? $hki->tanggal_permohonan->format('d F Y') : '15 Januari 2026',
                'tanggal_terbit' => $hki->tanggal_terbit ? $hki->tanggal_terbit->format('d F Y') : '28 Februari 2026',
            ], $certPath);

            $this->generateHkiManualBook([
                'judul_hki' => $hki->judul_hki,
                'jenis_ciptaan' => $hki->jenis_hki ?: 'Program Komputer',
                'pencipta' => $penciptaStr,
                'nomor_permohonan' => $hki->nomor_permohonan ?: ('EC002026' . str_pad($hki->id, 5, '0', STR_PAD_LEFT)),
            ], $manualPath);

            $this->generateHkiPernyataan([
                'judul_hki' => $hki->judul_hki,
                'jenis_ciptaan' => $hki->jenis_hki ?: 'Program Komputer',
                'nama_ketua' => $penciptaStr,
                'nidn' => $hki->user ? $hki->user->nidn : '0412088901',
                'nomor_permohonan' => $hki->nomor_permohonan ?: ('EC002026' . str_pad($hki->id, 5, '0', STR_PAD_LEFT)),
            ], $pernyataanPath);

            $this->generateHkiPengalihan([
                'judul_hki' => $hki->judul_hki,
                'jenis_ciptaan' => $hki->jenis_hki ?: 'Program Komputer',
                'nama_ketua' => $penciptaStr,
                'nidn' => $hki->user ? $hki->user->nidn : '0412088901',
            ], $pengalihanPath);

            $hki->file_sertifikat = $certPath;
            $hki->file_manual_book = $manualPath;
            $hki->file_surat_pernyataan = $pernyataanPath;
            $hki->file_surat_pengalihan = $pengalihanPath;
            $hki->save();

            $stats['hki']++;
        }

        // 4. ITERATE OVER PROPOSALS (PpmUsulan)
        $log('Sinkronisasi dan pembuatan PDF untuk Proposal Penelitian & Mitra...');
        $usulans = PpmUsulan::with('pengusul')->get();
        foreach ($usulans as $u) {
            $propPath = $u->file_proposal_path;
            if (!$propPath || !Storage::disk('public')->exists($propPath)) {
                $slug = Str::slug(substr($u->judul_usulan, 0, 40));
                $propPath = "proposals/usulan_{$u->id}_{$slug}.pdf";
            }

            $this->generateProposal([
                'judul_usulan' => $u->judul_usulan,
                'skema' => $u->skemaBima ? $u->skemaBima->nama_skema : 'Penelitian Dosen Pemula (PDP)',
                'ketua_nama' => $u->pengusul ? $u->pengusul->name : 'Ketua Peneliti UHN',
                'ketua_nidn' => $u->pengusul ? $u->pengusul->nidn : '0412088901',
                'total_rab' => $u->total_rab ?: 25000000,
            ], $propPath);

            $u->file_proposal_path = $propPath;

            // Mitra surat kesediaan
            if ($u->nama_mitra && (!$u->mitra_surat_kesediaan_path || !Storage::disk('public')->exists($u->mitra_surat_kesediaan_path))) {
                $mitraPath = "mitra/surat_mitra_{$u->id}.pdf";
                $this->generateMitraSurat([
                    'nama_mitra' => $u->nama_mitra,
                    'judul_usulan' => $u->judul_usulan,
                    'ketua_nama' => $u->pengusul ? $u->pengusul->name : 'Ketua Peneliti UHN',
                ], $mitraPath);
                $u->mitra_surat_kesediaan_path = $mitraPath;
            } elseif (!$u->mitra_surat_kesediaan_path) {
                $u->mitra_surat_kesediaan_path = 'mitra/sample_surat_kesediaan.pdf';
            }

            $u->save();
            $stats['proposals']++;
        }

        // 5. MONEV & LAPORAN AKHIR
        $log('Sinkronisasi dokumen Monev Kemajuan & Laporan Akhir...');
        $monevs = PpmMonevKemajuan::with('usulan.pengusul')->get();
        foreach ($monevs as $m) {
            $mPath = $m->file_laporan_kemajuan ?: 'monev/demo/laporan_kemajuan_sample.pdf';
            $sptbPath = $m->file_sptb_70 ?: 'monev/demo/sptb_70_sample.pdf';

            if (!Storage::disk('public')->exists($mPath)) {
                $this->generateLaporanKemajuan([
                    'judul_usulan' => $m->usulan ? $m->usulan->judul_usulan : 'Penelitian UHN',
                    'ketua_nama' => $m->usulan && $m->usulan->pengusul ? $m->usulan->pengusul->name : 'Ketua Peneliti',
                ], $mPath);
            }
            if (!Storage::disk('public')->exists($sptbPath)) {
                $this->generateSptb([
                    'persen' => 70,
                    'judul_usulan' => $m->usulan ? $m->usulan->judul_usulan : 'Penelitian UHN',
                    'ketua_nama' => $m->usulan && $m->usulan->pengusul ? $m->usulan->pengusul->name : 'Ketua Peneliti',
                ], $sptbPath);
            }

            $m->file_laporan_kemajuan = $mPath;
            $m->file_sptb_70 = $sptbPath;
            $m->save();
            $stats['monev']++;
        }

        $akhirs = PpmLaporanAkhir::with('usulan.pengusul')->get();
        foreach ($akhirs as $a) {
            $aPath = $a->file_laporan_akhir ?: 'laporan-akhir/demo/laporan_akhir_sample.pdf';
            $sptbPath = $a->file_sptb_100 ?: 'laporan-akhir/demo/sptb_100_sample.pdf';

            if (!Storage::disk('public')->exists($aPath)) {
                $this->generateLaporanAkhir([
                    'judul_usulan' => $a->usulan ? $a->usulan->judul_usulan : 'Penelitian UHN',
                    'ketua_nama' => $a->usulan && $a->usulan->pengusul ? $a->usulan->pengusul->name : 'Ketua Peneliti',
                ], $aPath);
            }
            if (!Storage::disk('public')->exists($sptbPath)) {
                $this->generateSptb([
                    'persen' => 100,
                    'judul_usulan' => $a->usulan ? $a->usulan->judul_usulan : 'Penelitian UHN',
                    'ketua_nama' => $a->usulan && $a->usulan->pengusul ? $a->usulan->pengusul->name : 'Ketua Peneliti',
                ], $sptbPath);
            }

            $a->file_laporan_akhir = $aPath;
            $a->file_sptb_100 = $sptbPath;
            $a->save();
        }

        // 6. KONTRAK & PENCAIRAN
        $log('Sinkronisasi dokumen SPK Kontrak & Pencairan Keuangan...');
        $kontraks = PpmKontrak::with('usulan.pengusul')->get();
        foreach ($kontraks as $k) {
            $spkPath = $k->file_spk_path ?: "kontrak/spk_kontrak_{$k->id}.pdf";
            $tabunganPath = $k->file_buku_tabungan ?: 'tabungan/sample.pdf';

            $ketua = $k->usulan && $k->usulan->pengusul ? $k->usulan->pengusul->name : 'Dosen Peneliti UHN';
            $nidn = $k->usulan && $k->usulan->pengusul ? $k->usulan->pengusul->nidn : '0412088901';
            $judul = $k->usulan ? $k->usulan->judul_usulan : 'Penelitian Terapan Unggulan UHN';

            $this->generateSpkKontrak([
                'nomor_kontrak' => $k->nomor_kontrak ?: "045/SPK-PENELITIAN/LPPM-UHN/III/2026",
                'tanggal_kontrak' => $k->tanggal_kontrak ? $k->tanggal_kontrak->format('d F Y') : '10 Maret 2026',
                'ketua_nama' => $ketua,
                'ketua_nidn' => $nidn,
                'judul_usulan' => $judul,
                'pagu' => $k->pagu_disetujui ?: 25000000,
                'nomor_rekening' => $k->nomor_rekening ?: '131-00-1928475-2',
            ], $spkPath);

            $this->generateBukuTabungan([
                'nama_pemilik' => $ketua,
                'nomor_rekening' => $k->nomor_rekening ?: '131-00-1928475-2',
            ], $tabunganPath);

            $k->file_spk_path = $spkPath;
            $k->file_buku_tabungan = $tabunganPath;
            $k->save();
            $stats['contracts']++;
        }

        // Pencairan Dana
        $pencairans = PpmPencairanDana::with('kontrak.usulan.pengusul')->get();
        foreach ($pencairans as $pc) {
            $buktiPath = $pc->file_bukti_transfer ?: "pencairan/transfer_cair_{$pc->id}.pdf";
            $penerima = $pc->kontrak && $pc->kontrak->usulan && $pc->kontrak->usulan->pengusul ? $pc->kontrak->usulan->pengusul->name : 'Dosen Pengusul';
            $rek = $pc->kontrak ? $pc->kontrak->nomor_rekening : '131-00-1928475-2';

            $this->generateBuktiTransfer([
                'nomor_referensi' => $pc->nomor_referensi ?: ("TRX/UHN/CAIR/" . str_pad($pc->id, 5, '0', STR_PAD_LEFT)),
                'tanggal_transfer' => $pc->tanggal_transfer ? $pc->tanggal_transfer->format('d F Y') : '18 Maret 2026',
                'nominal' => $pc->jumlah_dana ?: 17500000,
                'nama_penerima' => $penerima,
                'nomor_rekening_tujuan' => $rek,
                'keterangan' => "Pencairan Dana Hibah Riset PRISMA UHN Termin {$pc->termin}",
            ], $buktiPath);

            $pc->file_bukti_transfer = $buktiPath;
            $pc->save();
        }

        // 7. KLAIM REWARD & DISTRIBUSI
        $log('Sinkronisasi dokumen Klaim Reward & Bukti Transfer...');
        $rewards = PpmKlaimReward::with(['publikasi', 'hki', 'distribusi'])->get();
        foreach ($rewards as $r) {
            $pernyataanPath = $r->file_surat_pernyataan ?: 'surat_pernyataan_reward/sample_kesepakatan.pdf';
            $judul = $r->publikasi ? $r->publikasi->judul_artikel : ($r->hki ? $r->hki->judul_hki : 'Karya Inovasi UHN');
            $jenis = $r->jenis_klaim === 'Publikasi' ? 'Publikasi Ilmiah' : 'Kekayaan Intelektual (HKI)';

            $this->generateRewardKesepakatan([
                'nomor_klaim' => $r->nomor_klaim ?: 'REW/2026/09/DEMO1',
                'judul_karya' => $judul,
                'jenis_karya' => $jenis,
                'total_reward' => $r->total_reward ?: 5000000,
            ], $pernyataanPath);

            $r->file_surat_pernyataan = $pernyataanPath;
            $r->save();

            // Distribusi transfer receipts
            foreach ($r->distribusi as $dist) {
                $distPath = $dist->file_bukti_transfer ?: "bukti_transfer_reward/transfer_reward_{$dist->id}.pdf";
                $this->generateBuktiTransfer([
                    'nomor_referensi' => $dist->nomor_referensi ?: ("TRX/REW/UHN/" . str_pad($dist->id, 6, '0', STR_PAD_LEFT)),
                    'tanggal_transfer' => $dist->tanggal_transfer ? $dist->tanggal_transfer->format('d F Y') : '25 Maret 2026',
                    'nominal' => $dist->nominal_bagian ?: 3000000,
                    'nama_penerima' => $dist->nama_penulis,
                    'nomor_rekening_tujuan' => $dist->nomor_rekening ?: '131-00-1928475-2',
                    'nama_bank' => $dist->nama_bank ?: 'Bank Mandiri',
                    'keterangan' => "Pencairan Insentif Reward UHN - {$dist->nama_penulis} ({$dist->peran_penulis})",
                ], $distPath);

                $dist->file_bukti_transfer = $distPath;
                $dist->save();
            }

            $stats['rewards']++;
        }

        return $stats;
    }
}


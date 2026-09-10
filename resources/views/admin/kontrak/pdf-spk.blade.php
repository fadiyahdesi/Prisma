<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Perjanjian Kontrak - {{ $kontrak->nomor_kontrak }}</title>
    <style>
        @page {
            margin: 2.2cm 2cm 2cm 2cm;
            footer: page-footer;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #111;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            letter-spacing: 0.5px;
        }
        .header h2 {
            font-size: 12pt;
            font-weight: bold;
            margin: 3px 0;
            text-transform: uppercase;
        }
        .header p {
            font-size: 9pt;
            margin: 0;
            color: #444;
        }
        .title {
            text-align: center;
            margin-bottom: 22px;
        }
        .title h3 {
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
            text-transform: uppercase;
        }
        .title p {
            font-size: 10pt;
            margin: 4px 0 0 0;
        }
        .article {
            margin-bottom: 14px;
            text-align: justify;
        }
        .article-title {
            text-align: center;
            font-weight: bold;
            margin-bottom: 4px;
            text-transform: uppercase;
            font-size: 10.5pt;
        }
        table.meta-table {
            width: 100%;
            margin: 8px 0;
            border-collapse: collapse;
        }
        table.meta-table td {
            vertical-align: top;
            padding: 3px 0;
            font-size: 10.5pt;
        }
        .signatures {
            margin-top: 30px;
            width: 100%;
        }
        .signatures td {
            vertical-align: top;
            width: 50%;
            text-align: center;
            font-size: 10.5pt;
        }
        .qr-box {
            margin: 10px auto;
            text-align: center;
        }
        .qr-box svg {
            display: inline-block;
        }
        .doc-hash {
            font-family: monospace;
            font-size: 8pt;
            color: #666;
            margin-top: 5px;
            word-break: break-all;
        }
    </style>
</head>
<body>

    <!-- Kop Surat LPPM UHN -->
    <div class="header">
        <h1>UNIVERSITAS HARKAT NEGERI</h1>
        <h2>LEMBAGA PENELITIAN DAN PENGABDIAN KEPADA MASYARAKAT (LPPM)</h2>
        <p>Jalan Harkat Negeri No. 101, Medan, Sumatera Utara | Telepon: (061) 88997766 | Email: lppm@harkatnegeri.ac.id</p>
    </div>

    <!-- Judul Kontrak -->
    <div class="title">
        <h3>SURAT PERJANJIAN PENUGASAN PELAKSANAAN HIBAH RISET & ABMAS</h3>
        <p>Nomor Kontrak: <strong>{{ $kontrak->nomor_kontrak }}</strong></p>
        <p>Berdasarkan SK Penetapan Pemenang: <strong>{{ $kontrak->nomor_sk }}</strong></p>
    </div>

    <p style="text-align: justify;">
        Pada hari ini, tanggal <strong>{{ \Carbon\Carbon::parse($kontrak->tanggal_kontrak)->isoFormat('D MMMM Y') }}</strong>, bertempat di Universitas Harkat Negeri, yang bertanda tangan di bawah ini:
    </p>

    <!-- Pihak Pertama -->
    <table class="meta-table">
        <tr>
            <td style="width: 25px;">1.</td>
            <td style="width: 150px;">Nama Pejabat</td>
            <td style="width: 10px;">:</td>
            <td><strong>Prof. Dr. Ir. Mangatas Sitorus, M.Sc.</strong></td>
        </tr>
        <tr>
            <td></td>
            <td>Jabatan</td>
            <td>:</td>
            <td>Kepala Lembaga Penelitian dan Pengabdian kepada Masyarakat (LPPM)</td>
        </tr>
        <tr>
            <td></td>
            <td>Instansi</td>
            <td>:</td>
            <td>Universitas Harkat Negeri</td>
        </tr>
        <tr>
            <td></td>
            <td colspan="3" style="padding-top: 4px; color: #444; font-style: italic;">
                Bertindak untuk dan atas nama LPPM Universitas Harkat Negeri, selanjutnya disebut sebagai <strong>PIHAK PERTAMA</strong>.
            </td>
        </tr>
    </table>

    <!-- Pihak Kedua -->
    <table class="meta-table">
        <tr>
            <td style="width: 25px;">2.</td>
            <td style="width: 150px;">Nama Peneliti</td>
            <td style="width: 10px;">:</td>
            <td><strong>{{ $kontrak->usulan->pengusul->name ?? '-' }}</strong></td>
        </tr>
        <tr>
            <td></td>
            <td>NIDN / NIP</td>
            <td>:</td>
            <td>{{ $kontrak->usulan->pengusul->nidn_nim ?? '-' }}</td>
        </tr>
        <tr>
            <td></td>
            <td>Fakultas / Prodi</td>
            <td>:</td>
            <td>{{ $kontrak->usulan->pengusul->fakultas->nama_fakultas ?? '-' }} / {{ $kontrak->usulan->pengusul->prodi->nama_prodi ?? '-' }}</td>
        </tr>
        <tr>
            <td></td>
            <td colspan="3" style="padding-top: 4px; color: #444; font-style: italic;">
                Bertindak selaku Ketua Tim Pengusul Pelaksana Hibah, selanjutnya disebut sebagai <strong>PIHAK KEDUA</strong>.
            </td>
        </tr>
    </table>

    <p style="text-align: justify; margin-top: 10px;">
        PIHAK PERTAMA dan PIHAK KEDUA secara bersama-sama bersepakat mengikatkan diri dalam Surat Perjanjian Pelaksanaan Hibah Riset dan Pengabdian kepada Masyarakat dengan ketentuan-ketentuan sebagai berikut:
    </p>

    <!-- Pasal 1 -->
    <div class="article">
        <div class="article-title">Pasal 1: Ruang Lingkup Pekerjaan</div>
        <p>PIHAK PERTAMA menugaskan kepada PIHAK KEDUA, dan PIHAK KEDUA menerima penugasan tersebut untuk melaksanakan kegiatan hibah dengan rincian:</p>
        <table class="meta-table" style="margin-left: 15px;">
            <tr>
                <td style="width: 140px;">Judul Kegiatan</td>
                <td style="width: 10px;">:</td>
                <td><strong>{{ $kontrak->usulan->judul_usulan }}</strong></td>
            </tr>
            <tr>
                <td>Skema Hibah BIMA</td>
                <td>:</td>
                <td>{{ $kontrak->usulan->skema->nama_skema ?? '-' }}</td>
            </tr>
            <tr>
                <td>Tahun Anggaran</td>
                <td>:</td>
                <td>{{ $kontrak->usulan->periode->tahun_anggaran ?? date('Y') }}</td>
            </tr>
        </table>
    </div>

    <!-- Pasal 2 -->
    <div class="article">
        <div class="article-title">Pasal 2: Besaran Dana & Tata Cara Pembayaran</div>
        <p>1. Total dana hibah yang disetujui untuk pelaksanaan kegiatan ini adalah sebesar <strong>Rp {{ number_format($kontrak->pagu_disetujui, 0, ',', '.') }}</strong> (Termasuk pajak yang berlaku sesuai regulasi keuangan negara).</p>
        <p>2. Penyaluran dana dilakukan secara bertahap oleh Divisi Keuangan LPPM melalui transfer ke rekening bank resmi PIHAK KEDUA dengan mekanisme:</p>
        <ul style="margin: 4px 0 4px 20px; padding: 0;">
            <li><strong>Tahap I (Termin 70%)</strong>: Sebesar <strong>Rp {{ number_format($kontrak->dana_termin_1, 0, ',', '.') }}</strong> dibayarkan setelah Surat Perjanjian Kontrak (SPK) ditandatangani oleh kedua pihak dan rekening bank tervalidasi.</li>
            <li><strong>Tahap II (Termin 30%)</strong>: Sebesar <strong>Rp {{ number_format($kontrak->dana_termin_2, 0, ',', '.') }}</strong> dibayarkan setelah PIHAK KEDUA mengunggah Laporan Kemajuan, Logbook harian, Catatan Penggunaan Anggaran 70%, serta mengikuti Seminar Hasil (Monev).</li>
        </ul>
    </div>

    <!-- Pasal 3 -->
    <div class="article">
        <div class="article-title">Pasal 3: Hak dan Kewajiban</div>
        <p>1. PIHAK KEDUA berkewajiban menyelesaikan penelitian sesuai proposal dan mencapai target luaran yang dijanjikan.</p>
        <p>2. PIHAK PERTAMA berhak melakukan pemantauan dan evaluasi (Monev) berkala terhadap kemajuan pelaksanaan kegiatan.</p>
    </div>

    <!-- Pasal 4 -->
    <div class="article">
        <div class="article-title">Pasal 4: Keaslian & Verifikasi Digital</div>
        <p>Dokumen ini diterbitkan dan ditandatangani secara elektronik menggunakan sistem PRISMA UHN berstandar BIMA Kemdiktisaintek. Keaslian dokumen dapat diverifikasi secara publik melalui pemindaian QR Code resmi yang tertera.</p>
    </div>

    <!-- Tanda Tangan Ganda Ber-QR Code -->
    <table class="signatures">
        <tr>
            <td>
                <p>PIHAK PERTAMA,<br>Kepala LPPM Universitas Harkat Negeri</p>
                <div class="qr-box">
                    {!! $qrCodeSvg !!}
                </div>
                <p><strong>Prof. Dr. Ir. Mangatas Sitorus, M.Sc.</strong><br>NIP. 196803151993031002</p>
                <span style="font-size: 8pt; color: #2b6cb0;">(Ditandatangani secara digital &ndash; Sah)</span>
            </td>
            <td>
                <p>PIHAK KEDUA,<br>Ketua Tim Pengusul</p>
                <div class="qr-box">
                    @if($kontrak->signed_by_pengusul)
                        {!! $qrCodeSvg !!}
                    @else
                        <div style="height: 110px; line-height: 110px; border: 1px dashed #bbb; color: #999; font-size: 9pt;">
                            Menunggu Tanda Tangan Digital
                        </div>
                    @endif
                </div>
                <p><strong>{{ $kontrak->usulan->pengusul->name ?? '-' }}</strong><br>NIDN. {{ $kontrak->usulan->pengusul->nidn_nim ?? '-' }}</p>
                @if($kontrak->signed_by_pengusul)
                    <span style="font-size: 8pt; color: #2b6cb0;">(Ditandatangani secara digital pada {{ $kontrak->signed_by_pengusul_at ? $kontrak->signed_by_pengusul_at->isoFormat('D MMMM Y, HH:mm') : '-' }} WIB)</span>
                @else
                    <span style="font-size: 8pt; color: #e53e3e;">(Belum ditandatangani)</span>
                @endif
            </td>
        </tr>
    </table>

    <div style="margin-top: 25px; border-top: 1px solid #ddd; padding-top: 5px; text-align: center;">
        <p class="doc-hash">
            Kode Verifikasi Integritas (SHA-256): {{ $kontrak->document_hash ?? hash('sha256', $kontrak->verification_token) }}<br>
            Tautan Verifikasi: {{ $verificationUrl }}
        </p>
    </div>

</body>
</html>


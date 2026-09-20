<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $judul_usulan ?? 'Proposal Penelitian UHN' }}</title>
    <style>
        @page {
            margin: 20mm 15mm 20mm 15mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9.5pt;
            line-height: 1.4;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }
        .page-break {
            page-break-after: always;
        }
        /* COVER STYLES */
        .cover-box {
            text-align: center;
            padding: 20px 10px;
        }
        .cover-logo-badge {
            display: inline-block;
            background-color: #681727;
            color: #ffffff;
            font-size: 15pt;
            font-weight: bold;
            letter-spacing: 2px;
            padding: 12px 28px;
            border-radius: 8px;
            margin-bottom: 25px;
            border: 2px solid #854d0e;
        }
        .cover-type {
            font-size: 12pt;
            font-weight: bold;
            color: #854d0e;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 8px;
        }
        .cover-schema {
            font-size: 14pt;
            font-weight: bold;
            color: #681727;
            text-transform: uppercase;
            margin-bottom: 30px;
        }
        .cover-title {
            font-size: 17pt;
            font-weight: bold;
            color: #0f172a;
            line-height: 1.35;
            margin-bottom: 35px;
            padding: 0 15px;
        }
        .cover-team-box {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 16px 20px;
            margin: 0 auto 40px auto;
            width: 80%;
            text-align: left;
            font-size: 9.5pt;
            border-radius: 6px;
        }
        .cover-footer {
            margin-top: 40px;
            font-weight: bold;
            font-size: 10.5pt;
            color: #334155;
            line-height: 1.5;
        }

        /* LEMBAR PENGESAHAN */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .kop-univ {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #681727;
            text-align: center;
        }
        .kop-lembaga {
            font-size: 10.5pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #1e293b;
            text-align: center;
        }
        .kop-sub {
            font-size: 8pt;
            color: #64748b;
            text-align: center;
        }
        .kop-line {
            border-top: 2px solid #681727;
            border-bottom: 1px solid #681727;
            height: 2px;
            margin-bottom: 15px;
        }
        .doc-title {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin-bottom: 15px;
        }
        .spec-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-bottom: 15px;
        }
        .spec-table td {
            padding: 5px 6px;
            vertical-align: top;
            border-bottom: 1px solid #f1f5f9;
        }
        .sig-grid {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }
        .sig-grid td {
            vertical-align: top;
            font-size: 9pt;
        }

        /* CHAPTERS */
        h2.chap-head {
            font-size: 11pt;
            font-weight: bold;
            color: #681727;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 3px;
            margin-top: 16px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        h3.sec-head {
            font-size: 9.5pt;
            font-weight: bold;
            color: #1e293b;
            margin-top: 10px;
            margin-bottom: 4px;
        }
        p {
            text-align: justify;
            text-indent: 1.5em;
            margin: 0 0 6px 0;
        }
        .rab-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin: 8px 0 12px 0;
        }
        .rab-table th, .rab-table td {
            border: 1px solid #cbd5e1;
            padding: 5px 6px;
        }
        .rab-table th {
            background-color: #f1f5f9;
            font-weight: bold;
            color: #1e293b;
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- HALAMAN 1: COVER -->
    <div class="cover-box">
        <div class="cover-logo-badge">UNIVERSITAS HARKAT NEGERI</div>
        <div class="cover-type">PROPOSAL PENELITIAN HIBAH INTERNAL PRISMA</div>
        <div class="cover-schema">{{ $skema ?? 'SKEMA PENELITIAN DOSEN PEMULA (PDP)' }}</div>

        <h1 class="cover-title">{{ $judul_usulan ?? 'Penerapan Model Convolutional Neural Network (CNN) untuk Deteksi Dini Penyakit Daun Padi Berbasis Mobile App' }}</h1>

        <div class="cover-team-box">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 32%; font-weight: bold; color: #475569;">Ketua Peneliti:</td>
                    <td style="width: 68%;"><strong>{{ $ketua_nama ?? 'Lintang Patria, S.Kom., M.Cs.' }}</strong> (NIDN: {{ $ketua_nidn ?? '0412088901' }})</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; color: #475569; padding-top: 5px;">Anggota Peneliti:</td>
                    <td style="padding-top: 5px;">Dr. Budi Wicaksono, M.Kom. &bull; Siti Rahmawati, M.Eng.</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; color: #475569; padding-top: 5px;">Fakultas / Prodi:</td>
                    <td style="padding-top: 5px;">{{ $fakultas ?? 'Fakultas Sains dan Teknologi' }} / Informatika</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; color: #475569; padding-top: 5px;">Bidang Fokus RIRN:</td>
                    <td style="padding-top: 5px;">Pangan - Pertanian Presisi &amp; Ketahanan Pangan</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; color: #475569; padding-top: 5px;">Target TKT:</td>
                    <td style="padding-top: 5px;">TKT 3 Menuju TKT 6</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; color: #475569; padding-top: 5px;">Total Anggaran RAB:</td>
                    <td style="padding-top: 5px; font-weight: bold; color: #991b1b;">Rp {{ number_format($total_rab ?? 25000000, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <div class="cover-footer">
            LEMBAGA PENELITIAN DAN PENGABDIAN KEPADA MASYARAKAT<br>
            UNIVERSITAS HARKAT NEGERI<br>
            TAHUN ANGGARAN 2026
        </div>
    </div>

    <div class="page-break"></div>

    <!-- HALAMAN 2: LEMBAR PENGESAHAN -->
    <table class="kop-table">
        <tr>
            <td>
                <div class="kop-univ">UNIVERSITAS HARKAT NEGERI</div>
                <div class="kop-lembaga">LEMBAGA PENELITIAN DAN PENGABDIAN KEPADA MASYARAKAT (LPPM)</div>
                <div class="kop-sub">Sistem Informasi Riset dan Inovasi PRISMA UHN &bull; https://prisma.uhn.ac.id</div>
            </td>
        </tr>
    </table>
    <div class="kop-line"></div>

    <div class="doc-title">LEMBAR PENGESAHAN PROPOSAL PENELITIAN</div>

    <table class="spec-table">
        <tr>
            <td style="width: 28%; font-weight: bold;">1. Judul Penelitian</td>
            <td style="width: 3%;">:</td>
            <td style="width: 69%; font-weight: bold; color: #681727;">{{ $judul_usulan ?? 'Penerapan Model Convolutional Neural Network (CNN) untuk Deteksi Dini Penyakit Daun Padi Berbasis Mobile App' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">2. Skema Hibah</td>
            <td>:</td>
            <td>{{ $skema ?? 'Penelitian Dosen Pemula (PDP)' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">3. Ketua Peneliti</td>
            <td>:</td>
            <td>
                <strong>{{ $ketua_nama ?? 'Lintang Patria, S.Kom., M.Cs.' }}</strong><br>
                NIDN: {{ $ketua_nidn ?? '0412088901' }} &bull; Jabatan Fungsional: Asisten Ahli<br>
                Program Studi: S1 Informatika, Fakultas Sains dan Teknologi
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold;">4. Jumlah Anggota</td>
            <td>:</td>
            <td>2 Orang Dosen, 2 Orang Mahasiswa</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">5. Jangka Waktu Riset</td>
            <td>:</td>
            <td>8 (Delapan) Bulan (Maret - Oktober 2026)</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">6. Biaya yang Diusulkan</td>
            <td>:</td>
            <td style="font-weight: bold; color: #991b1b;">Rp {{ number_format($total_rab ?? 25000000, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">7. Luaran Wajib</td>
            <td>:</td>
            <td>1 Artikel Jurnal Terakreditasi SINTA 2 &bull; 1 Hak Cipta Perangkat Lunak</td>
        </tr>
    </table>

    <p style="text-indent: 0; margin-top: 15px; font-style: italic;">
        Proposal ini telah diperiksa kelayakan substansi dan kesesuaian roadmap risetnya serta disetujui untuk diajukan dalam seleksi pendanaan PRISMA UHN Tahun Anggaran 2026.
    </p>

    <table class="sig-grid">
        <tr>
            <td style="width: 50%;">
                Menyetujui,<br>
                Dekan Fakultas Sains dan Teknologi<br><br><br><br>
                <strong><u>Dr. Ir. Ahmad Sudrajat, M.T.</u></strong><br>
                NIP. 197405112001121002
            </td>
            <td style="width: 50%; padding-left: 20px;">
                Bandung, 10 Februari 2026<br>
                Ketua Peneliti Pengusul,<br><br><br><br>
                <strong><u>{{ $ketua_nama ?? 'Lintang Patria, S.Kom., M.Cs.' }}</u></strong><br>
                NIDN. {{ $ketua_nidn ?? '0412088901' }}
            </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center; padding-top: 25px;">
                Mengetahui,<br>
                <strong>Kepala LPPM Universitas Harkat Negeri</strong><br><br><br><br>
                <strong><u>Prof. Dr. Ir. Hendra Gunawan, M.T.</u></strong><br>
                NIP. 197008141995121001
            </td>
        </tr>
    </table>

    <div class="page-break"></div>

    <!-- HALAMAN 3: SUBSTANSI PROPOSAL & RAB -->
    <h2 class="chap-head">BAB 1. Pendahuluan &amp; Urgensi Penelitian</h2>
    <p>
        Sektor pertanian tanaman pangan menghadapi tantangan berat akibat serangan hama dan patogen daun tanaman yang sering terlambat diidentifikasi oleh para petani tradisional. Deteksi dini secara visual membutuhkan keahlian agronomis yang tidak selalu tersedia di lapangan. Pemanfaatan teknologi Computer Vision berbasis deep learning menawarkan akurasi tinggi dan dapat diakses dengan mudah melalui perangkat telepon pintar (smartphone) petani.
    </p>
    <p>
        Penelitian ini bertujuan membangun aplikasi deteksi cerdas berlatensi rendah yang dapat berjalan secara offline di lingkungan persawahan terpencil tanpa konektivitas internet stabil.
    </p>

    <h2 class="chap-head">BAB 2. Metode Penelitian &amp; Roadmap</h2>
    <p>
        Tahapan riset meliputi: (1) Akuisisi citra daun padi bergejala penyakit (Blast, Brown Spot, Leaf Blight) sebanyak 5.000 citra beranotasi; (2) Pre-processing dan augmentasi citra; (3) Pelatihan arsitektur Convolutional Neural Network teroptimasi INT8; (4) Integrasi framework model ke dalam aplikasi mobile Android Flutter; (5) Pengujian akurasi deteksi di lahan kemitraan petani binaan LPPM UHN.
    </p>

    <h2 class="chap-head">BAB 3. Rencana Anggaran Biaya (RAB) Rinci</h2>
    <table class="rab-table">
        <thead>
            <tr>
                <th style="width: 8%;">No</th>
                <th style="width: 42%;">Komponen Pengeluaran</th>
                <th style="width: 15%;">Volume</th>
                <th style="width: 17%;">Biaya Satuan</th>
                <th style="width: 18%;">Subtotal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center;">1</td>
                <td>Bahan Habis Pakai &amp; Sensor IoT Telemetri</td>
                <td style="text-align: center;">1 Paket</td>
                <td style="text-align: right;">8.500.000</td>
                <td style="text-align: right;">8.500.000</td>
            </tr>
            <tr>
                <td style="text-align: center;">2</td>
                <td>Sewa Komputasi Server GPU Cloud (Pelatihan CNN)</td>
                <td style="text-align: center;">3 Bulan</td>
                <td style="text-align: right;">2.000.000</td>
                <td style="text-align: right;">6.000.000</td>
            </tr>
            <tr>
                <td style="text-align: center;">3</td>
                <td>Perjalanan Lapangan &amp; Akuisisi Data Mitra</td>
                <td style="text-align: center;">4 Trip</td>
                <td style="text-align: right;">1.250.000</td>
                <td style="text-align: right;">5.000.000</td>
            </tr>
            <tr>
                <td style="text-align: center;">4</td>
                <td>Penyusunan Laporan, Publikasi &amp; Pendaftaran HKI</td>
                <td style="text-align: center;">1 Paket</td>
                <td style="text-align: right;">5.500.000</td>
                <td style="text-align: right;">5.500.000</td>
            </tr>
            <tr style="background-color: #f8fafc; font-weight: bold;">
                <td colspan="4" style="text-align: right;">TOTAL USULAN BIAYA PENELITIAN:</td>
                <td style="text-align: right; color: #991b1b;">Rp {{ number_format($total_rab ?? 25000000, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <h2 class="chap-head">BAB 4. Target Luaran dan Indikator Kinerja</h2>
    <p>
        Target capaian penelitian ini mencakup: (a) 1 Artikel Ilmiah pada Jurnal Nasional Terakreditasi SINTA 2; (b) 1 Sertifikat Hak Cipta Perangkat Lunak dari DJKI Kemenkumham RI; (c) Prototipe Mobile App fungsional dengan skor akurasi deteksi &gt; 94% pada TKT Level 6.
    </p>

</body>
</html>


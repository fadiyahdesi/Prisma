<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kemajuan Penelitian (Monev 70%) - PRISMA UHN</title>
    <style>
        @page {
            margin: 20mm 15mm 20mm 15mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 9.5pt;
            line-height: 1.4;
            color: #1e293b;
        }
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
        .doc-badge {
            background-color: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 8.5pt;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 6px;
        }
        .doc-title {
            text-align: center;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 15px;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-bottom: 16px;
        }
        .meta-table td {
            padding: 4px 6px;
            vertical-align: top;
            border-bottom: 1px solid #f1f5f9;
        }
        h2.sec-title {
            font-size: 10.5pt;
            font-weight: bold;
            color: #681727;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 3px;
            margin-top: 14px;
            margin-bottom: 6px;
        }
        p {
            text-align: justify;
            margin: 0 0 8px 0;
        }
        .progress-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin: 8px 0 12px 0;
        }
        .progress-table th, .progress-table td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
        }
        .progress-table th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: center;
        }
        .status-pill {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 7.5pt;
            font-weight: bold;
        }
        .pill-done { background-color: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
        .pill-progress { background-color: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .sig-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
    </style>
</head>
<body>

    <table class="kop-table">
        <tr>
            <td>
                <div class="kop-univ">UNIVERSITAS HARKAT NEGERI</div>
                <div class="kop-lembaga">LEMBAGA PENELITIAN DAN PENGABDIAN KEPADA MASYARAKAT (LPPM)</div>
                <div class="kop-sub">Monitoring dan Evaluasi (Monev) Penelitian Internal Tahun 2026</div>
            </td>
        </tr>
    </table>
    <div class="kop-line"></div>

    <div style="text-align: center;">
        <div class="doc-badge">MONITORING &amp; EVALUASI INTERMEDIER (KEMAJUAN 70%)</div>
        <div class="doc-title">LAPORAN KEMAJUAN PELAKSANAAN PENELITIAN</div>
    </div>

    <table class="meta-table">
        <tr>
            <td style="width: 25%; font-weight: bold;">Judul Usulan</td>
            <td style="width: 3%;">:</td>
            <td style="width: 72%; font-weight: bold; color: #681727;">{{ $judul_usulan ?? 'Penerapan Model Convolutional Neural Network (CNN) untuk Deteksi Dini Penyakit Daun Padi Berbasis Mobile App' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Ketua Peneliti</td>
            <td>:</td>
            <td><strong>{{ $ketua_nama ?? 'Lintang Patria, S.Kom., M.Cs.' }}</strong> (NIDN: {{ $ketua_nidn ?? '0412088901' }})</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Skema Hibah</td>
            <td>:</td>
            <td>{{ $skema ?? 'Penelitian Dosen Pemula (PDP)' }} - TA 2026</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Nomor Kontrak SPK</td>
            <td>:</td>
            <td>045/SPK-PENELITIAN/LPPM-UHN/III/2026</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Pagu Anggaran Total</td>
            <td>:</td>
            <td>Rp {{ number_format($pagu ?? 25000000, 0, ',', '.') }} (Pencairan Termin I 70%: Rp {{ number_format(($pagu ?? 25000000) * 0.7, 0, ',', '.') }})</td>
        </tr>
    </table>

    <h2 class="sec-title">1. Ringkasan Eksekutif Kemajuan Penelitian</h2>
    <p>
        Hingga periode monitoring pertengahan ini, pelaksanaan riset telah mencapai progres kumulatif sebesar <strong>76.5%</strong>. Tim peneliti telah menyelesaikan tahapan studi pustaka, instalasi sensor telemetri di lokasi percontohan mitra, serta pengumpulan basis data citra primer sebanyak 4.200 sampel foto daun padi dengan berbagai variasi kondisi pencahayaan. Model kecerdasan buatan telah memasuki tahap fine-tuning dengan akurasi validasi awal mencapai 92.8%.
    </p>

    <h2 class="sec-title">2. Matriks Capaian Indikator Kinerja &amp; Luaran</h2>
    <table class="progress-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 30%;">Rencana Kegiatan / Luaran</th>
                <th style="width: 15%;">Target Awal</th>
                <th style="width: 25%;">Realisasi Saat Ini</th>
                <th style="width: 15%;">Status</th>
                <th style="width: 10%;">% Capaian</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center;">1</td>
                <td>Akuisisi Data &amp; Preprocessing Citra</td>
                <td>3.000 Citra</td>
                <td>4.200 Citra Teranotasi</td>
                <td style="text-align: center;"><span class="status-pill pill-done">Selesai 100%</span></td>
                <td style="text-align: center; font-weight: bold;">100%</td>
            </tr>
            <tr>
                <td style="text-align: center;">2</td>
                <td>Pelatihan &amp; Optimasi Model CNN INT8</td>
                <td>Akurasi &gt; 90%</td>
                <td>Akurasi 92.8% (TFLite)</td>
                <td style="text-align: center;"><span class="status-pill pill-done">Selesai 100%</span></td>
                <td style="text-align: center; font-weight: bold;">100%</td>
            </tr>
            <tr>
                <td style="text-align: center;">3</td>
                <td>Integrasi Antarmuka Mobile App Flutter</td>
                <td>Aplikasi Android</td>
                <td>Modul Kamera &amp; Prediksi Ready</td>
                <td style="text-align: center;"><span class="status-pill pill-progress">Dalam Proses</span></td>
                <td style="text-align: center; font-weight: bold;">75%</td>
            </tr>
            <tr>
                <td style="text-align: center;">4</td>
                <td>Draft Artikel Jurnal SINTA 2</td>
                <td>1 Naskah Submit</td>
                <td>Status: Under Review di JRKC</td>
                <td style="text-align: center;"><span class="status-pill pill-progress">Dalam Proses</span></td>
                <td style="text-align: center; font-weight: bold;">65%</td>
            </tr>
            <tr>
                <td style="text-align: center;">5</td>
                <td>Pendaftaran Hak Cipta Perangkat Lunak</td>
                <td>1 Sertifikat HKI</td>
                <td>Draft Manual Book disusun</td>
                <td style="text-align: center;"><span class="status-pill pill-progress">Dalam Proses</span></td>
                <td style="text-align: center; font-weight: bold;">45%</td>
            </tr>
        </tbody>
    </table>

    <h2 class="sec-title">3. Rencana Tindak Lanjut Menuju Laporan Akhir (100%)</h2>
    <p>
        Pada sisa masa pelaksanaan riset (Termin II 30%), tim peneliti akan berfokus pada pengujian performa aplikasi secara langsung kepada kelompok tani mitra, perbaikan bug antarmuka pengguna, publikasi final artikel ilmiah, serta penyelesaian pendaftaran HKI di Sentra Kekayaan Intelektual LPPM UHN.
    </p>

    <table class="sig-table">
        <tr>
            <td style="width: 50%;">
                Mengetahui,<br>
                <strong>Reviewer Internal LPPM UHN</strong><br><br><br><br>
                <strong><u>Dr. Eng. Wahyu Tri Wibowo, S.T., M.T.</u></strong><br>
                NIP. 197802142005011002
            </td>
            <td style="width: 50%; padding-left: 20px;">
                Bandung, 15 Juli 2026<br>
                Ketua Peneliti,<br><br><br><br>
                <strong><u>{{ $ketua_nama ?? 'Lintang Patria, S.Kom., M.Cs.' }}</u></strong><br>
                NIDN. {{ $ketua_nidn ?? '0412088901' }}
            </td>
        </tr>
    </table>

</body>
</html>


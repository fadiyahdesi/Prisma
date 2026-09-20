<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Akhir Hasil Penelitian (100%) - PRISMA UHN</title>
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
            background-color: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
            padding: 4px 14px;
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
            background-color: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }
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
                <div class="kop-sub">Evaluasi Akhir Kinerja Riset &bull; Tahun Anggaran 2026</div>
            </td>
        </tr>
    </table>
    <div class="kop-line"></div>

    <div style="text-align: center;">
        <div class="doc-badge">LAPORAN AKHIR TUNTAS (CAPAIAN 100%)</div>
        <div class="doc-title">LAPORAN AKHIR HASIL PELAKSANAAN PENELITIAN</div>
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
            <td>{{ $skema ?? 'Penelitian Dosen Pemula (PDP)' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Nomor Kontrak SPK</td>
            <td>:</td>
            <td>045/SPK-PENELITIAN/LPPM-UHN/III/2026</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Realisasi Anggaran Total</td>
            <td>:</td>
            <td style="font-weight: bold; color: #047857;">Rp {{ number_format($pagu ?? 25000000, 0, ',', '.') }} (100% Terealisasi &amp; Diaudit)</td>
        </tr>
    </table>

    <h2 class="sec-title">1. Kesimpulan Akhir dan Hasil Penelitian</h2>
    <p>
        Program penelitian ini telah diselesaikan secara tuntas 100% sesuai dengan seluruh target indikator kinerja yang tertuang dalam kontrak penugasan riset. Prototipe aplikasi mobile deteksi penyakit tanaman padi telah diuji coba di lahan persawahan mitra seluas 2 hektar dengan tingkat akurasi inferensi mencapai 94.6% dan latensi pemrosesan 42 milidetik per frame citra.
    </p>

    <h2 class="sec-title">2. Rekapitulasi Pemenuhan Luaran Wajib dan Tambahan</h2>
    <table class="progress-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 32%;">Target Luaran</th>
                <th style="width: 30%;">Status Capaian Akhir</th>
                <th style="width: 18%;">Nomor Registrasi / Identitas</th>
                <th style="width: 15%;">Status Audit</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center;">1</td>
                <td>Artikel Jurnal Nasional SINTA 2</td>
                <td><strong>Published / Terbit Resmi</strong></td>
                <td>DOI: 10.24912/jrti.v12i2.892</td>
                <td style="text-align: center;"><span class="status-pill">&check; Tervalidasi</span></td>
            </tr>
            <tr>
                <td style="text-align: center;">2</td>
                <td>Hak Cipta Program Komputer</td>
                <td><strong>Sertifikat DJKI Terbit</strong></td>
                <td>No. Sertifikat: 000582914</td>
                <td style="text-align: center;"><span class="status-pill">&check; Tervalidasi</span></td>
            </tr>
            <tr>
                <td style="text-align: center;">3</td>
                <td>Tingkat Kesiapterapan Teknologi (TKT)</td>
                <td>Mencapai TKT Level 6</td>
                <td>Verifikasi Tim TKT LPPM</td>
                <td style="text-align: center;"><span class="status-pill">&check; Tervalidasi</span></td>
            </tr>
            <tr>
                <td style="text-align: center;">4</td>
                <td>Dokumentasi SPTB 100%</td>
                <td>Laporan Keuangan Diaudit</td>
                <td>SPTB-100/LPPM/2026</td>
                <td style="text-align: center;"><span class="status-pill">&check; Tervalidasi</span></td>
            </tr>
        </tbody>
    </table>

    <h2 class="sec-title">3. Rencana Keberlanjutan &amp; Komersialisasi Inovasi</h2>
    <p>
        Berdasarkan hasil positif dari uji lapangan mitra, tim peneliti merencanakan pengajuan proposal lanjutan pada skema Kolaborasi Industri Kedaireka / Matching Fund Kemendikbudristek untuk tahun anggaran berikutnya, guna memperluas implementasi ke skala multikabupaten di Jawa Barat.
    </p>

    <table class="sig-table">
        <tr>
            <td style="width: 50%;">
                Mengetahui &amp; Menyetujui,<br>
                <strong>Kepala LPPM Universitas Harkat Negeri</strong><br><br><br><br>
                <strong><u>Prof. Dr. Ir. Hendra Gunawan, M.T.</u></strong><br>
                NIP. 197008141995121001
            </td>
            <td style="width: 50%; padding-left: 20px;">
                Bandung, 10 Oktober 2026<br>
                Ketua Peneliti Pengusul,<br><br><br><br>
                <strong><u>{{ $ketua_nama ?? 'Lintang Patria, S.Kom., M.Cs.' }}</u></strong><br>
                NIDN. {{ $ketua_nidn ?? '0412088901' }}
            </td>
        </tr>
    </table>

</body>
</html>


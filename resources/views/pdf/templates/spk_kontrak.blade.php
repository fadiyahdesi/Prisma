<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Perjanjian Penugasan Penelitian (SPK Kontrak) - PRISMA UHN</title>
    <style>
        @page {
            margin: 20mm 15mm 20mm 15mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            line-height: 1.38;
            color: #0f172a;
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
            margin-bottom: 14px;
        }
        .doc-title {
            text-align: center;
            font-size: 11.5pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin-bottom: 2px;
        }
        .doc-no {
            text-align: center;
            font-size: 9pt;
            color: #475569;
            margin-bottom: 15px;
        }
        p {
            text-align: justify;
            margin: 0 0 6px 0;
        }
        .ident-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
            margin: 4px 0 10px 10px;
        }
        .ident-table td {
            padding: 2px 4px;
            vertical-align: top;
        }
        .pasal-title {
            text-align: center;
            font-weight: bold;
            font-size: 9.5pt;
            margin-top: 10px;
            margin-bottom: 3px;
            text-transform: uppercase;
        }
        .pasal-sub {
            text-align: center;
            font-weight: bold;
            font-size: 9pt;
            color: #475569;
            margin-bottom: 4px;
        }
        .sig-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 22px;
        }
        .sig-table td {
            vertical-align: top;
            font-size: 9pt;
        }
        .materai-box {
            border: 1px dashed #94a3b8;
            background-color: #f8fafc;
            color: #64748b;
            font-size: 7pt;
            text-align: center;
            padding: 12px 4px;
            width: 80px;
            margin-bottom: 6px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <table class="kop-table">
        <tr>
            <td>
                <div class="kop-univ">UNIVERSITAS HARKAT NEGERI</div>
                <div class="kop-lembaga">LEMBAGA PENELITIAN DAN PENGABDIAN KEPADA MASYARAKAT (LPPM)</div>
                <div class="kop-sub">Gedung Rektorat Lt. 3, Jl. Jenderal Sudirman No. 108 &bull; Pos-el: lppm@uhn.ac.id</div>
            </td>
        </tr>
    </table>
    <div class="kop-line"></div>

    <div class="doc-title">SURAT PERJANJIAN PENUGASAN PELAKSANAAN PENELITIAN</div>
    <div class="doc-no">Nomor Kontrak: {{ $nomor_kontrak ?? '045/SPK-PENELITIAN/LPPM-UHN/III/2026' }}</div>

    <p>Pada hari ini, tanggal {{ $tanggal_kontrak ?? '10 Maret 2026' }}, telah dibuat perjanjian kerjasama pelaksanaan penelitian antara:</p>

    <table class="ident-table">
        <tr>
            <td style="width: 25%; font-weight: bold;">1. Nama</td>
            <td style="width: 3%;">:</td>
            <td style="width: 72%;"><strong>Prof. Dr. Ir. Hendra Gunawan, M.T.</strong></td>
        </tr>
        <tr>
            <td style="font-weight: bold;">&nbsp;&nbsp;&nbsp;Jabatan</td>
            <td>:</td>
            <td>Kepala Lembaga Penelitian dan Pengabdian kepada Masyarakat (LPPM) UHN</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">&nbsp;&nbsp;&nbsp;Kedudukan</td>
            <td>:</td>
            <td>Bertindak untuk dan atas nama Universitas Harkat Negeri, selanjutnya disebut sebagai <strong>PIHAK PERTAMA</strong>.</td>
        </tr>
        <tr>
            <td style="font-weight: bold; padding-top: 6px;">2. Nama</td>
            <td style="padding-top: 6px;">:</td>
            <td style="padding-top: 6px;"><strong>{{ $ketua_nama ?? 'Lintang Patria, S.Kom., M.Cs.' }}</strong></td>
        </tr>
        <tr>
            <td style="font-weight: bold;">&nbsp;&nbsp;&nbsp;NIDN</td>
            <td>:</td>
            <td>{{ $ketua_nidn ?? '0412088901' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">&nbsp;&nbsp;&nbsp;Kedudukan</td>
            <td>:</td>
            <td>Dosen Pengusul / Ketua Tim Peneliti, selanjutnya disebut sebagai <strong>PIHAK KEDUA</strong>.</td>
        </tr>
    </table>

    <p>Kedua belah pihak sepakat mengikatkan diri dalam Perjanjian Penugasan Pelaksanaan Penelitian dengan ketentuan pasal-pasal berikut:</p>

    <div class="pasal-title">Pasal 1: Ruang Lingkup Penugasan</div>
    <p>
        PIHAK PERTAMA menugaskan PIHAK KEDUA dan PIHAK KEDUA menerima penugasan untuk melaksanakan kegiatan penelitian Tahun Anggaran 2026 dengan judul: <strong>"{{ $judul_usulan ?? 'Penerapan Model Convolutional Neural Network (CNN) untuk Deteksi Dini Penyakit Daun Padi Berbasis Mobile App' }}"</strong>.
    </p>

    <div class="pasal-title">Pasal 2: Dana Penelitian dan Tata Cara Pembayaran</div>
    <p>
        Besarnya pagu dana penelitian yang disetujui adalah sebesar <strong>Rp {{ number_format($pagu ?? 25000000, 0, ',', '.') }}</strong>, dicairkan dalam 2 (dua) termin melalui transfer rekening Bank Mandiri Nomor {{ $nomor_rekening ?? '131-00-1928475-2' }} an. {{ $ketua_nama ?? 'Lintang Patria' }}:
    </p>
    <ol style="margin-top: 0; padding-left: 20px;">
        <li><strong>Termin I (70%):</strong> Sebesar Rp {{ number_format(($pagu ?? 25000000) * 0.7, 0, ',', '.') }} dibayarkan setelah penandatanganan SPK Kontrak ini.</li>
        <li><strong>Termin II (30%):</strong> Sebesar Rp {{ number_format(($pagu ?? 25000000) * 0.3, 0, ',', '.') }} dibayarkan setelah PIHAK KEDUA menyerahkan Laporan Kemajuan, Logbook, dan SPTB 70% yang disetujui Tim Reviewer.</li>
    </ol>

    <div class="pasal-title">Pasal 3: Hak Kekayaan Intelektual dan Luaran Wajib</div>
    <p>
        PIHAK KEDUA wajib mempublikasikan 1 (satu) artikel ilmiah pada jurnal terakreditasi SINTA 2 dan mendaftarkan 1 (satu) Hak Cipta atas nama institusi Universitas Harkat Negeri selambat-lambatnya pada akhir periode tahun anggaran 2026.
    </p>

    <div class="pasal-title">Pasal 4: Sanksi dan Penyelesaian Sengketa</div>
    <p>
        Apabila PIHAK KEDUA tidak dapat menyelesaikan kewajiban luaran tanpa alasan force majeure yang sah, maka PIHAK PERTAMA berhak menangguhkan hak pengajuan proposal penelitian berikutnya dan meminta pengembalian sisa dana penelitian ke kas universitas.
    </p>

    <table class="sig-table">
        <tr>
            <td style="width: 50%;">
                PIHAK PERTAMA,<br>
                <strong>Kepala LPPM Universitas Harkat Negeri</strong><br><br><br><br><br>
                <strong><u>Prof. Dr. Ir. Hendra Gunawan, M.T.</u></strong><br>
                NIP. 197008141995121001
            </td>
            <td style="width: 50%; padding-left: 20px;">
                Bandung, {{ $tanggal_kontrak ?? '10 Maret 2026' }}<br>
                PIHAK KEDUA,<br>
                <strong>Ketua Tim Peneliti</strong><br>
                <div class="materai-box">MATERAI TEMPEL<br>Rp 10.000</div>
                <strong><u>{{ $ketua_nama ?? 'Lintang Patria, S.Kom., M.Cs.' }}</u></strong><br>
                NIDN. {{ $ketua_nidn ?? '0412088901' }}
            </td>
        </tr>
    </table>

</body>
</html>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Pengalihan Hak Cipta - HKI PRISMA UHN</title>
    <style>
        @page {
            margin: 20mm 20mm 20mm 20mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10.5pt;
            line-height: 1.4;
            color: #0f172a;
        }
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .kop-table td {
            vertical-align: middle;
        }
        .kop-univ {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #681727;
            margin: 0;
            text-align: center;
        }
        .kop-lembaga {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #1e293b;
            margin: 2px 0;
            text-align: center;
        }
        .kop-sub {
            font-size: 8.5pt;
            color: #64748b;
            margin: 0;
            text-align: center;
        }
        .kop-line {
            border-top: 2px solid #681727;
            border-bottom: 1px solid #681727;
            height: 2px;
            margin-bottom: 16px;
        }
        .title-block {
            text-align: center;
            margin-bottom: 18px;
        }
        .doc-title {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin: 0;
        }
        p {
            text-align: justify;
            margin: 0 0 8px 0;
        }
        .ident-table {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0 12px 10px;
            font-size: 10pt;
        }
        .ident-table td {
            padding: 3px 5px;
            vertical-align: top;
        }
        .sig-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 24px;
        }
        .sig-table td {
            vertical-align: top;
        }
        .materai-box {
            border: 1px dashed #94a3b8;
            background-color: #f8fafc;
            color: #64748b;
            font-size: 7.5pt;
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 14px 6px;
            width: 85px;
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
                <div class="kop-sub">
                    Sentra Kekayaan Intelektual &bull; Gedung Rektorat Lt. 3, Jl. Jenderal Sudirman No. 108<br>
                    Pos-el: hki@uhn.ac.id &bull; Laman Resmi: https://prisma.uhn.ac.id
                </div>
            </td>
        </tr>
    </table>
    <div class="kop-line"></div>

    <div class="title-block">
        <div class="doc-title">SURAT PENGALIHAN HAK CIPTA</div>
    </div>

    <p>Pada hari ini, tanggal {{ $tanggal_surat ?? '15 Januari 2026' }}, kami yang bertanda tangan di bawah ini:</p>

    <table class="ident-table">
        <tr>
            <td style="width: 25%; font-weight: bold;">Nama Lengkap</td>
            <td style="width: 3%;">:</td>
            <td style="width: 72%;"><strong>{{ $nama_ketua ?? 'Lintang Patria, S.Kom., M.Cs.' }}</strong> (beserta seluruh anggota tim pencipta)</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">NIDN / NIP</td>
            <td>:</td>
            <td>{{ $nidn ?? '0412088901' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Alamat</td>
            <td>:</td>
            <td>Fakultas Sains dan Teknologi, Universitas Harkat Negeri</td>
        </tr>
    </table>

    <p>Selaku Pihak I (Pencipta), dengan ini mengalihkan seluruh hak ekonomi atas karya ciptaan kami kepada:</p>

    <table class="ident-table">
        <tr>
            <td style="width: 25%; font-weight: bold;">Nama Lembaga</td>
            <td style="width: 3%;">:</td>
            <td style="width: 72%;"><strong>UNIVERSITAS HARKAT NEGERI (UHN)</strong></td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Diwakili Oleh</td>
            <td>:</td>
            <td><strong>Prof. Dr. Ir. Hendra Gunawan, M.T.</strong> (Kepala LPPM UHN)</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Alamat</td>
            <td>:</td>
            <td>Jl. Jenderal Sudirman No. 108, Indonesia</td>
        </tr>
    </table>

    <p>Selaku Pihak II (Pemegang Hak Cipta), berupa karya ciptaan:</p>

    <table class="ident-table">
        <tr>
            <td style="width: 25%; font-weight: bold;">Jenis Ciptaan</td>
            <td style="width: 3%;">:</td>
            <td style="width: 72%;"><strong>{{ $jenis_ciptaan ?? 'Program Komputer' }}</strong></td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Judul Ciptaan</td>
            <td>:</td>
            <td style="font-weight: bold; color: #681727;">{{ $judul_hki ?? 'Sistem Informasi Manajemen Presensi Berbasis Pengenalan Wajah dan Geofencing (SIMPRES UHN)' }}</td>
        </tr>
    </table>

    <p>
        Ketentuan pengalihan ini meliputi penyerahan hak untuk memperbanyak, mendistribusikan, mengomersialisasikan, serta mendaftarkan ciptaan tersebut pada Direktorat Jenderal Kekayaan Intelektual Kementerian Hukum dan HAM Republik Indonesia atas nama <strong>UNIVERSITAS HARKAT NEGERI</strong>, dengan tetap mencantumkan Pihak I sebagai Pencipta/Inventor yang berhak atas pengakuan hak moral dan insentif royalti sesuai Statuta dan Peraturan Rektor UHN.
    </p>
    <p>
        Surat pengalihan hak ini dibuat rangkap dua bermaterai cukup dan memiliki kekuatan hukum yang sama bagi kedua belah pihak.
    </p>

    <table class="sig-table">
        <tr>
            <td style="width: 50%;">
                Pihak II (Penerima Hak),<br>
                <strong>Kepala LPPM UHN</strong><br><br><br><br><br>
                <strong><u>Prof. Dr. Ir. Hendra Gunawan, M.T.</u></strong><br>
                NIP. 197008141995121001
            </td>
            <td style="width: 50%; padding-left: 20px;">
                Pihak I (Pencipta),<br>
                <strong>Ketua Tim Pencipta</strong><br>
                <div class="materai-box">MATERAI TEMPEL<br>Rp 10.000</div>
                <strong><u>{{ $nama_ketua ?? 'Lintang Patria, S.Kom., M.Cs.' }}</u></strong><br>
                NIDN. {{ $nidn ?? '0412088901' }}
            </td>
        </tr>
    </table>

</body>
</html>


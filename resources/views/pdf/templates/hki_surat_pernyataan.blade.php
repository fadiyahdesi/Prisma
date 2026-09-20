<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Pernyataan Keaslian Ciptaan - HKI</title>
    <style>
        @page {
            margin: 20mm 20mm 20mm 20mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.45;
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
            margin-bottom: 18px;
        }
        .title-block {
            text-align: center;
            margin-bottom: 22px;
        }
        .doc-title {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin: 0;
        }
        .doc-no {
            font-size: 9.5pt;
            color: #475569;
            margin-top: 3px;
        }
        p {
            text-align: justify;
            margin: 0 0 10px 0;
        }
        .ident-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0 16px 15px;
            font-size: 10.5pt;
        }
        .ident-table td {
            padding: 4px 6px;
            vertical-align: top;
        }
        .sig-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        .materai-box {
            border: 1px dashed #94a3b8;
            background-color: #f8fafc;
            color: #64748b;
            font-size: 8pt;
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 18px 8px;
            width: 90px;
            margin-bottom: 8px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <!-- KOP SURAT UHN -->
    <table class="kop-table">
        <tr>
            <td>
                <div class="kop-univ">UNIVERSITAS HARKAT NEGERI</div>
                <div class="kop-lembaga">LEMBAGA PENELITIAN DAN PENGABDIAN KEPADA MASYARAKAT (LPPM)</div>
                <div class="kop-sub">
                    Sentra Kekayaan Intelektual &bull; Gedung Rektorat Lt. 3, Jl. Jenderal Sudirman No. 108<br>
                    Pos-el: lppm@uhn.ac.id &bull; Laman Resmi: https://prisma.uhn.ac.id
                </div>
            </td>
        </tr>
    </table>
    <div class="kop-line"></div>

    <div class="title-block">
        <div class="doc-title">SURAT PERNYATAAN KEASLIAN KARYA CIPTAAN</div>
        <div class="doc-no">Nomor: {{ $nomor_surat ?? '412/LPPM-UHN/HKI/2026' }}</div>
    </div>

    <p>Yang bertanda tangan di bawah ini:</p>

    <table class="ident-table">
        <tr>
            <td style="width: 25%; font-weight: bold;">Nama Lengkap</td>
            <td style="width: 3%;">:</td>
            <td style="width: 72%;"><strong>{{ $nama_ketua ?? 'Lintang Patria, S.Kom., M.Cs.' }}</strong></td>
        </tr>
        <tr>
            <td style="font-weight: bold;">NIDN / NIP</td>
            <td>:</td>
            <td>{{ $nidn ?? '0412088901' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Jabatan / Unit Kerja</td>
            <td>:</td>
            <td>Dosen Tetap Fakultas Sains dan Teknologi, Universitas Harkat Negeri</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Alamat Domisili</td>
            <td>:</td>
            <td>Jl. Terusan Buah Batu No. 45, Bandung, Jawa Barat</td>
        </tr>
    </table>

    <p>
        Bertindak atas nama seluruh tim pencipta/inventor, dengan ini menyatakan dengan sesungguhnya dan penuh tanggung jawab bahwa ciptaan berupa:
    </p>

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
        <tr>
            <td style="font-weight: bold;">Nomor Permohonan</td>
            <td>:</td>
            <td>{{ $nomor_permohonan ?? 'EC00202618902' }}</td>
        </tr>
    </table>

    <p>
        Adalah <strong>benar-benar murni hasil gagasan, penelitian, dan karya cipta asli</strong> kami dan tidak menjiplak atau mengambil karya cipta orang lain secara melawan hukum (bebas plagiarisme), serta tidak sedang berada dalam sengketa kepemilikan maupun sengketa hukum dengan pihak manapun.
    </p>
    <p>
        Apabila di kemudian hari terbukti bahwa pernyataan ini tidak benar atau melanggar hak cipta pihak lain sebagaimana diatur dalam Undang-Undang Republik Indonesia Nomor 28 Tahun 2014 tentang Hak Cipta, maka kami bersedia bertanggung jawab penuh secara hukum perdata maupun pidana tanpa melibatkan pihak Universitas Harkat Negeri.
    </p>
    <p>
        Demikian surat pernyataan ini kami buat dengan sadar, sukarela, dan tanpa paksaan dari pihak mana pun untuk dapat dipergunakan sebagaimana mestinya.
    </p>

    <table class="sig-table">
        <tr>
            <td style="width: 50%;">
                Mengetahui,<br>
                <strong>Kepala LPPM Universitas Harkat Negeri</strong><br><br><br><br><br>
                <strong><u>Prof. Dr. Ir. Hendra Gunawan, M.T.</u></strong><br>
                NIP. 197008141995121001
            </td>
            <td style="width: 50%; text-align: left; padding-left: 20px;">
                Bandung, {{ $tanggal_surat ?? '15 Januari 2026' }}<br>
                Yang Menyatakan,<br>
                <div class="materai-box">MATERAI TEMPEL<br>Rp 10.000</div>
                <strong><u>{{ $nama_ketua ?? 'Lintang Patria, S.Kom., M.Cs.' }}</u></strong><br>
                NIDN. {{ $nidn ?? '0412088901' }}
            </td>
        </tr>
    </table>

</body>
</html>


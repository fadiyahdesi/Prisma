<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Kesediaan Kerjasama Mitra - PRISMA UHN</title>
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
        .kop-mitra {
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }
        .mitra-company {
            font-size: 14pt;
            font-weight: bold;
            color: #1e3a8a;
            text-transform: uppercase;
        }
        .mitra-sub {
            font-size: 8.5pt;
            color: #475569;
        }
        .title-block {
            text-align: center;
            margin-bottom: 20px;
        }
        .doc-title {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
        }
        p {
            text-align: justify;
            margin: 0 0 10px 0;
        }
        .ident-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0 15px 15px;
            font-size: 10.5pt;
        }
        .ident-table td {
            padding: 3px 5px;
            vertical-align: top;
        }
        .sig-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 35px;
        }
        .materai-box {
            border: 1px dashed #94a3b8;
            background-color: #f8fafc;
            color: #64748b;
            font-size: 8pt;
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 16px 6px;
            width: 90px;
            margin-bottom: 6px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="kop-mitra">
        <div class="mitra-company">{{ $nama_mitra ?? 'KOPERASI PRODUSEN KOPI LOKAL JAVA PREANGER' }}</div>
        <div class="mitra-sub">
            Badan Hukum No: AHU-0012948.AH.01.26 &bull; Sentra Komoditas Perkebunan Berkelanjutan<br>
            Jl. Raya Ciwidey - Patengan KM 12, Kabupaten Bandung &bull; Kontak: kemitraan@javapreanger.co.id
        </div>
    </div>

    <div class="title-block">
        <div class="doc-title">SURAT PERNYATAAN KESEDIAAN KERJASAMA MITRA</div>
        <div style="font-size: 9.5pt; color: #475569; margin-top: 2px;">Nomor: 088/SPK-MITRA/II/2026</div>
    </div>

    <p>Yang bertanda tangan di bawah ini:</p>

    <table class="ident-table">
        <tr>
            <td style="width: 25%; font-weight: bold;">Nama Lengkap</td>
            <td style="width: 3%;">:</td>
            <td style="width: 72%;"><strong>{{ $pimpinan_mitra ?? 'H. Rahmat Hidayat, S.P.' }}</strong></td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Jabatan</td>
            <td>:</td>
            <td>Ketua Pengurus Koperasi Produsen Kopi Java Preanger</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Instansi / Mitra</td>
            <td>:</td>
            <td>{{ $nama_mitra ?? 'Koperasi Produsen Kopi Lokal Java Preanger' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Alamat</td>
            <td>:</td>
            <td>Kawasan Agrowisata Ciwidey, Kabupaten Bandung</td>
        </tr>
    </table>

    <p>Dengan ini menyatakan bersedia dan berkomitmen penuh untuk menjadi Mitra Kerjasama dalam pelaksanaan riset dan hilirisasi inovasi berjudul:</p>

    <div style="background-color: #f8fafc; border-left: 4px solid #681727; padding: 10px 14px; margin: 10px 0 15px 0; font-weight: bold; font-size: 10.5pt; color: #0f172a;">
        "{{ $judul_usulan ?? 'Pemberdayaan UMKM Kopi Lokal Melalui Digitalisasi Pemasaran E-Commerce dan Pengolahan Limbah Kulit Kopi menjadi Bio-Pelet' }}"
    </div>

    <p>Yang diusulkan oleh tim peneliti dari Universitas Harkat Negeri:</p>

    <table class="ident-table">
        <tr>
            <td style="width: 25%; font-weight: bold;">Ketua Pengusul</td>
            <td style="width: 3%;">:</td>
            <td style="width: 72%;"><strong>{{ $ketua_nama ?? 'Lintang Patria, S.Kom., M.Cs.' }}</strong></td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Institusi</td>
            <td>:</td>
            <td>Universitas Harkat Negeri (LPPM UHN)</td>
        </tr>
    </table>

    <p>Bentuk partisipasi dan kontribusi yang disepakati oleh pihak mitra meliputi:</p>
    <ol style="margin-top: 0; padding-left: 20px;">
        <li>Penyediaan lokasi uji coba lapangan, fasilitas workshop, dan akses pengolahan limbah kopi.</li>
        <li>Keterlibatan 20 anggota kelompok tani kopi dalam pendampingan teknis dan uji coba prototipe bio-pelet.</li>
        <li>Dukungan in-kind berupa bahan baku kulit kopi basah sebanyak 500 kg untuk proses riset konversi energi terbarukan.</li>
    </ol>

    <p>Demikian surat pernyataan kesediaan kerjasama ini kami buat dengan penuh tanggung jawab demi keberhasilan program pengabdian dan hilirisasi riset untuk kesejahteraan masyarakat.</p>

    <table class="sig-table">
        <tr>
            <td style="width: 50%;"></td>
            <td style="width: 50%; text-align: left;">
                Bandung, 12 Februari 2026<br>
                Yang Menyatakan,<br>
                <strong>Ketua Koperasi / Pimpinan Mitra</strong><br>
                <div class="materai-box">MATERAI TEMPEL<br>Rp 10.000</div>
                <strong><u>{{ $pimpinan_mitra ?? 'H. Rahmat Hidayat, S.P.' }}</u></strong><br>
                NIP/NIK: 3204128801940003
            </td>
        </tr>
    </table>

</body>
</html>


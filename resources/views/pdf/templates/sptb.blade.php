<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Pernyataan Tanggung Jawab Belanja (SPTB) - PRISMA UHN</title>
    <style>
        @page {
            margin: 20mm 15mm 20mm 15mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 9.5pt;
            line-height: 1.4;
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
            margin-bottom: 15px;
        }
        .doc-title {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .doc-no {
            text-align: center;
            font-size: 9pt;
            color: #475569;
            margin-bottom: 15px;
        }
        .ident-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-bottom: 12px;
        }
        .ident-table td {
            padding: 3px 5px;
            vertical-align: top;
        }
        p {
            text-align: justify;
            margin: 0 0 6px 0;
        }
        .belanja-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin: 10px 0 14px 0;
        }
        .belanja-table th, .belanja-table td {
            border: 1px solid #cbd5e1;
            padding: 5px 6px;
        }
        .belanja-table th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: center;
        }
        .sig-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }
        .materai-box {
            border: 1px dashed #94a3b8;
            background-color: #f8fafc;
            color: #64748b;
            font-size: 7.5pt;
            text-align: center;
            padding: 14px 4px;
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
                <div class="kop-sub">Subbagian Keuangan dan Akuntabilitas Anggaran Hibah Riset</div>
            </td>
        </tr>
    </table>
    <div class="kop-line"></div>

    <div class="doc-title">SURAT PERNYATAAN TANGGUNG JAWAB BELANJA (SPTB {{ $persen ?? '70' }}%)</div>
    <div class="doc-no">Nomor: {{ $nomor_sptb ?? 'SPTB-70/LPPM-UHN/2026' }}</div>

    <p>Yang bertanda tangan di bawah ini:</p>

    <table class="ident-table">
        <tr>
            <td style="width: 25%; font-weight: bold;">Nama Lengkap</td>
            <td style="width: 3%;">:</td>
            <td style="width: 72%;"><strong>{{ $ketua_nama ?? 'Lintang Patria, S.Kom., M.Cs.' }}</strong></td>
        </tr>
        <tr>
            <td style="font-weight: bold;">NIDN / NIP</td>
            <td>:</td>
            <td>{{ $ketua_nidn ?? '0412088901' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Jabatan</td>
            <td>:</td>
            <td>Ketua Peneliti / Pengusul Hibah</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Judul Kegiatan</td>
            <td>:</td>
            <td style="font-weight: bold; color: #681727;">{{ $judul_usulan ?? 'Penerapan Model Convolutional Neural Network (CNN) untuk Deteksi Dini Penyakit Daun Padi Berbasis Mobile App' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Nomor Kontrak SPK</td>
            <td>:</td>
            <td>045/SPK-PENELITIAN/LPPM-UHN/III/2026</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Pagu Termin {{ $persen ?? '70' }}%</td>
            <td>:</td>
            <td style="font-weight: bold; color: #047857;">Rp {{ number_format($nominal_termin ?? 17500000, 0, ',', '.') }}</td>
        </tr>
    </table>

    <p>
        Dengan ini menyatakan bahwa saya bertanggung jawab penuh secara formal dan material atas penggunaan belanja dana hibah penelitian Termin {{ $persen ?? '70' }}% dengan rekapitulasi bukti pengeluaran sah sebagai berikut:
    </p>

    <table class="belanja-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 35%;">Uraian Pembelian / Transaksi Belanja</th>
                <th style="width: 20%;">Nomor Kuitansi / Bukti</th>
                <th style="width: 20%;">Penerima / Toko</th>
                <th style="width: 20%;">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center;">1</td>
                <td>Komponen Sensor ESP32, Modul Kamera &amp; Board IoT</td>
                <td>KWT-01/ELK/26</td>
                <td>CV Mitra Elektronika</td>
                <td style="text-align: right;">6.250.000</td>
            </tr>
            <tr>
                <td style="text-align: center;">2</td>
                <td>Langganan GPU Cloud Computing Training (2 Bulan)</td>
                <td>INV-AWS-8921</td>
                <td>Cloud Compute Provider</td>
                <td style="text-align: right;">4.000.000</td>
            </tr>
            <tr>
                <td style="text-align: center;">3</td>
                <td>Transportasi &amp; Akomodasi Uji Lapangan Mitra</td>
                <td>SPD-04/LPPM/26</td>
                <td>Tim Peneliti UHN</td>
                <td style="text-align: right;">3.750.000</td>
            </tr>
            <tr>
                <td style="text-align: center;">4</td>
                <td>ATK, Cetak Logbook &amp; Dokumentasi Riset</td>
                <td>NOTA-7782</td>
                <td>Toko ATK Graha Media</td>
                <td style="text-align: right;">3.500.000</td>
            </tr>
            <tr style="background-color: #f8fafc; font-weight: bold;">
                <td colspan="4" style="text-align: right;">TOTAL REALISASI BELANJA (Rp):</td>
                <td style="text-align: right; color: #047857;">Rp {{ number_format($nominal_termin ?? 17500000, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <p>
        Bukti-bukti pengeluaran tersebut di atas disimpan lengkap oleh Ketua Peneliti sesuai ketentuan peraturan perundang-undangan dan siap untuk diaudit sewaktu-waktu oleh Inspektorat, BPK, maupun Satuan Pengawas Internal (SPI) Universitas Harkat Negeri.
    </p>

    <table class="sig-table">
        <tr>
            <td style="width: 50%;">
                Mengetahui &amp; Telah Diverifikasi,<br>
                <strong>Bagian Keuangan LPPM UHN</strong><br><br><br><br><br>
                <strong><u>Ratna Dewi, S.E., Ak., M.Ak.</u></strong><br>
                NIP. 198503122008122001
            </td>
            <td style="width: 50%; padding-left: 20px;">
                Bandung, {{ $tanggal_sptb ?? '15 Juli 2026' }}<br>
                Ketua Peneliti,<br>
                <div class="materai-box">MATERAI TEMPEL<br>Rp 10.000</div>
                <strong><u>{{ $ketua_nama ?? 'Lintang Patria, S.Kom., M.Cs.' }}</u></strong><br>
                NIDN. {{ $ketua_nidn ?? '0412088901' }}
            </td>
        </tr>
    </table>

</body>
</html>


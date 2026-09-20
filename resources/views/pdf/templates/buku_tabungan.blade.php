<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Salinan Buku Tabungan / Rekening Koran - Keuangan UHN</title>
    <style>
        @page {
            margin: 20mm 15mm 20mm 15mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.35;
            color: #0f172a;
        }
        .bank-header {
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 8px;
            margin-bottom: 14px;
        }
        .bank-name {
            font-size: 14pt;
            font-weight: bold;
            color: #1e3a8a;
        }
        .bank-branch {
            font-size: 8.5pt;
            color: #475569;
        }
        .doc-title {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 12px 0;
            color: #1e293b;
        }
        .acc-info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
        }
        .acc-info-table td {
            padding: 6px 10px;
            font-size: 8.5pt;
        }
        .statement-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            margin-bottom: 20px;
        }
        .statement-table th, .statement-table td {
            border: 1px solid #cbd5e1;
            padding: 5px 6px;
        }
        .statement-table th {
            background-color: #f1f5f9;
            font-weight: bold;
            color: #1e293b;
            text-align: center;
        }
        .stamp-box {
            display: inline-block;
            border: 2px solid #1e3a8a;
            color: #1e3a8a;
            padding: 8px 14px;
            font-weight: bold;
            font-size: 8.5pt;
            text-transform: uppercase;
            text-align: center;
            border-radius: 6px;
            margin-top: 15px;
        }
    </style>
</head>
<body>

    <div class="bank-header">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td>
                    <div class="bank-name">BANK MANDIRI (PERSERO) Tbk.</div>
                    <div class="bank-branch">Kantor Cabang Pembantu Universitas Harkat Negeri &bull; KC Bandung Diponegoro</div>
                </td>
                <td style="text-align: right; vertical-align: top;">
                    <div style="font-weight: bold; color: #1e3a8a; font-size: 10pt;">KORESPONDENSI REKENING RESMI</div>
                    <div style="font-size: 7.5pt; color: #64748b;">Dicetak otomatis pada sistem PRISMA UHN</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="doc-title">BUKU TABUNGAN &amp; LEMBAR MUTASI REKENING AKTIF</div>

    <table class="acc-info-table">
        <tr>
            <td style="width: 22%; font-weight: bold;">Nama Pemilik</td>
            <td style="width: 2%;">:</td>
            <td style="width: 36%;"><strong>{{ $nama_pemilik ?? 'Lintang Patria' }}</strong></td>
            <td style="width: 18%; font-weight: bold;">Mata Uang</td>
            <td style="width: 2%;">:</td>
            <td style="width: 20%;">IDR (Rupiah)</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Nomor Rekening</td>
            <td>:</td>
            <td><strong style="font-family: monospace; font-size: 9.5pt; color: #1e3a8a;">{{ $nomor_rekening ?? '131-00-1928475-2' }}</strong></td>
            <td style="font-weight: bold;">Status Akun</td>
            <td>:</td>
            <td><span style="color: #047857; font-weight: bold;">AKTIF / VALID</span></td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Instansi Terdaftar</td>
            <td>:</td>
            <td>Universitas Harkat Negeri</td>
            <td style="font-weight: bold;">Tanggal Buka</td>
            <td>:</td>
            <td>14 Agustus 2021</td>
        </tr>
    </table>

    <table class="statement-table">
        <thead>
            <tr>
                <th style="width: 12%;">Tanggal</th>
                <th style="width: 48%;">Deskripsi Transaksi</th>
                <th style="width: 13%;">Debet (Rp)</th>
                <th style="width: 13%;">Kredit (Rp)</th>
                <th style="width: 14%;">Saldo (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center;">01/01/2026</td>
                <td>SALDO AWAL TAHUN BUKU</td>
                <td style="text-align: right;">-</td>
                <td style="text-align: right;">-</td>
                <td style="text-align: right;">12.450.000</td>
            </tr>
            <tr>
                <td style="text-align: center;">25/01/2026</td>
                <td>PAYROLL GAJI DAN TUNJANGAN DOSEN UHN</td>
                <td style="text-align: right;">-</td>
                <td style="text-align: right;">8.750.000</td>
                <td style="text-align: right;">21.200.000</td>
            </tr>
            <tr>
                <td style="text-align: center;">18/03/2026</td>
                <td>TRANSFER MASUK: LPPM UHN HIBAH PENELITIAN TERMIN 1 (70%)</td>
                <td style="text-align: right;">-</td>
                <td style="text-align: right; font-weight: bold; color: #047857;">17.500.000</td>
                <td style="text-align: right; font-weight: bold;">38.700.000</td>
            </tr>
            <tr>
                <td style="text-align: center;">22/03/2026</td>
                <td>PENARIKAN OPERASIONAL RISET &amp; BELANJA SENSOR</td>
                <td style="text-align: right; color: #b91c1c;">6.250.000</td>
                <td style="text-align: right;">-</td>
                <td style="text-align: right;">32.450.000</td>
            </tr>
        </tbody>
    </table>

    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="width: 50%;">
                <div class="stamp-box">
                    TERVERIFIKASI SISTEM BANK MANDIRI<br>
                    VALID FOR PRISMA UHN PAYOUTS
                </div>
            </td>
            <td style="width: 50%; text-align: right; font-size: 8pt; color: #64748b;">
                Customer Service Officer &bull; Bank Mandiri KCP UHN<br>
                Dokumen ini merupakan salinan digital resmi untuk kelengkapan administrasi pencairan dana hibah.
            </td>
        </tr>
    </table>

</body>
</html>


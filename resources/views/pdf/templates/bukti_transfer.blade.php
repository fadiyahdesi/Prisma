<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bukti Transfer / Pemindahbukuan - Keuangan PRISMA UHN</title>
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
        .bank-box {
            border: 2px solid #0284c7;
            border-radius: 8px;
            padding: 16px 20px;
            background-color: #f0f9ff;
            margin-bottom: 20px;
        }
        .bank-title {
            font-size: 13pt;
            font-weight: bold;
            color: #0369a1;
        }
        .bank-subtitle {
            font-size: 8.5pt;
            color: #475569;
        }
        .receipt-title {
            text-align: center;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 4px;
            color: #0f172a;
        }
        .receipt-no {
            text-align: center;
            font-size: 9pt;
            font-family: monospace;
            color: #475569;
            margin-bottom: 18px;
        }
        .status-box {
            background-color: #ecfdf5;
            border: 1px solid #6ee7b7;
            color: #047857;
            font-weight: bold;
            text-align: center;
            padding: 8px;
            border-radius: 6px;
            font-size: 10pt;
            margin-bottom: 18px;
        }
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-bottom: 20px;
        }
        .detail-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }
        .nominal-box {
            background-color: #f8fafc;
            border: 2px dashed #0284c7;
            padding: 12px;
            text-align: center;
            margin: 15px 0 20px 0;
            border-radius: 6px;
        }
        .nominal-label {
            font-size: 8.5pt;
            text-transform: uppercase;
            font-weight: bold;
            color: #475569;
        }
        .nominal-val {
            font-size: 16pt;
            font-weight: bold;
            color: #0369a1;
        }
        .nominal-terbilang {
            font-size: 8.5pt;
            font-style: italic;
            color: #64748b;
            margin-top: 3px;
        }
        .auth-note {
            font-size: 8pt;
            color: #64748b;
            text-align: center;
            margin-top: 30px;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    <div class="bank-box">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td>
                    <div class="bank-title">BANK MANDIRI (PERSERO) Tbk.</div>
                    <div class="bank-subtitle">Corporate Cash Management (MCM 2.0) System</div>
                </td>
                <td style="text-align: right;">
                    <div style="font-size: 8pt; color: #0369a1; font-weight: bold;">RESMI &amp; TERVERIFIKASI</div>
                    <div style="font-size: 7.5pt; color: #64748b;">Tanggal Cetak: {{ $tanggal_transfer ?? '18 Maret 2026' }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="receipt-title">STRUK BUKTI PEMINDAHBUKUAN / TRANSFER ELEKTRONIK</div>
    <div class="receipt-no">Nomor Referensi Transaksi: {{ $nomor_referensi ?? 'TRX/UHN/20260318/98821' }}</div>

    <div class="status-box">
        &check; STATUS TRANSAKSI: BERHASIL (SUCCESS / EXECUTED)
    </div>

    <div class="nominal-box">
        <div class="nominal-label">Jumlah Dana yang Ditransfer</div>
        <div class="nominal-val">Rp {{ number_format($nominal ?? 17500000, 0, ',', '.') }}</div>
        <div class="nominal-terbilang"># {{ $terbilang ?? 'Tujuh Belas Juta Lima Ratus Ribu Rupiah' }} #</div>
    </div>

    <table class="detail-table">
        <tr>
            <td style="width: 25%; font-weight: bold; color: #475569;">Rekening Pengirim (Debet)</td>
            <td style="width: 3%;">:</td>
            <td style="width: 72%;"><strong>131-00-9988776-1</strong> an. LPPM UNIVERSITAS HARKAT NEGERI</td>
        </tr>
        <tr>
            <td style="font-weight: bold; color: #475569;">Rekening Penerima (Kredit)</td>
            <td>:</td>
            <td><strong>{{ $nomor_rekening_tujuan ?? '131-00-1928475-2' }}</strong> an. <strong>{{ $nama_penerima ?? 'Lintang Patria' }}</strong></td>
        </tr>
        <tr>
            <td style="font-weight: bold; color: #475569;">Bank Tujuan</td>
            <td>:</td>
            <td>{{ $nama_bank ?? 'Bank Mandiri' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; color: #475569;">Tanggal &amp; Waktu Valuta</td>
            <td>:</td>
            <td>{{ $tanggal_transfer ?? '18 Maret 2026' }} - 10:45:12 WIB</td>
        </tr>
        <tr>
            <td style="font-weight: bold; color: #475569;">Berita Acara / Keterangan</td>
            <td>:</td>
            <td style="color: #681727; font-weight: bold;">{{ $keterangan ?? 'Pencairan Dana Hibah Riset PRISMA UHN 2026 Termin 1 (70%)' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; color: #475569;">Diotorisasi Oleh</td>
            <td>:</td>
            <td>Bendahara Pengeluaran Hibah LPPM UHN (Ratna Dewi, S.E., Ak.)</td>
        </tr>
    </table>

    <div class="auth-note">
        Struk elektronik ini diterbitkan secara otomatis oleh sistem host-to-host perbankan dan merupakan alat bukti pembayaran yang sah sesuai dengan ketentuan hukum perbankan yang berlaku di Republik Indonesia.
    </div>

</body>
</html>


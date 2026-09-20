<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Kesepakatan Pembagian Reward Insentif - PRISMA UHN</title>
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
        .dist-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin: 12px 0 18px 0;
        }
        .dist-table th, .dist-table td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
        }
        .dist-table th {
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
            padding: 12px 4px;
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
                <div class="kop-sub">Program Insentif Publikasi Ilmiah &amp; Kekayaan Intelektual Terpadu</div>
            </td>
        </tr>
    </table>
    <div class="kop-line"></div>

    <div class="doc-title">SURAT PERNYATAAN KESEPAKATAN PEMBAGIAN INSENTIF REWARD</div>
    <div class="doc-no">Nomor Pengajuan: {{ $nomor_klaim ?? 'REW/2026/09/DEMO1' }}</div>

    <p>
        Sehubungan dengan pengajuan klaim insentif reward publikasi ilmiah / kekayaan intelektual (HKI) atas karya yang telah terbit/tercatat resmi:
    </p>

    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 10px 14px; margin: 10px 0; border-radius: 6px;">
        <table style="width: 100%; border-collapse: collapse; font-size: 9pt;">
            <tr>
                <td style="width: 25%; font-weight: bold; color: #475569;">Jenis Karya / Aset</td>
                <td style="width: 3%;">:</td>
                <td style="width: 72%;"><strong>{{ $jenis_karya ?? 'Publikasi Jurnal Terakreditasi SINTA 2' }}</strong></td>
            </tr>
            <tr>
                <td style="font-weight: bold; color: #475569; padding-top: 4px;">Judul Karya</td>
                <td style="padding-top: 4px;">:</td>
                <td style="font-weight: bold; color: #681727; padding-top: 4px;">{{ $judul_karya ?? 'Autonomous Edge-AI Orchestration for Decentralized IoT Networks' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; color: #475569; padding-top: 4px;">Nomor SK Tarif Rektor</td>
                <td style="padding-top: 4px;">:</td>
                <td style="padding-top: 4px;">SK Rektor UHN No. 082/SK-REK/I/2026 tentang Standar Tarif Insentif</td>
            </tr>
            <tr>
                <td style="font-weight: bold; color: #475569; padding-top: 4px;">Total Nominal Reward</td>
                <td style="padding-top: 4px;">:</td>
                <td style="font-weight: bold; color: #047857; padding-top: 4px;">Rp {{ number_format($total_reward ?? 5000000, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <p>
        Kami seluruh penulis/inventor secara sadar dan bermusyawarah menyatakan telah bersepakat mengenai pembagian persentase proporsi insentif reward sebagai berikut:
    </p>

    <table class="dist-table">
        <thead>
            <tr>
                <th style="width: 6%;">No</th>
                <th style="width: 34%;">Nama Anggota / Penulis</th>
                <th style="width: 20%;">Peran Penulis</th>
                <th style="width: 15%;">Persentase</th>
                <th style="width: 25%;">Nominal Bagian (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center;">1</td>
                <td><strong>{{ $penulis_1 ?? 'Lintang Patria' }}</strong></td>
                <td>Penulis Pertama &amp; Korespondensi</td>
                <td style="text-align: center; font-weight: bold;">60%</td>
                <td style="text-align: right; font-weight: bold;">Rp {{ number_format(($total_reward ?? 5000000) * 0.6, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="text-align: center;">2</td>
                <td><strong>{{ $penulis_2 ?? 'Dr. Budi Wicaksono, M.Kom.' }}</strong></td>
                <td>Penulis Anggota (Co-Author)</td>
                <td style="text-align: center; font-weight: bold;">40%</td>
                <td style="text-align: right; font-weight: bold;">Rp {{ number_format(($total_reward ?? 5000000) * 0.4, 0, ',', '.') }}</td>
            </tr>
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <td colspan="3" style="text-align: right;">TOTAL:</td>
                <td style="text-align: center;">100%</td>
                <td style="text-align: right; color: #047857;">Rp {{ number_format($total_reward ?? 5000000, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <p>
        Dengan ditandatanganinya kesepakatan ini, kami memberikan kuasa kepada Bagian Keuangan LPPM UHN untuk mentransfer dana insentif secara langsung ke rekening masing-masing anggota.
    </p>

    <table class="sig-table">
        <tr>
            <td style="width: 50%;">
                Penulis Anggota,<br><br><br><br><br>
                <strong><u>{{ $penulis_2 ?? 'Dr. Budi Wicaksono, M.Kom.' }}</u></strong><br>
                NIDN: 0418048201
            </td>
            <td style="width: 50%; padding-left: 20px;">
                Bandung, 20 Maret 2026<br>
                Penulis Pertama / Pengusul,<br>
                <div class="materai-box">MATERAI TEMPEL<br>Rp 10.000</div>
                <strong><u>{{ $penulis_1 ?? 'Lintang Patria' }}</u></strong><br>
                NIDN: 0412088901
            </td>
        </tr>
    </table>

</body>
</html>


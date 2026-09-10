<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tanda Bukti Pelunasan Hibah - {{ $kontrak->nomor_kontrak }}</title>
    <style>
        @page {
            margin: 2cm 2cm 2cm 2cm;
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9.5pt;
            color: #0f172a;
        }
        body {
            line-height: 1.4;
        }
        .header-table {
            width: 100%;
            border-bottom: 2.5px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 16px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .univ-title {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #0f172a;
        }
        .lppm-title {
            font-size: 11pt;
            font-weight: bold;
            color: #047857;
        }
        .univ-address {
            font-size: 8pt;
            color: #475569;
        }
        .doc-title {
            text-align: center;
            margin-top: 12px;
            margin-bottom: 18px;
        }
        .doc-title h2 {
            font-size: 12pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #065f46;
        }
        .doc-title p {
            font-size: 9pt;
            color: #475569;
            margin: 4px 0 0 0;
        }
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            font-size: 9pt;
        }
        .content-table td {
            padding: 3px 2px;
            vertical-align: top;
        }
        .content-table .label {
            width: 32%;
            font-weight: bold;
            color: #334155;
        }
        .content-table .sep {
            width: 2%;
        }
        .content-table .val {
            width: 66%;
        }
        .breakdown-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-top: 10px;
            margin-bottom: 18px;
        }
        .breakdown-table th {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            font-weight: bold;
            text-align: left;
            text-transform: uppercase;
            font-size: 8pt;
            color: #334155;
        }
        .breakdown-table td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            vertical-align: middle;
        }
        .qr-section {
            width: 100%;
            border: 1px dashed #059669;
            background-color: #ecfdf5;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 6px;
        }
        .qr-section td {
            vertical-align: middle;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: avoid;
            margin-top: 15px;
        }
        .signature-table td {
            width: 50%;
            vertical-align: top;
            font-size: 9pt;
        }
    </style>
</head>
<body>

    <!-- Header Kop Surat -->
    <table class="header-table">
        <tr>
            <td style="width: 75px; text-align: center;">
                <div style="width: 55px; height: 55px; border-radius: 50%; background-color: #059669; color: #ffffff; text-align: center; line-height: 55px; font-weight: bold; font-size: 20pt; margin: 0 auto;">
                    P
                </div>
            </td>
            <td>
                <div class="univ-title">UNIVERSITAS HARKAT NEGERI</div>
                <div class="lppm-title">LEMBAGA PENELITIAN DAN PENGABDIAN KEPADA MASYARAKAT (LPPM) &bull; DIVISI KEUANGAN</div>
                <div class="univ-address">
                    Jl. Harkat Pendidikan No. 10, Kampus Terpadu &bull; Website: lppm.harkatnegeri.ac.id &bull; Surel: keuangan.p3m@harkatnegeri.ac.id
                </div>
            </td>
        </tr>
    </table>

    <!-- Judul Dokumen -->
    <div class="doc-title">
        <h2>TANDA BUKTI PELUNASAN DANA HIBAH (100%)</h2>
        <p>Nomor Surat Perjanjian Kontrak (SPK): <strong>{{ $kontrak->nomor_kontrak }}</strong></p>
    </div>

    <!-- Informasi Hibah -->
    <table class="content-table">
        <tr>
            <td class="label">Judul Usulan</td>
            <td class="sep">:</td>
            <td class="val"><strong>{{ $kontrak->usulan->judul_usulan }}</strong></td>
        </tr>
        <tr>
            <td class="label">Kode Usulan BIMA</td>
            <td class="sep">:</td>
            <td class="val">{{ $kontrak->usulan->kode_usulan }}</td>
        </tr>
        <tr>
            <td class="label">Skema & Tahun Anggaran</td>
            <td class="sep">:</td>
            <td class="val">{{ $kontrak->usulan->skema->nama_skema ?? '-' }} &bull; Tahun Anggaran {{ $kontrak->usulan->periode->tahun_anggaran ?? date('Y') }}</td>
        </tr>
        <tr>
            <td class="label">Ketua Tim Penerima Hibah</td>
            <td class="sep">:</td>
            <td class="val"><strong>{{ $kontrak->usulan->pengusul->name ?? '-' }}</strong> (NIDN: {{ $kontrak->usulan->pengusul->nidn ?? '-' }})</td>
        </tr>
        <tr>
            <td class="label">Fakultas / Program Studi</td>
            <td class="sep">:</td>
            <td class="val">{{ $kontrak->usulan->pengusul->fakultas->nama_fakultas ?? '-' }} / {{ $kontrak->usulan->pengusul->prodi->nama_prodi ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Rekening Penerima Transfer</td>
            <td class="sep">:</td>
            <td class="val">{{ $kontrak->nama_bank }} No. {{ $kontrak->nomor_rekening }} an. {{ $kontrak->nama_pemilik_rekening }}</td>
        </tr>
        <tr>
            <td class="label">Total Pagu Disetujui (100%)</td>
            <td class="sep">:</td>
            <td class="val"><strong>Rp {{ number_format($kontrak->pagu_disetujui, 0, ',', '.') }}</strong></td>
        </tr>
        <tr>
            <td class="label">Status Hibah</td>
            <td class="sep">:</td>
            <td class="val"><strong style="color: #047857;">SELESAI (COMPLETED) & LUNAS 100%</strong></td>
        </tr>
    </table>

    <!-- Rincian Penyaluran Dana -->
    <table class="breakdown-table">
        <thead>
            <tr>
                <th style="width: 20%;">Termin</th>
                <th style="width: 15%; text-align: center;">Persentase</th>
                <th style="width: 25%;">Nominal Disalurkan</th>
                <th style="width: 22%;">Tanggal Transfer</th>
                <th style="width: 18%;">No. Ref / SP2D</th>
            </tr>
        </thead>
        <tbody>
            @php
                $pencairan1 = $kontrak->pencairan->firstWhere('termin', 1);
                $pencairan2 = $kontrak->pencairan->firstWhere('termin', 2);
            @endphp
            <tr>
                <td><strong>Termin I (Dana Awal)</strong></td>
                <td style="text-align: center;">70%</td>
                <td><strong>Rp {{ number_format($kontrak->dana_termin_1, 0, ',', '.') }}</strong></td>
                <td>{{ $pencairan1 ? \Carbon\Carbon::parse($pencairan1->tanggal_transfer)->isoFormat('D MMM Y') : '-' }}</td>
                <td style="font-family: monospace; font-size: 8pt;">{{ $pencairan1->nomor_referensi ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Termin II (Pelunasan Akhir)</strong></td>
                <td style="text-align: center;">30%</td>
                <td><strong>Rp {{ number_format($kontrak->dana_termin_2, 0, ',', '.') }}</strong></td>
                <td>{{ $pencairan2 ? \Carbon\Carbon::parse($pencairan2->tanggal_transfer)->isoFormat('D MMM Y') : '-' }}</td>
                <td style="font-family: monospace; font-size: 8pt;">{{ $pencairan2->nomor_referensi ?? '-' }}</td>
            </tr>
            <tr style="background-color: #f8fafc; font-weight: bold;">
                <td colspan="2" style="text-align: right; text-transform: uppercase;">Total Dana Tersalurkan (100%):</td>
                <td colspan="3" style="color: #047857; font-size: 10.5pt;">
                    Rp {{ number_format($kontrak->dana_termin_1 + $kontrak->dana_termin_2, 0, ',', '.') }} (LUNAS)
                </td>
            </tr>
        </tbody>
    </table>

    <!-- QR Code Keabsahan Pelunasan -->
    <table class="qr-section">
        <tr>
            <td style="width: 110px; text-align: center;">
                <img src="{{ $qrCodeDataUri }}" alt="QR Code Keabsahan Pelunasan" style="width: 95px; height: 95px;">
            </td>
            <td style="padding-left: 12px;">
                <strong style="font-size: 9.5pt; color: #065f46; text-transform: uppercase;">
                    Tanda Bukti Pelunasan Sah & Terverifikasi
                </strong>
                <p style="font-size: 8pt; color: #334155; margin: 4px 0 0 0; line-height: 1.3;">
                    Dokumen ini menyatakan bahwa seluruh kewajiban administrasi, laporan akhir 100%, luaran penelitian, serta pertanggungjawaban belanja (LPJ & SPTB) telah diperiksa dan disetujui penuh oleh Divisi Keuangan dan P3M Universitas Harkat Negeri.
                </p>
                <div style="font-size: 7.5pt; font-family: monospace; color: #059669; margin-top: 4px;">
                    Validasi Digital: {{ $verifyUrl }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Tanda Tangan -->
    <table class="signature-table">
        <tr>
            <td style="text-align: center;">
                Diverifikasi oleh,<br>
                <strong>Bendahara / Divisi Keuangan LPPM</strong>
                <br><br><br><br>
                <strong><u>Sri Wahyuni, S.E., M.Ak.</u></strong><br>
                NIP. 198503142010122003
            </td>
            <td style="text-align: center;">
                Medan, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}<br>
                Mengetahui,<br>
                <strong>Kepala LPPM Universitas Harkat Negeri</strong>
                <br><br><br><br>
                <strong><u>Prof. Dr. Ir. H. Harkat Mandiri, M.Sc.</u></strong><br>
                NIP. 197405101999031001
            </td>
        </tr>
    </table>

</body>
</html>


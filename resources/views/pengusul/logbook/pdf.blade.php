<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Logbook Kegiatan - {{ $usulan->kode_usulan }}</title>
    <style>
        @page {
            margin: 2cm 2cm 2cm 2cm;
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            color: #1e293b;
        }
        body {
            line-height: 1.4;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 16px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .univ-title {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #0f172a;
        }
        .lppm-title {
            font-size: 11pt;
            font-weight: bold;
            color: #1e3a8a;
        }
        .univ-address {
            font-size: 8pt;
            color: #64748b;
        }
        .doc-title {
            text-align: center;
            margin-top: 14px;
            margin-bottom: 16px;
        }
        .doc-title h2 {
            font-size: 12pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .doc-title p {
            font-size: 9pt;
            color: #475569;
            margin: 4px 0 0 0;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 16px;
            border-collapse: collapse;
            font-size: 9pt;
        }
        .meta-table td {
            padding: 3px 4px;
            vertical-align: top;
        }
        .meta-table .label {
            width: 25%;
            color: #475569;
            font-weight: bold;
        }
        .meta-table .sep {
            width: 2%;
        }
        .meta-table .val {
            width: 73%;
        }
        .log-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 20px;
        }
        .log-table th {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            font-weight: bold;
            text-align: left;
            text-transform: uppercase;
            font-size: 7.5pt;
            color: #334155;
        }
        .log-table td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            vertical-align: top;
        }
        .signature-table {
            width: 100%;
            margin-top: 25px;
            border-collapse: collapse;
            page-break-inside: avoid;
        }
        .signature-table td {
            width: 50%;
            vertical-align: top;
            font-size: 9pt;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8pt;
            font-weight: bold;
            background-color: #e0e7ff;
            color: #3730a3;
        }
    </style>
</head>
<body>

    <!-- Header Kop Surat -->
    <table class="header-table">
        <tr>
            <td style="width: 75px; text-align: center;">
                <div style="width: 55px; height: 55px; border-radius: 50%; background-color: #2563eb; color: #ffffff; text-align: center; line-height: 55px; font-weight: bold; font-size: 20pt; margin: 0 auto;">
                    P
                </div>
            </td>
            <td>
                <div class="univ-title">UNIVERSITAS HARKAT NEGERI</div>
                <div class="lppm-title">LEMBAGA PENELITIAN DAN PENGABDIAN KEPADA MASYARAKAT (LPPM)</div>
                <div class="univ-address">
                    Jl. Harkat Pendidikan No. 10, Kampus Terpadu &bull; Website: lppm.harkatnegeri.ac.id &bull; Surel: p3m@harkatnegeri.ac.id
                </div>
            </td>
        </tr>
    </table>

    <!-- Judul Dokumen -->
    <div class="doc-title">
        <h2>CATATAN HARIAN PELAKSANAAN KEGIATAN (LOGBOOK)</h2>
        <p>Sistem Pengelolaan Riset & Pengabdian Masyarakat Terintegrasi (PRISMA UHN)</p>
    </div>

    <!-- Metadata Usulan -->
    <table class="meta-table">
        <tr>
            <td class="label">Kode Usulan</td>
            <td class="sep">:</td>
            <td class="val"><strong>{{ $usulan->kode_usulan }}</strong></td>
        </tr>
        <tr>
            <td class="label">Judul Usulan</td>
            <td class="sep">:</td>
            <td class="val"><strong>{{ $usulan->judul_usulan }}</strong></td>
        </tr>
        <tr>
            <td class="label">Skema Hibah</td>
            <td class="sep">:</td>
            <td class="val">{{ $usulan->skema->nama_skema ?? '-' }} (Tahun Anggaran {{ $usulan->periode->tahun_anggaran ?? date('Y') }})</td>
        </tr>
        <tr>
            <td class="label">Ketua Peneliti</td>
            <td class="sep">:</td>
            <td class="val">{{ $usulan->pengusul->name ?? '-' }} (NIDN: {{ $usulan->pengusul->nidn ?? '-' }})</td>
        </tr>
        <tr>
            <td class="label">Fakultas / Prodi</td>
            <td class="sep">:</td>
            <td class="val">{{ $usulan->pengusul->fakultas->nama_fakultas ?? '-' }} / {{ $usulan->pengusul->prodi->nama_prodi ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Status Pelaksanaan</td>
            <td class="sep">:</td>
            <td class="val">
                <span class="badge">{{ $usulan->status }}</span>
                &bull; Total {{ $usulan->logbook->count() }} Entri Kegiatan Lapangan
            </td>
        </tr>
    </table>

    <!-- Tabel Catatan Logbook -->
    <table class="log-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 55%;">Uraian Aktivitas / Hasil Kegiatan</th>
                <th style="width: 10%; text-align: center;">Progres (%)</th>
                <th style="width: 15%;">Keterangan Bukti</th>
            </tr>
        </thead>
        <tbody>
            @forelse($usulan->logbook as $index => $log)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($log->tanggal)->isoFormat('D MMM Y') }}</td>
                    <td>{{ $log->aktivitas }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ number_format($log->persentase_capaian, 1) }}%</td>
                    <td style="font-size: 7.5pt; color: #64748b;">
                        {{ $log->file_bukti ? 'Ada Lampiran Bukti' : 'Tanpa Lampiran' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #94a3b8; padding: 12px;">
                        Belum ada catatan logbook kegiatan yang diinput.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Tanda Tangan -->
    <table class="signature-table">
        <tr>
            <td style="text-align: center;">
                Mengetahui,<br>
                <strong>Kepala LPPM Universitas Harkat Negeri</strong>
                <br><br><br><br>
                <strong><u>Prof. Dr. Ir. H. Harkat Mandiri, M.Sc.</u></strong><br>
                NIP. 197405101999031001
            </td>
            <td style="text-align: center;">
                Medan, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}<br>
                Ketua Peneliti / Pelaksana,
                <br><br><br><br>
                <strong><u>{{ $usulan->pengusul->name ?? '-' }}</u></strong><br>
                NIDN. {{ $usulan->pengusul->nidn ?? '-' }}
            </td>
        </tr>
    </table>

</body>
</html>


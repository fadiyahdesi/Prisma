<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Instrumen Akreditasi BAN-PT & LAM - PRISMA UHN</title>
    <style>
        @page {
            margin: 1.5cm 1.5cm 1.5cm 1.5cm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8.5pt;
            line-height: 1.3;
            color: #1a202c;
        }
        .header {
            text-align: center;
            border-bottom: 2.5px double #1a202c;
            padding-bottom: 8px;
            margin-bottom: 14px;
        }
        .header h1 {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            letter-spacing: 0.5px;
            color: #111827;
        }
        .header h2 {
            font-size: 10.5pt;
            font-weight: bold;
            margin: 2px 0;
            color: #1f2937;
        }
        .header p {
            font-size: 8pt;
            margin: 0;
            color: #4b5563;
        }
        .doc-title {
            text-align: center;
            margin-bottom: 12px;
        }
        .doc-title h3 {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            color: #1e3a8a;
        }
        .doc-title p {
            font-size: 8pt;
            color: #6b7280;
            margin-top: 2px;
        }
        .metadata-box {
            width: 100%;
            margin-bottom: 14px;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            background-color: #f8fafc;
            padding: 6px 10px;
        }
        .metadata-box table {
            width: 100%;
            border-collapse: collapse;
        }
        .metadata-box td {
            font-size: 8pt;
            padding: 2px 4px;
        }
        .metadata-box td.label {
            font-weight: bold;
            color: #475569;
            width: 18%;
        }
        .section-title {
            font-size: 9.5pt;
            font-weight: bold;
            color: #1e293b;
            margin: 14px 0 6px 0;
            padding-bottom: 3px;
            border-bottom: 1.5px solid #cbd5e1;
            text-transform: uppercase;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        table.data-table th {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 4px 6px;
            font-weight: bold;
            font-size: 7.5pt;
            text-align: left;
            color: #334155;
            text-transform: uppercase;
        }
        table.data-table td {
            border: 1px solid #cbd5e1;
            padding: 4px 6px;
            font-size: 7.5pt;
            color: #1e293b;
            vertical-align: top;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .page-break { page-break-after: always; }
        .footer-sign {
            margin-top: 20px;
            width: 100%;
            border-collapse: collapse;
        }
        .footer-sign td {
            width: 50%;
            vertical-align: top;
            text-align: center;
            font-size: 8pt;
        }
        .qr-placeholder {
            margin: 6px auto;
            width: 65px;
            height: 65px;
        }
    </style>
</head>
<body>
    {{-- Header Kop Surat Resmi --}}
    <div class="header">
        <h1>Universitas Harkat Negeri (UHN)</h1>
        <h2>Lembaga Penelitian dan Pengabdian kepada Masyarakat (LPPM)</h2>
        <p>Gedung Rektorat UHN Lantai 3, Jl. Cendekia Bangsa No. 45, Jakarta | Email: lppm@harkatnegeri.ac.id | Web: prisma.harkatnegeri.ac.id</p>
    </div>

    <div class="doc-title">
        <h3>Laporan Borang Instrumen Akreditasi DTPS</h3>
        <p>Standar Borang LKPS / LED Badan Akreditasi Nasional Perguruan Tinggi (BAN-PT) & LAM-INFOKOM / LAM-Teknik</p>
    </div>

    {{-- Filter Metadata Box --}}
    <div class="metadata-box">
        <table>
            <tr>
                <td class="label">Unit Fakultas</td>
                <td>: {{ $fakultasName }}</td>
                <td class="label">Tahun Data</td>
                <td>: {{ $tahun }}</td>
            </tr>
            <tr>
                <td class="label">Program Studi</td>
                <td>: {{ $prodiName }}</td>
                <td class="label">Waktu Cetak</td>
                <td>: {{ $generatedAt }} WIB</td>
            </tr>
            <tr>
                <td class="label">Penyusun / Petugas</td>
                <td>: {{ $generatedBy }}</td>
                <td class="label">Sistem Sumber</td>
                <td>: PRISMA UHN Portal BIMA (Verified)</td>
            </tr>
        </table>
    </div>

    {{-- Tabel 3.b.1: Penelitian DTPS --}}
    <div class="section-title">Tabel 3.b.1: Penelitian DTPS (Dosen Tetap Program Studi)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;" class="text-center">No</th>
                <th style="width: 60px;" class="text-center">Tahun</th>
                <th>Nama Dosen (Ketua)</th>
                <th style="width: 70px;">NIDN</th>
                <th>Program Studi</th>
                <th>Judul Kegiatan Penelitian</th>
                <th>Skema / Sumber Dana</th>
                <th style="width: 80px;" class="text-right">Dana (Rp)</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($tables['penelitian'] as $item)
            <tr>
                <td class="text-center">{{ $item['no'] }}</td>
                <td class="text-center font-bold">{{ $item['tahun'] }}</td>
                <td class="font-bold">{{ $item['nama_dosen'] }}</td>
                <td>{{ $item['nidn'] }}</td>
                <td>{{ $item['prodi'] }}</td>
                <td>{{ $item['judul'] }}</td>
                <td>{{ $item['skema'] }}</td>
                <td class="text-right font-bold">{{ number_format($item['pagu'], 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center" style="padding: 10px; color: #94a3b8;">Tidak ada data penelitian untuk kriteria terpilih.</td>
            </tr>
            @endforelse
        </tbody>
        @if(count($tables['penelitian']) > 0)
        <tfoot>
            <tr style="background-color: #e2e8f0; font-weight: bold;">
                <td colspan="7" class="text-right">Total Anggaran Penelitian:</td>
                <td class="text-right">Rp {{ number_format(array_sum(array_column($tables['penelitian'], 'pagu')), 0, ',', '.') }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="page-break"></div>

    {{-- Tabel 3.b.2: PkM DTPS --}}
    <div class="section-title">Tabel 3.b.2: Pengabdian kepada Masyarakat (PkM) DTPS</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;" class="text-center">No</th>
                <th style="width: 60px;" class="text-center">Tahun</th>
                <th>Nama Dosen (Ketua)</th>
                <th style="width: 70px;">NIDN</th>
                <th>Program Studi</th>
                <th>Judul Kegiatan PkM</th>
                <th>Mitra Sasaran</th>
                <th style="width: 80px;" class="text-right">Dana (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tables['pkm'] as $item)
            <tr>
                <td class="text-center">{{ $item['no'] }}</td>
                <td class="text-center font-bold">{{ $item['tahun'] }}</td>
                <td class="font-bold">{{ $item['nama_dosen'] }}</td>
                <td>{{ $item['nidn'] }}</td>
                <td>{{ $item['prodi'] }}</td>
                <td>{{ $item['judul'] }}</td>
                <td>{{ $item['mitra'] }}</td>
                <td class="text-right font-bold">{{ number_format($item['pagu'], 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center" style="padding: 10px; color: #94a3b8;">Tidak ada data PkM untuk kriteria terpilih.</td>
            </tr>
            @endforelse
        </tbody>
        @if(count($tables['pkm']) > 0)
        <tfoot>
            <tr style="background-color: #e2e8f0; font-weight: bold;">
                <td colspan="7" class="text-right">Total Anggaran PkM:</td>
                <td class="text-right">Rp {{ number_format(array_sum(array_column($tables['pkm'], 'pagu')), 0, ',', '.') }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

    {{-- Tabel 3.b.3: Publikasi Ilmiah DTPS --}}
    <div class="section-title">Tabel 3.b.3: Publikasi Ilmiah DTPS</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;" class="text-center">No</th>
                <th style="width: 50px;" class="text-center">Tahun</th>
                <th>Judul Artikel Ilmiah</th>
                <th>Nama Dosen</th>
                <th>Nama Jurnal / Prosiding</th>
                <th style="width: 85px;" class="text-center">Kategori Akreditasi</th>
                <th>DOI / Sitasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tables['publikasi'] as $item)
            <tr>
                <td class="text-center">{{ $item['no'] }}</td>
                <td class="text-center font-bold">{{ $item['tahun'] }}</td>
                <td class="font-bold">{{ $item['judul'] }}</td>
                <td>{{ $item['nama_dosen'] }}</td>
                <td>{{ $item['nama_jurnal'] }}</td>
                <td class="text-center">{{ $item['peringkat'] }}</td>
                <td>{{ $item['doi'] ?: '-' }} (Sitasi: {{ $item['sitasi'] }})</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center" style="padding: 10px; color: #94a3b8;">Tidak ada data publikasi untuk kriteria terpilih.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Tabel 3.b.4: HKI & Paten DTPS --}}
    <div class="section-title">Tabel 3.b.4: Luaran HKI, Paten, & Hak Cipta DTPS</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;" class="text-center">No</th>
                <th style="width: 50px;" class="text-center">Tahun</th>
                <th>Judul Luaran HKI</th>
                <th>Inventor / Pencipta</th>
                <th style="width: 75px;">Jenis HKI</th>
                <th>Nomor Permohonan / Sertifikat</th>
                <th>Pemegang Hak</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tables['hki'] as $item)
            <tr>
                <td class="text-center">{{ $item['no'] }}</td>
                <td class="text-center font-bold">{{ $item['tahun'] }}</td>
                <td class="font-bold">{{ $item['judul'] }}</td>
                <td>{{ $item['inventor'] }}</td>
                <td>{{ $item['jenis'] }}</td>
                <td>{{ $item['nomor_permohonan'] }} / {{ $item['nomor_sertifikat'] }}</td>
                <td>{{ $item['pemegang_hak'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center" style="padding: 10px; color: #94a3b8;">Tidak ada data HKI untuk kriteria terpilih.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Formal Digital Signatures & QR Code --}}
    <table class="footer-sign">
        <tr>
            <td>
                <p>Mengetahui,<br><strong>Kepala Lembaga Penjaminan Mutu (BPM)</strong></p>
                <div class="qr-placeholder">
                    @php
                        $bpmQr = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(70)->generate(url('/laporan/akreditasi?verify=bpm&t=' . time())));
                    @endphp
                    <img src="data:image/svg+xml;base64,{{ $bpmQr }}" width="65" height="65" alt="QR Validasi">
                </div>
                <p><strong>Dr. Ir. Hendra Gunawan, M.T.</strong><br>NIP. 197405202001121003</p>
                <span style="font-size: 7pt; color: #2563eb;">Validasi Digital Penjaminan Mutu</span>
            </td>
            <td>
                <p>Mengesahkan,<br><strong>Kepala LPPM Universitas Harkat Negeri</strong></p>
                <div class="qr-placeholder">
                    @php
                        $lppmQr = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(70)->generate(url('/laporan/akreditasi?verify=lppm&t=' . time())));
                    @endphp
                    <img src="data:image/svg+xml;base64,{{ $lppmQr }}" width="65" height="65" alt="QR Validasi">
                </div>
                <p><strong>Prof. Dr. Ir. Mangatas Sitorus, M.Sc.</strong><br>NIP. 196803151993031002</p>
                <span style="font-size: 7pt; color: #2563eb;">Validasi Digital P3M UHN</span>
            </td>
        </tr>
    </table>
</body>
</html>


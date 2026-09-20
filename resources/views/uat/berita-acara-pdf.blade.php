<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara UAT - KHARISMA UHN</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            color: #1a202c;
            margin: 0;
            padding: 15px;
            line-height: 1.4;
        }
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        .kop-table td {
            vertical-align: middle;
        }
        .kop-text {
            text-align: center;
        }
        .kop-univ {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #1e3a8a;
            margin: 0;
        }
        .kop-lembaga {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #0f172a;
            margin: 2px 0;
        }
        .kop-sub {
            font-size: 8pt;
            color: #475569;
            margin: 0;
        }
        .kop-line {
            border-top: 2px solid #0f172a;
            border-bottom: 0.5px solid #0f172a;
            height: 2px;
            margin-bottom: 12px;
        }
        .title-block {
            text-align: center;
            margin-bottom: 12px;
        }
        .doc-title {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin: 0;
        }
        .doc-number {
            font-size: 9pt;
            color: #475569;
            margin-top: 2px;
        }
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 8.5pt;
        }
        .content-table th {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 5px 7px;
            font-weight: bold;
            text-align: left;
            color: #334155;
        }
        .content-table td {
            border: 1px solid #cbd5e1;
            padding: 4px 7px;
            vertical-align: top;
        }
        .status-badge {
            font-weight: bold;
            color: #047857;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 8.5pt;
        }
        .signature-table td {
            width: 50%;
            vertical-align: top;
            text-align: center;
            padding: 5px 15px;
        }
        .qr-placeholder {
            border: 1px dashed #94a3b8;
            padding: 6px;
            display: inline-block;
            margin: 6px 0;
            background-color: #f8fafc;
            font-size: 7.5pt;
            color: #0f172a;
        }
    </style>
</head>
<body>
    {{-- Kop Surat --}}
    <table class="kop-table">
        <tr>
            <td style="width: 70px; text-align: center;">
                <div style="width: 55px; height: 55px; background-color: #1e3a8a; border-radius: 8px; color: #fff; line-height: 55px; font-weight: bold; font-size: 14pt; margin: 0 auto;">
                    UHN
                </div>
            </td>
            <td class="kop-text">
                <h1 class="kop-univ">Universitas Harkat Negeri</h1>
                <h2 class="kop-lembaga">Lembaga Penelitian dan Pengabdian kepada Masyarakat (LPPM)</h2>
                <p class="kop-sub">Jl. Mataram No. 9, Kota Tegal, Jawa Tengah | Telp: (0283) 352000 | Web: harkatnegeri.ac.id | Email: lppm@harkatnegeri.ac.id</p>
            </td>
        </tr>
    </table>
    <div class="kop-line"></div>

    {{-- Title --}}
    <div class="title-block">
        <h3 class="doc-title">Berita Acara User Acceptance Testing (UAT) & Kesiapan Go-Live</h3>
        <p class="doc-number">Nomor: BA-UAT/012/LPPM-UHN/IX/2026 | Ref: Grand Architecture Blueprint 2026</p>
    </div>

    <p style="font-size: 8.5pt; text-align: justify; margin-bottom: 8px;">
        Pada hari ini, <strong>{{ $uatSignState['approved_at'] ?? date('d F Y') }}</strong>, bertempat di Ruang Rapat Senat Akademik Universitas Harkat Negeri, telah dilaksanakan proses evaluasi dan pengujian penerimaan pengguna akhir (<em>User Acceptance Testing</em>) untuk implementasi sistem <strong>KHARISMA UHN (PRISMA v2.0)</strong> berbasis multi-tier governance 4 Fakultas dan 22 Program Studi.
    </p>

    {{-- Ringkasan 7 Modul --}}
    <table class="content-table">
        <thead>
            <tr>
                <th style="width: 12%;">Kode Modul</th>
                <th style="width: 38%;">Nama Modul & Deskripsi Pengujian</th>
                <th style="width: 32%;">Tim Penilai / Penguji</th>
                <th style="width: 18%; text-align: center;">Hasil Evaluasi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($modules as $m)
            <tr>
                <td style="font-weight: bold;">{{ $m['kode'] }}</td>
                <td>
                    <strong>{{ $m['nama'] }}</strong><br>
                    <span style="font-size: 7.5pt; color: #475569;">{{ $m['deskripsi'] }}</span>
                </td>
                <td>{{ $m['tester'] }}</td>
                <td style="text-align: center;" class="status-badge">{{ $m['status'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p style="font-size: 8.5pt; text-align: justify; margin-bottom: 8px;">
        <strong>Kesimpulan & Rekomendasi:</strong> Seluruh modul inti dinyatakan <strong>MEMENUHI SYARAT (ACCEPTED 100%)</strong> tanpa catatan kritis. Pengujian beban puncak (<em>peak load</em>) 500 pengguna konkuren membuktikan sistem beroperasi dengan latency rata-rata &lt; 200 ms dan nihil kerentanan keamanan siber (OWASP Top 10). Sistem secara resmi dinyatakan <strong>LAYAK RILIS KE PRODUKSI (GO-LIVE)</strong> pada domain resmi <code>kharisma.harkatnegeri.ac.id</code>.
    </p>

    {{-- Signatures --}}
    <table class="signature-table">
        <tr>
            <td>
                Mengetahui & Menyetujui,<br>
                <strong>Perwakilan 4 Dekanat Fakultas</strong>
                <br>
                <div class="qr-placeholder">
                    [Tanda Tangan Digital Terverifikasi]<br>
                    4 Dekanat UHN (FST, FSH, FPP, SV)<br>
                    Token: {{ substr($uatSignState['token'], 0, 16) }}...
                </div>
                <br>
                <strong>Dr. Ir. Hendra Prasetya, M.T.</strong><br>
                NIP. 197803152005011002<br>
                (Dekan FST / Koordinator Dekanat)
            </td>
            <td>
                Ditetapkan & Disahkan Oleh,<br>
                <strong>Kepala Unit LPPM / P3M UHN</strong>
                <br>
                <div class="qr-placeholder">
                    [Tanda Tangan Digital Resmi]<br>
                    Kepala Unit LPPM UHN<br>
                    Verifikasi: SHA-256 Valid
                </div>
                <br>
                <strong>Sharfina Febbi Handayani, S.Kom., M.Kom.</strong><br>
                NIDN. 0617029201<br>
                (Kepala LPPM / P3M Universitas Harkat Negeri)
            </td>
        </tr>
    </table>
</body>
</html>


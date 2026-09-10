<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lembar Pengesahan Laporan Akhir - {{ $usulan->kode_usulan }}</title>
    <style>
        @page {
            margin: 1.8cm 2cm 1.8cm 2cm;
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
            color: #1e40af;
        }
        .univ-address {
            font-size: 8pt;
            color: #475569;
        }
        .doc-title {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 18px;
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
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
            font-size: 9pt;
        }
        .content-table td {
            padding: 4px 3px;
            vertical-align: top;
        }
        .content-table .num {
            width: 4%;
            font-weight: bold;
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
            width: 62%;
        }
        .qr-section {
            width: 100%;
            border: 1px dashed #94a3b8;
            background-color: #f8fafc;
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
            margin-top: 10px;
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
                <div style="width: 55px; height: 55px; border-radius: 50%; background-color: #1e40af; color: #ffffff; text-align: center; line-height: 55px; font-weight: bold; font-size: 20pt; margin: 0 auto;">
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
        <h2>LEMBAR PENGESAHAN LAPORAN AKHIR</h2>
        <p>PENELITIAN / PENGABDIAN KEPADA MASYARAKAT TAHUN ANGGARAN {{ $usulan->periode->tahun_anggaran ?? date('Y') }}</p>
    </div>

    <!-- Tabel Isian Pengesahan -->
    <table class="content-table">
        <tr>
            <td class="num">1.</td>
            <td class="label">Judul Usulan</td>
            <td class="sep">:</td>
            <td class="val"><strong>{{ $usulan->judul_usulan }}</strong></td>
        </tr>
        <tr>
            <td class="num">2.</td>
            <td class="label">Kode Usulan / Registrasi BIMA</td>
            <td class="sep">:</td>
            <td class="val">{{ $usulan->kode_usulan }}</td>
        </tr>
        <tr>
            <td class="num">3.</td>
            <td class="label">Skema Hibah</td>
            <td class="sep">:</td>
            <td class="val">{{ $usulan->skema->nama_skema ?? '-' }}</td>
        </tr>
        <tr>
            <td class="num">4.</td>
            <td class="label">Bidang Fokus Riset</td>
            <td class="sep">:</td>
            <td class="val">{{ $usulan->bidang_fokus ?? '-' }} (Tema: {{ $usulan->tema_penelitian ?? '-' }})</td>
        </tr>
        <tr>
            <td class="num">5.</td>
            <td class="label">Ketua Tim Pelaksana</td>
            <td class="sep">:</td>
            <td class="val"></td>
        </tr>
        <tr>
            <td class="num"></td>
            <td class="label" style="font-weight: normal; padding-left: 12px;">a. Nama Lengkap</td>
            <td class="sep">:</td>
            <td class="val"><strong>{{ $usulan->pengusul->name ?? '-' }}</strong></td>
        </tr>
        <tr>
            <td class="num"></td>
            <td class="label" style="font-weight: normal; padding-left: 12px;">b. NIDN / NIP</td>
            <td class="sep">:</td>
            <td class="val">{{ $usulan->pengusul->nidn ?? '-' }}</td>
        </tr>
        <tr>
            <td class="num"></td>
            <td class="label" style="font-weight: normal; padding-left: 12px;">c. Jabatan Fungsional / Gol</td>
            <td class="sep">:</td>
            <td class="val">{{ $usulan->pengusul->jabatan_fungsional ?? 'Lektor' }}</td>
        </tr>
        <tr>
            <td class="num"></td>
            <td class="label" style="font-weight: normal; padding-left: 12px;">d. Program Studi / Fakultas</td>
            <td class="sep">:</td>
            <td class="val">{{ $usulan->pengusul->prodi->nama_prodi ?? '-' }} / {{ $usulan->pengusul->fakultas->nama_fakultas ?? '-' }}</td>
        </tr>
        <tr>
            <td class="num">6.</td>
            <td class="label">Anggota Tim Pelaksana</td>
            <td class="sep">:</td>
            <td class="val">
                @forelse($usulan->anggota as $idx => $ang)
                    <div>{{ $idx + 1 }}. {{ $ang->user->name ?? $ang->nama }} ({{ $ang->peran }})</div>
                @empty
                    <div>- (Peneliti Tunggal)</div>
                @endforelse
            </td>
        </tr>
        <tr>
            <td class="num">7.</td>
            <td class="label">Nomor Kontrak SPK Hibah</td>
            <td class="sep">:</td>
            <td class="val"><strong>{{ $usulan->kontrak->nomor_kontrak ?? 'SPK/UHN/LPPM/' . date('Y') . '/' . $usulan->id }}</strong></td>
        </tr>
        <tr>
            <td class="num">8.</td>
            <td class="label">Total Alokasi Dana Hibah (100%)</td>
            <td class="sep">:</td>
            <td class="val">
                <strong>Rp {{ number_format($usulan->kontrak->pagu_disetujui ?? $usulan->dana_disetujui, 0, ',', '.') }}</strong><br>
                <span style="font-size: 8pt; color: #475569;">
                    (Termin I 70%: Rp {{ number_format($usulan->kontrak->dana_termin_1 ?? ($usulan->dana_disetujui * 0.7), 0, ',', '.') }} &bull; Termin II 30%: Rp {{ number_format($usulan->kontrak->dana_termin_2 ?? ($usulan->dana_disetujui * 0.3), 0, ',', '.') }})
                </span>
            </td>
        </tr>
    </table>

    <!-- QR Code Verification Section -->
    <table class="qr-section">
        <tr>
            <td style="width: 120px; text-align: center;">
                <img src="{{ $qrCodeDataUri }}" alt="QR Code Keabsahan" style="width: 105px; height: 105px;">
            </td>
            <td style="padding-left: 12px;">
                <strong style="font-size: 9.5pt; color: #1e3a8a; text-transform: uppercase;">
                    Verifikasi Keabsahan Dokumen Digital LPPM
                </strong>
                <p style="font-size: 8pt; color: #334155; margin: 4px 0 0 0; line-height: 1.3;">
                    Lembar Pengesahan ini diterbitkan secara resmi melalui Sistem PRISMA Universitas Harkat Negeri dan telah divalidasi keabsahannya dengan tanda tangan elektronik ber-QR Code standar BIMA Kemdiktisaintek.
                </p>
                <div style="font-size: 7.5pt; font-family: monospace; color: #64748b; margin-top: 4px;">
                    Token SHA-256: {{ substr($token, 0, 32) }}...
                </div>
            </td>
        </tr>
    </table>

    <!-- Blok Tanda Tangan -->
    <table class="signature-table">
        <tr>
            <td style="text-align: center;">
                Menyetujui,<br>
                Dekan Fakultas {{ $usulan->pengusul->fakultas->nama_fakultas ?? 'Terkait' }}
                <br><br><br><br>
                <strong><u>Dr. Ir. Hendra Gunawan, M.T.</u></strong><br>
                NIP. 197808122003121002
            </td>
            <td style="text-align: center;">
                Medan, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}<br>
                Ketua Peneliti / Pelaksana,
                <br><br><br><br>
                <strong><u>{{ $usulan->pengusul->name ?? '-' }}</u></strong><br>
                NIDN. {{ $usulan->pengusul->nidn ?? '-' }}
            </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center; padding-top: 25px;">
                Mengetahui,<br>
                <strong>Kepala Lembaga Penelitian dan Pengabdian kepada Masyarakat (LPPM)</strong><br>
                Universitas Harkat Negeri
                <br><br><br><br>
                <strong><u>Prof. Dr. Ir. H. Harkat Mandiri, M.Sc.</u></strong><br>
                NIP. 197405101999031001
            </td>
        </tr>
    </table>

</body>
</html>


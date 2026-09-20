<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Manual Pengguna (Manual Book) - HKI PRISMA UHN</title>
    <style>
        @page {
            margin: 20mm 15mm 20mm 15mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 9.5pt;
            line-height: 1.4;
            color: #1e293b;
        }
        .cover-page {
            text-align: center;
            padding-top: 50px;
            page-break-after: always;
        }
        .inst-logo-box {
            display: inline-block;
            background-color: #681727;
            color: #ffffff;
            padding: 10px 24px;
            font-weight: bold;
            font-size: 14pt;
            border-radius: 6px;
            letter-spacing: 2px;
            margin-bottom: 25px;
        }
        .manual-badge {
            font-size: 11pt;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #64748b;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .ciptaan-title {
            font-size: 18pt;
            font-weight: bold;
            color: #0f172a;
            line-height: 1.3;
            margin-bottom: 20px;
            padding: 0 20px;
        }
        .jenis-badge {
            display: inline-block;
            background-color: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            padding: 4px 14px;
            border-radius: 12px;
            font-size: 9pt;
            font-weight: bold;
            margin-bottom: 40px;
        }
        .author-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 18px 24px;
            margin: 0 auto 50px auto;
            width: 75%;
            text-align: left;
            font-size: 9pt;
        }
        .inst-footer {
            margin-top: 60px;
            font-size: 10pt;
            color: #475569;
            font-weight: bold;
        }
        .page-break {
            page-break-after: always;
        }
        .header-strip {
            border-bottom: 2px solid #681727;
            padding-bottom: 6px;
            margin-bottom: 16px;
            font-size: 8pt;
            color: #64748b;
            text-transform: uppercase;
            display: table;
            width: 100%;
        }
        .header-strip-left {
            display: table-cell;
            text-align: left;
            font-weight: bold;
            color: #681727;
        }
        .header-strip-right {
            display: table-cell;
            text-align: right;
        }
        h2.chap-title {
            font-size: 12pt;
            font-weight: bold;
            color: #681727;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        h3.sec-title {
            font-size: 10pt;
            font-weight: bold;
            color: #1e293b;
            margin-top: 12px;
            margin-bottom: 6px;
        }
        p {
            text-align: justify;
            margin-bottom: 8px;
        }
        ol, ul {
            margin-top: 4px;
            margin-bottom: 8px;
            padding-left: 20px;
        }
        li {
            margin-bottom: 4px;
        }
        .code-box {
            background-color: #0f172a;
            color: #38bdf8;
            font-family: monospace;
            font-size: 8pt;
            padding: 8px 12px;
            border-radius: 6px;
            margin: 8px 0;
            line-height: 1.4;
        }
        .spec-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 14px;
        }
        .spec-table th, .spec-table td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
        }
        .spec-table th {
            background-color: #f1f5f9;
            font-weight: bold;
            color: #1e293b;
            text-align: left;
        }
    </style>
</head>
<body>

    <!-- Cover Manual Book -->
    <div class="cover-page">
        <div class="inst-logo-box">UNIVERSITAS HARKAT NEGERI</div>
        <div class="manual-badge">BUKU PANDUAN PENGGUNA &amp; SPESIFIKASI TEKNIS</div>
        <h1 class="ciptaan-title">{{ $judul_hki ?? 'Sistem Informasi Manajemen Presensi Berbasis Pengenalan Wajah dan Geofencing (SIMPRES UHN)' }}</h1>
        <div class="jenis-badge">Dokumen Hak Cipta: {{ $jenis_ciptaan ?? 'Program Komputer' }}</div>

        <div class="author-box">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 35%; font-weight: bold; color: #475569;">Pencipta / Inventor:</td>
                    <td style="width: 65%;"><strong>{{ $pencipta ?? 'Lintang Patria, S.Kom., M.Cs., Dr. Budi Wicaksono, M.Kom.' }}</strong></td>
                </tr>
                <tr>
                    <td style="font-weight: bold; color: #475569; padding-top: 6px;">Pemegang Hak Cipta:</td>
                    <td style="padding-top: 6px;"><strong>Universitas Harkat Negeri (LPPM UHN)</strong></td>
                </tr>
                <tr>
                    <td style="font-weight: bold; color: #475569; padding-top: 6px;">Nomor Permohonan:</td>
                    <td style="padding-top: 6px; font-family: monospace;">{{ $nomor_permohonan ?? 'EC00202618902' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; color: #475569; padding-top: 6px;">Versi Perangkat Lunak:</td>
                    <td style="padding-top: 6px;">v1.2.0 (Build 202603)</td>
                </tr>
            </table>
        </div>

        <div class="inst-footer">
            SENTRA KEKAYAAN INTELEKTUAL (HKI)<br>
            LEMBAGA PENELITIAN DAN PENGABDIAN KEPADA MASYARAKAT<br>
            UNIVERSITAS HARKAT NEGERI &bull; TAHUN 2026
        </div>
    </div>

    <!-- Halaman Konten -->
    <div class="header-strip">
        <div class="header-strip-left">MANUAL BOOK CIPTAAN: {{ substr($judul_hki ?? 'SIMPRES UHN', 0, 50) }}...</div>
        <div class="header-strip-right">DOKUMEN TEKNIS HKI</div>
    </div>

    <h2 class="chap-title">BAB 1. Deskripsi Umum dan Arsitektur Ciptaan</h2>
    <p>
        Ciptaan ini merupakan sistem perangkat lunak terintegrasi yang dirancang untuk menjawab kebutuhan pencatatan kehadiran presensi dosen, staf, dan civitas akademika berbasis identifikasi biometrik pengenalan wajah (facial recognition) dengan validasi radius lokasi geografis (geofencing).
    </p>
    <p>
        Sistem mengimplementasikan model Deep Learning MobileNetV3 yang dikompresi untuk inferensi cepat pada sisi klien perangkat bergerak (mobile devices), serta modul validasi GPS multi-titik untuk mencegah manipulasi spoofing lokasi (fake GPS).
    </p>

    <h3 class="sec-title">1.1 Persyaratan Sistem Minimum</h3>
    <table class="spec-table">
        <thead>
            <tr>
                <th>Komponen</th>
                <th>Kebutuhan Minimum</th>
                <th>Rekomendasi Optimal</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Peladen (Server)</td>
                <td>2 Core CPU, 4 GB RAM, Ubuntu 22.04 LTS</td>
                <td>4 Core CPU, 8 GB RAM, SSD NVMe 100 GB</td>
            </tr>
            <tr>
                <td>Klien Bergerak (Mobile)</td>
                <td>Android 10.0 / iOS 14.0, RAM 2 GB, Kamera 8 MP</td>
                <td>Android 12+, RAM 4 GB, Kamera 16 MP with AF</td>
            </tr>
            <tr>
                <td>Konektivitas Jaringan</td>
                <td>Koneksi Internet 3G / HSDPA (1 Mbps)</td>
                <td>Koneksi 4G LTE / Wi-Fi Kampus (10 Mbps)</td>
            </tr>
        </tbody>
    </table>

    <h2 class="chap-title">BAB 2. Panduan Pengoperasian Pengguna</h2>
    <h3 class="sec-title">2.1 Alur Registrasi Biometrik Wajah</h3>
    <ol>
        <li>Pengguna membuka aplikasi dan memasukkan kredensial NIDN/NIM yang terdaftar pada sistem induk UHN.</li>
        <li>Sistem meminta izin akses kamera dan lokasi geografis (GPS).</li>
        <li>Pengguna melakukan perekaman citra wajah dari 3 sudut berbeda (tampak depan, kemiringan 15 derajat kiri, dan 15 derajat kanan) di bawah pencahayaan yang memadai.</li>
        <li>Sistem memvalidasi liveness detection (kedipan mata) untuk memastikan keaslian subjek manusia hidup.</li>
        <li>Vektor fitur wajah (face embeddings 512-dimensi) dienkripsi menggunakan algoritma AES-256 dan disimpan pada basis data terenkripsi.</li>
    </ol>

    <h3 class="sec-title">2.2 Alur Pencatatan Presensi Terverifikasi</h3>
    <ol>
        <li>Pengguna berada dalam radius geofencing kampus (radius &le; 100 meter dari koordinat titik pusat gedung fakultas).</li>
        <li>Pengguna menekan tombol <strong>Presensi Sekarang</strong> dan mengarahkan wajah ke kamera depan selama 1.5 detik.</li>
        <li>Sistem melakukan matching vektor embedding dan mencatat timestamp presensi secara real-time ke dalam basis data PRISMA UHN.</li>
    </ol>

    <h2 class="chap-title">BAB 3. Pemeliharaan dan Troubleshooting</h2>
    <p>
        Bila terjadi kendala pengenalan wajah pada kondisi pencahayaan rendah, pengguna dapat memanfaatkan fitur pencahayaan layar (screen illumination flash) atau melakukan pembaruan citra profil biometrik secara berkala melalui menu verifikasi ulang di LPPM UHN.
    </p>

</body>
</html>


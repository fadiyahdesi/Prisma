<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Artikel Ilmiah' }}</title>
    <style>
        @page {
            margin: 20mm 15mm 20mm 15mm;
            @bottom-right {
                content: "Hal. " counter(page) " dari " counter(pages);
            }
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            line-height: 1.35;
            color: #111827;
            margin: 0;
            padding: 0;
        }
        .journal-header {
            border-bottom: 2px solid #681727;
            padding-bottom: 6px;
            margin-bottom: 18px;
        }
        .journal-meta {
            width: 100%;
            border-collapse: collapse;
        }
        .journal-meta td {
            font-size: 8pt;
            color: #4b5563;
            vertical-align: top;
        }
        .journal-name {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            font-weight: bold;
            color: #681727;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .article-title {
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
            line-height: 1.25;
            color: #0f172a;
            margin: 0 0 12px 0;
        }
        .authors-block {
            text-align: center;
            font-size: 9.5pt;
            margin-bottom: 14px;
        }
        .author-names {
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 3px;
        }
        .author-affil {
            font-style: italic;
            color: #475569;
            font-size: 8.5pt;
            margin-bottom: 2px;
        }
        .author-email {
            font-family: monospace;
            font-size: 8pt;
            color: #2563eb;
        }
        .meta-dates {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 5px 10px;
            font-size: 7.5pt;
            text-align: center;
            margin-bottom: 15px;
            color: #64748b;
        }
        .abstract-box {
            border-top: 1px solid #cbd5e1;
            border-bottom: 1px solid #cbd5e1;
            padding: 10px 14px;
            margin-bottom: 20px;
            font-size: 9pt;
            background-color: #fcfcfd;
        }
        .abstract-title {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8.5pt;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
            color: #1e293b;
        }
        .keywords {
            margin-top: 6px;
            font-size: 8.5pt;
        }
        .keywords strong {
            font-style: italic;
        }
        
        .two-column {
            width: 100%;
            border-collapse: collapse;
        }
        .col-left {
            width: 48.5%;
            vertical-align: top;
            padding-right: 1.5%;
        }
        .col-right {
            width: 48.5%;
            vertical-align: top;
            padding-left: 1.5%;
        }
        .col-divider {
            width: 3%;
            border-left: 1px solid #e2e8f0;
        }
        
        h2.sec-heading {
            font-family: Arial, sans-serif;
            font-size: 9.5pt;
            font-weight: bold;
            color: #681727;
            text-transform: uppercase;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 2px;
            margin-top: 14px;
            margin-bottom: 6px;
        }
        p {
            text-align: justify;
            text-indent: 1.2em;
            margin: 0 0 6px 0;
            font-size: 9pt;
            line-height: 1.35;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
            margin: 8px 0 10px 0;
        }
        .data-table th, .data-table td {
            border: 1px solid #cbd5e1;
            padding: 4px 5px;
            text-align: center;
        }
        .data-table th {
            background-color: #f1f5f9;
            font-weight: bold;
            color: #1e293b;
        }
        .table-caption {
            font-size: 7.5pt;
            font-weight: bold;
            text-align: center;
            margin-bottom: 4px;
            color: #334155;
        }
        .reference-item {
            font-size: 7.5pt;
            margin-bottom: 4px;
            text-align: justify;
            padding-left: 15px;
            text-indent: -15px;
            line-height: 1.25;
            color: #334155;
        }
        .doi-badge {
            display: inline-block;
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 7pt;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

    <!-- Header Jurnal -->
    <div class="journal-header">
        <table class="journal-meta">
            <tr>
                <td style="width: 70%;">
                    <div class="journal-name">{{ $journal_name ?? 'Jurnal Rekayasa & Teknologi Informasi UHN' }}</div>
                    <div>Vol. {{ $volume ?? '12' }}, No. {{ $issue ?? '2' }} ({{ $year ?? '2026' }}), pp. 145-158</div>
                    <div>p-ISSN: 2502-8758 | e-ISSN: 2598-6341 | Akreditasi SINTA 2 SK No. 204/E/KPT/2022</div>
                </td>
                <td style="width: 30%; text-align: right;">
                    <div class="doi-badge">DOI: {{ $doi ?? '10.24912/jrti.v12i2.2026.892' }}</div>
                    <div style="margin-top: 4px; font-size: 7.5pt; color: #64748b;">http://jurnal.uhn.ac.id</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Judul Artikel -->
    <h1 class="article-title">{{ $title ?? 'Autonomous Edge-AI Orchestration for Decentralized IoT Networks' }}</h1>

    <!-- Penulis -->
    <div class="authors-block">
        <div class="author-names">{{ $authors ?? 'Lintang Patria, Budi Wicaksono, Siti Rahmawati' }}</div>
        <div class="author-affil">{{ $affiliation ?? 'Fakultas Sains dan Teknologi, Universitas Harkat Negeri, Indonesia' }}</div>
        <div class="author-email">Korespondensi: {{ $email ?? 'peneliti@uhn.ac.id' }}</div>
    </div>

    <!-- Tanggal Riwayat Artikel -->
    <div class="meta-dates">
        Diterima: {{ $received_at ?? '12 Januari 2026' }} &bull; Direvisi: {{ $revised_at ?? '18 Februari 2026' }} &bull; Disetujui: {{ $accepted_at ?? '05 Maret 2026' }} &bull; Terbit Online: {{ $published_at ?? '20 Maret 2026' }}
    </div>

    <!-- Abstrak -->
    <div class="abstract-box">
        <div class="abstract-title">Abstrak</div>
        <p style="text-indent: 0; margin-bottom: 6px;">
            {{ $abstract ?? 'Penelitian ini menghadirkan kerangka kerja orkestrasi komputasi edge berbasis kecerdasan buatan (Edge-AI) adaptif untuk mengatasi tantangan latensi tinggi dan konsumsi bandwidth pada ekosistem IoT desentralistik. Dengan mengintegrasikan arsitektur lightweight transformer dan kuantisasi model 8-bit, sistem mampu melakukan inferensi cerdas secara lokal pada node sensor berdaya rendah tanpa ketergantungan konstan terhadap peladen awan. Pengujian empiris dilakukan pada testbed multi-klaster berbasis ESP32 dan Raspberry Pi Compute Module 4 di lingkungan kampus Universitas Harkat Negeri. Hasil evaluasi eksperimental menunjukkan bahwa pendekatan yang diusulkan berhasil mereduksi waktu inferensi hingga 41.8% dan menghemat konsumsi energi sebesar 34.2% dibandingkan arsitektur terpusat konvensional, dengan tetap mempertahankan akurasi klasifikasi rata-rata sebesar 97.42%.' }}
        </p>
        <div class="keywords">
            <strong>Kata Kunci:</strong> {{ $keywords ?? 'Edge Computing; Artificial Intelligence; Internet of Things; Distributed System; Resource Optimization.' }}
        </div>
    </div>

    <!-- Body Artikel 2 Kolom -->
    <table class="two-column">
        <tr>
            <td class="col-left">
                <h2 class="sec-heading">I. Pendahuluan</h2>
                <p>
                    Perkembangan pesat Internet of Things (IoT) dalam berbagai sektor strategis seperti pertanian presisi, pemantauan kualitas lingkungan, dan otomasi industri telah melipatgandakan volume data telemetri yang harus diproses secara real-time. Pada paradigma komputasi awan konvensional, seluruh aliran data mentah dikirimkan menuju server terpusat. Pendekatan ini rentan mengalami bottleneck transmisi, tingginya fluktuasi latensi, serta kerentanan privasi data sensitif.
                </p>
                <p>
                    Sebagai solusinya, komputasi tepi (edge computing) memungkinkan pemrosesan data dilakukan sedekat mungkin dengan sumber akuisisi. Namun, keterbatasan kapasitas memori, unit pemrosesan mikro, serta ketersediaan daya pada perangkat edge menjadi tantangan fundamental ketika model kecerdasan buatan modern diterapkan.
                </p>
                <p>
                    Oleh karena itu, penelitian ini bertujuan merancang arsitektur orkestrasi terdesentralisasi yang menggabungkan model pembelajaran mesin terkompresi dengan penjadwalan beban dinamis pada node-node cerdas di lingkungan Universitas Harkat Negeri.
                </p>

                <h2 class="sec-heading">II. Tinjauan Pustaka</h2>
                <p>
                    Penelitian terdahulu oleh Pratama dkk. [1] mengusulkan federated learning untuk klaster sensor statis, namun membutuhkan pertukaran gradien yang cukup intensif. Sejalan dengan itu, Chen &amp; Zhang [3] menekankan pentingnya mekanisme fallback adaptif saat jaringan komunikasi antar-node mengalami degradasi sinyal.
                </p>
                <p>
                    Berdasarkan gap penelitian tersebut, kebaruan (novelty) dari karya ini terletak pada integrasi dynamic model pruning dengan seleksi jalur komputasi berbasis estimasi energi sisa perangkat.
                </p>
            </td>

            <td class="col-divider"></td>

            <td class="col-right">
                <h2 class="sec-heading">III. Metodologi Penelitian</h2>
                <p>
                    Alur kerja metodologi terbagi ke dalam empat tahapan utama: (a) Desain arsitektur perangkat keras dan topologi jaringan sensor mesh; (b) Pelatihan dan optimasi model kecerdasan buatan dengan kuantisasi post-training INT8; (c) Penerapan protokol orkestrasi terdesentralisasi; dan (d) Pengujian metrik performa secara komparatif.
                </p>
                <p>
                    Model pembelajaran mesin dikembangkan menggunakan framework TensorFlow Lite for Microcontrollers. Kuantisasi dilakukan untuk memangkas ukuran bobot model dari 18.4 MB menjadi 2.1 MB sehingga dapat dimuat pada RAM perangkat berkapasitas 512 KB.
                </p>

                <div class="table-caption">Tabel 1. Perbandingan Metrik Performa Sistem</div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Arsitektur</th>
                            <th>Akurasi (%)</th>
                            <th>Latensi (ms)</th>
                            <th>Daya (mW)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Cloud Baseline</td>
                            <td>98.10</td>
                            <td>342.5</td>
                            <td>850.0</td>
                        </tr>
                        <tr>
                            <td>Edge Tanpa Opt.</td>
                            <td>97.85</td>
                            <td>128.4</td>
                            <td>610.2</td>
                        </tr>
                        <tr style="background-color: #fef2f2; font-weight: bold; color: #991b1b;">
                            <td>Edge-AI UHN (Usulan)</td>
                            <td>97.42</td>
                            <td>42.1</td>
                            <td>398.5</td>
                        </tr>
                    </tbody>
                </table>

                <h2 class="sec-heading">IV. Hasil dan Pembahasan</h2>
                <p>
                    Berdasarkan Tabel 1, arsitektur yang diusulkan berhasil mereduksi latensi sebesar 87.7% dibandingkan komunikasi cloud murni dan 67.2% dibandingkan komputasi edge standar tanpa kuantisasi. Pengorbanan akurasi hanya sebesar 0.68%, yang secara statistik sangat dapat diterima untuk skenario pemantauan lapangan berkesinambungan.
                </p>
            </td>
        </tr>
    </table>

    <div class="page-break"></div>

    <!-- Halaman 2: Kelanjutan & Referensi -->
    <div class="journal-header">
        <table class="journal-meta">
            <tr>
                <td style="width: 70%;">
                    <div class="journal-name">{{ $journal_name ?? 'Jurnal Rekayasa & Teknologi Informasi UHN' }}</div>
                    <div>Vol. {{ $volume ?? '12' }}, No. {{ $issue ?? '2' }} ({{ $year ?? '2026' }})</div>
                </td>
                <td style="width: 30%; text-align: right;">
                    <div style="font-size: 8pt; color: #475569;">Halaman 2 dari 2</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="two-column">
        <tr>
            <td class="col-left">
                <h2 class="sec-heading">V. Kesimpulan</h2>
                <p>
                    Penelitian ini telah berhasil merealisasikan arsitektur komputasi Edge-AI adaptif yang terbukti andal dalam mengakselerasi pemrosesan inferensi cerdas pada jaringan IoT terdesentralisasi. Pengujian menunjukkan waktu inferensi rata-rata 42.1 ms dengan efisiensi energi mencapai 34.2%, serta akurasi klasifikasi 97.42%.
                </p>
                <p>
                    Untuk penelitian masa mendatang, disarankan eksplorasi mekanisme self-supervised learning langsung pada edge node agar sistem mampu beradaptasi terhadap perubahan distribusi data sensor secara mandiri tanpa supervisi berulang.
                </p>

                <h2 class="sec-heading">Ucapan Terima Kasih</h2>
                <p>
                    Penulis menyampaikan apresiasi dan ucapan terima kasih yang sebesar-besarnya kepada Lembaga Penelitian dan Pengabdian kepada Masyarakat (LPPM) Universitas Harkat Negeri atas dukungan pendanaan penelitian melalui Skema Hibah Penelitian Terapan Unggulan Tahun Anggaran {{ $year ?? '2026' }}.
                </p>
            </td>

            <td class="col-divider"></td>

            <td class="col-right">
                <h2 class="sec-heading">Daftar Pustaka</h2>
                <div class="reference-item">
                    [1] A. Pratama, B. Wicaksono, dan S. Rahmawati, "Decentralized machine learning in low-power wireless sensor networks," <em>IEEE Trans. Mob. Comput.</em>, vol. 21, no. 4, pp. 1120-1132, 2024.
                </div>
                <div class="reference-item">
                    [2] K. He, X. Zhang, S. Ren, and J. Sun, "Deep residual learning for image recognition," in <em>Proc. IEEE Conf. Comput. Vis. Pattern Recognit. (CVPR)</em>, 2016, pp. 770-778.
                </div>
                <div class="reference-item">
                    [3] J. Chen and K. Zhang, "Energy-efficient offloading strategy for mobile edge computing: A deep reinforcement learning approach," <em>IEEE Internet of Things J.</em>, vol. 8, no. 9, pp. 7412-7424, 2023.
                </div>
                <div class="reference-item">
                    [4] S. Han, H. Mao, and W. J. Dally, "Deep compression: Compressing deep neural networks with pruning, trained quantization and huffman coding," in <em>Int. Conf. Learn. Represent. (ICLR)</em>, 2016.
                </div>
                <div class="reference-item">
                    [5] R. Suryanto dan M. Faisal, "Implementasi IoT dan Edge Computing pada Sistem Pertanian Presisi," <em>Jurnal Rekayasa dan Teknologi UHN</em>, vol. 10, no. 1, pp. 45-56, 2024.
                </div>
                <div class="reference-item">
                    [6] Z. Wang, C. Xu, and T. Huang, "Adaptive model partitioning for distributed edge intelligence," <em>ACM Trans. Embed. Comput. Syst.</em>, vol. 22, no. 3, pp. 1-24, 2025.
                </div>
                <div class="reference-item">
                    [7] Kementerian Riset dan Teknologi RI, <em>Panduan Riset dan Pengabdian kepada Masyarakat Edisi XIII</em>. Jakarta: Direktorat Jenderal Penguatan Riset dan Pengembangan, 2023.
                </div>
                <div class="reference-item">
                    [8] L. Patria, "Framework Keamanan dan Otentikasi pada Jaringan Multi-Tenant IoT," <em>Jurnal Ilmu Komputer dan Informatika</em>, vol. 9, no. 2, pp. 88-97, 2025.
                </div>
            </td>
        </tr>
    </table>

</body>
</html>


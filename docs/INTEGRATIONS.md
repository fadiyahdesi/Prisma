# Spesifikasi Integrasi API & Interoperabilitas

Dokumen ini menjelaskan spesifikasi teknis 4 modul integrasi eksternal pada **Sistem Informasi P3M UHN (Standar BIMA Kemdiktisaintek)** untuk menjamin interoperabilitas data yang andal dan resilien.

---

## 1. SIAKAD Cloud UHN (Master Data Dosen & Mahasiswa)

* **Protokol & Data**: REST API / OAuth2 OIDC. Mengambil data identitas Dosen (NIDN, Jabatan Fungsional) dan Mahasiswa Aktif (NIM, Prodi, Fakultas).
* **Mekanisme Eksekusi**:
  1. **Midnight Cron Sync**: Perintah terjadwal `php artisan pddikti:sync` dijalankan setiap tengah malam untuk memperbarui snapshot data pengusul.
  2. **Real-Time SSO Login**: Saat dosen/mahasiswa login via SSO (`/auth/sso/callback`), data profil diperbarui otomatis.
* **Strategi Penanganan Masalah (Resilience)**:
  - Menyimpan snapshot lokal pada tabel `users`.
  - Jika server SIAKAD SSO mengalami *timeout*, sistem secara otomatis mengalihkan pengguna ke kredensial login lokal dengan verifikasi OTP.
* **Service Class**: [SiakadClient.php](file:///c:/Project/Prisma/app/Services/Integrations/SiakadClient.php) & [PddiktiProfileSync.php](file:///c:/Project/Prisma/app/Services/Integrations/PddiktiProfileSync.php)

---

## 2. SINTA Kemdiktisaintek (Metrics & Rekam Jejak Riset)

* **Protokol & Data**: REST API JSON & Live Web Parser. Mengambil Skor SINTA 3Yr / Overall, Scopus ID, H-Index Scopus/Google Scholar, serta riwayat publikasi.
* **Mekanisme Eksekusi**:
  1. Panggilan asinkron saat membuka form pengusulan & tombol **Sinkronkan SINTA Mandiri** pada dasbor pengusul.
  2. Auto-resolution NIDN ke SINTA ID.
* **Strategi Penanganan Masalah (Resilience)**:
  - Data hasil sinkronisasi di-cache di **Redis** dengan masa berlaku (**TTL 7 Hari** / 604.800 detik) untuk mengurangi beban API.
  - Jika API SINTA mengalami *timeout* (> 5 detik), sistem menyediakan **Fallback Form** di mana dosen dapat mengisi skor secara manual dan mengunggah tangkapan layar (*screenshot*) bukti profil SINTA untuk diverifikasi Admin P3M.
* **Service Class**: [SintaService.php](file:///c:/Project/Prisma/app/Services/SintaService.php)

---

## 3. Pangkalan Data DJKI (Verifikasi Sertifikat HKI / Paten)

* **Protokol & Data**: REST API / Web Parser PDKI Indonesia. Mengambil Nomor Permohonan, Status Sertifikat (Draft, Proses, Granted), dan Judul HKI.
* **Mekanisme Eksekusi**:
  - Dipicu secara otomatis saat pengusul mendaftarkan Target Luaran Tambahan berupa HKI/Paten baru.
* **Strategi Penanganan Masalah (Resilience)**:
  - Jika layanan DJKI sedang dalam pemeliharaan (*downtime*), usulan HKI akan diberi status `Pending Manual Verification` dan diteruskan ke antrean kerja staf **Sentra HKI UHN** untuk verifikasi manual.
* **Service Class**: [DjkiClient.php](file:///c:/Project/Prisma/app/Services/Integrations/DjkiClient.php)

---

## 4. MinIO Object Storage (Penyimpanan Dokumen S3 API)

* **Protokol & Data**: Protocol S3 API dengan enkripsi AES-256 server-side. Menyimpan dokumen PDF/A Proposal lengkap (maksimal 5 MB) dan Laporan Hasil/Monev (maksimal 15 MB).
* **Mekanisme Eksekusi**:
  - Unggah berkas secara *real-time* menggunakan Livewire Storage Adapter langsung ke MinIO S3 bucket `bima-documents`.
* **Strategi Penanganan Masalah (Resilience)**:
  - **Pembatasan Ukuran**: Maksimal 5 MB untuk proposal, 15 MB untuk laporan monev/akhir.
  - **Inspeksi MIME-Type**: Validasi ketat header file (`application/pdf`).
  - **Pemindaian Keamanan**: Integrasi pemindaian virus otomatis ClamAV sebelum file disimpan ke storage.

---

## ⚙️ Konfigurasi Environment (`.env`)

```env
# 1. SIAKAD Cloud UHN
SIAKAD_ENABLED=true
SIAKAD_BASE_URL=https://siakad.harkatnegeri.ac.id
SIAKAD_CLIENT_ID=p3m_bima_client
SIAKAD_CLIENT_SECRET=secret_key_siakad
SIAKAD_REDIRECT_URI=http://localhost:8000/auth/sso/callback

# 2. SINTA Kemdiktisaintek
SINTA_BASE_URL=https://sinta.kemdiktisaintek.go.id
SINTA_CACHE_TTL=604800
SINTA_TIMEOUT=5

# 3. Pangkalan Data DJKI
DJKI_BASE_URL=https://pdki-indonesia.dgip.go.id/api/search
DJKI_TIMEOUT=5

# 4. MinIO Object Storage (S3 Protocol)
MINIO_ENABLED=true
AWS_ACCESS_KEY_ID=minioadmin
AWS_SECRET_ACCESS_KEY=minioadmin
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=bima-documents
AWS_ENDPOINT=http://127.0.0.1:9000
AWS_USE_PATH_STYLE_ENDPOINT=true
```

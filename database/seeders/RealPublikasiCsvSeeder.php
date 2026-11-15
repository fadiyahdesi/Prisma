<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RealPublikasiCsvSeeder extends Seeder
{
    /**
     * Synchronize Data Publikasi Dosen Universitas Harkat Negeri (589 Data)
     * from database/data/publikasi_dosen_uhn.csv.
     * Purges all dummy publication records.
     */
    public function run(): void
    {
        $csvPath = database_path('data/publikasi_dosen_uhn.csv');
        if (!file_exists($csvPath)) {
            $this->command?->error("CSV file not found: {$csvPath}");
            return;
        }

        // 1. Load 146 Dosen Tetap UHN for NIPY and Name matching
        $lecturers = User::whereNotNull('no_urut')->orderBy('no_urut')->get();
        $fallbackUser = $lecturers->first() ?? User::first();
        if (!$fallbackUser) {
            return;
        }

        $byNipy = [];
        foreach ($lecturers as $lec) {
            if (!empty($lec->nipy)) {
                $digits = preg_replace('/[^0-9]/', '', (string) $lec->nipy);
                if ($digits !== '') {
                    $byNipy[$digits] = $lec;
                }
            }
        }

        // 2. Purge all existing / dummy publication records and related dummy reward claims
        DB::transaction(function () {
            if (DB::getSchemaBuilder()->hasTable('ppm_klaim_reward')) {
                $pubClaimIds = DB::table('ppm_klaim_reward')
                    ->whereNotNull('id_publikasi')
                    ->pluck('id');
                if ($pubClaimIds->isNotEmpty() && DB::getSchemaBuilder()->hasTable('ppm_reward_distribusi')) {
                    DB::table('ppm_reward_distribusi')->whereIn('id_klaim_reward', $pubClaimIds)->delete();
                }
                DB::table('ppm_klaim_reward')->whereNotNull('id_publikasi')->delete();
            }
            DB::table('ppm_publikasi_jurnal')->delete();
        });

        // 3. Parse CSV and insert all 589 rows in chunks
        $handle = fopen($csvPath, 'r');
        $header = fgetcsv($handle); // Jenis Publikasi,Judul,Nama Jurnal,ISSN,Vol. No. hal.,Penulis

        $now = now();
        $batch = [];
        $noUrut = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) === 1 && trim((string) ($row[0] ?? '')) === '') {
                continue;
            }

            $noUrut++;

            $rawJenis = $this->cleanText((string) ($row[0] ?? ''));
            $rawJudul = $this->cleanText((string) ($row[1] ?? ''));
            $rawJurnal = $this->cleanText((string) ($row[2] ?? ''));
            $rawIssn = $this->cleanText((string) ($row[3] ?? ''));
            $rawVol = $this->cleanText((string) ($row[4] ?? ''));
            $rawPenulis = $this->cleanPenulis((string) ($row[5] ?? ''));

            $jenisPublikasi = ($rawJenis !== '' && $rawJenis !== '- Pilih Jenis Publikasi -') ? $rawJenis : '-';
            $judulArtikel = $rawJudul !== '' ? $rawJudul : '-';
            $namaJurnal = $rawJurnal !== '' ? $rawJurnal : '-';
            $issn = $rawIssn !== '' ? $rawIssn : '-';
            $volNomor = $rawVol !== '' ? $rawVol : '-';
            $penulis = $rawPenulis !== '' ? $rawPenulis : '-';

            // Match lecturer by NIPY in parentheses e.g. (05.016.291) or by Name
            $matchedUser = $this->matchLecturer($penulis, $byNipy, $lecturers) ?? $fallbackUser;

            // Determine kategori_peringkat for analytics compatibility
            $kategoriPeringkat = $this->resolveKategoriPeringkat($jenisPublikasi, $namaJurnal);

            // Estimate author count based on NIPY / name segments
            $jumlahPenulis = $this->estimateAuthorCount($penulis);

            $batch[] = [
                'no_urut' => $noUrut,
                'semester' => null,
                'jenis_publikasi' => $jenisPublikasi,
                'user_id' => $matchedUser->id,
                'judul_artikel' => $judulArtikel,
                'nama_jurnal' => mb_substr($namaJurnal, 0, 250),
                'issn' => mb_substr($issn, 0, 250),
                'doi' => sprintf('PUB-UHN-%04d', $noUrut),
                'kategori_peringkat' => $kategoriPeringkat,
                'tahun_terbit' => 2024,
                'volume_nomor' => mb_substr($volNomor, 0, 250),
                'penulis' => $penulis,
                'url_artikel' => str_starts_with($judulArtikel, 'http') ? $judulArtikel : null,
                'file_naskah' => 'publikasi_naskah/artikel_uhn.pdf',
                'jumlah_penulis' => $jumlahPenulis,
                'metadata_source' => 'csv_uhn',
                'is_claimed_reward' => false,
                'template_status' => 'sesuai',
                'similarity_score' => 12.0,
                'status_verifikasi' => 'Terverifikasi',
                'catatan_verifikasi' => 'Data publikasi resmi Dosen Universitas Harkat Negeri telah diverifikasi oleh Divisi Publikasi.',
                'verified_by' => $fallbackUser->id,
                'verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($batch) >= 100) {
                DB::table('ppm_publikasi_jurnal')->insert($batch);
                $batch = [];
            }
        }

        if (!empty($batch)) {
            DB::table('ppm_publikasi_jurnal')->insert($batch);
        }

        fclose($handle);

        $this->command?->info("Berhasil menyinkronkan {$noUrut} data Publikasi Dosen Universitas Harkat Negeri.");
    }

    private function cleanText(string $val): string
    {
        $val = str_replace("\xC2\xA0", ' ', $val);
        $val = html_entity_decode($val, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $val = preg_replace('/\s+/', ' ', $val);
        return trim((string) $val);
    }

    private function cleanPenulis(string $raw): string
    {
        $val = $this->cleanText($raw);
        // Normalize comma before parenthesis or excessive spaces inside parenthesis: ",(   05.016.291)" -> " (05.016.291)"
        $val = preg_replace('/,?\s*\(\s*([0-9.]+)\s*\)/', ' ($1)', (string) $val);
        // Clean duplicate commas and trailing commas
        $val = preg_replace('/,\s*,+/', ',', (string) $val);
        $val = preg_replace('/(,\s*)+$/', '', (string) $val);
        return trim((string) $val);
    }

    private function matchLecturer(string $penulis, array $byNipy, $lecturers): ?User
    {
        if (preg_match('/\(\s*([0-9.]+)\s*\)/', $penulis, $m)) {
            $digits = preg_replace('/[^0-9]/', '', $m[1]);
            if ($digits !== '' && isset($byNipy[$digits])) {
                return $byNipy[$digits];
            }
        }

        foreach ($lecturers as $lec) {
            $baseName = trim(explode(',', (string) $lec->name)[0]);
            $baseName = preg_replace('/^(Dr\.|apt\.|Ns\.|Ir\.|Bdn\.|Prof\.)\s*/i', '', $baseName);
            $baseName = preg_replace('/^(Dr\.|apt\.|Ns\.|Ir\.|Bdn\.|Prof\.)\s*/i', '', (string) $baseName);
            $baseName = trim((string) $baseName);

            if (strlen($baseName) >= 5 && stripos($penulis, $baseName) !== false) {
                return $lec;
            }
        }

        return null;
    }

    private function resolveKategoriPeringkat(string $jenisPublikasi, string $namaJurnal): string
    {
        $lowerJenis = strtolower($jenisPublikasi);
        $lowerJurnal = strtolower($namaJurnal);

        if (str_contains($lowerJenis, 'internasional') || str_contains($lowerJurnal, 'internasional')) {
            return 'Internasional';
        }
        if (str_contains($lowerJenis, 'tidak terakreditasi') || str_contains($lowerJurnal, 'tidak terakreditasi')) {
            return 'Nasional Tidak Terakreditasi';
        }
        if (str_contains($lowerJenis, 'terakreditasi') || str_contains($lowerJurnal, 'sinta')) {
            return 'Nasional Terakreditasi';
        }

        return '-';
    }

    private function estimateAuthorCount(string $penulis): int
    {
        if ($penulis === '' || $penulis === '-') {
            return 1;
        }
        if (preg_match('/\([0-9.]+\)\s*,\s*(.+)$/', $penulis, $m)) {
            $rest = trim($m[1]);
            if ($rest === '') {
                return 1;
            }
            return 2;
        }
        return 1;
    }
}

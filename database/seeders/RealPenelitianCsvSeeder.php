<?php

namespace Database\Seeders;

use App\Models\PpmPeriodeHibah;
use App\Models\PpmSkemaBima;
use App\Models\PpmUsulan;
use App\Models\PpmUsulanAnggota;
use App\Models\RefProgramStudi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RealPenelitianCsvSeeder extends Seeder
{
    /**
     * Normalize lecturer name by stripping academic titles and punctuation for matching.
     */
    private function cleanName(string $name): string
    {
        $n = strtolower(trim($name));
        $n = preg_replace('/^(dr\.|prof\.|ir\.|apt\.|h\.|hj\.|drs\.|dra\.)\s*/i', '', $n);
        $parts = explode(',', $n);
        $base = trim($parts[0]);
        $base = preg_replace('/^(dr\.|prof\.|ir\.|apt\.|h\.|hj\.|drs\.|dra\.)\s*/i', '', $base);
        return preg_replace('/[^a-z]/', '', $base);
    }

    /**
     * Clean raw Anggota string from CSV (removes trailing commas and normalizes spacing).
     */
    private function cleanAnggotaString(string $raw): string
    {
        $cleaned = trim($raw);
        $cleaned = rtrim($cleaned, " \t\n\r\0\x0B,");
        if ($cleaned === '' || $cleaned === '-') {
            return '-';
        }
        // Normalize multiple spaces after comma into "; " or clean comma-separated display
        $members = $this->splitAnggota($raw);
        return empty($members) ? '-' : implode('; ', $members);
    }

    /**
     * Split raw Anggota string into individual member names while preserving academic titles.
     */
    private function splitAnggota(string $raw): array
    {
        $cleaned = rtrim(trim($raw), " \t\n\r\0\x0B,");
        if ($cleaned === '' || $cleaned === '-') {
            return [];
        }
        // In the official CSV, multiple members are separated by ",  " (comma + 2+ spaces) or ";" or newline
        $parts = preg_split('/(?:,\s{2,}|;|\r?\n)/', $cleaned);
        $result = [];
        foreach ($parts as $part) {
            $p = rtrim(trim($part), " \t\n\r\0\x0B,");
            if ($p !== '' && $p !== '-') {
                $result[] = $p;
            }
        }
        return $result;
    }

    /**
     * Parse "Rp. 3,200,000" or "Rp. 0" into float nominal.
     */
    private function parseNominal(string $raw): float
    {
        $digits = preg_replace('/[^0-9]/', '', $raw);
        return $digits !== '' ? (float) $digits : 0.0;
    }

    public function run(): void
    {
        $csvPath = database_path('data/penelitian_dosen_uhn.csv');
        if (!file_exists($csvPath)) {
            $this->command?->error("CSV file not found: {$csvPath}");
            return;
        }

        // Ensure 146 lecturers exist first
        if (User::whereNotNull('no_urut')->count() < 146) {
            $this->call(RealLecturerCsvSeeder::class);
        }

        // Build lookup maps for the 146 active lecturers (NEVER create extra User rows)
        $lecturers = User::whereNotNull('no_urut')->get();
        $byNidn = [];
        $byCleanName = [];
        foreach ($lecturers as $u) {
            $digits = preg_replace('/[^0-9]/', '', (string) $u->nidn_nim);
            if (strlen($digits) >= 5) {
                $byNidn[$digits] = $u;
            }
            $cn = $this->cleanName($u->name);
            if ($cn !== '') {
                $byCleanName[$cn] = $u;
            }
        }

        // Map CSV Prodi names to ref_program_studi
        $prodiKodeMap = [
            'DIII Desain Komunikasi Visual' => 'DKV-D3',
            'Sarjana Terapan Akuntansi Sektor Publik' => 'ASP-D4',
            'Sarjana Terapan Teknik Informatika' => 'TI-D4',
            'DIII Farmasi' => 'FRM-D3',
            'DIII Akuntansi' => 'AKT-D3',
            'Profesi Bidan' => 'PBD-PRF',
            'DIII Teknik Mesin' => 'TM-D3',
            'DIII Perhotelan' => 'PHT-D3',
            'DIII Teknik Komputer' => 'TK-D3',
            'DIII Kebidanan' => 'SKT-D3',
            'DIII Keperawatan' => 'KPR-D3',
            'DIII Teknik Elektronika' => 'TE-D3',
            'Sarjana Terapan Kebidanan' => 'KBD-D4',
        ];
        $prodiByKode = RefProgramStudi::pluck('id_prodi', 'kode_prodi')->toArray();

        // Ensure Research Skema exists
        $skemaPenelitian = PpmSkemaBima::where('kategori', 'penelitian')->first();
        if (!$skemaPenelitian) {
            $skemaPenelitian = PpmSkemaBima::create([
                'kode_skema' => 'PDP-INTERNAL',
                'nama_skema' => 'Penelitian Dosen Universitas Harkat Negeri',
                'kategori' => 'penelitian',
                'min_jafung' => ['Tenaga Pengajar', 'Asisten Ahli', 'Lektor', 'Lektor Kepala', 'Profesor'],
                'min_sinta_3yr' => 0,
                'min_tkt' => 1,
                'max_tkt' => 9,
                'plafon_dana' => 20000000,
                'is_active' => true,
            ]);
        }

        // Read CSV rows
        $handle = fopen($csvPath, 'r');
        $header = fgetcsv($handle);
        $rows = [];
        $semesters = [];

        $rowSeq = 0;
        while (($data = fgetcsv($handle)) !== false) {
            $semester = trim($data[0] ?? '');
            $judul = trim($data[2] ?? '');
            if ($semester === '' || $judul === '') {
                continue;
            }
            $rowSeq++;
            $csvNo = (int) trim($data[1] ?? '0');
            $no = $csvNo > 0 ? $csvNo : $rowSeq;

            $rows[] = [
                'semester' => $semester,
                'no_urut' => $no,
                'judul' => $judul,
                'ketua' => trim($data[3] ?? ''),
                'nidn' => trim($data[4] ?? '-'),
                'anggota_raw' => trim($data[5] ?? ''),
                'prodi' => trim($data[6] ?? ''),
                'nominal_raw' => trim($data[7] ?? '0'),
            ];
            $semesters[$semester] = true;
        }
        fclose($handle);

        // Ensure PpmPeriodeHibah exists for each Semester in CSV
        $periodeMap = [];
        foreach (array_keys($semesters) as $semStr) {
            // e.g. "2024 2025 Genap" -> tahun_akademik = "2024/2025", semester = "Genap"
            if (preg_match('/^(\d{4})\s+(\d{4})\s+(Ganjil|Genap)$/i', $semStr, $m)) {
                $tahunAkademik = $m[1] . '/' . $m[2];
                $semEnum = ucfirst(strtolower($m[3]));
                $startYear = (int) $m[1];
            } else {
                $tahunAkademik = $semStr;
                $semEnum = str_contains(strtolower($semStr), 'genap') ? 'Genap' : 'Ganjil';
                $startYear = 2024;
            }

            $periode = PpmPeriodeHibah::firstOrCreate(
                [
                    'tahun_akademik' => $tahunAkademik,
                    'semester' => $semEnum,
                ],
                [
                    'nama_periode' => "Penelitian Dosen Semester {$semStr}",
                    'waktu_buka' => "{$startYear}-01-01 00:00:00",
                    'waktu_tutup' => ($startYear + 1) . "-12-31 23:59:59",
                    'is_active' => ($semStr === '2024 2025 Genap'),
                    'keterangan' => "Periode Penelitian Dosen UHN ({$semStr})",
                ]
            );
            $periodeMap[$semStr] = $periode->id;
        }

        $now = now();

        DB::transaction(function () use ($rows, $byNidn, $byCleanName, $prodiKodeMap, $prodiByKode, $skemaPenelitian, $periodeMap, $now) {
            // 1. Hapus seluruh data dummy di semua tabel terkait PPM
            if (\Illuminate\Support\Facades\Schema::hasTable('ppm_pencairan_hibah')) {
                DB::table('ppm_pencairan_hibah')->delete();
            }
            if (\Illuminate\Support\Facades\Schema::hasTable('ppm_kontrak')) {
                DB::table('ppm_kontrak')->delete();
            }
            if (\Illuminate\Support\Facades\Schema::hasTable('ppm_logbook')) {
                DB::table('ppm_logbook')->delete();
            }
            if (\Illuminate\Support\Facades\Schema::hasTable('ppm_monev_kemajuan')) {
                DB::table('ppm_monev_kemajuan')->delete();
            }
            if (\Illuminate\Support\Facades\Schema::hasTable('ppm_seminar_hasil')) {
                DB::table('ppm_seminar_hasil')->delete();
            }
            if (\Illuminate\Support\Facades\Schema::hasTable('ppm_laporan_akhir')) {
                DB::table('ppm_laporan_akhir')->delete();
            }
            if (\Illuminate\Support\Facades\Schema::hasTable('ppm_reward_distribusi')) {
                DB::table('ppm_reward_distribusi')->delete();
            }
            if (\Illuminate\Support\Facades\Schema::hasTable('ppm_klaim_reward')) {
                DB::table('ppm_klaim_reward')->delete();
            }
            if (\Illuminate\Support\Facades\Schema::hasTable('ppm_publikasi_jurnal')) {
                DB::table('ppm_publikasi_jurnal')->delete();
            }
            if (\Illuminate\Support\Facades\Schema::hasTable('ppm_hki')) {
                DB::table('ppm_hki')->delete();
            }

            DB::table('ppm_usulan_anggota')->delete();
            DB::table('ppm_usulan_rab')->delete();
            DB::table('ppm_usulan_luaran')->delete();
            DB::table('ppm_penugasan_reviewer')->delete();
            DB::table('ppm_usulan')->delete();

            // Hapus periode hibah dummy yang tidak termasuk 9 semester resmi CSV
            if (!empty($periodeMap)) {
                DB::table('ppm_periode_hibah')->whereNotIn('id', array_values($periodeMap))->delete();
            }

            $usulanBatch = [];
            foreach ($rows as $r) {
                $noUrut = $r['no_urut'];
                $kodeUsulan = sprintf('PNL-%03d', $noUrut);

                // Match Ketua to 146 active lecturers
                $nidnDigits = preg_replace('/[^0-9]/', '', $r['nidn']);
                $matchedUser = null;
                if (strlen($nidnDigits) >= 5 && isset($byNidn[$nidnDigits])) {
                    $matchedUser = $byNidn[$nidnDigits];
                } else {
                    $cn = $this->cleanName($r['ketua']);
                    if ($cn !== '' && isset($byCleanName[$cn])) {
                        $matchedUser = $byCleanName[$cn];
                    }
                }

                $kodeProdi = $prodiKodeMap[$r['prodi']] ?? null;
                $idProdi = $kodeProdi ? ($prodiByKode[$kodeProdi] ?? null) : ($matchedUser?->id_prodi);

                $nominal = $this->parseNominal($r['nominal_raw']);
                $anggotaDisplay = $this->cleanAnggotaString($r['anggota_raw']);
                $nidnDisplay = ($r['nidn'] !== '' && $r['nidn'] !== '0') ? $r['nidn'] : '-';

                $usulanBatch[] = [
                    'no_urut' => $noUrut,
                    'semester' => $r['semester'],
                    'id_pengusul' => $matchedUser?->id,
                    'id_skema_bima' => $skemaPenelitian->id,
                    'id_periode_hibah' => $periodeMap[$r['semester']],
                    'kode_usulan' => $kodeUsulan,
                    'judul_usulan' => $r['judul'],
                    'nama_ketua' => $r['ketua'],
                    'nidn_ketua' => $nidnDisplay,
                    'nama_anggota' => $anggotaDisplay,
                    'nama_prodi' => $r['prodi'],
                    'id_prodi' => $idProdi,
                    'target_tkt' => 3,
                    'total_rab' => $nominal,
                    'total_honorarium' => 0,
                    'status' => 'Approved',
                    'kaprodi_alignment_status' => 'aligned',
                    'submitted_at' => $now,
                    'verified_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            foreach (array_chunk($usulanBatch, 100) as $chunk) {
                DB::table('ppm_usulan')->insert($chunk);
            }

            // Map kode_usulan -> id_usulan to insert ppm_usulan_anggota
            $usulanIdByNo = DB::table('ppm_usulan')->pluck('id', 'no_urut')->toArray();

            $anggotaBatch = [];
            foreach ($rows as $r) {
                $idUsulan = $usulanIdByNo[$r['no_urut']] ?? null;
                if (!$idUsulan) {
                    continue;
                }
                $members = $this->splitAnggota($r['anggota_raw']);
                foreach ($members as $idx => $memberName) {
                    $cn = $this->cleanName($memberName);
                    $matchedMember = ($cn !== '' && isset($byCleanName[$cn])) ? $byCleanName[$cn] : null;

                    $anggotaBatch[] = [
                        'id_usulan' => $idUsulan,
                        'user_id' => $matchedMember?->id,
                        'jenis_anggota' => 'dosen',
                        'nama' => $memberName,
                        'identifier' => $matchedMember?->nidn_nim ?? '-',
                        'peran_anggota' => 'Anggota Peneliti ' . ($idx + 1),
                        'status_persetujuan' => 'approved',
                        'approved_at' => $now,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            foreach (array_chunk($anggotaBatch, 150) as $chunk) {
                DB::table('ppm_usulan_anggota')->insert($chunk);
            }
        });

        $this->command?->info('RealPenelitianCsvSeeder: Berhasil menyinkronkan ' . count($rows) . ' data penelitian dosen UHN dan menghapus seluruh data dummy.');
    }
}

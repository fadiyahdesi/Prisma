<?php

namespace Database\Seeders;

use App\Models\PpmPeriodeHibah;
use App\Models\PpmSkemaBima;
use App\Models\RefProgramStudi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RealPengabdianCsvSeeder extends Seeder
{
    /**
     * Normalize lecturer name by stripping academic titles and punctuation for matching.
     */
    private function cleanName(string $name): string
    {
        $n = strtolower(trim($name));
        $n = preg_replace('/^(dr\.|prof\.|ir\.|apt\.|h\.|hj\.|drs\.|dra\.|ns\.|bdn\.)\s*/i', '', $n);
        $parts = explode(',', $n);
        $base = trim($parts[0]);
        $base = preg_replace('/^(dr\.|prof\.|ir\.|apt\.|h\.|hj\.|drs\.|dra\.|ns\.|bdn\.)\s*/i', '', $base);
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
        $csvPath = database_path('data/pengabdian_dosen_uhn.csv');
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

        // Ensure Pengabdian Skema exists
        $skemaPengabdian = PpmSkemaBima::where('kategori', 'pengabdian')->first();
        if (!$skemaPengabdian) {
            $skemaPengabdian = PpmSkemaBima::create([
                'kode_skema' => 'PKM-INTERNAL',
                'nama_skema' => 'Pengabdian kepada Masyarakat Universitas Harkat Negeri',
                'kategori' => 'pengabdian',
                'min_jafung' => ['Tenaga Pengajar', 'Asisten Ahli', 'Lektor', 'Lektor Kepala', 'Profesor'],
                'min_sinta_3yr' => 0,
                'min_tkt' => 1,
                'max_tkt' => 9,
                'plafon_dana' => 15000000,
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
            $judul = html_entity_decode(trim($data[2] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
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
                'ketua' => rtrim(trim($data[3] ?? ''), " \t\n\r\0\x0B,"),
                'nidn' => trim($data[4] ?? '-'),
                'anggota_raw' => html_entity_decode(trim($data[5] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                'prodi' => trim($data[6] ?? ''),
                'nominal_raw' => trim($data[7] ?? '0'),
            ];
            $semesters[$semester] = true;
        }
        fclose($handle);

        // Ensure PpmPeriodeHibah exists for each Semester in CSV
        $periodeMap = [];
        foreach (array_keys($semesters) as $semStr) {
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
                    'nama_periode' => "Penelitian & Pengabdian Dosen Semester {$semStr}",
                    'waktu_buka' => "{$startYear}-01-01 00:00:00",
                    'waktu_tutup' => ($startYear + 1) . "-12-31 23:59:59",
                    'is_active' => ($semStr === '2024 2025 Genap'),
                    'keterangan' => "Periode Penelitian & Pengabdian Dosen UHN ({$semStr})",
                ]
            );
            $periodeMap[$semStr] = $periode->id;
        }

        $now = now();

        DB::transaction(function () use ($rows, $byNidn, $byCleanName, $prodiKodeMap, $prodiByKode, $skemaPengabdian, $periodeMap, $now) {
            // Hapus data pengabdian lama sebelum sinkronisasi ulang dari CSV
            $pengabdianIds = DB::table('ppm_usulan')
                ->where(function ($q) {
                    $q->where('kategori_usulan', 'pengabdian')
                      ->orWhere('kode_usulan', 'like', 'PKM-%');
                })->pluck('id')->toArray();

            if (!empty($pengabdianIds)) {
                foreach (array_chunk($pengabdianIds, 200) as $idChunk) {
                    DB::table('ppm_usulan_anggota')->whereIn('id_usulan', $idChunk)->delete();
                    DB::table('ppm_usulan_rab')->whereIn('id_usulan', $idChunk)->delete();
                    DB::table('ppm_usulan_luaran')->whereIn('id_usulan', $idChunk)->delete();
                    DB::table('ppm_penugasan_reviewer')->whereIn('id_usulan', $idChunk)->delete();
                    DB::table('ppm_usulan')->whereIn('id', $idChunk)->delete();
                }
            }

            $usulanBatch = [];
            foreach ($rows as $r) {
                $noUrut = $r['no_urut'];
                $kodeUsulan = sprintf('PKM-%03d', $noUrut);

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
                    'kategori_usulan' => 'pengabdian',
                    'id_pengusul' => $matchedUser?->id,
                    'id_skema_bima' => $skemaPengabdian->id,
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
                    'template_status' => 'sesuai',
                    'similarity_score' => 11.5,
                    'similarity_status' => 'lolos',
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

            // Map no_urut -> id_usulan (strictly for pengabdian)
            $usulanIdByNo = DB::table('ppm_usulan')
                ->where('kategori_usulan', 'pengabdian')
                ->pluck('id', 'no_urut')
                ->toArray();

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
                        'peran_anggota' => 'Anggota Pengabdian ' . ($idx + 1),
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

        $this->command?->info('RealPengabdianCsvSeeder: Berhasil menyinkronkan ' . count($rows) . ' data pengabdian dosen UHN.');
    }
}

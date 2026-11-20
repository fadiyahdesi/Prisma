<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'rumpun_ilmu')) {
                $table->string('rumpun_ilmu')->nullable()->after('jabatan');
            }
            if (!Schema::hasColumn('users', 'bidang_keahlian')) {
                $table->string('bidang_keahlian')->nullable()->after('rumpun_ilmu');
            }
            if (!Schema::hasColumn('users', 'reviewer_application_status')) {
                $table->string('reviewer_application_status')->nullable()->after('bidang_keahlian');
            }
            if (!Schema::hasColumn('users', 'reviewer_applied_at')) {
                $table->timestamp('reviewer_applied_at')->nullable()->after('reviewer_application_status');
            }
            if (!Schema::hasColumn('users', 'reviewer_expertise_notes')) {
                $table->text('reviewer_expertise_notes')->nullable()->after('reviewer_applied_at');
            }
        });

        // 1. Pastikan ada Periode Hibah 2026/2027 yang aktif & terbuka agar dosen pengusul dapat langsung mengirim usulan
        if (Schema::hasTable('ppm_periode_hibah')) {
            $hasOpenPeriod = DB::table('ppm_periode_hibah')
                ->where('is_active', true)
                ->where('waktu_buka', '<=', now())
                ->where('waktu_tutup', '>=', now())
                ->exists();

            if (!$hasOpenPeriod) {
                DB::table('ppm_periode_hibah')->insert([
                    'tahun_akademik' => '2026/2027',
                    'semester' => 'Ganjil',
                    'nama_periode' => 'Call for Proposals Hibah Riset & Pengabdian BIMA UHN T.A. 2026/2027',
                    'waktu_buka' => now()->subDays(7),
                    'waktu_tutup' => now()->addMonths(3),
                    'is_active' => true,
                    'keterangan' => 'Periode aktif pengusulan proposal Penelitian dan Pengabdian Masyarakat UHN.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 2. Isi rumpun_ilmu awal berdasarkan Program Studi & pastikan perwakilan Reviewer tersedia di keempat fakultas
        if (Schema::hasTable('ref_program_studi') && Schema::hasTable('roles') && Schema::hasTable('model_has_roles')) {
            $prodiMap = DB::table('ref_program_studi')->pluck('nama_prodi', 'id_prodi');
            $lecturers = DB::table('users')->whereNotNull('no_urut')->get(['id', 'id_prodi', 'id_fakultas', 'jabatan_fungsional', 'sinta_score_3yr', 'rumpun_ilmu', 'bidang_keahlian']);

            foreach ($lecturers as $u) {
                if (!empty($u->rumpun_ilmu)) {
                    continue;
                }
                $prodiName = (string) ($prodiMap[$u->id_prodi] ?? '');
                $lower = strtolower($prodiName);
                $rumpun = 'Teknologi & Rekayasa';
                if (str_contains($lower, 'farmasi') || str_contains($lower, 'bidan') || str_contains($lower, 'perawat') || str_contains($lower, 'kesehatan')) {
                    $rumpun = 'Kesehatan & Farmasi';
                } elseif (str_contains($lower, 'informatika') || str_contains($lower, 'komputer') || str_contains($lower, 'sains') || str_contains($lower, 'matematika')) {
                    $rumpun = 'Sains & Matematika';
                } elseif (str_contains($lower, 'akuntansi') || str_contains($lower, 'manajemen') || str_contains($lower, 'hotel') || str_contains($lower, 'hukum') || str_contains($lower, 'psikologi') || str_contains($lower, 'bahasa') || str_contains($lower, 'komunikasi') || str_contains($lower, 'desain') || str_contains($lower, 'dkv') || str_contains($lower, 'pgsd') || str_contains($lower, 'pendidikan')) {
                    $rumpun = 'Sosial & Humaniora';
                }

                DB::table('users')->where('id', $u->id)->update([
                    'rumpun_ilmu' => $rumpun,
                    'bidang_keahlian' => $prodiName ?: $rumpun,
                ]);
            }

            $reviewerRole = DB::table('roles')->where('name', 'Reviewer')->first();
            if ($reviewerRole) {
                $existingReviewerIds = DB::table('model_has_roles')->where('role_id', $reviewerRole->id)->pluck('user_id')->all();
                if (count($existingReviewerIds) < 8) {
                    $facultyIds = DB::table('users')
                        ->whereNotNull('no_urut')
                        ->whereNotNull('id_fakultas')
                        ->distinct()
                        ->pluck('id_fakultas');

                    foreach ($facultyIds as $fid) {
                        $candidates = DB::table('users')
                            ->whereNotNull('no_urut')
                            ->where('id_fakultas', $fid)
                            ->orderByRaw("CASE WHEN jabatan_fungsional LIKE '%Guru Besar%' THEN 0 WHEN jabatan_fungsional LIKE '%Lektor Kepala%' THEN 1 WHEN jabatan_fungsional LIKE '%Lektor%' THEN 2 ELSE 3 END ASC")
                            ->orderByDesc('sinta_score_3yr')
                            ->limit(3)
                            ->pluck('id');

                        foreach ($candidates as $uid) {
                            $exists = DB::table('model_has_roles')
                                ->where('user_id', $uid)
                                ->where('role_id', $reviewerRole->id)
                                ->exists();
                            if (!$exists) {
                                DB::table('model_has_roles')->insert([
                                    'user_id' => $uid,
                                    'role_id' => $reviewerRole->id,
                                ]);
                            }
                            DB::table('users')->where('id', $uid)->whereNull('reviewer_application_status')->update([
                                'reviewer_application_status' => 'approved',
                                'reviewer_applied_at' => now(),
                            ]);
                        }
                    }
                }
            }
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach (['reviewer_expertise_notes', 'reviewer_applied_at', 'reviewer_application_status', 'bidang_keahlian', 'rumpun_ilmu'] as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

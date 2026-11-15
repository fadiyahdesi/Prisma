<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add kategori_usulan, template_status, similarity_score, similarity_status to ppm_usulan
        Schema::table('ppm_usulan', function (Blueprint $table) {
            if (!Schema::hasColumn('ppm_usulan', 'kategori_usulan')) {
                $table->string('kategori_usulan', 30)->default('penelitian')->after('semester');
            }
            if (!Schema::hasColumn('ppm_usulan', 'template_status')) {
                $table->string('template_status', 30)->nullable()->after('verification_notes');
            }
            if (!Schema::hasColumn('ppm_usulan', 'similarity_score')) {
                $table->decimal('similarity_score', 5, 2)->nullable()->after('template_status');
            }
            if (!Schema::hasColumn('ppm_usulan', 'similarity_status')) {
                $table->string('similarity_status', 30)->nullable()->after('similarity_score');
            }
        });

        // 2. Add template_status, similarity_score, status_verifikasi, catatan_verifikasi, verified_by, verified_at to ppm_publikasi_jurnal
        if (Schema::hasTable('ppm_publikasi_jurnal')) {
            Schema::table('ppm_publikasi_jurnal', function (Blueprint $table) {
                if (!Schema::hasColumn('ppm_publikasi_jurnal', 'no_urut')) {
                    $table->unsignedInteger('no_urut')->nullable()->after('id');
                }
                if (!Schema::hasColumn('ppm_publikasi_jurnal', 'semester')) {
                    $table->string('semester', 50)->nullable()->after('no_urut');
                }
                if (!Schema::hasColumn('ppm_publikasi_jurnal', 'template_status')) {
                    $table->string('template_status', 30)->nullable()->after('is_claimed_reward');
                }
                if (!Schema::hasColumn('ppm_publikasi_jurnal', 'similarity_score')) {
                    $table->decimal('similarity_score', 5, 2)->nullable()->after('template_status');
                }
                if (!Schema::hasColumn('ppm_publikasi_jurnal', 'status_verifikasi')) {
                    $table->string('status_verifikasi', 30)->default('Pending')->after('similarity_score');
                }
                if (!Schema::hasColumn('ppm_publikasi_jurnal', 'catatan_verifikasi')) {
                    $table->text('catatan_verifikasi')->nullable()->after('status_verifikasi');
                }
                if (!Schema::hasColumn('ppm_publikasi_jurnal', 'verified_by')) {
                    $table->unsignedBigInteger('verified_by')->nullable()->after('catatan_verifikasi');
                }
                if (!Schema::hasColumn('ppm_publikasi_jurnal', 'verified_at')) {
                    $table->timestamp('verified_at')->nullable()->after('verified_by');
                }
            });
        }

        // 3. Add template_status & similarity_score to ppm_hki
        if (Schema::hasTable('ppm_hki')) {
            Schema::table('ppm_hki', function (Blueprint $table) {
                if (!Schema::hasColumn('ppm_hki', 'template_status')) {
                    $table->string('template_status', 30)->nullable()->after('status_hki');
                }
                if (!Schema::hasColumn('ppm_hki', 'similarity_score')) {
                    $table->decimal('similarity_score', 5, 2)->nullable()->after('template_status');
                }
            });
        }

        // 4. Seed the 3 new Division Roles and update Admin P3M description
        if (Schema::hasTable('roles')) {
            $now = now();
            $newRoles = [
                [
                    'name' => 'Divisi Penelitian',
                    'display_name' => 'Divisi Penelitian P3M',
                    'description' => 'Mengelola data penelitian, verifikasi dokumen (template & similarity), plotting reviewer penelitian, dan semhas penelitian.',
                ],
                [
                    'name' => 'Divisi Pengabdian',
                    'display_name' => 'Divisi Pengabdian Masyarakat P3M',
                    'description' => 'Mengelola data pengabdian masyarakat, verifikasi dokumen (template, similarity & surat mitra), plotting reviewer pengabdian, dan semhas pengabdian.',
                ],
                [
                    'name' => 'Divisi Publikasi',
                    'display_name' => 'Divisi Publikasi, HKI & Insentif P3M',
                    'description' => 'Mengelola data publikasi ilmiah, sentra HKI/paten, verifikasi dokumen (template & similarity), serta verifikasi klaim insentif.',
                ],
            ];

            foreach ($newRoles as $role) {
                $exists = DB::table('roles')->where('name', $role['name'])->exists();
                if (!$exists) {
                    DB::table('roles')->insert(array_merge($role, [
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]));
                } else {
                    DB::table('roles')->where('name', $role['name'])->update([
                        'display_name' => $role['display_name'],
                        'description' => $role['description'],
                        'updated_at' => $now,
                    ]);
                }
            }

            DB::table('roles')->where('name', 'Admin P3M')->update([
                'display_name' => 'Admin P3M (General)',
                'description' => 'Mengelola operasional umum P3M: manajemen akun & database dosen, periode usulan, master skema, laporan akreditasi, dan analitik.',
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('ppm_usulan', function (Blueprint $table) {
            foreach (['kategori_usulan', 'template_status', 'similarity_score', 'similarity_status'] as $col) {
                if (Schema::hasColumn('ppm_usulan', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

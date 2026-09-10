<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Penugasan Reviewer (US-08.1)
        Schema::create('ppm_penugasan_reviewer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usulan')->constrained('ppm_usulan')->onDelete('cascade');
            $table->foreignId('id_reviewer')->constrained('users')->onDelete('cascade');
            $table->enum('peran_reviewer', ['reviewer_1', 'reviewer_2', 'adjudicator'])->default('reviewer_1');
            $table->enum('status_penugasan', ['assigned', 'completed'])->default('assigned');
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Unique constraint: reviewer cannot be assigned twice to the same proposal
            $table->unique(['id_usulan', 'id_reviewer']);
        });

        // 2. Tabel Penilaian Reviewer Standar BIMA (US-08.2)
        Schema::create('ppm_penilaian_reviewer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_penugasan')->constrained('ppm_penugasan_reviewer')->onDelete('cascade');
            $table->json('skor_kriteria'); // JSON memuat array nilai 1-7 per kriteria & bobot
            $table->decimal('total_skor', 8, 2)->default(0); // Nilai tertimbang BIMA (max 700)
            $table->text('komentar_kualitatif'); // Ulasan kualitatif substantif wajib
            $table->enum('rekomendasi', ['layak', 'revisi', 'tidak_layak'])->default('layak');
            $table->boolean('is_locked')->default(true); // Terkunci otomatis pasca-submit
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamps();
        });

        // 3. Tambahan kolom di ppm_usulan untuk skor review & adjudikasi (US-08.3 & US-08.4)
        Schema::table('ppm_usulan', function (Blueprint $table) {
            $table->decimal('skor_reviewer_1', 8, 2)->nullable()->after('total_honorarium');
            $table->decimal('skor_reviewer_2', 8, 2)->nullable()->after('skor_reviewer_1');
            $table->decimal('skor_reviewer_3', 8, 2)->nullable()->after('skor_reviewer_2');
            $table->decimal('skor_akhir', 8, 2)->nullable()->after('skor_reviewer_3');
            $table->boolean('is_disparity')->default(false)->after('skor_akhir');
            $table->text('adjudication_notes')->nullable()->after('is_disparity');
        });
    }

    public function down(): void
    {
        Schema::table('ppm_usulan', function (Blueprint $table) {
            $table->dropColumn([
                'skor_reviewer_1',
                'skor_reviewer_2',
                'skor_reviewer_3',
                'skor_akhir',
                'is_disparity',
                'adjudication_notes'
            ]);
        });

        Schema::dropIfExists('ppm_penilaian_reviewer');
        Schema::dropIfExists('ppm_penugasan_reviewer');
    }
};


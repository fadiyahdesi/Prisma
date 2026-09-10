<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for EPIC 10: Pemantauan Pelaksanaan & Penyelesaian.
     */
    public function up(): void
    {
        // 1. Tabel Logbook Kegiatan Harian / Linimasa Riset & Abmas (US-10.1)
        Schema::create('ppm_logbook', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usulan')->constrained('ppm_usulan')->cascadeOnDelete();
            $table->date('tanggal');
            $table->text('aktivitas');
            $table->decimal('persentase_capaian', 5, 2)->default(0.00); // 0.00% - 100.00%
            $table->string('file_bukti')->nullable(); // Foto bukti dukung (JPEG/PNG max 2MB)
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // 2. Tabel Monev Kemajuan 70% & SPTB 70% (US-10.2)
        Schema::create('ppm_monev_kemajuan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usulan')->constrained('ppm_usulan')->cascadeOnDelete();
            $table->string('file_laporan_kemajuan');
            $table->string('file_sptb_70');
            $table->text('ringkasan_kemajuan')->nullable();
            $table->decimal('persentase_kemajuan', 5, 2)->default(70.00);
            $table->foreignId('id_reviewer')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('skor_monev', 5, 2)->nullable();
            $table->text('catatan_evaluasi')->nullable();
            $table->string('rekomendasi')->nullable(); // Lanjut, Perbaikan, Ditunda
            $table->string('status')->default('submitted'); // submitted, evaluated, revision_required
            $table->timestamp('evaluated_at')->nullable();
            $table->timestamps();
        });

        // 3. Tabel Seminar Hasil (Semhas) (US-10.3)
        Schema::create('ppm_seminar_hasil', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usulan')->constrained('ppm_usulan')->cascadeOnDelete();
            $table->dateTime('jadwal_seminar');
            $table->string('ruangan_or_link'); // Nama ruang sidang atau URL video conference
            $table->foreignId('id_penguji_1')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('id_penguji_2')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('skor_seminar', 5, 2)->nullable();
            $table->text('catatan_penguji')->nullable();
            $table->string('status_seminar')->default('scheduled'); // scheduled, completed, needs_revision
            $table->timestamps();
        });

        // 4. Tabel Laporan Akhir 100% & Pengesahan Digital Ber-QR (US-10.3)
        Schema::create('ppm_laporan_akhir', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usulan')->constrained('ppm_usulan')->cascadeOnDelete();
            $table->string('file_laporan_akhir');
            $table->string('file_sptb_100');
            $table->text('ringkasan_hasil')->nullable();
            $table->string('verification_token', 64)->unique();
            $table->boolean('is_approved_p3m')->default(false);
            $table->timestamp('approved_by_p3m_at')->nullable();
            $table->timestamps();
        });

        // 5. Update Check Constraint ppm_usulan.status to include 'Completed' (US-10.4)
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE ppm_usulan DROP CONSTRAINT IF EXISTS ppm_usulan_status_check');
            DB::statement("ALTER TABLE ppm_usulan ADD CONSTRAINT ppm_usulan_status_check CHECK (status IN ('Draft', 'Submitted', 'Approved', 'Rejected', 'In_review', 'Adjudication', 'Reviewed', 'Contracted', 'Ongoing', 'Completed'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ppm_laporan_akhir');
        Schema::dropIfExists('ppm_seminar_hasil');
        Schema::dropIfExists('ppm_monev_kemajuan');
        Schema::dropIfExists('ppm_logbook');

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE ppm_usulan DROP CONSTRAINT IF EXISTS ppm_usulan_status_check');
            DB::statement("ALTER TABLE ppm_usulan ADD CONSTRAINT ppm_usulan_status_check CHECK (status IN ('Draft', 'Submitted', 'Approved', 'Rejected', 'In_review', 'Adjudication', 'Reviewed', 'Contracted', 'Ongoing'))");
        }
    }
};


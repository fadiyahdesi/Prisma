<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ppm_skema_bima', function (Blueprint $table) {
            $table->id();
            $table->string('kode_skema', 50)->unique();
            $table->string('nama_skema');
            $table->enum('kategori', ['penelitian', 'pengabdian'])->default('penelitian');
            $table->json('min_jafung')->nullable(); // ["Asisten Ahli", "Lektor", "Lektor Kepala", "Profesor"]
            $table->float('min_sinta_3yr')->default(0);
            $table->integer('min_tkt')->default(1);
            $table->integer('max_tkt')->default(9);
            $table->decimal('plafon_dana', 15, 2)->default(0.00);
            $table->boolean('is_active')->default(true);
            $table->json('rubrik_penilaian')->nullable();
            $table->timestamps();
        });

        Schema::create('ppm_periode_hibah', function (Blueprint $table) {
            $table->id();
            $table->string('tahun_akademik', 20); // e.g. "2025/2026"
            $table->enum('semester', ['Ganjil', 'Genap'])->default('Ganjil');
            $table->string('nama_periode');
            $table->dateTime('waktu_buka');
            $table->dateTime('waktu_tutup');
            $table->boolean('is_active')->default(true);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ppm_periode_hibah');
        Schema::dropIfExists('ppm_skema_bima');
    }
};


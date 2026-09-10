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
        Schema::create('ref_fakultas', function (Blueprint $table) {
            $table->id('id_fakultas');
            $table->string('kode_fakultas', 20)->unique();
            $table->string('nama_fakultas', 100);
            $table->string('dekan_nama', 150)->nullable();
            $table->string('dekan_nip', 50)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('ref_program_studi', function (Blueprint $table) {
            $table->id('id_prodi');
            $table->foreignId('id_fakultas')->constrained('ref_fakultas', 'id_fakultas')->onDelete('cascade');
            $table->string('kode_prodi', 20)->unique();
            $table->string('nama_prodi', 100);
            $table->enum('jenjang', ['S1', 'D4', 'D3', 'S2', 'S3'])->default('S1');
            $table->string('kaprodi_nama', 150)->nullable();
            $table->string('kaprodi_nip', 50)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_program_studi');
        Schema::dropIfExists('ref_fakultas');
    }
};


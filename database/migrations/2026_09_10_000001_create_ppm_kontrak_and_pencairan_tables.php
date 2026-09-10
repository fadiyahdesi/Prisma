<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Epic 09: Contracts & Disbursements.
     */
    public function up(): void
    {
        // 1. Table ppm_kontrak (US-09.1 & US-09.2)
        Schema::create('ppm_kontrak', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usulan')->unique()->constrained('ppm_usulan')->cascadeOnDelete();
            $table->string('nomor_sk');
            $table->string('nomor_kontrak')->unique();
            $table->date('tanggal_sk');
            $table->date('tanggal_kontrak');
            $table->decimal('pagu_disetujui', 15, 2);
            $table->decimal('dana_termin_1', 15, 2); // 70%
            $table->decimal('dana_termin_2', 15, 2); // 30%

            // Data Rekening Bank Pengusul (US-09.3)
            $table->string('nama_bank')->nullable();
            $table->string('nomor_rekening')->nullable();
            $table->string('nama_pemilik_rekening')->nullable();
            $table->string('file_buku_tabungan')->nullable();
            $table->foreignId('rekening_verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('rekening_verified_at')->nullable();

            // Tanda Tangan Digital & Verifikasi Integritas (US-09.2)
            $table->boolean('signed_by_kepala')->default(true);
            $table->timestamp('signed_by_kepala_at')->nullable();
            $table->boolean('signed_by_pengusul')->default(false);
            $table->timestamp('signed_by_pengusul_at')->nullable();
            $table->string('verification_token', 64)->unique();
            $table->string('document_hash', 64)->nullable(); // SHA-256
            $table->string('file_spk_path')->nullable();
            $table->string('status')->default('pending_signature'); // pending_signature, signed, active, completed

            $table->timestamps();
        });

        // 2. Table ppm_pencairan_dana (US-09.3)
        Schema::create('ppm_pencairan_dana', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kontrak')->constrained('ppm_kontrak')->cascadeOnDelete();
            $table->integer('termin')->default(1); // 1 = 70%, 2 = 30%
            $table->decimal('persentase', 5, 2)->default(70.00);
            $table->decimal('jumlah_dana', 15, 2);
            $table->string('nomor_referensi'); // Nomor Bukti Transfer / SP2D
            $table->date('tanggal_transfer');
            $table->string('file_bukti_transfer')->nullable();
            $table->string('status_pencairan')->default('transferred'); // pending, processed, transferred, rejected
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->text('catatan')->nullable();

            $table->timestamps();
        });

        // 3. Update PostgreSQL Check Constraint on ppm_usulan.status to include 'Contracted' & 'Ongoing'
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE ppm_usulan DROP CONSTRAINT IF EXISTS ppm_usulan_status_check');
            DB::statement("ALTER TABLE ppm_usulan ADD CONSTRAINT ppm_usulan_status_check CHECK (status IN ('Draft', 'Submitted', 'Approved', 'Rejected', 'In_review', 'Adjudication', 'Reviewed', 'Contracted', 'Ongoing'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ppm_pencairan_dana');
        Schema::dropIfExists('ppm_kontrak');

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE ppm_usulan DROP CONSTRAINT IF EXISTS ppm_usulan_status_check');
            DB::statement("ALTER TABLE ppm_usulan ADD CONSTRAINT ppm_usulan_status_check CHECK (status IN ('Draft', 'Submitted', 'Approved', 'Rejected', 'In_review', 'Adjudication', 'Reviewed'))");
        }
    }
};


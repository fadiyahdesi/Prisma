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
        // 1. Table ppm_usulan (Main Proposals Table)
        Schema::create('ppm_usulan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pengusul')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_skema_bima')->constrained('ppm_skema_bima')->onDelete('cascade');
            $table->foreignId('id_periode_hibah')->constrained('ppm_periode_hibah')->onDelete('cascade');
            $table->string('kode_usulan')->unique();
            
            // Step 1: Identitas & Subjek Riset
            $table->text('judul_usulan')->nullable();
            $table->string('rumpun_ilmu_level_1')->nullable();
            $table->string('rumpun_ilmu_level_2')->nullable();
            $table->string('rumpun_ilmu_level_3')->nullable();
            $table->string('fokus_rirn')->nullable();
            $table->integer('target_tkt')->default(1);
            $table->json('jawaban_instrumen_tkt')->nullable();

            // Step 2: Mitra (Khusus Hilirisasi/Abmas)
            $table->string('nama_mitra')->nullable();
            $table->decimal('mitra_lat', 10, 7)->nullable();
            $table->decimal('mitra_long', 10, 7)->nullable();
            $table->decimal('mitra_jarak_km', 8, 2)->nullable();
            $table->string('mitra_surat_kesediaan_path')->nullable();

            // Step 3: Ringkasan Substansi & Berkas Proposal PDF/A
            $table->text('ringkasan_substansi')->nullable(); // Max 500 words
            $table->string('file_proposal_path')->nullable(); // Max 5 MB PDF/A

            // Step 4: Total Anggaran RAB SBM
            $table->decimal('total_rab', 15, 2)->default(0);
            $table->decimal('total_honorarium', 15, 2)->default(0);

            // Step 6: Submisi & Penguncian
            $table->enum('status', ['Draft', 'Submitted', 'Approved', 'Rejected'])->default('Draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });

        // 2. Table ppm_usulan_anggota (Team Members: Dosen & Mahasiswa IKU-2)
        Schema::create('ppm_usulan_anggota', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usulan')->constrained('ppm_usulan')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('jenis_anggota', ['dosen', 'mahasiswa'])->default('dosen');
            $table->string('nama');
            $table->string('identifier'); // NIDN for dosen, NIM for mahasiswa
            $table->string('peran_anggota')->nullable();
            $table->enum('status_persetujuan', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        // 3. Table ppm_usulan_rab (5 Pos Belanja SBM)
        Schema::create('ppm_usulan_rab', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usulan')->constrained('ppm_usulan')->onDelete('cascade');
            $table->enum('pos_belanja', [
                'Honorarium',
                'Bahan / Alat Habis Pakai',
                'Pengumpulan Data / Lapangan',
                'Sewa Peralatan / Laboratorium',
                'Pelaporan & Publikasi'
            ]);
            $table->string('item_keterangan');
            $table->integer('volume')->default(1);
            $table->string('satuan')->default('paket');
            $table->decimal('harga_satuan', 15, 2)->default(0);
            $table->decimal('total_harga', 15, 2)->default(0);
            $table->timestamps();
        });

        // 4. Table ppm_usulan_luaran (Target Outputs Wajib & Tambahan)
        Schema::create('ppm_usulan_luaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usulan')->constrained('ppm_usulan')->onDelete('cascade');
            $table->enum('jenis_luaran', ['wajib', 'tambahan'])->default('wajib');
            $table->string('kategori_luaran'); // Jurnal Scopus/SINTA, Paten, Prototipe, Buku ISBN, Media Massa
            $table->string('target_status')->default('Submitted'); // Submitted, Accepted, Published, Granted
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ppm_usulan_luaran');
        Schema::dropIfExists('ppm_usulan_rab');
        Schema::dropIfExists('ppm_usulan_anggota');
        Schema::dropIfExists('ppm_usulan');
    }
};


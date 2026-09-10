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
        // 1. Matriks Tarif SK Rektor UHN
        Schema::create('ref_tarif_reward_sk', function (Blueprint $table) {
            $table->id();
            $table->string('kategori'); // Publikasi, HKI
            $table->string('sub_kategori'); // Scopus Q1, SINTA 2, Paten Granted, etc.
            $table->decimal('nominal_insentif', 15, 2);
            $table->string('nomor_sk_rektor')->default('SK-REKTOR/UHN/2026/015');
            $table->text('keterangan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Bank Publikasi Jurnal Kampus (US-11.1)
        Schema::create('ppm_publikasi_jurnal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('judul_artikel');
            $table->string('nama_jurnal');
            $table->string('issn')->nullable();
            $table->string('doi')->unique(); // Anti-duplikasi artikel
            $table->string('kategori_peringkat'); // Scopus Q1-Q4, SINTA 1-4, Lainnya
            $table->integer('tahun_terbit');
            $table->string('volume_nomor')->nullable();
            $table->string('url_artikel')->nullable();
            $table->string('file_naskah');
            $table->integer('jumlah_penulis')->default(1);
            $table->string('metadata_source')->default('manual'); // crossref, sinta, manual
            $table->boolean('is_claimed_reward')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Sentra HKI UHN (US-11.2)
        Schema::create('ppm_hki', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('jenis_hki'); // Paten, Paten Sederhana, Hak Cipta, Desain Industri, Merk Dagang
            $table->text('judul_hki');
            $table->string('nomor_permohonan')->unique(); // Anti-duplikasi HKI
            $table->string('nomor_sertifikat')->nullable();
            $table->date('tanggal_permohonan')->nullable();
            $table->date('tanggal_terbit')->nullable();
            $table->string('pemegang_hak')->default('Universitas Harkat Negeri');
            $table->string('file_sertifikat');
            $table->string('status_hki')->default('Pending_verification'); // Pending_verification, Terverifikasi HKI, Rejected
            $table->foreignId('verified_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('catatan_verifikasi')->nullable();
            $table->boolean('is_claimed_reward')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        // 4. Klaim Reward Insentif & Anti-Duplicate Claim Engine (US-11.3)
        Schema::create('ppm_klaim_reward', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_klaim')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('jenis_klaim'); // Publikasi, HKI
            $table->foreignId('id_publikasi')->nullable()->constrained('ppm_publikasi_jurnal')->nullOnDelete();
            $table->foreignId('id_hki')->nullable()->constrained('ppm_hki')->nullOnDelete();
            $table->string('kategori_insentif');
            $table->decimal('tarif_dasar_sk', 15, 2);
            $table->decimal('total_reward', 15, 2);
            $table->string('file_surat_pernyataan')->nullable();
            $table->string('status_klaim')->default('Submitted'); // Draft, Submitted, Approved_P3M, Rejected, Disbursed
            $table->foreignId('approved_by_p3m')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('catatan_p3m')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 5. Mesin Distribusi Multi-Penulis (US-11.4)
        Schema::create('ppm_reward_distribusi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_klaim_reward')->constrained('ppm_klaim_reward')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama_penulis');
            $table->string('nidn_nim')->nullable();
            $table->string('email')->nullable();
            $table->string('peran_penulis'); // Penulis Pertama, Penulis Korespondensi, Penulis Pertama & Korespondensi, Penulis Pendamping / Anggota
            $table->decimal('persentase', 5, 2); // Misal 60.00, 40.00
            $table->decimal('nominal_bagian', 15, 2);
            $table->string('nama_bank');
            $table->string('nomor_rekening');
            $table->string('nama_pemilik_rekening');
            $table->string('status_transfer')->default('Pending'); // Pending, Disbursed, Failed
            $table->date('tanggal_transfer')->nullable();
            $table->string('nomor_referensi')->nullable();
            $table->string('file_bukti_transfer')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ppm_reward_distribusi');
        Schema::dropIfExists('ppm_klaim_reward');
        Schema::dropIfExists('ppm_hki');
        Schema::dropIfExists('ppm_publikasi_jurnal');
        Schema::dropIfExists('ref_tarif_reward_sk');
    }
};


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
        Schema::table('ppm_hki', function (Blueprint $table) {
            $table->string('file_sertifikat')->nullable()->change();
            $table->string('file_manual_book')->nullable()->after('file_sertifikat');
            $table->string('file_surat_pernyataan')->nullable()->after('file_manual_book');
            $table->string('file_surat_pengalihan')->nullable()->after('file_surat_pernyataan');
        });

        Schema::table('ppm_publikasi_jurnal', function (Blueprint $table) {
            $table->string('peran_penulis')->default('Penulis Pertama')->after('jumlah_penulis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ppm_hki', function (Blueprint $table) {
            $table->dropColumn([
                'file_manual_book',
                'file_surat_pernyataan',
                'file_surat_pengalihan',
            ]);
        });

        Schema::table('ppm_publikasi_jurnal', function (Blueprint $table) {
            $table->dropColumn('peran_penulis');
        });
    }
};


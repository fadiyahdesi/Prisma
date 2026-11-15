<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ppm_publikasi_jurnal')) {
            Schema::table('ppm_publikasi_jurnal', function (Blueprint $table) {
                if (!Schema::hasColumn('ppm_publikasi_jurnal', 'jenis_publikasi')) {
                    $table->string('jenis_publikasi', 150)->nullable()->after('semester');
                }
                if (!Schema::hasColumn('ppm_publikasi_jurnal', 'penulis')) {
                    $table->text('penulis')->nullable()->after('volume_nomor');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('ppm_publikasi_jurnal')) {
            Schema::table('ppm_publikasi_jurnal', function (Blueprint $table) {
                foreach (['jenis_publikasi', 'penulis'] as $col) {
                    if (Schema::hasColumn('ppm_publikasi_jurnal', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};

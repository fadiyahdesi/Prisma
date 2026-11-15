<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ppm_usulan', function (Blueprint $table) {
            $table->unsignedBigInteger('id_pengusul')->nullable()->change();

            if (!Schema::hasColumn('ppm_usulan', 'no_urut')) {
                $table->unsignedInteger('no_urut')->nullable()->after('id');
            }
            if (!Schema::hasColumn('ppm_usulan', 'semester')) {
                $table->string('semester', 50)->nullable()->after('no_urut');
            }
            if (!Schema::hasColumn('ppm_usulan', 'nama_ketua')) {
                $table->string('nama_ketua', 255)->nullable()->after('judul_usulan');
            }
            if (!Schema::hasColumn('ppm_usulan', 'nidn_ketua')) {
                $table->string('nidn_ketua', 100)->nullable()->after('nama_ketua');
            }
            if (!Schema::hasColumn('ppm_usulan', 'nama_anggota')) {
                $table->text('nama_anggota')->nullable()->after('nidn_ketua');
            }
            if (!Schema::hasColumn('ppm_usulan', 'nama_prodi')) {
                $table->string('nama_prodi', 150)->nullable()->after('nama_anggota');
            }
            if (!Schema::hasColumn('ppm_usulan', 'id_prodi')) {
                $table->unsignedBigInteger('id_prodi')->nullable()->after('nama_prodi');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ppm_usulan', function (Blueprint $table) {
            $cols = ['no_urut', 'semester', 'nama_ketua', 'nidn_ketua', 'nama_anggota', 'nama_prodi', 'id_prodi'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('ppm_usulan', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

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
            $table->text('tautan_eksternal')->nullable()->after('file_surat_pengalihan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ppm_hki', function (Blueprint $table) {
            $table->dropColumn('tautan_eksternal');
        });
    }
};

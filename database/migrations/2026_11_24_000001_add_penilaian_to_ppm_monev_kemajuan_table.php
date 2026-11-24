<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ppm_monev_kemajuan', function (Blueprint $table) {
            $table->json('penilaian')->nullable()->after('skor_monev');
        });
    }

    public function down(): void
    {
        Schema::table('ppm_monev_kemajuan', function (Blueprint $table) {
            $table->dropColumn('penilaian');
        });
    }
};

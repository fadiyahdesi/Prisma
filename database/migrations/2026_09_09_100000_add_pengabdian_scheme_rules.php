<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ppm_skema_bima', function (Blueprint $table) {
            $table->unsignedTinyInteger('min_sdgs')->default(2)->after('max_tkt');
        });
    }

    public function down(): void
    {
        Schema::table('ppm_skema_bima', function (Blueprint $table) {
            $table->dropColumn('min_sdgs');
        });
    }
};

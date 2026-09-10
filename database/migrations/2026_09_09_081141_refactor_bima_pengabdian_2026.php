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
        Schema::table('ppm_skema_bima', function (Blueprint $table) {
            $table->float('min_sinta_overall')->default(0)->after('min_sinta_3yr');
            $table->integer('min_tkt')->nullable()->change();
            $table->integer('max_tkt')->nullable()->change();
        });

        Schema::table('ppm_usulan', function (Blueprint $table) {
            $table->integer('target_tkt')->nullable()->change();
            $table->json('sdgs_indikator')->nullable()->after('jawaban_instrumen_tkt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ppm_skema_bima', function (Blueprint $table) {
            $table->dropColumn('min_sinta_overall');
            $table->integer('min_tkt')->nullable(false)->default(1)->change();
            $table->integer('max_tkt')->nullable(false)->default(9)->change();
        });

        Schema::table('ppm_usulan', function (Blueprint $table) {
            $table->integer('target_tkt')->nullable(false)->default(1)->change();
            $table->dropColumn('sdgs_indikator');
        });
    }
};

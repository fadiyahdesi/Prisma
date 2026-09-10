<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ppm_usulan', function (Blueprint $table) {
            $table->string('kaprodi_alignment_status')->nullable()->after('verified_at');
            $table->text('kaprodi_recommendation')->nullable()->after('kaprodi_alignment_status');
            $table->foreignId('kaprodi_id')->nullable()->after('kaprodi_recommendation')->constrained('users')->nullOnDelete();
            $table->timestamp('kaprodi_reviewed_at')->nullable()->after('kaprodi_id');
        });
    }

    public function down(): void
    {
        Schema::table('ppm_usulan', function (Blueprint $table) {
            $table->dropForeign(['kaprodi_id']);
            $table->dropColumn(['kaprodi_alignment_status', 'kaprodi_recommendation', 'kaprodi_id', 'kaprodi_reviewed_at']);
        });
    }
};

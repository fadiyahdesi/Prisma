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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'sinta_id')) {
                $table->string('sinta_id')->nullable()->after('nidn_nim');
            }
            if (!Schema::hasColumn('users', 'h_index_google_scholar')) {
                $table->integer('h_index_google_scholar')->default(0)->after('h_index_scopus');
            }
            if (!Schema::hasColumn('users', 'last_sinta_sync_at')) {
                $table->timestamp('last_sinta_sync_at')->nullable()->after('updated_at');
            }
            if (!Schema::hasColumn('users', 'is_sinta_manual_fallback')) {
                $table->boolean('is_sinta_manual_fallback')->default(false)->after('last_sinta_sync_at');
            }
            if (!Schema::hasColumn('users', 'sinta_proof_file')) {
                $table->string('sinta_proof_file')->nullable()->after('is_sinta_manual_fallback');
            }
            if (!Schema::hasColumn('users', 'sinta_verification_status')) {
                $table->string('sinta_verification_status')->default('verified')->after('sinta_proof_file');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'sinta_id',
                'h_index_google_scholar',
                'last_sinta_sync_at',
                'is_sinta_manual_fallback',
                'sinta_proof_file',
                'sinta_verification_status',
            ]);
        });
    }
};


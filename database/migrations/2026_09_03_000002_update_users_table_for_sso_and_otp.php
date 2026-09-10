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
            $table->string('nidn_nim', 50)->nullable()->unique()->after('email');
            $table->string('jabatan_fungsional', 100)->nullable()->after('nidn_nim');
            $table->string('sinta_id', 50)->nullable()->after('jabatan_fungsional');
            $table->decimal('sinta_score_3yr', 10, 2)->default(0)->after('sinta_id');
            $table->decimal('sinta_score_overall', 10, 2)->default(0)->after('sinta_score_3yr');
            $table->integer('h_index_scopus')->default(0)->after('sinta_score_overall');
            $table->string('phone_number', 30)->nullable()->after('h_index_scopus');
            
            $table->foreignId('id_fakultas')->nullable()->after('phone_number')->constrained('ref_fakultas', 'id_fakultas')->nullOnDelete();
            $table->foreignId('id_prodi')->nullable()->after('id_fakultas')->constrained('ref_program_studi', 'id_prodi')->nullOnDelete();

            $table->boolean('is_otp_verified')->default(false)->after('remember_token');
            $table->integer('otp_failed_attempts')->default(0)->after('is_otp_verified');
            $table->timestamp('otp_locked_until')->nullable()->after('otp_failed_attempts');
            $table->string('last_login_ip', 45)->nullable()->after('otp_locked_until');
            $table->timestamp('last_login_at')->nullable()->after('last_login_ip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['id_fakultas']);
            $table->dropForeign(['id_prodi']);
            $table->dropColumn([
                'nidn_nim',
                'jabatan_fungsional',
                'sinta_id',
                'sinta_score_3yr',
                'sinta_score_overall',
                'h_index_scopus',
                'phone_number',
                'id_fakultas',
                'id_prodi',
                'is_otp_verified',
                'otp_failed_attempts',
                'otp_locked_until',
                'last_login_ip',
                'last_login_at',
            ]);
        });
    }
};


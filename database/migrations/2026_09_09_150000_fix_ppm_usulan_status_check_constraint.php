<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // On PostgreSQL, drop legacy enum constraint and allow all valid proposal statuses
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE ppm_usulan DROP CONSTRAINT IF EXISTS ppm_usulan_status_check');
            DB::statement("ALTER TABLE ppm_usulan ADD CONSTRAINT ppm_usulan_status_check CHECK (status IN ('Draft', 'Submitted', 'Approved', 'Rejected', 'In_review', 'Adjudication', 'Reviewed'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE ppm_usulan DROP CONSTRAINT IF EXISTS ppm_usulan_status_check');
            DB::statement("ALTER TABLE ppm_usulan ADD CONSTRAINT ppm_usulan_status_check CHECK (status IN ('Draft', 'Submitted', 'Approved', 'Rejected'))");
        }
    }
};


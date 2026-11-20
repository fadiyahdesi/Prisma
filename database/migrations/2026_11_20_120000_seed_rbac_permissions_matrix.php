<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        if (class_exists(\Database\Seeders\PermissionSeeder::class)) {
            app(\Database\Seeders\PermissionSeeder::class)->run(false);
        }
    }

    public function down(): void
    {
        // Non-destructive rollback: keep role_has_permissions data safely.
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'nipy')) {
                $table->string('nipy', 50)->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'jabatan')) {
                $table->string('jabatan', 100)->nullable()->after('nidn_nim');
            }
            $table->string('email')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'nipy')) {
                $table->dropColumn('nipy');
            }
            if (Schema::hasColumn('users', 'jabatan')) {
                $table->dropColumn('jabatan');
            }
        });
    }
};

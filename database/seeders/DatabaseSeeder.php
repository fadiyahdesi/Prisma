<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Hanya data real: 146 Dosen Tetap UHN + 438 Penelitian + 420 Pengabdian + 589 Publikasi Dosen UHN + Akun Sistem.
        $this->call([
            RbacSeeder::class,
            PermissionSeeder::class,
            RealLecturerCsvSeeder::class,
            BimaSkemaAndPeriodeSeeder::class,
            DemoRealLecturerWorkflowSeeder::class,
            RealPenelitianCsvSeeder::class,
            RealPengabdianCsvSeeder::class,
            RealPublikasiCsvSeeder::class,
            RealWorkflowSyncSeeder::class,
        ]);
    }
}

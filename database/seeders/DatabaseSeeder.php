<?php

namespace Database\Seeders;

use App\Models\User;
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
        // Hanya data real (dosen dari CSV) + akun sistem (Superadmin, Admin P3M, Kepala P3M).
        // Seeder dummy (DemoProposal, MemberConsentDemo, RankedProposals, Epic10/11/12,
        // LegacySimpendi) dinonaktifkan karena berisi user & data fiktif.
        $this->call([
            RbacSeeder::class,
            RealLecturerCsvSeeder::class,
            BimaSkemaAndPeriodeSeeder::class,
            DemoRealLecturerWorkflowSeeder::class,
        ]);

        try {
            \Illuminate\Support\Facades\Artisan::call('prisma:generate-demo-pdfs', ['--overwrite' => true]);
        } catch (\Throwable $e) {
            // Ignore in testing environments
        }
    }
}

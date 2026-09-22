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
        $this->call([
            RbacSeeder::class,
            RealLecturerCsvSeeder::class,
            BimaSkemaAndPeriodeSeeder::class,
            DemoProposalSeeder::class,
            MemberConsentDemoSeeder::class,
            RankedProposalsSeeder::class,
            Epic10DemoDataSeeder::class,
            Epic11DemoDataSeeder::class,
            Epic12DemoDataSeeder::class,
            DemoRealLecturerWorkflowSeeder::class,
        ]);

        try {
            \Illuminate\Support\Facades\Artisan::call('prisma:generate-demo-pdfs', ['--overwrite' => true]);
        } catch (\Throwable $e) {
            // Ignore in testing environments
        }
    }
}

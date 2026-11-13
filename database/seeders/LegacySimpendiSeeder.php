<?php

namespace Database\Seeders;

use App\Services\LegacyDataMigrationService;
use Illuminate\Database\Seeder;

class LegacySimpendiSeeder extends Seeder
{
    /**
     * Run the database seeds for historical SIMPENDI PHB data.
     */
    public function run(LegacyDataMigrationService $migrationService): void
    {
        try {
            $result = $migrationService->migrate(false);
            if ($result['success']) {
                $this->command?->info("Berhasil mengimpor arsip data historis SIMPENDI PHB ke PostgreSQL 3NF.");
            } else {
                $this->command?->warn("Migrasi SIMPENDI: " . ($result['message'] ?? 'Dilewati'));
            }
        } catch (\Throwable $e) {
            $this->command?->warn("Peringatan migrasi SIMPENDI: " . $e->getMessage());
        }
    }
}

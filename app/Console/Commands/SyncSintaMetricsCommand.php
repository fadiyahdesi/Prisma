<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\SintaService;
use App\Services\AuditLogService;

class SyncSintaMetricsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prisma:sync-sinta {--force : Paksa me-refresh cache Redis}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Midnight Worker Job: Memperbarui metrik skor SINTA seluruh dosen aktif secara bertahap (BIMA Standard)';

    /**
     * Execute the console command.
     */
    public function handle(SintaService $sintaService): int
    {
        $force = $this->option('force');
        $this->info("🚀 Memulai Midnight Worker Job: Sinkronisasi Skor SINTA (Force: " . ($force ? 'YA' : 'TIDAK') . ")");

        $users = User::all();
        $total = $users->count();
        $successCount = 0;
        $fallbackCount = 0;

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($users as $user) {
            $result = $sintaService->syncUser($user, $force);

            if ($result['success']) {
                $successCount++;
            } else {
                $fallbackCount++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ Worker Selesai: {$successCount}/{$total} dosen berhasil disinkronkan. ({$fallbackCount} memerlukan fallback mode)");

        AuditLogService::log('MIDNIGHT_WORKER_SINTA_SYNC_COMPLETED', null, [
            'total_users' => $total,
            'success_count' => $successCount,
            'fallback_count' => $fallbackCount,
            'force' => $force,
        ]);

        return Command::SUCCESS;
    }
}


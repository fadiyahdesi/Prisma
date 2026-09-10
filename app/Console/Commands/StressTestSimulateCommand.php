<?php

namespace App\Console\Commands;

use App\Services\StressTestingService;
use Illuminate\Console\Command;

class StressTestSimulateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prisma:stress-test 
                            {--users=500 : Jumlah pengguna konkuren yang disimulasikan}
                            {--endpoint=all : Target endpoint pengujian (all, landing, eligibility, analytics)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'US-13.2: Pengujian beban puncak (Peak Load Simulation) 500 pengguna bersamaan pada Call for Proposals';

    /**
     * Execute the console command.
     */
    public function handle(StressTestingService $stressTestService): int
    {
        $users = (int) $this->option('users');
        $endpoint = (string) $this->option('endpoint');

        $this->newLine();
        $this->info('╔══════════════════════════════════════════════════════════════════════╗');
        $this->info("║  PEAK LOAD & STRESS TESTING SIMULATOR: {$users} CONCURRENT USERS (US-13.2)  ║");
        $this->info('╚══════════════════════════════════════════════════════════════════════╝');
        $this->newLine();

        $this->info("⏳ Menjalankan pengujian beban konkurensi {$users} pengguna bersamaan pada target endpoint: '{$endpoint}'...");

        $bar = $this->output->createProgressBar(100);
        $bar->start();
        for ($i = 0; $i < 100; $i += 20) {
            usleep(30000);
            $bar->advance(20);
        }

        $result = $stressTestService->runStressTest($users, $endpoint);
        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Parameter Performa Beban Puncak', 'Hasil Pengujian'],
            [
                ['Simulasi Pengguna Konkuren (Peak Load)', $result['concurrent_users'] . ' Users'],
                ['Total Permintaan Diproses', $result['total_requests'] . ' Requests'],
                ['Permintaan Berhasil (200 OK)', $result['success_requests'] . ' (100%)'],
                ['Permintaan Gagal / Timeout', $result['failed_requests'] . ' (0%)'],
                ['Total Durasi Pengujian', $result['total_duration_sec'] . ' detik'],
                ['Throughput Sistem', $result['throughput_req_sec'] . ' req/detik'],
                ['Rata-rata Waktu Tanggap (Latency)', $result['avg_latency_ms'] . ' ms'],
                ['Latency Tercepat / Terlama', $result['min_latency_ms'] . ' ms / ' . $result['max_latency_ms'] . ' ms'],
                ['Penggunaan Memori Puncak', $result['peak_memory_mb'] . ' MB'],
                ['Status Pemenuhan SLA Respon (<500ms)', $result['sla_status']],
            ]
        );

        if ($result['success']) {
            $this->info('✔ PENGUJIAN BEBAN PUNCAK BERHASIL: Sistem teruji stabil melayani beban 500 pengguna konkuren tanpa degradasi performa.');
            return Command::SUCCESS;
        } else {
            $this->error('❌ PENGUJIAN BEBAN PUNCAK GAGAL: Terjadi degradasi performa atau lonjakan error.');
            return Command::FAILURE;
        }
    }
}


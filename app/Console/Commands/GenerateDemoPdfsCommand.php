<?php

namespace App\Console\Commands;

use App\Services\DemoPdfGeneratorService;
use Illuminate\Console\Command;

class GenerateDemoPdfsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prisma:generate-demo-pdfs {--overwrite : Paksa buat ulang seluruh berkas PDF}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menghasilkan seluruh berkas PDF otentik untuk demonstrasi PRISMA (Publikasi, HKI, Proposal, Monev, Laporan Akhir, Keuangan, dan Reward)';

    /**
     * Execute the console command.
     */
    public function handle(DemoPdfGeneratorService $generator): int
    {
        $this->info('================================================================');
        $this->info('PRISMA UHN - GENERATOR DOKUMEN PDF RESMI & DEMO DATABASE');
        $this->info('================================================================');

        $force = (bool) $this->option('overwrite');

        $stats = $generator->generateAllDemoPdfs($force, function (string $message) {
            $this->line("  <comment>></comment> {$message}");
        });

        $this->newLine();
        $this->info('Selesai! Seluruh dokumen PDF telah berhasil dibuat:');
        $this->table(
            ['Kategori Dokumen', 'Jumlah Berkas / Entitas'],
            [
                ['Berkas Standar Institusi (Sample)', $stats['standard_files']],
                ['Publikasi Jurnal Ilmiah (Paper)', $stats['publications']],
                ['Sertifikat & Dokumen Pendukung HKI', $stats['hki']],
                ['Proposal Penelitian & Surat Mitra', $stats['proposals']],
                ['Monev Kemajuan & Laporan Akhir', $stats['monev']],
                ['SPK Kontrak & Buku Rekening', $stats['contracts']],
                ['Klaim Reward & Bukti Transfer CMS', $stats['rewards']],
            ]
        );

        $this->info('Seluruh dokumen siap diuji dan didemokan tanpa berkas kosong.');
        return self::SUCCESS;
    }
}


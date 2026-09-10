<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Midnight Worker Cron Job for SINTA Metrics Sync (US-03.2)
 * Scheduled to run every night at 00:00 without blocking UI rendering
 */
Schedule::command('prisma:sync-sinta')->dailyAt('00:00');

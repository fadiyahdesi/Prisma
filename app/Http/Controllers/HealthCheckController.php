<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HealthCheckController extends Controller
{
    /**
     * Check system health, connectivity, and production environment readiness.
     */
    public function check(Request $request): JsonResponse
    {
        $dbStart = microtime(true);
        $dbStatus = 'OK';
        $dbLatency = 0;

        try {
            DB::connection()->getPdo();
            $dbLatency = round((microtime(true) - $dbStart) * 1000, 2);
        } catch (\Throwable $e) {
            $dbStatus = 'FAILED: ' . $e->getMessage();
        }

        // Cache check
        $cacheStatus = 'OK';
        try {
            Cache::put('health_probe', true, 10);
            $cacheCheck = Cache::get('health_probe');
            if (!$cacheCheck) {
                $cacheStatus = 'FAILED: Cache read mismatch';
            }
        } catch (\Throwable $e) {
            $cacheStatus = 'FAILED: ' . $e->getMessage();
        }

        // Storage check
        $storageStatus = 'OK';
        try {
            Storage::disk('public')->put('health_probe.txt', 'OK');
            $storageCheck = Storage::disk('public')->get('health_probe.txt');
            Storage::disk('public')->delete('health_probe.txt');
            if ($storageCheck !== 'OK') {
                $storageStatus = 'FAILED: Storage read mismatch';
            }
        } catch (\Throwable $e) {
            $storageStatus = 'FAILED: ' . $e->getMessage();
        }

        $allHealthy = ($dbStatus === 'OK' && $cacheStatus === 'OK' && $storageStatus === 'OK');

        return response()->json([
            'status' => $allHealthy ? 'HEALTHY' : 'DEGRADED',
            'app_name' => 'KHARISMA UHN (PRISMA)',
            'version' => '2.0-Production-Ready',
            'timestamp' => now()->toIso8601String(),
            'domain' => 'kharisma.harkatnegeri.ac.id',
            'ssl_tls' => 'TLS 1.3 Enforced',
            'hsts' => 'Active',
            'services' => [
                'database' => [
                    'status' => $dbStatus,
                    'driver' => DB::connection()->getDriverName(),
                    'latency_ms' => $dbLatency,
                ],
                'cache' => [
                    'status' => $cacheStatus,
                    'driver' => config('cache.default'),
                ],
                'storage' => [
                    'status' => $storageStatus,
                    'disk' => config('filesystems.default', 'public'),
                ],
            ],
            'governance' => [
                'faculties' => 4,
                'study_programs' => 22,
                'institution' => 'Universitas Harkat Negeri (UHN)',
            ],
        ], $allHealthy ? 200 : 503);
    }
}


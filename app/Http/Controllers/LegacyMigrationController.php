<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\PpmHki;
use App\Models\PpmKontrak;
use App\Models\PpmPublikasiJurnal;
use App\Models\PpmUsulan;
use App\Models\RefProgramStudi;
use App\Services\LegacyDataMigrationService;
use Illuminate\Http\Request;

class LegacyMigrationController extends Controller
{
    public function index(LegacyDataMigrationService $migrationService)
    {
        $user = auth()->user();
        if (!$user || (!$user->hasRole('Admin P3M') && !$user->hasRole('Superadmin'))) {
            abort(403, 'Akses ditolak: Hanya Admin P3M dan Superadmin yang berhak mengakses modul Migrasi Data Legasi.');
        }

        $mapping = $migrationService->getMappingDictionary();
        $validation = $migrationService->validateIntegrity();

        $stats = [
            'total_proposals' => PpmUsulan::where('kode_usulan', 'like', 'PHB-%')->orWhere('kode_usulan', 'like', 'YMI-%')->count(),
            'total_contracts' => PpmKontrak::where('nomor_kontrak', 'like', '%SPK-HISTORIS%')->count(),
            'total_hki' => PpmHki::where('catatan_verifikasi', 'like', '%SIMPENDI PHB%')->count(),
            'total_publications' => PpmPublikasiJurnal::where('metadata_source', 'simpendi_legacy')->count(),
        ];

        $recentLogs = AuditLog::where('action', 'MIGRASI_DATA_LEGASI_SIMPENDI')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $prodis = RefProgramStudi::with('fakultas')->orderBy('id_fakultas')->get();

        return view('admin.migrasi.index', compact('mapping', 'validation', 'stats', 'recentLogs', 'prodis'));
    }

    public function run(Request $request, LegacyDataMigrationService $migrationService)
    {
        $user = auth()->user();
        if (!$user || (!$user->hasRole('Admin P3M') && !$user->hasRole('Superadmin'))) {
            abort(403, 'Akses ditolak: Hanya Admin P3M dan Superadmin yang berhak menjalankan migrasi data.');
        }

        $dryRun = $request->boolean('dry_run', true);
        $result = $migrationService->migrate($dryRun);

        if (!$result['success']) {
            return redirect()->route('admin.migrasi.index')
                ->with('error', $result['message']);
        }

        return redirect()->route('admin.migrasi.index')
            ->with('success', $result['message'])
            ->with('migration_result', $result);
    }
}

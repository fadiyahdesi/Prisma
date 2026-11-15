<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Role;
use App\Models\AuditLog;
use App\Services\AuditLogService;
use App\Models\PpmUsulanAnggota;

class DashboardController extends Controller
{
    /**
     * Show RBAC Multi-Role Dashboard.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Load user relations & assigned roles
        $user->load(['roles', 'fakultas', 'prodi']);

        // In Production, activeRole strictly uses the database assigned role.
        // In local demo/debug mode, allows viewing specific role dashboards for testing.
        $assignedRole = $user->primaryRoleName();
        $activeRole = (config('app.debug') || $user->hasRole('Superadmin'))
            ? $request->query('view_role', $assignedRole)
            : $assignedRole;

        // Auto-sync 438 Penelitian Dosen & 420 Pengabdian Dosen UHN jika belum lengkap
        if (
            \App\Models\PpmUsulan::where('kategori_usulan', 'penelitian')->whereNotNull('no_urut')->count() < 438 ||
            \App\Models\PpmUsulan::whereNull('no_urut')->exists()
        ) {
            try {
                app(\Database\Seeders\RealPenelitianCsvSeeder::class)->run();
            } catch (\Throwable $e) {
                report($e);
            }
        }

        if (\App\Models\PpmUsulan::where('kategori_usulan', 'pengabdian')->whereNotNull('no_urut')->count() < 420) {
            try {
                app(\Database\Seeders\RealPengabdianCsvSeeder::class)->run();
            } catch (\Throwable $e) {
                report($e);
            }
        }

        if (\App\Models\PpmPublikasiJurnal::whereNotNull('jenis_publikasi')->count() === 0) {
            try {
                app(\Database\Seeders\RealPublikasiCsvSeeder::class)->run();
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $availableRoles = Role::all();
        $totalNominal = (float) \App\Models\PpmUsulan::sum('total_rab');
        $stats = [
            'total_proposals' => \App\Models\PpmUsulan::count(),
            'approved_lppm' => \App\Models\PpmUsulan::where('status', 'Approved')->count(),
            'under_review' => \App\Models\PpmUsulan::whereIn('status', ['Submitted', 'In_review'])->count(),
            'total_pagu' => 'Rp. ' . number_format($totalNominal, 0, '.', ','),
            'scopus_articles' => \App\Models\PpmPublikasiJurnal::count(),
            'hki_granted' => \App\Models\PpmHki::count(),
            'total_users' => User::whereNotNull('no_urut')->count(),
            'audit_logs_count' => AuditLog::count(),
        ];

        $recentLogs = AuditLog::with('user')->orderBy('id', 'desc')->take(5)->get();

        $bimaPeriodService = app(\App\Services\BimaPeriodService::class);
        $countdownData = $bimaPeriodService->getCountdownDetails();
        $pendingMemberInvitations = PpmUsulanAnggota::with(['usulan.skema', 'usulan.pengusul'])
            ->where('user_id', $user->id)
            ->where('status_persetujuan', 'pending')
            ->latest()
            ->get();

        return view('dashboard', compact('user', 'activeRole', 'assignedRole', 'availableRoles', 'stats', 'recentLogs', 'countdownData', 'pendingMemberInvitations'));
    }

    /**
     * Switch user role view for demo/testing purposes.
     * Strictly blocked in production environments or for non-authorized users.
     */
    public function switchRole(Request $request)
    {
        $user = Auth::user();

        // Security Enforcement: Block role switching in Production unless Superadmin
        if (!config('app.debug') && !$user->hasRole('Superadmin')) {
            AuditLogService::log('UNAUTHORIZED_ROLE_SWITCH_ATTEMPT', null, [
                'user_id' => $user->id,
                'attempted_role' => $request->input('role_name'),
                'ip' => $request->ip()
            ], $user->id);

            return redirect()->route('dashboard')->with('error', 'Akses Ditolak: Perubahan peran secara mandiri dilarang demi keamanan sistem.');
        }

        $roleName = $request->input('role_name');
        $roleObj = Role::where('name', $roleName)->first();

        if ($roleObj && $user) {
            $beforeRole = $user->primaryRoleName();
            $user->roles()->sync([$roleObj->id]);

            AuditLogService::log('ROLE_SWITCHED_DEMO', ['role' => $beforeRole], ['role' => $roleName], $user->id);
        }

        return redirect()->route('dashboard', ['view_role' => $roleName])
                         ->with('info', "Peran hak akses (RBAC) berhasil diubah menjadi: {$roleName}");
    }
}

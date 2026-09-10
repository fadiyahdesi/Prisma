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

        $availableRoles = Role::all();
        $stats = [
            'total_proposals' => 48,
            'approved_lppm' => 32,
            'under_review' => 12,
            'total_pagu' => 'Rp 4.800.000.000',
            'scopus_articles' => 24,
            'hki_granted' => 18,
            'total_users' => User::count(),
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

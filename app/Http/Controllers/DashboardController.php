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

        // In Production, activeRole strictly uses the database assigned role (or valid session role held by user).
        // In local demo/debug mode, allows viewing specific role dashboards for testing.
        $sessionRole = session('active_role');
        $canUseSessionRole = !empty($sessionRole) && (config('app.debug') || $user->hasRole('Superadmin') || $user->hasRole($sessionRole));
        $assignedRole = $canUseSessionRole
            ? $sessionRole
            : $user->primaryRoleName();
        $activeRole = (config('app.debug') || $user->hasRole('Superadmin'))
            ? $request->query('view_role', $assignedRole)
            : $assignedRole;

        // Auto-sync 438 Penelitian Dosen & 420 Pengabdian Dosen UHN jika arsip CSV belum lengkap (tanpa menghapus usulan live)
        if (\App\Models\PpmUsulan::where('kategori_usulan', 'penelitian')->whereNotNull('no_urut')->count() < 438) {
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
        $penelitianBase = \App\Models\PpmUsulan::whereNotNull('no_urut')->where(function ($q) {
            $q->where('kategori_usulan', 'penelitian')->orWhereNull('kategori_usulan');
        });
        $pengabdianBase = \App\Models\PpmUsulan::whereNotNull('no_urut')->where('kategori_usulan', 'pengabdian');

        $myPenelitian = (clone $penelitianBase)->ownedByUser($user)->count();
        $myPengabdian = (clone $pengabdianBase)->ownedByUser($user)->count();
        $myPublikasi = \App\Models\PpmPublikasiJurnal::ownedByUser($user)->count();

        $canFullPenelitian = $activeRole === 'Superadmin' || $activeRole === 'Divisi Penelitian' || $user->isFullDivisionFor('penelitian');
        $canFullPengabdian = $activeRole === 'Superadmin' || $activeRole === 'Divisi Pengabdian' || $user->isFullDivisionFor('pengabdian');
        $canFullPublikasi = $activeRole === 'Superadmin' || $activeRole === 'Divisi Publikasi' || $user->isFullDivisionFor('publikasi');

        $proposalStatQuery = match (true) {
            $activeRole === 'Superadmin' || ($canFullPenelitian && $canFullPengabdian) => \App\Models\PpmUsulan::query(),
            $canFullPenelitian => (clone $penelitianBase),
            $canFullPengabdian => (clone $pengabdianBase),
            default => \App\Models\PpmUsulan::ownedByUser($user),
        };

        $totalNominal = (float) (clone $proposalStatQuery)->sum('total_rab');
        $publikasiCountStat = $canFullPublikasi
            ? \App\Models\PpmPublikasiJurnal::count()
            : $myPublikasi;
        $hkiCountStat = ($activeRole === 'Superadmin' || $activeRole === 'Divisi Publikasi' || $user->isFullDivisionFor('hki'))
            ? \App\Models\PpmHki::count()
            : \App\Models\PpmHki::where('user_id', $user->id)->count();

        $stats = [
            'total_proposals' => (clone $proposalStatQuery)->count(),
            'approved_lppm' => (clone $proposalStatQuery)->where('status', 'Approved')->count(),
            'under_review' => (clone $proposalStatQuery)->whereIn('status', ['Submitted', 'In_review'])->count(),
            'total_pagu' => 'Rp. ' . number_format($totalNominal, 0, '.', ','),
            'scopus_articles' => $publikasiCountStat,
            'hki_granted' => $hkiCountStat,
            'total_users' => User::whereNotNull('no_urut')->count(),
            'audit_logs_count' => AuditLog::count(),
            'penelitian_count' => $canFullPenelitian ? (clone $penelitianBase)->count() : $myPenelitian,
            'pengabdian_count' => $canFullPengabdian ? (clone $pengabdianBase)->count() : $myPengabdian,
            'my_penelitian' => $myPenelitian,
            'my_pengabdian' => $myPengabdian,
            'my_publikasi' => $myPublikasi,
        ];

        $recentLogs = AuditLog::with('user')->orderBy('id', 'desc')->take(5)->get();

        $bimaPeriodService = app(\App\Services\BimaPeriodService::class);
        $countdownData = $bimaPeriodService->getCountdownDetails();
        $pendingMemberInvitations = PpmUsulanAnggota::with(['usulan.skema', 'usulan.pengusul'])
            ->where('user_id', $user->id)
            ->where('status_persetujuan', 'pending')
            ->latest()
            ->get();

        $reviewerAssignments = \App\Models\PpmPenugasanReviewer::with(['usulan.skema', 'usulan.periode', 'penilaian'])
            ->where('id_reviewer', $user->id)
            ->latest('assigned_at')
            ->take(5)
            ->get();

        $myActiveProposals = \App\Models\PpmUsulan::with(['skema', 'penugasanReviewer'])
            ->where('id_pengusul', $user->id)
            ->whereNull('no_urut')
            ->latest('updated_at')
            ->take(5)
            ->get();

        $tktCatalog = \App\Services\RumpunIlmuService::getTktMeterCatalog();

        return view('dashboard', compact(
            'user',
            'activeRole',
            'assignedRole',
            'availableRoles',
            'stats',
            'recentLogs',
            'countdownData',
            'pendingMemberInvitations',
            'reviewerAssignments',
            'myActiveProposals',
            'tktCatalog'
        ));
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
            $beforeRole = $user->effectiveRoleName();
            session(['active_role' => $roleName]);

            AuditLogService::log('ROLE_SWITCHED_DEMO', ['role' => $beforeRole], ['role' => $roleName], $user->id);
        }

        return redirect()->route('dashboard', ['view_role' => $roleName])
                         ->with('info', "Peran hak akses (RBAC) berhasil diubah menjadi: {$roleName}");
    }
}

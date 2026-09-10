<?php

namespace App\Http\Controllers;

use App\Models\PpmPeriodeHibah;
use App\Models\RefFakultas;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    /**
     * Executive University-Level Analytics Dashboard (US-12.1).
     * Accessible by: Rektor, Kepala P3M, Superadmin.
     */
    public function executive(Request $request, AnalyticsService $analytics): View
    {
        $user = Auth::user();
        abort_unless(
            $user->hasRole(['Kepala P3M', 'Superadmin', 'Rektor', 'Admin P3M']),
            403,
            'Akses ditolak: Khusus Rektor & Pimpinan P3M.'
        );

        $periodeId = $request->input('periode_id') ? (int) $request->input('periode_id') : null;
        $forceRefresh = $request->boolean('refresh');

        $metrics = $analytics->getExecutiveMetrics($periodeId, $forceRefresh);
        $periodes = PpmPeriodeHibah::latest('id')->get();

        return view('analitik.eksekutif', compact('metrics', 'periodes', 'periodeId'));
    }

    /**
     * Faculty-Level Analytics Dashboard with Tenant Isolation (US-12.2).
     * Accessible by: Dekanat (isolated to their faculty), Kepala P3M, Superadmin.
     */
    public function faculty(Request $request, AnalyticsService $analytics): View
    {
        $user = Auth::user();
        abort_unless(
            $user->hasRole(['Dekanat', 'Kepala P3M', 'Superadmin', 'Rektor']),
            403,
            'Akses ditolak: Khusus Dekan / Pimpinan Fakultas.'
        );

        $isPrivileged = $user->hasRole(['Superadmin', 'Kepala P3M', 'Rektor']);
        $userFakultasId = $user->id_fakultas;

        // Tenant Isolation Enforcement
        if (!$isPrivileged) {
            // Strict tenant isolation: Dekan can ONLY view their own faculty
            if (!$userFakultasId) {
                abort(403, 'Akun Dekanat belum ditautkan ke fakultas manapun.');
            }
            $targetFakultasId = $userFakultasId;
        } else {
            // Superadmin & Kepala P3M can switch faculties
            $targetFakultasId = $request->input('fakultas_id') 
                ? (int) $request->input('fakultas_id') 
                : ($userFakultasId ?: RefFakultas::first()->id_fakultas);
        }

        $periodeId = $request->input('periode_id') ? (int) $request->input('periode_id') : null;

        $facultyData = $analytics->getFacultyMetrics($targetFakultasId, $periodeId);
        $allFakultas = $isPrivileged ? RefFakultas::orderBy('nama_fakultas')->get() : collect([$facultyData['fakultas']]);
        $periodes = PpmPeriodeHibah::latest('id')->get();

        return view('analitik.fakultas', compact('facultyData', 'allFakultas', 'targetFakultasId', 'periodes', 'periodeId', 'isPrivileged'));
    }
}


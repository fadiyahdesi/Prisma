<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PpmPeriodeHibah;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeriodeHibahController extends Controller
{
    /**
     * Display listing of Call for Proposals periods.
     */
    public function index()
    {
        $periods = PpmPeriodeHibah::orderBy('waktu_tutup', 'desc')->get();
        return view('admin.periode-hibah.index', compact('periods'));
    }

    /**
     * Store a new Call for Proposals Period.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun_akademik' => 'required|string|max:20',
            'semester' => 'required|in:Ganjil,Genap',
            'nama_periode' => 'required|string|max:255',
            'waktu_buka' => 'required|date',
            'waktu_tutup' => 'required|date|after:waktu_buka',
            'keterangan' => 'nullable|string',
        ]);

        $period = PpmPeriodeHibah::create([
            'tahun_akademik' => trim($validated['tahun_akademik']),
            'semester' => $validated['semester'],
            'nama_periode' => trim($validated['nama_periode']),
            'waktu_buka' => $validated['waktu_buka'],
            'waktu_tutup' => $validated['waktu_tutup'],
            'is_active' => true,
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        AuditLogService::log('CALL_FOR_PROPOSALS_CREATED', null, [
            'admin_id' => Auth::id(),
            'period_id' => $period->id,
            'nama_periode' => $period->nama_periode,
            'waktu_tutup' => $period->waktu_tutup->toDateTimeString(),
        ]);

        return redirect()->route('admin.periode-hibah.index')
                         ->with('success', "Periode Usulan '{$period->nama_periode}' berhasil dibuat & diaktifkan!");
    }

    /**
     * Update an existing Call for Proposals Period.
     */
    public function update(Request $request, PpmPeriodeHibah $periodeHibah)
    {
        $validated = $request->validate([
            'tahun_akademik' => 'required|string|max:20',
            'semester' => 'required|in:Ganjil,Genap',
            'nama_periode' => 'required|string|max:255',
            'waktu_buka' => 'required|date',
            'waktu_tutup' => 'required|date|after:waktu_buka',
            'keterangan' => 'nullable|string',
        ]);

        $periodeHibah->update($validated);

        AuditLogService::log('CALL_FOR_PROPOSALS_UPDATED', null, [
            'admin_id' => Auth::id(),
            'period_id' => $periodeHibah->id,
            'nama_periode' => $periodeHibah->nama_periode,
            'waktu_tutup' => $periodeHibah->waktu_tutup->toDateTimeString(),
        ]);

        return redirect()->route('admin.periode-hibah.index')
                         ->with('success', "Periode Usulan '{$periodeHibah->nama_periode}' berhasil diperbarui!");
    }

    /**
     * Toggle active/inactive status of a period.
     */
    public function toggleActive(PpmPeriodeHibah $periodeHibah)
    {
        $periodeHibah->update([
            'is_active' => !$periodeHibah->is_active,
        ]);

        $statusStr = $periodeHibah->is_active ? 'DIFUNGSIKAN (AKTIF)' : 'DINONAKTIFKAN';

        AuditLogService::log('CALL_FOR_PROPOSALS_TOGGLED', null, [
            'admin_id' => Auth::id(),
            'period_id' => $periodeHibah->id,
            'is_active' => $periodeHibah->is_active,
        ]);

        return redirect()->route('admin.periode-hibah.index')
                         ->with('success', "Status Periode '{$periodeHibah->nama_periode}' diubah menjadi {$statusStr}.");
    }

    /**
     * Delete a period.
     */
    public function destroy(PpmPeriodeHibah $periodeHibah)
    {
        $name = $periodeHibah->nama_periode;
        $periodeHibah->delete();

        return redirect()->route('admin.periode-hibah.index')
                         ->with('success', "Periode Usulan '{$name}' berhasil dihapus.");
    }
}


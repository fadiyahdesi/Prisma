<?php

namespace App\Http\Controllers;

use App\Models\PpmLogbook;
use App\Models\PpmUsulan;
use App\Services\MonitoringAndCompletionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LogbookController extends Controller
{
    /**
     * Tampilan Daftar Usulan & Logbook Harian Penelitian (US-10.1).
     */
    public function index(Request $request, ?PpmUsulan $usulan = null)
    {
        $user = Auth::user();

        // If no usulan is specified in the route, fetch user's contracted/ongoing proposals
        if (!$usulan || !$usulan->exists) {
            $usulanList = PpmUsulan::with(['skema', 'periode', 'logbook'])
                ->where('id_pengusul', $user->id)
                ->whereIn('status', ['Contracted', 'Ongoing', 'Completed'])
                ->latest()
                ->get();

            if ($usulanList->count() === 1) {
                return redirect()->route('pengusul.logbook.show', $usulanList->first());
            }

            return view('pengusul.logbook.index', [
                'usulan' => null,
                'usulanList' => $usulanList,
                'logbooks' => collect(),
            ]);
        }

        // Authorize user (Must be owner or Superadmin)
        if ($usulan->id_pengusul !== $user->id && !$user->hasRole('Superadmin')) {
            abort(403, 'Akses ditolak: Anda bukan ketua pengusul usulan ini.');
        }

        $logbooks = $usulan->logbook()->orderBy('tanggal', 'asc')->get();

        $usulanList = PpmUsulan::where('id_pengusul', $user->id)
            ->whereIn('status', ['Contracted', 'Ongoing', 'Completed'])
            ->latest()
            ->get();

        return view('pengusul.logbook.index', [
            'usulan' => $usulan,
            'usulanList' => $usulanList,
            'logbooks' => $logbooks,
        ]);
    }

    /**
     * Simpan Catatan Logbook Baru (US-10.1).
     */
    public function store(Request $request, PpmUsulan $usulan)
    {
        $user = Auth::user();

        if ($usulan->id_pengusul !== $user->id && !$user->hasRole('Superadmin')) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'tanggal' => 'required|date|before_or_equal:today',
            'aktivitas' => 'required|string|min:10|max:2000',
            'persentase_capaian' => 'required|numeric|min:0|max:100',
            'file_bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ], [
            'tanggal.required' => 'Tanggal kegiatan wajib diisi.',
            'tanggal.before_or_equal' => 'Tanggal kegiatan tidak boleh di masa depan.',
            'aktivitas.required' => 'Uraian aktivitas kegiatan wajib diisi.',
            'aktivitas.min' => 'Uraian aktivitas minimal 10 karakter.',
            'persentase_capaian.required' => 'Persentase capaian kegiatan wajib diisi.',
            'file_bukti.max' => 'Ukuran berkas bukti maksimal 2MB.',
        ]);

        MonitoringAndCompletionService::createLogbookEntry(
            $usulan,
            $validated,
            $request->file('file_bukti'),
            $user->id
        );

        return redirect()->route('pengusul.logbook.show', $usulan)
            ->with('success', 'Catatan logbook kegiatan harian berhasil ditambahkan!');
    }

    /**
     * Hapus Catatan Logbook (US-10.1).
     */
    public function destroy(PpmLogbook $logbook)
    {
        $user = Auth::user();
        $usulan = $logbook->usulan;

        if ($usulan->id_pengusul !== $user->id && !$user->hasRole('Superadmin')) {
            abort(403, 'Akses ditolak.');
        }

        if ($logbook->file_bukti && Storage::disk('public')->exists($logbook->file_bukti)) {
            Storage::disk('public')->delete($logbook->file_bukti);
        }

        $logbook->delete();

        return redirect()->route('pengusul.logbook.show', $usulan)
            ->with('success', 'Catatan logbook berhasil dihapus.');
    }

    /**
     * Cetak & Unduh Rekap Logbook Formal Ber-format PDF (US-10.1).
     */
    public function downloadPdf(PpmUsulan $usulan)
    {
        $user = Auth::user();

        if ($usulan->id_pengusul !== $user->id && !$user->hasRole(['Superadmin', 'Admin P3M', 'Kepala P3M', 'Reviewer'])) {
            abort(403, 'Akses ditolak.');
        }

        $pdf = MonitoringAndCompletionService::generateLogbookPdf($usulan);

        return $pdf->download('Logbook_Kegiatan_' . ($usulan->kode_usulan ?: $usulan->id) . '.pdf');
    }
}


<?php

namespace App\Http\Controllers;

use App\Models\PpmMonevKemajuan;
use App\Models\PpmUsulan;
use App\Services\MonitoringAndCompletionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonevController extends Controller
{
    /**
     * Dosen Portal: Unggah Laporan Kemajuan 70% & SPTB 70% (US-10.2).
     */
    public function pengusulIndex(?PpmUsulan $usulan = null)
    {
        $user = Auth::user();

        if (!$usulan || !$usulan->exists) {
            $usulanList = PpmUsulan::with(['skema', 'periode', 'monevKemajuan'])
                ->where('id_pengusul', $user->id)
                ->whereIn('status', ['Contracted', 'Ongoing', 'Completed'])
                ->latest()
                ->get();

            if ($usulanList->count() === 1) {
                return redirect()->route('pengusul.monev.show', $usulanList->first());
            }

            return view('pengusul.monev.index', [
                'usulan' => null,
                'usulanList' => $usulanList,
                'monev' => null,
            ]);
        }

        if ($usulan->id_pengusul !== $user->id && !$user->hasRole('Superadmin')) {
            abort(403, 'Akses ditolak: Anda bukan ketua pengusul usulan ini.');
        }

        $monev = $usulan->monevKemajuan;
        $usulanList = PpmUsulan::where('id_pengusul', $user->id)
            ->whereIn('status', ['Contracted', 'Ongoing', 'Completed'])
            ->latest()
            ->get();

        return view('pengusul.monev.index', [
            'usulan' => $usulan,
            'usulanList' => $usulanList,
            'monev' => $monev,
        ]);
    }

    /**
     * Dosen Portal: Simpan / Submit Berkas Laporan Kemajuan (US-10.2).
     */
    public function pengusulStore(Request $request, PpmUsulan $usulan)
    {
        $user = Auth::user();

        if ($usulan->id_pengusul !== $user->id && !$user->hasRole('Superadmin')) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'file_laporan_kemajuan' => 'required|file|mimes:pdf|max:10240',
            'file_sptb_70' => 'required|file|mimes:pdf|max:5120',
            'ringkasan_kemajuan' => 'required|string|min:20|max:2000',
            'persentase_kemajuan' => 'nullable|numeric|min:0|max:100',
        ], [
            'file_laporan_kemajuan.required' => 'Berkas PDF Laporan Kemajuan wajib diunggah.',
            'file_laporan_kemajuan.mimes' => 'Berkas Laporan Kemajuan harus berformat PDF.',
            'file_laporan_kemajuan.max' => 'Ukuran berkas Laporan Kemajuan maksimal 10MB.',
            'file_sptb_70.required' => 'Berkas PDF SPTB 70% wajib diunggah.',
            'file_sptb_70.mimes' => 'Berkas SPTB 70% harus berformat PDF.',
            'file_sptb_70.max' => 'Ukuran berkas SPTB 70% maksimal 5MB.',
            'ringkasan_kemajuan.required' => 'Ringkasan capaian kemajuan riset wajib diisi.',
            'ringkasan_kemajuan.min' => 'Ringkasan capaian kemajuan minimal 20 karakter.',
        ]);

        MonitoringAndCompletionService::submitLaporanKemajuan(
            $usulan,
            $validated,
            $request->file('file_laporan_kemajuan'),
            $request->file('file_sptb_70'),
            $user->id
        );

        return redirect()->route('pengusul.monev.show', $usulan)
            ->with('success', 'Laporan Kemajuan 70% dan SPTB 70% berhasil diunggah! Berkas siap dievaluasi oleh Reviewer Monev.');
    }

    /**
     * Reviewer Portal: Daftar Monev Kemajuan Pelaksanaan Lapangan (US-10.2).
     */
    public function reviewerIndex(Request $request)
    {
        $user = Auth::user();
        abort_unless($user->hasRole(['Reviewer', 'Superadmin']), 403, 'Akses khusus reviewer monev.');

        $monevList = PpmMonevKemajuan::with(['usulan.pengusul.fakultas', 'usulan.pengusul.prodi', 'usulan.skema', 'reviewer'])
            ->latest('updated_at')
            ->paginate(15);

        return view('reviewer.monev.index', compact('monevList'));
    }

    /**
     * Reviewer Portal: Detail Monev & Form Penilaian Kemajuan (US-10.2).
     */
    public function reviewerShow(PpmMonevKemajuan $monev)
    {
        $user = Auth::user();
        abort_unless($user->hasRole(['Reviewer', 'Superadmin']), 403, 'Akses ditolak.');

        $monev->loadMissing(['usulan.pengusul.fakultas', 'usulan.pengusul.prodi', 'usulan.skema', 'usulan.logbook', 'reviewer']);

        return view('reviewer.monev.show', compact('monev'));
    }

    /**
     * Reviewer Portal: Simpan Penilaian & Rekomendasi Monev (US-10.2).
     */
    public function reviewerStore(Request $request, PpmMonevKemajuan $monev)
    {
        $user = Auth::user();
        abort_unless($user->hasRole(['Reviewer', 'Superadmin']), 403, 'Akses ditolak.');

        $validated = $request->validate([
            'skor_monev' => 'required|numeric|min:0|max:100',
            'rekomendasi' => 'required|in:Lanjut,Perbaikan,Ditunda',
            'catatan_evaluasi' => 'required|string|min:10|max:2000',
        ], [
            'skor_monev.required' => 'Skor monev wajib diisi (0-100).',
            'rekomendasi.required' => 'Rekomendasi kelanjutan wajib dipilih (Lanjut, Perbaikan, atau Ditunda).',
            'catatan_evaluasi.required' => 'Catatan evaluasi kemajuan wajib diisi minimal 10 karakter.',
        ]);

        MonitoringAndCompletionService::evaluateMonev($monev, $validated, $user->id);

        return redirect()->route('reviewer.monev.index')
            ->with('success', "Penilaian Monev untuk usulan '{$monev->usulan->judul_usulan}' berhasil disimpan dengan rekomendasi: {$validated['rekomendasi']}. Peneliti telah dinotifikasi!");
    }
}


<?php

namespace App\Http\Controllers;

use App\Models\PpmPenugasanReviewer;
use App\Services\ReviewEngineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewerController extends Controller
{
    /**
     * Display listing of assigned proposals for the authenticated Reviewer (US-08.1 & US-08.2).
     * Enforces Double-Blind: author identity is completely hidden.
     */
    public function index()
    {
        $user = Auth::user();
        abort_unless($user->hasRole(['Reviewer', 'Superadmin']), 403, 'Akses ditolak: Halaman ini khusus untuk Reviewer.');

        $assignments = PpmPenugasanReviewer::with(['usulan.skema', 'usulan.periode', 'penilaian'])
            ->where('id_reviewer', $user->id)
            ->latest('assigned_at')
            ->paginate(10);

        $stats = [
            'total_assigned' => PpmPenugasanReviewer::where('id_reviewer', $user->id)->count(),
            'pending' => PpmPenugasanReviewer::where('id_reviewer', $user->id)->where('status_penugasan', 'assigned')->count(),
            'completed' => PpmPenugasanReviewer::where('id_reviewer', $user->id)->where('status_penugasan', 'completed')->count(),
        ];

        return view('reviewer.index', compact('assignments', 'stats'));
    }

    /**
     * Show Double-Blind evaluation form or locked assessment (US-08.2).
     */
    public function show(PpmPenugasanReviewer $penugasan)
    {
        $user = Auth::user();
        abort_unless($user->hasRole('Superadmin') || $penugasan->id_reviewer === $user->id, 403, 'Akses ditolak ke berkas penugasan ini.');

        $penugasan->load(['usulan.skema', 'usulan.periode', 'usulan.luaran', 'usulan.rab', 'penilaian']);
        $usulan = $penugasan->usulan;
        $rubrikList = $usulan->skema->rubrik_penilaian ?? [];
        $penilaian = $penugasan->penilaian;

        return view('reviewer.form', compact('penugasan', 'usulan', 'rubrikList', 'penilaian'));
    }

    /**
     * Submit BIMA 1-7 Substantive Evaluation & lock assessment (US-08.2 & US-08.3).
     */
    public function store(Request $request, PpmPenugasanReviewer $penugasan)
    {
        $user = Auth::user();
        abort_unless($user->hasRole('Superadmin') || $penugasan->id_reviewer === $user->id, 403, 'Akses ditolak.');

        if ($penugasan->status_penugasan === 'completed') {
            return redirect()->route('reviewer.penilaian.show', $penugasan)
                             ->with('error', 'Form penilaian ini telah dikunci dan tidak dapat diubah lagi.');
        }

        $usulan = $penugasan->usulan()->with('skema')->first();
        $rubrikList = $usulan->skema->rubrik_penilaian ?? [];

        // Dynamic validation rules for each rubric criteria (scores 1-7)
        $rules = [
            'scores' => 'required|array|min:1',
            'komentar_kualitatif' => 'required|string|min:10|max:10000',
            'rekomendasi' => 'required|in:layak,revisi,tidak_layak',
        ];

        foreach ($rubrikList as $item) {
            $rules['scores.' . $item['id']] = 'required|numeric|min:1|max:7';
        }

        $validated = $request->validate($rules);

        ReviewEngineService::submitReview(
            $penugasan,
            $validated['scores'],
            $validated['komentar_kualitatif'],
            $validated['rekomendasi']
        );

        return redirect()->route('reviewer.penilaian.show', $penugasan)
                         ->with('success', 'Penilaian telaah substansi standar BIMA berhasil disimpan dan dikunci permanen!');
    }
}


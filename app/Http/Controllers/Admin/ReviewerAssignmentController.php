<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PpmUsulan;
use App\Services\ReviewEngineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class ReviewerAssignmentController extends Controller
{
    /**
     * Display proposal assignment queue and CoI overview (US-08.1 & US-08.3).
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        abort_unless($user->hasRole(['Admin P3M', 'Superadmin']), 403, 'Akses ditolak: Khusus Admin P3M.');

        $query = PpmUsulan::with([
            'pengusul.fakultas',
            'pengusul.prodi',
            'skema',
            'periode',
            'penugasanReviewer.reviewer.fakultas',
            'penugasanReviewer.reviewer.prodi',
            'penugasanReviewer.penilaian',
        ])
        ->whereIn('status', ['In_review', 'Approved', 'Submitted', 'Adjudication', 'Reviewed'])
        ->latest('submitted_at');

        if ($request->filled('status_filter')) {
            $query->where('status', $request->string('status_filter'));
        }

        $proposals = $query->paginate(12)->withQueryString();

        // Stats
        $stats = [
            'total_in_review' => PpmUsulan::where('status', 'In_review')->count(),
            'total_adjudication' => PpmUsulan::where('status', 'Adjudication')->count(),
            'total_reviewed' => PpmUsulan::where('status', 'Reviewed')->count(),
        ];

        return view('admin.reviewer-assignment.index', compact('proposals', 'stats'));
    }

    /**
     * Assign Reviewers to a proposal with Conflict of Interest check (US-08.1).
     * Dynamic: 1 or 2 reviewers (Pengabdian requires minimum 2 reviewers).
     */
    public function assign(Request $request, PpmUsulan $usulan)
    {
        $user = Auth::user();
        abort_unless($user->hasRole(['Admin P3M', 'Superadmin']), 403, 'Akses ditolak.');

        $isPengabdian = strtolower($usulan->skema?->kategori ?? '') === 'pengabdian';

        $rules = [
            'reviewer_1_id' => 'required|exists:users,id',
            'reviewer_2_id' => $isPengabdian ? 'required|exists:users,id|different:reviewer_1_id' : 'nullable|exists:users,id|different:reviewer_1_id',
        ];

        $messages = [
            'reviewer_2_id.required' => 'Skema Pengabdian kepada Masyarakat mewajibkan minimal 2 reviewer penilai.',
            'reviewer_2_id.different' => 'Reviewer 1 dan Reviewer 2 tidak boleh orang yang sama.',
        ];

        $validated = $request->validate($rules, $messages);

        try {
            $r2 = !empty($validated['reviewer_2_id']) ? (int) $validated['reviewer_2_id'] : null;

            ReviewEngineService::assignReviewers(
                $usulan,
                (int) $validated['reviewer_1_id'],
                $r2,
                $user->id
            );

            $msg = $r2 
                ? "Penugasan Reviewer 1 & 2 untuk usulan {$usulan->kode_usulan} berhasil disimpan!" 
                : "Penugasan Reviewer 1 (Penilai Tunggal) untuk usulan {$usulan->kode_usulan} berhasil disimpan!";

            return redirect()->route('admin.reviewer-assignment.index')
                             ->with('success', $msg);
        } catch (InvalidArgumentException $e) {
            return redirect()->back()
                             ->withInput()
                             ->with('error', "Gagal Penugasan: " . $e->getMessage());
        }
    }

    /**
     * Assign Reviewer 3 (Adjudicator) when extreme disparity occurs (US-08.3).
     */
    public function assignAdjudicator(Request $request, PpmUsulan $usulan)
    {
        $user = Auth::user();
        abort_unless($user->hasRole(['Admin P3M', 'Superadmin']), 403, 'Akses ditolak.');

        if ($usulan->status !== 'Adjudication') {
            return redirect()->back()->with('error', 'Usulan ini tidak berada dalam status Adjudication.');
        }

        $validated = $request->validate([
            'adjudicator_id' => 'required|exists:users,id',
        ]);

        try {
            ReviewEngineService::assignAdjudicator(
                $usulan,
                (int) $validated['adjudicator_id'],
                $user->id
            );

            return redirect()->route('admin.reviewer-assignment.index')
                             ->with('success', "Reviewer 3 (Penengah) berhasil ditugaskan untuk usulan {$usulan->kode_usulan}!");
        } catch (InvalidArgumentException $e) {
            return redirect()->back()
                             ->withInput()
                             ->with('error', "Gagal Penugasan Adjudicator: " . $e->getMessage());
        }
    }

    /**
     * API JSON endpoint to fetch eligible non-CoI reviewers for a proposal (US-08.1).
     */
    public function eligibleReviewers(PpmUsulan $usulan)
    {
        $user = Auth::user();
        abort_unless($user->hasRole(['Admin P3M', 'Superadmin']), 403, 'Akses ditolak.');

        $eligible = ReviewEngineService::getEligibleReviewers($usulan);

        return response()->json([
            'usulan_kode' => $usulan->kode_usulan,
            'pengusul_fakultas' => $usulan->pengusul?->fakultas?->nama_fakultas,
            'pengusul_prodi' => $usulan->pengusul?->prodi?->nama_prodi,
            'reviewers' => $eligible->map(fn($r) => [
                'id' => $r->id,
                'name' => $r->name,
                'nidn' => $r->nidn_nim,
                'fakultas' => $r->fakultas?->nama_fakultas,
                'prodi' => $r->prodi?->nama_prodi,
                'sinta_score' => $r->sinta_score_3yr,
            ]),
        ]);
    }
}


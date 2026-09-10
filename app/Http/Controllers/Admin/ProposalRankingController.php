<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PpmUsulan;
use App\Models\PpmSkemaBima;
use App\Models\RefFakultas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProposalRankingController extends Controller
{
    /**
     * Display proposal ranking table with dynamic passing grade cut-off (US-08.4).
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        abort_unless($user->hasRole(['Kepala P3M', 'Superadmin']), 403, 'Akses ditolak: Khusus Kepala P3M.');

        $schemes = PpmSkemaBima::where('is_active', true)->orderBy('nama_skema')->get();
        $faculties = RefFakultas::orderBy('nama_fakultas')->get();

        $budgetInput = $request->filled('total_budget') 
            ? (float) str_replace(['.', ','], ['', '.'], $request->input('total_budget'))
            : 250000000.0; // Default Rp 250 Juta

        $query = PpmUsulan::with(['pengusul.fakultas', 'pengusul.prodi', 'skema', 'periode', 'kontrak'])
            ->whereNotNull('skor_akhir')
            ->orderBy('skor_akhir', 'desc')
            ->orderBy('total_rab', 'asc');

        if ($request->filled('scheme_id')) {
            $query->where('id_skema_bima', $request->integer('scheme_id'));
        }

        if ($request->filled('faculty_id')) {
            $query->whereHas('pengusul', function ($q) use ($request) {
                $q->where('id_fakultas', $request->integer('faculty_id'));
            });
        }

        $rankedProposals = $query->get();

        // Calculate dynamic passing grade cut-off line
        $cumulativeBudget = 0.0;
        $passingCutoffScore = null;
        $passedCount = 0;

        $rankedList = $rankedProposals->map(function ($usulan, $index) use (&$cumulativeBudget, &$passingCutoffScore, &$passedCount, $budgetInput) {
            $rab = (float) $usulan->total_rab;
            $cumulativeBudget += $rab;
            $isWithinBudget = ($cumulativeBudget <= $budgetInput);

            if ($isWithinBudget) {
                $passedCount++;
                $passingCutoffScore = $usulan->skor_akhir;
            }

            return [
                'rank' => $index + 1,
                'usulan' => $usulan,
                'skor_akhir' => $usulan->skor_akhir,
                'skor_r1' => $usulan->skor_reviewer_1,
                'skor_r2' => $usulan->skor_reviewer_2,
                'skor_r3' => $usulan->skor_reviewer_3,
                'is_adjudication' => $usulan->is_disparity,
                'total_rab' => $rab,
                'cumulative_budget' => $cumulativeBudget,
                'is_passed' => $isWithinBudget,
            ];
        });

        return view('admin.ranking.index', compact(
            'rankedList',
            'schemes',
            'faculties',
            'budgetInput',
            'passedCount',
            'passingCutoffScore',
            'cumulativeBudget'
        ));
    }
}


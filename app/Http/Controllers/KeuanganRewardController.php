<?php

namespace App\Http\Controllers;

use App\Models\PpmKlaimReward;
use App\Models\PpmRewardDistribusi;
use App\Services\RewardDistributionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class KeuanganRewardController extends Controller
{
    /**
     * Divisi Keuangan Portal: Daftar Klaim Reward Siap Cair.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        abort_unless($user->hasRole(['Keuangan', 'Superadmin']), 403, 'Akses ditolak: Khusus Divisi Keuangan.');

        $tab = $request->query('tab', 'ready'); // ready, all, disbursed
        $search = $request->query('search');

        $query = PpmKlaimReward::with(['user', 'publikasi', 'hki', 'distribusi', 'approver']);

        if ($tab === 'ready') {
            $query->where('status_klaim', 'Approved_P3M');
        } elseif ($tab === 'disbursed') {
            $query->where('status_klaim', 'Disbursed');
        } else {
            $query->whereIn('status_klaim', ['Approved_P3M', 'Disbursed']);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_klaim', 'ilike', "%{$search}%")
                    ->orWhere('kategori_insentif', 'ilike', "%{$search}%")
                    ->orWhereHas('user', fn($uq) => $uq->where('name', 'ilike', "%{$search}%"));
            });
        }

        $klaimList = $query->latest('approved_at')->paginate(12)->withQueryString();

        $counts = [
            'ready' => PpmKlaimReward::where('status_klaim', 'Approved_P3M')->count(),
            'disbursed' => PpmKlaimReward::where('status_klaim', 'Disbursed')->count(),
        ];

        return view('keuangan.reward.index', compact('klaimList', 'tab', 'counts'));
    }

    /**
     * Show detail breakdown and transfer execution table for Keuangan.
     */
    public function show(PpmKlaimReward $klaim): View
    {
        $user = Auth::user();
        abort_unless($user->hasRole(['Keuangan', 'Superadmin']), 403, 'Akses ditolak: Khusus Divisi Keuangan.');

        $klaim->load(['user', 'publikasi', 'hki', 'distribusi', 'approver']);

        return view('keuangan.reward.show', compact('klaim'));
    }

    /**
     * Process transfer for a single distribution author.
     */
    public function disburseMember(Request $request, PpmRewardDistribusi $distribusi, RewardDistributionService $service): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user->hasRole(['Keuangan', 'Superadmin']), 403, 'Akses ditolak: Khusus Divisi Keuangan.');

        $validated = $request->validate([
            'tanggal_transfer' => 'required|date',
            'nomor_referensi' => 'required|string|max:100',
            'file_bukti_transfer' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'file_bukti_transfer.required' => 'Bukti struk/slip transfer bank wajib diunggah.',
            'nomor_referensi.required' => 'Nomor referensi mutasi / transfer perbankan wajib diisi.',
        ]);

        $filePath = $request->file('file_bukti_transfer')->store('bukti_transfer_reward', 'public');
        $validated['file_bukti_transfer'] = $filePath;

        $service->disburseMember($distribusi, $validated);

        return redirect()->back()->with('success', "Pencairan reward untuk {$distribusi->nama_penulis} sebesar Rp " . number_format($distribusi->nominal_bagian, 0, ',', '.') . " berhasil dicatat.");
    }
}


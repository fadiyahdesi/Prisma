<?php

namespace App\Http\Controllers;

use App\Models\PpmHki;
use App\Models\PpmKlaimReward;
use App\Models\PpmPublikasiJurnal;
use App\Models\RefTarifRewardSk;
use App\Services\RewardDistributionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RewardClaimController extends Controller
{
    /**
     * Dosen Portal: Daftar Pengajuan Klaim Reward Insentif.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $isStaff = $user->hasRole(['Admin P3M', 'Kepala P3M', 'Superadmin', 'Keuangan']);

        $query = PpmKlaimReward::with(['user', 'publikasi', 'hki', 'distribusi']);

        if (!$isStaff) {
            $query->where('user_id', $user->id);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_klaim', 'ilike', "%{$search}%")
                    ->orWhere('kategori_insentif', 'ilike', "%{$search}%")
                    ->orWhereHas('user', fn($uq) => $uq->where('name', 'ilike', "%{$search}%"))
                    ->orWhereHas('publikasi', fn($pq) => $pq->where('judul_artikel', 'ilike', "%{$search}%"))
                    ->orWhereHas('hki', fn($hq) => $hq->where('judul_hki', 'ilike', "%{$search}%"));
            });
        }

        if ($status = $request->input('status_klaim')) {
            $query->where('status_klaim', $status);
        }

        $klaimList = $query->latest()->paginate(10)->withQueryString();

        return view('reward.index', compact('klaimList', 'isStaff'));
    }

    /**
     * Form to create a new reward claim.
     */
    public function create(): View
    {
        $user = Auth::user();

        // Available publications (unclaimed)
        $publikasiList = PpmPublikasiJurnal::where('user_id', $user->id)
            ->where('is_claimed_reward', false)
            ->latest()
            ->get();

        // Available HKIs (verified and unclaimed)
        $hkiList = PpmHki::where('user_id', $user->id)
            ->where('status_hki', 'Terverifikasi HKI')
            ->where('is_claimed_reward', false)
            ->latest()
            ->get();

        // Tarif SK Matrix
        $tarifMatrix = RefTarifRewardSk::where('is_active', true)->get();

        return view('reward.create', compact('publikasiList', 'hkiList', 'tarifMatrix', 'user'));
    }

    /**
     * Store new reward claim.
     */
    public function store(Request $request, RewardDistributionService $service): RedirectResponse
    {
        $validated = $request->validate([
            'jenis_klaim' => 'required|in:Publikasi,HKI',
            'id_publikasi' => 'required_if:jenis_klaim,Publikasi|nullable|exists:ppm_publikasi_jurnal,id',
            'id_hki' => 'required_if:jenis_klaim,HKI|nullable|exists:ppm_hki,id',
            'kategori_insentif' => 'required|string|max:255',
            'file_surat_pernyataan' => 'required|file|mimes:pdf|max:10240',
            'distribusi' => 'required|array|min:1',
            'distribusi.*.nama_penulis' => 'required|string|max:255',
            'distribusi.*.nidn_nim' => 'nullable|string|max:50',
            'distribusi.*.email' => 'nullable|email|max:100',
            'distribusi.*.peran_penulis' => 'required|string|max:100',
            'distribusi.*.persentase' => 'required|numeric|min:0.01|max:100',
            'distribusi.*.nama_bank' => 'required|string|max:100',
            'distribusi.*.nomor_rekening' => 'required|string|max:50',
            'distribusi.*.nama_pemilik_rekening' => 'required|string|max:255',
        ], [
            'file_surat_pernyataan.required' => 'Surat Pernyataan Kesepakatan Pembagian Insentif bermaterai wajib diunggah.',
            'file_surat_pernyataan.mimes' => 'Surat pernyataan harus berformat PDF.',
            'distribusi.required' => 'Daftar pembagian insentif penulis wajib diisi.',
            'distribusi.*.persentase.required' => 'Persentase bagian wajib diisi untuk setiap penulis.',
            'distribusi.*.nomor_rekening.required' => 'Nomor rekening wajib diisi untuk setiap penulis.',
        ]);

        $filePath = $request->file('file_surat_pernyataan')->store('surat_pernyataan_reward', 'public');
        $validated['file_surat_pernyataan'] = $filePath;

        $klaim = $service->submitClaim(Auth::user(), $validated);

        return redirect()->route('reward.show', $klaim)
            ->with('success', "Pengajuan klaim reward {$klaim->nomor_klaim} berhasil diajukan ke P3M.");
    }

    /**
     * Show claim details.
     */
    public function show(PpmKlaimReward $klaim): View
    {
        $user = Auth::user();
        $isStaff = $user->hasRole(['Admin P3M', 'Kepala P3M', 'Superadmin', 'Keuangan']);

        if (!$isStaff && $klaim->user_id !== $user->id) {
            // Check if user is one of the distribution members
            $isMember = $klaim->distribusi()->where('user_id', $user->id)->exists();
            if (!$isMember) {
                abort(403, 'Akses ditolak ke klaim reward ini.');
            }
        }

        $klaim->load(['user', 'publikasi', 'hki', 'distribusi.user', 'approver']);

        return view('reward.show', compact('klaim', 'isStaff'));
    }

    /**
     * P3M Admin Queue: Review submitted reward claims.
     */
    public function adminIndex(Request $request): View
    {
        $user = Auth::user();
        abort_unless($user->hasRole(['Admin P3M', 'Kepala P3M', 'Superadmin']), 403, 'Khusus Pengelola P3M.');

        $status = $request->query('status', 'Submitted');
        $search = $request->query('search');

        $query = PpmKlaimReward::with(['user', 'publikasi', 'hki', 'distribusi', 'approver']);

        if ($status !== 'all') {
            $query->where('status_klaim', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_klaim', 'ilike', "%{$search}%")
                    ->orWhere('kategori_insentif', 'ilike', "%{$search}%")
                    ->orWhereHas('user', fn($uq) => $uq->where('name', 'ilike', "%{$search}%"));
            });
        }

        $klaimList = $query->latest()->paginate(15)->withQueryString();

        $counts = [
            'submitted' => PpmKlaimReward::where('status_klaim', 'Submitted')->count(),
            'approved' => PpmKlaimReward::where('status_klaim', 'Approved_P3M')->count(),
            'rejected' => PpmKlaimReward::where('status_klaim', 'Rejected')->count(),
            'disbursed' => PpmKlaimReward::where('status_klaim', 'Disbursed')->count(),
        ];

        return view('admin.reward.index', compact('klaimList', 'status', 'counts'));
    }

    /**
     * P3M Admin: Approve reward claim.
     */
    public function adminApprove(Request $request, PpmKlaimReward $klaim, RewardDistributionService $service): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user->hasRole(['Admin P3M', 'Kepala P3M', 'Superadmin']), 403, 'Khusus Pengelola P3M.');

        $catatan = $request->input('catatan_p3m', 'Klaim disetujui sesuai matriks SK Rektor.');
        $service->approveClaim($klaim, $user, $catatan);

        return redirect()->back()->with('success', "Klaim reward {$klaim->nomor_klaim} berhasil DISETUJUI dan diteruskan ke Divisi Keuangan.");
    }

    /**
     * P3M Admin: Reject reward claim.
     */
    public function adminReject(Request $request, PpmKlaimReward $klaim, RewardDistributionService $service): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user->hasRole(['Admin P3M', 'Kepala P3M', 'Superadmin']), 403, 'Khusus Pengelola P3M.');

        $request->validate([
            'catatan_p3m' => 'required|string|max:1000',
        ], [
            'catatan_p3m.required' => 'Alasan penolakan klaim wajib disertakan.',
        ]);

        $service->rejectClaim($klaim, $user, $request->input('catatan_p3m'));

        return redirect()->back()->with('warning', "Klaim reward {$klaim->nomor_klaim} DITOLAK. Aset telah dibuka kuncinya agar dapat diperbaiki.");
    }
}


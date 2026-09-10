<?php

namespace App\Http\Controllers;

use App\Models\PpmHki;
use App\Services\AuditLogService;
use App\Services\Integrations\DjkiClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SentraHkiController extends Controller
{
    /**
     * Dosen Portal: Daftar Pengajuan HKI Saya.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $isStaff = $user->hasRole(['Admin P3M', 'Kepala P3M', 'Superadmin']);

        $query = PpmHki::with(['user', 'verifier']);

        if (!$isStaff) {
            $query->where('user_id', $user->id);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('judul_hki', 'ilike', "%{$search}%")
                    ->orWhere('nomor_permohonan', 'ilike', "%{$search}%")
                    ->orWhere('nomor_sertifikat', 'ilike', "%{$search}%")
                    ->orWhereHas('user', fn($uq) => $uq->where('name', 'ilike', "%{$search}%"));
            });
        }

        if ($jenis = $request->input('jenis_hki')) {
            $query->where('jenis_hki', $jenis);
        }

        if ($status = $request->input('status_hki')) {
            $query->where('status_hki', $status);
        }

        $hkiList = $query->latest()->paginate(10)->withQueryString();

        return view('hki.index', compact('hkiList', 'isStaff'));
    }

    /**
     * Show form to submit HKI registration.
     */
    public function create(): View
    {
        return view('hki.create');
    }

    /**
     * Store newly submitted HKI registration.
     */
    public function store(Request $request, DjkiClient $djki): RedirectResponse
    {
        $validated = $request->validate([
            'jenis_hki' => 'required|string|in:Paten,Paten Sederhana,Hak Cipta,Desain Industri,Merk Dagang,Rahasia Dagang',
            'judul_hki' => 'required|string|max:500',
            'nomor_permohonan' => 'required|string|max:100|unique:ppm_hki,nomor_permohonan',
            'nomor_sertifikat' => 'nullable|string|max:100',
            'tanggal_permohonan' => 'required|date',
            'tanggal_terbit' => 'nullable|date|after_or_equal:tanggal_permohonan',
            'pemegang_hak' => 'required|string|max:255',
            'file_sertifikat' => 'required|file|mimes:pdf|max:10240',
        ], [
            'nomor_permohonan.unique' => 'Nomor permohonan HKI ini sudah terdaftar sebelumnya.',
            'file_sertifikat.mimes' => 'File sertifikat / bukti pendaftaran wajib berformat PDF.',
            'file_sertifikat.max' => 'Ukuran file sertifikat maksimal 10 MB.',
        ]);

        $path = $request->file('file_sertifikat')->store('hki_sertifikat', 'public');

        // Check DJKI Live API verification
        $cleanNumber = trim($validated['nomor_permohonan']);
        $djkiCheck = $djki->verifyHki($cleanNumber);

        $initialStatus = 'Pending_verification';
        $verifiedBy = null;
        $verifiedAt = null;
        $catatan = 'Menunggu verifikasi manual oleh Sentra HKI UHN.';

        if (!empty($djkiCheck['success']) && ($djkiCheck['status'] ?? '') === 'Granted') {
            $initialStatus = 'Terverifikasi HKI';
            $verifiedAt = now();
            $catatan = 'Diverifikasi secara otomatis via pangkalan data DJKI Kemenkumham.';
        }

        $hki = PpmHki::create([
            'user_id' => Auth::id(),
            'jenis_hki' => $validated['jenis_hki'],
            'judul_hki' => $validated['judul_hki'],
            'nomor_permohonan' => $cleanNumber,
            'nomor_sertifikat' => $validated['nomor_sertifikat'] ?? null,
            'tanggal_permohonan' => $validated['tanggal_permohonan'],
            'tanggal_terbit' => $validated['tanggal_terbit'] ?? null,
            'pemegang_hak' => $validated['pemegang_hak'],
            'file_sertifikat' => $path,
            'status_hki' => $initialStatus,
            'verified_by_user_id' => $verifiedBy,
            'verified_at' => $verifiedAt,
            'catatan_verifikasi' => $catatan,
            'is_claimed_reward' => false,
        ]);

        AuditLogService::log('hki_submitted', null, [
            'nomor_permohonan' => $cleanNumber,
            'jenis_hki' => $validated['jenis_hki'],
            'status' => $initialStatus,
        ]);

        $msg = $initialStatus === 'Terverifikasi HKI'
            ? 'HKI berhasil didaftarkan dan TERVERIFIKASI secara otomatis via DJKI!'
            : 'Pendaftaran HKI berhasil diajukan. Sentra HKI akan memverifikasi berkas Anda.';

        return redirect()->route('hki.show', $hki)->with('success', $msg);
    }

    /**
     * Show HKI details.
     */
    public function show(PpmHki $hki): View
    {
        $user = Auth::user();
        $isStaff = $user->hasRole(['Admin P3M', 'Kepala P3M', 'Superadmin']);

        if (!$isStaff && $hki->user_id !== $user->id) {
            abort(403, 'Akses ditolak ke dokumen HKI ini.');
        }

        $hki->load(['user', 'verifier', 'klaimReward.distribusi']);

        return view('hki.show', compact('hki', 'isStaff'));
    }

    /**
     * Admin/Sentra HKI Staff Queue: Verifikasi Berkas HKI.
     */
    public function adminIndex(Request $request): View
    {
        $user = Auth::user();
        abort_unless($user->hasRole(['Admin P3M', 'Kepala P3M', 'Superadmin']), 403, 'Khusus Pengelola Sentra HKI.');

        $status = $request->query('status', 'Pending_verification');
        $search = $request->query('search');

        $query = PpmHki::with(['user', 'verifier']);

        if ($status !== 'all') {
            $query->where('status_hki', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul_hki', 'ilike', "%{$search}%")
                    ->orWhere('nomor_permohonan', 'ilike', "%{$search}%")
                    ->orWhere('nomor_sertifikat', 'ilike', "%{$search}%")
                    ->orWhereHas('user', fn($uq) => $uq->where('name', 'ilike', "%{$search}%"));
            });
        }

        $hkiList = $query->latest()->paginate(15)->withQueryString();

        $counts = [
            'pending' => PpmHki::where('status_hki', 'Pending_verification')->count(),
            'verified' => PpmHki::where('status_hki', 'Terverifikasi HKI')->count(),
            'rejected' => PpmHki::where('status_hki', 'Rejected')->count(),
        ];

        return view('admin.hki.index', compact('hkiList', 'status', 'counts'));
    }

    /**
     * Sentra HKI Staff performs Verification (Approve/Reject).
     */
    public function verify(Request $request, PpmHki $hki): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user->hasRole(['Admin P3M', 'Kepala P3M', 'Superadmin']), 403, 'Khusus Pengelola Sentra HKI.');

        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
            'catatan_verifikasi' => 'required|string|max:1000',
            'nomor_sertifikat' => 'nullable|string|max:100',
            'tanggal_terbit' => 'nullable|date',
        ]);

        if ($validated['action'] === 'approve') {
            $hki->update([
                'status_hki' => 'Terverifikasi HKI',
                'nomor_sertifikat' => $validated['nomor_sertifikat'] ?? $hki->nomor_sertifikat,
                'tanggal_terbit' => $validated['tanggal_terbit'] ?? $hki->tanggal_terbit ?? now()->toDateString(),
                'verified_by_user_id' => $user->id,
                'verified_at' => now(),
                'catatan_verifikasi' => $validated['catatan_verifikasi'],
            ]);

            AuditLogService::log('hki_verified_approved', null, [
                'id_hki' => $hki->id,
                'nomor_permohonan' => $hki->nomor_permohonan,
                'verifier' => $user->id,
            ]);

            return redirect()->back()->with('success', "Status HKI '{$hki->judul_hki}' berhasil DIVERIFIKASI.");
        } else {
            $hki->update([
                'status_hki' => 'Rejected',
                'verified_by_user_id' => $user->id,
                'verified_at' => now(),
                'catatan_verifikasi' => $validated['catatan_verifikasi'],
            ]);

            AuditLogService::log('hki_verified_rejected', null, [
                'id_hki' => $hki->id,
                'nomor_permohonan' => $hki->nomor_permohonan,
                'verifier' => $user->id,
            ]);

            return redirect()->back()->with('warning', "Status HKI '{$hki->judul_hki}' ditandai DITOLAK / DITANGGUHKAN.");
        }
    }
}


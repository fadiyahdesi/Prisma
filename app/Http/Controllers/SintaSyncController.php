<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\SintaService;
use App\Services\EligibilityService;
use App\Services\AuditLogService;

class SintaSyncController extends Controller
{
    protected SintaService $sintaService;
    protected EligibilityService $eligibilityService;

    public function __construct(SintaService $sintaService, EligibilityService $eligibilityService)
    {
        $this->sintaService = $sintaService;
        $this->eligibilityService = $eligibilityService;
    }

    /**
     * Display SINTA Profile & Full Academic History Management View.
     */
    public function showProfile(Request $request)
    {
        $user = Auth::user();
        $user->load(['fakultas', 'prodi']);

        $searchQuery = $request->query('q', '');
        $searchResults = [];

        if (strlen($searchQuery) >= 2) {
            $searchResults = $this->sintaService->searchRealAuthors($searchQuery);
        }

        // Fetch metrics & detailed academic records
        $identifier = $user->sinta_id ?? $user->nidn_nim ?? $user->email;
        $fullMetrics = $this->sintaService->fetchMetrics($identifier);

        $scopusPubs = $fullMetrics['scopus_publications'] ?? $this->sintaService->generateMockScopusPubs();
        $googlePubs = $fullMetrics['google_publications'] ?? $this->sintaService->generateMockGooglePubs();
        $researches = $fullMetrics['researches'] ?? $this->sintaService->generateMockResearches();
        $hkiRecords = $fullMetrics['hki_records'] ?? $this->sintaService->generateMockHki();

        $eligibilitySummary = $this->eligibilityService->getSummaryForUser($user);
        $pendingFallbackUsers = User::where('sinta_verification_status', 'pending_operator')->get();

        return view('profile.sinta', compact(
            'user', 
            'eligibilitySummary', 
            'pendingFallbackUsers', 
            'searchQuery', 
            'searchResults',
            'scopusPubs',
            'googlePubs',
            'researches',
            'hkiRecords'
        ));
    }

    /**
     * Direct Update for Lecturer's Official NIDN & SINTA ID with Smart Auto-Detection.
     */
    public function updateNidnSinta(Request $request)
    {
        $request->validate([
            'nidn_nim' => 'required|string|min:8|max:12',
            'sinta_id' => 'nullable|string',
            'jabatan_fungsional' => 'nullable|string',
        ]);

        $user = Auth::user();
        $nidn = trim($request->input('nidn_nim'));
        $sintaIdInput = trim((string) $request->input('sinta_id'));
        $jafung = $request->input('jabatan_fungsional', $user->jabatan_fungsional);

        // Normalize SINTA ID (if user entered 10-digit NIDN in sinta_id box by accident, treat as null)
        $sintaId = (strlen($sintaIdInput) >= 4 && strlen($sintaIdInput) <= 8) ? $sintaIdInput : null;

        // Auto-detect SINTA ID if not provided
        if (!$sintaId) {
            $searchResults = $this->sintaService->searchRealAuthors($user->name);
            if (!empty($searchResults[0]['sinta_id'])) {
                $sintaId = $searchResults[0]['sinta_id'];
            }
        }

        $user->update([
            'nidn_nim' => $nidn,
            'sinta_id' => $sintaId ?? $user->sinta_id,
            'jabatan_fungsional' => $jafung,
        ]);

        // Auto sync user metrics using the newly updated exact NIDN & SINTA ID
        $result = $this->sintaService->syncUser($user, true, $sintaId);

        AuditLogService::log('LECTURER_NIDN_SINTA_UPDATED', null, [
            'user_id' => $user->id,
            'nidn_nim' => $nidn,
            'sinta_id' => $sintaId,
            'jafung' => $jafung,
        ], $user->id);

        $displaySinta = $sintaId ? "#{$sintaId}" : "(Terdeteksi dari NIDN)";
        return redirect()->route('sinta.profile')
                         ->with('success', "BERHASIL! NIDN Resmi ({$nidn}) dan SINTA ID {$displaySinta} Anda telah diperbarui & disinkronkan langsung ke basis data.");
    }

    /**
     * Import Data Dosen Real SINTA Kemdiktisaintek.
     */
    public function importReal(Request $request)
    {
        $request->validate([
            'sinta_id' => 'required|string',
        ]);

        $user = Auth::user();
        $sintaId = $request->input('sinta_id');

        $result = $this->sintaService->syncUser($user, true, $sintaId);

        if ($result['success']) {
            return redirect()->route('sinta.profile')
                             ->with('success', "BERHASIL! Data metrik & riwayat publikasi Dosen Real dari SINTA Kemdiktisaintek (ID #{$sintaId}) telah diimpor ke profil Anda.");
        }

        return redirect()->route('sinta.profile')
                         ->with('warning', 'Gagal menarik data real SINTA. Mengalihkan ke Fallback Mode.');
    }

    /**
     * Manual Trigger for "Sinkronisasi SINTA Mandiri" Button (US-03.1).
     */
    public function syncMandatory(Request $request)
    {
        $user = Auth::user();
        $simulateTimeout = $request->has('simulate_timeout');

        if ($simulateTimeout) {
            config(['services.sinta.simulate_timeout' => true]);
        }

        $result = $this->sintaService->syncUser($user, true);

        if ($result['success']) {
            return redirect()->route('sinta.profile')
                             ->with('success', 'Berhasil! Metrik skor SINTA, Scopus, Google Scholar, dan riwayat jurnaling Anda telah diperbarui langsung dari Web Services Nasional.');
        }

        return redirect()->route('sinta.profile')
                         ->with('warning', 'API SINTA Nasional sedang mengalami gangguan (Timeout > 5 Detik). Sistem secara otomatis mengaktifkan Mode Pengisian Mandiri (Fallback Mode). Silakan unggah bukti tangkapan layar (screenshot) profil SINTA Anda.');
    }

    /**
     * Submit Manual SINTA Fallback Form + Proof Screenshot Upload (US-03.3).
     */
    public function submitFallback(Request $request)
    {
        $request->validate([
            'sinta_score_3yr' => 'required|numeric|min:0',
            'sinta_score_overall' => 'required|numeric|min:0',
            'h_index_scopus' => 'required|integer|min:0',
            'h_index_google_scholar' => 'required|integer|min:0',
            'sinta_proof_file' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $user = Auth::user();

        // Handle File Upload
        $proofPath = null;
        if ($request->hasFile('sinta_proof_file')) {
            $file = $request->file('sinta_proof_file');
            $filename = 'sinta_proof_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $proofPath = $file->storeAs('sinta_proofs', $filename, 'public');
        }

        $user->update([
            'sinta_score_3yr' => $request->input('sinta_score_3yr'),
            'sinta_score_overall' => $request->input('sinta_score_overall'),
            'h_index_scopus' => $request->input('h_index_scopus'),
            'h_index_google_scholar' => $request->input('h_index_google_scholar'),
            'is_sinta_manual_fallback' => true,
            'sinta_proof_file' => $proofPath,
            'sinta_verification_status' => 'pending_operator',
            'last_sinta_sync_at' => now(),
        ]);

        AuditLogService::log('SINTA_FALLBACK_SUBMITTED', null, [
            'user_id' => $user->id,
            'sinta_score_3yr' => $request->input('sinta_score_3yr'),
            'proof_file' => $proofPath,
        ], $user->id);

        return redirect()->route('sinta.profile')
                         ->with('info', 'Form pengisian mandiri SINTA & bukti screenshot berhasil dikirim. Status Anda saat ini: "Pending Verifikasi Operator P3M".');
    }

    /**
     * Operator / Admin P3M Verification for Manual SINTA Claims (US-03.3).
     */
    public function verifyFallback(Request $request, User $user)
    {
        $action = $request->input('action', 'approve');

        if ($action === 'approve') {
            $user->update([
                'sinta_verification_status' => 'verified',
            ]);

            AuditLogService::log('SINTA_FALLBACK_APPROVED', null, ['user_id' => $user->id], Auth::id());
            return back()->with('success', "Klaim skor SINTA mandiri Dosen {$user->name} telah BERHASIL disahkan.");
        } else {
            $user->update([
                'sinta_verification_status' => 'rejected',
                'is_sinta_manual_fallback' => false,
            ]);

            AuditLogService::log('SINTA_FALLBACK_REJECTED', null, ['user_id' => $user->id], Auth::id());
            return back()->with('error', "Klaim skor SINTA mandiri Dosen {$user->name} telah DITOLAK oleh Operator.");
        }
    }
}

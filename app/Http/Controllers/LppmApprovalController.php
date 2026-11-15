<?php

namespace App\Http\Controllers;

use App\Models\PpmUsulan;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LppmApprovalController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeVerifier();

        $user = Auth::user();
        $activeRole = session('active_role') ?? $user->primaryRoleName();

        $defaultDivisi = match ($activeRole) {
            'Divisi Penelitian' => 'penelitian',
            'Divisi Pengabdian' => 'pengabdian',
            default => 'all',
        };
        $divisi = strtolower($request->input('divisi', $defaultDivisi));
        if (!in_array($divisi, ['all', 'penelitian', 'pengabdian'], true)) {
            $divisi = 'all';
        }

        $query = PpmUsulan::with(['pengusul.fakultas', 'pengusul.prodi', 'prodi', 'skema', 'periode', 'anggota', 'luaran'])
            ->whereIn('status', ['Submitted', 'In_review'])
            ->latest('submitted_at');

        if ($divisi !== 'all') {
            $query->where(function ($q) use ($divisi) {
                $q->where('kategori_usulan', $divisi)
                  ->orWhereHas('skema', fn ($sq) => $sq->where('kategori', $divisi));
            });
        }

        $query->when($request->filled('faculty'), fn ($builder) => $builder->whereHas('pengusul', fn ($userQuery) => $userQuery->where('id_fakultas', $request->integer('faculty'))));
        $query->when($request->filled('prodi'), fn ($builder) => $builder->whereHas('pengusul', fn ($userQuery) => $userQuery->where('id_prodi', $request->integer('prodi'))));
        $query->when($request->filled('scheme'), fn ($builder) => $builder->where('id_skema_bima', $request->integer('scheme')));
        $query->when($request->filled('leader'), function ($builder) use ($request) {
            $s = $request->string('leader');
            $like = \Illuminate\Support\Facades\DB::getDriverName() === 'pgsql' ? 'ilike' : 'like';
            $builder->where(function ($q) use ($s, $like) {
                $q->where('nama_ketua', $like, "%{$s}%")
                  ->orWhereHas('pengusul', fn ($uq) => $uq->where('name', $like, "%{$s}%"));
            });
        });

        $proposals = $query->get();
        $schemes = \App\Models\PpmSkemaBima::where('is_active', true)->orderBy('nama_skema')->get();

        return view('admin.lppm-approval.index', compact('proposals', 'schemes', 'divisi', 'activeRole'));
    }

    public function update(Request $request, PpmUsulan $usulan)
    {
        $this->authorizeVerifier();

        $validated = $request->validate([
            'decision' => 'required|in:in_review,approved,draft,rejected',
            'template_status' => 'nullable|in:sesuai,revisi,tidak_sesuai',
            'similarity_score' => 'nullable|numeric|min:0|max:100',
            'verification_notes' => 'nullable|string|max:5000',
        ]);

        if (!in_array($usulan->status, ['Submitted', 'In_review'], true)) {
            return back()->with('error', 'Usulan ini tidak berada dalam antrean verifikasi.');
        }

        if ($validated['decision'] === 'draft' && blank($validated['verification_notes'] ?? null)) {
            return back()->withErrors(['verification_notes' => 'Catatan revisi wajib diisi saat mengembalikan usulan ke draf.']);
        }

        $status = match ($validated['decision']) {
            'in_review' => 'In_review',
            'approved' => 'Approved',
            'draft' => 'Draft',
            'rejected' => 'Rejected',
        };

        $similarityScore = isset($validated['similarity_score']) && $validated['similarity_score'] !== ''
            ? (float) $validated['similarity_score']
            : $usulan->similarity_score;

        $similarityStatus = null;
        if ($similarityScore !== null) {
            $similarityStatus = $similarityScore <= 20.0 ? 'lolos' : 'melebihi_batas';
        }

        $usulan->update([
            'status' => $status,
            'template_status' => $validated['template_status'] ?? $usulan->template_status ?? 'sesuai',
            'similarity_score' => $similarityScore,
            'similarity_status' => $similarityStatus,
            'verification_notes' => $validated['verification_notes'] ?? null,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        AuditLogService::log('LPPM_PROPOSAL_VERIFIED', null, [
            'usulan_id' => $usulan->id,
            'decision' => $status,
            'template_status' => $usulan->template_status,
            'similarity_score' => $similarityScore,
            'verified_by' => Auth::id(),
        ], Auth::id());

        $statusLabel = match ($status) {
            'In_review' => 'Lolos Verifikasi Dokumen (Siap Plotting Reviewer)',
            'Approved' => 'Disetujui (Approved)',
            'Draft' => 'Dikembalikan untuk Revisi (Draft)',
            'Rejected' => 'Ditolak (Rejected)',
            default => $status,
        };

        return back()->with('success', "Verifikasi dokumen (Template & Similarity) untuk usulan {$usulan->kode_usulan} berhasil disimpan: {$statusLabel}.");
    }

    public function markInReview(PpmUsulan $usulan)
    {
        $this->authorizeVerifier();

        if ($usulan->status !== 'Submitted') {
            return back()->with('error', 'Hanya usulan Submitted yang dapat masuk proses verifikasi.');
        }

        $usulan->update([
            'status' => 'In_review',
            'template_status' => $usulan->template_status ?: 'sesuai',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        AuditLogService::log('LPPM_PROPOSAL_IN_REVIEW', null, [
            'usulan_id' => $usulan->id,
            'verified_by' => Auth::id(),
        ], Auth::id());

        return back()->with('success', "Usulan {$usulan->kode_usulan} lolos verifikasi dokumen dan siap di-plotting ke Reviewer.");
    }

    public function roadmapIndex()
    {
        abort_unless(Auth::user()->hasRole(['Kaprodi', 'Superadmin']), 403, 'Halaman ini hanya untuk Kaprodi.');

        $proposals = PpmUsulan::with(['pengusul', 'skema', 'anggota'])
            ->whereHas('pengusul', fn ($query) => $query->where('id_prodi', Auth::user()->id_prodi))
            ->whereIn('status', ['Submitted', 'In_review'])
            ->latest('submitted_at')
            ->get();

        return view('admin.lppm-approval.roadmap', compact('proposals'));
    }

    public function updateRoadmap(Request $request, PpmUsulan $usulan)
    {
        abort_unless(Auth::user()->hasRole(['Kaprodi', 'Superadmin']), 403, 'Akses ditolak.');

        $validated = $request->validate([
            'kaprodi_alignment_status' => 'required|in:aligned,revision,not_aligned',
            'kaprodi_recommendation' => 'required|string|max:5000',
        ]);

        $usulan->update([...$validated, 'kaprodi_id' => Auth::id(), 'kaprodi_reviewed_at' => now()]);

        AuditLogService::log('KAPRODI_ROADMAP_REVIEWED', null, ['usulan_id' => $usulan->id, 'status' => $validated['kaprodi_alignment_status']], Auth::id());

        return back()->with('success', 'Rekomendasi keselarasan roadmap berhasil disimpan.');
    }

    private function authorizeVerifier(): void
    {
        abort_unless(
            Auth::user()->hasRole(['Divisi Penelitian', 'Divisi Pengabdian', 'Operator P3M', 'Admin P3M', 'Kepala P3M', 'Superadmin']),
            403,
            'Anda tidak memiliki akses ke antrean verifikasi dokumen usulan.'
        );
    }
}

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
        $query = PpmUsulan::with(['pengusul.fakultas', 'pengusul.prodi', 'skema', 'periode', 'anggota', 'luaran'])
            ->whereIn('status', ['Submitted', 'In_review'])
            ->latest('submitted_at');

        $query->when($request->filled('faculty'), fn ($builder) => $builder->whereHas('pengusul', fn ($userQuery) => $userQuery->where('id_fakultas', $request->integer('faculty'))));
        $query->when($request->filled('prodi'), fn ($builder) => $builder->whereHas('pengusul', fn ($userQuery) => $userQuery->where('id_prodi', $request->integer('prodi'))));
        $query->when($request->filled('scheme'), fn ($builder) => $builder->where('id_skema_bima', $request->integer('scheme')));
        $query->when($request->filled('leader'), fn ($builder) => $builder->whereHas('pengusul', fn ($userQuery) => $userQuery->where('name', 'ilike', '%' . $request->string('leader') . '%')));

        $proposals = $query->get();
        $schemes = \App\Models\PpmSkemaBima::where('is_active', true)->orderBy('nama_skema')->get();

        return view('admin.lppm-approval.index', compact('proposals', 'schemes'));
    }

    public function update(Request $request, PpmUsulan $usulan)
    {
        $this->authorizeFinalVerifier();

        $validated = $request->validate([
            'decision' => 'required|in:approved,draft,rejected',
            'verification_notes' => 'nullable|string|max:5000',
        ]);

        if (!in_array($usulan->status, ['Submitted', 'In_review'], true)) {
            return back()->with('error', 'Usulan ini tidak berada dalam antrean verifikasi.');
        }

        if ($validated['decision'] === 'draft' && blank($validated['verification_notes'] ?? null)) {
            return back()->withErrors(['verification_notes' => 'Catatan revisi wajib diisi saat mengembalikan usulan ke draf.']);
        }

        $status = match ($validated['decision']) {
            'approved' => 'Approved',
            'draft' => 'Draft',
            'rejected' => 'Rejected',
        };

        $usulan->update([
            'status' => $status,
            'verification_notes' => $validated['verification_notes'] ?? null,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        AuditLogService::log('LPPM_PROPOSAL_VERIFIED', null, [
            'usulan_id' => $usulan->id,
            'decision' => $status,
            'verified_by' => Auth::id(),
        ], Auth::id());

        return back()->with('success', "Status usulan {$usulan->kode_usulan} berhasil diubah menjadi {$status}.");
    }

    public function markInReview(PpmUsulan $usulan)
    {
        $this->authorizeVerifier();

        if ($usulan->status !== 'Submitted') {
            return back()->with('error', 'Hanya usulan Submitted yang dapat masuk proses verifikasi.');
        }

        $usulan->update(['status' => 'In_review']);

        AuditLogService::log('LPPM_PROPOSAL_IN_REVIEW', null, [
            'usulan_id' => $usulan->id,
            'verified_by' => Auth::id(),
        ], Auth::id());

        return back()->with('success', "Usulan {$usulan->kode_usulan} masuk proses verifikasi.");
    }

    public function roadmapIndex()
    {
        abort_unless(Auth::user()->hasRole('Kaprodi'), 403, 'Halaman ini hanya untuk Kaprodi.');

        $proposals = PpmUsulan::with(['pengusul', 'skema', 'anggota'])
            ->whereHas('pengusul', fn ($query) => $query->where('id_prodi', Auth::user()->id_prodi))
            ->whereIn('status', ['Submitted', 'In_review'])
            ->latest('submitted_at')
            ->get();

        return view('admin.lppm-approval.roadmap', compact('proposals'));
    }

    public function updateRoadmap(Request $request, PpmUsulan $usulan)
    {
        abort_unless(Auth::user()->hasRole('Kaprodi'), 403, 'Akses ditolak.');

        abort_unless($usulan->pengusul()->where('id_prodi', Auth::user()->id_prodi)->exists(), 403, 'Usulan bukan bagian dari program studi Anda.');

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
        abort_unless(Auth::user()->hasRole(['Operator P3M', 'Admin P3M', 'Kepala P3M', 'Superadmin']), 403, 'Anda tidak memiliki akses ke antrean administrasi usulan.');
    }

    private function authorizeFinalVerifier(): void
    {
        abort_unless(Auth::user()->hasRole(['Kepala P3M', 'Superadmin']), 403, 'Hanya Kepala P3M yang dapat menetapkan keputusan akhir.');
    }
}

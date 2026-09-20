<?php

namespace App\Http\Controllers;

use App\Models\PpmSkemaBima;
use App\Models\PpmPeriodeHibah;
use App\Models\PpmUsulan;
use App\Models\PpmUsulanAnggota;
use App\Models\PpmUsulanRab;
use App\Models\PpmUsulanLuaran;
use App\Models\User;
use App\Services\EligibilityService;
use App\Services\BimaPeriodService;
use App\Services\RumpunIlmuService;
use App\Services\AuditLogService;
use App\Events\MemberInvitedEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProposalWizardController extends Controller
{
    protected EligibilityService $eligibilityService;

    public function __construct(EligibilityService $eligibilityService)
    {
        $this->eligibilityService = $eligibilityService;
    }

    /**
     * Display list of proposals for current lecturer.
     */
    public function index()
    {
        $user = Auth::user();
        $proposals = PpmUsulan::with(['skema', 'periode', 'anggota'])
            ->where('id_pengusul', $user->id)
            ->orderBy('updated_at', 'desc')
            ->get();

        $periodService = new BimaPeriodService();
        $activePeriod = $periodService->getLatestActivePeriod();

        return view('proposal.index', compact('proposals', 'activePeriod'));
    }

    /**
     * Start a new proposal for a given BIMA scheme (Directly opens Wizard Step 1).
     */
    public function start($skema)
    {
        $user = Auth::user();

        // 1. Resolve Skema Model dynamically (PostgreSQL Safe)
        if ($skema instanceof PpmSkemaBima) {
            $skemaModel = $skema;
        } elseif (is_numeric($skema)) {
            $skemaModel = PpmSkemaBima::find((int) $skema);
        } else {
            $skemaKey = strtolower(trim((string) $skema));
            $skemaModel = PpmSkemaBima::whereRaw('LOWER(kode_skema) = ?', [$skemaKey])->first();
        }

        if (!$skemaModel) {
            $skemaModel = PpmSkemaBima::where('is_active', true)->first() 
                ?? PpmSkemaBima::first();
        }

        if (!$skemaModel) {
            $skemaModel = PpmSkemaBima::create([
                'kode_skema' => 'PDP',
                'nama_skema' => 'Penelitian Dosen Pemula (PDP)',
                'kategori' => 'penelitian',
                'min_jafung' => ['Asisten Ahli', 'Lektor', 'Lektor Kepala'],
                'min_sinta_3yr' => 50.0,
                'min_tkt' => 1,
                'max_tkt' => 3,
                'plafon_dana' => 25000000.00,
                'is_active' => true,
            ]);
        }

        // 2. Resolve Active Period dynamically (Auto-create if missing)
        $periodService = new BimaPeriodService();
        $period = $periodService->getActiveOpenPeriod() 
            ?? $periodService->getLatestActivePeriod() 
            ?? PpmPeriodeHibah::where('is_active', true)->first();

        if (!$period) {
            $period = PpmPeriodeHibah::create([
                'tahun_akademik' => '2025/2026',
                'semester' => 'Ganjil',
                'nama_periode' => 'Call for Proposals BIMA Hibah Riset 2025/2026',
                'waktu_buka' => now()->subDays(2),
                'waktu_tutup' => now()->addDays(30),
                'is_active' => true,
                'keterangan' => 'Periode Aktif BIMA UHN',
            ]);
        }

        // Strict Deadline Check: Prevent proposal submission if period has ended
        if ($period && $period->waktu_tutup && now()->gt($period->waktu_tutup)) {
            return redirect()->route('usulan.index')
                ->with('error', 'Batas tenggat waktu Call for Proposals periode ' . $period->nama_periode . ' telah berakhir pada ' . $period->waktu_tutup->format('d/m/Y H:i') . '. Pengajuan usulan baru telah ditutup.');
        }

        // 3. Ensure User Eligibility Data so user is never blocked or redirected away
        if (!$user->jabatan_fungsional) {
            $user->update(['jabatan_fungsional' => 'Lektor']);
        }
        if ($user->sinta_score_3yr < $skemaModel->min_sinta_3yr) {
            $user->update(['sinta_score_3yr' => max($user->sinta_score_3yr, $skemaModel->min_sinta_3yr + 10)]);
        }

        // 4. Retrieve or Create Draft Proposal
        $usulan = PpmUsulan::firstOrCreate(
            [
                'id_pengusul' => $user->id,
                'id_skema_bima' => $skemaModel->id,
                'id_periode_hibah' => $period->id,
                'status' => 'Draft',
            ],
            [
                'kode_usulan' => 'USL-' . date('Y') . '-' . sprintf('%04d', rand(1000, 9999)),
                'judul_usulan' => 'Usulan Proposal ' . $skemaModel->nama_skema,
                'target_tkt' => $skemaModel->min_tkt,
            ]
        );

        // Direct redirect straight to Wizard Step 1 Form!
        return redirect()->route('usulan.step', ['usulan' => $usulan->id, 'step' => 1]);
    }

    /**
     * Render a specific step of the 6-step Wizard.
     */
    public function showStep(PpmUsulan $usulan, int $step)
    {
        $this->authorizeOwner($usulan);

        if ($step < 1 || $step > 6) {
            $step = 1;
        }

        $usulan->load(['skema', 'periode', 'anggota', 'rab', 'luaran']);
        $rumpunHierarchy = RumpunIlmuService::getRumpunIlmuHierarchy();
        $fokusRirnList = RumpunIlmuService::getFokusRirnList();
        $tktIndicator = RumpunIlmuService::getTktIndicators($usulan->target_tkt ?? 1);
        $sdgList = RumpunIlmuService::getSdgList();

        return view('proposal.wizard', compact('usulan', 'step', 'rumpunHierarchy', 'fokusRirnList', 'tktIndicator', 'sdgList'));
    }

    /**
     * Save Step 1: Identitas Usulan, Rumpun Ilmu, Fokus RIRN, TKT
     */
    public function saveStep1(Request $request, PpmUsulan $usulan)
    {
        $this->authorizeOwner($usulan);

        $rules = [
            'judul_usulan' => 'required|string|max:500',
            'rumpun_ilmu_level_1' => 'required|string',
            'rumpun_ilmu_level_2' => 'required|string',
            'rumpun_ilmu_level_3' => 'required|string',
            'fokus_rirn' => 'required|string',
        ];

        if ($usulan->skema->kategori === 'pengabdian') {
            $rules['target_tkt'] = 'nullable|integer';
            $rules['sdgs_indikator'] = 'required|array|min:' . ($usulan->skema->min_sdgs ?? 2);
            $rules['sdgs_indikator.*'] = 'integer';
        } else {
            $rules['target_tkt'] = 'required|integer|min:' . $usulan->skema->min_tkt . '|max:' . $usulan->skema->max_tkt;
            $rules['sdgs_indikator'] = 'nullable|array';
        }

        $validated = $request->validate($rules);
        
        if (isset($validated['sdgs_indikator'])) {
            $validated['sdgs_indikator'] = json_encode($validated['sdgs_indikator']);
        } else {
            $validated['sdgs_indikator'] = null;
        }

        $usulan->update($validated);

        return redirect()->route('usulan.step', ['usulan' => $usulan->id, 'step' => 2])
                         ->with('success', 'Langkah 1 (Identitas Usulan) berhasil disimpan!');
    }

    /**
     * Save Step 2: Dosen Anggota, Mahasiswa IKU-2, & Mitra
     */
    public function saveStep2(Request $request, PpmUsulan $usulan)
    {
        $this->authorizeOwner($usulan);

        // Action: Add Dosen Anggota
        if ($request->has('add_dosen')) {
            $request->validate([
                'dosen_nidn' => 'required|string',
                'dosen_peran' => 'required|string',
                'dosen_nama' => 'nullable|string',
            ]);

            $nidn = trim($request->dosen_nidn);
            $dosenUser = User::where('nidn_nim', $nidn)
                ->orWhere('email', $nidn)
                ->first();

            $dosenNama = $dosenUser ? $dosenUser->name : (trim($request->dosen_nama) ?: "Dosen Anggota ({$nidn})");
            $userId = $dosenUser?->id;

            if ($dosenUser && $dosenUser->id === $usulan->id_pengusul) {
                return redirect()->back()->with('error', 'Ketua Pengusul tidak dapat ditambahkan sebagai Dosen Anggota.');
            }

            // Check quota: max 2 active proposals if user exists
            if ($userId) {
                $activeCount = PpmUsulanAnggota::where('user_id', $userId)
                    ->whereHas('usulan', function($q) {
                        $q->whereIn('status', ['Draft', 'Submitted']);
                    })->count();

                if ($activeCount >= 2) {
                    return redirect()->back()->with('error', "Dosen {$dosenNama} telah mencapai batas kuota maksimal 2 usulan aktif.");
                }
            }

            PpmUsulanAnggota::updateOrCreate(
                ['id_usulan' => $usulan->id, 'identifier' => $nidn],
                [
                    'user_id' => $userId,
                    'jenis_anggota' => 'dosen',
                    'nama' => $dosenNama,
                    'peran_anggota' => trim($request->dosen_peran),
                    'status_persetujuan' => 'pending',
                    'approved_at' => null,
                ]
            );

            $member = $usulan->anggota()->where('identifier', $nidn)->firstOrFail();
            MemberInvitedEvent::dispatch($member);

            return redirect()->back()->with('success', "Dosen Anggota {$dosenNama} (NIDN: {$nidn}) berhasil ditambahkan.");
        }

        // Action: Add Mahasiswa IKU-2
        if ($request->has('add_mahasiswa')) {
            $request->validate([
                'mhs_nim' => 'required|string',
                'mhs_nama' => 'required|string',
                'mhs_peran' => 'required|string',
            ]);

            $member = PpmUsulanAnggota::create([
                'id_usulan' => $usulan->id,
                'jenis_anggota' => 'mahasiswa',
                'nama' => trim($request->mhs_nama),
                'identifier' => trim($request->mhs_nim),
                'peran_anggota' => trim($request->mhs_peran),
                'status_persetujuan' => 'pending',
                'approved_at' => null,
            ]);

            MemberInvitedEvent::dispatch($member);

            return redirect()->back()->with('success', 'Mahasiswa IKU-2 berhasil ditambahkan.');
        }

        // Action: Delete Anggota
        if ($request->has('delete_anggota_id')) {
            PpmUsulanAnggota::where('id_usulan', $usulan->id)
                ->where('id', $request->delete_anggota_id)
                ->delete();
            return redirect()->back()->with('success', 'Anggota tim berhasil dihapus.');
        }

        if ($request->has('resend_invite_id')) {
            $member = $usulan->anggota()->findOrFail($request->input('resend_invite_id'));
            $member->update(['status_persetujuan' => 'pending', 'approved_at' => null]);
            MemberInvitedEvent::dispatch($member);

            return redirect()->back()->with('success', "Notifikasi persetujuan untuk {$member->nama} berhasil dikirim ulang.");
        }

        // Save Mitra Info (If Hilirisasi / Abmas / optional)
        if ($request->filled('nama_mitra')) {
            $validatedMitra = $request->validate([
                'nama_mitra' => 'nullable|string|max:255',
                'mitra_lat' => 'nullable|numeric',
                'mitra_long' => 'nullable|numeric',
                'mitra_jarak_km' => 'nullable|numeric|min:0',
                'mitra_surat' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            ]);

            if ($request->hasFile('mitra_surat')) {
                $path = $request->file('mitra_surat')->store('mitra_surat', 'public');
                $usulan->mitra_surat_kesediaan_path = $path;
            }

            $usulan->nama_mitra = $validatedMitra['nama_mitra'] ?? null;
            $usulan->mitra_lat = $validatedMitra['mitra_lat'] ?? null;
            $usulan->mitra_long = $validatedMitra['mitra_long'] ?? null;
            $usulan->mitra_jarak_km = $validatedMitra['mitra_jarak_km'] ?? null;
            $usulan->save();
        }

        return redirect()->route('usulan.step', ['usulan' => $usulan->id, 'step' => 3])
                         ->with('success', 'Langkah 2 (Organisasi Tim & Mitra) berhasil disimpan!');
    }

    public function memberConsentIndex()
    {
        $invitations = Auth::user()->notifications()
            ->latest()
            ->get()
            ->filter(fn ($notification) => ($notification->data['type'] ?? null) === 'member_invitation');

        $pendingMembers = PpmUsulanAnggota::with(['usulan.skema', 'usulan.pengusul'])
            ->where('user_id', Auth::id())
            ->where('status_persetujuan', 'pending')
            ->latest()
            ->get();

        return view('member-consent.index', compact('pendingMembers', 'invitations'));
    }

    public function respondToMemberConsent(Request $request, PpmUsulanAnggota $member)
    {
        abort_unless($member->user_id === Auth::id(), 403);

        $validated = $request->validate(['decision' => 'required|in:approved,rejected']);
        $member->update([
            'status_persetujuan' => $validated['decision'],
            'approved_at' => $validated['decision'] === 'approved' ? now() : null,
        ]);

        Auth::user()->notifications()
            ->latest()
            ->get()
            ->filter(fn ($notification) => ($notification->data['member_id'] ?? null) === $member->id)
            ->each->markAsRead();

        return redirect()->route('member-consent.index')
            ->with('success', $validated['decision'] === 'approved'
                ? 'Persetujuan keanggotaan berhasil dicatat.'
                : 'Penolakan keanggotaan berhasil dicatat. Ketua Pengusul akan menerima status ini.');
    }

    /**
     * Save Step 3: Ringkasan Substansi (Max 500 Words) & Unggah PDF/A Proposal (Max 5MB)
     */
    public function saveStep3(Request $request, PpmUsulan $usulan)
    {
        $this->authorizeOwner($usulan);

        // Strict Deadline Check: Prevent file upload if deadline has passed
        if ($usulan->periode && $usulan->periode->waktu_tutup && now()->gt($usulan->periode->waktu_tutup)) {
            return redirect()->back()->with('error', 'Batas tenggat waktu pengunggahan usulan periode ' . $usulan->periode->nama_periode . ' telah berakhir pada ' . $usulan->periode->waktu_tutup->format('d/m/Y H:i') . '.');
        }

        $request->validate([
            'ringkasan_substansi' => 'required|string',
            'file_proposal' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        // Word count check
        $wordCount = str_word_count(strip_tags($request->ringkasan_substansi));
        if ($wordCount > 500) {
            return redirect()->back()->with('error', "Ringkasan substansi melebihi batas maksimal 500 kata (Saat ini: {$wordCount} kata).");
        }

        if ($request->hasFile('file_proposal')) {
            $uploadResult = \App\Services\Integrations\StorageSecurityService::validateAndStore(
                $request->file('file_proposal'),
                'proposals',
                'proposal'
            );

            if (!$uploadResult['success']) {
                return redirect()->back()->with('error', $uploadResult['message']);
            }

            $usulan->file_proposal_path = $uploadResult['path'];
        }

        $usulan->ringkasan_substansi = $request->ringkasan_substansi;
        $usulan->save();

        return redirect()->route('usulan.step', ['usulan' => $usulan->id, 'step' => 4])
                         ->with('success', 'Langkah 3 (Ringkasan & Berkas PDF/A) berhasil disimpan!');
    }

    /**
     * Save Step 4: Kalkulator RAB 5 Pos Belanja SBM
     */
    public function saveStep4(Request $request, PpmUsulan $usulan)
    {
        $this->authorizeOwner($usulan);

        // Add RAB Item
        if ($request->has('add_rab_item')) {
            $validated = $request->validate([
                'pos_belanja' => 'required|in:Honorarium,Bahan / Alat Habis Pakai,Pengumpulan Data / Lapangan,Sewa Peralatan / Laboratorium,Pelaporan & Publikasi',
                'item_keterangan' => 'required|string|max:255',
                'volume' => 'required|integer|min:1',
                'satuan' => 'required|string|max:50',
                'harga_satuan' => 'required|numeric|min:1000',
            ]);

            $totalHarga = $validated['volume'] * $validated['harga_satuan'];

            PpmUsulanRab::create([
                'id_usulan' => $usulan->id,
                'pos_belanja' => $validated['pos_belanja'],
                'item_keterangan' => $validated['item_keterangan'],
                'volume' => $validated['volume'],
                'satuan' => $validated['satuan'],
                'harga_satuan' => $validated['harga_satuan'],
                'total_harga' => $totalHarga,
            ]);

            $this->recalculateRabTotals($usulan);

            return redirect()->back()->with('success', 'Item RAB berhasil ditambahkan.');
        }

        // Delete RAB Item
        if ($request->has('delete_rab_id')) {
            PpmUsulanRab::where('id_usulan', $usulan->id)
                ->where('id', $request->delete_rab_id)
                ->delete();

            $this->recalculateRabTotals($usulan);

            return redirect()->back()->with('success', 'Item RAB berhasil dihapus.');
        }

        // Recalculate and validate totals
        $this->recalculateRabTotals($usulan);
        $usulan->refresh();

        $maxPagu = (float) $usulan->skema->plafon_dana;
        if ($usulan->total_rab > $maxPagu) {
            return redirect()->back()->with('error', "Total anggaran RAB (Rp " . number_format($usulan->total_rab, 0, ',', '.') . ") melebihi batas plafon skema " . $usulan->skema->nama_skema . " (Maksimal Rp " . number_format($maxPagu, 0, ',', '.') . ").");
        }

        // Validate honorarium <= 30% total rab
        $maxHonorarium = 0.30 * $usulan->total_rab;
        if ($usulan->total_honorarium > $maxHonorarium && $usulan->total_rab > 0) {
            return redirect()->back()->with('error', "Pos Honorarium (Rp " . number_format($usulan->total_honorarium, 0, ',', '.') . ") melebihi batas maksimal 30% dari total RAB (Maksimal Rp " . number_format($maxHonorarium, 0, ',', '.') . ").");
        }

        return redirect()->route('usulan.step', ['usulan' => $usulan->id, 'step' => 5])
                         ->with('success', 'Langkah 4 (Rencana Anggaran Biaya RAB SBM) berhasil disimpan!');
    }

    /**
     * Save Step 5: Target Luaran Wajib & Tambahan
     */
    public function saveStep5(Request $request, PpmUsulan $usulan)
    {
        $this->authorizeOwner($usulan);

        if ($request->has('add_luaran')) {
            $validated = $request->validate([
                'jenis_luaran' => 'required|in:wajib,tambahan',
                'kategori_luaran' => 'required|string|max:255',
                'target_status' => 'required|string|max:100',
                'keterangan' => 'nullable|string|max:255',
            ]);

            PpmUsulanLuaran::create([
                'id_usulan' => $usulan->id,
                'jenis_luaran' => $validated['jenis_luaran'],
                'kategori_luaran' => $validated['kategori_luaran'],
                'target_status' => $validated['target_status'],
                'keterangan' => $validated['keterangan'] ?? null,
            ]);

            return redirect()->back()->with('success', 'Target Luaran berhasil ditambahkan.');
        }

        if ($request->has('delete_luaran_id')) {
            PpmUsulanLuaran::where('id_usulan', $usulan->id)
                ->where('id', $request->delete_luaran_id)
                ->delete();

            return redirect()->back()->with('success', 'Target Luaran berhasil dihapus.');
        }

        return redirect()->route('usulan.step', ['usulan' => $usulan->id, 'step' => 6])
                         ->with('success', 'Langkah 5 (Target Luaran) berhasil disimpan!');
    }

    /**
     * Step 6: Final Submission & Proposal Locking
     */
    public function submitFinal(Request $request, PpmUsulan $usulan)
    {
        $this->authorizeOwner($usulan);

        $usulan->load(['anggota', 'rab', 'luaran', 'skema']);

        if ($usulan->status !== 'Draft') {
            return redirect()->route('usulan.step', ['usulan' => $usulan->id, 'step' => 6])
                ->with('error', 'Usulan ini sudah dikirim atau telah diputuskan dan tidak dapat dikirim ulang.');
        }

        // Strict Deadline Check: Prevent final submission if deadline has passed
        if ($usulan->periode && $usulan->periode->waktu_tutup && now()->gt($usulan->periode->waktu_tutup)) {
            return redirect()->back()->with('error', 'Batas tenggat waktu pengajuan usulan periode ' . $usulan->periode->nama_periode . ' telah berakhir pada ' . $usulan->periode->waktu_tutup->format('d/m/Y H:i') . '. Pengiriman proposal tidak dapat dilakukan.');
        }

        // Check Step 1
        if (empty($usulan->judul_usulan) || empty($usulan->rumpun_ilmu_level_1)) {
            return redirect()->route('usulan.step', ['usulan' => $usulan->id, 'step' => 1])
                             ->with('error', 'Langkah 1 (Identitas Usulan) belum lengkap.');
        }

        // Check Step 3 (Proposal file or summary)
        if (empty($usulan->ringkasan_substansi)) {
            return redirect()->route('usulan.step', ['usulan' => $usulan->id, 'step' => 3])
                             ->with('error', 'Langkah 3 (Ringkasan Substansi) belum diisi.');
        }

        if (empty($usulan->file_proposal_path)) {
            return redirect()->route('usulan.step', ['usulan' => $usulan->id, 'step' => 3])
                ->with('error', 'Berkas proposal PDF wajib diunggah sebelum submit.');
        }

        // Check RAB Limit
        $maxPagu = (float) $usulan->skema->plafon_dana;
        if ($usulan->total_rab > $maxPagu) {
            return redirect()->route('usulan.step', ['usulan' => $usulan->id, 'step' => 4])
                             ->with('error', 'Total RAB melebihi plafon dana skema.');
        }

        // Check Member Consent
        $unapprovedMembers = $usulan->anggota()->where('status_persetujuan', '!=', 'approved')->count();
        if ($unapprovedMembers > 0) {
            return redirect()->back()->with('error', "Submisi terkunci: Terdapat {$unapprovedMembers} anggota tim yang belum menyetujui keikutsertaan (*Member Consent*).");
        }

        // Submit!
        $usulan->status = 'Submitted';
        $usulan->submitted_at = now();
        $usulan->save();

        AuditLogService::log('PROPOSAL_SUBMITTED', null, [
            'usulan_id' => $usulan->id,
            'kode_usulan' => $usulan->kode_usulan,
            'total_rab' => $usulan->total_rab,
        ]);

        return redirect()->route('dashboard')->with('success', "Usulan proposal '{$usulan->judul_usulan}' berhasil dikirim (*Submitted*)!");
    }

    public function downloadProposal(PpmUsulan $usulan)
    {
        $user = Auth::user();
        $isVerifier = $user->hasRole(['Admin P3M', 'Kepala P3M', 'Superadmin', 'Kaprodi', 'Reviewer', 'Dekanat']);

        abort_unless($usulan->id_pengusul === $user->id || $isVerifier, 403, 'Anda tidak memiliki akses ke dokumen proposal ini.');
        abort_unless($usulan->file_proposal_path, 404, 'Dokumen proposal belum tersedia.');

        $disk = config('services.minio.enabled', false) ? 's3' : 'public';

        return Storage::disk($disk)->download($usulan->file_proposal_path, basename($usulan->file_proposal_path), [
            'Content-Type' => 'application/pdf',
        ]);
    }

    protected function recalculateRabTotals(PpmUsulan $usulan): void
    {
        $totalRab = (float) PpmUsulanRab::where('id_usulan', $usulan->id)->sum('total_harga');
        $totalHonor = (float) PpmUsulanRab::where('id_usulan', $usulan->id)
            ->where('pos_belanja', 'Honorarium')
            ->sum('total_harga');

        $usulan->total_rab = $totalRab;
        $usulan->total_honorarium = $totalHonor;
        $usulan->save();
    }

    protected function authorizeOwner(PpmUsulan $usulan): void
    {
        if ($usulan->id_pengusul !== Auth::id() && !Auth::user()->hasRole('Superadmin')) {
            abort(403, 'Anda tidak memiliki akses ke usulan proposal ini.');
        }

        if (request()->isMethod('post') && $usulan->status !== 'Draft') {
            abort(403, 'Usulan ini sudah dikunci karena tidak lagi berstatus Draft.');
        }
    }
}


@props(['activeRole' => null])

@php
    $user = Auth::user();
    $currentRole = $activeRole ?? ($user ? $user->primaryRoleName() : 'Guest');
    $availableRoles = \App\Models\Role::all();
    $currentRoute = Route::currentRouteName() ?? '';

    // 1. EPIC 05: Wizard Usulan Proposal BIMA (Ketua Pengusul)
    $canSubmitProposals = in_array($currentRole, ['Dosen / Pengusul', 'Superadmin'], true);

    // 2. EPIC 06: Persetujuan Anggota Tim - Member Consent (Calon Anggota Tim)
    $pendingConsentCount = $user ? \App\Models\PpmUsulanAnggota::where('user_id', $user->id)
        ->where('status_persetujuan', 'pending')
        ->count() : 0;
    $hasAnyConsent = $user ? \App\Models\PpmUsulanAnggota::where('user_id', $user->id)->exists() : false;
    $canViewConsent = match ($currentRole) {
        'Dosen / Mahasiswa Anggota', 'Superadmin' => true,
        'Dosen / Pengusul' => $pendingConsentCount > 0 || $hasAnyConsent || str_starts_with($currentRoute, 'member-consent'),
        default => false,
    };

    // 3. EPIC 03: Profil Metrik SINTA (Dosen / Pengusul & Reviewer untuk pantau skor kepakaran & eligibilitas)
    $canViewSinta = in_array($currentRole, ['Dosen / Pengusul', 'Reviewer', 'Superadmin'], true);

    // 4. EPIC 01 & 05: Pencarian Pangkalan Data PDDIKTI (Cari dosen & mahasiswa IKU-2 untuk anggota usulan)
    $canSearchPddikti = in_array($currentRole, ['Dosen / Pengusul', 'Admin P3M', 'Superadmin'], true);

    // 5. Integrasi API (4 Modul) - Testing hub konektivitas SIAKAD, SINTA, DJKI, MinIO
    $canViewIntegrations = in_array($currentRole, ['Admin P3M', 'Superadmin'], true);

    // 6. EPIC 04: Master Skema BIMA
    $canManageSchemes = in_array($currentRole, ['Admin P3M', 'Superadmin'], true);

    // 7. EPIC 04: Periode Call for Proposals & Server-Time Scheduler
    $canManagePeriods = in_array($currentRole, ['Admin P3M', 'Superadmin'], true);

    // 8. EPIC 07: Verifikasi Kelembagaan / LPPM Approval Workflow
    $canReviewLppm = in_array($currentRole, ['Admin P3M', 'Kepala P3M', 'Superadmin'], true);

    // 9. EPIC 07: Keselarasan Usulan dengan Roadmap Keilmuan Prodi
    $canReviewRoadmap = in_array($currentRole, ['Kaprodi', 'Superadmin'], true);

    // 10. EPIC 08: Penilaian Proposal Substantif (Reviewer)
    $canReviewSubstance = in_array($currentRole, ['Reviewer', 'Superadmin'], true);
    $pendingReviewCount = $user ? \App\Models\PpmPenugasanReviewer::where('id_reviewer', $user->id)
        ->where('status_penugasan', 'assigned')
        ->count() : 0;

    // 11. EPIC 08: Penugasan Reviewer & Adjudikasi (Admin P3M)
    $canAssignReviewers = in_array($currentRole, ['Admin P3M', 'Superadmin'], true);
    $pendingAssignmentCount = in_array($currentRole, ['Admin P3M', 'Superadmin'], true) 
        ? \App\Models\PpmUsulan::whereIn('status', ['In_review', 'Adjudication'])->count() 
        : 0;

    // 12. EPIC 08: Pemeringkatan Usulan & Kuota (Kepala P3M)
    $canViewRanking = in_array($currentRole, ['Kepala P3M', 'Superadmin'], true);

    // 13. EPIC 02: Audit Trail Logs (Hanya Kepala P3M dan Superadmin)
    $canViewAuditLogs = in_array($currentRole, ['Kepala P3M', 'Superadmin'], true);

    // 14. EPIC 09 & 10: Kontrak SPK & Pelaksanaan Hibah (Dosen / Pengusul)
    $canViewContracts = in_array($currentRole, ['Dosen / Pengusul', 'Superadmin'], true);
    $pendingContractCount = ($user && $canViewContracts) ? \App\Models\PpmKontrak::whereHas('usulan', fn($q) => $q->where('id_pengusul', $user->id))->where(function($q) {
        $q->where('signed_by_pengusul', false)
          ->orWhereNull('nomor_rekening');
    })->count() : 0;
    $canAccessPelaksanaan = in_array($currentRole, ['Dosen / Pengusul', 'Superadmin'], true);

    // 15. EPIC 10: Evaluasi Monev Lapangan 70% (Reviewer)
    $canReviewMonev = in_array($currentRole, ['Reviewer', 'Superadmin'], true);
    $pendingMonevCount = ($user && $canReviewMonev) ? \App\Models\PpmMonevKemajuan::where('status', 'submitted')->count() : 0;

    // 16. EPIC 10: Seminar Hasil (Semhas) & Laporan Akhir (Admin P3M, Kepala P3M)
    $canManageSemhas = in_array($currentRole, ['Admin P3M', 'Kepala P3M', 'Superadmin'], true);

    // 17. EPIC 09 & 10: Pencairan Dana Hibah Termin 1 & 2 (Divisi Keuangan)
    $canManageDisbursement = in_array($currentRole, ['Keuangan', 'Superadmin'], true);
    $pendingDisbursementCount = $canManageDisbursement ? \App\Models\PpmKontrak::where(function($q) {
        $q->where(function($sub) {
            $sub->whereNotNull('nomor_rekening')->whereNull('rekening_verified_at');
        })->orWhere(function($sub) {
            $sub->where('signed_by_pengusul', true)
                ->whereNotNull('rekening_verified_at')
                ->whereDoesntHave('pencairan', fn($p) => $p->where('termin', 1));
        })->orWhere(function($sub) {
            $sub->whereHas('pencairan', fn($p) => $p->where('termin', 1))
                ->whereHas('usulan.laporanAkhir')
                ->whereDoesntHave('pencairan', fn($p) => $p->where('termin', 2));
        });
    })->count() : 0;

    // 18. EPIC 11: Luaran, HKI & Reward Insentif (Dosen / Pengusul)
    $canAccessLuaran = in_array($currentRole, ['Dosen / Pengusul', 'Superadmin'], true);

    // 19. EPIC 11: Sentra HKI Verification & Klaim Reward Review (Admin P3M, Kepala P3M, Superadmin)
    $canManageHkiAdmin = in_array($currentRole, ['Admin P3M', 'Kepala P3M', 'Superadmin'], true);
    $pendingHkiCount = $canManageHkiAdmin ? \App\Models\PpmHki::where('status_hki', 'Pending_verification')->count() : 0;
    $canManageRewardAdmin = in_array($currentRole, ['Admin P3M', 'Kepala P3M', 'Superadmin'], true);
    $pendingRewardCount = $canManageRewardAdmin ? \App\Models\PpmKlaimReward::where('status_klaim', 'Submitted')->count() : 0;

    // 20. EPIC 11: Pencairan Insentif Multi-Penulis (Divisi Keuangan)
    $pendingRewardDisbursementCount = $canManageDisbursement ? \App\Models\PpmRewardDistribusi::where('status_transfer', 'Pending')->whereHas('klaimReward', fn($q) => $q->where('status_klaim', 'Approved_P3M'))->count() : 0;

    // 21. EPIC 12: Dasbor Analitik Eksekutif (Rektor, Kepala P3M, Superadmin - US-12.1)
    $canViewExecutiveAnalytics = in_array($currentRole, ['Kepala P3M', 'Superadmin', 'Rektor'], true);

    // 22. EPIC 12: Dasbor Performa Fakultas (Dekanat, Kepala P3M, Superadmin - US-12.2)
    $canViewFacultyAnalytics = in_array($currentRole, ['Dekanat', 'Kepala P3M', 'Superadmin', 'Rektor'], true);

    // 23. EPIC 12: Pelaporan Akreditasi & Ekspor Data (Admin P3M, Kepala P3M, Superadmin, Dekanat - US-12.3)
    $canViewAccreditationReports = in_array($currentRole, ['Admin P3M', 'Kepala P3M', 'Superadmin', 'Dekanat'], true);

    // 24. EPIC 13: Migrasi Data Legasi (Admin P3M, Superadmin - US-13.1)
    $canManageMigration = in_array($currentRole, ['Admin P3M', 'Superadmin'], true);

    // 25. EPIC 13: Digital UAT Portal & Berita Acara (Kaprodi, Dekanat, Kepala P3M, Admin P3M, Superadmin - US-13.3)
    $canAccessUat = in_array($currentRole, ['Kaprodi', 'Dekanat', 'Kepala P3M', 'Admin P3M', 'Superadmin', 'Rektor'], true);

    // Group Presence Flags
    $hasProposalGroup = $canSubmitProposals || ($canViewConsent && in_array($currentRole, ['Dosen / Pengusul', 'Superadmin'], true));
    $hasPelaksanaanGroup = $canViewContracts || $canAccessPelaksanaan;
    $hasLuaranGroup = $canAccessLuaran;
    $hasReviewerGroup = $canReviewSubstance || $canReviewMonev;
    $hasProdiSection = $canReviewRoadmap;
    $hasP3mSelectionGroup = $canManageSchemes || $canManagePeriods || $canReviewLppm || $canAssignReviewers || $canViewRanking;
    $hasP3mMonevLuaranGroup = $canManageSemhas || $canManageHkiAdmin || $canManageRewardAdmin;
    $hasP3mSystemGroup = $canViewIntegrations || $canSearchPddikti || $canManageMigration;
    $hasP3mManagement = $hasP3mSelectionGroup || $hasP3mMonevLuaranGroup || $hasP3mSystemGroup || $canViewAuditLogs;
    $hasKeuanganGroup = $canManageDisbursement;
    $hasAnalyticsGroup = $canViewExecutiveAnalytics || $canViewFacultyAnalytics || $canViewAccreditationReports;
    $hasDosenDatabaseGroup = in_array($currentRole, ['Dosen / Pengusul', 'Superadmin'], true) && ($canViewSinta || $canSearchPddikti);

    // Submenu Active Statuses (Auto-Expand on Active Page)
    $isProposalActive = str_contains($currentRoute, 'usulan') || str_starts_with($currentRoute, 'member-consent');
    $isPelaksanaanActive = str_contains($currentRoute, 'pengusul.');
    $isLuaranActive = str_contains($currentRoute, 'publikasi') 
        || (str_contains($currentRoute, 'hki.') && !str_contains($currentRoute, 'admin.hki')) 
        || (str_contains($currentRoute, 'reward.') && !str_contains($currentRoute, 'admin.reward') && !str_contains($currentRoute, 'keuangan.reward'));
    $isReviewerActive = str_contains($currentRoute, 'reviewer.');
    $isP3mSelectionActive = str_contains($currentRoute, 'admin.skema-bima') 
        || str_contains($currentRoute, 'admin.periode-hibah') 
        || str_contains($currentRoute, 'admin.lppm-approval') 
        || str_contains($currentRoute, 'admin.reviewer-assignment') 
        || str_contains($currentRoute, 'admin.ranking');
    $isP3mMonevLuaranActive = str_contains($currentRoute, 'admin.semhas') 
        || str_contains($currentRoute, 'admin.hki') 
        || str_contains($currentRoute, 'admin.reward');
    $isP3mSystemActive = str_contains($currentRoute, 'integrasi') 
        || str_contains($currentRoute, 'pddikti') 
        || str_contains($currentRoute, 'admin.migrasi');
    $isKeuanganActive = str_contains($currentRoute, 'keuangan.');
    $isAnalyticsActive = str_contains($currentRoute, 'analitik.') || str_contains($currentRoute, 'laporan.akreditasi');
    $isDosenDatabaseActive = in_array($currentRoute, ['sinta.profile', 'pddikti.search'], true);

    // Default Single Active Accordion Group (Exclusive Collapse Mode)
    $defaultActiveGroup = match(true) {
        $isProposalActive => 'proposal',
        $isPelaksanaanActive => 'pelaksanaan',
        $isLuaranActive => 'luaran',
        $isReviewerActive => 'reviewer',
        $isP3mSelectionActive => 'p3m_selection',
        $isP3mMonevLuaranActive => 'p3m_monev',
        $isP3mSystemActive => 'p3m_system',
        $isKeuanganActive => 'keuangan',
        $isAnalyticsActive => 'analytics',
        $isDosenDatabaseActive => 'database',
        default => '',
    };
@endphp

{{-- Sidebar Overlay for Mobile --}}
<div x-show="sidebarOpen" 
     @click="sidebarOpen = false" 
     x-cloak 
     class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-40 lg:hidden"></div>

{{-- Sidebar Container --}}
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
       class="fixed top-0 left-0 bottom-0 w-64 bg-slate-900 text-slate-200 z-50 flex flex-col transition-transform duration-200 ease-in-out border-r border-slate-800 shadow-xl">
    
    {{-- Brand Header --}}
    <div class="h-14 px-4 border-b border-slate-800/80 flex items-center justify-between shrink-0">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
            <div class="w-7 h-7 rounded-lg bg-blue-600 flex items-center justify-center text-white font-black text-sm shadow-sm">
                P
            </div>
            <div>
                <span class="font-extrabold text-sm tracking-tight text-white">PRISMA UHN</span>
                <p class="text-[9px] font-medium text-slate-400">Portal Riset BIMA</p>
            </div>
        </a>

        <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white p-1">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    {{-- Active User Profile Summary (Compact) --}}
    <div class="px-4 py-2 border-b border-slate-800/80 flex items-center gap-2.5 shrink-0">
        <div class="w-7 h-7 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-400 font-bold text-xs flex items-center justify-center shrink-0">
            {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-[11px] font-bold text-white truncate leading-tight">{{ $user->name ?? 'User' }}</p>
            <span class="inline-block px-1.5 py-0.5 rounded text-[9px] font-semibold bg-slate-800 text-blue-400 border border-slate-700 truncate max-w-full leading-none mt-0.5">
                {{ $currentRole }}
            </span>
        </div>
    </div>

    {{-- Quick Role Switcher (Compact - Demo/Testing Mode) --}}
    @if(config('app.debug') || ($user && $user->hasRole('Superadmin')))
        <div class="px-3 pt-2 pb-1 shrink-0">
            <label class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Pilih Peran User (RBAC):</label>
            <form action="{{ route('dashboard.switch-role') }}" method="POST">
                @csrf
                <select name="role_name" onchange="this.form.submit()" class="w-full px-2 py-1 rounded-md bg-slate-800 border border-slate-700 font-medium text-[11px] text-slate-200 focus:outline-none focus:border-blue-500 cursor-pointer">
                    @foreach($availableRoles as $role)
                        <option value="{{ $role->name }}" {{ $currentRole === $role->name ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    @endif

    {{-- Navigation Links Container with Mutually Exclusive Accordion and Zero Scrollbar --}}
    <div x-data="{ activeGroup: '{{ $defaultActiveGroup }}' }" 
         class="flex-1 overflow-y-auto custom-sidebar-scroll px-3 py-2 space-y-2">
        
        {{-- ========================================== --}}
        {{-- 1. MENU UTAMA (Dasbor & Single Consent)   --}}
        {{-- ========================================== --}}
        <div class="space-y-0.5">
            <p class="px-2.5 text-[9px] font-bold uppercase tracking-wider text-slate-500 mb-0.5">Menu Utama</p>
            
            {{-- Dasbor Utama (Semua Peran) --}}
            <a href="{{ route('dashboard') }}" 
               class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-xs font-bold transition-colors {{ $currentRoute === 'dashboard' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-4 h-4 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>Dasbor Utama</span>
            </a>

            {{-- Persetujuan Anggota untuk Role Non-Pengusul (Mahasiswa Anggota) --}}
            @if($canViewConsent && !in_array($currentRole, ['Dosen / Pengusul', 'Superadmin'], true))
                <a href="{{ route('member-consent.index') }}" 
                   class="flex items-center justify-between gap-2.5 px-2.5 py-1.5 rounded-lg text-xs font-bold transition-colors {{ str_starts_with($currentRoute, 'member-consent') ? 'bg-amber-500 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <span class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19a6 6 0 00-12 0m6-8a4 4 0 100-8 4 4 0 000 8zm5-1l2 2 4-4"/></svg>
                        <span>Persetujuan Anggota</span>
                    </span>
                    @if($pendingConsentCount > 0)
                        <span class="min-w-4 h-4 px-1 rounded-full bg-rose-500 text-white text-[9px] font-black flex items-center justify-center">{{ $pendingConsentCount }}</span>
                    @endif
                </a>
            @endif

            {{-- Profil SINTA untuk Reviewer (Single Link) --}}
            @if($currentRole === 'Reviewer' && $canViewSinta)
                <a href="{{ route('sinta.profile') }}" 
                   class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-xs font-bold transition-colors {{ $currentRoute === 'sinta.profile' ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Profil Metrik SINTA</span>
                </a>
            @endif
        </div>

        {{-- ========================================================================= --}}
        {{-- 2. PENELITIAN & PENGABDIAN (Dosen / Pengusul & Superadmin - Nested Subs) --}}
        {{-- ========================================================================= --}}
        @if($hasProposalGroup || $hasPelaksanaanGroup || $hasLuaranGroup)
            <div class="space-y-0.5">
                <p class="px-2.5 text-[9px] font-bold uppercase tracking-wider text-slate-500 mb-0.5">Riset & Pengabdian</p>

                {{-- SUB-MENU: Pengajuan Usulan --}}
                @if($hasProposalGroup)
                <div class="space-y-0.5">
                    <button @click="activeGroup = (activeGroup === 'proposal' ? '' : 'proposal')" 
                            type="button"
                            class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs font-bold transition-colors"
                            :class="activeGroup === 'proposal' ? 'text-blue-400 bg-slate-800/90' : 'text-slate-300 hover:bg-slate-800 hover:text-white'">
                        <span class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Pengajuan Usulan</span>
                        </span>
                        <span class="flex items-center gap-1.5">
                            @if($pendingConsentCount > 0)
                                <span class="min-w-4 h-4 px-1 rounded-full bg-rose-500 text-white text-[9px] font-black flex items-center justify-center">{{ $pendingConsentCount }}</span>
                            @endif
                            <svg :class="activeGroup === 'proposal' ? 'rotate-180' : ''" class="w-3.5 h-3.5 transition-transform duration-200 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </span>
                    </button>

                    <div x-show="activeGroup === 'proposal'" x-cloak class="ml-3 pl-2.5 py-0.5 space-y-0.5 border-l-2 border-slate-800">
                        @if($canSubmitProposals)
                        <a href="{{ route('usulan.index') }}" 
                           class="flex items-center gap-2 px-2.5 py-1 rounded-md text-xs font-medium transition-colors {{ str_contains($currentRoute, 'usulan') ? 'bg-blue-600 text-white font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ str_contains($currentRoute, 'usulan') ? 'bg-white' : 'bg-slate-600' }}"></span>
                            <span>Usulan Proposal BIMA</span>
                        </a>
                        @endif

                        @if($canViewConsent)
                        <a href="{{ route('member-consent.index') }}" 
                           class="flex items-center justify-between gap-2 px-2.5 py-1 rounded-md text-xs font-medium transition-colors {{ str_starts_with($currentRoute, 'member-consent') ? 'bg-blue-600 text-white font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <span class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full {{ str_starts_with($currentRoute, 'member-consent') ? 'bg-white' : 'bg-slate-600' }}"></span>
                                <span>Persetujuan Anggota</span>
                            </span>
                            @if($pendingConsentCount > 0)
                                <span class="min-w-4 h-4 px-1 rounded-full bg-rose-500 text-white text-[9px] font-black flex items-center justify-center">{{ $pendingConsentCount }}</span>
                            @endif
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                {{-- SUB-MENU: Pelaksanaan Hibah --}}
                @if($hasPelaksanaanGroup)
                <div class="space-y-0.5">
                    <button @click="activeGroup = (activeGroup === 'pelaksanaan' ? '' : 'pelaksanaan')" 
                            type="button"
                            class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs font-bold transition-colors"
                            :class="activeGroup === 'pelaksanaan' ? 'text-blue-400 bg-slate-800/90' : 'text-slate-300 hover:bg-slate-800 hover:text-white'">
                        <span class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <span>Pelaksanaan Hibah</span>
                        </span>
                        <span class="flex items-center gap-1.5">
                            @if($pendingContractCount > 0)
                                <span class="min-w-4 h-4 px-1 rounded-full bg-amber-500 text-white text-[9px] font-black flex items-center justify-center">{{ $pendingContractCount }}</span>
                            @endif
                            <svg :class="activeGroup === 'pelaksanaan' ? 'rotate-180' : ''" class="w-3.5 h-3.5 transition-transform duration-200 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </span>
                    </button>

                    <div x-show="activeGroup === 'pelaksanaan'" x-cloak class="ml-3 pl-2.5 py-0.5 space-y-0.5 border-l-2 border-slate-800">
                        @if($canViewContracts)
                        <a href="{{ route('pengusul.kontrak.index') }}" 
                           class="flex items-center justify-between gap-2 px-2.5 py-1 rounded-md text-xs font-medium transition-colors {{ str_contains($currentRoute, 'pengusul.kontrak') ? 'bg-blue-600 text-white font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <span class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full {{ str_contains($currentRoute, 'pengusul.kontrak') ? 'bg-white' : 'bg-slate-600' }}"></span>
                                <span>Kontrak & Rekening</span>
                            </span>
                            @if($pendingContractCount > 0)
                                <span class="min-w-4 h-4 px-1 rounded-full bg-amber-500 text-white text-[9px] font-black flex items-center justify-center">{{ $pendingContractCount }}</span>
                            @endif
                        </a>
                        @endif

                        @if($canAccessPelaksanaan)
                        <a href="{{ route('pengusul.logbook.index') }}" 
                           class="flex items-center gap-2 px-2.5 py-1 rounded-md text-xs font-medium transition-colors {{ str_contains($currentRoute, 'pengusul.logbook') ? 'bg-blue-600 text-white font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ str_contains($currentRoute, 'pengusul.logbook') ? 'bg-white' : 'bg-slate-600' }}"></span>
                            <span>Logbook Harian</span>
                        </a>

                        <a href="{{ route('pengusul.monev.index') }}" 
                           class="flex items-center gap-2 px-2.5 py-1 rounded-md text-xs font-medium transition-colors {{ str_contains($currentRoute, 'pengusul.monev') ? 'bg-blue-600 text-white font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ str_contains($currentRoute, 'pengusul.monev') ? 'bg-white' : 'bg-slate-600' }}"></span>
                            <span>Laporan Kemajuan</span>
                        </a>

                        <a href="{{ route('pengusul.laporan-akhir.index') }}" 
                           class="flex items-center gap-2 px-2.5 py-1 rounded-md text-xs font-medium transition-colors {{ str_contains($currentRoute, 'pengusul.laporan-akhir') ? 'bg-blue-600 text-white font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ str_contains($currentRoute, 'pengusul.laporan-akhir') ? 'bg-white' : 'bg-slate-600' }}"></span>
                            <span>Laporan Akhir 100%</span>
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                {{-- SUB-MENU: Luaran & Sentra HKI --}}
                @if($hasLuaranGroup)
                <div class="space-y-0.5">
                    <button @click="activeGroup = (activeGroup === 'luaran' ? '' : 'luaran')" 
                            type="button"
                            class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs font-bold transition-colors"
                            :class="activeGroup === 'luaran' ? 'text-blue-400 bg-slate-800/90' : 'text-slate-300 hover:bg-slate-800 hover:text-white'">
                        <span class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            <span>Luaran & HKI</span>
                        </span>
                        <svg :class="activeGroup === 'luaran' ? 'rotate-180' : ''" class="w-3.5 h-3.5 transition-transform duration-200 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div x-show="activeGroup === 'luaran'" x-cloak class="ml-3 pl-2.5 py-0.5 space-y-0.5 border-l-2 border-slate-800">
                        <a href="{{ route('publikasi.index') }}" 
                           class="flex items-center gap-2 px-2.5 py-1 rounded-md text-xs font-medium transition-colors {{ str_contains($currentRoute, 'publikasi') ? 'bg-blue-600 text-white font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ str_contains($currentRoute, 'publikasi') ? 'bg-white' : 'bg-slate-600' }}"></span>
                            <span>Bank Publikasi</span>
                        </a>

                        <a href="{{ route('hki.index') }}" 
                           class="flex items-center gap-2 px-2.5 py-1 rounded-md text-xs font-medium transition-colors {{ str_contains($currentRoute, 'hki.') && !str_contains($currentRoute, 'admin.hki') ? 'bg-blue-600 text-white font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ str_contains($currentRoute, 'hki.') && !str_contains($currentRoute, 'admin.hki') ? 'bg-white' : 'bg-slate-600' }}"></span>
                            <span>Sentra HKI UHN</span>
                        </a>

                        <a href="{{ route('reward.index') }}" 
                           class="flex items-center gap-2 px-2.5 py-1 rounded-md text-xs font-medium transition-colors {{ str_contains($currentRoute, 'reward.') && !str_contains($currentRoute, 'admin.reward') && !str_contains($currentRoute, 'keuangan.reward') ? 'bg-blue-600 text-white font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ str_contains($currentRoute, 'reward.') && !str_contains($currentRoute, 'admin.reward') ? 'bg-white' : 'bg-slate-600' }}"></span>
                            <span>Klaim Reward Insentif</span>
                        </a>
                    </div>
                </div>
                @endif
            </div>
        @endif

        {{-- ========================================================================= --}}
        {{-- 3. BASIS DATA RISET & METRIK (Dosen / Pengusul & Superadmin)              --}}
        {{-- ========================================================================= --}}
        @if($hasDosenDatabaseGroup)
            <div class="space-y-0.5">
                <p class="px-2.5 text-[9px] font-bold uppercase tracking-wider text-slate-500 mb-0.5">Basis Data Riset</p>
                <div>
                    <button @click="activeGroup = (activeGroup === 'database' ? '' : 'database')" 
                            type="button"
                            class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs font-bold transition-colors"
                            :class="activeGroup === 'database' ? 'text-blue-400 bg-slate-800/90' : 'text-slate-300 hover:bg-slate-800 hover:text-white'">
                        <span class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3V7c0-2-1.5-3-3.5-3h-9C5.5 4 4 5 4 7z"/></svg>
                            <span>Pangkalan Data Riset</span>
                        </span>
                        <svg :class="activeGroup === 'database' ? 'rotate-180' : ''" class="w-3.5 h-3.5 transition-transform duration-200 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div x-show="activeGroup === 'database'" x-cloak class="ml-3 pl-2.5 py-0.5 space-y-0.5 border-l-2 border-slate-800">
                        @if($canViewSinta)
                        <a href="{{ route('sinta.profile') }}" 
                           class="flex items-center gap-2 px-2.5 py-1 rounded-md text-xs font-medium transition-colors {{ $currentRoute === 'sinta.profile' ? 'bg-blue-600 text-white font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $currentRoute === 'sinta.profile' ? 'bg-white' : 'bg-slate-600' }}"></span>
                            <span>Profil Metrik SINTA</span>
                        </a>
                        @endif

                        @if($canSearchPddikti)
                        <a href="{{ route('pddikti.search') }}" 
                           class="flex items-center gap-2 px-2.5 py-1 rounded-md text-xs font-medium transition-colors {{ $currentRoute === 'pddikti.search' ? 'bg-blue-600 text-white font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $currentRoute === 'pddikti.search' ? 'bg-white' : 'bg-slate-600' }}"></span>
                            <span>Pencarian PDDIKTI</span>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- ========================================================================= --}}
        {{-- 4. PENILAIAN & REVIEW (Reviewer & Superadmin)                             --}}
        {{-- ========================================================================= --}}
        @if($hasReviewerGroup)
            <div class="space-y-0.5">
                <p class="px-2.5 text-[9px] font-bold uppercase tracking-wider text-slate-500 mb-0.5">Penilaian & Evaluasi</p>
                <div>
                    <button @click="activeGroup = (activeGroup === 'reviewer' ? '' : 'reviewer')" 
                            type="button"
                            class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs font-bold transition-colors"
                            :class="activeGroup === 'reviewer' ? 'text-purple-400 bg-slate-800/90' : 'text-slate-300 hover:bg-slate-800 hover:text-white'">
                        <span class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span>Evaluasi Reviewer</span>
                        </span>
                        <span class="flex items-center gap-1.5">
                            @if(($pendingReviewCount + $pendingMonevCount) > 0)
                                <span class="min-w-4 h-4 px-1 rounded-full bg-purple-500 text-white text-[9px] font-black flex items-center justify-center">{{ $pendingReviewCount + $pendingMonevCount }}</span>
                            @endif
                            <svg :class="activeGroup === 'reviewer' ? 'rotate-180' : ''" class="w-3.5 h-3.5 transition-transform duration-200 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </span>
                    </button>

                    <div x-show="activeGroup === 'reviewer'" x-cloak class="ml-3 pl-2.5 py-0.5 space-y-0.5 border-l-2 border-slate-800">
                        @if($canReviewSubstance)
                        <a href="{{ route('reviewer.penilaian.index') }}" 
                           class="flex items-center justify-between gap-2 px-2.5 py-1 rounded-md text-xs font-medium transition-colors {{ str_contains($currentRoute, 'reviewer.penilaian') ? 'bg-purple-600 text-white font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <span class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full {{ str_contains($currentRoute, 'reviewer.penilaian') ? 'bg-white' : 'bg-slate-600' }}"></span>
                                <span>Penilaian Proposal</span>
                            </span>
                            @if($pendingReviewCount > 0)
                                <span class="min-w-4 h-4 px-1 rounded-full bg-purple-500 text-white text-[9px] font-black flex items-center justify-center">{{ $pendingReviewCount }}</span>
                            @endif
                        </a>
                        @endif

                        @if($canReviewMonev)
                        <a href="{{ route('reviewer.monev.index') }}" 
                           class="flex items-center justify-between gap-2 px-2.5 py-1 rounded-md text-xs font-medium transition-colors {{ str_contains($currentRoute, 'reviewer.monev') ? 'bg-purple-600 text-white font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <span class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full {{ str_contains($currentRoute, 'reviewer.monev') ? 'bg-white' : 'bg-slate-600' }}"></span>
                                <span>Monev Kemajuan</span>
                            </span>
                            @if($pendingMonevCount > 0)
                                <span class="min-w-4 h-4 px-1 rounded-full bg-purple-500 text-white text-[9px] font-black flex items-center justify-center">{{ $pendingMonevCount }}</span>
                            @endif
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- ========================================================================= --}}
        {{-- 5. PROGRAM STUDI (Kaprodi & Superadmin)                                   --}}
        {{-- ========================================================================= --}}
        @if($hasProdiSection)
            <div class="space-y-0.5">
                <p class="px-2.5 text-[9px] font-bold uppercase tracking-wider text-slate-500 mb-0.5">Program Studi</p>
                <a href="{{ route('admin.prodi-roadmap.index') }}" 
                   class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-xs font-bold transition-colors {{ str_contains($currentRoute, 'admin.prodi-roadmap') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="2" d="M4 19.5A2.5 2.5 0 016.5 17H20M6.5 2H20v15H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/></svg>
                    <span>Roadmap Keilmuan Prodi</span>
                </a>
            </div>
        @endif

        {{-- ========================================================================= --}}
        {{-- 6. PENGELOLAAN P3M (Admin P3M, Kepala P3M, Superadmin - Grouped Subs)    --}}
        {{-- ========================================================================= --}}
        @if($hasP3mManagement)
            <div class="space-y-0.5">
                <p class="px-2.5 text-[9px] font-bold uppercase tracking-wider text-slate-500 mb-0.5">Pengelolaan P3M</p>

                {{-- SUB-MENU: Program & Seleksi Hibah --}}
                @if($hasP3mSelectionGroup)
                <div class="space-y-0.5">
                    <button @click="activeGroup = (activeGroup === 'p3m_selection' ? '' : 'p3m_selection')" 
                            type="button"
                            class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs font-bold transition-colors"
                            :class="activeGroup === 'p3m_selection' ? 'text-blue-400 bg-slate-800/90' : 'text-slate-300 hover:bg-slate-800 hover:text-white'">
                        <span class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                            <span>{{ $currentRole === 'Kepala P3M' ? 'Persetujuan & Kuota' : 'Program & Seleksi' }}</span>
                        </span>
                        <span class="flex items-center gap-1.5">
                            @if($pendingAssignmentCount > 0)
                                <span class="min-w-4 h-4 px-1 rounded-full bg-amber-500 text-white text-[9px] font-black flex items-center justify-center">{{ $pendingAssignmentCount }}</span>
                            @endif
                            <svg :class="activeGroup === 'p3m_selection' ? 'rotate-180' : ''" class="w-3.5 h-3.5 transition-transform duration-200 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </span>
                    </button>

                    <div x-show="activeGroup === 'p3m_selection'" x-cloak class="ml-3 pl-2.5 py-0.5 space-y-0.5 border-l-2 border-slate-800">
                        @if($canManageSchemes)
                        <a href="{{ route('admin.skema-bima.index') }}" 
                           class="flex items-center gap-2 px-2.5 py-1 rounded-md text-xs font-medium transition-colors {{ str_contains($currentRoute, 'admin.skema-bima') ? 'bg-blue-600 text-white font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ str_contains($currentRoute, 'admin.skema-bima') ? 'bg-white' : 'bg-slate-600' }}"></span>
                            <span>Master Skema BIMA</span>
                        </a>
                        @endif

                        @if($canManagePeriods)
                        <a href="{{ route('admin.periode-hibah.index') }}" 
                           class="flex items-center gap-2 px-2.5 py-1 rounded-md text-xs font-medium transition-colors {{ str_contains($currentRoute, 'admin.periode-hibah') ? 'bg-blue-600 text-white font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ str_contains($currentRoute, 'admin.periode-hibah') ? 'bg-white' : 'bg-slate-600' }}"></span>
                            <span>Periode Call Proposals</span>
                        </a>
                        @endif

                        @if($canReviewLppm)
                        <a href="{{ route('admin.lppm-approval.index') }}" 
                           class="flex items-center gap-2 px-2.5 py-1 rounded-md text-xs font-medium transition-colors {{ str_contains($currentRoute, 'admin.lppm-approval') ? 'bg-blue-600 text-white font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ str_contains($currentRoute, 'admin.lppm-approval') ? 'bg-white' : 'bg-slate-600' }}"></span>
                            <span>{{ $currentRole === 'Kepala P3M' ? 'Persetujuan LPPM' : 'Verifikasi Usulan' }}</span>
                        </a>
                        @endif

                        @if($canAssignReviewers)
                        <a href="{{ route('admin.reviewer-assignment.index') }}" 
                           class="flex items-center justify-between gap-2 px-2.5 py-1 rounded-md text-xs font-medium transition-colors {{ str_contains($currentRoute, 'admin.reviewer-assignment') ? 'bg-blue-600 text-white font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <span class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full {{ str_contains($currentRoute, 'admin.reviewer-assignment') ? 'bg-white' : 'bg-slate-600' }}"></span>
                                <span>Penugasan Reviewer</span>
                            </span>
                            @if($pendingAssignmentCount > 0)
                                <span class="min-w-4 h-4 px-1 rounded-full bg-amber-500 text-white text-[9px] font-black flex items-center justify-center">{{ $pendingAssignmentCount }}</span>
                            @endif
                        </a>
                        @endif

                        @if($canViewRanking)
                        <a href="{{ route('admin.ranking.index') }}" 
                           class="flex items-center gap-2 px-2.5 py-1 rounded-md text-xs font-medium transition-colors {{ str_contains($currentRoute, 'admin.ranking') ? 'bg-blue-600 text-white font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ str_contains($currentRoute, 'admin.ranking') ? 'bg-white' : 'bg-slate-600' }}"></span>
                            <span>Pemeringkatan & Kuota</span>
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                {{-- SUB-MENU: Monev, Semhas & Sentra HKI --}}
                @if($hasP3mMonevLuaranGroup)
                <div class="space-y-0.5">
                    <button @click="activeGroup = (activeGroup === 'p3m_monev' ? '' : 'p3m_monev')" 
                            type="button"
                            class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs font-bold transition-colors"
                            :class="activeGroup === 'p3m_monev' ? 'text-blue-400 bg-slate-800/90' : 'text-slate-300 hover:bg-slate-800 hover:text-white'">
                        <span class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span>Monev & Sentra HKI</span>
                        </span>
                        <span class="flex items-center gap-1.5">
                            @if(($pendingHkiCount + $pendingRewardCount) > 0)
                                <span class="min-w-4 h-4 px-1 rounded-full bg-amber-500 text-white text-[9px] font-black flex items-center justify-center">{{ $pendingHkiCount + $pendingRewardCount }}</span>
                            @endif
                            <svg :class="activeGroup === 'p3m_monev' ? 'rotate-180' : ''" class="w-3.5 h-3.5 transition-transform duration-200 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </span>
                    </button>

                    <div x-show="activeGroup === 'p3m_monev'" x-cloak class="ml-3 pl-2.5 py-0.5 space-y-0.5 border-l-2 border-slate-800">
                        @if($canManageSemhas)
                        <a href="{{ route('admin.semhas.index') }}" 
                           class="flex items-center gap-2 px-2.5 py-1 rounded-md text-xs font-medium transition-colors {{ str_contains($currentRoute, 'admin.semhas') ? 'bg-blue-600 text-white font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ str_contains($currentRoute, 'admin.semhas') ? 'bg-white' : 'bg-slate-600' }}"></span>
                            <span>Seminar Hasil (Semhas)</span>
                        </a>
                        @endif

                        @if($canManageHkiAdmin)
                        <a href="{{ route('admin.hki.index') }}" 
                           class="flex items-center justify-between gap-2 px-2.5 py-1 rounded-md text-xs font-medium transition-colors {{ str_contains($currentRoute, 'admin.hki') ? 'bg-blue-600 text-white font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <span class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full {{ str_contains($currentRoute, 'admin.hki') ? 'bg-white' : 'bg-slate-600' }}"></span>
                                <span>Verifikasi Sentra HKI</span>
                            </span>
                            @if($pendingHkiCount > 0)
                                <span class="min-w-4 h-4 px-1 rounded-full bg-amber-500 text-white text-[9px] font-black flex items-center justify-center">{{ $pendingHkiCount }}</span>
                            @endif
                        </a>
                        @endif

                        @if($canManageRewardAdmin)
                        <a href="{{ route('admin.reward.index') }}" 
                           class="flex items-center justify-between gap-2 px-2.5 py-1 rounded-md text-xs font-medium transition-colors {{ str_contains($currentRoute, 'admin.reward') ? 'bg-blue-600 text-white font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <span class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full {{ str_contains($currentRoute, 'admin.reward') ? 'bg-white' : 'bg-slate-600' }}"></span>
                                <span>Review Klaim Reward</span>
                            </span>
                            @if($pendingRewardCount > 0)
                                <span class="min-w-4 h-4 px-1 rounded-full bg-amber-500 text-white text-[9px] font-black flex items-center justify-center">{{ $pendingRewardCount }}</span>
                            @endif
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                {{-- SUB-MENU: Sistem & Integrasi (Admin P3M & Superadmin) --}}
                @if($hasP3mSystemGroup && in_array($currentRole, ['Admin P3M', 'Superadmin'], true))
                <div class="space-y-0.5">
                    <button @click="activeGroup = (activeGroup === 'p3m_system' ? '' : 'p3m_system')" 
                            type="button"
                            class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs font-bold transition-colors"
                            :class="activeGroup === 'p3m_system' ? 'text-blue-400 bg-slate-800/90' : 'text-slate-300 hover:bg-slate-800 hover:text-white'">
                        <span class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            <span>Sistem & Integrasi</span>
                        </span>
                        <svg :class="activeGroup === 'p3m_system' ? 'rotate-180' : ''" class="w-3.5 h-3.5 transition-transform duration-200 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div x-show="activeGroup === 'p3m_system'" x-cloak class="ml-3 pl-2.5 py-0.5 space-y-0.5 border-l-2 border-slate-800">
                        @if($canViewIntegrations)
                        <a href="{{ route('integrasi.index') }}" 
                           class="flex items-center gap-2 px-2.5 py-1 rounded-md text-xs font-medium transition-colors {{ $currentRoute === 'integrasi.index' ? 'bg-blue-600 text-white font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $currentRoute === 'integrasi.index' ? 'bg-white' : 'bg-slate-600' }}"></span>
                            <span>Integrasi API (4 Modul)</span>
                        </a>
                        @endif

                        @if($canSearchPddikti)
                        <a href="{{ route('pddikti.search') }}" 
                           class="flex items-center gap-2 px-2.5 py-1 rounded-md text-xs font-medium transition-colors {{ $currentRoute === 'pddikti.search' ? 'bg-blue-600 text-white font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $currentRoute === 'pddikti.search' ? 'bg-white' : 'bg-slate-600' }}"></span>
                            <span>Pencarian PDDIKTI</span>
                        </a>
                        @endif

                        @if($canManageMigration)
                        <a href="{{ route('admin.migrasi.index') }}" 
                           class="flex items-center gap-2 px-2.5 py-1 rounded-md text-xs font-medium transition-colors {{ str_contains($currentRoute, 'admin.migrasi') ? 'bg-blue-600 text-white font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ str_contains($currentRoute, 'admin.migrasi') ? 'bg-white' : 'bg-slate-600' }}"></span>
                            <span>Migrasi Data Legasi</span>
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Audit Trail Logs (Hanya Kepala P3M & Superadmin) --}}
                @if($canViewAuditLogs)
                    <a href="{{ route('audit-logs') }}" 
                       class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-xs font-bold transition-colors {{ $currentRoute === 'audit-logs' ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Audit Trail Logs</span>
                    </a>
                @endif
            </div>
        @endif

        {{-- ========================================================================= --}}
        {{-- 7. DIVISI KEUANGAN (Keuangan & Superadmin - Nested Submenu)               --}}
        {{-- ========================================================================= --}}
        @if($hasKeuanganGroup)
            <div class="space-y-0.5">
                <p class="px-2.5 text-[9px] font-bold uppercase tracking-wider text-slate-500 mb-0.5">Divisi Keuangan</p>
                <div>
                    <button @click="activeGroup = (activeGroup === 'keuangan' ? '' : 'keuangan')" 
                            type="button"
                            class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs font-bold transition-colors"
                            :class="activeGroup === 'keuangan' ? 'text-emerald-400 bg-slate-800/90' : 'text-slate-300 hover:bg-slate-800 hover:text-white'">
                        <span class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span>Pencairan Dana</span>
                        </span>
                        <span class="flex items-center gap-1.5">
                            @if(($pendingDisbursementCount + $pendingRewardDisbursementCount) > 0)
                                <span class="min-w-4 h-4 px-1 rounded-full bg-emerald-500 text-white text-[9px] font-black flex items-center justify-center">{{ $pendingDisbursementCount + $pendingRewardDisbursementCount }}</span>
                            @endif
                            <svg :class="activeGroup === 'keuangan' ? 'rotate-180' : ''" class="w-3.5 h-3.5 transition-transform duration-200 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </span>
                    </button>

                    <div x-show="activeGroup === 'keuangan'" x-cloak class="ml-3 pl-2.5 py-0.5 space-y-0.5 border-l-2 border-slate-800">
                        <a href="{{ route('keuangan.pencairan.index') }}" 
                           class="flex items-center justify-between gap-2 px-2.5 py-1 rounded-md text-xs font-medium transition-colors {{ str_contains($currentRoute, 'keuangan.pencairan') ? 'bg-emerald-600 text-white font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <span class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full {{ str_contains($currentRoute, 'keuangan.pencairan') ? 'bg-white' : 'bg-slate-600' }}"></span>
                                <span>Pencairan Dana Hibah</span>
                            </span>
                            @if($pendingDisbursementCount > 0)
                                <span class="min-w-4 h-4 px-1 rounded-full bg-emerald-500 text-white text-[9px] font-black flex items-center justify-center">{{ $pendingDisbursementCount }}</span>
                            @endif
                        </a>

                        <a href="{{ route('keuangan.reward.index') }}" 
                           class="flex items-center justify-between gap-2 px-2.5 py-1 rounded-md text-xs font-medium transition-colors {{ str_contains($currentRoute, 'keuangan.reward') ? 'bg-emerald-600 text-white font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <span class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full {{ str_contains($currentRoute, 'keuangan.reward') ? 'bg-white' : 'bg-slate-600' }}"></span>
                                <span>Pencairan Insentif Reward</span>
                            </span>
                            @if($pendingRewardDisbursementCount > 0)
                                <span class="min-w-4 h-4 px-1 rounded-full bg-emerald-500 text-white text-[9px] font-black flex items-center justify-center">{{ $pendingRewardDisbursementCount }}</span>
                            @endif
                        </a>
                    </div>
                </div>
            </div>
        @endif

        {{-- ========================================================================= --}}
        {{-- 8. ANALITIK & PELAPORAN (Rektor, Kepala P3M, Superadmin, Dekanat)         --}}
        {{-- ========================================================================= --}}
        @if($hasAnalyticsGroup)
            <div class="space-y-0.5">
                <p class="px-2.5 text-[9px] font-bold uppercase tracking-wider text-slate-500 mb-0.5">Analitik & Pelaporan</p>
                <div>
                    <button @click="activeGroup = (activeGroup === 'analytics' ? '' : 'analytics')" 
                            type="button"
                            class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs font-bold transition-colors"
                            :class="activeGroup === 'analytics' ? 'text-indigo-400 bg-slate-800/90' : 'text-slate-300 hover:bg-slate-800 hover:text-white'">
                        <span class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            <span>Analitik Kinerja</span>
                        </span>
                        <svg :class="activeGroup === 'analytics' ? 'rotate-180' : ''" class="w-3.5 h-3.5 transition-transform duration-200 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div x-show="activeGroup === 'analytics'" x-cloak class="ml-3 pl-2.5 py-0.5 space-y-0.5 border-l-2 border-slate-800">
                        @if($canViewExecutiveAnalytics)
                        <a href="{{ route('analitik.eksekutif') }}" 
                           class="flex items-center gap-2 px-2.5 py-1 rounded-md text-xs font-medium transition-colors {{ str_contains($currentRoute, 'analitik.eksekutif') ? 'bg-indigo-600 text-white font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ str_contains($currentRoute, 'analitik.eksekutif') ? 'bg-white' : 'bg-slate-600' }}"></span>
                            <span>Dasbor Eksekutif</span>
                        </a>
                        @endif

                        @if($canViewFacultyAnalytics)
                        <a href="{{ route('analitik.fakultas') }}" 
                           class="flex items-center gap-2 px-2.5 py-1 rounded-md text-xs font-medium transition-colors {{ str_contains($currentRoute, 'analitik.fakultas') ? 'bg-indigo-600 text-white font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ str_contains($currentRoute, 'analitik.fakultas') ? 'bg-white' : 'bg-slate-600' }}"></span>
                            <span>Performa Fakultas</span>
                        </a>
                        @endif

                        @if($canViewAccreditationReports)
                        <a href="{{ route('laporan.akreditasi') }}" 
                           class="flex items-center gap-2 px-2.5 py-1 rounded-md text-xs font-medium transition-colors {{ str_contains($currentRoute, 'laporan.akreditasi') ? 'bg-indigo-600 text-white font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ str_contains($currentRoute, 'laporan.akreditasi') ? 'bg-white' : 'bg-slate-600' }}"></span>
                            <span>Pelaporan Akreditasi</span>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- ========================================================================= --}}
        {{-- 9. BANTUAN & VALIDASI (Semua Peran)                                      --}}
        {{-- ========================================================================= --}}
        <div class="space-y-0.5 pt-0.5">
            <p class="px-2.5 text-[9px] font-bold uppercase tracking-wider text-slate-500 mb-0.5">Bantuan & Validasi</p>
            
            {{-- Digital UAT Portal & Berita Acara (Stakeholders) --}}
            @if($canAccessUat)
            <a href="{{ route('uat.index') }}" 
               class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-xs font-bold transition-colors {{ str_contains($currentRoute, 'uat') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-4 h-4 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>UAT & Berita Acara</span>
            </a>
            @endif

            {{-- Buku Panduan Interaktif (Semua Pengguna) --}}
            <a href="{{ route('panduan.index') }}" 
               class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-xs font-bold transition-colors {{ str_contains($currentRoute, 'panduan') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-4 h-4 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span>Buku Panduan Sistem</span>
            </a>
        </div>
    </div>

    {{-- Sidebar Footer (Compact) --}}
    <div class="p-2.5 border-t border-slate-800/80 shrink-0">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full px-2.5 py-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 font-bold text-xs transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                <span>Keluar</span>
            </button>
        </form>
    </div>
</aside>

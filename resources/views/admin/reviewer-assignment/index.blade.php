@extends('layouts.app')

@section('title', 'Penugasan Reviewer & Adjudikasi - PRISMA UHN')

@section('content')
<div x-data="{ 
    sidebarOpen: false,
    assignModalOpen: false,
    adjudicateModalOpen: false,
    selectedProposal: null,
    eligibleReviewers: [],
    loadingEligible: false,
    openAssignModal(proposal) {
        this.selectedProposal = proposal;
        this.loadingEligible = true;
        this.assignModalOpen = true;
        this.eligibleReviewers = [];
        
        fetch(`/admin/penugasan-reviewer/${proposal.id}/eligible`)
            .then(res => res.json())
            .then(data => {
                this.eligibleReviewers = data.reviewers;
                this.loadingEligible = false;
            })
            .catch(() => {
                this.loadingEligible = false;
            });
    },
    openAdjudicateModal(proposal) {
        this.selectedProposal = proposal;
        this.loadingEligible = true;
        this.adjudicateModalOpen = true;
        this.eligibleReviewers = [];
        
        fetch(`/admin/penugasan-reviewer/${proposal.id}/eligible`)
            .then(res => res.json())
            .then(data => {
                this.eligibleReviewers = data.reviewers;
                this.loadingEligible = false;
            })
            .catch(() => {
                this.loadingEligible = false;
            });
    }
}" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <x-sidebar />

    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        <header class="bg-white border-b border-slate-200 py-4 sticky top-0 z-30 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <h1 class="font-extrabold text-xl text-slate-900 leading-tight">Penugasan Reviewer & Adjudikasi</h1>
                        <p class="text-xs font-semibold text-slate-500">Manajemen Double-Blind Review & Penanganan Disparitas Nilai Ekstrem</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-black bg-blue-100 text-blue-800 border border-blue-300">
                        Admin P3M Portal
                    </span>
                </div>
            </div>
        </header>

        <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            @if(session('success'))
                <div class="p-4.5 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-sm font-bold flex items-center gap-3 shadow-sm">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4.5 rounded-2xl bg-red-50 border border-red-300 text-red-900 text-sm font-bold flex items-center gap-3 shadow-sm">
                    <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div class="p-5 rounded-3xl bg-white border border-slate-200 shadow-sm">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Dalam Penelaahan</span>
                    <span class="text-2xl font-black text-slate-900 mt-1 block">{{ $stats['total_in_review'] }} Usulan</span>
                    <p class="text-[11px] text-slate-400 mt-1">Status: In_review (Reviewer 1 & 2)</p>
                </div>
                <div class="p-5 rounded-3xl bg-amber-50 border border-amber-200 shadow-sm">
                    <span class="text-xs font-bold text-amber-900 uppercase tracking-wider block">Disparitas Ekstrem (Adjudikasi)</span>
                    <span class="text-2xl font-black text-amber-700 mt-1 block">{{ $stats['total_adjudication'] }} Usulan</span>
                    <p class="text-[11px] text-amber-600 mt-1">Perlu Reviewer 3 (Penengah)</p>
                </div>
                <div class="p-5 rounded-3xl bg-emerald-50 border border-emerald-200 shadow-sm">
                    <span class="text-xs font-bold text-emerald-900 uppercase tracking-wider block">Selesai Dinilai</span>
                    <span class="text-2xl font-black text-emerald-700 mt-1 block">{{ $stats['total_reviewed'] }} Usulan</span>
                    <p class="text-[11px] text-emerald-600 mt-1">Skor akhir telah terkalkulasi</p>
                </div>
            </div>

            <!-- Filter Status Bar -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-500">Filter Status:</span>
                    <a href="{{ route('admin.reviewer-assignment.index') }}" 
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ !request('status_filter') ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        Semua Status
                    </a>
                    <a href="{{ route('admin.reviewer-assignment.index', ['status_filter' => 'In_review']) }}" 
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ request('status_filter') === 'In_review' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        In_review
                    </a>
                    <a href="{{ route('admin.reviewer-assignment.index', ['status_filter' => 'Adjudication']) }}" 
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ request('status_filter') === 'Adjudication' ? 'bg-amber-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        Adjudication
                    </a>
                    <a href="{{ route('admin.reviewer-assignment.index', ['status_filter' => 'Reviewed']) }}" 
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ request('status_filter') === 'Reviewed' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        Reviewed
                    </a>
                </div>
                <div class="text-xs text-slate-500 font-medium">
                    Menampilkan <strong>{{ $proposals->total() }}</strong> usulan
                </div>
            </div>

            <!-- Proposals List -->
            <div class="space-y-4">
                @forelse($proposals as $usulan)
                    @php
                        $p1 = $usulan->penugasanReviewer->firstWhere('peran_reviewer', 'reviewer_1');
                        $p2 = $usulan->penugasanReviewer->firstWhere('peran_reviewer', 'reviewer_2');
                        $p3 = $usulan->penugasanReviewer->firstWhere('peran_reviewer', 'adjudicator');
                        $isAdjudication = $usulan->status === 'Adjudication';
                        $isReviewed = $usulan->status === 'Reviewed';
                    @endphp
                    <div class="bg-white rounded-3xl border {{ $isAdjudication ? 'border-amber-400 ring-2 ring-amber-200 bg-amber-50/20' : 'border-slate-200' }} shadow-sm p-6 space-y-4">
                        <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                            <div class="space-y-1.5">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded-md text-[11px] font-mono font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        {{ $usulan->kode_usulan }}
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-700">
                                        {{ $usulan->skema->nama_skema ?? 'Skema Hibah' }}
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-700">
                                        Thn {{ $usulan->periode->tahun_anggaran ?? date('Y') }}
                                    </span>
                                    @if($isAdjudication)
                                        <span class="px-3 py-0.5 rounded-full text-[11px] font-black bg-amber-100 text-amber-900 border border-amber-300 animate-pulse flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                            Disparitas Ekstrem (Perlu Reviewer 3)
                                        </span>
                                    @elseif($isReviewed)
                                        <span class="px-3 py-0.5 rounded-full text-[11px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300">
                                            Selesai Dinilai (Skor: {{ number_format($usulan->skor_akhir, 2) }})
                                        </span>
                                    @else
                                        <span class="px-3 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                            {{ $usulan->status }}
                                        </span>
                                    @endif
                                </div>
                                <h3 class="text-base font-extrabold text-slate-900 leading-snug">
                                    {{ $usulan->judul_usulan }}
                                </h3>
                                <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500 pt-1">
                                    <span>Pengusul: <strong class="text-slate-800">{{ $usulan->pengusul->name ?? '-' }}</strong></span>
                                    <span>Fakultas: <strong class="text-blue-700">{{ $usulan->pengusul->fakultas->nama_fakultas ?? '-' }}</strong></span>
                                    <span>Prodi: <strong class="text-slate-700">{{ $usulan->pengusul->prodi->nama_prodi ?? '-' }}</strong></span>
                                    <span>RAB: <strong class="text-slate-800">Rp {{ number_format($usulan->total_rab ?? 0, 0, ',', '.') }}</strong></span>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="shrink-0 flex items-center gap-2">
                                @if($isAdjudication && !$p3)
                                    <button type="button" 
                                            @click="openAdjudicateModal({{ json_encode($usulan) }})"
                                            class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-extrabold text-xs shadow-md hover:shadow-lg transition flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                                        <span>Tugaskan Reviewer 3 (Penengah)</span>
                                    </button>
                                @endif

                                <button type="button" 
                                        @click="openAssignModal({{ json_encode($usulan) }})"
                                        class="px-4 py-2 rounded-xl {{ ($p1 && $p2) ? 'bg-slate-100 hover:bg-slate-200 text-slate-700' : 'bg-blue-600 hover:bg-blue-700 text-white shadow-sm' }} font-extrabold text-xs transition flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    <span>{{ ($p1 && $p2) ? 'Ubah Penugasan R1 & R2' : 'Tugaskan Reviewer 1 & 2' }}</span>
                                </button>
                            </div>
                        </div>

                        <!-- Reviewer Assignment Status Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-3 border-t border-slate-100 text-xs">
                            <!-- Reviewer 1 -->
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Reviewer 1</span>
                                    @if($p1)
                                        <p class="font-extrabold text-slate-800 mt-0.5">{{ $p1->reviewer->name ?? 'Reviewer' }}</p>
                                        <p class="text-[11px] text-slate-500">{{ $p1->reviewer->fakultas->nama_fakultas ?? '-' }}</p>
                                    @else
                                        <p class="font-bold text-slate-400 italic mt-0.5">Belum ditugaskan</p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    @if($p1 && $p1->status_penugasan === 'completed')
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-200 block mb-1">
                                            Selesai
                                        </span>
                                        <span class="font-mono font-extrabold text-sm text-blue-700">
                                            {{ number_format($usulan->skor_reviewer_1, 2) }}
                                        </span>
                                    @elseif($p1)
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                            Menunggu
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Reviewer 2 -->
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Reviewer 2</span>
                                    @if($p2)
                                        <p class="font-extrabold text-slate-800 mt-0.5">{{ $p2->reviewer->name ?? 'Reviewer' }}</p>
                                        <p class="text-[11px] text-slate-500">{{ $p2->reviewer->fakultas->nama_fakultas ?? '-' }}</p>
                                    @else
                                        <p class="font-bold text-slate-400 italic mt-0.5">Belum ditugaskan</p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    @if($p2 && $p2->status_penugasan === 'completed')
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-200 block mb-1">
                                            Selesai
                                        </span>
                                        <span class="font-mono font-extrabold text-sm text-blue-700">
                                            {{ number_format($usulan->skor_reviewer_2, 2) }}
                                        </span>
                                    @elseif($p2)
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                            Menunggu
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Reviewer 3 / Adjudicator -->
                            <div class="p-3.5 rounded-2xl {{ $isAdjudication || $p3 ? 'bg-purple-50 border border-purple-200' : 'bg-slate-50 border border-slate-200' }} flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] font-bold {{ $isAdjudication || $p3 ? 'text-purple-600' : 'text-slate-400' }} uppercase tracking-wider block">Reviewer 3 (Penengah)</span>
                                    @if($p3)
                                        <p class="font-extrabold text-slate-800 mt-0.5">{{ $p3->reviewer->name ?? 'Adjudicator' }}</p>
                                        <p class="text-[11px] text-slate-500">{{ $p3->reviewer->fakultas->nama_fakultas ?? '-' }}</p>
                                    @elseif($isAdjudication)
                                        <p class="font-bold text-amber-800 italic mt-0.5">Perlu Penugasan Segera</p>
                                    @else
                                        <p class="font-bold text-slate-400 italic mt-0.5">Tidak Diperlukan</p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    @if($p3 && $p3->status_penugasan === 'completed')
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-200 block mb-1">
                                            Selesai
                                        </span>
                                        <span class="font-mono font-extrabold text-sm text-purple-700">
                                            {{ number_format($usulan->skor_reviewer_3, 2) }}
                                        </span>
                                    @elseif($p3)
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                            Menunggu
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Adjudication Disparity Alert Notice -->
                        @if($isAdjudication && $usulan->adjudication_notes)
                            <div class="p-3.5 rounded-2xl bg-amber-100/70 border border-amber-300 text-amber-900 text-xs flex items-center gap-3">
                                <svg class="w-5 h-5 text-amber-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <div>
                                    <strong class="font-bold">Notifikasi Adjudikasi:</strong> {{ $usulan->adjudication_notes }}
                                </div>
                            </div>
                        @endif

                        @if($isReviewed && $usulan->skor_reviewer_3)
                            <div class="p-3.5 rounded-2xl bg-purple-50 border border-purple-200 text-purple-900 text-xs flex items-center justify-between">
                                <span>Nilai akhir <strong class="font-black text-sm text-purple-800">{{ number_format($usulan->skor_akhir, 2) }}</strong> diselesaikan melalui adjudikasi (rata-rata 2 nilai terdekat dari 3 penilai).</span>
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-black bg-purple-200 text-purple-900">Nearest-Two Resolved</span>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center text-slate-400">
                        <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <p class="font-bold text-slate-600">Tidak ada usulan dalam antrean penugasan.</p>
                        <p class="text-xs text-slate-400 mt-1">Usulan yang telah disetujui verifikasi LPPM akan otomatis muncul di sini.</p>
                    </div>
                @endforelse

                <!-- Pagination -->
                @if($proposals->hasPages())
                    <div class="pt-4">
                        {{ $proposals->links() }}
                    </div>
                @endif
            </div>
        </main>
    </div>

    <!-- Modal Assign Reviewer 1 & Reviewer 2 -->
    <div x-show="assignModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.away="assignModalOpen = false" 
             class="bg-white rounded-3xl max-w-xl w-full p-6 space-y-5 shadow-2xl border border-slate-200">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <h3 class="font-black text-lg text-slate-900">Tugaskan Reviewer 1 & 2</h3>
                    <p class="text-xs text-slate-500 mt-0.5" x-text="selectedProposal?.kode_usulan"></p>
                </div>
                <button @click="assignModalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- CoI Info Warning Box -->
            <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs space-y-1">
                <div class="flex items-center gap-1.5 font-bold">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Pencegahan Conflict of Interest (CoI) Aktif</span>
                </div>
                <p class="text-[11px] leading-relaxed">
                    Sistem secara otomatis <strong>memfilter dan memblokir</strong> seluruh calon penilai dari fakultas/prodi yang sama dengan tim pengusul untuk memastikan integritas telaah ilmiah.
                </p>
            </div>

            <template x-if="loadingEligible">
                <div class="py-8 text-center text-slate-500 space-y-2">
                    <svg class="w-6 h-6 animate-spin mx-auto text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <p class="text-xs font-semibold">Memverifikasi daftar penilai non-CoI yang memenuhi syarat...</p>
                </div>
            </template>

            <template x-if="!loadingEligible">
                <form :action="`/admin/penugasan-reviewer/${selectedProposal?.id}/assign`" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Pilih Reviewer 1 <span class="text-red-500">*</span>
                        </label>
                        <select name="reviewer_1_id" required class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-300 text-xs font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="">-- Pilih Reviewer 1 (Non-CoI) --</option>
                            <template x-for="r in eligibleReviewers" :key="r.id">
                                <option :value="r.id" x-text="`${r.name} (${r.fakultas ?? 'Fakultas Lain'}) - SINTA: ${r.sinta_score ?? '-'}`"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Pilih Reviewer 2 <span class="text-red-500">*</span>
                        </label>
                        <select name="reviewer_2_id" required class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-300 text-xs font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="">-- Pilih Reviewer 2 (Non-CoI) --</option>
                            <template x-for="r in eligibleReviewers" :key="r.id">
                                <option :value="r.id" x-text="`${r.name} (${r.fakultas ?? 'Fakultas Lain'}) - SINTA: ${r.sinta_score ?? '-'}`"></option>
                            </template>
                        </select>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" @click="assignModalOpen = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md transition">
                            Simpan Penugasan
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>

    <!-- Modal Assign Adjudicator (Reviewer 3) -->
    <div x-show="adjudicateModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.away="adjudicateModalOpen = false" 
             class="bg-white rounded-3xl max-w-xl w-full p-6 space-y-5 shadow-2xl border border-slate-200">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <h3 class="font-black text-lg text-slate-900">Tugaskan Reviewer 3 (Penengah / Adjudikator)</h3>
                    <p class="text-xs text-amber-700 font-semibold mt-0.5" x-text="`Resolusi Disparitas Nilai Ekstrem Usulan: ${selectedProposal?.kode_usulan}`"></p>
                </div>
                <button @click="adjudicateModalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs space-y-1">
                <p class="font-bold">Ketentuan Reviewer Adjudikator:</p>
                <p class="text-[11px] leading-relaxed">
                    Reviewer 3 harus penilai independen yang <strong>bukan pengusul, bukan Reviewer 1 atau 2</strong>, dan bebas dari benturan kepentingan fakultas/prodi. Nilai akhir usulan akan ditentukan oleh rata-rata 2 nilai yang paling berdekatan (Nearest-Two Algorithm).
                </p>
            </div>

            <template x-if="loadingEligible">
                <div class="py-8 text-center text-slate-500 space-y-2">
                    <svg class="w-6 h-6 animate-spin mx-auto text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <p class="text-xs font-semibold">Memuat penilai penengah yang independen...</p>
                </div>
            </template>

            <template x-if="!loadingEligible">
                <form :action="`/admin/penugasan-reviewer/${selectedProposal?.id}/adjudicator`" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Pilih Reviewer 3 (Penengah) <span class="text-red-500">*</span>
                        </label>
                        <select name="adjudicator_id" required class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-300 text-xs font-medium focus:ring-2 focus:ring-amber-500 focus:outline-none">
                            <option value="">-- Pilih Reviewer 3 --</option>
                            <template x-for="r in eligibleReviewers" :key="r.id">
                                <option :value="r.id" x-text="`${r.name} (${r.fakultas ?? 'Fakultas Lain'}) - SINTA: ${r.sinta_score ?? '-'}`"></option>
                            </template>
                        </select>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" @click="adjudicateModalOpen = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shadow-md transition">
                            Tugaskan Penengah
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>
</div>
@endsection


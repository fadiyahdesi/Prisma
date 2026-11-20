@extends('layouts.app')

@php
    $kat = $kategori ?? 'penelitian';
    $isPengabdian = $kat === 'pengabdian';
    $labelBidang = $isPengabdian ? 'Pengabdian Masyarakat' : 'Penelitian Dosen';
    $isAdmin = $canViewAll ?? auth()->user()->isFullDivisionFor($kat);
    $canSubmitNew = auth()->user()->hasPermission('proposal.submit_wizard');
@endphp

@section('title', 'Data ' . $labelBidang . ' UHN - PRISMA UHN')

@section('content')
<div x-data="{
    sidebarOpen: false,
    ajukanModalOpen: false,
    createModalOpen: false,
    editModalOpen: false,
    detailModalOpen: false,
    detailData: {
        no_urut: '',
        semester: '',
        judul_usulan: '',
        nama_ketua: '',
        nidn_ketua: '',
        anggota_list: [],
        nama_prodi: '',
        nominal_formatted: ''
    },
    editData: {
        update_url: '',
        no_urut: '',
        semester: '',
        judul_usulan: '',
        nama_ketua: '',
        nidn_ketua: '',
        nama_anggota: '',
        nama_prodi: '',
        total_rab: 0
    },
    openDetailModal(payload) {
        this.detailData = payload;
        this.detailModalOpen = true;
    },
    openEditModal(payload) {
        this.editData = payload;
        this.editModalOpen = true;
    }
}" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <x-sidebar />

    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        {{-- Topbar Header --}}
        <header class="bg-white border-b border-slate-200 py-3.5 sticky top-0 z-30 shadow-2xs">
            <div class="w-full px-4 sm:px-6 lg:px-8 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <button @click="sidebarOpen = true" type="button" class="lg:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <a href="{{ route('dashboard') }}" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center shrink-0 transition" title="Kembali ke Dashboard" aria-label="Kembali ke Dashboard">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </a>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 class="font-extrabold text-lg sm:text-xl text-slate-900 leading-tight">
                                {{ $isAdmin ? 'Data ' . $labelBidang . ' UHN' : $labelBidang . ' Saya' }}
                            </h1>
                            <!-- <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-slate-100 text-slate-800 border border-slate-200 tabular-nums">
                                {{ number_format($totalAllCount) }} Data
                            </span> -->
                        </div>
                        <p class="text-xs font-medium text-slate-500 mt-0.5">
                            <!-- {{ $isAdmin ? 'Basis Data Resmi ' . $labelBidang . ' • Universitas Harkat Negeri' : 'Daftar karya ' . strtolower($labelBidang) . ' yang Anda hasilkan' }} -->
                        </p>
                    </div>
                </div>

                {{-- Satu Tombol Aksi Utama Sesuai Peran Agar Tidak Membingungkan --}}
                <div class="flex flex-wrap items-center gap-2 shrink-0">
                    @if($isAdmin)
                        <button type="button" @click="createModalOpen = true"
                                class="h-9 px-4 rounded-xl bg-[#681727] hover:bg-[#52111e] text-white font-extrabold text-xs shadow-xs transition inline-flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Tambah Data {{ $isPengabdian ? 'Pengabdian' : 'Penelitian' }}</span>
                        </button>
                    @else
                        <span class="h-9 px-3 rounded-xl bg-slate-100 text-slate-600 border border-slate-200 font-semibold text-xs inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <span>Hanya Data Yang Anda Hasilkan</span>
                        </span>

                        @if($canSubmitNew)
                            <button type="button"
                                    @click="ajukanModalOpen = true"
                                    class="h-9 px-4 rounded-xl bg-[#681727] hover:bg-[#52111e] text-white font-extrabold text-xs shadow-xs transition inline-flex items-center gap-1.5 cursor-pointer">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                <span>Ajukan Proposal Baru</span>
                            </button>
                        @endif
                    @endif
                </div>
            </div>
        </header>

        <main class="flex-grow w-full px-4 sm:px-6 lg:px-8 py-5 space-y-4">
            {{-- Flash Notifications --}}
            @if(session('success'))
                <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs sm:text-sm font-bold flex items-center gap-3 shadow-2xs">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs sm:text-sm font-bold flex items-center gap-3 shadow-2xs">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- Kotak Terpisah Khusus Dosen Jika Memiliki Draf/Usulan Proposal Hibah BIMA Aktif --}}
            @if(!$isAdmin && isset($activeWizardProposals) && $activeWizardProposals->isNotEmpty())
                <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-2xs space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-extrabold text-slate-800 uppercase tracking-wider">
                            Pengajuan Proposal Hibah BIMA Aktif Saya ({{ $activeWizardProposals->count() }})
                        </span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5">
                        @foreach($activeWizardProposals as $wp)
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between gap-3 text-xs">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-bold text-slate-700">{{ $wp->kode_usulan }}</span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">{{ $wp->status }}</span>
                                    </div>
                                    <p class="font-semibold text-slate-900 truncate mt-0.5">{{ $wp->judul_usulan }}</p>
                                </div>
                                <a href="{{ route('usulan.step', ['usulan' => $wp->id, 'step' => $wp->status === 'Draft' ? 1 : 6]) }}"
                                   class="px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-bold text-[11px] shrink-0">
                                    {{ $wp->status === 'Draft' ? 'Lanjutkan' : 'Lihat' }}
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- STATISTIK UTAMA & GRAFIK BAR (PRODI, DANA & SEMESTER) --}}
            @php
                $search = $search ?? request('search');
                $semesterFilter = $semesterFilter ?? request('semester');
                $prodiFilter = $prodiFilter ?? request('prodi');
                $hasFilter = !empty($search) || !empty($semesterFilter) || !empty($prodiFilter) || (($scope ?? 'all') === 'mine' && $isAdmin);
                $statFilteredCount = $summaryStats['filtered_count'] ?? $proposals->total();
                $statTotalAllCount = $summaryStats['total_all_count'] ?? $totalAllCount;
                $statAvgNominal = $summaryStats['avg_nominal'] ?? ($statFilteredCount > 0 ? ($totalNominal / $statFilteredCount) : 0);
                $statMaxNominal = $summaryStats['max_nominal'] ?? 0;
                $statProdiCount = $summaryStats['prodi_count'] ?? ($prodiStats ?? collect())->count();
                $statSemesterCount = $summaryStats['semester_count'] ?? ($semesterStats ?? collect())->count();
                $topProdiJudul = $summaryStats['top_prodi_judul'] ?? ($prodiStats ?? collect())->first();
                $topProdiDana = $summaryStats['top_prodi_dana'] ?? ($prodiStats ?? collect())->sortByDesc('total_dana')->first();
                $topSemester = $summaryStats['top_semester'] ?? ($semesterStats ?? collect())->sortByDesc('total_dana')->first();
                $coveragePct = $statTotalAllCount > 0 ? round(($statFilteredCount / $statTotalAllCount) * 100, 1) : 100;
            @endphp

            {{-- 4 Kartu Ringkasan Statistik --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5">
                {{-- Kartu 1: Total Judul --}}
                <div class="lg:col-span-3 bg-white rounded-2xl border border-slate-200 p-4 shadow-2xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Total Judul {{ $isPengabdian ? 'Pengabdian' : 'Penelitian' }}</span>
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold {{ $isPengabdian ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-[#681727]/10 text-[#681727] border border-[#681727]/20' }}">
                                {{ $coveragePct }}% Data
                            </span>
                        </div>
                        <div class="mt-2 flex items-baseline gap-2">
                            <span class="text-2xl sm:text-3xl font-black text-slate-900 tabular-nums">{{ number_format($statFilteredCount, 0, ',', '.') }}</span>
                            <span class="text-xs font-bold text-slate-500">Judul</span>
                        </div>
                    </div>
                    <div class="mt-3 pt-2.5 border-t border-slate-100">
                        <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden mb-1.5">
                            <div class="h-full rounded-full {{ $isPengabdian ? 'bg-emerald-600' : 'bg-[#681727]' }}" style="width: {{ min(100, max(4, $coveragePct)) }}%"></div>
                        </div>
                        <p class="text-[11px] font-semibold text-slate-500">
                            @if($hasFilter)
                                Terfilter dari <strong class="text-slate-700">{{ number_format($statTotalAllCount, 0, ',', '.') }}</strong> total data resmi
                            @else
                                Seluruh basis data resmi terdaftar (<strong class="text-slate-700">1..{{ number_format($statTotalAllCount, 0, ',', '.') }}</strong>)
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Kartu 2: Total Akumulasi Dana Hibah (Full Untruncated) --}}
                <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-200 p-4 shadow-2xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Total Akumulasi Dana Hibah</span>
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                Realisasi Pendanaan
                            </span>
                        </div>
                        <div class="mt-2">
                            <p class="text-xl sm:text-2xl font-black text-emerald-800 tabular-nums break-words leading-tight">
                                Rp {{ number_format($totalNominal, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2 text-[11px]">
                        <span class="font-semibold text-slate-500">
                            Rata-rata: <strong class="text-slate-800 tabular-nums">Rp {{ number_format($statAvgNominal, 0, ',', '.') }}</strong> / judul
                        </span>
                        <span class="font-semibold text-slate-500">
                            Maks: <strong class="text-emerald-700 tabular-nums">Rp {{ number_format($statMaxNominal, 0, ',', '.') }}</strong>
                        </span>
                    </div>
                </div>

                {{-- Kartu 3: Cakupan Program Studi --}}
                <div class="lg:col-span-3 bg-white rounded-2xl border border-slate-200 p-4 shadow-2xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Cakupan Program Studi</span>
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-slate-100 text-slate-700 border border-slate-200">
                                Distribusi Prodi
                            </span>
                        </div>
                        <div class="mt-2 flex items-baseline gap-2">
                            <span class="text-2xl sm:text-3xl font-black text-slate-900 tabular-nums">{{ number_format($statProdiCount, 0, ',', '.') }}</span>
                            <span class="text-xs font-bold text-slate-500">Program Studi</span>
                        </div>
                    </div>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 text-[11px] space-y-0.5">
                        @if($topProdiJudul)
                            <p class="font-semibold text-slate-500 truncate" title="{{ $topProdiJudul->label }} ({{ $topProdiJudul->total_judul }} Judul)">
                                Terbanyak: <strong class="text-slate-800">{{ $topProdiJudul->label }}</strong> ({{ number_format($topProdiJudul->total_judul, 0, ',', '.') }})
                            </p>
                        @endif
                        @if($topProdiDana)
                            <p class="font-semibold text-slate-500 break-words">
                                Dana Tertinggi: <strong class="text-emerald-700 tabular-nums">Rp {{ number_format($topProdiDana->total_dana, 0, ',', '.') }}</strong>
                            </p>
                        @endif
                    </div>
                </div>

                {{-- Kartu 4: Periode Semester --}}
                <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-4 shadow-2xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Periode Aktif</span>
                        </div>
                        <div class="mt-2 flex items-baseline gap-1.5">
                            <span class="text-2xl sm:text-3xl font-black text-slate-900 tabular-nums">{{ number_format($statSemesterCount, 0, ',', '.') }}</span>
                            <span class="text-xs font-bold text-slate-500">Semester</span>
                        </div>
                    </div>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 text-[11px] space-y-0.5">
                        @if($topSemester)
                            <p class="font-semibold text-slate-500">
                                Puncak: <strong class="text-slate-800">{{ $topSemester->label }}</strong>
                            </p>
                            <p class="font-bold text-emerald-700 tabular-nums">
                                {{ number_format($topSemester->total_judul, 0, ',', '.') }} Judul
                            </p>
                        @else
                            <p class="font-semibold text-slate-400">Belum ada data periode</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Panel Visualisasi Bar: Program Studi, Dana & Periode Semester --}}
            @if(($prodiStats ?? collect())->isNotEmpty() || ($semesterStats ?? collect())->isNotEmpty())
                <div x-data="{ showStatsBar: true, prodiMode: 'judul', showAllProdi: false }" class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
                    {{-- Header Panel Statistik Bar --}}
                    <div class="px-4 sm:px-5 py-3.5 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3 bg-white">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $isPengabdian ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-[#681727]/10 text-[#681727] border border-[#681727]/20' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xs sm:text-sm font-extrabold text-slate-900 leading-tight">
                                    Statistik & Grafik Bar Program Studi, Pendanaan, dan Semester
                                </h2>
                                <p class="text-[11px] font-medium text-slate-500">
                                    Klik baris <strong class="text-slate-700">Program Studi</strong> atau <strong class="text-slate-700">Semester</strong> pada grafik bar untuk memfilter tabel di bawah secara langsung.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            @if(!empty($prodiFilter) || !empty($semesterFilter))
                                <a href="{{ request()->fullUrlWithQuery(['prodi' => null, 'semester' => null, 'page' => null]) }}"
                                   class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-[11px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 transition-colors">
                                    Reset Filter Grafik
                                </a>
                            @endif
                            <button type="button"
                                    @click="showStatsBar = !showStatsBar"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11px] font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors cursor-pointer">
                                <span x-text="showStatsBar ? 'Sembunyikan Grafik' : 'Tampilkan Grafik'">Sembunyikan Grafik</span>
                                <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="showStatsBar ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Isi Grafik Bar 2 Kolom --}}
                    <div x-show="showStatsBar" x-transition class="p-4 sm:p-5 grid grid-cols-1 lg:grid-cols-12 gap-5 bg-slate-50/40">
                        {{-- KOLOM KIRI (7 COL): BAR DISTRIBUSI PROGRAM STUDI (JUDUL & DANA) --}}
                        <div class="lg:col-span-7 bg-white rounded-xl border border-slate-200 p-4 flex flex-col justify-between">
                            <div>
                                <div class="flex flex-wrap items-center justify-between gap-2 pb-3 mb-3 border-b border-slate-100">
                                    <div>
                                        <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider">
                                            Distribusi per Program Studi ({{ ($prodiStats ?? collect())->count() }} Prodi)
                                        </h3>
                                        <p class="text-[11px] text-slate-500 font-medium">
                                            Perbandingan jumlah judul & serapan dana hibah antar program studi
                                        </p>
                                    </div>
                                    {{-- Toggle Mode Bar: Berdasarkan Judul vs Dana --}}
                                    <div class="inline-flex rounded-lg p-0.5 bg-slate-100 border border-slate-200">
                                        <button type="button"
                                                @click="prodiMode = 'judul'"
                                                :class="prodiMode === 'judul' ? 'bg-white text-slate-900 shadow-2xs font-extrabold' : 'text-slate-600 hover:text-slate-900 font-semibold'"
                                                class="px-2.5 py-1 rounded-md text-[11px] transition-all cursor-pointer">
                                            Bar Jumlah Judul
                                        </button>
                                        <button type="button"
                                                @click="prodiMode = 'dana'"
                                                :class="prodiMode === 'dana' ? 'bg-white text-emerald-800 shadow-2xs font-extrabold' : 'text-slate-600 hover:text-slate-900 font-semibold'"
                                                class="px-2.5 py-1 rounded-md text-[11px] transition-all cursor-pointer">
                                            Bar Total Dana (Rp)
                                        </button>
                                    </div>
                                </div>

                                {{-- Daftar Bar Program Studi --}}
                                <div class="space-y-2.5 max-h-[340px] overflow-y-auto pr-1">
                                    @foreach(($prodiStats ?? collect()) as $idx => $pStat)
                                        @php
                                            $isProdiSelected = ($prodiFilter === $pStat->label);
                                            $prodiToggleUrl = request()->fullUrlWithQuery([
                                                'prodi' => $isProdiSelected ? null : $pStat->label,
                                                'page' => null,
                                            ]);
                                        @endphp
                                        <a href="{{ $prodiToggleUrl }}"
                                           x-show="showAllProdi || {{ $idx }} < 8"
                                           class="block p-2.5 rounded-xl border transition-all group {{ $isProdiSelected ? 'bg-[#681727]/5 border-[#681727] ring-1 ring-[#681727]/20' : 'bg-slate-50/60 hover:bg-slate-100/80 border-slate-200/80' }}">
                                            <div class="flex flex-wrap items-center justify-between gap-2 mb-1.5">
                                                <div class="flex items-center gap-2 min-w-0">
                                                    <span class="w-5 h-5 rounded-md text-[10px] font-extrabold flex items-center justify-center shrink-0 {{ $idx < 3 ? ($isPengabdian ? 'bg-emerald-600 text-white' : 'bg-[#681727] text-white') : 'bg-slate-200 text-slate-700' }}">
                                                        {{ $pStat->rank }}
                                                    </span>
                                                    <span class="text-xs font-extrabold text-slate-800 group-hover:text-[#681727] truncate">
                                                        {{ $pStat->label }}
                                                    </span>
                                                    @if($isProdiSelected)
                                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-[#681727] text-white">
                                                            Filter Aktif
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="flex items-center gap-3 text-[11px] shrink-0">
                                                    <span class="font-extrabold text-slate-800 tabular-nums">
                                                        {{ number_format($pStat->total_judul, 0, ',', '.') }} Judul
                                                        <span class="text-slate-400 font-semibold">({{ $pStat->share_judul_pct }}%)</span>
                                                    </span>
                                                    <span class="text-slate-300">|</span>
                                                    <span class="font-extrabold text-emerald-700 tabular-nums">
                                                        Rp {{ number_format($pStat->total_dana, 0, ',', '.') }}
                                                        <span class="text-emerald-600/75 font-semibold">({{ $pStat->share_dana_pct }}%)</span>
                                                    </span>
                                                </div>
                                            </div>

                                            {{-- Horizontal Progress Bar (Judul vs Dana) --}}
                                            <div class="w-full h-2.5 bg-slate-200/70 rounded-full overflow-hidden">
                                                <div x-show="prodiMode === 'judul'"
                                                     class="h-full rounded-full transition-all duration-300 {{ $isPengabdian ? 'bg-emerald-600' : 'bg-[#681727]' }}"
                                                     style="width: {{ max(4, $pStat->bar_judul_pct) }}%"></div>
                                                <div x-show="prodiMode === 'dana'"
                                                     style="display: none; width: {{ max(4, $pStat->bar_dana_pct) }}%"
                                                     class="h-full rounded-full transition-all duration-300 bg-emerald-600"></div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>

                            @if(($prodiStats ?? collect())->count() > 8)
                                <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between">
                                    <span class="text-[11px] font-medium text-slate-500">
                                        Menampilkan <span class="font-bold text-slate-700" x-text="showAllProdi ? '{{ ($prodiStats ?? collect())->count() }}' : '8'">8</span> dari {{ ($prodiStats ?? collect())->count() }} Program Studi
                                    </span>
                                    <button type="button"
                                            @click="showAllProdi = !showAllProdi"
                                            class="text-[11px] font-extrabold text-[#681727] hover:underline cursor-pointer">
                                        <span x-text="showAllProdi ? 'Ringkas Top 8 Program Studi' : 'Lihat Semua Program Studi ({{ ($prodiStats ?? collect())->count() }})'">Lihat Semua Program Studi</span>
                                    </button>
                                </div>
                            @endif
                        </div>

                        {{-- KOLOM KANAN (5 COL): BAR DISTRIBUSI PERIODE SEMESTER & DANA --}}
                        <div class="lg:col-span-5 bg-white rounded-xl border border-slate-200 p-4 flex flex-col justify-between">
                            <div>
                                <div class="flex flex-wrap items-center justify-between gap-2 pb-3 mb-3 border-b border-slate-100">
                                    <div>
                                        <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider">
                                            Tren Periode Semester & Pendanaan
                                        </h3>
                                        <p class="text-[11px] text-slate-500 font-medium">
                                            Proporsi volume judul & akumulasi dana per semester
                                        </p>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ ($semesterStats ?? collect())->count() }} Periode
                                    </span>
                                </div>

                                <div class="space-y-3 max-h-[295px] overflow-y-auto pr-1">
                                    @foreach(($semesterStats ?? collect()) as $sStat)
                                        @php
                                            $isSemSelected = ($semesterFilter === $sStat->label);
                                            $semToggleUrl = request()->fullUrlWithQuery([
                                                'semester' => $isSemSelected ? null : $sStat->label,
                                                'page' => null,
                                            ]);
                                        @endphp
                                        <a href="{{ $semToggleUrl }}"
                                           class="block p-3 rounded-xl border transition-all group {{ $isSemSelected ? 'bg-[#681727]/5 border-[#681727] ring-1 ring-[#681727]/20' : 'bg-slate-50/60 hover:bg-slate-100/80 border-slate-200/80' }}">
                                            <div class="flex items-center justify-between gap-2 mb-1">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs font-extrabold text-slate-900 group-hover:text-[#681727]">
                                                        Semester {{ $sStat->label }}
                                                    </span>
                                                    @if($isSemSelected)
                                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-[#681727] text-white">
                                                            Filter Aktif
                                                        </span>
                                                    @endif
                                                </div>
                                                <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-white text-slate-800 border border-slate-200 tabular-nums">
                                                    {{ number_format($sStat->total_judul, 0, ',', '.') }} Judul ({{ $sStat->share_judul_pct }}%)
                                                </span>
                                            </div>

                                            <div class="flex flex-wrap items-center justify-between gap-1 text-[11px] mb-2">
                                                <span class="font-extrabold text-emerald-700 tabular-nums break-words">
                                                    Rp {{ number_format($sStat->total_dana, 0, ',', '.') }}
                                                </span>
                                                <span class="text-[10px] font-semibold text-slate-500 tabular-nums">
                                                    Rata-rata Rp {{ number_format($sStat->avg_dana, 0, ',', '.') }} / judul ({{ $sStat->share_dana_pct }}% dana)
                                                </span>
                                            </div>

                                            {{-- Dual Bar: Bar 1 Volume Judul, Bar 2 Total Dana --}}
                                            <div class="space-y-1">
                                                <div class="w-full h-2 bg-slate-200/70 rounded-full overflow-hidden" title="Proporsi Jumlah Judul: {{ $sStat->share_judul_pct }}%">
                                                    <div class="h-full rounded-full {{ $isPengabdian ? 'bg-emerald-600' : 'bg-[#681727]' }}" style="width: {{ max(4, $sStat->bar_judul_pct) }}%"></div>
                                                </div>
                                                <div class="w-full h-1.5 bg-slate-200/70 rounded-full overflow-hidden" title="Proporsi Total Dana: {{ $sStat->share_dana_pct }}%">
                                                    <div class="h-full rounded-full bg-amber-500" style="width: {{ max(4, $sStat->bar_dana_pct) }}%"></div>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Legenda Dual Bar Semester --}}
                            <div class="mt-3 pt-2.5 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2 text-[11px] text-slate-500">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center gap-1.5 font-semibold">
                                        <span class="w-2.5 h-2.5 rounded-xs {{ $isPengabdian ? 'bg-emerald-600' : 'bg-[#681727]' }}"></span>
                                        Bar Volume Judul
                                    </span>
                                    <span class="inline-flex items-center gap-1.5 font-semibold">
                                        <span class="w-2.5 h-2.5 rounded-xs bg-amber-500"></span>
                                        Bar Akumulasi Dana
                                    </span>
                                </div>
                                <span class="font-bold text-slate-700">Klik baris untuk filter</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Kartu Utama: Filter + Ringkasan + Tabel 9 Kolom Resmi --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
                {{-- Baris Kontrol & Filter --}}
                <div class="p-4 sm:p-5 border-b border-slate-200 space-y-3.5 bg-white">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                        <div class="flex flex-wrap items-center gap-2">
                            @if($isAdmin && $totalMineCount > 0)
                                <div class="inline-flex items-center gap-1 bg-slate-100 p-1 rounded-xl">
                                    <a href="{{ route($isPengabdian ? 'pengabdian.index' : 'usulan.index', array_merge(request()->except('page'), ['kategori' => $kat, 'scope' => 'all'])) }}"
                                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ ($scope ?? 'all') === 'all' ? 'bg-slate-900 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                                        Semua ({{ $totalAllCount }})
                                    </a>
                                    <a href="{{ route($isPengabdian ? 'pengabdian.index' : 'usulan.index', array_merge(request()->except('page'), ['kategori' => $kat, 'scope' => 'mine'])) }}"
                                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ ($scope ?? 'all') === 'mine' ? 'bg-slate-900 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                                        Milik Saya ({{ $totalMineCount }})
                                    </a>
                                </div>
                            @elseif($isAdmin)
                                <span class="text-xs font-bold text-slate-700">
                                    Daftar Resmi {{ $labelBidang }} (No. 1 s/d {{ number_format($totalAllCount) }})
                                </span>
                            @else
                                <span class="text-xs font-bold text-slate-700">
                                    Daftar {{ $labelBidang }} Milik Saya ({{ number_format($totalMineCount) }} Data)
                                </span>
                            @endif
                        </div>

                        {{-- Ringkasan Singkat & Jelas (Nominal Utuh Tidak Terpotong) --}}
                        <div class="flex flex-wrap items-center gap-2 text-xs font-semibold">
                            <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700">
                                Periode: <strong class="text-slate-900">{{ $semesterList->count() }} Semester</strong>
                            </span>
                            <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700">
                                Prodi: <strong class="text-slate-900">{{ $prodiList->count() }} Prodi</strong>
                            </span>
                            <span class="px-3 py-1 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-900 whitespace-nowrap">
                                Akumulasi Dana: <strong class="font-extrabold text-emerald-700 tabular-nums">Rp {{ number_format($totalNominal, 0, ',', '.') }}</strong>
                            </span>
                        </div>
                    </div>

                    {{-- Form Pencarian & Filter --}}
                    <form method="GET" action="{{ route($isPengabdian ? 'pengabdian.index' : 'usulan.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-2.5 items-center">
                        <input type="hidden" name="kategori" value="{{ $kat }}">
                        <input type="hidden" name="scope" value="{{ $scope ?? 'all' }}">

                        <div class="lg:col-span-4 relative">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Cari judul, nama ketua, NIDN/NUPN, atau anggota..."
                                   class="w-full h-9 pl-9 pr-3 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium text-slate-900 focus:bg-white focus:ring-2 focus:ring-slate-900 focus:outline-hidden">
                        </div>

                        <div class="lg:col-span-3">
                            <select name="semester" onchange="this.form.submit()" class="w-full h-9 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 focus:bg-white focus:ring-2 focus:ring-slate-900 focus:outline-hidden">
                                <option value="">Semua Semester ({{ $semesterList->count() }})</option>
                                @foreach($semesterList as $sem)
                                    <option value="{{ $sem }}" {{ request('semester') === $sem ? 'selected' : '' }}>{{ $sem }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="lg:col-span-3">
                            <select name="prodi" onchange="this.form.submit()" class="w-full h-9 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 focus:bg-white focus:ring-2 focus:ring-slate-900 focus:outline-hidden">
                                <option value="">Semua Program Studi ({{ $prodiList->count() }})</option>
                                @foreach($prodiList as $pr)
                                    <option value="{{ $pr }}" {{ request('prodi') === $pr ? 'selected' : '' }}>{{ $pr }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="lg:col-span-2 flex items-center gap-1.5">
                            <select name="per_page" onchange="this.form.submit()" title="Jumlah baris per halaman"
                                    class="h-9 px-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 focus:bg-white focus:ring-2 focus:ring-slate-900 focus:outline-hidden">
                                <option value="25" {{ ($perPage ?? 25) == 25 ? 'selected' : '' }}>25 / hal</option>
                                <option value="50" {{ ($perPage ?? 25) == 50 ? 'selected' : '' }}>50 / hal</option>
                                <option value="100" {{ ($perPage ?? 25) == 100 ? 'selected' : '' }}>100 / hal</option>
                                <option value="250" {{ ($perPage ?? 25) == 250 ? 'selected' : '' }}>250 / hal</option>
                                <option value="500" {{ ($perPage ?? 25) == 500 ? 'selected' : '' }}>Semua</option>
                            </select>

                            <button type="submit" class="flex-1 h-9 px-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition cursor-pointer">
                                Cari
                            </button>

                            @if(request()->anyFilled(['search', 'semester', 'prodi']))
                                <a href="{{ route($isPengabdian ? 'pengabdian.index' : 'usulan.index', ['kategori' => $kat, 'scope' => $scope ?? 'all', 'per_page' => $perPage ?? 25]) }}"
                                   class="h-9 px-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition inline-flex items-center justify-center" title="Reset Filter">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                {{-- Tabel 9 Kolom Standar Berurutan 1 s/d 438 --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-extrabold uppercase tracking-wider text-[11px]">
                                <th class="py-3 px-2.5 w-12 text-center">No</th>
                                <th class="py-3 px-3 w-32 whitespace-nowrap">Semester</th>
                                <th class="py-3 px-3 min-w-[240px]">Judul</th>
                                <th class="py-3 px-3 min-w-[150px]">Ketua</th>
                                <th class="py-3 px-2.5 w-28 whitespace-nowrap">NIDN / NUPN</th>
                                <th class="py-3 px-3 min-w-[160px]">Anggota</th>
                                <th class="py-3 px-3 min-w-[135px]">Prodi</th>
                                <th class="py-3 px-3 w-32 text-right whitespace-nowrap">Nominal</th>
                                <th class="py-3 px-3 w-28 text-center whitespace-nowrap">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200/75 text-xs">
                            @forelse($proposals as $idx => $p)
                                @php
                                    $semesterVal = $p->semester ?: ($p->periode ? trim($p->periode->tahun_akademik . ' ' . $p->periode->semester) : '-');
                                    $noUrutVal = $p->no_urut ?? ($proposals->firstItem() + $idx);
                                    $ketuaVal = $p->nama_ketua ?: ($p->pengusul?->name ?? '-');
                                    $nidnVal = $p->nidn_ketua ?: ($p->pengusul?->nidn_nim ?? '-');
                                    $anggotaVal = $p->nama_anggota;
                                    if (empty($anggotaVal) || $anggotaVal === '-') {
                                        $anggotaNames = $p->anggota?->pluck('nama')->filter()->values()->all() ?? [];
                                        $anggotaVal = !empty($anggotaNames) ? implode('; ', $anggotaNames) : '-';
                                    }
                                    $anggotaItems = ($anggotaVal && $anggotaVal !== '-')
                                        ? collect(explode(';', $anggotaVal))->map(fn ($v) => trim($v))->filter()->values()
                                        : collect();
                                    $prodiVal = $p->nama_prodi ?: ($p->prodi?->nama_prodi ?? ($p->pengusul?->prodi?->nama_prodi ?? '-'));
                                    $nominalVal = (float) ($p->total_rab ?? 0);
                                    $nominalFormatted = 'Rp ' . number_format($nominalVal, 0, ',', '.');
                                @endphp
                                <tr class="odd:bg-white even:bg-slate-50/50 hover:bg-slate-100/70 transition-colors align-top">
                                    {{-- 1. No --}}
                                    <td class="py-3 px-2.5 text-center font-bold text-slate-500 tabular-nums">
                                        {{ $noUrutVal }}
                                    </td>

                                    {{-- 2. Semester --}}
                                    <td class="py-3 px-3 whitespace-nowrap">
                                        <span class="inline-block px-2 py-0.5 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-700">
                                            {{ $semesterVal }}
                                        </span>
                                    </td>

                                    {{-- 3. Judul --}}
                                    <td class="py-3 px-3">
                                        <button type="button"
                                                @click="openDetailModal({{ json_encode([
                                                    'no_urut' => $noUrutVal,
                                                    'semester' => $semesterVal,
                                                    'judul_usulan' => $p->judul_usulan ?: '(Judul Belum Diisi)',
                                                    'nama_ketua' => $ketuaVal,
                                                    'nidn_ketua' => $nidnVal,
                                                    'anggota_list' => $anggotaItems->all(),
                                                    'nama_prodi' => $prodiVal,
                                                    'nominal_formatted' => $nominalFormatted,
                                                ]) }})"
                                                class="text-left font-semibold text-slate-900 hover:text-[#681727] text-xs leading-relaxed transition cursor-pointer">
                                            {{ $p->judul_usulan ?: '(Judul Belum Diisi)' }}
                                        </button>
                                    </td>

                                    {{-- 4. Ketua --}}
                                    <td class="py-3 px-3">
                                        <span class="font-bold text-slate-800 text-xs leading-snug block">
                                            {{ $ketuaVal }}
                                        </span>
                                    </td>

                                    {{-- 5. NIDN / NUPN --}}
                                    <td class="py-3 px-2.5 whitespace-nowrap">
                                        @if(!empty($nidnVal) && $nidnVal !== '-')
                                            <span class="text-[11px] font-semibold text-slate-600 tabular-nums">
                                                {{ $nidnVal }}
                                            </span>
                                        @else
                                            <span class="text-slate-400 text-[11px]">-</span>
                                        @endif
                                    </td>

                                    {{-- 6. Anggota --}}
                                    <td class="py-3 px-3">
                                        @if($anggotaItems->isNotEmpty())
                                            @if($anggotaItems->count() === 1)
                                                <span class="text-[11px] font-medium text-slate-600 leading-snug block">
                                                    {{ $anggotaItems->first() }}
                                                </span>
                                            @else
                                                <ul class="space-y-0.5 text-[11px] font-medium text-slate-600 leading-snug">
                                                    @foreach($anggotaItems as $memberName)
                                                        <li class="flex items-start gap-1">
                                                            <span class="text-slate-400 select-none">&bull;</span>
                                                            <span>{{ $memberName }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        @else
                                            <span class="text-slate-400 text-[11px]">-</span>
                                        @endif
                                    </td>

                                    {{-- 7. Prodi --}}
                                    <td class="py-3 px-3">
                                        @if(!empty($prodiVal) && $prodiVal !== '-')
                                            <span class="text-[11px] font-semibold text-slate-700 leading-snug block">
                                                {{ $prodiVal }}
                                            </span>
                                        @else
                                            <span class="text-slate-400 text-[11px]">-</span>
                                        @endif
                                    </td>

                                    {{-- 8. Nominal --}}
                                    <td class="py-3 px-3 text-right whitespace-nowrap">
                                        <span class="{{ $nominalVal > 0 ? 'font-bold text-slate-900' : 'font-medium text-slate-400' }} tabular-nums text-xs">
                                            {{ $nominalFormatted }}
                                        </span>
                                    </td>

                                    {{-- 9. Aksi --}}
                                    <td class="py-3 px-3 text-center whitespace-nowrap">
                                        @if($isAdmin)
                                            <div class="inline-flex items-center justify-center gap-1">
                                                <button type="button"
                                                        @click="openEditModal({{ json_encode([
                                                            'update_url' => route('usulan.update-basic', $p->id),
                                                            'no_urut' => $noUrutVal,
                                                            'semester' => $semesterVal,
                                                            'judul_usulan' => $p->judul_usulan,
                                                            'nama_ketua' => $ketuaVal,
                                                            'nidn_ketua' => $nidnVal,
                                                            'nama_anggota' => $anggotaVal,
                                                            'nama_prodi' => $prodiVal,
                                                            'total_rab' => round($nominalVal),
                                                        ]) }})"
                                                        class="h-7 px-2.5 rounded-lg bg-slate-100 hover:bg-slate-800 text-slate-700 hover:text-white border border-slate-200 font-bold text-[11px] transition inline-flex items-center justify-center cursor-pointer">
                                                    Edit
                                                </button>

                                                <form method="POST" action="{{ route('usulan.destroy', $p->id) }}"
                                                      onsubmit="return confirm('Hapus data No. {{ $noUrutVal }} ini?');"
                                                      class="inline-flex m-0 p-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="h-7 px-2.5 rounded-lg bg-rose-50 hover:bg-rose-600 text-rose-700 hover:text-white border border-rose-200 font-bold text-[11px] transition inline-flex items-center justify-center cursor-pointer">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <button type="button"
                                                    @click="openDetailModal({{ json_encode([
                                                        'no_urut' => $noUrutVal,
                                                        'semester' => $semesterVal,
                                                        'judul_usulan' => $p->judul_usulan ?: '(Judul Belum Diisi)',
                                                        'nama_ketua' => $ketuaVal,
                                                        'nidn_ketua' => $nidnVal,
                                                        'anggota_list' => $anggotaItems->all(),
                                                        'nama_prodi' => $prodiVal,
                                                        'nominal_formatted' => $nominalFormatted,
                                                    ]) }})"
                                                    class="h-7 px-2.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 font-bold text-[11px] transition inline-flex items-center justify-center cursor-pointer">
                                                Detail
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="py-12 px-4 text-center">
                                        <p class="text-slate-600 font-bold text-sm mb-1">Belum ada data {{ strtolower($labelBidang) }} yang sesuai</p>
                                        <p class="text-slate-400 text-xs">
                                            @if(request()->anyFilled(['search', 'semester', 'prodi']))
                                                Silakan ubah kata kunci pencarian atau klik Reset.
                                            @else
                                                Data {{ strtolower($labelBidang) }} akan tampil di sini.
                                            @endif
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Footer Paginasi --}}
                <div class="px-5 py-3.5 border-t border-slate-200 bg-slate-50/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <p class="text-xs font-medium text-slate-500">
                        Menampilkan <strong class="text-slate-800">{{ $proposals->firstItem() ?? 0 }}&ndash;{{ $proposals->lastItem() ?? 0 }}</strong> dari total <strong class="text-slate-800">{{ number_format($proposals->total()) }}</strong> data {{ strtolower($labelBidang) }}
                    </p>
                    @if($proposals->hasPages())
                        <div>
                            {{ $proposals->onEachSide(1)->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>

    {{-- Datalist Program Studi Resmi UHN --}}
    <datalist id="prodiOptionsList">
        @foreach($allMasterProdi ?? $prodiList as $optProdi)
            <option value="{{ $optProdi }}"></option>
        @endforeach
    </datalist>

    {{-- Modal Detail Ringkas --}}
    <div x-show="detailModalOpen"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4"
         @keydown.escape.window="detailModalOpen = false">
        <div @click.away="detailModalOpen = false"
             class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-xl overflow-hidden">
            <div class="px-5 py-4 bg-slate-900 text-white flex items-center justify-between">
                <div>
                    <h3 class="font-extrabold text-sm sm:text-base">Detail {{ $labelBidang }} No. <span x-text="detailData.no_urut"></span></h3>
                    <p class="text-[11px] text-slate-300 font-medium" x-text="'Semester: ' + detailData.semester"></p>
                </div>
                <button type="button" @click="detailModalOpen = false" class="text-white/80 hover:text-white p-1 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-5 space-y-4 text-xs">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Judul</span>
                    <p class="text-sm font-bold text-slate-900 mt-1 leading-relaxed" x-text="detailData.judul_usulan"></p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-slate-50 p-3.5 rounded-xl border border-slate-200">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Ketua</span>
                        <p class="font-bold text-slate-900 mt-0.5" x-text="detailData.nama_ketua"></p>
                        <p class="text-[11px] text-slate-500 mt-0.5" x-text="'NIDN / NUPN: ' + detailData.nidn_ketua"></p>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Program Studi &amp; Nominal</span>
                        <p class="font-semibold text-slate-800 mt-0.5" x-text="detailData.nama_prodi"></p>
                        <p class="font-black text-emerald-700 text-sm mt-0.5 tabular-nums" x-text="detailData.nominal_formatted"></p>
                    </div>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Anggota</span>
                    <template x-if="detailData.anggota_list && detailData.anggota_list.length > 0">
                        <ul class="mt-1.5 space-y-1 text-slate-700 font-medium list-disc list-inside">
                            <template x-for="(m, idx) in detailData.anggota_list" :key="idx">
                                <li x-text="m"></li>
                            </template>
                        </ul>
                    </template>
                    <template x-if="!detailData.anggota_list || detailData.anggota_list.length === 0">
                        <p class="text-slate-400 mt-1">-</p>
                    </template>
                </div>
                <div class="pt-2 border-t border-slate-100 flex justify-end">
                    <button type="button" @click="detailModalOpen = false"
                            class="h-9 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Pilih Skema BIMA & Mulai Ajukan Proposal Baru --}}
    <div x-show="ajukanModalOpen"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4"
         @keydown.escape.window="ajukanModalOpen = false">
        <div @click.away="ajukanModalOpen = false"
             class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-3xl max-h-[88vh] flex flex-col overflow-hidden">
            <div class="px-5 py-4 bg-gradient-to-r from-[#681727] to-[#8C1D32] text-white flex items-center justify-between shrink-0">
                <div>
                    <h3 class="font-extrabold text-sm sm:text-base">Ajukan Proposal {{ $labelBidang }} Baru (Wizard 6 Langkah)</h3>
                    <p class="text-[11px] text-rose-100 font-medium">
                        Pilih skema hibah aktif{{ $activePeriod ? ' untuk periode ' . $activePeriod->tahun_akademik : '' }}.
                    </p>
                </div>
                <button type="button" @click="ajukanModalOpen = false" class="text-white/80 hover:text-white p-1 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-5 overflow-y-auto space-y-4">
                @if(!$activePeriod)
                    <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-300 text-amber-900 text-xs font-bold">
                        Saat ini belum ada periode pengajuan hibah yang berstatus terbuka.
                    </div>
                @else
                    @php
                        $matchedSchemes = ($activeSchemes ?? collect())->filter(function ($s) use ($kategori) {
                            return strtolower((string) $s->kategori) === strtolower((string) $kategori);
                        });
                        $displaySchemes = $matchedSchemes->isNotEmpty() ? $matchedSchemes : ($activeSchemes ?? collect());
                    @endphp

                    @if($displaySchemes->isEmpty())
                        <div class="p-6 text-center rounded-xl border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-500">
                            Belum ada skema aktif untuk kategori {{ strtolower($labelBidang) }}.
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            @foreach($displaySchemes as $skema)
                                <div class="rounded-xl border border-slate-200 hover:border-rose-300 bg-slate-50/70 hover:bg-white p-4 flex flex-col justify-between transition shadow-xs">
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase bg-rose-100 text-[#681727]">
                                                {{ $skema->kode_skema }}
                                            </span>
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase bg-slate-100 text-slate-700">
                                                {{ ucfirst($skema->kategori) }}
                                            </span>
                                        </div>
                                        <h4 class="text-sm font-extrabold text-slate-900 leading-snug">{{ $skema->nama_skema }}</h4>
                                        <div class="text-[11px] text-slate-600 space-y-1 font-medium">
                                            <div>Pagu Maksimal: <span class="font-extrabold text-slate-800">Rp {{ number_format((float) $skema->plafon_dana, 0, ',', '.') }}</span></div>
                                            <div>Rentang TKT: <span class="font-bold text-slate-800">TKT {{ $skema->min_tkt ?? 1 }} &ndash; {{ $skema->max_tkt ?? 9 }}</span></div>
                                        </div>
                                    </div>
                                    <div class="pt-3 mt-3 border-t border-slate-200">
                                        <a href="{{ route('usulan.start', $skema->id) }}"
                                           class="w-full h-9 px-3 rounded-lg bg-[#681727] hover:bg-[#52111e] text-white text-xs font-extrabold flex items-center justify-center gap-1.5 transition shadow-xs">
                                            <span>Mulai Pengisian Wizard</span>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    {{-- Modal Tambah Data 8 Kolom --}}
    <div x-show="createModalOpen"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4"
         @keydown.escape.window="createModalOpen = false">
        <div @click.away="createModalOpen = false"
             class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-2xl overflow-hidden">
            <div class="px-5 py-4 bg-[#681727] text-white flex items-center justify-between">
                <div>
                    <h3 class="font-extrabold text-sm sm:text-base">Tambah Data {{ $labelBidang }}</h3>
                    <p class="text-[11px] text-rose-100 font-medium">Lengkapi 8 kolom rincian {{ strtolower($labelBidang) }} UHN</p>
                </div>
                <button type="button" @click="createModalOpen = false" class="text-white/80 hover:text-white p-1 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('usulan.store-basic') }}" class="p-5 space-y-4">
                @csrf
                <input type="hidden" name="kategori_usulan" value="{{ $kat }}">

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 mb-1">No Urut (Opsional)</label>
                        <input type="number" name="no_urut" placeholder="Otomatis"
                               class="w-full h-9 px-3 rounded-xl border border-slate-300 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-slate-900 focus:outline-hidden">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-extrabold text-slate-700 mb-1">Semester</label>
                        <input type="text" name="semester" value="2024 2025 Genap" required placeholder="Contoh: 2024 2025 Genap"
                               class="w-full h-9 px-3 rounded-xl border border-slate-300 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-slate-900 focus:outline-hidden">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 mb-1">Judul {{ $labelBidang }}</label>
                    <textarea name="judul_usulan" rows="3" required placeholder="Masukkan judul lengkap..."
                              class="w-full p-3 rounded-xl border border-slate-300 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-slate-900 focus:outline-hidden"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-extrabold text-slate-700 mb-1">Ketua</label>
                        <input type="text" name="nama_ketua" required placeholder="Nama lengkap Ketua beserta gelar"
                               class="w-full h-9 px-3 rounded-xl border border-slate-300 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-slate-900 focus:outline-hidden">
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 mb-1">NIDN / NUPN</label>
                        <input type="text" name="nidn_ketua" placeholder="Contoh: 0612079602 atau -"
                               class="w-full h-9 px-3 rounded-xl border border-slate-300 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-slate-900 focus:outline-hidden">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 mb-1">Anggota</label>
                    <input type="text" name="nama_anggota" placeholder="Nama dosen/mahasiswa anggota (pisahkan dengan titik koma ;)"
                           class="w-full h-9 px-3 rounded-xl border border-slate-300 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-slate-900 focus:outline-hidden">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 mb-1">Program Studi (Prodi)</label>
                        <input type="text" list="prodiOptionsList" name="nama_prodi" required placeholder="Pilih atau ketik Program Studi"
                               class="w-full h-9 px-3 rounded-xl border border-slate-300 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-slate-900 focus:outline-hidden">
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 mb-1">Nominal (Rp)</label>
                        <input type="number" step="1" min="0" name="total_rab" value="0" required
                               class="w-full h-9 px-3 rounded-xl border border-slate-300 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-slate-900 focus:outline-hidden">
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-200 flex items-center justify-end gap-2">
                    <button type="button" @click="createModalOpen = false"
                            class="h-9 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                            class="h-9 px-5 rounded-xl bg-[#681727] hover:bg-[#52111e] text-white font-extrabold text-xs shadow-xs transition cursor-pointer">
                        Simpan Data {{ $isPengabdian ? 'Pengabdian' : 'Penelitian' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit Data --}}
    <div x-show="editModalOpen"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4"
         @keydown.escape.window="editModalOpen = false">
        <div @click.away="editModalOpen = false"
             class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-2xl overflow-hidden">
            <div class="px-5 py-4 bg-[#681727] text-white flex items-center justify-between">
                <div>
                    <h3 class="font-extrabold text-sm sm:text-base">Edit Data {{ $labelBidang }}</h3>
                    <p class="text-[11px] text-rose-100 font-medium">Perbarui rincian {{ strtolower($labelBidang) }} UHN</p>
                </div>
                <button type="button" @click="editModalOpen = false" class="text-white/80 hover:text-white p-1 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" :action="editData.update_url" class="p-5 space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 mb-1">No Urut</label>
                        <input type="number" name="no_urut" x-model="editData.no_urut" required
                               class="w-full h-9 px-3 rounded-xl border border-slate-300 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-slate-900 focus:outline-hidden">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-extrabold text-slate-700 mb-1">Semester</label>
                        <input type="text" name="semester" x-model="editData.semester" required placeholder="Contoh: 2024 2025 Genap"
                               class="w-full h-9 px-3 rounded-xl border border-slate-300 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-slate-900 focus:outline-hidden">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 mb-1">Judul {{ $labelBidang }}</label>
                    <textarea name="judul_usulan" x-model="editData.judul_usulan" rows="3" required
                              class="w-full p-3 rounded-xl border border-slate-300 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-slate-900 focus:outline-hidden"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-extrabold text-slate-700 mb-1">Ketua</label>
                        <input type="text" name="nama_ketua" x-model="editData.nama_ketua" required
                               class="w-full h-9 px-3 rounded-xl border border-slate-300 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-slate-900 focus:outline-hidden">
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 mb-1">NIDN / NUPN</label>
                        <input type="text" name="nidn_ketua" x-model="editData.nidn_ketua"
                               class="w-full h-9 px-3 rounded-xl border border-slate-300 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-slate-900 focus:outline-hidden">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 mb-1">Anggota</label>
                    <input type="text" name="nama_anggota" x-model="editData.nama_anggota"
                           class="w-full h-9 px-3 rounded-xl border border-slate-300 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-slate-900 focus:outline-hidden">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 mb-1">Program Studi (Prodi)</label>
                        <input type="text" list="prodiOptionsList" name="nama_prodi" x-model="editData.nama_prodi" required
                               class="w-full h-9 px-3 rounded-xl border border-slate-300 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-slate-900 focus:outline-hidden">
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 mb-1">Nominal (Rp)</label>
                        <input type="number" step="1" min="0" name="total_rab" x-model="editData.total_rab" required
                               class="w-full h-9 px-3 rounded-xl border border-slate-300 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-slate-900 focus:outline-hidden">
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-200 flex items-center justify-end gap-2">
                    <button type="button" @click="editModalOpen = false"
                            class="h-9 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                            class="h-9 px-5 rounded-xl bg-[#681727] hover:bg-[#52111e] text-white font-extrabold text-xs shadow-xs transition cursor-pointer">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

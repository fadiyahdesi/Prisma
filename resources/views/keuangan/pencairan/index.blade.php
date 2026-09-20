@extends('layouts.app')

@section('title', 'Pencairan Dana Hibah - PRISMA UHN')

@section('content')
<div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <x-sidebar />

    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        <header class="bg-white border-b border-slate-200 py-4 sticky top-0 z-30 shadow-sm">
            <div class="w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <h1 class="font-extrabold text-xl text-slate-900 leading-tight">Pencairan & Pelunasan Dana Hibah</h1>
                        <p class="text-xs font-semibold text-slate-500">Portal Divisi Keuangan LPPM &bull; Verifikasi Rekening, Penyaluran Termin I (70%), Termin II (30%) & Pelunasan 100%</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300">
                        Divisi Keuangan LPPM
                    </span>
                </div>
            </div>
        </header>

        <main class="flex-grow w-full px-4 sm:px-6 lg:px-8 py-8 space-y-6">
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

            <!-- Executive Financial & Operational KPI Summary -->
            <div class="space-y-4">
                <!-- Row 1: Macro Financial Balance (Pagu vs Realisasi) -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <!-- 1. Total Pagu Komitmen -->
                    <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-xs hover:border-slate-300 transition flex flex-col justify-between space-y-4">
                        <div class="flex items-start justify-between">
                            <div>
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Pagu Komitmen Anggaran</span>
                                <div class="text-2xl sm:text-3xl font-black text-slate-900 mt-1.5 whitespace-nowrap tracking-tight">
                                    <span class="text-base sm:text-lg font-bold text-slate-400 mr-1">Rp</span>{{ number_format($stats['total_pagu'], 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-3 border-t border-slate-100 text-xs text-slate-500">
                            <span class="font-semibold text-slate-700">{{ $stats['total_contracts'] }} Kontrak SPK Pemenang</span>
                            <span class="text-slate-400">Tahun Anggaran {{ date('Y') }}</span>
                        </div>
                    </div>

                    <!-- 2. Realisasi Dana Tersalurkan -->
                    <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-xs hover:border-slate-300 transition flex flex-col justify-between space-y-4">
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Realisasi Dana Tersalurkan</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        {{ $stats['disbursement_progress_pct'] }}% Terserap
                                    </span>
                                </div>
                                <div class="text-2xl sm:text-3xl font-black text-emerald-700 mt-1.5 whitespace-nowrap tracking-tight">
                                    <span class="text-base sm:text-lg font-bold text-emerald-600 mr-1">Rp</span>{{ number_format($stats['total_disbursed'], 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                        </div>
                        <div class="space-y-1.5 pt-3 border-t border-slate-100">
                            <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden flex">
                                <div class="h-full bg-emerald-500 rounded-full transition-all duration-500" style="width: {{ $stats['disbursement_progress_pct'] }}%"></div>
                            </div>
                            <div class="flex items-center justify-between text-xs text-slate-500">
                                <span>Termin I (70%): <strong class="text-slate-800 font-bold">Rp {{ number_format($stats['total_termin1_disbursed'], 0, ',', '.') }}</strong></span>
                                <span>Termin II (30%): <strong class="text-slate-800 font-bold">Rp {{ number_format($stats['total_termin2_disbursed'], 0, ',', '.') }}</strong></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row 2: Antrean Tindakan Operasional Keuangan (3 Cards) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- 3. Perlu Verifikasi Rekening -->
                    <a href="{{ route('keuangan.pencairan.index', array_merge(request()->except('page', 'tab'), ['tab' => 'need_verification'])) }}" 
                       class="p-5 rounded-2xl bg-white border {{ $tab === 'need_verification' ? 'border-amber-400 ring-2 ring-amber-100' : 'border-slate-200' }} shadow-xs hover:border-amber-300 hover:shadow-sm transition flex flex-col justify-between space-y-3 group">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider group-hover:text-amber-700 transition">Verifikasi Rekening</span>
                            <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                        </div>
                        <div>
                            <span class="text-2xl font-black text-slate-900 group-hover:text-amber-800 transition">{{ $stats['pending_verification'] }} Usulan</span>
                            <p class="text-xs text-slate-500 mt-0.5">Buku tabungan baru diunggah peneliti</p>
                        </div>
                        <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] {{ $stats['pending_verification'] > 0 ? 'text-amber-700 font-bold' : 'text-slate-400' }}">
                            <span>{{ $stats['pending_verification'] > 0 ? 'Perlu tindakan validasi' : 'Tidak ada antrean validasi' }}</span>
                            <span class="group-hover:translate-x-1 transition font-bold">&rarr;</span>
                        </div>
                    </a>

                    <!-- 4. Siap Transfer Termin I (70%) -->
                    <a href="{{ route('keuangan.pencairan.index', array_merge(request()->except('page', 'tab'), ['tab' => 'ready_termin1'])) }}" 
                       class="p-5 rounded-2xl bg-white border {{ $tab === 'ready_termin1' ? 'border-blue-400 ring-2 ring-blue-100' : 'border-slate-200' }} shadow-xs hover:border-blue-300 hover:shadow-sm transition flex flex-col justify-between space-y-3 group">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider group-hover:text-blue-700 transition">Siap Transfer Termin I</span>
                            <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            </div>
                        </div>
                        <div>
                            <span class="text-2xl font-black text-slate-900 group-hover:text-blue-800 transition">{{ $stats['ready_termin1'] }} Usulan</span>
                            <p class="text-xs text-slate-500 mt-0.5">Pencairan dana tahap awal (70%)</p>
                        </div>
                        <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] {{ $stats['ready_termin1'] > 0 ? 'text-blue-700 font-bold' : 'text-slate-400' }}">
                            <span>{{ $stats['ready_termin1'] > 0 ? 'Siap proses transfer bank' : 'Tidak ada antrean transfer' }}</span>
                            <span class="group-hover:translate-x-1 transition font-bold">&rarr;</span>
                        </div>
                    </a>

                    <!-- 5. Siap Termin II (30%) & Pelunasan -->
                    <a href="{{ route('keuangan.pencairan.index', array_merge(request()->except('page', 'tab'), ['tab' => 'ready_termin2'])) }}" 
                       class="p-5 rounded-2xl bg-white border {{ $tab === 'ready_termin2' ? 'border-indigo-400 ring-2 ring-indigo-100' : 'border-slate-200' }} shadow-xs hover:border-indigo-300 hover:shadow-sm transition flex flex-col justify-between space-y-3 group">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider group-hover:text-indigo-700 transition">Siap Termin II & Pelunasan</span>
                            <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                        </div>
                        <div>
                            <span class="text-2xl font-black text-slate-900 group-hover:text-indigo-800 transition">{{ $stats['ready_termin2'] }} Usulan Siap</span>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $stats['completed'] }} Usulan telah lunas 100%</p>
                        </div>
                        <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] {{ $stats['ready_termin2'] > 0 ? 'text-indigo-700 font-bold' : 'text-slate-400' }}">
                            <span>{{ $stats['ready_termin2'] > 0 ? 'Laporan akhir siap, siap transfer' : 'Menunggu berkas akhir' }}</span>
                            <span class="group-hover:translate-x-1 transition font-bold">&rarr;</span>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Filter Tabs & Search Bar -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-1.5 text-xs font-bold">
                    <a href="{{ route('keuangan.pencairan.index', array_merge(request()->except('page', 'tab'), ['tab' => 'all'])) }}" 
                       class="px-3.5 py-2 rounded-xl transition {{ $tab === 'all' ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        Semua Usulan ({{ $stats['total_contracts'] }})
                    </a>
                    <a href="{{ route('keuangan.pencairan.index', array_merge(request()->except('page', 'tab'), ['tab' => 'need_verification'])) }}" 
                       class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ $tab === 'need_verification' ? 'bg-amber-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        <span>Perlu Verifikasi Rekening</span>
                        @if($stats['pending_verification'] > 0)
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $tab === 'need_verification' ? 'bg-white text-amber-800 font-black' : 'bg-amber-500 text-white font-black' }}">{{ $stats['pending_verification'] }}</span>
                        @endif
                    </a>
                    <a href="{{ route('keuangan.pencairan.index', array_merge(request()->except('page', 'tab'), ['tab' => 'ready_termin1'])) }}" 
                       class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ $tab === 'ready_termin1' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        <span>Siap Transfer Termin I (70%)</span>
                        @if($stats['ready_termin1'] > 0)
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $tab === 'ready_termin1' ? 'bg-white text-blue-800 font-black' : 'bg-blue-500 text-white font-black' }}">{{ $stats['ready_termin1'] }}</span>
                        @endif
                    </a>
                    <a href="{{ route('keuangan.pencairan.index', array_merge(request()->except('page', 'tab'), ['tab' => 'ready_termin2'])) }}" 
                       class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ $tab === 'ready_termin2' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        <span>Siap Termin II (30%)</span>
                        @if($stats['ready_termin2'] > 0)
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $tab === 'ready_termin2' ? 'bg-white text-indigo-800 font-black' : 'bg-indigo-500 text-white font-black' }}">{{ $stats['ready_termin2'] }}</span>
                        @endif
                    </a>
                    <a href="{{ route('keuangan.pencairan.index', array_merge(request()->except('page', 'tab'), ['tab' => 'completed'])) }}" 
                       class="px-3.5 py-2 rounded-xl transition {{ $tab === 'completed' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        Pelunasan 100% ({{ $stats['completed'] }})
                    </a>
                </div>

                <form method="GET" action="{{ route('keuangan.pencairan.index') }}" class="flex items-center gap-2">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    <div class="relative w-full sm:w-64">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari judul / kode / SPK / peneliti..."
                               class="w-full pl-9 pr-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-none">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    @if($search)
                        <a href="{{ route('keuangan.pencairan.index', ['tab' => $tab]) }}" class="px-2.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold" title="Reset pencarian">
                            ✕
                        </a>
                    @endif
                </form>
            </div>

            <!-- Financial Contracts Card List -->
            <div class="space-y-5">
                @forelse($contracts as $kontrak)
                    @php
                        $u = $kontrak->usulan;
                        $lapAkhir = $u?->laporanAkhir;
                        $p1 = $kontrak->pencairan->firstWhere('termin', 1);
                        $p2 = $kontrak->pencairan->firstWhere('termin', 2);
                        $t1Disbursed = $p1 !== null;
                        $t2Disbursed = $p2 !== null;
                        $isCompleted = $t2Disbursed;
                        $hasLaporanAkhir = $lapAkhir !== null;
                        $isReadyTermin2 = $t1Disbursed && $hasLaporanAkhir && !$t2Disbursed;
                        $isReadyTermin1 = $kontrak->isFullySigned() && $kontrak->hasVerifiedRekening() && !$t1Disbursed;
                        $needsRekeningVerification = $kontrak->nomor_rekening && !$kontrak->rekening_verified_at;
                    @endphp
                    <div class="bg-white rounded-3xl border border-slate-200 hover:border-slate-300 shadow-sm hover:shadow-md transition duration-200 overflow-hidden flex flex-col justify-between">
                        <!-- Card Main Content -->
                        <div class="p-6 sm:p-7 space-y-6">
                            <!-- Header Row: Metadata Chips & Overall Status -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200 tracking-wide">
                                        {{ $u->kode_usulan }}
                                    </span>
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $u->skema->nama_skema ?? '-' }}
                                    </span>
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                        Thn {{ $u->periode->tahun_anggaran ?? date('Y') }}
                                    </span>
                                    <span class="text-xs text-slate-300 hidden sm:inline">&bull;</span>
                                    <span class="text-xs text-slate-500 font-medium">
                                        SPK: <strong class="text-slate-800">{{ $kontrak->nomor_kontrak }}</strong>
                                    </span>
                                </div>

                                <div>
                                    @if($isCompleted)
                                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300">
                                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Hibah Lunas 100% (Completed)
                                        </span>
                                    @elseif($isReadyTermin2)
                                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-xs font-black bg-indigo-100 text-indigo-800 border border-indigo-300">
                                            <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                            Siap Transfer Termin II (30%)
                                        </span>
                                    @elseif($isReadyTermin1)
                                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-xs font-black bg-blue-100 text-blue-800 border border-blue-300">
                                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                            Siap Transfer Termin I (70%)
                                        </span>
                                    @elseif($needsRekeningVerification)
                                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-xs font-black bg-amber-100 text-amber-900 border border-amber-300">
                                            <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                            Perlu Verifikasi Rekening Bank
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Menunggu TTD SPK Pengusul
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Proposal Title & Information Grid -->
                            <div class="space-y-3.5">
                                <h3 class="text-base sm:text-lg font-black text-slate-900 leading-snug">
                                    <a href="{{ route('keuangan.pencairan.show', $kontrak) }}" class="hover:text-blue-700 transition">
                                        {{ $u->judul_usulan }}
                                    </a>
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                                    <!-- Peneliti & Fakultas -->
                                    <div class="flex items-center gap-3 p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80">
                                        <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 font-black text-sm">
                                            {{ strtoupper(substr($u->pengusul->name ?? 'P', 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-extrabold text-slate-900 truncate">
                                                {{ $u->pengusul->name ?? '-' }}
                                                <span class="font-normal text-slate-500 text-[11px]">(NIDN: {{ $u->pengusul->nidn_nim ?? '-' }})</span>
                                            </div>
                                            <div class="text-[11px] text-slate-500 truncate mt-0.5">
                                                {{ $u->pengusul->fakultas->nama_fakultas ?? '-' }} &bull; {{ $u->pengusul->prodi->nama_prodi ?? '-' }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Rekening Bank Tujuan -->
                                    <div class="flex items-center justify-between gap-3 p-3.5 rounded-2xl {{ $kontrak->rekening_verified_at ? 'bg-slate-50 border-slate-200/80' : ($kontrak->nomor_rekening ? 'bg-amber-50/60 border-amber-200' : 'bg-rose-50/60 border-rose-200') }} border">
                                        <div class="flex items-center gap-3 min-w-0">
                                             <div class="w-10 h-10 rounded-xl {{ $kontrak->rekening_verified_at ? 'bg-emerald-100 text-emerald-700' : ($kontrak->nomor_rekening ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }} flex items-center justify-center shrink-0">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                            </div>
                                            <div class="min-w-0">
                                                <div class="font-extrabold text-slate-900 text-xs truncate">
                                                    @if($kontrak->nomor_rekening)
                                                        {{ $kontrak->nama_bank }} &bull; <span class="font-bold text-slate-800 tracking-wide">{{ $kontrak->nomor_rekening }}</span>
                                                    @else
                                                        <span class="text-rose-600 italic">Rekening Belum Diisi</span>
                                                    @endif
                                                </div>
                                                <div class="text-[11px] text-slate-500 truncate mt-0.5">
                                                    a.n {{ $kontrak->nama_pemilik_rekening ?? ($kontrak->nomor_rekening ? '-' : 'Menunggu input peneliti') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="shrink-0">
                                            @if($kontrak->rekening_verified_at)
                                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-200 inline-flex items-center gap-1">
                                                    <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                    Tervalidasi
                                                </span>
                                            @elseif($kontrak->nomor_rekening)
                                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-black bg-amber-100 text-amber-900 border border-amber-300 inline-flex items-center gap-1">
                                                    <svg class="w-3 h-3 text-amber-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                                    Perlu Validasi
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-rose-100 text-rose-800">
                                                    Kosong
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 3-Stage Financial Realization Pipeline -->
                            <div class="space-y-2.5">
                                <div class="flex items-center justify-between text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                        Tahapan Realisasi Pencairan Anggaran
                                    </span>
                                    <span class="font-bold text-slate-600">Total Pagu: Rp {{ number_format($kontrak->pagu_disetujui, 0, ',', '.') }}</span>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
                                    <!-- TAHAP 1: Legalitas & Rekening Bank -->
                                    <div class="p-4 rounded-2xl border transition {{ ($kontrak->isFullySigned() && $kontrak->hasVerifiedRekening()) ? 'bg-emerald-50/40 border-emerald-200' : ($needsRekeningVerification ? 'bg-amber-50/50 border-amber-300 ring-2 ring-amber-100' : 'bg-slate-50 border-slate-200') }} flex flex-col justify-between space-y-3">
                                        <div class="space-y-2">
                                            <div class="flex items-center justify-between">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-black {{ ($kontrak->isFullySigned() && $kontrak->hasVerifiedRekening()) ? 'bg-emerald-100 text-emerald-800' : ($needsRekeningVerification ? 'bg-amber-100 text-amber-900' : 'bg-slate-200 text-slate-700') }}">
                                                    TAHAP 1
                                                </span>
                                                @if($kontrak->isFullySigned() && $kontrak->hasVerifiedRekening())
                                                    <span class="text-[11px] font-extrabold text-emerald-700 flex items-center gap-1">
                                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                        Prasyarat Lengkap
                                                    </span>
                                                @elseif($needsRekeningVerification)
                                                    <span class="text-[11px] font-extrabold text-amber-700">Verifikasi Bank</span>
                                                @else
                                                    <span class="text-[11px] font-bold text-slate-500">Belum Lengkap</span>
                                                @endif
                                            </div>
                                            <div class="font-extrabold text-slate-900 text-xs">
                                                Legalitas & Rekening
                                            </div>
                                            <div class="space-y-1.5 text-xs">
                                                <div class="flex items-center justify-between text-[11px]">
                                                    <span class="text-slate-500">SPK Digital:</span>
                                                    @if($kontrak->signed_by_pengusul)
                                                        <span class="font-bold text-emerald-700">✓ Ditandatangani</span>
                                                    @else
                                                        <span class="font-bold text-amber-600">⏳ Menunggu TTD</span>
                                                    @endif
                                                </div>
                                                <div class="flex items-center justify-between text-[11px]">
                                                    <span class="text-slate-500">Buku Tabungan:</span>
                                                    @if($kontrak->rekening_verified_at)
                                                        <span class="font-bold text-emerald-700">✓ Terverifikasi</span>
                                                    @elseif($kontrak->nomor_rekening)
                                                        <span class="font-bold text-amber-600">⚠️ Belum Verifikasi</span>
                                                    @else
                                                        <span class="text-slate-400 italic">Belum Diunggah</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="pt-2 border-t {{ ($kontrak->isFullySigned() && $kontrak->hasVerifiedRekening()) ? 'border-emerald-200/60 text-emerald-800 font-semibold' : 'border-slate-200 text-slate-500' }} text-[11px]">
                                            @if($kontrak->isFullySigned() && $kontrak->hasVerifiedRekening())
                                                <span>✓ Siap Pencairan Dana Awal</span>
                                            @elseif($needsRekeningVerification)
                                                <span class="text-amber-800 font-bold">Buku tabungan siap divalidasi</span>
                                            @else
                                                <span>Menunggu kelengkapan tanda tangan</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- TAHAP 2: Termin I (70%) -->
                                    <div class="p-4 rounded-2xl border transition {{ $t1Disbursed ? 'bg-emerald-50/40 border-emerald-200' : ($isReadyTermin1 ? 'bg-blue-50/50 border-blue-300 ring-2 ring-blue-100' : 'bg-slate-50 border-slate-200 opacity-90') }} flex flex-col justify-between space-y-3">
                                        <div class="space-y-2">
                                            <div class="flex items-center justify-between">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-black {{ $t1Disbursed ? 'bg-emerald-100 text-emerald-800' : ($isReadyTermin1 ? 'bg-blue-100 text-blue-800' : 'bg-slate-200 text-slate-700') }}">
                                                    TAHAP 2 &bull; 70%
                                                </span>
                                                <span class="font-black text-xs {{ $t1Disbursed ? 'text-emerald-800' : ($isReadyTermin1 ? 'text-blue-800' : 'text-slate-700') }}">
                                                    Rp {{ number_format($kontrak->dana_termin_1, 0, ',', '.') }}
                                                </span>
                                            </div>
                                            <div class="font-extrabold text-slate-900 text-xs">
                                                Pencairan Termin I (70%)
                                            </div>
                                            <div class="space-y-1.5 text-xs">
                                                <div class="flex items-center justify-between text-[11px]">
                                                    <span class="text-slate-500">Status Transfer:</span>
                                                    @if($t1Disbursed)
                                                        <span class="font-bold text-emerald-700 flex items-center gap-1">
                                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                            Sudah Ditransfer
                                                        </span>
                                                    @elseif($isReadyTermin1)
                                                        <span class="font-bold text-blue-700 flex items-center gap-1">
                                                            <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                                                            Siap Ditransfer
                                                        </span>
                                                    @else
                                                        <span class="text-slate-400 italic">Menunggu Tahap 1</span>
                                                    @endif
                                                </div>
                                                <div class="flex items-center justify-between text-[11px]">
                                                    <span class="text-slate-500">Ref / SP2D:</span>
                                                    @if($p1)
                                                        <span class="font-bold text-slate-800 truncate max-w-[120px]" title="{{ $p1->nomor_referensi }}">{{ $p1->nomor_referensi ?? '-' }}</span>
                                                    @else
                                                        <span class="text-slate-400">-</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="pt-2 border-t {{ $t1Disbursed ? 'border-emerald-200/60 text-emerald-800' : ($isReadyTermin1 ? 'border-blue-200 text-blue-800 font-bold' : 'border-slate-200 text-slate-400') }} text-[11px]">
                                            @if($p1 && $p1->tanggal_transfer)
                                                <span>Tgl: {{ \Carbon\Carbon::parse($p1->tanggal_transfer)->isoFormat('D MMM Y') }}</span>
                                            @elseif($isReadyTermin1)
                                                <span>Siap input bukti transfer</span>
                                            @else
                                                <span>Dana operasional awal riset</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- TAHAP 3: Termin II (30%) & Pelunasan -->
                                    <div class="p-4 rounded-2xl border transition {{ $t2Disbursed ? 'bg-emerald-50/40 border-emerald-200' : ($isReadyTermin2 ? 'bg-indigo-50/50 border-indigo-300 ring-2 ring-indigo-100' : 'bg-slate-50 border-slate-200 opacity-90') }} flex flex-col justify-between space-y-3">
                                        <div class="space-y-2">
                                            <div class="flex items-center justify-between">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-black {{ $t2Disbursed ? 'bg-emerald-100 text-emerald-800' : ($isReadyTermin2 ? 'bg-indigo-100 text-indigo-800' : 'bg-slate-200 text-slate-700') }}">
                                                    TAHAP 3 &bull; 30%
                                                </span>
                                                <span class="font-black text-xs {{ $t2Disbursed ? 'text-emerald-800' : ($isReadyTermin2 ? 'text-indigo-800' : 'text-slate-700') }}">
                                                    Rp {{ number_format($kontrak->dana_termin_2, 0, ',', '.') }}
                                                </span>
                                            </div>
                                            <div class="font-extrabold text-slate-900 text-xs">
                                                Termin II & Pelunasan 100%
                                            </div>
                                            <div class="space-y-1.5 text-xs">
                                                <div class="flex items-center justify-between text-[11px]">
                                                    <span class="text-slate-500">Lap. Akhir 100%:</span>
                                                    @if($hasLaporanAkhir)
                                                        <span class="font-bold text-emerald-700">✓ Siap Terunggah</span>
                                                    @else
                                                        <span class="font-bold text-amber-600">Belum Diunggah</span>
                                                    @endif
                                                </div>
                                                <div class="flex items-center justify-between text-[11px]">
                                                    <span class="text-slate-500">Status Akhir:</span>
                                                    @if($t2Disbursed)
                                                        <span class="font-bold text-emerald-700 flex items-center gap-1">
                                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                            Lunas 100%
                                                        </span>
                                                    @elseif($isReadyTermin2)
                                                        <span class="font-bold text-indigo-700 flex items-center gap-1">
                                                            <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                                                            Siap Ditransfer
                                                        </span>
                                                    @elseif(!$t1Disbursed)
                                                        <span class="text-slate-400 italic">Menunggu Termin I</span>
                                                    @else
                                                        <span class="text-slate-400 italic">Menunggu Lap. Akhir</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="pt-2 border-t {{ $t2Disbursed ? 'border-emerald-200/60 text-emerald-800' : ($isReadyTermin2 ? 'border-indigo-200 text-indigo-800 font-bold' : 'border-slate-200 text-slate-400') }} text-[11px]">
                                            @if($p2 && $p2->tanggal_transfer)
                                                <span>Lunas: {{ \Carbon\Carbon::parse($p2->tanggal_transfer)->isoFormat('D MMM Y') }}</span>
                                            @elseif($isReadyTermin2)
                                                <span>Naskah siap, silakan cairkan</span>
                                            @else
                                                <span>Penyaluran sisa 30% anggaran</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Action & Financial Progress Footer -->
                        @php
                            $realisasi = ($t2Disbursed ? (float)$kontrak->pagu_disetujui : ($t1Disbursed ? (float)$kontrak->dana_termin_1 : 0));
                            $pct = $kontrak->pagu_disetujui > 0 ? min(100, round(($realisasi / $kontrak->pagu_disetujui) * 100)) : 0;
                        @endphp
                        <div class="bg-slate-50/80 border-t border-slate-200/80 px-6 sm:px-7 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <!-- Left: Realisasi Summary & Progress Bar -->
                            <div class="space-y-1.5 sm:min-w-[260px]">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-slate-500 font-bold text-[11px]">Realisasi Tersalurkan:</span>
                                    <span class="font-black {{ $pct == 100 ? 'text-emerald-700' : ($pct > 0 ? 'text-blue-700' : 'text-slate-700') }}">
                                        Rp {{ number_format($realisasi, 0, ',', '.') }} <span class="text-slate-400 font-normal text-[11px]">/ Rp {{ number_format($kontrak->pagu_disetujui, 0, ',', '.') }}</span>
                                    </span>
                                </div>
                                <div class="w-full h-2 bg-slate-200 rounded-full overflow-hidden flex">
                                    <div class="h-full {{ $pct == 100 ? 'bg-emerald-500' : 'bg-blue-600' }} rounded-full transition-all duration-300" style="width: {{ $pct }}%"></div>
                                </div>
                                <div class="flex items-center justify-between text-[10px] text-slate-500 font-semibold">
                                    <span>Terserap {{ $pct }}%</span>
                                    <span>{{ $pct == 100 ? '✓ Pelunasan Selesai' : ($pct == 70 ? 'Termin I Tersalurkan' : 'Belum Ada Penyaluran') }}</span>
                                </div>
                            </div>

                            <!-- Right: Action Buttons Group -->
                            <div class="flex flex-wrap items-center gap-2">
                                <a href="{{ route('kontrak.download-pdf', $kontrak) }}" target="_blank"
                                   class="px-3.5 py-2 rounded-xl bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs border border-slate-200 shadow-xs transition flex items-center gap-1.5"
                                   title="Unduh Surat Perjanjian Kontrak (PDF)">
                                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span>Unduh SPK</span>
                                </a>

                                @if($isCompleted)
                                    <a href="{{ route('keuangan.pencairan.pelunasan-pdf', $kontrak) }}" target="_blank"
                                       class="px-3.5 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-extrabold text-xs border border-emerald-300 shadow-xs transition flex items-center gap-1.5"
                                       title="Cetak Tanda Bukti Pelunasan Hibah 100% Ber-QR Code">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>Bukti Pelunasan</span>
                                    </a>
                                @endif

                                {{-- Contextual Primary Action Button --}}
                                @if($needsRekeningVerification)
                                    <a href="{{ route('keuangan.pencairan.show', $kontrak) }}"
                                       class="px-4.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-black text-xs shadow-sm transition flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>Verifikasi Rekening</span>
                                        <span>&rarr;</span>
                                    </a>
                                @elseif($isReadyTermin1)
                                    <a href="{{ route('keuangan.pencairan.show', $kontrak) }}"
                                       class="px-4.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-black text-xs shadow-sm transition flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        <span>Transfer Termin I (70%)</span>
                                        <span>&rarr;</span>
                                    </a>
                                @elseif($isReadyTermin2)
                                    <a href="{{ route('keuangan.pencairan.show', $kontrak) }}"
                                       class="px-4.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs shadow-sm transition flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        <span>Transfer Termin II (30%)</span>
                                        <span>&rarr;</span>
                                    </a>
                                @elseif($isCompleted)
                                    <a href="{{ route('keuangan.pencairan.show', $kontrak) }}"
                                       class="px-4.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-black text-xs shadow-sm transition flex items-center gap-1.5">
                                        <span>Detail & Pelunasan</span>
                                        <span>&rarr;</span>
                                    </a>
                                @else
                                    <a href="{{ route('keuangan.pencairan.show', $kontrak) }}"
                                       class="px-4.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-black text-xs shadow-sm transition flex items-center gap-1.5">
                                        <span>Lihat Detail & Syarat</span>
                                        <span>&rarr;</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 rounded-3xl bg-white border border-slate-200 text-center space-y-3">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        </div>
                        <h4 class="font-extrabold text-sm text-slate-800">Tidak ada kontrak hibah ditemukan</h4>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto">
                            @if($search)
                                Tidak ada kontrak yang cocok dengan kata kunci "{{ $search }}".
                            @else
                                Belum ada usulan pada kategori filter ini.
                            @endif
                        </p>
                        @if($search || $tab !== 'all')
                            <div class="pt-2">
                                <a href="{{ route('keuangan.pencairan.index') }}" class="px-4 py-2 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 transition">
                                    Reset Filter & Pencarian
                                </a>
                            </div>
                        @endif
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($contracts->hasPages())
                <div class="pt-2">
                    {{ $contracts->links() }}
                </div>
            @endif
        </main>
    </div>
</div>
@endsection


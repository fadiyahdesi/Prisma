@extends('layouts.app')

@section('title', 'Pencairan Reward Insentif - Divisi Keuangan')

@section('content')
<div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <x-sidebar />

    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        <header class="bg-white border-b border-slate-200 py-4 sticky top-0 z-30 shadow-xs">
            <div class="w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <h1 class="font-extrabold text-xl text-slate-900 leading-tight">Pencairan Reward Insentif Publikasi &amp; HKI</h1>
                        <p class="text-xs font-semibold text-slate-500">Divisi Keuangan LPPM &bull; Penyaluran Transfer Bank Berbasis Rekening Multi-Penulis (US-11.4)</p>
                    </div>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300">
                    Divisi Keuangan
                </span>
            </div>
        </header>

        <main class="flex-grow w-full px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-sm font-bold flex items-center gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Tabs -->
            <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 pb-3">
                <a href="{{ route('keuangan.reward.index', ['tab' => 'ready']) }}"
                   class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $tab === 'ready' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                    <span>Siap Ditransfer (Persetujuan P3M)</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $tab === 'ready' ? 'bg-emerald-700 text-white' : 'bg-emerald-100 text-emerald-800' }}">
                        {{ $counts['ready'] }}
                    </span>
                </a>

                <a href="{{ route('keuangan.reward.index', ['tab' => 'disbursed']) }}"
                   class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $tab === 'disbursed' ? 'bg-slate-900 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                    <span>Selesai Ditransfer</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $tab === 'disbursed' ? 'bg-slate-800 text-white' : 'bg-slate-200 text-slate-700' }}">
                        {{ $counts['disbursed'] }}
                    </span>
                </a>

                <a href="{{ route('keuangan.reward.index', ['tab' => 'all']) }}"
                   class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $tab === 'all' ? 'bg-slate-900 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                    Semua
                </a>
            </div>

            <!-- List Table -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h2 class="font-bold text-sm text-slate-800">Daftar Berkas Klaim Siap Pencairan</h2>
                        <p class="text-xs text-slate-500">Buka rincian untuk memproses transfer perbankan ke masing-masing rekening anggota</p>
                    </div>

                    <form method="GET" action="{{ route('keuangan.reward.index') }}" class="flex items-center gap-2">
                        <input type="hidden" name="tab" value="{{ $tab }}">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor klaim / pengusul..."
                               class="px-3.5 py-1.5 text-xs font-semibold rounded-xl border border-slate-200 focus:outline-hidden focus:ring-2 focus:ring-emerald-500">
                        <button type="submit" class="px-3 py-1.5 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-slate-800">
                            Cari
                        </button>
                    </form>
                </div>

                @if($klaimList->isEmpty())
                    <div class="p-12 text-center">
                        <p class="text-xs font-bold text-slate-400">Tidak ada pengajuan klaim reward pada antrean ini.</p>
                    </div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach($klaimList as $item)
                            @php
                                $pendingDist = $item->distribusi->where('status_transfer', 'Pending')->count();
                                $doneDist = $item->distribusi->where('status_transfer', 'Disbursed')->count();
                                $totalDist = $item->distribusi->count();
                            @endphp
                            <div class="p-5 hover:bg-slate-50/70 transition flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                                <div class="space-y-1.5 flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="px-2.5 py-0.5 rounded-md text-[11px] font-extrabold {{ $item->jenis_klaim === 'Publikasi' ? 'bg-blue-100 text-blue-800 border border-blue-300' : 'bg-indigo-100 text-indigo-800 border border-indigo-300' }}">
                                            {{ $item->jenis_klaim }}
                                        </span>
                                        <span class="px-2.5 py-0.5 rounded-md text-[11px] font-extrabold bg-slate-100 text-slate-800 border border-slate-300">
                                            {{ $item->kategori_insentif }}
                                        </span>

                                        @if($item->status_klaim === 'Disbursed')
                                            <span class="px-2.5 py-0.5 rounded-md text-[11px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                Lunas (100% Ditransfer)
                                            </span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-md text-[11px] font-black bg-amber-100 text-amber-800 border border-amber-300">
                                                Tersisa {{ $pendingDist }} dari {{ $totalDist }} Penulis Belum Ditransfer
                                            </span>
                                        @endif
                                    </div>

                                    <h3 class="font-extrabold text-slate-900 text-sm leading-snug">
                                        {{ $item->nomor_klaim }} &bull; {{ $item->publikasi?->judul_artikel ?? $item->hki?->judul_hki ?? 'Aset' }}
                                    </h3>

                                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500 font-semibold">
                                        <span>Total Insentif: <strong class="text-emerald-700 font-black text-sm">Rp {{ number_format($item->total_reward, 0, ',', '.') }}</strong></span>
                                        <span>&bull;</span>
                                        <span>Pengusul: <strong class="text-slate-900">{{ $item->user->name }}</strong></span>
                                        <span>&bull;</span>
                                        <span>Disetujui P3M: {{ $item->approved_at ? $item->approved_at->format('d/m/Y H:i') : '-' }}</span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0 pt-2 lg:pt-0">
                                    <a href="{{ route('keuangan.reward.show', $item) }}"
                                       class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        <span>Kelola Pencairan Transfer</span>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $klaimList->links() }}
                    </div>
                @endif
            </div>
        </main>
    </div>
</div>
@endsection


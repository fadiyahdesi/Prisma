@extends('layouts.app')

@section('title', 'Klaim Reward Insentif Publikasi & HKI - PRISMA UHN')

@section('content')
<div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <x-sidebar />

    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        <header class="bg-white border-b border-slate-200 py-4 sticky top-0 z-30 shadow-xs">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <h1 class="font-extrabold text-xl text-slate-900 leading-tight">Klaim Reward Insentif Publikasi &amp; HKI</h1>
                        <p class="text-xs font-semibold text-slate-500">Matriks SK Rektor, Anti-Duplicate Claim Lock &amp; Distribusi Multi-Penulis (US-11.3 &amp; US-11.4)</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('reward.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Ajukan Klaim Baru</span>
                    </a>
                </div>
            </div>
        </header>

        <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-sm font-bold flex items-center gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Search & Filters -->
            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs">
                <form method="GET" action="{{ route('reward.index') }}" class="flex flex-col md:flex-row gap-3">
                    <div class="flex-1 relative">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor klaim, kategori insentif, judul aset, atau nama pengusul..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-blue-500 bg-slate-50/50">
                    </div>
                    <div class="w-full md:w-52">
                        <select name="status_klaim" onchange="this.form.submit()" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-blue-500 bg-white">
                            <option value="">Semua Status Klaim</option>
                            <option value="Submitted" {{ request('status_klaim') == 'Submitted' ? 'selected' : '' }}>Diajukan (Review P3M)</option>
                            <option value="Approved_P3M" {{ request('status_klaim') == 'Approved_P3M' ? 'selected' : '' }}>Disetujui P3M (Siap Cair)</option>
                            <option value="Disbursed" {{ request('status_klaim') == 'Disbursed' ? 'selected' : '' }}>Selesai Dicairkan</option>
                            <option value="Rejected" {{ request('status_klaim') == 'Rejected' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2.5 bg-slate-900 text-white rounded-xl font-bold text-xs hover:bg-slate-800 transition">
                            Filter
                        </button>
                        @if(request('search') || request('status_klaim'))
                            <a href="{{ route('reward.index') }}" class="px-4 py-2.5 bg-slate-100 text-slate-600 rounded-xl font-bold text-xs hover:bg-slate-200 transition">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Claim List Table -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="font-bold text-sm text-slate-800">Daftar Pengajuan Klaim Reward</h2>
                        <p class="text-xs text-slate-500">Menampilkan {{ $klaimList->total() }} pengajuan klaim insentif</p>
                    </div>
                </div>

                @if($klaimList->isEmpty())
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="font-extrabold text-slate-800 text-base mb-1">Belum Ada Pengajuan Klaim</h3>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto mb-5">Ajukan klaim reward insentif untuk publikasi jurnal bereputasi atau perolehan HKI yang telah terdaftar dan terverifikasi.</p>
                        <a href="{{ route('reward.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Ajukan Klaim Sekarang</span>
                        </a>
                    </div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach($klaimList as $item)
                            <div class="p-5 sm:p-6 hover:bg-slate-50/70 transition flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div class="space-y-2 flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="px-2.5 py-0.5 rounded-md text-[11px] font-extrabold {{ $item->jenis_klaim === 'Publikasi' ? 'bg-blue-100 text-blue-800 border border-blue-300' : 'bg-indigo-100 text-indigo-800 border border-indigo-300' }}">
                                            {{ $item->jenis_klaim }}
                                        </span>

                                        <span class="px-2.5 py-0.5 rounded-md text-[11px] font-extrabold bg-slate-100 text-slate-800 border border-slate-300">
                                            {{ $item->kategori_insentif }}
                                        </span>

                                        <!-- Status Badge -->
                                        @if($item->status_klaim === 'Submitted')
                                            <span class="px-2.5 py-0.5 rounded-md text-[11px] font-black bg-amber-100 text-amber-800 border border-amber-300 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                Menunggu Review P3M
                                            </span>
                                        @elseif($item->status_klaim === 'Approved_P3M')
                                            <span class="px-2.5 py-0.5 rounded-md text-[11px] font-black bg-blue-100 text-blue-800 border border-blue-300 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                Disetujui P3M (Proses Keuangan)
                                            </span>
                                        @elseif($item->status_klaim === 'Disbursed')
                                            <span class="px-2.5 py-0.5 rounded-md text-[11px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                Selesai Dicairkan
                                            </span>
                                        @elseif($item->status_klaim === 'Rejected')
                                            <span class="px-2.5 py-0.5 rounded-md text-[11px] font-black bg-rose-100 text-rose-800 border border-rose-300">
                                                Ditolak P3M
                                            </span>
                                        @endif
                                    </div>

                                    <h3 class="font-extrabold text-slate-900 text-sm leading-snug">
                                        <a href="{{ route('reward.show', $item) }}" class="hover:text-blue-600 transition">
                                            {{ $item->nomor_klaim }} &bull; {{ $item->publikasi?->judul_artikel ?? $item->hki?->judul_hki ?? 'Klaim Insentif' }}
                                        </a>
                                    </h3>

                                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500 font-semibold">
                                        <span>Total Insentif: <strong class="text-emerald-700 font-extrabold text-sm">Rp {{ number_format($item->total_reward, 0, ',', '.') }}</strong></span>
                                        <span>&bull;</span>
                                        <span>Penerima: <strong class="text-slate-800">{{ $item->distribusi->count() }} Penulis</strong></span>
                                        <span>&bull;</span>
                                        <span>Pemohon: <strong class="text-slate-900">{{ $item->user->name }}</strong></span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0 pt-2 md:pt-0">
                                    <a href="{{ route('reward.show', $item) }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                                        Rincian &amp; Distribusi
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


@extends('layouts.app')

@section('title', $publikasi->judul_artikel . ' - Bank Publikasi PRISMA UHN')

@section('content')
<div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <x-sidebar />

    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        <header class="bg-white border-b border-slate-200 py-4 sticky top-0 z-30 shadow-xs">
            <div class="w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <a href="{{ route('publikasi.index') }}" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center shrink-0 transition" title="Kembali" aria-label="Kembali">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </a>
                    <div>
                        <h1 class="font-extrabold text-xl text-slate-900 leading-tight">Detail Publikasi Jurnal</h1>
                        <p class="text-xs font-semibold text-slate-500">Bank Publikasi Ilmiah &bull; Repositori Naskah &amp; Status Reward Insentif</p>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-grow w-full px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-sm font-bold flex items-center gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Main Info Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-6">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-5">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-3 py-1 rounded-lg text-xs font-extrabold {{ str_contains($publikasi->kategori_peringkat, 'Scopus') ? 'bg-amber-100 text-amber-900 border border-amber-300' : 'bg-blue-100 text-blue-900 border border-blue-300' }}">
                            {{ $publikasi->kategori_peringkat }}
                        </span>
                        @if($publikasi->metadata_source === 'crossref')
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-purple-100 text-purple-800 border border-purple-200">
                                Terverifikasi Crossref API
                            </span>
                        @endif
                    </div>

                    <div>
                        @if($publikasi->is_claimed_reward)
                            <span class="px-3 py-1 rounded-full text-xs font-black bg-rose-100 text-rose-800 border border-rose-300 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Reward Insentif Terkunci (Sudah Diklaim)
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Tersedia untuk Klaim Reward
                            </span>
                        @endif
                    </div>
                </div>

                <div>
                    <h2 class="text-xl sm:text-2xl font-black text-slate-900 leading-snug">
                        {{ $publikasi->judul_artikel }}
                    </h2>
                </div>

                <!-- Detail Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50/70 p-5 rounded-2xl border border-slate-100">
                    <div class="space-y-3">
                        <div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Nama Jurnal Ilmiah</span>
                            <span class="text-sm font-extrabold text-slate-800">{{ $publikasi->nama_jurnal }}</span>
                        </div>
                        <div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">DOI (Digital Object Identifier)</span>
                            <a href="https://doi.org/{{ $publikasi->doi }}" target="_blank" class="text-xs font-bold text-blue-600 hover:underline">
                                {{ $publikasi->doi }} &rarr;
                            </a>
                        </div>
                        <div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">ISSN / e-ISSN</span>
                            <span class="text-xs font-bold text-slate-700">{{ $publikasi->issn ?: '-' }}</span>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Tahun Terbit &amp; Edisi</span>
                            <span class="text-sm font-extrabold text-slate-800">{{ $publikasi->tahun_terbit }} {{ $publikasi->volume_nomor ? "({$publikasi->volume_nomor})" : '' }}</span>
                        </div>
                        <div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Jumlah Penulis</span>
                            <span class="text-xs font-bold text-slate-700">{{ $publikasi->jumlah_penulis }} Orang</span>
                        </div>
                        <div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Dosen Pengusul / Pemilik Aset</span>
                            <span class="text-xs font-bold text-slate-900">{{ $publikasi->user->name }} ({{ $publikasi->user->nidn ?: $publikasi->user->email }})</span>
                        </div>
                    </div>
                </div>

                <!-- File Naskah & Action Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2">
                    <a href="{{ asset('storage/' . $publikasi->file_naskah) }}" target="_blank"
                       class="w-full sm:w-auto px-5 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-xs transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <span>Unduh / Buka Naskah Lengkap (PDF)</span>
                    </a>

                    @if(!$publikasi->is_claimed_reward && $publikasi->user_id === Auth::id())
                        <a href="{{ route('reward.create', ['jenis' => 'Publikasi', 'id' => $publikasi->id]) }}"
                           class="w-full sm:w-auto px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-sm transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Ajukan Klaim Reward Insentif Sekarang</span>
                        </a>
                    @elseif($publikasi->klaimReward)
                        <a href="{{ route('reward.show', $publikasi->klaimReward) }}"
                           class="w-full sm:w-auto px-5 py-3 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs border border-blue-200 transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Lihat Klaim: {{ $publikasi->klaimReward->nomor_klaim }}</span>
                        </a>
                    @endif
                </div>
            </div>
        </main>
    </div>
</div>
@endsection


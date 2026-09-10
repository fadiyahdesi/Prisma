@extends('layouts.app')

@section('title', 'Bank Publikasi Jurnal Kampus - PRISMA UHN')

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
                        <h1 class="font-extrabold text-xl text-slate-900 leading-tight">Bank Publikasi Jurnal Kampus</h1>
                        <p class="text-xs font-semibold text-slate-500">Penyimpanan Naskah Terbit, Validasi Keunikan DOI &amp; Integrasi Metadata Crossref / SINTA (US-11.1)</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('publikasi.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-xs transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Daftarkan Artikel</span>
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

            @if(session('error'))
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-300 text-rose-900 text-sm font-bold flex items-center gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Search & Filters -->
            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs">
                <form method="GET" action="{{ route('publikasi.index') }}" class="flex flex-col md:flex-row gap-3">
                    <div class="flex-1 relative">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul artikel, nama jurnal, DOI, atau penulis..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-slate-50/50">
                    </div>
                    <div class="w-full md:w-56">
                        <select name="kategori_peringkat" onchange="this.form.submit()" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-blue-500 bg-white">
                            <option value="">Semua Peringkat Jurnal</option>
                            <option value="Scopus Q1" {{ request('kategori_peringkat') == 'Scopus Q1' ? 'selected' : '' }}>Scopus Q1</option>
                            <option value="Scopus Q2" {{ request('kategori_peringkat') == 'Scopus Q2' ? 'selected' : '' }}>Scopus Q2</option>
                            <option value="Scopus Q3" {{ request('kategori_peringkat') == 'Scopus Q3' ? 'selected' : '' }}>Scopus Q3</option>
                            <option value="Scopus Q4" {{ request('kategori_peringkat') == 'Scopus Q4' ? 'selected' : '' }}>Scopus Q4</option>
                            <option value="SINTA 1" {{ request('kategori_peringkat') == 'SINTA 1' ? 'selected' : '' }}>SINTA 1</option>
                            <option value="SINTA 2" {{ request('kategori_peringkat') == 'SINTA 2' ? 'selected' : '' }}>SINTA 2</option>
                            <option value="SINTA 3" {{ request('kategori_peringkat') == 'SINTA 3' ? 'selected' : '' }}>SINTA 3</option>
                            <option value="SINTA 4" {{ request('kategori_peringkat') == 'SINTA 4' ? 'selected' : '' }}>SINTA 4</option>
                            <option value="Internasional Terindeks Lainnya" {{ request('kategori_peringkat') == 'Internasional Terindeks Lainnya' ? 'selected' : '' }}>Internasional Lainnya</option>
                            <option value="Nasional Terakreditasi" {{ request('kategori_peringkat') == 'Nasional Terakreditasi' ? 'selected' : '' }}>Nasional Terakreditasi</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2.5 bg-slate-900 text-white rounded-xl font-bold text-xs hover:bg-slate-800 transition">
                            Filter
                        </button>
                        @if(request('search') || request('kategori_peringkat'))
                            <a href="{{ route('publikasi.index') }}" class="px-4 py-2.5 bg-slate-100 text-slate-600 rounded-xl font-bold text-xs hover:bg-slate-200 transition">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Publications List -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="font-bold text-sm text-slate-800">Daftar Publikasi Artikel Ilmiah</h2>
                        <p class="text-xs text-slate-500">Menampilkan {{ $publikasiList->total() }} artikel terdaftar</p>
                    </div>
                </div>

                @if($publikasiList->isEmpty())
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <h3 class="font-extrabold text-slate-800 text-base mb-1">Belum Ada Publikasi Terdaftar</h3>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto mb-5">Daftarkan artikel publikasi jurnal Anda dengan memasukkan DOI untuk penarikan metadata otomatis dan pengajuan klaim insentif reward.</p>
                        <a href="{{ route('publikasi.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-xs transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Daftarkan Artikel Pertama</span>
                        </a>
                    </div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach($publikasiList as $item)
                            <div class="p-5 sm:p-6 hover:bg-slate-50/70 transition flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div class="space-y-2 flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <!-- Category badge -->
                                        <span class="px-2.5 py-0.5 rounded-md text-[11px] font-extrabold {{ str_contains($item->kategori_peringkat, 'Scopus') ? 'bg-amber-100 text-amber-800 border border-amber-300' : 'bg-blue-100 text-blue-800 border border-blue-300' }}">
                                            {{ $item->kategori_peringkat }}
                                        </span>

                                        <!-- Metadata source -->
                                        @if($item->metadata_source === 'crossref')
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-purple-100 text-purple-700 border border-purple-200">
                                                Crossref API
                                            </span>
                                        @endif

                                        <!-- Claim Status -->
                                        @if($item->is_claimed_reward)
                                            <span class="px-2.5 py-0.5 rounded-md text-[11px] font-black bg-rose-100 text-rose-800 border border-rose-300 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                Sudah Diklaim Reward
                                            </span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-md text-[11px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                Tersedia untuk Klaim
                                            </span>
                                        @endif
                                    </div>

                                    <h3 class="font-extrabold text-slate-900 text-sm leading-snug">
                                        <a href="{{ route('publikasi.show', $item) }}" class="hover:text-blue-600 transition">
                                            {{ $item->judul_artikel }}
                                        </a>
                                    </h3>

                                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500 font-semibold">
                                        <span class="text-slate-700 font-bold">{{ $item->nama_jurnal }}</span>
                                        <span>&bull;</span>
                                        <span>Tahun: <strong class="text-slate-800">{{ $item->tahun_terbit }}</strong></span>
                                        @if($item->volume_nomor)
                                            <span>&bull;</span>
                                            <span>{{ $item->volume_nomor }}</span>
                                        @endif
                                        <span>&bull;</span>
                                        <span>Penulis: <strong class="text-slate-800">{{ $item->jumlah_penulis }} Orang</strong></span>
                                        @if($isStaff)
                                            <span>&bull;</span>
                                            <span>Pengusul: <strong class="text-slate-900">{{ $item->user->name }}</strong></span>
                                        @endif
                                    </div>

                                    <div class="text-xs text-slate-400 flex items-center gap-2">
                                        <span>DOI:</span>
                                        <a href="https://doi.org/{{ $item->doi }}" target="_blank" class="text-blue-600 hover:underline font-bold">
                                            {{ $item->doi }}
                                        </a>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0 pt-2 md:pt-0">
                                    <a href="{{ route('publikasi.show', $item) }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                                        Detail
                                    </a>
                                    <a href="{{ asset('storage/' . $item->file_naskah) }}" target="_blank" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        <span>PDF</span>
                                    </a>
                                    @if(!$item->is_claimed_reward && $item->user_id === Auth::id())
                                        <a href="{{ route('reward.create', ['jenis' => 'Publikasi', 'id' => $item->id]) }}" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition shadow-xs flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>Klaim Reward</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $publikasiList->links() }}
                    </div>
                @endif
            </div>
        </main>
    </div>
</div>
@endsection


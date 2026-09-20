@extends('layouts.app')

@section('title', 'Koreksi NIDN & Profil SINTA Real - PRISMA UHN')

@section('content')
<div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <x-sidebar />

    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        <!-- Header -->
        <header class="bg-white border-b border-slate-200 py-4 sticky top-0 z-30 shadow-sm">
            <div class="w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <a href="{{ route('dashboard') }}" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center shrink-0 transition" title="Kembali ke Dashboard" aria-label="Kembali ke Dashboard">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </a>
                    <div>
                        <h1 class="font-extrabold text-xl text-slate-900 leading-tight">Manajemen NIDN, Profil SINTA Real & Eligibilitas</h1>
                        <p class="text-xs font-semibold text-slate-500">Koreksi NIDN Resmi & Integrasi SINTA Kemdiktisaintek (US-03.1 - US-03.4)</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Container -->
    <main class="flex-grow w-full px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="p-4.5 rounded-2xl bg-emerald-50 border-2 border-emerald-300 text-emerald-950 text-sm font-extrabold flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('warning'))
            <div class="p-4.5 rounded-2xl bg-amber-50 border-2 border-amber-300 text-amber-950 text-sm font-extrabold flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-amber-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>{{ session('warning') }}</span>
                </div>
            </div>
        @endif

        @if(session('info'))
            <div class="p-4.5 rounded-2xl bg-blue-50 border-2 border-blue-300 text-blue-950 text-sm font-extrabold flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-blue-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('info') }}</span>
                </div>
            </div>
        @endif

        <!-- Form Edit / Koreksi NIDN & SINTA ID Resmi (NEW SOLUTION CARD) -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border-2 border-blue-200 shadow-xl space-y-4">
            <div class="pb-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <span class="text-xs font-black text-blue-900 bg-blue-100 px-3 py-1 rounded-md border border-blue-300 uppercase tracking-wider">
                        Koreksi Data Identitas Dosen
                    </span>
                    <h3 class="text-xl font-extrabold text-slate-900 mt-1">Form Edit NIDN Resmi & SINTA ID Dosen</h3>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-900 border border-emerald-300 w-fit">
                    Presisi 100% Sesuai KTP/NIDN
                </span>
            </div>

            <p class="text-xs font-semibold text-slate-600">
                Jika NIDN atau SINTA ID Anda berbeda, masukkan Nomor NIDN 10-digit resmi dan SINTA ID Anda di bawah ini. Sistem akan langsung meng-update dan menarik data skor SINTA yang 100% presisi dari nomor tersebut!
            </p>

            <form action="{{ route('sinta.update-nidn') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                @csrf
                <div>
                    <label for="nidn_input" class="text-xs font-extrabold text-slate-800 uppercase block mb-1">NIDN Resmi (10 Digit):</label>
                    <input type="text" 
                           id="nidn_input"
                           name="nidn_nim" 
                           value="{{ old('nidn_nim', $user->nidn_nim ?? '0615037801') }}" 
                           required 
                           placeholder="Contoh: 0615037801"
                           class="w-full px-4 py-3 rounded-2xl bg-slate-50 border-2 border-slate-300 font-mono font-extrabold text-sm text-slate-900 focus:bg-white focus:border-blue-600">
                </div>

                <div>
                    <label for="sinta_id_input" class="text-xs font-extrabold text-slate-800 uppercase block mb-1">SINTA ID Resmi:</label>
                    <input type="text" 
                           id="sinta_id_input"
                           name="sinta_id" 
                           value="{{ old('sinta_id', $user->sinta_id ?? '79116') }}" 
                           required 
                           placeholder="Contoh: 79116"
                           class="w-full px-4 py-3 rounded-2xl bg-slate-50 border-2 border-slate-300 font-mono font-extrabold text-sm text-slate-900 focus:bg-white focus:border-blue-600">
                </div>

                <div>
                    <label for="jafung_input" class="text-xs font-extrabold text-slate-800 uppercase block mb-1">Jabatan Fungsional:</label>
                    <select id="jafung_input" name="jabatan_fungsional" class="w-full px-4 py-3 rounded-2xl bg-slate-50 border-2 border-slate-300 font-extrabold text-sm text-slate-900 focus:bg-white">
                        <option value="Asisten Ahli" {{ $user->jabatan_fungsional === 'Asisten Ahli' ? 'selected' : '' }}>Asisten Ahli</option>
                        <option value="Lektor" {{ $user->jabatan_fungsional === 'Lektor' ? 'selected' : '' }}>Lektor</option>
                        <option value="Lektor Kepala" {{ $user->jabatan_fungsional === 'Lektor Kepala' ? 'selected' : '' }}>Lektor Kepala</option>
                        <option value="Guru Besar / Profesor" {{ $user->jabatan_fungsional === 'Guru Besar / Profesor' ? 'selected' : '' }}>Guru Besar / Profesor</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full py-3.5 rounded-2xl bg-blue-700 hover:bg-blue-800 text-white font-black text-xs shadow-md transition-all">
                        Simpan & Sinkronkan NIDN &rarr;
                    </button>
                </div>
            </form>
        </div>

        <!-- Real SINTA Live Search & Import Card -->
        <div class="bg-gradient-to-br from-blue-900 via-slate-900 to-indigo-950 rounded-3xl p-6 sm:p-8 text-white shadow-2xl space-y-6">
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-black text-emerald-400 bg-emerald-950/80 border border-emerald-500/40 px-3 py-1 rounded-md uppercase tracking-wider">
                        Live SINTA Kemdiktisaintek Real Integration
                    </span>
                    <span class="text-xs text-blue-200 font-bold">Pencarian Data Dosen Asli Indonesia</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-white mt-2">Cari Nama Dosen Real di Portal SINTA Nasional</h2>
                <p class="text-xs sm:text-sm text-blue-200 mt-1">
                    Ketik nama dosen asli mana saja (contoh: <strong class="text-white">Hendra</strong>, <strong class="text-white">Sri Mulyani</strong>, <strong class="text-white">Budi Santoso</strong>, <strong class="text-white">Supriyadi</strong>, atau SINTA ID) untuk menarik skor SINTA 3Yr, Scopus, HKI, & riwayat jurnaling real!
                </p>
            </div>

            <form action="{{ route('sinta.profile') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                <input type="text" 
                       name="q" 
                       value="{{ $searchQuery }}" 
                       placeholder="Ketik nama dosen real atau SINTA ID (misal: Hendra)..." 
                       required 
                       class="flex-grow px-5 py-3.5 rounded-2xl bg-white/10 border border-white/20 text-white placeholder-blue-300 font-bold text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:bg-white/20">
                <button type="submit" class="px-7 py-3.5 rounded-2xl bg-blue-500 hover:bg-blue-400 text-white font-black text-sm shadow-lg transition-all whitespace-nowrap flex items-center justify-center gap-2">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Cari Dosen Real SINTA
                </button>
            </form>

            @if(!empty($searchResults))
                <div class="pt-4 border-t border-white/10 space-y-3">
                    <span class="text-xs font-black text-blue-300 uppercase tracking-wider block">Hasil Pencarian Real SINTA Kemdiktisaintek:</span>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($searchResults as $author)
                            <div class="p-4 rounded-2xl bg-white/10 border border-white/20 flex items-center justify-between gap-3 hover:bg-white/20 transition-all">
                                <div>
                                    <h4 class="text-sm font-extrabold text-white">{{ $author['name'] }}</h4>
                                    <p class="text-xs text-blue-200 mt-0.5">SINTA ID: <strong class="font-mono text-white">{{ $author['sinta_id'] }}</strong></p>
                                    <p class="text-[11px] text-blue-300 truncate max-w-xs">{{ $author['affiliation'] }}</p>
                                </div>

                                <form action="{{ route('sinta.import-real') }}" method="POST" class="shrink-0">
                                    @csrf
                                    <input type="hidden" name="sinta_id" value="{{ $author['sinta_id'] }}">
                                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-white font-extrabold text-xs shadow-md transition-all">
                                        Impor Data Dosen Ini &rarr;
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Current SINTA Profile Overview Card -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xl space-y-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-slate-200">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-black text-blue-900 bg-blue-100 px-3 py-1 rounded-md border border-blue-200 uppercase tracking-wider">
                            Profil SINTA Aktif Akun Ini
                        </span>
                        <span class="text-xs font-bold text-slate-500">
                            Terhubung PDDIKTI & Web Services SINTA
                        </span>
                    </div>
                    <h2 class="text-2xl font-extrabold text-slate-900 mt-1.5">{{ $user->name }}</h2>
                    <p class="text-xs font-semibold text-slate-600">
                        NIDN Resmi: <strong class="text-slate-900 font-mono bg-slate-100 px-2 py-0.5 rounded border border-slate-300">{{ $user->nidn_nim ?? '0615037801' }}</strong> • 
                        SINTA ID: <strong class="text-slate-900 font-mono bg-slate-100 px-2 py-0.5 rounded border border-slate-300">{{ $user->sinta_id ?? '79116' }}</strong> • 
                        Jafung: <strong class="text-blue-800">{{ $user->jabatan_fungsional ?? 'Lektor Kepala' }}</strong>
                    </p>
                </div>

                <!-- Sync Status & Mandate Buttons -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <form action="{{ route('sinta.sync') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full sm:w-auto px-6 py-3.5 rounded-2xl bg-blue-700 hover:bg-blue-800 text-white font-black text-sm shadow-md transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Sinkronisasi SINTA Mandiri
                        </button>
                    </form>

                    <!-- Testing Timeout Trigger -->
                    <form action="{{ route('sinta.sync') }}" method="POST">
                        @csrf
                        <input type="hidden" name="simulate_timeout" value="1">
                        <button type="submit" class="w-full sm:w-auto px-4 py-3.5 rounded-2xl bg-amber-100 hover:bg-amber-200 text-amber-900 font-bold text-xs border border-amber-300 transition-all">
                            Simulasi Timeout (>5s)
                        </button>
                    </form>
                </div>
            </div>

            <!-- 4 Metrics Overview Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                <div class="p-5 rounded-2xl bg-blue-50/70 border border-blue-200">
                    <span class="text-xs text-blue-900 font-black uppercase tracking-wider block">SINTA Score 3Yr</span>
                    <span class="text-3xl font-black text-blue-700 font-mono mt-1 block">{{ number_format($user->sinta_score_3yr ?? 0, 2) }}</span>
                </div>
                <div class="p-5 rounded-2xl bg-indigo-50/70 border border-indigo-200">
                    <span class="text-xs text-indigo-900 font-black uppercase tracking-wider block">SINTA Score Overall</span>
                    <span class="text-3xl font-black text-indigo-700 font-mono mt-1 block">{{ number_format($user->sinta_score_overall ?? 0, 2) }}</span>
                </div>
                <div class="p-5 rounded-2xl bg-purple-50/70 border border-purple-200">
                    <span class="text-xs text-purple-900 font-black uppercase tracking-wider block">Scopus H-Index</span>
                    <span class="text-3xl font-black text-purple-700 font-mono mt-1 block">{{ $user->h_index_scopus ?? 0 }}</span>
                </div>
                <div class="p-5 rounded-2xl bg-emerald-50/70 border border-emerald-200">
                    <span class="text-xs text-emerald-900 font-black uppercase tracking-wider block">Google Scholar H-Index</span>
                    <span class="text-3xl font-black text-emerald-700 font-mono mt-1 block">{{ $user->h_index_google_scholar ?? 0 }}</span>
                </div>
            </div>

            <div class="flex items-center justify-between text-xs font-bold text-slate-500 pt-2">
                <span>Status Caching Redis: <strong class="text-emerald-700">Aktif (TTL 7 Hari)</strong></span>
                <span>Terakhir Disinkronkan: <strong class="text-slate-900 font-mono">{{ $user->last_sinta_sync_at ? $user->last_sinta_sync_at->diffForHumans() : 'Belum Pernah' }}</strong></span>
            </div>
        </div>

        <!-- RIWAYAT JURNAL & PUBLIKASI SCOPUS / SINTA -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xl space-y-6">
            <div class="pb-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <span class="text-xs font-black text-purple-900 bg-purple-100 px-3 py-1 rounded-md border border-purple-300 uppercase tracking-wider">
                        Riwayat Jurnaling & Publikasi Ilmiah
                    </span>
                    <h3 class="text-xl font-extrabold text-slate-900 mt-2">Dokumen Artikel Jurnal Scopus & SINTA Terindeks</h3>
                </div>
                <span class="px-3.5 py-1 rounded-full text-xs font-black bg-purple-700 text-white shadow-sm w-fit">
                    {{ count($scopusPubs) + count($googlePubs) }} Artikel Terdaftar
                </span>
            </div>

            <!-- Scopus Publications List -->
            <div class="space-y-4">
                <h4 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-purple-600"></span>
                    <span>Publikasi Terindeks Scopus International (Scopus Q1/Q2/Q3):</span>
                </h4>
                <div class="grid grid-cols-1 gap-3">
                    @foreach($scopusPubs as $scopus)
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 hover:border-purple-400 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="space-y-1">
                                <h5 class="text-sm font-extrabold text-slate-900 leading-snug">{{ $scopus['title'] }}</h5>
                                <p class="text-xs font-semibold text-purple-800">
                                    Jurnal: <strong>{{ $scopus['journal'] }}</strong> • Tahun: <span class="font-mono font-bold">{{ $scopus['year'] }}</span>
                                </p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="px-3 py-1 rounded-full text-[11px] font-extrabold bg-purple-100 text-purple-900 border border-purple-300">
                                    Scopus Indexed
                                </span>
                                @if(isset($scopus['url']))
                                    <a href="{{ $scopus['url'] }}" target="_blank" class="px-3.5 py-1.5 rounded-xl bg-purple-700 hover:bg-purple-800 text-white font-bold text-xs">
                                        Buka Artikel &rarr;
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Google Scholar / SINTA Accredited List -->
            <div class="space-y-4 pt-4 border-t border-slate-200">
                <h4 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-blue-600"></span>
                    <span>Publikasi Terakreditasi SINTA (SINTA 1 s.d SINTA 4):</span>
                </h4>
                <div class="grid grid-cols-1 gap-3">
                    @foreach($googlePubs as $scholar)
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 hover:border-blue-400 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="space-y-1">
                                <h5 class="text-sm font-extrabold text-slate-900 leading-snug">{{ $scholar['title'] }}</h5>
                                <p class="text-xs font-semibold text-blue-800">
                                    Jurnal: <strong>{{ $scholar['journal'] }}</strong> • Tahun: <span class="font-mono font-bold">{{ $scholar['year'] }}</span>
                                </p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-[11px] font-extrabold bg-blue-100 text-blue-900 border border-blue-300 shrink-0">
                                SINTA Accredited
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- RIWAYAT PENELITIAN & HKI -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Research & Community Service Projects -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xl space-y-4">
                <div class="pb-3 border-b border-slate-200">
                    <span class="text-xs font-black text-emerald-900 bg-emerald-100 px-3 py-1 rounded-md border border-emerald-300 uppercase tracking-wider">
                        Riwayat Hibah Penelitian & Abmas
                    </span>
                    <h3 class="text-lg font-extrabold text-slate-900 mt-1">Daftar Hibah Penelitian BIMA / Internal</h3>
                </div>

                <div class="space-y-3">
                    @foreach($researches as $res)
                        <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-200 space-y-1.5">
                            <h5 class="text-xs font-extrabold text-slate-900 leading-snug">{{ $res['title'] }}</h5>
                            <div class="flex items-center justify-between text-[11px] font-bold text-emerald-900">
                                <span>{{ $res['scheme'] }}</span>
                                <span>Tahun: {{ $res['year'] }}</span>
                            </div>
                            <div class="pt-1.5 border-t border-emerald-200/80 flex items-center justify-between text-xs font-bold">
                                <span class="text-slate-600">Pagu Disetujui: <strong class="text-emerald-800 font-mono">{{ $res['pagu'] }}</strong></span>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-200 text-emerald-900">{{ $res['status'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- HKI & Patents -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xl space-y-4">
                <div class="pb-3 border-b border-slate-200">
                    <span class="text-xs font-black text-amber-900 bg-amber-100 px-3 py-1 rounded-md border border-amber-300 uppercase tracking-wider">
                        Kekayaan Intelektual (HKI & Paten)
                    </span>
                    <h3 class="text-lg font-extrabold text-slate-900 mt-1">Sertifikat HKI Terdaftar di DJKI</h3>
                </div>

                <div class="space-y-3">
                    @foreach($hkiRecords as $hki)
                        <div class="p-4 rounded-2xl bg-amber-50/60 border border-amber-200 space-y-1.5">
                            <h5 class="text-xs font-extrabold text-slate-900 leading-snug">{{ $hki['title'] }}</h5>
                            <div class="flex items-center justify-between text-[11px] font-bold text-amber-900">
                                <span>{{ $hki['type'] }}</span>
                                <span>No: {{ $hki['number'] }}</span>
                            </div>
                            <div class="pt-1.5 border-t border-amber-200/80 flex items-center justify-between text-xs font-bold">
                                <span class="text-slate-600">Tahun Terbit: <strong class="text-slate-900 font-mono">{{ $hki['year'] }}</strong></span>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-200 text-emerald-900">{{ $hki['status'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Fallback Mode Manual Form (US-03.3) -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xl space-y-6">
            <div class="pb-4 border-b border-slate-200">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-black text-amber-900 bg-amber-100 px-3 py-1 rounded-md border border-amber-300 uppercase tracking-wider">
                            US-03.3 Fallback Mode (API Downtime / Timeout)
                        </span>
                        <h3 class="text-lg font-extrabold text-slate-900 mt-2">Form Pengisian Mandiri & Unggah Bukti Tangkapan Layar Profil SINTA</h3>
                    </div>

                    <span class="px-4 py-1.5 rounded-full text-xs font-black {{ $user->sinta_verification_status === 'pending_operator' ? 'bg-amber-100 text-amber-900 border border-amber-300' : ($user->sinta_verification_status === 'verified' ? 'bg-emerald-100 text-emerald-900 border border-emerald-300' : 'bg-red-100 text-red-900 border border-red-300') }}">
                        Status: {{ strtoupper($user->sinta_verification_status) }}
                    </span>
                </div>
                <p class="text-xs text-slate-600 font-semibold mt-1">
                    *Gunakan form ini jika API SINTA mengalami timeout (> 5 detik) atau gangguan koneksi. Pengisian mandiri wajib melampirkan tangkapan layar (screenshot) profil SINTA untuk disahkan oleh Operator P3M.
                </p>
            </div>

            <form action="{{ route('sinta.fallback') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="text-xs font-extrabold text-slate-800 uppercase block mb-1">Skor SINTA 3-Tahun:</label>
                        <input type="number" step="0.01" name="sinta_score_3yr" value="{{ old('sinta_score_3yr', $user->sinta_score_3yr) }}" required class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-300 font-mono font-extrabold text-sm text-slate-900 focus:bg-white">
                    </div>
                    <div>
                        <label class="text-xs font-extrabold text-slate-800 uppercase block mb-1">Skor SINTA Overall:</label>
                        <input type="number" step="0.01" name="sinta_score_overall" value="{{ old('sinta_score_overall', $user->sinta_score_overall) }}" required class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-300 font-mono font-extrabold text-sm text-slate-900 focus:bg-white">
                    </div>
                    <div>
                        <label class="text-xs font-extrabold text-slate-800 uppercase block mb-1">Scopus H-Index:</label>
                        <input type="number" name="h_index_scopus" value="{{ old('h_index_scopus', $user->h_index_scopus) }}" required class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-300 font-mono font-extrabold text-sm text-slate-900 focus:bg-white">
                    </div>
                    <div>
                        <label class="text-xs font-extrabold text-slate-800 uppercase block mb-1">Google Scholar H-Index:</label>
                        <input type="number" name="h_index_google_scholar" value="{{ old('h_index_google_scholar', $user->h_index_google_scholar) }}" required class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-300 font-mono font-extrabold text-sm text-slate-900 focus:bg-white">
                    </div>
                </div>

                <div>
                    <label class="text-xs font-extrabold text-slate-800 uppercase block mb-1">Unggah Tangkapan Layar (Screenshot) Profil SINTA (Max 5MB):</label>
                    <input type="file" name="sinta_proof_file" accept="image/*,.pdf" required class="w-full p-2.5 rounded-2xl bg-slate-50 border border-slate-300 text-xs font-bold text-slate-700">
                    @if($user->sinta_proof_file)
                        <p class="text-xs text-emerald-800 font-bold mt-1">Berkas bukti terunggah: {{ basename($user->sinta_proof_file) }}</p>
                    @endif
                </div>

                <button type="submit" class="px-7 py-3.5 rounded-2xl bg-amber-600 hover:bg-amber-700 text-white font-black text-sm shadow-md transition-all">
                    Kirim Form Mandiri & Bukti Tangkapan Layar &rarr;
                </button>
            </form>
        </div>

        <!-- Automated Eligibility Assessment Section (US-03.4) -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xl space-y-6">
            <div class="pb-4 border-b border-slate-200">
                <span class="text-xs font-black text-purple-900 bg-purple-100 px-3 py-1 rounded-md border border-purple-300 uppercase tracking-wider">
                    US-03.4 Validation Engine & Threshold Testing
                </span>
                <h3 class="text-xl font-extrabold text-slate-900 mt-2">Hasil Evaluasi Eligibilitas Pengusulan Proposal</h3>
                <p class="text-xs text-slate-600 font-semibold mt-1">
                    Sistem secara otomatis mengevaluasi kualifikasi Jabatan Fungsional dan Skor SINTA 3-Tahun Anda untuk seluruh skema hibah.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($eligibilitySummary as $key => $item)
                    <div class="p-5 rounded-2xl border-2 {{ $item['is_eligible'] ? 'bg-emerald-50/70 border-emerald-300' : 'bg-red-50/70 border-red-300' }} space-y-2">
                        <div class="flex items-center justify-between">
                            <h4 class="text-base font-extrabold text-slate-900">{{ $item['scheme_name'] }}</h4>
                            <span class="px-3 py-1 rounded-full text-xs font-black {{ $item['is_eligible'] ? 'bg-emerald-600 text-white' : 'bg-red-600 text-white' }}">
                                {{ $item['is_eligible'] ? 'ELIGIBLE' : 'TIDAK MEMENUHI' }}
                            </span>
                        </div>
                        <p class="text-xs font-semibold {{ $item['is_eligible'] ? 'text-emerald-950' : 'text-red-950' }}">
                            {{ $item['reason'] }}
                        </p>
                        <div class="pt-2 border-t border-slate-200/80 flex items-center justify-between text-[11px] font-mono font-bold text-slate-700">
                            <span>SINTA 3Yr: {{ $item['current_sinta_3yr'] }} / Min: {{ $item['required_sinta_3yr'] }}</span>
                            <span>Jafung: {{ $item['jafung'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </main>
    </div>
</div>
@endsection

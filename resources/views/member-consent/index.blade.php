@extends('layouts.app')

@section('title', 'Persetujuan Keanggotaan Tim - PRISMA UHN')

@section('content')
<div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <!-- Role-Based Interactive Sidebar -->
    <x-sidebar />

    <!-- Main Content Area -->
    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        <!-- Top Sticky Header -->
        <header class="bg-white border-b border-slate-200 py-4 sticky top-0 z-30 shadow-xs">
            <div class="w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-3 min-w-0">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 shrink-0" title="Buka Navigasi">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <a href="{{ route('dashboard') }}" aria-label="Kembali ke dashboard" title="Kembali ke dashboard" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center shrink-0 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </a>
                    <div class="min-w-0">
                        <h1 class="font-extrabold text-lg sm:text-xl text-slate-900 leading-tight truncate">Persetujuan Keanggotaan Tim</h1>
                        <p class="text-xs font-semibold text-slate-500 truncate">Konfirmasi Member Consent Usulan Proposal Riset &amp; Pengabdian BIMA (EPIC 06)</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition border border-slate-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span class="hidden sm:inline">Dasbor Utama</span>
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Body (Full Width Container) -->
        <main class="flex-grow w-full px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            <!-- Informational Banner -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center shrink-0 shadow-xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-lg sm:text-xl font-extrabold text-slate-900">Verifikasi Keterlibatan Anggota Peneliti (Member Consent)</h2>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1 max-w-3xl leading-relaxed">
                            Sesuai regulasi standar BIMA Kemendikbudristek, setiap calon anggota dosen maupun mahasiswa wajib memberikan konfirmasi kesediaan secara mandiri sebelum berkas usulan dapat diajukan ke tahap seleksi administrasi dan substantif.
                        </p>
                    </div>
                </div>
                <div class="text-right shrink-0">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-amber-100 text-amber-900 border border-amber-300">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                        <span>{{ $pendingMembers->count() }} Undangan Menunggu</span>
                    </span>
                </div>
            </div>

            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs sm:text-sm font-bold flex items-center gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-300 text-rose-900 text-xs sm:text-sm font-bold flex items-center gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Invitation Cards Grid -->
            <div class="space-y-5">
                @forelse($pendingMembers as $member)
                    <article class="bg-white rounded-3xl border border-slate-200 shadow-md hover:shadow-lg transition-all p-6 space-y-6">
                        <!-- Card Header -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-3 py-1 rounded-lg text-xs font-black bg-blue-100 text-blue-900 border border-blue-300">
                                    {{ $member->usulan->skema->kode_skema ?? 'BIMA' }}
                                </span>
                                <span class="text-xs font-bold text-slate-500">
                                    {{ $member->usulan->skema->nama_skema ?? 'Skema Penelitian / Pengabdian' }}
                                </span>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold bg-amber-50 text-amber-800 border border-amber-300">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                <span>Menunggu Persetujuan Anda</span>
                            </span>
                        </div>

                        <!-- Card Content Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                            <!-- Judul & Ringkasan -->
                            <div class="md:col-span-8 space-y-2">
                                <span class="text-[11px] font-black uppercase tracking-wider text-slate-400">Judul Usulan Proposal</span>
                                <h3 class="text-base sm:text-lg font-extrabold text-slate-900 leading-snug">
                                    {{ $member->usulan->judul_usulan }}
                                </h3>
                                @if($member->usulan->ringkasan)
                                    <p class="text-xs text-slate-600 line-clamp-3 leading-relaxed mt-2">
                                        {{ $member->usulan->ringkasan }}
                                    </p>
                                @endif
                            </div>

                            <!-- Detail Ketua & Peran -->
                            <div class="md:col-span-4 bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-3">
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Ketua Pengusul:</span>
                                    <p class="font-extrabold text-xs sm:text-sm text-slate-900 mt-0.5">
                                        {{ $member->usulan->pengusul->name ?? 'Dosen Pengusul' }}
                                    </p>
                                    <p class="text-[11px] text-slate-500 font-semibold">
                                        NIDN: {{ $member->usulan->pengusul->nidn_nim ?? '-' }}
                                    </p>
                                </div>

                                <div class="pt-2 border-t border-slate-200">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Peran yang Ditugaskan:</span>
                                    <span class="inline-block mt-1 px-2.5 py-1 rounded-lg text-xs font-black bg-emerald-100 text-emerald-900 border border-emerald-300">
                                        {{ $member->peran_anggota ?? 'Anggota Peneliti' }}
                                    </span>
                                </div>

                                @if($member->tugas_dalam_usulan)
                                <div class="pt-2 border-t border-slate-200">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Uraian Tugas:</span>
                                    <p class="text-xs text-slate-700 font-semibold mt-0.5">{{ $member->tugas_dalam_usulan }}</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3 pt-4 border-t border-slate-100">
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-extrabold border border-slate-200 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                <span>Kembali ke Dasbor</span>
                            </a>

                            <div class="flex flex-col sm:flex-row items-center gap-3">
                                <form method="POST" action="{{ route('member-consent.respond', $member) }}" onsubmit="return confirm('Apakah Anda yakin ingin MENOLAK undangan keikutsertaan dalam proposal ini?');">
                                    @csrf
                                    <input type="hidden" name="decision" value="rejected">
                                    <button type="submit" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-800 hover:text-rose-900 text-xs font-extrabold border border-rose-300 transition-colors flex items-center justify-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        <span>Tolak Undangan</span>
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('member-consent.respond', $member) }}" onsubmit="return confirm('Apakah Anda menyatakan BERSEDIA menjadi anggota tim dalam usulan ini?');">
                                    @csrf
                                    <input type="hidden" name="decision" value="approved">
                                    <button type="submit" class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-extrabold shadow-md transition-colors flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        <span>Setujui Keanggotaan</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-12 text-center space-y-4">
                        <div class="w-16 h-16 rounded-3xl bg-emerald-100 text-emerald-700 mx-auto flex items-center justify-center shadow-xs">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="space-y-1">
                            <h3 class="text-lg font-extrabold text-slate-900">Tidak Ada Undangan yang Menunggu Persetujuan</h3>
                            <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto">
                                Semua undangan keanggotaan tim riset atau pengabdian masyarakat Anda telah ditanggapi, atau belum ada ketua pengusul yang menambahkan Anda sebagai anggota.
                            </p>
                        </div>
                        <div class="pt-4 flex flex-wrap justify-center gap-3">
                            <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-xl bg-blue-700 hover:bg-blue-800 text-white text-xs font-extrabold transition shadow-sm">
                                Kembali ke Dasbor Utama
                            </a>
                            <a href="{{ route('hki.create') }}" class="px-5 py-2.5 rounded-xl bg-indigo-50 border border-indigo-200 hover:bg-indigo-100 text-indigo-900 text-xs font-extrabold transition">
                                Pengajuan KI / Paten / HKI &rarr;
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
        </main>
    </div>
</div>
@endsection

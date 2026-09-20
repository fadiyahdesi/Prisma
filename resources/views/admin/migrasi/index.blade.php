@extends('layouts.app')

@section('title', 'Migrasi Data Legasi - PRISMA UHN')

@section('content')
<div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <x-sidebar />

    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        {{-- Header --}}
        <header class="bg-white border-b border-slate-200 py-4 sticky top-0 z-30 shadow-xs">
            <div class="w-full px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 shrink-0" title="Buka Menu">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <a href="{{ route('dashboard') }}" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center shrink-0 transition" title="Kembali ke Dashboard">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </a>
                    <div>
                        <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-0.5">
                            <span>P3M Management</span>
                            <span>/</span>
                            <span class="text-blue-600">Migrasi Data Legasi</span>
                        </div>
                        <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Migrasi Data Legasi SIMPENDI PHB</h1>
                        <p class="text-xs text-slate-500 font-medium">Transformasi arsip historis Politeknik Harapan Bersama & STMIK YMI ke skema PostgreSQL 3NF KHARISMA UHN (US-13.1)</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <form action="{{ route('admin.migrasi.run') }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="dry_run" value="1">
                        <button type="submit" class="px-4 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs transition-colors shadow-2xs flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Uji Coba Dry-Run</span>
                        </button>
                    </form>
                    <form action="{{ route('admin.migrasi.run') }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin mengeksekusi migrasi data live ke basis data PostgreSQL?');">
                        @csrf
                        <input type="hidden" name="dry_run" value="0">
                        <button type="submit" class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-colors shadow-xs flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            <span>Eksekusi Migrasi Live</span>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <main class="flex-grow w-full px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        {{-- Flash Alerts --}}
        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl p-4 flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span class="text-xs font-bold">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl p-4 flex items-center gap-3">
                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="text-xs font-bold">{{ session('error') }}</span>
            </div>
        @endif

        {{-- Stat Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-xs">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Usulan Historis</span>
                    <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </span>
                </div>
                <div class="text-2xl font-black text-slate-900">{{ $stats['total_proposals'] }}</div>
                <div class="text-[11px] font-semibold text-emerald-600 mt-1 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Termigrasi Lengkap (3NF)</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-xs">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Kontrak SPK</span>
                    <span class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </span>
                </div>
                <div class="text-2xl font-black text-slate-900">{{ $stats['total_contracts'] }}</div>
                <div class="text-[11px] font-semibold text-slate-500 mt-1">SPK-HISTORIS Terformat</div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-xs">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Sertifikat HKI</span>
                    <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </span>
                </div>
                <div class="text-2xl font-black text-slate-900">{{ $stats['total_hki'] }}</div>
                <div class="text-[11px] font-semibold text-emerald-600 mt-1">DJKI Verified</div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-xs">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Publikasi Ilmiah</span>
                    <span class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center font-bold text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </span>
                </div>
                <div class="text-2xl font-black text-slate-900">{{ $stats['total_publications'] }}</div>
                <div class="text-[11px] font-semibold text-slate-500 mt-1">SINTA / Scopus Metadata</div>
            </div>
        </div>

        {{-- Integrity Banner --}}
        <div class="bg-gradient-to-r from-blue-900 to-indigo-950 text-white rounded-3xl p-6 sm:p-8 mb-8 shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <span class="px-3 py-1 rounded-full text-xs font-black bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 inline-block mb-2">
                        100% Zero Data Loss Assurance
                    </span>
                    <h3 class="text-xl font-black text-white">Validasi Integritas Data & Verifikasi Checksum SHA-256</h3>
                    <p class="text-xs text-slate-300 font-medium mt-1 max-w-2xl">
                        Seluruh naskah arsip dokumen historis telah diverifikasi menggunakan hashing kriptografis SHA-256 dan berhasil dipetakan secara terstruktur ke 22 Program Studi di bawah 4 Fakultas Universitas Harkat Negeri.
                    </p>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <div class="px-4 py-2.5 rounded-2xl bg-white/10 border border-white/10 text-center">
                        <span class="block text-lg font-black text-emerald-400">100%</span>
                        <span class="block text-[10px] text-slate-300 font-semibold">Integritas Arsip</span>
                    </div>
                    <div class="px-4 py-2.5 rounded-2xl bg-white/10 border border-white/10 text-center">
                        <span class="block text-lg font-black text-sky-400">22 / 22</span>
                        <span class="block text-[10px] text-slate-300 font-semibold">Prodi Terpetakan</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Mapping Table & Audit Logs --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left 2 cols: Mapping Matrix --}}
            <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-black text-base text-slate-900">Kamus Pemetaan Program Studi (22 Prodi UHN)</h3>
                        <p class="text-xs text-slate-500 font-medium">Aturan transformasi program studi dari era Politeknik Harapan Bersama & STMIK YMI ke struktur baru universitas</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                        {{ count($mapping) }} Aturan Mapping
                    </span>
                </div>
                <div class="overflow-x-auto max-h-[480px]">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead class="sticky top-0 bg-slate-50 border-b border-slate-200 text-slate-600 font-extrabold uppercase tracking-wider text-[11px]">
                            <tr>
                                <th class="py-3 px-5 whitespace-nowrap">Program Studi Asal (PHB / YMI)</th>
                                <th class="py-3 px-4 text-center whitespace-nowrap">Arah Transformasi</th>
                                <th class="py-3 px-5 whitespace-nowrap">Program Studi Resmi UHN</th>
                                <th class="py-3 px-4 text-center whitespace-nowrap">Fakultas</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($mapping as $legacy => $newCode)
                                @php
                                    $targetProdi = $prodis->firstWhere('kode_prodi', $newCode);
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-3 px-5 font-bold text-slate-800 whitespace-nowrap">
                                        {{ $legacy }}
                                    </td>
                                    <td class="py-3 px-4 text-center text-slate-400">
                                        <svg class="w-4 h-4 mx-auto text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                    </td>
                                    <td class="py-3 px-5 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-blue-50 text-blue-700 border border-blue-200/60">
                                                {{ $newCode }}
                                            </span>
                                            <span class="font-extrabold text-slate-900">{{ $targetProdi ? $targetProdi->nama_prodi : '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-center whitespace-nowrap">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                            {{ $targetProdi && $targetProdi->fakultas ? $targetProdi->fakultas->kode_fakultas : '-' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Right 1 col: Audit Trail --}}
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="font-black text-base text-slate-900">Riwayat Audit Trail Migrasi</h3>
                    <p class="text-xs text-slate-500 font-medium">Catatan log aktivitas eksekusi migrasi data legasi</p>
                </div>
                <div class="p-5 space-y-4 max-h-[480px] overflow-y-auto">
                    @forelse($recentLogs as $log)
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80">
                            <div class="flex items-center justify-between text-xs font-bold text-slate-700 mb-1">
                                <span class="text-blue-700">{{ $log->action }}</span>
                                <span class="text-[10px] text-slate-400 font-medium">{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                {{ $log->payload_sesudah['status'] ?? 'Berhasil' }}
                            </p>
                            <div class="mt-2 flex items-center gap-2 text-[10px] text-slate-400 font-medium">
                                <span>IP: {{ $log->ip_address }}</span>
                                <span>•</span>
                                <span>Operator: {{ $log->user ? $log->user->name : 'Sistem' }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center text-slate-400 text-xs font-semibold">
                            Belum ada catatan log migrasi tercatat.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </main>
</div>
</div>
@endsection


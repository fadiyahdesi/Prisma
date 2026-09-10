@extends('layouts.app')

@section('title', 'Dasbor Analitik Eksekutif - PRISMA UHN')

@section('content')
<div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <x-sidebar />

    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        {{-- Top Header --}}
        <header class="bg-white border-b border-slate-200 py-4 sticky top-0 z-30 shadow-xs print:hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="font-extrabold text-xl text-slate-900 leading-tight">Dasbor Analitik Eksekutif</h1>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black bg-indigo-100 text-indigo-800 border border-indigo-200">
                                Tingkat Universitas
                            </span>
                        </div>
                        <p class="text-xs font-semibold text-slate-500">Agregasi Makro Kinerja Riset, Serapan Anggaran, TKT & Indikator Kinerja Utama (IKU)</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('analitik.eksekutif', array_merge(request()->query(), ['refresh' => 1])) }}" 
                       class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs flex items-center gap-2 transition"
                       title="Perbarui cache agregasi secara manual">
                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span>Refresh Cache</span>
                    </a>
                    <a href="{{ route('laporan.akreditasi') }}" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs flex items-center gap-2 shadow-xs transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Borang Akreditasi</span>
                    </a>
                </div>
            </div>
        </header>

        <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            {{-- Filter Periode & Cache Status Bar --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs flex flex-wrap items-center justify-between gap-4">
                <form action="{{ route('analitik.eksekutif') }}" method="GET" class="flex items-center gap-3">
                    <label for="periode_id" class="text-xs font-bold text-slate-700">Filter Periode Hibah:</label>
                    <select name="periode_id" id="periode_id" onchange="this.form.submit()" class="px-3 py-1.5 rounded-lg border border-slate-300 text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-indigo-500 focus:outline-hidden">
                        <option value="">-- Semua Periode Tahun Anggaran --</option>
                        @foreach($periodes as $p)
                            <option value="{{ $p->id }}" {{ $periodeId == $p->id ? 'selected' : '' }}>
                                {{ $p->nama_periode }} ({{ $p->tahun_akademik ?? $p->tahun_anggaran }})
                            </option>
                        @endforeach
                    </select>
                </form>

                <div class="flex items-center gap-3 text-xs">
                    <div class="flex items-center gap-1.5 text-emerald-700 bg-emerald-50 px-3 py-1 rounded-lg border border-emerald-200 font-bold">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Waktu Muat: &lt; 1,5 Detik (Aggregation Cache)</span>
                    </div>
                    <span class="text-slate-400 font-medium">Diperbarui: {{ $metrics['cached_at'] }}</span>
                </div>
            </div>

            {{-- Executive KPI Metrics Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                {{-- Card 1: Total Usulan & Selektivitas --}}
                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs relative overflow-hidden">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Usulan Masuk & Lolos</span>
                        <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-black text-slate-900">{{ $metrics['usulan_approved'] }}</span>
                        <span class="text-sm font-semibold text-slate-500">/ {{ $metrics['total_usulan'] }} Judul</span>
                    </div>
                    <div class="mt-3 flex items-center justify-between text-xs">
                        <span class="text-slate-500 font-medium">Tingkat Kelolosan (Acceptance)</span>
                        <span class="font-bold text-blue-600">{{ $metrics['acceptance_rate'] }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-1.5 mt-1.5 overflow-hidden">
                        <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ min(100, $metrics['acceptance_rate']) }}%"></div>
                    </div>
                </div>

                {{-- Card 2: Realisasi Anggaran Hibah --}}
                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs relative overflow-hidden">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Realisasi & Serapan Dana</span>
                        <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-1">
                        <span class="text-xs font-bold text-slate-500">Rp</span>
                        <span class="text-2xl font-black text-slate-900">{{ number_format($metrics['total_realisasi'], 0, ',', '.') }}</span>
                    </div>
                    <div class="mt-3 flex items-center justify-between text-xs">
                        <span class="text-slate-500 font-medium">Pagu: Rp {{ number_format($metrics['total_pagu'], 0, ',', '.') }}</span>
                        <span class="font-bold text-emerald-600">{{ $metrics['serapan_percentage'] }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-1.5 mt-1.5 overflow-hidden">
                        <div class="bg-emerald-600 h-1.5 rounded-full" style="width: {{ min(100, $metrics['serapan_percentage']) }}%"></div>
                    </div>
                </div>

                {{-- Card 3: Target IKU-2 (Mahasiswa Riset Luar Kampus) --}}
                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs relative overflow-hidden">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Ketercapaian IKU-2</span>
                        <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-black text-slate-900">{{ $metrics['iku2']['realisasi'] }}</span>
                        <span class="text-sm font-semibold text-slate-500">/ Target {{ $metrics['iku2']['target'] }} Mhs</span>
                    </div>
                    <div class="mt-3 flex items-center justify-between text-xs">
                        <span class="text-slate-500 font-medium">Mahasiswa Terlibat Riset BIMA</span>
                        <span class="font-bold text-purple-600">{{ $metrics['iku2']['pct'] }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-1.5 mt-1.5 overflow-hidden">
                        <div class="bg-purple-600 h-1.5 rounded-full" style="width: {{ min(100, $metrics['iku2']['pct']) }}%"></div>
                    </div>
                </div>

                {{-- Card 4: Target IKU-5 (Karya Ilmiah & HKI Dosen) --}}
                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs relative overflow-hidden">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Ketercapaian IKU-5</span>
                        <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-black text-slate-900">{{ $metrics['iku5']['realisasi'] }}</span>
                        <span class="text-sm font-semibold text-slate-500">/ Target {{ $metrics['iku5']['target'] }} Luaran</span>
                    </div>
                    <div class="mt-3 flex items-center justify-between text-xs">
                        <span class="text-slate-500 font-medium">Scopus: {{ $metrics['iku5']['scopus'] }} | SINTA: {{ $metrics['iku5']['sinta'] }} | HKI: {{ $metrics['iku5']['hki'] }}</span>
                        <span class="font-bold text-amber-600">{{ $metrics['iku5']['pct'] }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-1.5 mt-1.5 overflow-hidden">
                        <div class="bg-amber-600 h-1.5 rounded-full" style="width: {{ min(100, $metrics['iku5']['pct']) }}%"></div>
                    </div>
                </div>
            </div>

            {{-- Interactive Charts Section (Chart.js) --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Chart 1: Pemetaan Level TKT (Tingkat Kesiapterapan Teknologi) --}}
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="font-bold text-sm text-slate-900">Distribusi Level TKT Riset</h3>
                            <p class="text-xs text-slate-500">Pemetaan Skema BIMA (Dasar, Terapan, Pengembangan)</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-bold">
                            TKT 1 - 9
                        </span>
                    </div>
                    <div class="relative h-60 flex items-center justify-center">
                        <canvas id="tktChart"></canvas>
                    </div>
                    <div class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-3 gap-2 text-center text-xs">
                        <div class="p-2 rounded-xl bg-sky-50">
                            <p class="text-slate-500 font-medium text-[11px]">TKT 1-3 Dasar</p>
                            <p class="font-black text-sky-700 text-base mt-0.5">{{ $metrics['tkt_distribution']['dasar']['count'] }}</p>
                            <p class="text-[10px] text-slate-400 font-bold">{{ $metrics['tkt_distribution']['dasar']['pct'] }}%</p>
                        </div>
                        <div class="p-2 rounded-xl bg-indigo-50">
                            <p class="text-slate-500 font-medium text-[11px]">TKT 4-6 Terapan</p>
                            <p class="font-black text-indigo-700 text-base mt-0.5">{{ $metrics['tkt_distribution']['terapan']['count'] }}</p>
                            <p class="text-[10px] text-slate-400 font-bold">{{ $metrics['tkt_distribution']['terapan']['pct'] }}%</p>
                        </div>
                        <div class="p-2 rounded-xl bg-emerald-50">
                            <p class="text-slate-500 font-medium text-[11px]">TKT 7-9 Pengemb.</p>
                            <p class="font-black text-emerald-700 text-base mt-0.5">{{ $metrics['tkt_distribution']['pengembangan']['count'] }}</p>
                            <p class="text-[10px] text-slate-400 font-bold">{{ $metrics['tkt_distribution']['pengembangan']['pct'] }}%</p>
                        </div>
                    </div>
                </div>

                {{-- Chart 2: Komparasi Realisasi Hibah & Serapan 4 Fakultas --}}
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs lg:col-span-2">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="font-bold text-sm text-slate-900">Perbandingan Anggaran & Serapan Dana Hibah (Juta Rp)</h3>
                            <p class="text-xs text-slate-500">Komparasi Pagu Kontrak vs Realisasi Pencairan 4 Fakultas</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200">
                            4 Fakultas UHN
                        </span>
                    </div>
                    <div class="relative h-64">
                        <canvas id="fakultasChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Rekapitulasi Real-Time 4 Fakultas Table (US-12.1) --}}
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4 bg-slate-50/50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-black shadow-xs shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-extrabold text-base text-slate-900">Rekapitulasi Kinerja 4 Fakultas UHN</h3>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    Real-Time Multi-Tenant
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 font-medium">Monitoring komprehensif usulan riset, realisasi anggaran, persentase serapan, dan rekognisi luaran DTPS</p>
                        </div>
                    </div>
                    <div class="text-xs font-semibold text-slate-500">
                        Total 4 Fakultas Terdata
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-extrabold uppercase tracking-wider text-[11px]">
                                <th class="py-3.5 px-5 whitespace-nowrap">Fakultas & Pimpinan</th>
                                <th class="py-3.5 px-4 text-center whitespace-nowrap">Usulan (Lolos / Masuk)</th>
                                <th class="py-3.5 px-4 text-right whitespace-nowrap">Alokasi & Serapan Pagu</th>
                                <th class="py-3.5 px-4 text-center whitespace-nowrap">% Serapan Dana</th>
                                <th class="py-3.5 px-4 text-center whitespace-nowrap">Luaran (Publikasi / HKI)</th>
                                <th class="py-3.5 px-5 text-center whitespace-nowrap">Aksi Analisis</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($metrics['fakultas_breakdown'] as $fb)
                            <tr class="hover:bg-slate-50/90 transition-colors">
                                <td class="py-4 px-5 align-middle">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-indigo-50 border border-indigo-200/60 text-indigo-700 font-black text-xs flex items-center justify-center shrink-0 shadow-xs">
                                            {{ $fb['kode'] }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-extrabold text-slate-900 text-xs tracking-normal">{{ $fb['nama'] }}</div>
                                            <div class="text-[11px] text-slate-500 font-medium truncate flex items-center gap-1 mt-0.5">
                                                <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                <span>{{ $fb['dekan'] ?: 'Pimpinan Fakultas' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-center align-middle whitespace-nowrap">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="flex items-center gap-1 text-xs">
                                            <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 font-extrabold border border-emerald-200/60">
                                                {{ $fb['usulan_lolos'] }}
                                            </span>
                                            <span class="text-slate-400 font-bold">/</span>
                                            <span class="text-slate-600 font-bold">{{ $fb['total_usulan'] }}</span>
                                        </div>
                                        <span class="text-[10px] text-slate-400 font-medium mt-1">
                                            {{ $fb['total_usulan'] > 0 ? round(($fb['usulan_lolos'] / $fb['total_usulan']) * 100, 0) : 0 }}% Tingkat Lolos
                                        </span>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-right align-middle whitespace-nowrap">
                                    <div class="font-black text-slate-900 text-xs">
                                        Rp {{ number_format($fb['total_pagu'], 0, ',', '.') }}
                                    </div>
                                    <div class="text-[11px] font-bold text-emerald-600 mt-1 flex items-center justify-end gap-1">
                                        <span class="text-slate-400 font-normal">Serap:</span>
                                        <span>Rp {{ number_format($fb['total_serapan'], 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-4 align-middle">
                                    <div class="w-36 mx-auto">
                                        <div class="flex items-center justify-between text-[11px] font-extrabold mb-1">
                                            <span class="{{ $fb['serapan_pct'] >= 70 ? 'text-emerald-700' : ($fb['serapan_pct'] >= 40 ? 'text-blue-700' : 'text-amber-700') }}">
                                                {{ $fb['serapan_pct'] }}%
                                            </span>
                                            <span class="text-[10px] text-slate-400 font-medium">Realisasi</span>
                                        </div>
                                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden border border-slate-200/50">
                                            <div class="h-2 rounded-full transition-all duration-500 {{ $fb['serapan_pct'] >= 70 ? 'bg-emerald-500' : ($fb['serapan_pct'] >= 40 ? 'bg-blue-500' : 'bg-amber-500') }}" 
                                                 style="width: {{ min(100, $fb['serapan_pct']) }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-center align-middle whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-2">
                                        <span class="px-2.5 py-1 rounded-lg bg-sky-50 text-sky-800 font-extrabold text-xs border border-sky-200/60 flex items-center gap-1 shadow-2xs" title="Publikasi Terbit">
                                            <svg class="w-3 h-3 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                            <span>{{ $fb['total_publikasi'] }}</span>
                                        </span>
                                        <span class="px-2.5 py-1 rounded-lg bg-amber-50 text-amber-800 font-extrabold text-xs border border-amber-200/60 flex items-center gap-1 shadow-2xs" title="HKI & Paten Terdaftar">
                                            <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                            <span>{{ $fb['total_hki'] }}</span>
                                        </span>
                                    </div>
                                </td>
                                <td class="py-4 px-5 text-center align-middle whitespace-nowrap">
                                    <a href="{{ route('analitik.fakultas', ['fakultas_id' => $fb['id']]) }}" 
                                       class="px-3.5 py-1.5 rounded-xl bg-slate-900 hover:bg-indigo-600 text-white font-bold text-xs transition-colors inline-flex items-center gap-1.5 shadow-xs group">
                                        <span>Performa</span>
                                        <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400 font-semibold">
                                    Belum ada data fakultas tercatat.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="bg-slate-900 text-white font-extrabold text-xs">
                                <td class="py-4 px-5 align-middle whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                        <span class="tracking-wide uppercase text-[11px]">TOTAL KINERJA UNIVERSITAS</span>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-center align-middle whitespace-nowrap">
                                    <div class="font-black text-white text-xs">
                                        <span class="text-emerald-300">{{ $metrics['usulan_approved'] }}</span>
                                        <span class="text-slate-500">/</span>
                                        <span>{{ $metrics['total_usulan'] }}</span>
                                    </div>
                                    <div class="text-[10px] text-slate-400 font-semibold mt-0.5">Total Usulan</div>
                                </td>
                                <td class="py-4 px-4 text-right align-middle whitespace-nowrap">
                                    <div class="font-black text-white text-xs">Rp {{ number_format($metrics['total_pagu'], 0, ',', '.') }}</div>
                                    <div class="text-[11px] text-emerald-400 font-bold mt-0.5">Serap: Rp {{ number_format($metrics['total_realisasi'], 0, ',', '.') }}</div>
                                </td>
                                <td class="py-4 px-4 text-center align-middle whitespace-nowrap">
                                    <div class="w-32 mx-auto flex flex-col items-center justify-center">
                                        <span class="text-xs font-black text-emerald-300 mb-1">
                                            {{ $metrics['serapan_percentage'] }}%
                                        </span>
                                        <div class="w-full bg-white/10 rounded-full h-1.5 overflow-hidden border border-white/10">
                                            <div class="h-1.5 rounded-full bg-emerald-400"
                                                 style="width: {{ min(100, $metrics['serapan_percentage']) }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-center align-middle whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-3">
                                        <div class="text-center">
                                            <div class="font-black text-sky-300 text-xs">{{ array_sum(array_column($metrics['fakultas_breakdown'], 'total_publikasi')) }}</div>
                                            <div class="text-[10px] text-slate-400 font-semibold mt-0.5">Publikasi</div>
                                        </div>
                                        <span class="text-slate-600 font-bold">|</span>
                                        <div class="text-center">
                                            <div class="font-black text-amber-300 text-xs">{{ array_sum(array_column($metrics['fakultas_breakdown'], 'total_hki')) }}</div>
                                            <div class="text-[10px] text-slate-400 font-semibold mt-0.5">HKI</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-5 text-center align-middle whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-white/10 text-slate-300 border border-white/10 whitespace-nowrap inline-block">
                                        4 Fakultas Terpadu
                                    </span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

{{-- Chart.js Script Integration --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartPayload = @json($metrics['chart_data']);

    // 1. TKT Doughnut Chart
    const ctxTkt = document.getElementById('tktChart');
    if (ctxTkt) {
        new Chart(ctxTkt, {
            type: 'doughnut',
            data: {
                labels: chartPayload.tkt.labels,
                datasets: [{
                    data: chartPayload.tkt.values,
                    backgroundColor: ['#0284c7', '#4f46e5', '#059669'],
                    hoverOffset: 4,
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { size: 10, weight: 'bold' },
                            boxWidth: 12,
                            padding: 10
                        }
                    }
                },
                cutout: '65%'
            }
        });
    }

    // 2. Fakultas Grouped Bar Chart
    const ctxFak = document.getElementById('fakultasChart');
    if (ctxFak) {
        new Chart(ctxFak, {
            type: 'bar',
            data: {
                labels: chartPayload.fakultas.labels,
                datasets: [
                    {
                        label: 'Pagu Disetujui (Juta Rp)',
                        data: chartPayload.fakultas.pagu_juta,
                        backgroundColor: '#94a3b8',
                        borderRadius: 6,
                        barPercentage: 0.7,
                        categoryPercentage: 0.6
                    },
                    {
                        label: 'Realisasi Serapan (Juta Rp)',
                        data: chartPayload.fakultas.serapan_juta,
                        backgroundColor: '#10b981',
                        borderRadius: 6,
                        barPercentage: 0.7,
                        categoryPercentage: 0.6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { font: { size: 11, weight: 'bold' } }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': Rp ' + context.parsed.y.toLocaleString('id-ID') + ' Juta';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Juta Rupiah (Rp)', font: { size: 10, weight: 'bold' } },
                        grid: { color: '#f1f5f9' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }
});
</script>
@endsection


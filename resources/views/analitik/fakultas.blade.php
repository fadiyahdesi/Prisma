@extends('layouts.app')

@section('title', 'Performa Fakultas: ' . ($facultyData['fakultas']->nama_fakultas ?? 'Fakultas') . ' - PRISMA UHN')

@section('content')
<div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <x-sidebar />

    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        {{-- Top Header --}}
        <header class="bg-white border-b border-slate-200 py-4 sticky top-0 z-30 shadow-xs print:hidden">
            <div class="w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="font-extrabold text-xl text-slate-900 leading-tight">{{ $facultyData['fakultas']->nama_fakultas }}</h1>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black bg-emerald-100 text-emerald-800 border border-emerald-200">
                                {{ $facultyData['fakultas']->kode_fakultas }}
                            </span>
                        </div>
                        <p class="text-xs font-semibold text-slate-500">Dasbor Monitoring Kinerja Riset, Serapan Anggaran & Realisasi Luaran Program Studi</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('laporan.akreditasi', ['fakultas_id' => $targetFakultasId]) }}" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs flex items-center gap-2 shadow-xs transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Borang Akreditasi Fakultas</span>
                    </a>
                </div>
            </div>
        </header>

        <main class="flex-grow w-full px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            {{-- Tenant Controls & Filter Bar --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs flex flex-wrap items-center justify-between gap-4">
                <form action="{{ route('analitik.fakultas') }}" method="GET" class="flex flex-wrap items-center gap-3">
                    @if($isPrivileged)
                    <div class="flex items-center gap-2">
                        <label for="fakultas_id" class="text-xs font-bold text-slate-700">Pilih Fakultas:</label>
                        <select name="fakultas_id" id="fakultas_id" onchange="this.form.submit()" class="px-3 py-1.5 rounded-lg border border-slate-300 text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-indigo-500 focus:outline-hidden">
                            @foreach($allFakultas as $fak)
                                <option value="{{ $fak->id_fakultas }}" {{ $targetFakultasId == $fak->id_fakultas ? 'selected' : '' }}>
                                    {{ $fak->nama_fakultas }} ({{ $fak->kode_fakultas }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @else
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-slate-500">Unit Terisolasi:</span>
                        <span class="px-3 py-1 rounded-lg bg-slate-100 text-slate-800 text-xs font-bold border border-slate-200">
                            {{ $facultyData['fakultas']->nama_fakultas }} (Dekanat Terverifikasi)
                        </span>
                    </div>
                    @endif

                    <div class="flex items-center gap-2">
                        <label for="periode_id" class="text-xs font-bold text-slate-700">Periode:</label>
                        <select name="periode_id" id="periode_id" onchange="this.form.submit()" class="px-3 py-1.5 rounded-lg border border-slate-300 text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-indigo-500 focus:outline-hidden">
                            <option value="">Semua Periode</option>
                            @foreach($periodes as $p)
                                <option value="{{ $p->id }}" {{ $periodeId == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama_periode }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>

                <div class="text-xs font-semibold text-slate-500 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Dekan: <strong class="text-slate-800">{{ $facultyData['fakultas']->dekan_nama ?: 'Pimpinan Fakultas' }}</strong></span>
                </div>
            </div>

            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                {{-- Total Usulan Didanai --}}
                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Usulan Lolos / Total</span>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-3xl font-black text-slate-900">{{ $facultyData['summary']['total_lolos'] }}</span>
                        <span class="text-sm font-semibold text-slate-500">/ {{ $facultyData['summary']['total_usulan'] }} Judul</span>
                    </div>
                    <div class="mt-2 text-xs font-medium text-slate-500">
                        Kelolosan: <strong class="text-blue-600">{{ $facultyData['summary']['total_usulan'] > 0 ? round(($facultyData['summary']['total_lolos'] / $facultyData['summary']['total_usulan']) * 100, 1) : 0 }}%</strong>
                    </div>
                </div>

                {{-- Serapan Anggaran Fakultas --}}
                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Serapan Dana</span>
                    <div class="mt-2 flex items-baseline gap-1">
                        <span class="text-xs font-bold text-slate-500">Rp</span>
                        <span class="text-2xl font-black text-slate-900">{{ number_format($facultyData['summary']['total_serapan'], 0, ',', '.') }}</span>
                    </div>
                    <div class="mt-2 text-xs font-medium text-slate-500">
                        Pagu: Rp {{ number_format($facultyData['summary']['total_pagu'], 0, ',', '.') }} (<strong class="text-emerald-600">{{ $facultyData['summary']['serapan_pct'] }}%</strong>)
                    </div>
                </div>

                {{-- Publikasi DTPS --}}
                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Publikasi Terbit</span>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-3xl font-black text-slate-900">{{ $facultyData['summary']['total_publikasi'] }}</span>
                        <span class="text-sm font-semibold text-slate-500">Artikel Ilmiah</span>
                    </div>
                    <div class="mt-2 text-xs font-medium text-slate-500">
                        Scopus & SINTA Bereputasi
                    </div>
                </div>

                {{-- HKI Terverifikasi --}}
                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">HKI & Paten Terdaftar</span>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-3xl font-black text-slate-900">{{ $facultyData['summary']['total_hki'] }}</span>
                        <span class="text-sm font-semibold text-slate-500">Karya Cipta</span>
                    </div>
                    <div class="mt-2 text-xs font-medium text-slate-500">
                        Sertifikat DJKI Kemenkumham
                    </div>
                </div>
            </div>

            {{-- Highlight Callouts: Deteksi Prodi Serapan Tertinggi & Terendah (US-12.2) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Highest Absorber --}}
                <div class="bg-emerald-50/70 border-2 border-emerald-200 rounded-2xl p-5 flex items-start justify-between shadow-xs">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <span class="text-xs font-extrabold uppercase tracking-wider text-emerald-800">Prodi Serapan Dana Tertinggi</span>
                        </div>
                        <h4 class="text-lg font-black text-emerald-950">
                            {{ $facultyData['highest_prodi']['nama'] ?? 'Belum ada data' }}
                        </h4>
                        <p class="text-xs text-emerald-700 font-medium mt-1">
                            Kaprodi: {{ $facultyData['highest_prodi']['kaprodi'] ?? '-' }}
                        </p>
                        <div class="mt-3 flex items-center gap-4 text-xs">
                            <div>
                                <span class="text-emerald-700 block font-medium">Realisasi:</span>
                                <span class="font-black text-emerald-900 text-sm">Rp {{ number_format($facultyData['highest_prodi']['serapan'] ?? 0, 0, ',', '.') }}</span>
                            </div>
                            <div class="border-l border-emerald-200 pl-4">
                                <span class="text-emerald-700 block font-medium">Persentase Serapan:</span>
                                <span class="font-black text-emerald-900 text-sm">{{ $facultyData['highest_prodi']['serapan_pct'] ?? 0 }}%</span>
                            </div>
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-black bg-emerald-600 text-white shadow-xs">
                        Performa Terbaik
                    </span>
                </div>

                {{-- Lowest Absorber / Needs Attention --}}
                <div class="bg-amber-50/70 border-2 border-amber-200 rounded-2xl p-5 flex items-start justify-between shadow-xs">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            <span class="text-xs font-extrabold uppercase tracking-wider text-amber-800">Prodi Serapan Terendah (Perlu Perhatian)</span>
                        </div>
                        <h4 class="text-lg font-black text-amber-950">
                            {{ $facultyData['lowest_prodi']['nama'] ?? 'Belum ada data' }}
                        </h4>
                        <p class="text-xs text-amber-700 font-medium mt-1">
                            Kaprodi: {{ $facultyData['lowest_prodi']['kaprodi'] ?? '-' }}
                        </p>
                        <div class="mt-3 flex items-center gap-4 text-xs">
                            <div>
                                <span class="text-amber-700 block font-medium">Realisasi:</span>
                                <span class="font-black text-amber-900 text-sm">Rp {{ number_format($facultyData['lowest_prodi']['serapan'] ?? 0, 0, ',', '.') }}</span>
                            </div>
                            <div class="border-l border-amber-200 pl-4">
                                <span class="text-amber-700 block font-medium">Persentase Serapan:</span>
                                <span class="font-black text-amber-900 text-sm">{{ $facultyData['lowest_prodi']['serapan_pct'] ?? 0 }}%</span>
                            </div>
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-black bg-amber-500 text-white shadow-xs">
                        Perlu Evaluasi
                    </span>
                </div>
            </div>

            {{-- Matriks Perbandingan Target Renstra vs Realisasi Capaian Fakultas (Rincian Target vs Realisasi) --}}
            @php
                $targetPubTotal = max(1, (int) collect($facultyData['prodi_metrics'])->sum('target_publikasi'));
                $realPubTotal = (int) ($facultyData['summary']['total_publikasi'] ?? 0);
                $pctPub = round(($realPubTotal / $targetPubTotal) * 100, 1);

                $targetHkiTotal = max(1, (int) collect($facultyData['prodi_metrics'])->sum('target_hki'));
                $realHkiTotal = (int) ($facultyData['summary']['total_hki'] ?? 0);
                $pctHki = round(($realHkiTotal / $targetHkiTotal) * 100, 1);

                $targetUsulanTotal = max(1, (int) ($facultyData['summary']['total_usulan'] ?? 0));
                $realLolosTotal = (int) ($facultyData['summary']['total_lolos'] ?? 0);
                $pctLolos = round(($realLolosTotal / $targetUsulanTotal) * 100, 1);

                $paguFakTotal = max(1, (float) ($facultyData['summary']['total_pagu'] ?? 0));
                $serapanFakTotal = (float) ($facultyData['summary']['total_serapan'] ?? 0);
                $pctSerapan = round(($serapanFakTotal / $paguFakTotal) * 100, 1);
            @endphp

            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xs space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="font-extrabold text-base text-slate-900 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                            <span>Matriks Perbandingan Target Renstra vs Realisasi Capaian Fakultas</span>
                        </h3>
                        <p class="text-xs text-slate-500 font-medium">Evaluasi pemenuhan target indikator kinerja utama (IKU) riset dan pengabdian tingkat fakultas</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-black bg-indigo-50 text-indigo-800 border border-indigo-200 self-start sm:self-auto">
                        Evaluasi Capaian Renstra
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    {{-- 1. Publikasi DTPS --}}
                    <div class="p-4.5 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col justify-between space-y-3">
                        <div>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Publikasi Scopus & SINTA</span>
                            <div class="mt-1 flex items-baseline justify-between">
                                <span class="text-xl font-black text-slate-900">{{ $realPubTotal }} <span class="text-xs text-slate-400 font-normal">Artikel</span></span>
                                <span class="text-xs font-bold text-slate-500">Target: {{ $targetPubTotal }}</span>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center justify-between text-[11px] mb-1">
                                <span class="font-bold text-slate-600">Capaian:</span>
                                <strong class="{{ $pctPub >= 100 ? 'text-emerald-600' : 'text-blue-600' }}">{{ $pctPub }}%</strong>
                            </div>
                            <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                                <div class="h-2 rounded-full {{ $pctPub >= 100 ? 'bg-emerald-500' : 'bg-blue-600' }}" style="width: {{ min(100, $pctPub) }}%"></div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. HKI & Paten --}}
                    <div class="p-4.5 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col justify-between space-y-3">
                        <div>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Perolehan HKI & Paten</span>
                            <div class="mt-1 flex items-baseline justify-between">
                                <span class="text-xl font-black text-slate-900">{{ $realHkiTotal }} <span class="text-xs text-slate-400 font-normal">Sertifikat</span></span>
                                <span class="text-xs font-bold text-slate-500">Target: {{ $targetHkiTotal }}</span>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center justify-between text-[11px] mb-1">
                                <span class="font-bold text-slate-600">Capaian:</span>
                                <strong class="{{ $pctHki >= 100 ? 'text-emerald-600' : 'text-amber-600' }}">{{ $pctHki }}%</strong>
                            </div>
                            <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                                <div class="h-2 rounded-full {{ $pctHki >= 100 ? 'bg-emerald-500' : 'bg-amber-500' }}" style="width: {{ min(100, $pctHki) }}%"></div>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Kelolosan Usulan --}}
                    <div class="p-4.5 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col justify-between space-y-3">
                        <div>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Usulan Lolos Seleksi</span>
                            <div class="mt-1 flex items-baseline justify-between">
                                <span class="text-xl font-black text-slate-900">{{ $realLolosTotal }} <span class="text-xs text-slate-400 font-normal">Judul</span></span>
                                <span class="text-xs font-bold text-slate-500">Masuk: {{ $targetUsulanTotal }}</span>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center justify-between text-[11px] mb-1">
                                <span class="font-bold text-slate-600">Rasio Lolos:</span>
                                <strong class="text-purple-600">{{ $pctLolos }}%</strong>
                            </div>
                            <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                                <div class="h-2 rounded-full bg-purple-600" style="width: {{ min(100, $pctLolos) }}%"></div>
                            </div>
                        </div>
                    </div>

                    {{-- 4. Serapan Dana --}}
                    <div class="p-4.5 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col justify-between space-y-3">
                        <div>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Serapan Anggaran Hibah</span>
                            <div class="mt-1 flex items-baseline justify-between">
                                <span class="text-sm font-black text-slate-900">Rp {{ number_format($serapanFakTotal, 0, ',', '.') }}</span>
                            </div>
                            <span class="text-[10px] text-slate-400 block mt-0.5">Pagu: Rp {{ number_format($paguFakTotal, 0, ',', '.') }}</span>
                        </div>
                        <div>
                            <div class="flex items-center justify-between text-[11px] mb-1">
                                <span class="font-bold text-slate-600">Efisiensi Serapan:</span>
                                <strong class="{{ $pctSerapan >= 70 ? 'text-emerald-600' : 'text-blue-600' }}">{{ $pctSerapan }}%</strong>
                            </div>
                            <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                                <div class="h-2 rounded-full {{ $pctSerapan >= 70 ? 'bg-emerald-500' : 'bg-blue-600' }}" style="width: {{ min(100, $pctSerapan) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Comparison Chart: Target vs Realisasi Luaran (Publikasi & HKI) --}}
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-bold text-base text-slate-900">Perbandingan Target vs Realisasi Luaran per Program Studi</h3>
                        <p class="text-xs text-slate-500">Evaluasi pemenuhan target publikasi ilmiah dan luaran HKI/Paten DTPS</p>
                    </div>
                </div>
                <div class="relative h-72">
                    <canvas id="luaranComparisonChart"></canvas>
                </div>
            </div>

            {{-- Detailed Prodi Table --}}
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4 bg-slate-50/50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-600 text-white flex items-center justify-center font-black shadow-xs shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-base text-slate-900">Rincian Capaian Kinerja per Program Studi</h3>
                            <p class="text-xs text-slate-500 font-medium">Tabel evaluasi dosen, usulan lolos, realisasi serapan anggaran, serta rasio pemenuhan luaran publikasi dan HKI</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                        {{ count($facultyData['prodi_metrics']) }} Program Studi Terdaftar
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-extrabold uppercase tracking-wider text-[11px]">
                                <th class="py-3.5 px-5 whitespace-nowrap">Program Studi & Kaprodi</th>
                                <th class="py-3.5 px-3 text-center whitespace-nowrap">Dosen</th>
                                <th class="py-3.5 px-3 text-center whitespace-nowrap">Usulan (Lolos / Masuk)</th>
                                <th class="py-3.5 px-4 text-right whitespace-nowrap">Alokasi & Serapan Pagu</th>
                                <th class="py-3.5 px-4 text-center whitespace-nowrap">% Serapan</th>
                                <th class="py-3.5 px-4 text-center whitespace-nowrap">Publikasi (Real / Target)</th>
                                <th class="py-3.5 px-4 text-center whitespace-nowrap">HKI (Real / Target)</th>
                                <th class="py-3.5 px-5 text-center whitespace-nowrap">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($facultyData['prodi_metrics'] as $pm)
                            <tr class="hover:bg-slate-50/90 transition-colors">
                                <td class="py-4 px-5 align-middle">
                                    <div class="flex items-center gap-2.5">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-slate-100 text-slate-700 border border-slate-200 shrink-0">
                                            {{ $pm['jenjang'] ?? 'PRODI' }}
                                        </span>
                                        <div class="min-w-0">
                                            <span class="font-extrabold text-slate-900 block text-xs tracking-normal">{{ preg_replace('/^(S-1|D-4|D-3|S1|D4|D3)[\s\-]*/i', '', $pm['nama']) }}</span>
                                            <span class="text-[11px] font-medium text-slate-400 truncate block mt-0.5">Kaprodi: {{ $pm['kaprodi'] ?: '-' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-3 text-center align-middle whitespace-nowrap">
                                    <span class="font-extrabold text-slate-800 text-xs">{{ $pm['dosen_count'] }}</span>
                                    <span class="text-[10px] text-slate-400 block mt-0.5">Dosen</span>
                                </td>
                                <td class="py-4 px-3 text-center align-middle whitespace-nowrap">
                                    <div class="inline-flex items-center justify-center gap-1.5 px-2.5 py-1 rounded-lg bg-indigo-50/80 border border-indigo-100 text-indigo-700">
                                        <span class="font-black text-xs">{{ $pm['lolos_count'] }}</span>
                                        <span class="text-slate-300 font-bold">/</span>
                                        <span class="text-slate-500 font-bold text-xs">{{ $pm['usulan_count'] }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-right align-middle whitespace-nowrap">
                                    <div class="font-black text-slate-900 text-xs">
                                        Rp {{ number_format($pm['pagu'], 0, ',', '.') }}
                                    </div>
                                    <div class="text-[11px] font-bold text-emerald-600 mt-0.5">
                                        Serap: Rp {{ number_format($pm['serapan'], 0, ',', '.') }}
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-center align-middle">
                                    <div class="w-24 mx-auto flex flex-col items-center justify-center">
                                        <span class="text-xs font-black mb-1 {{ $pm['serapan_pct'] >= 70 ? 'text-emerald-700' : ($pm['serapan_pct'] >= 40 ? 'text-blue-700' : 'text-rose-700') }}">
                                            {{ $pm['serapan_pct'] }}%
                                        </span>
                                        <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden border border-slate-200/50">
                                            <div class="h-1.5 rounded-full {{ $pm['serapan_pct'] >= 70 ? 'bg-emerald-500' : ($pm['serapan_pct'] >= 40 ? 'bg-blue-500' : 'bg-rose-500') }}"
                                                 style="width: {{ min(100, $pm['serapan_pct']) }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-center align-middle whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-sky-50 text-sky-800 border border-sky-200/60">
                                        <span class="font-black text-xs {{ $pm['realisasi_publikasi'] >= $pm['target_publikasi'] ? 'text-emerald-700' : 'text-sky-800' }}">
                                            {{ $pm['realisasi_publikasi'] }}
                                        </span>
                                        <span class="text-slate-400 font-bold">/</span>
                                        <span class="text-[11px] text-slate-600 font-semibold">{{ $pm['target_publikasi'] }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-center align-middle whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-800 border border-amber-200/60">
                                        <span class="font-black text-xs {{ $pm['realisasi_hki'] >= $pm['target_hki'] ? 'text-emerald-700' : 'text-amber-800' }}">
                                            {{ $pm['realisasi_hki'] }}
                                        </span>
                                        <span class="text-slate-400 font-bold">/</span>
                                        <span class="text-[11px] text-slate-600 font-semibold">{{ $pm['target_hki'] }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-5 text-center align-middle whitespace-nowrap">
                                    @if($pm['serapan_pct'] >= 80 && $pm['realisasi_publikasi'] >= $pm['target_publikasi'])
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-200 whitespace-nowrap inline-block">
                                            Memuaskan
                                        </span>
                                    @elseif($pm['serapan_pct'] >= 50)
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-blue-100 text-blue-800 border border-blue-200 whitespace-nowrap inline-block">
                                            Sesuai Jalur
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-rose-100 text-rose-800 border border-rose-200 whitespace-nowrap inline-block">
                                            Perhatian
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-slate-400 font-semibold">
                                    Belum ada data program studi untuk fakultas ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if(count($facultyData['prodi_metrics']) > 0)
                        <tfoot>
                            <tr class="bg-slate-900 text-white font-extrabold text-xs">
                                <td class="py-4 px-5 align-middle whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                                        <span class="tracking-wide uppercase text-[11px]">TOTAL FAKULTAS</span>
                                    </div>
                                </td>
                                <td class="py-4 px-3 text-center align-middle whitespace-nowrap">
                                    <div class="font-black text-white text-xs">{{ array_sum(array_column($facultyData['prodi_metrics'], 'dosen_count')) }}</div>
                                    <div class="text-[10px] text-slate-400 font-semibold mt-0.5">Total Dosen</div>
                                </td>
                                <td class="py-4 px-3 text-center align-middle whitespace-nowrap">
                                    <div class="font-black text-white text-xs">
                                        <span class="text-indigo-300">{{ $facultyData['summary']['total_lolos'] }}</span>
                                        <span class="text-slate-500">/</span>
                                        <span>{{ $facultyData['summary']['total_usulan'] }}</span>
                                    </div>
                                    <div class="text-[10px] text-slate-400 font-semibold mt-0.5">Total Usulan</div>
                                </td>
                                <td class="py-4 px-4 text-right align-middle whitespace-nowrap">
                                    <div class="font-black text-white text-xs">Rp {{ number_format($facultyData['summary']['total_pagu'], 0, ',', '.') }}</div>
                                    <div class="text-[11px] text-emerald-400 font-bold mt-0.5">Serap: Rp {{ number_format($facultyData['summary']['total_serapan'], 0, ',', '.') }}</div>
                                </td>
                                <td class="py-4 px-4 text-center align-middle whitespace-nowrap">
                                    <div class="w-24 mx-auto flex flex-col items-center justify-center">
                                        <span class="text-xs font-black text-emerald-300 mb-1">
                                            {{ $facultyData['summary']['serapan_pct'] }}%
                                        </span>
                                        <div class="w-full bg-white/10 rounded-full h-1.5 overflow-hidden border border-white/10">
                                            <div class="h-1.5 rounded-full bg-emerald-400"
                                                 style="width: {{ min(100, $facultyData['summary']['serapan_pct']) }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-center align-middle whitespace-nowrap">
                                    <div class="font-black text-sky-300 text-xs">{{ $facultyData['summary']['total_publikasi'] }}</div>
                                    <div class="text-[10px] text-slate-400 font-semibold mt-0.5">Total Publikasi</div>
                                </td>
                                <td class="py-4 px-4 text-center align-middle whitespace-nowrap">
                                    <div class="font-black text-amber-300 text-xs">{{ $facultyData['summary']['total_hki'] }}</div>
                                    <div class="text-[10px] text-slate-400 font-semibold mt-0.5">Total HKI</div>
                                </td>
                                <td class="py-4 px-5 text-center align-middle whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-white/10 text-slate-300 border border-white/10 whitespace-nowrap inline-block">
                                        Semua Prodi
                                    </span>
                                </td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartData = @json($facultyData['chart_data']);

    const ctx = document.getElementById('luaranComparisonChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [
                    {
                        label: 'Target Publikasi',
                        data: chartData.target_pub,
                        backgroundColor: '#cbd5e1',
                        borderRadius: 4,
                    },
                    {
                        label: 'Realisasi Publikasi',
                        data: chartData.realisasi_pub,
                        backgroundColor: '#3b82f6',
                        borderRadius: 4,
                    },
                    {
                        label: 'Target HKI',
                        data: chartData.target_hki,
                        backgroundColor: '#fde68a',
                        borderRadius: 4,
                    },
                    {
                        label: 'Realisasi HKI',
                        data: chartData.realisasi_hki,
                        backgroundColor: '#f59e0b',
                        borderRadius: 4,
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
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                        grid: { color: '#f1f5f9' },
                        title: { display: true, text: 'Jumlah Luaran', font: { size: 10, weight: 'bold' } }
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


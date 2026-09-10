@extends('layouts.app')

@section('title', 'Pemeringkatan Usulan & Kuota Anggaran - PRISMA UHN')

@section('content')
<div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <x-sidebar />

    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        <header class="bg-white border-b border-slate-200 py-4 sticky top-0 z-30 shadow-sm print:hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <h1 class="font-extrabold text-xl text-slate-900 leading-tight">Pemeringkatan Usulan & Kuota</h1>
                        <p class="text-xs font-semibold text-slate-500">Simulasi Dynamic Passing Grade Cut-off Berdasarkan Alokasi Pagu Anggaran</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs flex items-center gap-2 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Laporan</span>
                    </button>
                    <span class="px-3 py-1 rounded-full text-xs font-black bg-indigo-100 text-indigo-800 border border-indigo-300">
                        Kepala P3M Portal
                    </span>
                </div>
            </div>
        </header>

        <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between shadow-xs print:hidden">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center justify-between shadow-xs print:hidden">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Filter & Budget Simulation Controls -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4 print:hidden">
                <form action="{{ route('admin.ranking.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                    <div>
                        <label for="total_budget" class="block text-xs font-bold text-slate-700 mb-1">
                            Total Pagu Anggaran (Rp)
                        </label>
                        <input type="number" 
                               id="total_budget" 
                               name="total_budget" 
                               value="{{ (int) $budgetInput }}" 
                               step="1000000"
                               min="0"
                               placeholder="250000000" 
                               class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 font-extrabold text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="scheme_id" class="block text-xs font-bold text-slate-700 mb-1">
                            Filter Skema BIMA
                        </label>
                        <select id="scheme_id" name="scheme_id" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-slate-800 font-medium text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="">-- Semua Skema --</option>
                            @foreach($schemes as $scheme)
                                <option value="{{ $scheme->id }}" {{ request('scheme_id') == $scheme->id ? 'selected' : '' }}>
                                    {{ $scheme->nama_skema }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="faculty_id" class="block text-xs font-bold text-slate-700 mb-1">
                            Filter Fakultas
                        </label>
                        <select id="faculty_id" name="faculty_id" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-slate-800 font-medium text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="">-- Semua Fakultas --</option>
                            @foreach($faculties as $fac)
                                <option value="{{ $fac->id }}" {{ request('faculty_id') == $fac->id ? 'selected' : '' }}>
                                    {{ $fac->nama_fakultas }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-md transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                            <span>Simulasikan Pagu</span>
                        </button>
                        <a href="{{ route('admin.ranking.index') }}" class="px-3.5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- Dynamic Decision Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-5 rounded-3xl bg-white border border-slate-200 shadow-sm">
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Pagu Anggaran Disimulasikan</span>
                    <span class="text-xl font-black text-slate-900 mt-1 block">
                        Rp {{ number_format($budgetInput, 0, ',', '.') }}
                    </span>
                    <p class="text-[11px] text-slate-400 mt-0.5">Alokasi maksimal DIPA institusi</p>
                </div>

                <div class="p-5 rounded-3xl bg-emerald-50 border border-emerald-200 shadow-sm">
                    <span class="text-[10px] font-bold text-emerald-900 uppercase tracking-wider block">Usulan Lolos Kuota</span>
                    <span class="text-2xl font-black text-emerald-700 mt-1 block">
                        {{ $passedCount }} Usulan
                    </span>
                    <p class="text-[11px] text-emerald-600 mt-0.5">Memenuhi batas pagu anggaran</p>
                </div>

                <div class="p-5 rounded-3xl bg-blue-50 border border-blue-200 shadow-sm">
                    <span class="text-[10px] font-bold text-blue-900 uppercase tracking-wider block">Passing Grade Cut-off Score</span>
                    <span class="text-2xl font-black text-blue-700 font-mono mt-1 block">
                        {{ $passingCutoffScore ? number_format($passingCutoffScore, 2) : '-' }}
                    </span>
                    <p class="text-[11px] text-blue-600 mt-0.5">Skor terendah yang terdanai</p>
                </div>

                <div class="p-5 rounded-3xl bg-purple-50 border border-purple-200 shadow-sm">
                    <span class="text-[10px] font-bold text-purple-900 uppercase tracking-wider block">Total Usulan Dinilai</span>
                    <span class="text-2xl font-black text-purple-700 mt-1 block">
                        {{ $rankedList->count() }} Usulan
                    </span>
                    <p class="text-[11px] text-purple-600 mt-0.5">Telah menyelesaikan proses review</p>
                </div>
            </div>

            <!-- Print Header -->
            <div class="hidden print:block border-b-2 border-slate-800 pb-4 mb-4">
                <h2 class="text-xl font-black text-slate-900">LEMBAR KEPUTUSAN PEMERINGKATAN USULAN HIBAH RISET & ABMAS</h2>
                <p class="text-xs text-slate-600 mt-1">Universitas Harkat Negeri - Lembaga Penelitian dan Pengabdian kepada Masyarakat (LPPM)</p>
                <div class="mt-2 text-xs flex gap-6 text-slate-700">
                    <span>Pagu Anggaran: <strong>Rp {{ number_format($budgetInput, 0, ',', '.') }}</strong></span>
                    <span>Total Lolos: <strong>{{ $passedCount }} Usulan</strong></span>
                    <span>Passing Grade: <strong>{{ $passingCutoffScore ? number_format($passingCutoffScore, 2) : '-' }}</strong></span>
                    <span>Tanggal Cetak: <strong>{{ now()->isoFormat('D MMMM Y') }}</strong></span>
                </div>
            </div>

            @php
                $uncontractedPassedIds = $rankedList
                    ->filter(fn($item) => $item['is_passed'] && !in_array($item['usulan']->status, ['Contracted', 'Ongoing']))
                    ->map(fn($item) => $item['usulan']->id)
                    ->values()
                    ->all();
            @endphp

            <!-- Ranked Proposals Table & Mass Winner Assignment -->
            <form id="form-penetapan-pemenang" 
                  action="{{ route('admin.kontrak.penetapan-massal') }}" 
                  method="POST" 
                  x-data="{ 
                      selected: [], 
                      eligibleIds: {{ json_encode($uncontractedPassedIds) }},
                      toggleAll() { 
                          if (this.selected.length === this.eligibleIds.length) { 
                              this.selected = []; 
                          } else { 
                              this.selected = [...this.eligibleIds]; 
                          } 
                      } 
                  }">
                @csrf
                <div class="bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden">
                    <div class="p-6 border-b border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-4 print:hidden">
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-lg font-extrabold text-slate-900">Daftar Peringkat & Kelolosan Pendanaan</h2>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-blue-100 text-blue-800">
                                    EPIC 09 (US-09.1)
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">Usulan diurutkan berdasarkan Skor Akhir Tertinggi & Efisiensi Anggaran (RAB).</p>
                        </div>

                        <div class="flex items-center gap-3">
                            <button type="button" 
                                    @click="toggleAll()" 
                                    class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                <span>Pilih Semua yang Lolos (<span x-text="eligibleIds.length">{{ count($uncontractedPassedIds) }}</span>)</span>
                            </button>

                            <button type="submit" 
                                    :disabled="selected.length === 0" 
                                    :class="selected.length === 0 ? 'opacity-50 cursor-not-allowed bg-slate-300 text-slate-500' : 'bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white shadow-md shadow-emerald-500/20'"
                                    class="px-4 py-2 rounded-xl font-extrabold text-xs transition flex items-center gap-2"
                                    onclick="return confirm('Tetapkan usulan terpilih sebagai Pemenang Hibah dan terbitkan SK Digital P3M?')">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                <span>Tetapkan Pemenang Hibah & Terbitkan SK (<span x-text="selected.length">0</span>)</span>
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 text-slate-500 font-extrabold uppercase border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-4 w-10 text-center print:hidden">
                                        <input type="checkbox" 
                                               @click="toggleAll()" 
                                               :checked="selected.length > 0 && selected.length === eligibleIds.length" 
                                               class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                    </th>
                                    <th class="px-5 py-4 w-14 text-center">Rank</th>
                                    <th class="px-5 py-4">Usulan & Pengusul</th>
                                    <th class="px-5 py-4">Skema Hibah</th>
                                    <th class="px-5 py-4 text-center">Skor R1 & R2</th>
                                    <th class="px-5 py-4 text-center">Skor Akhir</th>
                                    <th class="px-5 py-4 text-right">RAB Usulan</th>
                                    <th class="px-5 py-4 text-right">Akumulasi Biaya</th>
                                    <th class="px-5 py-4 text-center">Status Kelolosan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                @forelse($rankedList as $index => $item)
                                    @php
                                        $u = $item['usulan'];
                                        $isPassed = $item['is_passed'];
                                        $isContracted = in_array($u->status, ['Contracted', 'Ongoing'], true);
                                        $showCutoffAfterThis = ($isPassed && isset($rankedList[$index + 1]) && !$rankedList[$index + 1]['is_passed']);
                                    @endphp
                                    <tr class="hover:bg-slate-50/80 transition-colors {{ $isContracted ? 'bg-indigo-50/20' : ($isPassed ? 'bg-emerald-50/20' : 'bg-slate-50/40 text-slate-500') }}">
                                        <!-- Selection Checkbox -->
                                        <td class="px-4 py-4 text-center print:hidden">
                                            @if($isContracted)
                                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-700" title="Pemenang Hibah (SK Telah Diterbitkan)">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                </span>
                                            @else
                                                <input type="checkbox" 
                                                       name="usulan_ids[]" 
                                                       value="{{ $u->id }}" 
                                                       x-model="selected" 
                                                       class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 w-4 h-4 cursor-pointer">
                                            @endif
                                        </td>

                                        <!-- Rank Number -->
                                        <td class="px-5 py-4 text-center">
                                            <span class="w-8 h-8 mx-auto rounded-xl font-black text-xs flex items-center justify-center {{ $isPassed ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-200 text-slate-600' }}">
                                                #{{ $item['rank'] }}
                                            </span>
                                        </td>

                                        <!-- Usulan & Author Info -->
                                        <td class="px-5 py-4 max-w-sm">
                                            <span class="font-mono font-bold text-blue-700 text-[11px] block">
                                                {{ $u->kode_usulan }}
                                            </span>
                                            <p class="font-extrabold text-slate-900 text-xs leading-snug line-clamp-2 mt-0.5">
                                                {{ $u->judul_usulan }}
                                            </p>
                                            <div class="text-[11px] text-slate-500 mt-1 flex items-center gap-1.5 flex-wrap">
                                                <span>{{ $u->pengusul->name ?? '-' }}</span>
                                                <span>&bull;</span>
                                                <span class="text-blue-600 font-semibold">{{ $u->pengusul->fakultas->nama_fakultas ?? '-' }}</span>
                                                <span>&bull;</span>
                                                <span>{{ $u->pengusul->prodi->nama_prodi ?? '-' }}</span>
                                            </div>
                                        </td>

                                        <!-- Skema -->
                                        <td class="px-5 py-4 whitespace-nowrap text-slate-700 font-semibold">
                                            {{ $u->skema->nama_skema ?? '-' }}
                                        </td>

                                        <!-- Reviewer Scores -->
                                        <td class="px-5 py-4 text-center whitespace-nowrap text-[11px]">
                                            <div class="flex items-center justify-center gap-1.5 font-mono">
                                                <span title="Reviewer 1" class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-800 border border-slate-200 font-bold">
                                                    R1: {{ number_format($item['skor_r1'] ?? 0, 1) }}
                                                </span>
                                                <span title="Reviewer 2" class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-800 border border-slate-200 font-bold">
                                                    R2: {{ number_format($item['skor_r2'] ?? 0, 1) }}
                                                </span>
                                            </div>
                                            @if($item['is_adjudication'] && $item['skor_r3'])
                                                <div class="mt-1 font-mono text-[10px] text-purple-700 font-bold">
                                                    Penengah (R3): {{ number_format($item['skor_r3'], 1) }}
                                                </div>
                                            @endif
                                        </td>

                                        <!-- Final Score -->
                                        <td class="px-5 py-4 text-center whitespace-nowrap">
                                            <span class="font-mono font-black text-base {{ $isPassed ? 'text-blue-700' : 'text-slate-600' }}">
                                                {{ number_format($item['skor_akhir'], 2) }}
                                            </span>
                                            <span class="text-[10px] text-slate-400 block">/ 700.00</span>
                                        </td>

                                        <!-- RAB -->
                                        <td class="px-5 py-4 text-right whitespace-nowrap font-mono font-bold text-slate-800">
                                            Rp {{ number_format($item['total_rab'], 0, ',', '.') }}
                                        </td>

                                        <!-- Cumulative Budget -->
                                        <td class="px-5 py-4 text-right whitespace-nowrap font-mono font-bold {{ $item['cumulative_budget'] <= $budgetInput ? 'text-emerald-700' : 'text-rose-600' }}">
                                            Rp {{ number_format($item['cumulative_budget'], 0, ',', '.') }}
                                        </td>

                                        <!-- Pass/Fail Status Badge -->
                                        <td class="px-5 py-4 text-center whitespace-nowrap">
                                            @if($u->status === 'Ongoing')
                                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[11px] font-black bg-teal-100 text-teal-800 border border-teal-300">
                                                    <svg class="w-3.5 h-3.5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    Dana Cair (Ongoing)
                                                </span>
                                                @if($u->kontrak)
                                                    <a href="{{ route('kontrak.download-pdf', $u->kontrak->id) }}" target="_blank" class="block text-[10px] font-bold text-blue-600 hover:underline mt-1">Unduh SPK</a>
                                                @endif
                                            @elseif($u->status === 'Contracted')
                                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[11px] font-black bg-indigo-100 text-indigo-800 border border-indigo-300">
                                                    <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    Pemenang Hibah (SK Terbit)
                                                </span>
                                                @if($u->kontrak)
                                                    <a href="{{ route('kontrak.download-pdf', $u->kontrak->id) }}" target="_blank" class="block text-[10px] font-bold text-blue-600 hover:underline mt-1">Unduh SPK</a>
                                                @endif
                                            @elseif($isPassed)
                                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[11px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    Lolos Pendanaan
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[11px] font-bold bg-slate-200 text-slate-600 border border-slate-300">
                                                    Di Luar Kuota Pagu
                                                </span>
                                            @endif
                                        </td>
                                    </tr>

                                    <!-- Dynamic Passing Grade Cut-off Line -->
                                    @if($showCutoffAfterThis)
                                        <tr class="bg-emerald-600 text-white font-extrabold">
                                            <td colspan="9" class="px-6 py-2.5 text-center text-xs tracking-wider shadow-inner">
                                                <div class="flex items-center justify-center gap-3">
                                                    <div class="h-0.5 flex-1 bg-white/40"></div>
                                                    <span class="flex items-center gap-2">
                                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        BATAS PASSING GRADE PENDANAAN (CUT-OFF LINE) &mdash; PAGU ANGGARAN RP {{ number_format($budgetInput, 0, ',', '.') }} TERCAPAI
                                                    </span>
                                                    <div class="h-0.5 flex-1 bg-white/40"></div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-6 py-12 text-center text-slate-400">
                                            <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                            <p class="font-bold text-slate-600">Belum ada usulan yang selesai dinilai.</p>
                                            <p class="text-xs text-slate-400 mt-1">Proposal yang telah melalui penilaian reviewer akan secara otomatis masuk ke dalam tabel pemeringkatan ini.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>

            <!-- Signature block for institutional official print -->
            <div class="hidden print:flex justify-between items-end pt-12 text-xs text-slate-800">
                <div class="space-y-1">
                    <p>Mengetahui,</p>
                    <p class="font-bold">Sekretaris LPPM UHN</p>
                    <div class="h-20"></div>
                    <p class="font-bold underline">(.........................................................)</p>
                    <p>NIP/NIDN.</p>
                </div>
                <div class="space-y-1 text-right">
                    <p>Medan, {{ now()->isoFormat('D MMMM Y') }}</p>
                    <p class="font-bold">Kepala LPPM UHN</p>
                    <div class="h-20"></div>
                    <p class="font-bold underline">(.........................................................)</p>
                    <p>NIP/NIDN.</p>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection


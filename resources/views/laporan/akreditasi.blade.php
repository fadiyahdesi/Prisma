@extends('layouts.app')

@section('title', 'Pelaporan Akreditasi BAN-PT & LAM - PRISMA UHN')

@section('content')
<div x-data="{ sidebarOpen: false, activeTab: '{{ $tab ?? 'penelitian' }}' }" class="min-h-screen bg-slate-100 text-slate-800 flex">
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
                            <h1 class="font-extrabold text-xl text-slate-900 leading-tight">Pelaporan Akreditasi BAN-PT & LAM</h1>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black bg-emerald-100 text-emerald-800 border border-emerald-200">
                                Standar LKPS / LED
                            </span>
                        </div>
                        <p class="text-xs font-semibold text-slate-500">Kompilasi Otomatis Tabel 3.b.1 (Penelitian), 3.b.2 (PkM), 3.b.3 (Publikasi), & 3.b.4 (HKI Paten)</p>
                    </div>
                </div>
                {{-- Export Actions --}}
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('laporan.akreditasi.excel', array_merge($filters, ['tab' => 'all'])) }}" 
                       class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center gap-2 shadow-xs transition"
                       title="Download dokumen Excel .xlsx multi-sheet berstandar BAN-PT">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Ekspor Excel (.xlsx)</span>
                    </a>
                    <a href="{{ route('laporan.akreditasi.pdf', $filters) }}" 
                       class="px-3.5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs flex items-center gap-2 shadow-xs transition"
                       title="Cetak format PDF instrumen siap tanda tangan / QR Code">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <span>Cetak PDF Resmi</span>
                    </a>
                </div>
            </div>
        </header>

        <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            {{-- Filter Bar --}}
            {{-- Filter Bar --}}
            <div class="bg-white rounded-3xl border border-slate-200/90 p-5 shadow-xs">
                <form action="{{ route('laporan.akreditasi') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                    <div>
                        <label for="fakultas_id" class="block text-xs font-bold text-slate-700 mb-1.5">Fakultas:</label>
                        <select name="fakultas_id" id="fakultas_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:outline-hidden bg-slate-50/50">
                            <option value="">-- Seluruh Universitas --</option>
                            @foreach($fakultasList as $f)
                                <option value="{{ $f->id_fakultas }}" {{ ($filters['fakultas_id'] ?? '') == $f->id_fakultas ? 'selected' : '' }}>
                                    {{ $f->nama_fakultas }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="prodi_id" class="block text-xs font-bold text-slate-700 mb-1.5">Program Studi:</label>
                        <select name="prodi_id" id="prodi_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:outline-hidden bg-slate-50/50">
                            <option value="">-- Semua Program Studi --</option>
                            @foreach($prodiList as $pr)
                                <option value="{{ $pr->id_prodi }}" {{ ($filters['prodi_id'] ?? '') == $pr->id_prodi ? 'selected' : '' }}>
                                    {{ $pr->jenjang }} {{ $pr->nama_prodi }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="tahun" class="block text-xs font-bold text-slate-700 mb-1.5">Tahun Anggaran / Terbit:</label>
                        <select name="tahun" id="tahun" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:outline-hidden bg-slate-50/50">
                            <option value="">-- Semua Tahun --</option>
                            @for($y = (int)date('Y') + 1; $y >= (int)date('Y') - 4; $y--)
                                <option value="{{ $y }}" {{ ($filters['tahun'] ?? '') == $y ? 'selected' : '' }}>
                                    Tahun {{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition shadow-xs">
                            Terapkan Filter
                        </button>
                        <a href="{{ route('laporan.akreditasi') }}" class="px-3.5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition" title="Reset filter">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- Main Tabbed Container --}}
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
                {{-- Modern Segmented Tab Buttons --}}
                <div class="p-2 sm:p-2.5 bg-slate-50/80 border-b border-slate-200">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                        <!-- Tab 1: Penelitian -->
                        <button type="button" @click="activeTab = 'penelitian'"
                                :class="activeTab === 'penelitian' ? 'bg-white text-indigo-700 shadow-xs border-indigo-200 font-extrabold' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 border-transparent font-bold'"
                                class="px-3.5 py-3 rounded-2xl border text-xs flex items-center justify-between gap-2 transition duration-150 text-left">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0"
                                     :class="activeTab === 'penelitian' ? 'bg-indigo-50 text-indigo-700' : 'bg-slate-200/60 text-slate-500'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <span class="block font-black text-xs truncate">Tabel 3.b.1: Penelitian DTPS</span>
                                    <span class="block text-[10px] font-semibold text-slate-400">Riset Terpadu</span>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-black shrink-0"
                                  :class="activeTab === 'penelitian' ? 'bg-indigo-100 text-indigo-800' : 'bg-slate-100 text-slate-600'">
                                {{ count($tables['penelitian']) }}
                            </span>
                        </button>

                        <!-- Tab 2: PkM -->
                        <button type="button" @click="activeTab = 'pkm'"
                                :class="activeTab === 'pkm' ? 'bg-white text-emerald-700 shadow-xs border-emerald-200 font-extrabold' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 border-transparent font-bold'"
                                class="px-3.5 py-3 rounded-2xl border text-xs flex items-center justify-between gap-2 transition duration-150 text-left">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0"
                                     :class="activeTab === 'pkm' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-200/60 text-slate-500'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <span class="block font-black text-xs truncate">Tabel 3.b.2: PkM DTPS</span>
                                    <span class="block text-[10px] font-semibold text-slate-400">Pengabdian Masyarakat</span>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-black shrink-0"
                                  :class="activeTab === 'pkm' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600'">
                                {{ count($tables['pkm']) }}
                            </span>
                        </button>

                        <!-- Tab 3: Publikasi -->
                        <button type="button" @click="activeTab = 'publikasi'"
                                :class="activeTab === 'publikasi' ? 'bg-white text-sky-700 shadow-xs border-sky-200 font-extrabold' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 border-transparent font-bold'"
                                class="px-3.5 py-3 rounded-2xl border text-xs flex items-center justify-between gap-2 transition duration-150 text-left">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0"
                                     :class="activeTab === 'publikasi' ? 'bg-sky-50 text-sky-700' : 'bg-slate-200/60 text-slate-500'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <span class="block font-black text-xs truncate">Tabel 3.b.3: Publikasi Ilmiah DTPS</span>
                                    <span class="block text-[10px] font-semibold text-slate-400">Scopus &amp; SINTA</span>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-black shrink-0"
                                  :class="activeTab === 'publikasi' ? 'bg-sky-100 text-sky-800' : 'bg-slate-100 text-slate-600'">
                                {{ count($tables['publikasi']) }}
                            </span>
                        </button>

                        <!-- Tab 4: HKI -->
                        <button type="button" @click="activeTab = 'hki'"
                                :class="activeTab === 'hki' ? 'bg-white text-purple-700 shadow-xs border-purple-200 font-extrabold' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 border-transparent font-bold'"
                                class="px-3.5 py-3 rounded-2xl border text-xs flex items-center justify-between gap-2 transition duration-150 text-left">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0"
                                     :class="activeTab === 'hki' ? 'bg-purple-50 text-purple-700' : 'bg-slate-200/60 text-slate-500'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <span class="block font-black text-xs truncate">Tabel 3.b.4: HKI &amp; Paten DTPS</span>
                                    <span class="block text-[10px] font-semibold text-slate-400">Terdaftar DJKI</span>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-black shrink-0"
                                  :class="activeTab === 'hki' ? 'bg-purple-100 text-purple-800' : 'bg-slate-100 text-slate-600'">
                                {{ count($tables['hki']) }}
                            </span>
                        </button>
                    </div>
                </div>

                {{-- Tab 1: Tabel 3.b.1 Penelitian DTPS --}}
                <div x-show="activeTab === 'penelitian'">
                    <div class="p-6 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4 bg-slate-50/40">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-black shadow-xs shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="font-extrabold text-base text-slate-900">Tabel 3.b.1: Rekapitulasi Penelitian DTPS</h3>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-indigo-100 text-indigo-800 border border-indigo-200">
                                        Standar BAN-PT / LAM
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 font-medium">Rekapitulasi aktivitas penelitian Dosen Tetap Program Studi (DTPS) yang didanai melalui skema internal maupun nasional</p>
                            </div>
                        </div>
                        <span class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs border border-slate-200">
                            Total: <strong class="text-slate-900 font-black">{{ count($tables['penelitian']) }}</strong> Judul Penelitian
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-extrabold uppercase tracking-wider text-[11px]">
                                    <th class="py-3.5 px-4 w-12 text-center">No</th>
                                    <th class="py-3.5 px-4">Nama Dosen &amp; Unit</th>
                                    <th class="py-3.5 px-5">Judul Penelitian</th>
                                    <th class="py-3.5 px-4">Skema &amp; Sumber Dana</th>
                                    <th class="py-3.5 px-4 text-center">Tahun</th>
                                    <th class="py-3.5 px-5 text-right">Pagu Disetujui</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($tables['penelitian'] as $item)
                                <tr class="hover:bg-slate-50/90 transition-colors">
                                    <td class="py-4 px-4 text-center font-bold text-slate-400 text-xs">{{ $item['no'] }}</td>
                                    <td class="py-4 px-4">
                                        <div class="font-extrabold text-slate-900 text-xs">{{ $item['nama_dosen'] }}</div>
                                        <div class="text-[11px] text-slate-500 font-medium flex items-center gap-1.5 mt-0.5">
                                            <span class="text-slate-400">NIDN: {{ $item['nidn'] }}</span>
                                            <span class="text-slate-300">&bull;</span>
                                            <span class="text-slate-600 font-semibold">{{ $item['prodi'] }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-5">
                                        <div class="font-bold text-slate-800 text-xs leading-relaxed max-w-lg min-w-[260px]">
                                            {{ $item['judul'] }}
                                        </div>
                                        <div class="text-[10px] text-slate-400 mt-1">Fakultas: {{ $item['fakultas'] }}</div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <span class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 font-extrabold text-xs border border-indigo-200/60 inline-block">
                                            {{ $item['skema'] }}
                                        </span>
                                        <div class="text-[11px] text-slate-500 font-medium mt-1">
                                            {{ $item['sumber_dana'] ?? 'Internal UHN' }}
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-extrabold text-xs border border-slate-200/60">
                                            {{ $item['tahun'] }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-5 text-right">
                                        <div class="font-black text-slate-900 text-xs whitespace-nowrap">
                                            Rp {{ number_format($item['pagu'], 0, ',', '.') }}
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center">
                                        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <p class="text-xs font-bold text-slate-700">Tidak ada data penelitian yang memenuhi filter</p>
                                        <p class="text-[11px] text-slate-400 mt-1">Silakan sesuaikan pilihan fakultas, program studi, atau tahun anggaran.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if(count($tables['penelitian']) > 0)
                            <tfoot>
                                <tr class="bg-slate-900 text-white font-extrabold text-xs">
                                    <td colspan="4" class="py-4 px-5 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                            <span class="tracking-wide uppercase text-[11px]">TOTAL ANGGARAN PENELITIAN DTPS ({{ count($tables['penelitian']) }} JUDUL)</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <span class="px-2.5 py-1 rounded-lg bg-white/10 text-white font-black text-xs border border-white/10">
                                            {{ count($tables['penelitian']) }} Riset
                                        </span>
                                    </td>
                                    <td class="py-4 px-5 text-right font-black text-emerald-400 whitespace-nowrap text-xs">
                                        Rp {{ number_format(array_sum(array_column($tables['penelitian'], 'pagu')), 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>

                {{-- Tab 2: Tabel 3.b.2 PkM DTPS --}}
                <div x-show="activeTab === 'pkm'" x-cloak>
                    <div class="p-6 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4 bg-slate-50/40">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-emerald-600 text-white flex items-center justify-center font-black shadow-xs shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="font-extrabold text-base text-slate-900">Tabel 3.b.2: Rekapitulasi Pengabdian kepada Masyarakat (PkM) DTPS</h3>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        Standar BAN-PT / LAM
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 font-medium">Kegiatan PkM DTPS yang melibatkan mitra masyarakat, UMKM, atau sektor industri binaan UHN</p>
                            </div>
                        </div>
                        <span class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs border border-slate-200">
                            Total: <strong class="text-slate-900 font-black">{{ count($tables['pkm']) }}</strong> Kegiatan PkM
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-extrabold uppercase tracking-wider text-[11px]">
                                    <th class="py-3.5 px-4 w-12 text-center">No</th>
                                    <th class="py-3.5 px-4">Nama Dosen &amp; Unit</th>
                                    <th class="py-3.5 px-5">Judul Kegiatan PkM</th>
                                    <th class="py-3.5 px-4">Mitra Sasaran</th>
                                    <th class="py-3.5 px-4 text-center">Tahun</th>
                                    <th class="py-3.5 px-5 text-right">Dana PkM (Rp)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($tables['pkm'] as $item)
                                <tr class="hover:bg-slate-50/90 transition-colors">
                                    <td class="py-4 px-4 text-center font-bold text-slate-400 text-xs">{{ $item['no'] }}</td>
                                    <td class="py-4 px-4">
                                        <div class="font-extrabold text-slate-900 text-xs">{{ $item['nama_dosen'] }}</div>
                                        <div class="text-[11px] text-slate-500 font-medium flex items-center gap-1.5 mt-0.5">
                                            <span class="text-slate-400">NIDN: {{ $item['nidn'] }}</span>
                                            <span class="text-slate-300">&bull;</span>
                                            <span class="text-slate-600 font-semibold">{{ $item['prodi'] }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-5">
                                        <div class="font-bold text-slate-800 text-xs leading-relaxed max-w-lg min-w-[260px]">
                                            {{ $item['judul'] }}
                                        </div>
                                        <div class="text-[10px] text-slate-400 mt-1">Fakultas: {{ $item['fakultas'] }}</div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 font-semibold text-xs border border-emerald-200/60">
                                            <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                            <span>{{ $item['mitra'] }}</span>
                                        </div>
                                        <div class="text-[10px] text-slate-400 font-medium mt-1">Sumber: {{ $item['sumber_dana'] ?? 'Internal UHN' }}</div>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-extrabold text-xs border border-slate-200/60">
                                            {{ $item['tahun'] }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-5 text-right">
                                        <div class="font-black text-slate-900 text-xs whitespace-nowrap">
                                            Rp {{ number_format($item['pagu'], 0, ',', '.') }}
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center">
                                        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <p class="text-xs font-bold text-slate-700">Tidak ada data PkM yang memenuhi filter</p>
                                        <p class="text-[11px] text-slate-400 mt-1">Silakan sesuaikan pilihan fakultas, program studi, atau tahun anggaran.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if(count($tables['pkm']) > 0)
                            <tfoot>
                                <tr class="bg-slate-900 text-white font-extrabold text-xs">
                                    <td colspan="4" class="py-4 px-5 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                            <span class="tracking-wide uppercase text-[11px]">TOTAL ANGGARAN PKM DTPS ({{ count($tables['pkm']) }} KEGIATAN)</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <span class="px-2.5 py-1 rounded-lg bg-white/10 text-white font-black text-xs border border-white/10">
                                            {{ count($tables['pkm']) }} Mitra
                                        </span>
                                    </td>
                                    <td class="py-4 px-5 text-right font-black text-emerald-400 whitespace-nowrap text-xs">
                                        Rp {{ number_format(array_sum(array_column($tables['pkm'], 'pagu')), 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>

                {{-- Tab 3: Tabel 3.b.3 Publikasi Ilmiah DTPS --}}
                <div x-show="activeTab === 'publikasi'" x-cloak>
                    <div class="p-6 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4 bg-slate-50/40">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-sky-600 text-white flex items-center justify-center font-black shadow-xs shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="font-extrabold text-base text-slate-900">Tabel 3.b.3: Publikasi Artikel Ilmiah DTPS</h3>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-sky-100 text-sky-800 border border-sky-200">
                                        Standar BAN-PT / LAM
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 font-medium">Artikel ilmiah pada jurnal nasional terakreditasi SINTA dan jurnal internasional bereputasi (Scopus / WoS)</p>
                            </div>
                        </div>
                        <span class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs border border-slate-200">
                            Total: <strong class="text-slate-900 font-black">{{ count($tables['publikasi']) }}</strong> Artikel Ilmiah
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-extrabold uppercase tracking-wider text-[11px]">
                                    <th class="py-3.5 px-4 w-12 text-center">No</th>
                                    <th class="py-3.5 px-4">Nama Dosen &amp; Unit</th>
                                    <th class="py-3.5 px-5">Judul Artikel Ilmiah</th>
                                    <th class="py-3.5 px-4">Nama Jurnal &amp; Terbitan</th>
                                    <th class="py-3.5 px-3 text-center">Kategori Akreditasi</th>
                                    <th class="py-3.5 px-3 text-center">Tahun</th>
                                    <th class="py-3.5 px-4">DOI / URL</th>
                                    <th class="py-3.5 px-3 text-center">Sitasi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($tables['publikasi'] as $item)
                                <tr class="hover:bg-slate-50/90 transition-colors">
                                    <td class="py-4 px-4 text-center font-bold text-slate-400 text-xs">{{ $item['no'] }}</td>
                                    <td class="py-4 px-4">
                                        <div class="font-extrabold text-slate-900 text-xs">{{ $item['nama_dosen'] }}</div>
                                        <div class="text-[11px] text-slate-500 font-medium flex items-center gap-1 mt-0.5">
                                            <span>NIDN: {{ $item['nidn'] }}</span>
                                            <span class="text-slate-300">&bull;</span>
                                            <span class="text-slate-600 font-semibold">{{ $item['prodi'] }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-5">
                                        <div class="font-bold text-slate-900 text-xs leading-relaxed max-w-md min-w-[240px]">
                                            {{ $item['judul'] }}
                                        </div>
                                        <div class="text-[10px] text-slate-400 mt-1">Fakultas: {{ $item['fakultas'] }}</div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="font-semibold text-slate-800 text-xs italic">{{ $item['nama_jurnal'] }}</div>
                                        <div class="text-[10px] text-slate-400 font-medium mt-0.5">
                                            ISSN: {{ $item['issn'] }} &bull; Vol/No: {{ $item['volume_nomor'] }}
                                        </div>
                                    </td>
                                    <td class="py-4 px-3 text-center">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black {{ str_contains($item['peringkat'], 'Scopus') ? 'bg-amber-50 text-amber-800 border border-amber-200/80' : 'bg-sky-50 text-sky-800 border border-sky-200/80' }}">
                                            {{ $item['peringkat'] }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-3 text-center">
                                        <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-extrabold text-xs border border-slate-200/60">
                                            {{ $item['tahun'] }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4">
                                        @if($item['doi'])
                                            <a href="{{ str_starts_with($item['doi'], 'http') ? $item['doi'] : 'https://doi.org/' . $item['doi'] }}" 
                                               target="_blank" 
                                               class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold text-[11px] transition max-w-[160px] truncate group border border-indigo-200/50"
                                               title="{{ $item['doi'] }}">
                                                <svg class="w-3.5 h-3.5 shrink-0 text-indigo-500 group-hover:text-indigo-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                <span class="truncate">{{ $item['doi'] }}</span>
                                            </a>
                                        @else
                                            <span class="text-slate-400 italic text-[11px]">-</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-3 text-center">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-black bg-slate-100 text-slate-800 border border-slate-200/70">
                                            {{ $item['sitasi'] }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="py-12 text-center">
                                        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <p class="text-xs font-bold text-slate-700">Tidak ada data publikasi yang memenuhi filter</p>
                                        <p class="text-[11px] text-slate-400 mt-1">Silakan sesuaikan pilihan fakultas, program studi, atau tahun terbit.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if(count($tables['publikasi']) > 0)
                            <tfoot>
                                <tr class="bg-slate-900 text-white font-extrabold text-xs">
                                    <td colspan="4" class="py-4 px-5 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                            <span class="tracking-wide uppercase text-[11px]">TOTAL ARTIKEL ILMIAH DTPS:</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-3 text-center">
                                        <span class="px-2 py-0.5 rounded-md bg-white/10 text-white font-bold text-[11px]">
                                            {{ count(array_filter($tables['publikasi'], fn($p) => str_contains($p['peringkat'], 'Scopus'))) }} Scopus
                                        </span>
                                    </td>
                                    <td class="py-4 px-3 text-center text-slate-300 font-bold">
                                        {{ count($tables['publikasi']) }} Art
                                    </td>
                                    <td class="py-4 px-4 text-slate-400 text-[11px] font-medium">
                                        Total Sitasi:
                                    </td>
                                    <td class="py-4 px-3 text-center text-emerald-400 font-black text-xs">
                                        {{ array_sum(array_column($tables['publikasi'], 'sitasi')) }}
                                    </td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>

                {{-- Tab 4: Tabel 3.b.4 HKI & Paten DTPS --}}
                <div x-show="activeTab === 'hki'" x-cloak>
                    <div class="p-6 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4 bg-slate-50/40">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-purple-600 text-white flex items-center justify-center font-black shadow-xs shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="font-extrabold text-base text-slate-900">Tabel 3.b.4: Rekapitulasi HKI &amp; Paten DTPS</h3>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-purple-100 text-purple-800 border border-purple-200">
                                        Standar BAN-PT / LAM
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 font-medium">Perolehan Hak Kekayaan Intelektual (Paten, Hak Cipta, Desain Industri) yang terverifikasi resmi DJKI Kemenkumham</p>
                            </div>
                        </div>
                        <span class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs border border-slate-200">
                            Total: <strong class="text-slate-900 font-black">{{ count($tables['hki']) }}</strong> HKI Terdaftar
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-extrabold uppercase tracking-wider text-[11px]">
                                    <th class="py-3.5 px-4 w-12 text-center">No</th>
                                    <th class="py-3.5 px-4">Inventor / Pencipta</th>
                                    <th class="py-3.5 px-5">Judul Luaran HKI</th>
                                    <th class="py-3.5 px-3 text-center">Jenis HKI</th>
                                    <th class="py-3.5 px-4">Status &amp; Nomor DJKI</th>
                                    <th class="py-3.5 px-3 text-center">Tahun Terbit</th>
                                    <th class="py-3.5 px-4">Pemegang Hak</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($tables['hki'] as $item)
                                <tr class="hover:bg-slate-50/90 transition-colors">
                                    <td class="py-4 px-4 text-center font-bold text-slate-400 text-xs">{{ $item['no'] }}</td>
                                    <td class="py-4 px-4">
                                        <div class="font-extrabold text-slate-900 text-xs">{{ $item['inventor'] }}</div>
                                        <div class="text-[11px] text-slate-500 font-medium flex items-center gap-1 mt-0.5">
                                            <span>NIDN: {{ $item['nidn'] }}</span>
                                            <span class="text-slate-300">&bull;</span>
                                            <span class="text-slate-600 font-semibold">{{ $item['prodi'] }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-5">
                                        <div class="font-bold text-slate-900 text-xs leading-relaxed max-w-md min-w-[240px]">
                                            {{ $item['judul'] }}
                                        </div>
                                        <div class="text-[10px] text-slate-400 mt-1">Fakultas: {{ $item['fakultas'] }}</div>
                                    </td>
                                    <td class="py-4 px-3 text-center">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black {{ $item['jenis'] === 'Paten' ? 'bg-indigo-50 text-indigo-800 border border-indigo-200' : ($item['jenis'] === 'Hak Cipta' ? 'bg-purple-50 text-purple-800 border border-purple-200' : 'bg-amber-50 text-amber-800 border border-amber-200') }}">
                                            {{ $item['jenis'] }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="space-y-1">
                                            <div class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                                                <span class="px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[10px] font-black border border-emerald-200/80">Sertifikat</span>
                                                <span>{{ $item['nomor_sertifikat'] }}</span>
                                            </div>
                                            <div class="text-[11px] text-slate-500 flex items-center gap-1.5">
                                                <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 text-[10px] font-semibold border border-slate-200/60">Permohonan</span>
                                                <span>{{ $item['nomor_permohonan'] }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-3 text-center">
                                        <div class="font-bold text-slate-800 text-xs">{{ $item['tanggal_terbit'] }}</div>
                                        <div class="text-[10px] text-slate-400 font-medium">Thn {{ $item['tahun'] }}</div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <span class="font-semibold text-slate-700 text-xs block truncate max-w-[200px]" title="{{ $item['pemegang_hak'] }}">
                                            {{ $item['pemegang_hak'] ?: 'Universitas HKBP Nommensen' }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center">
                                        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <p class="text-xs font-bold text-slate-700">Tidak ada data HKI yang memenuhi filter</p>
                                        <p class="text-[11px] text-slate-400 mt-1">Silakan sesuaikan pilihan fakultas, program studi, atau tahun terbit.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if(count($tables['hki']) > 0)
                            <tfoot>
                                <tr class="bg-slate-900 text-white font-extrabold text-xs">
                                    <td colspan="3" class="py-4 px-5 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                            <span class="tracking-wide uppercase text-[11px]">TOTAL HKI DTPS TERDAFTAR:</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-3 text-center">
                                        <span class="px-2.5 py-1 rounded-lg bg-purple-500/20 text-purple-200 font-bold text-xs">
                                            {{ count($tables['hki']) }} Terdata
                                        </span>
                                    </td>
                                    <td colspan="3" class="py-4 px-5 text-right text-emerald-400 font-black text-xs">
                                        100% Terverifikasi DJKI Kemenkumham
                                    </td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection


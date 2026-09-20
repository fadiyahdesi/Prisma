@extends('layouts.app')

@section('title', 'Penilaian Proposal Substantif - PRISMA UHN')

@section('content')
<div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <x-sidebar />

    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        <header class="bg-white border-b border-slate-200 py-4 sticky top-0 z-30 shadow-sm">
            <div class="w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <h1 class="font-extrabold text-xl text-slate-900 leading-tight">Penilaian Proposal Riset & Abmas</h1>
                        <p class="text-xs font-semibold text-slate-500">Portal Telaah Substansi Ilmiah Standar BIMA (Double-Blind Review)</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-black bg-purple-100 text-purple-800 border border-purple-300">
                        Double-Blind Protocol Active
                    </span>
                </div>
            </div>
        </header>

        <main class="flex-grow w-full px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            @if(session('success'))
                <div class="p-4.5 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-sm font-bold flex items-center gap-3 shadow-sm">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4.5 rounded-2xl bg-red-50 border border-red-300 text-red-900 text-sm font-bold flex items-center gap-3 shadow-sm">
                    <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Reviewer Summary Stat Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div class="p-5 rounded-3xl bg-white border border-slate-200 shadow-sm">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Total Ditugaskan</span>
                    <span class="text-2xl font-black text-slate-900 mt-1 block">{{ $stats['total_assigned'] }} Usulan</span>
                </div>
                <div class="p-5 rounded-3xl bg-amber-50 border border-amber-200 shadow-sm">
                    <span class="text-xs font-bold text-amber-900 uppercase tracking-wider block">Menunggu Penilaian</span>
                    <span class="text-2xl font-black text-amber-700 mt-1 block">{{ $stats['pending'] }} Usulan</span>
                </div>
                <div class="p-5 rounded-3xl bg-emerald-50 border border-emerald-200 shadow-sm">
                    <span class="text-xs font-bold text-emerald-900 uppercase tracking-wider block">Selesai Dinilai (Locked)</span>
                    <span class="text-2xl font-black text-emerald-700 mt-1 block">{{ $stats['completed'] }} Usulan</span>
                </div>
            </div>

            <!-- Double-Blind Notice Banner -->
            <div class="p-4.5 rounded-2xl bg-blue-50 border border-blue-200 flex items-center gap-3 text-xs text-blue-900 font-semibold">
                <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span><strong>Protokol Penilaian Anonim (Double-Blind)</strong>: Identitas pengusul (nama dosen, NIDN, prodi, dan tim) disamarkan untuk menjaga objektivitas dan integritas telaah ilmiah.</span>
            </div>

            <!-- Assigned Proposals Table -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden">
                <div class="p-6 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-extrabold text-slate-900">Daftar Usulan Ditugaskan</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Silakan isi borang penilaian skala 1-7 sebelum batas tenggat berakhir.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50 text-slate-500 font-extrabold uppercase border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4">Kode Usulan</th>
                                <th class="px-6 py-4">Judul Usulan & Bidang Fokus</th>
                                <th class="px-6 py-4">Skema Hibah</th>
                                <th class="px-6 py-4">Peran Telaah</th>
                                <th class="px-6 py-4">Status & Nilai</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @forelse($assignments as $assignment)
                                @php
                                    $u = $assignment->usulan;
                                    $isCompleted = $assignment->status_penugasan === 'completed';
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="px-6 py-4 font-mono font-bold text-blue-700 whitespace-nowrap">
                                        {{ $u->kode_usulan }}
                                    </td>
                                    <td class="px-6 py-4 max-w-md">
                                        <p class="font-extrabold text-slate-900 text-sm leading-snug line-clamp-2">
                                            {{ $u->judul_usulan }}
                                        </p>
                                        <div class="flex items-center gap-2 mt-1 text-[11px] text-slate-500">
                                            <span>Fokus: <strong>{{ $u->fokus_rirn ?? '-' }}</strong></span>
                                            <span>&bull;</span>
                                            <span>Rumpun: <strong>{{ $u->rumpun_ilmu_level_1 ?? '-' }}</strong></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-slate-100 text-slate-800 border border-slate-300">
                                            {{ $u->skema->kode_skema ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($assignment->peran_reviewer === 'adjudicator')
                                            <span class="px-2.5 py-1 rounded-lg text-[11px] font-black bg-rose-100 text-rose-800 border border-rose-300">
                                                Reviewer 3 (Penengah)
                                            </span>
                                        @elseif($assignment->peran_reviewer === 'reviewer_1')
                                            <span class="px-2.5 py-1 rounded-lg text-[11px] font-black bg-blue-100 text-blue-800 border border-blue-300">
                                                Reviewer 1
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-lg text-[11px] font-black bg-purple-100 text-purple-800 border border-purple-300">
                                                Reviewer 2
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($isCompleted)
                                            <div>
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                    Selesai Dinilai
                                                </span>
                                                <p class="font-mono font-extrabold text-emerald-900 mt-0.5">Skor: {{ $assignment->penilaian->total_skor ?? 0 }} / 700</p>
                                            </div>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                Belum Dinilai
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        <a href="{{ route('reviewer.penilaian.show', $assignment) }}" 
                                           class="px-4 py-2 rounded-xl text-xs font-extrabold transition-all inline-flex items-center gap-1.5 {{ $isCompleted ? 'bg-slate-100 hover:bg-slate-200 text-slate-800 border border-slate-300' : 'bg-blue-600 hover:bg-blue-700 text-white shadow-sm' }}">
                                            @if($isCompleted)
                                                <span>Lihat Borang Terkunci</span>
                                            @else
                                                <span>Buka Borang Telaah &rarr;</span>
                                            @endif
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-500 font-semibold">
                                        Belum ada usulan yang ditugaskan kepada Anda saat ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($assignments->hasPages())
                    <div class="p-5 border-t border-slate-200 bg-slate-50">
                        {{ $assignments->links() }}
                    </div>
                @endif
            </div>
        </main>
    </div>
</div>
@endsection


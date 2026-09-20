@extends('layouts.app')

@section('title', 'Penilaian Monev Kemajuan - PRISMA UHN')

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
                        <h1 class="font-extrabold text-xl text-slate-900 leading-tight">Monev Kemajuan Pelaksanaan</h1>
                        <p class="text-xs font-semibold text-slate-500">Evaluasi Lapangan, Penilaian Skor & Rekomendasi Kelanjutan Riset (US-10.2)</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-black bg-purple-100 text-purple-800 border border-purple-300">
                        Reviewer Monev
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

            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="font-extrabold text-base text-slate-900">Daftar Usulan Tahap Monev Kemajuan (70%)</h2>
                        <p class="text-xs text-slate-500">Periksa berkas laporan kemajuan, logbook, dan berikan skor evaluasi serta rekomendasi kelanjutan.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 font-extrabold uppercase tracking-wider text-[10px]">
                                <th class="py-3.5 px-6">Usulan & Pengusul</th>
                                <th class="py-3.5 px-6">Skema Hibah</th>
                                <th class="py-3.5 px-6">Capaian Progres</th>
                                <th class="py-3.5 px-6">Status Monev</th>
                                <th class="py-3.5 px-6">Skor & Rekomendasi</th>
                                <th class="py-3.5 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($monevList as $monev)
                                @php
                                    $u = $monev->usulan;
                                @endphp
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="py-4 px-6 max-w-sm">
                                        <span class="px-2 py-0.5 rounded font-mono text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                            {{ $u->kode_usulan }}
                                        </span>
                                        <p class="font-black text-slate-900 text-xs mt-1 leading-snug">
                                            {{ $u->judul_usulan }}
                                        </p>
                                        <p class="text-[11px] text-slate-500 mt-0.5">
                                            Ketua: <strong>{{ $u->pengusul->name ?? '-' }}</strong> ({{ $u->pengusul->prodi->nama_prodi ?? '-' }})
                                        </p>
                                    </td>
                                    <td class="py-4 px-6 font-semibold text-slate-700">
                                        {{ $u->skema->nama_skema ?? '-' }}
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-2">
                                            <span class="font-extrabold text-blue-600 text-xs">{{ number_format($monev->persentase_kemajuan, 1) }}%</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        @if($monev->status === 'evaluated')
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                Sudah Dievaluasi
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-100 text-amber-900 border border-amber-300 animate-pulse">
                                                Perlu Dinilai
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6">
                                        @if($monev->status === 'evaluated')
                                            <div class="space-y-0.5">
                                                <span class="font-black text-slate-900 text-xs">Skor: {{ number_format($monev->skor_monev, 1) }}</span>
                                                <div>
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-black {{ $monev->rekomendasi === 'Lanjut' ? 'bg-emerald-100 text-emerald-800' : ($monev->rekomendasi === 'Perbaikan' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                                        {{ $monev->rekomendasi }}
                                                    </span>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-slate-400 italic text-[11px]">- Belum dinilai -</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <a href="{{ route('reviewer.monev.show', $monev) }}" class="px-3.5 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-extrabold text-xs shadow-sm transition inline-flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <span>{{ $monev->status === 'evaluated' ? 'Lihat / Edit' : 'Nilai Monev' }}</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-slate-400">
                                        <svg class="w-12 h-12 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <p class="font-bold text-slate-600">Belum ada berkas Monev Kemajuan yang diunggah peneliti.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($monevList->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $monevList->links() }}
                    </div>
                @endif
            </div>
        </main>
    </div>
</div>
@endsection


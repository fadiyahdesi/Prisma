@extends('layouts.app')

@section('title', 'Daftar Usulan Proposal Riset - PRISMA UHN')

@section('content')
<div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <x-sidebar />

    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        <!-- Header -->
        <header class="bg-white border-b border-slate-200 py-4 sticky top-0 z-30 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <h1 class="font-extrabold text-xl text-slate-900 leading-tight">Daftar Usulan Proposal BIMA</h1>
                        <p class="text-xs font-semibold text-slate-500">Kelola Draf Usulan & Proposal Riset Standar Kemdiktisaintek</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('usulan.start', 'PDP') }}" class="px-4 py-2.5 rounded-xl bg-blue-700 hover:bg-blue-800 text-white text-xs font-extrabold shadow-md transition-all">
                        + Buat Usulan Baru &rarr;
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Body -->
        <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            <!-- Flash Alert -->
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-sm font-extrabold flex items-center gap-3 shadow-sm">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-300 text-rose-900 text-sm font-extrabold flex items-center gap-3 shadow-sm">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xl space-y-6">
                <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                    <div>
                        <h3 class="text-lg font-extrabold text-slate-900">Riwayat Usulan Proposal Anda</h3>
                        <p class="text-xs font-medium text-slate-500">Daftar proposal yang sedang disusun maupun yang telah dikirim.</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-blue-100 text-blue-900 border border-blue-300">
                        Total: {{ count($proposals) }} Usulan
                    </span>
                </div>

                @if(count($proposals) === 0)
                    <div class="text-center py-12 bg-slate-50 rounded-2xl border border-slate-200 space-y-4">
                        <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center mx-auto text-xl font-bold">
                            📝
                        </div>
                        <div>
                            <h4 class="text-base font-extrabold text-slate-900">Belum Ada Usulan Proposal</h4>
                            <p class="text-xs font-semibold text-slate-500 mt-1">Pilih skema BIMA yang sesuai dengan eligibilitas Anda dari Dasbor Utama.</p>
                        </div>
                        <a href="{{ route('dashboard') }}" class="inline-block px-5 py-2.5 rounded-xl bg-blue-700 hover:bg-blue-800 text-white font-extrabold text-xs shadow-md transition-all">
                            Buka Dasbor Skema &rarr;
                        </a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs whitespace-nowrap">
                            <thead class="bg-slate-50 text-slate-600 uppercase font-black border-b border-slate-200">
                                <tr>
                                    <th class="p-4">Kode Usulan</th>
                                    <th class="p-4">Judul Proposal</th>
                                    <th class="p-4">Skema Hibah</th>
                                    <th class="p-4">Total RAB</th>
                                    <th class="p-4">Status</th>
                                    <th class="p-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-semibold text-slate-800">
                                @foreach($proposals as $p)
                                    <tr class="hover:bg-slate-50">
                                        <td class="p-4 font-mono font-bold text-blue-900">{{ $p->kode_usulan }}</td>
                                        <td class="p-4 font-bold text-slate-900 max-w-xs truncate" title="{{ $p->judul_usulan }}">
                                            {{ Str::limit($p->judul_usulan ?? 'Draf Tanpa Judul', 40) }}
                                        </td>
                                        <td class="p-4">
                                            <span class="px-2.5 py-1 rounded-lg text-[11px] font-extrabold bg-blue-100 text-blue-900 border border-blue-300">
                                                {{ $p->skema->kode_skema }}
                                            </span>
                                        </td>
                                        <td class="p-4 font-mono font-bold text-emerald-800">
                                            Rp {{ number_format($p->total_rab, 0, ',', '.') }}
                                        </td>
                                        <td class="p-4">
                                            @if($p->status === 'Draft')
                                                <span class="px-2.5 py-1 rounded-full text-[11px] font-black bg-amber-100 text-amber-900 border border-amber-300">
                                                    ✏️ Draft Usulan
                                                </span>
                                            @elseif($p->status === 'Submitted')
                                                <span class="px-2.5 py-1 rounded-full text-[11px] font-black bg-emerald-100 text-emerald-900 border border-emerald-300">
                                                    ✅ Submitted
                                                </span>
                                            @else
                                                <span class="px-2.5 py-1 rounded-full text-[11px] font-black bg-slate-100 text-slate-800 border border-slate-300">
                                                    {{ $p->status }}
                                                </span>
                                            @endif
                                            @if($p->verification_notes)
                                                <p class="mt-2 max-w-xs whitespace-normal text-[11px] font-semibold text-amber-800" title="{{ $p->verification_notes }}">Catatan: {{ Str::limit($p->verification_notes, 80) }}</p>
                                            @endif
                                        </td>
                                        <td class="p-4 text-center">
                                            @if($p->status === 'Draft')
                                                <a href="{{ route('usulan.step', ['usulan' => $p->id, 'step' => 1]) }}" class="px-3 py-1.5 rounded-lg bg-blue-700 hover:bg-blue-800 text-white font-extrabold text-[11px] shadow-sm">
                                                    Lanjutkan Wizard &rarr;
                                                </a>
                                            @else
                                                <a href="{{ route('usulan.step', ['usulan' => $p->id, 'step' => 6]) }}" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-900 text-white font-bold text-[11px]">
                                                    Lihat Rekapitulasi
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </main>
    </div>
</div>
@endsection


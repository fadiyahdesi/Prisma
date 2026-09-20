@extends('layouts.app')

@section('title', 'Manajemen Periode Usulan Call for Proposals - PRISMA UHN')

@section('content')
<div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <x-sidebar />

    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        <!-- Header -->
        <header class="bg-white border-b border-slate-200 py-4 sticky top-0 z-30 shadow-sm">
            <div class="w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <h1 class="font-extrabold text-xl text-slate-900 leading-tight">Periode Usulan (Call for Proposals)</h1>
                        <p class="text-xs font-semibold text-slate-500">Penjadwalan Batas Waktu Server-Time Scheduler & Lock Form (US-04.2)</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button onclick="document.getElementById('modalTambahPeriode').classList.remove('hidden')" class="px-5 py-2.5 rounded-xl bg-blue-700 hover:bg-blue-800 text-white text-xs font-extrabold shadow-md transition-all flex items-center gap-2">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        <span class="hidden sm:inline">+ Buat Periode Call for Proposals</span>
                    </button>
                    <a href="{{ route('dashboard') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-extrabold border border-slate-300">
                        Dashboard
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Container -->
    <main class="flex-grow w-full px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="p-4.5 rounded-2xl bg-emerald-50 border-2 border-emerald-300 text-emerald-950 text-sm font-extrabold flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Server-Time Status Banner -->
        <div class="p-5 rounded-3xl bg-gradient-to-r from-blue-900 via-slate-900 to-indigo-950 text-white shadow-xl flex flex-col sm:flex-row items-center justify-between gap-4 border border-blue-800">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-white/10 border border-white/20 flex items-center justify-center text-white shrink-0">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-white">Jam Server Aktif (Server Time Scheduler)</h3>
                    <p class="text-xs text-blue-200 mt-0.5 font-mono">
                        {{ now()->format('l, d F Y H:i:s T') }} &bull; Waktu server lokal otomatis mengunci form saat tenggat tercapai.
                    </p>
                </div>
            </div>
            <span class="px-3.5 py-1.5 rounded-full text-xs font-black bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 whitespace-nowrap">
                Server Scheduler Online
            </span>
        </div>

        <!-- List Card -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xl space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
                <div>
                    <span class="text-xs font-black text-blue-900 bg-blue-100 px-3 py-1 rounded-md border border-blue-300 uppercase tracking-wider">
                        Jadwal Penerimaan Proposal
                    </span>
                    <h3 class="text-xl font-extrabold text-slate-900 mt-1">Daftar Periode Usulan Hibah</h3>
                </div>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-left border-collapse min-w-[900px]">
                    <thead>
                        <tr class="bg-slate-100 text-slate-800 text-xs font-black uppercase tracking-wider border-b-2 border-slate-300">
                            <th class="py-4 px-5 whitespace-nowrap">Tahun & Semester</th>
                            <th class="py-4 px-5 whitespace-nowrap">Nama Periode Call for Proposals</th>
                            <th class="py-4 px-4 whitespace-nowrap">Waktu Buka (Server Time)</th>
                            <th class="py-4 px-4 whitespace-nowrap">Waktu Tutup (Tenggat)</th>
                            <th class="py-4 px-4 text-center whitespace-nowrap">Status Server</th>
                            <th class="py-4 px-5 text-center whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-sm font-semibold">
                        @foreach($periods as $period)
                            @php
                                $now = now();
                                $isOpen = $period->is_active && $now->greaterThanOrEqualTo($period->waktu_buka) && $now->lessThanOrEqualTo($period->waktu_tutup);
                                $isUpcoming = $now->lessThan($period->waktu_buka);
                                $isExpired = $now->greaterThan($period->waktu_tutup);
                            @endphp
                            <tr class="hover:bg-slate-50/90 transition-all">
                                <td class="py-4 px-5 font-mono font-black text-slate-900 whitespace-nowrap">
                                    {{ $period->tahun_akademik }} ({{ $period->semester }})
                                </td>
                                <td class="py-4 px-5">
                                    <div class="font-extrabold text-slate-900">{{ $period->nama_periode }}</div>
                                    @if($period->keterangan)
                                        <div class="text-xs text-slate-500 font-normal truncate max-w-xs">{{ $period->keterangan }}</div>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-xs font-mono font-bold text-slate-700 whitespace-nowrap">
                                    {{ $period->waktu_buka->format('d M Y, H:i') }}
                                </td>
                                <td class="py-4 px-4 text-xs font-mono font-black text-slate-900 whitespace-nowrap">
                                    {{ $period->waktu_tutup->format('d M Y, H:i') }}
                                </td>
                                <td class="py-4 px-4 text-center whitespace-nowrap">
                                    @if($isOpen)
                                        <span class="px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-950 border border-emerald-400">
                                            ● DIBUKA (AKTIF)
                                        </span>
                                    @elseif($isUpcoming)
                                        <span class="px-3 py-1 rounded-full text-xs font-black bg-amber-100 text-amber-950 border border-amber-400">
                                            ⏳ AKAN DIBUKA
                                        </span>
                                    @else
                                        <span class="px-3 py-1 rounded-full text-xs font-black bg-rose-100 text-rose-950 border border-rose-400">
                                            🔒 TERKUNCI / DITUTUP
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-5 text-center space-x-1 whitespace-nowrap">
                                    <form action="{{ route('admin.periode-hibah.toggle', $period) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition-all border {{ $period->is_active ? 'bg-slate-200 text-slate-800 border-slate-300' : 'bg-emerald-100 text-emerald-900 border-emerald-300' }}">
                                            {{ $period->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.periode-hibah.destroy', $period) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus periode ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-100 hover:bg-rose-200 text-rose-950 text-xs font-extrabold border border-rose-300 transition-all">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<!-- Modal Tambah Periode -->
<div id="modalTambahPeriode" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-xl w-full border-2 border-blue-300 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200">
            <h3 class="text-lg font-extrabold text-slate-900">Buat Periode Call for Proposals Baru</h3>
            <button onclick="document.getElementById('modalTambahPeriode').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 font-bold text-xl">&times;</button>
        </div>

        <form action="{{ route('admin.periode-hibah.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-extrabold text-slate-800 uppercase block mb-1">Tahun Akademik:</label>
                    <input type="text" name="tahun_akademik" required value="2025/2026" placeholder="Contoh: 2025/2026" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 font-bold text-sm text-slate-900">
                </div>
                <div>
                    <label class="text-xs font-extrabold text-slate-800 uppercase block mb-1">Semester:</label>
                    <select name="semester" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 font-bold text-sm text-slate-900">
                        <option value="Ganjil">Ganjil</option>
                        <option value="Genap">Genap</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="text-xs font-extrabold text-slate-800 uppercase block mb-1">Nama Periode Usulan:</label>
                <input type="text" name="nama_periode" required placeholder="Contoh: Call for Proposals BIMA Batch 1 2025/2026" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 font-bold text-sm text-slate-900">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-extrabold text-slate-800 uppercase block mb-1">Waktu Buka (Server Time):</label>
                    <input type="datetime-local" name="waktu_buka" required value="{{ now()->format('Y-m-d\TH:i') }}" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 font-bold text-xs text-slate-900">
                </div>
                <div>
                    <label class="text-xs font-extrabold text-slate-800 uppercase block mb-1">Waktu Tutup (Tenggat):</label>
                    <input type="datetime-local" name="waktu_tutup" required value="{{ now()->addDays(30)->format('Y-m-d\TH:i') }}" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 font-bold text-xs text-slate-900">
                </div>
            </div>

            <div>
                <label class="text-xs font-extrabold text-slate-800 uppercase block mb-1">Keterangan Tambahan:</label>
                <textarea name="keterangan" rows="2" placeholder="Catatan untuk dosen pengusul..." class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 font-semibold text-xs text-slate-900"></textarea>
            </div>

            <div class="pt-2 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modalTambahPeriode').classList.add('hidden')" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-700 hover:bg-blue-800 text-white text-xs font-black shadow-md">Buat Periode Usulan</button>
            </div>
        </form>
    </div>
</div>
    </div>
</div>
@endsection

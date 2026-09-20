@extends('layouts.app')

@section('title', 'Pencarian Data PDDIKTI - PRISMA UHN')

@section('content')
<div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <x-sidebar />

    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        <!-- Header -->
        <header class="bg-white border-b border-slate-200 py-4 sticky top-0 z-30 shadow-sm">
            <div class="w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <a href="{{ route('dashboard') }}" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center shrink-0 transition" title="Kembali ke Dashboard" aria-label="Kembali ke Dashboard">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </a>
                    <div>
                        <h1 class="font-extrabold text-xl text-slate-900 leading-tight">Integrasi & Pencarian PDDIKTI</h1>
                        <p class="text-xs font-semibold text-slate-500">Pencarian Profil Dosen, Mahasiswa & Data Master Pangkalan Data Pendidikan Tinggi</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Body -->
        <main class="flex-grow w-full px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xl space-y-6">
                <div>
                    <span class="text-xs font-black text-cyan-900 bg-cyan-100 px-3 py-1 rounded-md border border-cyan-300 uppercase tracking-wider">
                        PDDIKTI Official API Integration
                    </span>
                    <h3 class="text-xl font-extrabold text-slate-900 mt-2">Cari Profil Sivitas Akademika</h3>
                    <p class="text-xs font-semibold text-slate-500 mt-1">Cari berdasarkan nama. Metrik penelitian hanya ditampilkan dari respons resmi PDDIKTI yang terhubung.</p>
                </div>

                <form method="GET" action="{{ route('pddikti.search') }}" class="flex flex-col sm:flex-row gap-3">
                    <label for="pddikti-name" class="sr-only">Nama dosen atau mahasiswa</label>
                    <input id="pddikti-name" name="name" value="{{ $name }}" minlength="3" maxlength="100" required placeholder="Contoh: Hendra Prasetya" class="flex-1 px-4 py-3 rounded-2xl bg-slate-50 border border-slate-300 text-sm font-semibold text-slate-900 outline-none focus:ring-2 focus:ring-cyan-500">
                    <button class="px-6 py-3 rounded-2xl bg-cyan-700 hover:bg-cyan-800 text-white font-extrabold text-sm shadow-md transition-all whitespace-nowrap" type="submit">
                        Cari Data PDDIKTI &rarr;
                    </button>
                </form>

                @if($error)
                    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-300 text-rose-950 text-sm font-extrabold flex items-center gap-3">
                        <svg class="w-5 h-5 text-rose-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ $error }}</span>
                    </div>
                @endif

                @if($name !== '' && !$error && count($results) === 0)
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 text-sm font-bold text-slate-600 text-center">
                        Tidak ada profil yang ditemukan untuk "{{ $name }}".
                    </div>
                @endif

                @if(count($results) > 0)
                    <div class="overflow-hidden rounded-2xl border border-slate-200 shadow-sm bg-white">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm whitespace-nowrap">
                                <thead class="border-b border-slate-200 bg-slate-50 text-xs font-black uppercase text-slate-600">
                                    <tr>
                                        <th class="px-5 py-4">Nama</th>
                                        <th class="px-5 py-4">NIDN/NIM</th>
                                        <th class="px-5 py-4">Jenis</th>
                                        <th class="px-5 py-4">Penelitian</th>
                                        <th class="px-5 py-4">Publikasi</th>
                                        <th class="px-5 py-4">HKI</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 font-semibold text-slate-800">
                                    @foreach($results as $result)
                                        <tr class="hover:bg-slate-50">
                                            <td class="px-5 py-4 font-bold text-slate-900">{{ $result['name'] }}</td>
                                            <td class="px-5 py-4 font-mono text-xs text-blue-900 font-extrabold">{{ $result['identifier'] }}</td>
                                            <td class="px-5 py-4 text-slate-600">{{ $result['type'] }}</td>
                                            <td class="px-5 py-4 font-bold text-cyan-700">{{ $result['total_research'] ?? 'Belum tersedia' }}</td>
                                            <td class="px-5 py-4 font-bold text-blue-700">{{ $result['total_publications'] ?? 'Belum tersedia' }}</td>
                                            <td class="px-5 py-4 font-bold text-emerald-700">{{ $result['total_hki'] ?? 'Belum tersedia' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </main>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('title', 'Manajemen Pengguna & Database Dosen - Admin P3M')

@section('content')
<div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <x-sidebar />

    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        <!-- Topbar Header -->
        <header class="bg-white border-b border-slate-200 py-3.5 sticky top-0 z-30 shadow-xs">
            <div class="w-full px-4 sm:px-6 lg:px-8 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <a href="{{ route('dashboard') }}" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center shrink-0 transition" title="Kembali ke Dashboard" aria-label="Kembali ke Dashboard">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </a>
                    <div>
                        <h1 class="font-extrabold text-lg sm:text-xl text-slate-900 leading-tight">Database Dosen Tetap &amp; Manajemen Akun</h1>
                        <p class="text-xs font-semibold text-slate-500">Data resmi {{ $totalDosenTetap ?? 146 }} Dosen Tetap Universitas Harkat Negeri (SK SDM) &amp; otorisasi peran PRISMA UHN</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <form method="POST" action="{{ route('admin.users.sync-sdm') }}" class="inline-flex m-0 p-0">
                        @csrf
                        <button type="submit"
                                class="h-9 px-3.5 rounded-xl bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200 hover:border-emerald-600 font-extrabold text-xs shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span>Sinkronkan 146 Dosen SDM</span>
                        </button>
                    </form>

                    <a href="{{ route('admin.users.create') }}"
                       style="background-color: #681727 !important; color: #ffffff !important;"
                       class="h-9 px-4 rounded-xl text-white font-extrabold text-xs shadow-xs transition inline-flex items-center gap-1.5 hover:opacity-90">
                        <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span style="color: #ffffff !important;">Tambah Akun</span>
                    </a>
                </div>
            </div>
        </header>

        <main class="flex-grow w-full px-4 sm:px-6 lg:px-8 py-6 space-y-4">
            <!-- Flash Notification -->
            @if(session('success'))
                <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs sm:text-sm font-bold flex items-center gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('warning'))
                <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-300 text-amber-900 text-xs sm:text-sm font-bold flex items-center gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('warning') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-300 text-rose-900 text-xs sm:text-sm font-bold flex items-center gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Category Tabs & Filter Toolbar -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs space-y-3.5">
                <!-- Row 1: Category Pills & Summary Counter -->
                <div class="flex flex-wrap items-center justify-between gap-2 pb-3 border-b border-slate-100">
                    <div class="inline-flex flex-wrap items-center gap-1.5 bg-slate-100 p-1 rounded-xl">
                        <a href="{{ route('admin.users.index', array_merge(request()->except('page'), ['kategori' => 'dosen'])) }}"
                           class="px-3 py-1.5 rounded-lg text-xs font-extrabold transition inline-flex items-center gap-1.5 {{ ($kategori ?? 'dosen') === 'dosen' ? 'bg-[#681727] text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                            <span>Dosen Tetap UHN</span>
                            <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ ($kategori ?? 'dosen') === 'dosen' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }}">
                                {{ $totalDosenTetap ?? 146 }}
                            </span>
                        </a>
                        <a href="{{ route('admin.users.index', array_merge(request()->except('page'), ['kategori' => 'sistem'])) }}"
                           class="px-3 py-1.5 rounded-lg text-xs font-extrabold transition inline-flex items-center gap-1.5 {{ ($kategori ?? 'dosen') === 'sistem' ? 'bg-[#681727] text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                            <span>Akun Sistem</span>
                            <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ ($kategori ?? 'dosen') === 'sistem' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }}">
                                {{ $totalAkunSistem ?? 2 }}
                            </span>
                        </a>
                        <a href="{{ route('admin.users.index', array_merge(request()->except('page'), ['kategori' => 'semua'])) }}"
                           class="px-3 py-1.5 rounded-lg text-xs font-extrabold transition inline-flex items-center gap-1.5 {{ ($kategori ?? 'dosen') === 'semua' ? 'bg-[#681727] text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                            <span>Semua Akun</span>
                            <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ ($kategori ?? 'dosen') === 'semua' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }}">
                                {{ $totalSemuaAkun ?? 148 }}
                            </span>
                        </a>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 text-xs font-bold text-slate-600">
                        <span class="px-2.5 py-1 rounded-lg bg-slate-100 border border-slate-200 text-slate-700">
                            Program Studi: <strong class="text-slate-900">{{ $prodis->count() }} Prodi</strong>
                        </span>
                        <span class="px-2.5 py-1 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800">
                            Ditampilkan: <strong class="text-emerald-950">{{ $users->count() }}</strong> dari <strong class="text-emerald-950">{{ $users->total() }}</strong> Data
                        </span>
                    </div>
                </div>

                <!-- Row 2: Search & Filter Controls -->
                <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-center gap-2">
                    <input type="hidden" name="kategori" value="{{ $kategori ?? 'dosen' }}">

                    <div class="relative flex-1 min-w-[220px]">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama, NIPY, NIDN/NUPTK, atau Jabatan..."
                               class="w-full h-9 pl-3.5 pr-3 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-[#681727]">
                    </div>

                    <select name="prodi" class="h-9 px-3 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 focus:outline-hidden focus:ring-2 focus:ring-[#681727]">
                        <option value="">Semua Program Studi ({{ $prodis->count() }})</option>
                        @foreach($prodis as $p)
                            <option value="{{ $p->id_prodi }}" {{ (string) request('prodi') === (string) $p->id_prodi ? 'selected' : '' }}>{{ $p->nama_prodi }}</option>
                        @endforeach
                    </select>

                    <select name="role" class="h-9 px-3 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 focus:outline-hidden focus:ring-2 focus:ring-[#681727]">
                        <option value="">Semua Peran (Roles)</option>
                        @foreach($roles as $r)
                            <option value="{{ $r->name }}" {{ request('role') === $r->name ? 'selected' : '' }}>{{ $r->name }}</option>
                        @endforeach
                    </select>

                    <select name="per_page" onchange="this.form.submit()" class="h-9 px-3 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 focus:outline-hidden focus:ring-2 focus:ring-[#681727]">
                        <option value="150" {{ ($perPage ?? 150) == 150 ? 'selected' : '' }}>Tampilkan: Semua (146)</option>
                        <option value="50" {{ ($perPage ?? 150) == 50 ? 'selected' : '' }}>50 Baris / Hal</option>
                        <option value="25" {{ ($perPage ?? 150) == 25 ? 'selected' : '' }}>25 Baris / Hal</option>
                    </select>

                    <button type="submit" class="h-9 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition cursor-pointer">
                        Terapkan Filter
                    </button>

                    @if(request('search') || request('role') || request('prodi'))
                        <a href="{{ route('admin.users.index', ['kategori' => $kategori ?? 'dosen']) }}"
                           class="h-9 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold inline-flex items-center transition">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <!-- Users Table -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-slate-50 text-slate-600 uppercase tracking-wider font-extrabold text-[10px] border-b border-slate-200">
                            <tr>
                                <th class="px-3.5 py-3.5 text-center w-12">No</th>
                                <th class="px-4 py-3.5 min-w-[230px]">Nama</th>
                                <th class="px-3.5 py-3.5 whitespace-nowrap">NIPY</th>
                                <th class="px-3.5 py-3.5 whitespace-nowrap">NIDN / NUPTK</th>
                                <th class="px-3.5 py-3.5 min-w-[170px]">Jabatan</th>
                                <th class="px-3.5 py-3.5 whitespace-nowrap">Jabatan Akademik Dosen</th>
                                <th class="px-3.5 py-3.5 min-w-[180px]">Prodi</th>
                                <th class="px-3.5 py-3.5 whitespace-nowrap">Email</th>
                                <th class="px-4 py-3.5 text-right whitespace-nowrap w-40">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($users as $index => $u)
                                @php
                                    $nidnRaw = trim((string) ($u->nidn_nim ?? ''));
                                    $nidnParts = $nidnRaw !== '' ? array_map('trim', explode('/', $nidnRaw)) : [];
                                    $primaryNidn = $nidnParts[0] ?? '';
                                    $secondaryNuptk = $nidnParts[1] ?? '';
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition">
                                    <!-- No Urut -->
                                    <td class="px-3.5 py-3 align-middle text-center">
                                        <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-700 font-extrabold text-[11px] inline-flex items-center justify-center border border-slate-200/70">
                                            {{ $u->no_urut ?? ($users->firstItem() + $index) }}
                                        </span>
                                    </td>

                                    <!-- Nama & Peran -->
                                    <td class="px-4 py-3 align-middle">
                                        <div class="flex items-center gap-2.5">
                                            @if($u->avatar && Storage::disk('public')->exists($u->avatar))
                                                <img src="{{ Storage::url($u->avatar) }}" alt="{{ $u->name }}" class="w-8 h-8 rounded-xl object-cover shrink-0">
                                            @else
                                                <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-[#681727] to-[#8c1d34] text-white flex items-center justify-center font-black text-[11px] shrink-0 shadow-2xs">
                                                    {{ strtoupper(substr(preg_replace('/^(Prof\.?\s*|Dr\.?\s*|Ir\.?\s*|apt\.?\s*|Bdn\.?\s*|Ns\.?\s*)+/i', '', $u->name), 0, 2)) }}
                                                </div>
                                            @endif
                                            <div class="min-w-0">
                                                <span class="font-extrabold text-slate-900 block leading-snug">{{ $u->name }}</span>
                                                <div class="flex flex-wrap gap-1 mt-0.5">
                                                    @foreach($u->roles as $r)
                                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-100 text-slate-600 border border-slate-200 leading-none">
                                                            {{ $r->name }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- NIPY -->
                                    <td class="px-3.5 py-3 align-middle font-mono font-bold text-slate-700 whitespace-nowrap">
                                        @if(!empty($u->nipy))
                                            <span class="px-2 py-0.5 rounded-md bg-slate-50 border border-slate-200 text-slate-800 text-[11px]">{{ $u->nipy }}</span>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>

                                    <!-- NIDN / NUPTK -->
                                    <td class="px-3.5 py-3 align-middle whitespace-nowrap">
                                        @if($primaryNidn !== '')
                                            <span class="font-mono font-bold text-slate-800 block text-[11px] leading-tight">{{ $primaryNidn }}</span>
                                            @if($secondaryNuptk !== '')
                                                <span class="font-mono text-[10px] text-slate-500 block leading-tight mt-0.5">NUPTK: {{ $secondaryNuptk }}</span>
                                            @endif
                                        @else
                                            <span class="text-slate-400 font-mono">-</span>
                                        @endif
                                    </td>

                                    <!-- Jabatan -->
                                    <td class="px-3.5 py-3 align-middle text-slate-700 font-semibold leading-snug">
                                        {{ $u->jabatan ?? 'Dosen' }}
                                    </td>

                                    <!-- Jabatan Akademik Dosen -->
                                    <td class="px-3.5 py-3 align-middle whitespace-nowrap">
                                        @if(!empty($u->jabatan_fungsional))
                                            <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 inline-block">
                                                {{ $u->jabatan_fungsional }}
                                            </span>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>

                                    <!-- Prodi -->
                                    <td class="px-3.5 py-3 align-middle">
                                        <span class="font-bold block text-slate-800 leading-snug">{{ $u->prodi?->nama_prodi ?? '-' }}</span>
                                        <span class="text-[10px] text-slate-400 block leading-tight">{{ $u->fakultas?->nama_fakultas ?? '-' }}</span>
                                    </td>

                                    <!-- Email -->
                                    <td class="px-3.5 py-3 align-middle whitespace-nowrap">
                                        @if(!empty($u->email))
                                            <span class="font-mono text-[11px] text-slate-600">{{ $u->email }}</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200 inline-block">
                                                Belum diisi
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Aksi (Edit & Delete) -->
                                    <td class="px-4 py-3 align-middle text-right whitespace-nowrap">
                                        <div class="inline-flex items-center justify-end gap-1.5">
                                            <a href="{{ route('admin.users.edit', $u) }}"
                                               class="inline-flex items-center justify-center gap-1.5 h-8 px-3 rounded-lg bg-indigo-50 hover:bg-indigo-600 text-indigo-700 hover:text-white border border-indigo-200 hover:border-indigo-600 text-[11px] font-bold transition shadow-2xs shrink-0"
                                               title="Edit Data {{ $u->name }}">
                                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                <span>Edit</span>
                                            </a>
                                            @if($u->id !== Auth::id())
                                                <form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="inline-flex items-center m-0 p-0"
                                                      data-confirm="Apakah Anda yakin ingin menghapus akun pengguna ini dari sistem PRISMA UHN?"
                                                      data-confirm-title="Konfirmasi Hapus Akun Pengguna"
                                                      data-confirm-detail="{{ $u->name }} ({{ $u->email }})"
                                                      data-confirm-variant="danger"
                                                      data-confirm-label="Ya, Hapus Akun">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="inline-flex items-center justify-center gap-1.5 h-8 px-3 rounded-lg bg-rose-50 hover:bg-rose-600 text-rose-700 hover:text-white border border-rose-200 hover:border-rose-600 text-[11px] font-bold transition shadow-2xs cursor-pointer shrink-0"
                                                            title="Hapus Akun {{ $u->name }}">
                                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        <span>Hapus</span>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="inline-flex items-center justify-center h-8 px-2.5 rounded-lg bg-slate-100 text-slate-400 border border-slate-200 text-[10px] font-bold select-none" title="Akun Anda sedang aktif">
                                                    Aktif
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="p-12 text-center text-slate-400 font-bold text-xs">
                                        Tidak ada data dosen/pengguna yang cocok dengan kriteria pencarian.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </main>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Manajemen Pengguna - Admin P3M')

@section('content')
<div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <x-sidebar />

    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        <!-- Topbar Header -->
        <header class="bg-white border-b border-slate-200 py-4 sticky top-0 z-30 shadow-xs">
            <div class="w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <a href="{{ route('dashboard') }}" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center shrink-0 transition" title="Kembali ke Dashboard" aria-label="Kembali ke Dashboard">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </a>
                    <div>
                        <h1 class="font-extrabold text-xl text-slate-900 leading-tight">Manajemen Pengguna &amp; Akun</h1>
                        <p class="text-xs font-semibold text-slate-500">Kelola otorisasi akun dosen, reviewer, pimpinan, dan staf di sistem PRISMA UHN</p>
                    </div>
                </div>
                <a href="{{ route('admin.users.create') }}"
                   style="background-color: #681727 !important; color: #ffffff !important;"
                   class="px-4 py-2.5 rounded-xl text-white font-extrabold text-xs shadow-xs transition flex items-center gap-2 hover:opacity-90">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span style="color: #ffffff !important;">Tambah Akun Pengguna</span>
                </a>
            </div>
        </header>

        <main class="flex-grow w-full px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            <!-- Flash Notification -->
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-sm font-bold flex items-center gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('warning'))
                <div class="p-4 rounded-2xl bg-amber-50 border border-amber-300 text-amber-900 text-sm font-bold flex items-center gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('warning') }}</span>
                </div>
            @endif

            <!-- Filter & Search Toolbar -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-3">
                <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / email / NIDN..."
                           class="px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-[#681727] w-full sm:w-64">

                    <select name="role" class="px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 focus:outline-hidden focus:ring-2 focus:ring-[#681727]">
                        <option value="">Semua Peran (Roles)</option>
                        @foreach($roles as $r)
                            <option value="{{ $r->name }}" {{ request('role') === $r->name ? 'selected' : '' }}>{{ $r->name }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition">
                        Filter
                    </button>

                    @if(request('search') || request('role'))
                        <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 px-2">
                            Reset
                        </a>
                    @endif
                </form>

                <div class="text-xs font-bold text-slate-500 shrink-0">
                    Total Pengguna: <span class="text-slate-900">{{ $users->total() }}</span>
                </div>
            </div>

            <!-- Users Table -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-extrabold text-[10px] border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4">Nama Lengkap &amp; Jabatan</th>
                                <th class="px-6 py-4">NIDN / NIM</th>
                                <th class="px-6 py-4">Email Aktif</th>
                                <th class="px-6 py-4">Fakultas &amp; Prodi</th>
                                <th class="px-6 py-4">Peran (Roles)</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($users as $u)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            @if($u->avatar && Storage::disk('public')->exists($u->avatar))
                                                <img src="{{ Storage::url($u->avatar) }}" alt="{{ $u->name }}" class="w-8 h-8 rounded-xl object-cover">
                                            @else
                                                <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-[#681727] to-[#8c1d34] text-white flex items-center justify-center font-black text-xs shrink-0">
                                                    {{ strtoupper(substr($u->name, 0, 2)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <span class="font-extrabold text-slate-900 block leading-tight">{{ $u->name }}</span>
                                                <span class="text-[10px] text-slate-400 font-semibold">{{ $u->jabatan_fungsional ?? 'Dosen UHN' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-mono font-bold text-slate-700">
                                        {{ $u->nidn_nim ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-slate-600 font-mono text-[11px]">
                                        {{ $u->email }}
                                    </td>
                                    <td class="px-6 py-4 text-slate-600">
                                        <span class="font-bold block text-slate-800">{{ $u->prodi?->nama_prodi ?? '-' }}</span>
                                        <span class="text-[10px] text-slate-400">{{ $u->fakultas?->nama_fakultas ?? '-' }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($u->roles as $r)
                                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                                    {{ $r->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('admin.users.edit', $u) }}"
                                               class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition"
                                               title="Edit Akun">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>
                                            @if($u->id !== Auth::id())
                                                <form method="POST" action="{{ route('admin.users.destroy', $u) }}"
                                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $u->name }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 transition" title="Hapus Akun">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-12 text-center text-slate-400 font-bold text-xs">
                                        Tidak ada akun pengguna yang cocok dengan kriteria pencarian.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                    <div class="px-6 py-4 border-t border-slate-100">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </main>
    </div>
</div>
@endsection


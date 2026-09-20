@extends('layouts.app')

@section('title', 'Sentra HKI UHN - PRISMA UHN')

@section('content')
<div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <x-sidebar />

    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        <header class="bg-white border-b border-slate-200 py-4 sticky top-0 z-30 shadow-xs">
            <div class="w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <h1 class="font-extrabold text-xl text-slate-900 leading-tight">Sentra Hak Kekayaan Intelektual (HKI)</h1>
                        <p class="text-xs font-semibold text-slate-500">Pendaftaran Paten, Hak Cipta, Desain Industri &amp; Verifikasi Terintegrasi DJKI (US-11.2)</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('hki.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-700 hover:bg-indigo-800 text-white font-extrabold text-xs shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>+ Pengajuan KI / Paten / HKI</span>
                    </a>
                </div>
            </div>
        </header>

        <main class="flex-grow w-full px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-sm font-bold flex items-center gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Search & Filters -->
            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs">
                <form method="GET" action="{{ route('hki.index') }}" class="flex flex-col md:flex-row gap-3">
                    <div class="flex-1 relative">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul ciptaan, nomor permohonan, sertifikat, atau nama pengusul..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-blue-500 bg-slate-50/50">
                    </div>
                    <div class="w-full md:w-48">
                        <select name="jenis_hki" onchange="this.form.submit()" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-blue-500 bg-white">
                            <option value="">Semua Jenis HKI</option>
                            <option value="Paten" {{ request('jenis_hki') == 'Paten' ? 'selected' : '' }}>Paten</option>
                            <option value="Paten Sederhana" {{ request('jenis_hki') == 'Paten Sederhana' ? 'selected' : '' }}>Paten Sederhana</option>
                            <option value="Hak Cipta" {{ request('jenis_hki') == 'Hak Cipta' ? 'selected' : '' }}>Hak Cipta</option>
                            <option value="Desain Industri" {{ request('jenis_hki') == 'Desain Industri' ? 'selected' : '' }}>Desain Industri</option>
                            <option value="Merk Dagang" {{ request('jenis_hki') == 'Merk Dagang' ? 'selected' : '' }}>Merk Dagang</option>
                        </select>
                    </div>
                    <div class="w-full md:w-48">
                        <select name="status_hki" onchange="this.form.submit()" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-blue-500 bg-white">
                            <option value="">Semua Status</option>
                            <option value="Pending_verification" {{ request('status_hki') == 'Pending_verification' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                            <option value="Terverifikasi HKI" {{ request('status_hki') == 'Terverifikasi HKI' ? 'selected' : '' }}>Terverifikasi HKI</option>
                            <option value="Rejected" {{ request('status_hki') == 'Rejected' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2.5 bg-slate-900 text-white rounded-xl font-bold text-xs hover:bg-slate-800 transition">
                            Filter
                        </button>
                        @if(request('search') || request('jenis_hki') || request('status_hki'))
                            <a href="{{ route('hki.index') }}" class="px-4 py-2.5 bg-slate-100 text-slate-600 rounded-xl font-bold text-xs hover:bg-slate-200 transition">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- HKI List -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="font-bold text-sm text-slate-800">Daftar Aset Hak Kekayaan Intelektual</h2>
                        <p class="text-xs text-slate-500">Menampilkan {{ $hkiList->total() }} berkas pendaftaran HKI</p>
                    </div>
                </div>

                @if($hkiList->isEmpty())
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <h3 class="font-extrabold text-slate-800 text-base mb-1">Belum Ada Berkas HKI</h3>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto mb-5">Daftarkan perolehan Paten, Hak Cipta, atau Desain Industri Anda untuk verifikasi Sentra HKI UHN dan pencairan reward insentif.</p>
                        <a href="{{ route('hki.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-xs transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Daftarkan HKI Pertama</span>
                        </a>
                    </div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach($hkiList as $item)
                            <div class="p-5 sm:p-6 hover:bg-slate-50/70 transition flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div class="space-y-2 flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <!-- Jenis HKI badge -->
                                        <span class="px-2.5 py-0.5 rounded-md text-[11px] font-extrabold bg-indigo-100 text-indigo-800 border border-indigo-300">
                                            {{ $item->jenis_hki }}
                                        </span>

                                        <!-- Verification Status -->
                                        @if($item->status_hki === 'Terverifikasi HKI')
                                            <span class="px-2.5 py-0.5 rounded-md text-[11px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                Terverifikasi HKI
                                            </span>
                                        @elseif($item->status_hki === 'Rejected')
                                            <span class="px-2.5 py-0.5 rounded-md text-[11px] font-black bg-rose-100 text-rose-800 border border-rose-300">
                                                Ditolak / Ditangguhkan
                                            </span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-md text-[11px] font-black bg-amber-100 text-amber-800 border border-amber-300 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                Menunggu Verifikasi
                                            </span>
                                        @endif

                                        <!-- Claim Status -->
                                        @if($item->is_claimed_reward)
                                            <span class="px-2.5 py-0.5 rounded-md text-[11px] font-black bg-rose-100 text-rose-800 border border-rose-300 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                Reward Diklaim
                                            </span>
                                        @endif
                                    </div>

                                    <h3 class="font-extrabold text-slate-900 text-sm leading-snug">
                                        <a href="{{ route('hki.show', $item) }}" class="hover:text-blue-600 transition">
                                            {{ $item->judul_hki }}
                                        </a>
                                    </h3>

                                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500 font-semibold">
                                        <span>No. Permohonan: <strong class="text-slate-800">{{ $item->nomor_permohonan }}</strong></span>
                                        @if($item->nomor_sertifikat)
                                            <span>&bull;</span>
                                            <span>No. Sertifikat: <strong class="text-emerald-700">{{ $item->nomor_sertifikat }}</strong></span>
                                        @endif
                                        <span>&bull;</span>
                                        <span>Pemegang: <strong class="text-slate-800">{{ $item->pemegang_hak }}</strong></span>
                                        @if($isStaff)
                                            <span>&bull;</span>
                                            <span>Pengusul: <strong class="text-slate-900">{{ $item->user->name }}</strong></span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0 pt-2 md:pt-0">
                                    <a href="{{ route('hki.show', $item) }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                                        Detail
                                    </a>
                                    <a href="{{ asset('storage/' . $item->file_sertifikat) }}" target="_blank" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        <span>Berkas</span>
                                    </a>
                                    @if($item->status_hki === 'Terverifikasi HKI' && !$item->is_claimed_reward && $item->user_id === Auth::id())
                                        <a href="{{ route('reward.create', ['jenis' => 'HKI', 'id' => $item->id]) }}" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition shadow-xs flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>Klaim Reward</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $hkiList->links() }}
                    </div>
                @endif
            </div>
        </main>
    </div>
</div>
@endsection


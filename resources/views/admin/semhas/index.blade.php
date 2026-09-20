@extends('layouts.app')

@section('title', 'Seminar Hasil (Semhas) - PRISMA UHN')

@section('content')
<div x-data="{ 
    sidebarOpen: false, 
    scheduleModal: false, 
    gradeModal: false,
    selectedUsulan: null,
    selectedSemhas: null,
    formScheduleUrl: '',
    formGradeUrl: '',
    scheduleData: { jadwal_seminar: '', ruangan_or_link: '', id_penguji_1: '', id_penguji_2: '' },
    gradeData: { skor_seminar: '', catatan_penguji: '' }
}" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <x-sidebar />

    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        <header class="bg-white border-b border-slate-200 py-4 sticky top-0 z-30 shadow-sm">
            <div class="w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <h1 class="font-extrabold text-xl text-slate-900 leading-tight">Seminar Hasil (Semhas) & Dewan Penguji</h1>
                        <p class="text-xs font-semibold text-slate-500">Plotting Jadwal Semhas, Dewan Penguji, Input Nilai & Pantau Laporan Akhir (US-10.3)</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-black bg-blue-100 text-blue-800 border border-blue-300">
                        Admin P3M Portal
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

            @if($errors->any())
                <div class="p-4.5 rounded-2xl bg-red-50 border border-red-300 text-red-900 text-xs font-semibold shadow-sm">
                    <p class="font-bold text-sm mb-1">Mohon perbaiki kesalahan berikut:</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Summary Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-5 rounded-3xl bg-white border border-slate-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Total Pelaksanaan Hibah</span>
                        <div class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </div>
                    </div>
                    <span class="text-2xl font-black text-slate-900 mt-2 block">{{ $stats['total'] }} Usulan</span>
                    <p class="text-[11px] text-slate-400 mt-1">Status Ongoing & Completed</p>
                </div>

                <div class="p-5 rounded-3xl bg-amber-50/70 border border-amber-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-amber-900 uppercase tracking-wider">Belum Dijadwalkan</span>
                        <div class="w-8 h-8 rounded-xl bg-amber-100 flex items-center justify-center text-amber-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <span class="text-2xl font-black text-amber-800 mt-2 block">{{ $stats['unscheduled'] }} Usulan</span>
                    <p class="text-[11px] text-amber-600 mt-1">Perlu penetapan jadwal sidang</p>
                </div>

                <div class="p-5 rounded-3xl bg-blue-50/70 border border-blue-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-blue-900 uppercase tracking-wider">Sidang Terjadwal</span>
                        <div class="w-8 h-8 rounded-xl bg-blue-100 flex items-center justify-center text-blue-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    </div>
                    <span class="text-2xl font-black text-blue-800 mt-2 block">{{ $stats['scheduled'] }} Usulan</span>
                    <p class="text-[11px] text-blue-600 mt-1">Menunggu pelaksanaan & penilaian</p>
                </div>

                <div class="p-5 rounded-3xl bg-emerald-50/70 border border-emerald-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-emerald-900 uppercase tracking-wider">Selesai Dinilai</span>
                        <div class="w-8 h-8 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <span class="text-2xl font-black text-emerald-800 mt-2 block">{{ $stats['graded'] }} Usulan</span>
                    <p class="text-[11px] text-emerald-600 mt-1">{{ $stats['has_laporan'] }} usulan unggah naskah 100%</p>
                </div>
            </div>

            <!-- Filter Tabs & Search Bar -->
            <div class="bg-white p-4 rounded-3xl border border-slate-200 shadow-sm flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-1.5 text-xs font-bold">
                    <a href="{{ route('admin.semhas.index', array_merge(request()->except('page', 'filter'), ['filter' => 'all'])) }}"
                       class="px-3.5 py-2 rounded-xl transition {{ $filter === 'all' ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        Semua Usulan ({{ $stats['total'] }})
                    </a>
                    <a href="{{ route('admin.semhas.index', array_merge(request()->except('page', 'filter'), ['filter' => 'unscheduled'])) }}"
                       class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ $filter === 'unscheduled' ? 'bg-amber-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        <span>Belum Terjadwal</span>
                        @if($stats['unscheduled'] > 0)
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $filter === 'unscheduled' ? 'bg-white text-amber-800 font-black' : 'bg-amber-500 text-white font-black' }}">{{ $stats['unscheduled'] }}</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.semhas.index', array_merge(request()->except('page', 'filter'), ['filter' => 'scheduled'])) }}"
                       class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ $filter === 'scheduled' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        <span>Sidang Terjadwal</span>
                        @if($stats['scheduled'] > 0)
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $filter === 'scheduled' ? 'bg-white text-blue-800 font-black' : 'bg-blue-500 text-white font-black' }}">{{ $stats['scheduled'] }}</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.semhas.index', array_merge(request()->except('page', 'filter'), ['filter' => 'graded'])) }}"
                       class="px-3.5 py-2 rounded-xl transition {{ $filter === 'graded' ? 'bg-purple-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        Sudah Dinilai ({{ $stats['graded'] }})
                    </a>
                    <a href="{{ route('admin.semhas.index', array_merge(request()->except('page', 'filter'), ['filter' => 'has_laporan'])) }}"
                       class="px-3.5 py-2 rounded-xl transition {{ $filter === 'has_laporan' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        Laporan Akhir 100% ({{ $stats['has_laporan'] }})
                    </a>
                </div>

                <form method="GET" action="{{ route('admin.semhas.index') }}" class="flex items-center gap-2">
                    <input type="hidden" name="filter" value="{{ $filter }}">
                    <div class="relative w-full sm:w-64">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari judul / kode / ketua..."
                               class="w-full pl-9 pr-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    @if($search)
                        <a href="{{ route('admin.semhas.index', ['filter' => $filter]) }}" class="px-2.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold" title="Reset pencarian">
                            ✕
                        </a>
                    @endif
                </form>
            </div>

            <!-- Proposals Card List -->
            <div class="space-y-5">
                @forelse($proposals as $u)
                    @php
                        $semhas = $u->seminarHasil;
                        $monev = $u->monevKemajuan;
                        $lapAkhir = $u->laporanAkhir;
                        $isGraded = $semhas && $semhas->skor_seminar !== null;
                        $isScheduled = $semhas && $semhas->jadwal_seminar;
                    @endphp
                    <div class="bg-white rounded-3xl border border-slate-200 hover:border-slate-300 shadow-sm p-6 space-y-5 transition">
                        <!-- Card Header & Actions -->
                        <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4">
                            <div class="space-y-2">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded-md font-mono text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        {{ $u->kode_usulan }}
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-700">
                                        {{ $u->skema->nama_skema ?? 'Skema Riset' }}
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-700">
                                        Thn {{ $u->periode->tahun_anggaran ?? date('Y') }}
                                    </span>

                                    @if($isGraded)
                                        <span class="px-3 py-0.5 rounded-full text-[11px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Selesai Sidang (Nilai: {{ number_format($semhas->skor_seminar, 1) }})
                                        </span>
                                    @elseif($isScheduled)
                                        <span class="px-3 py-0.5 rounded-full text-[11px] font-black bg-blue-100 text-blue-800 border border-blue-300 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            Terjadwal ({{ $semhas->jadwal_seminar->isoFormat('D MMM Y, HH:mm') }} WIB)
                                        </span>
                                    @else
                                        <span class="px-3 py-0.5 rounded-full text-[11px] font-black bg-amber-100 text-amber-900 border border-amber-300 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                            Belum Dijadwalkan
                                        </span>
                                    @endif
                                </div>

                                <h3 class="text-base font-extrabold text-slate-900 leading-snug">
                                    {{ $u->judul_usulan }}
                                </h3>

                                <div class="flex flex-wrap items-center gap-x-5 gap-y-1 text-xs text-slate-500 pt-0.5">
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        Ketua: <strong class="text-slate-800">{{ $u->pengusul->name ?? '-' }}</strong>
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                        <span>{{ $u->pengusul->fakultas->nama_fakultas ?? '-' }} • {{ $u->pengusul->prodi->nama_prodi ?? '-' }}</span>
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>Pagu: <strong class="text-slate-700">Rp {{ number_format($u->kontrak?->pagu_disetujui ?? $u->total_rab, 0, ',', '.') }}</strong></span>
                                    </span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="shrink-0 flex flex-wrap items-center gap-2">
                                <button @click="
                                    selectedUsulan = {{ json_encode($u) }};
                                    formScheduleUrl = '{{ route('admin.semhas.schedule', $u) }}';
                                    scheduleData.jadwal_seminar = '{{ $semhas?->jadwal_seminar ? $semhas->jadwal_seminar->format('Y-m-d\TH:i') : '' }}';
                                    scheduleData.ruangan_or_link = '{{ addslashes($semhas?->ruangan_or_link ?? '') }}';
                                    scheduleData.id_penguji_1 = '{{ $semhas?->id_penguji_1 ?? '' }}';
                                    scheduleData.id_penguji_2 = '{{ $semhas?->id_penguji_2 ?? '' }}';
                                    scheduleModal = true;
                                " class="px-4 py-2 rounded-xl {{ $isScheduled ? 'bg-slate-100 hover:bg-slate-200 text-slate-700' : 'bg-blue-600 hover:bg-blue-700 text-white shadow-sm' }} font-extrabold text-xs transition flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span>{{ $isScheduled ? 'Ubah Jadwal & Penguji' : 'Jadwalkan Semhas' }}</span>
                                </button>

                                @if($semhas)
                                    <button @click="
                                        selectedSemhas = {{ json_encode($semhas) }};
                                        formGradeUrl = '{{ route('admin.semhas.grade', $semhas) }}';
                                        gradeData.skor_seminar = '{{ $semhas->skor_seminar ?? '' }}';
                                        gradeData.catatan_penguji = '{{ addslashes($semhas->catatan_penguji ?? '') }}';
                                        gradeModal = true;
                                    " class="px-4 py-2 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-700 font-extrabold text-xs border border-purple-200 transition flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        <span>{{ $isGraded ? 'Ubah Nilai Sidang' : 'Input Nilai Sidang' }}</span>
                                    </button>
                                @endif

                                @if($lapAkhir)
                                    <a href="{{ route('laporan-akhir.download-pengesahan', $u) }}" target="_blank"
                                       class="px-4 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-extrabold text-xs border border-emerald-200 transition flex items-center gap-1.5"
                                       title="Unduh Lembar Pengesahan Ber-QR Code">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span>Lembar Pengesahan (PDF)</span>
                                    </a>
                                @endif

                                @if(auth()->user()->hasRole(['Kepala P3M', 'Superadmin']))
                                    @if(!$semhas || $semhas->status_kelulusan !== 'lulus')
                                        <form method="POST" action="{{ route('admin.semhas.kepala-approval', $u) }}" class="inline">
                                            @csrf
                                            <input type="hidden" name="status" value="acc">
                                            <button type="submit" onclick="return confirm('ACC dan Luluskan Seminar Hasil usulan ini?')" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-sm transition flex items-center gap-1.5" title="Persetujuan Langsung Kepala LPPM">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                <span>ACC Semhas</span>
                                            </button>
                                        </form>
                                    @else
                                        <div class="flex items-center gap-1.5">
                                            <span class="px-3 py-1.5 rounded-xl bg-emerald-100 text-emerald-800 text-xs font-black flex items-center gap-1">
                                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                Telah di-ACC
                                            </span>
                                            <form method="POST" action="{{ route('admin.semhas.kepala-approval', $u) }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="status" value="revisi">
                                                <button type="submit" onclick="return confirm('Batalkan status ACC dan minta revisi naskah?')" class="px-2.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold border border-rose-200 transition">
                                                    Minta Revisi
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <!-- 4 Structured Information Panels -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3.5 pt-4 border-t border-slate-100 text-xs">
                            <!-- 1. Monev Kemajuan -->
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col justify-between space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Monev Kemajuan (70%)</span>
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                </div>
                                <div>
                                    @if($monev && $monev->status === 'evaluated')
                                        <div class="flex items-center gap-2">
                                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black {{ $monev->rekomendasi === 'Lanjut' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                                {{ $monev->rekomendasi }}
                                            </span>
                                            <span class="font-bold text-slate-800">Skor: {{ $monev->skor_monev }}</span>
                                        </div>
                                    @elseif($monev)
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-800">
                                            Menunggu Evaluasi
                                        </span>
                                    @else
                                        <span class="text-slate-400 italic text-[11px]">Belum Unggah Monev</span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-slate-500">
                                    @if($monev && $monev->persentase_kemajuan)
                                        Capaian: <strong class="text-slate-700">{{ $monev->persentase_kemajuan }}%</strong>
                                    @else
                                        Progress: -
                                    @endif
                                </div>
                            </div>

                            <!-- 2. Jadwal & Tempat Sidang -->
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col justify-between space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Jadwal & Tempat Sidang</span>
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <div>
                                    @if($isScheduled)
                                        <p class="font-extrabold text-slate-900 text-xs">
                                            {{ $semhas->jadwal_seminar->isoFormat('dddd, D MMM Y') }}
                                        </p>
                                        <p class="text-[11px] font-bold text-blue-700 mt-0.5">
                                            Pukul {{ $semhas->jadwal_seminar->format('H:i') }} WIB
                                        </p>
                                    @else
                                        <span class="text-amber-700 font-semibold text-[11px]">Belum Dijadwalkan</span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-slate-500 truncate" title="{{ $semhas?->ruangan_or_link ?? '-' }}">
                                    @if($semhas && $semhas->ruangan_or_link)
                                        Ruang: <span class="font-medium text-slate-700">{{ $semhas->ruangan_or_link }}</span>
                                    @else
                                        Ruangan: -
                                    @endif
                                </div>
                            </div>

                            <!-- 3. Dewan Penguji -->
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col justify-between space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Dewan Penguji Semhas</span>
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                </div>
                                <div class="space-y-1 text-[11px]">
                                    <div class="truncate" title="{{ $semhas?->penguji1?->name ?? 'Belum Ditunjuk' }}">
                                        <span class="text-slate-400 font-bold">P1:</span>
                                        <strong class="text-slate-800">{{ $semhas?->penguji1?->name ?? 'Belum Ditunjuk' }}</strong>
                                    </div>
                                    <div class="truncate" title="{{ $semhas?->penguji2?->name ?? 'Belum Ditunjuk' }}">
                                        <span class="text-slate-400 font-bold">P2:</span>
                                        <strong class="text-slate-800">{{ $semhas?->penguji2?->name ?? 'Belum Ditunjuk' }}</strong>
                                    </div>
                                </div>
                                <div class="text-[10px] text-slate-400">
                                    {{ ($semhas?->id_penguji_1 && $semhas?->id_penguji_2) ? 'Dewan Penguji Lengkap' : 'Penguji belum lengkap' }}
                                </div>
                            </div>

                            <!-- 4. Laporan Akhir & Hasil -->
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col justify-between space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Laporan Akhir 100%</span>
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <div>
                                    @if($lapAkhir)
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-200 inline-flex items-center gap-1">
                                            <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Naskah 100% Siap
                                        </span>
                                    @else
                                        <span class="text-slate-400 italic text-[11px]">Belum Diunggah</span>
                                    @endif
                                </div>
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="text-slate-500">Nilai Semhas:</span>
                                    @if($isGraded)
                                        <span class="font-black text-xs text-purple-700 bg-purple-50 px-2 py-0.5 rounded-md border border-purple-200">
                                            {{ number_format($semhas->skor_seminar, 1) }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 font-bold">-</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 rounded-3xl bg-white border border-slate-200 text-center space-y-3">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <h4 class="font-extrabold text-sm text-slate-800">Tidak ada usulan ditemukan</h4>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto">
                            @if($search)
                                Tidak ada usulan yang cocok dengan kata kunci pencarian "{{ $search }}".
                            @else
                                Belum ada usulan pada status filter ini.
                            @endif
                        </p>
                        @if($search || $filter !== 'all')
                            <div class="pt-2">
                                <a href="{{ route('admin.semhas.index') }}" class="px-4 py-2 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 transition">
                                    Reset Filter & Pencarian
                                </a>
                            </div>
                        @endif
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($proposals->hasPages())
                <div class="pt-2">
                    {{ $proposals->links() }}
                </div>
            @endif
        </main>
    </div>

    <!-- Modal Jadwal Semhas -->
    <div x-show="scheduleModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs">
        <div @click.away="scheduleModal = false" class="bg-white rounded-3xl p-6 max-w-lg w-full shadow-2xl space-y-4">
            <div class="flex justify-between items-center pb-3 border-b border-slate-100">
                <h3 class="font-extrabold text-base text-slate-900">Jadwalkan Seminar Hasil & Dewan Penguji</h3>
                <button @click="scheduleModal = false" class="text-slate-400 hover:text-slate-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form :action="formScheduleUrl" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tanggal & Waktu Sidang Semhas *</label>
                    <input type="datetime-local" name="jadwal_seminar" x-model="scheduleData.jadwal_seminar" required class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Ruangan / Tautan Sidang Daring (Zoom/GMeet) *</label>
                    <input type="text" name="ruangan_or_link" x-model="scheduleData.ruangan_or_link" required placeholder="Contoh: Ruang Sidang LPPM Gd. A Lt. 2 / https://meet.google.com/..." class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Dewan Penguji 1</label>
                        <select name="id_penguji_1" x-model="scheduleData.id_penguji_1" class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="">-- Pilih Penguji 1 --</option>
                            @foreach($examiners as $ex)
                                <option value="{{ $ex->id }}">{{ $ex->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Dewan Penguji 2</label>
                        <select name="id_penguji_2" x-model="scheduleData.id_penguji_2" class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="">-- Pilih Penguji 2 --</option>
                            @foreach($examiners as $ex)
                                <option value="{{ $ex->id }}">{{ $ex->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" @click="scheduleModal = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-md transition">
                        Simpan & Notifikasi Peneliti
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Input Nilai Semhas -->
    <div x-show="gradeModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs">
        <div @click.away="gradeModal = false" class="bg-white rounded-3xl p-6 max-w-lg w-full shadow-2xl space-y-4">
            <div class="flex justify-between items-center pb-3 border-b border-slate-100">
                <h3 class="font-extrabold text-base text-slate-900">Input Nilai & Berita Acara Seminar Hasil</h3>
                <button @click="gradeModal = false" class="text-slate-400 hover:text-slate-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form :action="formGradeUrl" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Skor Akhir Seminar Hasil (0 - 100) *</label>
                    <input type="number" step="0.1" min="0" max="100" name="skor_seminar" x-model="gradeData.skor_seminar" required placeholder="Contoh: 88.5" class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-300 text-xs font-bold focus:ring-2 focus:ring-purple-500 focus:outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Catatan & Masukan Dewan Penguji</label>
                    <textarea name="catatan_penguji" rows="4" x-model="gradeData.catatan_penguji" placeholder="Uraikan catatan revisi naskah akhir, masukan luaran publikasi, atau rekomendasi penyempurnaan..." class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-300 text-xs focus:ring-2 focus:ring-purple-500 focus:outline-none"></textarea>
                </div>

                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" @click="gradeModal = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-extrabold text-xs shadow-md transition">
                        Simpan Nilai Berita Acara
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


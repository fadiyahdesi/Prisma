@extends('layouts.app')

@php
    $kat = $kategori ?? 'penelitian';
    $isPengabdian = $kat === 'pengabdian';
    $labelBidang = $isPengabdian ? 'Pengabdian Masyarakat' : 'Penelitian Dosen';
    $isAdmin = $canViewAll ?? auth()->user()->isFullDivisionFor($kat);
    $canSubmitNew = auth()->user()->hasPermission('proposal.submit_wizard');
@endphp

@section('title', 'Database ' . $labelBidang . ' UHN - PRISMA UHN')

@section('content')
<div x-data="{
    sidebarOpen: false,
    ajukanModalOpen: false,
    createModalOpen: false,
    editModalOpen: false,
    detailModalOpen: false,
    detailData: {
        no_urut: '',
        semester: '',
        judul_usulan: '',
        nama_ketua: '',
        nidn_ketua: '',
        anggota_list: [],
        nama_prodi: '',
        nominal_formatted: '',
        status: '',
        kode_usulan: ''
    },
    editData: {
        update_url: '',
        no_urut: '',
        semester: '',
        judul_usulan: '',
        nama_ketua: '',
        nidn_ketua: '',
        nama_anggota: '',
        nama_prodi: '',
        total_rab: 0
    },
    openDetailModal(payload) {
        this.detailData = payload;
        this.detailModalOpen = true;
    },
    openEditModal(payload) {
        this.editData = payload;
        this.editModalOpen = true;
    }
}" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <x-sidebar />

    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        {{-- Topbar Header --}}
        <header class="bg-white border-b border-slate-200 py-3.5 sticky top-0 z-30 shadow-2xs">
            <div class="w-full px-4 sm:px-6 lg:px-8 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <button @click="sidebarOpen = true" type="button" class="lg:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <a href="{{ route('dashboard') }}" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center shrink-0 transition" title="Kembali ke Dashboard" aria-label="Kembali ke Dashboard">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </a>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 class="font-extrabold text-lg sm:text-xl text-slate-900 leading-tight">Data {{ $labelBidang }} UHN</h1>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                {{ number_format($totalAllCount) }} Data {{ $isPengabdian ? 'Pengabdian' : 'Penelitian' }}
                            </span>
                        </div>
                        <p class="text-xs font-medium text-slate-500 mt-0.5">
                            {{ $isAdmin ? 'Pusat Penelitian dan Pengabdian kepada Masyarakat (P3M) • Universitas Harkat Negeri' : 'Menampilkan data karya ' . strtolower($labelBidang) . ' yang Anda hasilkan' }}
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2 shrink-0">
                    @if($isAdmin)
                        <button type="button" @click="createModalOpen = true"
                                class="h-9 px-3.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Tambah Data {{ $isPengabdian ? 'Pengabdian' : 'Penelitian' }}</span>
                        </button>
                    @else
                        <span class="h-9 px-3 rounded-xl bg-slate-100 text-slate-600 border border-slate-200 font-semibold text-xs inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <span>Hanya Data Yang Anda Hasilkan</span>
                        </span>
                    @endif

                    @if($canSubmitNew)
                        <button type="button"
                                @click="ajukanModalOpen = true"
                                class="h-9 px-4 rounded-xl bg-[#681727] hover:bg-[#52111e] text-white font-extrabold text-xs shadow-xs transition inline-flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Ajukan Proposal Baru</span>
                        </button>
                    @endif
                </div>
            </div>
        </header>

        <main class="flex-grow w-full px-4 sm:px-6 lg:px-8 py-5 space-y-4">
            {{-- Flash Notifications --}}
            @if(session('success'))
                <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs sm:text-sm font-bold flex items-center gap-3 shadow-2xs">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs sm:text-sm font-bold flex items-center gap-3 shadow-2xs">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- Filter, Tabs & Ringkasan Nominal Utuh --}}
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-2xs space-y-4">
                {{-- Row 1: Category Tabs + Scope Switcher + Full Summary --}}
                <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-3 pb-3.5 border-b border-slate-100">
                    <div class="flex flex-wrap items-center gap-2">
                        <div class="inline-flex flex-wrap items-center gap-1 bg-slate-100 p-1 rounded-xl">
                            <a href="{{ route('usulan.index', ['kategori' => 'penelitian', 'scope' => $scope ?? 'all']) }}"
                               class="px-3.5 py-1.5 rounded-lg text-xs font-extrabold transition inline-flex items-center gap-1.5 {{ !$isPengabdian ? 'bg-[#681727] text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                                <span>Penelitian Dosen</span>
                                <span class="px-1.5 py-0.5 rounded-md text-[10px] tabular-nums {{ !$isPengabdian ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }}">
                                    {{ $totalPenelitianCount ?? 0 }}
                                </span>
                            </a>
                            <a href="{{ route('pengabdian.index', ['scope' => $scope ?? 'all']) }}"
                               class="px-3.5 py-1.5 rounded-lg text-xs font-extrabold transition inline-flex items-center gap-1.5 {{ $isPengabdian ? 'bg-[#681727] text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                                <span>Pengabdian Masyarakat</span>
                                <span class="px-1.5 py-0.5 rounded-md text-[10px] tabular-nums {{ $isPengabdian ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }}">
                                    {{ $totalPengabdianCount ?? 0 }}
                                </span>
                            </a>
                        </div>

                        @if($isAdmin)
                            <div class="inline-flex items-center gap-1 bg-slate-100 p-1 rounded-xl">
                                <a href="{{ route($isPengabdian ? 'pengabdian.index' : 'usulan.index', array_merge(request()->except('page'), ['kategori' => $kat, 'scope' => 'all'])) }}"
                                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ ($scope ?? 'all') === 'all' ? 'bg-slate-900 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                                    Semua ({{ $totalAllCount }})
                                </a>
                                <a href="{{ route($isPengabdian ? 'pengabdian.index' : 'usulan.index', array_merge(request()->except('page'), ['kategori' => $kat, 'scope' => 'mine'])) }}"
                                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ ($scope ?? 'all') === 'mine' ? 'bg-slate-900 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                                    Milik Saya ({{ $totalMineCount }})
                                </a>
                            </div>
                        @else
                            <span class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold">
                                Data Milik Saya ({{ $totalMineCount }})
                            </span>
                        @endif
                    </div>

                    {{-- Ringkasan Angka (Tanpa Terpotong) --}}
                    <div class="flex flex-wrap items-center gap-2 text-xs font-semibold">
                        <span class="px-2.5 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-600">
                            Periode: <strong class="text-slate-900">{{ $semesterList->count() }} Semester</strong>
                        </span>
                        <span class="px-2.5 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-600">
                            Prodi: <strong class="text-slate-900">{{ $prodiList->count() }} Unit</strong>
                        </span>
                        <span class="px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 whitespace-nowrap">
                            Akumulasi Dana: <strong class="font-black text-emerald-700 tabular-nums">Rp {{ number_format($totalNominal, 0, ',', '.') }}</strong>
                        </span>
                    </div>
                </div>

                {{-- Row 2: Pencarian & Filter Responsif --}}
                <form method="GET" action="{{ route($isPengabdian ? 'pengabdian.index' : 'usulan.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-2.5 items-center">
                    <input type="hidden" name="kategori" value="{{ $kat }}">
                    <input type="hidden" name="scope" value="{{ $scope ?? 'all' }}">

                    <div class="lg:col-span-4 relative">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Cari judul, ketua, NIDN/NUPN, atau anggota..."
                               class="w-full h-9 pl-9 pr-3 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium text-slate-900 focus:bg-white focus:ring-2 focus:ring-slate-900 focus:outline-hidden">
                    </div>

                    <div class="lg:col-span-3">
                        <select name="semester" onchange="this.form.submit()" class="w-full h-9 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 focus:bg-white focus:ring-2 focus:ring-slate-900 focus:outline-hidden">
                            <option value="">Semua Semester ({{ $semesterList->count() }})</option>
                            @foreach($semesterList as $sem)
                                <option value="{{ $sem }}" {{ request('semester') === $sem ? 'selected' : '' }}>{{ $sem }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="lg:col-span-3">
                        <select name="prodi" onchange="this.form.submit()" class="w-full h-9 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 focus:bg-white focus:ring-2 focus:ring-slate-900 focus:outline-hidden">
                            <option value="">Semua Program Studi ({{ $prodiList->count() }})</option>
                            @foreach($prodiList as $pr)
                                <option value="{{ $pr }}" {{ request('prodi') === $pr ? 'selected' : '' }}>{{ $pr }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="lg:col-span-2 flex items-center gap-1.5">
                        <select name="per_page" onchange="this.form.submit()" title="Jumlah data per halaman"
                                class="h-9 px-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 focus:bg-white focus:ring-2 focus:ring-slate-900 focus:outline-hidden">
                            <option value="25" {{ ($perPage ?? 25) == 25 ? 'selected' : '' }}>25 / hal</option>
                            <option value="50" {{ ($perPage ?? 25) == 50 ? 'selected' : '' }}>50 / hal</option>
                            <option value="100" {{ ($perPage ?? 25) == 100 ? 'selected' : '' }}>100 / hal</option>
                            <option value="250" {{ ($perPage ?? 25) == 250 ? 'selected' : '' }}>250 / hal</option>
                            <option value="500" {{ ($perPage ?? 25) == 500 ? 'selected' : '' }}>Semua</option>
                        </select>

                        <button type="submit" class="flex-1 h-9 px-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition cursor-pointer">
                            Filter
                        </button>

                        @if(request()->anyFilled(['search', 'semester', 'prodi']))
                            <a href="{{ route($isPengabdian ? 'pengabdian.index' : 'usulan.index', ['kategori' => $kat, 'scope' => $scope ?? 'all', 'per_page' => $perPage ?? 25]) }}"
                               class="h-9 px-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition inline-flex items-center justify-center" title="Reset Filter">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Tabel 9 Kolom Resmi Rapi & Mudah Dibaca --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-extrabold uppercase tracking-wider text-[11px]">
                                <th class="py-3 px-2.5 w-11 text-center">No</th>
                                <th class="py-3 px-2.5 w-28">Semester</th>
                                <th class="py-3 px-3 min-w-[230px]">Judul</th>
                                <th class="py-3 px-2.5 min-w-[140px]">Ketua</th>
                                <th class="py-3 px-2.5 w-24 whitespace-nowrap">NIDN / NUPN</th>
                                <th class="py-3 px-2.5 min-w-[145px]">Anggota</th>
                                <th class="py-3 px-2.5 min-w-[120px]">Prodi</th>
                                <th class="py-3 px-2.5 w-28 text-right whitespace-nowrap">Nominal</th>
                                <th class="py-3 px-3 w-24 text-center whitespace-nowrap">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200/70 text-xs">
                            @forelse($proposals as $idx => $p)
                                @php
                                    $semesterVal = $p->semester ?: ($p->periode ? trim($p->periode->tahun_akademik . ' ' . $p->periode->semester) : '-');
                                    $noUrutVal = $p->no_urut ?? ($proposals->firstItem() + $idx);
                                    $ketuaVal = $p->nama_ketua ?: ($p->pengusul?->name ?? '-');
                                    $nidnVal = $p->nidn_ketua ?: ($p->pengusul?->nidn_nim ?? '-');
                                    $anggotaVal = $p->nama_anggota;
                                    if (empty($anggotaVal) || $anggotaVal === '-') {
                                        $anggotaNames = $p->anggota?->pluck('nama')->filter()->values()->all() ?? [];
                                        $anggotaVal = !empty($anggotaNames) ? implode('; ', $anggotaNames) : '-';
                                    }
                                    $anggotaItems = ($anggotaVal && $anggotaVal !== '-')
                                        ? collect(explode(';', $anggotaVal))->map(fn ($v) => trim($v))->filter()->values()
                                        : collect();
                                    $prodiVal = $p->nama_prodi ?: ($p->prodi?->nama_prodi ?? ($p->pengusul?->prodi?->nama_prodi ?? '-'));
                                    $nominalVal = (float) ($p->total_rab ?? 0);
                                    $nominalFormatted = 'Rp ' . number_format($nominalVal, 0, ',', '.');
                                    $canManage = $isAdmin || ($p->id_pengusul === auth()->id() && $p->status === 'Draft');
                                @endphp
                                <tr class="odd:bg-white even:bg-slate-50/45 hover:bg-slate-100/60 transition-colors align-top">
                                    {{-- 1. No --}}
                                    <td class="py-3 px-2.5 text-center font-bold text-slate-400 tabular-nums">
                                        {{ $noUrutVal }}
                                    </td>

                                    {{-- 2. Semester --}}
                                    <td class="py-3 px-2.5">
                                        <span class="inline-block px-2 py-0.5 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-700 leading-tight">
                                            {{ $semesterVal }}
                                        </span>
                                    </td>

                                    {{-- 3. Judul (klik untuk detail ringkas) --}}
                                    <td class="py-3 px-3">
                                        <button type="button"
                                                @click="openDetailModal({{ json_encode([
                                                    'no_urut' => $noUrutVal,
                                                    'semester' => $semesterVal,
                                                    'judul_usulan' => $p->judul_usulan ?: '(Judul Belum Diisi)',
                                                    'nama_ketua' => $ketuaVal,
                                                    'nidn_ketua' => $nidnVal,
                                                    'anggota_list' => $anggotaItems->all(),
                                                    'nama_prodi' => $prodiVal,
                                                    'nominal_formatted' => $nominalFormatted,
                                                    'status' => $p->no_urut === null ? $p->status : 'Tercatat Resmi',
                                                    'kode_usulan' => $p->kode_usulan ?? '-',
                                                ]) }})"
                                                class="text-left font-semibold text-slate-900 hover:text-[#681727] text-xs leading-relaxed transition cursor-pointer">
                                            {{ $p->judul_usulan ?: '(Judul Belum Diisi)' }}
                                        </button>
                                        @if($p->no_urut === null)
                                            <div class="mt-1.5 flex flex-wrap items-center gap-1.5">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $p->status === 'Approved' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($p->status === 'Rejected' ? 'bg-rose-50 text-rose-700 border-rose-200' : ($p->status === 'Draft' ? 'bg-slate-100 text-slate-700 border-slate-300' : 'bg-amber-50 text-amber-800 border-amber-200')) }}">
                                                    {{ $p->status }}
                                                </span>
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                                    {{ $p->kode_usulan }}
                                                </span>
                                            </div>
                                        @endif
                                    </td>

                                    {{-- 4. Ketua --}}
                                    <td class="py-3 px-2.5">
                                        <div class="font-bold text-slate-800 text-xs leading-snug">
                                            {{ $ketuaVal }}
                                        </div>
                                    </td>

                                    {{-- 5. NIDN / NUPN --}}
                                    <td class="py-3 px-2.5 whitespace-nowrap">
                                        @if(!empty($nidnVal) && $nidnVal !== '-')
                                            <span class="text-[11px] font-semibold text-slate-600 tabular-nums">
                                                {{ $nidnVal }}
                                            </span>
                                        @else
                                            <span class="text-slate-400 text-[11px]">-</span>
                                        @endif
                                    </td>

                                    {{-- 6. Anggota --}}
                                    <td class="py-3 px-2.5">
                                        @if($anggotaItems->isNotEmpty())
                                            @if($anggotaItems->count() === 1)
                                                <span class="text-[11px] font-medium text-slate-600 leading-snug block">
                                                    {{ $anggotaItems->first() }}
                                                </span>
                                            @else
                                                <ul class="space-y-0.5 text-[11px] font-medium text-slate-600 leading-snug">
                                                    @foreach($anggotaItems as $memberName)
                                                        <li class="flex items-start gap-1">
                                                            <span class="text-slate-400 select-none">&bull;</span>
                                                            <span>{{ $memberName }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        @else
                                            <span class="text-slate-400 text-[11px]">-</span>
                                        @endif
                                    </td>

                                    {{-- 7. Prodi --}}
                                    <td class="py-3 px-2.5">
                                        @if(!empty($prodiVal) && $prodiVal !== '-')
                                            <span class="inline-flex items-start gap-1.5 text-[11px] font-semibold text-slate-700 leading-snug">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mt-1 shrink-0"></span>
                                                <span>{{ $prodiVal }}</span>
                                            </span>
                                        @else
                                            <span class="text-slate-400 text-[11px]">-</span>
                                        @endif
                                    </td>

                                    {{-- 8. Nominal --}}
                                    <td class="py-3 px-2.5 text-right whitespace-nowrap">
                                        <span class="{{ $nominalVal > 0 ? 'font-bold text-slate-900' : 'font-medium text-slate-400' }} tabular-nums text-xs">
                                            {{ $nominalFormatted }}
                                        </span>
                                    </td>

                                    {{-- 9. Aksi (Jelas & Tidak Menabrak) --}}
                                    <td class="py-3 px-3 text-center whitespace-nowrap">
                                        @if($canManage)
                                            <div class="flex flex-col gap-1 items-stretch">
                                                <button type="button"
                                                        @click="openEditModal({{ json_encode([
                                                            'update_url' => route('usulan.update-basic', $p->id),
                                                            'no_urut' => $noUrutVal,
                                                            'semester' => $semesterVal,
                                                            'judul_usulan' => $p->judul_usulan,
                                                            'nama_ketua' => $ketuaVal,
                                                            'nidn_ketua' => $nidnVal,
                                                            'nama_anggota' => $anggotaVal,
                                                            'nama_prodi' => $prodiVal,
                                                            'total_rab' => round($nominalVal),
                                                        ]) }})"
                                                        class="h-7 px-2.5 rounded-lg bg-slate-100 hover:bg-slate-800 text-slate-700 hover:text-white border border-slate-200 font-bold text-[11px] transition inline-flex items-center justify-center gap-1 cursor-pointer">
                                                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                    <span>Edit</span>
                                                </button>

                                                <form method="POST" action="{{ route('usulan.destroy', $p->id) }}"
                                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus data No. {{ $noUrutVal }} ini?');"
                                                      class="m-0 p-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="w-full h-7 px-2.5 rounded-lg bg-rose-50 hover:bg-rose-600 text-rose-700 hover:text-white border border-rose-200 font-bold text-[11px] transition inline-flex items-center justify-center gap-1 cursor-pointer">
                                                        <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        <span>Hapus</span>
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <a href="{{ route('usulan.step', ['usulan' => $p->id, 'step' => $p->status === 'Draft' ? 1 : 6]) }}"
                                               class="h-7 px-2.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 font-bold text-[11px] transition inline-flex items-center justify-center">
                                                Detail
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="py-12 px-4 text-center">
                                        <p class="text-slate-600 font-bold text-sm mb-1">Belum ada data {{ strtolower($labelBidang) }} yang sesuai</p>
                                        <p class="text-slate-400 text-xs">
                                            @if(request()->anyFilled(['search', 'semester', 'prodi']))
                                                Ubah kata kunci pencarian atau klik Reset untuk melihat seluruh daftar.
                                            @elseif($isAdmin)
                                                Gunakan tombol Tambah Data di pojok kanan atas untuk menambahkan data baru.
                                            @else
                                                Data yang Anda ketuai atau ikuti sebagai anggota akan tampil di sini.
                                            @endif
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-5 py-3.5 border-t border-slate-200 bg-slate-50/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <p class="text-xs font-medium text-slate-500">
                        Menampilkan <strong class="text-slate-800">{{ $proposals->firstItem() ?? 0 }}&ndash;{{ $proposals->lastItem() ?? 0 }}</strong> dari total <strong class="text-slate-800">{{ number_format($proposals->total()) }}</strong> data {{ strtolower($labelBidang) }}
                    </p>
                    @if($proposals->hasPages())
                        <div>
                            {{ $proposals->onEachSide(1)->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>

    {{-- Datalist Program Studi Resmi UHN --}}
    <datalist id="prodiOptionsList">
        @foreach($allMasterProdi ?? $prodiList as $optProdi)
            <option value="{{ $optProdi }}"></option>
        @endforeach
    </datalist>

    {{-- Modal Detail Ringkas Saat Judul Diklik --}}
    <div x-show="detailModalOpen"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4"
         @keydown.escape.window="detailModalOpen = false">
        <div @click.away="detailModalOpen = false"
             class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-xl overflow-hidden">
            <div class="px-5 py-4 bg-slate-900 text-white flex items-center justify-between">
                <div>
                    <h3 class="font-extrabold text-sm sm:text-base">Rincian {{ $labelBidang }} #<span x-text="detailData.no_urut"></span></h3>
                    <p class="text-[11px] text-slate-300 font-medium" x-text="detailData.semester + ' • ' + detailData.status"></p>
                </div>
                <button type="button" @click="detailModalOpen = false" class="text-white/80 hover:text-white p-1 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-5 space-y-4 text-xs">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Judul Usulan</span>
                    <p class="text-sm font-bold text-slate-900 mt-1 leading-relaxed" x-text="detailData.judul_usulan"></p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-slate-50 p-3.5 rounded-xl border border-slate-200">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Ketua Peneliti</span>
                        <p class="font-bold text-slate-900 mt-0.5" x-text="detailData.nama_ketua"></p>
                        <p class="text-[11px] text-slate-500 mt-0.5" x-text="'NIDN / NUPN: ' + detailData.nidn_ketua"></p>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Program Studi &amp; Nominal</span>
                        <p class="font-semibold text-slate-800 mt-0.5" x-text="detailData.nama_prodi"></p>
                        <p class="font-black text-emerald-700 text-sm mt-0.5 tabular-nums" x-text="detailData.nominal_formatted"></p>
                    </div>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Anggota Tim</span>
                    <template x-if="detailData.anggota_list && detailData.anggota_list.length > 0">
                        <ul class="mt-1.5 space-y-1 text-slate-700 font-medium list-disc list-inside">
                            <template x-for="(m, idx) in detailData.anggota_list" :key="idx">
                                <li x-text="m"></li>
                            </template>
                        </ul>
                    </template>
                    <template x-if="!detailData.anggota_list || detailData.anggota_list.length === 0">
                        <p class="text-slate-400 mt-1">Tidak ada anggota tercatat.</p>
                    </template>
                </div>
                <div class="pt-2 border-t border-slate-100 flex justify-end">
                    <button type="button" @click="detailModalOpen = false"
                            class="h-9 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Pilih Skema BIMA & Mulai Ajukan Proposal Baru --}}
    <div x-show="ajukanModalOpen"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4"
         @keydown.escape.window="ajukanModalOpen = false">
        <div @click.away="ajukanModalOpen = false"
             class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-3xl max-h-[88vh] flex flex-col overflow-hidden">
            <div class="px-5 py-4 bg-gradient-to-r from-[#681727] to-[#8C1D32] text-white flex items-center justify-between shrink-0">
                <div>
                    <h3 class="font-extrabold text-sm sm:text-base">Ajukan Proposal {{ $labelBidang }} Baru (Wizard 6 Langkah)</h3>
                    <p class="text-[11px] text-rose-100 font-medium">
                        Pilih skema hibah aktif{{ $activePeriod ? ' untuk periode ' . $activePeriod->tahun_akademik : '' }} agar usulan tersimpan sebagai draf &amp; masuk alur verifikasi Divisi.
                    </p>
                </div>
                <button type="button" @click="ajukanModalOpen = false" class="text-white/80 hover:text-white p-1 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-5 overflow-y-auto space-y-4">
                @if(!$activePeriod)
                    <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-300 text-amber-900 text-xs font-bold">
                        Saat ini belum ada periode pengajuan hibah yang berstatus terbuka. Hubungi Admin P3M untuk membuka jadwal Call for Proposals.
                    </div>
                @else
                    @php
                        $matchedSchemes = ($activeSchemes ?? collect())->filter(function ($s) use ($kategori) {
                            return strtolower((string) $s->kategori) === strtolower((string) $kategori);
                        });
                        $displaySchemes = $matchedSchemes->isNotEmpty() ? $matchedSchemes : ($activeSchemes ?? collect());
                    @endphp

                    @if($displaySchemes->isEmpty())
                        <div class="p-6 text-center rounded-xl border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-500">
                            Belum ada skema aktif untuk kategori {{ strtolower($labelBidang) }}.
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            @foreach($displaySchemes as $skema)
                                <div class="rounded-xl border border-slate-200 hover:border-rose-300 bg-slate-50/70 hover:bg-white p-4 flex flex-col justify-between transition shadow-xs">
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase bg-rose-100 text-[#681727]">
                                                {{ $skema->kode_skema }}
                                            </span>
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase bg-slate-100 text-slate-700">
                                                {{ ucfirst($skema->kategori) }}
                                            </span>
                                        </div>
                                        <h4 class="text-sm font-extrabold text-slate-900 leading-snug">{{ $skema->nama_skema }}</h4>
                                        <div class="text-[11px] text-slate-600 space-y-1 font-medium">
                                            <div>Pagu Maksimal: <span class="font-extrabold text-slate-800">Rp {{ number_format((float) $skema->plafon_dana, 0, ',', '.') }}</span></div>
                                            <div>Rentang TKT: <span class="font-bold text-slate-800">TKT {{ $skema->min_tkt ?? 1 }} &ndash; {{ $skema->max_tkt ?? 9 }}</span></div>
                                            @if($skema->min_sinta_overall || $skema->min_sinta_3yr)
                                                <div>Min. SINTA Score: <span class="font-bold text-slate-800">{{ number_format((float) max($skema->min_sinta_overall ?? 0, $skema->min_sinta_3yr ?? 0), 0) }}</span></div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="pt-3 mt-3 border-t border-slate-200">
                                        <a href="{{ route('usulan.start', $skema->id) }}"
                                           class="w-full h-9 px-3 rounded-lg bg-[#681727] hover:bg-[#52111e] text-white text-xs font-extrabold flex items-center justify-center gap-1.5 transition shadow-xs">
                                            <span>Mulai Pengisian Wizard</span>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    {{-- Modal Tambah Data 8 Kolom --}}
    <div x-show="createModalOpen"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4"
         @keydown.escape.window="createModalOpen = false">
        <div @click.away="createModalOpen = false"
             class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-2xl overflow-hidden">
            <div class="px-5 py-4 bg-[#681727] text-white flex items-center justify-between">
                <div>
                    <h3 class="font-extrabold text-sm sm:text-base">Tambah Data {{ $labelBidang }}</h3>
                    <p class="text-[11px] text-rose-100 font-medium">Lengkapi 8 kolom rincian {{ strtolower($labelBidang) }} UHN</p>
                </div>
                <button type="button" @click="createModalOpen = false" class="text-white/80 hover:text-white p-1 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('usulan.store-basic') }}" class="p-5 space-y-4">
                @csrf
                <input type="hidden" name="kategori_usulan" value="{{ $kat }}">

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 mb-1">No Urut (Opsional)</label>
                        <input type="number" name="no_urut" placeholder="Otomatis"
                               class="w-full h-9 px-3 rounded-xl border border-slate-300 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-slate-900 focus:outline-hidden">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-extrabold text-slate-700 mb-1">Semester</label>
                        <input type="text" name="semester" value="2024 2025 Genap" required placeholder="Contoh: 2024 2025 Genap"
                               class="w-full h-9 px-3 rounded-xl border border-slate-300 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-slate-900 focus:outline-hidden">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 mb-1">Judul {{ $labelBidang }}</label>
                    <textarea name="judul_usulan" rows="3" required placeholder="Masukkan judul lengkap..."
                              class="w-full p-3 rounded-xl border border-slate-300 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-slate-900 focus:outline-hidden"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-extrabold text-slate-700 mb-1">Ketua</label>
                        <input type="text" name="nama_ketua" required placeholder="Nama lengkap Ketua beserta gelar"
                               class="w-full h-9 px-3 rounded-xl border border-slate-300 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-slate-900 focus:outline-hidden">
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 mb-1">NIDN / NUPN</label>
                        <input type="text" name="nidn_ketua" placeholder="Contoh: 0612079602 atau -"
                               class="w-full h-9 px-3 rounded-xl border border-slate-300 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-slate-900 focus:outline-hidden">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 mb-1">Anggota</label>
                    <input type="text" name="nama_anggota" placeholder="Nama dosen/mahasiswa anggota (pisahkan dengan titik koma ;)"
                           class="w-full h-9 px-3 rounded-xl border border-slate-300 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-slate-900 focus:outline-hidden">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 mb-1">Program Studi (Prodi)</label>
                        <input type="text" list="prodiOptionsList" name="nama_prodi" required placeholder="Pilih atau ketik Program Studi"
                               class="w-full h-9 px-3 rounded-xl border border-slate-300 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-slate-900 focus:outline-hidden">
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 mb-1">Nominal (Rp)</label>
                        <input type="number" step="1" min="0" name="total_rab" value="0" required
                               class="w-full h-9 px-3 rounded-xl border border-slate-300 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-slate-900 focus:outline-hidden">
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-200 flex items-center justify-end gap-2">
                    <button type="button" @click="createModalOpen = false"
                            class="h-9 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                            class="h-9 px-5 rounded-xl bg-[#681727] hover:bg-[#52111e] text-white font-extrabold text-xs shadow-xs transition cursor-pointer">
                        Simpan Data {{ $isPengabdian ? 'Pengabdian' : 'Penelitian' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit Data --}}
    <div x-show="editModalOpen"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4"
         @keydown.escape.window="editModalOpen = false">
        <div @click.away="editModalOpen = false"
             class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-2xl overflow-hidden">
            <div class="px-5 py-4 bg-[#681727] text-white flex items-center justify-between">
                <div>
                    <h3 class="font-extrabold text-sm sm:text-base">Edit Data {{ $labelBidang }}</h3>
                    <p class="text-[11px] text-rose-100 font-medium">Perbarui rincian {{ strtolower($labelBidang) }} UHN</p>
                </div>
                <button type="button" @click="editModalOpen = false" class="text-white/80 hover:text-white p-1 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" :action="editData.update_url" class="p-5 space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 mb-1">No Urut</label>
                        <input type="number" name="no_urut" x-model="editData.no_urut" required
                               class="w-full h-9 px-3 rounded-xl border border-slate-300 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-slate-900 focus:outline-hidden">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-extrabold text-slate-700 mb-1">Semester</label>
                        <input type="text" name="semester" x-model="editData.semester" required placeholder="Contoh: 2024 2025 Genap"
                               class="w-full h-9 px-3 rounded-xl border border-slate-300 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-slate-900 focus:outline-hidden">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 mb-1">Judul {{ $labelBidang }}</label>
                    <textarea name="judul_usulan" x-model="editData.judul_usulan" rows="3" required
                              class="w-full p-3 rounded-xl border border-slate-300 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-slate-900 focus:outline-hidden"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-extrabold text-slate-700 mb-1">Ketua</label>
                        <input type="text" name="nama_ketua" x-model="editData.nama_ketua" required
                               class="w-full h-9 px-3 rounded-xl border border-slate-300 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-slate-900 focus:outline-hidden">
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 mb-1">NIDN / NUPN</label>
                        <input type="text" name="nidn_ketua" x-model="editData.nidn_ketua"
                               class="w-full h-9 px-3 rounded-xl border border-slate-300 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-slate-900 focus:outline-hidden">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 mb-1">Anggota</label>
                    <input type="text" name="nama_anggota" x-model="editData.nama_anggota"
                           class="w-full h-9 px-3 rounded-xl border border-slate-300 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-slate-900 focus:outline-hidden">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 mb-1">Program Studi (Prodi)</label>
                        <input type="text" list="prodiOptionsList" name="nama_prodi" x-model="editData.nama_prodi" required
                               class="w-full h-9 px-3 rounded-xl border border-slate-300 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-slate-900 focus:outline-hidden">
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 mb-1">Nominal (Rp)</label>
                        <input type="number" step="1" min="0" name="total_rab" x-model="editData.total_rab" required
                               class="w-full h-9 px-3 rounded-xl border border-slate-300 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-slate-900 focus:outline-hidden">
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-200 flex items-center justify-end gap-2">
                    <button type="button" @click="editModalOpen = false"
                            class="h-9 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                            class="h-9 px-5 rounded-xl bg-[#681727] hover:bg-[#52111e] text-white font-extrabold text-xs shadow-xs transition cursor-pointer">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

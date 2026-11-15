@extends('layouts.app')

@php
    $kat = $kategori ?? 'penelitian';
    $isPengabdian = $kat === 'pengabdian';
    $labelBidang = $isPengabdian ? 'Pengabdian Masyarakat' : 'Penelitian Dosen';
    $isAdmin = auth()->user()->hasRole(['Superadmin', 'Admin P3M', 'Kepala P3M', 'Divisi Penelitian', 'Divisi Pengabdian']);
@endphp

@section('title', 'Database ' . $labelBidang . ' UHN - PRISMA UHN')

@section('content')
<div x-data="{
    sidebarOpen: false,
    createModalOpen: false,
    editModalOpen: false,
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
    openEditModal(payload) {
        this.editData = payload;
        this.editModalOpen = true;
    }
}" class="min-h-screen bg-slate-100 text-slate-800 flex">
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
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 class="font-extrabold text-lg sm:text-xl text-slate-900 leading-tight">Database {{ $labelBidang }} UHN</h1>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold {{ $isPengabdian ? 'bg-purple-100 text-purple-800 border border-purple-200' : 'bg-emerald-100 text-emerald-800 border border-emerald-200' }}">
                                {{ $totalAllCount }} Data {{ $isPengabdian ? 'Pengabdian' : 'Penelitian' }}
                            </span>
                        </div>
                        <p class="text-xs font-semibold text-slate-500">
                            Kolom standar: <strong class="text-slate-700">Semester &bull; No &bull; Judul &bull; Ketua &bull; NIDN / NUPN &bull; Anggota &bull; Prodi &bull; Nominal</strong>
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    @if($isPengabdian)
                        <form method="POST" action="{{ route('usulan.sync-penelitian') }}" class="inline-flex m-0 p-0"
                              onsubmit="return confirm('Sinkronkan ulang 420 Data Pengabdian Masyarakat Dosen dari CSV resmi?');">
                            @csrf
                            <input type="hidden" name="kategori" value="pengabdian">
                            <button type="submit"
                                    class="h-9 px-3.5 rounded-xl bg-purple-50 hover:bg-purple-600 text-purple-700 hover:text-white border border-purple-200 hover:border-purple-600 font-extrabold text-xs shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                <span>Sinkronkan 420 Pengabdian CSV</span>
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('usulan.sync-penelitian') }}" class="inline-flex m-0 p-0"
                              onsubmit="return confirm('Sinkronkan ulang 438 Data Penelitian Dosen dari CSV resmi?');">
                            @csrf
                            <input type="hidden" name="kategori" value="penelitian">
                            <button type="submit"
                                    class="h-9 px-3.5 rounded-xl bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200 hover:border-emerald-600 font-extrabold text-xs shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                <span>Sinkronkan 438 Penelitian CSV</span>
                            </button>
                        </form>
                    @endif

                    @if($isAdmin)
                        <button type="button" @click="createModalOpen = true"
                                class="h-9 px-3.5 rounded-xl bg-blue-50 hover:bg-blue-600 text-blue-700 hover:text-white border border-blue-200 hover:border-blue-600 font-extrabold text-xs shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Tambah Data {{ $isPengabdian ? 'Pengabdian' : 'Penelitian' }}</span>
                        </button>
                    @endif

                    <a href="{{ route('sinta.profile') }}"
                       style="background-color: #681727 !important; color: #ffffff !important;"
                       class="h-9 px-4 rounded-xl text-white font-extrabold text-xs shadow-xs transition inline-flex items-center gap-1.5 hover:opacity-90">
                        <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span style="color: #ffffff !important;">Ajukan Proposal Baru</span>
                    </a>
                </div>
            </div>
        </header>

        <main class="flex-grow w-full px-4 sm:px-6 lg:px-8 py-6 space-y-4">
            <!-- Flash Notifications -->
            @if(session('success'))
                <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs sm:text-sm font-bold flex items-center gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
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
                    <div class="flex flex-wrap items-center gap-2">
                        <!-- Switcher Bidang: Penelitian vs Pengabdian -->
                        <div class="inline-flex flex-wrap items-center gap-1 bg-slate-100 p-1 rounded-xl">
                            <a href="{{ route('usulan.index', ['kategori' => 'penelitian', 'scope' => $scope ?? 'all']) }}"
                               class="px-3 py-1.5 rounded-lg text-xs font-extrabold transition inline-flex items-center gap-1.5 {{ !$isPengabdian ? 'bg-[#681727] text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                                <span>🔬 Penelitian Dosen</span>
                                <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ !$isPengabdian ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }}">
                                    {{ $totalPenelitianCount ?? 438 }}
                                </span>
                            </a>
                            <a href="{{ route('pengabdian.index', ['scope' => $scope ?? 'all']) }}"
                               class="px-3 py-1.5 rounded-lg text-xs font-extrabold transition inline-flex items-center gap-1.5 {{ $isPengabdian ? 'bg-[#681727] text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                                <span>🤝 Pengabdian Masyarakat</span>
                                <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $isPengabdian ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }}">
                                    {{ $totalPengabdianCount ?? 0 }}
                                </span>
                            </a>
                        </div>

                        <!-- Switcher Scope: Semua vs Milik Saya -->
                        <div class="inline-flex flex-wrap items-center gap-1 bg-slate-100 p-1 rounded-xl">
                            <a href="{{ route($isPengabdian ? 'pengabdian.index' : 'usulan.index', array_merge(request()->except('page'), ['kategori' => $kat, 'scope' => 'all'])) }}"
                               class="px-2.5 py-1.5 rounded-lg text-xs font-bold transition {{ ($scope ?? 'all') === 'all' ? 'bg-slate-800 text-white' : 'text-slate-600 hover:text-slate-900' }}">
                                Semua ({{ $totalAllCount }})
                            </a>
                            <a href="{{ route($isPengabdian ? 'pengabdian.index' : 'usulan.index', array_merge(request()->except('page'), ['kategori' => $kat, 'scope' => 'mine'])) }}"
                               class="px-2.5 py-1.5 rounded-lg text-xs font-bold transition {{ ($scope ?? 'all') === 'mine' ? 'bg-slate-800 text-white' : 'text-slate-600 hover:text-slate-900' }}">
                                Milik Saya ({{ $totalMineCount }})
                            </a>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 text-xs font-bold text-slate-600">
                        <span class="px-2.5 py-1 rounded-lg bg-slate-100 border border-slate-200 text-slate-700">
                            Periode: <strong class="text-slate-900">{{ $semesterList->count() }} Semester</strong>
                        </span>
                        <span class="px-2.5 py-1 rounded-lg bg-blue-50 border border-blue-200 text-blue-800">
                            Menampilkan <strong class="font-extrabold">{{ $proposals->count() }}</strong> dari <strong class="font-extrabold">{{ $proposals->total() }}</strong> data
                        </span>
                        <span class="px-2.5 py-1 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800">
                            Total Nominal: <strong class="font-extrabold">Rp. {{ number_format($totalNominal, 0, '.', ',') }}</strong>
                        </span>
                    </div>
                </div>

                <!-- Row 2: Search & Filter Form -->
                <form method="GET" action="{{ route($isPengabdian ? 'pengabdian.index' : 'usulan.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-2.5 items-center">
                    <input type="hidden" name="kategori" value="{{ $kat }}">
                    <input type="hidden" name="scope" value="{{ $scope ?? 'all' }}">

                    <div class="lg:col-span-4 relative">
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Cari Judul, Ketua, NIDN / NUPN, atau Anggota..."
                               class="w-full h-9 pl-9 pr-3 rounded-xl bg-slate-50 border border-slate-300 text-xs font-semibold text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-600 focus:outline-hidden">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>

                    <div class="lg:col-span-3">
                        <select name="semester" class="w-full h-9 px-3 rounded-xl bg-slate-50 border border-slate-300 text-xs font-semibold text-slate-700 focus:bg-white focus:ring-2 focus:ring-blue-600 focus:outline-hidden">
                            <option value="">Semua Semester ({{ $semesterList->count() }} Periode)</option>
                            @foreach($semesterList as $sem)
                                <option value="{{ $sem }}" {{ request('semester') === $sem ? 'selected' : '' }}>{{ $sem }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="lg:col-span-3">
                        <select name="prodi" class="w-full h-9 px-3 rounded-xl bg-slate-50 border border-slate-300 text-xs font-semibold text-slate-700 focus:bg-white focus:ring-2 focus:ring-blue-600 focus:outline-hidden">
                            <option value="">Semua Program Studi ({{ $prodiList->count() }} Prodi)</option>
                            @foreach($prodiList as $pr)
                                <option value="{{ $pr }}" {{ request('prodi') === $pr ? 'selected' : '' }}>{{ $pr }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="lg:col-span-2 flex items-center gap-1.5">
                        <select name="per_page" onchange="this.form.submit()" title="Jumlah baris per halaman"
                                class="h-9 px-2.5 rounded-xl bg-slate-50 border border-slate-300 text-xs font-bold text-slate-700 focus:bg-white focus:ring-2 focus:ring-blue-600 focus:outline-hidden">
                            <option value="500" {{ ($perPage ?? 500) == 500 ? 'selected' : '' }}>Semua</option>
                            <option value="250" {{ ($perPage ?? 500) == 250 ? 'selected' : '' }}>250 Baris</option>
                            <option value="100" {{ ($perPage ?? 500) == 100 ? 'selected' : '' }}>100 Baris</option>
                            <option value="50" {{ ($perPage ?? 500) == 50 ? 'selected' : '' }}>50 Baris</option>
                            <option value="25" {{ ($perPage ?? 500) == 25 ? 'selected' : '' }}>25 Baris</option>
                        </select>

                        <button type="submit" class="flex-1 h-9 px-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs transition shadow-2xs cursor-pointer">
                            Filter
                        </button>

                        @if(request()->anyFilled(['search', 'semester', 'prodi']))
                            <a href="{{ route($isPengabdian ? 'pengabdian.index' : 'usulan.index', ['kategori' => $kat, 'scope' => $scope ?? 'all', 'per_page' => $perPage ?? 500]) }}"
                               class="h-9 px-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition inline-flex items-center justify-center" title="Reset Filter">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Main Table: 8 Standard Columns (Semester | No | Judul | Ketua | NIDN / NUPN | Anggota | Prodi | Nominal) -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/90 border-b border-slate-200 text-slate-600 font-extrabold uppercase tracking-wider text-[11px]">
                                <th class="py-3.5 px-3.5 w-36 whitespace-nowrap">Semester</th>
                                <th class="py-3.5 px-2.5 w-14 text-center">No</th>
                                <th class="py-3.5 px-4 min-w-[280px] max-w-[390px]">Judul</th>
                                <th class="py-3.5 px-3.5 min-w-[185px]">Ketua</th>
                                <th class="py-3.5 px-3.5 w-32 whitespace-nowrap">NIDN / NUPN</th>
                                <th class="py-3.5 px-3.5 min-w-[195px] max-w-[260px]">Anggota</th>
                                <th class="py-3.5 px-3.5 min-w-[165px]">Prodi</th>
                                <th class="py-3.5 px-3.5 w-36 text-right whitespace-nowrap">Nominal</th>
                                <th class="py-3.5 px-4 w-40 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
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
                                    $prodiVal = $p->nama_prodi ?: ($p->prodi?->nama_prodi ?? ($p->pengusul?->prodi?->nama_prodi ?? '-'));
                                    $nominalVal = (float) ($p->total_rab ?? 0);
                                    $canManage = $isAdmin || $p->id_pengusul === auth()->id();
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition-colors align-top">
                                    <!-- 1. Semester -->
                                    <td class="py-3 px-3.5 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-extrabold bg-blue-50 text-blue-700 border border-blue-200">
                                            {{ $semesterVal }}
                                        </span>
                                    </td>

                                    <!-- 2. No -->
                                    <td class="py-3 px-2.5 text-center font-extrabold text-slate-500">
                                        <span class="inline-flex items-center justify-center min-w-7 h-7 px-1.5 rounded-lg bg-slate-100 text-slate-700 font-extrabold text-xs border border-slate-200">
                                            {{ $noUrutVal }}
                                        </span>
                                    </td>

                                    <!-- 3. Judul -->
                                    <td class="py-3 px-4">
                                        <div class="font-bold text-slate-900 text-xs sm:text-[13px] leading-relaxed">
                                            {{ $p->judul_usulan ?: '(Judul Belum Diisi)' }}
                                        </div>
                                    </td>

                                    <!-- 4. Ketua -->
                                    <td class="py-3 px-3.5">
                                        <div class="font-extrabold text-slate-900 text-xs leading-snug">
                                            {{ $ketuaVal }}
                                        </div>
                                    </td>

                                    <!-- 5. NIDN / NUPN -->
                                    <td class="py-3 px-3.5 whitespace-nowrap">
                                        @if(!empty($nidnVal) && $nidnVal !== '-')
                                            <span class="inline-block font-mono font-bold text-slate-800 bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200 text-[11px]">
                                                {{ $nidnVal }}
                                            </span>
                                        @else
                                            <span class="inline-block text-slate-400 font-bold text-xs px-2 py-0.5 bg-slate-50 rounded border border-slate-200">-</span>
                                        @endif
                                    </td>

                                    <!-- 6. Anggota -->
                                    <td class="py-3 px-3.5">
                                        @if(!empty($anggotaVal) && $anggotaVal !== '-')
                                            <div class="text-xs font-semibold text-slate-700 leading-relaxed">
                                                {{ $anggotaVal }}
                                            </div>
                                        @else
                                            <span class="text-slate-400 italic text-[11px]">-</span>
                                        @endif
                                    </td>

                                    <!-- 7. Prodi -->
                                    <td class="py-3 px-3.5">
                                        @if(!empty($prodiVal) && $prodiVal !== '-')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 leading-tight">
                                                {{ $prodiVal }}
                                            </span>
                                        @else
                                            <span class="text-slate-400 italic text-[11px]">-</span>
                                        @endif
                                    </td>

                                    <!-- 8. Nominal -->
                                    <td class="py-3 px-3.5 text-right whitespace-nowrap">
                                        @if($nominalVal > 0)
                                            <span class="inline-block font-mono font-extrabold text-slate-900 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-200 text-xs">
                                                Rp. {{ number_format($nominalVal, 0, '.', ',') }}
                                            </span>
                                        @else
                                            <span class="inline-block font-mono font-bold text-slate-500 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-200 text-xs">
                                                Rp. 0
                                            </span>
                                        @endif
                                    </td>

                                    <!-- 9. Aksi (Edit & Hapus Rapi Sejajar) -->
                                    <td class="py-3 px-4 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center justify-end gap-1.5">
                                            @if($canManage)
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
                                                        class="h-8 px-3 rounded-lg bg-blue-50 hover:bg-blue-600 text-blue-700 hover:text-white border border-blue-200 hover:border-blue-600 font-extrabold text-xs transition inline-flex items-center justify-center gap-1.5 shadow-2xs cursor-pointer"
                                                        title="Edit Data">
                                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                    <span>Edit</span>
                                                </button>

                                                <form method="POST" action="{{ route('usulan.destroy', $p->id) }}"
                                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus data No. {{ $noUrutVal }} ini?');"
                                                      class="inline-flex m-0 p-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="h-8 px-3 rounded-lg bg-rose-50 hover:bg-rose-600 text-rose-700 hover:text-white border border-rose-200 hover:border-rose-600 font-extrabold text-xs transition inline-flex items-center justify-center gap-1.5 shadow-2xs cursor-pointer"
                                                            title="Hapus Data">
                                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        <span>Hapus</span>
                                                    </button>
                                                </form>
                                            @else
                                                <a href="{{ route('usulan.step', ['usulan' => $p->id, 'step' => 6]) }}"
                                                   class="h-8 px-3 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 font-extrabold text-xs transition inline-flex items-center justify-center gap-1.5">
                                                    <span>Detail</span>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="py-12 text-center">
                                        <p class="text-slate-600 font-bold text-sm mb-1">Belum ada data {{ strtolower($labelBidang) }} yang ditampilkan</p>
                                        <p class="text-slate-400 text-xs mb-3">Silakan gunakan tombol Tambah Data {{ $isPengabdian ? 'Pengabdian' : 'Penelitian' }} atau unggah data CSV resmi.</p>
                                        @if($isAdmin)
                                            <button type="button" @click="createModalOpen = true"
                                                    class="h-9 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs transition inline-flex items-center gap-1.5 cursor-pointer">
                                                + Tambah Data {{ $labelBidang }}
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($proposals->hasPages())
                    <div class="p-4 border-t border-slate-200 bg-slate-50">
                        {{ $proposals->links() }}
                    </div>
                @endif
            </div>
        </main>
    </div>

    <!-- Modal Tambah Data 8 Kolom (Penelitian / Pengabdian) -->
    <div x-show="createModalOpen"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4"
         @keydown.escape.window="createModalOpen = false">
        <div @click.away="createModalOpen = false"
             class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-2xl overflow-hidden">
            <div class="px-5 py-4 bg-[#681727] text-white flex items-center justify-between">
                <div>
                    <h3 class="font-extrabold text-sm sm:text-base">Tambah Data {{ $labelBidang }}</h3>
                    <p class="text-[11px] text-rose-100 font-medium">Input 8 kolom standar {{ strtolower($labelBidang) }} UHN</p>
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
                               class="w-full h-9 px-3 rounded-xl border border-slate-300 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-blue-600 focus:outline-hidden">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-extrabold text-slate-700 mb-1">Semester</label>
                        <input type="text" name="semester" value="2024 2025 Genap" required placeholder="Contoh: 2024 2025 Genap"
                               class="w-full h-9 px-3 rounded-xl border border-slate-300 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-blue-600 focus:outline-hidden">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 mb-1">Judul {{ $labelBidang }}</label>
                    <textarea name="judul_usulan" rows="3" required placeholder="Masukkan judul lengkap..."
                              class="w-full p-3 rounded-xl border border-slate-300 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-blue-600 focus:outline-hidden"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-extrabold text-slate-700 mb-1">Ketua</label>
                        <input type="text" name="nama_ketua" required placeholder="Nama lengkap Ketua beserta gelar"
                               class="w-full h-9 px-3 rounded-xl border border-slate-300 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-blue-600 focus:outline-hidden">
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 mb-1">NIDN / NUPN</label>
                        <input type="text" name="nidn_ketua" placeholder="Contoh: 0612079602 atau -"
                               class="w-full h-9 px-3 rounded-xl border border-slate-300 text-xs font-mono font-bold text-slate-900 focus:ring-2 focus:ring-blue-600 focus:outline-hidden">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 mb-1">Anggota</label>
                    <input type="text" name="nama_anggota" placeholder="Nama dosen/mahasiswa anggota (pisahkan dengan titik koma ;)"
                           class="w-full h-9 px-3 rounded-xl border border-slate-300 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-blue-600 focus:outline-hidden">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 mb-1">Program Studi (Prodi)</label>
                        <input type="text" name="nama_prodi" required placeholder="Contoh: DIII Farmasi / Sarjana Terapan Teknik Informatika"
                               class="w-full h-9 px-3 rounded-xl border border-slate-300 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-blue-600 focus:outline-hidden">
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 mb-1">Nominal (Rp)</label>
                        <input type="number" step="1" min="0" name="total_rab" value="0" required
                               class="w-full h-9 px-3 rounded-xl border border-slate-300 text-xs font-mono font-bold text-slate-900 focus:ring-2 focus:ring-blue-600 focus:outline-hidden">
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-200 flex items-center justify-end gap-2">
                    <button type="button" @click="createModalOpen = false"
                            class="h-9 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                            style="background-color: #681727 !important; color: #ffffff !important;"
                            class="h-9 px-5 rounded-xl text-white font-extrabold text-xs shadow-xs transition hover:opacity-90 cursor-pointer">
                        Simpan Data {{ $isPengabdian ? 'Pengabdian' : 'Penelitian' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit 8 Kolom Standar (Alpine.js x-show + x-cloak) -->
    <div x-show="editModalOpen"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4"
         @keydown.escape.window="editModalOpen = false">
        <div @click.away="editModalOpen = false"
             class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-2xl overflow-hidden">
            <div class="px-5 py-4 bg-[#681727] text-white flex items-center justify-between">
                <div>
                    <h3 class="font-extrabold text-sm sm:text-base">Edit Data {{ $labelBidang }}</h3>
                    <p class="text-[11px] text-rose-100 font-medium">Perbarui 8 kolom standar {{ strtolower($labelBidang) }} UHN</p>
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
                               class="w-full h-9 px-3 rounded-xl border border-slate-300 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-blue-600 focus:outline-hidden">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-extrabold text-slate-700 mb-1">Semester</label>
                        <input type="text" name="semester" x-model="editData.semester" required placeholder="Contoh: 2024 2025 Genap"
                               class="w-full h-9 px-3 rounded-xl border border-slate-300 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-blue-600 focus:outline-hidden">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 mb-1">Judul {{ $labelBidang }}</label>
                    <textarea name="judul_usulan" x-model="editData.judul_usulan" rows="3" required
                              class="w-full p-3 rounded-xl border border-slate-300 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-blue-600 focus:outline-hidden"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-extrabold text-slate-700 mb-1">Ketua</label>
                        <input type="text" name="nama_ketua" x-model="editData.nama_ketua" required
                               class="w-full h-9 px-3 rounded-xl border border-slate-300 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-blue-600 focus:outline-hidden">
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 mb-1">NIDN / NUPN</label>
                        <input type="text" name="nidn_ketua" x-model="editData.nidn_ketua"
                               class="w-full h-9 px-3 rounded-xl border border-slate-300 text-xs font-mono font-bold text-slate-900 focus:ring-2 focus:ring-blue-600 focus:outline-hidden">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 mb-1">Anggota</label>
                    <input type="text" name="nama_anggota" x-model="editData.nama_anggota"
                           class="w-full h-9 px-3 rounded-xl border border-slate-300 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-blue-600 focus:outline-hidden">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 mb-1">Program Studi (Prodi)</label>
                        <input type="text" name="nama_prodi" x-model="editData.nama_prodi" required
                               class="w-full h-9 px-3 rounded-xl border border-slate-300 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-blue-600 focus:outline-hidden">
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 mb-1">Nominal (Rp)</label>
                        <input type="number" step="1" min="0" name="total_rab" x-model="editData.total_rab" required
                               class="w-full h-9 px-3 rounded-xl border border-slate-300 text-xs font-mono font-bold text-slate-900 focus:ring-2 focus:ring-blue-600 focus:outline-hidden">
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-200 flex items-center justify-end gap-2">
                    <button type="button" @click="editModalOpen = false"
                            class="h-9 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                            style="background-color: #681727 !important; color: #ffffff !important;"
                            class="h-9 px-5 rounded-xl text-white font-extrabold text-xs shadow-xs transition hover:opacity-90 cursor-pointer">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

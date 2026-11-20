@extends('layouts.app')

@section('title', 'Data Publikasi Dosen UHN - PRISMA UHN')

@section('content')
<div x-data="{
    sidebarOpen: false,
    openCreateModal: false,
    editModalOpen: false,
    editData: {
        id: null,
        action: '',
        no_urut: '',
        jenis_publikasi: 'nasional terakreditasi',
        judul_artikel: '',
        nama_jurnal: '',
        issn: '',
        volume_nomor: '',
        penulis: ''
    },
    verifyModalOpen: false,
    verifyData: {
        id: null,
        action: '',
        no_urut: '',
        judul_artikel: '',
        template_status: 'sesuai',
        similarity_score: 12.0,
        status_verifikasi: 'Terverifikasi',
        catatan_verifikasi: ''
    },
    openEdit(item) {
        this.editData = {
            id: item.id,
            action: item.update_url,
            no_urut: item.no_urut,
            jenis_publikasi: item.jenis_publikasi,
            judul_artikel: item.judul_artikel,
            nama_jurnal: item.nama_jurnal,
            issn: item.issn,
            volume_nomor: item.volume_nomor,
            penulis: item.penulis
        };
        this.editModalOpen = true;
    },
    openVerify(item) {
        this.verifyData = {
            id: item.id,
            action: item.verify_url,
            no_urut: item.no_urut,
            judul_artikel: item.judul_artikel,
            template_status: item.template_status || 'sesuai',
            similarity_score: item.similarity_score ?? 12.0,
            status_verifikasi: item.status_verifikasi || 'Terverifikasi',
            catatan_verifikasi: item.catatan_verifikasi || ''
        };
        this.verifyModalOpen = true;
    }
}" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <x-sidebar />

    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        <!-- Top Header -->
        <header class="bg-white border-b border-slate-200 py-4 sticky top-0 z-30 shadow-xs">
            <div class="w-full px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $isStaff ? 'bg-indigo-100 text-indigo-800 border border-indigo-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                {{ $isStaff ? 'Divisi Publikasi • Database Resmi UHN' : 'Publikasi Ilmiah Saya • Repositori Pribadi' }}
                            </span>
                        </div>
                        <h1 class="font-extrabold text-xl text-slate-900 leading-tight mt-0.5">Data Publikasi Ilmiah Dosen UHN</h1>
                        <p class="text-xs font-semibold text-slate-500">
                            Kolom Standar Sistem: Jenis Publikasi &bull; Judul &bull; Nama Jurnal &bull; ISSN &bull; Vol. No. hal. &bull; Penulis
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    @if($isStaff)
                        <form action="{{ route('publikasi.sync') }}" method="POST"
                              data-confirm="Sinkronkan ulang 589 data Publikasi Dosen UHN dari file CSV resmi?"
                              data-confirm-title="Konfirmasi Sinkronisasi Data Publikasi"
                              data-confirm-variant="warning"
                              data-confirm-label="Ya, Sinkronkan Data">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200 font-bold text-xs shadow-xs transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                <span>Sinkronkan CSV (589 Data)</span>
                            </button>
                        </form>
                        <button type="button" @click="openCreateModal = true" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-xs transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Tambah Data Publikasi</span>
                        </button>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 text-slate-700 border border-slate-200 font-bold text-xs">
                            <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <span>Hanya Publikasi Yang Anda Hasilkan</span>
                        </span>
                    @endif
                </div>
            </div>
        </header>

        <main class="flex-grow w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-sm font-bold flex items-center gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-300 text-rose-900 text-sm font-bold flex items-center gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Summary Metric Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">{{ $isStaff ? 'Total Publikasi Dosen' : 'Publikasi Milik Saya' }}</p>
                        <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($totalAllCount) }}</p>
                        <p class="text-[11px] font-semibold text-emerald-600 mt-0.5">{{ number_format($countTerverifikasi) }} Terverifikasi Divisi Publikasi</p>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Nasional Terakreditasi</p>
                        <p class="text-2xl font-black text-blue-700 mt-1">{{ number_format($countNasionalTerakreditasi) }}</p>
                        <p class="text-[11px] font-semibold text-slate-500 mt-0.5">Jurnal SINTA 1 - SINTA 6</p>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Internasional</p>
                        <p class="text-2xl font-black text-amber-600 mt-1">{{ number_format($countInternasional) }}</p>
                        <p class="text-[11px] font-semibold text-slate-500 mt-0.5">Jurnal &amp; Prosiding Internasional</p>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Nasional Tidak Terakreditasi</p>
                        <p class="text-2xl font-black text-teal-700 mt-1">{{ number_format($countNasionalTidakTerakreditasi) }}</p>
                        <p class="text-[11px] font-semibold text-slate-500 mt-0.5">Jurnal Nasional Ber-ISSN</p>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                    </div>
                </div>
            </div>

            <!-- Filter & Search Bar -->
            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs">
                <form method="GET" action="{{ route('publikasi.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
                    <div class="md:col-span-4 relative">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul, nama jurnal, ISSN, vol/hal, atau penulis..." class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-indigo-500 bg-slate-50/50">
                    </div>

                    <div class="md:col-span-3">
                        <select name="jenis_publikasi" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-indigo-500 bg-white">
                            <option value="">Semua Jenis Publikasi ({{ $totalAllCount }})</option>
                            <option value="nasional terakreditasi" {{ request('jenis_publikasi') === 'nasional terakreditasi' ? 'selected' : '' }}>nasional terakreditasi ({{ $countNasionalTerakreditasi }})</option>
                            <option value="internasional" {{ request('jenis_publikasi') === 'internasional' ? 'selected' : '' }}>internasional ({{ $countInternasional }})</option>
                            <option value="nasional tidak terakreditasi" {{ request('jenis_publikasi') === 'nasional tidak terakreditasi' ? 'selected' : '' }}>nasional tidak terakreditasi ({{ $countNasionalTidakTerakreditasi }})</option>
                            <option value="-" {{ request('jenis_publikasi') === '-' ? 'selected' : '' }}>Belum Ditentukan (-)</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        @if($isStaff)
                            <select name="scope" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-indigo-500 bg-white">
                                <option value="all" {{ ($scope ?? 'all') === 'all' ? 'selected' : '' }}>Semua Dosen UHN</option>
                                <option value="mine" {{ ($scope ?? 'all') === 'mine' ? 'selected' : '' }}>Publikasi Saya</option>
                            </select>
                        @else
                            <div class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold bg-slate-100 text-slate-700 text-center">
                                Publikasi Saya ({{ $totalAllCount }})
                            </div>
                        @endif
                    </div>

                    <div class="md:col-span-1">
                        <select name="per_page" onchange="this.form.submit()" class="w-full px-2.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-indigo-500 bg-white">
                            <option value="1000" {{ ($perPage ?? 1000) == 1000 ? 'selected' : '' }}>Semua</option>
                            <option value="250" {{ ($perPage ?? 1000) == 250 ? 'selected' : '' }}>250</option>
                            <option value="100" {{ ($perPage ?? 1000) == 100 ? 'selected' : '' }}>100</option>
                            <option value="50" {{ ($perPage ?? 1000) == 50 ? 'selected' : '' }}>50</option>
                        </select>
                    </div>

                    <div class="md:col-span-2 flex items-center gap-2">
                        <button type="submit" class="flex-1 px-4 py-2 bg-slate-900 text-white rounded-xl font-bold text-xs hover:bg-slate-800 transition">
                            Cari
                        </button>
                        @if(request('search') || request('jenis_publikasi') || request('scope') === 'mine')
                            <a href="{{ route('publikasi.index') }}" class="px-3 py-2 bg-slate-100 text-slate-600 rounded-xl font-bold text-xs hover:bg-slate-200 transition">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Main Table Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2 bg-slate-50/60">
                    <div>
                        <h2 class="font-extrabold text-sm text-slate-900">
                            {{ $isStaff ? 'Tabel Data Publikasi Dosen Universitas Harkat Negeri' : 'Tabel Data Publikasi Yang Anda Hasilkan' }}
                        </h2>
                        <p class="text-xs text-slate-500 font-semibold">
                            Menampilkan <strong class="text-slate-800">{{ $publikasiList->count() }}</strong> dari total <strong class="text-slate-800">{{ $publikasiList->total() }}</strong> data publikasi {{ $isStaff ? 'resmi' : 'milik Anda' }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2 text-[11px] font-bold text-slate-500">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-teal-50 text-teal-700 border border-teal-200">
                            <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                            Cek Template &amp; Similarity Aktif
                        </span>
                    </div>
                </div>

                @if($publikasiList->isEmpty())
                    <div class="p-12 text-center">
                        <p class="font-bold text-slate-700 text-sm">
                            {{ $isStaff ? 'Tidak ada data publikasi yang sesuai dengan pencarian.' : 'Belum ada data publikasi milik Anda yang tercatat.' }}
                        </p>
                        @if(request('search') || request('jenis_publikasi'))
                            <a href="{{ route('publikasi.index') }}" class="inline-block mt-3 px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold">Reset Pencarian</a>
                        @endif
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-900 text-white text-[11px] font-extrabold uppercase tracking-wider">
                                    <th class="py-3.5 px-3 text-center w-12 border-r border-slate-800">No</th>
                                    <th class="py-3.5 px-3.5 w-40 border-r border-slate-800">Jenis Publikasi</th>
                                    <th class="py-3.5 px-4 min-w-[260px] border-r border-slate-800">Judul</th>
                                    <th class="py-3.5 px-3.5 w-52 border-r border-slate-800">Nama Jurnal</th>
                                    <th class="py-3.5 px-3 w-36 border-r border-slate-800">ISSN</th>
                                    <th class="py-3.5 px-3 w-40 border-r border-slate-800">Vol. No. hal.</th>
                                    <th class="py-3.5 px-4 min-w-[230px] border-r border-slate-800">Penulis</th>
                                    <th class="py-3.5 px-3 w-40 text-center border-r border-slate-800">Verifikasi</th>
                                    <th class="py-3.5 px-3 w-36 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 text-xs">
                                @foreach($publikasiList as $index => $item)
                                    @php
                                        $no = $item->no_urut ?? ($publikasiList->firstItem() + $index);
                                        $jenis = $item->jenis_publikasi ?? '-';
                                        $jenisLower = strtolower($jenis);
                                        $verifStatus = $item->status_verifikasi ?? 'Terverifikasi';
                                        $tplStatus = $item->template_status ?? 'sesuai';
                                        $simScore = $item->similarity_score ?? 12.0;

                                        $rowPayload = [
                                            'id' => $item->id,
                                            'no_urut' => $no,
                                            'jenis_publikasi' => $jenis,
                                            'judul_artikel' => $item->judul_artikel ?? '-',
                                            'nama_jurnal' => $item->nama_jurnal ?? '-',
                                            'issn' => $item->issn ?? '-',
                                            'volume_nomor' => $item->volume_nomor ?? '-',
                                            'penulis' => $item->penulis ?? ($item->user->name ?? '-'),
                                            'template_status' => $tplStatus,
                                            'similarity_score' => (float) $simScore,
                                            'status_verifikasi' => $verifStatus,
                                            'catatan_verifikasi' => $item->catatan_verifikasi ?? '',
                                            'update_url' => route('publikasi.update-basic', $item),
                                            'verify_url' => route('publikasi.verify', $item),
                                        ];
                                    @endphp
                                    <tr class="hover:bg-indigo-50/40 transition align-top odd:bg-white even:bg-slate-50/40">
                                        <!-- No -->
                                        <td class="py-3 px-3 text-center font-extrabold text-slate-700 border-r border-slate-100">
                                            {{ $no }}
                                        </td>

                                        <!-- Jenis Publikasi -->
                                        <td class="py-3 px-3.5 border-r border-slate-100">
                                            @if($jenisLower === 'internasional')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-extrabold bg-amber-100 text-amber-800 border border-amber-300">
                                                    {{ $jenis }}
                                                </span>
                                            @elseif($jenisLower === 'nasional terakreditasi')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-extrabold bg-blue-100 text-blue-800 border border-blue-300">
                                                    {{ $jenis }}
                                                </span>
                                            @elseif($jenisLower === 'nasional tidak terakreditasi')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-extrabold bg-teal-100 text-teal-800 border border-teal-300">
                                                    {{ $jenis }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-[11px] font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                                    {{ $jenis }}
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Judul -->
                                        <td class="py-3 px-4 border-r border-slate-100">
                                            <div class="font-bold text-slate-900 leading-snug">
                                                {{ $item->judul_artikel ?: '-' }}
                                            </div>
                                        </td>

                                        <!-- Nama Jurnal -->
                                        <td class="py-3 px-3.5 border-r border-slate-100 font-semibold text-slate-700 leading-snug">
                                            {{ $item->nama_jurnal ?: '-' }}
                                        </td>

                                        <!-- ISSN -->
                                        <td class="py-3 px-3 border-r border-slate-100 font-mono text-[11px] font-bold text-slate-700">
                                            {{ $item->issn ?: '-' }}
                                        </td>

                                        <!-- Vol. No. hal. -->
                                        <td class="py-3 px-3 border-r border-slate-100 font-semibold text-slate-700">
                                            {{ $item->volume_nomor ?: '-' }}
                                        </td>

                                        <!-- Penulis -->
                                        <td class="py-3 px-4 border-r border-slate-100 text-slate-700 font-medium leading-relaxed">
                                            {{ $item->penulis ?: ($item->user->name ?? '-') }}
                                        </td>

                                        <!-- Verifikasi (Template & Similarity) -->
                                        <td class="py-3 px-3 border-r border-slate-100 text-center">
                                            <div class="inline-flex flex-col items-center gap-1">
                                                <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold {{ $verifStatus === 'Terverifikasi' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : ($verifStatus === 'Revisi' ? 'bg-amber-100 text-amber-800 border border-amber-300' : 'bg-rose-100 text-rose-800 border border-rose-300') }}">
                                                    {{ $verifStatus }}
                                                </span>
                                                <span class="text-[10px] font-bold text-slate-500">
                                                    Tpl: {{ ucfirst($tplStatus) }} &bull; {{ number_format((float)$simScore, 0) }}%
                                                </span>
                                            </div>
                                        </td>

                                        <!-- Aksi -->
                                        <td class="py-3 px-3 text-center whitespace-nowrap">
                                            <div class="inline-flex items-center justify-center gap-1">
                                                @if($isStaff)
                                                    <button type="button"
                                                        @click="openEdit(@js($rowPayload))"
                                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-500 text-amber-700 hover:text-white border border-amber-200 font-bold text-[11px] transition shadow-2xs"
                                                        title="Edit Data Publikasi">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                        <span>Edit</span>
                                                    </button>

                                                    <button type="button"
                                                        @click="openVerify(@js($rowPayload))"
                                                        class="inline-flex items-center gap-1 px-2 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-600 text-indigo-700 hover:text-white border border-indigo-200 font-bold text-[11px] transition shadow-2xs"
                                                        title="Verifikasi Template & Similarity">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    </button>

                                                    <form action="{{ route('publikasi.destroy', $item) }}" method="POST" class="inline"
                                                          data-confirm="Apakah Anda yakin ingin menghapus data publikasi ilmiah No. {{ $no }} ini dari sistem?"
                                                          data-confirm-title="Konfirmasi Hapus Data Publikasi"
                                                          data-confirm-detail="No. {{ $no }} • {{ $item->judul_publikasi }}"
                                                          data-confirm-variant="danger"
                                                          data-confirm-label="Ya, Hapus Publikasi">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white border border-rose-200 font-bold text-[11px] transition shadow-2xs"
                                                            title="Hapus Data Publikasi">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                            <span>Hapus</span>
                                                        </button>
                                                    </form>
                                                @else
                                                    <a href="{{ route('publikasi.show', $item) }}"
                                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 font-bold text-[11px] transition shadow-2xs">
                                                        <span>Detail</span>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($publikasiList->hasPages())
                        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                            {{ $publikasiList->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </main>
    </div>

    @if($isStaff)
    <!-- Modal Tambah Data Publikasi (6 Kolom CSV) -->
    <div x-show="openCreateModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="openCreateModal = false" class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-lg overflow-hidden">
            <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between">
                <div>
                    <h3 class="font-extrabold text-sm">Tambah Data Publikasi Dosen</h3>
                    <p class="text-[11px] text-slate-300">Sesuai Format Kolom Standar Publikasi UHN</p>
                </div>
                <button type="button" @click="openCreateModal = false" class="text-slate-400 hover:text-white text-xl leading-none">&times;</button>
            </div>
            <form action="{{ route('publikasi.store-basic') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase text-slate-600 mb-1">No Urut</label>
                        <input type="number" name="no_urut" value="{{ $totalAllCount + 1 }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-extrabold uppercase text-slate-600 mb-1">Jenis Publikasi</label>
                        <select name="jenis_publikasi" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold">
                            <option value="nasional terakreditasi">nasional terakreditasi</option>
                            <option value="internasional">internasional</option>
                            <option value="nasional tidak terakreditasi">nasional tidak terakreditasi</option>
                            <option value="-">-</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-extrabold uppercase text-slate-600 mb-1">Judul</label>
                    <textarea name="judul_artikel" rows="2" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold" placeholder="Judul artikel publikasi..."></textarea>
                </div>

                <div>
                    <label class="block text-[11px] font-extrabold uppercase text-slate-600 mb-1">Nama Jurnal</label>
                    <input type="text" name="nama_jurnal" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold" placeholder="Contoh: Jurnal Masyarakat Mandiri">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase text-slate-600 mb-1">ISSN</label>
                        <input type="text" name="issn" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold" placeholder="Contoh: 2614-5758">
                    </div>
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase text-slate-600 mb-1">Vol. No. hal.</label>
                        <input type="text" name="volume_nomor" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold" placeholder="Contoh: Vol. 9 No. 2 hal. 1-10">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-extrabold uppercase text-slate-600 mb-1">Penulis</label>
                    <textarea name="penulis" rows="2" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold" placeholder="Nama Penulis (NIPY), Anggota Penulis..."></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="openCreateModal = false" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs">Simpan Publikasi</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Data Publikasi -->
    <div x-show="editModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="editModalOpen = false" class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-lg overflow-hidden">
            <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between">
                <div>
                    <h3 class="font-extrabold text-sm">Edit Data Publikasi Dosen</h3>
                    <p class="text-[11px] text-slate-300">No. Urut <span x-text="editData.no_urut"></span></p>
                </div>
                <button type="button" @click="editModalOpen = false" class="text-slate-400 hover:text-white text-xl leading-none">&times;</button>
            </div>
            <form :action="editData.action" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase text-slate-600 mb-1">No Urut</label>
                        <input type="number" name="no_urut" x-model="editData.no_urut" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-extrabold uppercase text-slate-600 mb-1">Jenis Publikasi</label>
                        <select name="jenis_publikasi" x-model="editData.jenis_publikasi" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold">
                            <option value="nasional terakreditasi">nasional terakreditasi</option>
                            <option value="internasional">internasional</option>
                            <option value="nasional tidak terakreditasi">nasional tidak terakreditasi</option>
                            <option value="-">-</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-extrabold uppercase text-slate-600 mb-1">Judul</label>
                    <textarea name="judul_artikel" rows="2" x-model="editData.judul_artikel" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold"></textarea>
                </div>

                <div>
                    <label class="block text-[11px] font-extrabold uppercase text-slate-600 mb-1">Nama Jurnal</label>
                    <input type="text" name="nama_jurnal" x-model="editData.nama_jurnal" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase text-slate-600 mb-1">ISSN</label>
                        <input type="text" name="issn" x-model="editData.issn" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold">
                    </div>
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase text-slate-600 mb-1">Vol. No. hal.</label>
                        <input type="text" name="volume_nomor" x-model="editData.volume_nomor" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-extrabold uppercase text-slate-600 mb-1">Penulis</label>
                    <textarea name="penulis" rows="2" x-model="editData.penulis" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="editModalOpen = false" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Verifikasi Dokumen Publikasi (Template & Similarity) -->
    <div x-show="verifyModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="verifyModalOpen = false" class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-md overflow-hidden">
            <div class="px-5 py-4 bg-slate-900 text-white flex items-center justify-between">
                <div>
                    <h4 class="font-extrabold text-sm">Verifikasi Dokumen Publikasi</h4>
                    <p class="text-[11px] text-slate-300">Divisi Publikasi &bull; Cek Template &amp; Similarity</p>
                </div>
                <button type="button" @click="verifyModalOpen = false" class="text-slate-400 hover:text-white text-xl leading-none">&times;</button>
            </div>
            <form :action="verifyData.action" method="POST" class="p-5 space-y-4 text-left">
                @csrf
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-800" x-text="verifyData.judul_artikel"></div>
                <div>
                    <label class="block text-[11px] font-extrabold uppercase text-slate-600 mb-1">1. Cek Kesesuaian Template Jurnal</label>
                    <select name="template_status" x-model="verifyData.template_status" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold">
                        <option value="sesuai">Sesuai Standar Template</option>
                        <option value="revisi">Perlu Revisi Format</option>
                        <option value="tidak_sesuai">Tidak Sesuai Template</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-extrabold uppercase text-slate-600 mb-1">2. Skor Similarity / Turnitin (%)</label>
                    <input type="number" step="0.1" min="0" max="100" name="similarity_score" x-model="verifyData.similarity_score" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold" required>
                    <span class="text-[10px] text-slate-500 font-semibold">Ambang batas wajar &le; 20%</span>
                </div>
                <div>
                    <label class="block text-[11px] font-extrabold uppercase text-slate-600 mb-1">3. Keputusan Verifikasi</label>
                    <select name="status_verifikasi" x-model="verifyData.status_verifikasi" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold">
                        <option value="Terverifikasi">Terverifikasi (Lolos)</option>
                        <option value="Revisi">Minta Revisi Dokumen</option>
                        <option value="Ditolak">Ditolak</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-extrabold uppercase text-slate-600 mb-1">Catatan Verifikator</label>
                    <textarea name="catatan_verifikasi" rows="2" x-model="verifyData.catatan_verifikasi" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-medium" placeholder="Catatan hasil pengecekan template & similarity..."></textarea>
                </div>
                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="verifyModalOpen = false" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs">Simpan Verifikasi</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection

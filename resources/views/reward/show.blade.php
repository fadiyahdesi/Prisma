@extends('layouts.app')

@section('title', 'Klaim ' . $klaim->nomor_klaim . ' - PRISMA UHN')

@section('content')
<div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <x-sidebar />

    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        <header class="bg-white border-b border-slate-200 py-4 sticky top-0 z-30 shadow-xs">
            <div class="w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <a href="{{ route('reward.index') }}" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center shrink-0 transition" title="Kembali" aria-label="Kembali">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </a>
                    <div>
                        <h1 class="font-extrabold text-xl text-slate-900 leading-tight">Detail Klaim Reward Insentif</h1>
                        <p class="text-xs font-semibold text-slate-500">Nomor Registrasi: {{ $klaim->nomor_klaim }} &bull; Distribusi Multi-Penulis (US-11.4)</p>
                    </div>
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

            <!-- Claim Status Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-6">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-5">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-3 py-1 rounded-lg text-xs font-extrabold {{ $klaim->jenis_klaim === 'Publikasi' ? 'bg-blue-100 text-blue-900 border border-blue-300' : 'bg-indigo-100 text-indigo-900 border border-indigo-300' }}">
                            {{ $klaim->jenis_klaim }}
                        </span>
                        <span class="px-3 py-1 rounded-lg text-xs font-extrabold bg-slate-100 text-slate-900 border border-slate-300">
                            {{ $klaim->kategori_insentif }}
                        </span>
                    </div>

                    <div>
                        @if($klaim->status_klaim === 'Submitted')
                            <span class="px-3.5 py-1.5 rounded-full text-xs font-black bg-amber-100 text-amber-900 border border-amber-300 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Sedang Ditinjau Admin P3M
                            </span>
                        @elseif($klaim->status_klaim === 'Approved_P3M')
                            <span class="px-3.5 py-1.5 rounded-full text-xs font-black bg-blue-100 text-blue-900 border border-blue-300 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Disetujui P3M (Antrean Pencairan Keuangan)
                            </span>
                        @elseif($klaim->status_klaim === 'Disbursed')
                            <span class="px-3.5 py-1.5 rounded-full text-xs font-black bg-emerald-100 text-emerald-900 border border-emerald-300 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Seluruh Insentif Telah Dicairkan
                            </span>
                        @elseif($klaim->status_klaim === 'Rejected')
                            <span class="px-3.5 py-1.5 rounded-full text-xs font-black bg-rose-100 text-rose-900 border border-rose-300">
                                Klaim Ditolak P3M
                            </span>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="p-5 rounded-2xl bg-emerald-50/70 border border-emerald-200">
                        <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider block">Total Nominal Reward Insentif</span>
                        <div class="text-2xl font-black text-emerald-800 mt-1">
                            <span class="text-sm font-bold mr-1">Rp</span>{{ number_format($klaim->total_reward, 0, ',', '.') }}
                        </div>
                        <span class="text-[10px] font-semibold text-emerald-600 mt-0.5 block">Sesuai Matriks SK Rektor UHN</span>
                    </div>

                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Jumlah Penulis Penerima</span>
                        <div class="text-2xl font-black text-slate-800 mt-1">
                            {{ $klaim->distribusi->count() }} <span class="text-sm font-bold text-slate-500">Orang</span>
                        </div>
                        <span class="text-[10px] font-semibold text-slate-500 mt-0.5 block">Total Persentase: 100.00%</span>
                    </div>

                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Tanggal Pengajuan</span>
                        <div class="text-base font-extrabold text-slate-800 mt-1">
                            {{ $klaim->created_at->format('d F Y, H:i') }}
                        </div>
                        <span class="text-[10px] font-semibold text-slate-500 mt-0.5 block">Pemohon: {{ $klaim->user->name }}</span>
                    </div>
                </div>

                <!-- Asset Detail Accordion -->
                <div class="p-5 rounded-2xl bg-slate-50/70 border border-slate-200 space-y-2">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Objek Aset yang Diklaim</span>
                    @if($klaim->publikasi)
                        <h3 class="font-extrabold text-sm text-slate-900">
                            {{ $klaim->publikasi->judul_artikel }}
                        </h3>
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500 font-semibold">
                            <span>Jurnal: <strong class="text-slate-800">{{ $klaim->publikasi->nama_jurnal }}</strong></span>
                            <span>&bull;</span>
                            <span>DOI: <a href="https://doi.org/{{ $klaim->publikasi->doi }}" target="_blank" class="text-blue-600 font-bold hover:underline">{{ $klaim->publikasi->doi }}</a></span>
                            <span>&bull;</span>
                            <span>Tahun: {{ $klaim->publikasi->tahun_terbit }}</span>
                        </div>
                    @elseif($klaim->hki)
                        <h3 class="font-extrabold text-sm text-slate-900">
                            {{ $klaim->hki->judul_hki }}
                        </h3>
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500 font-semibold">
                            <span>Jenis: <strong class="text-slate-800">{{ $klaim->hki->jenis_hki }}</strong></span>
                            <span>&bull;</span>
                            <span>No. Permohonan: <strong class="text-slate-800">{{ $klaim->hki->nomor_permohonan }}</strong></span>
                            @if($klaim->hki->nomor_sertifikat)
                                <span>&bull;</span>
                                <span>No. Sertifikat: <strong class="text-emerald-700">{{ $klaim->hki->nomor_sertifikat }}</strong></span>
                            @endif
                        </div>
                    @endif
                </div>

                @if($klaim->catatan_p3m)
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Catatan Review P3M:</span>
                        <p class="text-slate-700 font-semibold">{{ $klaim->catatan_p3m }}</p>
                        @if($klaim->approver)
                            <p class="text-[11px] text-slate-500 font-bold">Oleh: {{ $klaim->approver->name }} ({{ $klaim->approved_at ? $klaim->approved_at->format('d/m/Y H:i') : '' }})</p>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Multi-Author Distribution Table -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="font-bold text-sm text-slate-800">Rincian Pembagian Insentif Multi-Penulis (US-11.4)</h2>
                        <p class="text-xs text-slate-500">Daftar anggota penerima, persentase bagian, nomor rekening, dan status pencairan perorangan</p>
                    </div>

                    @if($klaim->file_surat_pernyataan)
                        <a href="{{ asset('storage/' . $klaim->file_surat_pernyataan) }}" target="_blank"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                            <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            <span>Surat Pernyataan Kesepakatan</span>
                        </a>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50/80 border-b border-slate-200/70 text-slate-600 font-extrabold uppercase text-[10px] tracking-wider">
                            <tr>
                                <th class="py-3 px-4">No</th>
                                <th class="py-3 px-4">Nama Penulis &amp; NIDN</th>
                                <th class="py-3 px-4">Peran</th>
                                <th class="py-3 px-4 text-center">Porsi (%)</th>
                                <th class="py-3 px-4 text-right">Nominal Bagian</th>
                                <th class="py-3 px-4">Rekening Transfer</th>
                                <th class="py-3 px-4 text-center">Status Cair</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($klaim->distribusi as $index => $dist)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="py-3.5 px-4 font-bold text-slate-500">{{ $index + 1 }}</td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-extrabold text-slate-900">{{ $dist->nama_penulis }}</div>
                                        <div class="text-[11px] text-slate-400 font-semibold">{{ $dist->nidn_nim ?: '-' }} {{ $dist->email ? "• {$dist->email}" : '' }}</div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-700">
                                            {{ $dist->peran_penulis }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-extrabold text-blue-700 text-xs">
                                        {{ number_format($dist->persentase, 2, ',', '.') }}%
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-black text-slate-900 text-xs">
                                        Rp {{ number_format($dist->nominal_bagian, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-slate-800">{{ $dist->nama_bank }} - {{ $dist->nomor_rekening }}</div>
                                        <div class="text-[11px] text-slate-500">a.n. {{ $dist->nama_pemilik_rekening }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if($dist->status_transfer === 'Disbursed')
                                            <div class="inline-flex flex-col items-center">
                                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                    Ditransfer
                                                </span>
                                                <span class="text-[9px] text-slate-400 mt-0.5">
                                                    {{ $dist->tanggal_transfer ? $dist->tanggal_transfer->format('d/m/Y') : '' }}
                                                </span>
                                                @if($dist->file_bukti_transfer)
                                                    <a href="{{ asset('storage/' . $dist->file_bukti_transfer) }}" target="_blank" class="text-[10px] text-blue-600 font-bold hover:underline mt-0.5">
                                                        Bukti Transfer
                                                    </a>
                                                @endif
                                            </div>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-800 border border-amber-300">
                                                Menunggu Cair
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50 border-t-2 border-slate-200 text-xs font-black text-slate-900">
                            <tr>
                                <td colspan="3" class="py-3 px-4 text-right">TOTAL DISTRIBUSI:</td>
                                <td class="py-3 px-4 text-center text-blue-700">100.00%</td>
                                <td class="py-3 px-4 text-right text-emerald-700">Rp {{ number_format($klaim->total_reward, 0, ',', '.') }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection


@extends('layouts.app')

@section('title', 'Detail Kontrak & Pencairan Termin I - PRISMA UHN')

@section('content')
<div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <x-sidebar />

    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        <header class="bg-white border-b border-slate-200 py-4 sticky top-0 z-30 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <a href="{{ route('keuangan.pencairan.index') }}" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </a>
                    <div>
                        <h1 class="font-extrabold text-xl text-slate-900 leading-tight">Proses Pencairan Termin I (70%)</h1>
                        <p class="text-xs font-semibold text-slate-500">SPK: {{ $kontrak->nomor_kontrak }} &bull; SK: {{ $kontrak->nomor_sk }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300">
                        Divisi Keuangan
                    </span>
                </div>
            </div>
        </header>

        <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
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

            @php
                $u = $kontrak->usulan;
                $t1Disbursed = $kontrak->isTermin1Disbursed();
                $pencairan1 = $kontrak->pencairan->firstWhere('termin', 1);
                $pencairan2 = $kontrak->pencairan->firstWhere('termin', 2);
                $t2Disbursed = $pencairan2 !== null;
                $hasLaporanAkhir = $u->laporanAkhir !== null;
                $isReadyToDisburse = $kontrak->isFullySigned() && $kontrak->hasVerifiedRekening() && !$t1Disbursed;
                $isReadyForTermin2 = $t1Disbursed && $hasLaporanAkhir && !$t2Disbursed;
            @endphp

            <!-- Proposal & Contract Overview -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-6">
                <div class="flex flex-col md:flex-row md:items-start justify-between gap-4 border-b border-slate-100 pb-5">
                    <div class="space-y-1">
                        <div class="flex flex-wrap items-center gap-2 mb-1.5">
                            <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200 tracking-wide">
                                {{ $u->kode_usulan }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-700">
                                {{ $u->skema->nama_skema ?? '-' }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-700">
                                Tahun {{ $u->periode->tahun_anggaran ?? date('Y') }}
                            </span>
                        </div>
                        <h2 class="text-xl font-black text-slate-900 leading-snug">
                            {{ $u->judul_usulan }}
                        </h2>
                        <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500 pt-1">
                            <span>Ketua Peneliti: <strong class="text-slate-800">{{ $u->pengusul->name ?? '-' }}</strong> (NIDN: {{ $u->pengusul->nidn_nim ?? '-' }})</span>
                            <span>Fakultas: <strong class="text-blue-700">{{ $u->pengusul->fakultas->nama_fakultas ?? '-' }}</strong></span>
                            <span>Prodi: <strong class="text-slate-700">{{ $u->pengusul->prodi->nama_prodi ?? '-' }}</strong></span>
                        </div>
                    </div>

                    <div class="shrink-0 flex items-center gap-2">
                        <a href="{{ route('kontrak.download-pdf', $kontrak) }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs flex items-center gap-1.5 border border-slate-300 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Unduh SPK (PDF)</span>
                        </a>
                        <a href="{{ route('spk.verify', $kontrak->verification_token) }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-extrabold text-xs flex items-center gap-1.5 border border-indigo-200 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                            <span>Verifikasi QR</span>
                        </a>
                    </div>
                </div>

                <!-- Financial Breakdown -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-400 font-bold uppercase tracking-wider block text-[10px]">Pagu Dana Disetujui</span>
                        <span class="text-xl font-black text-slate-900 mt-1 block">Rp {{ number_format($kontrak->pagu_disetujui, 0, ',', '.') }}</span>
                        <p class="text-[11px] text-slate-400 mt-0.5">Total komitmen dana hibah</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200">
                        <span class="text-emerald-800 font-bold uppercase tracking-wider block text-[10px]">Termin I (70% Dana Awal)</span>
                        <span class="text-xl font-black text-emerald-700 mt-1 block">Rp {{ number_format($kontrak->dana_termin_1, 0, ',', '.') }}</span>
                        <p class="text-[11px] text-emerald-600 mt-0.5">Pencairan saat ini</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-500 font-bold uppercase tracking-wider block text-[10px]">Termin II (30% Sisa Dana)</span>
                        <span class="text-xl font-black text-slate-700 mt-1 block">Rp {{ number_format($kontrak->dana_termin_2, 0, ',', '.') }}</span>
                        <p class="text-[11px] text-slate-400 mt-0.5">Pencairan pasca-Monev kemajuan</p>
                    </div>
                </div>
            </div>

            <!-- Prerequisites Checklist & Bank Account Validation -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Check 1: Tanda Tangan SPK -->
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="font-extrabold text-slate-900 text-sm flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full {{ $kontrak->signed_by_pengusul ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }} font-bold text-xs flex items-center justify-center">1</span>
                            Tanda Tangan Digital SPK
                        </h3>
                        @if($kontrak->signed_by_pengusul)
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800">
                                Sudah Ditandatangani
                            </span>
                        @else
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-100 text-amber-800">
                                Menunggu Pengusul
                            </span>
                        @endif
                    </div>

                    <p class="text-xs text-slate-500 leading-relaxed">
                        Sesuai SOP Keuangan UHN, pencairan dana hanya dapat dilakukan setelah kedua belah pihak (Kepala LPPM & Peneliti) membubuhkan tanda tangan digital pada Surat Perjanjian Kontrak (SPK).
                    </p>

                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2 text-xs">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Pihak Pertama (Kepala LPPM):</span>
                            <strong class="text-emerald-700 flex items-center gap-1">✓ Ditandatangani</strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Pihak Kedua (Ketua Peneliti):</span>
                            @if($kontrak->signed_by_pengusul)
                                <strong class="text-emerald-700 flex items-center gap-1">
                                    ✓ Ditandatangani ({{ $kontrak->signed_by_pengusul_at ? $kontrak->signed_by_pengusul_at->isoFormat('D MMM Y, HH:mm') : '-' }} WIB)
                                </strong>
                            @else
                                <strong class="text-rose-600">Belum Ditandatangani</strong>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Check 2: Verifikasi Rekening Bank Pengusul -->
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="font-extrabold text-slate-900 text-sm flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full {{ $kontrak->rekening_verified_at ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }} font-bold text-xs flex items-center justify-center">2</span>
                            Validasi Rekening Bank Pengusul
                        </h3>
                        @if($kontrak->rekening_verified_at)
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800">
                                Rekening Tervalidasi
                            </span>
                        @elseif($kontrak->nomor_rekening)
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-100 text-amber-800">
                                Menunggu Validasi Keuangan
                            </span>
                        @else
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-rose-100 text-rose-800">
                                Rekening Belum Diisi
                            </span>
                        @endif
                    </div>

                    @if($kontrak->nomor_rekening)
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2 text-xs">
                            <div class="flex justify-between">
                                <span class="text-slate-400">Nama Bank:</span>
                                <strong class="text-slate-800">{{ $kontrak->nama_bank }}</strong>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Nomor Rekening:</span>
                                <strong class="font-bold text-slate-900 text-sm tracking-wide">{{ $kontrak->nomor_rekening }}</strong>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Nama Pemilik:</span>
                                <strong class="text-slate-800">{{ $kontrak->nama_pemilik_rekening }}</strong>
                            </div>
                            @if($kontrak->file_buku_tabungan)
                                <div class="pt-2 border-t border-slate-200 flex justify-between items-center">
                                    <span class="text-slate-400">Salinan Buku Tabungan:</span>
                                    <a href="{{ asset('storage/' . $kontrak->file_buku_tabungan) }}" target="_blank" class="px-3 py-1 rounded-lg bg-blue-50 text-blue-700 font-bold hover:bg-blue-100 text-[11px] transition">
                                        Buka Berkas Scan Tabungan &rarr;
                                    </a>
                                </div>
                            @endif
                        </div>

                        @if(!$kontrak->rekening_verified_at)
                            <form action="{{ route('keuangan.pencairan.verify-rekening', $kontrak) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-md transition flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Verifikasi & Setujui Rekening Bank</span>
                                </button>
                            </form>
                        @endif
                    @else
                        <p class="text-xs text-slate-400 italic">
                            Peneliti belum mengisi rincian rekening bank atau mengunggah buku tabungan aktif.
                        </p>
                    @endif
                </div>
            </div>

            <!-- Form Pencairan Termin I (70%) -->
            @if(!$t1Disbursed)
                <div class="bg-white rounded-3xl border {{ $isReadyToDisburse ? 'border-emerald-400 ring-2 ring-emerald-200' : 'border-slate-200 opacity-80' }} shadow-sm p-6 space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div>
                            <h3 class="font-black text-slate-900 text-base">Eksekusi Penyaluran Dana Termin I (70%)</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Input nomor referensi SP2D / transfer bank dan unggah bukti transfer resmi.</p>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Nominal Ditransfer</span>
                            <span class="text-xl font-black text-emerald-700">Rp {{ number_format($kontrak->dana_termin_1, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    @if(!$isReadyToDisburse)
                        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs font-semibold flex items-center gap-3">
                            <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span>Formulir pencairan akan aktif otomatis setelah SPK ditandatangani oleh Pengusul dan Rekening Bank telah diverifikasi Keuangan.</span>
                        </div>
                    @endif

                    <form action="{{ route('keuangan.pencairan.disburse-termin-1', $kontrak) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">
                                    Nomor Referensi Bank / SP2D <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="nomor_referensi" value="{{ old('nomor_referensi') }}" {{ $isReadyToDisburse ? 'required' : 'disabled' }} placeholder="Contoh: TRF-UHN-2026-0881 / SP2D-0012" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">
                                    Tanggal Transfer Bank <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="tanggal_transfer" value="{{ old('tanggal_transfer', date('Y-m-d')) }}" {{ $isReadyToDisburse ? 'required' : 'disabled' }} class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            </div>
                        </div>

                        <div class="text-xs">
                            <label class="block font-bold text-slate-700 mb-1">
                                Unggah Salinan Bukti Transfer Resmi (PDF/JPG/PNG max 3MB)
                            </label>
                            <input type="file" name="file_bukti_transfer" accept=".pdf,.jpg,.jpeg,.png" {{ $isReadyToDisburse ? '' : 'disabled' }} class="w-full px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-300 text-xs file:mr-2 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700">
                        </div>

                        <div class="text-xs">
                            <label class="block font-bold text-slate-700 mb-1">Catatan Tambahan Keuangan</label>
                            <textarea name="catatan" rows="3" {{ $isReadyToDisburse ? '' : 'disabled' }} placeholder="Catatan opsional mengenai nomor rekening penerima atau kliring..." class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">{{ old('catatan') }}</textarea>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                            <a href="{{ route('keuangan.pencairan.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                                Kembali ke Daftar
                            </a>
                            <button type="submit" {{ $isReadyToDisburse ? '' : 'disabled' }} onclick="return confirm('Apakah Anda yakin ingin mengonfirmasi pencairan dana Termin I sebesar 70%? Tindakan ini akan resmi mengalihkan status usulan menjadi Pelaksanaan (Ongoing).');" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white font-extrabold text-xs shadow-md transition flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span>Konfirmasi Pencairan Termin I & Mulai Pelaksanaan</span>
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <!-- Sudah Dicairkan Summary Card -->
                <div class="bg-white rounded-3xl border border-emerald-200 shadow-sm p-6 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-md">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-900 text-base">Dana Termin I (70%) Telah Selesai Dicairkan</h3>
                            <p class="text-xs text-slate-500">Usulan ini telah resmi berstatus <strong>Ongoing (Dalam Pelaksanaan Riset/Abmas)</strong>.</p>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-200 divide-y divide-emerald-200/60 text-xs">
                        <div class="py-2 flex justify-between">
                            <span class="text-slate-500">Nominal Termin I (70%):</span>
                            <strong class="font-bold text-emerald-800 text-sm">Rp {{ number_format($pencairan1->jumlah_dana ?? $kontrak->dana_termin_1, 0, ',', '.') }}</strong>
                        </div>
                        <div class="py-2 flex justify-between">
                            <span class="text-slate-500">Nomor Referensi Bank / SP2D:</span>
                            <strong class="font-bold text-slate-900">{{ $pencairan1->nomor_referensi ?? '-' }}</strong>
                        </div>
                        <div class="py-2 flex justify-between">
                            <span class="text-slate-500">Tanggal Transfer:</span>
                            <strong>{{ $pencairan1->tanggal_transfer ? \Carbon\Carbon::parse($pencairan1->tanggal_transfer)->isoFormat('D MMMM Y') : '-' }}</strong>
                        </div>
                        <div class="py-2 flex justify-between">
                            <span class="text-slate-500">Diproses oleh Petugas Keuangan:</span>
                            <strong>{{ $pencairan1->processor->name ?? 'Divisi Keuangan LPPM' }}</strong>
                        </div>
                        @if($pencairan1 && $pencairan1->file_bukti_transfer)
                            <div class="py-2 flex justify-between items-center">
                                <span class="text-slate-500">Berkas Bukti Transfer:</span>
                                <a href="{{ asset('storage/' . $pencairan1->file_bukti_transfer) }}" target="_blank" class="text-blue-700 font-bold underline">
                                    Unduh Bukti Transfer Bank &rarr;
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Bagian Pencairan Termin II (30%) & Pelunasan Hibah -->
                <div class="bg-white rounded-3xl border {{ $t2Disbursed ? 'border-emerald-300' : ($isReadyForTermin2 ? 'border-indigo-400 ring-2 ring-indigo-200' : 'border-slate-200') }} shadow-sm p-6 space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="w-6 h-6 rounded-full {{ $t2Disbursed ? 'bg-emerald-100 text-emerald-700' : ($isReadyForTermin2 ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-500') }} font-black text-xs flex items-center justify-center">3</span>
                                <h3 class="font-black text-slate-900 text-base">Pencairan Dana Pelunasan Termin II (30%)</h3>
                            </div>
                            <p class="text-xs text-slate-500">Penyaluran sisa 30% anggaran pasca pengunggahan Laporan Akhir 100% & SPTB 100% (US-10.4).</p>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Nominal Termin II</span>
                            <span class="text-xl font-black text-indigo-700">Rp {{ number_format($kontrak->dana_termin_2, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    @if($t2Disbursed)
                        <!-- Summary Pelunasan Selesai -->
                        <div class="p-5 rounded-2xl bg-emerald-50 border border-emerald-300 space-y-4">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-md">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <div>
                                        <h4 class="font-extrabold text-emerald-950 text-sm">Hibah Telah Lunas 100% & Usulan Berstatus COMPLETED</h4>
                                        <p class="text-xs text-emerald-800">Seluruh alokasi dana hibah telah berhasil ditransfer kepada ketua tim peneliti.</p>
                                    </div>
                                </div>
                                <a href="{{ route('keuangan.pencairan.pelunasan-pdf', $kontrak) }}" target="_blank" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-extrabold text-xs shadow-md flex items-center gap-2 transition shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span>Unduh Tanda Bukti Pelunasan (PDF)</span>
                                </a>
                            </div>

                            <div class="p-4 rounded-xl bg-white/90 border border-emerald-200 divide-y divide-emerald-100 text-xs">
                                <div class="py-2 flex justify-between">
                                    <span class="text-slate-500">Nomor Referensi Transfer Pelunasan:</span>
                                    <strong class="font-bold text-slate-900">{{ $pencairan2->nomor_referensi ?? '-' }}</strong>
                                </div>
                                <div class="py-2 flex justify-between">
                                    <span class="text-slate-500">Tanggal Transfer:</span>
                                    <strong>{{ $pencairan2->tanggal_transfer ? \Carbon\Carbon::parse($pencairan2->tanggal_transfer)->isoFormat('D MMMM Y') : '-' }}</strong>
                                </div>
                                <div class="py-2 flex justify-between">
                                    <span class="text-slate-500">Petugas Keuangan:</span>
                                    <strong>{{ $pencairan2->processor->name ?? 'Divisi Keuangan LPPM' }}</strong>
                                </div>
                                @if($pencairan2 && $pencairan2->file_bukti_transfer)
                                    <div class="py-2 flex justify-between items-center">
                                        <span class="text-slate-500">Bukti Transfer Pelunasan:</span>
                                        <a href="{{ asset('storage/' . $pencairan2->file_bukti_transfer) }}" target="_blank" class="text-blue-700 font-bold underline">
                                            Lihat Bukti Transfer &rarr;
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <!-- Syarat Prasyarat Pencairan Termin II -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3 text-xs">
                            <h4 class="font-bold text-slate-700 uppercase tracking-wider text-[11px]">Pemeriksaan Prasyarat Termin II (30%):</h4>
                            <div class="flex items-center justify-between p-3 rounded-xl bg-white border border-slate-200">
                                <span>1. Penyaluran Dana Termin I (70%) Selesai:</span>
                                <strong class="text-emerald-700 flex items-center gap-1">✓ Selesai</strong>
                            </div>
                            <div class="flex items-center justify-between p-3 rounded-xl bg-white border border-slate-200">
                                <span>2. Pengunggahan Laporan Akhir 100% & SPTB 100%:</span>
                                @if($hasLaporanAkhir)
                                    <div class="flex items-center gap-2">
                                        <strong class="text-emerald-700 flex items-center gap-1">✓ Terunggah & Diverifikasi P3M</strong>
                                        <a href="{{ asset('storage/' . $u->laporanAkhir->file_laporan_akhir) }}" target="_blank" class="text-blue-600 underline font-bold text-[11px]">Lihat Naskah</a>
                                    </div>
                                @else
                                    <strong class="text-amber-600">Belum Diunggah Peneliti</strong>
                                @endif
                            </div>
                        </div>

                        @if(!$isReadyForTermin2)
                            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs font-semibold flex items-center gap-3">
                                <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <span>Pencairan Termin II (30%) akan dibuka setelah peneliti mengunggah berkas Laporan Akhir 100% dan SPTB 100%.</span>
                            </div>
                        @else
                            <!-- Formulir Eksekusi Termin II -->
                            <form action="{{ route('keuangan.pencairan.disburse-termin-2', $kontrak) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                                    <div>
                                        <label class="block font-bold text-slate-700 mb-1">
                                            Nomor Referensi Bank / SP2D Pelunasan <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="nomor_referensi" value="{{ old('nomor_referensi') }}" required placeholder="Contoh: TRF-PELUNASAN-2026-0099" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                    </div>
                                    <div>
                                        <label class="block font-bold text-slate-700 mb-1">
                                            Tanggal Transfer Bank <span class="text-red-500">*</span>
                                        </label>
                                        <input type="date" name="tanggal_transfer" value="{{ old('tanggal_transfer', date('Y-m-d')) }}" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                    </div>
                                </div>

                                <div class="text-xs">
                                    <label class="block font-bold text-slate-700 mb-1">
                                        Unggah Salinan Bukti Transfer Pelunasan (PDF/JPG/PNG max 3MB)
                                    </label>
                                    <input type="file" name="file_bukti_transfer" accept=".pdf,.jpg,.jpeg,.png" class="w-full px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-300 text-xs file:mr-2 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700">
                                </div>

                                <div class="text-xs">
                                    <label class="block font-bold text-slate-700 mb-1">Catatan Tambahan Keuangan</label>
                                    <textarea name="catatan" rows="3" placeholder="Catatan opsional mengenai kelengkapan LPJ dan pelunasan 100%..." class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none">{{ old('catatan') }}</textarea>
                                </div>

                                <div class="pt-2 flex justify-end">
                                    <button type="submit" onclick="return confirm('Konfirmasi pencairan Termin II (30%) sebesar Rp {{ number_format($kontrak->dana_termin_2, 0, ',', '.') }}? Status usulan akan resmi berubah menjadi Completed (Selesai).');" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white font-extrabold text-xs shadow-md transition flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>Konfirmasi Pelunasan Termin II & Selesaikan Hibah (Completed)</span>
                                    </button>
                                </div>
                            </form>
                        @endif
                    @endif
                </div>
            @endif
        </main>
    </div>
</div>
@endsection


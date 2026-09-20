@extends('layouts.app')

@section('title', 'Kontrak Hibah & Pencairan Dana - PRISMA UHN')

@section('content')
<div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <x-sidebar />

    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        <header class="bg-white border-b border-slate-200 py-4 sticky top-0 z-30 shadow-sm">
            <div class="w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <h1 class="font-extrabold text-xl text-slate-900 leading-tight">Kontrak Hibah & Pencairan Dana</h1>
                        <p class="text-xs font-semibold text-slate-500">Penandatanganan SPK Digital Ber-QR Code & Manajemen Rekening Hibah</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300">
                        Dosen Pengusul Portal
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

            <div class="space-y-6">
                @forelse($contracts as $kontrak)
                    @php
                        $u = $kontrak->usulan;
                        $t1Disbursed = $kontrak->isTermin1Disbursed();
                        $pencairan1 = $kontrak->pencairan->firstWhere('termin', 1);
                    @endphp
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                        <!-- Header Kontrak & SK -->
                        <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-blue-50/30 flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="space-y-1.5">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded-md text-[11px] font-mono font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                        {{ $u->kode_usulan }}
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-slate-200 text-slate-800">
                                        SK: {{ $kontrak->nomor_sk }}
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-slate-200 text-slate-800">
                                        SPK: {{ $kontrak->nomor_kontrak }}
                                    </span>
                                    @if($t1Disbursed)
                                        <span class="px-3 py-0.5 rounded-full text-[11px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Pelaksanaan (Termin 1 Dicairkan)
                                        </span>
                                    @elseif($kontrak->signed_by_pengusul)
                                        <span class="px-3 py-0.5 rounded-full text-[11px] font-black bg-blue-100 text-blue-800 border border-blue-300">
                                            SPK Ditandatangani &bull; Menunggu Transfer
                                        </span>
                                    @else
                                        <span class="px-3 py-0.5 rounded-full text-[11px] font-black bg-amber-100 text-amber-900 border border-amber-300 animate-pulse">
                                            Menunggu Tanda Tangan SPK
                                        </span>
                                    @endif
                                </div>
                                <h2 class="text-lg font-black text-slate-900 leading-snug">
                                    {{ $u->judul_usulan }}
                                </h2>
                                <p class="text-xs text-slate-500">
                                    {{ $u->skema->nama_skema ?? '-' }} &bull; Tahun Anggaran {{ $u->periode->tahun_anggaran ?? date('Y') }}
                                </p>
                            </div>

                            <div class="shrink-0 flex items-center gap-2">
                                <a href="{{ route('kontrak.download-pdf', $kontrak) }}" target="_blank" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs flex items-center gap-2 transition border border-slate-300">
                                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span>Unduh SPK (PDF)</span>
                                </a>

                                <a href="{{ route('spk.verify', $kontrak->verification_token) }}" target="_blank" class="px-4 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-extrabold text-xs flex items-center gap-2 transition border border-indigo-200">
                                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                    <span>Validasi QR Publik</span>
                                </a>
                            </div>
                        </div>

                        <!-- Funding Summary Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-slate-100 bg-slate-50/50 p-6 border-b border-slate-100 text-xs">
                            <div class="pb-3 sm:pb-0 sm:pr-4">
                                <span class="text-slate-400 font-bold uppercase tracking-wider block text-[10px]">Total Dana Disetujui</span>
                                <span class="text-xl font-black text-slate-900 mt-1 block">Rp {{ number_format($kontrak->pagu_disetujui, 0, ',', '.') }}</span>
                                <p class="text-[11px] text-slate-400 mt-0.5">Alokasi penuh dana hibah 100%</p>
                            </div>
                            <div class="py-3 sm:py-0 sm:px-4">
                                <span class="text-emerald-700 font-bold uppercase tracking-wider block text-[10px]">Termin I (70% Dana Awal)</span>
                                <span class="text-xl font-black text-emerald-700 mt-1 block">Rp {{ number_format($kontrak->dana_termin_1, 0, ',', '.') }}</span>
                                <p class="text-[11px] text-emerald-600 mt-0.5">Syarat: Tanda tangan SPK & validasi rekening</p>
                            </div>
                            <div class="pt-3 sm:pt-0 sm:pl-4">
                                <span class="text-slate-500 font-bold uppercase tracking-wider block text-[10px]">Termin II (30% Sisa Dana)</span>
                                <span class="text-xl font-black text-slate-700 mt-1 block">Rp {{ number_format($kontrak->dana_termin_2, 0, ',', '.') }}</span>
                                <p class="text-[11px] text-slate-400 mt-0.5">Syarat: Laporan kemajuan, logbook, & monev</p>
                            </div>
                        </div>

                        <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Left Card: Digital Signature -->
                            <div class="p-5 rounded-2xl border border-slate-200 bg-slate-50/30 space-y-4">
                                <div class="flex items-center justify-between">
                                    <h3 class="font-extrabold text-slate-900 text-sm flex items-center gap-2">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        Tanda Tangan Digital SPK
                                    </h3>
                                    @if($kontrak->signed_by_pengusul)
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800">
                                            Lengkap (Sudah Ditandatangani)
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-800">
                                            Wajib Ditandatangani
                                        </span>
                                    @endif
                                </div>

                                <div class="space-y-2 text-xs">
                                    <div class="flex items-center justify-between p-3 rounded-xl bg-white border border-slate-200">
                                        <span>Pihak Pertama (Kepala LPPM UHN):</span>
                                        <span class="font-bold text-emerald-700 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                            Ditandatangani
                                        </span>
                                    </div>

                                    <div class="flex items-center justify-between p-3 rounded-xl bg-white border border-slate-200">
                                        <span>Pihak Kedua (Ketua Peneliti):</span>
                                        @if($kontrak->signed_by_pengusul)
                                            <span class="font-bold text-emerald-700 flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                {{ $kontrak->signed_by_pengusul_at ? $kontrak->signed_by_pengusul_at->isoFormat('D MMM Y, HH:mm') : 'Ditandatangani' }}
                                            </span>
                                        @else
                                            <span class="font-bold text-rose-600">Belum Ditandatangani</span>
                                        @endif
                                    </div>
                                </div>

                                @if(!$kontrak->signed_by_pengusul)
                                    <form action="{{ route('pengusul.kontrak.sign', $kontrak) }}" method="POST" onsubmit="return confirm('Dengan menandatangani Surat Perjanjian Kontrak (SPK) ini secara digital, Anda menyetujui seluruh ketentuan pelaksanaan hibah, pencapaian target luaran, serta pelaporan keuangan standar BIMA Kemdiktisaintek. Lanjutkan penandatanganan?');">
                                        @csrf
                                        <button type="submit" class="w-full py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-extrabold text-xs shadow-md transition flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.5-5.5A9 9 0 1112 3a9 9 0 014.5 1.2z"/></svg>
                                            <span>Setujui & Tanda Tangani SPK Digital</span>
                                        </button>
                                    </form>
                                @else
                                    <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-[11px] leading-relaxed">
                                        Surat Perjanjian Kontrak telah ditandatangani secara digital dengan enkripsi SHA-256. QR Code keaslian dokumen aktif.
                                    </div>
                                @endif
                            </div>

                            <!-- Right Card: Bank Account & Passbook -->
                            <div class="p-5 rounded-2xl border border-slate-200 bg-slate-50/30 space-y-4">
                                <div class="flex items-center justify-between">
                                    <h3 class="font-extrabold text-slate-900 text-sm flex items-center gap-2">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                        Rekening Bank & Buku Tabungan
                                    </h3>
                                    @if($kontrak->rekening_verified_at)
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800">
                                            Rekening Terverifikasi
                                        </span>
                                    @elseif($kontrak->nomor_rekening)
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-800">
                                            Menunggu Verifikasi Keuangan
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-rose-100 text-rose-800">
                                            Belum Diunggah
                                        </span>
                                    @endif
                                </div>

                                @if($kontrak->nomor_rekening)
                                    <div class="p-3.5 rounded-xl bg-white border border-slate-200 space-y-1.5 text-xs">
                                        <div class="flex justify-between">
                                            <span class="text-slate-400">Nama Bank:</span>
                                            <strong class="text-slate-800">{{ $kontrak->nama_bank }}</strong>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-slate-400">Nomor Rekening:</span>
                                            <strong class="font-mono text-slate-900">{{ $kontrak->nomor_rekening }}</strong>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-slate-400">Atas Nama:</span>
                                            <strong class="text-slate-800">{{ $kontrak->nama_pemilik_rekening }}</strong>
                                        </div>
                                        @if($kontrak->file_buku_tabungan)
                                            <div class="pt-2 border-t border-slate-100 flex justify-between items-center">
                                                <span class="text-slate-400">Salinan Buku Tabungan:</span>
                                                <a href="{{ asset('storage/' . $kontrak->file_buku_tabungan) }}" target="_blank" class="text-blue-600 hover:text-blue-800 font-bold underline text-[11px]">
                                                    Lihat Berkas Scan
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <!-- Form Update Rekening (If not yet disbursed) -->
                                @if(!$t1Disbursed)
                                    <form action="{{ route('pengusul.kontrak.update-rekening', $kontrak) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                                        @csrf
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                                            <div>
                                                <label class="block font-bold text-slate-700 mb-1">Nama Bank *</label>
                                                <input type="text" name="nama_bank" value="{{ old('nama_bank', $kontrak->nama_bank) }}" required placeholder="Contoh: Bank Mandiri / BNI" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                            </div>
                                            <div>
                                                <label class="block font-bold text-slate-700 mb-1">Nomor Rekening *</label>
                                                <input type="text" name="nomor_rekening" value="{{ old('nomor_rekening', $kontrak->nomor_rekening) }}" required placeholder="Contoh: 1234567890" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                            </div>
                                        </div>
                                        <div class="text-xs">
                                            <label class="block font-bold text-slate-700 mb-1">Nama Pemilik Rekening *</label>
                                            <input type="text" name="nama_pemilik_rekening" value="{{ old('nama_pemilik_rekening', $kontrak->nama_pemilik_rekening ?? Auth::user()->name) }}" required placeholder="Sesuai nama di buku tabungan" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                        </div>
                                        <div class="text-xs">
                                            <label class="block font-bold text-slate-700 mb-1">Unggah Scan Buku Tabungan (PDF/JPG max 2MB) {{ $kontrak->file_buku_tabungan ? '(Opsional jika ingin mengganti)' : '*' }}</label>
                                            <input type="file" name="file_buku_tabungan" accept=".pdf,.jpg,.jpeg,.png" {{ $kontrak->file_buku_tabungan ? '' : 'required' }} class="w-full px-3 py-1.5 rounded-xl bg-white border border-slate-300 text-xs file:mr-2 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700">
                                        </div>
                                        <button type="submit" class="w-full py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs transition">
                                            Simpan Rincian Rekening
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <!-- Status Pencairan Dana & Fitur Pelaksanaan Riset (EPIC 10) -->
                        @if($t1Disbursed && $pencairan1)
                            @php
                                $pencairan2 = $kontrak->pencairan->firstWhere('termin', 2);
                            @endphp
                            <div class="p-6 bg-emerald-50/70 border-t border-emerald-200 space-y-4">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-2xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-sm">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                        <div class="space-y-0.5">
                                            <h4 class="font-extrabold text-emerald-950 text-sm">
                                                Dana Termin I (70%) Sebesar Rp {{ number_format($pencairan1->jumlah_dana, 0, ',', '.') }} Telah Berhasil Ditransfer
                                            </h4>
                                            <p class="text-xs text-emerald-800">
                                                Nomor Referensi Bank / SP2D: <strong>{{ $pencairan1->nomor_referensi }}</strong> &bull; Tanggal Transfer: <strong>{{ \Carbon\Carbon::parse($pencairan1->tanggal_transfer)->isoFormat('D MMMM Y') }}</strong>
                                            </p>
                                        </div>
                                    </div>

                                    @if($pencairan2)
                                        <a href="{{ route('keuangan.pencairan.pelunasan-pdf', $kontrak) }}" target="_blank" class="px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-extrabold text-xs shadow-sm flex items-center gap-2 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            <span>Bukti Pelunasan 100% (PDF)</span>
                                        </a>
                                    @endif
                                </div>

                                <!-- Action Buttons: Logbook, Monev, Laporan Akhir -->
                                <div class="pt-3 border-t border-emerald-200/60 flex flex-wrap items-center gap-2">
                                    <a href="{{ route('pengusul.logbook.show', $u) }}" class="px-3.5 py-2 rounded-xl bg-white hover:bg-blue-50 text-blue-700 font-bold text-xs border border-blue-200 shadow-xs flex items-center gap-1.5 transition">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>Logbook Harian</span>
                                    </a>
                                    <a href="{{ route('pengusul.monev.show', $u) }}" class="px-3.5 py-2 rounded-xl bg-white hover:bg-purple-50 text-purple-700 font-bold text-xs border border-purple-200 shadow-xs flex items-center gap-1.5 transition">
                                        <svg class="w-3.5 h-3.5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span>Laporan Kemajuan (Monev)</span>
                                    </a>
                                    <a href="{{ route('pengusul.laporan-akhir.show', $u) }}" class="px-3.5 py-2 rounded-xl bg-white hover:bg-indigo-50 text-indigo-700 font-bold text-xs border border-indigo-200 shadow-xs flex items-center gap-1.5 transition">
                                        <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.5-5.5A9 9 0 1112 3a9 9 0 014.5 1.2z"/></svg>
                                        <span>Laporan Akhir & Pengesahan QR</span>
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center text-slate-400">
                        <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <p class="font-bold text-slate-600">Belum ada usulan yang berstatus Pemenang Kontrak.</p>
                        <p class="text-xs text-slate-400 mt-1">Usulan yang telah lulus passing grade dan ditetapkan oleh Kepala P3M akan otomatis terdaftar di sini.</p>
                    </div>
                @endforelse
            </div>
        </main>
    </div>
</div>
@endsection


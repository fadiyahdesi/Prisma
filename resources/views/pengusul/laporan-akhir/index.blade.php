@extends('layouts.app')

@section('title', 'Laporan Akhir 100% & Pengesahan - PRISMA UHN')

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
                        <h1 class="font-extrabold text-xl text-slate-900 leading-tight">Laporan Akhir 100% & Lembar Pengesahan</h1>
                        <p class="text-xs font-semibold text-slate-500">Unggah Naskah Akhir, SPTB 100% & Terbitkan Lembar Pengesahan Ber-QR Code Resmi LPPM (US-10.3)</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-black bg-blue-100 text-blue-800 border border-blue-300">
                        Dosen Pengusul
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

            @if(!$usulan)
                <!-- Proposal List Selector -->
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
                    <h2 class="text-base font-extrabold text-slate-900">Pilih Usulan Penelitian / Pengabdian</h2>
                    <p class="text-xs text-slate-500">Pilih usulan aktif Anda untuk mengunggah Laporan Akhir 100% dan menerbitkan Lembar Pengesahan:</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                        @forelse($usulanList as $item)
                            <div class="p-5 rounded-2xl border border-slate-200 hover:border-blue-500 hover:shadow-md transition bg-slate-50/50 flex flex-col justify-between gap-4">
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="px-2.5 py-0.5 rounded text-[11px] font-mono font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                            {{ $item->kode_usulan }}
                                        </span>
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                            {{ $item->status }}
                                        </span>
                                    </div>
                                    <h3 class="font-black text-slate-900 text-sm leading-snug">
                                        {{ $item->judul_usulan }}
                                    </h3>
                                    <p class="text-xs text-slate-500">
                                        {{ $item->skema->nama_skema ?? '-' }} &bull; Laporan Akhir:
                                        @if($item->laporanAkhir)
                                            <span class="font-bold text-emerald-600">Sudah Diunggah</span>
                                        @else
                                            <span class="text-slate-400">Belum Diunggah</span>
                                        @endif
                                    </p>
                                </div>
                                <a href="{{ route('pengusul.laporan-akhir.show', $item) }}" class="w-full py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs flex items-center justify-center gap-2 transition">
                                    <span>Kelola Laporan Akhir</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
                            </div>
                        @empty
                            <div class="col-span-2 py-8 text-center text-slate-400">
                                <p class="font-bold">Belum ada usulan dalam status pelaksanaan / penyelesaian.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @else
                <!-- Proposal Details Header -->
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-blue-50/30 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="space-y-1.5">
                            <div class="flex flex-wrap items-center gap-2">
                                <a href="{{ route('pengusul.laporan-akhir.index') }}" class="px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-slate-200 hover:bg-slate-300 text-slate-700 transition">
                                    &larr; Ganti Usulan
                                </a>
                                <span class="px-2.5 py-0.5 rounded-md text-[11px] font-mono font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                    {{ $usulan->kode_usulan }}
                                </span>
                                <span class="px-3 py-0.5 rounded-full text-[11px] font-black {{ $laporan ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-amber-100 text-amber-900 border border-amber-300' }}">
                                    {{ $laporan ? 'Laporan Akhir Lengkap' : 'Menunggu Unggah Laporan Akhir' }}
                                </span>
                            </div>
                            <h2 class="text-lg font-black text-slate-900 leading-snug">
                                {{ $usulan->judul_usulan }}
                            </h2>
                            <p class="text-xs text-slate-500">
                                {{ $usulan->skema->nama_skema ?? '-' }} &bull; Tahun Anggaran {{ $usulan->periode->tahun_anggaran ?? date('Y') }}
                            </p>
                        </div>

                        @if($laporan)
                            <div class="shrink-0 flex flex-wrap items-center gap-2">
                                <a href="{{ route('laporan-akhir.download-pengesahan', $usulan) }}" target="_blank" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-extrabold text-xs flex items-center gap-2 shadow-md transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span>Unduh Lembar Pengesahan (PDF)</span>
                                </a>
                                <a href="{{ route('laporan-akhir.verify', $laporan->verification_token) }}" target="_blank" class="px-4 py-2.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-extrabold text-xs flex items-center gap-2 border border-indigo-200 transition">
                                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                    <span>Validasi QR Publik</span>
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Seminar Hasil Info Banner -->
                    @php
                        $semhas = $usulan->seminarHasil;
                    @endphp
                    <div class="p-6 bg-slate-50/70 border-b border-slate-100">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                            <div>
                                <span class="text-slate-400 font-bold uppercase tracking-wider block text-[10px]">Status Sidang Semhas</span>
                                <strong class="text-slate-800 mt-1 block">
                                    {{ $semhas?->status_seminar === 'completed' ? 'Selesai Dilaksanakan' : ($semhas ? 'Dijadwalkan' : 'Belum Dijadwalkan') }}
                                </strong>
                            </div>
                            <div>
                                <span class="text-slate-400 font-bold uppercase tracking-wider block text-[10px]">Jadwal Sidang Semhas</span>
                                <strong class="text-slate-800 mt-1 block">
                                    {{ $semhas?->jadwal_seminar ? $semhas->jadwal_seminar->isoFormat('D MMMM Y, HH:mm') . ' WIB' : '-' }}
                                </strong>
                            </div>
                            <div>
                                <span class="text-slate-400 font-bold uppercase tracking-wider block text-[10px]">Skor Dewan Penguji</span>
                                <strong class="text-emerald-700 text-sm mt-0.5 block">
                                    {{ $semhas?->skor_seminar !== null ? number_format($semhas->skor_seminar, 1) . ' / 100' : '-' }}
                                </strong>
                            </div>
                        </div>
                    </div>

                    <!-- Upload Form & Document Status Grid -->
                    <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Left: Form Unggah Laporan Akhir -->
                        <div class="space-y-4">
                            <h3 class="font-extrabold text-slate-900 text-sm flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                Unggah Naskah Akhir & SPTB 100%
                            </h3>

                            <form action="{{ route('pengusul.laporan-akhir.store', $usulan) }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                                @csrf
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">
                                        Naskah Laporan Akhir 100% (PDF, Maks 15MB) *
                                    </label>
                                    <input type="file" name="file_laporan_akhir" accept=".pdf" required class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-300 text-xs file:mr-2 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700">
                                    <p class="text-[10px] text-slate-400 mt-1">Naskah komprehensif memuat luaran wajib (artikel jurnal, paten, prototype, dll).</p>
                                </div>

                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">
                                        SPTB 100% (Pertanggungjawaban Belanja Penuh) *
                                    </label>
                                    <input type="file" name="file_sptb_100" accept=".pdf" required class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-300 text-xs file:mr-2 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700">
                                    <p class="text-[10px] text-slate-400 mt-1">Surat Pernyataan Tanggung Jawab Belanja 100% bermeterai Rp 10.000,-.</p>
                                </div>

                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Ringkasan Hasil Penelitian & Capaian Luaran *</label>
                                    <textarea name="ringkasan_hasil" rows="5" required placeholder="Tuliskan intisari temuan penelitian, signifikansi keilmuan/masyarakat, dan status capaian luaran wajib..." class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('ringkasan_hasil', $laporan->ringkasan_hasil ?? '') }}</textarea>
                                </div>

                                <button type="submit" class="w-full py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-extrabold text-xs shadow-md transition flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                    <span>{{ $laporan ? 'Perbarui Berkas Laporan Akhir & SPTB 100%' : 'Kirim Laporan Akhir & SPTB 100%' }}</span>
                                </button>
                            </form>
                        </div>

                        <!-- Right: Uploaded Files & QR Code Lembar Pengesahan -->
                        <div class="space-y-4">
                            <h3 class="font-extrabold text-slate-900 text-sm flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Lembar Pengesahan & Dokumen Terunggah
                            </h3>

                            @if($laporan)
                                <div class="p-5 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-4 text-xs">
                                    <!-- Lembar Pengesahan Badge -->
                                    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-300 space-y-2">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                                            <h4 class="font-extrabold text-emerald-950 text-xs">Lembar Pengesahan Resmi LPPM Telah Diterbitkan</h4>
                                        </div>
                                        <p class="text-[11px] text-emerald-800 leading-relaxed">
                                            Dokumen Lembar Pengesahan telah diverifikasi secara kelembagaan dan dilengkapi QR Code enkripsi SHA-256 yang dapat divalidasi langsung oleh pihak eksternal/Kemdiktisaintek.
                                        </p>
                                        <div class="pt-1">
                                            <a href="{{ route('laporan-akhir.download-pengesahan', $usulan) }}" target="_blank" class="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs inline-flex items-center gap-1.5 shadow-sm transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                <span>Unduh Lembar Pengesahan (PDF)</span>
                                            </a>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between p-3.5 rounded-xl bg-white border border-slate-200">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-red-100 text-red-600 flex items-center justify-center shrink-0 font-bold text-xs">
                                                PDF
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-800">Laporan Akhir 100%</p>
                                                <span class="text-[10px] text-slate-400">Diunggah: {{ $laporan->updated_at->isoFormat('D MMM Y, HH:mm') }}</span>
                                            </div>
                                        </div>
                                        <a href="{{ asset('storage/' . $laporan->file_laporan_akhir) }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 font-bold text-xs transition">
                                            Buka Berkas
                                        </a>
                                    </div>

                                    <div class="flex items-center justify-between p-3.5 rounded-xl bg-white border border-slate-200">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 font-bold text-xs">
                                                PDF
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-800">SPTB 100% (Pertanggungjawaban Penuh)</p>
                                                <span class="text-[10px] text-slate-400">Diunggah: {{ $laporan->updated_at->isoFormat('D MMM Y, HH:mm') }}</span>
                                            </div>
                                        </div>
                                        <a href="{{ asset('storage/' . $laporan->file_sptb_100) }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-bold text-xs transition">
                                            Buka Berkas
                                        </a>
                                    </div>

                                    <div class="p-3.5 rounded-xl bg-white border border-slate-200 space-y-1">
                                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Ringkasan Hasil Riset:</span>
                                        <p class="text-xs text-slate-700 leading-relaxed whitespace-pre-line">
                                            {{ $laporan->ringkasan_hasil }}
                                        </p>
                                    </div>
                                </div>
                            @else
                                <div class="p-8 rounded-2xl border-2 border-dashed border-slate-200 text-center text-slate-400">
                                    <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                    <p class="font-bold text-slate-600 text-xs">Belum ada naskah laporan akhir yang diunggah.</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Unggah berkas melalui formulir di samping untuk menerbitkan Lembar Pengesahan ber-QR Code.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </main>
    </div>
</div>
@endsection


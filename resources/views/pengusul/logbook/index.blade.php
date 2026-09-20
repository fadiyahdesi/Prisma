@extends('layouts.app')

@section('title', 'Logbook Harian Penelitian - PRISMA UHN')

@section('content')
<div x-data="{ sidebarOpen: false, modalOpen: false, previewImg: null }" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <x-sidebar />

    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        <header class="bg-white border-b border-slate-200 py-4 sticky top-0 z-30 shadow-sm">
            <div class="w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <h1 class="font-extrabold text-xl text-slate-900 leading-tight">Logbook Harian Penelitian</h1>
                        <p class="text-xs font-semibold text-slate-500">Pencatatan Aktivitas Lapangan, Progres Capaian, dan Ekspor Rekap Formal (US-10.1)</p>
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
                    <p class="text-xs text-slate-500">Berikut adalah usulan aktif Anda yang telah berkontrak dan berada dalam tahap pelaksanaan riset:</p>

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
                                        {{ $item->skema->nama_skema ?? '-' }} &bull; {{ $item->logbook->count() }} Catatan Logbook
                                    </p>
                                </div>
                                <a href="{{ route('pengusul.logbook.show', $item) }}" class="w-full py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs flex items-center justify-center gap-2 transition">
                                    <span>Buka Logbook Harian</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
                            </div>
                        @empty
                            <div class="col-span-2 py-8 text-center text-slate-400">
                                <p class="font-bold">Belum ada usulan dalam status pelaksanaan (Ongoing/Contracted).</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @else
                <!-- Detail Usulan & Logbook Header -->
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-blue-50/30 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="space-y-1.5">
                            <div class="flex flex-wrap items-center gap-2">
                                <a href="{{ route('pengusul.logbook.index') }}" class="px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-slate-200 hover:bg-slate-300 text-slate-700 transition">
                                    &larr; Ganti Usulan
                                </a>
                                <span class="px-2.5 py-0.5 rounded-md text-[11px] font-mono font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                    {{ $usulan->kode_usulan }}
                                </span>
                                <span class="px-3 py-0.5 rounded-full text-[11px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300">
                                    {{ $usulan->status }}
                                </span>
                            </div>
                            <h2 class="text-lg font-black text-slate-900 leading-snug">
                                {{ $usulan->judul_usulan }}
                            </h2>
                            <p class="text-xs text-slate-500">
                                {{ $usulan->skema->nama_skema ?? '-' }} &bull; Tahun Anggaran {{ $usulan->periode->tahun_anggaran ?? date('Y') }}
                            </p>
                        </div>

                    @php
                        $latestProgress = (float) ($logbooks->max('persentase_capaian') ?? 0);
                    @endphp
                        <div class="shrink-0 flex items-center gap-2">
                            @if($latestProgress >= 50.0)
                                <a href="{{ route('pengusul.logbook.download-pdf', $usulan) }}" target="_blank" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-700 hover:to-red-700 text-white font-extrabold text-xs flex items-center gap-2 shadow-sm transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span>Ekspor Rekap Logbook (PDF)</span>
                                </a>
                            @else
                                <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-100 border border-slate-200 text-slate-500 font-bold text-xs" title="Unduh PDF aktif setelah progres minimal 50%">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    <span>Unduh PDF (Minimal 50%)</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Progress Summary Bar -->
                    <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                        <div class="flex items-center justify-between text-xs mb-2">
                            <span class="font-extrabold text-slate-700 uppercase tracking-wider">Estimasi Progres Capaian Kegiatan Lapangan:</span>
                            <span class="text-base font-black text-blue-600">{{ number_format($latestProgress, 1) }}%</span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-3.5 overflow-hidden shadow-inner">
                            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-3.5 rounded-full transition-all duration-500 ease-out" style="width: {{ min(100, max(0, $latestProgress)) }}%"></div>
                        </div>
                        <div class="flex justify-between items-center text-[11px] text-slate-400 mt-1.5 font-semibold">
                            <span>0% (Awal Penelitian)</span>
                            <span>50% (Pengambilan Data)</span>
                            <span>70% (Monev Kemajuan)</span>
                            <span>100% (Laporan Akhir Selesai)</span>
                        </div>
                    </div>
                </div>

                <!-- Form Input & Timeline Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left: Form Tambah Logbook -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm sticky top-24 space-y-4">
                            <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                                <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                </div>
                                <div>
                                    <h3 class="font-extrabold text-slate-900 text-sm">Entri Logbook Baru</h3>
                                    <p class="text-[11px] text-slate-500">Catat aktivitas riset harian</p>
                                </div>
                            </div>

                            <form action="{{ route('pengusul.logbook.store', $usulan) }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                                @csrf
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Tanggal Kegiatan *</label>
                                    <input type="date" name="tanggal" max="{{ date('Y-m-d') }}" value="{{ old('tanggal', date('Y-m-d')) }}" required class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none">
                                </div>

                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Aktivitas / Kegiatan *</label>
                                    <textarea name="aktivitas" rows="4" required placeholder="Jelaskan secara detail pelaksanaan riset, uji laboratorium, wawancara responden, atau pengolahan data..." class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none">{{ old('aktivitas') }}</textarea>
                                </div>

                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Estimasi Capaian Progres (%) *</label>
                                    <input type="number" name="persentase_capaian" step="0.5" min="0" max="100" value="{{ old('persentase_capaian', $latestProgress) }}" required placeholder="Contoh: 35.0" class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none">
                                </div>

                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Foto Bukti Kegiatan (Maks 2MB)</label>
                                    <input type="file" name="file_bukti" accept=".jpg,.jpeg,.png,.pdf" class="w-full px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-300 text-xs file:mr-2 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700">
                                    <p class="text-[10px] text-slate-400 mt-1">Format: JPG, PNG, atau PDF lampiran dokumentasi.</p>
                                </div>

                                <button type="submit" class="w-full py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-md transition flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                                    <span>Simpan Catatan Logbook</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Right: Chronological Timeline List -->
                    <div class="lg:col-span-2 space-y-4">
                        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
                            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                                <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Kronologi Rekam Jejak Pelaksanaan ({{ $logbooks->count() }} Kegiatan)
                                </h3>
                                <span class="text-xs font-semibold text-slate-400">Urutan Kronologis Tanggal</span>
                            </div>

                            <div class="pt-6">
                                @if($logbooks->isEmpty())
                                    <div class="py-12 text-center text-slate-400">
                                        <svg class="w-12 h-12 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <p class="font-bold text-slate-600">Belum ada catatan logbook kegiatan.</p>
                                        <p class="text-xs text-slate-400 mt-1">Gunakan formulir di sebelah kiri untuk mencatat kegiatan penelitian pertama Anda.</p>
                                    </div>
                                @else
                                    <div class="relative pl-6 border-l-2 border-blue-200 space-y-8 my-2">
                                        @foreach($logbooks as $entry)
                                            <div class="relative group">
                                                <!-- Marker dot -->
                                                <div class="absolute -left-[31px] top-1.5 w-4 h-4 rounded-full border-2 border-blue-600 bg-white group-hover:bg-blue-600 transition"></div>

                                                <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/70 hover:bg-white hover:border-blue-300 hover:shadow-sm transition space-y-3">
                                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                                        <div class="flex items-center gap-2">
                                                            <span class="font-black text-slate-900 text-xs">
                                                                {{ \Carbon\Carbon::parse($entry->tanggal)->isoFormat('dddd, D MMMM Y') }}
                                                            </span>
                                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-blue-100 text-blue-800 border border-blue-200">
                                                                Capaian: {{ number_format($entry->persentase_capaian, 1) }}%
                                                            </span>
                                                        </div>

                                                        <form action="{{ route('pengusul.logbook.destroy', $entry) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus catatan logbook tanggal {{ $entry->tanggal->format('d/m/Y') }}?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="p-1 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus Catatan">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                            </button>
                                                        </form>
                                                    </div>

                                                    <p class="text-xs text-slate-700 leading-relaxed whitespace-pre-line">
                                                        {{ $entry->aktivitas }}
                                                    </p>

                                                    @if($entry->file_bukti)
                                                        @php
                                                            $ext = pathinfo($entry->file_bukti, PATHINFO_EXTENSION);
                                                            $isImg = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'webp']);
                                                        @endphp
                                                        <div class="pt-2 border-t border-slate-200/60 flex items-center justify-between text-xs">
                                                            <span class="text-slate-400 text-[11px] flex items-center gap-1">
                                                                <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                                                Lampiran Bukti Dokumentasi
                                                            </span>
                                                            <div class="flex items-center gap-2">
                                                                @if($isImg)
                                                                    <button @click="previewImg = '{{ asset('storage/' . $entry->file_bukti) }}'; modalOpen = true" class="text-blue-600 hover:text-blue-800 font-bold text-[11px] underline">
                                                                        Pratinjau Foto
                                                                    </button>
                                                                @endif
                                                                <a href="{{ asset('storage/' . $entry->file_bukti) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 text-[11px] font-bold transition">
                                                                    Unduh Berkas
                                                                </a>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </main>
    </div>

    <!-- Modal Pratinjau Foto Bukti -->
    <div x-show="modalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs">
        <div @click.away="modalOpen = false" class="bg-white rounded-3xl p-4 max-w-2xl w-full shadow-2xl space-y-4">
            <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                <h4 class="font-extrabold text-sm text-slate-800">Dokumentasi Bukti Lapangan</h4>
                <button @click="modalOpen = false" class="text-slate-400 hover:text-slate-700 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="max-h-[70vh] overflow-auto flex items-center justify-center bg-slate-900 rounded-2xl p-2">
                <img :src="previewImg" alt="Bukti Logbook" class="max-h-[65vh] object-contain rounded-xl">
            </div>
        </div>
    </div>
</div>
@endsection


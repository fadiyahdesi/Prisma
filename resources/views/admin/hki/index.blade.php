@extends('layouts.app')

@section('title', 'Verifikasi Berkas Sentra HKI - Admin P3M')

@section('content')
<div x-data="{ sidebarOpen: false, modalVerifyOpen: false, modalImportOpen: false, modalGuideOpen: false, selectedHki: null, verifyAction: 'approve', importTab: 'upload' }" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <x-sidebar />

    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        <header class="bg-white border-b border-slate-200 py-4 sticky top-0 z-30 shadow-xs">
            <div class="w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <h1 class="font-extrabold text-xl text-slate-900 leading-tight">Antrean Verifikasi Sentra HKI</h1>
                        <p class="text-xs font-semibold text-slate-500">Pemeriksaan Keabsahan Dokumen DJKI, Validasi Invensi &amp; Penetapan Status HKI (US-11.2)</p>
                    </div>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-black bg-indigo-100 text-indigo-800 border border-indigo-300">
                    Pengelola Sentra HKI
                </span>
            </div>
        </header>

        <main class="flex-grow w-full px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-sm font-bold flex items-center gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('warning'))
                <div class="p-4 rounded-2xl bg-amber-50 border border-amber-300 text-amber-900 text-sm font-bold flex items-center gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('warning') }}</span>
                </div>
            @endif

            <!-- HKI Integration & Automation Action Hub -->
            <div class="bg-gradient-to-r from-slate-900 to-slate-800 rounded-2xl p-5 text-white shadow-md flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                            Automation Hub
                        </span>
                        <h2 class="font-extrabold text-base text-white">Integrasi DJKI PDKI &amp; SINTA Kemdiktisaintek</h2>
                    </div>
                    <p class="text-xs text-slate-300">Sinkronisasi otomatis hasil crawling berkas PDKI (JSON/CSV), cetak berkas SINTA, atau sinkronisasi instan.</p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <!-- Tombol Import Universal (JSON PDKI / Excel CSV) -->
                    <button type="button" @click="modalImportOpen = true; importTab = 'upload'"
                            class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        <span>Impor PDKI / CSV</span>
                    </button>

                    <!-- Download Template CSV -->
                    <a href="{{ route('admin.hki.template-csv') }}"
                       class="px-3.5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs border border-white/20 transition flex items-center gap-1.5"
                       title="Unduh Template Rekap CSV HKI">
                        <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Template CSV</span>
                    </a>

                    <!-- Export Format SINTA Extension -->
                    <a href="{{ route('admin.hki.export-sinta') }}"
                       class="px-3.5 py-2.5 rounded-xl bg-indigo-600/80 hover:bg-indigo-600 text-white font-bold text-xs border border-indigo-400/30 transition flex items-center gap-1.5"
                       title="Ekspor ke format JSON yang langsung pas untuk Ekstensi Auto Input SINTA">
                        <svg class="w-4 h-4 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span>Ekspor JSON SINTA</span>
                    </a>

                    <!-- Fast Sync Real UHN Lecturers Demo Guarantee -->
                    <form method="POST" action="{{ route('admin.hki.quick-sync-real') }}" onsubmit="return confirm('Jalankan Sinkronisasi Kilat data HKI resmi dosen UHN (Sharfina Febbi Handayani, Ginanjar Wiro Sasmito, Slamet Wiyono, Ida Farida)?')">
                        @csrf
                        <button type="submit"
                                class="px-3.5 py-2.5 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 font-bold text-xs border border-amber-500/40 transition flex items-center gap-1.5"
                                title="Sinkronisasi instan data HKI dosen riil UHN (Jaminan Anti-Gagal)">
                            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            <span>Sinkronisasi Kilat</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Tabs Navigation -->
            <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 pb-3">
                <a href="{{ route('admin.hki.index', ['status' => 'Pending_verification']) }}"
                   class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $status === 'Pending_verification' ? 'bg-amber-500 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                    <span>Menunggu Verifikasi</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $status === 'Pending_verification' ? 'bg-amber-600 text-white' : 'bg-amber-100 text-amber-800' }}">
                        {{ $counts['pending'] }}
                    </span>
                </a>

                <a href="{{ route('admin.hki.index', ['status' => 'Terverifikasi HKI']) }}"
                   class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $status === 'Terverifikasi HKI' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                    <span>Terverifikasi HKI</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $status === 'Terverifikasi HKI' ? 'bg-emerald-700 text-white' : 'bg-emerald-100 text-emerald-800' }}">
                        {{ $counts['verified'] }}
                    </span>
                </a>

                <a href="{{ route('admin.hki.index', ['status' => 'Rejected']) }}"
                   class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $status === 'Rejected' ? 'bg-rose-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                    <span>Ditolak</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $status === 'Rejected' ? 'bg-rose-700 text-white' : 'bg-rose-100 text-rose-800' }}">
                        {{ $counts['rejected'] }}
                    </span>
                </a>

                <a href="{{ route('admin.hki.index', ['status' => 'all']) }}"
                   class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $status === 'all' ? 'bg-slate-900 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                    Semua Berkas
                </a>
            </div>

            <!-- List Table -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h2 class="font-bold text-sm text-slate-800">Daftar Berkas Masuk Sentra HKI</h2>
                        <p class="text-xs text-slate-500">Pilih berkas untuk memeriksa salinan sertifikat dan menetapkan status validasi</p>
                    </div>

                    <form method="GET" action="{{ route('admin.hki.index') }}" class="flex items-center gap-2">
                        <input type="hidden" name="status" value="{{ $status }}">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor / judul / pengusul..."
                               class="px-3.5 py-1.5 text-xs font-semibold rounded-xl border border-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500">
                        <button type="submit" class="px-3 py-1.5 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-slate-800">
                            Cari
                        </button>
                    </form>
                </div>

                @if($hkiList->isEmpty())
                    <div class="p-12 text-center">
                        <p class="text-xs font-bold text-slate-400">Tidak ada data pendaftaran HKI pada kategori ini.</p>
                    </div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach($hkiList as $item)
                            <div class="p-5 hover:bg-slate-50/70 transition flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                                <div class="space-y-1.5 flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="px-2.5 py-0.5 rounded-md text-[11px] font-extrabold bg-indigo-100 text-indigo-800 border border-indigo-300">
                                            {{ $item->jenis_hki }}
                                        </span>

                                        @if($item->status_hki === 'Terverifikasi HKI')
                                            <span class="px-2.5 py-0.5 rounded-md text-[11px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                Terverifikasi HKI
                                            </span>
                                        @elseif($item->status_hki === 'Rejected')
                                            <span class="px-2.5 py-0.5 rounded-md text-[11px] font-black bg-rose-100 text-rose-800 border border-rose-300">
                                                Ditolak
                                            </span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-md text-[11px] font-black bg-amber-100 text-amber-800 border border-amber-300">
                                                Menunggu Verifikasi
                                            </span>
                                        @endif
                                    </div>

                                    <h3 class="font-extrabold text-slate-900 text-sm leading-snug">
                                        {{ $item->judul_hki }}
                                    </h3>

                                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500 font-semibold">
                                        <span>No. Permohonan: <strong class="text-slate-800">{{ $item->nomor_permohonan }}</strong></span>
                                        @if($item->nomor_sertifikat)
                                            <span>&bull;</span>
                                            <span>Sertifikat: <strong class="text-emerald-700">{{ $item->nomor_sertifikat }}</strong></span>
                                        @endif
                                        <span>&bull;</span>
                                        <span>Pengusul: <strong class="text-slate-900">{{ $item->user->name }}</strong></span>
                                        <span>&bull;</span>
                                        <span>Tanggal: {{ $item->tanggal_permohonan ? $item->tanggal_permohonan->format('d/m/Y') : '-' }}</span>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-2 pt-1">
                                        @if($item->file_sertifikat)
                                            <a href="{{ route('hki.download-file', ['hki' => $item->id, 'type' => 'sertifikat']) }}" target="_blank"
                                               class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px] transition flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                                <span>Sertifikat</span>
                                            </a>
                                        @endif
                                        @if($item->file_manual_book)
                                            <a href="{{ route('hki.download-file', ['hki' => $item->id, 'type' => 'manual_book']) }}" target="_blank"
                                               class="px-2.5 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-[11px] border border-blue-200 transition flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                <span>Manual Book</span>
                                            </a>
                                        @endif
                                        @if($item->file_surat_pernyataan)
                                            <a href="{{ route('hki.download-file', ['hki' => $item->id, 'type' => 'surat_pernyataan']) }}" target="_blank"
                                               class="px-2.5 py-1 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-[11px] border border-indigo-200 transition flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                <span>Surat Pernyataan</span>
                                            </a>
                                        @endif
                                        @if($item->file_surat_pengalihan)
                                            <a href="{{ route('hki.download-file', ['hki' => $item->id, 'type' => 'surat_pengalihan']) }}" target="_blank"
                                               class="px-2.5 py-1 rounded-lg bg-purple-50 hover:bg-purple-100 text-purple-700 font-bold text-[11px] border border-purple-200 transition flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                                <span>Surat Pengalihan</span>
                                            </a>
                                        @endif
                                        <a href="{{ route('hki.show', $item) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px] transition">
                                            Detail Lengkap &rarr;
                                        </a>
                                    </div>

                                    @if($item->catatan_verifikasi)
                                        <p class="text-[11px] text-slate-500 italic bg-slate-50 p-2 rounded-lg border border-slate-100">
                                            Catatan: {{ $item->catatan_verifikasi }}
                                        </p>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2 shrink-0 pt-2 lg:pt-0">
                                    <button type="button"
                                            @click="selectedHki = {{ $item->toJson() }}; verifyAction = 'approve'; modalVerifyOpen = true"
                                            class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>Verifikasi Form</span>
                                    </button>

                                    <button type="button"
                                            @click="selectedHki = {{ $item->toJson() }}; verifyAction = 'reject'; modalVerifyOpen = true"
                                            class="px-3 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-xs transition">
                                        Tolak
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $hkiList->links() }}
                    </div>
                @endif
            </div>

            <!-- Modal Verification -->
            <div x-show="modalVerifyOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
                <div @click.away="modalVerifyOpen = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="font-extrabold text-slate-900 text-base" x-text="verifyAction === 'approve' ? 'Verifikasi &amp; Setujui HKI' : 'Tolak / Tangguhkan Berkas HKI'"></h3>
                        <button @click="modalVerifyOpen = false" class="text-slate-400 hover:text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form :action="selectedHki ? `{{ url('/admin/hki') }}/${selectedHki.id}/verify` : '#'" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="action" :value="verifyAction">

                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-1">
                            <div class="font-bold text-slate-900" x-text="selectedHki?.judul_hki"></div>
                            <div class="text-slate-500 font-semibold">
                                No. Permohonan: <span class="font-bold text-slate-700" x-text="selectedHki?.nomor_permohonan"></span>
                            </div>
                        </div>

                        <template x-if="verifyAction === 'approve'">
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">
                                        Nomor Sertifikat / Pencatatan Resmi
                                    </label>
                                    <input type="text" name="nomor_sertifikat" :value="selectedHki?.nomor_sertifikat || ''"
                                           placeholder="Contoh: 000543219 / IDP00005544"
                                           class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-emerald-500">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">
                                        Tanggal Terbit Sertifikat
                                    </label>
                                    <input type="date" name="tanggal_terbit" :value="selectedHki?.tanggal_terbit || '{{ date('Y-m-d') }}'"
                                           class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-emerald-500">
                                </div>
                            </div>
                        </template>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Catatan Verifikator <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="catatan_verifikasi" rows="3" required
                                      placeholder="Masukkan catatan verifikasi keabsahan berkas..."
                                      class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-blue-500"
                                      :value="verifyAction === 'approve' ? 'Dokumen sertifikat pencatatan ciptaan / paten telah diverifikasi sah dan sesuai ketentuan Sentra HKI UHN.' : 'Berkas pendaftaran HKI belum memenuhi kelengkapan bukti sah tanda terima DJKI.'"></textarea>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2">
                            <button type="button" @click="modalVerifyOpen = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                                Batal
                            </button>
                            <button type="submit"
                                    :class="verifyAction === 'approve' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-rose-600 hover:bg-rose-700'"
                                    class="px-5 py-2 rounded-xl text-white font-extrabold text-xs shadow-xs transition">
                                <span x-text="verifyAction === 'approve' ? 'Setujui &amp; Verifikasi' : 'Simpan Penolakan'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal Universal Importer (JSON PDKI & CSV) -->
            <div x-show="modalImportOpen" style="display: none;"
                 class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
                <div @click.away="modalImportOpen = false"
                     class="bg-white rounded-3xl max-w-2xl w-full shadow-2xl border border-slate-100 overflow-hidden transform transition-all">
                    
                    <!-- Header Modal -->
                    <div class="px-6 py-5 bg-gradient-to-r from-slate-900 to-slate-800 text-white flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            </div>
                            <div>
                                <h3 class="font-extrabold text-base leading-tight">Pusat Integrasi &amp; Impor Data HKI</h3>
                                <p class="text-xs text-slate-300">Impor Berkas JSON dari PDKI Auto Fetch atau Spreadsheet CSV Rekap Kampus</p>
                            </div>
                        </div>
                        <button @click="modalImportOpen = false" class="text-slate-400 hover:text-white p-1 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Nav Tabs Modal -->
                    <div class="px-6 pt-4 border-b border-slate-100 flex items-center gap-3">
                        <button type="button" @click="importTab = 'upload'"
                                :class="importTab === 'upload' ? 'border-b-2 border-emerald-600 text-emerald-700 font-extrabold' : 'text-slate-500 font-semibold hover:text-slate-700'"
                                class="pb-3 text-xs flex items-center gap-1.5 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            <span>Unggah Berkas (JSON / CSV)</span>
                        </button>
                        <button type="button" @click="importTab = 'guide'"
                                :class="importTab === 'guide' ? 'border-b-2 border-indigo-600 text-indigo-700 font-extrabold' : 'text-slate-500 font-semibold hover:text-slate-700'"
                                class="pb-3 text-xs flex items-center gap-1.5 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Panduan Ekstensi PDKI &amp; SINTA</span>
                        </button>
                    </div>

                    <!-- Body: Tab Upload -->
                    <div x-show="importTab === 'upload'" class="p-6 space-y-4">
                        <form method="POST" action="{{ route('admin.hki.import') }}" enctype="multipart/form-data" class="space-y-4">
                            @csrf

                            <div class="border-2 border-dashed border-slate-200 hover:border-emerald-500 rounded-2xl p-6 text-center transition bg-slate-50/50">
                                <svg class="w-10 h-10 mx-auto text-slate-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <label for="hki_file" class="cursor-pointer">
                                    <span class="block text-xs font-extrabold text-slate-800 hover:text-emerald-600">Pilih Berkas JSON / CSV</span>
                                    <span class="block text-[11px] text-slate-500 mt-1">Dukung file <span class="font-bold text-emerald-700">.json</span> (dari PDKI Auto Fetch) atau <span class="font-bold text-indigo-700">.csv</span> (rekap spreadsheet). Maks 20 MB.</span>
                                </label>
                                <input type="file" id="hki_file" name="hki_file" accept=".json,.csv,.txt" required class="mt-3 block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-extrabold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
                            </div>

                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600 space-y-1">
                                <p class="font-bold text-slate-800 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Fitur Otomatisasi Cerdas PRISMA:
                                </p>
                                <ul class="list-disc list-inside text-[11px] space-y-0.5 text-slate-500">
                                    <li>Mencocokkan nama dosen / NIDN pencipta secara otomatis ke database dosen UHN.</li>
                                    <li>Data yang diimpor langsung berstatus <span class="font-bold text-emerald-700">Terverifikasi HKI</span> resmi.</li>
                                    <li>Mencegah duplikasi data berdasarkan nomor permohonan resmi.</li>
                                </ul>
                            </div>

                            <div class="flex items-center justify-between pt-2">
                                <a href="{{ route('admin.hki.template-csv') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    <span>Unduh Template Format CSV</span>
                                </a>
                                <div class="flex items-center gap-2">
                                    <button type="button" @click="modalImportOpen = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">
                                        Batal
                                    </button>
                                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-xs transition">
                                        Mulai Impor Data
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Body: Tab Panduan -->
                    <div x-show="importTab === 'guide'" class="p-6 space-y-4 max-h-[60vh] overflow-y-auto">
                        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs space-y-1">
                            <p class="font-extrabold flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                Mengapa Ekstensi PDKI Sering Gagal / Macet?
                            </p>
                            <p class="text-[11px] leading-relaxed text-amber-800">
                                Situs PDKI Kemenkumham membutuhkan token otentikasi sesi aktif dan sering memblokir akses robot. Ekstensi browser tidak dapat berjalan jika Anda membuka tab selain halaman PDKI atau belum melakukan inisialisasi pencarian manual.
                            </p>
                        </div>

                        <ol class="space-y-3 text-xs text-slate-700">
                            <li class="flex items-start gap-2.5 p-3 rounded-xl bg-slate-50 border border-slate-200">
                                <span class="w-5 h-5 rounded-full bg-slate-900 text-white font-extrabold flex items-center justify-center text-[10px] shrink-0 mt-0.5">1</span>
                                <div>
                                    <p class="font-bold text-slate-900">Buka Tab Web Resmi PDKI</p>
                                    <p class="text-[11px] text-slate-500">Buka alamat <code class="px-1.5 py-0.5 bg-slate-200 rounded font-mono">https://pdki-indonesia.dgip.go.id/</code> di tab aktif browser.</p>
                                </div>
                            </li>

                            <li class="flex items-start gap-2.5 p-3 rounded-xl bg-slate-50 border border-slate-200">
                                <span class="w-5 h-5 rounded-full bg-slate-900 text-white font-extrabold flex items-center justify-center text-[10px] shrink-0 mt-0.5">2</span>
                                <div>
                                    <p class="font-bold text-slate-900">Pilih Kategori yang Sesuai</p>
                                    <p class="text-[11px] text-slate-500">Jika ingin mencari Hak Cipta, pastikan tab yang aktif di situs PDKI adalah tab <strong>Hak Cipta</strong>. Jika Paten, pilih <strong>Paten</strong>.</p>
                                </div>
                            </li>

                            <li class="flex items-start gap-2.5 p-3 rounded-xl bg-slate-50 border border-slate-200">
                                <span class="w-5 h-5 rounded-full bg-slate-900 text-white font-extrabold flex items-center justify-center text-[10px] shrink-0 mt-0.5">3</span>
                                <div>
                                    <p class="font-bold text-slate-900">Lakukan Inisialisasi Manual 1 Kali (Krusial!)</p>
                                    <p class="text-[11px] text-slate-500">Ketik satu nomor di kotak pencarian web PDKI, klik <strong>Cari</strong>, lalu <strong>klik salah satu hasil agar detail terbuka</strong>. Langkah ini bertujuan agar token otentikasi PDKI aktif di browser Anda.</p>
                                </div>
                            </li>

                            <li class="flex items-start gap-2.5 p-3 rounded-xl bg-slate-50 border border-slate-200">
                                <span class="w-5 h-5 rounded-full bg-slate-900 text-white font-extrabold flex items-center justify-center text-[10px] shrink-0 mt-0.5">4</span>
                                <div>
                                    <p class="font-bold text-slate-900">Jalankan Ekstensi &amp; Ekspor JSON</p>
                                    <p class="text-[11px] text-slate-500">Buka ekstensi <em>PDKI Auto Fetch</em>, masukkan daftar kode, klik <strong>Jalankan</strong>, lalu klik <strong>Export JSON</strong>.</p>
                                </div>
                            </li>

                            <li class="flex items-start gap-2.5 p-3 rounded-xl bg-emerald-50 border border-emerald-200">
                                <span class="w-5 h-5 rounded-full bg-emerald-700 text-white font-extrabold flex items-center justify-center text-[10px] shrink-0 mt-0.5">5</span>
                                <div>
                                    <p class="font-bold text-emerald-900">Unggah ke PRISMA</p>
                                    <p class="text-[11px] text-emerald-800">Kembali ke tab PRISMA ini, klik tab <em>"Unggah Berkas"</em> di atas, dan masukkan file JSON hasil export tersebut.</p>
                                </div>
                            </li>

                            <li class="flex items-start gap-2.5 p-3 rounded-xl bg-indigo-50 border border-indigo-200">
                                <span class="w-5 h-5 rounded-full bg-indigo-700 text-white font-extrabold flex items-center justify-center text-[10px] shrink-0 mt-0.5">6</span>
                                <div>
                                    <p class="font-bold text-indigo-900">Kirim ke SINTA (Opsional)</p>
                                    <p class="text-[11px] text-indigo-800">Klik tombol <strong>"Ekspor JSON SINTA"</strong> di toolbar PRISMA, lalu buka ekstensi <em>Auto Input HKI - SINTA</em> untuk input otomatis ke situs SINTA.</p>
                                </div>
                            </li>
                        </ol>

                        <div class="pt-2 flex justify-end">
                            <button type="button" @click="importTab = 'upload'" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs">
                                Lanjut Unggah File &rarr;
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>
</div>
@endsection


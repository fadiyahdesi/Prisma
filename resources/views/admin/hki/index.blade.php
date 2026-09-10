@extends('layouts.app')

@section('title', 'Verifikasi Berkas Sentra HKI - Admin P3M')

@section('content')
<div x-data="{ sidebarOpen: false, modalVerifyOpen: false, selectedHki: null, verifyAction: 'approve' }" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <x-sidebar />

    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        <header class="bg-white border-b border-slate-200 py-4 sticky top-0 z-30 shadow-xs">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
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

        <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
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

                                    @if($item->catatan_verifikasi)
                                        <p class="text-[11px] text-slate-500 italic bg-slate-50 p-2 rounded-lg border border-slate-100">
                                            Catatan: {{ $item->catatan_verifikasi }}
                                        </p>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2 shrink-0 pt-2 lg:pt-0">
                                    <a href="{{ asset('storage/' . $item->file_sertifikat) }}" target="_blank"
                                       class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        <span>Berkas Sertifikat</span>
                                    </a>

                                    <button type="button"
                                            @click="selectedHki = {{ $item->toJson() }}; verifyAction = 'approve'; modalVerifyOpen = true"
                                            class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>Verifikasi</span>
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
        </main>
    </div>
</div>
@endsection


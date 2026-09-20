@extends('layouts.app')

@section('title', 'Eksekusi Transfer Insentif ' . $klaim->nomor_klaim . ' - Keuangan')

@section('content')
<div x-data="{ sidebarOpen: false, modalTransferOpen: false, selectedDist: null }" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <x-sidebar />

    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        <header class="bg-white border-b border-slate-200 py-4 sticky top-0 z-30 shadow-xs">
            <div class="w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <a href="{{ route('keuangan.reward.index') }}" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center shrink-0 transition" title="Kembali" aria-label="Kembali">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </a>
                    <div>
                        <h1 class="font-extrabold text-xl text-slate-900 leading-tight">Eksekusi Transfer Rekening Anggota</h1>
                        <p class="text-xs font-semibold text-slate-500">Klaim: {{ $klaim->nomor_klaim }} &bull; Penyaluran Insentif Multi-Penulis (US-11.4)</p>
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

            <!-- Claim Summary Box -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-5">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-4">
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 rounded-lg text-xs font-extrabold {{ $klaim->jenis_klaim === 'Publikasi' ? 'bg-blue-100 text-blue-900 border border-blue-300' : 'bg-indigo-100 text-indigo-900 border border-indigo-300' }}">
                            {{ $klaim->jenis_klaim }}
                        </span>
                        <span class="px-3 py-1 rounded-lg text-xs font-extrabold bg-slate-100 text-slate-900 border border-slate-300">
                            {{ $klaim->kategori_insentif }}
                        </span>
                    </div>

                    <div>
                        @if($klaim->status_klaim === 'Disbursed')
                            <span class="px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Seluruh Insentif Selesai Dicairkan (100%)
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs font-black bg-blue-100 text-blue-800 border border-blue-300 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Menunggu Penyelesaian Transfer Bank
                            </span>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="p-5 rounded-2xl bg-emerald-50/70 border border-emerald-200">
                        <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider block">Total Insentif yang Harus Ditransfer</span>
                        <div class="text-2xl font-black text-emerald-800 mt-1">
                            <span class="text-sm font-bold mr-1">Rp</span>{{ number_format($klaim->total_reward, 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Objek Aset</span>
                        <div class="text-xs font-extrabold text-slate-900 mt-1 truncate">
                            {{ $klaim->publikasi?->judul_artikel ?? $klaim->hki?->judul_hki }}
                        </div>
                        <span class="text-[10px] font-semibold text-slate-500 mt-0.5 block">Pemohon: {{ $klaim->user->name }}</span>
                    </div>

                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Persetujuan P3M</span>
                        <div class="text-xs font-extrabold text-slate-800 mt-1">
                            {{ $klaim->approved_at ? $klaim->approved_at->format('d F Y, H:i') : '-' }}
                        </div>
                        <span class="text-[10px] font-semibold text-slate-500 mt-0.5 block">Oleh: {{ $klaim->approver?->name ?? 'Admin P3M' }}</span>
                    </div>
                </div>
            </div>

            <!-- Distribution Members Table -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="font-bold text-sm text-slate-800">Daftar Rekening Tujuan Transfer Multi-Penulis</h2>
                        <p class="text-xs text-slate-500">Lakukan transfer ke rekening bank masing-masing penerima dan unggah bukti transfer mutasi</p>
                    </div>

                    @if($klaim->file_surat_pernyataan)
                        <a href="{{ asset('storage/' . $klaim->file_surat_pernyataan) }}" target="_blank"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                            <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            <span>Surat Kesepakatan (PDF)</span>
                        </a>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50/80 border-b border-slate-200/70 text-slate-600 font-extrabold uppercase text-[10px] tracking-wider">
                            <tr>
                                <th class="py-3 px-4">No</th>
                                <th class="py-3 px-4">Nama Penerima &amp; Peran</th>
                                <th class="py-3 px-4 text-center">Porsi (%)</th>
                                <th class="py-3 px-4 text-right">Nominal Transfer</th>
                                <th class="py-3 px-4">Rekening Tujuan</th>
                                <th class="py-3 px-4 text-center">Status Mutasi</th>
                                <th class="py-3 px-4 text-right">Aksi Keuangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($klaim->distribusi as $idx => $dist)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="py-4 px-4 font-bold text-slate-500">{{ $idx + 1 }}</td>
                                    <td class="py-4 px-4">
                                        <div class="font-extrabold text-slate-900">{{ $dist->nama_penulis }}</div>
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-700 mt-1 inline-block">
                                            {{ $dist->peran_penulis }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-center font-extrabold text-blue-700 text-xs">
                                        {{ number_format($dist->persentase, 2, ',', '.') }}%
                                    </td>
                                    <td class="py-4 px-4 text-right font-black text-emerald-800 text-xs">
                                        Rp {{ number_format($dist->nominal_bagian, 0, ',', '.') }}
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="font-bold text-slate-900">{{ $dist->nama_bank }} - {{ $dist->nomor_rekening }}</div>
                                        <div class="text-[11px] text-slate-500">a.n. {{ $dist->nama_pemilik_rekening }}</div>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        @if($dist->status_transfer === 'Disbursed')
                                            <div class="inline-flex flex-col items-center">
                                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                    Ditransfer
                                                </span>
                                                <span class="text-[9px] text-slate-500 mt-0.5">
                                                    Ref: {{ $dist->nomor_referensi }}
                                                </span>
                                                <span class="text-[9px] text-slate-400">
                                                    {{ $dist->tanggal_transfer ? $dist->tanggal_transfer->format('d/m/Y') : '' }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-800 border border-amber-300">
                                                Menunggu Cair
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-right">
                                        @if($dist->status_transfer === 'Pending')
                                            <button type="button"
                                                    @click="selectedDist = {{ $dist->toJson() }}; modalTransferOpen = true"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-xs transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                <span>Proses Transfer</span>
                                            </button>
                                        @else
                                            @if($dist->file_bukti_transfer)
                                                <a href="{{ asset('storage/' . $dist->file_bukti_transfer) }}" target="_blank"
                                                   class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-blue-700 font-bold text-[11px] transition">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                    <span>Bukti Mutasi</span>
                                                </a>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50 border-t-2 border-slate-200 text-xs font-black text-slate-900">
                            <tr>
                                <td colspan="3" class="py-3 px-4 text-right">TOTAL TRANSFER:</td>
                                <td class="py-3 px-4 text-right text-emerald-800">Rp {{ number_format($klaim->total_reward, 0, ',', '.') }}</td>
                                <td colspan="3"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Transfer Execution Modal -->
            <div x-show="modalTransferOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
                <div @click.away="modalTransferOpen = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="font-extrabold text-slate-900 text-base">Rekam Bukti Transfer Perbankan</h3>
                        <button @click="modalTransferOpen = false" class="text-slate-400 hover:text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form :action="selectedDist ? `{{ url('/keuangan/reward/distribusi') }}/${selectedDist.id}/disburse` : '#'" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs space-y-1">
                            <div class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider">Penerima Transfer:</div>
                            <div class="font-extrabold text-emerald-950 text-sm" x-text="selectedDist?.nama_penulis"></div>
                            <div class="font-semibold text-emerald-900">
                                Rekening: <span class="font-bold" x-text="`${selectedDist?.nama_bank} - ${selectedDist?.nomor_rekening} (a.n. ${selectedDist?.nama_pemilik_rekening})`"></span>
                            </div>
                            <div class="text-sm font-black text-emerald-800 mt-1">
                                Nominal: Rp <span x-text="selectedDist ? new Intl.NumberFormat('id-ID').format(selectedDist.nominal_bagian) : '0'"></span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Tanggal Transfer Bank <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" name="tanggal_transfer" value="{{ date('Y-m-d') }}" required
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-emerald-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Nomor Referensi Mutasi / Transaksi Perbankan <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="nomor_referensi" required placeholder="Contoh: TRX-BNI-20260911-00892"
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-emerald-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Unggah Struk / Slip Bukti Transfer (PDF/JPG/PNG) <span class="text-rose-500">*</span>
                            </label>
                            <input type="file" name="file_bukti_transfer" accept=".pdf,.jpg,.jpeg,.png" required
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-emerald-500 bg-slate-50/50 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-emerald-100 file:text-emerald-800">
                            <p class="text-[11px] text-slate-400 mt-1 font-semibold">Ukuran file maksimal 5 MB.</p>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2">
                            <button type="button" @click="modalTransferOpen = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-xs transition">
                                Simpan Bukti &amp; Tandai Ditransfer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection


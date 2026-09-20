@extends('layouts.app')

@section('title', 'Review Klaim Reward Insentif - Admin P3M')

@section('content')
<div x-data="{ sidebarOpen: false, modalApproveOpen: false, modalRejectOpen: false, selectedClaim: null }" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <x-sidebar />

    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        <header class="bg-white border-b border-slate-200 py-4 sticky top-0 z-30 shadow-xs">
            <div class="w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <h1 class="font-extrabold text-xl text-slate-900 leading-tight">Review Klaim Reward Insentif</h1>
                        <p class="text-xs font-semibold text-slate-500">P3M UHN &bull; Verifikasi Kelayakan Berkas &amp; Persetujuan Penerusan ke Keuangan (US-11.3)</p>
                    </div>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-black bg-blue-100 text-blue-800 border border-blue-300">
                    Pengelola P3M
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

            <!-- Tabs -->
            <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 pb-3">
                <a href="{{ route('admin.reward.index', ['status' => 'Submitted']) }}"
                   class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $status === 'Submitted' ? 'bg-amber-500 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                    <span>Menunggu Review</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $status === 'Submitted' ? 'bg-amber-600 text-white' : 'bg-amber-100 text-amber-800' }}">
                        {{ $counts['submitted'] }}
                    </span>
                </a>

                <a href="{{ route('admin.reward.index', ['status' => 'Approved_P3M']) }}"
                   class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $status === 'Approved_P3M' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                    <span>Disetujui P3M</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $status === 'Approved_P3M' ? 'bg-blue-700 text-white' : 'bg-blue-100 text-blue-800' }}">
                        {{ $counts['approved'] }}
                    </span>
                </a>

                <a href="{{ route('admin.reward.index', ['status' => 'Disbursed']) }}"
                   class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $status === 'Disbursed' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                    <span>Selesai Dicairkan</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $status === 'Disbursed' ? 'bg-emerald-700 text-white' : 'bg-emerald-100 text-emerald-800' }}">
                        {{ $counts['disbursed'] }}
                    </span>
                </a>

                <a href="{{ route('admin.reward.index', ['status' => 'Rejected']) }}"
                   class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $status === 'Rejected' ? 'bg-rose-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                    <span>Ditolak</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $status === 'Rejected' ? 'bg-rose-700 text-white' : 'bg-rose-100 text-rose-800' }}">
                        {{ $counts['rejected'] }}
                    </span>
                </a>

                <a href="{{ route('admin.reward.index', ['status' => 'all']) }}"
                   class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $status === 'all' ? 'bg-slate-900 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                    Semua
                </a>
            </div>

            <!-- Claim List -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h2 class="font-bold text-sm text-slate-800">Antrean Pengajuan Insentif Publikasi &amp; HKI</h2>
                        <p class="text-xs text-slate-500">Periksa kesepakatan pembagian 100% dan setujui untuk dialihkan ke bagian keuangan</p>
                    </div>

                    <form method="GET" action="{{ route('admin.reward.index') }}" class="flex items-center gap-2">
                        <input type="hidden" name="status" value="{{ $status }}">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor klaim / pemohon..."
                               class="px-3.5 py-1.5 text-xs font-semibold rounded-xl border border-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500">
                        <button type="submit" class="px-3 py-1.5 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-slate-800">
                            Cari
                        </button>
                    </form>
                </div>

                @if($klaimList->isEmpty())
                    <div class="p-12 text-center">
                        <p class="text-xs font-bold text-slate-400">Tidak ada pengajuan klaim reward pada status ini.</p>
                    </div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach($klaimList as $item)
                            <div class="p-5 hover:bg-slate-50/70 transition flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                                <div class="space-y-1.5 flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="px-2.5 py-0.5 rounded-md text-[11px] font-extrabold {{ $item->jenis_klaim === 'Publikasi' ? 'bg-blue-100 text-blue-800 border border-blue-300' : 'bg-indigo-100 text-indigo-800 border border-indigo-300' }}">
                                            {{ $item->jenis_klaim }}
                                        </span>
                                        <span class="px-2.5 py-0.5 rounded-md text-[11px] font-extrabold bg-slate-100 text-slate-800 border border-slate-300">
                                            {{ $item->kategori_insentif }}
                                        </span>
                                        <span class="px-2.5 py-0.5 rounded-md text-[11px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300">
                                            Rp {{ number_format($item->total_reward, 0, ',', '.') }}
                                        </span>
                                    </div>

                                    <h3 class="font-extrabold text-slate-900 text-sm leading-snug">
                                        {{ $item->nomor_klaim }} &bull; {{ $item->publikasi?->judul_artikel ?? $item->hki?->judul_hki ?? 'Aset' }}
                                    </h3>

                                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500 font-semibold">
                                        <span>Pemohon: <strong class="text-slate-900">{{ $item->user->name }}</strong></span>
                                        <span>&bull;</span>
                                        <span>Distribusi: <strong class="text-slate-800">{{ $item->distribusi->count() }} Anggota</strong></span>
                                        <span>&bull;</span>
                                        <span>Tanggal: {{ $item->created_at->format('d/m/Y H:i') }}</span>
                                    </div>

                                    @if($item->catatan_p3m)
                                        <p class="text-[11px] text-slate-500 italic bg-slate-50 p-2 rounded-lg border border-slate-100">
                                            Catatan: {{ $item->catatan_p3m }}
                                        </p>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2 shrink-0 pt-2 lg:pt-0">
                                    <a href="{{ route('reward.show', $item) }}"
                                       class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                                        Rincian
                                    </a>

                                    @if($item->file_surat_pernyataan)
                                        <a href="{{ asset('storage/' . $item->file_surat_pernyataan) }}" target="_blank"
                                           class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                            <span>Surat 100%</span>
                                        </a>
                                    @endif

                                    @if($item->status_klaim === 'Submitted')
                                        <button type="button"
                                                @click="selectedClaim = {{ $item->toJson() }}; modalApproveOpen = true"
                                                class="px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            <span>Setujui Klaim</span>
                                        </button>

                                        <button type="button"
                                                @click="selectedClaim = {{ $item->toJson() }}; modalRejectOpen = true"
                                                class="px-3 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-xs transition">
                                            Tolak
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $klaimList->links() }}
                    </div>
                @endif
            </div>

            <!-- Approve Modal -->
            <div x-show="modalApproveOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
                <div @click.away="modalApproveOpen = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="font-extrabold text-slate-900 text-base">Persetujuan Klaim Reward Insentif</h3>
                        <button @click="modalApproveOpen = false" class="text-slate-400 hover:text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form :action="selectedClaim ? `{{ url('/admin/reward') }}/${selectedClaim.id}/approve` : '#'" method="POST" class="space-y-4">
                        @csrf
                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-1">
                            <div class="font-bold text-slate-900" x-text="selectedClaim?.nomor_klaim"></div>
                            <div class="text-slate-500 font-semibold">
                                Total Reward: <span class="font-bold text-emerald-700" x-text="selectedClaim ? `Rp ${new Intl.NumberFormat('id-ID').format(selectedClaim.total_reward)}` : ''"></span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Catatan Persetujuan P3M
                            </label>
                            <textarea name="catatan_p3m" rows="3"
                                      placeholder="Klaim memenuhi seluruh persyaratan matriks tarif SK Rektor UHN."
                                      class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-blue-500">Berkas dan kesepakatan pembagian insentif multi-penulis telah diverifikasi valid 100%. Disetujui untuk diteruskan ke Divisi Keuangan.</textarea>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2">
                            <button type="button" @click="modalApproveOpen = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-xs transition">
                                Setujui &amp; Teruskan ke Keuangan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Reject Modal -->
            <div x-show="modalRejectOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
                <div @click.away="modalRejectOpen = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="font-extrabold text-rose-700 text-base">Tolak Pengajuan Klaim Reward</h3>
                        <button @click="modalRejectOpen = false" class="text-slate-400 hover:text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form :action="selectedClaim ? `{{ url('/admin/reward') }}/${selectedClaim.id}/reject` : '#'" method="POST" class="space-y-4">
                        @csrf
                        <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-950 space-y-1">
                            <div class="font-bold" x-text="selectedClaim?.nomor_klaim"></div>
                            <p class="text-[11px] font-semibold text-rose-800">
                                Penolakan ini akan melepaskan kunci klaim aset (unlock) agar pengusul dapat memperbaiki berkas dan mengajukan ulang.
                            </p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Alasan Penolakan <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="catatan_p3m" rows="3" required
                                      placeholder="Tuliskan alasan penolakan secara jelas..."
                                      class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-rose-500"></textarea>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2">
                            <button type="button" @click="modalRejectOpen = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs shadow-xs transition">
                                Tolak Klaim
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection


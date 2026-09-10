@extends('layouts.app')

@section('title', 'Verifikasi Usulan LPPM - PRISMA UHN')

@section('content')
<div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <x-sidebar />
    <div class="flex-1 lg:pl-64 min-w-0">
        <header class="bg-white border-b border-slate-200 py-4 sticky top-0 z-30 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center gap-3">
                <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl bg-slate-100 text-slate-700" aria-label="Buka menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h-16"/></svg>
                </button>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-wider text-blue-700">Verifikasi Institusi</p>
                    <h1 class="text-xl font-extrabold text-slate-900">Antrian Verifikasi Usulan</h1>
                    <p class="text-xs font-semibold text-slate-500">Periksa kelengkapan administratif sebelum usulan diteruskan ke tahap berikutnya.</p>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-sm font-bold">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-300 text-rose-900 text-sm font-bold">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-300 text-rose-900 text-sm font-bold">{{ $errors->first() }}</div>
            @endif

            <div class="space-y-4">
                <div class="bg-white rounded-2xl border border-slate-200 border-l-4 border-l-blue-600 shadow-sm px-5 sm:px-7 py-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-600 shrink-0"></span>
                            <h2 class="text-lg font-extrabold text-slate-900">Antrean Verifikasi</h2>
                        </div>
                        <p class="text-xs text-slate-500 mt-1 ml-4">{{ $proposals->count() }} usulan menunggu pemeriksaan.</p>
                    </div>
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-300 text-xs font-extrabold text-slate-700 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Kembali
                    </a>
                </div>

                <form method="GET" class="bg-white rounded-3xl border border-slate-200 shadow-sm p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
                    <label class="text-xs font-bold text-slate-600">Ketua Pengusul<input name="leader" value="{{ request('leader') }}" placeholder="Cari nama ketua" class="mt-1.5 w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold"></label>
                    <label class="text-xs font-bold text-slate-600">Fakultas<input name="faculty" value="{{ request('faculty') }}" placeholder="ID fakultas" class="mt-1.5 w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold"></label>
                    <label class="text-xs font-bold text-slate-600">Program Studi<input name="prodi" value="{{ request('prodi') }}" placeholder="ID program studi" class="mt-1.5 w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold"></label>
                    <label class="text-xs font-bold text-slate-600">Skema<select name="scheme" class="mt-1.5 w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold"><option value="">Semua skema</option>@foreach($schemes as $scheme)<option value="{{ $scheme->id }}" @selected((string) request('scheme') === (string) $scheme->id)>{{ $scheme->kode_skema }}</option>@endforeach</select></label>
                    <button class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-extrabold">Terapkan Filter</button>
                </form>

                @forelse($proposals as $proposal)
                    <article class="bg-white rounded-3xl border border-slate-200 shadow-lg p-5 sm:p-6 space-y-5">
                        <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_220px] gap-5">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="px-2.5 py-1 rounded-lg bg-blue-900 text-white font-mono text-xs font-black">{{ $proposal->kode_usulan }}</span>
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-black {{ $proposal->status === 'In_review' ? 'bg-blue-100 text-blue-900' : 'bg-amber-100 text-amber-900' }}">{{ $proposal->status === 'In_review' ? 'In Review' : 'Submitted' }}</span>
                                </div>
                                <h3 class="text-base sm:text-lg font-extrabold text-slate-900 mt-3 break-words">{{ $proposal->judul_usulan }}</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mt-4 text-xs">
                                    <div><span class="block text-slate-500 font-bold uppercase">Pengusul</span><strong>{{ $proposal->pengusul->name }}</strong></div>
                                    <div><span class="block text-slate-500 font-bold uppercase">Fakultas</span><strong>{{ $proposal->pengusul->fakultas->nama_fakultas ?? '-' }}</strong></div>
                                    <div><span class="block text-slate-500 font-bold uppercase">Program Studi</span><strong>{{ $proposal->pengusul->prodi->nama_prodi ?? '-' }}</strong></div>
                                    <div><span class="block text-slate-500 font-bold uppercase">Skema</span><strong>{{ $proposal->skema->nama_skema }}</strong></div>
                                </div>
                                <div class="mt-4 rounded-xl bg-blue-50 border border-blue-200 p-3 text-xs"><span class="font-bold text-blue-900">Anggota Tim:</span> {{ $proposal->anggota->pluck('nama')->join(', ') ?: 'Tidak ada anggota tambahan' }}</div>
                                @if($proposal->file_proposal_path)
                                    <a href="{{ route('usulan.document', $proposal) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 mt-3 text-xs font-extrabold text-blue-700 hover:text-blue-900">Buka dokumen proposal PDF</a>
                                @endif
                                @if($proposal->kaprodi_alignment_status)
                                    <div class="mt-3 rounded-xl bg-indigo-50 border border-indigo-200 p-3 text-xs text-indigo-950"><span class="font-bold">Rekomendasi Kaprodi ({{ $proposal->kaprodi_alignment_status }}):</span> {{ $proposal->kaprodi_recommendation }}</div>
                                @endif
                            </div>
                            <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4 text-xs space-y-2">
                                <div class="flex justify-between gap-3"><span class="text-slate-500">Dikirim</span><strong>{{ $proposal->submitted_at?->format('d M Y H:i') ?? '-' }}</strong></div>
                                <div class="flex justify-between gap-3"><span class="text-slate-500">RAB</span><strong>Rp {{ number_format($proposal->total_rab, 0, ',', '.') }}</strong></div>
                                <div class="flex justify-between gap-3"><span class="text-slate-500">Luaran</span><strong>{{ $proposal->luaran->count() }}</strong></div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-200 rounded-2xl bg-slate-50 p-4">
                            <div class="flex flex-col lg:flex-row lg:items-end gap-3">
                                <div class="shrink-0 lg:w-36">
                                    <p class="text-xs font-black uppercase tracking-wide text-slate-700">Keputusan</p>
                                    <p class="text-[11px] text-slate-500 mt-1">Pilih status akhir usulan.</p>
                                </div>
                                <form method="POST" action="{{ route('admin.lppm-approval.in-review', $proposal) }}" class="shrink-0">
                                    @csrf
                                    <button class="w-full px-4 py-2.5 rounded-xl bg-blue-100 hover:bg-blue-200 text-blue-900 border border-blue-300 text-xs font-extrabold">Tandai In Review</button>
                                </form>
                                <form method="POST" action="{{ route('admin.lppm-approval.update', $proposal) }}" class="flex flex-col xl:flex-row gap-3 flex-1">
                                    @csrf
                                    <textarea name="verification_notes" rows="1" placeholder="Catatan revisi untuk pengusul (wajib untuk draf)" class="min-h-11 flex-1 px-3 py-2.5 rounded-xl bg-white border border-slate-300 text-xs font-semibold"></textarea>
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                        <button name="decision" value="approved" class="px-4 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-extrabold">Disetujui</button>
                                        <button name="decision" value="draft" class="px-4 py-2.5 rounded-xl bg-amber-100 hover:bg-amber-200 text-amber-900 border border-amber-300 text-xs font-extrabold">Kembalikan</button>
                                        <button name="decision" value="rejected" onclick="return confirm('Tolak usulan ini secara permanen?')" class="px-4 py-2.5 rounded-xl bg-rose-100 hover:bg-rose-200 text-rose-900 border border-rose-300 text-xs font-extrabold">Ditolak</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="bg-white rounded-3xl border border-slate-200 p-10 text-center text-sm font-semibold text-slate-500">Belum ada usulan yang menunggu verifikasi.</div>
                @endforelse
            </div>
        </main>
    </div>
</div>
@endsection

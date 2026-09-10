@extends('layouts.app')

@section('title', 'Keselarasan Roadmap Prodi - PRISMA UHN')

@section('content')
<div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <x-sidebar />
    <div class="flex-1 lg:pl-64 min-w-0">
        <header class="bg-white border-b border-slate-200 py-4 sticky top-0 z-30 shadow-sm">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center gap-3">
                <a href="{{ route('dashboard') }}" aria-label="Kembali ke dashboard" class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700 hover:bg-slate-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div><p class="text-[10px] font-black uppercase tracking-wider text-indigo-700">Program Studi</p><h1 class="text-xl font-extrabold text-slate-900">Keselarasan Roadmap Prodi</h1><p class="text-xs text-slate-500">Berikan rekomendasi keselarasan topik usulan dengan roadmap dan kelompok keahlian prodi.</p></div>
            </div>
        </header>
        <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-5">
            @if(session('success'))<div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-sm font-bold">{{ session('success') }}</div>@endif
            @forelse($proposals as $proposal)
                <article class="bg-white rounded-3xl border border-slate-200 shadow-lg p-5 sm:p-6 space-y-5">
                    <div class="flex flex-wrap items-center gap-2"><span class="px-2.5 py-1 rounded-lg bg-indigo-900 text-white font-mono text-xs font-black">{{ $proposal->kode_usulan }}</span><span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-bold">{{ $proposal->status }}</span></div>
                    <h2 class="text-lg font-extrabold text-slate-900">{{ $proposal->judul_usulan }}</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs"><div><span class="block text-slate-500 font-bold uppercase">Pengusul</span><strong>{{ $proposal->pengusul->name }}</strong></div><div><span class="block text-slate-500 font-bold uppercase">Skema</span><strong>{{ $proposal->skema->nama_skema }}</strong></div><div><span class="block text-slate-500 font-bold uppercase">Rumpun Ilmu</span><strong>{{ $proposal->rumpun_ilmu_level_1 }} / {{ $proposal->rumpun_ilmu_level_2 }}</strong></div></div>
                    <form method="POST" action="{{ route('admin.prodi-roadmap.update', $proposal) }}" class="border-t border-slate-200 pt-4 space-y-3">
                        @csrf
                        <label class="block text-xs font-extrabold text-slate-700">Rekomendasi Keselarasan<select name="kaprodi_alignment_status" required class="mt-1.5 w-full sm:w-72 px-3 py-2.5 rounded-xl border border-slate-300 text-xs font-bold"><option value="">Pilih rekomendasi</option><option value="aligned" @selected($proposal->kaprodi_alignment_status === 'aligned')>Selaras dengan roadmap</option><option value="revision" @selected($proposal->kaprodi_alignment_status === 'revision')>Selaras dengan catatan revisi</option><option value="not_aligned" @selected($proposal->kaprodi_alignment_status === 'not_aligned')>Tidak selaras</option></select></label>
                        <textarea name="kaprodi_recommendation" required rows="3" placeholder="Tuliskan alasan, kelompok keahlian, atau catatan roadmap untuk P3M/reviewer." class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold">{{ $proposal->kaprodi_recommendation }}</textarea>
                        <button class="px-5 py-2.5 rounded-xl bg-indigo-700 hover:bg-indigo-800 text-white text-xs font-extrabold">Simpan Rekomendasi</button>
                    </form>
                </article>
            @empty
                <div class="bg-white rounded-3xl border border-slate-200 p-10 text-center text-sm font-semibold text-slate-500">Belum ada usulan prodi dalam antrean.</div>
            @endforelse
        </main>
    </div>
</div>
@endsection

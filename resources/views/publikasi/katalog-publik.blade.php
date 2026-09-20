@extends('layouts.app')

@section('title', 'Katalog Publikasi & Riset Dosen - PRISMA UHN')

@section('content')
    @include('components.navbar')

    <main class="flex-grow pt-28 pb-20 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center max-w-3xl mx-auto mb-10">
                <span class="text-xs font-bold text-blue-700 bg-blue-50 border border-blue-200 px-3.5 py-1.5 rounded-full uppercase tracking-wider">
                    Pangkalan Data Terbuka • Tanpa Login
                </span>
                <h1 class="mt-4 text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Katalog Publikasi &amp; Karya Ilmiah Dosen UHN
                </h1>
                <p class="mt-3 text-sm text-slate-600">
                    Eksplorasi seluruh artikel jurnal internasional, Scopus, dan SINTA yang telah dipublikasikan oleh para dosen dan peneliti Universitas Harkat Negeri.
                </p>
            </div>

            <!-- Search & Filter Card -->
            <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm mb-8">
                <form action="{{ route('publikasi.katalog-publik') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Pencarian Judul, Penulis, DOI, atau Jurnal:</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ $search }}"
                                   placeholder="Contoh: Machine Learning, 0613028601, Ginanjar, IEEE..."
                                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-blue-600 focus:outline-none bg-slate-50/50">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Peringkat Jurnal:</label>
                        <select name="kategori_peringkat" onchange="this.form.submit()" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-blue-600 focus:outline-none bg-slate-50/50">
                            <option value="">-- Semua Peringkat --</option>
                            <option value="Scopus Q1" {{ $kategori === 'Scopus Q1' ? 'selected' : '' }}>Scopus Q1</option>
                            <option value="Scopus Q2" {{ $kategori === 'Scopus Q2' ? 'selected' : '' }}>Scopus Q2</option>
                            <option value="Scopus Q3" {{ $kategori === 'Scopus Q3' ? 'selected' : '' }}>Scopus Q3</option>
                            <option value="Scopus Q4" {{ $kategori === 'Scopus Q4' ? 'selected' : '' }}>Scopus Q4</option>
                            <option value="SINTA 1" {{ $kategori === 'SINTA 1' ? 'selected' : '' }}>SINTA 1</option>
                            <option value="SINTA 2" {{ $kategori === 'SINTA 2' ? 'selected' : '' }}>SINTA 2</option>
                            <option value="SINTA 3" {{ $kategori === 'SINTA 3' ? 'selected' : '' }}>SINTA 3</option>
                            <option value="SINTA 4" {{ $kategori === 'SINTA 4' ? 'selected' : '' }}>SINTA 4</option>
                        </select>
                    </div>
                </form>
            </div>

            <!-- Articles Grid -->
            @if($publikasiList->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($publikasiList as $pub)
                        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-3">
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider {{ str_contains($pub->kategori_peringkat, 'Scopus') ? 'bg-amber-100 text-amber-900 border border-amber-200' : 'bg-blue-100 text-blue-900 border border-blue-200' }}">
                                        {{ $pub->kategori_peringkat }}
                                    </span>
                                    <span class="text-xs text-slate-400 font-bold">Tahun {{ $pub->tahun_terbit }}</span>
                                </div>
                                <h3 class="font-extrabold text-sm text-slate-900 line-clamp-3 leading-snug hover:text-blue-700 transition">
                                    {{ $pub->judul_artikel }}
                                </h3>
                                <p class="text-xs text-slate-500 font-medium mt-2">
                                    <strong class="text-slate-700">{{ $pub->user->name ?? 'Dosen UHN' }}</strong> &bull; {{ $pub->nama_jurnal }}
                                </p>
                            </div>

                            <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                                <span class="font-mono text-[11px] text-slate-400 truncate max-w-[180px]">DOI: {{ $pub->doi ?: '-' }}</span>
                                @if($pub->url_artikel)
                                    <a href="{{ $pub->url_artikel }}" target="_blank" class="text-blue-700 hover:text-blue-900 font-bold inline-flex items-center gap-1">
                                        <span>Buka</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $publikasiList->links() }}
                </div>
            @else
                <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center">
                    <p class="text-sm font-semibold text-slate-500">Belum ada data publikasi yang sesuai dengan kriteria pencarian.</p>
                </div>
            @endif
        </div>
    </main>

    @include('components.footer')
@endsection


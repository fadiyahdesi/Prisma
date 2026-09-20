<section id="bank-publikasi" class="py-20 bg-white relative overflow-hidden border-t border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
            <div>
                <span class="text-xs font-bold text-blue-700 bg-blue-50 border border-blue-200 px-3.5 py-1.5 rounded-full uppercase tracking-wider">
                    Bank Publikasi &amp; Karya Ilmiah Dosen
                </span>
                <h2 class="mt-4 text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Riset &amp; Publikasi Ilmiah Bereputasi
                </h2>
                <p class="mt-3 text-base text-slate-600 max-w-2xl">
                    Daftar artikel ilmiah internasional dan nasional yang dipublikasikan oleh para dosen dan peneliti Universitas Harkat Negeri, terindeks Scopus &amp; SINTA.
                </p>
            </div>
            <div class="shrink-0">
                <a href="{{ route('publikasi.katalog-publik') }}" class="px-5 py-3 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs shadow-md transition flex items-center gap-2">
                    <span>Lihat Seluruh Publikasi Dosen</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>

        @if(!empty($recentPublications) && $recentPublications->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($recentPublications as $pub)
                    <div class="bg-slate-50/70 rounded-3xl border border-slate-200/80 p-6 shadow-xs hover:shadow-lg hover:bg-white transition-all flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider {{ str_contains($pub->kategori_peringkat, 'Scopus') ? 'bg-amber-100 text-amber-900 border border-amber-200' : 'bg-blue-100 text-blue-900 border border-blue-200' }}">
                                    {{ $pub->kategori_peringkat }}
                                </span>
                                <span class="text-xs text-slate-400 font-bold">{{ $pub->tahun_terbit }}</span>
                            </div>
                            <h3 class="font-extrabold text-sm text-slate-900 line-clamp-3 leading-snug">
                                {{ $pub->judul_artikel }}
                            </h3>
                            <p class="text-xs text-slate-600 font-medium mt-2">
                                <strong class="text-slate-900">{{ $pub->user->name ?? 'Dosen UHN' }}</strong>
                                <span class="block text-slate-400 text-[11px] truncate mt-0.5">{{ $pub->nama_jurnal }}</span>
                            </p>
                        </div>
                        <div class="pt-4 mt-4 border-t border-slate-200/60 flex items-center justify-between text-xs">
                            <span class="font-mono text-[11px] text-slate-400 truncate max-w-[170px]">DOI: {{ $pub->doi ?: '-' }}</span>
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
        @else
            <div class="bg-slate-50 rounded-3xl border border-slate-200 p-8 text-center text-sm font-semibold text-slate-500">
                Pangkalan data publikasi sedang dalam proses sinkronisasi dengan pangkalan data nasional.
            </div>
        @endif
    </div>
</section>


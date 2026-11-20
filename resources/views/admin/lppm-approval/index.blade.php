@extends('layouts.app')

@php
    $div = $divisi ?? 'all';
    $labelDivisi = match ($div) {
        'penelitian' => 'Divisi Penelitian',
        'pengabdian' => 'Divisi Pengabdian Masyarakat',
        default => 'Semua Divisi (Penelitian & Pengabdian)',
    };
@endphp

@section('title', 'Verifikasi Dokumen (Template & Similarity) - ' . $labelDivisi)

@section('content')
<div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <x-sidebar />
    <div class="flex-1 lg:pl-64 min-w-0 flex flex-col">
        <header class="bg-white border-b border-slate-200 py-3.5 sticky top-0 z-30 shadow-xs">
            <div class="w-full px-4 sm:px-6 lg:px-8 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl bg-slate-100 text-slate-700" aria-label="Buka menu">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h-16"/></svg>
                    </button>
                    <a href="{{ route('dashboard') }}" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center shrink-0 transition" title="Kembali ke Dashboard">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </a>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 class="text-lg sm:text-xl font-extrabold text-slate-900">Verifikasi Dokumen Proposal (Template &amp; Similarity)</h1>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-blue-100 text-blue-800 border border-blue-200">
                                {{ $labelDivisi }}
                            </span>
                        </div>
                        <p class="text-xs font-semibold text-slate-500">Pemeriksaan kesesuaian template standar BIMA &amp; ambang batas similarity (&le; 20%) sebelum plotting Reviewer.</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.reviewer-assignment.index', $div !== 'all' ? ['divisi' => $div] : []) }}"
                       class="h-9 px-3.5 rounded-xl bg-indigo-50 hover:bg-indigo-600 text-indigo-700 hover:text-white border border-indigo-200 hover:border-indigo-600 font-extrabold text-xs transition inline-flex items-center gap-1.5">
                        <span>Lanjut ke Plotting Reviewer &rarr;</span>
                    </a>
                </div>
            </div>
        </header>

        <main class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-5">
            @if(session('success'))
                <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs sm:text-sm font-bold flex items-center gap-2.5 shadow-xs">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-300 text-rose-900 text-xs sm:text-sm font-bold">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-300 text-rose-900 text-xs sm:text-sm font-bold">{{ $errors->first() }}</div>
            @endif

            <!-- Division Tabs & Filter Toolbar -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-4 space-y-3.5">
                <div class="flex flex-wrap items-center justify-between gap-2 pb-3 border-b border-slate-100">
                    @if($canSwitchDivisi ?? false)
                        <div class="inline-flex flex-wrap items-center gap-1.5 bg-slate-100 p-1 rounded-xl">
                            <a href="{{ route('admin.lppm-approval.index', array_merge(request()->except('divisi'), ['divisi' => 'penelitian'])) }}"
                               class="px-3 py-1.5 rounded-lg text-xs font-extrabold transition inline-flex items-center gap-1.5 {{ $div === 'penelitian' ? 'bg-[#681727] text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                                <span>Divisi Penelitian</span>
                            </a>
                            <a href="{{ route('admin.lppm-approval.index', array_merge(request()->except('divisi'), ['divisi' => 'pengabdian'])) }}"
                               class="px-3 py-1.5 rounded-lg text-xs font-extrabold transition inline-flex items-center gap-1.5 {{ $div === 'pengabdian' ? 'bg-[#681727] text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                                <span>Divisi Pengabdian</span>
                            </a>
                            <a href="{{ route('admin.lppm-approval.index', array_merge(request()->except('divisi'), ['divisi' => 'all'])) }}"
                               class="px-3 py-1.5 rounded-lg text-xs font-extrabold transition inline-flex items-center gap-1.5 {{ $div === 'all' ? 'bg-[#681727] text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                                <span>Semua Divisi</span>
                            </a>
                        </div>
                    @else
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-extrabold {{ $div === 'pengabdian' ? 'bg-purple-100 text-purple-900 border border-purple-200' : 'bg-blue-100 text-blue-900 border border-blue-200' }}">
                            <span>{{ $div === 'pengabdian' ? 'Antrean Khusus Divisi Pengabdian Masyarakat' : 'Antrean Khusus Divisi Penelitian' }}</span>
                        </div>
                    @endif

                    <div class="flex items-center gap-2 text-xs font-bold text-slate-600">
                        <span class="px-2.5 py-1 rounded-lg bg-blue-50 border border-blue-200 text-blue-800">
                            Antrean Verifikasi: <strong class="font-extrabold">{{ $proposals->count() }} Usulan</strong>
                        </span>
                    </div>
                </div>

                <form method="GET" action="{{ route('admin.lppm-approval.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
                    <input type="hidden" name="divisi" value="{{ $div }}">
                    <label class="text-xs font-bold text-slate-600">
                        Ketua Pengusul
                        <input name="leader" value="{{ request('leader') }}" placeholder="Cari nama ketua..." class="mt-1 w-full h-9 px-3 rounded-xl border border-slate-300 text-xs font-semibold">
                    </label>
                    <label class="text-xs font-bold text-slate-600">
                        Skema Hibah
                        <select name="scheme" class="mt-1 w-full h-9 px-3 rounded-xl border border-slate-300 text-xs font-semibold">
                            <option value="">Semua Skema</option>
                            @foreach($schemes as $scheme)
                                <option value="{{ $scheme->id }}" @selected((string) request('scheme') === (string) $scheme->id)>{{ $scheme->kode_skema }} - {{ $scheme->nama_skema }}</option>
                            @endforeach
                        </select>
                    </label>
                    <div class="sm:col-span-2 flex items-center gap-2">
                        <button type="submit" class="h-9 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-extrabold cursor-pointer">Terapkan Filter</button>
                        @if(request()->anyFilled(['leader', 'scheme']))
                            <a href="{{ route('admin.lppm-approval.index', ['divisi' => $div]) }}" class="h-9 px-3.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold inline-flex items-center">Reset</a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Proposal Verification Cards -->
            <div class="space-y-4">
                @forelse($proposals as $proposal)
                    @php
                        $initSimilarity = $proposal->similarity_score !== null ? (float) $proposal->similarity_score : 12;
                        $initTemplate = $proposal->template_status ?: 'sesuai';
                        $katProposal = strtolower($proposal->kategori_usulan ?: ($proposal->skema->kategori ?? 'penelitian'));
                    @endphp
                    <article x-data="{ similarity: {{ $initSimilarity }}, templateStatus: '{{ $initTemplate }}' }"
                             class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5 space-y-4">
                        <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_240px] gap-4">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="px-2.5 py-1 rounded-lg bg-slate-900 text-white font-mono text-xs font-black">{{ $proposal->kode_usulan }}</span>
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-extrabold {{ $katProposal === 'pengabdian' ? 'bg-purple-100 text-purple-800 border border-purple-200' : 'bg-blue-100 text-blue-800 border border-blue-200' }}">
                                        {{ $katProposal === 'pengabdian' ? 'Divisi Pengabdian' : 'Divisi Penelitian' }}
                                    </span>
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-black {{ $proposal->status === 'In_review' ? 'bg-emerald-100 text-emerald-900' : 'bg-amber-100 text-amber-900' }}">
                                        {{ $proposal->status === 'In_review' ? 'Lolos Dokumen (In Review)' : 'Menunggu Verifikasi Dokumen' }}
                                    </span>
                                </div>

                                <h3 class="text-base font-extrabold text-slate-900 mt-2.5 break-words">{{ $proposal->judul_usulan }}</h3>

                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mt-3 text-xs">
                                    <div><span class="block text-slate-500 font-bold uppercase text-[10px]">Ketua Pengusul</span><strong>{{ $proposal->nama_ketua ?: $proposal->pengusul->name }}</strong></div>
                                    <div><span class="block text-slate-500 font-bold uppercase text-[10px]">NIDN / NUPN</span><strong class="font-mono">{{ $proposal->nidn_ketua ?: ($proposal->pengusul->nidn_nim ?? '-') }}</strong></div>
                                    <div><span class="block text-slate-500 font-bold uppercase text-[10px]">Program Studi</span><strong>{{ $proposal->nama_prodi ?: ($proposal->prodi->nama_prodi ?? $proposal->pengusul->prodi->nama_prodi ?? '-') }}</strong></div>
                                    <div><span class="block text-slate-500 font-bold uppercase text-[10px]">Skema</span><strong>{{ $proposal->skema->nama_skema ?? '-' }}</strong></div>
                                </div>

                                <div class="mt-3 rounded-xl bg-slate-50 border border-slate-200 p-2.5 text-xs">
                                    <span class="font-bold text-slate-700">Anggota Tim:</span>
                                    {{ $proposal->nama_anggota ?: ($proposal->anggota->pluck('nama')->join('; ') ?: '-') }}
                                </div>

                                @if($proposal->file_proposal_path)
                                    <a href="{{ route('usulan.document', $proposal) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 mt-2.5 text-xs font-extrabold text-blue-700 hover:text-blue-900">
                                        <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        <span>Buka Berkas Proposal PDF</span>
                                    </a>
                                @endif
                            </div>

                            <div class="rounded-2xl bg-slate-50 border border-slate-200 p-3.5 text-xs space-y-2 h-fit">
                                <div class="flex justify-between gap-2"><span class="text-slate-500">Semester</span><strong>{{ $proposal->semester ?: '-' }}</strong></div>
                                <div class="flex justify-between gap-2"><span class="text-slate-500">Nominal RAB</span><strong class="font-mono">Rp. {{ number_format($proposal->total_rab, 0, '.', ',') }}</strong></div>
                                <div class="flex justify-between gap-2">
                                    <span class="text-slate-500">Cek Template</span>
                                    <span class="font-extrabold" :class="templateStatus === 'sesuai' ? 'text-emerald-700' : 'text-amber-700'"
                                          x-text="templateStatus === 'sesuai' ? 'Sesuai Template' : (templateStatus === 'revisi' ? 'Perlu Revisi' : 'Tidak Sesuai')"></span>
                                </div>
                                <div class="flex justify-between gap-2">
                                    <span class="text-slate-500">Similarity</span>
                                    <span class="px-2 py-0.5 rounded font-mono font-extrabold text-[11px]"
                                          :class="Number(similarity) <= 20 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'"
                                          x-text="similarity + '% (' + (Number(similarity) <= 20 ? 'Lolos' : '> 20%') + ')'"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Form Verifikasi Dokumen: Template & Similarity -->
                        <div class="pt-3.5 border-t border-slate-200 rounded-2xl bg-slate-50/80 p-4">
                            <form method="POST" action="{{ route('admin.lppm-approval.update', $proposal) }}" class="space-y-3">
                                @csrf
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-[11px] font-extrabold text-slate-700 uppercase mb-1">1. Pengecekan Template Dokumen</label>
                                        <select name="template_status" x-model="templateStatus" class="w-full h-9 px-3 rounded-xl bg-white border border-slate-300 text-xs font-bold text-slate-800">
                                            <option value="sesuai">Sesuai Template Standar P3M</option>
                                            <option value="revisi">Perlu Revisi Format / Template</option>
                                            <option value="tidak_sesuai">Tidak Sesuai Template</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-[11px] font-extrabold text-slate-700 uppercase mb-1">2. Skor Similarity / Turnitin (%)</label>
                                        <div class="flex items-center gap-2">
                                            <input type="number" step="0.1" min="0" max="100" name="similarity_score" x-model="similarity"
                                                   placeholder="Contoh: 14.5"
                                                   class="w-full h-9 px-3 rounded-xl bg-white border border-slate-300 text-xs font-mono font-bold text-slate-900">
                                            <span class="shrink-0 px-2.5 py-1.5 rounded-xl text-[11px] font-extrabold border"
                                                  :class="Number(similarity) <= 20 ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-rose-50 text-rose-800 border-rose-200'"
                                                  x-text="Number(similarity) <= 20 ? 'Lolos (&le;20%)' : 'Tinggi (&gt;20%)'"></span>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-[11px] font-extrabold text-slate-700 uppercase mb-1">3. Catatan Verifikasi Dokumen</label>
                                        <input type="text" name="verification_notes" value="{{ $proposal->verification_notes }}"
                                               placeholder="Catatan hasil cek template & similarity..."
                                               class="w-full h-9 px-3 rounded-xl bg-white border border-slate-300 text-xs font-semibold text-slate-800">
                                    </div>
                                </div>

                                <div class="flex flex-wrap items-center justify-end gap-2 pt-1">
                                    <button type="submit" name="decision" value="in_review"
                                            class="h-9 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-extrabold transition cursor-pointer">
                                        Lolos Dokumen &amp; Lanjut Plotting Reviewer
                                    </button>
                                    <button type="submit" name="decision" value="approved"
                                            class="h-9 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold transition cursor-pointer">
                                        Setujui Usulan (Approved)
                                    </button>
                                    <button type="submit" name="decision" value="draft"
                                            class="h-9 px-3.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-extrabold transition cursor-pointer">
                                        Kembalikan Revisi
                                    </button>
                                    <button type="submit" name="decision" value="rejected"
                                            class="h-9 px-3.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-extrabold transition cursor-pointer">
                                        Tolak
                                    </button>
                                </div>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-8 text-center space-y-2">
                        <p class="text-sm font-extrabold text-slate-800">Tidak ada usulan baru yang menunggu verifikasi dokumen pada {{ $labelDivisi }}.</p>
                        <p class="text-xs text-slate-500">Usulan baru yang dikirim oleh dosen pada kategori {{ $div === 'pengabdian' ? 'Pengabdian' : 'Penelitian' }} akan langsung masuk ke halaman ini.</p>
                        <div class="pt-2">
                            <a href="{{ route('usulan.index', $div === 'pengabdian' ? ['kategori' => 'pengabdian'] : ['kategori' => 'penelitian']) }}"
                               class="inline-flex items-center gap-1.5 h-9 px-4 rounded-xl bg-slate-900 text-white text-xs font-extrabold">
                                Lihat Database {{ $div === 'pengabdian' ? 'Pengabdian' : 'Penelitian' }} &rarr;
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
        </main>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Persetujuan Anggota - PRISMA UHN')

@section('content')
<div class="min-h-screen bg-slate-100 text-slate-800">
    <header class="bg-white border-b border-slate-200 shadow-sm">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ route('dashboard') }}" aria-label="Kembali ke dashboard" title="Kembali ke dashboard" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center shrink-0 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div class="min-w-0">
                    <p class="text-[10px] font-black uppercase tracking-wider text-amber-700">Keanggotaan Tim</p>
                    <h1 class="text-lg sm:text-xl font-extrabold text-slate-900 truncate">Persetujuan Anggota</h1>
                </div>
            </div>
            <a href="{{ route('dashboard') }}" class="hidden sm:inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-extrabold border border-slate-300 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/></svg>
                Dashboard
            </a>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-6">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xl p-5 sm:p-6">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M5.07 19a9 9 0 1113.86 0M12 3v1"/></svg>
                </div>
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900">Tinjau undangan Anda</h2>
                    <p class="text-sm text-slate-600 mt-1">Periksa detail proposal sebelum memilih untuk menyetujui atau menolak keikutsertaan Anda.</p>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-sm font-bold">{{ session('success') }}</div>
        @endif

        @forelse($pendingMembers as $member)
            <article class="bg-white rounded-3xl border border-slate-200 shadow-lg p-5 sm:p-6 space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div><span class="text-xs font-bold uppercase text-slate-500">Judul Proposal</span><p class="font-extrabold text-slate-900 mt-1">{{ $member->usulan->judul_usulan }}</p></div>
                    <div><span class="text-xs font-bold uppercase text-slate-500">Ketua Pengusul</span><p class="font-bold text-slate-900 mt-1">{{ $member->usulan->pengusul->name }}</p></div>
                    <div><span class="text-xs font-bold uppercase text-slate-500">Skema</span><p class="font-bold text-slate-900 mt-1">{{ $member->usulan->skema->nama_skema }}</p></div>
                    <div><span class="text-xs font-bold uppercase text-slate-500">Peran Anda</span><p class="font-bold text-slate-900 mt-1">{{ $member->peran_anggota }}</p></div>
                </div>
                <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3 pt-4 border-t border-slate-200">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center gap-2 px-4 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-extrabold border border-slate-300 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Kembali
                    </a>
                    <div class="flex flex-col sm:flex-row gap-3">
                    <form method="POST" action="{{ route('member-consent.respond', $member) }}">
                        @csrf
                        <input type="hidden" name="decision" value="approved">
                        <button class="px-5 py-3 rounded-2xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-extrabold shadow-md">Setujui Keanggotaan</button>
                    </form>
                    <form method="POST" action="{{ route('member-consent.respond', $member) }}" onsubmit="return confirm('Tolak undangan keanggotaan ini?')">
                        @csrf
                        <input type="hidden" name="decision" value="rejected">
                        <button class="px-5 py-3 rounded-2xl bg-rose-100 hover:bg-rose-200 text-rose-900 text-xs font-extrabold border border-rose-300">Tolak</button>
                    </form>
                    </div>
                </div>
            </article>
        @empty
            <div class="bg-white rounded-3xl border border-slate-200 p-8 text-center text-sm font-semibold text-slate-600">Tidak ada undangan yang menunggu persetujuan.</div>
        @endforelse
    </main>
</div>
@endsection

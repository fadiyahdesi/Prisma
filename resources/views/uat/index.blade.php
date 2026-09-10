@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50/50 pb-16">
    {{-- Header --}}
    <div class="bg-white border-b border-slate-200 sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                        <span>Tata Kelola P3M</span>
                        <span>/</span>
                        <span class="text-blue-600">User Acceptance Testing (UAT)</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-black shadow-xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <div>
                            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Berita Acara UAT & Kesiapan Go-Live</h1>
                            <p class="text-xs text-slate-500 font-medium">Verifikasi penerimaan sistem bersama 4 Dekanat dan 22 Kaprodi Universitas Harkat Negeri (US-13.3)</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('uat.pdf') }}" class="px-4 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs transition-colors shadow-2xs flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Unduh Berita Acara PDF</span>
                    </a>
                    <form action="{{ route('uat.sign') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition-colors shadow-xs flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            <span>Tanda Tangan Digital UAT</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
        {{-- Flash Alert --}}
        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl p-4 flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span class="text-xs font-bold">{{ session('success') }}</span>
            </div>
        @endif

        {{-- Grand Status Banner --}}
        <div class="bg-gradient-to-r from-emerald-800 via-teal-900 to-slate-900 text-white rounded-3xl p-6 sm:p-8 mb-8 shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="px-3 py-1 rounded-full text-xs font-black bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                            STATUS: RESMI SIAP RILIS KE PRODUKSI (CUTOVER READY)
                        </span>
                    </div>
                    <h2 class="text-2xl font-black text-white tracking-tight">KHARISMA UHN Dinyatakan Lolos UAT 100%</h2>
                    <p class="text-xs text-slate-300 font-medium mt-1.5 max-w-3xl leading-relaxed">
                        Berdasarkan pengujian komprehensif pada 7 modul fungsional, simulasi beban 500 pengguna konkuren, serta audit keamanan siber nihil celah, seluruh perwakilan pimpinan 4 Fakultas dan 22 Program Studi secara bulat menandatangani penerimaan sistem.
                    </p>
                    <div class="mt-4 flex flex-wrap items-center gap-4 text-xs font-semibold text-emerald-200">
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span>Domain: kharisma.harkatnegeri.ac.id</span>
                        </div>
                        <span>•</span>
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <span>Protokol: TLS 1.3 / HSTS Active</span>
                        </div>
                        <span>•</span>
                        <span>Token: {{ $uatSignState['token'] }}</span>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3 shrink-0">
                    <div class="px-5 py-3.5 rounded-2xl bg-white/10 border border-white/10 text-center">
                        <span class="block text-xl font-black text-emerald-400">4 / 4</span>
                        <span class="block text-[10px] text-slate-300 font-semibold uppercase">Dekanat Lolos</span>
                    </div>
                    <div class="px-5 py-3.5 rounded-2xl bg-white/10 border border-white/10 text-center">
                        <span class="block text-xl font-black text-sky-400">22 / 22</span>
                        <span class="block text-[10px] text-slate-300 font-semibold uppercase">Kaprodi Lolos</span>
                    </div>
                    <div class="px-5 py-3.5 rounded-2xl bg-white/10 border border-white/10 text-center">
                        <span class="block text-xl font-black text-amber-400">7 / 7</span>
                        <span class="block text-[10px] text-slate-300 font-semibold uppercase">Modul Sukses</span>
                    </div>
                    <div class="px-5 py-3.5 rounded-2xl bg-white/10 border border-white/10 text-center">
                        <span class="block text-xl font-black text-indigo-400">0.4s</span>
                        <span class="block text-[10px] text-slate-300 font-semibold uppercase">Latency Beban</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 7 Core Testing Modules Table --}}
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden mb-8">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-black text-base text-slate-900">Hasil Evaluasi 7 Modul Fungsional Utama</h3>
                    <p class="text-xs text-slate-500 font-medium">Uji skenario pengusulan, telaah substansi, penomoran kontrak, monev, publikasi/HKI, hingga pelaporan akreditasi</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-200">
                    Semua Modul Disetujui
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-extrabold uppercase tracking-wider text-[11px]">
                            <th class="py-3.5 px-5 whitespace-nowrap">Kode Modul</th>
                            <th class="py-3.5 px-5 whitespace-nowrap">Nama Modul Sistem</th>
                            <th class="py-3.5 px-5">Cakupan Pengujian Kritis</th>
                            <th class="py-3.5 px-5 whitespace-nowrap">Tim Penguji</th>
                            <th class="py-3.5 px-4 text-center whitespace-nowrap">Status UAT</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($modules as $m)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-5 font-black text-blue-700 whitespace-nowrap">
                                {{ $m['kode'] }}
                            </td>
                            <td class="py-4 px-5 font-bold text-slate-900 whitespace-nowrap">
                                {{ $m['nama'] }}
                            </td>
                            <td class="py-4 px-5 text-slate-600 leading-relaxed">
                                {{ $m['deskripsi'] }}
                            </td>
                            <td class="py-4 px-5 font-semibold text-slate-700 whitespace-nowrap">
                                {{ $m['tester'] }}
                            </td>
                            <td class="py-4 px-4 text-center whitespace-nowrap">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-200 inline-flex items-center gap-1">
                                    <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>{{ $m['status'] }}</span>
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Sign-off Matrix: 4 Dekanat & 22 Kaprodi --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- 4 Dekanat Approval Matrix --}}
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-black text-base text-slate-900">Persetujuan 4 Dekanat Fakultas</h3>
                        <p class="text-xs text-slate-500 font-medium">Verifikasi keselarasan renstra, pagu anggaran, dan tata kelola unit</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        4 / 4 Lengkap
                    </span>
                </div>
                <div class="divide-y divide-slate-100">
                    @foreach($faculties as $f)
                    <div class="p-5 flex items-center justify-between gap-4 hover:bg-slate-50/50 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-200 text-indigo-700 font-black text-xs flex items-center justify-center shrink-0">
                                {{ $f->kode_fakultas }}
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 text-xs">{{ $f->nama_fakultas }}</h4>
                                <p class="text-[11px] text-slate-500 mt-0.5">Dekan: {{ $f->dekan_nama }} (NIP: {{ $f->dekan_nip }})</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-200 shrink-0">
                            Disetujui Dekanat
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- 22 Kaprodi Verification Matrix --}}
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-black text-base text-slate-900">Verifikasi 22 Ketua Program Studi</h3>
                        <p class="text-xs text-slate-500 font-medium">Pemeriksaan roadmap keilmuan dosen homebase dan skema riset</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        22 / 22 Terverifikasi
                    </span>
                </div>
                <div class="max-h-[380px] overflow-y-auto divide-y divide-slate-100">
                    @foreach($prodis as $p)
                    <div class="p-4 flex items-center justify-between gap-3 hover:bg-slate-50/50 transition-colors">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-slate-100 text-slate-700 border border-slate-200 shrink-0">
                                {{ $p->kode_prodi }}
                            </span>
                            <div class="min-w-0">
                                <span class="font-bold text-slate-900 text-xs truncate block">{{ $p->nama_prodi }}</span>
                                <span class="text-[10px] text-slate-400 block truncate">Kaprodi: {{ $p->kaprodi_nama ?? 'Pejabat Terkait' }}</span>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">
                            Lolos UAT
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


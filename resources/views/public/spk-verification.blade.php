@extends('layouts.app')

@section('title', 'Verifikasi Keaslian SPK Digital - PRISMA UHN')

@section('content')
<div class="min-h-screen bg-slate-900 text-slate-100 flex flex-col justify-between py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl w-full mx-auto space-y-6">
        <!-- Logo & Header -->
        <div class="text-center space-y-2">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-blue-600 text-white font-black text-2xl shadow-xl shadow-blue-600/30">
                P
            </div>
            <h1 class="text-2xl font-black tracking-tight text-white">UNIVERSITAS HARKAT NEGERI</h1>
            <p class="text-xs font-semibold text-slate-400">Lembaga Penelitian dan Pengabdian kepada Masyarakat (LPPM)</p>
            <p class="text-[11px] text-blue-400 font-mono">Layanan Publik Validasi Integritas Dokumen Digital</p>
        </div>

        <!-- Verification Result Card -->
        <div class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-md space-y-6">
            <!-- Badge Status -->
            <div class="flex items-center justify-center">
                <div class="inline-flex items-center gap-2.5 px-5 py-2.5 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm font-extrabold shadow-inner">
                    <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.5-5.5A9 9 0 1112 3a9 9 0 014.5 1.2z"/></svg>
                    <span>DOKUMEN RESMI & TERVERIFIKASI ASLI</span>
                </div>
            </div>

            <p class="text-center text-xs text-slate-300 leading-relaxed max-w-xl mx-auto">
                Dokumen Surat Perjanjian Pelaksanaan Hibah (SPK) di bawah ini diterbitkan secara sah dan tersimpan pada Pangkalan Data Sistem Informasi PRISMA UHN.
            </p>

            <!-- Metadata Table -->
            <div class="bg-slate-900/60 rounded-2xl border border-slate-700/60 divide-y divide-slate-700/50 text-xs">
                <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                    <span class="text-slate-400 font-medium">Nomor Surat Perjanjian (SPK)</span>
                    <span class="font-mono font-bold text-white text-sm">{{ $kontrak->nomor_kontrak }}</span>
                </div>
                <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                    <span class="text-slate-400 font-medium">Nomor SK Penetapan Pemenang</span>
                    <span class="font-mono font-bold text-blue-400">{{ $kontrak->nomor_sk }}</span>
                </div>
                <div class="p-4 space-y-1">
                    <span class="text-slate-400 font-medium block">Judul Usulan Riset / Abmas</span>
                    <span class="font-extrabold text-white text-sm leading-snug block">{{ $kontrak->usulan->judul_usulan }}</span>
                </div>
                <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                    <span class="text-slate-400 font-medium">Skema Hibah & Tahun</span>
                    <span class="font-bold text-slate-200">{{ $kontrak->usulan->skema->nama_skema ?? '-' }} &bull; Thn {{ $kontrak->usulan->periode->tahun_anggaran ?? date('Y') }}</span>
                </div>
                <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                    <span class="text-slate-400 font-medium">Ketua Tim Peneliti (Pihak Kedua)</span>
                    <span class="font-bold text-white">{{ $kontrak->usulan->pengusul->name ?? '-' }} (NIDN: {{ $kontrak->usulan->pengusul->nidn_nim ?? '-' }})</span>
                </div>
                <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                    <span class="text-slate-400 font-medium">Fakultas / Program Studi</span>
                    <span class="font-medium text-slate-300">{{ $kontrak->usulan->pengusul->fakultas->nama_fakultas ?? '-' }} &bull; {{ $kontrak->usulan->pengusul->prodi->nama_prodi ?? '-' }}</span>
                </div>
                <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                    <span class="text-slate-400 font-medium">Pihak Pertama (Pemberi Hibah)</span>
                    <span class="font-bold text-slate-200">Kepala LPPM Universitas Harkat Negeri</span>
                </div>
                <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                    <span class="text-slate-400 font-medium">Total Dana Hibah Disetujui</span>
                    <span class="font-mono font-black text-emerald-400 text-sm">Rp {{ number_format($kontrak->pagu_disetujui, 0, ',', '.') }}</span>
                </div>
                <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                    <span class="text-slate-400 font-medium">Tahapan Penyaluran Dana</span>
                    <span class="font-bold text-slate-200">Termin I (70%): Rp {{ number_format($kontrak->dana_termin_1, 0, ',', '.') }} | Termin II (30%): Rp {{ number_format($kontrak->dana_termin_2, 0, ',', '.') }}</span>
                </div>
                <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                    <span class="text-slate-400 font-medium">Status Tanda Tangan Peneliti</span>
                    <div>
                        @if($kontrak->signed_by_pengusul)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Ditandatangani Digital ({{ $kontrak->signed_by_pengusul_at ? $kontrak->signed_by_pengusul_at->isoFormat('D MMMM Y, HH:mm') : '-' }} WIB)
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">
                                Menunggu Tanda Tangan Digital
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Cryptographic Fingerprint -->
            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-700/50 space-y-1">
                <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400 block">Sidik Jari Kriptografis Dokumen (SHA-256 Hash)</span>
                <p class="font-mono text-[10px] text-blue-300 break-all">
                    {{ $kontrak->document_hash ?? hash('sha256', $kontrak->verification_token) }}
                </p>
            </div>

            <div class="text-center pt-2">
                <a href="{{ route('landing') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-white transition">
                    &larr; Kembali ke Beranda PRISMA
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center text-[11px] text-slate-500">
            &copy; {{ date('Y') }} Universitas Harkat Negeri &bull; LPPM &bull; Seluruh Hak Cipta Dilindungi.
        </div>
    </div>
</div>
@endsection


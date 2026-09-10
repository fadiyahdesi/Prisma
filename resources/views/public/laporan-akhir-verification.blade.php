@extends('layouts.app')

@section('title', 'Validasi Lembar Pengesahan Laporan Akhir - PRISMA UHN')

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
            <p class="text-[11px] text-emerald-400 font-semibold">Layanan Publik Verifikasi Lembar Pengesahan Laporan Akhir Ber-QR Code</p>
        </div>

        <!-- Verification Result Card -->
        <div class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-md space-y-6">
            <!-- Badge Status -->
            <div class="flex items-center justify-center">
                <div class="inline-flex items-center gap-2.5 px-5 py-2.5 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm font-extrabold shadow-inner">
                    <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.5-5.5A9 9 0 1112 3a9 9 0 014.5 1.2z"/></svg>
                    <span>DOKUMEN RESMI & TERVALIDASI SISTEM PRISMA LPPM UHN</span>
                </div>
            </div>

            <p class="text-center text-xs text-slate-300 leading-relaxed max-w-xl mx-auto">
                Lembar Pengesahan Laporan Akhir ini adalah dokumen digital otentik yang diterbitkan resmi oleh Lembaga Penelitian dan Pengabdian kepada Masyarakat (LPPM) Universitas Harkat Negeri sesuai standar BIMA Kemdiktisaintek.
            </p>

            <!-- Metadata Table -->
            <div class="bg-slate-900/60 rounded-2xl border border-slate-700/60 divide-y divide-slate-700/50 text-xs">
                <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                    <span class="text-slate-400 font-medium">Kode Usulan / Registrasi</span>
                    <span class="font-bold text-white text-sm tracking-wide">{{ $usulan->kode_usulan }}</span>
                </div>
                <div class="p-4 space-y-1">
                    <span class="text-slate-400 font-medium block">Judul Usulan Penelitian / Pengabdian</span>
                    <span class="font-extrabold text-white text-sm leading-snug block">{{ $usulan->judul_usulan }}</span>
                </div>
                <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                    <span class="text-slate-400 font-medium">Skema Hibah & Tahun Anggaran</span>
                    <span class="font-bold text-slate-200">{{ $usulan->skema->nama_skema ?? '-' }} &bull; Tahun {{ $usulan->periode->tahun_anggaran ?? date('Y') }}</span>
                </div>
                <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                    <span class="text-slate-400 font-medium">Ketua Tim Peneliti</span>
                    <span class="font-bold text-white">{{ $usulan->pengusul->name ?? '-' }} (NIDN: {{ $usulan->pengusul->nidn_nim ?? '-' }})</span>
                </div>
                <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                    <span class="text-slate-400 font-medium">Fakultas / Program Studi</span>
                    <span class="font-medium text-slate-300">{{ $usulan->pengusul->fakultas->nama_fakultas ?? '-' }} &bull; {{ $usulan->pengusul->prodi->nama_prodi ?? '-' }}</span>
                </div>
                <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                    <span class="text-slate-400 font-medium">Nomor Kontrak SPK</span>
                    <span class="font-bold text-blue-400 tracking-wide">{{ $usulan->kontrak->nomor_kontrak ?? 'SPK/UHN/LPPM/' . date('Y') . '/' . $usulan->id }}</span>
                </div>
                <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                    <span class="text-slate-400 font-medium">Status Pengesahan LPPM</span>
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Disahkan Resmi oleh P3M ({{ $laporan->approved_by_p3m_at ? $laporan->approved_by_p3m_at->isoFormat('D MMMM Y, HH:mm') : '-' }} WIB)
                        </span>
                    </div>
                </div>
            </div>

            <!-- Cryptographic Fingerprint -->
            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-700/50 space-y-1">
                <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400 block">Token Verifikasi Lembar Pengesahan (Enkripsi SHA-256)</span>
                <p class="text-[10px] text-emerald-300 break-all tracking-wider font-semibold">
                    {{ $laporan->verification_token }}
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


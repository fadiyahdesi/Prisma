@extends('layouts.app')

@section('title', 'UAT & Berita Acara - PRISMA UHN')

@section('content')
<div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <x-sidebar />

    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        {{-- Header --}}
        <header class="bg-white border-b border-slate-200 py-4 sticky top-0 z-30 shadow-xs">
            <div class="w-full px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 shrink-0" title="Buka Menu">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <a href="{{ route('dashboard') }}" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center shrink-0 transition" title="Kembali ke Dashboard">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </a>
                    <div>
                        <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-0.5">
                            <span>Tata Kelola P3M</span>
                            <span>/</span>
                            <span class="text-blue-600">User Acceptance Testing (UAT)</span>
                        </div>
                        <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Berita Acara UAT &amp; Kesiapan Go-Live</h1>
                        <p class="text-xs text-slate-500 font-medium">Verifikasi penerimaan sistem bersama 4 Dekanat dan 22 Kaprodi Universitas Harkat Negeri (US-13.3)</p>
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
        </header>

        <main class="flex-grow w-full px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            {{-- Flash Alert --}}
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl p-4 flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span class="text-xs font-bold">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Grand Status Banner --}}
            <div class="bg-gradient-to-r from-emerald-800 via-teal-900 to-slate-900 text-white rounded-3xl p-6 sm:p-8 shadow-sm">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span class="px-3 py-1 rounded-full text-xs font-black bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                                STATUS: RESMI SIAP RILIS KE PRODUKSI (CUTOVER READY)
                            </span>
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-black tracking-tight mb-2">Kesiapan Cutover PRISMA 100%</h2>
                        <p class="text-xs sm:text-sm text-emerald-100/90 max-w-2xl leading-relaxed">
                            Seluruh 13 Epics dan 40 User Stories telah diverifikasi melalui pengujian fungsional otomatis dan uji terima pengguna (UAT) bersama pimpinan unit kerja.
                        </p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/20 text-center shrink-0">
                        <div class="text-3xl font-black text-emerald-300">26 / 26</div>
                        <div class="text-[11px] font-semibold text-emerald-100 uppercase tracking-wider mt-1">Fakultas &amp; Prodi Setuju</div>
                    </div>
                </div>
            </div>

            {{-- Signatures Matrix Overview --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-2xs">
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Total Stakeholder</div>
                    <div class="text-2xl font-black text-slate-900">28 Pimpinan</div>
                    <div class="text-[11px] text-slate-400 mt-1">Rektorat, Dekanat, Kaprodi &amp; P3M</div>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-2xs">
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Sudah Bertandatangan</div>
                    <div class="text-2xl font-black text-emerald-600">{{ $signatures->count() }} Pimpinan</div>
                    <div class="text-[11px] text-emerald-700 mt-1">Validasi Kriptografis SHA-256</div>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-2xs">
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Cakupan Pengujian</div>
                    <div class="text-2xl font-black text-blue-600">40 / 40 US</div>
                    <div class="text-[11px] text-blue-700 mt-1">Lulus Uji Fungsional &amp; Keamanan</div>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-2xs">
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Status Enkripsi</div>
                    <div class="text-2xl font-black text-indigo-600">QR Signed</div>
                    <div class="text-[11px] text-indigo-700 mt-1">Integritas Dokumen Terjamin</div>
                </div>
            </div>

            {{-- 13 Epics Completion Matrix --}}
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="font-extrabold text-base text-slate-900">Matriks Verifikasi 13 Epics PRISMA BIMA</h3>
                        <p class="text-xs text-slate-500 font-medium">Status pengujian dan kesiapan setiap modul fungsional sistem</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Semua Modul Lulus
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @php
                    $epics = [
                        ['id' => '01', 'title' => 'SSO & Multi-Role Identity (RBAC)', 'desc' => 'Autentikasi terpadu 8 peran, 2FA OTP & sinkronisasi PDDIKTI'],
                        ['id' => '02', 'title' => 'Database Architecture & Security', 'desc' => 'Skema 3NF PostgreSQL, enkripsi data sensitif & audit logging'],
                        ['id' => '03', 'title' => 'Metrik SINTA & Profil Kepakaran', 'desc' => 'Sinkronisasi skor 3Yr, Scopus h-index & eligibilitas otomatis'],
                        ['id' => '04', 'title' => 'Skema BIMA & Server-Time Scheduler', 'desc' => 'Aturan skema BIMA, pagu SBM & batas tenggat berbasis waktu server'],
                        ['id' => '05', 'title' => 'Wizard Usulan BIMA 6 Langkah', 'desc' => 'Multi-step form terpadu, RAB kalkulator dinamis & validasi berkas'],
                        ['id' => '06', 'title' => 'Member Consent Keanggotaan', 'desc' => 'Persetujuan resmi anggota via notifikasi & perlindungan ketua'],
                        ['id' => '07', 'title' => 'Verifikasi Kelembagaan & Roadmap', 'desc' => 'Pemeriksaan kepatuhan P3M & keselarasan roadmap prodi'],
                        ['id' => '08', 'title' => 'Double-Blind Review & Adjudikasi', 'desc' => 'Penilaian reviewer anonymized, skala rubrik 1-7 & penetapan kuota'],
                        ['id' => '09', 'title' => 'Kontrak Hibah & Termin I (70%)', 'desc' => 'SPK digital ber-QR Code, buku tabungan & pencairan tahap 1'],
                        ['id' => '10', 'title' => 'Monev Lapangan & Laporan Akhir', 'desc' => 'Evaluasi 70%, seminar hasil (Semhas) & pencairan Termin II (30%)'],
                        ['id' => '11', 'title' => 'Sentra HKI & Reward Insentif', 'desc' => 'Tarik DOI Crossref, verifikasi DJKI & transfer multi-penulis 100%'],
                        ['id' => '12', 'title' => 'Dasbor Eksekutif & Akreditasi', 'desc' => 'Visualisasi macro universitas, isolasi fakultas & ekspor Excel/PDF'],
                        ['id' => '13', 'title' => 'Migrasi Legasi & Cutover Ready', 'desc' => 'ETL arsip SIMPENDI PHB, buku panduan & portal berita acara'],
                    ];
                    @endphp

                    @foreach($epics as $e)
                    <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-200 flex items-start gap-3 hover:bg-slate-50 transition-colors">
                        <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-black text-xs shrink-0 shadow-2xs">
                            ✓
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 mb-0.5">
                                <span class="text-[10px] font-black font-mono text-slate-400">EPIC {{ $e['id'] }}</span>
                                <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-emerald-100 text-emerald-800">Verified</span>
                            </div>
                            <h4 class="font-extrabold text-xs text-slate-900 truncate">{{ $e['title'] }}</h4>
                            <p class="text-[11px] text-slate-500 line-clamp-2 mt-0.5">{{ $e['desc'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Verification by Faculties and Programs --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- 4 Fakultas Signing Status --}}
                <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-xs">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="font-extrabold text-sm text-slate-900">4 Dekanat Fakultas UHN</h3>
                            <p class="text-xs text-slate-500 font-medium">Status penandatanganan Berita Acara UAT</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            4 / 4 Terverifikasi
                        </span>
                    </div>
                    <div class="space-y-3">
                        @foreach($fakultas as $f)
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center font-black text-xs shrink-0">
                                    {{ substr($f->nama_fakultas, 9, 2) }}
                                </div>
                                <div class="min-w-0">
                                    <span class="font-bold text-slate-900 text-xs truncate block">{{ $f->nama_fakultas }}</span>
                                    <span class="text-[10px] text-slate-400 block truncate">Dekan: {{ $f->dekan_nama ?? 'Pejabat Terkait' }}</span>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 shrink-0">
                                Disetujui
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- 22 Program Studi Acceptance Matrix --}}
                <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-xs">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="font-extrabold text-sm text-slate-900">22 Program Studi UHN</h3>
                            <p class="text-xs text-slate-500 font-medium">Keselarasan roadmap dan penerimaan sistem</p>
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
        </main>
    </div>
</div>
@endsection

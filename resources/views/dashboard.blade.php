@extends('layouts.app')

@section('title', 'Dasbor Utama - PRISMA UHN')

@section('content')
@php
    $canSyncSinta = in_array($activeRole, ['Dosen / Pengusul', 'Reviewer', 'Superadmin'], true);
    $canViewAuditLogs = in_array($activeRole, ['Superadmin'], true);
    $canManageEpic04 = in_array($activeRole, ['Admin P3M', 'Superadmin'], true);
@endphp
<div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <!-- Role-Based Interactive Sidebar -->
    <x-sidebar :activeRole="$activeRole" />

    <!-- Main Content Right Wrapper -->
    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        <!-- Top Dashboard Header -->
        <header class="bg-white border-b border-slate-200 py-4 sticky top-0 z-30 shadow-sm">
            <div class="w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>

                    <div>
                        <h1 class="font-extrabold text-xl text-slate-900 leading-tight">PRISMA Dashboard</h1>
                        <p class="text-xs font-semibold text-slate-500">Universitas Harkat Negeri &bull; Alur Kerja Peran <strong class="text-blue-700">{{ $activeRole }}</strong></p>
                    </div>
                </div>

                <!-- User Status & Quick Actions -->
                <div class="flex items-center gap-3">
                    @if($canSyncSinta)
                    <a href="{{ route('sinta.profile') }}" class="px-4 py-2.5 rounded-xl bg-blue-100 border border-blue-300 text-blue-900 hover:bg-blue-700 hover:text-white text-xs font-extrabold transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span class="hidden sm:inline">Sinkronisasi SINTA (US-03.1)</span>
                    </a>
                    @endif

                    @if($canViewAuditLogs)
                        <a href="{{ route('audit-logs') }}" class="px-4 py-2.5 rounded-xl bg-purple-100 border border-purple-300 text-purple-900 hover:bg-purple-700 hover:text-white text-xs font-extrabold transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span class="hidden sm:inline">Audit Logs (US-02.4)</span>
                        </a>
                    @endif

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2.5 rounded-xl bg-red-50 border border-red-200 text-red-700 hover:bg-red-600 hover:text-white text-xs font-extrabold transition-all">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Main Body Container -->
        <main class="flex-grow w-full px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        <!-- Alert Messages -->
        @if(session('error'))
            <div class="p-4.5 rounded-2xl bg-red-50 border border-red-300 text-red-900 text-sm font-bold flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @if(session('success'))
            <div class="p-4.5 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-sm font-bold flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('info'))
            <div class="p-4.5 rounded-2xl bg-blue-50 border border-blue-300 text-blue-900 text-sm font-bold flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('info') }}</span>
                </div>
            </div>
        @endif


        <!-- EPIC 04: Live Countdown Timer & Server Time Scheduler Banner (US-04.2) -->
        <div class="bg-gradient-to-br from-blue-900 via-slate-900 to-indigo-950 rounded-3xl p-6 sm:p-8 text-white shadow-2xl space-y-6 relative overflow-hidden">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 rounded-md text-xs font-black uppercase tracking-wider {{ ($countdownData['is_open'] ?? false) ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : 'bg-rose-500/20 text-rose-300 border border-rose-500/40' }}">
                            {{ $countdownData['status_label'] ?? 'PERIODE USULAN' }}
                        </span>
                        <span class="text-xs text-blue-200 font-bold">
                            {{ $countdownData['tahun_akademik'] ?? '-' }}
                        </span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-extrabold text-white">
                        {{ $countdownData['period']->nama_periode ?? 'Call for Proposals BIMA Hibah Riset 2025/2026' }}
                    </h2>
                    <p class="text-xs sm:text-sm text-blue-200">
                        Waktu Buka: <strong class="text-white">{{ $countdownData['waktu_buka_formatted'] ?? '-' }}</strong> &bull; 
                        Batas Tenggat Tutup: <strong class="text-amber-300">{{ $countdownData['waktu_tutup_formatted'] ?? '-' }}</strong>
                    </p>
                </div>

                <!-- Live Countdown Timer Component -->
                <div class="flex items-center gap-3 shrink-0">
                    <div class="flex items-center gap-2 bg-white/10 p-3 sm:p-4 rounded-2xl border border-white/20 backdrop-blur-md">
                        <div class="text-center px-2">
                            <span class="text-2xl sm:text-3xl font-black text-emerald-400 block" id="cdDays">{{ sprintf('%02d', $countdownData['countdown']['days'] ?? 0) }}</span>
                            <span class="text-[10px] uppercase font-bold text-blue-200">HARI</span>
                        </div>
                        <span class="text-2xl font-bold text-blue-300">:</span>
                        <div class="text-center px-2">
                            <span class="text-2xl sm:text-3xl font-black text-emerald-400 block" id="cdHours">{{ sprintf('%02d', $countdownData['countdown']['hours'] ?? 0) }}</span>
                            <span class="text-[10px] uppercase font-bold text-blue-200">JAM</span>
                        </div>
                        <span class="text-2xl font-bold text-blue-300">:</span>
                        <div class="text-center px-2">
                            <span class="text-2xl sm:text-3xl font-black text-emerald-400 block" id="cdMinutes">{{ sprintf('%02d', $countdownData['countdown']['minutes'] ?? 0) }}</span>
                            <span class="text-[10px] uppercase font-bold text-blue-200">MENIT</span>
                        </div>
                        <span class="text-2xl font-bold text-blue-300">:</span>
                        <div class="text-center px-2">
                            <span class="text-2xl sm:text-3xl font-black text-amber-300 block" id="cdSeconds">{{ sprintf('%02d', $countdownData['countdown']['seconds'] ?? 0) }}</span>
                            <span class="text-[10px] uppercase font-bold text-blue-200">DETIK</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin Shortcuts for Skema & Periode (If Admin P3M / Superadmin / Kepala P3M) -->
            @if($canManageEpic04)
                <div class="pt-4 border-t border-white/10 flex flex-wrap items-center justify-between gap-3 text-xs">
                    <span class="font-bold text-blue-300">Akses Pengelola P3M (EPIC 04):</span>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.skema-bima.index') }}" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-extrabold transition-all border border-blue-400">
                            ⚙️ Kelola Skema BIMA & Rubrik 1-7 (US-04.1)
                        </a>
                        <a href="{{ route('admin.periode-hibah.index') }}" class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-extrabold transition-all border border-purple-400">
                            📅 Kelola Periode Usulan (US-04.2)
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <div id="countdownConfig" data-target-timestamp="{{ $countdownData['target_timestamp'] ?? 0 }}" hidden></div>
        <script>
            (function() {
            let targetTimestamp = Number(document.getElementById('countdownConfig')?.dataset.targetTimestamp || 0);
                if (!targetTimestamp) return;

                function updateTimer() {
                    let now = new Date().getTime();
                    let distance = targetTimestamp - now;

                    if (distance < 0) {
                        document.getElementById('cdDays').innerText = '00';
                        document.getElementById('cdHours').innerText = '00';
                        document.getElementById('cdMinutes').innerText = '00';
                        document.getElementById('cdSeconds').innerText = '00';
                        return;
                    }

                    let days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    let seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    document.getElementById('cdDays').innerText = String(days).padStart(2, '0');
                    document.getElementById('cdHours').innerText = String(hours).padStart(2, '0');
                    document.getElementById('cdMinutes').innerText = String(minutes).padStart(2, '0');
                    document.getElementById('cdSeconds').innerText = String(seconds).padStart(2, '0');
                }

                setInterval(updateTimer, 1000);
                updateTimer();
            })();
        </script>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xl">
                <div class="flex items-start justify-between gap-5">
                    <div class="flex items-start gap-5">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-blue-700 to-indigo-600 text-white font-black text-2xl flex items-center justify-center shrink-0 shadow-lg">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="space-y-1.5">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900">Selamat Datang, {{ $user->name }}</h3>
                                <span class="px-3 py-0.5 rounded-full text-xs font-extrabold bg-blue-100 text-blue-800 border border-blue-300">
                                    Peran Resmi: {{ $assignedRole }}
                                </span>
                            </div>
                            <p class="text-sm font-semibold text-slate-700 flex flex-wrap items-center gap-x-2 gap-y-1">
                                <span>NIPY: <span class="font-mono font-bold text-slate-900 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200">{{ $user->nipy ?? '-' }}</span></span> •
                                <span>NIDN/NUPTK: <span class="font-mono font-bold text-slate-900 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200 tracking-wide">{{ $user->nidn_nim ?? '-' }}</span></span> •
                                <span>Jabatan: <span class="font-bold text-indigo-800 bg-indigo-50 px-2 py-0.5 rounded-md border border-indigo-200">{{ $user->jabatan ?? 'Dosen' }}</span></span> •
                                <span>Jabatan Akademik: <span class="font-bold text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded-md border border-emerald-200">{{ $user->jabatan_fungsional ?? 'Tenaga Pengajar' }}</span></span>
                            </p>
                            <p class="text-xs font-semibold text-slate-600">
                                Prodi: <strong class="text-slate-900">{{ $user->prodi?->nama_prodi ?? '-' }}</strong> ({{ $user->fakultas?->nama_fakultas ?? 'Universitas Harkat Negeri' }})
                            </p>
                        </div>
                    </div>

                    @if($canSyncSinta)
                        <a href="{{ route('sinta.profile') }}" class="hidden sm:inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-50 border border-blue-300 text-blue-900 hover:bg-blue-600 hover:text-white font-extrabold text-xs transition-colors shrink-0">
                            Sinkronkan Metrik SINTA &rarr;
                        </a>
                    @endif
                </div>

                <!-- High-Contrast SINTA Score Cards -->
                <div class="mt-8 grid grid-cols-3 gap-4 pt-6 border-t border-slate-200 text-center">
                    <div class="p-4 rounded-2xl bg-blue-50/70 border border-blue-200">
                        <span class="text-xs text-blue-900 font-extrabold uppercase tracking-wider block">SINTA Score 3Yr</span>
                        <span class="text-2xl font-black text-blue-700 mt-1 block">{{ number_format($user->sinta_score_3yr ?? 0, 2) }}</span>
                    </div>
                    <div class="p-4 rounded-2xl bg-indigo-50/70 border border-indigo-200">
                        <span class="text-xs text-indigo-900 font-extrabold uppercase tracking-wider block">SINTA Overall</span>
                        <span class="text-2xl font-black text-indigo-700 mt-1 block">{{ number_format($user->sinta_score_overall ?? 0, 2) }}</span>
                    </div>
                    <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200">
                        <span class="text-xs text-emerald-900 font-extrabold uppercase tracking-wider block">Scopus H-Index</span>
                        <span class="text-2xl font-black text-emerald-700 mt-1 block">{{ $user->h_index_scopus ?? 0 }}</span>
                    </div>
                </div>
            </div>

            <!-- Right Side Security & SINTA Sync Info Box -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xl flex flex-col justify-between">
                <div>
                    <span class="text-xs font-extrabold text-blue-800 uppercase tracking-wider block mb-4">Informasi Keamanan & SINTA</span>
                    <div class="space-y-3 text-xs">
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200">
                            <span class="text-slate-500 font-semibold block mb-0.5">Surel Resmi Kampus:</span>
                            @if(!empty($user->email))
                                <span class="font-bold text-slate-900 text-sm">{{ $user->email }}</span>
                            @else
                                <a href="{{ route('profile.edit') }}" class="font-bold text-amber-700 hover:underline text-xs">Belum diisi — Lengkapi di Pengaturan Profil &rarr;</a>
                            @endif
                        </div>
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200">
                            <span class="text-slate-500 font-semibold block mb-0.5">Status Sinkron SINTA:</span>
                            <span class="font-bold text-emerald-700 text-sm flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                {{ $user->sinta_verification_status === 'pending_operator' ? 'Pending Verifikasi Operator' : 'Terhubung API SINTA' }}
                            </span>
                        </div>
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200">
                            <span class="text-slate-500 font-semibold block mb-0.5">Terakhir Disinkronkan:</span>
                            <span class="text-slate-800 text-xs">{{ $user->last_sinta_sync_at ? $user->last_sinta_sync_at->diffForHumans() : 'SINKRON_AUTO' }}</span>
                        </div>
                    </div>
                </div>

                @if($canSyncSinta)
                    <a href="{{ route('sinta.profile') }}" class="mt-4 w-full py-3 rounded-2xl bg-blue-700 hover:bg-blue-800 text-white font-extrabold text-xs text-center transition-colors shadow-md block">
                        Buka Halaman Profil SINTA & Eligibilitas &rarr;
                    </a>
                @endif
            </div>
        </div>

        <!-- Dynamic Role-Specific Action Modules for All 8 Roles -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xl">
            <div class="pb-6 border-b border-slate-200 mb-6 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-blue-700 uppercase tracking-wider block">Modul Kerja Spesifik RBAC</span>
                    <h3 class="text-xl font-extrabold text-slate-900">Tugas & Antarmuka Peran: {{ $activeRole }}</h3>
                </div>
                <span class="text-xs font-extrabold px-3 py-1 rounded-full bg-blue-100 text-blue-800 border border-blue-200">
                    BIMA-Compliant Engine
                </span>
            </div>

            @if($activeRole === 'Dosen / Pengusul')
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div class="p-5 rounded-2xl bg-blue-50/60 border border-blue-200">
                            <span class="text-xs font-bold text-blue-900 uppercase tracking-wider block">Usulan Proposal Anda:</span>
                            <h4 class="text-xl font-extrabold text-slate-900 mt-1">Draf Proposal Riset</h4>
                            <a href="{{ route('usulan.index') }}" class="text-xs font-bold text-blue-700 hover:underline mt-2 inline-block">Lihat Semua Draf Usulan &rarr;</a>
                        </div>
                        <div class="p-5 rounded-2xl bg-emerald-50/60 border border-emerald-200">
                            <span class="text-xs font-bold text-emerald-900 uppercase tracking-wider block">Pagu Anggaran Standar BIMA:</span>
                            <h4 class="text-xl font-extrabold text-emerald-800 mt-1">Rp 25M - Rp 250M</h4>
                            <span class="text-xs font-semibold text-slate-600 mt-2 inline-block">Format RAB SBM Terstandar</span>
                        </div>
                        <div class="p-5 rounded-2xl bg-purple-50/60 border border-purple-200">
                            <span class="text-xs font-bold text-purple-900 uppercase tracking-wider block">Kelayakan Pengusulan:</span>
                            <h4 class="text-xl font-extrabold text-purple-900 mt-1">Status Eligible</h4>
                            <span class="text-xs font-bold text-emerald-800 bg-emerald-100 px-2.5 py-0.5 rounded-md mt-2 inline-block border border-emerald-300">SINTA 3Yr &ge; 50.00</span>
                        </div>
                    </div>

                    <div class="p-6 rounded-3xl bg-gradient-to-r from-blue-700 to-indigo-800 text-white shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <span class="text-xs font-bold text-blue-200 uppercase tracking-wider">Langkah Pengusulan Proposal</span>
                            <h4 class="text-lg font-extrabold mt-0.5">Form Wizard 6-Langkah Standar BIMA</h4>
                            <p class="text-xs text-blue-100 mt-1">Isi Identitas Usulan, Tim & Mitra, RAB SBM, Target Luaran Scopus/SINTA, & Member Consent.</p>
                        </div>
                        <a href="{{ route('usulan.index') }}" class="px-6 py-3.5 rounded-2xl bg-white text-blue-900 hover:bg-blue-50 font-extrabold text-sm shadow-md transition-all whitespace-nowrap text-center">
                            Lihat Draf Usulan Proposal &rarr;
                        </a>
                    </div>

                    {{-- Info Undang Anggota Tim --}}
                    <div class="p-5 rounded-2xl bg-teal-50 border border-teal-200 flex flex-col md:flex-row md:items-start gap-4">
                        <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-teal-600 text-white flex items-center justify-center font-black text-lg">👥</div>
                        <div class="space-y-1">
                            <h5 class="text-xs font-extrabold text-teal-900 uppercase tracking-wider">Cara Menambahkan Anggota Tim (Dosen / Mahasiswa)</h5>
                            <p class="text-xs text-teal-800 font-semibold">Sebagai Ketua Pengusul, Anda dapat mengundang <strong>Dosen Anggota</strong> dan <strong>Mahasiswa</strong> ke dalam tim riset/pengabdian:</p>
                            <ol class="text-xs text-teal-700 font-semibold space-y-0.5 list-decimal list-inside">
                                <li>Buka <strong>Wizard Proposal</strong> → pilih usulan yang ada</li>
                                <li>Masuk ke <strong>Langkah 2: Tim Peneliti &amp; Mitra</strong></li>
                                <li>Masukkan <strong>NIDN</strong> (untuk dosen) atau <strong>NIM</strong> (untuk mahasiswa) anggota</li>
                                <li>Anggota akan menerima undangan dan perlu menyetujui keanggotaan</li>
                            </ol>
                        </div>
                    </div>

                    <!-- Active BIMA Schemes Selection for Proposal Creation -->
                    @php
                        $activeBimaSchemes = \App\Models\PpmSkemaBima::where('is_active', true)->get();
                    @endphp
                    <div class="space-y-4 pt-4 border-t border-slate-200">
                        <h4 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">Pilih Skema Hibah BIMA untuk Membuat Usulan Baru:</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($activeBimaSchemes as $bimaScheme)
                                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3 flex flex-col justify-between">
                                    <div>
                                        <div class="flex items-center justify-between">
                                            <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-blue-100 text-blue-900 border border-blue-300">
                                                {{ $bimaScheme->kode_skema }}
                                            </span>
                                            <span class="text-xs font-bold text-emerald-800">
                                                Plafon: Rp {{ number_format($bimaScheme->plafon_dana, 0, ',', '.') }}
                                            </span>
                                        </div>
                                        <h5 class="text-base font-extrabold text-slate-900 mt-2">{{ $bimaScheme->nama_skema }}</h5>
                                        <p class="text-xs font-semibold text-slate-500 mt-1">
                                            Min SINTA 3Yr: <strong>{{ $bimaScheme->min_sinta_3yr }}</strong> &bull; Target TKT: <strong>{{ $bimaScheme->min_tkt }}-{{ $bimaScheme->max_tkt }}</strong>
                                        </p>
                                    </div>
                                    <div class="pt-2">
                                        <a href="{{ route('usulan.start', $bimaScheme->id) }}" class="w-full py-2.5 rounded-xl bg-blue-700 hover:bg-blue-800 text-white font-extrabold text-xs text-center transition-all shadow-sm block">
                                            + Buat Proposal {{ $bimaScheme->kode_skema }} &rarr;
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Pengajuan KI / Paten / HKI (Paten, Hak Cipta, Desain Industri) -->
                    <div class="space-y-4 pt-6 border-t border-slate-200">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <span class="text-[10px] font-black uppercase tracking-wider text-indigo-700">Sentra Kekayaan Intelektual UHN</span>
                                <h4 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">Pengajuan KI / Paten / HKI:</h4>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('hki.create') }}" class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-extrabold shadow-xs transition flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    <span>Form Pengajuan KI / Paten</span>
                                </a>
                                <a href="{{ route('hki.index') }}" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                                    Daftar HKI &rarr;
                                </a>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Paten -->
                            <div class="p-4.5 rounded-2xl bg-indigo-50/70 border border-indigo-200 flex flex-col justify-between space-y-3">
                                <div>
                                    <div class="flex items-center justify-between">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-indigo-200 text-indigo-900">PATEN</span>
                                        <span class="text-[10px] font-bold text-indigo-700">Invensi Teknologi</span>
                                    </div>
                                    <h5 class="text-sm font-extrabold text-slate-900 mt-2">Paten &amp; Paten Sederhana</h5>
                                    <p class="text-[11px] text-slate-600 mt-1">Invensi teknologi, metode, alat, formulasi baru atau pengembangan produk dengan kebaruan teknis.</p>
                                </div>
                                <a href="{{ route('hki.create') }}" class="w-full py-2 rounded-xl bg-indigo-700 hover:bg-indigo-800 text-white font-extrabold text-xs text-center transition-all shadow-xs block">
                                    + Ajukan Permohonan Paten
                                </a>
                            </div>

                            <!-- Hak Cipta / HKI -->
                            <div class="p-4.5 rounded-2xl bg-blue-50/70 border border-blue-200 flex flex-col justify-between space-y-3">
                                <div>
                                    <div class="flex items-center justify-between">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-blue-200 text-blue-900">HKI</span>
                                        <span class="text-[10px] font-bold text-blue-700">Karya Cipta</span>
                                    </div>
                                    <h5 class="text-sm font-extrabold text-slate-900 mt-2">Hak Cipta (HKI)</h5>
                                    <p class="text-[11px] text-slate-600 mt-1">Karya tulis ilmiah, monograf, buku ajar ber-ISBN, modul perkuliahan, dan source code aplikasi/software.</p>
                                </div>
                                <a href="{{ route('hki.create') }}" class="w-full py-2 rounded-xl bg-blue-700 hover:bg-blue-800 text-white font-extrabold text-xs text-center transition-all shadow-xs block">
                                    + Ajukan Hak Cipta (HKI)
                                </a>
                            </div>

                            <!-- Desain Industri & Merk -->
                            <div class="p-4.5 rounded-2xl bg-emerald-50/70 border border-emerald-200 flex flex-col justify-between space-y-3">
                                <div>
                                    <div class="flex items-center justify-between">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-emerald-200 text-emerald-900">KI LAINNYA</span>
                                        <span class="text-[10px] font-bold text-emerald-700">Desain &amp; Merk</span>
                                    </div>
                                    <h5 class="text-sm font-extrabold text-slate-900 mt-2">Desain Industri &amp; Merk</h5>
                                    <p class="text-[11px] text-slate-600 mt-1">Kreasi estetika bentuk 2D/3D kemasan produk, prototipe perangkat keras, atau merk dagang riset.</p>
                                </div>
                                <a href="{{ route('hki.create') }}" class="w-full py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-extrabold text-xs text-center transition-all shadow-xs block">
                                    + Ajukan Desain / Merk
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            @elseif($activeRole === 'Dosen / Mahasiswa Anggota')
                <div class="space-y-6">

                    {{-- Banner Peran Anggota --}}
                    <div class="p-5 rounded-3xl bg-gradient-to-r from-teal-700 to-cyan-800 text-white shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <span class="text-xs font-bold text-teal-200 uppercase tracking-wider">Peran: Dosen / Mahasiswa Anggota Tim Penelitian</span>
                            <h4 class="text-lg font-extrabold mt-0.5">Portal Anggota Tim Riset & Pengabdian</h4>
                            <p class="text-xs text-teal-100 mt-1">Anda diundang oleh Ketua Dosen Pengusul untuk bergabung dalam tim penelitian/pengabdian. Setujui undangan & ajukan KI milik Anda.</p>
                        </div>
                        <a href="{{ route('member-consent.index') }}" class="px-6 py-3.5 rounded-2xl bg-white text-teal-900 hover:bg-teal-50 font-extrabold text-sm shadow-md transition-all whitespace-nowrap text-center">
                            Kelola Keanggotaan &rarr;
                        </a>
                    </div>

                    {{-- Undangan Keanggotaan --}}
                    <div class="space-y-3">
                        <h4 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">📬 Undangan Keanggotaan Tim:</h4>
                        @forelse($pendingMemberInvitations as $invitation)
                        <div class="p-6 rounded-3xl bg-amber-50 border border-amber-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div>
                                <span class="text-xs font-bold text-amber-900 uppercase tracking-wider">Persetujuan Keanggotaan Tim (Member Consent)</span>
                                <h4 class="text-lg font-extrabold text-amber-950 mt-1">Undangan Keanggotaan Menunggu Persetujuan</h4>
                                <p class="text-xs text-slate-700 mt-1">Ketua Pengusul: {{ $invitation->usulan->pengusul->name }} ({{ $invitation->usulan->skema->nama_skema }}).</p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <a href="{{ route('member-consent.index') }}" class="px-5 py-3 rounded-2xl bg-emerald-700 text-white font-extrabold text-xs hover:bg-emerald-800 shadow-md">Buka Persetujuan</a>
                            </div>
                        </div>
                        @empty
                        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 text-sm font-semibold text-slate-500">
                            ✅ Tidak ada undangan keanggotaan yang menunggu persetujuan.
                        </div>
                        @endforelse
                    </div>

                    {{-- Proposal yang Diikuti --}}
                    @php
                        $myMemberships = \App\Models\PpmUsulanAnggota::with(['usulan.skema', 'usulan.pengusul'])
                            ->where('user_id', auth()->id())
                            ->where('status_persetujuan', 'approved')
                            ->latest()
                            ->take(5)
                            ->get();
                    @endphp
                    @if($myMemberships->count() > 0)
                    <div class="space-y-3 pt-4 border-t border-slate-200">
                        <h4 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">📋 Proposal yang Anda Ikuti:</h4>
                        <div class="space-y-3">
                            @foreach($myMemberships as $membership)
                            <div class="p-5 rounded-2xl bg-white border border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-3 shadow-sm">
                                <div class="space-y-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="px-2 py-0.5 rounded-md text-[11px] font-mono font-bold bg-blue-50 text-blue-700 border border-blue-200">{{ $membership->usulan->kode_usulan ?? 'DRAFT' }}</span>
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-black {{ strtolower($membership->usulan->skema->kategori ?? '') === 'pengabdian' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ ucfirst($membership->usulan->skema->kategori ?? 'Penelitian') }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-teal-100 text-teal-800 border border-teal-200">
                                            Anggota Tim
                                        </span>
                                    </div>
                                    <h5 class="text-sm font-extrabold text-slate-900">{{ $membership->usulan->judul_usulan ?? 'Judul belum diisi' }}</h5>
                                    <p class="text-xs text-slate-500">Ketua: {{ $membership->usulan->nama_ketua ?? $membership->usulan->pengusul->name ?? '-' }} &bull; Semester: {{ $membership->usulan->semester ?? '-' }}</p>
                                </div>
                                <span class="px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap
                                    {{ $membership->usulan->status === 'Approved' ? 'bg-emerald-100 text-emerald-800' :
                                       ($membership->usulan->status === 'In_review' ? 'bg-blue-100 text-blue-800' :
                                       'bg-slate-100 text-slate-700') }}">
                                    {{ $membership->usulan->status ?? 'Draft' }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Info cara diundang --}}
                    <div class="p-5 rounded-2xl bg-blue-50 border border-blue-200 space-y-2">
                        <h5 class="text-xs font-extrabold text-blue-900 uppercase tracking-wider">ℹ️ Cara Bergabung sebagai Anggota Tim</h5>
                        <p class="text-xs text-blue-800 font-semibold">Anda akan secara otomatis diundang oleh <strong>Ketua Dosen Pengusul</strong> saat mengisi Langkah 2 (Tim Peneliti) pada Form Wizard Proposal mereka. Masukkan NIDN/NIM Anda sebagai anggota dosen/mahasiswa.</p>
                        <p class="text-xs text-blue-700">Setelah diundang, undangan akan muncul di atas halaman ini dan perlu Anda setujui.</p>
                    </div>

                    <!-- Pengajuan KI / Paten / HKI untuk Anggota Dosen & Mahasiswa -->
                    <div class="space-y-4 pt-6 border-t border-slate-200">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <span class="text-[10px] font-black uppercase tracking-wider text-indigo-700">Sentra Kekayaan Intelektual UHN</span>
                                <h4 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">Pengajuan KI / Paten / HKI (Dosen &amp; Mahasiswa):</h4>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('hki.create') }}" class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-extrabold shadow-xs transition flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    <span>Form Pengajuan KI / Paten</span>
                                </a>
                                <a href="{{ route('hki.index') }}" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                                    Daftar HKI &rarr;
                                </a>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Paten -->
                            <div class="p-4.5 rounded-2xl bg-indigo-50/70 border border-indigo-200 flex flex-col justify-between space-y-3">
                                <div>
                                    <div class="flex items-center justify-between">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-indigo-200 text-indigo-900">PATEN</span>
                                        <span class="text-[10px] font-bold text-indigo-700">Invensi Teknologi</span>
                                    </div>
                                    <h5 class="text-sm font-extrabold text-slate-900 mt-2">Paten &amp; Paten Sederhana</h5>
                                    <p class="text-[11px] text-slate-600 mt-1">Invensi teknologi, metode, alat, formulasi baru atau pengembangan produk dengan kebaruan teknis.</p>
                                </div>
                                <a href="{{ route('hki.create') }}" class="w-full py-2 rounded-xl bg-indigo-700 hover:bg-indigo-800 text-white font-extrabold text-xs text-center transition-all shadow-xs block">
                                    + Ajukan Permohonan Paten
                                </a>
                            </div>

                            <!-- Hak Cipta / HKI -->
                            <div class="p-4.5 rounded-2xl bg-blue-50/70 border border-blue-200 flex flex-col justify-between space-y-3">
                                <div>
                                    <div class="flex items-center justify-between">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-blue-200 text-blue-900">HKI</span>
                                        <span class="text-[10px] font-bold text-blue-700">Karya Cipta</span>
                                    </div>
                                    <h5 class="text-sm font-extrabold text-slate-900 mt-2">Hak Cipta (HKI)</h5>
                                    <p class="text-[11px] text-slate-600 mt-1">Karya tulis ilmiah, monograf, buku ajar ber-ISBN, modul perkuliahan, dan source code aplikasi/software.</p>
                                </div>
                                <a href="{{ route('hki.create') }}" class="w-full py-2 rounded-xl bg-blue-700 hover:bg-blue-800 text-white font-extrabold text-xs text-center transition-all shadow-xs block">
                                    + Ajukan Hak Cipta (HKI)
                                </a>
                            </div>

                            <!-- Desain Industri & Merk -->
                            <div class="p-4.5 rounded-2xl bg-emerald-50/70 border border-emerald-200 flex flex-col justify-between space-y-3">
                                <div>
                                    <div class="flex items-center justify-between">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-emerald-200 text-emerald-900">KI LAINNYA</span>
                                        <span class="text-[10px] font-bold text-emerald-700">Desain &amp; Merk</span>
                                    </div>
                                    <h5 class="text-sm font-extrabold text-slate-900 mt-2">Desain Industri &amp; Merk</h5>
                                    <p class="text-[11px] text-slate-600 mt-1">Kreasi estetika bentuk 2D/3D kemasan produk, prototipe perangkat keras, atau merk dagang riset.</p>
                                </div>
                                <a href="{{ route('hki.create') }}" class="w-full py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-extrabold text-xs text-center transition-all shadow-xs block">
                                    + Ajukan Desain / Merk
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            @elseif($activeRole === 'Reviewer')
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div class="p-5 rounded-2xl bg-purple-50 border border-purple-200">
                            <span class="text-xs font-bold text-purple-900 uppercase tracking-wider block">Total Penelitian Dosen:</span>
                            <h4 class="text-xl font-extrabold text-purple-950 mt-1">{{ $stats['total_proposals'] }} Penelitian</h4>
                            <span class="text-xs font-semibold text-purple-800">Basis Data Penelitian UHN</span>
                        </div>
                        <div class="p-5 rounded-2xl bg-blue-50 border border-blue-200">
                            <span class="text-xs font-bold text-blue-900 uppercase tracking-wider block">Batas Waktu Penilaian:</span>
                            <h4 class="text-xl font-extrabold text-blue-950 mt-1">14 Hari Kerja</h4>
                            <span class="text-xs font-semibold text-blue-800">Skala Rubrik 1-7 BIMA</span>
                        </div>
                        <div class="p-5 rounded-2xl bg-emerald-50 border border-emerald-200">
                            <span class="text-xs font-bold text-emerald-900 uppercase tracking-wider block">Penelitian Disetujui:</span>
                            <h4 class="text-xl font-extrabold text-emerald-950 mt-1">{{ $stats['approved_lppm'] }} Penelitian</h4>
                            <span class="text-xs font-bold text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded">Data Resmi Terverifikasi</span>
                        </div>
                    </div>
                    <div class="p-6 rounded-3xl bg-purple-900 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-xl">
                        <div>
                            <span class="text-xs font-bold text-purple-200 uppercase">Database Penelitian & Double-Blind Assessment</span>
                            <h4 class="text-lg font-extrabold mt-0.5">Kelola Penilaian & Lihat {{ $stats['total_proposals'] }} Data Penelitian Dosen</h4>
                            <p class="text-xs text-purple-100 mt-1">Penilaian objektif berbasis rubrik BIMA dan rekapitulasi penelitian dosen Universitas Harkat Negeri.</p>
                        </div>
                        <a href="{{ route('usulan.index') }}" class="px-6 py-3.5 rounded-2xl bg-white text-purple-900 font-extrabold text-sm hover:bg-purple-50 shadow-md whitespace-nowrap text-center">
                            Lihat Data Penelitian &rarr;
                        </a>
                    </div>
                </div>

            @elseif($activeRole === 'Kaprodi')
                <div class="space-y-6">
                    <div class="p-6 rounded-3xl bg-indigo-50 border border-indigo-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <span class="text-xs font-bold text-indigo-900 uppercase tracking-wider">Database Penelitian Program Studi</span>
                            <h4 class="text-lg font-extrabold text-indigo-950 mt-1">{{ $stats['total_proposals'] }} Data Penelitian Dosen Terdaftar</h4>
                            <p class="text-xs text-slate-700 mt-1">Pantau daftar penelitian dosen per Semester & Program Studi serta keselarasan roadmap keilmuan.</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('usulan.index') }}" class="px-5 py-3 rounded-2xl bg-indigo-700 text-white font-extrabold text-xs sm:text-sm hover:bg-indigo-800 shadow-md whitespace-nowrap">
                                Data Penelitian Dosen ({{ $stats['total_proposals'] }}) &rarr;
                            </a>
                            <a href="{{ route('admin.prodi-roadmap.index') }}" class="px-5 py-3 rounded-2xl bg-white text-indigo-800 border border-indigo-300 font-extrabold text-xs sm:text-sm hover:bg-indigo-100 whitespace-nowrap">
                                Roadmap Prodi
                            </a>
                        </div>
                    </div>
                </div>

            @elseif($activeRole === 'Dekanat')
                <div class="space-y-6">
                    <div class="p-6 rounded-3xl bg-emerald-50 border border-emerald-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <span class="text-xs font-bold text-emerald-900 uppercase tracking-wider">Rekapitulasi Penelitian Fakultas & Universitas</span>
                            <h4 class="text-lg font-extrabold text-emerald-950 mt-1">{{ $stats['total_proposals'] }} Penelitian Dosen ({{ $stats['total_pagu'] }})</h4>
                            <p class="text-xs text-slate-700 mt-1">Monitoring capaian penelitian 146 Dosen Tetap UHN lintas Program Studi dan Semester.</p>
                        </div>
                        <a href="{{ route('usulan.index') }}" class="px-6 py-3.5 rounded-2xl bg-emerald-700 text-white font-extrabold text-sm hover:bg-emerald-800 shadow-md whitespace-nowrap">
                            Buka Data Penelitian ({{ $stats['total_proposals'] }}) &rarr;
                        </a>
                    </div>
                </div>

            @elseif($activeRole === 'Admin P3M')
                <div class="space-y-6">
                    <div class="p-6 rounded-3xl bg-amber-50 border border-amber-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <span class="text-xs font-bold text-amber-900 uppercase tracking-wider">Manajemen Data Penelitian & Dosen P3M</span>
                            <h4 class="text-lg font-extrabold text-amber-950 mt-1">{{ $stats['total_proposals'] }} Penelitian Dosen &bull; {{ $stats['total_users'] }} Dosen Tetap UHN</h4>
                            <p class="text-xs text-slate-700 mt-1">Kelola basis data resmi penelitian dosen (Semester, No, Judul, Ketua, NIDN/NUPN, Anggota, Prodi, Nominal) & verifikasi P3M.</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('usulan.index') }}" class="px-5 py-3 rounded-2xl bg-amber-700 text-white font-extrabold text-xs sm:text-sm hover:bg-amber-800 shadow-md whitespace-nowrap">
                                Data Penelitian ({{ $stats['total_proposals'] }}) &rarr;
                            </a>
                            <a href="{{ route('admin.users.index') }}" class="px-5 py-3 rounded-2xl bg-white text-amber-900 border border-amber-300 font-extrabold text-xs sm:text-sm hover:bg-amber-100 whitespace-nowrap">
                                Data Dosen ({{ $stats['total_users'] }})
                            </a>
                        </div>
                    </div>
                </div>

            @elseif($activeRole === 'Kepala P3M')
                <div class="space-y-6">
                    <div class="p-6 rounded-3xl bg-cyan-50 border border-cyan-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <span class="text-xs font-bold text-cyan-900 uppercase tracking-wider">Rekapitulasi & Persetujuan Kepala P3M</span>
                            <h4 class="text-lg font-extrabold text-cyan-950 mt-1">{{ $stats['total_proposals'] }} Penelitian Dosen Resmi ({{ $stats['total_pagu'] }})</h4>
                            <p class="text-xs text-slate-700 mt-1">Monitoring seluruh penelitian dosen Universitas Harkat Negeri, verifikasi LPPM, dan laporan akreditasi.</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('usulan.index') }}" class="px-5 py-3 rounded-2xl bg-cyan-700 text-white font-extrabold text-xs sm:text-sm hover:bg-cyan-800 shadow-md whitespace-nowrap">
                                Data Penelitian ({{ $stats['total_proposals'] }}) &rarr;
                            </a>
                            <a href="{{ route('admin.users.index') }}" class="px-5 py-3 rounded-2xl bg-white text-cyan-900 border border-cyan-300 font-extrabold text-xs sm:text-sm hover:bg-cyan-100 whitespace-nowrap">
                                Data Dosen ({{ $stats['total_users'] }})
                            </a>
                        </div>
                    </div>
                </div>

            @elseif($activeRole === 'Keuangan')
                <div class="space-y-6">
                    <div class="p-6 rounded-3xl bg-emerald-50 border border-emerald-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <span class="text-xs font-bold text-emerald-900 uppercase tracking-wider">Rekapitulasi Nominal Penelitian Dosen</span>
                            <h4 class="text-lg font-extrabold text-emerald-950 mt-1">Total Nominal: {{ $stats['total_pagu'] }} ({{ $stats['total_proposals'] }} Penelitian)</h4>
                            <p class="text-xs text-slate-700 mt-1">Monitoring anggaran penelitian dosen Universitas Harkat Negeri dan pencairan multi-termin.</p>
                        </div>
                        <a href="{{ route('usulan.index') }}" class="px-6 py-3.5 rounded-2xl bg-emerald-700 text-white font-extrabold text-sm hover:bg-emerald-800 shadow-md whitespace-nowrap">
                            Lihat Nominal Penelitian &rarr;
                        </a>
                    </div>
                </div>

            @elseif($activeRole === 'Superadmin')
                <div class="space-y-6">
                    <div class="p-6 rounded-3xl bg-red-50 border border-red-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <span class="text-xs font-bold text-red-900 uppercase tracking-wider">Pusat Kendali Superadmin & Basis Data Resmi UHN</span>
                            <h4 class="text-lg font-extrabold text-red-950 mt-1">{{ $stats['total_users'] }} Dosen Tetap &bull; {{ $stats['total_proposals'] }} Penelitian Dosen Resmi</h4>
                            <p class="text-xs text-slate-700 mt-1">Kelola langsung database 146 Dosen Tetap UHN dan 438 Data Penelitian Dosen bebas data dummy.</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('usulan.index') }}" class="px-5 py-3 rounded-2xl bg-red-700 text-white font-extrabold text-xs sm:text-sm hover:bg-red-800 shadow-md whitespace-nowrap">
                                Data Penelitian ({{ $stats['total_proposals'] }}) &rarr;
                            </a>
                            <a href="{{ route('admin.users.index') }}" class="px-5 py-3 rounded-2xl bg-white text-red-800 border border-red-300 font-extrabold text-xs sm:text-sm hover:bg-red-100 whitespace-nowrap">
                                Data Dosen ({{ $stats['total_users'] }})
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </main>
    </div>
</div>
@endsection

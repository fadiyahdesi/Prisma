@extends('layouts.app')

@section('title', 'Login SSO SIAKAD Cloud - PRISMA UHN')

@section('content')
<div class="min-h-screen bg-slate-900 flex items-center justify-center p-4 sm:p-6 lg:p-8 relative overflow-hidden">
    <!-- Ambient Background Lighting -->
    <div class="absolute -top-40 -left-40 w-[500px] h-[500px] bg-blue-600/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-[500px] h-[500px] bg-indigo-600/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-4xl w-full relative z-10">
        <!-- Logo & Header -->
        <div class="text-center mb-8">
            <a href="{{ route('landing') }}" class="inline-flex items-center gap-3 group">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-cyan-400 flex items-center justify-center text-white shadow-lg shadow-blue-500/30 group-hover:scale-105 transition-transform">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                </div>
                <div class="text-left">
                    <div class="flex items-center gap-2">
                        <span class="font-black text-2xl tracking-tight text-white">PRISMA</span>
                        <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-full bg-blue-500/20 text-blue-300 border border-blue-500/30">UHN</span>
                    </div>
                    <p class="text-xs font-medium text-slate-400">Universitas Harkat Negeri</p>
                </div>
            </a>

            <h2 class="mt-6 text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                Single Sign-On (SSO OAuth2) SIAKAD Cloud
            </h2>
            <p class="mt-2 text-sm text-slate-400">
                Pilih akun demo atau peran Anda untuk masuk secara otomatis dengan otentikasi terpadu.
            </p>
        </div>

        <!-- Alert Notification if any -->
        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-950/80 border border-emerald-500/30 text-emerald-300 text-sm font-semibold flex items-center gap-3 shadow-lg">
                <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-red-950/80 border border-red-500/30 text-red-300 text-sm font-semibold flex items-center gap-3 shadow-lg">
                <svg class="w-5 h-5 text-red-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <!-- SSO Login Card -->
        <div class="bg-slate-800/90 rounded-3xl p-6 sm:p-8 border border-slate-700/80 shadow-2xl backdrop-blur-md">
            <div class="flex items-center justify-between pb-6 border-b border-slate-700/80">
                <div>
                    <span class="text-xs font-bold text-blue-400 uppercase tracking-wider block">Otentikasi Terpadu US-02.1</span>
                    <h3 class="text-lg font-bold text-white">Pilih Peran Akun SSO (Simulasi SIAKAD Cloud)</h3>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                    SSO OAuth2 Ready
                </span>
            </div>

            <!-- Demo Role Select Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
                @php
                    $roleList = [
                        ['name' => 'Dosen / Pengusul', 'email' => 'dosen@harkatnegeri.ac.id', 'icon' => 'microscope', 'color' => 'blue', 'desc' => 'Buat Usulan & RAB SBM'],
                        ['name' => 'Reviewer', 'email' => 'reviewer@harkatnegeri.ac.id', 'icon' => 'award', 'color' => 'purple', 'desc' => 'Double-Blind Review (1-7)'],
                        ['name' => 'Kaprodi', 'email' => 'kaprodi@harkatnegeri.ac.id', 'icon' => 'home', 'color' => 'indigo', 'desc' => 'Monitoring Roadmap Prodi'],
                        ['name' => 'Dekanat', 'email' => 'dekan@harkatnegeri.ac.id', 'icon' => 'building', 'color' => 'emerald', 'desc' => 'Dasbor Agregasi Fakultas'],
                        ['name' => 'Admin P3M', 'email' => 'adminp3m@harkatnegeri.ac.id', 'icon' => 'cog', 'color' => 'amber', 'desc' => 'Kelola Call for Proposals'],
                        ['name' => 'Kepala P3M', 'email' => 'kepalap3m@harkatnegeri.ac.id', 'icon' => 'check-circle', 'color' => 'cyan', 'desc' => 'LPPM Approval & SK'],
                        ['name' => 'Keuangan', 'email' => 'keuangan@harkatnegeri.ac.id', 'icon' => 'dollar', 'color' => 'emerald', 'desc' => 'Pencairan 70/30 & Reward'],
                        ['name' => 'Superadmin', 'email' => 'superadmin@harkatnegeri.ac.id', 'icon' => 'shield', 'color' => 'red', 'desc' => 'Audit Trail & Full Config'],
                    ];
                @endphp

                @foreach($roleList as $r)
                    <a href="{{ route('sso.redirect', ['role' => $r['name']]) }}" 
                       class="p-4 rounded-2xl bg-slate-900/90 border border-slate-700/70 hover:border-blue-500 hover:bg-slate-900 transition-all duration-200 flex flex-col justify-between group shadow-sm hover:shadow-lg">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <div class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-400 group-hover:bg-blue-600 group-hover:text-white flex items-center justify-center transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <span class="text-[10px] font-mono text-slate-500">OAuth2</span>
                            </div>
                            <h4 class="text-sm font-bold text-white group-hover:text-blue-400 transition-colors">{{ $r['name'] }}</h4>
                            <p class="text-[11px] text-slate-400 mt-1 leading-tight">{{ $r['desc'] }}</p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-800 flex items-center justify-between text-[11px] text-slate-400 group-hover:text-white">
                            <span class="truncate font-mono">{{ $r['email'] }}</span>
                            <span class="font-bold text-blue-400">&rarr;</span>
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Direct SSO Button -->
            <div class="mt-8 pt-6 border-t border-slate-700/80 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-xs text-slate-400">
                    Integrasi resmi akun: <strong class="text-slate-200">siakad.harkatnegeri.ac.id/oauth</strong>
                </div>
                <a href="{{ route('sso.redirect') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-sm shadow-lg shadow-blue-600/30 flex items-center justify-center gap-2 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    Masuk SSO SIAKAD Utama
                </a>
            </div>
        </div>
    </div>
</div>
@endsection


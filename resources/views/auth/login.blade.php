@extends('layouts.app')

@section('title', 'Login Sistem PRISMA - Universitas Harkat Negeri')

@section('content')
<div x-data="{ showPassword: false, showSsoSim: false }" class="min-h-screen bg-slate-900 flex items-center justify-center p-4 sm:p-6 lg:p-8 relative overflow-hidden">
    <!-- Ambient Background Lighting -->
    <div class="absolute -top-40 -left-40 w-[500px] h-[500px] bg-[#681727]/30 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-[500px] h-[500px] bg-amber-600/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-4xl w-full relative z-10 space-y-6">
        <!-- Logo & Header -->
        <div class="text-center">
            <a href="{{ route('landing') }}" class="inline-flex items-center gap-3 group">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-[#681727] via-[#8c1d34] to-[#c99738] flex items-center justify-center text-white shadow-lg shadow-[#681727]/40 group-hover:scale-105 transition-transform">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                </div>
                <div class="text-left">
                    <div class="flex items-center gap-2">
                        <span class="font-black text-2xl tracking-tight text-white">PRISMA</span>
                        <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30">UHN</span>
                    </div>
                    <p class="text-xs font-medium text-slate-400">Universitas Harkat Negeri</p>
                </div>
            </a>

            <h2 class="mt-4 text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                Portal Masuk Sistem PRISMA
            </h2>
            <p class="mt-1 text-xs sm:text-sm text-slate-400 max-w-lg mx-auto">
                Sistem Informasi Riset, Pengabdian Masyarakat, Publikasi &amp; Sentra HKI UHN
            </p>
        </div>

        <!-- Alert Notification -->
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-950/80 border border-emerald-500/30 text-emerald-300 text-xs sm:text-sm font-semibold flex items-center gap-3 shadow-lg">
                <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 rounded-2xl bg-red-950/80 border border-red-500/30 text-red-300 text-xs sm:text-sm font-semibold flex items-center gap-3 shadow-lg">
                <svg class="w-5 h-5 text-red-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <!-- Main Form Login Card -->
        <div class="bg-slate-800/90 rounded-3xl p-6 sm:p-8 border border-slate-700/80 shadow-2xl backdrop-blur-md max-w-xl mx-auto w-full">
            <div class="flex items-center justify-between pb-4 border-b border-slate-700/80 mb-6">
                <div>
                    <span class="text-[11px] font-black text-[#c99738] uppercase tracking-wider block">Otentikasi Akun</span>
                    <h3 class="text-lg font-bold text-white">Masuk ke Akun Anda</h3>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                    Keamanan Terenkripsi
                </span>
            </div>

            <!-- Petunjuk Akun Dosen -->
            <div class="mb-5 p-3.5 rounded-2xl bg-slate-900/80 border border-slate-700 text-xs text-slate-300 flex items-start gap-2.5">
                <div class="w-5 h-5 rounded-full bg-amber-500/20 text-amber-400 flex items-center justify-center shrink-0 mt-0.5 text-xs font-bold">i</div>
                <div class="text-[11px] leading-relaxed">
                    <p class="font-bold text-slate-200">Petunjuk Masuk Dosen &amp; Pimpinan:</p>
                    <p class="text-slate-400 mt-0.5">
                        Username: <strong>Nama Lengkap</strong> (contoh: <span class="text-amber-300">Sharfina Febbi Handayani</span>) atau <strong>NIDN</strong>.<br>
                        Password: <strong>NIDN Anda</strong> (contoh: <span class="text-amber-300">0617029201</span>).
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf

                <!-- Input Nama / NIDN / Email -->
                <div>
                    <label for="identity" class="block text-xs font-bold text-slate-300 mb-1.5">
                        Nama Lengkap atau NIDN <span class="text-rose-400">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <input type="text" id="identity" name="identity" required autofocus
                               value="{{ old('identity') }}"
                               placeholder="Contoh: Sharfina Febbi Handayani atau 0617029201"
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-900/90 border border-slate-700 text-white text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-[#681727] focus:border-transparent placeholder:text-slate-500">
                    </div>
                </div>

                <!-- Input Password (NIDN) -->
                <div>
                    <label for="password" class="block text-xs font-bold text-slate-300 mb-1.5">
                        Kata Sandi (NIDN) <span class="text-rose-400">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <input :type="showPassword ? 'text' : 'password'" id="password" name="password" required
                               placeholder="Masukkan NIDN Anda (contoh: 0617029201)"
                               class="w-full pl-10 pr-10 py-2.5 rounded-xl bg-slate-900/90 border border-slate-700 text-white text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-[#681727] focus:border-transparent placeholder:text-slate-500">
                        <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-500 hover:text-slate-300">
                            <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="showPassword" style="display:none;" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2 cursor-pointer text-slate-400 hover:text-slate-200">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded-sm bg-slate-900 border-slate-700 text-[#681727] focus:ring-[#681727]">
                        <span>Ingat saya di perangkat ini</span>
                    </label>
                    <span class="text-slate-500 text-[11px]">Lupa NIDN? Hubungi Admin P3M</span>
                </div>

                <!-- Submit Button with UHN Maroon Theme -->
                <button type="submit"
                        class="w-full py-3 px-4 rounded-xl bg-[#681727] hover:bg-[#52121f] active:scale-[0.99] text-white font-extrabold text-xs sm:text-sm shadow-lg shadow-[#681727]/30 transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    <span>Masuk ke PRISMA</span>
                </button>
            </form>
        </div>

        <!-- Quick Access Simulation (Accordion for Testing & Grading) -->
        <div class="bg-slate-850/60 rounded-3xl p-6 border border-slate-800 text-center">
            <div class="flex items-center justify-between">
                <div class="text-left">
                    <h4 class="text-xs font-bold text-slate-300">Simulasi Cepat Peran (SSO SIAKAD Cloud)</h4>
                    <p class="text-[11px] text-slate-500">Klik kartu peran untuk simulasi login instan tanpa mengetik kredensial.</p>
                </div>
                <button type="button" @click="showSsoSim = !showSsoSim"
                        class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold border border-slate-700 transition flex items-center gap-1.5">
                    <span x-text="showSsoSim ? 'Tutup Peran' : 'Buka Pilihan Peran'"></span>
                    <svg class="w-3.5 h-3.5 transform transition-transform" :class="showSsoSim ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
            </div>

            <!-- Role Cards Grid -->
            <div x-show="showSsoSim" style="display: none;" class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4 text-left">
                @php
                    $roleList = [
                        ['name' => 'Dosen / Pengusul', 'email' => 'Ginanjar Wiro Sasmito', 'desc' => 'NIDN: 0613028601', 'role' => 'Dosen / Pengusul'],
                        ['name' => 'Kepala P3M', 'email' => 'Sharfina Febbi Handayani', 'desc' => 'NIDN: 0617029201', 'role' => 'Kepala P3M'],
                        ['name' => 'Reviewer', 'email' => 'Prof. Dr. Ir. Budi Santoso', 'desc' => 'NIDN: 0620087102', 'role' => 'Reviewer'],
                        ['name' => 'Kaprodi', 'email' => 'Dr. Ratna Sari, S.E.', 'desc' => 'NIDN: 0612058001', 'role' => 'Kaprodi'],
                        ['name' => 'Dekanat', 'email' => 'Drs. Bambang Sudiro', 'desc' => 'Fakultas Vokasi', 'role' => 'Dekanat'],
                        ['name' => 'Admin P3M', 'email' => 'Staf Sentra HKI', 'desc' => 'adminp3m@harkatnegeri.ac.id', 'role' => 'Admin P3M'],
                        ['name' => 'Keuangan', 'email' => 'Bendahara Riset', 'desc' => 'Pencairan 70/30', 'role' => 'Keuangan'],
                        ['name' => 'Superadmin', 'email' => 'Administrator Sistem', 'desc' => 'Full Access Control', 'role' => 'Superadmin'],
                    ];
                @endphp

                @foreach($roleList as $r)
                    <a href="{{ route('sso.redirect', ['role' => $r['role']]) }}"
                       class="p-3 rounded-xl bg-slate-900/90 border border-slate-800 hover:border-[#681727] transition flex flex-col justify-between group">
                        <div>
                            <span class="text-[10px] font-bold text-amber-400 block">{{ $r['name'] }}</span>
                            <p class="text-xs font-extrabold text-white mt-0.5 truncate group-hover:text-amber-200">{{ $r['email'] }}</p>
                        </div>
                        <p class="text-[10px] text-slate-500 mt-2 font-mono">{{ $r['desc'] }}</p>
                    </a>
                @endforeach
            </div>
        </div>

    </div>
</div>
@endsection

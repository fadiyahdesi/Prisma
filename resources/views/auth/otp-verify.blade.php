@extends('layouts.app')

@section('title', 'Verifikasi Keamanan OTP - PRISMA UHN')

@section('content')
<div class="min-h-screen bg-slate-100 flex flex-col justify-between"
     x-data="{ 
         timeLeft: 300, 
         formattedTime: '05:00',
         timer: null,
         startTimer() {
             this.timer = setInterval(() => {
                 if (this.timeLeft > 0) {
                     this.timeLeft--;
                     let mins = Math.floor(this.timeLeft / 60).toString().padStart(2, '0');
                     let secs = (this.timeLeft % 60).toString().padStart(2, '0');
                     this.formattedTime = `${mins}:${secs}`;
                 } else {
                     clearInterval(this.timer);
                 }
             }, 1000);
         }
     }"
     x-init="startTimer()">

    <!-- Solid Navy Header Banner (High Contrast White Text) -->
    <div class="bg-gradient-to-r from-blue-950 via-slate-900 to-indigo-950 text-white pt-10 pb-16 px-4 sm:px-6 lg:px-8 text-center border-b border-blue-900 shadow-md">
        <div class="max-w-md mx-auto space-y-3">
            <div class="w-14 h-14 rounded-2xl bg-white/10 backdrop-blur-md text-white flex items-center justify-center mx-auto shadow-xl border border-white/20">
                <svg class="w-7 h-7 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Verifikasi Kode OTP (2FA)</h1>
            <p class="text-xs sm:text-sm text-blue-200 font-semibold">
                Kode keamanan 6-angka telah dikirimkan ke Email Kampus Anda: <br>
                <strong class="text-white font-mono bg-blue-900 px-3 py-1 rounded-lg mt-1 inline-block border border-blue-500/40 text-sm">{{ $user->email }}</strong>
            </p>
        </div>
    </div>

    <!-- Main Content Container Overlapping Banner -->
    <main class="max-w-md w-full mx-auto px-4 sm:px-6 lg:px-8 -mt-8 pb-16 space-y-5">
        <!-- Alert Lockout / Failure Messages -->
        @if(isset($errors) && $errors->any())
            <div class="p-4 rounded-2xl bg-red-50 border-2 border-red-300 text-red-900 text-sm font-black space-y-1 shadow-md">
                @foreach ($errors->all() as $error)
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ $error }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        @if(session('info'))
            <div class="p-4 rounded-2xl bg-blue-50 border-2 border-blue-300 text-blue-900 text-sm font-black flex items-center gap-3 shadow-md">
                <svg class="w-5 h-5 text-blue-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('info') }}</span>
            </div>
        @endif

        <!-- Simulated OTP Code Banner Box for Demo Testing -->
        @if($demoOtpCode)
            <div class="p-4.5 rounded-3xl bg-emerald-50 border-2 border-emerald-300 text-emerald-950 text-xs flex items-center justify-between shadow-lg">
                <div>
                    <span class="text-[11px] text-emerald-800 uppercase tracking-wider block font-black">Kode OTP Simulasi (Redis TTL 5m):</span>
                    <span class="text-2xl font-black font-mono tracking-widest text-emerald-900">{{ $demoOtpCode }}</span>
                </div>
                <button type="button" @click="document.getElementById('otp_code_input').value = '{{ $demoOtpCode }}'" class="px-4 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-black text-xs transition-colors shadow-md">
                    Isi Otomatis
                </button>
            </div>
        @endif

        <!-- Main OTP Form Card -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-2xl space-y-6">
            <form action="{{ route('otp.verify') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="otp_code_input" class="text-xs font-black text-blue-900 uppercase tracking-wider">Masukkan 6 Angka OTP:</label>
                        <span class="text-xs font-mono font-black text-amber-900 bg-amber-100 px-2.5 py-1 rounded-md border border-amber-300" x-text="'Berlaku: ' + formattedTime"></span>
                    </div>
                    <input type="text" 
                           id="otp_code_input" 
                           name="otp_code" 
                           maxlength="6" 
                           placeholder="123456" 
                           required 
                           autofocus
                           class="w-full text-center tracking-[0.5em] text-3xl font-mono font-black py-4 rounded-2xl bg-slate-50 border-2 border-slate-300 text-blue-950 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 focus:bg-white transition-all shadow-inner">
                </div>

                <button type="submit" 
                        @if($user->isOtpLocked()) disabled @endif
                        class="w-full py-4 rounded-2xl bg-blue-700 hover:bg-blue-800 text-white font-black text-base shadow-xl shadow-blue-600/30 disabled:opacity-50 transition-all">
                    Verifikasi Kode & Masuk Dashboard
                </button>
            </form>

            <div class="pt-6 border-t border-slate-200 flex items-center justify-between text-xs font-extrabold">
                <form action="{{ route('otp.resend') }}" method="POST">
                    @csrf
                    <button type="submit" 
                            :disabled="timeLeft > 270"
                            class="text-blue-700 hover:text-blue-900 underline disabled:opacity-50 transition-colors">
                        Kirim Ulang Kode OTP
                    </button>
                </form>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-slate-600 hover:text-slate-900">Ganti Akun &rarr;</button>
                </form>
            </div>
        </div>
    </main>

    <!-- Simple Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-600 font-semibold">
        <p>&copy; {{ date('Y') }} Unit P3M Universitas Harkat Negeri • Otentikasi 2FA OTP BIMA-Compliant</p>
    </footer>
</div>
@endsection

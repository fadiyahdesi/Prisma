@extends('layouts.app')

@section('title', 'Pengaturan Profil - PRISMA UHN')

@section('content')
<div x-data="{
        sidebarOpen: false,
        activeTab: '{{ $errors->has('current_password') || $errors->has('password') || $errors->has('password_confirmation') ? 'keamanan' : 'profil' }}',
        avatarPreview: null,
        fileName: '',
        hasNewAvatar: false
    }"
    class="min-h-screen bg-slate-100 text-slate-800 flex">
    
    <x-sidebar />

    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        <!-- Topbar Header: Clean & Standard with Back Button to Left of Title -->
        <header class="bg-white border-b border-slate-200 py-3 sticky top-0 z-30 shadow-xs">
            <div class="w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 shrink-0" aria-label="Buka Menu">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <!-- Standard Back Button to Left of Title -->
                    <a href="{{ route('dashboard') }}" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center shrink-0 transition" title="Kembali ke Dashboard" aria-label="Kembali ke Dashboard">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </a>
                    <div class="truncate">
                        <h1 class="font-extrabold text-lg sm:text-xl text-slate-900 leading-tight truncate">Pengaturan Profil</h1>
                        <p class="text-[11px] font-semibold text-slate-500 hidden sm:block">Kelola identitas diri, email aktif, foto profil, dan kata sandi &bull; PRISMA UHN</p>
                    </div>
                </div>

                <!-- Role Badges (Clean & Uncluttered Header) -->
                <div class="flex items-center gap-1.5 shrink-0">
                    @foreach($user->roles as $r)
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-[#681727]/10 text-[#681727] border border-[#681727]/20">
                            {{ $r->name }}
                        </span>
                    @endforeach
                </div>
            </div>
        </header>

        <main class="flex-grow w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
            <!-- Flash Notification -->
            @if(session('success'))
                <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-950 text-xs font-extrabold flex items-center gap-2.5 shadow-xs">
                    <div class="w-6 h-6 rounded-lg bg-emerald-600 text-white flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-300 text-rose-950 text-xs font-bold space-y-1 shadow-xs">
                    <div class="flex items-center gap-2 text-rose-900 font-extrabold">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Terdapat kesalahan isian formulir:</span>
                    </div>
                    <ul class="list-disc list-inside pl-6 text-rose-800 text-[11px]">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Tab Navigation Bar (Compact & Fits on 1 Screen) -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                <div class="inline-flex p-1 bg-slate-200/80 rounded-2xl border border-slate-300/60 shadow-xs">
                    <button type="button" @click="activeTab = 'profil'"
                            :class="activeTab === 'profil' ? 'bg-white text-slate-900 shadow-xs font-black' : 'text-slate-600 hover:text-slate-900 font-bold'"
                            class="px-4 py-1.5 rounded-xl text-xs transition flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>Profil &amp; Kontak</span>
                    </button>

                    <button type="button" @click="activeTab = 'keamanan'"
                            :class="activeTab === 'keamanan' ? 'bg-white text-slate-900 shadow-xs font-black' : 'text-slate-600 hover:text-slate-900 font-bold'"
                            class="px-4 py-1.5 rounded-xl text-xs transition flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <span>Keamanan &amp; Kata Sandi</span>
                    </button>
                </div>

                <!-- Email Status Quick Notice -->
                @if($isPlaceholderEmail)
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-xl bg-amber-100 text-amber-900 border border-amber-300 text-xs font-bold">
                        <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
                        <span>Wajib tambahkan email aktif Anda</span>
                    </div>
                @else
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-xl bg-emerald-50 text-emerald-900 border border-emerald-300 text-xs font-bold">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                        <span>Email Aktif: {{ $user->email }}</span>
                    </div>
                @endif
            </div>

            <!-- ================= TAB 1: PROFIL & INFORMASI PRIBADI ================= -->
            <div x-show="activeTab === 'profil'" x-cloak class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">
                <!-- Left Sub-column (4 cols): Photo, Identity, SINTA -->
                <div class="lg:col-span-4 space-y-3.5">
                    <!-- Kartu Identitas & Foto -->
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-4 text-center space-y-2.5">
                        <div class="w-20 h-20 mx-auto rounded-2xl overflow-hidden ring-4 ring-slate-100 shadow-xs bg-slate-100 flex items-center justify-center shrink-0">
                            <template x-if="avatarPreview">
                                <img :src="avatarPreview" alt="Foto Baru" class="w-20 h-20 object-cover rounded-2xl">
                            </template>
                            <template x-if="!avatarPreview">
                                @if($user->avatar && Storage::disk('public')->exists($user->avatar))
                                    <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="w-20 h-20 object-cover rounded-2xl">
                                @else
                                    <div style="background-color: #681727 !important; color: #ffffff !important;" class="w-full h-full flex items-center justify-center font-black text-2xl">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                @endif
                            </template>
                        </div>

                        <div>
                            <h2 class="text-xs sm:text-sm font-black text-slate-900 leading-snug">{{ $user->name }}</h2>
                            @if(!empty($user->nidn_nim))
                                <span class="inline-block mt-0.5 px-2 py-0.5 rounded-md text-[11px] font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                    NIDN: {{ $user->nidn_nim }}
                                </span>
                            @endif
                        </div>

                        <div class="pt-2 border-t border-slate-100 text-[11px] text-slate-600 space-y-1 text-left">
                            <div class="flex justify-between"><span class="text-slate-400 font-bold">Fakultas:</span><span class="font-bold text-slate-800 text-right">{{ $user->fakultas?->nama_fakultas ?? 'Universitas Harkat Negeri' }}</span></div>
                            <div class="flex justify-between"><span class="text-slate-400 font-bold">Prodi:</span><span class="font-bold text-slate-800 text-right">{{ $user->prodi?->nama_prodi ?? 'P3M UHN' }}</span></div>
                            @if(!empty($user->jabatan_fungsional))
                                <div class="flex justify-between"><span class="text-slate-400 font-bold">Jabatan:</span><span class="font-bold text-slate-800 text-right">{{ $user->jabatan_fungsional }}</span></div>
                            @endif
                        </div>
                    </div>

                    <!-- Kartu SINTA (With Explicit Solid Dark Styling) -->
                    <div style="background-color: #0f172a !important; color: #ffffff !important;" class="rounded-3xl p-3.5 shadow-xs space-y-2">
                        <div class="flex items-center justify-between">
                            <span style="color: #f59e0b !important;" class="text-[10px] font-black uppercase tracking-wider">Pangkalan SINTA</span>
                            <span style="color: #94a3b8 !important;" class="text-xs font-mono font-bold">ID: {{ $user->sinta_id ?? '-' }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-center">
                            <div style="background-color: rgba(255,255,255,0.08) !important;" class="p-1.5 rounded-xl">
                                <span style="color: #94a3b8 !important;" class="text-[10px] block font-bold">Skor 3 Thn</span>
                                <span style="color: #ffffff !important;" class="text-sm font-black">{{ number_format($user->sinta_score_3yr ?? 0, 1) }}</span>
                            </div>
                            <div style="background-color: rgba(255,255,255,0.08) !important;" class="p-1.5 rounded-xl">
                                <span style="color: #94a3b8 !important;" class="text-[10px] block font-bold">Skor Total</span>
                                <span style="color: #fbbf24 !important;" class="text-sm font-black">{{ number_format($user->sinta_score_overall ?? 0, 1) }}</span>
                            </div>
                        </div>
                        <a href="{{ route('sinta.profile') }}" style="background-color: rgba(255,255,255,0.12) !important; color: #ffffff !important;" class="w-full py-1 px-3 rounded-xl font-bold text-[11px] flex items-center justify-center gap-1 hover:opacity-90 transition">
                            <span style="color: #ffffff !important;">Detail SINTA Sync &rarr;</span>
                        </a>
                    </div>
                </div>

                <!-- Right Sub-column (8 cols): Edit Form -->
                <div class="lg:col-span-8">
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
                        <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between bg-slate-50/60">
                            <div>
                                <h3 class="font-black text-sm text-slate-900">Formulir Data Pribadi &amp; Kontak</h3>
                                <p class="text-[11px] text-slate-500 font-semibold">Perbarui identitas, alamat email aktif, dan foto profil Anda</p>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-50 text-blue-800 border border-blue-200">
                                Profil Mandiri
                            </span>
                        </div>

                        <form id="profile-form" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="p-5 space-y-3.5">
                            @csrf
                            @method('PUT')

                            <!-- Avatar Upload Section -->
                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200 flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl overflow-hidden bg-slate-200 shrink-0 ring-1 ring-slate-300 flex items-center justify-center">
                                    <template x-if="avatarPreview">
                                        <img :src="avatarPreview" alt="Preview Foto" class="w-12 h-12 object-cover rounded-xl">
                                    </template>
                                    <template x-if="!avatarPreview">
                                        @if($user->avatar && Storage::disk('public')->exists($user->avatar))
                                            <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="w-12 h-12 object-cover rounded-xl">
                                        @else
                                            <div style="background-color: #681727 !important; color: #ffffff !important;" class="w-full h-full flex items-center justify-center font-bold text-sm">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </div>
                                        @endif
                                    </template>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="truncate">
                                            <h4 class="text-xs font-black text-slate-900 truncate">Foto Profil Resmi</h4>
                                            <p class="text-[10px] text-slate-500 truncate">JPG, PNG, atau WEBP (Maks 2 MB)</p>
                                        </div>
                                        <div class="flex items-center gap-1.5 shrink-0">
                                            <input type="file" name="avatar" accept="image/jpeg,image/png,image/jpg,image/webp" x-ref="avatarInput" class="hidden"
                                                   @change="const file = $event.target.files[0]; if (file) { fileName = file.name; hasNewAvatar = true; const reader = new FileReader(); reader.onload = (e) => avatarPreview = e.target.result; reader.readAsDataURL(file); }">
                                            <button type="button" @click="$refs.avatarInput.click()"
                                                    class="px-3 py-1 rounded-xl bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 text-xs font-bold transition flex items-center gap-1 cursor-pointer">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                <span x-text="hasNewAvatar ? 'Ganti Berkas' : 'Pilih Foto'">Pilih Foto</span>
                                            </button>
                                            <template x-if="hasNewAvatar">
                                                <button type="button" @click="avatarPreview = null; fileName = ''; hasNewAvatar = false; $refs.avatarInput.value = '';"
                                                        class="px-2 py-1 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold hover:bg-rose-100 transition cursor-pointer">
                                                    Batal
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                    <template x-if="fileName">
                                        <p class="text-[10px] font-bold text-emerald-700 truncate mt-0.5">
                                            &bull; Berkas baru dipilih: <span class="font-mono" x-text="fileName"></span> (Klik simpan untuk menerapkan)
                                        </p>
                                    </template>
                                </div>
                            </div>

                            <!-- Input Nama Lengkap -->
                            <div class="space-y-1">
                                <label class="block text-xs font-black text-slate-800">
                                    Nama Lengkap &amp; Gelar Akademik <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                       class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-[#681727] focus:border-[#681727] transition">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <!-- Input Email Aktif -->
                                <div class="space-y-1">
                                    <label class="block text-xs font-black text-slate-800 flex items-center justify-between">
                                        <span>Alamat Email Aktif <span class="text-rose-500">*</span></span>
                                        @if($isPlaceholderEmail)
                                            <span class="text-[9px] font-black text-amber-700 bg-amber-100 px-1.5 py-0.5 rounded">Wajib Diisi</span>
                                        @endif
                                    </label>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                           placeholder="nama.anda@gmail.com atau @harkatnegeri.ac.id"
                                           class="w-full px-3.5 py-2 rounded-xl border {{ $isPlaceholderEmail ? 'border-amber-400 bg-amber-50/40 ring-1 ring-amber-300' : 'border-slate-300' }} text-xs font-bold text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-[#681727] focus:border-[#681727] transition">
                                    <p class="text-[10px] text-slate-500">Untuk notifikasi hasil review &amp; pencairan.</p>
                                </div>

                                <!-- Input Nomor WhatsApp -->
                                <div class="space-y-1">
                                    <label class="block text-xs font-black text-slate-800">
                                        Nomor Handphone / WhatsApp
                                    </label>
                                    <input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}"
                                           placeholder="Contoh: 081234567890"
                                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-[#681727] focus:border-[#681727] transition">
                                    <p class="text-[10px] text-slate-500">Untuk koordinasi verifikasi luaran.</p>
                                </div>
                            </div>

                            <!-- Satu-satunya Tombol Simpan Form Profil (Jelas, Eksklusif & Proporsional) -->
                            <div class="pt-3 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                                <span class="text-[11px] text-slate-500 font-semibold flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Pastikan data sudah benar sebelum menyimpan</span>
                                </span>
                                <button type="submit"
                                        style="background-color: #681727 !important; color: #ffffff !important;"
                                        class="w-full sm:w-auto px-6 py-2 rounded-xl font-black text-xs shadow-md transition flex items-center justify-center gap-2 cursor-pointer hover:opacity-90">
                                    <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span style="color: #ffffff !important;">Simpan Perubahan Profil</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ================= TAB 2: KEAMANAN & KATA SANDI ================= -->
            <div x-show="activeTab === 'keamanan'" x-cloak class="max-w-2xl mx-auto">
                <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
                    <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between bg-slate-50/60">
                        <div>
                            <h3 class="font-black text-sm text-slate-900">Ganti Kata Sandi Akun</h3>
                            <p class="text-[11px] text-slate-500 font-semibold">Perbarui kata sandi login sistem PRISMA Anda</p>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-200">
                            Keamanan
                        </span>
                    </div>

                    <form id="password-form" method="POST" action="{{ route('profile.update-password') }}" class="p-5 space-y-3.5">
                        @csrf
                        @method('PUT')

                        <div class="space-y-1">
                            <label class="block text-xs font-black text-slate-800">
                                Kata Sandi Saat Ini <span class="text-rose-500">*</span>
                            </label>
                            <input type="password" name="current_password" required
                                   placeholder="Kata sandi saat ini atau NIDN"
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-slate-900 focus:border-slate-900 transition">
                            <p class="text-[10px] text-slate-500">Gunakan nomor NIDN Anda jika belum pernah diubah.</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div class="space-y-1">
                                <label class="block text-xs font-black text-slate-800">
                                    Kata Sandi Baru <span class="text-rose-500">*</span>
                                </label>
                                <input type="password" name="password" required minlength="6"
                                       placeholder="Minimal 6 karakter"
                                       class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-slate-900 focus:border-slate-900 transition">
                            </div>

                            <div class="space-y-1">
                                <label class="block text-xs font-black text-slate-800">
                                    Konfirmasi Kata Sandi Baru <span class="text-rose-500">*</span>
                                </label>
                                <input type="password" name="password_confirmation" required minlength="6"
                                       placeholder="Ketik ulang kata sandi baru"
                                       class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-slate-900 focus:border-slate-900 transition">
                            </div>
                        </div>

                        <!-- Satu-satunya Tombol Simpan Kata Sandi (Jelas & Eksklusif) -->
                        <div class="pt-3 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                            <span class="text-[11px] text-slate-500 font-semibold">
                                Kata sandi dienkripsi dengan standar hash bcrypt
                            </span>
                            <button type="submit"
                                    style="background-color: #681727 !important; color: #ffffff !important;"
                                    class="w-full sm:w-auto px-6 py-2 rounded-xl font-black text-xs shadow-md transition flex items-center justify-center gap-2 cursor-pointer hover:opacity-90">
                                <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                <span style="color: #ffffff !important;">Simpan Kata Sandi Baru</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Edit Akun Pengguna - Admin P3M')

@section('content')
<div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <x-sidebar />

    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        <!-- Topbar Header -->
        <header class="bg-white border-b border-slate-200 py-4 sticky top-0 z-30 shadow-xs">
            <div class="w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center shrink-0 transition" title="Kembali" aria-label="Kembali">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </a>
                    <div>
                        <h1 class="font-extrabold text-xl text-slate-900 leading-tight">Edit Akun Pengguna</h1>
                        <p class="text-xs font-semibold text-slate-500">Perbarui data, penugasan peran, dan reset password untuk {{ $user->name }}</p>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-grow w-full px-4 sm:px-6 lg:px-8 py-8">
            @if($errors->any())
                <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-300 text-rose-900 text-sm font-bold space-y-1 shadow-xs">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Terjadi kesalahan pengisian formulir:</span>
                    </div>
                    <ul class="list-disc list-inside text-xs pl-7 text-rose-800">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="p-6 sm:p-8 space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Identitas Dasar -->
                    <div class="space-y-4">
                        <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider pb-2 border-b border-slate-100 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#681727]"></span>
                            Informasi Identitas &amp; Akun
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Nama Lengkap &amp; Gelar <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-[#681727]">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    NIDN / NIM Resmi
                                </label>
                                <input type="text" name="nidn_nim" value="{{ old('nidn_nim', $user->nidn_nim) }}"
                                       placeholder="Contoh: 0617029201"
                                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-mono font-semibold text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-[#681727]">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Alamat Email Aktif <span class="text-rose-500">*</span>
                                </label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-[#681727]">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Reset Kata Sandi Baru
                                </label>
                                <input type="password" name="password" minlength="6"
                                       placeholder="Kosongkan jika tidak ingin mengubah password"
                                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-[#681727]">
                                <p class="text-[10px] text-slate-400 mt-1">Kosongkan jika kata sandi tidak ingin diubah.</p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Nomor WhatsApp / HP
                                </label>
                                <input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}"
                                       placeholder="081234567890"
                                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-[#681727]">
                            </div>
                        </div>
                    </div>

                    <!-- Afiliasi Akademik -->
                    <div class="space-y-4 pt-4">
                        <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider pb-2 border-b border-slate-100 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            Afiliasi &amp; Jabatan Akademik
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Fakultas
                                </label>
                                <select name="id_fakultas" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 focus:outline-hidden focus:ring-2 focus:ring-[#681727]">
                                    <option value="">Pilih Fakultas</option>
                                    @foreach($fakultas as $f)
                                        <option value="{{ $f->id_fakultas }}" {{ old('id_fakultas', $user->id_fakultas) == $f->id_fakultas ? 'selected' : '' }}>
                                            {{ $f->nama_fakultas }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Program Studi
                                </label>
                                <select name="id_prodi" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 focus:outline-hidden focus:ring-2 focus:ring-[#681727]">
                                    <option value="">Pilih Program Studi</option>
                                    @foreach($prodis as $p)
                                        <option value="{{ $p->id_prodi }}" {{ old('id_prodi', $user->id_prodi) == $p->id_prodi ? 'selected' : '' }}>
                                            {{ $p->nama_prodi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Jabatan Fungsional
                                </label>
                                <select name="jabatan_fungsional" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 focus:outline-hidden focus:ring-2 focus:ring-[#681727]">
                                    <option value="">Pilih Jabatan</option>
                                    @php $jafungVal = old('jabatan_fungsional', $user->jabatan_fungsional); @endphp
                                    <option value="Tenaga Pengajar" {{ $jafungVal === 'Tenaga Pengajar' ? 'selected' : '' }}>Tenaga Pengajar</option>
                                    <option value="Asisten Ahli" {{ $jafungVal === 'Asisten Ahli' ? 'selected' : '' }}>Asisten Ahli</option>
                                    <option value="Lektor" {{ $jafungVal === 'Lektor' ? 'selected' : '' }}>Lektor</option>
                                    <option value="Lektor Kepala" {{ $jafungVal === 'Lektor Kepala' ? 'selected' : '' }}>Lektor Kepala</option>
                                    <option value="Guru Besar" {{ $jafungVal === 'Guru Besar' ? 'selected' : '' }}>Guru Besar</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    SINTA ID
                                </label>
                                <input type="text" name="sinta_id" value="{{ old('sinta_id', $user->sinta_id) }}"
                                       placeholder="Contoh: 6753189"
                                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-mono font-semibold text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-[#681727]">
                            </div>
                        </div>
                    </div>

                    <!-- Penetapan Role -->
                    <div class="space-y-4 pt-4">
                        <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider pb-2 border-b border-slate-100 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                            Penetapan Peran (Multi-Role) <span class="text-rose-500">*</span>
                        </h3>

                        @php $userRoleIds = old('roles', $user->roles->pluck('id')->toArray()); @endphp
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            @foreach($roles as $r)
                                <label class="p-3 rounded-2xl border border-slate-200 hover:border-[#681727] cursor-pointer transition flex items-start gap-2.5 bg-slate-50/60">
                                    <input type="checkbox" name="roles[]" value="{{ $r->id }}"
                                           {{ in_array($r->id, $userRoleIds) ? 'checked' : '' }}
                                           class="mt-0.5 rounded-sm text-[#681727] focus:ring-[#681727]">
                                    <div>
                                        <span class="text-xs font-bold text-slate-900 block leading-tight">{{ $r->name }}</span>
                                        <span class="text-[10px] text-slate-500 leading-tight block mt-0.5">{{ Str::limit($r->description, 35) }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                        <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                            Batal
                        </a>
                        <button type="submit"
                                style="background-color: #681727 !important; color: #ffffff !important;"
                                class="px-6 py-2.5 rounded-xl text-white font-extrabold text-xs shadow-xs transition flex items-center gap-2 hover:opacity-90 cursor-pointer">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span style="color: #ffffff !important;">Perbarui Data Akun</span>
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
@endsection


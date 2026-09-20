@extends('layouts.app')

@section('title', 'Pendaftaran Perolehan HKI - Sentra HKI UHN')

@section('content')
<div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <x-sidebar />

    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        <header class="bg-white border-b border-slate-200 py-4 sticky top-0 z-30 shadow-xs">
            <div class="w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <a href="{{ route('hki.index') }}" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center shrink-0 transition" title="Kembali" aria-label="Kembali">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </a>
                    <div>
                        <h1 class="font-extrabold text-xl text-slate-900 leading-tight">Pendaftaran Perolehan HKI</h1>
                        <p class="text-xs font-semibold text-slate-500">Sentra HKI UHN &bull; Integrasi Verifikasi Pangkalan Data DJKI (US-11.2)</p>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-grow w-full px-4 sm:px-6 lg:px-8 py-8">
            @if($errors->any())
                <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-300 text-rose-900 text-xs font-bold space-y-1 shadow-xs">
                    <div class="font-extrabold flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Periksa kembali isian formulir pendaftaran HKI:</span>
                    </div>
                    <ul class="list-disc list-inside pl-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('hki.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- DJKI Live Parser Information Notice -->
                <div class="p-4.5 rounded-2xl bg-indigo-50/80 border border-indigo-200 text-indigo-950 text-xs flex items-start gap-3 shadow-xs">
                    <div class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="space-y-1">
                        <h4 class="font-extrabold text-indigo-900 text-sm">Verifikasi DJKI Otomatis &amp; Manual Sentra HKI</h4>
                        <p class="text-indigo-800 leading-relaxed font-semibold">
                            Sistem akan secara otomatis memeriksa keabsahan Nomor Permohonan ke pangkalan data DJKI Kemenkumham melalui <code>DjkiClient</code>. Jika sistem DJKI sedang dalam pemeliharaan, berkas Anda akan dialihkan secara mulus ke antrean verifikasi manual staf Sentra HKI UHN.
                        </p>
                    </div>
                </div>

                <!-- Form Fields -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs space-y-4">
                    <div class="border-b border-slate-100 pb-3">
                        <h2 class="font-extrabold text-sm text-slate-900">Identitas Hak Kekayaan Intelektual</h2>
                        <p class="text-xs font-semibold text-slate-500">Rincian jenis perolehan, nomor registrasi, dan kepemilikan hak</p>
                    </div>

                    <!-- Pilihan Kategori: KI / Paten / HKI -->
                    <div x-data="{ kategoriPilihan: '{{ old('jenis_hki') === 'Hak Cipta' ? 'hki' : (str_contains(old('jenis_hki', ''), 'Paten') ? 'paten' : 'ki') }}' }">
                        <label class="block text-xs font-bold text-slate-700 mb-2">
                            Pilih Kategori Permohonan: KI / Paten / HKI <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
                            <div @click="kategoriPilihan = 'paten'; $refs.selectJenis.value = 'Paten'"
                                 :class="kategoriPilihan === 'paten' ? 'border-indigo-600 bg-indigo-50/70 shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-white'"
                                 class="cursor-pointer p-3.5 rounded-2xl border-2 transition text-left flex flex-col justify-between">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-indigo-200 text-indigo-900">PATEN</span>
                                    <span :class="kategoriPilihan === 'paten' ? 'bg-indigo-600 border-indigo-600 text-white' : 'border-slate-300'" class="w-4 h-4 rounded-full border flex items-center justify-center text-[10px]">✓</span>
                                </div>
                                <h4 class="font-extrabold text-xs text-slate-900">Paten &amp; Paten Sederhana</h4>
                                <p class="text-[11px] text-slate-500 mt-1">Invensi teknologi, metode, alat, atau formulasi baru yang memiliki kebaruan ilmiah.</p>
                            </div>

                            <div @click="kategoriPilihan = 'hki'; $refs.selectJenis.value = 'Hak Cipta'"
                                 :class="kategoriPilihan === 'hki' ? 'border-indigo-600 bg-indigo-50/70 shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-white'"
                                 class="cursor-pointer p-3.5 rounded-2xl border-2 transition text-left flex flex-col justify-between">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-blue-200 text-blue-900">HKI</span>
                                    <span :class="kategoriPilihan === 'hki' ? 'bg-indigo-600 border-indigo-600 text-white' : 'border-slate-300'" class="w-4 h-4 rounded-full border flex items-center justify-center text-[10px]">✓</span>
                                </div>
                                <h4 class="font-extrabold text-xs text-slate-900">Hak Cipta (HKI)</h4>
                                <p class="text-[11px] text-slate-500 mt-1">Karya tulis, monograf, buku ajar, aplikasi perangkat lunak (source code), dan modul.</p>
                            </div>

                            <div @click="kategoriPilihan = 'ki'; $refs.selectJenis.value = 'Desain Industri'"
                                 :class="kategoriPilihan === 'ki' ? 'border-indigo-600 bg-indigo-50/70 shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-white'"
                                 class="cursor-pointer p-3.5 rounded-2xl border-2 transition text-left flex flex-col justify-between">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-emerald-200 text-emerald-900">KI LAINNYA</span>
                                    <span :class="kategoriPilihan === 'ki' ? 'bg-indigo-600 border-indigo-600 text-white' : 'border-slate-300'" class="w-4 h-4 rounded-full border flex items-center justify-center text-[10px]">✓</span>
                                </div>
                                <h4 class="font-extrabold text-xs text-slate-900">Desain Industri &amp; Merk</h4>
                                <p class="text-[11px] text-slate-500 mt-1">Kreasi bentuk, konfigurasi 3 dimensi, rancangan grafis kemasan produk, dan merk dagang.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Jenis Spesifik Hak Kekayaan Intelektual <span class="text-rose-500">*</span>
                                </label>
                                <select x-ref="selectJenis" name="jenis_hki" required
                                        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-blue-500 bg-white">
                                    <option value="">-- Pilih Jenis HKI --</option>
                                    <option value="Paten" {{ old('jenis_hki') == 'Paten' ? 'selected' : '' }}>Paten Biasa (Invensi Teknologi)</option>
                                    <option value="Paten Sederhana" {{ old('jenis_hki') == 'Paten Sederhana' ? 'selected' : '' }}>Paten Sederhana</option>
                                    <option value="Hak Cipta" {{ old('jenis_hki', 'Hak Cipta') == 'Hak Cipta' ? 'selected' : '' }}>Hak Cipta (Karya Tulis / Perangkat Lunak / HKI)</option>
                                    <option value="Desain Industri" {{ old('jenis_hki') == 'Desain Industri' ? 'selected' : '' }}>Desain Industri</option>
                                    <option value="Merk Dagang" {{ old('jenis_hki') == 'Merk Dagang' ? 'selected' : '' }}>Merk Dagang</option>
                                    <option value="Rahasia Dagang" {{ old('jenis_hki') == 'Rahasia Dagang' ? 'selected' : '' }}>Rahasia Dagang</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Lembaga Pemegang Hak Cipta/Paten <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="pemegang_hak" value="{{ old('pemegang_hak', 'Universitas Harkat Negeri') }}" required
                                       class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Judul Ciptaan / Invensi / Desain <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="judul_hki" rows="3" required
                                  placeholder="Contoh: Sistem Otomatisasi Irigasi Berbasis Internet of Things (IoT) pada Lahan Pertanian Dataran Tinggi"
                                  class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-blue-500">{{ old('judul_hki') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Nomor Permohonan DJKI <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="nomor_permohonan" value="{{ old('nomor_permohonan') }}" required
                                   placeholder="Contoh: EC00202612345 atau P00202600123"
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-blue-500">
                            <p class="text-[11px] text-slate-400 mt-1 font-semibold">Nomor registrasi pendaftaran pada portal resmi DJKI.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Nomor Sertifikat / Pencatatan (Jika Sudah Terbit)
                            </label>
                            <input type="text" name="nomor_sertifikat" value="{{ old('nomor_sertifikat') }}"
                                   placeholder="Contoh: 000456789 / IDP000012345"
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Tanggal Permohonan <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" name="tanggal_permohonan" value="{{ old('tanggal_permohonan', date('Y-m-d')) }}" required
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Tanggal Terbit Sertifikat (Jika Ada)
                            </label>
                            <input type="date" name="tanggal_terbit" value="{{ old('tanggal_terbit') }}"
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <!-- File Uploads & Berkas Pendukung -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs space-y-5">
                    <div class="border-b border-slate-100 pb-3">
                        <h2 class="font-extrabold text-sm text-slate-900">Berkas Permohonan &amp; Dokumen Pendukung</h2>
                        <p class="text-xs font-semibold text-slate-500">Unggah berkas karya cipta, surat pernyataan, dan surat pengalihan hak sesuai format standar Sentra HKI UHN</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Manual Book / Bukti Pendukung Karya <span class="text-slate-400 font-normal">(PDF / Dokumen)</span>
                            </label>
                            <input type="file" name="file_manual_book" accept=".pdf,.doc,.docx,.zip,.rar"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-blue-500 bg-slate-50/50 file:mr-3 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="text-[11px] text-slate-400 mt-1 font-semibold">Buku panduan, modul, naskah ciptaan, atau tangkapan layar sistem (Maks. 15 MB).</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Surat Pernyataan Keaslian Karya (PDF)
                            </label>
                            <input type="file" name="file_surat_pernyataan" accept=".pdf,application/pdf"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-blue-500 bg-slate-50/50 file:mr-3 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="text-[11px] text-slate-400 mt-1 font-semibold">Surat pernyataan keaslian bermaterai (Maks. 10 MB).</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Surat Pengalihan Hak Cipta / Invensi (PDF)
                            </label>
                            <input type="file" name="file_surat_pengalihan" accept=".pdf,application/pdf"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-blue-500 bg-slate-50/50 file:mr-3 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="text-[11px] text-slate-400 mt-1 font-semibold">Surat pengalihan hak kepada UHN (Maks. 10 MB).</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Sertifikat DJKI / Bukti Pendaftaran (PDF) <span class="text-slate-400 font-normal">(Opsional)</span>
                            </label>
                            <input type="file" name="file_sertifikat" accept=".pdf,application/pdf"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-blue-500 bg-slate-50/50 file:mr-3 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="text-[11px] text-slate-400 mt-1 font-semibold">Jika sertifikat sudah terbit dari Kemenkumham (Maks. 10 MB).</p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('hki.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Daftarkan ke Sentra HKI</span>
                    </button>
                </div>
            </form>
        </main>
    </div>
</div>
@endsection


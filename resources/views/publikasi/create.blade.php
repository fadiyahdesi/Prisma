@extends('layouts.app')

@section('title', 'Daftarkan Publikasi Jurnal - Bank Publikasi PRISMA UHN')

@section('content')
<div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <x-sidebar />

    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        <header class="bg-white border-b border-slate-200 py-4 sticky top-0 z-30 shadow-xs">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <h1 class="font-extrabold text-xl text-slate-900 leading-tight">Pendaftaran Artikel Jurnal</h1>
                        <p class="text-xs font-semibold text-slate-500">Bank Publikasi Ilmiah &bull; Tarik Otomatis Metadata Crossref / SINTA (US-11.1)</p>
                    </div>
                </div>
                <div>
                    <a href="{{ route('publikasi.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                        Kembali
                    </a>
                </div>
            </div>
        </header>

        <main class="flex-grow max-w-4xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8"
              x-data="{
                  doi: '{{ old('doi', '') }}',
                  judul: '{{ old('judul_artikel', '') }}',
                  jurnal: '{{ old('nama_jurnal', '') }}',
                  issn: '{{ old('issn', '') }}',
                  tahun: '{{ old('tahun_terbit', date('Y')) }}',
                  volume: '{{ old('volume_nomor', '') }}',
                  urlArtikel: '{{ old('url_artikel', '') }}',
                  jumlahPenulis: '{{ old('jumlah_penulis', 1) }}',
                  metadataSource: '{{ old('metadata_source', 'manual') }}',
                  loading: false,
                  feedbackMessage: '',
                  feedbackType: '',
                  detectedAuthors: [],
                  async fetchMetadata() {
                      if (!this.doi.trim()) {
                          this.feedbackMessage = 'Silakan masukkan kode DOI artikel terlebih dahulu.';
                          this.feedbackType = 'error';
                          return;
                      }
                      this.loading = true;
                      this.feedbackMessage = '';
                      try {
                          const res = await fetch(`{{ route('publikasi.fetch-doi') }}?doi=${encodeURIComponent(this.doi.trim())}`);
                          const data = await res.json();
                          if (res.status === 409) {
                              this.feedbackType = 'error';
                              this.feedbackMessage = data.message || 'DOI ini sudah terdaftar dalam sistem.';
                          } else if (data.success) {
                              this.judul = data.judul_artikel || this.judul;
                              this.jurnal = data.nama_jurnal || this.jurnal;
                              this.issn = data.issn || this.issn;
                              this.tahun = data.tahun_terbit || this.tahun;
                              this.volume = data.volume_nomor || this.volume;
                              this.urlArtikel = data.url_artikel || this.urlArtikel;
                              this.jumlahPenulis = data.jumlah_penulis || this.jumlahPenulis;
                              this.detectedAuthors = data.authors || [];
                              this.metadataSource = 'crossref';
                              this.feedbackType = 'success';
                              this.feedbackMessage = `Metadata berhasil ditarik dari Crossref REST API (${this.detectedAuthors.length} penulis terdeteksi).`;
                          } else {
                              this.feedbackType = 'warning';
                              this.feedbackMessage = data.message || 'Data tidak ditemukan di Crossref. Silakan lengkapi data naskah secara manual.';
                          }
                      } catch (err) {
                          this.feedbackType = 'warning';
                          this.feedbackMessage = 'Gagal menghubungi server Crossref. Anda dapat mengisi form secara manual.';
                      } finally {
                          this.loading = false;
                      }
                  }
              }">

            @if($errors->any())
                <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-300 text-rose-900 text-xs font-bold space-y-1 shadow-xs">
                    <div class="font-extrabold flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Periksa kembali isian formulir:</span>
                    </div>
                    <ul class="list-disc list-inside pl-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('publikasi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <input type="hidden" name="metadata_source" :value="metadataSource">

                <!-- Section 1: Integrasi DOI & Crossref -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs space-y-4">
                    <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                        <div>
                            <h2 class="font-extrabold text-sm text-slate-900">Validasi Keunikan DOI &amp; Penarikan Metadata</h2>
                            <p class="text-xs font-semibold text-slate-500">Mencegah duplikasi artikel dan menarik metadata resmi penerbit internasional secara instan</p>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-purple-100 text-purple-800 border border-purple-200">
                            Crossref REST API
                        </span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Digital Object Identifier (DOI) <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex flex-col sm:flex-row gap-2">
                            <div class="flex-1 relative">
                                <span class="absolute left-3.5 top-2.5 text-xs font-bold text-slate-400 select-none">doi:</span>
                                <input type="text" name="doi" x-model="doi" placeholder="10.1016/j.future.2025.10.001" required
                                       class="w-full pl-12 pr-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-blue-500">
                            </div>
                            <button type="button" @click="fetchMetadata()" :disabled="loading"
                                    class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 disabled:opacity-50 text-white font-bold text-xs transition flex items-center justify-center gap-2 shrink-0 shadow-xs">
                                <template x-if="loading">
                                    <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                </template>
                                <template x-if="!loading">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                </template>
                                <span x-text="loading ? 'Menghubungkan...' : 'Tarik Metadata Crossref'"></span>
                            </button>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1 font-semibold">Dapat berupa format DOI murni (contoh: <code>10.1109/ACCESS.2024.123456</code>) atau tautan URL <code>https://doi.org/...</code></p>
                    </div>

                    <!-- Feedback Alert -->
                    <template x-if="feedbackMessage">
                        <div :class="{
                            'p-3.5 rounded-xl text-xs font-bold flex items-start gap-2.5': true,
                            'bg-emerald-50 border border-emerald-300 text-emerald-900': feedbackType === 'success',
                            'bg-rose-50 border border-rose-300 text-rose-900': feedbackType === 'error',
                            'bg-amber-50 border border-amber-300 text-amber-900': feedbackType === 'warning'
                        }">
                            <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="flex-1">
                                <span x-text="feedbackMessage"></span>
                                <template x-if="detectedAuthors.length > 0">
                                    <div class="mt-1 text-[11px] text-slate-600">
                                        <strong>Penulis terdeteksi:</strong> <span x-text="detectedAuthors.join(', ')"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Section 2: Informasi Artikel & Jurnal -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs space-y-4">
                    <div class="border-b border-slate-100 pb-3">
                        <h2 class="font-extrabold text-sm text-slate-900">Metadata Publikasi</h2>
                        <p class="text-xs font-semibold text-slate-500">Lengkapi atau sesuaikan rincian identitas publikasi ilmiah</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Judul Lengkap Artikel <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="judul_artikel" x-model="judul" rows="3" required
                                  placeholder="Contoh: Optimizing Machine Learning Architectures for Autonomous UAV Fleet Routing"
                                  class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Nama Jurnal / Konferensi <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="nama_jurnal" x-model="jurnal" required
                                   placeholder="Contoh: IEEE Access / Journal of Systems Architecture"
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Kategori Peringkat Jurnal <span class="text-rose-500">*</span>
                            </label>
                            <select name="kategori_peringkat" required
                                    class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-blue-500 bg-white">
                                <option value="">-- Pilih Peringkat --</option>
                                <optgroup label="Indeks Scopus / Internasional Bereputasi">
                                    <option value="Scopus Q1" {{ old('kategori_peringkat') == 'Scopus Q1' ? 'selected' : '' }}>Scopus Q1</option>
                                    <option value="Scopus Q2" {{ old('kategori_peringkat') == 'Scopus Q2' ? 'selected' : '' }}>Scopus Q2</option>
                                    <option value="Scopus Q3" {{ old('kategori_peringkat') == 'Scopus Q3' ? 'selected' : '' }}>Scopus Q3</option>
                                    <option value="Scopus Q4" {{ old('kategori_peringkat') == 'Scopus Q4' ? 'selected' : '' }}>Scopus Q4</option>
                                    <option value="Internasional Terindeks Lainnya" {{ old('kategori_peringkat') == 'Internasional Terindeks Lainnya' ? 'selected' : '' }}>Internasional Lainnya (Non-Q)</option>
                                </optgroup>
                                <optgroup label="Akreditasi Nasional SINTA (Kemdiktisaintek)">
                                    <option value="SINTA 1" {{ old('kategori_peringkat') == 'SINTA 1' ? 'selected' : '' }}>SINTA 1</option>
                                    <option value="SINTA 2" {{ old('kategori_peringkat') == 'SINTA 2' ? 'selected' : '' }}>SINTA 2</option>
                                    <option value="SINTA 3" {{ old('kategori_peringkat') == 'SINTA 3' ? 'selected' : '' }}>SINTA 3</option>
                                    <option value="SINTA 4" {{ old('kategori_peringkat') == 'SINTA 4' ? 'selected' : '' }}>SINTA 4</option>
                                    <option value="Nasional Terakreditasi" {{ old('kategori_peringkat') == 'Nasional Terakreditasi' ? 'selected' : '' }}>Nasional Terakreditasi Lainnya</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                ISSN / e-ISSN
                            </label>
                            <input type="text" name="issn" x-model="issn"
                                   placeholder="Contoh: 2169-3536"
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Tahun Terbit <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" name="tahun_terbit" x-model="tahun" min="2000" max="{{ date('Y') + 1 }}" required
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Volume &amp; Nomor
                            </label>
                            <input type="text" name="volume_nomor" x-model="volume"
                                   placeholder="Contoh: Vol. 12 No. 4"
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                URL Tautan Artikel Terbit
                            </label>
                            <input type="url" name="url_artikel" x-model="urlArtikel"
                                   placeholder="https://ieeexplore.ieee.org/document/..."
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Jumlah Total Penulis <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" name="jumlah_penulis" x-model="jumlahPenulis" min="1" max="50" required
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-blue-500">
                            <p class="text-[11px] text-slate-400 mt-1 font-semibold">Termasuk penulis pertama, korespondensi, dan anggota</p>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Unggah Berkas Naskah PDF -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs space-y-4">
                    <div class="border-b border-slate-100 pb-3">
                        <h2 class="font-extrabold text-sm text-slate-900">Penyimpanan Naskah Artikel (Reposititori)</h2>
                        <p class="text-xs font-semibold text-slate-500">Unggah berkas naskah lengkap artikel jurnal (Full Paper PDF)</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Berkas Naskah Artikel (PDF) <span class="text-rose-500">*</span>
                        </label>
                        <input type="file" name="file_naskah" accept=".pdf,application/pdf" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-blue-500 bg-slate-50/50 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="text-[11px] text-slate-400 mt-1 font-semibold">Format file wajib PDF, ukuran maksimal 10 MB.</p>
                    </div>
                </div>

                <!-- Submit Action -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('publikasi.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Simpan ke Bank Publikasi</span>
                    </button>
                </div>
            </form>
        </main>
    </div>
</div>
@endsection


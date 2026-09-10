@extends('layouts.app')

@section('title', 'Form Pengajuan Klaim Reward Insentif - PRISMA UHN')

@section('content')
<div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <x-sidebar />

    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        <header class="bg-white border-b border-slate-200 py-4 sticky top-0 z-30 shadow-xs">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <h1 class="font-extrabold text-xl text-slate-900 leading-tight">Pengajuan Klaim Reward Insentif</h1>
                        <p class="text-xs font-semibold text-slate-500">Matriks SK Rektor UHN &amp; Mesin Distribusi Multi-Penulis 100% (US-11.3 &amp; US-11.4)</p>
                    </div>
                </div>
                <div>
                    <a href="{{ route('reward.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                        Kembali
                    </a>
                </div>
            </div>
        </header>

        <main class="flex-grow max-w-6xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8"
              x-data="{
                  jenisKlaim: '{{ request('jenis', 'Publikasi') }}',
                  selectedAssetId: '{{ request('id', '') }}',
                  kategoriInsentif: '',
                  tarifNominal: 0,
                  nomorSkRektor: 'SK-REKTOR/UHN/2026/015',
                  tarifMatrix: {{ $tarifMatrix->toJson() }},
                  publikasiList: {{ $publikasiList->toJson() }},
                  hkiList: {{ $hkiList->toJson() }},
                  distribusi: [
                      {
                          nama_penulis: '{{ addslashes($user->name) }}',
                          nidn_nim: '{{ $user->nidn ?? '' }}',
                          email: '{{ $user->email }}',
                          peran_penulis: 'Penulis Pertama & Korespondensi',
                          persentase: 100,
                          nama_bank: 'BNI',
                          nomor_rekening: '',
                          nama_pemilik_rekening: '{{ addslashes($user->name) }}'
                      }
                  ],
                  init() {
                      if (this.selectedAssetId) {
                          this.onAssetChanged();
                      }
                  },
                  onJenisChanged() {
                      this.selectedAssetId = '';
                      this.kategoriInsentif = '';
                      this.tarifNominal = 0;
                  },
                  onAssetChanged() {
                      if (!this.selectedAssetId) {
                          this.kategoriInsentif = '';
                          this.tarifNominal = 0;
                          return;
                      }
                      if (this.jenisKlaim === 'Publikasi') {
                          const pub = this.publikasiList.find(p => p.id == this.selectedAssetId);
                          if (pub) {
                              this.kategoriInsentif = pub.kategori_peringkat;
                              this.lookupTarif('Publikasi', pub.kategori_peringkat);
                          }
                      } else {
                          const hki = this.hkiList.find(h => h.id == this.selectedAssetId);
                          if (hki) {
                              this.kategoriInsentif = hki.jenis_hki;
                              this.lookupTarif('HKI', hki.jenis_hki);
                          }
                      }
                  },
                  lookupTarif(kategori, sub) {
                      const match = this.tarifMatrix.find(t => t.kategori === kategori && t.sub_kategori === sub);
                      if (match) {
                          this.tarifNominal = parseFloat(match.nominal_insentif);
                          this.nomorSkRektor = match.nomor_sk_rektor || 'SK-REKTOR/UHN/2026/015';
                      } else {
                          this.tarifNominal = 0;
                      }
                  },
                  addAuthor() {
                      this.distribusi.push({
                          nama_penulis: '',
                          nidn_nim: '',
                          email: '',
                          peran_penulis: 'Penulis Anggota',
                          persentase: 0,
                          nama_bank: 'BNI',
                          nomor_rekening: '',
                          nama_pemilik_rekening: ''
                      });
                  },
                  removeAuthor(index) {
                      if (this.distribusi.length > 1) {
                          this.distribusi.splice(index, 1);
                      }
                  },
                  totalPersentase() {
                      let sum = 0;
                      this.distribusi.forEach(d => {
                          sum += parseFloat(d.persentase || 0);
                      });
                      return Math.round(sum * 100) / 100;
                  },
                  isExactly100() {
                      return Math.abs(this.totalPersentase() - 100.0) < 0.01;
                  },
                  formatRupiah(num) {
                      return new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(num || 0);
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

            <form action="{{ route('reward.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Section 1: Pemilihan Aset (Publikasi / HKI) -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs space-y-5">
                    <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                        <div>
                            <h2 class="font-extrabold text-sm text-slate-900">1. Pemilihan Aset Ilmiah &amp; Tarif SK Rektor UHN</h2>
                            <p class="text-xs font-semibold text-slate-500">Pilih artikel jurnal atau sertifikat HKI yang telah diverifikasi dan belum pernah diklaim</p>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-blue-100 text-blue-800 border border-blue-200">
                            Anti-Duplicate Claim Lock
                        </span>
                    </div>

                    <!-- Radio Jenis Klaim -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Jenis Klaim Reward <span class="text-rose-500">*</span></label>
                        <div class="grid grid-cols-2 gap-3 max-w-md">
                            <label :class="jenisKlaim === 'Publikasi' ? 'border-blue-600 bg-blue-50/60 text-blue-900' : 'border-slate-200 hover:bg-slate-50 text-slate-700'"
                                   class="p-3.5 rounded-xl border-2 cursor-pointer flex items-center gap-3 transition">
                                <input type="radio" name="jenis_klaim" value="Publikasi" x-model="jenisKlaim" @change="onJenisChanged()" class="sr-only">
                                <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center shrink-0" :class="jenisKlaim === 'Publikasi' ? 'border-blue-600' : 'border-slate-300'">
                                    <div class="w-2 h-2 rounded-full bg-blue-600" x-show="jenisKlaim === 'Publikasi'"></div>
                                </div>
                                <span class="text-xs font-extrabold">Publikasi Jurnal</span>
                            </label>

                            <label :class="jenisKlaim === 'HKI' ? 'border-indigo-600 bg-indigo-50/60 text-indigo-900' : 'border-slate-200 hover:bg-slate-50 text-slate-700'"
                                   class="p-3.5 rounded-xl border-2 cursor-pointer flex items-center gap-3 transition">
                                <input type="radio" name="jenis_klaim" value="HKI" x-model="jenisKlaim" @change="onJenisChanged()" class="sr-only">
                                <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center shrink-0" :class="jenisKlaim === 'HKI' ? 'border-indigo-600' : 'border-slate-300'">
                                    <div class="w-2 h-2 rounded-full bg-indigo-600" x-show="jenisKlaim === 'HKI'"></div>
                                </div>
                                <span class="text-xs font-extrabold">Sentra HKI (Paten/Cipta)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Dropdown Aset Publikasi -->
                    <template x-if="jenisKlaim === 'Publikasi'">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Pilih Artikel dari Bank Publikasi Anda <span class="text-rose-500">*</span>
                            </label>
                            <select name="id_publikasi" x-model="selectedAssetId" @change="onAssetChanged()" required
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-blue-500 bg-white">
                                <option value="">-- Pilih Artikel Publikasi (Hanya yang Belum Diklaim) --</option>
                                <template x-for="pub in publikasiList" :key="pub.id">
                                    <option :value="pub.id" x-text="`[${pub.kategori_peringkat}] ${pub.judul_artikel} (${pub.nama_jurnal}, ${pub.tahun_terbit})`"></option>
                                </template>
                            </select>
                            <template x-if="publikasiList.length === 0">
                                <p class="text-[11px] text-amber-600 font-bold mt-1">
                                    Tidak ada artikel publikasi yang dapat diklaim. Silakan daftarkan artikel pada <a href="{{ route('publikasi.create') }}" class="underline">Bank Publikasi</a> terlebih dahulu.
                                </p>
                            </template>
                        </div>
                    </template>

                    <!-- Dropdown Aset HKI -->
                    <template x-if="jenisKlaim === 'HKI'">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Pilih Sertifikat HKI Terverifikasi <span class="text-rose-500">*</span>
                            </label>
                            <select name="id_hki" x-model="selectedAssetId" @change="onAssetChanged()" required
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-blue-500 bg-white">
                                <option value="">-- Pilih HKI (Hanya yang Berstatus Terverifikasi HKI) --</option>
                                <template x-for="hki in hkiList" :key="hki.id">
                                    <option :value="hki.id" x-text="`[${hki.jenis_hki}] ${hki.judul_hki} (No: ${hki.nomor_permohonan})`"></option>
                                </template>
                            </select>
                            <template x-if="hkiList.length === 0">
                                <p class="text-[11px] text-amber-600 font-bold mt-1">
                                    Tidak ada berkas HKI yang berstatus 'Terverifikasi HKI'. Pastikan pengajuan Anda telah diverifikasi oleh Sentra HKI.
                                </p>
                            </template>
                        </div>
                    </template>

                    <!-- Rate & Matrix Info Box -->
                    <input type="hidden" name="kategori_insentif" :value="kategoriInsentif">

                    <template x-if="tarifNominal > 0">
                        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-950 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black bg-emerald-200 text-emerald-900" x-text="nomorSkRektor"></span>
                                    <span class="text-xs font-bold text-emerald-800" x-text="`Kategori: ${kategoriInsentif}`"></span>
                                </div>
                                <h3 class="text-sm font-extrabold text-emerald-900">Total Tarif Dasar Insentif SK Rektor</h3>
                            </div>
                            <div class="text-left sm:text-right">
                                <div class="text-2xl font-black text-emerald-700 tracking-tight">
                                    <span class="text-sm font-bold text-emerald-600 mr-1">Rp</span><span x-text="formatRupiah(tarifNominal)"></span>
                                </div>
                                <span class="text-[10px] font-bold text-emerald-700">100% Alokasi Anggaran Penghargaan</span>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Section 2: Mesin Distribusi Multi-Penulis (US-11.4) -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs space-y-5">
                    <div class="border-b border-slate-100 pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <h2 class="font-extrabold text-sm text-slate-900">2. Mesin Distribusi Rekening Multi-Penulis</h2>
                            <p class="text-xs font-semibold text-slate-500">Tentukan persentase bagian masing-masing penulis secara transparan. Wajib total tepat 100.00%</p>
                        </div>
                        <button type="button" @click="addAuthor()"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-xs transition self-start sm:self-auto">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Tambah Penulis</span>
                        </button>
                    </div>

                    <!-- Real-time 100% Validation Banner -->
                    <div class="p-4 rounded-2xl border flex items-center justify-between transition"
                         :class="isExactly100() ? 'bg-emerald-50 border-emerald-300 text-emerald-900' : 'bg-rose-50 border-rose-300 text-rose-900'">
                        <div class="flex items-center gap-3">
                            <template x-if="isExactly100()">
                                <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                            </template>
                            <template x-if="!isExactly100()">
                                <div class="w-8 h-8 rounded-full bg-rose-600 text-white flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                            </template>
                            <div>
                                <span class="text-xs font-bold block" x-text="isExactly100() ? 'Total Persentase Sesuai Ketentuan (Tepat 100.00%)' : 'Total Persentase Belum Tepat 100.00%'"></span>
                                <span class="text-[11px] font-semibold opacity-80" x-text="isExactly100() ? 'Pembagian insentif valid dan siap diajukan ke P3M.' : `Selisih: ${(100.0 - totalPersentase()).toFixed(2)}%. Sesuaikan persentase tiap penulis hingga bernilai tepat 100%.`"></span>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xl font-black tracking-tight" :class="isExactly100() ? 'text-emerald-700' : 'text-rose-700'">
                                <span x-text="totalPersentase().toFixed(2)"></span>%
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-wider block opacity-75">Akumulasi Pembagian</span>
                        </div>
                    </div>

                    <!-- Authors List -->
                    <div class="space-y-4">
                        <template x-for="(author, idx) in distribusi" :key="idx">
                            <div class="p-4.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3 relative">
                                <div class="flex items-center justify-between border-b border-slate-200/70 pb-2.5">
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-lg bg-slate-200 text-slate-700 text-xs font-black flex items-center justify-center" x-text="idx + 1"></span>
                                        <span class="font-extrabold text-xs text-slate-800" x-text="author.nama_penulis || `Penulis #${idx + 1}`"></span>
                                    </div>
                                    <template x-if="distribusi.length > 1">
                                        <button type="button" @click="removeAuthor(idx)" class="text-xs font-bold text-rose-600 hover:text-rose-800 transition">
                                            Hapus Penulis
                                        </button>
                                    </template>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                                    <div class="md:col-span-2">
                                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Nama Lengkap &amp; Gelar <span class="text-rose-500">*</span></label>
                                        <input type="text" :name="`distribusi[${idx}][nama_penulis]`" x-model="author.nama_penulis" required
                                               class="w-full px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-blue-500 bg-white">
                                    </div>

                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 mb-1">NIDN / NIM</label>
                                        <input type="text" :name="`distribusi[${idx}][nidn_nim]`" x-model="author.nidn_nim"
                                               class="w-full px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-blue-500 bg-white">
                                    </div>

                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Email</label>
                                        <input type="email" :name="`distribusi[${idx}][email]`" x-model="author.email"
                                               class="w-full px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-blue-500 bg-white">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                                    <div class="md:col-span-2">
                                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Peran Penulis <span class="text-rose-500">*</span></label>
                                        <select :name="`distribusi[${idx}][peran_penulis]`" x-model="author.peran_penulis" required
                                                class="w-full px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-blue-500 bg-white">
                                            <option value="Penulis Pertama &amp; Korespondensi">Penulis Pertama &amp; Korespondensi</option>
                                            <option value="Penulis Pertama">Penulis Pertama (First Author)</option>
                                            <option value="Penulis Korespondensi">Penulis Korespondensi (Corresponding Author)</option>
                                            <option value="Penulis Anggota">Penulis Anggota (Co-Author)</option>
                                            <option value="Ketua Inventor">Ketua Inventor (HKI)</option>
                                            <option value="Anggota Inventor">Anggota Inventor (HKI)</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Persentase (%) <span class="text-rose-500">*</span></label>
                                        <input type="number" step="0.01" min="0.01" max="100" :name="`distribusi[${idx}][persentase]`" x-model.number="author.persentase" required
                                               class="w-full px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-bold text-blue-700 focus:outline-hidden focus:ring-2 focus:ring-blue-500 bg-white">
                                    </div>

                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Nominal Bagian (Auto)</label>
                                        <div class="px-3 py-1.5 rounded-xl border border-slate-200 bg-slate-100 text-xs font-extrabold text-emerald-700 truncate">
                                            Rp <span x-text="formatRupiah(Math.round(((author.persentase || 0) / 100) * tarifNominal))"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bank & Rekening Penulis -->
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1 border-t border-slate-200/40">
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Nama Bank <span class="text-rose-500">*</span></label>
                                        <input type="text" :name="`distribusi[${idx}][nama_bank]`" x-model="author.nama_bank" required placeholder="BNI / BRI / Mandiri / BCA"
                                               class="w-full px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-blue-500 bg-white">
                                    </div>

                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Nomor Rekening <span class="text-rose-500">*</span></label>
                                        <input type="text" :name="`distribusi[${idx}][nomor_rekening]`" x-model="author.nomor_rekening" required placeholder="0123456789"
                                               class="w-full px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-blue-500 bg-white">
                                    </div>

                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Nama Pemilik Rekening <span class="text-rose-500">*</span></label>
                                        <input type="text" :name="`distribusi[${idx}][nama_pemilik_rekening]`" x-model="author.nama_pemilik_rekening" required placeholder="Harus sesuai buku tabungan"
                                               class="w-full px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-blue-500 bg-white">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Section 3: Surat Pernyataan Kesepakatan (PDF) -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs space-y-4">
                    <div class="border-b border-slate-100 pb-3">
                        <h2 class="font-extrabold text-sm text-slate-900">3. Surat Pernyataan Kesepakatan Pembagian Insentif</h2>
                        <p class="text-xs font-semibold text-slate-500">Wajib ditandatangani oleh seluruh penulis yang tercantum dalam distribusi</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Unggah Surat Pernyataan Bermaterai (PDF) <span class="text-rose-500">*</span>
                        </label>
                        <input type="file" name="file_surat_pernyataan" accept=".pdf,application/pdf" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-blue-500 bg-slate-50/50 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="text-[11px] text-slate-400 mt-1 font-semibold">Format PDF, ukuran maksimal 10 MB.</p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('reward.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                        Batal
                    </a>
                    <button type="submit" :disabled="!isExactly100() || tarifNominal <= 0"
                            class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-extrabold text-xs shadow-xs transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Kirim Pengajuan Klaim ke P3M</span>
                    </button>
                </div>
            </form>
        </main>
    </div>
</div>
@endsection


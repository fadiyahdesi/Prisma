@extends('layouts.app')

@section('title', 'Wizard Usulan BIMA - Step ' . $step . ' dari 6 - PRISMA UHN')

@section('content')
<div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <x-sidebar />

    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        <!-- Top Header Navigation -->
        <header class="bg-white border-b border-slate-200 py-4 sticky top-0 z-30 shadow-sm">
            <div class="w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <a href="{{ route('usulan.index') }}" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center shrink-0 transition" title="Kembali ke Daftar Usulan" aria-label="Kembali ke Daftar Usulan">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </a>
                    <div>
                        <h1 class="font-extrabold text-xl text-slate-900 leading-tight">Wizard 6 Langkah Usulan BIMA</h1>
                        <p class="text-xs font-semibold text-slate-500">
                            Skema: <strong class="text-blue-700 font-extrabold">{{ $usulan->skema->nama_skema }}</strong> &bull; Kode: <span class="font-bold text-slate-700 tracking-wide">{{ $usulan->kode_usulan }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Body -->
        <main class="flex-grow w-full px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            <!-- Flash Alerts -->
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-950 text-sm font-extrabold flex items-center gap-3 shadow-sm">
                    <svg class="w-5 h-5 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-300 text-rose-950 text-sm font-extrabold flex items-center gap-3 shadow-sm">
                    <svg class="w-5 h-5 text-rose-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Stepper Header Component -->
            @php
                $steps = [
                    1 => 'Identitas Usulan',
                    2 => 'Organisasi Tim & Mitra',
                    3 => 'Substansi & Berkas PDF/A',
                    4 => 'RAB 5 Pos SBM',
                    5 => 'Target Luaran',
                    6 => 'Rekapitulasi & Submit',
                ];
            @endphp

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2">
                @foreach($steps as $s => $label)
                    <a href="{{ route('usulan.step', ['usulan' => $usulan->id, 'step' => $s]) }}" 
                       class="p-3 rounded-2xl border text-left transition-all flex flex-col justify-between h-20 {{ $step == $s ? 'bg-blue-700 text-white border-blue-800 shadow-md' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}">
                        <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded w-max {{ $step == $s ? 'bg-white text-blue-900' : 'bg-slate-100 text-slate-600' }}">
                            Step {{ $s }}
                        </span>
                        <span class="text-xs font-extrabold truncate leading-tight mt-1">{{ $label }}</span>
                    </a>
                @endforeach
            </div>

            <!-- STEP 1: IDENTITAS USULAN -->
            @if($step == 1)
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xl space-y-6">
                    <div>
                        <span class="text-xs font-black text-blue-900 bg-blue-100 px-3 py-1 rounded-md border border-blue-300 uppercase tracking-wider">
                            Langkah 1 dari 6
                        </span>
                        <h3 class="text-xl font-extrabold text-slate-900 mt-2">Identitas Usulan, Rumpun Ilmu & Instrument TKT</h3>
                        <p class="text-xs font-semibold text-slate-500 mt-1">Isi judul usulan, rumpun ilmu berjenjang, bidang fokus RIRN, dan target TKT riset Anda.</p>
                    </div>

                    <form action="{{ route('usulan.save-step1', $usulan) }}" method="POST" class="space-y-6">
                        @csrf
                        <div>
                            <label class="text-xs font-extrabold uppercase text-slate-800 block mb-1">Judul Usulan Penelitian / Abmas:</label>
                            <textarea name="judul_usulan" rows="3" required placeholder="Contoh: Pengembangan Sistem Kecerdasan Buatan Terintegrasi..."
                                      class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-300 font-bold text-sm text-slate-900 focus:ring-2 focus:ring-blue-500">{{ old('judul_usulan', $usulan->judul_usulan) }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="text-xs font-extrabold uppercase text-slate-800 block mb-1">Rumpun Ilmu Level 1:</label>
                                <select name="rumpun_ilmu_level_1" required class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-300 font-bold text-xs text-slate-900">
                                    <option value="">-- Pilih Level 1 --</option>
                                    @foreach(array_keys($rumpunHierarchy) as $l1)
                                        <option value="{{ $l1 }}" {{ old('rumpun_ilmu_level_1', $usulan->rumpun_ilmu_level_1) == $l1 ? 'selected' : '' }}>{{ $l1 }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-extrabold uppercase text-slate-800 block mb-1">Rumpun Ilmu Level 2:</label>
                                <input type="text" name="rumpun_ilmu_level_2" value="{{ old('rumpun_ilmu_level_2', $usulan->rumpun_ilmu_level_2 ?? 'Ilmu Komputer') }}" required placeholder="Contoh: Ilmu Komputer" class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-300 font-bold text-xs text-slate-900">
                            </div>
                            <div>
                                <label class="text-xs font-extrabold uppercase text-slate-800 block mb-1">Rumpun Ilmu Level 3:</label>
                                <input type="text" name="rumpun_ilmu_level_3" value="{{ old('rumpun_ilmu_level_3', $usulan->rumpun_ilmu_level_3 ?? 'Rekayasa Perangkat Lunak') }}" required placeholder="Contoh: Rekayasa Perangkat Lunak" class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-300 font-bold text-xs text-slate-900">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-extrabold uppercase text-slate-800 block mb-1">Bidang Fokus RIRN / PRN:</label>
                                <select name="fokus_rirn" required class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-300 font-bold text-xs text-slate-900">
                                    <option value="">-- Pilih Bidang Fokus --</option>
                                    @foreach($fokusRirnList as $fokus)
                                        <option value="{{ $fokus }}" {{ old('fokus_rirn', $usulan->fokus_rirn) == $fokus ? 'selected' : '' }}>{{ $fokus }}</option>
                                    @endforeach
                                </select>
                            </div>

                            @if($usulan->skema->kategori === 'penelitian')
                            <div>
                                <label class="text-xs font-extrabold uppercase text-slate-800 block mb-1">
                                    Target TKT (Rentang Skema: {{ $usulan->skema->min_tkt }} - {{ $usulan->skema->max_tkt }}):
                                </label>
                                <input type="number" name="target_tkt" min="{{ $usulan->skema->min_tkt }}" max="{{ $usulan->skema->max_tkt }}" value="{{ old('target_tkt', $usulan->target_tkt) }}" required class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-300 font-bold text-xs text-slate-900">
                            </div>
                            @endif
                        </div>

                        @if($usulan->skema->kategori === 'pengabdian')
                        <div class="mt-4">
                            <label class="text-xs font-extrabold uppercase text-slate-800 block mb-2">
                                Indikator SDGs yang Disasar (Minimal 2 Indikator):
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                                @php
                                    $selectedSdgs = old('sdgs_indikator', json_decode($usulan->sdgs_indikator, true) ?? []);
                                @endphp
                                @foreach($sdgList as $index => $sdgLabel)
                                <label class="flex items-start space-x-2 p-2 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer">
                                    <input type="checkbox" name="sdgs_indikator[]" value="{{ $index }}" @if(in_array($index, $selectedSdgs)) checked @endif class="mt-1 text-blue-600 rounded">
                                    <span class="text-xs font-semibold text-slate-700 leading-tight">{{ $sdgLabel }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- TKT Indicator Badge Card -->
                        @if($usulan->skema->kategori === 'penelitian')
                        <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-600 text-white font-black flex items-center justify-center text-sm shrink-0">
                                TKT {{ $usulan->target_tkt }}
                            </div>
                            <div>
                                <h4 class="text-xs font-extrabold text-blue-950">Indikator Kesiapterapan Teknologi (Self-Assessment TKT {{ $usulan->target_tkt }}):</h4>
                                <p class="text-xs font-semibold text-blue-800 mt-0.5">{{ $tktIndicator }}</p>
                            </div>
                        </div>
                        @endif

                        <div class="pt-4 border-t border-slate-200 flex justify-end">
                            <button type="submit" class="px-6 py-3 rounded-2xl bg-blue-700 hover:bg-blue-800 text-white font-black text-xs shadow-md transition-all">
                                Simpan & Lanjut ke Langkah 2 &rarr;
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            <!-- STEP 2: ORGANISASI TIM & MITRA -->
            @if($step == 2)
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xl space-y-6">
                    <div>
                        <span class="text-xs font-black text-blue-900 bg-blue-100 px-3 py-1 rounded-md border border-blue-300 uppercase tracking-wider">
                            Langkah 2 dari 6
                        </span>
                        <h3 class="text-xl font-extrabold text-slate-900 mt-2">Organisasi Tim (Dosen & Mahasiswa IKU-2) & Profil Mitra</h3>
                        <p class="text-xs font-semibold text-slate-500 mt-1">Tambahkan Dosen Anggota (via NIDN), Mahasiswa (via NIM), dan data mitra kerja sama.</p>
                    </div>

                    <!-- Add Dosen Form -->
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                        <h4 class="text-xs font-black uppercase text-slate-800">+ Tambah Dosen Anggota (Autocomplete NIDN)</h4>
                        <form action="{{ route('usulan.save-step2', $usulan) }}" method="POST" class="flex flex-col sm:flex-row gap-3">
                            @csrf
                            <input type="hidden" name="add_dosen" value="1">
                            <input type="text" name="dosen_nidn" placeholder="NIDN Dosen (Contoh: 0620087102)" required class="flex-1 px-4 py-2.5 rounded-xl bg-white border border-slate-300 font-bold text-xs text-slate-900">
                            <input type="text" name="dosen_peran" placeholder="Peran/Tugas (Contoh: Analis Algoritma)" required class="flex-1 px-4 py-2.5 rounded-xl bg-white border border-slate-300 font-bold text-xs text-slate-900">
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-700 hover:bg-blue-800 text-white font-extrabold text-xs shadow-sm">
                                + Tambah Dosen
                            </button>
                        </form>
                    </div>

                    <!-- Add Mahasiswa Form -->
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                        <h4 class="text-xs font-black uppercase text-slate-800">+ Tambah Mahasiswa Aktif (Pencapaian IKU-2)</h4>
                        <form action="{{ route('usulan.save-step2', $usulan) }}" method="POST" class="flex flex-col sm:flex-row gap-3">
                            @csrf
                            <input type="hidden" name="add_mahasiswa" value="1">
                            <input type="text" name="mhs_nim" placeholder="NIM Mahasiswa (Contoh: 210108001)" required class="w-full sm:w-44 px-4 py-2.5 rounded-xl bg-white border border-slate-300 font-bold text-xs text-slate-900">
                            <input type="text" name="mhs_nama" placeholder="Nama Mahasiswa" required class="flex-1 px-4 py-2.5 rounded-xl bg-white border border-slate-300 font-bold text-xs text-slate-900">
                            <input type="text" name="mhs_peran" placeholder="Tugas Mahasiswa (Contoh: Pengumpul Data)" required class="flex-1 px-4 py-2.5 rounded-xl bg-white border border-slate-300 font-bold text-xs text-slate-900">
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-extrabold text-xs shadow-sm">
                                + Tambah Mahasiswa
                            </button>
                        </form>
                    </div>

                    <!-- Members List Table -->
                    <div class="space-y-2">
                        <h4 class="text-xs font-black uppercase text-slate-800">Daftar Anggota Tim Pengusul:</h4>
                        <div class="overflow-x-auto rounded-2xl border border-slate-200">
                            <table class="w-full text-left text-xs whitespace-nowrap">
                                <thead class="bg-slate-50 text-slate-600 uppercase font-black border-b border-slate-200">
                                    <tr>
                                        <th class="p-3">Nama Anggota</th>
                                        <th class="p-3">NIDN / NIM</th>
                                        <th class="p-3">Jenis</th>
                                        <th class="p-3">Tugas / Peran</th>
                                        <th class="p-3">Status Consent</th>
                                        <th class="p-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 font-semibold text-slate-800">
                                    @forelse($usulan->anggota as $anggota)
                                        <tr>
                                            <td class="p-3 font-bold text-slate-900">{{ $anggota->nama }}</td>
                                            <td class="p-3 font-bold text-blue-900 tracking-wide">{{ $anggota->identifier }}</td>
                                            <td class="p-3">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase {{ $anggota->jenis_anggota === 'dosen' ? 'bg-blue-100 text-blue-900' : 'bg-emerald-100 text-emerald-900' }}">
                                                    {{ $anggota->jenis_anggota }}
                                                </span>
                                            </td>
                                            <td class="p-3 text-slate-600">{{ $anggota->peran_anggota }}</td>
                                            <td class="p-3">
                                                @if($anggota->status_persetujuan === 'approved')
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-900 border border-emerald-300">
                                                        ✅ Approved
                                                    </span>
                                                @elseif($anggota->status_persetujuan === 'rejected')
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-100 text-rose-900 border border-rose-300">
                                                        ✕ Rejected - Kirim Ulang
                                                    </span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-900 border border-amber-300">
                                                        ⏳ Pending
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="p-3 text-center space-y-1">
                                                @if($anggota->status_persetujuan === 'rejected')
                                                    <form action="{{ route('usulan.save-step2', $usulan) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="resend_invite_id" value="{{ $anggota->id }}">
                                                        <button type="submit" class="text-blue-700 hover:text-blue-900 font-bold text-xs">Kirim Ulang</button>
                                                    </form>
                                                @endif
                                                <form action="{{ route('usulan.save-step2', $usulan) }}" method="POST" onsubmit="return confirm('Hapus anggota ini?')">
                                                    @csrf
                                                    <input type="hidden" name="delete_anggota_id" value="{{ $anggota->id }}">
                                                    <button type="submit" class="text-rose-600 hover:text-rose-800 font-bold text-xs">Hapus</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="p-6 text-center text-slate-400 font-normal">Belum ada anggota tim yang ditambahkan.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Mitra Information Form (Optional / Abmas / Hilirisasi) -->
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-4 pt-4">
                        <h4 class="text-xs font-black uppercase text-slate-800">Profil Mitra Kerja Sama (Khusus Hilirisasi & Abmas):</h4>
                        <form action="{{ route('usulan.save-step2', $usulan) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs font-extrabold uppercase text-slate-800 block mb-1">Nama Mitra Sasaran:</label>
                                    <input type="text" name="nama_mitra" value="{{ old('nama_mitra', $usulan->nama_mitra) }}" placeholder="Contoh: Koperasi Tani Harapan Jaya" class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 font-bold text-xs text-slate-900">
                                </div>
                                <div>
                                    <label class="text-xs font-extrabold uppercase text-slate-800 block mb-1">Jarak Lokasi (KM):</label>
                                    <input type="number" step="0.1" name="mitra_jarak_km" value="{{ old('mitra_jarak_km', $usulan->mitra_jarak_km) }}" placeholder="Contoh: 15.5" class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 font-bold text-xs text-slate-900">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs font-extrabold uppercase text-slate-800 block mb-1">Titik Koordinat Latitude:</label>
                                    <input type="text" name="mitra_lat" value="{{ old('mitra_lat', $usulan->mitra_lat) }}" placeholder="Contoh: -6.200000" class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 font-bold text-xs text-slate-900">
                                </div>
                                <div>
                                    <label class="text-xs font-extrabold uppercase text-slate-800 block mb-1">Titik Koordinat Longitude:</label>
                                    <input type="text" name="mitra_long" value="{{ old('mitra_long', $usulan->mitra_long) }}" placeholder="Contoh: 106.816666" class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 font-bold text-xs text-slate-900">
                                </div>
                            </div>

                            <div>
                                <label class="text-xs font-extrabold uppercase text-slate-800 block mb-1">Unggah Surat Kesediaan Bermeterai (PDF/DOC, Max 5MB):</label>
                                <input type="file" name="mitra_surat" accept=".pdf,.doc,.docx" class="w-full px-4 py-2 rounded-xl bg-white border border-slate-300 font-semibold text-xs text-slate-700">
                                @if($usulan->mitra_surat_kesediaan_path)
                                    <span class="text-xs text-emerald-700 font-bold block mt-1">✓ File Surat Kesediaan Telah Diunggah.</span>
                                @endif
                            </div>

                            <div class="pt-2 flex justify-end">
                                <button type="submit" class="px-6 py-3 rounded-2xl bg-blue-700 hover:bg-blue-800 text-white font-black text-xs shadow-md transition-all">
                                    Simpan & Lanjut ke Langkah 3 &rarr;
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <!-- STEP 3: SUBSTANSI PROPOSAL & UNGGAH PDF/A -->
            @if($step == 3)
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xl space-y-6"
                     x-data="{ 
                        text: `{{ addslashes($usulan->ringkasan_substansi ?? '') }}`,
                        get wordCount() {
                            let trimmed = this.text.trim();
                            return trimmed ? trimmed.split(/\s+/).length : 0;
                        }
                     }">
                    <div>
                        <span class="text-xs font-black text-blue-900 bg-blue-100 px-3 py-1 rounded-md border border-blue-300 uppercase tracking-wider">
                            Langkah 3 dari 6
                        </span>
                        <h3 class="text-xl font-extrabold text-slate-900 mt-2">Ringkasan Substansi & Berkas Proposal (PDF/A)</h3>
                        <p class="text-xs font-semibold text-slate-500 mt-1">Isi ringkasan usulan (Maksimal 500 kata) dan unggah berkas proposal lengkap berformat PDF/A.</p>
                    </div>

                    <form action="{{ route('usulan.save-step3', $usulan) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="text-xs font-extrabold uppercase text-slate-800">Ringkasan Substansi Proposal:</label>
                                <span class="text-xs font-bold" :class="wordCount > 500 ? 'text-rose-600' : 'text-slate-600'">
                                    Jumlah Kata: <span x-text="wordCount"></span> / 500 Kata
                                </span>
                            </div>
                            <textarea name="ringkasan_substansi" x-model="text" rows="8" required placeholder="Tuliskan latar belakang, tujuan, dan metode ringkas proposal Anda di sini..."
                                      class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-300 font-semibold text-xs text-slate-900 focus:ring-2 focus:ring-blue-500"></textarea>
                            <p x-show="wordCount > 500" class="text-xs font-bold text-rose-600 mt-1">⚠️ Ringkasan melebihi batas 500 kata! Silakan ringkas teks Anda.</p>
                        </div>

                        <div class="space-y-4">
                            <label class="text-xs font-extrabold uppercase text-slate-800 block">Berkas Proposal Lengkap (PDF, Maks 10MB):</label>
                            @if($usulan->file_proposal)
                                <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-8 h-8 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        <div>
                                            <p class="text-xs font-bold text-blue-950">Berkas Proposal Telah Diunggah</p>
                                            <p class="text-[11px] text-blue-700">Tersimpan di sistem LPPM</p>
                                        </div>
                                    </div>
                                    <a href="{{ asset('storage/' . $usulan->file_proposal) }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition">
                                        Lihat Berkas &rarr;
                                    </a>
                                </div>
                            @endif
                            <input type="file" name="file_proposal" accept=".pdf" {{ $usulan->file_proposal ? '' : 'required' }}
                                   class="w-full p-3 rounded-xl bg-slate-50 border border-slate-300 font-semibold text-xs text-slate-900 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-100 file:text-blue-800 hover:file:bg-blue-200">
                        </div>

                        <div class="pt-4 border-t border-slate-200 flex justify-end">
                            <button type="submit" class="px-6 py-3 rounded-2xl bg-blue-700 hover:bg-blue-800 text-white font-black text-xs shadow-md transition-all">
                                Simpan & Lanjut ke Langkah 4 &rarr;
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            <!-- STEP 4: RAB 5 POS SBM -->
            @if($step == 4)
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xl space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-5">
                        <div>
                            <span class="text-xs font-black text-purple-900 bg-purple-100 px-3 py-1 rounded-md border border-purple-300 uppercase tracking-wider">
                                Langkah 4 dari 6
                            </span>
                            <h3 class="text-xl font-extrabold text-slate-900 mt-2">Kalkulator Anggaran RAB (5 Pos SBM)</h3>
                            <p class="text-xs font-semibold text-slate-500 mt-1">Plafons Skema {{ $usulan->skema->nama_skema }}: <strong class="text-blue-900 font-black">Rp {{ number_format($usulan->skema->plafon_dana, 0, ',', '.') }}</strong></p>
                        </div>

                        <div class="p-4 rounded-2xl border-2 text-right transition-all {{ $usulan->total_rab > $usulan->skema->plafon_dana ? 'bg-rose-50 border-rose-300 text-rose-950' : 'bg-emerald-50 border-emerald-300 text-emerald-950' }}">
                            <span class="text-[10px] font-black uppercase block text-slate-500">Total Akumulasi RAB:</span>
                            <strong class="text-2xl font-black">Rp {{ number_format($usulan->total_rab, 0, ',', '.') }}</strong>
                            <span class="block text-[11px] font-extrabold mt-0.5">Honorarium: Rp {{ number_format($usulan->total_honorarium, 0, ',', '.') }} (Max 30%)</span>
                        </div>
                    </div>

                    <!-- Add RAB Item Form -->
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                        <h4 class="text-xs font-black uppercase text-slate-800">+ Tambah Baris Belanja SBM</h4>
                        <form action="{{ route('usulan.save-step4', $usulan) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-6 gap-3">
                            @csrf
                            <input type="hidden" name="add_rab_item" value="1">
                            <div class="sm:col-span-2">
                                <label class="text-[10px] font-bold text-slate-500 block mb-1">Pos Belanja SBM</label>
                                <select name="pos_belanja" required class="w-full px-3 py-2 rounded-xl bg-white border border-slate-300 text-xs font-semibold text-slate-900">
                                    <option value="Honorarium (Max 30%)">Honorarium (Max 30%)</option>
                                    <option value="Bahan Habis Pakai">Bahan Habis Pakai</option>
                                    <option value="Pengumpulan Data">Pengumpulan Data</option>
                                    <option value="Sewa Peralatan">Sewa Peralatan</option>
                                    <option value="Analisis Data & Luaran">Analisis Data & Luaran</option>
                                </select>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="text-[10px] font-bold text-slate-500 block mb-1">Uraian / Item</label>
                                <input type="text" name="item_keterangan" placeholder="Contoh: Kuesioner, Bahan Lab" required class="w-full px-3 py-2 rounded-xl bg-white border border-slate-300 text-xs font-semibold text-slate-900">
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-slate-500 block mb-1">Vol & Satuan</label>
                                <div class="flex gap-1">
                                    <input type="number" name="volume" min="1" value="1" required class="w-1/2 px-2 py-2 rounded-xl bg-white border border-slate-300 text-xs font-semibold text-slate-900">
                                    <input type="text" name="satuan" placeholder="Satuan" value="Paket" required class="w-1/2 px-2 py-2 rounded-xl bg-white border border-slate-300 text-xs font-semibold text-slate-900">
                                </div>
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-slate-500 block mb-1">Harga Satuan (Rp)</label>
                                <input type="number" name="harga_satuan" min="1000" step="500" placeholder="Rp" required class="w-full px-3 py-2 rounded-xl bg-white border border-slate-300 text-xs font-semibold text-slate-900">
                            </div>
                            <div class="sm:col-span-6 flex justify-end">
                                <button type="submit" class="px-4 py-2 rounded-xl bg-purple-700 hover:bg-purple-800 text-white font-bold text-xs transition">
                                    + Tambahkan ke Tabel RAB
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- RAB Table List -->
                    <div class="overflow-x-auto rounded-2xl border border-slate-200">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-100 text-slate-600 uppercase font-black tracking-wider text-[10px]">
                                    <th class="p-3">Pos Belanja</th>
                                    <th class="p-3">Item Uraian</th>
                                    <th class="p-3">Volume</th>
                                    <th class="p-3">Harga Satuan</th>
                                    <th class="p-3">Total Harga</th>
                                    <th class="p-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-semibold text-slate-800">
                                @forelse($usulan->rab as $rab)
                                    <tr>
                                        <td class="p-3 font-bold text-purple-900">{{ $rab->pos_belanja }}</td>
                                        <td class="p-3">{{ $rab->item_keterangan }}</td>
                                        <td class="p-3">{{ $rab->volume }} {{ $rab->satuan }}</td>
                                        <td class="p-3">Rp {{ number_format($rab->harga_satuan, 0, ',', '.') }}</td>
                                        <td class="p-3 font-bold text-emerald-800">Rp {{ number_format($rab->total_harga, 0, ',', '.') }}</td>
                                        <td class="p-3 text-center">
                                            <form action="{{ route('usulan.save-step4', $usulan) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="delete_rab_id" value="{{ $rab->id }}">
                                                <button type="submit" class="text-rose-600 hover:text-rose-800 font-bold text-xs">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="p-6 text-center text-slate-400 font-normal">Belum ada rincian RAB yang ditambahkan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <form action="{{ route('usulan.save-step4', $usulan) }}" method="POST" class="pt-4 border-t border-slate-200 flex justify-end">
                        @csrf
                        <button type="submit" class="px-6 py-3 rounded-2xl bg-blue-700 hover:bg-blue-800 text-white font-black text-xs shadow-md transition-all">
                            Simpan & Lanjut ke Langkah 5 &rarr;
                        </button>
                    </form>
                </div>
            @endif

            <!-- STEP 5: TARGET LUARAN WAJIB & TAMBAHAN -->
            @if($step == 5)
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xl space-y-6">
                    <div>
                        <span class="text-xs font-black text-blue-900 bg-blue-100 px-3 py-1 rounded-md border border-blue-300 uppercase tracking-wider">
                            Langkah 5 dari 6
                        </span>
                        <h3 class="text-xl font-extrabold text-slate-900 mt-2">Target Luaran Wajib & Luaran Tambahan</h3>
                        <p class="text-xs font-semibold text-slate-500 mt-1">Tentukan publikasi jurnal, paten, HKI, atau prototipe yang menjadi target capaian riset.</p>
                    </div>

                    <!-- Add Luaran Form -->
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3" x-data="{ kategoriVal: '' }">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-black uppercase text-slate-800">+ Tambah Target Luaran</h4>
                            <span class="text-[10px] font-semibold text-slate-500">Pilih kategori luaran KI / Paten / Publikasi</span>
                        </div>
                        <form action="{{ route('usulan.save-step5', $usulan) }}" method="POST" class="space-y-3">
                            @csrf
                            <input type="hidden" name="add_luaran" value="1">
                            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                                <div>
                                    <select name="jenis_luaran" required class="w-full px-3 py-2.5 rounded-xl bg-white border border-slate-300 font-bold text-xs text-slate-900">
                                        <option value="wajib">Luaran Wajib</option>
                                        <option value="tambahan">Luaran Tambahan</option>
                                    </select>
                                </div>
                                <div>
                                    <input type="text" x-model="kategoriVal" name="kategori_luaran" list="luaranList" placeholder="Kategori (KI/Paten/HKI, Scopus)" required class="w-full px-3 py-2.5 rounded-xl bg-white border border-slate-300 font-bold text-xs text-slate-900">
                                    <datalist id="luaranList">
                                        <option value="Paten &amp; Paten Sederhana (KI)">
                                        <option value="Hak Cipta (HKI)">
                                        <option value="Desain Industri (KI)">
                                        <option value="Jurnal Internasional Terindeks Scopus">
                                        <option value="Jurnal Nasional Terakreditasi SINTA">
                                        <option value="Prototipe Teruji / Produk Inovasi">
                                    </datalist>
                                </div>
                                <div>
                                    <select name="target_status" required class="w-full px-3 py-2.5 rounded-xl bg-white border border-slate-300 font-bold text-xs text-slate-900">
                                        <option value="Submitted">Submitted</option>
                                        <option value="Accepted">Accepted</option>
                                        <option value="Published">Published</option>
                                        <option value="Granted">Granted (Paten/HKI)</option>
                                    </select>
                                </div>
                                <div>
                                    <button type="submit" class="w-full px-5 py-2.5 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white font-extrabold text-xs shadow-sm transition">
                                        + Tambah Luaran
                                    </button>
                                </div>
                            </div>
                            <!-- Quick Select Pills for KI / Paten / HKI -->
                            <div class="flex flex-wrap items-center gap-1.5 pt-1 border-t border-slate-200/60">
                                <span class="text-[10px] font-black uppercase text-slate-400">Pilihan Cepat KI &amp; Paten:</span>
                                <button type="button" @click="kategoriVal = 'Paten & Paten Sederhana (KI)'" class="px-2 py-0.5 rounded-md bg-indigo-50 border border-indigo-200 text-indigo-700 hover:bg-indigo-100 text-[10px] font-extrabold transition">
                                    💡 Paten (KI)
                                </button>
                                <button type="button" @click="kategoriVal = 'Hak Cipta (HKI)'" class="px-2 py-0.5 rounded-md bg-blue-50 border border-blue-200 text-blue-700 hover:bg-blue-100 text-[10px] font-extrabold transition">
                                    📜 Hak Cipta (HKI)
                                </button>
                                <button type="button" @click="kategoriVal = 'Desain Industri (KI)'" class="px-2 py-0.5 rounded-md bg-emerald-50 border border-emerald-200 text-emerald-700 hover:bg-emerald-100 text-[10px] font-extrabold transition">
                                    🎨 Desain Industri (KI)
                                </button>
                                <button type="button" @click="kategoriVal = 'Jurnal Internasional Terindeks Scopus'" class="px-2 py-0.5 rounded-md bg-purple-50 border border-purple-200 text-purple-700 hover:bg-purple-100 text-[10px] font-extrabold transition">
                                    📚 Scopus
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Luaran Table -->
                    <div class="overflow-x-auto rounded-2xl border border-slate-200">
                        <table class="w-full text-left text-xs whitespace-nowrap">
                            <thead class="bg-slate-50 text-slate-600 uppercase font-black border-b border-slate-200">
                                <tr>
                                    <th class="p-3">Jenis</th>
                                    <th class="p-3">Kategori Luaran</th>
                                    <th class="p-3">Target Status</th>
                                    <th class="p-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-semibold text-slate-800">
                                @forelse($usulan->luaran as $luaran)
                                    <tr>
                                        <td class="p-3">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase {{ $luaran->jenis_luaran === 'wajib' ? 'bg-blue-100 text-blue-900' : 'bg-slate-100 text-slate-800' }}">
                                                {{ $luaran->jenis_luaran }}
                                            </span>
                                        </td>
                                        <td class="p-3 font-bold text-slate-900">{{ $luaran->kategori_luaran }}</td>
                                        <td class="p-3 font-bold text-emerald-800">{{ $luaran->target_status }}</td>
                                        <td class="p-3 text-center">
                                            <form action="{{ route('usulan.save-step5', $usulan) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="delete_luaran_id" value="{{ $luaran->id }}">
                                                <button type="submit" class="text-rose-600 hover:text-rose-800 font-bold text-xs">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="p-6 text-center text-slate-400 font-normal">Belum ada target luaran yang ditambahkan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <form action="{{ route('usulan.save-step5', $usulan) }}" method="POST" class="pt-4 border-t border-slate-200 flex justify-end">
                        @csrf
                        <button type="submit" class="px-6 py-3 rounded-2xl bg-blue-700 hover:bg-blue-800 text-white font-black text-xs shadow-md transition-all">
                            Simpan & Lanjut ke Langkah 6 &rarr;
                        </button>
                    </form>
                </div>
            @endif

            <!-- STEP 6: REKAPITULASI & FINAL SUBMISSION -->
            @if($step == 6)
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xl space-y-6">
                    <div>
                        <span class="text-xs font-black text-emerald-900 bg-emerald-100 px-3 py-1 rounded-md border border-emerald-300 uppercase tracking-wider">
                            Langkah 6 dari 6 (Final)
                        </span>
                        <h3 class="text-xl font-extrabold text-slate-900 mt-2">Rekapitulasi Utuh & Submisi Akhir Proposal</h3>
                        <p class="text-xs font-semibold text-slate-500 mt-1">Periksa kembali ringkasan draf usulan sebelum melakukan submisi penguncian akhir.</p>
                    </div>

                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 space-y-4 text-xs">
                        <div class="flex justify-between border-b border-slate-200 pb-2">
                            <span class="text-slate-500 font-bold">Judul Usulan:</span>
                            <span class="font-bold text-slate-900 max-w-lg text-right">{{ $usulan->judul_usulan }}</span>
                        </div>
                        <div class="flex justify-between border-b border-slate-200 pb-2">
                            <span class="text-slate-500 font-bold">Skema Hibah:</span>
                            <span class="font-bold text-blue-900">{{ $usulan->skema->nama_skema }} ({{ $usulan->skema->kode_skema }})</span>
                        </div>
                        <div class="flex justify-between border-b border-slate-200 pb-2">
                            <span class="text-slate-500 font-bold">Rumpun Ilmu & TKT:</span>
                            <span class="font-bold text-slate-800">{{ $usulan->rumpun_ilmu_level_1 }} &bull; Target TKT {{ $usulan->target_tkt }}</span>
                        </div>
                        <div class="flex justify-between border-b border-slate-200 pb-2">
                            <span class="text-slate-500 font-bold">Jumlah Anggota Tim:</span>
                            <span class="font-bold text-slate-800">{{ count($usulan->anggota) }} Orang</span>
                        </div>
                        <div class="flex justify-between border-b border-slate-200 pb-2">
                            <span class="text-slate-500 font-bold">Total Anggaran RAB:</span>
                            <span class="font-bold text-emerald-800">Rp {{ number_format($usulan->total_rab, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500 font-bold">Status Berkas PDF/A:</span>
                            <span class="font-bold {{ $usulan->file_proposal_path ? 'text-emerald-700' : 'text-rose-600' }}">
                                {{ $usulan->file_proposal_path ? '✓ Terunggah' : '❌ Belum Diunggah' }}
                            </span>
                        </div>
                    </div>

                    @if($usulan->status !== 'Draft')
                        <div class="p-4 rounded-2xl {{ $usulan->status === 'Approved' ? 'bg-emerald-50 border-emerald-300 text-emerald-950' : ($usulan->status === 'Rejected' ? 'bg-rose-50 border-rose-300 text-rose-950' : 'bg-blue-50 border-blue-300 text-blue-950') }} border font-bold text-sm text-center">
                            Status proposal: <strong>{{ $usulan->status }}</strong>{{ $usulan->submitted_at ? ' pada ' . $usulan->submitted_at->format('d M Y H:i') : '' }}.
                            @if($usulan->verification_notes)
                                <p class="mt-2 text-xs font-semibold">Catatan verifikasi: {{ $usulan->verification_notes }}</p>
                            @endif
                            @if($usulan->kaprodi_alignment_status)
                                <p class="mt-1 text-xs font-semibold">Rekomendasi Kaprodi: {{ $usulan->kaprodi_alignment_status }}{{ $usulan->kaprodi_recommendation ? ' - ' . $usulan->kaprodi_recommendation : '' }}</p>
                            @endif
                        </div>
                    @else
                        <form action="{{ route('usulan.submit-final', $usulan) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengirim proposal ini? Draf akan dikunci.')" class="pt-4 border-t border-slate-200 flex justify-end">
                            @csrf
                            <button type="submit" class="px-8 py-4 rounded-2xl bg-emerald-700 hover:bg-emerald-800 text-white font-black text-sm shadow-xl transition-all">
                                Submit Final Proposal & Penguncian Draf &rarr;
                            </button>
                        </form>
                    @endif
                </div>
            @endif
        </main>
    </div>
</div>
@endsection


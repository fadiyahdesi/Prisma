@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50/50 pb-16">
    {{-- Header --}}
    <div class="bg-white border-b border-slate-200 sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                        <span>Pusat Bantuan</span>
                        <span>/</span>
                        <span class="text-blue-600">Buku Panduan Pengguna</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center font-black shadow-xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <div>
                            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Buku Panduan Pengguna Interaktif (User Guide)</h1>
                            <p class="text-xs text-slate-500 font-medium">Dokumentasi resmi alur operasional KHARISMA UHN untuk seluruh sivitas akademika (US-13.3)</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1.5 rounded-xl bg-blue-50 text-blue-700 text-xs font-bold border border-blue-200">
                        Edisi Resmi 2026 (v2.0)
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
        {{-- Role Navigation Pills --}}
        <div class="flex flex-wrap items-center gap-2 mb-8 bg-white p-2 rounded-2xl border border-slate-200/90 shadow-2xs">
            <a href="{{ route('panduan.index', ['role' => 'dosen']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-colors flex items-center gap-2 {{ $activeTab === 'dosen' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span>Dosen / Pengusul</span>
            </a>
            <a href="{{ route('panduan.index', ['role' => 'anggota']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-colors flex items-center gap-2 {{ $activeTab === 'anggota' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span>Anggota Tim</span>
            </a>
            <a href="{{ route('panduan.index', ['role' => 'reviewer']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-colors flex items-center gap-2 {{ $activeTab === 'reviewer' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Reviewer Ilmiah</span>
            </a>
            <a href="{{ route('panduan.index', ['role' => 'kaprodi']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-colors flex items-center gap-2 {{ $activeTab === 'kaprodi' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span>Ketua Prodi</span>
            </a>
            <a href="{{ route('panduan.index', ['role' => 'dekanat']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-colors flex items-center gap-2 {{ $activeTab === 'dekanat' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
                <span>Dekanat Fakultas</span>
            </a>
            <a href="{{ route('panduan.index', ['role' => 'keuangan']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-colors flex items-center gap-2 {{ $activeTab === 'keuangan' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span>Keuangan LPPM</span>
            </a>
            <a href="{{ route('panduan.index', ['role' => 'admin']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-colors flex items-center gap-2 {{ $activeTab === 'admin' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Admin & Kepala P3M</span>
            </a>
        </div>

        {{-- Tab Contents --}}
        @if($activeTab === 'dosen')
        <div class="space-y-6">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold">1</div>
                    <div>
                        <h3 class="font-extrabold text-base text-slate-900">Alur Pengusulan Proposal Penelitian & PkM (Wizard 6 Langkah)</h3>
                        <p class="text-xs text-slate-500">Panduan submisi proposal berbasis standar BIMA Kemendikbudristek</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <span class="font-bold text-blue-700 block mb-1">Langkah 1 & 2: Identitas & Anggota</span>
                        <p class="text-slate-600">Pilih skema BIMA sesuai eligibility SINTA, isi fokus RIRN dan target TKT. Daftarkan dosen anggota dan mahasiswa (IKU-2).</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <span class="font-bold text-blue-700 block mb-1">Langkah 3 & 4: Substansi & RAB SBM</span>
                        <p class="text-slate-600">Isi ringkasan substansi (maks 500 kata). Susun anggaran menggunakan kalkulator standar SBM (honorarium maks 30%).</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <span class="font-bold text-blue-700 block mb-1">Langkah 5 & 6: Dokumen & Final Submit</span>
                        <p class="text-slate-600">Unggah berkas proposal PDF (maks 5MB) dan surat mitra. Pastikan seluruh anggota menyetujui consent sebelum Submit Final.</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold">2</div>
                    <div>
                        <h3 class="font-extrabold text-base text-slate-900">Pelaksanaan Riset, Logbook Harian & Pelaporan</h3>
                        <p class="text-xs text-slate-500">Tahapan monev kemajuan 70% dan laporan akhir 100%</p>
                    </div>
                </div>
                <ul class="space-y-3 text-xs text-slate-600">
                    <li class="flex items-start gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-600 mt-1.5 shrink-0"></span>
                        <span><strong>Pencatatan Logbook:</strong> Isi aktivitas berkas, tanggal kegiatan, dokumentasi foto, dan catatan pengeluaran secara berkala.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-600 mt-1.5 shrink-0"></span>
                        <span><strong>Laporan Kemajuan (70%):</strong> Unggah draf laporan dan SPTB 70% sebagai prasyarat penilaian monev lapangan/daring.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-600 mt-1.5 shrink-0"></span>
                        <span><strong>Laporan Akhir (100%):</strong> Unggah laporan akhir lengkap, naskah luaran wajib, dan cetak Lembar Pengesahan ber-QR Code publik.</span>
                    </li>
                </ul>
            </div>

            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">3</div>
                    <div>
                        <h3 class="font-extrabold text-base text-slate-900">Bank Publikasi, Sentra HKI & Klaim Insentif Reward</h3>
                        <p class="text-xs text-slate-500">Pengelolaan luaran ilmiah dan pencairan insentif multi-penulis</p>
                    </div>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Daftarkan artikel terbit di menu <strong>Bank Publikasi</strong> (menggunakan fitur tarik DOI otomatis Crossref) dan paten/hak cipta di menu <strong>Sentra HKI</strong>. Setelah terverifikasi, ajukan klaim insentif reward dengan mendistribusikan persentase kontribusi tim pengusul (total tepat 100%).
                </p>
            </div>
        </div>
        @elseif($activeTab === 'reviewer')
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs">
            <h3 class="font-extrabold text-base text-slate-900 mb-2">Panduan Penilaian Double-Blind Reviewer</h3>
            <p class="text-xs text-slate-500 mb-6">Reviewer menilai proposal tanpa mengetahui identitas pengusul (Double-Blind) untuk menjamin objektivitas akademik.</p>
            <div class="space-y-4 text-xs text-slate-600">
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    <strong class="text-slate-900 block mb-1">Skala Rubrik BIMA (1–7):</strong>
                    <p>Berikan nilai numerik 1 s.d. 7 pada 4 parameter: Kualifikasi Tim, Urgensi Masalah, Ketepatan Metode/TKT, dan Kelayakan Luaran & RAB.</p>
                </div>
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    <strong class="text-slate-900 block mb-1">Ambang Batas Disparitas (&gt;15 Poin):</strong>
                    <p>Jika selisih skor Reviewer 1 dan Reviewer 2 melebihi 15 poin, sistem otomatis memicu status Adjudikasi untuk penunjukan Reviewer ke-3 oleh Kepala P3M.</p>
                </div>
            </div>
        </div>
        @elseif($activeTab === 'keuangan')
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs">
            <h3 class="font-extrabold text-base text-slate-900 mb-2">Panduan Divisi Keuangan LPPM</h3>
            <p class="text-xs text-slate-500 mb-6">Verifikasi nomor rekening, eksekusi pencairan termin dana hibah, dan transfer reward insentif.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    <strong class="text-slate-900 block mb-1">Pencairan Termin I (70%) & Termin II (30%):</strong>
                    <p class="text-slate-600">Termin 1 dicairkan setelah buku tabungan & SPK terverifikasi. Termin 2 dicairkan setelah laporan akhir dan lembar pengesahan ber-QR Code disetujui P3M.</p>
                </div>
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    <strong class="text-slate-900 block mb-1">Pencairan Insentif Multi-Penulis:</strong>
                    <p class="text-slate-600">Keuangan mentransfer nominal reward langsung ke rekening masing-masing anggota tim sesuai persentase kontribusi yang telah disetujui P3M.</p>
                </div>
            </div>
        </div>
        @else
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs">
            <h3 class="font-extrabold text-base text-slate-900 mb-2">Panduan Tata Kelola Pimpinan (Kaprodi, Dekanat & P3M)</h3>
            <p class="text-xs text-slate-500 mb-6">Pemantauan indikator kinerja riset (IKU-2 & IKU-5), serapan anggaran pagu fakultas, dan penyusunan borang akreditasi.</p>
            <div class="space-y-4 text-xs text-slate-600">
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    <strong class="text-slate-900 block mb-1">Dasbor Analitik Fakultas (Tenant Isolation):</strong>
                    <p>Dekanat dapat memantau perbandingan prodi serapan tertinggi vs terendah, serta komparasi target vs realisasi luaran publikasi & HKI.</p>
                </div>
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    <strong class="text-slate-900 block mb-1">Pelaporan Akreditasi BAN-PT / LAM (Tabel 3.b.1 s.d. 3.b.4):</strong>
                    <p>P3M dan Penjaminan Mutu dapat mengekspor borang otomatis dalam format Microsoft Excel (*multi-sheet styled*) atau PDF siap cetak berkop surat resmi.</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection


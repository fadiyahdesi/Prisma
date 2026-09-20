@extends('layouts.app')

@section('title', 'Buku Panduan Pengguna - PRISMA UHN')

@section('content')
<div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <x-sidebar />

    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        {{-- Header --}}
        <header class="bg-white border-b border-slate-200 py-4 sticky top-0 z-30 shadow-xs">
            <div class="w-full px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 shrink-0" title="Buka Menu">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <a href="{{ route('dashboard') }}" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center shrink-0 transition" title="Kembali ke Dashboard">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </a>
                    <div>
                        <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Buku Panduan Pengguna Interaktif (User Guide)</h1>
                        <p class="text-xs text-slate-500 font-medium">Dokumentasi resmi alur operasional PRISMA UHN untuk seluruh sivitas akademika (US-13.3)</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1.5 rounded-xl bg-blue-50 text-blue-700 text-xs font-bold border border-blue-200">
                        Edisi Resmi 2026 (v2.0)
                    </span>
                </div>
            </div>
        </header>

        <main class="flex-grow w-full px-4 sm:px-6 lg:px-8 py-8 space-y-6">
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
                <a href="{{ route('panduan.index', ['role' => 'p3m']) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold transition-colors flex items-center gap-2 {{ $activeTab === 'p3m' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <span>Admin P3M</span>
                </a>
                <a href="{{ route('panduan.index', ['role' => 'keuangan']) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold transition-colors flex items-center gap-2 {{ $activeTab === 'keuangan' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Divisi Keuangan</span>
                </a>
            </div>

            {{-- Role-specific Content --}}
            @if($activeTab === 'dosen')
            <div class="space-y-6">
                {{-- Quick Workflow Steps --}}
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs">
                    <h3 class="font-extrabold text-base text-slate-900 mb-6 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-blue-600 text-white text-xs flex items-center justify-center font-bold">1</span>
                        <span>Alur Kerja Pengusul Riset &amp; Pengabdian (End-to-End)</span>
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80">
                            <span class="text-xs font-black text-blue-600 block mb-1">LANGKAH 1</span>
                            <h4 class="font-extrabold text-xs text-slate-800 mb-1">Cek Skor SINTA &amp; Kuota</h4>
                            <p class="text-[11px] text-slate-500">Pastikan SINTA Score 3Yr &ge; 50 (Penelitian) atau &ge; 25 (Pengabdian). Cek pada menu Profil Metrik SINTA.</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80">
                            <span class="text-xs font-black text-blue-600 block mb-1">LANGKAH 2</span>
                            <h4 class="font-extrabold text-xs text-slate-800 mb-1">Wizard Usulan 6 Langkah</h4>
                            <p class="text-[11px] text-slate-500">Lengkapi identitas, anggota tim &amp; mitra, RAB format SBM, target luaran, berkas PDF, serta kirim undangan anggota.</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80">
                            <span class="text-xs font-black text-blue-600 block mb-1">LANGKAH 3</span>
                            <h4 class="font-extrabold text-xs text-slate-800 mb-1">Kontrak &amp; Logbook Harian</h4>
                            <p class="text-[11px] text-slate-500">Jika dinyatakan lolos, tanda tangani SPK digital ber-QR Code, input rekening, dan catat aktivitas logbook mingguan.</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80">
                            <span class="text-xs font-black text-blue-600 block mb-1">LANGKAH 4</span>
                            <h4 class="font-extrabold text-xs text-slate-800 mb-1">Monev, Luaran &amp; Laporan</h4>
                            <p class="text-[11px] text-slate-500">Unggah kemajuan 70%, ikuti Semhas, daftarkan publikasi/HKI di Sentra HKI, lalu tuntaskan laporan akhir 100%.</p>
                        </div>
                    </div>
                </div>

                {{-- Detailed FAQ/Tips for Dosen --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-xs space-y-4">
                        <h4 class="font-extrabold text-sm text-slate-900 flex items-center gap-2">
                            <span class="text-blue-600">📌</span>
                            <span>Aturan Ambang Batas SINTA &amp; Eligibilitas</span>
                        </h4>
                        <ul class="text-xs text-slate-600 space-y-2 list-disc list-inside">
                            <li>Skor dihitung otomatis via API integrasi SINTA Kemendikbudristek.</li>
                            <li>Dosen dapat mengajukan maksimal <strong>2 usulan per tahun</strong> (1 sebagai Ketua dan 1 sebagai Anggota, atau 2 sebagai Anggota).</li>
                            <li>Dosen yang memiliki tanggungan laporan akhir tahun sebelumnya tidak diizinkan membuat usulan baru (*hard-block*).</li>
                        </ul>
                    </div>

                    <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-xs space-y-4">
                        <h4 class="font-extrabold text-sm text-slate-900 flex items-center gap-2">
                            <span class="text-blue-600">💡</span>
                            <span>Tips Bank Publikasi &amp; Sentra HKI</span>
                        </h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Daftarkan artikel terbit di menu <strong>Bank Publikasi</strong> (menggunakan fitur tarik DOI otomatis Crossref) dan paten/hak cipta di menu <strong>Sentra HKI</strong>. Setelah terverifikasi, ajukan klaim insentif reward dengan mendistribusikan persentase kontribusi tim pengusul (total tepat 100%).
                        </p>
                    </div>
                </div>
            </div>
            @elseif($activeTab === 'anggota')
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs">
                <h3 class="font-extrabold text-base text-slate-900 mb-2">Panduan Calon Anggota Tim Riset (Dosen &amp; Mahasiswa)</h3>
                <p class="text-xs text-slate-500 mb-6">Mekanisme persetujuan keanggotaan (Member Consent) berbasis notifikasi resmi.</p>
                <div class="space-y-4 text-xs text-slate-600">
                    <div class="p-4 rounded-2xl bg-amber-50/60 border border-amber-200/80">
                        <strong class="text-amber-900 block mb-1">Konfirmasi Member Consent Wajib:</strong>
                        <p>Ketika ketua pengusul menambahkan Anda ke dalam tim, sistem mengirimkan notifikasi. Anda wajib membuka menu <strong>Persetujuan Anggota</strong> untuk meninjau judul dan skema, lalu mengeklik <em>Setujui Keikutsertaan</em>. Usulan tidak dapat diajukan ke P3M jika ada anggota yang belum memberikan persetujuan.</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <strong class="text-slate-900 block mb-1">Hak Reward Insentif:</strong>
                        <p>Anggota yang terdaftar dan menyetujui keanggotaan berhak menerima distribusi pencairan reward publikasi atau HKI yang langsung ditransfer ke rekening masing-masing oleh Divisi Keuangan.</p>
                    </div>
                </div>
            </div>
            @elseif($activeTab === 'reviewer')
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs">
                <h3 class="font-extrabold text-base text-slate-900 mb-2">Panduan Reviewer Ilmiah (Double-Blind Peer Review)</h3>
                <p class="text-xs text-slate-500 mb-6">Penilaian proposal riset dan monev kemajuan menggunakan standar rubrik BIMA 1-7.</p>
                <div class="space-y-4 text-xs text-slate-600">
                    <div class="p-4 rounded-2xl bg-purple-50/60 border border-purple-200/80">
                        <strong class="text-purple-900 block mb-1">Prinsip Kerahasiaan (Double-Blind):</strong>
                        <p>Identitas pengusul dan reviewer disamarkan (*anonymized*) secara sistemik. Reviewer menilai proposal murni berdasarkan mutu substansi, urgensi kebaruan, kelayakan metode, dan kewajaran RAB format SBM.</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <strong class="text-slate-900 block mb-1">Penilaian Monev Kemajuan 70%:</strong>
                        <p>Reviewer mengevaluasi logbook, SPTB 70%, dan capaian luaran draft publikasi/HKI pengusul secara daring melalui antarmuka Monev Reviewer.</p>
                    </div>
                </div>
            </div>
            @elseif($activeTab === 'keuangan')
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs">
                <h3 class="font-extrabold text-base text-slate-900 mb-2">Panduan Divisi Keuangan LPPM</h3>
                <p class="text-xs text-slate-500 mb-6">Verifikasi nomor rekening, eksekusi pencairan termin dana hibah, dan transfer reward insentif.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <strong class="text-slate-900 block mb-1">Pencairan Termin I (70%) &amp; Termin II (30%):</strong>
                        <p class="text-slate-600">Termin 1 dicairkan setelah buku tabungan &amp; SPK terverifikasi. Termin 2 dicairkan setelah laporan akhir dan lembar pengesahan ber-QR Code disetujui P3M.</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <strong class="text-slate-900 block mb-1">Pencairan Insentif Multi-Penulis:</strong>
                        <p class="text-slate-600">Keuangan mentransfer nominal reward langsung ke rekening masing-masing anggota tim sesuai persentase kontribusi yang telah disetujui P3M.</p>
                    </div>
                </div>
            </div>
            @else
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs">
                <h3 class="font-extrabold text-base text-slate-900 mb-2">Panduan Tata Kelola Pimpinan (Kaprodi, Dekanat &amp; P3M)</h3>
                <p class="text-xs text-slate-500 mb-6">Pemantauan indikator kinerja riset (IKU-2 &amp; IKU-5), serapan anggaran pagu fakultas, dan penyusunan borang akreditasi.</p>
                <div class="space-y-4 text-xs text-slate-600">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <strong class="text-slate-900 block mb-1">Dasbor Analitik Fakultas (Tenant Isolation):</strong>
                        <p>Dekanat dapat memantau perbandingan prodi serapan tertinggi vs terendah, serta komparasi target vs realisasi luaran publikasi &amp; HKI.</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <strong class="text-slate-900 block mb-1">Pelaporan Akreditasi BAN-PT / LAM (Tabel 3.b.1 s.d. 3.b.4):</strong>
                        <p>P3M dan Penjaminan Mutu dapat mengekspor borang otomatis dalam format Microsoft Excel (*multi-sheet styled*) atau PDF siap cetak berkop surat resmi.</p>
                    </div>
                </div>
            </div>
            @endif
        </main>
    </div>
</div>
@endsection

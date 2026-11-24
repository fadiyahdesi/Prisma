@extends('layouts.app')

@section('title', 'Evaluasi Monev Lapangan - PRISMA UHN')

@section('content')
<div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <x-sidebar />

    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        <header class="bg-white border-b border-slate-200 py-4 sticky top-0 z-30 shadow-sm">
            <div class="w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <a href="{{ route('reviewer.monev.index') }}" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center shrink-0 transition" title="Kembali ke Daftar Monev" aria-label="Kembali ke Daftar Monev">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </a>
                    <div>
                        <h1 class="font-extrabold text-xl text-slate-900 leading-tight">Formulir Evaluasi Monev Lapangan</h1>
                        <p class="text-xs font-semibold text-slate-500">Penilaian Kemajuan Riset 70% & Rekomendasi Kelanjutan (US-10.2)</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-black bg-purple-100 text-purple-800 border border-purple-300">
                        Reviewer Monev
                    </span>
                </div>
            </div>
        </header>

        <main class="flex-grow w-full px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            @if(session('error'))
                <div class="p-4.5 rounded-2xl bg-red-50 border border-red-300 text-red-900 text-sm font-bold flex items-center gap-3 shadow-sm">
                    <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="p-4.5 rounded-2xl bg-red-50 border border-red-300 text-red-900 text-xs font-semibold shadow-sm">
                    <p class="font-bold text-sm mb-1">Mohon perbaiki kesalahan formulir:</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @php
                $u = $monev->usulan;
            @endphp

            <!-- Header Usulan -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-3">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded font-mono text-[11px] font-bold bg-blue-100 text-blue-800 border border-blue-200">
                        {{ $u->kode_usulan }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded text-[11px] font-semibold bg-slate-200 text-slate-800">
                        {{ $u->skema->nama_skema ?? '-' }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded text-[11px] font-semibold bg-slate-200 text-slate-800">
                        Tahun {{ $u->periode->tahun_anggaran ?? date('Y') }}
                    </span>
                </div>
                <h2 class="text-lg font-black text-slate-900 leading-snug">
                    {{ $u->judul_usulan }}
                </h2>
                <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500 pt-1">
                    <span>Ketua Peneliti: <strong class="text-slate-800">{{ $u->pengusul->name ?? '-' }}</strong></span>
                    <span>NIDN: <strong class="text-slate-800">{{ $u->pengusul->nidn ?? '-' }}</strong></span>
                    <span>Prodi: <strong class="text-slate-800">{{ $u->pengusul->prodi->nama_prodi ?? '-' }}</strong></span>
                    <span>Fakultas: <strong class="text-slate-800">{{ $u->pengusul->fakultas->nama_fakultas ?? '-' }}</strong></span>
                </div>
            </div>

            <!-- Documents & Scoring Form Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left: Berkas & Logbook Summary -->
                <div class="space-y-4">
                    <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm space-y-4 text-xs">
                        <h3 class="font-extrabold text-slate-900 text-sm flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Berkas yang Dievaluasi
                        </h3>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center font-bold text-xs shrink-0">
                                    PDF
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800">Laporan Kemajuan 70%</p>
                                    <span class="text-[10px] text-slate-400">Naskah kemajuan substansi riset</span>
                                </div>
                            </div>
                            <a href="{{ asset('storage/' . $monev->file_laporan_kemajuan) }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition">
                                Unduh / Lihat
                            </a>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-xs shrink-0">
                                    PDF
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800">SPTB 70% (Laporan Keuangan)</p>
                                    <span class="text-[10px] text-slate-400">Bukti pertanggungjawaban dana termin 1</span>
                                </div>
                            </div>
                            <a href="{{ asset('storage/' . $monev->file_sptb_70) }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition">
                                Unduh / Lihat
                            </a>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center font-bold text-xs shrink-0">
                                    PDF
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800">Rekap Logbook Harian ({{ $u->logbook->count() }} Kegiatan)</p>
                                    <span class="text-[10px] text-slate-400">Catatan kronologis aktivitas lapangan</span>
                                </div>
                            </div>
                            <a href="{{ route('pengusul.logbook.download-pdf', $u) }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs transition">
                                Unduh PDF
                            </a>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Ringkasan Kemajuan Peneliti:</span>
                            <p class="text-xs text-slate-700 leading-relaxed whitespace-pre-line">
                                {{ $monev->ringkasan_kemajuan }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Right: Form Penilaian Monev -->
                <div>
                    <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div>
                                <h3 class="font-extrabold text-slate-900 text-sm">Penilaian & Rekomendasi Reviewer</h3>
                                <p class="text-[11px] text-slate-500">Evaluasi lapangan pencapaian luaran dan belanja dana</p>
                            </div>
                            @if($monev->status === 'evaluated')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800">
                                    Sudah Dinilai
                                </span>
                            @endif
                        </div>

                        <form id="form-monev" action="{{ route('reviewer.monev.store', $monev) }}" method="POST" class="space-y-4 text-xs">
                            @csrf
                            <div class="p-4 rounded-2xl bg-purple-50/70 border border-purple-200 space-y-4">
                                <div class="flex items-start justify-between gap-3 pb-2 border-b border-purple-200">
                                    <div>
                                        <h4 class="font-extrabold text-slate-900 text-xs">Indikator Penilaian Evaluasi Keberlanjutan</h4>
                                        <p class="text-[10px] text-slate-500 mt-0.5">{{ $pedoman['nama_skema'] }}</p>
                                        <p class="text-[9px] text-slate-400">{{ $pedoman['sumber'] }}</p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="block text-[9px] font-bold text-purple-700 uppercase">Skor terhitung</span>
                                        <span id="skor_terhitung" class="text-lg font-black text-purple-900">0.00</span>
                                    </div>
                                </div>

                                @foreach($pedoman['komponen'] as $ck => $komponen)
                                    <div class="space-y-2">
                                        <p class="font-extrabold text-slate-800 text-[11px]">
                                            {{ $loop->iteration }}. {{ $komponen['nama'] }}
                                            <span class="font-bold text-purple-700">(maks. {{ $komponen['bobot_maks'] }})</span>
                                        </p>
                                        <select name="penilaian[{{ $ck }}][status]"
                                                data-status
                                                data-max="{{ $komponen['bobot_maks'] }}"
                                                class="w-full px-3 py-2 rounded-xl bg-white border border-purple-200 text-[11px] font-bold focus:ring-2 focus:ring-purple-500 focus:outline-none">
                                            <option value="">-- Pilih Status --</option>
                                            @foreach($komponen['status'] as $label => $nilai)
                                                <option value="{{ $nilai }}"
                                                        {{ (string) old("penilaian.{$ck}.status", $penilaianAwal[$ck]['status'] ?? '') === (string) $nilai ? 'selected' : '' }}>
                                                    {{ $label }} (Skor {{ $nilai }})
                                                </option>
                                            @endforeach
                                        </select>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                            @foreach($komponen['items'] as $ik => $item)
                                                <div>
                                                    <label class="block text-[10px] font-bold text-slate-600 mb-1">
                                                        {{ $item['nama'] }} <span class="text-purple-700">(bobot {{ $item['bobot'] }}%)</span>
                                                    </label>
                                                    <select name="penilaian[{{ $ck }}][items][{{ $ik }}]"
                                                            data-item
                                                            data-bobot="{{ $item['bobot'] }}"
                                                            class="w-full px-2.5 py-2 rounded-xl bg-white border border-purple-200 text-[11px] font-semibold focus:ring-2 focus:ring-purple-500 focus:outline-none">
                                                        <option value="">-- Pilih Nilai --</option>
                                                        @foreach($item['penilaian'] as $label => $nilai)
                                                            <option value="{{ $nilai }}"
                                                                    {{ (string) old("penilaian.{$ck}.items.{$ik}", $penilaianAwal[$ck]['items'][$ik] ?? '') === (string) $nilai ? 'selected' : '' }}>
                                                                {{ $label }} ({{ $nilai }}%)
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach

                                <p class="text-[10px] text-slate-500 leading-relaxed">
                                    Skor akhir = Σ (status komponen × rata-rata bobot item yang terpenuhi). Field skor di bawah akan diisi otomatis setelah status komponen dipilih.
                                </p>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1">
                                    Skor Evaluasi Kemajuan (0 - 100) *
                                </label>
                                <input type="number" id="skor_monev" name="skor_monev" step="0.01" min="0" max="100" value="{{ old('skor_monev', $monev->skor_monev) }}" required placeholder="Contoh: 85.5" class="w-full px-3 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-xs font-bold focus:ring-2 focus:ring-purple-500 focus:bg-white focus:outline-none">
                                <p class="text-[10px] text-slate-400 mt-1">Passing grade rekomendasi lanjut minimal adalah 70.0.</p>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1">
                                    Rekomendasi Kelanjutan Pelaksanaan *
                                </label>
                                <select name="rekomendasi" required class="w-full px-3 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-xs font-bold focus:ring-2 focus:ring-purple-500 focus:bg-white focus:outline-none">
                                    <option value="">-- Pilih Rekomendasi --</option>
                                    <option value="Lanjut" {{ old('rekomendasi', $monev->rekomendasi) === 'Lanjut' ? 'selected' : '' }}>
                                        Lanjut (Pelaksanaan memenuhi standar, siap lanjut ke Seminar Hasil)
                                    </option>
                                    <option value="Perbaikan" {{ old('rekomendasi', $monev->rekomendasi) === 'Perbaikan' ? 'selected' : '' }}>
                                        Perbaikan (Memerlukan revisi laporan kemajuan / bukti luaran)
                                    </option>
                                    <option value="Ditunda" {{ old('rekomendasi', $monev->rekomendasi) === 'Ditunda' ? 'selected' : '' }}>
                                        Ditunda (Capaian kritis tertunda / temuan ketidaksesuaian anggaran)
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1">
                                    Catatan Evaluasi & Masukan Lapangan *
                                </label>
                                <textarea name="catatan_evaluasi" rows="5" required placeholder="Berikan catatan konstruktif mengenai kualitas data, capaian target luaran wajib, kepatuhan jadwal pelaksanaan, dan saran perbaikan..." class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-300 text-xs focus:ring-2 focus:ring-purple-500 focus:bg-white focus:outline-none">{{ old('catatan_evaluasi', $monev->catatan_evaluasi) }}</textarea>
                            </div>

                            <button type="submit" class="w-full py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-extrabold text-xs shadow-md transition flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Simpan & Kirim Evaluasi Monev</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<script>
(function () {
    const form = document.getElementById('form-monev');
    const skorInput = document.getElementById('skor_monev');
    const skorTeks = document.getElementById('skor_terhitung');
    if (!form || !skorInput || !skorTeks) return;

    function hitung() {
        const komponen = new Map();

        form.querySelectorAll('[data-status]').forEach(function (select) {
            const status = parseFloat(select.value || '0');
            const bobot = 0;
            komponen.set(select, { status: status, bobot: bobot, bobotPenuh: 0 });
        });

        form.querySelectorAll('[data-item]').forEach(function (select) {
            const statusSelect = select.closest('.space-y-2').querySelector('[data-status]');
            if (!statusSelect || !komponen.has(statusSelect)) return;
            const data = komponen.get(statusSelect);
            const nilai = parseFloat(select.value || '0');
            const bobot = parseFloat(select.dataset.bobot || '0');
            data.bobot += (nilai / 100) * bobot;
            data.bobotPenuh += bobot;
        });

        let total = 0;
        komponen.forEach(function (data) {
            if (data.bobotPenuh <= 0) return;
            total += data.status * (data.bobot / data.bobotPenuh);
        });

        const hasil = Math.round(Math.min(100, Math.max(0, total)) * 100) / 100;
        skorTeks.textContent = hasil.toFixed(2);
        if ([...form.querySelectorAll('[data-status]')].some(function (s) { return s.value !== ''; })) {
            skorInput.value = hasil.toFixed(2);
        }
    }

    form.querySelectorAll('select').forEach(function (select) {
        select.addEventListener('change', hitung);
    });
    hitung();
})();
</script>

@endsection


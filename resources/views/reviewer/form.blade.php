@extends('layouts.app')

@section('title', 'Borang Telaah Substansi (Double-Blind) - PRISMA UHN')

@section('content')
<div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <x-sidebar />

    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        <header class="bg-white border-b border-slate-200 py-4 sticky top-0 z-30 shadow-sm">
            <div class="w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <a href="{{ route('reviewer.penilaian.index') }}" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center shrink-0 transition" title="Kembali ke Daftar Penugasan" aria-label="Kembali ke Daftar Penugasan">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </a>
                    <div>
                        <h1 class="font-extrabold text-xl text-slate-900 leading-tight">Borang Penilaian Substansi</h1>
                        <p class="text-xs font-semibold text-slate-500">Skala Penilaian 1 - 7 Standar BIMA Kemendikbudristek</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-black bg-purple-100 text-purple-800 border border-purple-300 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        Double-Blind Active
                    </span>
                    @if($penugasan->status_penugasan === 'completed')
                        <span class="px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            Locked (Permanen)
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full text-xs font-black bg-amber-100 text-amber-800 border border-amber-300">
                            Draft Penilaian
                        </span>
                    @endif
                </div>
            </div>
        </header>

        <main class="flex-grow w-full px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            @if(session('success'))
                <div class="p-4.5 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-sm font-bold flex items-center gap-3 shadow-sm">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4.5 rounded-2xl bg-red-50 border border-red-300 text-red-900 text-sm font-bold flex items-center gap-3 shadow-sm">
                    <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="p-4.5 rounded-2xl bg-red-50 border border-red-300 text-red-900 text-xs font-semibold shadow-sm">
                    <p class="font-bold text-sm mb-1">Mohon perbaiki kesalahan berikut:</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Proposal Information (Double Blind Protected) -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-5">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2.5 py-0.5 rounded-md text-[11px] font-mono font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                {{ $usulan->kode_usulan }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-700">
                                {{ $usulan->skema->nama_skema ?? 'Skema Hibah' }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-700">
                                Thn {{ $usulan->periode->tahun_anggaran ?? date('Y') }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                Peran: {{ strtoupper(str_replace('_', ' ', $penugasan->peran_reviewer)) }}
                            </span>
                        </div>
                        <h2 class="text-xl font-black text-slate-900 leading-snug">
                            {{ $usulan->judul_usulan }}
                        </h2>
                    </div>

                    @if($usulan->file_proposal_path)
                    <div class="shrink-0">
                        <a href="{{ route('usulan.document', $usulan) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-extrabold border border-blue-200 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Unduh Naskah Proposal</span>
                        </a>
                    </div>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-xs">
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-400 font-bold uppercase tracking-wider block text-[10px]">Fokus RIRN</span>
                        <span class="font-extrabold text-slate-800 text-sm mt-0.5 block">{{ $usulan->fokus_rirn ?? '-' }}</span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-400 font-bold uppercase tracking-wider block text-[10px]">Rumpun Ilmu</span>
                        <span class="font-bold text-slate-800 text-xs mt-0.5 block truncate">{{ $usulan->rumpun_ilmu_level_1 ?? '-' }}</span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-400 font-bold uppercase tracking-wider block text-[10px]">Target TKT</span>
                        <span class="font-bold text-slate-800 text-xs mt-0.5 block">Level: {{ $usulan->target_tkt ?? '-' }}</span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-400 font-bold uppercase tracking-wider block text-[10px]">Usulan Anggaran (RAB)</span>
                        <span class="font-black text-blue-700 text-sm mt-0.5 block">Rp {{ number_format($usulan->total_rab ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>

                @if($usulan->ringkasan_substansi)
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Ringkasan Eksekutif Proposal</h3>
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs leading-relaxed text-slate-700">
                        {{ $usulan->ringkasan_substansi }}
                    </div>
                </div>
                @endif
            </div>

            @php
                $isLocked = $penugasan->status_penugasan === 'completed' || ($penilaian && $penilaian->is_locked);
                $initialScores = $penilaian?->skor_kriteria ?? [];
                $rubrikArray = is_array($rubrikList) ? $rubrikList : [];
            @endphp

            <!-- Interactive Rubric Scoring Form (Alpine.js State) -->
            <div x-data="{
                scores: {{ json_encode($initialScores) }},
                rubrik: {{ json_encode($rubrikArray) }},
                get totalScore() {
                    let total = 0;
                    for (let item of this.rubrik) {
                        let val = parseFloat(this.scores[item.id] || 0);
                        let bobot = parseFloat(item.bobot || 0);
                        total += (val * bobot);
                    }
                    return Math.round(total * 100) / 100;
                },
                confirmSubmit(e) {
                    if (!confirm('PERHATIAN: Setelah Anda menekan tombol Kirim, borang penilaian ini akan DIKUNCI PERMANEN dan tidak dapat diubah lagi demi menjaga integritas Double-Blind review. Lanjutkan pengiriman?')) {
                        e.preventDefault();
                    }
                }
            }" class="space-y-6">

                <form action="{{ route('reviewer.penilaian.store', $penugasan) }}" method="POST" @submit="confirmSubmit($event)">
                    @csrf

                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden mb-6">
                        <div class="p-6 border-b border-slate-200 bg-slate-50/70 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-extrabold text-slate-900">Kriteria Penilaian Substansi BIMA</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Beri skor 1 (Sangat Buruk) hingga 7 (Istimewa) untuk masing-masing kriteria.</p>
                            </div>
                            <!-- Live Score Display Card -->
                            <div class="px-5 py-3 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-700 text-white shadow-md flex items-center gap-4">
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-blue-200 block">Total Nilai Terbobot</span>
                                    <span class="text-2xl font-black font-mono tracking-tight" x-text="totalScore.toFixed(2)">
                                        {{ number_format($penilaian?->total_skor ?? 0, 2) }}
                                    </span>
                                </div>
                                <div class="text-right border-l border-blue-400/40 pl-3">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-blue-200 block">Skala Maksimum</span>
                                    <span class="text-sm font-bold font-mono">700.00</span>
                                </div>
                            </div>
                        </div>

                        <!-- Rubric Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-100 text-slate-600 font-extrabold uppercase border-b border-slate-200">
                                    <tr>
                                        <th class="px-6 py-4 w-12 text-center">No</th>
                                        <th class="px-6 py-4">Kriteria Penilaian & Deskripsi</th>
                                        <th class="px-6 py-4 w-24 text-center">Bobot (%)</th>
                                        <th class="px-6 py-4 w-96 text-center">Skor (1 - 7)</th>
                                        <th class="px-6 py-4 w-28 text-right">Nilai Terbobot</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 font-medium">
                                    @forelse($rubrikArray as $index => $item)
                                        @php
                                            $itemId = $item['id'];
                                            $savedScore = $initialScores[$itemId] ?? null;
                                        @endphp
                                        <tr class="hover:bg-slate-50/80 transition-colors">
                                            <td class="px-6 py-4 text-center font-bold text-slate-400">
                                                {{ $index + 1 }}
                                            </td>
                                            <td class="px-6 py-4">
                                                <p class="font-bold text-slate-900 text-sm">{{ $item['kriteria'] }}</p>
                                                @if(!empty($item['keterangan']))
                                                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">{{ $item['keterangan'] }}</p>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-center font-black text-slate-700 text-sm">
                                                {{ $item['bobot'] }}%
                                            </td>
                                            <td class="px-6 py-4">
                                                @if($isLocked)
                                                    <div class="flex items-center justify-center">
                                                        <span class="w-10 h-10 rounded-xl bg-blue-100 text-blue-800 font-black text-base flex items-center justify-center border border-blue-300">
                                                            {{ $savedScore ?? '-' }}
                                                        </span>
                                                    </div>
                                                @else
                                                    <!-- 1-7 Score Selector -->
                                                    <div class="flex items-center justify-between gap-1 bg-slate-100 p-1.5 rounded-2xl border border-slate-200">
                                                        @for($s = 1; $s <= 7; $s++)
                                                            <label class="flex-1 text-center cursor-pointer">
                                                                <input type="radio" 
                                                                       name="scores[{{ $itemId }}]" 
                                                                       value="{{ $s }}" 
                                                                       x-model="scores['{{ $itemId }}']" 
                                                                       required 
                                                                       class="sr-only peer">
                                                                <span class="block py-2 rounded-xl font-extrabold text-xs text-slate-600 peer-checked:bg-blue-600 peer-checked:text-white peer-checked:shadow-sm hover:bg-slate-200 transition">
                                                                    {{ $s }}
                                                                </span>
                                                            </label>
                                                        @endfor
                                                    </div>
                                                    <div class="flex justify-between text-[9px] font-bold text-slate-400 px-1 mt-1">
                                                        <span>1 = Sangat Buruk</span>
                                                        <span>4 = Cukup</span>
                                                        <span>7 = Istimewa</span>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-right font-mono font-bold text-sm text-slate-800">
                                                @if($isLocked)
                                                    {{ number_format(($savedScore ?? 0) * ($item['bobot'] ?? 0), 2) }}
                                                @else
                                                    <span x-text="((parseFloat(scores['{{ $itemId }}'] || 0)) * {{ (float) ($item['bobot'] ?? 0) }}).toFixed(2)">0.00</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-8 text-center text-slate-400">
                                                Tidak ada rubrik kriteria untuk skema ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="bg-slate-50 border-t-2 border-slate-200">
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-right font-extrabold text-slate-700 text-sm">
                                            TOTAL SKOR AKHIR SUBSTANSI:
                                        </td>
                                        <td class="px-6 py-4 text-right font-mono font-black text-lg text-blue-700">
                                            @if($isLocked)
                                                {{ number_format($penilaian?->total_skor ?? 0, 2) }}
                                            @else
                                                <span x-text="totalScore.toFixed(2)">0.00</span>
                                            @endif
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Qualitative Comments & Recommendation -->
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-6">
                        <div>
                            <label for="komentar_kualitatif" class="block text-sm font-extrabold text-slate-900 mb-1">
                                Komentar & Catatan Kualitatif Reviewer <span class="text-red-500">*</span>
                            </label>
                            <p class="text-xs text-slate-500 mb-3">
                                Uraikan kelebihan, kelemahan, kelayakan metodologi, serta saran perbaikan substansial (wajib diisi minimal 10 karakter).
                            </p>
                            @if($isLocked)
                                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-800 leading-relaxed font-normal whitespace-pre-line">
                                    {{ $penilaian->komentar_kualitatif ?? 'Tidak ada komentar kualitatif.' }}
                                </div>
                            @else
                                <textarea id="komentar_kualitatif" 
                                          name="komentar_kualitatif" 
                                          rows="6" 
                                          required 
                                          minlength="10" 
                                          maxlength="10000"
                                          placeholder="Tuliskan telaah kritis dan konstruktif terhadap usulan proposal ini..."
                                          class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-300 text-slate-800 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none focus:bg-white transition">{{ old('komentar_kualitatif', $penilaian?->komentar_kualitatif) }}</textarea>
                            @endif
                        </div>

                        <div>
                            <label class="block text-sm font-extrabold text-slate-900 mb-1">
                                Rekomendasi Kelayakan Akhir <span class="text-red-500">*</span>
                            </label>
                            <p class="text-xs text-slate-500 mb-3">Pilih rekomendasi kelayakan pendanaan usulan proposal ini.</p>

                            @if($isLocked)
                                <div class="inline-flex items-center gap-2">
                                    @php
                                        $rek = $penilaian?->rekomendasi;
                                    @endphp
                                    @if($rek === 'layak')
                                        <span class="px-4 py-2 rounded-xl text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                            Rekomendasi: Diterima / Layak Didanai
                                        </span>
                                    @elseif($rek === 'revisi')
                                        <span class="px-4 py-2 rounded-xl text-xs font-extrabold bg-amber-100 text-amber-800 border border-amber-300">
                                            Rekomendasi: Layak dengan Revisi Minor
                                        </span>
                                    @else
                                        <span class="px-4 py-2 rounded-xl text-xs font-extrabold bg-rose-100 text-rose-800 border border-rose-300">
                                            Rekomendasi: Tidak Layak Didanai
                                        </span>
                                    @endif
                                </div>
                            @else
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <label class="relative flex items-center gap-3 p-4 rounded-2xl border-2 border-slate-200 hover:border-emerald-500 cursor-pointer transition">
                                        <input type="radio" name="rekomendasi" value="layak" {{ old('rekomendasi', $penilaian?->rekomendasi) === 'layak' ? 'checked' : '' }} required class="text-emerald-600 focus:ring-emerald-500">
                                        <div>
                                            <span class="block text-xs font-extrabold text-emerald-800">Layak Didanai</span>
                                            <span class="block text-[11px] text-slate-500">Kualitas substansi memenuhi standar</span>
                                        </div>
                                    </label>
                                    <label class="relative flex items-center gap-3 p-4 rounded-2xl border-2 border-slate-200 hover:border-amber-500 cursor-pointer transition">
                                        <input type="radio" name="rekomendasi" value="revisi" {{ old('rekomendasi', $penilaian?->rekomendasi) === 'revisi' ? 'checked' : '' }} required class="text-amber-600 focus:ring-amber-500">
                                        <div>
                                            <span class="block text-xs font-extrabold text-amber-800">Layak dengan Revisi</span>
                                            <span class="block text-[11px] text-slate-500">Diperlukan penyempurnaan minor</span>
                                        </div>
                                    </label>
                                    <label class="relative flex items-center gap-3 p-4 rounded-2xl border-2 border-slate-200 hover:border-rose-500 cursor-pointer transition">
                                        <input type="radio" name="rekomendasi" value="tidak_layak" {{ old('rekomendasi', $penilaian?->rekomendasi) === 'tidak_layak' ? 'checked' : '' }} required class="text-rose-600 focus:ring-rose-500">
                                        <div>
                                            <span class="block text-xs font-extrabold text-rose-800">Tidak Layak</span>
                                            <span class="block text-[11px] text-slate-500">Substansi di bawah standar minimal</span>
                                        </div>
                                    </label>
                                </div>
                            @endif
                        </div>

                        @if(!$isLocked)
                            <div class="pt-4 border-t border-slate-200 flex items-center justify-between">
                                <a href="{{ route('reviewer.penilaian.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                                    Batal / Kembali
                                </a>
                                <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-extrabold text-xs shadow-lg hover:shadow-xl transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Kirim & Kunci Permanen Penilaian</span>
                                </button>
                            </div>
                        @else
                            <div class="pt-4 border-t border-slate-200 flex items-center justify-between text-xs text-slate-500">
                                <span>Penilaian ini dikirim dan dikunci pada: <strong>{{ $penilaian->submitted_at ? $penilaian->submitted_at->isoFormat('D MMMM Y, HH:mm') : '-' }} WIB</strong></span>
                                <a href="{{ route('reviewer.penilaian.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
                                    Kembali ke Daftar Penugasan
                                </a>
                            </div>
                        @endif
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
@endsection

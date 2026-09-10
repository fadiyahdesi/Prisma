@extends('layouts.app')

@section('title', 'Konfigurasi Rubrik Reviewer Skala 1-7 - ' . $skemaBima->kode_skema)

@section('content')
<div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <x-sidebar />

    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        <!-- Header -->
        <header class="bg-white border-b border-slate-200 py-4 sticky top-0 z-30 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-3.5">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <a href="{{ route('admin.skema-bima.index') }}" class="w-10 h-10 rounded-2xl bg-blue-700 flex items-center justify-center text-white font-black text-lg shadow-md">
                        &larr;
                    </a>
                    <div>
                        <h1 class="font-extrabold text-xl text-slate-900 leading-tight">Editor Rubrik Penilaian Reviewer (JSONB)</h1>
                        <p class="text-xs font-semibold text-slate-500">Skema: <strong class="text-blue-900 font-extrabold">{{ $skemaBima->nama_skema }} ({{ $skemaBima->kode_skema }})</strong> &bull; Skala Numerik 1-7 (US-04.3)</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.skema-bima.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-extrabold border border-slate-300">
                        Kembali ke Skema
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Container -->
        <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <!-- Flash Messages -->
        @if(session('error'))
            <div class="p-4.5 rounded-2xl bg-rose-50 border-2 border-rose-300 text-rose-950 text-sm font-extrabold flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5 text-rose-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xl space-y-6">
            <div class="pb-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <span class="text-xs font-black text-purple-900 bg-purple-100 px-3 py-1 rounded-md border border-purple-300 uppercase tracking-wider">
                        Kriteria Standar BIMA Berskala 1-7 (JSONB)
                    </span>
                    <h3 class="text-xl font-extrabold text-slate-900 mt-1">Konfigurasi Bobot & Deskriptor Penilaian</h3>
                </div>

                <!-- Total Weight Accumulation Counter -->
                <div class="p-3.5 rounded-2xl border-2 flex items-center gap-3 transition-all" id="counterBox">
                    <div class="text-right">
                        <span class="text-[11px] font-black uppercase block text-slate-500">Total Akumulasi Bobot:</span>
                        <strong class="text-2xl font-black font-mono" id="totalWeightDisplay">{{ $totalBobot }}%</strong>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-extrabold shadow-sm" id="badgeStatus">
                        {{ $totalBobot == 100 ? 'VALID (100%)' : 'INVALID (!= 100%)' }}
                    </span>
                </div>
            </div>

            <form action="{{ route('admin.skema-bima.update-rubrik', $skemaBima) }}" method="POST" id="formRubrik" class="space-y-6">
                @csrf
                <div id="criteriaContainer" class="space-y-6">
                    @foreach($rubrikList as $index => $item)
                        <div class="p-5 rounded-2xl bg-slate-50 border-2 border-slate-200 space-y-4 criteria-row">
                            <div class="flex items-center justify-between gap-2 border-b border-slate-200 pb-3">
                                <span class="text-xs font-black text-blue-900 bg-blue-100 px-2.5 py-0.5 rounded border border-blue-300 uppercase">
                                    Kriteria #{{ $index + 1 }}
                                </span>
                                <button type="button" onclick="removeCriteria(this)" class="text-xs font-bold text-rose-600 hover:text-rose-800">
                                    &times; Hapus Kriteria
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="md:col-span-3">
                                    <label class="text-xs font-extrabold text-slate-800 uppercase block mb-1">Nama Kriteria Penilaian:</label>
                                    <input type="text" 
                                           name="rubrik[{{ $index }}][kriteria]" 
                                           value="{{ old("rubrik.{$index}.kriteria", $item['kriteria'] ?? '') }}" 
                                           required 
                                           placeholder="Contoh: Kualifikasi & Rekam Jejak Tim Pengusul"
                                           class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 font-bold text-sm text-slate-900">
                                </div>
                                <div>
                                    <label class="text-xs font-extrabold text-slate-800 uppercase block mb-1">Bobot Persentase (%):</label>
                                    <input type="number" 
                                           step="0.5"
                                           min="1"
                                           max="100"
                                           name="rubrik[{{ $index }}][bobot]" 
                                           value="{{ old("rubrik.{$index}.bobot", $item['bobot'] ?? 25) }}" 
                                           required 
                                           oninput="calculateTotalWeight()"
                                           class="w-full px-4 py-2.5 rounded-xl bg-white border-2 border-blue-400 font-mono font-extrabold text-sm text-slate-900 weight-input">
                                </div>
                            </div>

                            <div>
                                <label class="text-xs font-extrabold text-slate-800 uppercase block mb-1">Deskripsi Ringkas Penilaian:</label>
                                <input type="text" 
                                       name="rubrik[{{ $index }}][deskripsi]" 
                                       value="{{ old("rubrik.{$index}.deskripsi", $item['deskripsi'] ?? '') }}" 
                                       placeholder="Panduan penilaian untuk reviewer substantive..."
                                       class="w-full px-4 py-2 rounded-xl bg-white border border-slate-200 text-xs font-semibold text-slate-700">
                            </div>

                            <!-- Skala 1-7 Descriptors -->
                            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 pt-2">
                                <div>
                                    <label class="text-[11px] font-black text-rose-800 uppercase block mb-1">Skala 1-2 (Sangat Kurang):</label>
                                    <input type="text" name="rubrik[{{ $index }}][skala_1_2]" value="{{ old("rubrik.{$index}.skala_1_2", $item['skala']['1_2'] ?? 'Sangat Kurang') }}" class="w-full px-3 py-1.5 rounded-lg bg-white border border-slate-300 text-xs text-slate-700">
                                </div>
                                <div>
                                    <label class="text-[11px] font-black text-amber-800 uppercase block mb-1">Skala 3-4 (Cukup):</label>
                                    <input type="text" name="rubrik[{{ $index }}][skala_3_4]" value="{{ old("rubrik.{$index}.skala_3_4", $item['skala']['3_4'] ?? 'Cukup') }}" class="w-full px-3 py-1.5 rounded-lg bg-white border border-slate-300 text-xs text-slate-700">
                                </div>
                                <div>
                                    <label class="text-[11px] font-black text-blue-800 uppercase block mb-1">Skala 5-6 (Baik):</label>
                                    <input type="text" name="rubrik[{{ $index }}][skala_5_6]" value="{{ old("rubrik.{$index}.skala_5_6", $item['skala']['5_6'] ?? 'Baik') }}" class="w-full px-3 py-1.5 rounded-lg bg-white border border-slate-300 text-xs text-slate-700">
                                </div>
                                <div>
                                    <label class="text-[11px] font-black text-emerald-800 uppercase block mb-1">Skala 7 (Sangat Baik):</label>
                                    <input type="text" name="rubrik[{{ $index }}][skala_7]" value="{{ old("rubrik.{$index}.skala_7", $item['skala']['7'] ?? 'Sangat Baik / Sangat Layak') }}" class="w-full px-3 py-1.5 rounded-lg bg-white border border-slate-300 text-xs text-slate-700">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-slate-200">
                    <button type="button" onclick="addCriteria()" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-extrabold border border-slate-300">
                        + Tambah Baris Kriteria
                    </button>

                    <button type="submit" id="btnSubmitRubrik" class="px-7 py-3.5 rounded-2xl bg-purple-700 hover:bg-purple-800 text-white font-black text-sm shadow-xl transition-all">
                        Simpan Rubrik JSONB (Wajib 100%) &rarr;
                    </button>
                </div>
            </form>
        </div>
    </main>
    </div>
</div>

<script>
function calculateTotalWeight() {
    const inputs = document.querySelectorAll('.weight-input');
    let total = 0;
    inputs.forEach(input => {
        total += parseFloat(input.value) || 0;
    });

    const display = document.getElementById('totalWeightDisplay');
    const box = document.getElementById('counterBox');
    const badge = document.getElementById('badgeStatus');
    const btn = document.getElementById('btnSubmitRubrik');

    display.textContent = total + '%';

    if (Math.abs(total - 100) < 0.01) {
        box.className = 'p-3.5 rounded-2xl border-2 flex items-center gap-3 transition-all bg-emerald-50 border-emerald-300 text-emerald-950';
        badge.className = 'px-3 py-1 rounded-full text-xs font-black bg-emerald-600 text-white shadow-sm';
        badge.textContent = 'VALID (100%)';
        btn.disabled = false;
        btn.classList.remove('opacity-50', 'cursor-not-allowed');
    } else {
        box.className = 'p-3.5 rounded-2xl border-2 flex items-center gap-3 transition-all bg-rose-50 border-rose-300 text-rose-950';
        badge.className = 'px-3 py-1 rounded-full text-xs font-black bg-rose-600 text-white shadow-sm';
        badge.textContent = 'INVALID (!= 100%)';
    }
}

let criteriaCount = {{ count($rubrikList) }};

function addCriteria() {
    const container = document.getElementById('criteriaContainer');
    const index = criteriaCount++;
    
    const html = `
    <div class="p-5 rounded-2xl bg-slate-50 border-2 border-slate-200 space-y-4 criteria-row">
        <div class="flex items-center justify-between gap-2 border-b border-slate-200 pb-3">
            <span class="text-xs font-black text-blue-900 bg-blue-100 px-2.5 py-0.5 rounded border border-blue-300 uppercase">
                Kriteria #${index + 1}
            </span>
            <button type="button" onclick="removeCriteria(this)" class="text-xs font-bold text-rose-600 hover:text-rose-800">
                &times; Hapus Kriteria
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-3">
                <label class="text-xs font-extrabold text-slate-800 uppercase block mb-1">Nama Kriteria Penilaian:</label>
                <input type="text" name="rubrik[${index}][kriteria]" required placeholder="Contoh: Kriteria Penilaian Baru" class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 font-bold text-sm text-slate-900">
            </div>
            <div>
                <label class="text-xs font-extrabold text-slate-800 uppercase block mb-1">Bobot Persentase (%):</label>
                <input type="number" step="0.5" min="1" max="100" name="rubrik[${index}][bobot]" value="10" required oninput="calculateTotalWeight()" class="w-full px-4 py-2.5 rounded-xl bg-white border-2 border-blue-400 font-mono font-extrabold text-sm text-slate-900 weight-input">
            </div>
        </div>

        <div>
            <label class="text-xs font-extrabold text-slate-800 uppercase block mb-1">Deskripsi Ringkas Penilaian:</label>
            <input type="text" name="rubrik[${index}][deskripsi]" placeholder="Deskripsi kriteria..." class="w-full px-4 py-2 rounded-xl bg-white border border-slate-200 text-xs font-semibold text-slate-700">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 pt-2">
            <div><label class="text-[11px] font-black text-rose-800 uppercase block mb-1">Skala 1-2:</label><input type="text" name="rubrik[${index}][skala_1_2]" value="Sangat Kurang" class="w-full px-3 py-1.5 rounded-lg bg-white border border-slate-300 text-xs text-slate-700"></div>
            <div><label class="text-[11px] font-black text-amber-800 uppercase block mb-1">Skala 3-4:</label><input type="text" name="rubrik[${index}][skala_3_4]" value="Cukup" class="w-full px-3 py-1.5 rounded-lg bg-white border border-slate-300 text-xs text-slate-700"></div>
            <div><label class="text-[11px] font-black text-blue-800 uppercase block mb-1">Skala 5-6:</label><input type="text" name="rubrik[${index}][skala_5_6]" value="Baik" class="w-full px-3 py-1.5 rounded-lg bg-white border border-slate-300 text-xs text-slate-700"></div>
            <div><label class="text-[11px] font-black text-emerald-800 uppercase block mb-1">Skala 7:</label><input type="text" name="rubrik[${index}][skala_7]" value="Sangat Baik" class="w-full px-3 py-1.5 rounded-lg bg-white border border-slate-300 text-xs text-slate-700"></div>
        </div>
    </div>`;

    container.insertAdjacentHTML('beforeend', html);
    calculateTotalWeight();
}

function removeCriteria(btn) {
    btn.closest('.criteria-row').remove();
    calculateTotalWeight();
}

document.addEventListener('DOMContentLoaded', calculateTotalWeight);
</script>
@endsection


@props(['catalog' => null, 'compact' => false])

@php
    $tktData = $catalog ?? \App\Services\RumpunIlmuService::getTktMeterCatalog();
@endphp

<section id="tkt-meter"
         x-data="tktMeterWidget(@js($tktData))"
         class="{{ $compact ? 'bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xl' : 'py-16 bg-gradient-to-b from-slate-50 to-white border-t border-slate-200/80' }}">
    <div class="{{ $compact ? '' : 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8' }}">
        <!-- Header -->
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4 mb-8">
            <div>
                <span class="inline-flex items-center gap-1.5 text-[11px] font-black text-blue-800 bg-blue-100 border border-blue-200 px-3 py-1 rounded-full uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                    TKT-Meter UHN &bull; Level 1 – 9 (Permenristekdikti No. 42/2016)
                </span>
                <h2 class="mt-3 text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Pengukur Tingkat Kesiapterapan Teknologi (TKT Meter)
                </h2>
                <p class="mt-1.5 text-xs sm:text-sm text-slate-600 font-medium max-w-3xl">
                    Evaluasi tingkat kematangan inovasi riset Anda secara interaktif per bidang teknologi. Suatu level dinyatakan terpenuhi apabila indikator kesiapan mencapai ambang batas <strong class="text-slate-900">&ge; 80%</strong>.
                </p>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <div class="px-4 py-2.5 rounded-2xl bg-slate-900 text-white shadow-md flex items-center gap-3">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">TKT Terukur Saat Ini</span>
                        <span class="text-lg font-black text-emerald-400" x-text="'Level ' + highestPassedLevel"></span>
                    </div>
                    <div class="h-8 w-px bg-slate-700"></div>
                    <div>
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Rekomendasi Skema</span>
                        <span class="text-xs font-extrabold text-blue-300" x-text="recommendedScheme"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Tabs -->
        <div class="mb-6">
            <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-2">
                1. Pilih Kategori / Bidang Inovasi Teknologi:
            </label>
            <div class="flex flex-wrap gap-2">
                <template x-for="(catObj, catKey) in catalog" :key="catKey">
                    <button type="button"
                            @click="selectCategory(catKey)"
                            :class="category === catKey ? 'bg-[#681727] text-white border-[#681727] shadow-md' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50 hover:border-slate-300'"
                            class="px-3.5 py-2 rounded-xl border text-xs font-extrabold transition cursor-pointer">
                        <span x-text="catObj.label"></span>
                    </button>
                </template>
            </div>
        </div>

        <!-- TKT 1-9 Level Progress Bar & Selector -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-2">
                <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">
                    2. Pilih &amp; Uji Indikator Level TKT (1 – 9):
                </label>
                <button type="button" @click="resetChecks()" class="text-[11px] font-bold text-slate-500 hover:text-rose-600 underline cursor-pointer">
                    Reset Simulasi
                </button>
            </div>
            <div class="grid grid-cols-3 sm:grid-cols-9 gap-2">
                <template x-for="lvl in [1,2,3,4,5,6,7,8,9]" :key="lvl">
                    <button type="button"
                            @click="activeLevel = lvl"
                            :class="{
                                'ring-2 ring-blue-600 border-blue-600 bg-blue-50 text-blue-950 shadow-sm': activeLevel === lvl,
                                'border-emerald-300 bg-emerald-50/80 text-emerald-900': activeLevel !== lvl && levelPercent(lvl) >= 80,
                                'border-slate-200 bg-white text-slate-700 hover:bg-slate-50': activeLevel !== lvl && levelPercent(lvl) < 80
                            }"
                            class="p-2.5 rounded-2xl border text-left transition cursor-pointer flex flex-col justify-between">
                        <div class="flex items-center justify-between gap-1">
                            <span class="text-[11px] font-black uppercase" x-text="'TKT ' + lvl"></span>
                            <span class="w-2 h-2 rounded-full"
                                  :class="levelPercent(lvl) >= 80 ? 'bg-emerald-500' : (levelPercent(lvl) > 0 ? 'bg-amber-400' : 'bg-slate-300')"></span>
                        </div>
                        <div class="mt-2">
                            <div class="w-full h-1.5 bg-slate-200/80 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-300"
                                     :class="levelPercent(lvl) >= 80 ? 'bg-emerald-500' : 'bg-blue-600'"
                                     :style="`width: ${levelPercent(lvl)}%`"></div>
                            </div>
                            <span class="text-[10px] font-extrabold mt-1 block" x-text="levelPercent(lvl) + '%'"></span>
                        </div>
                    </button>
                </template>
            </div>
        </div>

        <!-- Active Level Assessment Panel -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
            <!-- Left: Indicators Checklist -->
            <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-200 p-6 shadow-xs flex flex-col justify-between space-y-5">
                <div>
                    <div class="flex flex-wrap items-center justify-between gap-2 pb-4 border-b border-slate-100">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-lg bg-slate-900 text-white text-xs font-black" x-text="'LEVEL TKT ' + activeLevel"></span>
                                <span class="px-2.5 py-0.5 rounded-lg text-[11px] font-extrabold bg-indigo-50 text-indigo-800 border border-indigo-200" x-text="currentLevelObj.phase"></span>
                            </div>
                            <h3 class="text-base sm:text-lg font-extrabold text-slate-900 mt-2" x-text="currentLevelObj.title"></h3>
                        </div>
                        <div class="text-right">
                            <span class="text-2xl font-black"
                                  :class="levelPercent(activeLevel) >= 80 ? 'text-emerald-600' : 'text-blue-700'"
                                  x-text="levelPercent(activeLevel) + '%'"></span>
                            <span class="block text-[10px] font-extrabold uppercase tracking-wider"
                                  :class="levelPercent(activeLevel) >= 80 ? 'text-emerald-700' : 'text-slate-400'"
                                  x-text="levelPercent(activeLevel) >= 80 ? 'Memenuhi (>= 80%)' : 'Target >= 80%'"></span>
                        </div>
                    </div>

                    <div class="mt-4 space-y-2.5">
                        <template x-for="(ind, idx) in currentLevelObj.indicators" :key="idx">
                            <label class="flex items-start gap-3 p-3.5 rounded-2xl border transition cursor-pointer"
                                   :class="isChecked(activeLevel, idx) ? 'bg-emerald-50/70 border-emerald-300' : 'bg-slate-50/70 border-slate-200 hover:bg-slate-100/70'">
                                <input type="checkbox"
                                       :checked="isChecked(activeLevel, idx)"
                                       @change="toggleCheck(activeLevel, idx)"
                                       class="mt-0.5 h-4 w-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 cursor-pointer">
                                <span class="text-xs sm:text-sm font-semibold text-slate-800 leading-snug" x-text="ind"></span>
                            </label>
                        </template>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2">
                    <button type="button"
                            @click="markAllCurrentLevel()"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200 text-xs font-extrabold transition cursor-pointer">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>Tandai Semua Indikator Level <span x-text="activeLevel"></span> Terpenuhi</span>
                    </button>

                    <div class="flex items-center gap-2">
                        <button type="button" x-show="activeLevel > 1" @click="activeLevel--" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold cursor-pointer">
                            &larr; TKT <span x-text="activeLevel - 1"></span>
                        </button>
                        <button type="button" x-show="activeLevel < 9" @click="activeLevel++" class="px-3 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-extrabold cursor-pointer">
                            Lanjut TKT <span x-text="activeLevel + 1"></span> &rarr;
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right: TKT Gauge Summary & Scheme Mapping -->
            <div class="lg:col-span-4 bg-gradient-to-br from-slate-900 via-slate-900 to-blue-950 rounded-3xl p-6 text-white shadow-lg flex flex-col justify-between space-y-5">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-black uppercase tracking-wider text-blue-300">Ringkasan TKT Meter</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-white/10 text-emerald-300 border border-emerald-400/30" x-text="catalog[category].short"></span>
                    </div>

                    <!-- Meter Gauge -->
                    <div class="p-4 rounded-2xl bg-white/5 border border-white/10 space-y-2.5">
                        <div class="flex items-baseline justify-between">
                            <span class="text-xs font-bold text-slate-300">Posisi Kesiapan Inovasi</span>
                            <span class="text-2xl font-black text-emerald-400" x-text="'TKT ' + highestPassedLevel"></span>
                        </div>
                        <div class="grid grid-cols-9 gap-1 pt-1">
                            <template x-for="lvl in [1,2,3,4,5,6,7,8,9]" :key="'bar-' + lvl">
                                <div class="h-3 rounded-sm transition-all"
                                     :class="lvl <= highestPassedLevel ? (lvl <= 3 ? 'bg-sky-400' : (lvl <= 6 ? 'bg-amber-400' : 'bg-emerald-400')) : 'bg-slate-700'"></div>
                            </template>
                        </div>
                        <div class="flex justify-between text-[10px] font-bold text-slate-400">
                            <span>Dasar (1-3)</span>
                            <span>Terapan (4-6)</span>
                            <span>Hilirisasi (7-9)</span>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-white/5 border border-white/10 space-y-1.5 text-xs">
                        <span class="text-[10px] font-black uppercase tracking-wider text-amber-300 block">Pemetaan Skema Hibah BIMA UHN</span>
                        <p class="font-extrabold text-white text-sm" x-text="recommendedScheme"></p>
                        <p class="text-[11px] text-slate-300 leading-relaxed" x-text="schemeGuidance"></p>
                    </div>
                </div>

                @auth
                    <a href="{{ route('usulan.index', ['kategori' => 'penelitian']) }}"
                       class="w-full py-3 px-4 rounded-2xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-xs text-center transition shadow-md block">
                        Gunakan Hasil TKT di Usulan Proposal &rarr;
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="w-full py-3 px-4 rounded-2xl bg-white hover:bg-blue-50 text-slate-950 font-black text-xs text-center transition shadow-md block">
                        Masuk PRISMA untuk Ajukan Hibah &rarr;
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <script>
        if (typeof window.tktMeterWidget !== 'function') {
            window.tktMeterWidget = function(catalogData) {
                return {
                    catalog: catalogData,
                    category: Object.keys(catalogData)[0] || 'rekayasa',
                    activeLevel: 1,
                    checked: { '1': [0, 1, 2], '2': [0, 1, 2] },
                    selectCategory(cat) {
                        this.category = cat;
                    },
                    get currentLevelObj() {
                        return this.catalog[this.category].levels[this.activeLevel];
                    },
                    isChecked(lvl, idx) {
                        const arr = this.checked[String(lvl)] || [];
                        return arr.includes(idx);
                    },
                    toggleCheck(lvl, idx) {
                        const key = String(lvl);
                        const arr = this.checked[key] ? [...this.checked[key]] : [];
                        const pos = arr.indexOf(idx);
                        if (pos > -1) arr.splice(pos, 1);
                        else arr.push(idx);
                        this.checked[key] = arr;
                    },
                    markAllCurrentLevel() {
                        const count = this.currentLevelObj.indicators.length;
                        this.checked[String(this.activeLevel)] = Array.from({ length: count }, (_, i) => i);
                        if (this.activeLevel < 9) this.activeLevel++;
                    },
                    resetChecks() {
                        this.checked = {};
                        this.activeLevel = 1;
                    },
                    levelPercent(lvl) {
                        const total = this.catalog[this.category].levels[lvl].indicators.length;
                        const count = (this.checked[String(lvl)] || []).length;
                        return total > 0 ? Math.round((count / total) * 100) : 0;
                    },
                    get highestPassedLevel() {
                        let passed = 0;
                        for (let l = 1; l <= 9; l++) {
                            if (this.levelPercent(l) >= 80) passed = l;
                            else break;
                        }
                        return passed > 0 ? passed : this.activeLevel;
                    },
                    get recommendedScheme() {
                        const tkt = this.highestPassedLevel;
                        if (tkt <= 3) return 'PDP / Penelitian Fundamental (TKT 1-3)';
                        if (tkt <= 6) return 'Penelitian Terapan Unggulan (TKT 4-6)';
                        return 'Penelitian Hilirisasi & Inovasi / PISN (TKT 7-9)';
                    },
                    get schemeGuidance() {
                        const tkt = this.highestPassedLevel;
                        if (tkt <= 3) return 'Fokus pada riset dasar, validasi teori, dan pembuktian konsep (Proof of Concept) skala laboratorium.';
                        if (tkt <= 6) return 'Fokus pada pengembangan model/prototipe fungsional dan pengujian dalam lingkungan relevan bersama mitra.';
                        return 'Prototipe telah matang untuk uji lapangan nyata, sertifikasi mutu, HKI industri, dan hilirisasi komersial.';
                    }
                };
            };
        }
    </script>
</section>

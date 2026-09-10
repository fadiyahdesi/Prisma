<section id="wizard" x-data="{ currentStep: 1 }" class="py-20 bg-slate-900 text-white relative overflow-hidden">
    <!-- Ambient Lighting Background -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-indigo-600/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <!-- Header -->
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="text-xs font-bold text-blue-400 bg-blue-950 border border-blue-800 px-3.5 py-1.5 rounded-full uppercase tracking-wider">
                Alur Pengusulan Standar BIMA
            </span>
            <h2 class="mt-4 text-3xl sm:text-4xl font-extrabold tracking-tight">
                Wizard 6 Langkah Usulan Proposal
            </h2>
            <p class="mt-4 text-base text-slate-300">
                Formulir bertahap sekuensial yang dirancang identik dengan platform BIMA Kemdiktisaintek untuk membiasakan dosen UHN dengan standar pengusulan nasional.
            </p>
        </div>

        <!-- Interactive Wizard Stepper Control -->
        <div class="grid grid-cols-2 md:grid-cols-6 gap-3 mb-12">
            @foreach($wizardSteps as $w)
                <button @click="currentStep = {{ $w['step'] }}"
                        :class="currentStep === {{ $w['step'] }} ? 'bg-blue-600 border-blue-400 text-white shadow-lg shadow-blue-600/30' : 'bg-slate-800/80 border-slate-700 text-slate-400 hover:bg-slate-800 hover:text-slate-200'"
                        class="p-4 rounded-2xl border text-left transition-all duration-300 flex flex-col justify-between h-28 group">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-extrabold px-2 py-0.5 rounded-md" 
                              :class="currentStep === {{ $w['step'] }} ? 'bg-white text-blue-800' : 'bg-slate-700 text-slate-300'">
                            Step {{ $w['step'] }}
                        </span>
                        <svg class="w-4 h-4 opacity-50 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                    <span class="text-xs font-bold leading-tight" :class="currentStep === {{ $w['step'] }} ? 'text-white' : 'text-slate-300'">
                        {{ $w['title'] }}
                    </span>
                </button>
            @endforeach
        </div>

        <!-- Wizard Step Preview Display Box -->
        <div class="bg-slate-800/90 rounded-3xl border border-slate-700/80 p-8 lg:p-10 shadow-2xl backdrop-blur-sm">
            @foreach($wizardSteps as $w)
                <div x-show="currentStep === {{ $w['step'] }}" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    
                    <div class="lg:col-span-6 space-y-4">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs font-bold uppercase">
                            <span>Langkah Ke-{{ $w['step'] }} dari 6</span>
                        </div>
                        <h3 class="text-2xl font-bold text-white">{{ $w['title'] }}</h3>
                        <p class="text-sm text-slate-300 leading-relaxed">{{ $w['desc'] }}</p>

                        <!-- Special Details per Step -->
                        <div class="pt-4 space-y-2 text-xs text-slate-300">
                            @if($w['step'] == 1)
                                <div class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span> Validasi otomatis skor SINTA 3Yr & Jabatan Fungsional pengusul.</div>
                                <div class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span> Pemilihan Rumpun Ilmu Level 1 (Utama), Level 2, & Level 3.</div>
                                <div class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span> Form Self-Assessment indikator capaian TKT (Skala 1 - 9).</div>
                            @elseif($w['step'] == 2)
                                <div class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Autocomplete NIDN Dosen Anggota & penugasan deskripsi jobdesk.</div>
                                <div class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Integrasi NIM Mahasiswa Aktif UHN untuk pemenuhan Indikator IKU-2.</div>
                                <div class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Input Titik Koordinat Peta & Surat Kesediaan Kerja Sama Mitra.</div>
                            @elseif($w['step'] == 3)
                                <div class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Ringkasan Usulan, Latar Belakang, Tinjauan Pustaka, & Metodologi.</div>
                                <div class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Unggah Berkas Proposal PDF/A (Maksimal 5 MB) sesuai template resmi.</div>
                            @elseif($w['step'] == 4)
                                <div class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-purple-400"></span> Kalkulator 5 Pos Belanja Standar Biaya Masukan (SBM).</div>
                                <div class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-purple-400"></span> Pembatasan otomatis plafon honorarium & bahan sesuai regulasi.</div>
                            @elseif($w['step'] == 5)
                                <div class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span> Penentuan Target Luaran Wajib: Jurnal Scopus/SINTA 1-6.</div>
                                <div class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span> Target Luaran HKI: Paten Granted, Hak Cipta Buku, atau Prototipe.</div>
                            @elseif($w['step'] == 6)
                                <div class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-red-400"></span> Modul Member Consent: Seluruh anggota tim wajib klik persetujuan.</div>
                                <div class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-red-400"></span> Tombol Submit Aktif & Penguncian otomatis draf usulan.</div>
                            @endif
                        </div>
                    </div>

                    <!-- Visual Mockup Box -->
                    <div class="lg:col-span-6 bg-slate-900 rounded-2xl p-6 border border-slate-700/70 shadow-inner">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-800 text-xs font-mono text-slate-400">
                            <span class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-red-500 inline-block"></span>
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 inline-block"></span>
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span>
                                prisma.harkatnegeri.ac.id/wizard/step-{{ $w['step'] }}
                            </span>
                            <span class="text-blue-400">BIMA-Wizard v2.0</span>
                        </div>

                        <div class="mt-5 space-y-4 font-mono text-xs">
                            <div class="p-4 rounded-xl bg-slate-800/90 border border-slate-700/60 flex items-center justify-between">
                                <span class="text-slate-300">Skema Hibah Terpilih:</span>
                                <span class="text-emerald-400 font-bold">Penelitian Dosen Pemula (PDP)</span>
                            </div>
                            <div class="p-4 rounded-xl bg-slate-800/90 border border-slate-700/60 flex items-center justify-between">
                                <span class="text-slate-300">Status Validasi System:</span>
                                <span class="text-blue-400 font-bold">Eligible (SINTA Score: 185)</span>
                            </div>
                            <div class="p-4 rounded-xl bg-slate-800/90 border border-slate-700/60 flex items-center justify-between">
                                <span class="text-slate-300">Persetujuan Anggota (Consent):</span>
                                <span class="text-amber-400 font-bold">2/2 Anggota Approved</span>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-between">
                            <button @click="currentStep = Math.max(1, currentStep - 1)" 
                                    :disabled="currentStep === 1"
                                    class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-800 text-slate-300 hover:bg-slate-700 disabled:opacity-40 transition-colors">
                                &larr; Langkah Sebelumnya
                            </button>
                            <button @click="currentStep = Math.min(6, currentStep + 1)" 
                                    :disabled="currentStep === 6"
                                    class="px-4 py-2 rounded-xl text-xs font-bold bg-blue-600 text-white hover:bg-blue-500 disabled:opacity-40 transition-colors">
                                Langkah Selanjutnya &rarr;
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>


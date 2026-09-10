<section id="tools" class="py-20 bg-slate-50 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="text-xs font-bold text-blue-700 bg-blue-50 border border-blue-200 px-3.5 py-1.5 rounded-full uppercase tracking-wider">
                Simulasi & Tools Interaktif Dosen
            </span>
            <h2 class="mt-4 text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                Uji Eligibilitas & Validasi QR SPK Digital
            </h2>
            <p class="mt-4 text-base text-slate-600">
                Gunakan modul simulator interaktif di bawah ini untuk mengecek skema hibah yang berhak Anda daftarkan atau memverifikasi keaslian dokumen SPK digital UHN.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Tool A: SINTA & TKT Eligibility Checker Simulator -->
            <div x-data="{
                jafung: 'Lektor',
                sintaScore: 120,
                targetTkt: 3,
                get eligibleSchemes() {
                    let results = [];
                    if (this.sintaScore >= 50) results.push('Penelitian Dosen Pemula (PDP)');
                    if (this.jafung !== 'Tenaga Pengajar' && this.sintaScore >= 100) results.push('Penelitian Fundamental (PF)');
                    if (['Lektor Kepala', 'Guru Besar'].includes(this.jafung) && this.sintaScore >= 200 && this.targetTkt >= 6) results.push('Hilirisasi Prototipe & Inovasi (PISN)');
                    if (this.sintaScore >= 50) results.push('Pemberdayaan Masyarakat Pemula (PMP)');
                    if (this.sintaScore >= 100) results.push('Pemberdayaan Kemitraan Masyarakat (PKM)');
                    results.push('Insentif Publikasi Jurnal & HKI');
                    return results;
                }
            }" class="bg-white rounded-3xl p-8 border border-slate-200 shadow-xl shadow-slate-200/50 flex flex-col justify-between">
                <div>
                    <!-- Header -->
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold shadow-inner">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-blue-700 uppercase tracking-wide">Simulator 1</span>
                            <h3 class="text-xl font-bold text-slate-900">Cek Eligibilitas SINTA & TKT</h3>
                        </div>
                    </div>

                    <!-- Input Controls -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Jabatan Fungsional (Jafung):</label>
                            <select x-model="jafung" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-blue-600 focus:outline-none">
                                <option value="Tenaga Pengajar">Tenaga Pengajar / Dosen Baru</option>
                                <option value="Asisten Ahli">Asisten Ahli</option>
                                <option value="Lektor">Lektor</option>
                                <option value="Lektor Kepala">Lektor Kepala</option>
                                <option value="Guru Besar">Guru Besar (Profesor)</option>
                            </select>
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label class="text-xs font-bold text-slate-700">Skor SINTA 3-Years:</label>
                                <span class="text-xs font-extrabold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md" x-text="sintaScore + ' Poin'"></span>
                            </div>
                            <input type="range" min="0" max="400" step="5" x-model="sintaScore" class="w-full accent-blue-600 cursor-pointer">
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label class="text-xs font-bold text-slate-700">Target Level TKT (1 - 9):</label>
                                <span class="text-xs font-extrabold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md" x-text="'TKT Level ' + targetTkt"></span>
                            </div>
                            <input type="range" min="1" max="9" step="1" x-model="targetTkt" class="w-full accent-emerald-600 cursor-pointer">
                        </div>
                    </div>

                    <!-- Results Output Box -->
                    <div class="mt-6 p-4 rounded-2xl bg-blue-50/70 border border-blue-200/80">
                        <span class="text-xs font-bold text-blue-800 uppercase tracking-wide flex items-center gap-1 mb-2">
                            <span>Skema Yang Berhak Anda Daftar</span>
                            <span>(<span x-text="eligibleSchemes.length"></span> Skema):</span>
                        </span>
                        <div class="space-y-1.5">
                            <template x-for="s in eligibleSchemes" :key="s">
                                <div class="flex items-center gap-2 text-xs font-bold text-slate-800 bg-white p-2 rounded-xl shadow-xs border border-blue-100">
                                    <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span x-text="s"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tool B: QR Code SPK Digital Signature Validator Simulator -->
            <div x-data="{
                spkCode: 'SPK-UHN/FST/2026/089',
                isValidated: false,
                isVerifying: false,
                verifyCode() {
                    this.isVerifying = true;
                    setTimeout(() => {
                        this.isVerifying = false;
                        this.isValidated = true;
                    }, 600);
                }
            }" class="bg-white rounded-3xl p-8 border border-slate-200 shadow-xl shadow-slate-200/50 flex flex-col justify-between">
                <div>
                    <!-- Header -->
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold shadow-inner">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-indigo-700 uppercase tracking-wide">Simulator 2</span>
                            <h3 class="text-xl font-bold text-slate-900">Validasi QR Code SPK Digital</h3>
                        </div>
                    </div>

                    <!-- Input Box -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nomor SPK / Perjanjian Kontrak Digital:</label>
                            <div class="flex gap-2">
                                <input type="text" x-model="spkCode" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-mono text-slate-800 focus:ring-2 focus:ring-indigo-600 focus:outline-none">
                                <button @click="verifyCode()" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white font-bold text-xs hover:bg-indigo-700 transition-colors whitespace-nowrap">
                                    <span x-show="!isVerifying">Verifikasi</span>
                                    <span x-show="isVerifying">Checking...</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Scan Result Card -->
                    <div class="mt-6 p-5 rounded-2xl bg-slate-900 text-white border border-slate-800">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                            <span class="text-xs font-mono text-slate-400">QR-Validator Engine v2.0</span>
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">AES-256 Verified</span>
                        </div>

                        <div class="mt-4 flex items-start gap-4">
                            <!-- Dummy QR Code Graphic -->
                            <div class="w-20 h-20 bg-white p-2 rounded-xl shrink-0 flex items-center justify-center shadow-inner">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=https://prisma.harkatnegeri.ac.id/verify/SPK-UHN-2026" alt="QR Code SPK" class="w-full h-full object-contain">
                            </div>

                            <div class="text-xs space-y-1 font-mono">
                                <div class="text-slate-400">No. Kontrak: <span class="text-white font-bold" x-text="spkCode"></span></div>
                                <div class="text-slate-400">Pengusul: <span class="text-emerald-400">Dr. Ir. Hendra P., M.T.</span></div>
                                <div class="text-slate-400">Status Pencairan: <span class="text-amber-400">Termin I (70%) Dicairkan</span></div>
                                <div class="text-slate-400">Digital Sign: <span class="text-blue-400">Tanda Tangan QR Sah LPPM</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

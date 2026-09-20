<header x-data="{ 
    mobileMenuOpen: false, 
    scrolled: false,
    activeDropdown: null,
    toggle(name) {
        if (this.activeDropdown === name) {
            this.activeDropdown = null;
        } else {
            this.activeDropdown = name;
        }
    },
    close() {
        this.activeDropdown = null;
    }
}" 
        @scroll.window="scrolled = (window.pageYOffset > 10)"
        @click.outside="close()"
        @keydown.escape.window="close()"
        :class="scrolled ? 'bg-white/95 backdrop-blur-md shadow-md border-b border-slate-200/80 py-3' : 'bg-white/90 backdrop-blur-sm border-b border-slate-200/60 py-4'"
        class="fixed top-0 left-0 right-0 z-50 transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="{{ route('landing') }}" class="flex items-center gap-3 group shrink-0">
                <div class="w-10 h-10 max-w-[40px] max-h-[40px] overflow-hidden rounded-xl bg-gradient-to-tr from-[#681727] via-[#8c1d34] to-[#c99738] flex items-center justify-center text-white shadow-md shadow-[#681727]/30 group-hover:scale-105 transition-transform shrink-0">
                    <svg class="w-6 h-6 shrink-0" width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-extrabold text-xl tracking-tight text-slate-900 group-hover:text-blue-700 transition-colors">PRISMA</span>
                        <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 border border-blue-200">UHN</span>
                    </div>
                    <p class="text-[11px] font-medium text-slate-500 leading-none">Universitas Harkat Negeri</p>
                </div>
            </a>

            <!-- Desktop Nav Links with 3 Interactive Dropdowns -->
            <nav class="hidden md:flex items-center gap-2">
                <!-- Dropdown 1: Skema & Usulan -->
                <div class="relative">
                    <button type="button"
                            @click.stop="toggle('skema')" 
                            :class="activeDropdown === 'skema' ? 'bg-blue-50 text-blue-700' : 'text-slate-700 hover:text-blue-700 hover:bg-slate-100/80'"
                            class="px-4 py-2.5 text-sm font-bold rounded-xl flex items-center gap-1.5 transition-all">
                        <span>Skema & Usulan</span>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="activeDropdown === 'skema' ? 'rotate-180 text-blue-700' : 'text-slate-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <!-- Dropdown Popover Card 1 -->
                    <div x-show="activeDropdown === 'skema'"
                         x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                         class="absolute left-1/2 -translate-x-1/2 mt-3 w-80 bg-white rounded-2xl p-3 shadow-2xl shadow-blue-950/15 border border-slate-200/90 z-50">
                        <div class="space-y-1">
                            <a href="#pilar" @click="close()" class="flex items-start gap-3 p-3 rounded-xl hover:bg-blue-50/80 transition-colors group">
                                <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                                </div>
                                <div>
                                    <h5 class="text-xs font-extrabold text-slate-900 group-hover:text-blue-700">4 Pilar Program</h5>
                                    <p class="text-[11px] text-slate-500 leading-tight">Riset dasar, terapan, abmas & HKI</p>
                                </div>
                            </a>
                            <a href="{{ route('admin.skema-bima.index') }}" @click="close()" class="flex items-start gap-3 p-3 rounded-xl hover:bg-blue-50/80 transition-colors group">
                                <div class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center shrink-0 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <div>
                                    <h5 class="text-xs font-extrabold text-slate-900 group-hover:text-indigo-700">Master Skema BIMA</h5>
                                    <p class="text-[11px] text-slate-500 leading-tight">Plafon & Rubrik Reviewer 1-7 (US-04.1)</p>
                                </div>
                            </a>
                            <a href="{{ route('admin.periode-hibah.index') }}" @click="close()" class="flex items-start gap-3 p-3 rounded-xl hover:bg-blue-50/80 transition-colors group">
                                <div class="w-9 h-9 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center shrink-0 group-hover:bg-purple-600 group-hover:text-white transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
                                    <h5 class="text-xs font-extrabold text-slate-900 group-hover:text-purple-700">Periode Call for Proposals</h5>
                                    <p class="text-[11px] text-slate-500 leading-tight">Server-time scheduler & countdown (US-04.2)</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Dropdown 2: Tools & Integrasi -->
                <div class="relative">
                    <button type="button"
                            @click.stop="toggle('tools')" 
                            :class="activeDropdown === 'tools' ? 'bg-blue-50 text-blue-700' : 'text-slate-700 hover:text-blue-700 hover:bg-slate-100/80'"
                            class="px-4 py-2.5 text-sm font-bold rounded-xl flex items-center gap-1.5 transition-all">
                        <span>Tools & Integrasi</span>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="activeDropdown === 'tools' ? 'rotate-180 text-blue-700' : 'text-slate-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <!-- Dropdown Popover Card 2 -->
                    <div x-show="activeDropdown === 'tools'"
                         x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                         class="absolute left-1/2 -translate-x-1/2 mt-3 w-80 bg-white rounded-2xl p-3 shadow-2xl shadow-blue-950/15 border border-slate-200/90 z-50">
                        <div class="space-y-1">
                            <a href="#tools" @click="close()" class="flex items-start gap-3 p-3 rounded-xl hover:bg-blue-50/80 transition-colors group">
                                <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                </div>
                                <div>
                                    <h5 class="text-xs font-extrabold text-slate-900 group-hover:text-blue-700">Simulator SINTA & TKT</h5>
                                    <p class="text-[11px] text-slate-500 leading-tight">Uji kelayakan instrumen usulan</p>
                                </div>
                            </a>
                            <a href="#tools" @click="close()" class="flex items-start gap-3 p-3 rounded-xl hover:bg-blue-50/80 transition-colors group">
                                <div class="w-9 h-9 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center shrink-0 group-hover:bg-purple-600 group-hover:text-white transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                </div>
                                <div>
                                    <h5 class="text-xs font-extrabold text-slate-900 group-hover:text-purple-700">Validasi QR SPK Digital</h5>
                                    <p class="text-[11px] text-slate-500 leading-tight">Cek keaslian tanda tangan digital</p>
                                </div>
                            </a>
                            <a href="#integrasi" @click="close()" class="flex items-start gap-3 p-3 rounded-xl hover:bg-blue-50/80 transition-colors group">
                                <div class="w-9 h-9 rounded-xl bg-cyan-100 text-cyan-700 flex items-center justify-center shrink-0 group-hover:bg-cyan-600 group-hover:text-white transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                </div>
                                <div>
                                    <h5 class="text-xs font-extrabold text-slate-900 group-hover:text-cyan-700">Integrasi API Nasional</h5>
                                    <p class="text-[11px] text-slate-500 leading-tight">SIAKAD, SINTA, DJKI & MinIO</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Dropdown 3: Akademik & Jadwal -->
                <div class="relative">
                    <button type="button"
                            @click.stop="toggle('akademik')" 
                            :class="activeDropdown === 'akademik' ? 'bg-blue-50 text-blue-700' : 'text-slate-700 hover:text-blue-700 hover:bg-slate-100/80'"
                            class="px-4 py-2.5 text-sm font-bold rounded-xl flex items-center gap-1.5 transition-all">
                        <span>Akademik & Jadwal</span>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="activeDropdown === 'akademik' ? 'rotate-180 text-blue-700' : 'text-slate-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <!-- Dropdown Popover Card 3 -->
                    <div x-show="activeDropdown === 'akademik'"
                         x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                         class="absolute left-1/2 -translate-x-1/2 mt-3 w-80 bg-white rounded-2xl p-3 shadow-2xl shadow-blue-950/15 border border-slate-200/90 z-50">
                        <div class="space-y-1">
                            <a href="#fakultas" @click="close()" class="flex items-start gap-3 p-3 rounded-xl hover:bg-blue-50/80 transition-colors group">
                                <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 group-hover:bg-amber-600 group-hover:text-white transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                </div>
                                <div>
                                    <h5 class="text-xs font-extrabold text-slate-900 group-hover:text-amber-700">4 Fakultas & 22 Prodi</h5>
                                    <p class="text-[11px] text-slate-500 leading-tight">Saintek, Soshum, Psikopen, Vokasi</p>
                                </div>
                            </a>
                            <a href="#timeline" @click="close()" class="flex items-start gap-3 p-3 rounded-xl hover:bg-blue-50/80 transition-colors group">
                                <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <div>
                                    <h5 class="text-xs font-extrabold text-slate-900 group-hover:text-emerald-700">Timeline & Deadline</h5>
                                    <p class="text-[11px] text-slate-500 leading-tight">Jadwal Call for Proposals 26/27</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Right Actions & CTA Button -->
            <div class="flex items-center gap-3">
                <div class="hidden xl:flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 border border-emerald-200 text-xs font-bold text-emerald-700">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Call for Proposals Aktif</span>
                </div>

                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs lg:text-sm font-extrabold text-white bg-gradient-to-r from-blue-700 via-indigo-700 to-blue-800 hover:from-blue-800 hover:to-indigo-900 shadow-md shadow-blue-600/25 hover:shadow-lg transition-all transform hover:-translate-y-0.5 active:translate-y-0 whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    <span>Masuk SSO SIAKAD</span>
                </a>

                <!-- Mobile Menu Button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="md:hidden p-2 rounded-xl text-slate-700 hover:text-blue-700 hover:bg-slate-100 focus:outline-none">
                    <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Drawer -->
    <div x-show="mobileMenuOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-4"
         class="md:hidden bg-white border-b border-slate-200 mt-3 px-4 pt-3 pb-6 shadow-xl">
        <div class="flex flex-col gap-2">
            <div class="px-4 pt-2 pb-1 text-xs font-bold text-slate-400 uppercase tracking-wider">Skema & Usulan</div>
            <a href="#pilar" @click="mobileMenuOpen = false" class="px-4 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-blue-50 hover:text-blue-700 flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                <span>4 Pilar Program</span>
            </a>
            <a href="#skema" @click="mobileMenuOpen = false" class="px-4 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-blue-50 hover:text-blue-700 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Katalog Skema BIMA</span>
            </a>
            <a href="#wizard" @click="mobileMenuOpen = false" class="px-4 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-blue-50 hover:text-blue-700 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span>Form BIMA 6-Langkah</span>
            </a>

            <div class="px-4 pt-3 pb-1 text-xs font-bold text-slate-400 uppercase tracking-wider border-t border-slate-100 mt-1">Tools & Integrasi</div>
            <a href="#tools" @click="mobileMenuOpen = false" class="px-4 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-blue-50 hover:text-blue-700 flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <span>Simulator SINTA & TKT</span>
            </a>
            <a href="#tools" @click="mobileMenuOpen = false" class="px-4 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-blue-50 hover:text-blue-700 flex items-center gap-2">
                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                <span>Validasi QR SPK Digital</span>
            </a>
            <a href="#integrasi" @click="mobileMenuOpen = false" class="px-4 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-blue-50 hover:text-blue-700 flex items-center gap-2">
                <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <span>Integrasi API Nasional</span>
            </a>

            <div class="px-4 pt-3 pb-1 text-xs font-bold text-slate-400 uppercase tracking-wider border-t border-slate-100 mt-1">Akademik & Timeline</div>
            <a href="#fakultas" @click="mobileMenuOpen = false" class="px-4 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-blue-50 hover:text-blue-700 flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span>4 Fakultas & 22 Prodi</span>
            </a>
            <a href="#timeline" @click="mobileMenuOpen = false" class="px-4 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-blue-50 hover:text-blue-700 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>Timeline & Deadline</span>
            </a>
            
            <div class="pt-3 mt-2 border-t border-slate-100 flex flex-col gap-3">
                <div class="flex items-center gap-2 px-3 py-2 rounded-lg bg-emerald-50 text-xs font-semibold text-emerald-700">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Call for Proposals Gelombang 1 Aktif
                </div>
                <a href="{{ route('login') }}" class="w-full text-center py-3 rounded-xl text-sm font-bold text-white bg-blue-700 hover:bg-blue-800 shadow-md block">
                    Masuk SSO SIAKAD Cloud
                </a>
            </div>
        </div>
    </div>
</header>

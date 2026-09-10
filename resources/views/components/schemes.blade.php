<section id="skema" x-data="{ 
    activeTab: 'Semua', 
    searchQuery: '',
    schemes: {{ json_encode($schemes) }},
    get filteredSchemes() {
        return this.schemes.filter(s => {
            const matchesTab = (this.activeTab === 'Semua') || (s.category === this.activeTab);
            const matchesSearch = s.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                                  s.code.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                                  s.description.toLowerCase().includes(this.searchQuery.toLowerCase());
            return matchesTab && matchesSearch;
        });
    }
}" class="py-20 bg-white relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
            <div>
                <span class="text-xs font-bold text-blue-700 bg-blue-50 border border-blue-200 px-3.5 py-1.5 rounded-full uppercase tracking-wider">
                    Katalog Program Hibah Internal
                </span>
                <h2 class="mt-4 text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Jelajahi Skema Pendanaan Riset & Abmas
                </h2>
                <p class="mt-2 text-base text-slate-600 max-w-2xl">
                    Pilih skema pendanaan yang sesuai dengan profil SINTA, jabatan fungsional, dan target TKT luaran riset Anda.
                </p>
            </div>

            <!-- Search Bar -->
            <div class="relative w-full md:w-80">
                <input type="text" x-model="searchQuery" placeholder="Cari skema, kode, atau keyword..." 
                       class="w-full pl-10 pr-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white transition-all shadow-sm">
                <svg class="w-5 h-5 text-slate-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>

        <!-- Filter Category Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto pb-4 mb-8 no-scrollbar">
            <template x-for="tab in ['Semua', 'Riset Dasar', 'Riset Terapan', 'Pengabdian', 'HKI & Insentif']" :key="tab">
                <button @click="activeTab = tab" 
                        :class="activeTab === tab ? 'bg-blue-700 text-white shadow-md shadow-blue-600/20' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                        class="px-5 py-2.5 rounded-xl text-sm font-bold whitespace-nowrap transition-all">
                    <span x-text="tab"></span>
                </button>
            </template>
        </div>

        <!-- Schemes Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <template x-for="scheme in filteredSchemes" :key="scheme.id">
                <div class="bg-slate-50/80 rounded-3xl p-7 border border-slate-200/90 shadow-sm hover:shadow-xl hover:border-blue-300 hover:bg-white transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <!-- Header Badges -->
                        <div class="flex items-center justify-between gap-2 mb-4">
                            <span class="px-3 py-1 rounded-xl text-xs font-black bg-blue-600 text-white uppercase tracking-wider shadow-sm" x-text="scheme.code"></span>
                            <span class="px-2.5 py-0.5 rounded-md text-xs font-semibold text-slate-600 bg-white border border-slate-200" x-text="scheme.category"></span>
                        </div>

                        <!-- Name & Description -->
                        <h3 class="text-xl font-bold text-slate-900 group-hover:text-blue-700 transition-colors" x-text="scheme.name"></h3>
                        <p class="mt-2.5 text-sm text-slate-600 leading-relaxed" x-text="scheme.description"></p>

                        <!-- Key Criteria Specs List -->
                        <div class="mt-6 space-y-2.5 text-xs">
                            <div class="flex items-center justify-between p-2.5 rounded-xl bg-white border border-slate-100">
                                <span class="text-slate-500 font-medium">Pagu Anggaran Maksimal:</span>
                                <span class="font-extrabold text-blue-700 text-sm" x-text="scheme.pagu"></span>
                            </div>
                            <div class="flex items-center justify-between p-2 rounded-lg">
                                <span class="text-slate-500">Syarat Skor SINTA:</span>
                                <span class="font-bold text-slate-800" x-text="scheme.min_sinta"></span>
                            </div>
                            <div class="flex items-center justify-between p-2 rounded-lg">
                                <span class="text-slate-500">Jabatan Fungsional:</span>
                                <span class="font-bold text-slate-800 text-right" x-text="scheme.min_jafung"></span>
                            </div>
                            <div class="flex items-center justify-between p-2 rounded-lg">
                                <span class="text-slate-500">Target TKT / Capaian:</span>
                                <span class="font-bold text-emerald-700" x-text="scheme.target_tkt"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Card Footer Outcome & Action -->
                    <div class="mt-6 pt-4 border-t border-slate-200/80 flex items-center justify-between">
                        <div class="text-[11px] font-semibold text-slate-500">
                            Luaran: <span class="text-slate-800" x-text="scheme.luaran"></span>
                        </div>
                        <a href="#login" class="px-3.5 py-2 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white text-xs font-bold transition-all shadow-sm whitespace-nowrap">
                            Pilih Skema &rarr;
                        </a>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty Search State -->
        <div x-show="filteredSchemes.length === 0" class="text-center py-16 bg-slate-50 rounded-3xl border border-dashed border-slate-300">
            <svg class="w-12 h-12 text-slate-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h4 class="mt-3 text-base font-bold text-slate-800">Skema Pendanaan Tidak Ditemukan</h4>
            <p class="text-sm text-slate-500 mt-1">Coba sesuaikan kata kunci pencarian atau ganti kategori tab di atas.</p>
        </div>
    </div>
</section>


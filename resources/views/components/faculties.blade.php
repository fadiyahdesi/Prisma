<section id="fakultas" class="py-20 bg-slate-50 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="text-xs font-bold text-blue-700 bg-blue-50 border border-blue-200 px-3.5 py-1.5 rounded-full uppercase tracking-wider">
                Cakupan Multi-Tenancy Akademik
            </span>
            <h2 class="mt-4 text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                4 Fakultas & 22 Program Studi UHN
            </h2>
            <p class="mt-4 text-base text-slate-600">
                PRISMA mengisolasi data usulan, pagu, dan laporan secara otomatis berjenjang di bawah Dekanat Fakultas & Kaprodi.
            </p>
        </div>

        <!-- Faculty Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($faculties as $fac)
                <div class="bg-white rounded-3xl p-7 border border-slate-200 shadow-md shadow-slate-100 flex flex-col justify-between hover:shadow-xl hover:border-blue-300 transition-all">
                    <div>
                        <!-- Code Badge & Name -->
                        <div class="flex items-center justify-between mb-4">
                            <span class="px-3 py-1 rounded-xl text-xs font-black bg-slate-900 text-white uppercase tracking-wider">
                                {{ $fac['code'] }}
                            </span>
                            <span class="text-[11px] font-semibold text-slate-500">
                                {{ count($fac['programs']) }} Program Studi
                            </span>
                        </div>

                        <h3 class="text-xl font-bold text-slate-900">
                            {{ $fac['name'] }}
                        </h3>
                        <p class="mt-1 text-xs text-slate-500 font-medium">
                            Dekan: {{ $fac['dean'] }}
                        </p>

                        <!-- Programs List -->
                        <div class="mt-6 space-y-2 border-t border-slate-100 pt-4">
                            @foreach($fac['programs'] as $prog)
                                <div class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600 shrink-0"></span>
                                    <span>{{ $prog }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span>Akses Dasbor Dekanat</span>
                        <span class="font-bold text-blue-700">Aktif &rarr;</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>


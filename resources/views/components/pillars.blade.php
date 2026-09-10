<section id="pilar" class="py-20 bg-slate-50 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="text-xs font-bold text-blue-700 bg-blue-50 border border-blue-200 px-3.5 py-1.5 rounded-full uppercase tracking-wider">
                Taksonomi Hibah BIMA
            </span>
            <h2 class="mt-4 text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                4 Pilar Utama Ekosistem Riset UHN
            </h2>
            <p class="mt-4 text-base text-slate-600">
                PRISMA mengadopsi struktur taksonomi hibah nasional 4 pilar Kemdiktisaintek untuk mempermudah adaptasi dosen dan meningkatkan kualitas capaian IKU-5.
            </p>
        </div>

        <!-- Pillars Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($pillars as $pillar)
                <div class="bg-white rounded-3xl p-7 border border-slate-200/80 shadow-md shadow-slate-100 hover:shadow-xl hover:border-blue-300 transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <!-- Icon & Badge Header -->
                        <div class="flex items-center justify-between mb-6">
                            <div class="w-14 h-14 rounded-2xl bg-blue-50 group-hover:bg-blue-600 text-blue-700 group-hover:text-white flex items-center justify-center transition-colors shadow-sm">
                                @if($pillar['icon'] === 'microscope')
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                    </svg>
                                @elseif($pillar['icon'] === 'cpu')
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M3 9h2m-2 6h2m14-6h2m-2 6h2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                                    </svg>
                                @elseif($pillar['icon'] === 'users')
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                @else
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @endif
                            </div>
                            <span class="text-xs font-bold px-3 py-1 rounded-full bg-slate-100 text-slate-700">
                                {{ $pillar['badge'] }}
                            </span>
                        </div>

                        <!-- Pillar Details -->
                        <h3 class="text-xl font-bold text-slate-900 group-hover:text-blue-700 transition-colors">
                            {{ $pillar['title'] }}
                        </h3>
                        <p class="mt-3 text-sm text-slate-600 leading-relaxed">
                            {{ $pillar['description'] }}
                        </p>
                    </div>

                    <div class="mt-6 pt-5 border-t border-slate-100 flex items-center justify-between text-xs font-semibold">
                        <span class="text-blue-700 bg-blue-50 px-2.5 py-1 rounded-lg">
                            {{ $pillar['focus'] }}
                        </span>
                        <span class="text-slate-500 font-medium">
                            {{ $pillar['schemes_count'] }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>


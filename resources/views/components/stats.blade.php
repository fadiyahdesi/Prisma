<section class="py-12 bg-white border-y border-slate-200/70 relative z-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6 lg:gap-4 divide-y md:divide-y-0 md:divide-x divide-slate-100">
            @foreach($stats as $stat)
                <div class="flex flex-col items-center text-center p-3 first:pt-0 md:first:pt-3">
                    <span class="text-3xl lg:text-4xl font-extrabold text-blue-700 tracking-tight">
                        {{ $stat['number'] }}
                    </span>
                    <span class="mt-1 text-sm font-bold text-slate-900">
                        {{ $stat['label'] }}
                    </span>
                    <span class="mt-0.5 text-[11px] text-slate-500 font-medium">
                        {{ $stat['sub'] }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>
</section>


@extends('layouts.app')

@section('title', 'Manajemen Master Skema BIMA - PRISMA UHN')

@section('content')
<div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <x-sidebar />

    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        <!-- Header -->
        <header class="bg-white border-b border-slate-200 py-4 sticky top-0 z-30 shadow-sm">
            <div class="w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <h1 class="font-extrabold text-xl text-slate-900 leading-tight">Master Skema Hibah BIMA</h1>
                        <p class="text-xs font-semibold text-slate-500">Konfigurasi Skema Riset, Plafon Dana, Eligibilitas & Rubrik Reviewer (US-04.1)</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button onclick="document.getElementById('modalTambahSkema').classList.remove('hidden')" class="px-5 py-2.5 rounded-xl bg-blue-700 hover:bg-blue-800 text-white text-xs font-extrabold shadow-md transition-all flex items-center gap-2">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        <span class="hidden sm:inline">+ Tambah Skema BIMA</span>
                    </button>
                    <a href="{{ route('dashboard') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-extrabold border border-slate-300">
                        Dashboard
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Container -->
    <main class="flex-grow w-full px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="p-4.5 rounded-2xl bg-emerald-50 border-2 border-emerald-300 text-emerald-950 text-sm font-extrabold flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4.5 rounded-2xl bg-rose-50 border-2 border-rose-300 text-rose-950 text-sm font-extrabold flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5 text-rose-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- List Card -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xl space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
                <div>
                    <span class="text-xs font-black text-blue-900 bg-blue-100 px-3 py-1 rounded-md border border-blue-300 uppercase tracking-wider">
                        Master Data Skema Standar BIMA
                    </span>
                    <h3 class="text-xl font-extrabold text-slate-900 mt-1">Daftar Skema Hibah Penelitian & Abmas</h3>
                </div>
                <span class="px-3.5 py-1.5 rounded-full text-xs font-black bg-blue-50 text-blue-900 border border-blue-200">
                    Total: {{ $schemes->count() }} Skema Dikonfigurasi
                </span>
            </div>

            <!-- Table Container with Optimized Layout & No Awkward Wraps -->
            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-left border-collapse" style="table-layout: fixed; min-width: 1460px; width: 1460px;">
                    <colgroup>
                        <col style="width: 360px;">
                        <col style="width: 150px;">
                        <col style="width: 270px;">
                        <col style="width: 170px;">
                        <col style="width: 175px;">
                        <col style="width: 155px;">
                        <col style="width: 210px;">
                    </colgroup>
                    <thead>
                        <tr class="bg-slate-100 text-slate-800 text-xs font-black uppercase tracking-wider border-b-2 border-slate-300">
                            <th class="py-4 px-5 whitespace-nowrap">Kode & Nama Skema</th>
                            <th class="py-4 px-4 whitespace-nowrap">Kategori</th>
                            <th class="py-4 px-4 whitespace-nowrap">Syarat Jafung & SINTA</th>
                            <th class="py-4 px-4 text-center whitespace-nowrap">Rentang TKT</th>
                            <th class="py-4 px-5 text-right whitespace-nowrap">Plafon Dana (SBM)</th>
                            <th class="py-4 px-4 text-center whitespace-nowrap">Status Skema</th>
                            <th class="py-4 px-5 text-center whitespace-nowrap min-w-[180px]">Aksi / Rubrik</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-sm font-semibold">
                        @foreach($schemes as $scheme)
                            <tr class="hover:bg-slate-50/90 transition-all">
                                <!-- Kode & Nama -->
                                <td class="py-4 px-5 align-top">
                                    <div class="min-w-0">
                                        <span class="inline-flex max-w-full px-2.5 py-1 rounded-lg bg-blue-900 text-white font-mono font-black text-xs shadow-sm">
                                            {{ $scheme->kode_skema }}
                                        </span>
                                        <span class="font-extrabold text-slate-900 block leading-snug mt-2 break-words">{{ $scheme->nama_skema }}</span>
                                    </div>
                                </td>

                                <!-- Kategori -->
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <span class="px-3 py-1 rounded-lg text-xs font-black {{ $scheme->kategori === 'penelitian' ? 'bg-indigo-100 text-indigo-900 border border-indigo-300' : 'bg-amber-100 text-amber-950 border border-amber-300' }} uppercase">
                                        {{ $scheme->kategori }}
                                    </span>
                                </td>

                                <!-- Syarat Jafung & SINTA -->
                                <td class="py-4 px-4 text-xs space-y-1">
                                    <div class="flex items-center gap-1.5 whitespace-nowrap">
                                        <span class="text-slate-500 font-bold">Min SINTA {{ $scheme->kategori === 'pengabdian' ? 'Overall' : '3Yr' }}:</span>
                                        <strong class="text-blue-950 font-black bg-blue-50 px-2.5 py-0.5 rounded-md border border-blue-300 font-mono text-xs">
                                            &ge; {{ number_format($scheme->kategori === 'pengabdian' ? $scheme->min_sinta_overall : $scheme->min_sinta_3yr, 1) }}
                                        </strong>
                                    </div>
                                    @if($scheme->kategori === 'pengabdian')
                                    <div class="text-[11px] text-amber-700 font-bold">Min SDG: {{ $scheme->min_sdgs ?? 2 }} indikator</div>
                                    @endif
                                    <div class="text-[11px] text-slate-600 font-medium max-w-[240px] truncate" title="{{ is_array($scheme->min_jafung) ? implode(', ', $scheme->min_jafung) : '-' }}">
                                        Min Jafung: <strong class="text-slate-900">{{ is_array($scheme->min_jafung) ? implode(', ', $scheme->min_jafung) : '-' }}</strong>
                                    </div>
                                </td>

                                <!-- Rentang TKT (Fixed Inline Badge) -->
                                <td class="py-4 px-4 text-center whitespace-nowrap">
                                    <span class="inline-block px-3 py-1 rounded-full {{ $scheme->kategori === 'pengabdian' ? 'bg-slate-100 text-slate-700 border-slate-300' : 'bg-emerald-100 text-emerald-950 border-emerald-400' }} font-black text-xs border shadow-sm whitespace-nowrap">
                                        {{ $scheme->kategori === 'pengabdian' ? 'TKT Tidak Berlaku' : 'TKT ' . $scheme->min_tkt . ' - ' . $scheme->max_tkt }}
                                    </span>
                                </td>

                                <!-- Plafon Dana -->
                                <td class="py-4 px-5 text-right font-mono font-black text-slate-900 whitespace-nowrap text-base">
                                    <span class="text-xs font-bold text-slate-500 mr-1">Rp</span>{{ number_format($scheme->plafon_dana, 0, ',', '.') }}
                                </td>

                                <!-- Status Skema Toggle -->
                                <td class="py-4 px-4 text-center whitespace-nowrap">
                                    <form action="{{ route('admin.skema-bima.toggle', $scheme) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-3.5 py-1.5 rounded-full text-xs font-black transition-all border shadow-sm inline-flex items-center gap-1.5 {{ $scheme->is_active ? 'bg-emerald-600 hover:bg-emerald-700 text-white border-emerald-700' : 'bg-slate-200 hover:bg-slate-300 text-slate-700 border-slate-400' }}">
                                            <span>{{ $scheme->is_active ? '✓ AKTIF' : '✕ NON-AKTIF' }}</span>
                                        </button>
                                    </form>
                                </td>

                                <!-- Action Buttons -->
                                <td class="py-4 px-5 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.skema-bima.rubrik', $scheme) }}" class="px-3 py-1.5 rounded-xl bg-purple-100 hover:bg-purple-200 text-purple-950 text-xs font-black border border-purple-300 shadow-sm inline-flex items-center gap-1.5 transition-all">
                                            <svg class="w-3.5 h-3.5 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                            <span>Rubrik 1-7</span>
                                        </a>

                                        <form action="{{ route('admin.skema-bima.destroy', $scheme) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus skema ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-100 hover:bg-rose-200 text-rose-950 text-xs font-extrabold border border-rose-300 transition-all">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<!-- Modal Tambah Skema -->
<div id="modalTambahSkema" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-xl w-full border-2 border-blue-300 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200">
            <h3 class="text-lg font-extrabold text-slate-900">Tambah Skema BIMA Baru</h3>
            <button onclick="document.getElementById('modalTambahSkema').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 font-bold text-xl">&times;</button>
        </div>

        <form action="{{ route('admin.skema-bima.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-extrabold text-slate-800 uppercase block mb-1">Kode Skema:</label>
                    <input type="text" name="kode_skema" required placeholder="Contoh: PDP" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 font-mono font-bold text-sm text-slate-900 uppercase">
                </div>
                <div>
                    <label class="text-xs font-extrabold text-slate-800 uppercase block mb-1">Kategori:</label>
                    <select id="kategoriTambah" name="kategori" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 font-bold text-sm text-slate-900" onchange="togglePengabdianFields()">
                        <option value="penelitian">Penelitian</option>
                        <option value="pengabdian">Pengabdian Kepada Masyarakat</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="text-xs font-extrabold text-slate-800 uppercase block mb-1">Nama Lengkap Skema:</label>
                <input type="text" name="nama_skema" required placeholder="Contoh: Penelitian Dosen Pemula (PDP)" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 font-bold text-sm text-slate-900">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-extrabold text-slate-800 uppercase block mb-1">Min SINTA 3Yr:</label>
                    <input type="number" step="0.1" name="min_sinta_3yr" value="50" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 font-bold text-sm text-slate-900">
                </div>
                <div>
                    <label class="text-xs font-extrabold text-slate-800 uppercase block mb-1">Min SINTA Overall:</label>
                    <input type="number" step="0.1" name="min_sinta_overall" value="0" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 font-bold text-sm text-slate-900">
                </div>
            </div>

            <div id="tktFields" class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-extrabold text-slate-800 uppercase block mb-1">Min TKT:</label>
                    <input id="minTktTambah" type="number" min="1" max="9" name="min_tkt" value="1" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 font-bold text-sm text-slate-900">
                </div>
                <div>
                    <label class="text-xs font-extrabold text-slate-800 uppercase block mb-1">Max TKT:</label>
                    <input id="maxTktTambah" type="number" min="1" max="9" name="max_tkt" value="3" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 font-bold text-sm text-slate-900">
                </div>
            </div>

            <div id="sdgFields" class="hidden">
                <label class="text-xs font-extrabold text-slate-800 uppercase block mb-1">Minimal SDG yang Disasar:</label>
                <input type="number" min="1" max="17" name="min_sdgs" value="2" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 font-bold text-sm text-slate-900">
            </div>

            <div>
                <label class="text-xs font-extrabold text-slate-800 uppercase block mb-1">Plafon Dana Maksimal (Rp):</label>
                <input type="number" name="plafon_dana" required value="25000000" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 font-mono font-bold text-sm text-slate-900">
            </div>

            <div>
                <label class="text-xs font-extrabold text-slate-800 uppercase block mb-1">Minimal Jabatan Fungsional:</label>
                <div class="grid grid-cols-2 gap-2 text-xs font-bold text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-200">
                    <label class="flex items-center gap-2"><input type="checkbox" name="min_jafung[]" value="Asisten Ahli" checked> Asisten Ahli</label>
                    <label class="flex items-center gap-2"><input type="checkbox" name="min_jafung[]" value="Lektor" checked> Lektor</label>
                    <label class="flex items-center gap-2"><input type="checkbox" name="min_jafung[]" value="Lektor Kepala"> Lektor Kepala</label>
                    <label class="flex items-center gap-2"><input type="checkbox" name="min_jafung[]" value="Guru Besar / Profesor"> Guru Besar</label>
                </div>
            </div>

            <div class="pt-2 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modalTambahSkema').classList.add('hidden')" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-700 hover:bg-blue-800 text-white text-xs font-black shadow-md">Simpan Skema</button>
            </div>
        </form>
    </div>
</div>
    </div>
</div>
<script>
    function togglePengabdianFields() {
        const isPengabdian = document.getElementById('kategoriTambah').value === 'pengabdian';
        document.getElementById('tktFields').classList.toggle('hidden', isPengabdian);
        document.getElementById('sdgFields').classList.toggle('hidden', !isPengabdian);
        document.getElementById('minTktTambah').required = !isPengabdian;
        document.getElementById('maxTktTambah').required = !isPengabdian;
    }

    togglePengabdianFields();
</script>
@endsection

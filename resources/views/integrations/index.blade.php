@extends('layouts.app')

@section('title', 'Integrasi API & Interoperabilitas - PRISMA UHN')

@section('content')
<div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-100 text-slate-800 flex">
    <!-- Role-Based Interactive Sidebar -->
    <x-sidebar />

    <!-- Main Content Right Wrapper -->
    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        <!-- Top Dashboard Header -->
        <header class="bg-white border-b border-slate-200 py-4 sticky top-0 z-30 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <h1 class="font-extrabold text-xl text-slate-900 leading-tight">Integrasi API & Interoperabilitas</h1>
                        <p class="text-xs font-semibold text-slate-500">Spesifikasi 4 Modul Integrasi Eksternal (SIAKAD, SINTA, DJKI, MinIO)</p>
                    </div>
                </div>
            </div>
        </header>

        <main class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full space-y-6">

            @if (session('success'))
                <div class="p-4 mb-4 text-sm text-emerald-800 rounded-xl bg-emerald-50 border border-emerald-200" role="alert">
                    <span class="font-bold">Berhasil!</span> {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 mb-4 text-sm text-rose-800 rounded-xl bg-rose-50 border border-rose-200" role="alert">
                    <span class="font-bold">Perhatian!</span> {{ session('error') }}
                </div>
            @endif

            <!-- 4 Integration Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- 1. SIAKAD Cloud UHN -->
                <div class="bg-white p-6 rounded-2xl shadow-xs border border-slate-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-bold text-slate-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            SIAKAD Cloud UHN
                        </h3>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                            {{ $integrationsStatus['siakad']['status'] }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-600 mb-2"><strong>Protokol & Data:</strong> {{ $integrationsStatus['siakad']['protocol'] }}</p>
                    <p class="text-xs text-slate-600 mb-2"><strong>Terakhir Sinkron:</strong> {{ $integrationsStatus['siakad']['last_sync'] }}</p>
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs text-slate-700 mt-3">
                        <strong>Strategi Resilience:</strong> {{ $integrationsStatus['siakad']['resilience'] }}
                    </div>
                </div>

                <!-- 2. SINTA Kemdiktisaintek -->
                <div class="bg-white p-6 rounded-2xl shadow-xs border border-slate-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-bold text-slate-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            SINTA Kemdiktisaintek
                        </h3>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                            Redis Cache TTL 7 Hari
                        </span>
                    </div>
                    <p class="text-xs text-slate-600 mb-1"><strong>SINTA ID:</strong> {{ $integrationsStatus['sinta']['sinta_id'] }} | <strong>Skor 3Yr:</strong> {{ $integrationsStatus['sinta']['sinta_score_3yr'] }}</p>
                    <p class="text-xs text-slate-600 mb-3"><strong>Terakhir Sinkron:</strong> {{ $integrationsStatus['sinta']['last_sync'] }}</p>
                    
                    <form action="{{ route('sinta.sync') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow-xs">
                            ⚡ Sinkronkan SINTA Mandiri
                        </button>
                    </form>
                </div>

                <!-- 3. Pangkalan Data DJKI -->
                <div class="bg-white p-6 rounded-2xl shadow-xs border border-slate-200" x-data="{ query: 'EC00202518274', result: null, loading: false }">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-bold text-slate-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            Pangkalan Data DJKI HKI
                        </h3>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                            Web Parser / Fallback Sentra HKI
                        </span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Uji verifikasi nomor permohonan HKI / Paten secara instan:</p>
                    
                    <div class="flex space-x-2 mb-3">
                        <input type="text" x-model="query" placeholder="Contoh: EC00202518274" class="text-xs border-slate-300 rounded-lg shadow-xs w-full">
                        <button @click="loading = true; fetch('{{ route('integrasi.djki.verify') }}?nomor_permohonan=' + encodeURIComponent(query)).then(r=>r.json()).then(d=>{ result = d; loading = false; })" class="px-3.5 py-1.5 bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold rounded-lg whitespace-nowrap">
                            Cek HKI
                        </button>
                    </div>

                    <div x-show="loading" class="text-xs text-slate-500 italic">Memeriksa ke pangkalan data DJKI...</div>
                    <template x-if="result">
                        <div class="p-3 bg-purple-50 rounded-xl border border-purple-100 text-xs mt-2">
                            <p class="font-bold text-purple-900" x-text="result.status"></p>
                            <p class="text-purple-700 mt-0.5" x-text="result.judul_hki || result.message"></p>
                            <p class="text-slate-500 text-[10px] mt-1" x-text="'Diverifikasi oleh: ' + result.verified_by"></p>
                        </div>
                    </template>
                </div>

                <!-- 4. MinIO Object Storage -->
                <div class="bg-white p-6 rounded-2xl shadow-xs border border-slate-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-bold text-slate-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
                            MinIO Object Storage
                        </h3>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                            S3 AES-256 Protocol
                        </span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Inspeksi Keamanan: Max 5MB (Proposal), Max 15MB (Monev), MIME application/pdf, ClamAV scan.</p>
                    
                    <form action="{{ route('integrasi.storage.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <input type="hidden" name="doc_type" value="proposal">
                        <input type="file" name="file_document" accept="application/pdf" class="block w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 cursor-pointer" required>
                        <button type="submit" class="px-3.5 py-1.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold rounded-lg shadow-xs">
                            📤 Uji Unggah Berkas PDF Safe-Scan
                        </button>
                    </form>
                </div>

            </div>

        </main>
    </div>
</div>
@endsection


@extends('layouts.app')

@section('title', 'Audit Trail Logging (US-02.4) - PRISMA UHN')

@section('content')
<div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-900 text-white flex">
    <x-sidebar />

    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        <!-- Header Navigation -->
        <header class="bg-slate-950 border-b border-slate-800 py-4 sticky top-0 z-30">
            <div class="w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <a href="{{ route('dashboard') }}" class="w-10 h-10 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 flex items-center justify-center shrink-0 transition" title="Kembali ke Dashboard" aria-label="Kembali ke Dashboard">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </a>
                    <div>
                        <h1 class="font-extrabold text-lg text-white leading-tight">Audit Trail Logging (US-02.4)</h1>
                        <p class="text-xs text-purple-400">Perekaman Aktivitas Keamanan System • Restricted Access</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30">
                        Otorisasi: {{ Auth::user()->primaryRoleName() }}
                    </span>
                </div>
            </div>
        </header>

        <!-- Main Content Container -->
        <main class="flex-grow w-full px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <!-- Search & Filter Bar -->
        <div class="bg-slate-800/90 rounded-3xl p-6 border border-slate-700/80 shadow-xl backdrop-blur-md">
            <form action="{{ route('audit-logs') }}" method="GET" class="flex flex-col md:flex-row items-center gap-4">
                <div class="relative flex-grow w-full">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari aksi (LOGIN, OTP, ROLE), IP Address, nama/email user..." 
                           class="w-full pl-10 pr-4 py-3 rounded-2xl bg-slate-900 border border-slate-700 text-xs font-mono text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <div class="w-full md:w-64">
                    <select name="action" class="w-full px-4 py-3 rounded-2xl bg-slate-900 border border-slate-700 text-xs font-mono text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="">-- Semua Jenis Aksi --</option>
                        @foreach($actionTypes as $type)
                            <option value="{{ $type }}" {{ request('action') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="w-full md:w-auto px-6 py-3 rounded-2xl bg-purple-600 hover:bg-purple-500 font-bold text-xs text-white transition-colors shadow-md whitespace-nowrap">
                    Filter Log
                </button>
            </form>
        </div>

        <!-- Audit Trail Logs Table -->
        <div class="bg-slate-800/90 rounded-3xl border border-slate-700/80 shadow-xl overflow-hidden" x-data="{ selectedLog: null }">
            <div class="p-6 border-b border-slate-700/80 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-purple-400 uppercase tracking-wider block">Tabel System Audit (ppm_audit_logs)</span>
                    <h3 class="text-base font-bold text-white">Rekaman Aktivitas Otentikasi & Keamanan</h3>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-900 text-slate-300 font-mono">
                    Total: {{ $logs->total() }} Records
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-950/80 text-slate-400 uppercase font-mono text-[11px] border-b border-slate-800">
                        <tr>
                            <th class="p-4">ID</th>
                            <th class="p-4">Timestamp</th>
                            <th class="p-4">Pengguna</th>
                            <th class="p-4">Aksi System (Action)</th>
                            <th class="p-4">IP Address</th>
                            <th class="p-4">User Agent</th>
                            <th class="p-4 text-center">Payload JSON</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 font-mono">
                        @forelse($logs as $log)
                            <tr class="hover:bg-slate-700/40 transition-colors">
                                <td class="p-4 text-slate-500 font-bold">#{{ $log->id }}</td>
                                <td class="p-4 text-slate-300 whitespace-nowrap">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                <td class="p-4">
                                    @if($log->user)
                                        <div class="font-sans">
                                            <div class="font-bold text-white">{{ $log->user->name }}</div>
                                            <div class="text-[11px] text-purple-300 font-mono">{{ $log->user->email }}</div>
                                        </div>
                                    @else
                                        <span class="text-slate-500">System / Guest</span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider
                                        @if(str_contains($log->action, 'SUCCESS') || str_contains($log->action, 'VERIFIED')) bg-emerald-500/20 text-emerald-300 border border-emerald-500/30
                                        @elseif(str_contains($log->action, 'LOCKED') || str_contains($log->action, 'FAILED')) bg-red-500/20 text-red-300 border border-red-500/30
                                        @else bg-blue-500/20 text-blue-300 border border-blue-500/30 @endif">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="p-4 text-slate-300">{{ $log->ip_address }}</td>
                                <td class="p-4 text-slate-400 max-w-xs truncate" title="{{ $log->user_agent }}">
                                    {{ Str::limit($log->user_agent, 35) }}
                                </td>
                                <td class="p-4 text-center">
                                    <button type="button" 
                                            @click="selectedLog = {{ json_encode($log) }}"
                                            class="px-3 py-1 rounded-lg bg-slate-900 hover:bg-purple-600 text-purple-300 hover:text-white border border-slate-700 transition-colors text-[11px] font-bold">
                                        Inspeksi Diff
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-slate-500 font-sans">
                                    Tidak ada rekaman audit log yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            <div class="p-4 border-t border-slate-800 bg-slate-950/40">
                {{ $logs->links() }}
            </div>

            <!-- JSON Payload Inspection Modal -->
            <div x-show="selectedLog !== null" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
                
                <div class="bg-slate-900 rounded-3xl max-w-2xl w-full border border-slate-700 shadow-2xl p-6 relative space-y-4" @click.outside="selectedLog = null">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                        <h4 class="text-base font-bold text-white">Inspektur Log Audit ID #<span x-text="selectedLog?.id"></span></h4>
                        <button type="button" @click="selectedLog = null" class="text-slate-400 hover:text-white text-lg font-bold">&times;</button>
                    </div>

                    <div class="space-y-3 font-mono text-xs">
                        <div class="p-3 rounded-xl bg-slate-950 border border-slate-800">
                            <span class="text-purple-400 font-bold block mb-1">Aksi System:</span>
                            <span class="text-white font-bold" x-text="selectedLog?.action"></span>
                        </div>

                        <div class="p-3 rounded-xl bg-slate-950 border border-slate-800">
                            <span class="text-amber-400 font-bold block mb-1">Payload Sebelum (Before):</span>
                            <pre class="text-slate-300 text-[11px] overflow-x-auto whitespace-pre-wrap" x-text="JSON.stringify(selectedLog?.payload_sebelum, null, 2) || 'null'"></pre>
                        </div>

                        <div class="p-3 rounded-xl bg-slate-950 border border-slate-800">
                            <span class="text-emerald-400 font-bold block mb-1">Payload Sesudah (After):</span>
                            <pre class="text-slate-300 text-[11px] overflow-x-auto whitespace-pre-wrap" x-text="JSON.stringify(selectedLog?.payload_sesudah, null, 2) || 'null'"></pre>
                        </div>
                    </div>

                    <div class="pt-2 text-right">
                        <button type="button" @click="selectedLog = null" class="px-5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
    </div>
</div>
@endsection


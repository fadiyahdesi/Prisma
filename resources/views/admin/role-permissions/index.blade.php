@extends('layouts.app')

@section('title', 'Manajemen Role & Permission (RBAC) - PRISMA UHN')

@section('content')
<div x-data="{
        sidebarOpen: false,
        activeTab: '{{ $activeTab }}',
        permSearch: '',
        createRoleOpen: false,
        userModalOpen: false,
        editUserId: null,
        editUserName: '',
        editUserEmail: '',
        editUserIsProtected: false,
        editUserRoles: [],
        userActionUrl: '',
        openUserModal(user) {
            this.editUserId = user.id;
            this.editUserName = user.name;
            this.editUserEmail = user.email || '-';
            this.editUserIsProtected = user.isProtected;
            this.editUserRoles = user.roleIds.slice();
            this.userActionUrl = user.actionUrl;
            this.userModalOpen = true;
        },
        toggleAllPermissions(checked) {
            document.querySelectorAll('.rbac-perm-checkbox').forEach((el) => {
                if (!el.disabled) el.checked = checked;
            });
            this.refreshCounters();
        },
        toggleGroupPermissions(groupSlug) {
            const boxes = Array.from(document.querySelectorAll('.rbac-perm-checkbox[data-group=\'' + groupSlug + '\']'));
            const enabled = boxes.filter(b => !b.disabled);
            if (!enabled.length) return;
            const allChecked = enabled.every(b => b.checked);
            enabled.forEach(b => b.checked = !allChecked);
            this.refreshCounters();
        },
        refreshCounters() {
            const checkedCount = document.querySelectorAll('.rbac-perm-checkbox:checked').length;
            const counterEl = document.getElementById('selectedPermCounter');
            if (counterEl) counterEl.textContent = checkedCount;
        }
    }"
    class="min-h-screen bg-slate-50 text-slate-900 flex">

    <x-sidebar />

    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        {{-- Top Header --}}
        <header class="bg-white border-b border-slate-200 sticky top-0 z-30">
            <div class="w-full px-4 sm:px-6 lg:px-8 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" type="button" class="lg:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <a href="{{ route('dashboard') }}" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center shrink-0 transition" title="Kembali ke Dasbor" aria-label="Kembali ke Dasbor">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </a>
                    <div>
                        <h1 class="font-extrabold text-lg sm:text-xl text-slate-900 leading-tight">Matriks Role &amp; Permission Dinamis (RBAC)</h1>
                        <p class="text-xs text-slate-500 font-medium">Pengaturan Hak Akses Seluruh Fitur PRISMA UHN oleh Superadmin</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <button @click="createRoleOpen = true" type="button"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-extrabold shadow-xs transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Tambah Role Baru</span>
                    </button>
                    <span class="px-3 py-1.5 rounded-xl text-xs font-extrabold bg-red-50 text-red-700 border border-red-200">
                        Kendali Penuh: Superadmin
                    </span>
                </div>
            </div>
        </header>

        <main class="flex-1 w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-bold flex items-start gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm font-bold flex items-start gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
            @if($errors->any())
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold space-y-1">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            {{-- KPI Summary --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Role Aktif</p>
                    <p class="text-2xl font-black text-slate-900 mt-1">{{ $stats['total_roles'] }}</p>
                    <p class="text-[11px] text-slate-500 mt-0.5">Peran institusi &amp; kustom</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Granularitas Izin</p>
                    <p class="text-2xl font-black text-blue-700 mt-1">{{ $stats['total_permissions'] }}</p>
                    <p class="text-[11px] text-slate-500 mt-0.5">Mencakup seluruh fitur sistem</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Kategori Modul</p>
                    <p class="text-2xl font-black text-indigo-700 mt-1">{{ $stats['total_groups'] }}</p>
                    <p class="text-[11px] text-slate-500 mt-0.5">Grup fitur &amp; otoritas divisi</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Akun Terdaftar</p>
                    <p class="text-2xl font-black text-emerald-700 mt-1">{{ $stats['total_users'] }}</p>
                    <p class="text-[11px] text-slate-500 mt-0.5">Terikat ke matriks RBAC</p>
                </div>
            </div>

            {{-- Navigation Tabs --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-1.5 inline-flex items-center gap-1 shadow-xs">
                <button type="button" @click="activeTab = 'matrix'"
                        :class="activeTab === 'matrix' ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'"
                        class="px-4 py-2 rounded-xl text-xs font-extrabold transition">
                    Matriks Role &amp; Hak Akses Fitur
                </button>
                <button type="button" @click="activeTab = 'users'"
                        :class="activeTab === 'users' ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'"
                        class="px-4 py-2 rounded-xl text-xs font-extrabold transition">
                    Penugasan Role Pengguna
                </button>
            </div>

            {{-- TAB 1: MATRIX ROLE & PERMISSIONS --}}
            <div x-show="activeTab === 'matrix'" x-cloak class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                {{-- Left Column: Role List --}}
                <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
                        <div>
                            <h2 class="text-sm font-extrabold text-slate-900">Daftar Role Sistem</h2>
                            <p class="text-[11px] text-slate-500">Pilih role untuk mengatur izin fiturnya</p>
                        </div>
                        <span class="px-2 py-0.5 rounded-full bg-slate-200 text-slate-700 text-[10px] font-extrabold">{{ $roles->count() }}</span>
                    </div>

                    <div class="divide-y divide-slate-100 max-h-[680px] overflow-y-auto">
                        @foreach($roles as $roleItem)
                            @php
                                $isSelected = $selectedRole && $selectedRole->id === $roleItem->id;
                                $isRoleSuper = $roleItem->name === 'Superadmin';
                                $isCore = in_array($roleItem->name, $coreRoleNames, true);
                            @endphp
                            <div class="p-3.5 transition flex items-start justify-between gap-2 {{ $isSelected ? 'bg-blue-50/80 border-l-4 border-blue-600' : 'hover:bg-slate-50' }}">
                                <a href="{{ route('admin.role-permissions.index', ['role_id' => $roleItem->id]) }}" class="min-w-0 flex-1 block">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-xs font-extrabold {{ $isSelected ? 'text-blue-900' : 'text-slate-800' }}">{{ $roleItem->display_name }}</span>
                                        @if($isRoleSuper)
                                            <span class="px-1.5 py-0.5 rounded bg-red-100 text-red-700 text-[9px] font-black uppercase">Full Access</span>
                                        @elseif(!$isCore)
                                            <span class="px-1.5 py-0.5 rounded bg-indigo-100 text-indigo-700 text-[9px] font-black uppercase">Kustom</span>
                                        @endif
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5 line-clamp-1">{{ $roleItem->description ?: $roleItem->name }}</p>
                                    <div class="flex items-center gap-3 mt-1.5 text-[10px] font-bold text-slate-500">
                                        <span>{{ $isRoleSuper ? $stats['total_permissions'] : $roleItem->permissions_count }}/{{ $stats['total_permissions'] }} Izin</span>
                                        <span>&bull;</span>
                                        <span>{{ $roleItem->users_count }} Pengguna</span>
                                    </div>
                                </a>

                                @if(!$isCore && $roleItem->users_count === 0)
                                    <form method="POST" action="{{ route('admin.role-permissions.roles.destroy', $roleItem) }}"
                                          data-confirm="Apakah Anda yakin ingin menghapus role kustom {{ $roleItem->display_name }} dari matriks hak akses?"
                                          data-confirm-title="Konfirmasi Hapus Role Kustom"
                                          data-confirm-detail="{{ $roleItem->display_name }} ({{ $roleItem->name }})"
                                          data-confirm-variant="danger"
                                          data-confirm-label="Ya, Hapus Role">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus Role Kustom">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Right Column: Permission Checkboxes for Selected Role --}}
                <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                    @if($selectedRole)
                        @php $isSelectedSuperadmin = $selectedRole->name === 'Superadmin'; @endphp

                        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50/70 space-y-4">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h2 class="text-base sm:text-lg font-black text-slate-900">{{ $selectedRole->display_name }}</h2>
                                        <span class="px-2 py-0.5 rounded-md bg-slate-200 text-slate-700 font-mono text-[10px] font-bold">{{ $selectedRole->name }}</span>
                                    </div>
                                    <p class="text-xs text-slate-600 mt-1">{{ $selectedRole->description }}</p>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <div class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-xs font-extrabold text-slate-700">
                                        Aktif: <span id="selectedPermCounter" class="text-blue-700">{{ $isSelectedSuperadmin ? $stats['total_permissions'] : count($selectedPermissionIds) }}</span> / {{ $stats['total_permissions'] }}
                                    </div>
                                    @if(!$isSelectedSuperadmin && in_array($selectedRole->name, $coreRoleNames, true))
                                        <form method="POST" action="{{ route('admin.role-permissions.reset', $selectedRole) }}"
                                              data-confirm="Kembalikan seluruh izin role {{ $selectedRole->display_name }} ke standar default UHN?"
                                              data-confirm-title="Konfirmasi Reset Hak Akses Default"
                                              data-confirm-detail="Role: {{ $selectedRole->display_name }}"
                                              data-confirm-variant="warning"
                                              data-confirm-label="Ya, Reset ke Default">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-white hover:bg-amber-50 text-amber-700 border border-amber-200 text-xs font-extrabold transition">
                                                Reset Default
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>

                            @if($isSelectedSuperadmin)
                                <div class="p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-900 text-xs font-semibold flex items-start gap-2.5">
                                    <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    <div>
                                        <p class="font-extrabold">Proteksi Superadmin Aktif</p>
                                        <p class="mt-0.5">Sesuai kebijakan keamanan sistem, role Superadmin secara otomatis memiliki akses penuh ke seluruh {{ $stats['total_permissions'] }} fitur PRISMA UHN dan tidak dapat dibatasi.</p>
                                    </div>
                                </div>
                            @else
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div class="relative flex-1">
                                        <input type="text" x-model="permSearch" placeholder="Cari nama fitur atau kode izin (mis. verifikasi, keuangan, hki, analitik)..."
                                               class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-300 bg-white text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <button type="button" @click="toggleAllPermissions(true)"
                                                class="px-3 py-2 rounded-xl bg-white hover:bg-blue-50 text-blue-700 border border-blue-200 text-xs font-bold transition">
                                            Pilih Semua
                                        </button>
                                        <button type="button" @click="toggleAllPermissions(false)"
                                                class="px-3 py-2 rounded-xl bg-white hover:bg-slate-100 text-slate-600 border border-slate-200 text-xs font-bold transition">
                                            Kosongkan
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <form method="POST" action="{{ route('admin.role-permissions.update', $selectedRole) }}" class="p-6 space-y-5">
                            @csrf
                            @method('PUT')

                            @foreach($permissionGroups as $groupName => $groupPerms)
                                @php $groupSlug = \Illuminate\Support\Str::slug($groupName); @endphp
                                <div class="rounded-2xl border border-slate-200 overflow-hidden">
                                    <div class="px-4 py-3 bg-slate-100/80 border-b border-slate-200 flex items-center justify-between gap-2">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                                            <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-800">{{ $groupName }}</h3>
                                            <span class="px-2 py-0.5 rounded-full bg-white border border-slate-200 text-[10px] font-bold text-slate-600">{{ $groupPerms->count() }} fitur</span>
                                        </div>
                                        @if(!$isSelectedSuperadmin)
                                            <button type="button" @click="toggleGroupPermissions('{{ $groupSlug }}')"
                                                    class="text-[11px] font-bold text-blue-700 hover:underline">
                                                Toggle Grup
                                            </button>
                                        @endif
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5 p-3.5 bg-white">
                                        @foreach($groupPerms as $perm)
                                            @php
                                                $isChecked = $isSelectedSuperadmin || in_array($perm->id, $selectedPermissionIds, true);
                                                $searchText = strtolower($perm->display_name . ' ' . $perm->name . ' ' . $groupName);
                                            @endphp
                                            <label x-show="!permSearch || '{{ addslashes($searchText) }}'.includes(permSearch.toLowerCase())"
                                                   class="flex items-start gap-3 p-2.5 rounded-xl border border-slate-200 hover:border-blue-300 hover:bg-blue-50/30 transition cursor-pointer">
                                                <input type="checkbox"
                                                       name="permissions[]"
                                                       value="{{ $perm->id }}"
                                                       data-group="{{ $groupSlug }}"
                                                       @change="refreshCounters()"
                                                       {{ $isChecked ? 'checked' : '' }}
                                                       {{ $isSelectedSuperadmin ? 'disabled' : '' }}
                                                       class="rbac-perm-checkbox mt-0.5 w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-xs font-bold text-slate-800 leading-snug">{{ $perm->display_name }}</p>
                                                    <p class="text-[10px] font-mono text-slate-400 mt-0.5">{{ $perm->name }}</p>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach

                            @if(!$isSelectedSuperadmin)
                                <div class="pt-3 border-t border-slate-200 flex items-center justify-between gap-3">
                                    <p class="text-xs text-slate-500">Perubahan akan langsung memperbarui menu sidebar &amp; verifikasi akses pada seluruh rute modul terkait.</p>
                                    <button type="submit"
                                            class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-blue-700 text-white text-xs font-extrabold shadow-sm transition shrink-0">
                                        Simpan Hak Akses Role
                                    </button>
                                </div>
                            @endif
                        </form>
                    @endif
                </div>
            </div>

            {{-- TAB 2: USER ROLE ASSIGNMENT --}}
            <div x-show="activeTab === 'users'" x-cloak class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/70 flex flex-col md:flex-row md:items-center justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900">Penugasan Multi-Role Akun Pengguna</h2>
                        <p class="text-xs text-slate-500">Atur satu atau beberapa role untuk akun Dosen Tetap dan Operator P3M.</p>
                    </div>

                    <form method="GET" action="{{ route('admin.role-permissions.index') }}" class="flex flex-wrap items-center gap-2">
                        <input type="hidden" name="tab" value="users">
                        <input type="text" name="user_q" value="{{ request('user_q') }}" placeholder="Cari nama, email, NIDN..."
                               class="px-3 py-2 rounded-xl border border-slate-300 bg-white text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <select name="user_role" class="px-3 py-2 rounded-xl border border-slate-300 bg-white text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Role</option>
                            @foreach($roles as $r)
                                <option value="{{ $r->id }}" {{ (string) request('user_role') === (string) $r->id ? 'selected' : '' }}>
                                    {{ $r->display_name }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-extrabold">
                            Filter
                        </button>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-xs">
                        <thead class="bg-slate-50 text-slate-500 uppercase font-extrabold text-[10px]">
                            <tr>
                                <th class="px-5 py-3 text-left">Pengguna &amp; Identitas</th>
                                <th class="px-5 py-3 text-left">Unit / Program Studi</th>
                                <th class="px-5 py-3 text-left">Role Saat Ini</th>
                                <th class="px-5 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($users as $u)
                                @php
                                    $uRoleIds = $u->roles->pluck('id')->all();
                                    $isProtectedSuper = ($u->id === Auth::id() || in_array($u->email, ['superadmin@harkatnegeri.ac.id', 'superadmin@uhn.ac.id'], true));
                                    $userPayload = [
                                        'id' => $u->id,
                                        'name' => $u->name,
                                        'email' => $u->email,
                                        'isProtected' => $isProtectedSuper,
                                        'roleIds' => $uRoleIds,
                                        'actionUrl' => route('admin.role-permissions.users.update', $u),
                                    ];
                                @endphp
                                <tr class="hover:bg-slate-50/70">
                                    <td class="px-5 py-3">
                                        <p class="font-extrabold text-slate-900">{{ $u->name }}</p>
                                        <p class="text-[11px] text-slate-500">{{ $u->email ?? 'Belum ada email' }} &bull; {{ $u->nidn_nim ?? '-' }}</p>
                                    </td>
                                    <td class="px-5 py-3 text-slate-600">
                                        <p class="font-semibold">{{ $u->prodi->nama_prodi ?? ($u->jabatan ?? 'Unit P3M') }}</p>
                                        <p class="text-[11px] text-slate-400">{{ $u->fakultas->nama_fakultas ?? 'Universitas Harkat Negeri' }}</p>
                                    </td>
                                    <td class="px-5 py-3">
                                        <div class="flex flex-wrap gap-1.5">
                                            @forelse($u->roles as $ur)
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $ur->name === 'Superadmin' ? 'bg-red-100 text-red-700 border border-red-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                                    {{ $ur->display_name }}
                                                </span>
                                            @empty
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500">Belum ditetapkan</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        <button type="button"
                                                @click='openUserModal(@json($userPayload))'
                                                class="px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-blue-700 text-white text-[11px] font-bold transition">
                                            Atur Role
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-8 text-center text-slate-500">Tidak ada pengguna ditemukan sesuai filter.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $users->appends(['tab' => 'users'])->links() }}
                </div>
            </div>
        </main>
    </div>

    {{-- MODAL: TAMBAH ROLE BARU --}}
    <div x-show="createRoleOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
        <div @click.outside="createRoleOpen = false" class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-md overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <h3 class="text-sm font-black text-slate-900">Tambah Role Baru</h3>
                <button type="button" @click="createRoleOpen = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.role-permissions.roles.store') }}" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Tampilan Role *</label>
                    <input type="text" name="display_name" required placeholder="Contoh: Operator Sentra HKI"
                           class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Kode Identitas Role (Opsional)</label>
                    <input type="text" name="name" placeholder="Kosongkan untuk otomatis mengikuti Nama Tampilan"
                           class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Salin Izin Awal Dari Role (Opsional)</label>
                    <select name="clone_role_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Mulai kosong (tanpa izin bawaan)</option>
                        @foreach($roles as $r)
                            <option value="{{ $r->id }}">{{ $r->display_name }} ({{ $r->permissions_count }} izin)</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi Tugas Role</label>
                    <textarea name="description" rows="2" placeholder="Jelaskan tanggung jawab dan ruang lingkup fitur role ini..."
                              class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                <div class="pt-2 flex items-center justify-end gap-2">
                    <button type="button" @click="createRoleOpen = false" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-extrabold">
                        Buat Role
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL: ATUR MULTI-ROLE PENGGUNA --}}
    <div x-show="userModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
        <div @click.outside="userModalOpen = false" class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-black text-slate-900">Atur Role Pengguna</h3>
                    <p class="text-[11px] text-slate-500" x-text="editUserName + ' (' + editUserEmail + ')'"></p>
                </div>
                <button type="button" @click="userModalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" :action="userActionUrl" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="user_q" value="{{ request('user_q') }}">
                <input type="hidden" name="user_role" value="{{ request('user_role') }}">
                <input type="hidden" name="page" value="{{ request('page', 1) }}">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-72 overflow-y-auto pr-1">
                    @foreach($roles as $r)
                        <label class="flex items-center gap-2.5 p-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer">
                            <input type="checkbox"
                                   name="role_ids[]"
                                   value="{{ $r->id }}"
                                   x-model.number="editUserRoles"
                                   class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-xs font-bold text-slate-800">{{ $r->display_name }}</span>
                        </label>
                    @endforeach
                </div>

                <template x-if="editUserIsProtected">
                    <p class="text-[11px] text-amber-700 bg-amber-50 border border-amber-200 rounded-xl p-2.5 font-semibold">
                        Akun Superadmin utama secara otomatis mempertahankan role Superadmin untuk mencegah terkuncinya sistem.
                    </p>
                </template>

                <div class="pt-2 flex items-center justify-end gap-2">
                    <button type="button" @click="userModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-blue-700 text-white text-xs font-extrabold">
                        Simpan Role Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

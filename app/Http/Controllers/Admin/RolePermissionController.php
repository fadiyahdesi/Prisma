<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RolePermissionController extends Controller
{
    /**
     * Tampilkan konsol matriks Role & Permission PRISMA UHN untuk Superadmin.
     */
    public function index(Request $request): View
    {
        $this->authorizeRbac();

        // Sinkronisasi katalog jika ada izin baru pada definisi sistem
        Permission::firstOrCreate(
            ['name' => 'rbac.manage'],
            ['display_name' => 'Pengaturan Role & Permission Dinamis (Superadmin)', 'group' => 'Keamanan & Kontrol Superadmin']
        );

        $roleOrder = array_keys(Permission::defaultRoleMap());

        $roles = Role::with('permissions:id,name')
            ->withCount(['users', 'permissions'])
            ->get()
            ->sortBy(function (Role $role) use ($roleOrder) {
                $idx = array_search($role->name, $roleOrder, true);
                return $idx === false ? 999 : $idx;
            })
            ->values();

        $requestedRoleId = $request->integer('role_id');
        $selectedRole = $requestedRoleId
            ? $roles->firstWhere('id', $requestedRoleId)
            : ($roles->firstWhere('name', 'Admin P3M') ?? $roles->first());

        if (!$selectedRole && $roles->isNotEmpty()) {
            $selectedRole = $roles->first();
        }

        $selectedPermissionIds = $selectedRole
            ? $selectedRole->permissions->pluck('id')->all()
            : [];

        $allPermissions = Permission::orderBy('id')->get();
        $permissionGroups = $allPermissions->groupBy('group');

        $defaultRoleMap = Permission::defaultRoleMap();
        $coreRoleNames = array_keys($defaultRoleMap);

        // Daftar pengguna untuk manajemen penugasan role cepat
        $userQuery = User::with(['roles', 'fakultas', 'prodi'])->orderBy('name');

        if ($request->filled('user_q')) {
            $q = trim((string) $request->query('user_q'));
            $like = DB::getDriverName() === 'pgsql' ? 'ilike' : 'like';
            $userQuery->where(function ($sub) use ($q, $like) {
                $sub->where('name', $like, "%{$q}%")
                    ->orWhere('email', $like, "%{$q}%")
                    ->orWhere('nidn_nim', $like, "%{$q}%");
            });
        }

        if ($request->filled('user_role')) {
            $roleFilter = $request->integer('user_role');
            $userQuery->whereHas('roles', fn ($rq) => $rq->where('roles.id', $roleFilter));
        }

        $users = $userQuery->paginate(12)->withQueryString();
        $activeTab = $request->query('tab', ($request->filled('user_q') || $request->filled('user_role')) ? 'users' : 'matrix');

        $stats = [
            'total_roles' => $roles->count(),
            'total_permissions' => $allPermissions->count(),
            'total_groups' => $permissionGroups->count(),
            'total_users' => User::count(),
        ];

        return view('admin.role-permissions.index', compact(
            'roles',
            'selectedRole',
            'selectedPermissionIds',
            'permissionGroups',
            'allPermissions',
            'defaultRoleMap',
            'coreRoleNames',
            'users',
            'activeTab',
            'stats'
        ));
    }

    /**
     * Simpan perubahan matriks izin fitur pada sebuah role.
     */
    public function updateRolePermissions(Request $request, Role $role): RedirectResponse
    {
        $this->authorizeRbac();

        if ($role->name === 'Superadmin') {
            $allIds = Permission::pluck('id')->all();
            $role->permissions()->sync($allIds);

            return redirect()
                ->route('admin.role-permissions.index', ['role_id' => $role->id])
                ->with('success', 'Keamanan sistem aktif: Role Superadmin selalu memiliki akses penuh ke seluruh ' . count($allIds) . ' fitur dan tidak dapat dibatasi.');
        }

        $validated = $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        $beforeNames = $role->permissions()->orderBy('name')->pluck('name')->all();
        $selectedIds = array_values(array_unique(array_map('intval', $validated['permissions'] ?? [])));

        $role->permissions()->sync($selectedIds);
        $afterNames = Permission::whereIn('id', $selectedIds)->orderBy('name')->pluck('name')->all();

        AuditLogService::log(
            'ROLE_PERMISSIONS_UPDATED',
            ['role' => $role->name, 'permissions_count' => count($beforeNames), 'permissions' => $beforeNames],
            ['role' => $role->name, 'permissions_count' => count($afterNames), 'permissions' => $afterNames],
            Auth::id()
        );

        return redirect()
            ->route('admin.role-permissions.index', ['role_id' => $role->id])
            ->with('success', "Hak akses role '{$role->display_name}' berhasil diperbarui (" . count($afterNames) . " izin fitur aktif). Perubahan berlaku langsung pada menu dan otorisasi sistem.");
    }

    /**
     * Pulihkan hak akses role ke konfigurasi bawaan standar UHN.
     */
    public function resetRoleDefaults(Role $role): RedirectResponse
    {
        $this->authorizeRbac();

        $defaultMap = Permission::defaultRoleMap();
        if ($role->name === 'Superadmin') {
            $targetNames = array_column(Permission::catalog(), 'name');
        } elseif (array_key_exists($role->name, $defaultMap)) {
            $targetNames = $defaultMap[$role->name];
        } else {
            return redirect()
                ->route('admin.role-permissions.index', ['role_id' => $role->id])
                ->with('error', "Role kustom '{$role->display_name}' tidak memiliki templat bawaan.");
        }

        $beforeCount = $role->permissions()->count();
        $ids = Permission::whereIn('name', $targetNames)->pluck('id')->all();
        $role->permissions()->sync($ids);

        AuditLogService::log(
            'ROLE_PERMISSIONS_RESET',
            ['role' => $role->name, 'permissions_count' => $beforeCount],
            ['role' => $role->name, 'permissions_count' => count($ids)],
            Auth::id()
        );

        return redirect()
            ->route('admin.role-permissions.index', ['role_id' => $role->id])
            ->with('success', "Hak akses role '{$role->display_name}' berhasil dipulihkan ke pengaturan standar UHN (" . count($ids) . ' izin fitur).');
    }

    /**
     * Buat role baru oleh Superadmin (opsional menyalin izin dari role yang sudah ada).
     */
    public function storeRole(Request $request): RedirectResponse
    {
        $this->authorizeRbac();

        $validated = $request->validate([
            'display_name' => 'required|string|min:2|max:80',
            'name' => 'nullable|string|min:2|max:60',
            'description' => 'nullable|string|max:255',
            'clone_role_id' => 'nullable|integer|exists:roles,id',
        ]);

        $roleName = trim((string) ($validated['name'] ?? $validated['display_name']));
        if (Role::where('name', $roleName)->exists()) {
            return redirect()
                ->route('admin.role-permissions.index')
                ->withInput()
                ->with('error', "Nama role '{$roleName}' sudah terdaftar di sistem.");
        }

        $role = Role::create([
            'name' => $roleName,
            'display_name' => trim($validated['display_name']),
            'description' => trim((string) ($validated['description'] ?? 'Role kustom yang dibuat oleh Superadmin PRISMA.')),
        ]);

        $copiedCount = 0;
        if (!empty($validated['clone_role_id'])) {
            $sourceRole = Role::with('permissions:id')->find($validated['clone_role_id']);
            if ($sourceRole) {
                $ids = $sourceRole->permissions->pluck('id')->all();
                $role->permissions()->sync($ids);
                $copiedCount = count($ids);
            }
        }

        AuditLogService::log(
            'ROLE_CREATED',
            null,
            ['role_id' => $role->id, 'role' => $role->name, 'copied_permissions' => $copiedCount],
            Auth::id()
        );

        return redirect()
            ->route('admin.role-permissions.index', ['role_id' => $role->id])
            ->with('success', "Role baru '{$role->display_name}' berhasil ditambahkan ({$copiedCount} izin disalin).");
    }

    /**
     * Hapus role kustom yang tidak lagi memiliki pengguna.
     */
    public function destroyRole(Role $role): RedirectResponse
    {
        $this->authorizeRbac();

        $coreRoles = array_keys(Permission::defaultRoleMap());
        if (in_array($role->name, $coreRoles, true)) {
            return redirect()
                ->route('admin.role-permissions.index', ['role_id' => $role->id])
                ->with('error', "Role resmi sistem '{$role->display_name}' tidak dapat dihapus.");
        }

        if ($role->users()->count() > 0) {
            return redirect()
                ->route('admin.role-permissions.index', ['role_id' => $role->id])
                ->with('error', "Role '{$role->display_name}' masih dimiliki oleh {$role->users()->count()} pengguna. Pindahkan pengguna terlebih dahulu.");
        }

        $roleName = $role->display_name;
        $role->permissions()->detach();
        $role->delete();

        AuditLogService::log('ROLE_DELETED', ['role' => $roleName], null, Auth::id());

        return redirect()
            ->route('admin.role-permissions.index')
            ->with('success', "Role kustom '{$roleName}' berhasil dihapus.");
    }

    /**
     * Perbarui multi-role milik pengguna langsung dari konsol Superadmin.
     */
    public function updateUserRoles(Request $request, User $user): RedirectResponse
    {
        $this->authorizeRbac();

        $validated = $request->validate([
            'role_ids' => 'required|array|min:1',
            'role_ids.*' => 'integer|exists:roles,id',
        ]);

        $roleIds = array_values(array_unique(array_map('intval', $validated['role_ids'])));

        // Proteksi diri sendiri & akun superadmin utama agar tidak pernah kehilangan akses Superadmin
        $superRoleId = Role::where('name', 'Superadmin')->value('id');
        if ($superRoleId && ($user->id === Auth::id() || in_array($user->email, ['superadmin@harkatnegeri.ac.id', 'superadmin@uhn.ac.id'], true))) {
            if (!in_array($superRoleId, $roleIds, true)) {
                $roleIds[] = $superRoleId;
            }
        }

        $beforeRoles = $user->roles()->pluck('name')->all();
        $user->roles()->sync($roleIds);
        $afterRoles = Role::whereIn('id', $roleIds)->pluck('name')->all();

        AuditLogService::log(
            'USER_ROLES_UPDATED_BY_SUPERADMIN',
            ['target_user' => $user->email, 'roles' => $beforeRoles],
            ['target_user' => $user->email, 'roles' => $afterRoles],
            Auth::id()
        );

        return redirect()
            ->route('admin.role-permissions.index', [
                'tab' => 'users',
                'user_q' => $request->input('user_q'),
                'user_role' => $request->input('user_role'),
                'page' => $request->input('page', 1),
            ])
            ->with('success', "Role pengguna '{$user->name}' berhasil diperbarui: " . implode(', ', $afterRoles) . '.');
    }

    private function authorizeRbac(): void
    {
        $user = Auth::user();
        abort_unless(
            $user && ($user->hasRole('Superadmin') || $user->hasPermission('rbac.manage')),
            403,
            'Akses Ditolak: Hanya Superadmin yang dapat mengatur Role & Permission sistem.'
        );
    }
}

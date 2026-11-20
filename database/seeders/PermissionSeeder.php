<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(bool $forceReset = false): void
    {
        // 1. Upsert semua 42 izin fitur dari katalog resmi
        $permIdsByName = [];
        foreach (Permission::catalog() as $item) {
            $perm = Permission::updateOrCreate(
                ['name' => $item['name']],
                [
                    'display_name' => $item['display_name'],
                    'group' => $item['group'],
                ]
            );
            $permIdsByName[$perm->name] = $perm->id;
        }

        // 2. Sinkronisasi permission bawaan setiap role (tanpa menimpa ubahan custom Superadmin kecuali $forceReset=true)
        foreach (Permission::defaultRoleMap() as $roleName => $permNames) {
            $role = Role::where('name', $roleName)->first();
            if (!$role) {
                continue;
            }

            $ids = collect($permNames)
                ->map(fn ($name) => $permIdsByName[$name] ?? null)
                ->filter()
                ->values()
                ->all();

            // Superadmin SELALU memegang semua izin
            if ($roleName === 'Superadmin' || $forceReset || $role->permissions()->count() === 0) {
                $role->permissions()->sync($ids);
            }
        }
    }
}

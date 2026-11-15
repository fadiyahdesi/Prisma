<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class DemoRealLecturerWorkflowSeeder extends Seeder
{
    public function run(): void
    {
        $dosenRole = Role::firstOrCreate(['name' => 'Dosen / Pengusul']);
        $kplRole = Role::firstOrCreate(['name' => 'Kepala P3M']);

        // Kepala P3M: Sharfina Febbi Handayani (dari daftar 146 Dosen Tetap UHN)
        $sharfina = User::where('nidn_nim', 'like', '0617029201%')->first()
            ?? User::where('nipy', '08.020.451')->first();

        if ($sharfina) {
            $sharfina->update(['is_otp_verified' => true]);
            $sharfina->roles()->syncWithoutDetaching([$kplRole->id, $dosenRole->id]);
        }

        $this->command?->info('Role Kepala P3M untuk Sharfina Febbi Handayani berhasil disinkronkan (tanpa data usulan dummy).');
    }
}

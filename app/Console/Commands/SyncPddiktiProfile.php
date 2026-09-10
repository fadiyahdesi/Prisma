<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\Integrations\PddiktiProfileSync;
use Illuminate\Console\Command;
use Throwable;

class SyncPddiktiProfile extends Command
{
    protected $signature = 'pddikti:sync-profile
                            {user : User ID, NIDN, or NIM}
                            {--type=nidn : Identifier type: nidn or nim}';

    protected $description = 'Synchronize one user profile from PDDIKTI or the local mock connector';

    public function handle(PddiktiProfileSync $sync): int
    {
        $type = $this->option('type');
        if (!in_array($type, ['nidn', 'nim'], true)) {
            $this->error('Option --type harus nidn atau nim.');
            return self::INVALID;
        }

        $value = (string) $this->argument('user');
        $user = User::query()
            ->whereKey(is_numeric($value) ? (int) $value : 0)
            ->orWhere('nidn_nim', $value)
            ->first();

        if (!$user) {
            $this->error('User tidak ditemukan berdasarkan ID/NIDN/NIM tersebut.');
            return self::FAILURE;
        }

        try {
            $sync->sync($user, $type);
        } catch (Throwable $exception) {
            report($exception);
            $this->error($exception->getMessage());
            return self::FAILURE;
        }

        $this->info("Profil {$user->email} berhasil disinkronkan menggunakan mode ".(config('services.pddikti.enabled') ? 'API PDDIKTI' : 'mock').'.');
        return self::SUCCESS;
    }
}

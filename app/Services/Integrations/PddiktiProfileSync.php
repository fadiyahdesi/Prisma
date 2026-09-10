<?php

namespace App\Services\Integrations;

use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class PddiktiProfileSync
{
    public function __construct(private readonly PddiktiClient $client)
    {
    }

    public function sync(User $user, string $identifierType = 'nidn'): User
    {
        $identifier = trim((string) $user->nidn_nim);

        if ($identifier === '') {
            throw new RuntimeException("User {$user->id} belum memiliki NIDN/NIM.");
        }

        $profile = config('services.pddikti.enabled')
            ? $this->fetchRemoteProfile($identifier, $identifierType)
            : $this->mockProfile($user, $identifier, $identifierType);

        $user->update(array_filter([
            'name' => Arr::get($profile, 'name'),
            'email' => Arr::get($profile, 'email'),
            'nidn_nim' => Arr::get($profile, 'identifier', $identifier),
            'jabatan_fungsional' => Arr::get($profile, 'jabatan_fungsional'),
            'id_fakultas' => Arr::get($profile, 'id_fakultas'),
            'id_prodi' => Arr::get($profile, 'id_prodi'),
            'email_verified_at' => Arr::get($profile, 'email') ? now() : null,
        ], static fn ($value) => $value !== null));

        AuditLogService::log('PDDIKTI_PROFILE_SYNCED', null, [
            'provider' => config('services.pddikti.enabled') ? 'pddikti' : 'mock',
            'identifier_type' => $identifierType,
            'identifier' => $identifier,
        ], $user->id);

        return $user->refresh();
    }

    private function fetchRemoteProfile(string $identifier, string $identifierType): array
    {
        $response = $identifierType === 'nim'
            ? $this->client->findByNim($identifier)
            : $this->client->findByNidn($identifier);

        $profile = Arr::get($response, 'data.0', Arr::get($response, 'data', $response));

        if (!is_array($profile) || empty($profile)) {
            throw new RuntimeException('Profil PDDIKTI tidak ditemukan.');
        }

        return [
            'name' => $profile['nama'] ?? $profile['name'] ?? null,
            'email' => $profile['email'] ?? null,
            'identifier' => $profile[$identifierType] ?? $identifier,
            'jabatan_fungsional' => $profile['jabatan_fungsional'] ?? null,
            'id_fakultas' => $profile['id_fakultas'] ?? null,
            'id_prodi' => $profile['id_prodi'] ?? null,
        ];
    }

    private function mockProfile(User $user, string $identifier, string $identifierType): array
    {
        Log::info('PDDIKTI mock profile used', [
            'user_id' => $user->id,
            'identifier_type' => $identifierType,
            'identifier' => $identifier,
        ]);

        return [
            'name' => $user->name,
            'email' => $user->email,
            'identifier' => $identifier,
            'jabatan_fungsional' => $user->jabatan_fungsional,
            'id_fakultas' => $user->id_fakultas,
            'id_prodi' => $user->id_prodi,
        ];
    }
}

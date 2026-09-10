<?php

namespace App\Services\Integrations;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PddiktiClient
{
    private function client(): PendingRequest
    {
        $baseUrl = config('services.pddikti.base_url');

        if (!$baseUrl || !config('services.pddikti.token')) {
            throw new RuntimeException('PDDIKTI belum dikonfigurasi. Isi PDDIKTI_BASE_URL dan PDDIKTI_API_TOKEN.');
        }

        return Http::baseUrl(rtrim($baseUrl, '/'))
            ->acceptJson()
            ->withToken(config('services.pddikti.token'))
            ->timeout(config('services.pddikti.timeout', 15));
    }

    public function findByNidn(string $nidn): array
    {
        return $this->client()
            ->get(config('services.pddikti.dosen_path'), ['nidn' => $nidn, 'kode_pt' => config('services.pddikti.institution_code')])
            ->throw()
            ->json();
    }

    public function findByNim(string $nim): array
    {
        return $this->client()
            ->get(config('services.pddikti.mahasiswa_path'), ['nim' => $nim, 'kode_pt' => config('services.pddikti.institution_code')])
            ->throw()
            ->json();
    }

    public function searchByName(string $name): array
    {
        return $this->client()
            ->get(config('services.pddikti.search_path'), [
                'nama' => $name,
                'kode_pt' => config('services.pddikti.institution_code'),
            ])
            ->throw()
            ->json();
    }
}

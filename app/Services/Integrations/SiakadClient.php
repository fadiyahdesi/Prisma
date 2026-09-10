<?php

namespace App\Services\Integrations;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class SiakadClient
{
    public function authorizationUrl(string $state): string
    {
        $url = config('services.siakad.authorize_url');
        if (!$url) {
            throw new RuntimeException('SIAKAD OAuth belum dikonfigurasi.');
        }

        return $url.'?'.http_build_query([
            'response_type' => 'code',
            'client_id' => config('services.siakad.client_id'),
            'redirect_uri' => config('services.siakad.redirect_uri'),
            'scope' => config('services.siakad.scopes'),
            'state' => $state,
        ]);
    }

    public function userFromCode(string $code): array
    {
        $token = Http::asForm()->post(config('services.siakad.token_url'), [
            'grant_type' => 'authorization_code',
            'client_id' => config('services.siakad.client_id'),
            'client_secret' => config('services.siakad.client_secret'),
            'redirect_uri' => config('services.siakad.redirect_uri'),
            'code' => $code,
        ])->throw()->json();

        return Http::withToken($token['access_token'])
            ->acceptJson()
            ->get(config('services.siakad.userinfo_url'))
            ->throw()
            ->json();
    }
}

<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'siakad' => [
        'enabled' => (bool) env('SIAKAD_ENABLED', false),
        'base_url' => env('SIAKAD_BASE_URL'),
        'authorize_url' => env('SIAKAD_AUTHORIZE_URL'),
        'token_url' => env('SIAKAD_TOKEN_URL'),
        'userinfo_url' => env('SIAKAD_USERINFO_URL'),
        'client_id' => env('SIAKAD_CLIENT_ID'),
        'client_secret' => env('SIAKAD_CLIENT_SECRET'),
        'redirect_uri' => env('SIAKAD_REDIRECT_URI'),
        'scopes' => env('SIAKAD_SCOPES', 'openid profile email'),
        'allowed_email_domains' => array_values(array_filter(array_map('trim', explode(',', env('SIAKAD_ALLOWED_EMAIL_DOMAINS', ''))))),
    ],

    'pddikti' => [
        'enabled' => (bool) env('PDDIKTI_ENABLED', false),
        'base_url' => env('PDDIKTI_BASE_URL'),
        'token' => env('PDDIKTI_API_TOKEN'),
        'institution_code' => env('PDDIKTI_INSTITUTION_CODE'),
        'search_path' => env('PDDIKTI_SEARCH_PATH', '/search'),
        'dosen_path' => env('PDDIKTI_DOSEN_PATH', '/dosen'),
        'mahasiswa_path' => env('PDDIKTI_MAHASISWA_PATH', '/mahasiswa'),
        'timeout' => (int) env('PDDIKTI_TIMEOUT', 15),
    ],

    'sinta' => [
        'base_url' => env('SINTA_BASE_URL', 'https://sinta.kemdiktisaintek.go.id'),
        'cache_ttl' => (int) env('SINTA_CACHE_TTL', 604800), // 7 Days TTL
        'timeout' => (int) env('SINTA_TIMEOUT', 5),
        'simulate_timeout' => (bool) env('SINTA_SIMULATE_TIMEOUT', false),
    ],

    'djki' => [
        'base_url' => env('DJKI_BASE_URL', 'https://pdki-indonesia.dgip.go.id/api/search'),
        'timeout' => (int) env('DJKI_TIMEOUT', 5),
    ],

    'minio' => [
        'enabled' => (bool) env('MINIO_ENABLED', false),
        'endpoint' => env('MINIO_ENDPOINT', 'http://127.0.0.1:9000'),
        'key' => env('MINIO_KEY'),
        'secret' => env('MINIO_SECRET'),
        'bucket' => env('MINIO_BUCKET', 'bima-documents'),
        'use_path_style_endpoint' => env('MINIO_USE_PATH_STYLE_ENDPOINT', true),
        'max_file_size_proposal_mb' => (int) env('MINIO_MAX_PROPOSAL_MB', 5),
        'max_file_size_report_mb' => (int) env('MINIO_MAX_REPORT_MB', 15),
        'clamav_enabled' => (bool) env('CLAMAV_ENABLED', false),
    ],

];

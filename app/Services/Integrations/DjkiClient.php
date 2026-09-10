<?php

namespace App\Services\Integrations;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class DjkiClient
{
    /**
     * Timeout for DJKI Web Service requests in seconds.
     */
    const TIMEOUT_SECONDS = 5;

    /**
     * Search and verify DJKI HKI registration status by Application Number or Title.
     *
     * @param string $nomorPermohonan
     * @return array
     */
    public function verifyHki(string $nomorPermohonan): array
    {
        $cleanNumber = trim($nomorPermohonan);

        if (empty($cleanNumber)) {
            return [
                'success' => false,
                'status' => 'invalid_input',
                'message' => 'Nomor permohonan HKI tidak boleh kosong.',
            ];
        }

        $baseUrl = config('services.djki.base_url', 'https://pdki-indonesia.dgip.go.id/api/search');

        try {
            // Attempt DJKI Web Parser / REST API Call
            $response = Http::withoutVerifying()
                ->timeout(self::TIMEOUT_SECONDS)
                ->get($baseUrl, [
                    'type' => 'copyright',
                    'keyword' => $cleanNumber,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (!empty($data['results']) || !empty($data['data'])) {
                    $item = $data['results'][0] ?? $data['data'][0] ?? [];
                    return [
                        'success' => true,
                        'status' => 'Granted',
                        'nomor_permohonan' => $item['nomor_permohonan'] ?? $cleanNumber,
                        'judul_hki' => $item['judul'] ?? 'Hak Cipta Perangkat Lunak / Karya Tulis',
                        'pemegang_hak' => $item['pemegang'] ?? 'Universitas Harkat Negeri',
                        'tanggal_terbit' => $item['tgl_terbit'] ?? now()->toDateString(),
                        'verified_by' => 'DJKI Live Service API',
                    ];
                }
            }
        } catch (Exception $e) {
            Log::warning("DJKI Web Parser API Exception: " . $e->getMessage() . ". Falling back to manual Sentra HKI verification.");
        }

        // Resilience Fallback Strategy: Mark for manual verification by Sentra HKI Staff
        return [
            'success' => false,
            'status' => 'Pending Manual Verification',
            'nomor_permohonan' => $cleanNumber,
            'message' => 'Layanan DJKI sedang dalam pemeliharaan. Permohonan HKI akan diverifikasi secara manual oleh staf Sentra HKI.',
            'requires_manual_verification' => true,
            'verified_by' => 'Fallback Sentra HKI UHN',
        ];
    }
}


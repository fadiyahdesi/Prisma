<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\SintaService;
use App\Services\Integrations\PddiktiClient;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Throwable;

class PddiktiController extends Controller
{
    protected SintaService $sintaService;

    public function __construct(SintaService $sintaService)
    {
        $this->sintaService = $sintaService;
    }

    public function search(Request $request, PddiktiClient $client)
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'min:2', 'max:100'],
        ]);
        $name = trim($validated['name'] ?? '');
        $results = [];
        $error = null;

        if ($name !== '') {
            if (config('services.pddikti.enabled') && config('services.pddikti.token')) {
                try {
                    $payload = $client->searchByName($name);
                    $records = Arr::get($payload, 'data', $payload);
                    $results = collect(is_array($records) ? $records : [])
                        ->map(fn (array $record) => $this->normalize($record))
                        ->values()
                        ->all();
                } catch (Throwable $exception) {
                    report($exception);
                    $error = 'PDDIKTI Feeder API tidak dapat dihubungi. Menggunakan pencarian publik live SINTA & SIAKAD Mirror.';
                }
            }

            // Fallback / Hybrid Search: Query local database & live SINTA real authors
            if (empty($results)) {
                $localUsers = User::query()
                    ->where('name', 'like', "%{$name}%")
                    ->orWhere('nidn_nim', 'like', "%{$name}%")
                    ->orWhere('sinta_id', 'like', "%{$name}%")
                    ->limit(10)
                    ->get();

                $localResults = $localUsers->map(fn (User $user) => [
                    'name' => $user->name,
                    'identifier' => 'NIDN: ' . ($user->nidn_nim ?? '-') . ' | SINTA ID: ' . ($user->sinta_id ?? '-'),
                    'type' => 'Dosen SIAKAD UHN (Terverifikasi)',
                    'affiliation' => $user->prodi?->nama_prodi ?? 'Fakultas Sains & Teknologi',
                    'total_research' => 'Metrik SINTA: ' . ($user->sinta_score_3yr ?? 0) . ' (3Yr)',
                    'total_publications' => 'Scopus H-Index: ' . ($user->h_index_scopus ?? 0),
                    'total_hki' => 'Google Scholar H-Index: ' . ($user->h_index_google_scholar ?? 0),
                ])->all();

                $liveSintaResults = $this->sintaService->searchRealAuthors($name);

                $sintaResults = collect($liveSintaResults)->map(function ($item) {
                    return [
                        'name' => $item['name'],
                        'identifier' => 'SINTA ID: ' . $item['sinta_id'],
                        'type' => 'Dosen (SINTA Kemdiktisaintek Live)',
                        'affiliation' => $item['affiliation'],
                        'total_research' => 'Terhubung BIMA',
                        'total_publications' => 'Terhubung Scopus/SINTA',
                        'total_hki' => 'Terhubung DJKI',
                    ];
                })->all();

                $results = array_merge($localResults, $sintaResults);
            }
        }

        return view('integrations.pddikti-search', compact('name', 'results', 'error'));
    }

    private function normalize(array $record): array
    {
        return [
            'name' => $record['nama'] ?? $record['name'] ?? '-',
            'identifier' => $record['nidn'] ?? $record['nim'] ?? $record['id'] ?? '-',
            'type' => isset($record['nim']) ? 'Mahasiswa' : 'Dosen',
            'affiliation' => $record['nama_pt'] ?? $record['perguruan_tinggi'] ?? 'Universitas Harkat Negeri',
            'total_research' => $record['total_penelitian'] ?? $record['jumlah_penelitian'] ?? null,
            'total_publications' => $record['total_publikasi'] ?? $record['jumlah_publikasi'] ?? null,
            'total_hki' => $record['total_hki'] ?? $record['jumlah_hki'] ?? null,
        ];
    }
}
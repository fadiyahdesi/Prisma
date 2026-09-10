<?php

namespace App\Services\Integrations;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class CrossrefClient
{
    const TIMEOUT_SECONDS = 5;

    /**
     * Clean and normalize a DOI string.
     */
    public function cleanDoi(string $doi): string
    {
        $doi = trim($doi);
        $doi = preg_replace('#^https?://(dx\.)?doi\.org/#i', '', $doi);
        return trim($doi);
    }

    /**
     * Fetch publication metadata from Crossref REST API.
     */
    public function fetchMetadata(string $doi): array
    {
        $cleanDoi = $this->cleanDoi($doi);

        if (empty($cleanDoi)) {
            return [
                'success' => false,
                'message' => 'DOI tidak boleh kosong.',
            ];
        }

        $url = "https://api.crossref.org/works/" . urlencode($cleanDoi);

        try {
            $response = Http::withoutVerifying()
                ->timeout(self::TIMEOUT_SECONDS)
                ->withHeaders([
                    'User-Agent' => 'PRISMA-UHN/1.0 (mailto:lp2m@uhn.ac.id)',
                ])
                ->get($url);

            if ($response->successful()) {
                $message = $response->json('message');

                if ($message) {
                    $title = '';
                    if (!empty($message['title'])) {
                        $title = is_array($message['title']) ? implode(' ', $message['title']) : $message['title'];
                    }

                    $journal = '';
                    if (!empty($message['container-title'])) {
                        $journal = is_array($message['container-title']) ? ($message['container-title'][0] ?? '') : $message['container-title'];
                    }

                    $issn = '';
                    if (!empty($message['ISSN'])) {
                        $issn = is_array($message['ISSN']) ? implode(', ', $message['ISSN']) : $message['ISSN'];
                    }

                    $publishedYear = null;
                    if (!empty($message['published']['date-parts'][0][0])) {
                        $publishedYear = (int) $message['published']['date-parts'][0][0];
                    } elseif (!empty($message['published-print']['date-parts'][0][0])) {
                        $publishedYear = (int) $message['published-print']['date-parts'][0][0];
                    } elseif (!empty($message['published-online']['date-parts'][0][0])) {
                        $publishedYear = (int) $message['published-online']['date-parts'][0][0];
                    } elseif (!empty($message['created']['date-parts'][0][0])) {
                        $publishedYear = (int) $message['created']['date-parts'][0][0];
                    }

                    $volume = $message['volume'] ?? '';
                    $issue = $message['issue'] ?? '';
                    $volNo = trim("Vol. {$volume} No. {$issue}", " Vol.No.");

                    $authors = [];
                    if (!empty($message['author']) && is_array($message['author'])) {
                        foreach ($message['author'] as $author) {
                            $name = trim(($author['given'] ?? '') . ' ' . ($author['family'] ?? ''));
                            if (!empty($name)) {
                                $authors[] = $name;
                            }
                        }
                    }

                    return [
                        'success' => true,
                        'doi' => $cleanDoi,
                        'judul_artikel' => $title,
                        'nama_jurnal' => $journal,
                        'issn' => $issn,
                        'tahun_terbit' => $publishedYear ?? (int) date('Y'),
                        'volume_nomor' => $volNo ?: null,
                        'url_artikel' => $message['URL'] ?? "https://doi.org/{$cleanDoi}",
                        'authors' => $authors,
                        'jumlah_penulis' => max(1, count($authors)),
                        'source' => 'crossref',
                    ];
                }
            }

            return [
                'success' => false,
                'doi' => $cleanDoi,
                'message' => 'Data artikel tidak ditemukan di Crossref untuk DOI tersebut.',
            ];
        } catch (Exception $e) {
            Log::warning("Crossref API Exception for DOI {$cleanDoi}: " . $e->getMessage());
            return [
                'success' => false,
                'doi' => $cleanDoi,
                'message' => 'Gagal menghubungi server Crossref: ' . $e->getMessage(),
            ];
        }
    }
}


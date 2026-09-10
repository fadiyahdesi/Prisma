<?php

namespace App\Services;

use App\Models\PpmHki;
use App\Models\PpmKontrak;
use App\Models\PpmPencairanDana;
use App\Models\PpmPublikasiJurnal;
use App\Models\PpmUsulan;
use App\Models\PpmUsulanAnggota;
use App\Models\RefFakultas;
use App\Models\RefProgramStudi;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    const CACHE_TTL_SECONDS = 300; // 5 minutes cache for sub-1.5s load

    /**
     * Get Executive University-Level Analytics (US-12.1).
     */
    public function getExecutiveMetrics(?int $periodeId = null, bool $forceRefresh = false): array
    {
        $cacheKey = 'executive_analytics_summary_' . ($periodeId ?? 'all');

        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, self::CACHE_TTL_SECONDS, function () use ($periodeId) {
            // 1. Usulan Base Query
            $usulanQuery = PpmUsulan::query();
            if ($periodeId) {
                $usulanQuery->where('id_periode_hibah', $periodeId);
            }

            $totalUsulan = (clone $usulanQuery)->count();
            $approvedStatuses = ['Approved', 'Ongoing', 'Completed'];
            $usulanApproved = (clone $usulanQuery)->whereIn('status', $approvedStatuses)->count();
            $usulanOngoing = (clone $usulanQuery)->where('status', 'Ongoing')->count();
            $usulanCompleted = (clone $usulanQuery)->where('status', 'Completed')->count();
            $usulanRejected = (clone $usulanQuery)->where('status', 'Rejected')->count();

            $acceptanceRate = $totalUsulan > 0 ? round(($usulanApproved / $totalUsulan) * 100, 1) : 0;

            // 2. Budget Commitment vs Realization
            $kontrakQuery = PpmKontrak::query();
            if ($periodeId) {
                $kontrakQuery->whereHas('usulan', fn($q) => $q->where('id_periode_hibah', $periodeId));
            }

            $totalPagu = (float) (clone $kontrakQuery)->sum('pagu_disetujui');
            if ($totalPagu <= 0) {
                // Fallback to dana_disetujui in ppm_usulan if kontrak is not yet set
                $totalPagu = (float) (clone $usulanQuery)->whereIn('status', $approvedStatuses)->sum('total_rab');
            }

            $pencairanQuery = PpmPencairanDana::where('status_pencairan', 'transferred');
            if ($periodeId) {
                $pencairanQuery->whereHas('kontrak.usulan', fn($q) => $q->where('id_periode_hibah', $periodeId));
            }
            $totalRealisasi = (float) $pencairanQuery->sum('jumlah_dana');
            $serapanPercentage = $totalPagu > 0 ? round(($totalRealisasi / $totalPagu) * 100, 1) : 0;

            // 3. TKT Mapping (Level 1-3 Dasar, 4-6 Terapan, 7-9 Pengembangan)
            $tktDasar = (clone $usulanQuery)->whereBetween('target_tkt', [1, 3])->count();
            $tktTerapan = (clone $usulanQuery)->whereBetween('target_tkt', [4, 6])->count();
            $tktPengembangan = (clone $usulanQuery)->whereBetween('target_tkt', [7, 9])->count();
            $tktTotal = $tktDasar + $tktTerapan + $tktPengembangan;

            $tktDistribution = [
                'dasar' => [
                    'count' => $tktDasar,
                    'pct' => $tktTotal > 0 ? round(($tktDasar / $tktTotal) * 100, 1) : 0,
                ],
                'terapan' => [
                    'count' => $tktTerapan,
                    'pct' => $tktTotal > 0 ? round(($tktTerapan / $tktTotal) * 100, 1) : 0,
                ],
                'pengembangan' => [
                    'count' => $tktPengembangan,
                    'pct' => $tktTotal > 0 ? round(($tktPengembangan / $tktTotal) * 100, 1) : 0,
                ],
            ];

            // 4. IKU-2 & IKU-5 Metrics
            // IKU-2: Mahasiswa berkegiatan di luar kampus (anggota riset mahasiswa IKU-2)
            $anggotaMhsQuery = PpmUsulanAnggota::where('peran_anggota', 'Mahasiswa IKU-2')
                ->whereHas('usulan', fn($q) => $q->whereIn('status', $approvedStatuses));
            if ($periodeId) {
                $anggotaMhsQuery->whereHas('usulan', fn($q) => $q->where('id_periode_hibah', $periodeId));
            }
            $realisasiIku2 = $anggotaMhsQuery->distinct('identifier')->count('identifier');
            if ($realisasiIku2 === 0) {
                // Also count any user role student or member in approved proposals
                $realisasiIku2 = PpmUsulanAnggota::whereHas('usulan', fn($q) => $q->whereIn('status', $approvedStatuses))->count();
            }
            $targetIku2 = 60; // SK Target Rektor
            $pctIku2 = round(($realisasiIku2 / max(1, $targetIku2)) * 100, 1);

            // IKU-5: Karya Dosen yang Mendapat Rekognisi Internasional / Diterapkan (Publikasi Scopus/SINTA & HKI)
            $pubScopus = PpmPublikasiJurnal::where('kategori_peringkat', 'like', 'Scopus%')->count();
            $pubSinta = PpmPublikasiJurnal::where('kategori_peringkat', 'like', 'SINTA%')->count();
            $hkiGranted = PpmHki::where('status_hki', 'Terverifikasi HKI')->count();
            $realisasiIku5 = $pubScopus + $pubSinta + $hkiGranted;
            $targetIku5 = 50; // SK Target Rektor
            $pctIku5 = round(($realisasiIku5 / max(1, $targetIku5)) * 100, 1);

            // 5. Rekapitulasi Real-Time 4 Fakultas
            $fakultasRecords = RefFakultas::orderBy('id_fakultas')->get();
            $fakultasBreakdown = [];

            foreach ($fakultasRecords as $fak) {
                $fakUserIds = User::where('id_fakultas', $fak->id_fakultas)->pluck('id');

                $fakUsulanQuery = PpmUsulan::whereIn('id_pengusul', $fakUserIds);
                if ($periodeId) {
                    $fakUsulanQuery->where('id_periode_hibah', $periodeId);
                }

                $fTotalUsulan = (clone $fakUsulanQuery)->count();
                $fUsulanLolos = (clone $fakUsulanQuery)->whereIn('status', $approvedStatuses)->count();

                // Pagu & Serapan
                $fPagu = (float) PpmKontrak::whereHas('usulan', function ($uq) use ($fakUserIds, $periodeId) {
                    $uq->whereIn('id_pengusul', $fakUserIds);
                    if ($periodeId) {
                        $uq->where('id_periode_hibah', $periodeId);
                    }
                })->sum('pagu_disetujui');

                if ($fPagu <= 0) {
                    $fPagu = (float) (clone $fakUsulanQuery)->whereIn('status', $approvedStatuses)->sum('total_rab');
                }

                $fSerapan = (float) PpmPencairanDana::where('status_pencairan', 'transferred')
                    ->whereHas('kontrak.usulan', function ($uq) use ($fakUserIds, $periodeId) {
                        $uq->whereIn('id_pengusul', $fakUserIds);
                        if ($periodeId) {
                            $uq->where('id_periode_hibah', $periodeId);
                        }
                    })->sum('jumlah_dana');

                $fSerapanPct = $fPagu > 0 ? round(($fSerapan / $fPagu) * 100, 1) : 0;

                // Publications & HKI from faculty users
                $fPublikasi = PpmPublikasiJurnal::whereIn('user_id', $fakUserIds)->count();
                $fHki = PpmHki::whereIn('user_id', $fakUserIds)->where('status_hki', 'Terverifikasi HKI')->count();

                $fakultasBreakdown[] = [
                    'id' => $fak->id_fakultas,
                    'kode' => $fak->kode_fakultas,
                    'nama' => $fak->nama_fakultas,
                    'dekan' => $fak->dekan_nama,
                    'total_usulan' => $fTotalUsulan,
                    'usulan_lolos' => $fUsulanLolos,
                    'total_pagu' => $fPagu,
                    'total_serapan' => $fSerapan,
                    'serapan_pct' => $fSerapanPct,
                    'total_publikasi' => $fPublikasi,
                    'total_hki' => $fHki,
                ];
            }

            // Chart Payload Formats
            $chartData = [
                'tkt' => [
                    'labels' => ['TKT 1-3 (Riset Dasar)', 'TKT 4-6 (Riset Terapan)', 'TKT 7-9 (Pengembangan)'],
                    'values' => [$tktDasar, $tktTerapan, $tktPengembangan],
                ],
                'fakultas' => [
                    'labels' => array_column($fakultasBreakdown, 'kode'),
                    'usulan' => array_column($fakultasBreakdown, 'total_usulan'),
                    'lolos' => array_column($fakultasBreakdown, 'usulan_lolos'),
                    'pagu_juta' => array_map(fn($v) => round($v / 1000000, 2), array_column($fakultasBreakdown, 'total_pagu')),
                    'serapan_juta' => array_map(fn($v) => round($v / 1000000, 2), array_column($fakultasBreakdown, 'total_serapan')),
                ],
                'iku' => [
                    'labels' => ['IKU-2 (Mahasiswa Riset)', 'IKU-5 (Karya & HKI)'],
                    'target' => [$targetIku2, $targetIku5],
                    'realisasi' => [$realisasiIku2, $realisasiIku5],
                    'pct' => [$pctIku2, $pctIku5],
                ],
            ];

            return [
                'total_usulan' => $totalUsulan,
                'usulan_approved' => $usulanApproved,
                'usulan_ongoing' => $usulanOngoing,
                'usulan_completed' => $usulanCompleted,
                'usulan_rejected' => $usulanRejected,
                'acceptance_rate' => $acceptanceRate,
                'total_pagu' => $totalPagu,
                'total_realisasi' => $totalRealisasi,
                'serapan_percentage' => $serapanPercentage,
                'tkt_distribution' => $tktDistribution,
                'iku2' => [
                    'target' => $targetIku2,
                    'realisasi' => $realisasiIku2,
                    'pct' => $pctIku2,
                ],
                'iku5' => [
                    'target' => $targetIku5,
                    'realisasi' => $realisasiIku5,
                    'pct' => $pctIku5,
                    'scopus' => $pubScopus,
                    'sinta' => $pubSinta,
                    'hki' => $hkiGranted,
                ],
                'fakultas_breakdown' => $fakultasBreakdown,
                'chart_data' => $chartData,
                'cached_at' => now()->toDateTimeString(),
            ];
        });
    }

    /**
     * Get Faculty-Level Performance with Tenant Isolation (US-12.2).
     */
    public function getFacultyMetrics(int $fakultasId, ?int $periodeId = null): array
    {
        $fakultas = RefFakultas::with('programStudi')->findOrFail($fakultasId);
        $prodis = $fakultas->programStudi;

        $approvedStatuses = ['Approved', 'Ongoing', 'Completed'];
        $prodiMetrics = [];

        $totalFakUsulan = 0;
        $totalFakLolos = 0;
        $totalFakPagu = 0.0;
        $totalFakSerapan = 0.0;
        $totalFakPublikasi = 0;
        $totalFakHki = 0;

        foreach ($prodis as $prodi) {
            $prodiUserIds = User::where('id_prodi', $prodi->id_prodi)->pluck('id');
            $dosenCount = $prodiUserIds->count();

            $pQuery = PpmUsulan::whereIn('id_pengusul', $prodiUserIds);
            if ($periodeId) {
                $pQuery->where('id_periode_hibah', $periodeId);
            }

            $pUsulanCount = (clone $pQuery)->count();
            $pLolosCount = (clone $pQuery)->whereIn('status', $approvedStatuses)->count();

            // Pagu & Serapan
            $pPagu = (float) PpmKontrak::whereHas('usulan', function ($uq) use ($prodiUserIds, $periodeId) {
                $uq->whereIn('id_pengusul', $prodiUserIds);
                if ($periodeId) {
                    $uq->where('id_periode_hibah', $periodeId);
                }
            })->sum('pagu_disetujui');

            if ($pPagu <= 0) {
                $pPagu = (float) (clone $pQuery)->whereIn('status', $approvedStatuses)->sum('total_rab');
            }

            $pSerapan = (float) PpmPencairanDana::where('status_pencairan', 'transferred')
                ->whereHas('kontrak.usulan', function ($uq) use ($prodiUserIds, $periodeId) {
                    $uq->whereIn('id_pengusul', $prodiUserIds);
                    if ($periodeId) {
                        $uq->where('id_periode_hibah', $periodeId);
                    }
                })->sum('jumlah_dana');

            $pSerapanPct = $pPagu > 0 ? round(($pSerapan / $pPagu) * 100, 1) : 0;

            // Target vs Realisasi Luaran
            $realisasiPub = PpmPublikasiJurnal::whereIn('user_id', $prodiUserIds)->count();
            $targetPub = max(3, $dosenCount * 2);

            $realisasiHki = PpmHki::whereIn('user_id', $prodiUserIds)->where('status_hki', 'Terverifikasi HKI')->count();
            $targetHki = max(1, $dosenCount * 1);

            $totalFakUsulan += $pUsulanCount;
            $totalFakLolos += $pLolosCount;
            $totalFakPagu += $pPagu;
            $totalFakSerapan += $pSerapan;
            $totalFakPublikasi += $realisasiPub;
            $totalFakHki += $realisasiHki;

            $prodiMetrics[] = [
                'id' => $prodi->id_prodi,
                'kode' => $prodi->kode_prodi,
                'nama' => $prodi->nama_prodi,
                'jenjang' => $prodi->jenjang,
                'kaprodi' => $prodi->kaprodi_nama,
                'dosen_count' => $dosenCount,
                'usulan_count' => $pUsulanCount,
                'lolos_count' => $pLolosCount,
                'pagu' => $pPagu,
                'serapan' => $pSerapan,
                'serapan_pct' => $pSerapanPct,
                'target_publikasi' => $targetPub,
                'realisasi_publikasi' => $realisasiPub,
                'target_hki' => $targetHki,
                'realisasi_hki' => $realisasiHki,
            ];
        }

        // Identify highest and lowest budget absorption
        $sortedBySerapan = collect($prodiMetrics)->filter(fn($p) => $p['pagu'] > 0)->sortByDesc('serapan_pct')->values();
        $highestProdi = $sortedBySerapan->first() ?? (collect($prodiMetrics)->sortByDesc('serapan')->first());
        $lowestProdi = $sortedBySerapan->last() ?? (collect($prodiMetrics)->sortBy('serapan')->first());

        $fakSerapanPct = $totalFakPagu > 0 ? round(($totalFakSerapan / $totalFakPagu) * 100, 1) : 0;

        return [
            'fakultas' => $fakultas,
            'prodi_metrics' => $prodiMetrics,
            'highest_prodi' => $highestProdi,
            'lowest_prodi' => $lowestProdi,
            'summary' => [
                'total_usulan' => $totalFakUsulan,
                'total_lolos' => $totalFakLolos,
                'total_pagu' => $totalFakPagu,
                'total_serapan' => $totalFakSerapan,
                'serapan_pct' => $fakSerapanPct,
                'total_publikasi' => $totalFakPublikasi,
                'total_hki' => $totalFakHki,
            ],
            'chart_data' => [
                'labels' => array_column($prodiMetrics, 'nama'),
                'target_pub' => array_column($prodiMetrics, 'target_publikasi'),
                'realisasi_pub' => array_column($prodiMetrics, 'realisasi_publikasi'),
                'target_hki' => array_column($prodiMetrics, 'target_hki'),
                'realisasi_hki' => array_column($prodiMetrics, 'realisasi_hki'),
                'serapan_pct' => array_column($prodiMetrics, 'serapan_pct'),
            ],
        ];
    }
}

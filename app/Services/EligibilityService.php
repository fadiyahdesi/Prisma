<?php

namespace App\Services;

use App\Models\User;
use App\Models\PpmSkemaBima;

class EligibilityService
{
    /**
     * Fallback thresholds per scheme according to BIMA Standards and US-03.4
     */
    const SCHEMES = [
        'pdp' => [
            'name' => 'Penelitian Dosen Pemula (PDP)',
            'min_sinta_3yr' => 50.0,
            'allowed_jafung' => ['Dosen', 'Tenaga Pengajar', 'Asisten Ahli', 'Lektor'],
            'disallowed_jafung' => ['Lektor Kepala', 'Guru Besar', 'Profesor'],
            'max_pagu' => 25000000,
        ],
        'fundamental' => [
            'name' => 'Penelitian Fundamental',
            'min_sinta_3yr' => 150.0,
            'allowed_jafung' => ['Lektor', 'Lektor Kepala', 'Guru Besar', 'Profesor'],
            'disallowed_jafung' => ['Tenaga Pengajar'],
            'max_pagu' => 150000000,
        ],
        'hilirisasi' => [
            'name' => 'Penelitian Terapan & Hilirisasi',
            'min_sinta_3yr' => 250.0,
            'allowed_jafung' => ['Lektor Kepala', 'Guru Besar', 'Profesor'],
            'disallowed_jafung' => ['Tenaga Pengajar', 'Asisten Ahli'],
            'max_pagu' => 250000000,
        ],
        'pkm' => [
            'name' => 'Pengabdian Kepada Masyarakat (PKM)',
            'min_sinta_3yr' => 30.0,
            'allowed_jafung' => ['Asisten Ahli', 'Lektor', 'Lektor Kepala', 'Guru Besar', 'Profesor'],
            'disallowed_jafung' => [],
            'max_pagu' => 50000000,
        ],
    ];

    /**
     * Check if a lecturer user is eligible for a specific scheme code.
     */
    public function checkEligibility(User $user, string $schemeKey = 'pdp'): array
    {
        $schemeKeyLower = strtolower(trim($schemeKey));
        $dbScheme = PpmSkemaBima::whereRaw('LOWER(kode_skema) = ?', [$schemeKeyLower])->first();

        $kategori = 'penelitian';
        $minSintaOverall = 0.0;
        
        if ($dbScheme) {
            $schemeName = $dbScheme->nama_skema;
            $minSinta3yr = (float) $dbScheme->min_sinta_3yr;
            $minSintaOverall = (float) $dbScheme->min_sinta_overall;
            $kategori = $dbScheme->kategori;
            $allowedJafung = is_array($dbScheme->min_jafung) ? $dbScheme->min_jafung : (json_decode($dbScheme->min_jafung, true) ?? []);
            $disallowedJafung = [];
            $maxPagu = (float) $dbScheme->plafon_dana;
            $isActive = (bool) $dbScheme->is_active;
        } elseif (isset(self::SCHEMES[$schemeKeyLower])) {
            $fallback = self::SCHEMES[$schemeKeyLower];
            $schemeName = $fallback['name'];
            $minSinta3yr = (float) $fallback['min_sinta_3yr'];
            $allowedJafung = $fallback['allowed_jafung'];
            $disallowedJafung = $fallback['disallowed_jafung'];
            $maxPagu = (float) $fallback['max_pagu'];
            $isActive = true;
        } else {
            return [
                'is_eligible' => false,
                'status_code' => 'SCHEME_NOT_FOUND',
                'reason' => 'Skema penelitian tidak ditemukan.',
                'scheme_name' => 'Unknown',
            ];
        }

        $jafung = $user->jabatan_fungsional ?? 'Asisten Ahli';
        $sinta3yr = (float) ($user->sinta_score_3yr ?? 0);
        $sintaOverall = (float) ($user->sinta_score_overall ?? 0);

        // Check 1: Inactive Scheme
        if (!$isActive) {
            return [
                'is_eligible' => false,
                'status_code' => 'SCHEME_INACTIVE',
                'reason' => "Skema {$schemeName} saat ini sedang non-aktif oleh Admin P3M.",
                'scheme_name' => $schemeName,
                'current_sinta_3yr' => $sinta3yr,
                'required_sinta_3yr' => $minSinta3yr,
                'jafung' => $jafung,
                'max_pagu' => $maxPagu,
            ];
        }

        // Check 2: Manual Fallback Pending Verification
        if ($user->is_sinta_manual_fallback && $user->sinta_verification_status === 'pending_operator') {
            return [
                'is_eligible' => false,
                'status_code' => 'PENDING_OPERATOR_VERIFICATION',
                'reason' => 'Bukti pengisian mandiri profil SINTA Anda sedang dalam antrean verifikasi Operator P3M. Usulan baru dapat dibuat setelah disahkan.',
                'scheme_name' => $schemeName,
                'current_sinta_3yr' => $sinta3yr,
                'required_sinta_3yr' => $minSinta3yr,
                'jafung' => $jafung,
                'max_pagu' => $maxPagu,
            ];
        }

        // Check 3: Disallowed or Unallowed Jafung
        $jafungAllowed = true;
        if (!empty($disallowedJafung) && in_array($jafung, $disallowedJafung)) {
            $jafungAllowed = false;
        } elseif (!empty($allowedJafung)) {
            $matched = false;
            foreach ($allowedJafung as $allowed) {
                if (strcasecmp(trim($allowed), trim($jafung)) === 0 
                    || str_contains(strtolower($allowed), strtolower($jafung)) 
                    || str_contains(strtolower($jafung), strtolower($allowed))) {
                    $matched = true;
                    break;
                }
            }
            if (!$matched) {
                $jafungAllowed = false;
            }
        }

        if (!$jafungAllowed) {
            return [
                'is_eligible' => false,
                'status_code' => 'JAFUNG_EXCEEDED_OR_INVALID',
                'reason' => "Jabatan fungsional Anda ({$jafung}) tidak memenuhi kualifikasi skema {$schemeName}.",
                'scheme_name' => $schemeName,
                'current_sinta_3yr' => $sinta3yr,
                'required_sinta_3yr' => $minSinta3yr,
                'jafung' => $jafung,
                'max_pagu' => $maxPagu,
            ];
        }

        // Check 4: SINTA Score Threshold (3Yr or Overall)
        if ($kategori === 'pengabdian') {
            if ($sintaOverall < $minSintaOverall) {
                $shortage = round($minSintaOverall - $sintaOverall, 2);
                $shortageFormatted = (floor($shortage) == $shortage) ? (int)$shortage : $shortage;
                $minSintaFormatted = (floor($minSintaOverall) == $minSintaOverall) ? (int)$minSintaOverall : $minSintaOverall;

                return [
                    'is_eligible' => false,
                    'status_code' => 'SINTA_SCORE_BELOW_THRESHOLD',
                    'reason' => "Skor SINTA Overall Anda ({$sintaOverall}) kurang {$shortageFormatted} poin dari batas minimal skema {$schemeName} ({$minSintaFormatted}).",
                    'scheme_name' => $schemeName,
                    'current_sinta_3yr' => $sinta3yr,
                    'required_sinta_3yr' => $minSinta3yr,
                    'jafung' => $jafung,
                    'max_pagu' => $maxPagu,
                ];
            }
        } else {
            if ($sinta3yr < $minSinta3yr) {
                $shortage = round($minSinta3yr - $sinta3yr, 2);
                $shortageFormatted = (floor($shortage) == $shortage) ? (int)$shortage : $shortage;
                $minSintaFormatted = (floor($minSinta3yr) == $minSinta3yr) ? (int)$minSinta3yr : $minSinta3yr;

                return [
                    'is_eligible' => false,
                    'status_code' => 'SINTA_SCORE_BELOW_THRESHOLD',
                    'reason' => "Skor SINTA 3-Tahun Anda ({$sinta3yr}) kurang {$shortageFormatted} poin dari batas minimal skema {$schemeName} ({$minSintaFormatted}).",
                    'scheme_name' => $schemeName,
                    'current_sinta_3yr' => $sinta3yr,
                    'required_sinta_3yr' => $minSinta3yr,
                    'jafung' => $jafung,
                    'max_pagu' => $maxPagu,
                ];
            }
        }

        return [
            'is_eligible' => true,
            'status_code' => 'ELIGIBLE',
            'reason' => "Selamat! Anda memenuhi seluruh kualifikasi Jabatan Fungsional ({$jafung}) dan Skor SINTA 3Yr ({$sinta3yr}) untuk skema {$schemeName}.",
            'scheme_name' => $schemeName,
            'current_sinta_3yr' => $sinta3yr,
            'required_sinta_3yr' => $minSinta3yr,
            'jafung' => $jafung,
            'max_pagu' => $maxPagu,
        ];
    }

    /**
     * Get eligibility summary for all available schemes for a lecturer.
     */
    public function getSummaryForUser(User $user): array
    {
        $summary = [];
        $dbSchemes = PpmSkemaBima::where('is_active', true)->get();

        if ($dbSchemes->count() > 0) {
            foreach ($dbSchemes as $dbScheme) {
                $key = strtolower($dbScheme->kode_skema);
                $summary[$key] = $this->checkEligibility($user, $dbScheme->kode_skema);
            }
        } else {
            foreach (array_keys(self::SCHEMES) as $key) {
                $summary[$key] = $this->checkEligibility($user, $key);
            }
        }

        return $summary;
    }
}

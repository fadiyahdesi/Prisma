<?php

namespace App\Services;

use App\Models\PpmPeriodeHibah;
use Carbon\Carbon;

class BimaPeriodService
{
    /**
     * Get the currently active & open Call for Proposals period.
     */
    public function getActiveOpenPeriod(): ?PpmPeriodeHibah
    {
        return PpmPeriodeHibah::openNow()->orderBy('waktu_tutup', 'asc')->first();
    }

    /**
     * Get the latest active period (whether open or closing soon).
     */
    public function getLatestActivePeriod(): ?PpmPeriodeHibah
    {
        return PpmPeriodeHibah::where('is_active', true)
            ->orderBy('waktu_tutup', 'desc')
            ->first();
    }

    /**
     * Check if proposal submission form is currently open.
     */
    public function isSubmissionOpen(): bool
    {
        return $this->getActiveOpenPeriod() !== null;
    }

    /**
     * Get transparent countdown timer details for dashboard display.
     */
    public function getCountdownDetails(): array
    {
        $period = $this->getLatestActivePeriod();
        $now = now();

        if (!$period) {
            return [
                'has_active_period' => false,
                'is_open' => false,
                'status_label' => 'Tidak Ada Periode Aktif',
                'countdown' => [
                    'days' => 0,
                    'hours' => 0,
                    'minutes' => 0,
                    'seconds' => 0,
                ],
                'waktu_tutup_formatted' => '-',
                'tahun_akademik' => '-',
            ];
        }

        $isOpen = $period->is_active && $now->greaterThanOrEqualTo($period->waktu_buka) && $now->lessThanOrEqualTo($period->waktu_tutup);
        $isUpcoming = $now->lessThan($period->waktu_buka);
        $isExpired = $now->greaterThan($period->waktu_tutup);

        $targetDate = $isUpcoming ? $period->waktu_buka : $period->waktu_tutup;
        $diff = $now->diff($targetDate);

        $statusLabel = 'PERIODE DIBUKA';
        if ($isUpcoming) {
            $statusLabel = 'AKAN DIBUKA';
        } elseif ($isExpired) {
            $statusLabel = 'TERKUNCI / DITUTUP';
        }

        return [
            'has_active_period' => true,
            'period' => $period,
            'is_open' => $isOpen,
            'is_upcoming' => $isUpcoming,
            'is_expired' => $isExpired,
            'status_label' => $statusLabel,
            'countdown' => [
                'days' => $isExpired ? 0 : $diff->d + ($diff->m * 30),
                'hours' => $isExpired ? 0 : $diff->h,
                'minutes' => $isExpired ? 0 : $diff->i,
                'seconds' => $isExpired ? 0 : $diff->s,
            ],
            'target_timestamp' => $targetDate->getTimestamp() * 1000,
            'waktu_buka_formatted' => $period->waktu_buka->format('d M Y H:i T'),
            'waktu_tutup_formatted' => $period->waktu_tutup->format('d M Y H:i T'),
            'tahun_akademik' => $period->tahun_akademik . ' (' . $period->semester . ')',
        ];
    }
}


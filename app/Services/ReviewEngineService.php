<?php

namespace App\Services;

use App\Models\PpmUsulan;
use App\Models\PpmPenugasanReviewer;
use App\Models\PpmPenilaianReviewer;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ReviewEngineService
{
    /**
     * Get all User IDs that have Conflict of Interest with the proposal (US-08.1).
     * Automatically filters out anyone from the same Faculty or Program Studi as the proposal team.
     */
    public static function getConflictOfInterestUserIds(PpmUsulan $usulan): array
    {
        $usulan->loadMissing(['pengusul.fakultas', 'pengusul.prodi', 'anggota.user']);

        $blockedUserIds = collect([$usulan->id_pengusul]);
        $blockedFacultyIds = collect();
        $blockedProdiIds = collect();

        if ($usulan->pengusul) {
            if ($usulan->pengusul->id_fakultas) {
                $blockedFacultyIds->push($usulan->pengusul->id_fakultas);
            }
            if ($usulan->pengusul->id_prodi) {
                $blockedProdiIds->push($usulan->pengusul->id_prodi);
            }
        }

        foreach ($usulan->anggota as $anggota) {
            if ($anggota->user_id) {
                $blockedUserIds->push($anggota->user_id);
            }
            if ($anggota->user) {
                if ($anggota->user->id_fakultas) {
                    $blockedFacultyIds->push($anggota->user->id_fakultas);
                }
                if ($anggota->user->id_prodi) {
                    $blockedProdiIds->push($anggota->user->id_prodi);
                }
            }
        }

        // Fetch all users matching blocked faculty or prodi
        $coiUsersQuery = User::query()
            ->where(function ($q) use ($blockedUserIds, $blockedFacultyIds, $blockedProdiIds) {
                $q->whereIn('id', $blockedUserIds->unique());
                if ($blockedFacultyIds->isNotEmpty()) {
                    $q->orWhereIn('id_fakultas', $blockedFacultyIds->unique());
                }
                if ($blockedProdiIds->isNotEmpty()) {
                    $q->orWhereIn('id_prodi', $blockedProdiIds->unique());
                }
            });

        return $coiUsersQuery->pluck('id')->all();
    }

    /**
     * Get all eligible reviewers for a proposal (US-08.1).
     */
    public static function getEligibleReviewers(PpmUsulan $usulan): Collection
    {
        $coiUserIds = self::getConflictOfInterestUserIds($usulan);
        $alreadyAssignedIds = $usulan->penugasanReviewer()->pluck('id_reviewer')->all();

        return User::whereHas('roles', function ($q) {
                $q->where('name', 'Reviewer');
            })
            ->whereNotIn('id', array_merge($coiUserIds, $alreadyAssignedIds))
            ->with(['fakultas', 'prodi'])
            ->get();
    }

    /**
     * Assign Reviewers to a Proposal with Conflict of Interest enforcement (US-08.1).
     * Supports 1 or 2 Reviewers dynamically.
     */
    public static function assignReviewers(PpmUsulan $usulan, int $reviewer1Id, ?int $reviewer2Id, int $adminId): array
    {
        if ($reviewer2Id && $reviewer1Id === $reviewer2Id) {
            throw new InvalidArgumentException('Reviewer 1 dan Reviewer 2 tidak boleh orang yang sama.');
        }

        $coiUserIds = self::getConflictOfInterestUserIds($usulan);

        if (in_array($reviewer1Id, $coiUserIds, true)) {
            throw new InvalidArgumentException('Reviewer 1 melanggar aturan Conflict of Interest (berasal dari fakultas/prodi yang sama dengan pengusul).');
        }

        if ($reviewer2Id && in_array($reviewer2Id, $coiUserIds, true)) {
            throw new InvalidArgumentException('Reviewer 2 melanggar aturan Conflict of Interest (berasal dari fakultas/prodi yang sama dengan pengusul).');
        }

        return DB::transaction(function () use ($usulan, $reviewer1Id, $reviewer2Id, $adminId) {
            // Delete previous assignments if any in draft
            $usulan->penugasanReviewer()->delete();

            $assignments = [];

            $assignments[] = PpmPenugasanReviewer::create([
                'id_usulan' => $usulan->id,
                'id_reviewer' => $reviewer1Id,
                'peran_reviewer' => 'reviewer_1',
                'status_penugasan' => 'assigned',
                'assigned_by' => $adminId,
                'assigned_at' => now(),
            ]);

            if ($reviewer2Id) {
                $assignments[] = PpmPenugasanReviewer::create([
                    'id_usulan' => $usulan->id,
                    'id_reviewer' => $reviewer2Id,
                    'peran_reviewer' => 'reviewer_2',
                    'status_penugasan' => 'assigned',
                    'assigned_by' => $adminId,
                    'assigned_at' => now(),
                ]);
            }

            $usulan->update([
                'status' => 'In_review',
                'skor_reviewer_1' => null,
                'skor_reviewer_2' => null,
                'skor_reviewer_3' => null,
                'skor_akhir' => null,
                'is_disparity' => false,
            ]);

            AuditLogService::log('REVIEWERS_ASSIGNED', null, [
                'usulan_id' => $usulan->id,
                'reviewer_1' => $reviewer1Id,
                'reviewer_2' => $reviewer2Id,
                'assigned_by' => $adminId,
            ], $adminId);

            return $assignments;
        });
    }

    /**
     * Assign Reviewer 3 (Adjudicator / Penengah) for Adjudication (US-08.3).
     */
    public static function assignAdjudicator(PpmUsulan $usulan, int $adjudicatorId, int $adminId): PpmPenugasanReviewer
    {
        $coiUserIds = self::getConflictOfInterestUserIds($usulan);
        $existingReviewerIds = $usulan->penugasanReviewer()->pluck('id_reviewer')->all();

        if (in_array($adjudicatorId, $coiUserIds, true)) {
            throw new InvalidArgumentException('Reviewer 3 melanggar aturan Conflict of Interest (berasal dari fakultas/prodi yang sama dengan pengusul).');
        }

        if (in_array($adjudicatorId, $existingReviewerIds, true)) {
            throw new InvalidArgumentException('Reviewer 3 harus reviewer independen yang berbeda dari Reviewer 1 dan Reviewer 2.');
        }

        return DB::transaction(function () use ($usulan, $adjudicatorId, $adminId) {
            $p3 = PpmPenugasanReviewer::create([
                'id_usulan' => $usulan->id,
                'id_reviewer' => $adjudicatorId,
                'peran_reviewer' => 'adjudicator',
                'status_penugasan' => 'assigned',
                'assigned_by' => $adminId,
                'assigned_at' => now(),
            ]);

            AuditLogService::log('ADJUDICATOR_ASSIGNED', null, [
                'usulan_id' => $usulan->id,
                'adjudicator_id' => $adjudicatorId,
                'assigned_by' => $adminId,
            ], $adminId);

            return $p3;
        });
    }

    /**
     * Check if disparity between 2 reviewer scores is extreme (US-08.3).
     * Trigger condition: difference >= 25% OR difference >= 150 points.
     */
    public static function calculateDisparity(float $score1, float $score2): array
    {
        $diff = abs($score1 - $score2);
        $maxScore = max($score1, $score2);
        $percent = $maxScore > 0 ? ($diff / $maxScore) * 100 : 0;
        $isDisparity = ($diff >= 150.0) || ($percent >= 25.0);

        return [
            'difference' => round($diff, 2),
            'percent' => round($percent, 2),
            'is_disparity' => $isDisparity,
        ];
    }

    /**
     * Calculate nearest-two average from 3 reviewer scores (US-08.3).
     */
    public static function resolveAdjudicationScore(float $s1, float $s2, float $s3): float
    {
        $d12 = abs($s1 - $s2);
        $d13 = abs($s1 - $s3);
        $d23 = abs($s2 - $s3);

        $minDist = min($d12, $d13, $d23);

        if ($minDist === $d12) {
            return round(($s1 + $s2) / 2, 2);
        } elseif ($minDist === $d13) {
            return round(($s1 + $s3) / 2, 2);
        } else {
            return round(($s2 + $s3) / 2, 2);
        }
    }

    /**
     * Calculate total weighted score from rubric and user inputs (US-08.2).
     * Rubric weights sum to 100%. Scores are 1-7. Max total score = 700.
     */
    public static function calculateRubricScore(array $rubrikList, array $inputScores): float
    {
        $total = 0.0;

        foreach ($rubrikList as $item) {
            $id = $item['id'];
            $bobot = (float) ($item['bobot'] ?? 0);
            $nilai = isset($inputScores[$id]) ? (float) $inputScores[$id] : 1.0;

            // Constrain score between 1 and 7
            $nilai = max(1.0, min(7.0, $nilai));

            $total += ($nilai * $bobot);
        }

        return round($total, 2);
    }

    /**
     * Submit a substantive review and lock the assessment (US-08.2 & US-08.3).
     */
    public static function submitReview(
        PpmPenugasanReviewer $penugasan,
        array $inputScores,
        string $komentar,
        string $rekomendasi
    ): PpmPenilaianReviewer {
        $usulan = $penugasan->usulan()->with('skema')->first();
        $rubrikList = $usulan->skema->rubrik_penilaian ?? [];

        $totalScore = self::calculateRubricScore($rubrikList, $inputScores);

        return DB::transaction(function () use ($penugasan, $usulan, $rubrikList, $inputScores, $totalScore, $komentar, $rekomendasi) {
            // 1. Create or update locked penilaian
            $penilaian = PpmPenilaianReviewer::updateOrCreate(
                ['id_penugasan' => $penugasan->id],
                [
                    'skor_kriteria' => $inputScores,
                    'total_skor' => $totalScore,
                    'komentar_kualitatif' => trim($komentar),
                    'rekomendasi' => $rekomendasi,
                    'is_locked' => true,
                    'submitted_at' => now(),
                ]
            );

            // 2. Mark assignment completed
            $penugasan->update([
                'status_penugasan' => 'completed',
                'completed_at' => now(),
            ]);

            // 3. Update usulan reviewer score column
            if ($penugasan->peran_reviewer === 'reviewer_1') {
                $usulan->skor_reviewer_1 = $totalScore;
            } elseif ($penugasan->peran_reviewer === 'reviewer_2') {
                $usulan->skor_reviewer_2 = $totalScore;
            } elseif ($penugasan->peran_reviewer === 'adjudicator') {
                $usulan->skor_reviewer_3 = $totalScore;
            }
            $usulan->save();

            // 4. Handle Disparity & Final Score Resolution
            $hasR2 = $usulan->penugasanReviewer()->where('peran_reviewer', 'reviewer_2')->exists();
            $r1Completed = $usulan->penugasanReviewer()->where('peran_reviewer', 'reviewer_1')->where('status_penugasan', 'completed')->exists();
            $r2Completed = $usulan->penugasanReviewer()->where('peran_reviewer', 'reviewer_2')->where('status_penugasan', 'completed')->exists();
            $r3Completed = $usulan->penugasanReviewer()->where('peran_reviewer', 'adjudicator')->where('status_penugasan', 'completed')->exists();

            if ($penugasan->peran_reviewer === 'adjudicator' && $r3Completed && $usulan->skor_reviewer_1 && $usulan->skor_reviewer_2 && $usulan->skor_reviewer_3) {
                // Adjudication finished: calculate nearest-two average
                $finalScore = self::resolveAdjudicationScore(
                    (float) $usulan->skor_reviewer_1,
                    (float) $usulan->skor_reviewer_2,
                    (float) $usulan->skor_reviewer_3
                );

                $usulan->update([
                    'skor_akhir' => $finalScore,
                    'status' => 'Reviewed',
                ]);

                AuditLogService::log('ADJUDICATION_COMPLETED', null, [
                    'usulan_id' => $usulan->id,
                    'skor_r1' => $usulan->skor_reviewer_1,
                    'skor_r2' => $usulan->skor_reviewer_2,
                    'skor_r3' => $usulan->skor_reviewer_3,
                    'skor_akhir' => $finalScore,
                ], $penugasan->id_reviewer);

            } elseif ($r1Completed && $r2Completed && !$r3Completed) {
                // Both primary reviewers completed: check disparity
                $disparityCheck = self::calculateDisparity(
                    (float) $usulan->skor_reviewer_1,
                    (float) $usulan->skor_reviewer_2
                );

                if ($disparityCheck['is_disparity']) {
                    $usulan->update([
                        'status' => 'Adjudication',
                        'is_disparity' => true,
                        'adjudication_notes' => "Disparitas nilai ekstrem terdeteksi: Selisih {$disparityCheck['difference']} poin ({$disparityCheck['percent']}%). Memerlukan Reviewer 3 (Penengah).",
                    ]);

                    AuditLogService::log('DISPARITY_DETECTED', null, [
                        'usulan_id' => $usulan->id,
                        'skor_r1' => $usulan->skor_reviewer_1,
                        'skor_r2' => $usulan->skor_reviewer_2,
                        'selisih' => $disparityCheck['difference'],
                        'percent' => $disparityCheck['percent'],
                    ], $penugasan->id_reviewer);
                } else {
                    $finalScore = round(((float) $usulan->skor_reviewer_1 + (float) $usulan->skor_reviewer_2) / 2, 2);

                    $usulan->update([
                        'skor_akhir' => $finalScore,
                        'is_disparity' => false,
                        'status' => 'Reviewed',
                    ]);

                    AuditLogService::log('REVIEW_COMPLETED', null, [
                        'usulan_id' => $usulan->id,
                        'skor_r1' => $usulan->skor_reviewer_1,
                        'skor_r2' => $usulan->skor_reviewer_2,
                        'skor_akhir' => $finalScore,
                    ], $penugasan->id_reviewer);
                }
            } elseif ($r1Completed && !$hasR2) {
                // Single reviewer assigned and completed
                $finalScore = round((float) $usulan->skor_reviewer_1, 2);

                $usulan->update([
                    'skor_akhir' => $finalScore,
                    'is_disparity' => false,
                    'status' => 'Reviewed',
                ]);

                AuditLogService::log('REVIEW_COMPLETED', null, [
                    'usulan_id' => $usulan->id,
                    'skor_r1' => $usulan->skor_reviewer_1,
                    'skor_akhir' => $finalScore,
                ], $penugasan->id_reviewer);
            }

            return $penilaian;
        });
    }
}


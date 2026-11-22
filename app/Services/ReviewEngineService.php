<?php

namespace App\Services;

use App\Models\PpmUsulan;
use App\Models\PpmPenugasanReviewer;
use App\Models\PpmPenilaianReviewer;
use App\Models\Role;
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
     * Evaluasi kecocokan rumpun/bidang calon reviewer terhadap usulan.
     * Dipakai baik untuk daftar kandidat maupun untuk memblokir penugasan lintas bidang.
     */
    public static function evaluateFieldMatch(PpmUsulan $usulan, User $candidate): array
    {
        $usulan->loadMissing(['prodi']);
        $candidate->loadMissing(['prodi']);

        $proposalRumpun = strtolower(trim(implode(' ', array_filter([
            $usulan->rumpun_ilmu_level_1,
            $usulan->rumpun_ilmu_level_2,
            $usulan->rumpun_ilmu_level_3,
            $usulan->nama_prodi,
            $usulan->prodi?->nama_prodi,
            $usulan->judul_usulan,
        ]))));

        $targetRumpunCategory = strtolower((string) ($usulan->rumpun_ilmu_level_1
            ?: RumpunIlmuService::resolveRumpunFromProdi($usulan->nama_prodi ?: $usulan->prodi?->nama_prodi)));

        $candidateProdi = (string) ($candidate->prodi?->nama_prodi ?? '');
        $candidateRumpun = (string) ($candidate->rumpun_ilmu ?: RumpunIlmuService::resolveRumpunFromProdi($candidateProdi));
        $candidateBidang = trim((string) ($candidate->bidang_keahlian ?: $candidateProdi));

        $result = [
            'matched'  => false,
            'reason'   => 'Lintas Bidang / Rumpun tidak sesuai dengan usulan',
            'rumpun'   => $candidateRumpun,
            'bidang'   => $candidateBidang ?: '-',
        ];

        if ($targetRumpunCategory !== '' && strtolower($candidateRumpun) === $targetRumpunCategory) {
            $result['matched'] = true;
            $result['reason'] = 'Sesuai Rumpun: ' . $candidateRumpun;

            return $result;
        }

        // Kata generik seperti "Ilmu" sering muncul di rumpun usulan maupun nama prodi
        // sehingga tidak boleh dianggap sebagai bukti kecocokan bidang.
        $genericTokens = [
            'ilmu', 'sains', 'studi', 'pendidikan', 'profesi', 'sarjana', 'strata',
            'universitas', 'kampus', 'fakultas', 'program', 'umum', 'dasar', 'lanjut',
        ];

        $keywords = array_filter(
            preg_split('/[\s,&\/\-]+/', strtolower($candidateBidang . ' ' . $candidateProdi)),
            fn ($w) => strlen($w) >= 4 && !in_array($w, $genericTokens, true)
        );

        foreach ($keywords as $kw) {
            if ($proposalRumpun !== '' && str_contains($proposalRumpun, $kw)) {
                $result['matched'] = true;
                $result['reason'] = 'Sesuai Bidang: ' . ($candidateBidang ?: $candidateProdi);
                break;
            }
        }

        return $result;
    }

    /**
     * Get all eligible reviewers for a proposal, prioritized by field match & non-CoI (US-08.1).
     */
    public static function getEligibleReviewers(PpmUsulan $usulan): \Illuminate\Support\Collection
    {
        $usulan->loadMissing(['skema', 'prodi', 'pengusul.prodi', 'pengusul.fakultas']);
        $coiUserIds = self::getConflictOfInterestUserIds($usulan);
        $alreadyAssignedIds = $usulan->penugasanReviewer()->pluck('id_reviewer')->all();

        $excludedIds = array_unique(array_merge($coiUserIds, $alreadyAssignedIds));

        $candidates = User::with(['fakultas', 'prodi', 'roles'])
            ->where(function ($q) {
                $q->whereHas('roles', fn ($rq) => $rq->where('name', 'Reviewer'))
                  ->orWhere('reviewer_application_status', 'approved')
                  ->orWhere(function ($lq) {
                      $lq->whereNotNull('no_urut')
                         ->where(function ($jq) {
                             $jq->where('jabatan_fungsional', 'like', '%Lektor%')
                                ->orWhere('jabatan_fungsional', 'like', '%Guru Besar%')
                                ->orWhere('jabatan_fungsional', 'like', '%Asisten Ahli%');
                         });
                  });
            })
            ->whereNotIn('id', $excludedIds)
            ->get();

        // Fallback aman jika semua dosen luar fakultas terfilter (tetap blokir pengusul & anggota & satu prodi)
        if ($candidates->isEmpty()) {
            $teamIds = collect([$usulan->id_pengusul])
                ->merge($usulan->anggota()->pluck('user_id'))
                ->filter()
                ->all();
            $prodiId = $usulan->id_prodi ?: $usulan->pengusul?->id_prodi;

            $candidates = User::with(['fakultas', 'prodi', 'roles'])
                ->whereNotNull('no_urut')
                ->whereNotIn('id', array_merge($teamIds, $alreadyAssignedIds))
                ->when($prodiId, fn ($q) => $q->where('id_prodi', '!=', $prodiId))
                ->get();
        }

        return $candidates->map(function (User $candidate) use ($usulan) {
            $match = self::evaluateFieldMatch($usulan, $candidate);

            $candidate->setAttribute('is_reviewer_role', $candidate->roles->contains('name', 'Reviewer'));
            $candidate->setAttribute('sesuai_bidang', $match['matched']);
            $candidate->setAttribute('match_reason', $match['reason']);
            $candidate->setAttribute('resolved_rumpun', $match['rumpun']);
            $candidate->setAttribute('resolved_bidang', $match['bidang']);

            return $candidate;
        })->sortByDesc(function (User $u) {
            return [
                $u->getAttribute('sesuai_bidang') ? 1 : 0,
                $u->getAttribute('is_reviewer_role') ? 1 : 0,
                $u->reviewer_application_status === 'approved' ? 1 : 0,
                (float) ($u->sinta_score_3yr ?? 0),
            ];
        })->values();
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

        // Penetapan reviewer WAJIB sesuai rumpun/bidang usulan (selain bebas CoI).
        foreach (array_filter([$reviewer1Id, $reviewer2Id]) as $reviewerId) {
            $reviewer = User::find($reviewerId);
            if (!$reviewer) {
                throw new InvalidArgumentException('Reviewer yang dipilih tidak ditemukan.');
            }

            $match = self::evaluateFieldMatch($usulan, $reviewer);
            if (!$match['matched']) {
                $label = $reviewerId === $reviewer1Id ? 'Reviewer 1' : 'Reviewer 2';
                throw new InvalidArgumentException(
                    "{$label} ({$reviewer->name}) tidak sesuai bidang usulan. {$match['reason']}. Pilih reviewer yang sesuai rumpun/bidang proposal."
                );
            }
        }

        return DB::transaction(function () use ($usulan, $reviewer1Id, $reviewer2Id, $adminId) {
            // Pastikan kedua penilai memiliki role Reviewer aktif agar langsung muncul pada portal Reviewer
            $reviewerRole = Role::where('name', 'Reviewer')->first();
            if ($reviewerRole) {
                foreach (array_filter([$reviewer1Id, $reviewer2Id]) as $rid) {
                    $revUser = User::find($rid);
                    if ($revUser) {
                        $revUser->roles()->syncWithoutDetaching([$reviewerRole->id]);
                        if ($revUser->reviewer_application_status !== 'approved') {
                            $revUser->update([
                                'reviewer_application_status' => 'approved',
                                'reviewer_applied_at' => $revUser->reviewer_applied_at ?? now(),
                            ]);
                        }
                    }
                }
            }

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

        $adjudicator = User::find($adjudicatorId);
        if (!$adjudicator) {
            throw new InvalidArgumentException('Reviewer 3 yang dipilih tidak ditemukan.');
        }

        $match = self::evaluateFieldMatch($usulan, $adjudicator);
        if (!$match['matched']) {
            throw new InvalidArgumentException(
                "Reviewer 3 ({$adjudicator->name}) tidak sesuai bidang usulan. {$match['reason']}. Pilih penengah yang sesuai rumpun/bidang proposal."
            );
        }

        return DB::transaction(function () use ($usulan, $adjudicatorId, $adminId) {
            $reviewerRole = Role::where('name', 'Reviewer')->first();
            if ($reviewerRole) {
                $adjUser = User::find($adjudicatorId);
                if ($adjUser) {
                    $adjUser->roles()->syncWithoutDetaching([$reviewerRole->id]);
                    if ($adjUser->reviewer_application_status !== 'approved') {
                        $adjUser->update([
                            'reviewer_application_status' => 'approved',
                            'reviewer_applied_at' => $adjUser->reviewer_applied_at ?? now(),
                        ]);
                    }
                }
            }

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


<?php

namespace Tests\Feature;

use App\Models\PpmPenugasanReviewer;
use App\Models\PpmPeriodeHibah;
use App\Models\PpmSkemaBima;
use App\Models\PpmUsulan;
use App\Models\User;
use App\Services\ReviewEngineService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Epic08DoubleBlindReviewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    private function createSampleProposal(): PpmUsulan
    {
        $dosen = User::where('email', 'dosen@harkatnegeri.ac.id')->first();
        $skema = PpmSkemaBima::first();
        $periode = PpmPeriodeHibah::first();

        // Ensure predictable rubric on schema for testing
        $skema->update([
            'rubrik_penilaian' => [
                ['id' => 'k1', 'kriteria' => 'Kualitas Rekam Jejak dan Urgensi Riset', 'bobot' => 30, 'keterangan' => 'Urgensi masalah'],
                ['id' => 'k2', 'kriteria' => 'Metodologi dan Kelayakan Teknis', 'bobot' => 40, 'keterangan' => 'Rancangan metodologi'],
                ['id' => 'k3', 'kriteria' => 'Target Luaran dan Dampak IKU', 'bobot' => 30, 'keterangan' => 'Target publikasi/paten'],
            ]
        ]);

        return PpmUsulan::create([
            'id_pengusul' => $dosen->id,
            'id_skema_bima' => $skema->id,
            'id_periode_hibah' => $periode->id,
            'kode_usulan' => 'BIMA-2026-TEST-' . rand(100, 999),
            'judul_usulan' => 'Pengembangan Sistem Sensor Cerdas Berbasis AI untuk Pertanian Presisi',
            'rumpun_ilmu_level_1' => 'Teknik',
            'fokus_rirn' => 'Pangan dan Pertanian',
            'target_tkt' => 3,
            'ringkasan_substansi' => 'Proposal penelitian sistem sensor cerdas untuk pemantauan tanaman.',
            'total_rab' => 50000000.0,
            'status' => 'In_review',
            'submitted_at' => now(),
        ]);
    }

    /** @test */
    public function us_08_1_admin_can_view_assignment_queue_and_eligible_reviewers()
    {
        $admin = User::where('email', 'adminp3m@harkatnegeri.ac.id')->first();
        $usulan = $this->createSampleProposal();

        $response = $this->actingAs($admin)
            ->withSession(['is_otp_verified' => true])
            ->get(route('admin.reviewer-assignment.index'));

        $response->assertStatus(200);
        $response->assertSee($usulan->kode_usulan);
        $response->assertSee('Tugaskan Reviewer');

        // Test API endpoint for eligible non-CoI reviewers
        $apiResponse = $this->actingAs($admin)
            ->withSession(['is_otp_verified' => true])
            ->get(route('admin.reviewer-assignment.eligible', $usulan));

        $apiResponse->assertStatus(200);
        $data = $apiResponse->json();
        
        // Reviewer 1 (FST, same as Dosen FST) must NOT be in eligible list due to CoI
        $eligibleIds = collect($data['reviewers'])->pluck('id')->all();
        $fstReviewer = User::where('email', 'reviewer@harkatnegeri.ac.id')->first();
        $this->assertNotContains($fstReviewer->id, $eligibleIds, 'FST Reviewer must be blocked by CoI');

        // Reviewer 2 (FSH) and Reviewer 3 (FPP) must be eligible
        $fshReviewer = User::where('email', 'reviewer2@harkatnegeri.ac.id')->first();
        $fppReviewer = User::where('email', 'reviewer3@harkatnegeri.ac.id')->first();
        $this->assertContains($fshReviewer->id, $eligibleIds);
        $this->assertContains($fppReviewer->id, $eligibleIds);
    }

    /** @test */
    public function us_08_1_automated_conflict_of_interest_blocks_same_faculty_assignment()
    {
        $admin = User::where('email', 'adminp3m@harkatnegeri.ac.id')->first();
        $usulan = $this->createSampleProposal();

        $fstReviewer = User::where('email', 'reviewer@harkatnegeri.ac.id')->first();
        $fshReviewer = User::where('email', 'reviewer2@harkatnegeri.ac.id')->first();

        // Attempt to assign FST Reviewer (CoI violation)
        $response = $this->actingAs($admin)
            ->withSession(['is_otp_verified' => true])
            ->post(route('admin.reviewer-assignment.assign', $usulan), [
                'reviewer_1_id' => $fstReviewer->id,
                'reviewer_2_id' => $fshReviewer->id,
            ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('ppm_penugasan_reviewer', [
            'id_usulan' => $usulan->id,
            'id_reviewer' => $fstReviewer->id,
        ]);
    }

    /** @test */
    public function us_08_1_assigning_two_eligible_reviewers_succeeds()
    {
        $admin = User::where('email', 'adminp3m@harkatnegeri.ac.id')->first();
        $usulan = $this->createSampleProposal();

        $fshReviewer = User::where('email', 'reviewer2@harkatnegeri.ac.id')->first();
        $fppReviewer = User::where('email', 'reviewer3@harkatnegeri.ac.id')->first();

        $response = $this->actingAs($admin)
            ->withSession(['is_otp_verified' => true])
            ->post(route('admin.reviewer-assignment.assign', $usulan), [
                'reviewer_1_id' => $fshReviewer->id,
                'reviewer_2_id' => $fppReviewer->id,
            ]);

        $response->assertRedirect(route('admin.reviewer-assignment.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('ppm_penugasan_reviewer', [
            'id_usulan' => $usulan->id,
            'id_reviewer' => $fshReviewer->id,
            'peran_reviewer' => 'reviewer_1',
            'status_penugasan' => 'assigned',
        ]);

        $this->assertDatabaseHas('ppm_penugasan_reviewer', [
            'id_usulan' => $usulan->id,
            'id_reviewer' => $fppReviewer->id,
            'peran_reviewer' => 'reviewer_2',
            'status_penugasan' => 'assigned',
        ]);
    }

    /** @test */
    public function us_08_2_reviewer_sees_double_blind_proposal_without_author_identity()
    {
        $usulan = $this->createSampleProposal();
        $fshReviewer = User::where('email', 'reviewer2@harkatnegeri.ac.id')->first();
        $admin = User::where('email', 'adminp3m@harkatnegeri.ac.id')->first();

        $penugasan = PpmPenugasanReviewer::create([
            'id_usulan' => $usulan->id,
            'id_reviewer' => $fshReviewer->id,
            'peran_reviewer' => 'reviewer_1',
            'status_penugasan' => 'assigned',
            'assigned_by' => $admin->id,
            'assigned_at' => now(),
        ]);

        // Reviewer index page
        $indexResponse = $this->actingAs($fshReviewer)
            ->withSession(['is_otp_verified' => true])
            ->get(route('reviewer.penilaian.index'));

        $indexResponse->assertStatus(200);
        $indexResponse->assertSee($usulan->kode_usulan);
        $indexResponse->assertSee($usulan->judul_usulan);
        // Double-Blind protection: Author name and NIDN must NOT be in the reviewer view
        $indexResponse->assertDontSee($usulan->pengusul->name);
        $indexResponse->assertDontSee($usulan->pengusul->nidn_nim);

        // Reviewer form page
        $formResponse = $this->actingAs($fshReviewer)
            ->withSession(['is_otp_verified' => true])
            ->get(route('reviewer.penilaian.show', $penugasan));

        $formResponse->assertStatus(200);
        $formResponse->assertSee('Borang Penilaian Substansi');
        $formResponse->assertSee('Double-Blind Active');
        $formResponse->assertDontSee($usulan->pengusul->name);
        $formResponse->assertDontSee($usulan->pengusul->nidn_nim);
    }

    /** @test */
    public function us_08_2_submitting_rubric_scores_calculates_weighted_score_and_locks_permanently()
    {
        $usulan = $this->createSampleProposal();
        $fshReviewer = User::where('email', 'reviewer2@harkatnegeri.ac.id')->first();
        $admin = User::where('email', 'adminp3m@harkatnegeri.ac.id')->first();

        $penugasan = PpmPenugasanReviewer::create([
            'id_usulan' => $usulan->id,
            'id_reviewer' => $fshReviewer->id,
            'peran_reviewer' => 'reviewer_1',
            'status_penugasan' => 'assigned',
            'assigned_by' => $admin->id,
            'assigned_at' => now(),
        ]);

        // Rubric: k1 (bobot 30), k2 (bobot 40), k3 (bobot 30)
        // Scores: k1=6 (6*30=180), k2=5 (5*40=200), k3=6 (6*30=180) -> Total = 560
        $response = $this->actingAs($fshReviewer)
            ->withSession(['is_otp_verified' => true])
            ->post(route('reviewer.penilaian.store', $penugasan), [
                'scores' => [
                    'k1' => 6,
                    'k2' => 5,
                    'k3' => 6,
                ],
                'komentar_kualitatif' => 'Metodologi sangat baik dan luaran jurnal terindeks Scopus sangat realistis dicapai.',
                'rekomendasi' => 'layak',
            ]);

        $response->assertRedirect(route('reviewer.penilaian.show', $penugasan));
        $response->assertSessionHas('success');

        // Verify locked penilaian in database
        $this->assertDatabaseHas('ppm_penilaian_reviewer', [
            'id_penugasan' => $penugasan->id,
            'total_skor' => 560.00,
            'is_locked' => true,
            'rekomendasi' => 'layak',
        ]);

        $this->assertDatabaseHas('ppm_penugasan_reviewer', [
            'id' => $penugasan->id,
            'status_penugasan' => 'completed',
        ]);

        $this->assertDatabaseHas('ppm_usulan', [
            'id' => $usulan->id,
            'skor_reviewer_1' => 560.00,
        ]);

        // Attempting to modify locked form must be rejected
        $repeatResponse = $this->actingAs($fshReviewer)
            ->withSession(['is_otp_verified' => true])
            ->post(route('reviewer.penilaian.store', $penugasan), [
                'scores' => [
                    'k1' => 7,
                    'k2' => 7,
                    'k3' => 7,
                ],
                'komentar_kualitatif' => 'Ingin mengganti nilai.',
                'rekomendasi' => 'layak',
            ]);

        $repeatResponse->assertSessionHas('error');
    }

    /** @test */
    public function us_08_3_extreme_disparity_triggers_adjudication_and_reviewer_3_assignment()
    {
        $usulan = $this->createSampleProposal();
        $admin = User::where('email', 'adminp3m@harkatnegeri.ac.id')->first();
        $r1 = User::where('email', 'reviewer2@harkatnegeri.ac.id')->first(); // FSH
        $r2 = User::where('email', 'reviewer3@harkatnegeri.ac.id')->first(); // FPP

        [$p1, $p2] = ReviewEngineService::assignReviewers($usulan, $r1->id, $r2->id, $admin->id);

        // R1 gives high score: k1=6, k2=6, k3=6 -> Total = 600.00
        ReviewEngineService::submitReview($p1, ['k1' => 6, 'k2' => 6, 'k3' => 6], 'Sangat bagus.', 'layak');

        // R2 gives low score: k1=4, k2=4, k3=4 -> Total = 400.00
        // Selisih = 200 poin (33.3%), triggering Extreme Disparity (>= 150 pts OR >= 25%)
        ReviewEngineService::submitReview($p2, ['k1' => 4, 'k2' => 4, 'k3' => 4], 'Kurang tajam.', 'revisi');

        $usulan->refresh();
        $this->assertEquals('Adjudication', $usulan->status);
        $this->assertTrue($usulan->is_disparity);
        $this->assertStringContainsString('Disparitas nilai ekstrem terdeteksi', $usulan->adjudication_notes);

        // Admin P3M assigns Reviewer 3 (Penengah)
        // Let's create an external/independent 4th reviewer in another faculty (e.g. Sekolah Vokasi)
        $reviewerRole = \App\Models\Role::where('name', 'Reviewer')->first();
        $r3 = User::factory()->create([
            'email' => 'reviewer_adjudicator@harkatnegeri.ac.id',
            'id_fakultas' => 4,
            'id_prodi' => 17,
        ]);
        $r3->roles()->attach($reviewerRole->id);

        $adjudicateResponse = $this->actingAs($admin)
            ->withSession(['is_otp_verified' => true])
            ->post(route('admin.reviewer-assignment.adjudicator', $usulan), [
                'adjudicator_id' => $r3->id,
            ]);

        $adjudicateResponse->assertRedirect(route('admin.reviewer-assignment.index'));
        $adjudicateResponse->assertSessionHas('success');

        $p3 = PpmPenugasanReviewer::where('id_usulan', $usulan->id)->where('peran_reviewer', 'adjudicator')->first();
        $this->assertNotNull($p3);

        // Reviewer 3 evaluates with score near R1:
        // k1=6, k2=6, k3=5 -> Total = (6*30) + (6*40) + (5*30) = 180 + 240 + 150 = 570.00
        ReviewEngineService::submitReview($p3, ['k1' => 6, 'k2' => 6, 'k3' => 5], 'Review adil.', 'layak');

        $usulan->refresh();
        $this->assertEquals('Reviewed', $usulan->status);
        $this->assertEquals(570.00, (float) $usulan->skor_reviewer_3);

        // Nearest two among (600, 400, 570):
        // |600 - 400| = 200
        // |600 - 570| = 30
        // |400 - 570| = 170
        // Nearest two are 600 and 570 -> Average = (600 + 570) / 2 = 585.00
        $this->assertEquals(585.00, (float) $usulan->skor_akhir);
    }

    /** @test */
    public function us_08_4_kepala_p3m_can_view_ranking_and_dynamic_budget_cutoff()
    {
        $kepala = User::where('email', 'kepalap3m@harkatnegeri.ac.id')->first();
        $skema = PpmSkemaBima::first();
        $periode = PpmPeriodeHibah::first();
        $dosen = User::where('email', 'dosen@harkatnegeri.ac.id')->first();

        // Create 3 ranked proposals
        $u1 = PpmUsulan::create([
            'id_pengusul' => $dosen->id,
            'id_skema_bima' => $skema->id,
            'id_periode_hibah' => $periode->id,
            'kode_usulan' => 'RANK-001',
            'judul_usulan' => 'Usulan Peringkat Pertama',
            'total_rab' => 100000000.0, // 100 Juta
            'skor_akhir' => 650.00,
            'status' => 'Reviewed',
        ]);

        $u2 = PpmUsulan::create([
            'id_pengusul' => $dosen->id,
            'id_skema_bima' => $skema->id,
            'id_periode_hibah' => $periode->id,
            'kode_usulan' => 'RANK-002',
            'judul_usulan' => 'Usulan Peringkat Kedua',
            'total_rab' => 100000000.0, // 100 Juta (Kumulatif 200 Juta)
            'skor_akhir' => 600.00,
            'status' => 'Reviewed',
        ]);

        $u3 = PpmUsulan::create([
            'id_pengusul' => $dosen->id,
            'id_skema_bima' => $skema->id,
            'id_periode_hibah' => $periode->id,
            'kode_usulan' => 'RANK-003',
            'judul_usulan' => 'Usulan Peringkat Ketiga',
            'total_rab' => 100000000.0, // 100 Juta (Kumulatif 300 Juta)
            'skor_akhir' => 520.00,
            'status' => 'Reviewed',
        ]);

        // Access ranking with pagu 200 Juta (only #1 and #2 will pass)
        $response = $this->actingAs($kepala)
            ->withSession(['is_otp_verified' => true])
            ->get(route('admin.ranking.index', ['total_budget' => 200000000]));

        $response->assertStatus(200);
        $response->assertSee('Pemeringkatan Usulan & Kuota');
        $response->assertSee('RANK-001');
        $response->assertSee('RANK-002');
        $response->assertSee('RANK-003');
        $response->assertSee('BATAS PASSING GRADE PENDANAAN');

        // Non-Kepala P3M (e.g. Dosen) must be denied access (403)
        $deniedResponse = $this->actingAs($dosen)
            ->withSession(['is_otp_verified' => true])
            ->get(route('admin.ranking.index'));

        $deniedResponse->assertStatus(403);
    }

    /** @test */
    public function epic_08_sidebar_respects_role_authority()
    {
        $reviewer = User::where('email', 'reviewer@harkatnegeri.ac.id')->first();
        $admin = User::where('email', 'adminp3m@harkatnegeri.ac.id')->first();
        $kepala = User::where('email', 'kepalap3m@harkatnegeri.ac.id')->first();

        // 1. Reviewer sees Penilaian Proposal, but not Penugasan or Ranking
        $reviewerResponse = $this->actingAs($reviewer)
            ->withSession(['is_otp_verified' => true])
            ->get(route('dashboard'));

        $reviewerResponse->assertStatus(200);
        $reviewerResponse->assertSee(route('reviewer.penilaian.index'));
        $reviewerResponse->assertDontSee(route('admin.reviewer-assignment.index'));
        $reviewerResponse->assertDontSee(route('admin.ranking.index'));

        // 2. Admin P3M sees Penugasan Reviewer, but not Ranking
        $adminResponse = $this->actingAs($admin)
            ->withSession(['is_otp_verified' => true])
            ->get(route('dashboard'));

        $adminResponse->assertStatus(200);
        $adminResponse->assertSee(route('admin.reviewer-assignment.index'));
        $adminResponse->assertDontSee(route('admin.ranking.index'));
        $adminResponse->assertDontSee(route('reviewer.penilaian.index'));

        // 3. Kepala P3M sees Pemeringkatan & Kuota, but not Penugasan Reviewer
        $kepalaResponse = $this->actingAs($kepala)
            ->withSession(['is_otp_verified' => true])
            ->get(route('dashboard'));

        $kepalaResponse->assertStatus(200);
        $kepalaResponse->assertSee(route('admin.ranking.index'));
        $kepalaResponse->assertDontSee(route('admin.reviewer-assignment.index'));
        $kepalaResponse->assertDontSee(route('reviewer.penilaian.index'));
    }
}

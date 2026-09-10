<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\User;
use App\Services\EligibilityService;
use App\Services\SintaService;

class SintaEligibilityTest extends TestCase
{
    use RefreshDatabase;

    protected EligibilityService $eligibilityService;
    protected SintaService $sintaService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->eligibilityService = new EligibilityService();
        $this->sintaService = new SintaService();
    }

    #[Test]
    public function dosen_asisten_ahli_with_sinta_3yr_above_50_is_eligible_for_pdp()
    {
        $user = User::factory()->create([
            'jabatan_fungsional' => 'Asisten Ahli',
            'sinta_score_3yr' => 65.50,
            'sinta_score_overall' => 150.00,
        ]);

        $result = $this->eligibilityService->checkEligibility($user, 'pdp');

        $this->assertTrue($result['is_eligible']);
        $this->assertEquals('ELIGIBLE', $result['status_code']);
    }

    #[Test]
    public function dosen_lektor_with_sinta_3yr_above_150_is_eligible_for_fundamental()
    {
        $user = User::factory()->create([
            'jabatan_fungsional' => 'Lektor',
            'sinta_score_3yr' => 180.00,
            'sinta_score_overall' => 320.00,
        ]);

        $result = $this->eligibilityService->checkEligibility($user, 'fundamental');

        $this->assertTrue($result['is_eligible']);
        $this->assertEquals('ELIGIBLE', $result['status_code']);
    }

    #[Test]
    public function dosen_below_threshold_is_rejected_with_clear_reason()
    {
        $user = User::factory()->create([
            'jabatan_fungsional' => 'Asisten Ahli',
            'sinta_score_3yr' => 20.00,
        ]);

        $result = $this->eligibilityService->checkEligibility($user, 'pdp');

        $this->assertFalse($result['is_eligible']);
        $this->assertEquals('SINTA_SCORE_BELOW_THRESHOLD', $result['status_code']);
        $this->assertStringContainsString('kurang 30 poin', $result['reason']);
    }

    #[Test]
    public function sinta_fallback_pending_verification_blocks_proposal_creation()
    {
        $user = User::factory()->create([
            'jabatan_fungsional' => 'Lektor',
            'sinta_score_3yr' => 200.00,
            'is_sinta_manual_fallback' => true,
            'sinta_verification_status' => 'pending_operator',
        ]);

        $result = $this->eligibilityService->checkEligibility($user, 'fundamental');

        $this->assertFalse($result['is_eligible']);
        $this->assertEquals('PENDING_OPERATOR_VERIFICATION', $result['status_code']);
    }

    #[Test]
    public function midnight_worker_sync_command_runs_cleanly()
    {
        User::factory()->create(['nidn_nim' => '0615037801']);
        User::factory()->create(['nidn_nim' => '0615037802']);
        User::factory()->create(['nidn_nim' => '0615037803']);

        $this->artisan('prisma:sync-sinta')
             ->assertExitCode(0);
    }
}


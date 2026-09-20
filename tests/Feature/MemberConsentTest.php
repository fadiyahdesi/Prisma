<?php

namespace Tests\Feature;

use App\Models\PpmUsulanAnggota;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberConsentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
        $this->artisan('db:seed', ['--class' => 'MemberConsentDemoSeeder']);
    }

    /** @test */
    public function member_consent_index_page_renders_with_sidebar_and_widescreen_layout()
    {
        $dosen = User::where('email', 'dosen@harkatnegeri.ac.id')->first();

        $response = $this->actingAs($dosen)
            ->withSession(['is_otp_verified' => true])
            ->get(route('member-consent.index'));

        $response->assertStatus(200);
        // Ensure sidebar is present
        $response->assertSee('PRISMA UHN');
        $response->assertSee('Dasbor Utama');
        // Ensure wide layout and header
        $response->assertSee('w-full', false);
        $response->assertSee('Persetujuan Keanggotaan Tim');
        // Ensure action buttons exist
        $response->assertSee('Setujui Keanggotaan');
        $response->assertSee('Tolak Undangan');
    }

    /** @test */
    public function member_can_approve_invitation()
    {
        $dosen = User::where('email', 'dosen@harkatnegeri.ac.id')->first();
        $pendingInvitation = PpmUsulanAnggota::where('user_id', $dosen->id)
            ->where('status_persetujuan', 'pending')
            ->first();

        $response = $this->actingAs($dosen)
            ->withSession(['is_otp_verified' => true])
            ->post(route('member-consent.respond', $pendingInvitation), [
                'decision' => 'approved',
            ]);

        $response->assertRedirect(route('member-consent.index'));
        $this->assertDatabaseHas('ppm_usulan_anggota', [
            'id' => $pendingInvitation->id,
            'status_persetujuan' => 'approved',
        ]);
    }

    /** @test */
    public function member_can_reject_invitation()
    {
        $dosen = User::where('email', 'dosen@harkatnegeri.ac.id')->first();
        $pendingInvitation = PpmUsulanAnggota::where('user_id', $dosen->id)
            ->where('status_persetujuan', 'pending')
            ->first();

        $response = $this->actingAs($dosen)
            ->withSession(['is_otp_verified' => true])
            ->post(route('member-consent.respond', $pendingInvitation), [
                'decision' => 'rejected',
            ]);

        $response->assertRedirect(route('member-consent.index'));
        $this->assertDatabaseHas('ppm_usulan_anggota', [
            'id' => $pendingInvitation->id,
            'status_persetujuan' => 'rejected',
        ]);
    }
}


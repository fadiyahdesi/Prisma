<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class RoleBasedSidebarVisibilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    /** @test */
    public function dosen_pengusul_sidebar_only_shows_authorized_features()
    {
        $dosen = User::where('email', 'dosen@harkatnegeri.ac.id')->first();

        $response = $this->actingAs($dosen)
            ->withSession(['is_otp_verified' => true])
            ->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee(route('usulan.index'));
        $response->assertSee(route('sinta.profile'));
        $response->assertSee(route('pddikti.search'));
        $response->assertSee(route('pengusul.kontrak.index'));

        // Unauthorized P3M management & Keuangan items must NOT be visible
        $response->assertDontSee(route('keuangan.pencairan.index'));
        $response->assertDontSee(route('admin.skema-bima.index'));
        $response->assertDontSee(route('admin.periode-hibah.index'));
        $response->assertDontSee(route('admin.lppm-approval.index'));
        $response->assertDontSee(route('audit-logs'));
        $response->assertDontSee(route('admin.prodi-roadmap.index'));
        $response->assertDontSee('href="' . route('integrasi.index') . '"', false);
    }

    /** @test */
    public function mahasiswa_anggota_sidebar_only_shows_member_consent()
    {
        $anggota = User::where('email', 'mahasiswa1@harkatnegeri.ac.id')->first();

        $response = $this->actingAs($anggota)
            ->withSession(['is_otp_verified' => true])
            ->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee(route('dashboard'));
        $response->assertSee(route('member-consent.index'));

        // All other features must NOT be visible
        $response->assertDontSee(route('usulan.index'));
        $response->assertDontSee(route('sinta.profile'));
        $response->assertDontSee(route('pddikti.search'));
        $response->assertDontSee(route('admin.skema-bima.index'));
        $response->assertDontSee(route('admin.periode-hibah.index'));
        $response->assertDontSee(route('admin.lppm-approval.index'));
        $response->assertDontSee(route('audit-logs'));
        $response->assertDontSee(route('admin.prodi-roadmap.index'));
        $response->assertDontSee(route('integrasi.index'));
    }

    /** @test */
    public function reviewer_sidebar_only_shows_dashboard_and_sinta()
    {
        $reviewer = User::where('email', 'reviewer@harkatnegeri.ac.id')->first();

        $response = $this->actingAs($reviewer)
            ->withSession(['is_otp_verified' => true])
            ->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee(route('dashboard'));
        $response->assertSee(route('sinta.profile'));

        $response->assertDontSee(route('usulan.index'));
        $response->assertDontSee(route('member-consent.index'));
        $response->assertDontSee(route('pddikti.search'));
        $response->assertDontSee(route('admin.skema-bima.index'));
        $response->assertDontSee(route('admin.periode-hibah.index'));
        $response->assertDontSee(route('admin.lppm-approval.index'));
        $response->assertDontSee(route('audit-logs'));
        $response->assertDontSee(route('admin.prodi-roadmap.index'));
        $response->assertDontSee(route('integrasi.index'));
    }

    /** @test */
    public function kaprodi_sidebar_only_shows_prodi_roadmap()
    {
        $kaprodi = User::where('email', 'kaprodi@harkatnegeri.ac.id')->first();

        $response = $this->actingAs($kaprodi)
            ->withSession(['is_otp_verified' => true])
            ->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee(route('dashboard'));
        $response->assertSee(route('admin.prodi-roadmap.index'));

        $response->assertDontSee(route('usulan.index'));
        $response->assertDontSee(route('member-consent.index'));
        $response->assertDontSee(route('sinta.profile'));
        $response->assertDontSee(route('pddikti.search'));
        $response->assertDontSee(route('admin.skema-bima.index'));
        $response->assertDontSee(route('admin.periode-hibah.index'));
        $response->assertDontSee(route('admin.lppm-approval.index'));
        $response->assertDontSee(route('audit-logs'));
        $response->assertDontSee(route('integrasi.index'));
    }

    /** @test */
    public function dekanat_sidebar_is_clean()
    {
        $dekan = User::where('email', 'dekan@harkatnegeri.ac.id')->first();

        $response = $this->actingAs($dekan)
            ->withSession(['is_otp_verified' => true])
            ->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee(route('dashboard'));

        $response->assertDontSee(route('usulan.index'));
        $response->assertDontSee(route('member-consent.index'));
        $response->assertDontSee(route('sinta.profile'));
        $response->assertDontSee(route('pddikti.search'));
        $response->assertDontSee(route('admin.skema-bima.index'));
        $response->assertDontSee(route('admin.periode-hibah.index'));
        $response->assertDontSee(route('admin.lppm-approval.index'));
        $response->assertDontSee(route('audit-logs'));
        $response->assertDontSee(route('admin.prodi-roadmap.index'));
        $response->assertDontSee(route('integrasi.index'));
    }

    /** @test */
    public function admin_p3m_sidebar_only_shows_p3m_configuration_and_verification()
    {
        $admin = User::where('email', 'adminp3m@harkatnegeri.ac.id')->first();

        $response = $this->actingAs($admin)
            ->withSession(['is_otp_verified' => true])
            ->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee(route('dashboard'));
        $response->assertSee(route('integrasi.index'));
        $response->assertSee(route('pddikti.search'));
        $response->assertSee(route('admin.skema-bima.index'));
        $response->assertSee(route('admin.periode-hibah.index'));
        $response->assertSee(route('admin.lppm-approval.index'));

        // Not allowed for Admin P3M
        $response->assertDontSee(route('usulan.index'));
        $response->assertDontSee(route('member-consent.index'));
        $response->assertDontSee(route('sinta.profile'));
        $response->assertDontSee(route('audit-logs')); // US-02.4: Kepala P3M & Superadmin only
        $response->assertDontSee(route('admin.prodi-roadmap.index'));
    }

    /** @test */
    public function kepala_p3m_sidebar_only_shows_approval_and_audit_logs()
    {
        $kepala = User::where('email', 'kepalap3m@harkatnegeri.ac.id')->first();

        $response = $this->actingAs($kepala)
            ->withSession(['is_otp_verified' => true])
            ->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee(route('dashboard'));
        $response->assertSee(route('admin.lppm-approval.index'));
        $response->assertSee(route('audit-logs'));

        // Not allowed for Kepala P3M
        $response->assertDontSee(route('admin.skema-bima.index'));
        $response->assertDontSee(route('admin.periode-hibah.index'));
        $response->assertDontSee(route('integrasi.index'));
        $response->assertDontSee(route('pddikti.search'));
        $response->assertDontSee(route('usulan.index'));
        $response->assertDontSee(route('member-consent.index'));
        $response->assertDontSee(route('sinta.profile'));
        $response->assertDontSee(route('admin.prodi-roadmap.index'));
    }

    /** @test */
    public function keuangan_sidebar_is_clean()
    {
        $keuangan = User::where('email', 'keuangan@harkatnegeri.ac.id')->first();

        $response = $this->actingAs($keuangan)
            ->withSession(['is_otp_verified' => true])
            ->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee(route('dashboard'));
        $response->assertSee(route('keuangan.pencairan.index'));

        $response->assertDontSee(route('pengusul.kontrak.index'));
        $response->assertDontSee(route('usulan.index'));
        $response->assertDontSee(route('member-consent.index'));
        $response->assertDontSee(route('sinta.profile'));
        $response->assertDontSee(route('pddikti.search'));
        $response->assertDontSee(route('admin.skema-bima.index'));
        $response->assertDontSee(route('admin.periode-hibah.index'));
        $response->assertDontSee(route('admin.lppm-approval.index'));
        $response->assertDontSee(route('audit-logs'));
        $response->assertDontSee(route('admin.prodi-roadmap.index'));
        $response->assertDontSee(route('integrasi.index'));
    }

    /** @test */
    public function superadmin_sidebar_shows_all_features()
    {
        $superadmin = User::where('email', 'superadmin@harkatnegeri.ac.id')->first();

        $response = $this->actingAs($superadmin)
            ->withSession(['is_otp_verified' => true])
            ->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee(route('dashboard'));
        $response->assertSee(route('usulan.index'));
        $response->assertSee(route('member-consent.index'));
        $response->assertSee(route('sinta.profile'));
        $response->assertSee(route('pddikti.search'));
        $response->assertSee(route('integrasi.index'));
        $response->assertSee(route('admin.skema-bima.index'));
        $response->assertSee(route('admin.periode-hibah.index'));
        $response->assertSee(route('admin.lppm-approval.index'));
        $response->assertDontSee('fake-non-existent-route');
        $response->assertSee(route('audit-logs'));
        $response->assertSee(route('admin.prodi-roadmap.index'));
        $response->assertSee(route('pengusul.kontrak.index'));
        $response->assertSee(route('keuangan.pencairan.index'));
    }
}

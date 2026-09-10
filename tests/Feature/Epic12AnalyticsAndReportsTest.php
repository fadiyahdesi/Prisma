<?php

namespace Tests\Feature;

use App\Models\PpmPeriodeHibah;
use App\Models\RefFakultas;
use App\Models\User;
use App\Services\AnalyticsService;
use App\Services\AccreditationReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class Epic12AnalyticsAndReportsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
        $this->artisan('db:seed', ['--class' => 'Epic12DemoDataSeeder']);
    }

    /** @test */
    public function us_12_1_executive_dashboard_is_accessible_by_authorized_roles()
    {
        $kepala = User::where('email', 'kepalap3m@harkatnegeri.ac.id')->first();
        $superadmin = User::where('email', 'superadmin@harkatnegeri.ac.id')->first();

        // Kepala P3M can access
        $resKepala = $this->actingAs($kepala)
            ->withSession(['is_otp_verified' => true])
            ->get(route('analitik.eksekutif'));
        $resKepala->assertStatus(200);
        $resKepala->assertSee('Dasbor Analitik Eksekutif');
        $resKepala->assertSee('Tingkat Universitas');
        $resKepala->assertSee('Fakultas Sains & Teknologi');
        $resKepala->assertSee('Sekolah Vokasi');

        // Superadmin can access
        $resSuper = $this->actingAs($superadmin)
            ->withSession(['is_otp_verified' => true])
            ->get(route('analitik.eksekutif'));
        $resSuper->assertStatus(200);
    }

    /** @test */
    public function us_12_1_executive_dashboard_denies_unauthorized_roles()
    {
        $dosen = User::where('email', 'dosen@harkatnegeri.ac.id')->first();
        $keuangan = User::where('email', 'keuangan@harkatnegeri.ac.id')->first();

        $this->actingAs($dosen)
            ->withSession(['is_otp_verified' => true])
            ->get(route('analitik.eksekutif'))
            ->assertStatus(403);

        $this->actingAs($keuangan)
            ->withSession(['is_otp_verified' => true])
            ->get(route('analitik.eksekutif'))
            ->assertStatus(403);
    }

    /** @test */
    public function us_12_1_analytics_service_calculates_macro_metrics_and_caches_result()
    {
        Cache::flush();
        $analyticsService = app(AnalyticsService::class);

        $metrics = $analyticsService->getExecutiveMetrics(null, false);

        $this->assertArrayHasKey('total_usulan', $metrics);
        $this->assertArrayHasKey('total_pagu', $metrics);
        $this->assertArrayHasKey('total_realisasi', $metrics);
        $this->assertArrayHasKey('serapan_percentage', $metrics);
        $this->assertArrayHasKey('tkt_distribution', $metrics);
        $this->assertArrayHasKey('iku2', $metrics);
        $this->assertArrayHasKey('iku5', $metrics);
        $this->assertArrayHasKey('fakultas_breakdown', $metrics);

        // 4 Faculties aggregated
        $this->assertCount(4, $metrics['fakultas_breakdown']);
        $facultyCodes = array_column($metrics['fakultas_breakdown'], 'kode');
        $this->assertContains('FST', $facultyCodes);
        $this->assertContains('FSH', $facultyCodes);
        $this->assertContains('FPP', $facultyCodes);
        $this->assertContains('SV', $facultyCodes);

        // TKT distribution mapped (1-3, 4-6, 7-9)
        $this->assertGreaterThanOrEqual(0, $metrics['tkt_distribution']['dasar']['count']);
        $this->assertGreaterThanOrEqual(0, $metrics['tkt_distribution']['terapan']['count']);
        $this->assertGreaterThanOrEqual(0, $metrics['tkt_distribution']['pengembangan']['count']);

        // Cache verification
        $this->assertTrue(Cache::has('executive_analytics_summary_all'));
    }

    /** @test */
    public function us_12_2_faculty_performance_dashboard_enforces_tenant_isolation_for_dekanat()
    {
        $dekan = User::where('email', 'dekan@harkatnegeri.ac.id')->first();
        $fst = RefFakultas::where('kode_fakultas', 'FST')->first();
        $fsh = RefFakultas::where('kode_fakultas', 'FSH')->first();

        // Assign dekan to FST
        $dekan->update(['id_fakultas' => $fst->id_fakultas]);

        $response = $this->actingAs($dekan)
            ->withSession(['is_otp_verified' => true])
            ->get(route('analitik.fakultas'));

        $response->assertStatus(200);
        $response->assertSee($fst->nama_fakultas);
        $response->assertSee('Prodi Serapan Dana Tertinggi');
        $response->assertSee('Prodi Serapan Terendah (Perlu Perhatian)');

        // Attempt to breach tenant isolation by passing another faculty id
        $responseOther = $this->actingAs($dekan)
            ->withSession(['is_otp_verified' => true])
            ->get(route('analitik.fakultas', ['fakultas_id' => $fsh->id_fakultas]));

        // Dekan must be isolated: response still renders their own FST or throws 403
        $responseOther->assertStatus(200);
        $responseOther->assertSee($fst->nama_fakultas);
    }

    /** @test */
    public function us_12_2_privileged_roles_can_switch_faculties()
    {
        $superadmin = User::where('email', 'superadmin@harkatnegeri.ac.id')->first();
        $sv = RefFakultas::where('kode_fakultas', 'SV')->first();

        $response = $this->actingAs($superadmin)
            ->withSession(['is_otp_verified' => true])
            ->get(route('analitik.fakultas', ['fakultas_id' => $sv->id_fakultas]));

        $response->assertStatus(200);
        $response->assertSee('Sekolah Vokasi');
    }

    /** @test */
    public function us_12_3_accreditation_report_preview_shows_all_4_tables()
    {
        $adminP3m = User::where('email', 'adminp3m@harkatnegeri.ac.id')->first();

        $response = $this->actingAs($adminP3m)
            ->withSession(['is_otp_verified' => true])
            ->get(route('laporan.akreditasi'));

        $response->assertStatus(200);
        $response->assertSee('Pelaporan Akreditasi BAN-PT', false);
        $response->assertSee('Tabel 3.b.1: Penelitian DTPS');
        $response->assertSee('Tabel 3.b.2: PkM DTPS');
        $response->assertSee('Tabel 3.b.3: Publikasi Ilmiah DTPS');
        $response->assertSee('Tabel 3.b.4: HKI', false);
    }

    /** @test */
    public function us_12_3_excel_export_returns_xlsx_spreadsheet_download()
    {
        $adminP3m = User::where('email', 'adminp3m@harkatnegeri.ac.id')->first();

        $response = $this->actingAs($adminP3m)
            ->withSession(['is_otp_verified' => true])
            ->get(route('laporan.akreditasi.excel'));

        $response->assertStatus(200);
        $this->assertTrue(
            str_contains($response->headers->get('content-type'), 'spreadsheet') ||
            str_contains($response->headers->get('content-type'), 'vnd.openxmlformats-officedocument.spreadsheetml.sheet') ||
            str_contains($response->headers->get('content-disposition'), '.xlsx')
        );
    }

    /** @test */
    public function us_12_3_pdf_export_returns_printable_document()
    {
        $adminP3m = User::where('email', 'adminp3m@harkatnegeri.ac.id')->first();

        $response = $this->actingAs($adminP3m)
            ->withSession(['is_otp_verified' => true])
            ->get(route('laporan.akreditasi.pdf'));

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
        $this->assertTrue(str_contains($response->headers->get('content-disposition'), '.pdf'));
    }

    /** @test */
    public function sidebar_displays_analytics_and_report_links_based_on_role()
    {
        $kepala = User::where('email', 'kepalap3m@harkatnegeri.ac.id')->first();
        $dekan = User::where('email', 'dekan@harkatnegeri.ac.id')->first();
        $dosen = User::where('email', 'dosen@harkatnegeri.ac.id')->first();

        // Kepala P3M sees Dasbor Eksekutif and Akreditasi
        $resKepala = $this->actingAs($kepala)
            ->withSession(['is_otp_verified' => true])
            ->get(route('dashboard'));
        $resKepala->assertSee(route('analitik.eksekutif'));
        $resKepala->assertSee(route('laporan.akreditasi'));

        // Dekanat sees Performa Fakultas and Akreditasi, but NOT Dasbor Eksekutif
        $resDekan = $this->actingAs($dekan)
            ->withSession(['is_otp_verified' => true])
            ->get(route('dashboard'));
        $resDekan->assertSee(route('analitik.fakultas'));
        $resDekan->assertSee(route('laporan.akreditasi'));
        $resDekan->assertDontSee(route('analitik.eksekutif'));

        // Dosen does not see executive or accreditation reports
        $resDosen = $this->actingAs($dosen)
            ->withSession(['is_otp_verified' => true])
            ->get(route('dashboard'));
        $resDosen->assertDontSee(route('analitik.eksekutif'));
        $resDosen->assertDontSee(route('analitik.fakultas'));
        $resDosen->assertDontSee(route('laporan.akreditasi'));
    }
}

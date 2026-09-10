<?php

namespace Tests\Feature;

use App\Models\PpmHki;
use App\Models\PpmKontrak;
use App\Models\PpmPublikasiJurnal;
use App\Models\PpmUsulan;
use App\Models\RefFakultas;
use App\Models\RefProgramStudi;
use App\Models\User;
use App\Services\Integrations\StorageSecurityService;
use App\Services\LegacyDataMigrationService;
use App\Services\StressTestingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class Epic13MigrationSecurityAndUatTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    // ==========================================
    // 1. MASTER DATA INTEGRITY (4 FAKULTAS, 22 PRODI)
    // ==========================================

    public function test_master_data_strictly_aligns_with_official_blueprint(): void
    {
        // Must have exactly 4 faculties: FST, FSH, FPP, SV
        $this->assertEquals(4, RefFakultas::count());
        $faculties = RefFakultas::pluck('kode_fakultas')->sort()->values()->toArray();
        $this->assertEquals(['FPP', 'FSH', 'FST', 'SV'], $faculties);

        // Must have exactly 22 official program studi
        $this->assertEquals(22, RefProgramStudi::count());

        // Check distribution: FST (4), FSH (4), FPP (2), SV (12)
        $fst = RefFakultas::where('kode_fakultas', 'FST')->first();
        $fsh = RefFakultas::where('kode_fakultas', 'FSH')->first();
        $fpp = RefFakultas::where('kode_fakultas', 'FPP')->first();
        $sv  = RefFakultas::where('kode_fakultas', 'SV')->first();

        $this->assertEquals(4, RefProgramStudi::where('id_fakultas', $fst->id_fakultas)->count());
        $this->assertEquals(4, RefProgramStudi::where('id_fakultas', $fsh->id_fakultas)->count());
        $this->assertEquals(2, RefProgramStudi::where('id_fakultas', $fpp->id_fakultas)->count());
        $this->assertEquals(12, RefProgramStudi::where('id_fakultas', $sv->id_fakultas)->count());
    }

    // ==========================================
    // 2. US-13.1 LEGACY DATA MIGRATION (ZERO DATA LOSS)
    // ==========================================

    public function test_legacy_migration_service_verifies_sha256_and_executes_with_zero_data_loss(): void
    {
        $service = app(LegacyDataMigrationService::class);

        // 1. Verification of SHA-256 Checksum & Dataset Integrity
        $validation = $service->validateIntegrity();
        $this->assertTrue($validation['valid']);
        $this->assertTrue($validation['checksums_verified']);
        $this->assertGreaterThan(0, $validation['checksums_count']);

        // 2. Dry Run
        $dryRun = $service->migrate(dryRun: true);
        $this->assertTrue($dryRun['success']);
        $this->assertTrue($dryRun['dry_run']);
        $this->assertEquals(7, $dryRun['stats']['proposals_to_migrate']);
        $this->assertEquals(3, $dryRun['stats']['hki_to_migrate']);
        $this->assertEquals(3, $dryRun['stats']['publications_to_migrate']);

        // 3. Live Execution
        $live = $service->migrate(dryRun: false);
        $this->assertTrue($live['success']);
        $this->assertFalse($live['dry_run']);
        $this->assertEquals(7, $live['stats']['proposals_migrated']);
        $this->assertEquals(7, $live['stats']['contracts_migrated']);
        $this->assertEquals(3, $live['stats']['hki_migrated']);
        $this->assertEquals(3, $live['stats']['publications_migrated']);

        // Verify entities created in database
        $this->assertGreaterThanOrEqual(7, PpmUsulan::count());
        $this->assertGreaterThanOrEqual(7, PpmKontrak::count());
        $this->assertGreaterThanOrEqual(3, PpmHki::count());
        $this->assertGreaterThanOrEqual(3, PpmPublikasiJurnal::count());

        // Verify specific migrated records
        $this->assertDatabaseHas('ppm_usulan', [
            'judul_usulan' => 'Implementasi IoT Sensor Kelembaban Tanah Berbasis ESP32 pada Perkebunan Sayur',
        ]);
        $this->assertDatabaseHas('ppm_kontrak', [
            'nomor_kontrak' => 'SPK-HISTORIS/2021/PHB-RIS-2021-001',
        ]);
        $this->assertDatabaseHas('ppm_hki', [
            'nomor_sertifikat' => '000389124',
        ]);
        $this->assertDatabaseHas('ppm_publikasi_jurnal', [
            'doi' => '10.32815/jakv.v3i2.891',
        ]);
    }

    public function test_prodi_mapping_dictionary_resolves_all_legacy_codes(): void
    {
        $service = app(LegacyDataMigrationService::class);
        $mapping = $service->getMappingDictionary();

        $this->assertArrayHasKey('D3 Teknik Komputer', $mapping);
        $this->assertArrayHasKey('S1 Sistem Informasi STMIK YMI', $mapping);
        $this->assertEquals('TK-D3', $mapping['D3 Teknik Komputer']);
        $this->assertEquals('SI-S1', $mapping['S1 Sistem Informasi STMIK YMI']);

        // Verify all mapped target prodi codes exist in RefProgramStudi
        foreach ($mapping as $legacyProdi => $targetCode) {
            $this->assertDatabaseHas('ref_program_studi', ['kode_prodi' => $targetCode]);
        }
    }

    public function test_legacy_migration_artisan_command(): void
    {
        $this->artisan('prisma:migrate-legacy', ['--dry-run' => true])
            ->expectsOutputToContain('Simulasi Dry-Run sukses')
            ->assertExitCode(0);

        $this->artisan('prisma:migrate-legacy', ['--verify' => true])
            ->expectsOutputToContain('Audit verifikasi selesai')
            ->assertExitCode(0);
    }

    public function test_legacy_migration_web_access_control(): void
    {
        $admin = User::where('email', 'adminp3m@harkatnegeri.ac.id')->first();
        $dosen = User::where('email', 'dosen@harkatnegeri.ac.id')->first();

        // Dosen should be forbidden
        $this->actingAs($dosen)
            ->withSession(['is_otp_verified' => true])
            ->get(route('admin.migrasi.index'))
            ->assertStatus(403);

        // Admin P3M can access
        $this->actingAs($admin)
            ->withSession(['is_otp_verified' => true])
            ->get(route('admin.migrasi.index'))
            ->assertStatus(200)
            ->assertSee('Migrasi Data Legasi SIMPENDI');
    }

    // ==========================================
    // 3. US-13.2 STRESS TESTING & SECURITY HARDENING
    // ==========================================

    public function test_security_headers_middleware_attaches_owasp_headers(): void
    {
        $response = $this->get('/login');

        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $this->assertNotNull($response->headers->get('Content-Security-Policy'));
        $this->assertNotNull($response->headers->get('Permissions-Policy'));
    }

    public function test_storage_security_service_detects_malicious_code_and_magic_bytes(): void
    {
        // 1. Malicious PHP script with .pdf spoofing
        $maliciousPhp = UploadedFile::fake()->createWithContent(
            'exploit.pdf',
            "<?php echo 'Remote Code Execution attempt!'; system(\$_GET['cmd']); ?>"
        );

        $result = StorageSecurityService::validateAndStore($maliciousPhp, 'proposals', 'proposal');
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Anomali keamanan terdeteksi', $result['message']);
    }

    public function test_storage_security_service_blocks_bash_script_upload(): void
    {
        $maliciousBash = UploadedFile::fake()->createWithContent(
            'attack.pdf',
            "#!/bin/bash\nrm -rf /"
        );

        $result = StorageSecurityService::validateAndStore($maliciousBash, 'proposals', 'proposal');
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Anomali keamanan terdeteksi', $result['message']);
    }

    public function test_stress_testing_service_simulates_concurrent_users(): void
    {
        $service = app(StressTestingService::class);
        $result = $service->runStressTest(concurrentUsers: 100);

        $this->assertEquals(100, $result['total_requests']);
        $this->assertEquals(100, $result['success_requests']);
        $this->assertEquals(0, $result['failed_requests']);
        $this->assertLessThan(200.0, $result['avg_latency_ms']);
    }

    public function test_stress_test_artisan_command(): void
    {
        $this->artisan('prisma:stress-test', ['--users' => 50])
            ->expectsOutputToContain('PENGUJIAN BEBAN PUNCAK BERHASIL')
            ->assertExitCode(0);
    }

    // ==========================================
    // 4. US-13.3 DIGITAL UAT, HEALTH CHECK & USER GUIDE
    // ==========================================

    public function test_production_health_check_endpoint_returns_json(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'app_name',
            'version',
            'timestamp',
            'domain',
            'ssl_tls',
            'hsts',
            'services' => [
                'database' => ['status', 'driver', 'latency_ms'],
                'cache' => ['status', 'driver'],
                'storage' => ['status', 'disk'],
            ],
            'governance' => [
                'faculties',
                'study_programs',
                'institution',
            ],
        ]);

        $response->assertJson([
            'status' => 'HEALTHY',
            'services' => [
                'database' => [
                    'status' => 'OK',
                ],
            ],
            'governance' => [
                'faculties' => 4,
                'study_programs' => 22,
            ],
        ]);
    }

    public function test_digital_uat_portal_signoff_and_pdf_export(): void
    {
        $kaprodi = User::where('email', 'kaprodi@harkatnegeri.ac.id')->first();

        // 1. Access UAT portal
        $response = $this->actingAs($kaprodi)
            ->withSession(['is_otp_verified' => true])
            ->get(route('uat.index'));

        $response->assertStatus(200);
        $response->assertSee('Berita Acara UAT');

        // 2. Perform Sign-Off
        $signResponse = $this->actingAs($kaprodi)
            ->withSession(['is_otp_verified' => true])
            ->post(route('uat.sign'), [
                'module' => 'Pendaftaran & Keselarasan Roadmap',
                'status' => 'ACCEPTED',
                'notes' => 'Telah diuji dan disetujui sesuai kurikulum MBKM prodi.',
            ]);

        $signResponse->assertRedirect(route('uat.index'));
        $signResponse->assertSessionHas('success');

        // 3. Download Berita Acara PDF
        $pdfResponse = $this->actingAs($kaprodi)
            ->withSession(['is_otp_verified' => true])
            ->get(route('uat.pdf'));

        $pdfResponse->assertStatus(200);
        $pdfResponse->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringContainsString('Berita_Acara_UAT_KHARISMA_UHN_', $pdfResponse->headers->get('Content-Disposition'));
    }

    public function test_interactive_user_guide_panduan_page_loads_for_all_roles(): void
    {
        $dosen = User::where('email', 'dosen@harkatnegeri.ac.id')->first();
        $reviewer = User::where('email', 'reviewer@harkatnegeri.ac.id')->first();

        // Dosen view
        $this->actingAs($dosen)
            ->withSession(['is_otp_verified' => true])
            ->get(route('panduan.index'))
            ->assertStatus(200)
            ->assertSee('Buku Panduan Pengguna Interaktif')
            ->assertSee('Dosen / Pengusul');

        // Reviewer view
        $this->actingAs($reviewer)
            ->withSession(['is_otp_verified' => true])
            ->get(route('panduan.index'))
            ->assertStatus(200)
            ->assertSee('Reviewer Ilmiah');
    }
}

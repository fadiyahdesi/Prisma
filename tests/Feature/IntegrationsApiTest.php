<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Services\Integrations\DjkiClient;
use App\Services\Integrations\StorageSecurityService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class IntegrationsApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    /** @test */
    public function user_can_view_integrations_dashboard()
    {
        $dosen = User::where('email', 'dosen@harkatnegeri.ac.id')->first();

        $response = $this->actingAs($dosen)
            ->withSession(['is_otp_verified' => true])
            ->get(route('integrasi.index'));

        $response->assertStatus(200);
        $response->assertSee('Integrasi API &amp; Interoperabilitas', false);
        $response->assertSee('SIAKAD Cloud UHN');
        $response->assertSee('SINTA Kemdiktisaintek');
        $response->assertSee('Pangkalan Data DJKI HKI');
        $response->assertSee('MinIO Object Storage');
    }

    /** @test */
    public function djki_hki_client_verifies_or_falls_back_resiliently()
    {
        $djkiClient = new DjkiClient();
        $result = $djkiClient->verifyHki('EC00202518274');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('nomor_permohonan', $result);
        $this->assertArrayHasKey('verified_by', $result);
    }

    /** @test */
    public function storage_security_service_enforces_pdf_mime_type_and_size_limits()
    {
        Storage::fake('public');

        // Valid PDF file under 5MB
        $validPdf = UploadedFile::fake()->create('proposal_valid.pdf', 1024, 'application/pdf');
        $resultValid = StorageSecurityService::validateAndStore($validPdf, 'proposals', 'proposal');
        $this->assertTrue($resultValid['success']);

        // Invalid file type (Image png)
        $invalidImage = UploadedFile::fake()->create('hacker_script.png', 500, 'image/png');
        $resultInvalidMime = StorageSecurityService::validateAndStore($invalidImage, 'proposals', 'proposal');
        $this->assertFalse($resultInvalidMime['success']);
        $this->assertStringContainsString('PDF', $resultInvalidMime['message']);

        // Exceeding 5MB limit (6MB file)
        $oversizedPdf = UploadedFile::fake()->create('huge_proposal.pdf', 6144, 'application/pdf');
        $resultOversized = StorageSecurityService::validateAndStore($oversizedPdf, 'proposals', 'proposal');
        $this->assertFalse($resultOversized['success']);
        $this->assertStringContainsString('melebihi batas maksimal', $resultOversized['message']);
    }
}

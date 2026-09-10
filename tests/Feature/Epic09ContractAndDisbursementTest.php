<?php

namespace Tests\Feature;

use App\Models\PpmKontrak;
use App\Models\PpmPencairanDana;
use App\Models\PpmPeriodeHibah;
use App\Models\PpmSkemaBima;
use App\Models\PpmUsulan;
use App\Models\RefFakultas;
use App\Models\User;
use App\Notifications\DisbursementTermin1Notification;
use App\Notifications\GrantWinnerNotification;
use App\Services\ContractAndDisbursementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class Epic09ContractAndDisbursementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
        Storage::fake('public');
    }

    private function createReviewedProposal(float $rab = 50000000.0, float $score = 620.0): PpmUsulan
    {
        $dosen = User::where('email', 'dosen@harkatnegeri.ac.id')->first();
        $skema = PpmSkemaBima::first();
        $periode = PpmPeriodeHibah::first();

        return PpmUsulan::create([
            'id_pengusul' => $dosen->id,
            'id_skema_bima' => $skema->id,
            'id_periode_hibah' => $periode->id,
            'kode_usulan' => 'BIMA-2026-WIN-' . rand(100, 999),
            'judul_usulan' => 'Pengembangan Prototipe Kendaraan Otonom Listrik Kampus',
            'rumpun_ilmu_level_1' => 'Teknik',
            'fokus_rirn' => 'Transportasi dan Kendaraan Listrik',
            'target_tkt' => 5,
            'ringkasan_substansi' => 'Riset implementasi sistem kendali otonom untuk armada kampus hijau.',
            'total_rab' => $rab,
            'skor_akhir' => $score,
            'status' => 'Reviewed',
            'submitted_at' => now(),
        ]);
    }

    /** @test */
    public function us_09_1_kepala_p3m_can_mass_assign_winners_and_generate_sk()
    {
        $kepala = User::where('email', 'kepalap3m@harkatnegeri.ac.id')->first();
        $u1 = $this->createReviewedProposal(30000000.0, 650.0);
        $u2 = $this->createReviewedProposal(25000000.0, 640.0);

        $response = $this->actingAs($kepala)
            ->withSession(['is_otp_verified' => true])
            ->post(route('admin.kontrak.penetapan-massal'), [
                'usulan_ids' => [$u1->id, $u2->id],
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check usulan status transition
        $this->assertEquals('Contracted', $u1->fresh()->status);
        $this->assertEquals('Contracted', $u2->fresh()->status);

        // Check contracts generated in DB
        $year = date('Y');
        $this->assertDatabaseHas('ppm_kontrak', [
            'id_usulan' => $u1->id,
            'signed_by_kepala' => true,
            'status' => 'pending_signature',
        ]);

        $contract1 = PpmKontrak::where('id_usulan', $u1->id)->first();
        $this->assertStringContainsString("/SK-PEMENANG/P3M-UHN/{$year}", $contract1->nomor_sk);
        $this->assertStringContainsString("/SPK-BIMA/P3M-UHN/{$year}", $contract1->nomor_kontrak);
        $this->assertEquals(21000000.0, (float) $contract1->dana_termin_1); // 70% of 30jt
        $this->assertEquals(9000000.0, (float) $contract1->dana_termin_2);  // 30% of 30jt

        // Check notification created for Dosen
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $u1->id_pengusul,
            'type' => GrantWinnerNotification::class,
        ]);
    }

    /** @test */
    public function us_09_1_unauthorized_roles_cannot_execute_penetapan_massal()
    {
        $dosen = User::where('email', 'dosen@harkatnegeri.ac.id')->first();
        $u1 = $this->createReviewedProposal();

        $response = $this->actingAs($dosen)
            ->withSession(['is_otp_verified' => true])
            ->post(route('admin.kontrak.penetapan-massal'), [
                'usulan_ids' => [$u1->id],
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function us_09_2_public_can_verify_spk_via_qr_token()
    {
        $kepala = User::where('email', 'kepalap3m@harkatnegeri.ac.id')->first();
        $usulan = $this->createReviewedProposal(40000000.0);
        $contracts = ContractAndDisbursementService::penetapanPemenang([$usulan->id], $kepala->id);
        $contract = $contracts[0];

        // Public route doesn't require auth
        $response = $this->get(route('spk.verify', $contract->verification_token));

        $response->assertStatus(200);
        $response->assertSee('DOKUMEN RESMI', false);
        $response->assertSee($contract->nomor_kontrak);
        $response->assertSee($contract->nomor_sk);
        $response->assertSee($usulan->judul_usulan);
        $response->assertSee(hash('sha256', $contract->verification_token));
    }

    /** @test */
    public function us_09_2_dosen_can_view_contract_and_perform_digital_signature()
    {
        $kepala = User::where('email', 'kepalap3m@harkatnegeri.ac.id')->first();
        $dosen = User::where('email', 'dosen@harkatnegeri.ac.id')->first();
        $usulan = $this->createReviewedProposal();
        $contracts = ContractAndDisbursementService::penetapanPemenang([$usulan->id], $kepala->id);
        $contract = $contracts[0];

        // Dosen views their contract portal
        $response = $this->actingAs($dosen)
            ->withSession(['is_otp_verified' => true])
            ->get(route('pengusul.kontrak.index'));

        $response->assertStatus(200);
        $response->assertSee($contract->nomor_kontrak);
        $response->assertSee('Tanda Tangani SPK Digital');

        // Dosen performs digital signature
        $signResponse = $this->actingAs($dosen)
            ->withSession(['is_otp_verified' => true])
            ->post(route('pengusul.kontrak.sign', $contract));

        $signResponse->assertRedirect();
        $signResponse->assertSessionHas('success');

        $contractFresh = $contract->fresh();
        $this->assertTrue($contractFresh->signed_by_pengusul);
        $this->assertNotNull($contractFresh->signed_by_pengusul_at);
        $this->assertEquals('signed', $contractFresh->status);
    }

    /** @test */
    public function us_09_2_spk_pdf_can_be_generated_and_downloaded()
    {
        $kepala = User::where('email', 'kepalap3m@harkatnegeri.ac.id')->first();
        $dosen = User::where('email', 'dosen@harkatnegeri.ac.id')->first();
        $usulan = $this->createReviewedProposal();
        $contracts = ContractAndDisbursementService::penetapanPemenang([$usulan->id], $kepala->id);
        $contract = $contracts[0];

        $response = $this->actingAs($dosen)
            ->withSession(['is_otp_verified' => true])
            ->get(route('kontrak.download-pdf', $contract));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    /** @test */
    public function us_09_3_dosen_can_update_bank_account_with_passbook_upload()
    {
        $kepala = User::where('email', 'kepalap3m@harkatnegeri.ac.id')->first();
        $dosen = User::where('email', 'dosen@harkatnegeri.ac.id')->first();
        $usulan = $this->createReviewedProposal();
        $contracts = ContractAndDisbursementService::penetapanPemenang([$usulan->id], $kepala->id);
        $contract = $contracts[0];

        $dummyFile = UploadedFile::fake()->create('buku_tabungan.pdf', 500, 'application/pdf');

        $response = $this->actingAs($dosen)
            ->withSession(['is_otp_verified' => true])
            ->post(route('pengusul.kontrak.update-rekening', $contract), [
                'nama_bank' => 'Bank Mandiri',
                'nomor_rekening' => '1080019283746',
                'nama_pemilik_rekening' => 'Dr. Ir. Hendra Prasetya, M.T.',
                'file_buku_tabungan' => $dummyFile,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $contractFresh = $contract->fresh();
        $this->assertEquals('Bank Mandiri', $contractFresh->nama_bank);
        $this->assertEquals('1080019283746', $contractFresh->nomor_rekening);
        $this->assertEquals('Dr. Ir. Hendra Prasetya, M.T.', $contractFresh->nama_pemilik_rekening);
        $this->assertNotNull($contractFresh->file_buku_tabungan);
        Storage::disk('public')->assertExists($contractFresh->file_buku_tabungan);
    }

    /** @test */
    public function us_09_3_keuangan_can_verify_rekening_and_disburse_termin_1_70_percent()
    {
        $kepala = User::where('email', 'kepalap3m@harkatnegeri.ac.id')->first();
        $keuangan = User::where('email', 'keuangan@harkatnegeri.ac.id')->first();
        $usulan = $this->createReviewedProposal(50000000.0);
        $contracts = ContractAndDisbursementService::penetapanPemenang([$usulan->id], $kepala->id);
        $contract = $contracts[0];

        // Simulate Dosen signed contract and uploaded bank details
        $contract->update([
            'signed_by_pengusul' => true,
            'signed_by_pengusul_at' => now(),
            'nama_bank' => 'BNI',
            'nomor_rekening' => '0298371625',
            'nama_pemilik_rekening' => 'Dr. Ir. Hendra Prasetya',
            'file_buku_tabungan' => 'passbooks/' . $contract->id . '/sample.pdf',
        ]);

        // 1. Divisi Keuangan accesses disbursement index and show
        $indexResponse = $this->actingAs($keuangan)
            ->withSession(['is_otp_verified' => true])
            ->get(route('keuangan.pencairan.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee($contract->nomor_kontrak);

        $showResponse = $this->actingAs($keuangan)
            ->withSession(['is_otp_verified' => true])
            ->get(route('keuangan.pencairan.show', $contract));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('0298371625');

        // 2. Divisi Keuangan verifies bank account
        $verifyResponse = $this->actingAs($keuangan)
            ->withSession(['is_otp_verified' => true])
            ->post(route('keuangan.pencairan.verify-rekening', $contract));
        $verifyResponse->assertRedirect();
        $this->assertEquals($keuangan->id, $contract->fresh()->rekening_verified_by);
        $this->assertNotNull($contract->fresh()->rekening_verified_at);

        // 3. Divisi Keuangan disburses Termin 1 (70%)
        $transferProof = UploadedFile::fake()->create('bukti_transfer_termin1.pdf', 300, 'application/pdf');
        $disburseResponse = $this->actingAs($keuangan)
            ->withSession(['is_otp_verified' => true])
            ->post(route('keuangan.pencairan.disburse-termin-1', $contract), [
                'nomor_referensi' => 'TRF-BNI-2026-009182',
                'tanggal_transfer' => now()->toDateString(),
                'catatan' => 'Pencairan 70% Tahap 1 Hibah BIMA Harkat Negeri.',
                'file_bukti_transfer' => $transferProof,
            ]);

        $disburseResponse->assertRedirect();
        $disburseResponse->assertSessionHas('success');

        // Verify Pencairan Dana record in DB
        $this->assertDatabaseHas('ppm_pencairan_dana', [
            'id_kontrak' => $contract->id,
            'termin' => 1,
            'persentase' => 70.00,
            'jumlah_dana' => 35000000.0, // 70% of 50jt
            'nomor_referensi' => 'TRF-BNI-2026-009182',
            'status_pencairan' => 'transferred',
            'processed_by' => $keuangan->id,
        ]);

        // Verify usulan transitions to Ongoing
        $this->assertEquals('Ongoing', $usulan->fresh()->status);

        // Verify Dosen received disbursement notification
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $usulan->id_pengusul,
            'type' => DisbursementTermin1Notification::class,
        ]);
    }

    /** @test */
    public function epic_09_sidebar_shows_authorized_links_for_dosen_and_keuangan()
    {
        $dosen = User::where('email', 'dosen@harkatnegeri.ac.id')->first();
        $keuangan = User::where('email', 'keuangan@harkatnegeri.ac.id')->first();
        $reviewer = User::where('email', 'reviewer@harkatnegeri.ac.id')->first();

        // Dosen should see Kontrak & Pencairan
        $dosenRes = $this->actingAs($dosen)
            ->withSession(['is_otp_verified' => true])
            ->get(route('dashboard'));
        $dosenRes->assertSee(route('pengusul.kontrak.index'));
        $dosenRes->assertDontSee(route('keuangan.pencairan.index'));

        // Keuangan should see Pencairan Dana Hibah
        $keuRes = $this->actingAs($keuangan)
            ->withSession(['is_otp_verified' => true])
            ->get(route('dashboard'));
        $keuRes->assertSee(route('keuangan.pencairan.index'));
        $keuRes->assertDontSee(route('pengusul.kontrak.index'));
        $keuRes->assertDontSee(route('usulan.index'));

        // Reviewer should see neither
        $revRes = $this->actingAs($reviewer)
            ->withSession(['is_otp_verified' => true])
            ->get(route('dashboard'));
        $revRes->assertDontSee(route('pengusul.kontrak.index'));
        $revRes->assertDontSee(route('keuangan.pencairan.index'));
    }
}

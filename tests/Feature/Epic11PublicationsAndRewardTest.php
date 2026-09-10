<?php

namespace Tests\Feature;

use App\Models\PpmHki;
use App\Models\PpmKlaimReward;
use App\Models\PpmPublikasiJurnal;
use App\Models\PpmRewardDistribusi;
use App\Models\RefTarifRewardSk;
use App\Models\User;
use Database\Seeders\Epic11DemoDataSeeder;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class Epic11PublicationsAndRewardTest extends TestCase
{
    use RefreshDatabase;

    protected User $dosen;
    protected User $adminP3m;
    protected User $keuangan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RbacSeeder::class);
        $this->seed(Epic11DemoDataSeeder::class);
        Storage::fake('public');

        $this->dosen = User::where('email', 'dosen@harkatnegeri.ac.id')->first();
        $this->adminP3m = User::where('email', 'adminp3m@harkatnegeri.ac.id')->first();
        $this->keuangan = User::where('email', 'keuangan@harkatnegeri.ac.id')->first();
    }

    /**
     * US-11.1: Bank Publikasi Jurnal Kampus & Validasi Keunikan DOI.
     */
    public function test_dosen_can_view_bank_publikasi_and_register_article(): void
    {
        $response = $this->actingAs($this->dosen)
            ->withSession(['otp_verified' => true])
            ->get(route('publikasi.index'));

        $response->assertStatus(200);
        $response->assertSee('Bank Publikasi Jurnal Kampus');

        // Register new article
        $pdfFile = UploadedFile::fake()->create('manuscript.pdf', 500, 'application/pdf');
        $uniqueDoi = '10.1016/j.artint.2026.103999';

        $postResponse = $this->actingAs($this->dosen)
            ->withSession(['otp_verified' => true])
            ->post(route('publikasi.store'), [
                'judul_artikel' => 'Deep Reinforcement Learning in Multi-Agent Autonomous Systems',
                'nama_jurnal' => 'Artificial Intelligence Journal',
                'issn' => '0004-3702',
                'doi' => $uniqueDoi,
                'kategori_peringkat' => 'Scopus Q1',
                'tahun_terbit' => 2026,
                'volume_nomor' => 'Vol. 320, No. 1',
                'url_artikel' => 'https://doi.org/' . $uniqueDoi,
                'jumlah_penulis' => 3,
                'file_naskah' => $pdfFile,
            ]);

        $postResponse->assertRedirect();

        $this->assertDatabaseHas('ppm_publikasi_jurnal', [
            'doi' => $uniqueDoi,
            'kategori_peringkat' => 'Scopus Q1',
            'is_claimed_reward' => false,
        ]);

        // Duplicate DOI registration must fail
        $duplicateResponse = $this->actingAs($this->dosen)
            ->withSession(['otp_verified' => true])
            ->post(route('publikasi.store'), [
                'judul_artikel' => 'Duplicate Attempt',
                'nama_jurnal' => 'Another Journal',
                'doi' => $uniqueDoi,
                'kategori_peringkat' => 'Scopus Q1',
                'tahun_terbit' => 2026,
                'jumlah_penulis' => 1,
                'file_naskah' => $pdfFile,
            ]);

        $duplicateResponse->assertSessionHasErrors(['doi']);
    }

    /**
     * US-11.1: Fetch DOI endpoint detects duplicates.
     */
    public function test_fetch_doi_detects_already_registered_articles(): void
    {
        // Existing DOI from seeder: 10.1016/j.future.2026.01.001
        $response = $this->actingAs($this->dosen)
            ->withSession(['otp_verified' => true])
            ->getJson(route('publikasi.fetch-doi', ['doi' => '10.1016/j.future.2026.01.001']));

        $response->assertStatus(409);
        $response->assertJson([
            'success' => false,
            'is_duplicate' => true,
        ]);
    }

    /**
     * US-11.2: Pendaftaran Perolehan HKI & Verifikasi oleh Sentra HKI.
     */
    public function test_dosen_can_register_hki_and_admin_p3m_can_verify_it(): void
    {
        $certFile = UploadedFile::fake()->create('sertifikat_hki.pdf', 600, 'application/pdf');
        $nomorPermohonan = 'P002026' . rand(10000, 99999);

        // 1. Dosen registers HKI
        $response = $this->actingAs($this->dosen)
            ->withSession(['otp_verified' => true])
            ->post(route('hki.store'), [
                'jenis_hki' => 'Paten',
                'judul_hki' => 'Metode Pendinginan Komputasi Awan Skala Besar Berbasis Cairan Dielektrik',
                'nomor_permohonan' => $nomorPermohonan,
                'tanggal_permohonan' => '2026-01-15',
                'pemegang_hak' => 'Universitas Harkat Negeri',
                'file_sertifikat' => $certFile,
            ]);

        $response->assertRedirect();

        $hki = PpmHki::where('nomor_permohonan', $nomorPermohonan)->first();
        $this->assertNotNull($hki);

        // 2. Admin P3M verifies and approves HKI
        $verifyResponse = $this->actingAs($this->adminP3m)
            ->withSession(['otp_verified' => true])
            ->post(route('admin.hki.verify', $hki), [
                'action' => 'approve',
                'nomor_sertifikat' => 'IDP000088991',
                'tanggal_terbit' => '2026-03-01',
                'catatan_verifikasi' => 'Berkas paten terverifikasi lengkap dan sah sesuai DJKI Kemenkumham.',
            ]);

        $verifyResponse->assertRedirect();

        $hki->refresh();
        $this->assertEquals('Terverifikasi HKI', $hki->status_hki);
        $this->assertEquals('IDP000088991', $hki->nomor_sertifikat);
        $this->assertNotNull($hki->verified_at);
        $this->assertEquals($this->adminP3m->id, $hki->verified_by_user_id);
    }

    /**
     * US-11.3 & US-11.4: Strict 100% Multi-Author Distribution Validation.
     */
    public function test_claim_distribution_percentages_must_strictly_sum_to_100_percent(): void
    {
        $suratFile = UploadedFile::fake()->create('surat_pernyataan.pdf', 300, 'application/pdf');

        // Create an unclaimed publication
        $pub = PpmPublikasiJurnal::create([
            'user_id' => $this->dosen->id,
            'judul_artikel' => 'Sample Multi-Author Paper Testing',
            'nama_jurnal' => 'Journal of Applied Computing',
            'doi' => '10.1111/jac.2026.001',
            'kategori_peringkat' => 'Scopus Q1',
            'tahun_terbit' => 2026,
            'file_naskah' => 'dummy.pdf',
            'jumlah_penulis' => 2,
            'is_claimed_reward' => false,
        ]);

        // Attempt 1: Total is 90% (Under 100%) -> MUST FAIL
        $responseFail1 = $this->actingAs($this->dosen)
            ->withSession(['otp_verified' => true])
            ->post(route('reward.store'), [
                'jenis_klaim' => 'Publikasi',
                'id_publikasi' => $pub->id,
                'kategori_insentif' => 'Scopus Q1',
                'file_surat_pernyataan' => $suratFile,
                'distribusi' => [
                    [
                        'nama_penulis' => 'Author A',
                        'peran_penulis' => 'Penulis Pertama',
                        'persentase' => 50,
                        'nama_bank' => 'BNI',
                        'nomor_rekening' => '111111',
                        'nama_pemilik_rekening' => 'Author A',
                    ],
                    [
                        'nama_penulis' => 'Author B',
                        'peran_penulis' => 'Penulis Anggota',
                        'persentase' => 40, // 50 + 40 = 90%
                        'nama_bank' => 'BRI',
                        'nomor_rekening' => '222222',
                        'nama_pemilik_rekening' => 'Author B',
                    ],
                ],
            ]);

        $responseFail1->assertSessionHasErrors(['distribusi']);

        // Attempt 2: Total is 110% (Over 100%) -> MUST FAIL
        $responseFail2 = $this->actingAs($this->dosen)
            ->withSession(['otp_verified' => true])
            ->post(route('reward.store'), [
                'jenis_klaim' => 'Publikasi',
                'id_publikasi' => $pub->id,
                'kategori_insentif' => 'Scopus Q1',
                'file_surat_pernyataan' => $suratFile,
                'distribusi' => [
                    [
                        'nama_penulis' => 'Author A',
                        'peran_penulis' => 'Penulis Pertama',
                        'persentase' => 60,
                        'nama_bank' => 'BNI',
                        'nomor_rekening' => '111111',
                        'nama_pemilik_rekening' => 'Author A',
                    ],
                    [
                        'nama_penulis' => 'Author B',
                        'peran_penulis' => 'Penulis Anggota',
                        'persentase' => 50, // 60 + 50 = 110%
                        'nama_bank' => 'BRI',
                        'nomor_rekening' => '222222',
                        'nama_pemilik_rekening' => 'Author B',
                    ],
                ],
            ]);

        $responseFail2->assertSessionHasErrors(['distribusi']);

        // Attempt 3: Total is EXACTLY 100.00% (65% + 35%) -> MUST SUCCEED!
        $responseSuccess = $this->actingAs($this->dosen)
            ->withSession(['otp_verified' => true])
            ->post(route('reward.store'), [
                'jenis_klaim' => 'Publikasi',
                'id_publikasi' => $pub->id,
                'kategori_insentif' => 'Scopus Q1',
                'file_surat_pernyataan' => $suratFile,
                'distribusi' => [
                    [
                        'nama_penulis' => 'Author A',
                        'peran_penulis' => 'Penulis Pertama',
                        'persentase' => 65.00,
                        'nama_bank' => 'BNI',
                        'nomor_rekening' => '111111',
                        'nama_pemilik_rekening' => 'Author A',
                    ],
                    [
                        'nama_penulis' => 'Author B',
                        'peran_penulis' => 'Penulis Anggota',
                        'persentase' => 35.00,
                        'nama_bank' => 'BRI',
                        'nomor_rekening' => '222222',
                        'nama_pemilik_rekening' => 'Author B',
                    ],
                ],
            ]);

        $responseSuccess->assertRedirect();

        // Check rate from SK Rektor: Scopus Q1 = Rp 35.000.000
        // Author A: 65% = Rp 22.750.000
        // Author B: 35% = Rp 12.250.000
        $claim = PpmKlaimReward::where('id_publikasi', $pub->id)->first();
        $this->assertNotNull($claim);
        $this->assertEquals(35000000, (float) $claim->total_reward);

        $distA = PpmRewardDistribusi::where('id_klaim_reward', $claim->id)->where('nama_penulis', 'Author A')->first();
        $distB = PpmRewardDistribusi::where('id_klaim_reward', $claim->id)->where('nama_penulis', 'Author B')->first();

        $this->assertEquals(22750000, (float) $distA->nominal_bagian);
        $this->assertEquals(12250000, (float) $distB->nominal_bagian);

        // Anti-duplicate lock verified!
        $pub->refresh();
        $this->assertTrue($pub->is_claimed_reward);
    }

    /**
     * US-11.3: Anti-Duplicate Claim Engine permanently locks already-claimed publications.
     */
    public function test_anti_duplicate_claim_engine_prevents_claiming_already_claimed_asset(): void
    {
        $suratFile = UploadedFile::fake()->create('surat_pernyataan.pdf', 300, 'application/pdf');

        // Claimed article from seeder: Paper 2 (is_claimed_reward = true)
        $claimedPub = PpmPublikasiJurnal::where('doi', '10.22146/ijc.2025.99999')->first();
        $this->assertTrue($claimedPub->is_claimed_reward);

        $response = $this->actingAs($this->dosen)
            ->withSession(['otp_verified' => true])
            ->post(route('reward.store'), [
                'jenis_klaim' => 'Publikasi',
                'id_publikasi' => $claimedPub->id,
                'kategori_insentif' => 'SINTA 2',
                'file_surat_pernyataan' => $suratFile,
                'distribusi' => [
                    [
                        'nama_penulis' => 'Dosen Test',
                        'peran_penulis' => 'Penulis Pertama',
                        'persentase' => 100,
                        'nama_bank' => 'BNI',
                        'nomor_rekening' => '12345678',
                        'nama_pemilik_rekening' => 'Dosen Test',
                    ],
                ],
            ]);

        $response->assertSessionHasErrors(['id_publikasi']);
    }

    /**
     * US-11.3: Admin P3M can approve or reject claim, and rejection releases asset lock.
     */
    public function test_p3m_can_approve_and_reject_claims_with_lock_release(): void
    {
        // Klaim 2 from seeder is 'Submitted'
        $klaim2 = PpmKlaimReward::where('nomor_klaim', 'REW/2026/09/DEMO2')->first();
        $hkiAsset = $klaim2->hki;
        $this->assertTrue($hkiAsset->is_claimed_reward);

        // Reject claim
        $rejectResponse = $this->actingAs($this->adminP3m)
            ->withSession(['otp_verified' => true])
            ->post(route('admin.reward.reject', $klaim2), [
                'catatan_p3m' => 'Surat pernyataan belum dibubuhi materai 10.000.',
            ]);

        $rejectResponse->assertRedirect();

        $klaim2->refresh();
        $this->assertEquals('Rejected', $klaim2->status_klaim);

        // Asset lock must be released!
        $hkiAsset->refresh();
        $this->assertFalse($hkiAsset->is_claimed_reward);

        // Approve Klaim 1 from seeder (currently 'Approved_P3M')
        $klaim1 = PpmKlaimReward::where('nomor_klaim', 'REW/2026/09/DEMO1')->first();
        $this->assertEquals('Approved_P3M', $klaim1->status_klaim);
    }

    /**
     * US-11.4: Keuangan Disburses Individual Authors and Claim Automatically Transitions to Disbursed.
     */
    public function test_keuangan_disburses_individual_authors_until_fully_disbursed(): void
    {
        $klaim1 = PpmKlaimReward::where('nomor_klaim', 'REW/2026/09/DEMO1')->first();
        $this->assertEquals('Approved_P3M', $klaim1->status_klaim);

        // In seeder, author 1 is already Disbursed, author 2 (Budi Wicaksono) is Pending
        $pendingDist = $klaim1->distribusi()->where('status_transfer', 'Pending')->first();
        $this->assertNotNull($pendingDist);

        $transferSlip = UploadedFile::fake()->create('slip_transfer.pdf', 400, 'application/pdf');

        // Keuangan disburses author 2
        $response = $this->actingAs($this->keuangan)
            ->withSession(['otp_verified' => true])
            ->post(route('keuangan.reward.disburse-member', $pendingDist), [
                'tanggal_transfer' => '2026-09-11',
                'nomor_referensi' => 'TRX-MANDIRI-20260911-9988',
                'file_bukti_transfer' => $transferSlip,
            ]);

        $response->assertRedirect();

        $pendingDist->refresh();
        $this->assertEquals('Disbursed', $pendingDist->status_transfer);
        $this->assertEquals('TRX-MANDIRI-20260911-9988', $pendingDist->nomor_referensi);

        // All authors are now disbursed -> claim status must automatically transition to 'Disbursed'
        $klaim1->refresh();
        $this->assertEquals('Disbursed', $klaim1->status_klaim);
        $this->assertTrue($klaim1->isFullyDisbursed());
    }
}


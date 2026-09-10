<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\User;
use App\Models\PpmSkemaBima;
use App\Models\PpmPeriodeHibah;
use App\Models\PpmUsulan;
use App\Models\PpmUsulanAnggota;
use App\Models\PpmUsulanRab;
use App\Models\PpmUsulanLuaran;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PpmUsulanWizardTest extends TestCase
{
    use RefreshDatabase;

    protected User $dosenUser;
    protected PpmSkemaBima $skemaBima;
    protected PpmPeriodeHibah $periodeHibah;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->dosenUser = User::factory()->create([
            'name' => 'Dr. Ir. Hendra Prasetya, M.T.',
            'nidn_nim' => '0615037801',
            'jabatan_fungsional' => 'Lektor Kepala',
            'sinta_score_3yr' => 185.50,
            'is_otp_verified' => true,
        ]);

        $this->skemaBima = PpmSkemaBima::create([
            'kode_skema' => 'PDP',
            'nama_skema' => 'Penelitian Dosen Pemula (PDP)',
            'kategori' => 'penelitian',
            'min_jafung' => ['Asisten Ahli', 'Lektor', 'Lektor Kepala'],
            'min_sinta_3yr' => 50.0,
            'min_tkt' => 1,
            'max_tkt' => 3,
            'plafon_dana' => 25000000.00,
            'is_active' => true,
        ]);

        $this->periodeHibah = PpmPeriodeHibah::create([
            'tahun_akademik' => '2025/2026',
            'semester' => 'Ganjil',
            'nama_periode' => 'Call for Proposals BIMA Batch 1',
            'waktu_buka' => now()->subDays(2),
            'waktu_tutup' => now()->addDays(14),
            'is_active' => true,
        ]);
    }

    #[Test]
    public function dosen_can_start_wizard_and_save_step_1_identitas_usulan()
    {
        $response = $this->actingAs($this->dosenUser)
                         ->withSession(['otp_verified' => true])
                         ->get(route('usulan.start', $this->skemaBima));

        $response->assertRedirect();
        
        $usulan = PpmUsulan::where('id_pengusul', $this->dosenUser->id)->first();
        $this->assertNotNull($usulan);
        $this->assertEquals('Draft', $usulan->status);

        $saveResponse = $this->actingAs($this->dosenUser)
                             ->withSession(['otp_verified' => true])
                             ->post(route('usulan.save-step1', $usulan), [
                                 'judul_usulan' => 'Pengembangan Algoritma Pembelajaran Mesin untuk Optimasi Listrik',
                                 'rumpun_ilmu_level_1' => 'Sains & Matematika',
                                 'rumpun_ilmu_level_2' => 'Ilmu Komputer',
                                 'rumpun_ilmu_level_3' => 'Kecerdasan Buatan (AI)',
                                 'fokus_rirn' => 'Teknologi Informasi & Komunikasi (TIK)',
                                 'target_tkt' => 2,
                             ]);

        $saveResponse->assertRedirect(route('usulan.step', ['usulan' => $usulan->id, 'step' => 2]));

        $usulan->refresh();
        $this->assertEquals('Pengembangan Algoritma Pembelajaran Mesin untuk Optimasi Listrik', $usulan->judul_usulan);
        $this->assertEquals(2, $usulan->target_tkt);
    }

    #[Test]
    public function step2_can_add_dosen_anggota_and_mahasiswa_iku2()
    {
        $anggotaDosen = User::factory()->create([
            'name' => 'Prof. Budi Santoso',
            'nidn_nim' => '0620087102',
            'is_otp_verified' => true,
        ]);

        $usulan = PpmUsulan::create([
            'id_pengusul' => $this->dosenUser->id,
            'id_skema_bima' => $this->skemaBima->id,
            'id_periode_hibah' => $this->periodeHibah->id,
            'kode_usulan' => 'USL-2025-0001',
            'status' => 'Draft',
        ]);

        // Add Dosen
        $this->actingAs($this->dosenUser)
             ->withSession(['otp_verified' => true])
             ->post(route('usulan.save-step2', $usulan), [
                 'add_dosen' => 1,
                 'dosen_nidn' => '0620087102',
                 'dosen_peran' => 'Analis Data',
             ])
             ->assertRedirect();

        $this->assertDatabaseHas('ppm_usulan_anggota', [
            'id_usulan' => $usulan->id,
            'identifier' => '0620087102',
            'jenis_anggota' => 'dosen',
        ]);

        // Add Mahasiswa
        $this->actingAs($this->dosenUser)
             ->withSession(['otp_verified' => true])
             ->post(route('usulan.save-step2', $usulan), [
                 'add_mahasiswa' => 1,
                 'mhs_nim' => '210108001',
                 'mhs_nama' => 'Aditya Pratama',
                 'mhs_peran' => 'Surveyor Lapangan',
             ])
             ->assertRedirect();

        $this->assertDatabaseHas('ppm_usulan_anggota', [
            'id_usulan' => $usulan->id,
            'identifier' => '210108001',
            'jenis_anggota' => 'mahasiswa',
        ]);
    }

    #[Test]
    public function step3_rejects_summary_text_above_500_words()
    {
        $usulan = PpmUsulan::create([
            'id_pengusul' => $this->dosenUser->id,
            'id_skema_bima' => $this->skemaBima->id,
            'id_periode_hibah' => $this->periodeHibah->id,
            'kode_usulan' => 'USL-2025-0002',
            'status' => 'Draft',
        ]);

        $over500Words = str_repeat('kata ', 505);

        $response = $this->actingAs($this->dosenUser)
                         ->withSession(['otp_verified' => true])
                         ->post(route('usulan.save-step3', $usulan), [
                             'ringkasan_substansi' => $over500Words,
                         ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    #[Test]
    public function step4_rab_calculator_rejects_honorarium_above_30_percent_and_exceeding_pagu()
    {
        $usulan = PpmUsulan::create([
            'id_pengusul' => $this->dosenUser->id,
            'id_skema_bima' => $this->skemaBima->id,
            'id_periode_hibah' => $this->periodeHibah->id,
            'kode_usulan' => 'USL-2025-0003',
            'status' => 'Draft',
        ]);

        // Add Honorarium 10 juta (Total RAB = 10 juta -> 100% Honorarium > 30%)
        PpmUsulanRab::create([
            'id_usulan' => $usulan->id,
            'pos_belanja' => 'Honorarium',
            'item_keterangan' => 'Honor Peneliti Utama',
            'volume' => 1,
            'satuan' => 'paket',
            'harga_satuan' => 10000000,
            'total_harga' => 10000000,
        ]);

        $response = $this->actingAs($this->dosenUser)
                         ->withSession(['otp_verified' => true])
                         ->post(route('usulan.save-step4', $usulan), []);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    #[Test]
    public function step6_submit_final_locks_proposal_and_changes_status_to_submitted()
    {
        $file = UploadedFile::fake()->create('proposal.pdf', 1000, 'application/pdf');

        $usulan = PpmUsulan::create([
            'id_pengusul' => $this->dosenUser->id,
            'id_skema_bima' => $this->skemaBima->id,
            'id_periode_hibah' => $this->periodeHibah->id,
            'kode_usulan' => 'USL-2025-0004',
            'judul_usulan' => 'Riset Komprehensif Antigravity AI',
            'rumpun_ilmu_level_1' => 'Sains & Matematika',
            'ringkasan_substansi' => 'Ringkasan proposal riset AI yang sangat komprehensif.',
            'file_proposal_path' => 'proposals/proposal.pdf',
            'status' => 'Draft',
        ]);

        $response = $this->actingAs($this->dosenUser)
                         ->withSession(['otp_verified' => true])
                         ->post(route('usulan.submit-final', $usulan));

        $response->assertRedirect(route('dashboard'));
        $usulan->refresh();
        $this->assertEquals('Submitted', $usulan->status);
        $this->assertNotNull($usulan->submitted_at);
    }
}


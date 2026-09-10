<?php

namespace Tests\Feature;

use App\Models\PpmPeriodeHibah;
use App\Models\PpmSkemaBima;
use App\Models\User;
use App\Services\BimaPeriodService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BimaSchemeAndPeriodTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    #[Test]
    public function admin_can_view_and_create_bima_scheme(): void
    {
        $user = User::factory()->create(['is_otp_verified' => true, 'sinta_score_3yr' => 200, 'nidn_nim' => '0699999001']);

        $response = $this->actingAs($user)->get(route('admin.skema-bima.index'));
        $response->assertStatus(200);
        $response->assertSee('Penelitian Dosen Pemula (PDP)');

        $storeResponse = $this->actingAs($user)->post(route('admin.skema-bima.store'), [
            'kode_skema' => 'RISPRO_INTERNAL',
            'nama_skema' => 'Rispro Hibah Internal UHN',
            'kategori' => 'penelitian',
            'min_jafung' => ['Lektor', 'Lektor Kepala'],
            'min_sinta_3yr' => 100,
            'min_tkt' => 2,
            'max_tkt' => 6,
            'plafon_dana' => 75000000,
        ]);

        $storeResponse->assertRedirect(route('admin.skema-bima.index'));
        $this->assertDatabaseHas('ppm_skema_bima', [
            'kode_skema' => 'RISPRO_INTERNAL',
            'plafon_dana' => 75000000,
        ]);
    }

    #[Test]
    public function admin_can_toggle_scheme_active_status(): void
    {
        $user = User::factory()->create(['is_otp_verified' => true, 'nidn_nim' => '0699999002']);
        $scheme = PpmSkemaBima::where('kode_skema', 'PDP')->first();

        $this->assertTrue($scheme->is_active);

        $response = $this->actingAs($user)->post(route('admin.skema-bima.toggle', $scheme));
        $response->assertRedirect(route('admin.skema-bima.index'));

        $this->assertFalse($scheme->fresh()->is_active);
    }

    #[Test]
    public function rubric_weight_validation_rejects_sum_not_100_percent(): void
    {
        $user = User::factory()->create(['is_otp_verified' => true, 'nidn_nim' => '0699999003']);
        $scheme = PpmSkemaBima::where('kode_skema', 'PDP')->first();

        $response = $this->actingAs($user)->post(route('admin.skema-bima.update-rubrik', $scheme), [
            'rubrik' => [
                [
                    'kriteria' => 'Kriteria 1',
                    'bobot' => 40,
                    'deskripsi' => 'Deskripsi 1',
                ],
                [
                    'kriteria' => 'Kriteria 2',
                    'bobot' => 40, // Total = 80% (INVALID)
                    'deskripsi' => 'Deskripsi 2',
                ],
            ],
        ]);

        $response->assertSessionHas('error');
        $this->assertNotEquals(80, array_sum(array_column($scheme->fresh()->rubrik_penilaian, 'bobot')));
    }

    #[Test]
    public function rubric_weight_validation_accepts_sum_equal_to_100_percent(): void
    {
        $user = User::factory()->create(['is_otp_verified' => true, 'nidn_nim' => '0699999004']);
        $scheme = PpmSkemaBima::where('kode_skema', 'PDP')->first();

        $response = $this->actingAs($user)->post(route('admin.skema-bima.update-rubrik', $scheme), [
            'rubrik' => [
                [
                    'kriteria' => 'Rekam Jejak & Kualifikasi Pengusul',
                    'bobot' => 30,
                    'deskripsi' => 'H-Index & SINTA 3Yr',
                ],
                [
                    'kriteria' => 'Kebaharuan & State-of-the-Art',
                    'bobot' => 40,
                    'deskripsi' => 'Urgensi masalah',
                ],
                [
                    'kriteria' => 'Target Luaran & Kelayakan RAB SBM',
                    'bobot' => 30, // Total = 100% (VALID)
                    'deskripsi' => 'Target Jurnal & RAB SBM',
                ],
            ],
        ]);

        $response->assertRedirect(route('admin.skema-bima.index'));
        $response->assertSessionHas('success');

        $updatedRubrik = $scheme->fresh()->rubrik_penilaian;
        $this->assertEquals(100, array_sum(array_column($updatedRubrik, 'bobot')));
        $this->assertCount(3, $updatedRubrik);
    }

    #[Test]
    public function admin_can_create_periode_hibah_and_scheduler_calculates_countdown(): void
    {
        $user = User::factory()->create(['is_otp_verified' => true, 'nidn_nim' => '0699999005']);

        $response = $this->actingAs($user)->post(route('admin.periode-hibah.store'), [
            'tahun_akademik' => '2026/2027',
            'semester' => 'Ganjil',
            'nama_periode' => 'Call for Proposals Batch 2 2026/2027',
            'waktu_buka' => now()->subDay()->format('Y-m-d H:i:s'),
            'waktu_tutup' => now()->addDays(15)->format('Y-m-d H:i:s'),
            'keterangan' => 'Periode Pengajuan Baru',
        ]);

        $response->assertRedirect(route('admin.periode-hibah.index'));
        $this->assertDatabaseHas('ppm_periode_hibah', [
            'nama_periode' => 'Call for Proposals Batch 2 2026/2027',
        ]);

        $service = app(BimaPeriodService::class);
        $countdown = $service->getCountdownDetails();

        $this->assertTrue($countdown['has_active_period']);
        $this->assertTrue($countdown['is_open']);
        $this->assertEquals('PERIODE DIBUKA', $countdown['status_label']);
    }
}

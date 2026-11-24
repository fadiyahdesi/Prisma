<?php

namespace Tests\Feature;

use App\Models\PpmPublikasiJurnal;
use App\Models\RefTarifRewardSk;
use App\Models\User;
use App\Services\RewardDistributionService;
use App\Support\PedomanKriteria;
use Database\Seeders\Epic11DemoDataSeeder;
use Database\Seeders\RbacSeeder;
use Database\Seeders\TestingDemoAccountsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PedomanKriteriaTest extends TestCase
{
    use RefreshDatabase;

    protected User $dosen;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RbacSeeder::class);
        $this->seed(TestingDemoAccountsSeeder::class);
        $this->seed(Epic11DemoDataSeeder::class);

        $this->dosen = User::where('email', 'dosen@harkatnegeri.ac.id')->first();
    }

    public function test_tarif_reward_mengikuti_matriks_dispo_ppm_2026(): void
    {
        $this->assertSame(15000000.0, (float) RefTarifRewardSk::where('sub_kategori', 'Scopus Q1')->value('nominal_insentif'));
        $this->assertSame(10000000.0, (float) RefTarifRewardSk::where('sub_kategori', 'Scopus Q2')->value('nominal_insentif'));
        $this->assertSame(7500000.0, (float) RefTarifRewardSk::where('sub_kategori', 'Paten')->value('nominal_insentif'));
        $this->assertSame(5000000.0, (float) RefTarifRewardSk::where('sub_kategori', 'Paten Sederhana')->value('nominal_insentif'));

        $keterangan = RefTarifRewardSk::where('sub_kategori', 'Paten')->value('keterangan');
        $this->assertStringContainsString('1 klaim per tahun', $keterangan);
        $this->assertStringContainsString('40', $keterangan);

        $this->assertSame(0, RefTarifRewardSk::where('sub_kategori', 'SINTA 4')->where('is_active', true)->count());
        $this->assertSame(0, RefTarifRewardSk::where('sub_kategori', 'Hak Cipta')->where('is_active', true)->count());
    }

    public function test_kategori_lama_dinormalisasi_ke_pedoman_2026(): void
    {
        $this->assertSame('Prosiding Internasional (Scopus/DOAJ/CABI)', PedomanKriteria::aliasReward('Internasional'));
        $this->assertSame('SINTA 3', PedomanKriteria::aliasReward('Nasional Terakreditasi'));
        $this->assertSame('Paten', PedomanKriteria::aliasReward('Paten Granted'));

        $tarif = app(RewardDistributionService::class)->getTarif('Publikasi', 'Internasional');
        $this->assertNotNull($tarif);
        $this->assertSame(3500000.0, (float) $tarif->nominal_insentif);
    }

    public function test_form_klaim_menampilkan_matriks_pedoman_2026(): void
    {
        $this->actingAs($this->dosen)
            ->withSession(['otp_verified' => true])
            ->get(route('reward.create'))
            ->assertOk()
            ->assertSee('Matriks Kriteria & Besaran Reward Luaran 2026')
            ->assertSee('Rp 15.000.000')
            ->assertSee('Rp 7.500.000');
    }

    public function test_batas_klaim_per_semester_ditegakkan(): void
    {
        // Epic11DemoDataSeeder sudah membuat 1 klaim SINTA 2 untuk dosen ini pada semester berjalan.
        $publikasi = PpmPublikasiJurnal::create([
            'user_id' => $this->dosen->id,
            'judul_artikel' => 'Artikel Uji Batas Klaim SINTA 2',
            'nama_jurnal' => 'Jurnal Uji Pedoman',
            'doi' => '10.99999/pedoman.2026.batas.' . uniqid(),
            'file_naskah' => 'publikasi_naskah/sample_paper.pdf',
            'kategori_peringkat' => 'SINTA 2',
            'tahun_terbit' => now()->year,
            'penulis' => $this->dosen->name,
            'jumlah_penulis' => 1,
            'peran_penulis' => 'Penulis Pertama',
            'is_claimed_reward' => false,
            'status_verifikasi' => 'Verified',
        ]);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Batas klaim');

        app(RewardDistributionService::class)->submitClaim($this->dosen, [
            'jenis_klaim' => 'Publikasi',
            'id_publikasi' => $publikasi->id,
            'kategori_insentif' => 'SINTA 2',
            'distribusi' => [[
                'nama_penulis' => $this->dosen->name,
                'peran_penulis' => 'Penulis Pertama',
                'persentase' => 100,
                'nama_bank' => 'BNI',
                'nomor_rekening' => '123456',
                'nama_pemilik_rekening' => $this->dosen->name,
            ]],
        ]);
    }

    public function test_skor_monev_dihitung_dari_status_dan_bobot_item(): void
    {
        $penilaianDasar = [
            'c1' => [
                'status' => 60,
                'items' => ['i1' => 100, 'i2' => 75, 'i3' => 100, 'i4' => 75],
            ],
            'c2' => [
                'status' => 20,
                'items' => ['i1' => 100],
            ],
        ];

        $this->assertSame(72.5, PedomanKriteria::hitungSkorMonev('PDP', $penilaianDasar));
        $this->assertSame(72.5, PedomanKriteria::hitungSkorMonev('FUNDAMENTAL', $penilaianDasar));

        $penilaianTerapan = [
            'c1' => [
                'status' => 80,
                'items' => ['i1' => 100, 'i2' => 100, 'i3' => 100, 'i4' => 100],
            ],
            'c2' => ['status' => 10, 'items' => ['i1' => 100]],
            'c3' => ['status' => 10, 'items' => ['i1' => 100, 'i2' => 100]],
        ];

        $this->assertSame(100.0, PedomanKriteria::hitungSkorMonev('HILIRISASI', $penilaianTerapan));
    }
}

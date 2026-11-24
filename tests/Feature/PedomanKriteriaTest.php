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

    public function test_indikator_monev_hanya_dipasang_untuk_skema_pkm(): void
    {
        $pkm = PedomanKriteria::monev('PKM');

        $this->assertSame('Program Kemitraan Masyarakat (PKM)', $pkm['nama_skema']);
        $this->assertSame(60, $pkm['kelompok']['umum']['maks']);
        $this->assertSame(40, $pkm['kelompok']['khusus']['maks']);
        $this->assertSame(5, $pkm['komponen']['u3']['pembagi']);
        $this->assertCount(7, $pkm['komponen']['k2']['items']);

        foreach (['PDP', 'FUNDAMENTAL', 'HILIRISASI', 'PDB', 'PW'] as $kode) {
            $this->assertSame([], PedomanKriteria::monev($kode)['komponen']);
        }
    }

    public function test_skor_monev_dihitung_dari_nilai_sub_indikator_pkm(): void
    {
        $penilaianMaksimal = [];
        foreach (PedomanKriteria::monev('PKM')['komponen'] as $ck => $komponen) {
            foreach ($komponen['items'] as $ik => $item) {
                $penilaianMaksimal[$ck]['items'][$ik] = max(array_values($item['penilaian']));
            }
        }
        $this->assertSame(100.0, PedomanKriteria::hitungSkorMonev('PKM', $penilaianMaksimal));

        $penilaianSebagian = [
            'u1' => ['items' => ['i1' => 10, 'i2' => 10, 'i3' => 10, 'i4' => 10, 'i5' => 10]],
            'u2' => ['items' => ['i1' => 70]],
            'u3' => ['items' => ['i1' => 50]],
            'u5' => ['items' => ['i1' => 40, 'i2' => 15, 'i3' => 15]],
            'u6' => ['items' => ['i1' => 40, 'i2' => 30, 'i3' => 30]],
            'k1' => ['items' => ['i1' => 70]],
            'k2' => ['items' => ['i1' => 25, 'i2' => 25, 'i3' => 12, 'i4' => 12, 'i5' => 15, 'i6' => 20, 'i7' => 20]],
        ];
        $this->assertSame(58.9, PedomanKriteria::hitungSkorMonev('PKM', $penilaianSebagian));
    }
}

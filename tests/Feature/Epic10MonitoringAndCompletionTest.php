<?php

namespace Tests\Feature;

use App\Models\PpmKontrak;
use App\Models\PpmLaporanAkhir;
use App\Models\PpmLogbook;
use App\Models\PpmMonevKemajuan;
use App\Models\PpmPencairanDana;
use App\Models\PpmPeriodeHibah;
use App\Models\PpmSeminarHasil;
use App\Models\PpmSkemaBima;
use App\Models\PpmUsulan;
use App\Models\User;
use App\Notifications\DisbursementTermin2Notification;
use App\Notifications\MonevEvaluatedNotification;
use App\Notifications\SemhasScheduledNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class Epic10MonitoringAndCompletionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
        Storage::fake('public');
    }

    /**
     * Helper to create an ongoing contracted proposal with Termin 1 disbursed.
     */
    private function createOngoingProposal(float $pagu = 30000000.0): array
    {
        $dosen = User::where('email', 'dosen@harkatnegeri.ac.id')->first();
        $skema = PpmSkemaBima::first();
        $periode = PpmPeriodeHibah::first();
        $keuangan = User::where('email', 'keuangan@harkatnegeri.ac.id')->first();

        $usulan = PpmUsulan::create([
            'id_pengusul' => $dosen->id,
            'id_skema_bima' => $skema->id,
            'id_periode_hibah' => $periode->id,
            'kode_usulan' => 'BIMA-2026-EP10-' . rand(100, 999),
            'judul_usulan' => 'Implementasi Sistem Kendali Cerdas Berbasis IoT dan AI',
            'rumpun_ilmu_level_1' => 'Teknik',
            'fokus_rirn' => 'Teknologi Informasi dan Komunikasi',
            'target_tkt' => 5,
            'ringkasan_substansi' => 'Riset implementasi smart sensor pada otomasi industri terpadu.',
            'total_rab' => $pagu,
            'dana_disetujui' => $pagu,
            'status' => 'Ongoing',
            'submitted_at' => now(),
        ]);

        $kontrak = PpmKontrak::create([
            'id_usulan' => $usulan->id,
            'nomor_sk' => 'SK-PEMENANG/P3M-UHN/' . date('Y') . '/' . $usulan->id,
            'nomor_kontrak' => 'SPK-BIMA/P3M-UHN/' . date('Y') . '/' . $usulan->id,
            'tanggal_sk' => now(),
            'tanggal_kontrak' => now(),
            'pagu_disetujui' => $pagu,
            'dana_termin_1' => $pagu * 0.70,
            'dana_termin_2' => $pagu * 0.30,
            'signed_by_kepala' => true,
            'signed_by_kepala_at' => now(),
            'signed_by_pengusul' => true,
            'signed_by_pengusul_at' => now(),
            'nomor_rekening' => '1234567890',
            'nama_bank' => 'Bank Mandiri',
            'nama_pemilik_rekening' => $dosen->name,
            'file_buku_tabungan' => 'tabungan/sample.pdf',
            'rekening_verified_at' => now(),
            'rekening_verified_by' => $keuangan->id,
            'status' => 'ongoing',
            'verification_token' => bin2hex(random_bytes(32)),
        ]);

        $pencairan1 = PpmPencairanDana::create([
            'id_kontrak' => $kontrak->id,
            'termin' => 1,
            'persentase' => 70.00,
            'jumlah_dana' => $kontrak->dana_termin_1,
            'nomor_referensi' => 'TRF-T1-2026-001',
            'tanggal_transfer' => now(),
            'status_pencairan' => 'transferred',
            'processed_by' => $keuangan->id,
            'processed_at' => now(),
        ]);

        return compact('usulan', 'kontrak', 'pencairan1', 'dosen', 'keuangan');
    }

    public function test_us_10_1_dosen_can_create_view_export_and_delete_logbook()
    {
        extract($this->createOngoingProposal());

        // 1. Create Logbook Entry
        $photo = UploadedFile::fake()->image('kegiatan1.jpg', 600, 400);

        $response = $this->actingAs($dosen)
            ->withSession(['is_otp_verified' => true])
            ->post(route('pengusul.logbook.store', $usulan), [
                'tanggal' => date('Y-m-d'),
                'aktivitas' => 'Melakukan pengujian sensor suhu dan kalibrasi mikrokontroler di laboratorium.',
                'persentase_capaian' => 35.0,
                'file_bukti' => $photo,
            ]);

        $response->assertRedirect(route('pengusul.logbook.show', $usulan));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('ppm_logbook', [
            'id_usulan' => $usulan->id,
            'persentase_capaian' => 35.0,
            'created_by' => $dosen->id,
        ]);

        $logbook = PpmLogbook::where('id_usulan', $usulan->id)->first();
        $this->assertNotNull($logbook->file_bukti);
        Storage::disk('public')->assertExists($logbook->file_bukti);

        // 2. View Logbook Index & Timeline
        $viewResponse = $this->actingAs($dosen)
            ->withSession(['is_otp_verified' => true])
            ->get(route('pengusul.logbook.show', $usulan));

        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('Kronologi Rekam Jejak Pelaksanaan');
        $viewResponse->assertSee('Melakukan pengujian sensor suhu');
        $viewResponse->assertSee('35.0%');

        // 3. Download Formal Logbook PDF
        $pdfResponse = $this->actingAs($dosen)
            ->withSession(['is_otp_verified' => true])
            ->get(route('pengusul.logbook.download-pdf', $usulan));

        $pdfResponse->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $pdfResponse->headers->get('Content-Type'));

        // 4. Delete Logbook Entry
        $deleteResponse = $this->actingAs($dosen)
            ->withSession(['is_otp_verified' => true])
            ->delete(route('pengusul.logbook.destroy', $logbook));

        $deleteResponse->assertRedirect(route('pengusul.logbook.show', $usulan));
        $this->assertDatabaseMissing('ppm_logbook', ['id' => $logbook->id]);
    }

    public function test_us_10_1_unauthorized_user_cannot_access_or_modify_other_logbook()
    {
        extract($this->createOngoingProposal());
        $otherDosen = User::factory()->create(['is_otp_verified' => true]);
        $roleDosen = \App\Models\Role::where('name', 'Dosen / Pengusul')->first();
        if ($roleDosen) {
            $otherDosen->roles()->attach($roleDosen);
        }

        $response = $this->actingAs($otherDosen)
            ->withSession(['is_otp_verified' => true])
            ->post(route('pengusul.logbook.store', $usulan), [
                'tanggal' => date('Y-m-d'),
                'aktivitas' => 'Percobaan ilegal pada riset orang lain.',
                'persentase_capaian' => 20.0,
            ]);

        $response->assertStatus(403);
    }

    public function test_us_10_2_dosen_can_upload_laporan_kemajuan_and_sptb_70()
    {
        extract($this->createOngoingProposal());

        $fileKemajuan = UploadedFile::fake()->create('laporan_kemajuan_70.pdf', 1024, 'application/pdf');
        $fileSptb70 = UploadedFile::fake()->create('sptb_70.pdf', 512, 'application/pdf');

        $response = $this->actingAs($dosen)
            ->withSession(['is_otp_verified' => true])
            ->post(route('pengusul.monev.store', $usulan), [
                'file_laporan_kemajuan' => $fileKemajuan,
                'file_sptb_70' => $fileSptb70,
                'persentase_kemajuan' => 72.5,
                'ringkasan_kemajuan' => 'Pengambilan data responden 50 sampel telah rampung dan prototype awal berhasil dirakit.',
            ]);

        $response->assertRedirect(route('pengusul.monev.show', $usulan));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('ppm_monev_kemajuan', [
            'id_usulan' => $usulan->id,
            'persentase_kemajuan' => 72.5,
            'status' => 'submitted',
        ]);

        $monev = PpmMonevKemajuan::where('id_usulan', $usulan->id)->first();
        Storage::disk('public')->assertExists($monev->file_laporan_kemajuan);
        Storage::disk('public')->assertExists($monev->file_sptb_70);
    }

    public function test_us_10_2_reviewer_can_evaluate_monev_and_notify_dosen()
    {
        extract($this->createOngoingProposal());
        $reviewer = User::where('email', 'reviewer@harkatnegeri.ac.id')->first();

        // Proposer submits monev
        $monev = PpmMonevKemajuan::create([
            'id_usulan' => $usulan->id,
            'file_laporan_kemajuan' => 'monev/sample_kemajuan.pdf',
            'file_sptb_70' => 'monev/sample_sptb70.pdf',
            'persentase_kemajuan' => 70.0,
            'ringkasan_kemajuan' => 'Capaian 70% telah menyelesaikan analisis spektrum awal.',
            'status' => 'submitted',
        ]);

        // Reviewer evaluates monev
        $response = $this->actingAs($reviewer)
            ->withSession(['is_otp_verified' => true])
            ->post(route('reviewer.monev.store', $monev), [
                'skor_monev' => 87.5,
                'rekomendasi' => 'Lanjut',
                'catatan_evaluasi' => 'Kemajuan lapangan sangat memuaskan, data primer lengkap, siap lanjut ke Semhas.',
            ]);

        $response->assertRedirect(route('reviewer.monev.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('ppm_monev_kemajuan', [
            'id' => $monev->id,
            'id_reviewer' => $reviewer->id,
            'skor_monev' => 87.5,
            'rekomendasi' => 'Lanjut',
            'status' => 'evaluated',
        ]);

        // Check Notification for Dosen
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $dosen->id,
            'type' => MonevEvaluatedNotification::class,
        ]);
    }

    public function test_us_10_3_p3m_can_schedule_semhas_and_record_examiner_grades()
    {
        extract($this->createOngoingProposal());
        $adminP3m = User::where('email', 'adminp3m@harkatnegeri.ac.id')->first();
        $reviewer = User::where('email', 'reviewer@harkatnegeri.ac.id')->first();
        // 0. Admin visits Semhas index page
        $indexResponse = $this->actingAs($adminP3m)
            ->withSession(['is_otp_verified' => true])
            ->get(route('admin.semhas.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Seminar Hasil');

        // 1. Admin schedules Semhas
        $jadwal = now()->addDays(5)->format('Y-m-d\TH:i');
        $scheduleResponse = $this->actingAs($adminP3m)
            ->withSession(['is_otp_verified' => true])
            ->post(route('admin.semhas.schedule', $usulan), [
                'jadwal_seminar' => $jadwal,
                'ruangan_or_link' => 'Ruang Sidang Utama LPPM Gedung A',
                'id_penguji_1' => $reviewer->id,
            ]);

        $scheduleResponse->assertRedirect();
        $scheduleResponse->assertSessionHas('success');

        $this->assertDatabaseHas('ppm_seminar_hasil', [
            'id_usulan' => $usulan->id,
            'ruangan_or_link' => 'Ruang Sidang Utama LPPM Gedung A',
            'id_penguji_1' => $reviewer->id,
            'status_seminar' => 'scheduled',
        ]);

        // Check Notification for Dosen
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $dosen->id,
            'type' => SemhasScheduledNotification::class,
        ]);

        // 2. Grade Semhas
        $semhas = PpmSeminarHasil::where('id_usulan', $usulan->id)->first();
        $gradeResponse = $this->actingAs($adminP3m)
            ->withSession(['is_otp_verified' => true])
            ->post(route('admin.semhas.grade', $semhas), [
                'skor_seminar' => 91.0,
                'catatan_penguji' => 'Pemaparan sangat baik, masukkan saran perbaikan pada bab pembahasan.',
            ]);

        $gradeResponse->assertRedirect();
        $this->assertDatabaseHas('ppm_seminar_hasil', [
            'id' => $semhas->id,
            'skor_seminar' => 91.0,
            'status_seminar' => 'completed',
        ]);
    }

    public function test_us_10_3_dosen_can_upload_laporan_akhir_and_verify_qr_publicly()
    {
        extract($this->createOngoingProposal());

        $fileAkhir = UploadedFile::fake()->create('laporan_akhir_100.pdf', 2048, 'application/pdf');
        $fileSptb100 = UploadedFile::fake()->create('sptb_100.pdf', 512, 'application/pdf');

        // 1. Proposer uploads final report
        $uploadResponse = $this->actingAs($dosen)
            ->withSession(['is_otp_verified' => true])
            ->post(route('pengusul.laporan-akhir.store', $usulan), [
                'file_laporan_akhir' => $fileAkhir,
                'file_sptb_100' => $fileSptb100,
                'ringkasan_hasil' => 'Seluruh target luaran wajib artikel jurnal terindeks SINTA 2 telah submitted.',
            ]);

        $uploadResponse->assertRedirect(route('pengusul.laporan-akhir.show', $usulan));
        $uploadResponse->assertSessionHas('success');

        $this->assertDatabaseHas('ppm_laporan_akhir', [
            'id_usulan' => $usulan->id,
            'is_approved_p3m' => true,
        ]);

        $laporan = PpmLaporanAkhir::where('id_usulan', $usulan->id)->first();
        $this->assertNotNull($laporan->verification_token);
        Storage::disk('public')->assertExists($laporan->file_laporan_akhir);

        // 2. Download Lembar Pengesahan PDF ber-QR Code
        $pdfResponse = $this->actingAs($dosen)
            ->withSession(['is_otp_verified' => true])
            ->get(route('laporan-akhir.download-pengesahan', $usulan));

        $pdfResponse->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $pdfResponse->headers->get('Content-Type'));

        // 3. Public Verification without Auth
        $publicResponse = $this->get(route('laporan-akhir.verify', $laporan->verification_token));
        $publicResponse->assertStatus(200);
        $publicResponse->assertSee('DOKUMEN RESMI & TERVALIDASI SISTEM PRISMA LPPM UHN', false);
        $publicResponse->assertSee($usulan->judul_usulan);
        $publicResponse->assertSee($dosen->name);
    }

    public function test_us_10_4_keuangan_can_disburse_termin_2_and_complete_grant()
    {
        extract($this->createOngoingProposal(40000000.0));

        // Create Laporan Akhir first
        PpmLaporanAkhir::create([
            'id_usulan' => $usulan->id,
            'file_laporan_akhir' => 'laporan-akhir/sample.pdf',
            'file_sptb_100' => 'laporan-akhir/sptb100.pdf',
            'ringkasan_hasil' => 'Luaran wajib 100% tercapai.',
            'verification_token' => bin2hex(random_bytes(32)),
            'is_approved_p3m' => true,
            'approved_by_p3m_at' => now(),
        ]);

        $buktiT2 = UploadedFile::fake()->create('bukti_transfer_termin2.pdf', 512, 'application/pdf');

        // Keuangan disburses Termin 2 (30% = 12.000.000)
        $response = $this->actingAs($keuangan)
            ->withSession(['is_otp_verified' => true])
            ->post(route('keuangan.pencairan.disburse-termin-2', $kontrak), [
                'nomor_referensi' => 'TRF-T2-PELUNASAN-2026-99',
                'tanggal_transfer' => date('Y-m-d'),
                'file_bukti_transfer' => $buktiT2,
                'catatan' => 'Pelunasan hibah 100% setelah pemeriksaan naskah akhir dan LPJ lengkap.',
            ]);

        $response->assertRedirect(route('keuangan.pencairan.show', $kontrak));
        $response->assertSessionHas('success');

        // Check Pencairan Termin 2
        $this->assertDatabaseHas('ppm_pencairan_dana', [
            'id_kontrak' => $kontrak->id,
            'termin' => 2,
            'persentase' => 30.00,
            'jumlah_dana' => 12000000.0,
            'nomor_referensi' => 'TRF-T2-PELUNASAN-2026-99',
            'status_pencairan' => 'transferred',
        ]);

        // Check Usulan & Kontrak status transitioned to Completed
        $this->assertEquals('completed', $kontrak->fresh()->status);
        $this->assertEquals('Completed', $usulan->fresh()->status);

        // Check Proposer Notification
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $dosen->id,
            'type' => DisbursementTermin2Notification::class,
        ]);

        // Download Tanda Bukti Pelunasan Hibah (PDF)
        $pdfResponse = $this->actingAs($keuangan)
            ->withSession(['is_otp_verified' => true])
            ->get(route('keuangan.pencairan.pelunasan-pdf', $kontrak));

        $pdfResponse->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $pdfResponse->headers->get('Content-Type'));
    }

    public function test_us_10_4_cannot_disburse_termin_2_if_laporan_akhir_missing()
    {
        extract($this->createOngoingProposal());

        // Laporan akhir is not created yet
        $response = $this->actingAs($keuangan)
            ->withSession(['is_otp_verified' => true])
            ->post(route('keuangan.pencairan.disburse-termin-2', $kontrak), [
                'nomor_referensi' => 'TRF-FAIL-001',
                'tanggal_transfer' => date('Y-m-d'),
            ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('ppm_pencairan_dana', [
            'id_kontrak' => $kontrak->id,
            'termin' => 2,
        ]);
        $this->assertEquals('Ongoing', $usulan->fresh()->status);
    }
}

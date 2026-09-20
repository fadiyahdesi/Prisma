<?php

namespace Database\Seeders;

use App\Models\PpmPeriodeHibah;
use App\Models\PpmSkemaBima;
use App\Models\PpmUsulan;
use App\Models\PpmUsulanAnggota;
use App\Models\User;
use App\Notifications\MemberInvitationNotification;
use App\Services\DemoPdfGeneratorService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MemberConsentDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Get or create users
        $dosen1 = User::where('email', 'dosen@harkatnegeri.ac.id')->first();
        $dosen2 = User::where('email', 'dosen2@harkatnegeri.ac.id')->first();
        $dosen3 = User::where('email', 'dosen3@harkatnegeri.ac.id')->first();
        $mhs1   = User::where('email', 'mahasiswa1@harkatnegeri.ac.id')->first();
        $mhs2   = User::where('email', 'mahasiswa2@harkatnegeri.ac.id')->first();

        $skemaPtu = PpmSkemaBima::where('kode_skema', 'PTU')->first() ?? PpmSkemaBima::first();
        $skemaPdp = PpmSkemaBima::where('kode_skema', 'PDP')->first() ?? PpmSkemaBima::first();
        $skemaPkm = PpmSkemaBima::where('kode_skema', 'PKM')->first() ?? PpmSkemaBima::first();
        $periode  = PpmPeriodeHibah::where('is_active', true)->first() ?? PpmPeriodeHibah::first();

        $pdfService = app(DemoPdfGeneratorService::class);

        // =====================================================================
        // PROPOSAL 1: Ketua = Dr. Ir. Hendra Prasetya (dosen@harkatnegeri.ac.id)
        // Anggota yang diundang: Mahasiswa 1 (pending), Dosen 3 (pending), Mahasiswa 2 (rejected), Dosen 2 (approved)
        // =====================================================================
        $usulan1 = PpmUsulan::updateOrCreate(
            ['kode_usulan' => 'USL-2026-CONSENT-01'],
            [
                'id_pengusul' => $dosen1->id,
                'id_skema_bima' => $skemaPtu->id,
                'id_periode_hibah' => $periode->id,
                'judul_usulan' => 'Pengembangan Sistem Deteksi Dini Banjir Berbasis AI & IoT pada DAS Citarum',
                'rumpun_ilmu_level_1' => 'Teknik',
                'rumpun_ilmu_level_2' => 'Teknik Informatika',
                'rumpun_ilmu_level_3' => 'Sistem Cerdas & IoT',
                'fokus_rirn' => 'Kebencanaan & Lingkungan',
                'target_tkt' => 5,
                'ringkasan_substansi' => 'Riset ini bertujuan merancang arsitektur telemetri sensor ketinggian air, debit air, dan curah hujan secara terdistribusi yang diolah dengan model deep learning LSTM untuk prediksi potensi banjir 6 jam sebelum kejadian.',
                'status' => 'Draft',
                'total_rab' => 48500000,
                'total_honorarium' => 12000000,
                'file_proposal_path' => 'proposals/usulan_consent_01.pdf',
                'mitra_surat_kesediaan_path' => 'mitra/sample_surat_kesediaan.pdf',
            ]
        );

        $pdfService->generateProposal([
            'judul_usulan' => $usulan1->judul_usulan,
            'skema' => 'Penelitian Terapan Unggulan (PTU)',
            'ketua_nama' => $dosen1->name,
            'ketua_nidn' => $dosen1->nidn ?? '0620087101',
            'total_rab' => 48500000,
        ], 'proposals/usulan_consent_01.pdf');

        PpmUsulanAnggota::where('id_usulan', $usulan1->id)->delete();

        // Mahasiswa 1: Pending
        $ang1 = PpmUsulanAnggota::create([
            'id_usulan' => $usulan1->id,
            'user_id' => $mhs1->id,
            'jenis_anggota' => 'mahasiswa',
            'nama' => $mhs1->name,
            'identifier' => '210108001',
            'peran_anggota' => 'Pengembangan Firmware Sensor IoT & Kalibrasi Telemetri',
            'status_persetujuan' => 'pending',
        ]);
        $mhs1->notify(new MemberInvitationNotification($ang1));

        // Dosen 3 (Anita Wijaya): Pending
        $ang2 = PpmUsulanAnggota::create([
            'id_usulan' => $usulan1->id,
            'user_id' => $dosen3->id,
            'jenis_anggota' => 'dosen',
            'nama' => $dosen3->name,
            'identifier' => '0620087103',
            'peran_anggota' => 'Analis Algoritma Machine Learning & Model Prediksi LSTM',
            'status_persetujuan' => 'pending',
        ]);
        $dosen3->notify(new MemberInvitationNotification($ang2));

        // Mahasiswa 2: Rejected
        PpmUsulanAnggota::create([
            'id_usulan' => $usulan1->id,
            'user_id' => $mhs2->id,
            'jenis_anggota' => 'mahasiswa',
            'nama' => $mhs2->name,
            'identifier' => '210108002',
            'peran_anggota' => 'Asisten Validasi Data Lapangan & Pengujian Akurasi AI',
            'status_persetujuan' => 'rejected',
        ]);

        // Dosen 2: Approved
        PpmUsulanAnggota::create([
            'id_usulan' => $usulan1->id,
            'user_id' => $dosen2->id,
            'jenis_anggota' => 'dosen',
            'nama' => $dosen2->name,
            'identifier' => '0620087102',
            'peran_anggota' => 'Spesialis Pemrosesan Citra Satelit & Pemetaan GIS',
            'status_persetujuan' => 'approved',
            'approved_at' => now()->subDay(),
        ]);

        // =====================================================================
        // PROPOSAL 2: Ketua = Dr. Ahmad Fauzi (dosen2@harkatnegeri.ac.id)
        // MENGUNDANG: Dr. Ir. Hendra Prasetya (PENDING) & Mahasiswa 1 (PENDING)
        // =====================================================================
        $usulan2 = PpmUsulan::updateOrCreate(
            ['kode_usulan' => 'USL-2026-CONSENT-02'],
            [
                'id_pengusul' => $dosen2->id,
                'id_skema_bima' => $skemaPkm->id,
                'id_periode_hibah' => $periode->id,
                'judul_usulan' => 'Penerapan Green Technology Pengolahan Limbah Kulit Kopi Menjadi Biogas pada Kelompok Tani Mandiri',
                'rumpun_ilmu_level_1' => 'Pertanian',
                'rumpun_ilmu_level_2' => 'Teknologi Pertanian',
                'rumpun_ilmu_level_3' => 'Energi Terbarukan Pedesaan',
                'fokus_rirn' => 'Energi Bersih Terbarukan',
                'target_tkt' => 6,
                'ringkasan_substansi' => 'Pengabdian masyarakat ini menerapkan biodigester portabel skala komunal untuk mengolah limbah pulp kopi basah menjadi biogas bersih dan pupuk organik cair di Desa Lumban Suhi-Suhi.',
                'status' => 'Draft',
                'total_rab' => 24000000,
                'total_honorarium' => 4500000,
                'file_proposal_path' => 'proposals/usulan_consent_02.pdf',
                'mitra_surat_kesediaan_path' => 'mitra/sample_surat_kesediaan.pdf',
            ]
        );

        $pdfService->generateProposal([
            'judul_usulan' => $usulan2->judul_usulan,
            'skema' => 'Pemberdayaan Kemitraan Masyarakat (PKM)',
            'ketua_nama' => $dosen2->name,
            'ketua_nidn' => $dosen2->nidn ?? '0620087102',
            'total_rab' => 24000000,
        ], 'proposals/usulan_consent_02.pdf');

        PpmUsulanAnggota::where('id_usulan', $usulan2->id)->delete();

        // Dr. Ir. Hendra Prasetya: PENDING!
        $angHend1 = PpmUsulanAnggota::create([
            'id_usulan' => $usulan2->id,
            'user_id' => $dosen1->id,
            'jenis_anggota' => 'dosen',
            'nama' => $dosen1->name,
            'identifier' => '0620087101',
            'peran_anggota' => 'Pakar Sistem Telemetri IoT & Otomasi Kontrol Biodigester',
            'status_persetujuan' => 'pending',
        ]);
        $dosen1->notify(new MemberInvitationNotification($angHend1));

        // Mahasiswa 1: PENDING
        $angMhs1 = PpmUsulanAnggota::create([
            'id_usulan' => $usulan2->id,
            'user_id' => $mhs1->id,
            'jenis_anggota' => 'mahasiswa',
            'nama' => $mhs1->name,
            'identifier' => '210108001',
            'peran_anggota' => 'Instalasi Sensor Suhu & Tekanan Biodigester Biogas',
            'status_persetujuan' => 'pending',
        ]);
        $mhs1->notify(new MemberInvitationNotification($angMhs1));

        // =====================================================================
        // PROPOSAL 3: Ketua = Anita Wijaya, S.T., M.Eng. (dosen3@harkatnegeri.ac.id)
        // MENGUNDANG: Dr. Ir. Hendra Prasetya (PENDING) & Mahasiswa 2 (PENDING)
        // =====================================================================
        $usulan3 = PpmUsulan::updateOrCreate(
            ['kode_usulan' => 'USL-2026-CONSENT-03'],
            [
                'id_pengusul' => $dosen3->id,
                'id_skema_bima' => $skemaPtu->id,
                'id_periode_hibah' => $periode->id,
                'judul_usulan' => 'Pengembangan Aplikasi Mobile Edu-Health untuk Pemantauan Tumbuh Kembang Balita Stunting',
                'rumpun_ilmu_level_1' => 'Kesehatan & Rekayasa',
                'rumpun_ilmu_level_2' => 'Informatika Medis',
                'rumpun_ilmu_level_3' => 'Aplikasi Kesehatan Bergerak',
                'fokus_rirn' => 'Kesehatan & Obat',
                'target_tkt' => 6,
                'ringkasan_substansi' => 'Riset terapan pembuatan aplikasi cerdas deteksi dini risiko stunting balita berbasis kurva pertumbuhan WHO terintegrasi kader Posyandu dan Puskesmas.',
                'status' => 'Draft',
                'total_rab' => 38000000,
                'total_honorarium' => 8000000,
                'file_proposal_path' => 'proposals/usulan_consent_03.pdf',
                'mitra_surat_kesediaan_path' => 'mitra/sample_surat_kesediaan.pdf',
            ]
        );

        $pdfService->generateProposal([
            'judul_usulan' => $usulan3->judul_usulan,
            'skema' => 'Penelitian Terapan Unggulan (PTU)',
            'ketua_nama' => $dosen3->name,
            'ketua_nidn' => $dosen3->nidn ?? '0620087103',
            'total_rab' => 38000000,
        ], 'proposals/usulan_consent_03.pdf');

        PpmUsulanAnggota::where('id_usulan', $usulan3->id)->delete();

        // Dr. Ir. Hendra Prasetya: PENDING!
        $angHend2 = PpmUsulanAnggota::create([
            'id_usulan' => $usulan3->id,
            'user_id' => $dosen1->id,
            'jenis_anggota' => 'dosen',
            'nama' => $dosen1->name,
            'identifier' => '0620087101',
            'peran_anggota' => 'Pakar Arsitektur Sistem Cerdas & Keamanan Data Medis',
            'status_persetujuan' => 'pending',
        ]);
        $dosen1->notify(new MemberInvitationNotification($angHend2));

        // Mahasiswa 2: PENDING
        $angMhs2 = PpmUsulanAnggota::create([
            'id_usulan' => $usulan3->id,
            'user_id' => $mhs2->id,
            'jenis_anggota' => 'mahasiswa',
            'nama' => $mhs2->name,
            'identifier' => '210108002',
            'peran_anggota' => 'Pengembang Desain Antarmuka Mobile Flutter & UAT',
            'status_persetujuan' => 'pending',
        ]);
        $mhs2->notify(new MemberInvitationNotification($angMhs2));

        // =====================================================================
        // PROPOSAL 4: Ketua = Dr. Ahmad Fauzi (dosen2@harkatnegeri.ac.id)
        // MENGUNDANG: Dr. Ir. Hendra Prasetya (PENDING)
        // =====================================================================
        $usulan4 = PpmUsulan::updateOrCreate(
            ['kode_usulan' => 'USL-2026-CONSENT-04'],
            [
                'id_pengusul' => $dosen2->id,
                'id_skema_bima' => $skemaPtu->id,
                'id_periode_hibah' => $periode->id,
                'judul_usulan' => 'Prototipe Mini Turbin Angin Sumbu Vertikal (VAWT) Efisiensi Tinggi untuk Kawasan Pesisir Terpencil',
                'rumpun_ilmu_level_1' => 'Teknik',
                'rumpun_ilmu_level_2' => 'Teknik Mesin & Elektro',
                'rumpun_ilmu_level_3' => 'Pembangkit Energi Baru Terbarukan',
                'fokus_rirn' => 'Energi Terbarukan',
                'target_tkt' => 5,
                'ringkasan_substansi' => 'Rancang bangun prototipe turbin angin vertikal dengan bilah aerodinamis NACA 0018 untuk membangkitkan listrik di daerah kecepatan angin rendah 3-5 m/s.',
                'status' => 'Draft',
                'total_rab' => 45000000,
                'total_honorarium' => 10000000,
                'file_proposal_path' => 'proposals/usulan_consent_04.pdf',
                'mitra_surat_kesediaan_path' => 'mitra/sample_surat_kesediaan.pdf',
            ]
        );

        $pdfService->generateProposal([
            'judul_usulan' => $usulan4->judul_usulan,
            'skema' => 'Penelitian Terapan Unggulan (PTU)',
            'ketua_nama' => $dosen2->name,
            'ketua_nidn' => $dosen2->nidn ?? '0620087102',
            'total_rab' => 45000000,
        ], 'proposals/usulan_consent_04.pdf');

        PpmUsulanAnggota::where('id_usulan', $usulan4->id)->delete();

        // Dr. Ir. Hendra Prasetya: PENDING!
        $angHend3 = PpmUsulanAnggota::create([
            'id_usulan' => $usulan4->id,
            'user_id' => $dosen1->id,
            'jenis_anggota' => 'dosen',
            'nama' => $dosen1->name,
            'identifier' => '0620087101',
            'peran_anggota' => 'Pakar Konversi Daya Listrik & Instrumentasi Sensor Mikro',
            'status_persetujuan' => 'pending',
        ]);
        $dosen1->notify(new MemberInvitationNotification($angHend3));
    }
}

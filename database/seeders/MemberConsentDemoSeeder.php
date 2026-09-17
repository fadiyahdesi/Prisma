<?php

namespace Database\Seeders;

use App\Models\PpmPeriodeHibah;
use App\Models\PpmSkemaBima;
use App\Models\PpmUsulan;
use App\Models\PpmUsulanAnggota;
use App\Models\User;
use App\Notifications\MemberInvitationNotification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MemberConsentDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Get or create users
        $ketua1 = User::where('email', 'dosen@harkatnegeri.ac.id')->first();
        $ketua2 = User::where('email', 'dosen2@harkatnegeri.ac.id')->first();
        $mhs1 = User::where('email', 'mahasiswa1@harkatnegeri.ac.id')->first();
        $mhs2 = User::where('email', 'mahasiswa2@harkatnegeri.ac.id')->first();
        $dosen3 = User::where('email', 'dosen3@harkatnegeri.ac.id')->first();

        $skemaPtu = PpmSkemaBima::where('kode_skema', 'PTU')->first() ?? PpmSkemaBima::first();
        $skemaPkm = PpmSkemaBima::where('kode_skema', 'PKM')->first() ?? PpmSkemaBima::first();
        $periode = PpmPeriodeHibah::where('is_active', true)->first() ?? PpmPeriodeHibah::first();

        // 2. Proposal 1: Ketua Dr. Ir. Hendra Prasetya
        $usulan1 = PpmUsulan::updateOrCreate(
            ['kode_usulan' => 'USL-2026-CONSENT-01'],
            [
                'id_pengusul' => $ketua1->id,
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
            ]
        );

        // Clear existing anggota for clean state
        PpmUsulanAnggota::where('id_usulan', $usulan1->id)->delete();

        // Anggota 1: Rizky Pratama (Mahasiswa 1) - PENDING
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

        // Anggota 2: Anita Wijaya (Dosen 3) - PENDING
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

        // Anggota 3: Siti Rahmawati (Mahasiswa 2) - REJECTED
        $ang3 = PpmUsulanAnggota::create([
            'id_usulan' => $usulan1->id,
            'user_id' => $mhs2->id,
            'jenis_anggota' => 'mahasiswa',
            'nama' => $mhs2->name,
            'identifier' => '210108002',
            'peran_anggota' => 'Asisten Validasi Data Lapangan & Pengujian Akurasi AI',
            'status_persetujuan' => 'rejected',
        ]);

        // Anggota 4: Dr. Ahmad Fauzi (Dosen 2) - APPROVED
        $ang4 = PpmUsulanAnggota::create([
            'id_usulan' => $usulan1->id,
            'user_id' => $ketua2->id,
            'jenis_anggota' => 'dosen',
            'nama' => $ketua2->name,
            'identifier' => '0620087102',
            'peran_anggota' => 'Spesialis Pemrosesan Citra Satelit & Pemetaan GIS',
            'status_persetujuan' => 'approved',
            'approved_at' => now()->subDay(),
        ]);

        // 3. Proposal 2: Ketua Dr. Ahmad Fauzi
        $usulan2 = PpmUsulan::updateOrCreate(
            ['kode_usulan' => 'USL-2026-CONSENT-02'],
            [
                'id_pengusul' => $ketua2->id,
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
            ]
        );

        PpmUsulanAnggota::where('id_usulan', $usulan2->id)->delete();

        // Anggota 5: Rizky Pratama (Mahasiswa 1) - PENDING di Proposal 2
        $ang5 = PpmUsulanAnggota::create([
            'id_usulan' => $usulan2->id,
            'user_id' => $mhs1->id,
            'jenis_anggota' => 'mahasiswa',
            'nama' => $mhs1->name,
            'identifier' => '210108001',
            'peran_anggota' => 'Instalasi Sensor Suhu & Tekanan Biodigester Biogas',
            'status_persetujuan' => 'pending',
        ]);
        $mhs1->notify(new MemberInvitationNotification($ang5));
    }
}


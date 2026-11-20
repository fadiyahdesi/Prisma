<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'group',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_has_permissions', 'permission_id', 'role_id');
    }

    /**
     * Katalog lengkap seluruh izin fitur dalam sistem PRISMA UHN (42 izin dalam 8 grup).
     */
    public static function catalog(): array
    {
        return [
            // 1. Riset, Pengabdian & Pengajuan Usulan
            ['name' => 'penelitian.view_own', 'display_name' => 'Lihat Riwayat Penelitian Sendiri', 'group' => 'Riset, Abmas & Pengajuan Usulan'],
            ['name' => 'penelitian.manage_all', 'display_name' => 'Kelola Full Data Penelitian & Sinkron CSV', 'group' => 'Riset, Abmas & Pengajuan Usulan'],
            ['name' => 'pengabdian.view_own', 'display_name' => 'Lihat Riwayat Pengabdian Sendiri', 'group' => 'Riset, Abmas & Pengajuan Usulan'],
            ['name' => 'pengabdian.manage_all', 'display_name' => 'Kelola Full Data Pengabdian & Sinkron CSV', 'group' => 'Riset, Abmas & Pengajuan Usulan'],
            ['name' => 'proposal.submit_wizard', 'display_name' => 'Ajukan Proposal Baru (Wizard 6 Langkah)', 'group' => 'Riset, Abmas & Pengajuan Usulan'],
            ['name' => 'proposal.member_consent', 'display_name' => 'Persetujuan Keanggotaan Tim (Member Consent)', 'group' => 'Riset, Abmas & Pengajuan Usulan'],

            // 2. Seleksi, Verifikasi & Penilaian Reviewer
            ['name' => 'verifikasi.usulan_penelitian', 'display_name' => 'Verifikasi Dokumen Penelitian (Template & Similarity)', 'group' => 'Seleksi, Verifikasi & Penilaian Reviewer'],
            ['name' => 'verifikasi.usulan_pengabdian', 'display_name' => 'Verifikasi Dokumen Pengabdian (Template & Similarity)', 'group' => 'Seleksi, Verifikasi & Penilaian Reviewer'],
            ['name' => 'prodi.roadmap', 'display_name' => 'Evaluasi Keselarasan Roadmap Keilmuan Prodi', 'group' => 'Seleksi, Verifikasi & Penilaian Reviewer'],
            ['name' => 'reviewer.assign', 'display_name' => 'Penugasan Reviewer & Persetujuan Calon Reviewer', 'group' => 'Seleksi, Verifikasi & Penilaian Reviewer'],
            ['name' => 'reviewer.apply', 'display_name' => 'Mengusulkan Diri Sebagai Calon Reviewer', 'group' => 'Seleksi, Verifikasi & Penilaian Reviewer'],
            ['name' => 'reviewer.evaluate', 'display_name' => 'Menilai Telaah Substansi Proposal (Rubrik 1-7)', 'group' => 'Seleksi, Verifikasi & Penilaian Reviewer'],
            ['name' => 'ranking.view', 'display_name' => 'Pemeringkatan Usulan & Passing Grade Kuota', 'group' => 'Seleksi, Verifikasi & Penilaian Reviewer'],
            ['name' => 'kontrak.establish_winners', 'display_name' => 'Penetapan Pemenang Hibah Massal & Penerbitan SK/SPK', 'group' => 'Seleksi, Verifikasi & Penilaian Reviewer'],

            // 3. Pelaksanaan Hibah & Monitoring
            ['name' => 'kontrak.pengusul', 'display_name' => 'Kontrak SPK Digital & Rekening Peneliti', 'group' => 'Pelaksanaan Hibah & Monitoring'],
            ['name' => 'logbook.manage', 'display_name' => 'Logbook Harian Pelaksanaan & Ekspor PDF', 'group' => 'Pelaksanaan Hibah & Monitoring'],
            ['name' => 'monev.submit', 'display_name' => 'Unggah Laporan Kemajuan 70% & SPTB 70%', 'group' => 'Pelaksanaan Hibah & Monitoring'],
            ['name' => 'monev.evaluate', 'display_name' => 'Evaluasi Monev Lapangan Kemajuan (Reviewer)', 'group' => 'Pelaksanaan Hibah & Monitoring'],
            ['name' => 'semhas.manage', 'display_name' => 'Manajemen Seminar Hasil & Laporan Akhir Divisi', 'group' => 'Pelaksanaan Hibah & Monitoring'],
            ['name' => 'laporan_akhir.submit', 'display_name' => 'Unggah Laporan Akhir 100%, SPTB & Lembar Pengesahan', 'group' => 'Pelaksanaan Hibah & Monitoring'],

            // 4. Keuangan & Pencairan Dana
            ['name' => 'keuangan.pencairan_hibah', 'display_name' => 'Verifikasi Rekening & Pencairan Hibah Termin I/II', 'group' => 'Keuangan & Pencairan Dana'],
            ['name' => 'keuangan.pencairan_reward', 'display_name' => 'Pencairan Insentif Reward Multi-Penulis', 'group' => 'Keuangan & Pencairan Dana'],

            // 5. Publikasi, Sentra HKI & Insentif Reward
            ['name' => 'publikasi.view_own', 'display_name' => 'Lihat & Ajukan Publikasi Ilmiah Pribadi', 'group' => 'Publikasi, Sentra HKI & Insentif Reward'],
            ['name' => 'publikasi.manage_all', 'display_name' => 'Kelola Full Data Publikasi, Sinkron CSV & Verifikasi Dokumen', 'group' => 'Publikasi, Sentra HKI & Insentif Reward'],
            ['name' => 'hki.submit', 'display_name' => 'Pengajuan & Daftar Sentra HKI Pribadi', 'group' => 'Publikasi, Sentra HKI & Insentif Reward'],
            ['name' => 'hki.manage_all', 'display_name' => 'Kelola Full & Verifikasi Sentra HKI / DJKI', 'group' => 'Publikasi, Sentra HKI & Insentif Reward'],
            ['name' => 'reward.claim', 'display_name' => 'Ajukan & Pantau Klaim Reward Insentif', 'group' => 'Publikasi, Sentra HKI & Insentif Reward'],
            ['name' => 'reward.verify', 'display_name' => 'Verifikasi & Persetujuan Klaim Reward Insentif', 'group' => 'Publikasi, Sentra HKI & Insentif Reward'],

            // 6. Analitik, Akreditasi & Tata Kelola
            ['name' => 'analitik.eksekutif', 'display_name' => 'Dasbor Analitik Eksekutif Universitas', 'group' => 'Analitik, Akreditasi & Tata Kelola'],
            ['name' => 'analitik.fakultas', 'display_name' => 'Dasbor Performa Fakultas & Program Studi', 'group' => 'Analitik, Akreditasi & Tata Kelola'],
            ['name' => 'laporan.akreditasi', 'display_name' => 'Pelaporan Akreditasi BAN-PT & Ekspor Excel/PDF', 'group' => 'Analitik, Akreditasi & Tata Kelola'],
            ['name' => 'uat.access', 'display_name' => 'Portal Berita Acara UAT & Kesiapan Go-Live', 'group' => 'Analitik, Akreditasi & Tata Kelola'],
            ['name' => 'panduan.access', 'display_name' => 'Buku Panduan Pengguna Interaktif', 'group' => 'Analitik, Akreditasi & Tata Kelola'],

            // 7. Konfigurasi Master, SDM & Integrasi API
            ['name' => 'master.skema_bima', 'display_name' => 'Master Skema BIMA & Rubrik Penilaian 1-7', 'group' => 'Konfigurasi Master, SDM & Integrasi'],
            ['name' => 'master.periode_hibah', 'display_name' => 'Kelola Periode Call for Proposals Hibah', 'group' => 'Konfigurasi Master, SDM & Integrasi'],
            ['name' => 'users.manage', 'display_name' => 'Manajemen Akun Pengguna & 146 Dosen Tetap UHN', 'group' => 'Konfigurasi Master, SDM & Integrasi'],
            ['name' => 'sinta.profile', 'display_name' => 'Profil Metrik SINTA & Eligibilitas Mandiri', 'group' => 'Konfigurasi Master, SDM & Integrasi'],
            ['name' => 'pddikti.search', 'display_name' => 'Pencarian Pangkalan Data PDDIKTI', 'group' => 'Konfigurasi Master, SDM & Integrasi'],
            ['name' => 'integrasi.manage', 'display_name' => 'Integrasi API 4 Modul (SIAKAD, SINTA, DJKI, MinIO)', 'group' => 'Konfigurasi Master, SDM & Integrasi'],
            ['name' => 'migrasi.legasi', 'display_name' => 'Migrasi Data Legasi SIMPENDI', 'group' => 'Konfigurasi Master, SDM & Integrasi'],

            // 8. Keamanan & Pengendalian Superadmin
            ['name' => 'audit_logs.view', 'display_name' => 'Audit Trail Logging Keamanan Sistem', 'group' => 'Keamanan & Kontrol Superadmin'],
            ['name' => 'rbac.manage', 'display_name' => 'Pengaturan Role & Permission Dinamis (Superadmin)', 'group' => 'Keamanan & Kontrol Superadmin'],
        ];
    }

    /**
     * Peta izin bawaan (default) untuk masing-masing peran resmi UHN.
     */
    public static function defaultRoleMap(): array
    {
        $allNames = array_column(self::catalog(), 'name');

        return [
            'Superadmin' => $allNames,
            'Dosen / Pengusul' => [
                'penelitian.view_own',
                'pengabdian.view_own',
                'proposal.submit_wizard',
                'proposal.member_consent',
                'kontrak.pengusul',
                'logbook.manage',
                'monev.submit',
                'laporan_akhir.submit',
                'publikasi.view_own',
                'hki.submit',
                'reward.claim',
                'sinta.profile',
                'pddikti.search',
                'reviewer.apply',
                'panduan.access',
            ],
            'Dosen / Mahasiswa Anggota' => [
                'proposal.member_consent',
                'publikasi.view_own',
                'hki.submit',
                'reward.claim',
                'panduan.access',
            ],
            'Reviewer' => [
                'reviewer.evaluate',
                'monev.evaluate',
                'sinta.profile',
                'penelitian.view_own',
                'pengabdian.view_own',
                'publikasi.view_own',
                'panduan.access',
            ],
            'Kaprodi' => [
                'prodi.roadmap',
                'laporan.akreditasi',
                'penelitian.view_own',
                'pengabdian.view_own',
                'proposal.submit_wizard',
                'publikasi.view_own',
                'panduan.access',
            ],
            'Dekanat' => [
                'analitik.fakultas',
                'laporan.akreditasi',
                'uat.access',
                'penelitian.view_own',
                'pengabdian.view_own',
                'proposal.submit_wizard',
                'publikasi.view_own',
                'panduan.access',
            ],
            'Divisi Penelitian' => [
                'penelitian.view_own',
                'penelitian.manage_all',
                'verifikasi.usulan_penelitian',
                'reviewer.assign',
                'semhas.manage',
                'panduan.access',
            ],
            'Divisi Pengabdian' => [
                'pengabdian.view_own',
                'pengabdian.manage_all',
                'verifikasi.usulan_pengabdian',
                'reviewer.assign',
                'semhas.manage',
                'panduan.access',
            ],
            'Divisi Publikasi' => [
                'publikasi.view_own',
                'publikasi.manage_all',
                'hki.submit',
                'hki.manage_all',
                'reward.verify',
                'panduan.access',
            ],
            'Admin P3M' => [
                'users.manage',
                'master.skema_bima',
                'master.periode_hibah',
                'reviewer.assign',
                'integrasi.manage',
                'pddikti.search',
                'migrasi.legasi',
                'uat.access',
                'panduan.access',
            ],
            'Kepala P3M' => [
                'penelitian.view_own',
                'pengabdian.view_own',
                'publikasi.view_own',
                'proposal.submit_wizard',
                'verifikasi.usulan_penelitian',
                'verifikasi.usulan_pengabdian',
                'reviewer.assign',
                'ranking.view',
                'kontrak.establish_winners',
                'semhas.manage',
                'users.manage',
                'integrasi.manage',
                'analitik.eksekutif',
                'analitik.fakultas',
                'laporan.akreditasi',
                'panduan.access',
            ],
            'Keuangan' => [
                'keuangan.pencairan_hibah',
                'keuangan.pencairan_reward',
                'panduan.access',
            ],
        ];
    }
}

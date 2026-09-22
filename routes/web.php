<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Auth\SsoController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\PddiktiController;
use App\Http\Controllers\SintaSyncController;
use App\Http\Controllers\EligibilityController;
use App\Http\Middleware\EnsureOtpVerified;
use App\Http\Middleware\CheckRole;

// Landing Page & Public Catalog (Tanpa Login)
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/katalog-publikasi', [LandingController::class, 'publikasi'])->name('publikasi.katalog-publik');
Route::get('/integrasi/pddikti/search', [PddiktiController::class, 'search'])->name('pddikti.search');

// EPIC 09: Public QR Code SPK Verification Route (US-09.2)
Route::get('/spk/verify/{token}', [\App\Http\Controllers\ContractController::class, 'publicVerify'])->name('spk.verify');

// EPIC 10: Public QR Code Laporan Akhir Verification Route (US-10.3)
Route::get('/laporan-akhir/verify/{token}', [\App\Http\Controllers\SemhasController::class, 'publicVerify'])->name('laporan-akhir.verify');

// Rute Sekali-Klik Penyiapan Database Demo Cloud & Pengisian Seluruh Dosen
Route::get('/system/setup-demo-database', function () {
    \Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--force' => true, '--seed' => true]);
    try {
        \Illuminate\Support\Facades\Artisan::call('prisma:generate-demo-pdfs', ['--overwrite' => true]);
    } catch (\Throwable $e) {
        // ignore if not critical
    }

    $lecturers = \App\Models\User::whereNotNull('nidn_nim')->select('name', 'nidn_nim', 'email')->get();

    return response()->json([
        'status' => 'success',
        'message' => 'Database PRISMA cloud berhasil dimigrasikan dan seluruh data dosen riil UHN telah diisi!',
        'total_dosen' => $lecturers->count(),
        'petunjuk_login' => 'Gunakan Nama Lengkap atau NIDN sebagai Username, dan NIDN sebagai Password.',
        'daftar_dosen' => $lecturers
    ], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
});

// Rute Pembuatan Berkas PDF Demo & Perbaikan Symlink Storage
Route::get('/system/generate-demo-pdfs', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('storage:link', ['--force' => true]);
        \Illuminate\Support\Facades\Artisan::call('prisma:generate-demo-pdfs', ['--overwrite' => true]);
        return response()->json([
            'status' => 'success',
            'message' => 'Seluruh berkas PDF resmi demo PRISMA telah berhasil dibuat di public storage!',
            'output' => \Illuminate\Support\Facades\Artisan::output()
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});

// Dynamic Public Storage & On-Demand Demo PDF Fallback
Route::get('/storage/{path}', function (string $path) {
    $fullPath = storage_path('app/public/' . $path);

    // 1. If physical file exists in storage/app/public, serve it directly
    if (file_exists($fullPath) && !is_dir($fullPath)) {
        $mime = mime_content_type($fullPath) ?: 'application/octet-stream';
        return response()->file($fullPath, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . basename($fullPath) . '"',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    // 2. If it is a PDF, auto-generate on demand so it never 404s
    if (str_ends_with(strtolower($path), '.pdf')) {
        try {
            $generator = app(\App\Services\DemoPdfGeneratorService::class);
            $lower = strtolower($path);

            if (str_contains($lower, 'publikasi_naskah') || str_contains($lower, 'paper')) {
                $generator->generatePaper([], $path);
            } elseif (str_contains($lower, 'hki_sertifikat') || str_contains($lower, 'sertifikat')) {
                $generator->generateHkiCertificate([], $path);
            } elseif (str_contains($lower, 'manual_book')) {
                $generator->generateHkiManualBook([], $path);
            } elseif (str_contains($lower, 'surat_pernyataan')) {
                $generator->generateHkiPernyataan([], $path);
            } elseif (str_contains($lower, 'surat_pengalihan')) {
                $generator->generateHkiPengalihan([], $path);
            } elseif (str_contains($lower, 'proposals') || str_contains($lower, 'proposal')) {
                $generator->generateProposal([], $path);
            } elseif (str_contains($lower, 'mitra')) {
                $generator->generateMitraSurat([], $path);
            } elseif (str_contains($lower, 'laporan_kemajuan') || str_contains($lower, 'kemajuan')) {
                $generator->generateLaporanKemajuan([], $path);
            } elseif (str_contains($lower, 'laporan_akhir') || str_contains($lower, 'laporan-akhir')) {
                $generator->generateLaporanAkhir([], $path);
            } elseif (str_contains($lower, 'sptb_70') || str_contains($lower, 'sptb-70')) {
                $generator->generateSptb(['persen' => 70], $path);
            } elseif (str_contains($lower, 'sptb')) {
                $generator->generateSptb(['persen' => 100], $path);
            } elseif (str_contains($lower, 'tabungan')) {
                $generator->generateBukuTabungan([], $path);
            } elseif (str_contains($lower, 'kontrak') || str_contains($lower, 'spk')) {
                $generator->generateSpkKontrak([], $path);
            } elseif (str_contains($lower, 'bukti_transfer') || str_contains($lower, 'transfer')) {
                $generator->generateBuktiTransfer([], $path);
            } elseif (str_contains($lower, 'reward') || str_contains($lower, 'kesepakatan')) {
                $generator->generateRewardKesepakatan([], $path);
            } else {
                $generator->generateProposal(['title' => 'Dokumen Resmi PRISMA UHN'], $path);
            }

            if (file_exists($fullPath)) {
                return response()->file($fullPath, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . basename($fullPath) . '"',
                ]);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Gagal auto-generate PDF untuk {$path}: " . $e->getMessage());
        }
    }

    abort(404, 'Berkas tidak ditemukan.');
})->where('path', '.*')->name('storage.fallback');

// Auth & SSO Routes (US-02.1)
Route::get('/login', [SsoController::class, 'showLogin'])->name('login');
Route::post('/login', [\App\Http\Controllers\Auth\AuthController::class, 'login'])->name('login.post');
Route::get('/auth/sso/redirect', [SsoController::class, 'redirect'])->name('sso.redirect');
Route::get('/auth/sso/callback', [SsoController::class, 'callback'])->name('sso.callback');
Route::post('/logout', [SsoController::class, 'logout'])->name('logout');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    // 2FA OTP Verification Routes (US-02.2)
    Route::get('/auth/otp', [OtpController::class, 'showForm'])->name('otp.show');
    Route::post('/auth/otp', [OtpController::class, 'verify'])->name('otp.verify');
    Route::post('/auth/otp/resend', [OtpController::class, 'resend'])->name('otp.resend');

    // Verified OTP Routes
    Route::middleware([EnsureOtpVerified::class])->group(function () {
        // RBAC Multi-Role Dashboard (US-02.3)
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/dashboard/switch-role', [DashboardController::class, 'switchRole'])->name('dashboard.switch-role');

        // Pengaturan Profil Mandiri (All Roles)
        Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.update-password');

        // Manajemen Akun Pengguna (Admin P3M, Kepala P3M, & Superadmin)
        Route::resource('/admin/users', \App\Http\Controllers\Admin\UserController::class)->names([
            'index' => 'admin.users.index',
            'create' => 'admin.users.create',
            'store' => 'admin.users.store',
            'edit' => 'admin.users.edit',
            'update' => 'admin.users.update',
            'destroy' => 'admin.users.destroy',
        ]);

        // EPIC 03: SINTA Sync & Real Search Routes (US-03.1, US-03.3)
        Route::get('/sinta/profile', [SintaSyncController::class, 'showProfile'])->name('sinta.profile');
        Route::post('/sinta/sync', [SintaSyncController::class, 'syncMandatory'])->name('sinta.sync');
        Route::post('/sinta/update-nidn', [SintaSyncController::class, 'updateNidnSinta'])->name('sinta.update-nidn');
        Route::post('/sinta/import-real', [SintaSyncController::class, 'importReal'])->name('sinta.import-real');
        Route::post('/sinta/fallback', [SintaSyncController::class, 'submitFallback'])->name('sinta.fallback');
        Route::post('/sinta/verify/{user}', [SintaSyncController::class, 'verifyFallback'])->name('sinta.verify');

        // EPIC 03: Automated Eligibility Assessment API (US-03.4)
        Route::get('/api/eligibility/check', [EligibilityController::class, 'check'])->name('api.eligibility.check');

        // Integrations Hub API & Status Routes (SIAKAD, SINTA, DJKI, MinIO)
        Route::get('/integrasi', [\App\Http\Controllers\IntegrationsController::class, 'index'])->name('integrasi.index');
        Route::get('/integrasi/djki/verify', [\App\Http\Controllers\IntegrationsController::class, 'verifyDjki'])->name('integrasi.djki.verify');
        Route::post('/integrasi/storage/upload', [\App\Http\Controllers\IntegrationsController::class, 'uploadTestFile'])->name('integrasi.storage.upload');

        // Audit Trail Logging (US-02.4 - Restricted to Kepala P3M & Superadmin)
        Route::get('/admin/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs');

        // EPIC 04: Master Skema BIMA & Rubrik Penilaian Reviewer (US-04.1, US-04.3)
        Route::get('/admin/skema-bima', [\App\Http\Controllers\Admin\SkemaBimaController::class, 'index'])->name('admin.skema-bima.index');
        Route::post('/admin/skema-bima', [\App\Http\Controllers\Admin\SkemaBimaController::class, 'store'])->name('admin.skema-bima.store');
        Route::put('/admin/skema-bima/{skemaBima}', [\App\Http\Controllers\Admin\SkemaBimaController::class, 'update'])->name('admin.skema-bima.update');
        Route::post('/admin/skema-bima/{skemaBima}/toggle', [\App\Http\Controllers\Admin\SkemaBimaController::class, 'toggleActive'])->name('admin.skema-bima.toggle');
        Route::delete('/admin/skema-bima/{skemaBima}', [\App\Http\Controllers\Admin\SkemaBimaController::class, 'destroy'])->name('admin.skema-bima.destroy');
        Route::get('/admin/skema-bima/{skemaBima}/rubrik', [\App\Http\Controllers\Admin\SkemaBimaController::class, 'showRubrik'])->name('admin.skema-bima.rubrik');
        Route::post('/admin/skema-bima/{skemaBima}/rubrik', [\App\Http\Controllers\Admin\SkemaBimaController::class, 'updateRubrik'])->name('admin.skema-bima.update-rubrik');

        // EPIC 04: Periode Usulan Call for Proposals & Server-Time Scheduler (US-04.2)
        Route::get('/admin/periode-hibah', [\App\Http\Controllers\Admin\PeriodeHibahController::class, 'index'])->name('admin.periode-hibah.index');
        Route::post('/admin/periode-hibah', [\App\Http\Controllers\Admin\PeriodeHibahController::class, 'store'])->name('admin.periode-hibah.store');
        Route::put('/admin/periode-hibah/{periodeHibah}', [\App\Http\Controllers\Admin\PeriodeHibahController::class, 'update'])->name('admin.periode-hibah.update');
        Route::post('/admin/periode-hibah/{periodeHibah}/toggle', [\App\Http\Controllers\Admin\PeriodeHibahController::class, 'toggleActive'])->name('admin.periode-hibah.toggle');
        Route::delete('/admin/periode-hibah/{periodeHibah}', [\App\Http\Controllers\Admin\PeriodeHibahController::class, 'destroy'])->name('admin.periode-hibah.destroy');

        // EPIC 05: Wizard 6 Langkah Usulan Proposal BIMA (US-05.1 s.d. US-05.6)
        Route::get('/usulan', [\App\Http\Controllers\ProposalWizardController::class, 'index'])->name('usulan.index');
        Route::get('/usulan/buat/{skema}', [\App\Http\Controllers\ProposalWizardController::class, 'start'])->name('usulan.start');
        Route::get('/usulan/{usulan}/step/{step}', [\App\Http\Controllers\ProposalWizardController::class, 'showStep'])->name('usulan.step');
        Route::post('/usulan/{usulan}/step/1', [\App\Http\Controllers\ProposalWizardController::class, 'saveStep1'])->name('usulan.save-step1');
        Route::post('/usulan/{usulan}/step/2', [\App\Http\Controllers\ProposalWizardController::class, 'saveStep2'])->name('usulan.save-step2');
        Route::post('/usulan/{usulan}/step/3', [\App\Http\Controllers\ProposalWizardController::class, 'saveStep3'])->name('usulan.save-step3');
        Route::post('/usulan/{usulan}/step/4', [\App\Http\Controllers\ProposalWizardController::class, 'saveStep4'])->name('usulan.save-step4');
        Route::post('/usulan/{usulan}/step/5', [\App\Http\Controllers\ProposalWizardController::class, 'saveStep5'])->name('usulan.save-step5');
        Route::post('/usulan/{usulan}/submit', [\App\Http\Controllers\ProposalWizardController::class, 'submitFinal'])->name('usulan.submit-final');
        Route::get('/usulan/{usulan}/document', [\App\Http\Controllers\ProposalWizardController::class, 'downloadProposal'])->name('usulan.document');

        // EPIC 06: Digital member consent workflow
        Route::get('/member-consent', [\App\Http\Controllers\ProposalWizardController::class, 'memberConsentIndex'])->name('member-consent.index');
        Route::post('/member-consent/{member}', [\App\Http\Controllers\ProposalWizardController::class, 'respondToMemberConsent'])->name('member-consent.respond');

        // EPIC 07: LPPM institutional verification workflow
        Route::get('/admin/lppm-approval', [\App\Http\Controllers\LppmApprovalController::class, 'index'])->name('admin.lppm-approval.index');
        Route::post('/admin/lppm-approval/{usulan}/in-review', [\App\Http\Controllers\LppmApprovalController::class, 'markInReview'])->name('admin.lppm-approval.in-review');
        Route::post('/admin/lppm-approval/{usulan}', [\App\Http\Controllers\LppmApprovalController::class, 'update'])->name('admin.lppm-approval.update');
        Route::get('/admin/prodi-roadmap', [\App\Http\Controllers\LppmApprovalController::class, 'roadmapIndex'])->name('admin.prodi-roadmap.index');
        Route::post('/admin/prodi-roadmap/{usulan}', [\App\Http\Controllers\LppmApprovalController::class, 'updateRoadmap'])->name('admin.prodi-roadmap.update');

        // EPIC 08: Reviewer Substantive Evaluation (Double-Blind) (US-08.2)
        Route::get('/reviewer/penilaian', [\App\Http\Controllers\ReviewerController::class, 'index'])->name('reviewer.penilaian.index');
        Route::get('/reviewer/penilaian/{penugasan}', [\App\Http\Controllers\ReviewerController::class, 'show'])->name('reviewer.penilaian.show');
        Route::post('/reviewer/penilaian/{penugasan}', [\App\Http\Controllers\ReviewerController::class, 'store'])->name('reviewer.penilaian.store');

        // EPIC 08: Penugasan Reviewer & Adjudikasi Disparitas Ekstrem (US-08.1, US-08.3)
        Route::get('/admin/penugasan-reviewer', [\App\Http\Controllers\Admin\ReviewerAssignmentController::class, 'index'])->name('admin.reviewer-assignment.index');
        Route::post('/admin/penugasan-reviewer/{usulan}/assign', [\App\Http\Controllers\Admin\ReviewerAssignmentController::class, 'assign'])->name('admin.reviewer-assignment.assign');
        Route::post('/admin/penugasan-reviewer/{usulan}/adjudicator', [\App\Http\Controllers\Admin\ReviewerAssignmentController::class, 'assignAdjudicator'])->name('admin.reviewer-assignment.adjudicator');
        Route::get('/admin/penugasan-reviewer/{usulan}/eligible', [\App\Http\Controllers\Admin\ReviewerAssignmentController::class, 'eligibleReviewers'])->name('admin.reviewer-assignment.eligible');

        // EPIC 08: Pemeringkatan Usulan & Passing Grade Cut-off (US-08.4)
        Route::get('/admin/pemeringkatan-usulan', [\App\Http\Controllers\Admin\ProposalRankingController::class, 'index'])->name('admin.ranking.index');

        // EPIC 09: Penetapan Pemenang Hibah Secara Massal (Kepala P3M - US-09.1)
        Route::post('/admin/kontrak/penetapan-massal', [\App\Http\Controllers\ContractController::class, 'penetapanMassal'])->name('admin.kontrak.penetapan-massal');

        // EPIC 09: Portal Dosen Pengusul - SPK Digital & Rekening (US-09.2, US-09.3)
        Route::get('/pengusul/kontrak', [\App\Http\Controllers\ContractController::class, 'pengusulIndex'])->name('pengusul.kontrak.index');
        Route::post('/pengusul/kontrak/{kontrak}/sign', [\App\Http\Controllers\ContractController::class, 'pengusulSign'])->name('pengusul.kontrak.sign');
        Route::post('/pengusul/kontrak/{kontrak}/rekening', [\App\Http\Controllers\ContractController::class, 'pengusulUpdateRekening'])->name('pengusul.kontrak.update-rekening');
        Route::get('/kontrak/{kontrak}/pdf', [\App\Http\Controllers\ContractController::class, 'downloadPdf'])->name('kontrak.download-pdf');

        // EPIC 09: Divisi Keuangan - Validasi Rekening & Pencairan Dana Hibah Termin I 70% (US-09.3)
        Route::get('/keuangan/pencairan', [\App\Http\Controllers\DisbursementController::class, 'index'])->name('keuangan.pencairan.index');
        Route::get('/keuangan/pencairan/{kontrak}', [\App\Http\Controllers\DisbursementController::class, 'show'])->name('keuangan.pencairan.show');
        Route::post('/keuangan/pencairan/{kontrak}/verify-rekening', [\App\Http\Controllers\DisbursementController::class, 'verifyRekening'])->name('keuangan.pencairan.verify-rekening');
        Route::post('/keuangan/pencairan/{kontrak}/termin-1', [\App\Http\Controllers\DisbursementController::class, 'disburseTermin1'])->name('keuangan.pencairan.disburse-termin-1');

        // EPIC 10: Logbook Harian Penelitian & Ekspor PDF Formal (US-10.1)
        Route::get('/pengusul/logbook', [\App\Http\Controllers\LogbookController::class, 'index'])->name('pengusul.logbook.index');
        Route::get('/pengusul/logbook/{usulan}', [\App\Http\Controllers\LogbookController::class, 'index'])->name('pengusul.logbook.show');
        Route::post('/pengusul/logbook/{usulan}', [\App\Http\Controllers\LogbookController::class, 'store'])->name('pengusul.logbook.store');
        Route::delete('/pengusul/logbook/entry/{logbook}', [\App\Http\Controllers\LogbookController::class, 'destroy'])->name('pengusul.logbook.destroy');
        Route::get('/pengusul/logbook/{usulan}/pdf', [\App\Http\Controllers\LogbookController::class, 'downloadPdf'])->name('pengusul.logbook.download-pdf');

        // EPIC 10: Laporan Kemajuan 70%, SPTB 70% & Evaluasi Monev Lapangan (US-10.2)
        Route::get('/pengusul/monev', [\App\Http\Controllers\MonevController::class, 'pengusulIndex'])->name('pengusul.monev.index');
        Route::get('/pengusul/monev/{usulan}', [\App\Http\Controllers\MonevController::class, 'pengusulIndex'])->name('pengusul.monev.show');
        Route::post('/pengusul/monev/{usulan}', [\App\Http\Controllers\MonevController::class, 'pengusulStore'])->name('pengusul.monev.store');
        Route::get('/reviewer/monev', [\App\Http\Controllers\MonevController::class, 'reviewerIndex'])->name('reviewer.monev.index');
        Route::get('/reviewer/monev/{monev}', [\App\Http\Controllers\MonevController::class, 'reviewerShow'])->name('reviewer.monev.show');
        Route::post('/reviewer/monev/{monev}', [\App\Http\Controllers\MonevController::class, 'reviewerStore'])->name('reviewer.monev.store');

        // EPIC 10: Seminar Hasil (Semhas) & Laporan Akhir 100% (US-10.3)
        Route::get('/admin/semhas', [\App\Http\Controllers\SemhasController::class, 'adminIndex'])->name('admin.semhas.index');
        Route::post('/admin/semhas/{usulan}/schedule', [\App\Http\Controllers\SemhasController::class, 'adminSchedule'])->name('admin.semhas.schedule');
        Route::post('/admin/semhas/{semhas}/grade', [\App\Http\Controllers\SemhasController::class, 'adminGrade'])->name('admin.semhas.grade');
        Route::post('/admin/semhas/{usulan}/kepala-approval', [\App\Http\Controllers\SemhasController::class, 'kepalaApproval'])->name('admin.semhas.kepala-approval');
        Route::get('/pengusul/laporan-akhir', [\App\Http\Controllers\SemhasController::class, 'pengusulLaporanAkhir'])->name('pengusul.laporan-akhir.index');
        Route::get('/pengusul/laporan-akhir/{usulan}', [\App\Http\Controllers\SemhasController::class, 'pengusulLaporanAkhir'])->name('pengusul.laporan-akhir.show');
        Route::post('/pengusul/laporan-akhir/{usulan}', [\App\Http\Controllers\SemhasController::class, 'pengusulStoreLaporanAkhir'])->name('pengusul.laporan-akhir.store');
        Route::get('/laporan-akhir/{usulan}/pengesahan-pdf', [\App\Http\Controllers\SemhasController::class, 'downloadPengesahan'])->name('laporan-akhir.download-pengesahan');

        // EPIC 10: Divisi Keuangan - Verifikasi 100%, Pencairan Termin II 30% & Pelunasan Hibah (US-10.4)
        Route::post('/keuangan/pencairan/{kontrak}/termin-2', [\App\Http\Controllers\DisbursementController::class, 'disburseTermin2'])->name('keuangan.pencairan.disburse-termin-2');
        Route::get('/keuangan/pencairan/{kontrak}/pelunasan-pdf', [\App\Http\Controllers\DisbursementController::class, 'downloadPelunasanPdf'])->name('keuangan.pencairan.pelunasan-pdf');

        // EPIC 11: Bank Publikasi Jurnal Kampus (US-11.1)
        Route::get('/publikasi/fetch-doi', [\App\Http\Controllers\PublikasiController::class, 'fetchDoi'])->name('publikasi.fetch-doi');
        Route::get('/publikasi', [\App\Http\Controllers\PublikasiController::class, 'index'])->name('publikasi.index');
        Route::get('/publikasi/create', [\App\Http\Controllers\PublikasiController::class, 'create'])->name('publikasi.create');
        Route::post('/publikasi', [\App\Http\Controllers\PublikasiController::class, 'store'])->name('publikasi.store');
        Route::get('/publikasi/{publikasi}', [\App\Http\Controllers\PublikasiController::class, 'show'])->name('publikasi.show');

        // EPIC 11: Sentra HKI UHN & Verifikasi DJKI (US-11.2)
        Route::get('/hki', [\App\Http\Controllers\SentraHkiController::class, 'index'])->name('hki.index');
        Route::get('/hki/create', [\App\Http\Controllers\SentraHkiController::class, 'create'])->name('hki.create');
        Route::post('/hki', [\App\Http\Controllers\SentraHkiController::class, 'store'])->name('hki.store');
        Route::get('/hki/{hki}', [\App\Http\Controllers\SentraHkiController::class, 'show'])->name('hki.show');
        Route::get('/hki/{hki}/download/{type}', [\App\Http\Controllers\SentraHkiController::class, 'downloadFile'])->name('hki.download-file');
        Route::get('/admin/hki', [\App\Http\Controllers\SentraHkiController::class, 'adminIndex'])->name('admin.hki.index');
        Route::get('/admin/hki/template-csv', [\App\Http\Controllers\SentraHkiController::class, 'downloadTemplate'])->name('admin.hki.template-csv');
        Route::get('/admin/hki/export-sinta', [\App\Http\Controllers\SentraHkiController::class, 'exportSintaJson'])->name('admin.hki.export-sinta');
        Route::post('/admin/hki/import', [\App\Http\Controllers\SentraHkiController::class, 'import'])->name('admin.hki.import');
        Route::post('/admin/hki/quick-sync-real', [\App\Http\Controllers\SentraHkiController::class, 'quickSyncDemo'])->name('admin.hki.quick-sync-real');
        Route::post('/admin/hki/{hki}/verify', [\App\Http\Controllers\SentraHkiController::class, 'verify'])->name('admin.hki.verify');

        // EPIC 11: Klaim Reward Insentif & Mesin Distribusi Multi-Penulis (US-11.3, US-11.4)
        Route::get('/reward', [\App\Http\Controllers\RewardClaimController::class, 'index'])->name('reward.index');
        Route::get('/reward/create', [\App\Http\Controllers\RewardClaimController::class, 'create'])->name('reward.create');
        Route::post('/reward', [\App\Http\Controllers\RewardClaimController::class, 'store'])->name('reward.store');
        Route::get('/reward/{klaim}', [\App\Http\Controllers\RewardClaimController::class, 'show'])->name('reward.show');
        Route::get('/admin/reward', [\App\Http\Controllers\RewardClaimController::class, 'adminIndex'])->name('admin.reward.index');
        Route::post('/admin/reward/{klaim}/approve', [\App\Http\Controllers\RewardClaimController::class, 'adminApprove'])->name('admin.reward.approve');
        Route::post('/admin/reward/{klaim}/reject', [\App\Http\Controllers\RewardClaimController::class, 'adminReject'])->name('admin.reward.reject');

        // EPIC 11: Divisi Keuangan - Pencairan Transfer Rekening Anggota Multi-Penulis (US-11.4)
        Route::get('/keuangan/reward', [\App\Http\Controllers\KeuanganRewardController::class, 'index'])->name('keuangan.reward.index');
        Route::get('/keuangan/reward/{klaim}', [\App\Http\Controllers\KeuanganRewardController::class, 'show'])->name('keuangan.reward.show');
        Route::post('/keuangan/reward/distribusi/{distribusi}/disburse', [\App\Http\Controllers\KeuanganRewardController::class, 'disburseMember'])->name('keuangan.reward.disburse-member');

        // EPIC 12: Dasbor Analitik Eksekutif Tingkat Universitas (US-12.1)
        Route::get('/analitik/eksekutif', [\App\Http\Controllers\AnalyticsController::class, 'executive'])->name('analitik.eksekutif');

        // EPIC 12: Dasbor Performa Fakultas dengan Isolasi Tenant (US-12.2)
        Route::get('/analitik/fakultas', [\App\Http\Controllers\AnalyticsController::class, 'faculty'])->name('analitik.fakultas');

        // EPIC 12: Pelaporan Akreditasi & Ekspor Excel/PDF (US-12.3)
        Route::get('/laporan/akreditasi', [\App\Http\Controllers\ReportController::class, 'index'])->name('laporan.akreditasi');
        Route::get('/laporan/akreditasi/excel', [\App\Http\Controllers\ReportController::class, 'exportExcel'])->name('laporan.akreditasi.excel');
        Route::get('/laporan/akreditasi/pdf', [\App\Http\Controllers\ReportController::class, 'exportPdf'])->name('laporan.akreditasi.pdf');

        // EPIC 13: Buku Panduan Pengguna Interaktif (US-13.3)
        Route::get('/panduan', [\App\Http\Controllers\UserGuideController::class, 'index'])->name('panduan.index');

        // EPIC 13: Portal Berita Acara UAT & Go-Live (US-13.3)
        Route::get('/uat', [\App\Http\Controllers\UatController::class, 'index'])->name('uat.index');
        Route::post('/uat/sign', [\App\Http\Controllers\UatController::class, 'sign'])->name('uat.sign');
        Route::get('/uat/pdf', [\App\Http\Controllers\UatController::class, 'exportPdf'])->name('uat.pdf');

        // EPIC 13: Migrasi Data Legasi SIMPENDI PHB (US-13.1)
        Route::get('/admin/migrasi-legasi', [\App\Http\Controllers\LegacyMigrationController::class, 'index'])->name('admin.migrasi.index');
        Route::post('/admin/migrasi-legasi/run', [\App\Http\Controllers\LegacyMigrationController::class, 'run'])->name('admin.migrasi.run');
    });
});

// EPIC 13: Production Health Check & Monitoring API (Public Liveness Probe)
Route::get('/api/health', [\App\Http\Controllers\HealthCheckController::class, 'check'])->name('api.health');
Route::get('/health', [\App\Http\Controllers\HealthCheckController::class, 'check'])->name('health');




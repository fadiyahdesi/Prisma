<?php

namespace App\Services;

use App\Models\PpmKontrak;
use App\Models\PpmLaporanAkhir;
use App\Models\PpmLogbook;
use App\Models\PpmMonevKemajuan;
use App\Models\PpmPencairanDana;
use App\Models\PpmSeminarHasil;
use App\Models\PpmUsulan;
use App\Notifications\DisbursementTermin2Notification;
use App\Notifications\MonevEvaluatedNotification;
use App\Notifications\SemhasScheduledNotification;
use App\Services\AuditLogService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MonitoringAndCompletionService
{
    /**
     * Create Logbook Entry with optional photo proof (US-10.1).
     */
    public static function createLogbookEntry(PpmUsulan $usulan, array $data, ?UploadedFile $fotoBukti, int $userId): PpmLogbook
    {
        $fotoPath = null;
        if ($fotoBukti) {
            $fotoPath = $fotoBukti->store('logbooks/' . $usulan->id, 'public');
        }

        $logbook = PpmLogbook::create([
            'id_usulan' => $usulan->id,
            'tanggal' => $data['tanggal'],
            'aktivitas' => trim($data['aktivitas']),
            'persentase_capaian' => (float) $data['persentase_capaian'],
            'file_bukti' => $fotoPath,
            'created_by' => $userId,
        ]);

        AuditLogService::log('LOGBOOK_ENTRY_CREATED', null, [
            'usulan_id' => $usulan->id,
            'logbook_id' => $logbook->id,
            'tanggal' => $data['tanggal'],
            'persentase_capaian' => $data['persentase_capaian'],
        ], $userId);

        return $logbook;
    }

    /**
     * Generate Chronological Logbook PDF (US-10.1).
     */
    public static function generateLogbookPdf(PpmUsulan $usulan)
    {
        $usulan->loadMissing(['pengusul.fakultas', 'pengusul.prodi', 'skema', 'periode', 'anggota', 'logbook']);

        $pdf = Pdf::loadView('pengusul.logbook.pdf', compact('usulan'))
            ->setPaper('a4', 'portrait')
            ->setOption(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

        return $pdf;
    }

    /**
     * Submit Laporan Kemajuan 70% & SPTB 70% (US-10.2).
     */
    public static function submitLaporanKemajuan(
        PpmUsulan $usulan,
        array $data,
        UploadedFile $fileKemajuan,
        UploadedFile $fileSptb70,
        int $userId
    ): PpmMonevKemajuan {
        $pathKemajuan = $fileKemajuan->store('monev/' . $usulan->id . '/kemajuan', 'public');
        $pathSptb70 = $fileSptb70->store('monev/' . $usulan->id . '/sptb70', 'public');

        $monev = PpmMonevKemajuan::updateOrCreate(
            ['id_usulan' => $usulan->id],
            [
                'file_laporan_kemajuan' => $pathKemajuan,
                'file_sptb_70' => $pathSptb70,
                'ringkasan_kemajuan' => $data['ringkasan_kemajuan'] ?? null,
                'persentase_kemajuan' => (float) ($data['persentase_kemajuan'] ?? 70.00),
                'status' => 'submitted',
            ]
        );

        AuditLogService::log('MONEV_LAPORAN_KEMAJUAN_SUBMITTED', null, [
            'usulan_id' => $usulan->id,
            'monev_id' => $monev->id,
        ], $userId);

        return $monev;
    }

    /**
     * Evaluate Laporan Kemajuan 70% by Reviewer Monev (US-10.2).
     */
    public static function evaluateMonev(PpmMonevKemajuan $monev, array $data, int $reviewerId): PpmMonevKemajuan
    {
        $monev->update([
            'id_reviewer' => $reviewerId,
            'skor_monev' => (float) $data['skor_monev'],
            'catatan_evaluasi' => trim($data['catatan_evaluasi']),
            'rekomendasi' => $data['rekomendasi'], // Lanjut, Perbaikan, Ditunda
            'status' => 'evaluated',
            'evaluated_at' => now(),
        ]);

        $monev->loadMissing('usulan.pengusul');
        if ($monev->usulan && $monev->usulan->pengusul) {
            $monev->usulan->pengusul->notify(new MonevEvaluatedNotification($monev));
        }

        AuditLogService::log('MONEV_EVALUATED', null, [
            'monev_id' => $monev->id,
            'usulan_id' => $monev->id_usulan,
            'skor_monev' => $data['skor_monev'],
            'rekomendasi' => $data['rekomendasi'],
        ], $reviewerId);

        return $monev;
    }

    /**
     * Schedule Seminar Hasil (Semhas) & assign examiners (US-10.3).
     */
    public static function scheduleSeminarHasil(PpmUsulan $usulan, array $data, int $adminId): PpmSeminarHasil
    {
        $semhas = PpmSeminarHasil::updateOrCreate(
            ['id_usulan' => $usulan->id],
            [
                'jadwal_seminar' => $data['jadwal_seminar'],
                'ruangan_or_link' => trim($data['ruangan_or_link']),
                'id_penguji_1' => $data['id_penguji_1'] ?? null,
                'id_penguji_2' => $data['id_penguji_2'] ?? null,
                'status_seminar' => 'scheduled',
            ]
        );

        $usulan->loadMissing('pengusul');
        if ($usulan->pengusul) {
            $usulan->pengusul->notify(new SemhasScheduledNotification($semhas));
        }

        AuditLogService::log('SEMHAS_SCHEDULED', null, [
            'usulan_id' => $usulan->id,
            'semhas_id' => $semhas->id,
            'jadwal' => $data['jadwal_seminar'],
        ], $adminId);

        return $semhas;
    }

    /**
     * Grade Seminar Hasil by examiner/P3M (US-10.3).
     */
    public static function gradeSeminarHasil(PpmSeminarHasil $semhas, array $data, int $userId): PpmSeminarHasil
    {
        $semhas->update([
            'skor_seminar' => (float) $data['skor_seminar'],
            'catatan_penguji' => trim($data['catatan_penguji'] ?? ''),
            'status_seminar' => 'completed',
        ]);

        AuditLogService::log('SEMHAS_GRADED', null, [
            'semhas_id' => $semhas->id,
            'usulan_id' => $semhas->id_usulan,
            'skor' => $data['skor_seminar'],
        ], $userId);

        return $semhas;
    }

    /**
     * Submit Laporan Akhir 100% & SPTB 100% (US-10.3).
     */
    public static function submitLaporanAkhir(
        PpmUsulan $usulan,
        array $data,
        UploadedFile $fileAkhir,
        UploadedFile $fileSptb100,
        int $userId
    ): PpmLaporanAkhir {
        $pathAkhir = $fileAkhir->store('laporan-akhir/' . $usulan->id . '/naskah', 'public');
        $pathSptb100 = $fileSptb100->store('laporan-akhir/' . $usulan->id . '/sptb100', 'public');
        $token = bin2hex(random_bytes(32));

        $laporan = PpmLaporanAkhir::updateOrCreate(
            ['id_usulan' => $usulan->id],
            [
                'file_laporan_akhir' => $pathAkhir,
                'file_sptb_100' => $pathSptb100,
                'ringkasan_hasil' => $data['ringkasan_hasil'] ?? null,
                'verification_token' => $token,
                'is_approved_p3m' => true,
                'approved_by_p3m_at' => now(),
            ]
        );

        AuditLogService::log('LAPORAN_AKHIR_SUBMITTED', null, [
            'usulan_id' => $usulan->id,
            'laporan_id' => $laporan->id,
            'verification_token' => $token,
        ], $userId);

        return $laporan;
    }

    /**
     * Generate QR Code SVG for Laporan Akhir verification.
     */
    public static function generateQrCodeSvg(string $url, int $size = 100): string
    {
        return QrCode::format('svg')
            ->size($size)
            ->margin(1)
            ->errorCorrection('M')
            ->generate($url);
    }

    /**
     * Generate Lembar Pengesahan Laporan Akhir ber-QR Code (US-10.3).
     */
    public static function generateLembarPengesahanPdf(PpmUsulan $usulan)
    {
        $usulan->loadMissing(['pengusul.fakultas', 'pengusul.prodi', 'skema', 'periode', 'anggota', 'laporanAkhir', 'kontrak']);

        $token = $usulan->laporanAkhir->verification_token ?? bin2hex(random_bytes(32));
        $verifyUrl = route('laporan-akhir.verify', ['token' => $token]);
        $qrCodeSvg = self::generateQrCodeSvg($verifyUrl, 110);
        $qrCodeDataUri = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);

        $pdf = Pdf::loadView('admin.laporan-akhir.pdf-pengesahan', compact('usulan', 'qrCodeDataUri', 'verifyUrl', 'token'))
            ->setPaper('a4', 'portrait')
            ->setOption(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

        return $pdf;
    }

    /**
     * Divisi Keuangan verifies LPJ 100% and disburses Termin II (30%) (US-10.4).
     */
    public static function disburseTermin2(
        PpmKontrak $kontrak,
        array $data,
        ?UploadedFile $fileBuktiTransfer,
        int $keuanganId
    ): PpmPencairanDana {
        $kontrak->loadMissing(['usulan.pengusul', 'pencairan']);

        // Check if Termin 1 has been disbursed
        $termin1Exists = $kontrak->pencairan()->where('termin', 1)->where('status_pencairan', 'transferred')->exists();
        if (!$termin1Exists) {
            throw new InvalidArgumentException('Termin I belum dicairkan.');
        }

        // Check if Laporan Akhir is uploaded
        if (!$kontrak->usulan->laporanAkhir) {
            throw new InvalidArgumentException('Laporan Akhir 100% belum diunggah oleh peneliti.');
        }

        return DB::transaction(function () use ($kontrak, $data, $fileBuktiTransfer, $keuanganId) {
            $buktiPath = null;
            if ($fileBuktiTransfer) {
                $buktiPath = $fileBuktiTransfer->store('pencairan/termin2/' . $kontrak->id, 'public');
            }

            $pencairan = PpmPencairanDana::create([
                'id_kontrak' => $kontrak->id,
                'termin' => 2,
                'persentase' => 30.00,
                'jumlah_dana' => $kontrak->dana_termin_2,
                'nomor_referensi' => trim($data['nomor_referensi']),
                'tanggal_transfer' => $data['tanggal_transfer'] ?? now(),
                'file_bukti_transfer' => $buktiPath,
                'status_pencairan' => 'transferred',
                'processed_by' => $keuanganId,
                'processed_at' => now(),
                'catatan' => $data['catatan'] ?? null,
            ]);

            // Update contract & usulan status to Completed
            $kontrak->update(['status' => 'completed']);
            $kontrak->usulan->update(['status' => 'Completed']);

            // Notify proposer
            if ($kontrak->usulan->pengusul) {
                $kontrak->usulan->pengusul->notify(new DisbursementTermin2Notification($kontrak, $pencairan));
            }

            AuditLogService::log('DISBURSEMENT_TERMIN_2_TRANSFERRED', null, [
                'kontrak_id' => $kontrak->id,
                'pencairan_id' => $pencairan->id,
                'jumlah_dana' => $kontrak->dana_termin_2,
                'nomor_referensi' => $pencairan->nomor_referensi,
                'status_usulan' => 'Completed',
            ], $keuanganId);

            return $pencairan;
        });
    }

    /**
     * Generate Official Certificate of Settlement / Proof of 100% Grant Completion (US-10.4).
     */
    public static function generateBuktiPelunasanPdf(PpmKontrak $kontrak)
    {
        $kontrak->loadMissing(['usulan.pengusul.fakultas', 'usulan.pengusul.prodi', 'usulan.skema', 'usulan.periode', 'pencairan.processor']);

        $token = $kontrak->verification_token;
        $verifyUrl = route('spk.verify', ['token' => $token]);
        $qrCodeSvg = self::generateQrCodeSvg($verifyUrl, 100);
        $qrCodeDataUri = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);

        $pdf = Pdf::loadView('keuangan.pencairan.pdf-pelunasan', compact('kontrak', 'qrCodeDataUri', 'verifyUrl'))
            ->setPaper('a4', 'portrait')
            ->setOption(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

        return $pdf;
    }
}


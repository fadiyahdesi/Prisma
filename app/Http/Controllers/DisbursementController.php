<?php

namespace App\Http\Controllers;

use App\Models\PpmKontrak;
use App\Services\ContractAndDisbursementService;
use App\Services\MonitoringAndCompletionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class DisbursementController extends Controller
{
    /**
     * Portal Divisi Keuangan LPPM: Daftar Pencairan Dana Hibah (US-09.3).
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        abort_unless($user->hasRole(['Keuangan', 'Superadmin']), 403, 'Akses ditolak: Khusus Divisi Keuangan LPPM.');

        $tab = $request->query('tab', 'all');
        $search = $request->query('search');

        $query = PpmKontrak::with([
            'usulan.pengusul.fakultas',
            'usulan.pengusul.prodi',
            'usulan.skema',
            'usulan.periode',
            'usulan.laporanAkhir',
            'pencairan.processor',
        ])->latest('tanggal_kontrak');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_kontrak', 'like', "%{$search}%")
                  ->orWhere('nomor_sk', 'like', "%{$search}%")
                  ->orWhere('nomor_rekening', 'like', "%{$search}%")
                  ->orWhere('nama_pemilik_rekening', 'like', "%{$search}%")
                  ->orWhere('nama_bank', 'like', "%{$search}%")
                  ->orWhereHas('usulan', function ($uq) use ($search) {
                      $uq->where('judul_usulan', 'like', "%{$search}%")
                         ->orWhere('kode_usulan', 'like', "%{$search}%")
                         ->orWhereHas('pengusul', function ($puq) use ($search) {
                             $puq->where('name', 'like', "%{$search}%");
                         });
                  });
            });
        }

        if ($tab === 'need_verification') {
            $query->whereNotNull('nomor_rekening')
                  ->whereNull('rekening_verified_at');
        } elseif ($tab === 'ready_termin1') {
            $query->where('signed_by_pengusul', true)
                  ->whereNotNull('rekening_verified_at')
                  ->whereDoesntHave('pencairan', fn($q) => $q->where('termin', 1));
        } elseif ($tab === 'ready_termin2') {
            $query->whereHas('pencairan', fn($q) => $q->where('termin', 1))
                  ->whereHas('usulan.laporanAkhir')
                  ->whereDoesntHave('pencairan', fn($q) => $q->where('termin', 2));
        } elseif ($tab === 'completed') {
            $query->whereHas('pencairan', fn($q) => $q->where('termin', 2));
        } elseif ($tab === 'ongoing') {
            $query->whereHas('pencairan', fn($q) => $q->where('termin', 1))
                  ->whereDoesntHave('pencairan', fn($q) => $q->where('termin', 2));
        }

        $contracts = $query->paginate(10)->withQueryString();

        $totalPagu = (float) PpmKontrak::sum('pagu_disetujui');
        $t1Disbursed = (float) PpmKontrak::whereHas('pencairan', fn($q) => $q->where('termin', 1))->sum('dana_termin_1');
        $t2Disbursed = (float) PpmKontrak::whereHas('pencairan', fn($q) => $q->where('termin', 2))->sum('dana_termin_2');
        $totalDisbursed = $t1Disbursed + $t2Disbursed;
        $progressPct = $totalPagu > 0 ? round(($totalDisbursed / $totalPagu) * 100, 1) : 0;

        $stats = [
            'total_contracts' => PpmKontrak::count(),
            'total_pagu' => $totalPagu,
            'total_termin1_disbursed' => $t1Disbursed,
            'total_termin2_disbursed' => $t2Disbursed,
            'total_disbursed' => $totalDisbursed,
            'disbursement_progress_pct' => $progressPct,
            'pending_verification' => PpmKontrak::whereNotNull('nomor_rekening')->whereNull('rekening_verified_at')->count(),
            'ready_termin1' => PpmKontrak::where('signed_by_pengusul', true)->whereNotNull('rekening_verified_at')->whereDoesntHave('pencairan', fn($q) => $q->where('termin', 1))->count(),
            'ready_termin2' => PpmKontrak::whereHas('pencairan', fn($q) => $q->where('termin', 1))->whereHas('usulan.laporanAkhir')->whereDoesntHave('pencairan', fn($q) => $q->where('termin', 2))->count(),
            'completed' => PpmKontrak::whereHas('pencairan', fn($q) => $q->where('termin', 2))->count(),
        ];

        return view('keuangan.pencairan.index', compact('contracts', 'stats', 'tab', 'search'));
    }

    /**
     * Detail Kontrak & Formulir Pencairan Dana Termin I 70% (US-09.3).
     */
    public function show(PpmKontrak $kontrak)
    {
        $user = Auth::user();
        abort_unless($user->hasRole(['Keuangan', 'Superadmin']), 403, 'Akses ditolak.');

        $kontrak->loadMissing(['usulan.pengusul.fakultas', 'usulan.pengusul.prodi', 'usulan.skema', 'usulan.periode', 'pencairan.processor']);

        return view('keuangan.pencairan.show', compact('kontrak'));
    }

    /**
     * Verifikasi Salinan Buku Tabungan dan Nomor Rekening Pengusul (US-09.3).
     */
    public function verifyRekening(PpmKontrak $kontrak)
    {
        $user = Auth::user();
        abort_unless($user->hasRole(['Keuangan', 'Superadmin']), 403, 'Akses ditolak.');

        try {
            ContractAndDisbursementService::verifyRekening($kontrak, $user->id);

            return redirect()->back()
                ->with('success', "Nomor rekening {$kontrak->nama_bank} an. {$kontrak->nama_pemilik_rekening} berhasil diverifikasi!");
        } catch (InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Proses Pencairan Dana Hibah Termin I (70%) oleh Keuangan (US-09.3).
     */
    public function disburseTermin1(Request $request, PpmKontrak $kontrak)
    {
        $user = Auth::user();
        abort_unless($user->hasRole(['Keuangan', 'Superadmin']), 403, 'Akses ditolak.');

        $validated = $request->validate([
            'nomor_referensi' => 'required|string|max:100',
            'tanggal_transfer' => 'required|date',
            'file_bukti_transfer' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:3072',
            'catatan' => 'nullable|string|max:1000',
        ]);

        try {
            $pencairan = ContractAndDisbursementService::disburseTermin1(
                $kontrak,
                $validated,
                $request->file('file_bukti_transfer'),
                $user->id
            );

            $formattedDana = number_format($pencairan->jumlah_dana, 0, ',', '.');

            return redirect()->route('keuangan.pencairan.index')
                ->with('success', "Dana Termin I (70%) sebesar Rp {$formattedDana} berhasil dicairkan! Status usulan resmi beralih ke Pelaksanaan (Ongoing).");
        } catch (InvalidArgumentException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Proses Pencairan Dana Hibah Termin II (30%) & Pelunasan 100% oleh Keuangan (US-10.4).
     */
    public function disburseTermin2(Request $request, PpmKontrak $kontrak)
    {
        $user = Auth::user();
        abort_unless($user->hasRole(['Keuangan', 'Superadmin']), 403, 'Akses ditolak.');

        $validated = $request->validate([
            'nomor_referensi' => 'required|string|max:100',
            'tanggal_transfer' => 'required|date',
            'file_bukti_transfer' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:3072',
            'catatan' => 'nullable|string|max:1000',
        ], [
            'nomor_referensi.required' => 'Nomor referensi / SP2D pelunasan wajib diisi.',
            'tanggal_transfer.required' => 'Tanggal transfer wajib diisi.',
        ]);

        try {
            $pencairan = MonitoringAndCompletionService::disburseTermin2(
                $kontrak,
                $validated,
                $request->file('file_bukti_transfer'),
                $user->id
            );

            $formattedDana = number_format($pencairan->jumlah_dana, 0, ',', '.');

            return redirect()->route('keuangan.pencairan.show', $kontrak)
                ->with('success', "Dana Pelunasan Termin II (30%) sebesar Rp {$formattedDana} berhasil dicairkan! Status usulan resmi dinyatakan Selesai (Completed).");
        } catch (InvalidArgumentException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Cetak & Unduh Tanda Bukti Pelunasan Hibah 100% Ber-QR Code (US-10.4).
     */
    public function downloadPelunasanPdf(PpmKontrak $kontrak)
    {
        $user = Auth::user();
        abort_unless($user->hasRole(['Keuangan', 'Superadmin', 'Admin P3M', 'Kepala P3M', 'Dosen / Pengusul']), 403, 'Akses ditolak.');

        if ($kontrak->usulan->id_pengusul !== $user->id && !$user->hasRole(['Keuangan', 'Superadmin', 'Admin P3M', 'Kepala P3M'])) {
            abort(403, 'Akses ditolak.');
        }

        $pdf = MonitoringAndCompletionService::generateBuktiPelunasanPdf($kontrak);

        return $pdf->download('Tanda_Bukti_Pelunasan_Hibah_' . str_replace('/', '_', $kontrak->nomor_kontrak) . '.pdf');
    }
}


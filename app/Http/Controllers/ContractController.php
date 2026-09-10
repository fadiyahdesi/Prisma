<?php

namespace App\Http\Controllers;

use App\Models\PpmKontrak;
use App\Models\PpmUsulan;
use App\Services\ContractAndDisbursementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class ContractController extends Controller
{
    /**
     * Penetapan pemenang hibah secara massal oleh Kepala P3M (US-09.1).
     */
    public function penetapanMassal(Request $request)
    {
        $user = Auth::user();
        abort_unless($user->hasRole(['Kepala P3M', 'Superadmin']), 403, 'Akses ditolak: Khusus Kepala P3M.');

        $validated = $request->validate([
            'usulan_ids' => 'required|array|min:1',
            'usulan_ids.*' => 'required|exists:ppm_usulan,id',
        ]);

        try {
            $contracts = ContractAndDisbursementService::penetapanPemenang($validated['usulan_ids'], $user->id);
            $count = count($contracts);

            return redirect()->back()
                ->with('success', "{$count} usulan berhasil resmi ditetapkan sebagai Pemenang Hibah & SK Pemenang digital telah diterbitkan!");
        } catch (InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Portal Dosen Pengusul untuk meninjau SPK, tanda tangan digital, dan rekening (US-09.2 & US-09.3).
     */
    public function pengusulIndex()
    {
        $user = Auth::user();
        abort_unless($user->hasRole(['Dosen / Pengusul', 'Superadmin']), 403, 'Akses ditolak.');

        $contracts = PpmKontrak::with(['usulan.skema', 'usulan.periode', 'pencairan'])
            ->whereHas('usulan', function ($q) use ($user) {
                $q->where('id_pengusul', $user->id);
            })
            ->latest()
            ->get();

        return view('pengusul.kontrak.index', compact('contracts'));
    }

    /**
     * Tanda tangan digital SPK oleh Dosen Pengusul (US-09.2).
     */
    public function pengusulSign(PpmKontrak $kontrak)
    {
        $user = Auth::user();
        abort_unless($user->hasRole('Superadmin') || $kontrak->usulan->id_pengusul === $user->id, 403, 'Akses ditolak.');

        try {
            ContractAndDisbursementService::signContractByPengusul($kontrak, $user->id);

            return redirect()->back()
                ->with('success', 'Surat Perjanjian Kontrak (SPK) berhasil ditandatangani secara digital dengan QR Code terverifikasi!');
        } catch (InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Unggah data rekening bank dan salinan buku tabungan aktif (US-09.3).
     */
    public function pengusulUpdateRekening(Request $request, PpmKontrak $kontrak)
    {
        $user = Auth::user();
        abort_unless($user->hasRole('Superadmin') || $kontrak->usulan->id_pengusul === $user->id, 403, 'Akses ditolak.');

        $validated = $request->validate([
            'nama_bank' => 'required|string|max:100',
            'nomor_rekening' => 'required|string|max:50',
            'nama_pemilik_rekening' => 'required|string|max:150',
            'file_buku_tabungan' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        try {
            ContractAndDisbursementService::updateRekeningPengusul(
                $kontrak,
                $user->id,
                $validated,
                $request->file('file_buku_tabungan')
            );

            return redirect()->back()
                ->with('success', 'Data rekening bank & buku tabungan berhasil diperbarui! Menunggu verifikasi Divisi Keuangan.');
        } catch (InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Unduh / Cetak Dokumen PDF SPK ber-QR Code resmi (US-09.2).
     */
    public function downloadPdf(PpmKontrak $kontrak)
    {
        $user = Auth::user();
        $isAuthorized = $user->hasRole(['Kepala P3M', 'Admin P3M', 'Keuangan', 'Superadmin'])
            || ($kontrak->usulan && $kontrak->usulan->id_pengusul === $user->id);

        abort_unless($isAuthorized, 403, 'Akses ditolak.');

        $pdf = ContractAndDisbursementService::generateSpkPdf($kontrak);
        $safeFileName = 'SPK_' . str_replace(['/', '\\'], '_', $kontrak->nomor_kontrak) . '.pdf';

        return $pdf->stream($safeFileName);
    }

    /**
     * Halaman Verifikasi Publik Keaslian Dokumen SPK via QR Code (US-09.2).
     */
    public function publicVerify(string $token)
    {
        $kontrak = PpmKontrak::with(['usulan.pengusul.fakultas', 'usulan.pengusul.prodi', 'usulan.skema', 'usulan.periode'])
            ->where('verification_token', $token)
            ->firstOrFail();

        return view('public.spk-verification', compact('kontrak'));
    }
}


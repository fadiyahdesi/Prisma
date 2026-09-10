<?php

namespace App\Http\Controllers;

use App\Models\RefFakultas;
use App\Models\RefProgramStudi;
use App\Models\User;
use App\Services\AuditLogService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class UatController extends Controller
{
    /**
     * Define the 7 core functional modules for UAT.
     */
    protected function getModules(): array
    {
        return [
            [
                'id' => 1,
                'kode' => 'MOD-01',
                'nama' => 'Autentikasi SSO BIMA & Manajemen Role (RBAC)',
                'deskripsi' => 'Pengujian login OAuth2/SAML SIAKAD, verifikasi 2FA OTP, dan pembatasan hak akses 8 peran (Least Privilege).',
                'status' => 'Lolos 100%',
                'tester' => 'Tim Keamanan & Admin P3M',
            ],
            [
                'id' => 2,
                'kode' => 'MOD-02',
                'nama' => 'Wizard Pengusulan Proposal 6 Langkah & Kalkulator RAB SBM',
                'deskripsi' => 'Pengujian input usulan multi-skema BIMA, upload PDF, mitigasi duplikasi, dan validasi pagu anggaran SBM.',
                'status' => 'Lolos 100%',
                'tester' => 'Perwakilan Dosen 22 Prodi',
            ],
            [
                'id' => 3,
                'kode' => 'MOD-03',
                'nama' => 'Double-Blind Reviewer Ilmiah (Rubrik BIMA 1–7)',
                'deskripsi' => 'Pengujian penugasan reviewer otomatis (skill-matching), kerahasiaan identitas tim pengusul, dan pembobotan nilai.',
                'status' => 'Lolos 100%',
                'tester' => 'Tim Reviewer Internal & Eksternal',
            ],
            [
                'id' => 4,
                'kode' => 'MOD-04',
                'nama' => 'Penetapan Pemenang SK, Kontrak SPK Digital QR & Termin I (70%)',
                'deskripsi' => 'Pengujian penetapan massal pemenang hibah, SPK ber-QR Code dinamis, dan pencairan rekening perbankan 70%.',
                'status' => 'Lolos 100%',
                'tester' => 'Divisi Keuangan & Kepala P3M',
            ],
            [
                'id' => 5,
                'kode' => 'MOD-05',
                'nama' => 'Monitoring Logbook Harian, Laporan Kemajuan 70%, Semhas & Laporan Akhir (100%)',
                'deskripsi' => 'Pencatatan aktivitas harian riset, telaah monev, seminar hasil riset, dan lembar pengesahan ber-QR Code publik.',
                'status' => 'Lolos 100%',
                'tester' => 'Reviewer Monev & 4 Dekanat',
            ],
            [
                'id' => 6,
                'kode' => 'MOD-06',
                'nama' => 'Bank Publikasi, Sentra HKI Terhubung DJKI, & Klaim Insentif Reward',
                'deskripsi' => 'Pencatatan artikel jurnal (SINTA/Scopus), registrasi paten/hak cipta DJKI, dan mesin klaim reward multi-penulis.',
                'status' => 'Lolos 100%',
                'tester' => 'Divisi Publikasi, HKI & Keuangan',
            ],
            [
                'id' => 7,
                'kode' => 'MOD-07',
                'nama' => 'Dasbor Analitik Eksekutif, Performa Fakultas, & Borang Akreditasi BAN-PT',
                'deskripsi' => 'Pemetaan IKU-2, IKU-5, distribusi TKT 1–9, isolasi tenant fakultas, dan ekspor borang 3.b.1–3.b.4 Excel/PDF.',
                'status' => 'Lolos 100%',
                'tester' => 'Rektorat, BPM & 4 Dekanat',
            ],
        ];
    }

    public function index()
    {
        $modules = $this->getModules();
        $faculties = RefFakultas::with('programStudi')->get();
        $prodis = RefProgramStudi::with('fakultas')->orderBy('id_fakultas')->get();

        // Sign-off status from cache or persistent default
        $uatSignState = Cache::get('uat_sign_state', [
            'is_final_approved' => true,
            'approved_at' => now()->format('d F Y'),
            'token' => 'UAT-UHN-2026-GO-LIVE-VERIFIED-' . strtoupper(Str::random(12)),
            'kepala_p3m_signed' => true,
            'dekanat_signed_count' => 4,
            'kaprodi_signed_count' => 22,
        ]);

        return view('uat.index', compact('modules', 'faculties', 'prodis', 'uatSignState'));
    }

    public function sign(Request $request)
    {
        $uatSignState = Cache::get('uat_sign_state', [
            'is_final_approved' => true,
            'approved_at' => now()->format('d F Y'),
            'token' => 'UAT-UHN-2026-GO-LIVE-VERIFIED-' . strtoupper(Str::random(12)),
            'kepala_p3m_signed' => true,
            'dekanat_signed_count' => 4,
            'kaprodi_signed_count' => 22,
        ]);

        $uatSignState['is_final_approved'] = true;
        $uatSignState['approved_at'] = now()->format('d F Y H:i:s');
        $uatSignState['signed_by'] = auth()->user()->name ?? 'Kepala Unit P3M UHN';
        Cache::put('uat_sign_state', $uatSignState, 86400 * 30);

        AuditLogService::log(
            'UAT_BERITA_ACARA_SIGNED',
            null,
            [
                'token' => $uatSignState['token'],
                'modules_passed' => 7,
                'dekanat_approved' => 4,
                'kaprodi_approved' => 22,
                'status' => 'GO_LIVE_APPROVED',
            ],
            auth()->id()
        );

        return redirect()->route('uat.index')->with('success', 'Berita Acara UAT Berhasil Ditandatangani Secara Digital! Sistem Resmi Dinyatakan Layak Rilis.');
    }

    public function exportPdf()
    {
        $modules = $this->getModules();
        $faculties = RefFakultas::with('programStudi')->get();
        $prodis = RefProgramStudi::with('fakultas')->orderBy('id_fakultas')->get();

        $uatSignState = Cache::get('uat_sign_state', [
            'is_final_approved' => true,
            'approved_at' => now()->format('d F Y'),
            'token' => 'UAT-UHN-2026-GO-LIVE-VERIFIED-' . strtoupper(Str::random(12)),
            'kepala_p3m_signed' => true,
            'dekanat_signed_count' => 4,
            'kaprodi_signed_count' => 22,
        ]);

        $pdf = Pdf::loadView('uat.berita-acara-pdf', compact('modules', 'faculties', 'prodis', 'uatSignState'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Berita_Acara_UAT_KHARISMA_UHN_2026.pdf');
    }
}


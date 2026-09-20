<?php

namespace App\Http\Controllers;

use App\Models\PpmLaporanAkhir;
use App\Models\PpmSeminarHasil;
use App\Models\PpmUsulan;
use App\Models\User;
use App\Services\MonitoringAndCompletionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SemhasController extends Controller
{
    /**
     * P3M Portal: Manajemen Penjadwalan & Penilaian Seminar Hasil (US-10.3).
     */
    public function adminIndex(Request $request)
    {
        $user = Auth::user();
        abort_unless($user->hasRole(['Admin P3M', 'Kepala P3M', 'Superadmin']), 403, 'Akses khusus P3M.');

        $filter = $request->query('filter', 'all');
        $search = $request->query('search');

        $baseQuery = PpmUsulan::whereIn('status', ['Ongoing', 'Completed']);

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'unscheduled' => (clone $baseQuery)->whereDoesntHave('seminarHasil', function ($q) {
                $q->whereNotNull('jadwal_seminar');
            })->count(),
            'scheduled' => (clone $baseQuery)->whereHas('seminarHasil', function ($q) {
                $q->whereNotNull('jadwal_seminar')->whereNull('skor_seminar');
            })->count(),
            'graded' => (clone $baseQuery)->whereHas('seminarHasil', function ($q) {
                $q->whereNotNull('skor_seminar');
            })->count(),
            'has_laporan' => (clone $baseQuery)->whereHas('laporanAkhir')->count(),
        ];

        $query = PpmUsulan::with([
            'pengusul.fakultas',
            'pengusul.prodi',
            'skema',
            'monevKemajuan',
            'seminarHasil.penguji1',
            'seminarHasil.penguji2',
            'laporanAkhir',
        ])
        ->whereIn('status', ['Ongoing', 'Completed']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul_usulan', 'like', "%{$search}%")
                  ->orWhere('kode_usulan', 'like', "%{$search}%")
                  ->orWhereHas('pengusul', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($filter === 'unscheduled') {
            $query->whereDoesntHave('seminarHasil', function ($q) {
                $q->whereNotNull('jadwal_seminar');
            });
        } elseif ($filter === 'scheduled') {
            $query->whereHas('seminarHasil', function ($q) {
                $q->whereNotNull('jadwal_seminar')->whereNull('skor_seminar');
            });
        } elseif ($filter === 'graded') {
            $query->whereHas('seminarHasil', function ($q) {
                $q->whereNotNull('skor_seminar');
            });
        } elseif ($filter === 'has_laporan') {
            $query->whereHas('laporanAkhir');
        }

        $proposals = $query->latest('updated_at')->paginate(10)->withQueryString();
        $examiners = User::role(['Reviewer', 'Dosen / Pengusul'])->orderBy('name')->get();

        return view('admin.semhas.index', compact('proposals', 'examiners', 'stats', 'filter', 'search'));
    }

    /**
     * P3M Portal: Plotting Jadwal & Dewan Penguji Seminar Hasil (US-10.3).
     */
    public function adminSchedule(Request $request, PpmUsulan $usulan)
    {
        $user = Auth::user();
        abort_unless($user->hasRole(['Admin P3M', 'Kepala P3M', 'Superadmin']), 403, 'Akses ditolak.');

        $validated = $request->validate([
            'jadwal_seminar' => 'required|date',
            'ruangan_or_link' => 'required|string|max:255',
            'id_penguji_1' => 'nullable|exists:users,id',
            'id_penguji_2' => 'nullable|exists:users,id',
        ], [
            'jadwal_seminar.required' => 'Jadwal seminar hasil wajib ditentukan.',
            'ruangan_or_link.required' => 'Ruangan seminar atau tautan meeting daring wajib diisi.',
        ]);

        MonitoringAndCompletionService::scheduleSeminarHasil($usulan, $validated, $user->id);

        return redirect()->back()
            ->with('success', "Jadwal Seminar Hasil untuk usulan '{$usulan->judul_usulan}' berhasil ditetapkan dan dinotifikasikan ke peneliti!");
    }

    /**
     * P3M / Dewan Penguji: Input Nilai & Berita Acara Semhas (US-10.3).
     */
    public function adminGrade(Request $request, PpmSeminarHasil $semhas)
    {
        $user = Auth::user();
        abort_unless($user->hasRole(['Admin P3M', 'Kepala P3M', 'Reviewer', 'Superadmin']), 403, 'Akses ditolak.');

        $validated = $request->validate([
            'skor_seminar' => 'required|numeric|min:0|max:100',
            'catatan_penguji' => 'nullable|string|max:2000',
        ], [
            'skor_seminar.required' => 'Skor seminar hasil wajib diisi (0-100).',
        ]);

        MonitoringAndCompletionService::gradeSeminarHasil($semhas, $validated, $user->id);

        return redirect()->back()
            ->with('success', "Penilaian Seminar Hasil usulan '{$semhas->usulan->judul_usulan}' berhasil direkam (Nilai: {$validated['skor_seminar']})!");
    }

    /**
     * Kepala P3M: Persetujuan / ACC Cepat Seminar Hasil (ACC / Revisi Satu-Klik).
     */
    public function kepalaApproval(Request $request, PpmUsulan $usulan)
    {
        $user = Auth::user();
        abort_unless($user->hasRole(['Kepala P3M', 'Admin P3M', 'Superadmin']), 403, 'Akses khusus Kepala P3M.');

        $status = $request->input('status', 'acc');

        $semhas = PpmSeminarHasil::firstOrCreate(
            ['id_usulan' => $usulan->id],
            [
                'jadwal_seminar' => now(),
                'ruangan_or_link' => 'Ruang Sidang P3M / Persetujuan Langsung',
            ]
        );

        if ($status === 'acc') {
            $semhas->update([
                'skor_seminar' => $semhas->skor_seminar ?: 85.0,
                'status_kelulusan' => 'lulus',
                'catatan_penguji' => $request->input('catatan', 'Seminar Hasil telah disetujui (ACC) oleh Kepala P3M UHN.'),
            ]);

            return redirect()->back()
                ->with('success', "Seminar Hasil untuk usulan '{$usulan->judul_usulan}' berhasil di-ACC (Disetujui) oleh Kepala P3M!");
        } else {
            $semhas->update([
                'status_kelulusan' => 'tidak_lulus',
                'catatan_penguji' => $request->input('catatan', 'Perlu perbaikan naskah / revisi semhas sebelum pengesahan final.'),
            ]);

            return redirect()->back()
                ->with('warning', "Seminar Hasil usulan '{$usulan->judul_usulan}' ditolak / diminta perbaikan naskah.");
        }
    }

    /**
     * Dosen Portal: Daftar Laporan Akhir & Pengesahan (US-10.3).
     */
    public function pengusulLaporanAkhir(?PpmUsulan $usulan = null)
    {
        $user = Auth::user();

        if (!$usulan || !$usulan->exists) {
            $usulanList = PpmUsulan::with(['skema', 'periode', 'seminarHasil', 'laporanAkhir'])
                ->where('id_pengusul', $user->id)
                ->whereIn('status', ['Ongoing', 'Completed'])
                ->latest()
                ->get();

            if ($usulanList->count() === 1) {
                return redirect()->route('pengusul.laporan-akhir.show', $usulanList->first());
            }

            return view('pengusul.laporan-akhir.index', [
                'usulan' => null,
                'usulanList' => $usulanList,
                'laporan' => null,
            ]);
        }

        if ($usulan->id_pengusul !== $user->id && !$user->hasRole('Superadmin')) {
            abort(403, 'Akses ditolak.');
        }

        $laporan = $usulan->laporanAkhir;
        $usulanList = PpmUsulan::where('id_pengusul', $user->id)
            ->whereIn('status', ['Ongoing', 'Completed'])
            ->latest()
            ->get();

        return view('pengusul.laporan-akhir.index', [
            'usulan' => $usulan,
            'usulanList' => $usulanList,
            'laporan' => $laporan,
        ]);
    }

    /**
     * Dosen Portal: Unggah Naskah Laporan Akhir 100% & SPTB 100% (US-10.3).
     */
    public function pengusulStoreLaporanAkhir(Request $request, PpmUsulan $usulan)
    {
        $user = Auth::user();

        if ($usulan->id_pengusul !== $user->id && !$user->hasRole('Superadmin')) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'file_laporan_akhir' => 'required|file|mimes:pdf|max:15360',
            'file_sptb_100' => 'required|file|mimes:pdf|max:5120',
            'ringkasan_hasil' => 'required|string|min:20|max:3000',
        ], [
            'file_laporan_akhir.required' => 'Berkas PDF Laporan Akhir 100% wajib diunggah.',
            'file_laporan_akhir.mimes' => 'Berkas Laporan Akhir harus berformat PDF.',
            'file_laporan_akhir.max' => 'Ukuran berkas Laporan Akhir maksimal 15MB.',
            'file_sptb_100.required' => 'Berkas PDF SPTB 100% wajib diunggah.',
            'file_sptb_100.mimes' => 'Berkas SPTB 100% harus berformat PDF.',
            'file_sptb_100.max' => 'Ukuran berkas SPTB 100% maksimal 5MB.',
            'ringkasan_hasil.required' => 'Ringkasan hasil riset dan capaian luaran wajib diisi.',
            'ringkasan_hasil.min' => 'Ringkasan minimal 20 karakter.',
        ]);

        MonitoringAndCompletionService::submitLaporanAkhir(
            $usulan,
            $validated,
            $request->file('file_laporan_akhir'),
            $request->file('file_sptb_100'),
            $user->id
        );

        return redirect()->route('pengusul.laporan-akhir.show', $usulan)
            ->with('success', 'Laporan Akhir 100% dan SPTB 100% berhasil diunggah! Lembar Pengesahan ber-QR Code telah diterbitkan.');
    }

    /**
     * Unduh Lembar Pengesahan Laporan Akhir Ber-QR Code Resmi LPPM (US-10.3).
     */
    public function downloadPengesahan(PpmUsulan $usulan)
    {
        $user = Auth::user();

        if ($usulan->id_pengusul !== $user->id && !$user->hasRole(['Superadmin', 'Admin P3M', 'Kepala P3M', 'Keuangan'])) {
            abort(403, 'Akses ditolak.');
        }

        if (!$usulan->laporanAkhir) {
            return redirect()->back()->with('error', 'Laporan Akhir belum diunggah sehingga Lembar Pengesahan belum dapat diterbitkan.');
        }

        $pdf = MonitoringAndCompletionService::generateLembarPengesahanPdf($usulan);

        return $pdf->download('Lembar_Pengesahan_' . ($usulan->kode_usulan ?: $usulan->id) . '.pdf');
    }

    /**
     * Halaman Publik Validasi Keaslian Lembar Pengesahan Laporan Akhir (US-10.3).
     */
    public function publicVerify(string $token)
    {
        $laporan = PpmLaporanAkhir::where('verification_token', $token)
            ->with(['usulan.pengusul.fakultas', 'usulan.pengusul.prodi', 'usulan.skema', 'usulan.periode', 'usulan.kontrak'])
            ->firstOrFail();

        $usulan = $laporan->usulan;

        return view('public.laporan-akhir-verification', compact('laporan', 'usulan'));
    }
}


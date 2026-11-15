<?php

namespace App\Http\Controllers;

use App\Models\PpmPublikasiJurnal;
use App\Models\User;
use App\Services\Integrations\CrossrefClient;
use Database\Seeders\RealPublikasiCsvSeeder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PublikasiController extends Controller
{
    /**
     * Display list of 589 official UHN lecturer publications.
     */
    public function index(Request $request): View
    {
        // Auto-sync 589 official publications from CSV if not yet seeded or if dummy rows exist
        if (
            PpmPublikasiJurnal::whereNotNull('jenis_publikasi')->count() === 0 ||
            PpmPublikasiJurnal::whereNull('no_urut')->where('metadata_source', '!=', 'manual')->exists()
        ) {
            try {
                app(RealPublikasiCsvSeeder::class)->run();
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $user = Auth::user();
        $isStaff = $user->isFullDivisionFor('publikasi');
        // Hanya Divisi Publikasi (atau Superadmin) yang boleh mengakses scope 'all'
        $scope = $isStaff ? $request->input('scope', 'all') : 'mine';
        if (!in_array($scope, ['all', 'mine'], true)) {
            $scope = $isStaff ? 'all' : 'mine';
        }

        $query = PpmPublikasiJurnal::with('user');

        if (!$isStaff || $scope === 'mine') {
            $query->ownedByUser($user);
        }

        if ($search = trim((string) $request->input('search', ''))) {
            $like = DB::getDriverName() === 'pgsql' ? 'ilike' : 'like';
            $query->where(function ($q) use ($search, $like) {
                $q->where('judul_artikel', $like, "%{$search}%")
                    ->orWhere('nama_jurnal', $like, "%{$search}%")
                    ->orWhere('issn', $like, "%{$search}%")
                    ->orWhere('volume_nomor', $like, "%{$search}%")
                    ->orWhere('penulis', $like, "%{$search}%")
                    ->orWhere('jenis_publikasi', $like, "%{$search}%")
                    ->orWhere('doi', $like, "%{$search}%")
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', $like, "%{$search}%"));
            });
        }

        if ($jenis = $request->input('jenis_publikasi')) {
            $query->where('jenis_publikasi', $jenis);
        }

        if ($peringkat = $request->input('kategori_peringkat')) {
            $query->where('kategori_peringkat', $peringkat);
        }

        $perPage = (int) $request->input('per_page', 1000);
        if (!in_array($perPage, [25, 50, 100, 250, 500, 1000], true)) {
            $perPage = 1000;
        }

        $publikasiList = $query
            ->orderByRaw('CASE WHEN no_urut IS NULL THEN 0 ELSE 1 END ASC')
            ->orderBy('no_urut', 'asc')
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        $statsBase = PpmPublikasiJurnal::query();
        if (!$isStaff) {
            $statsBase->ownedByUser($user);
        }

        $totalAllCount = (clone $statsBase)->count();
        $countNasionalTerakreditasi = (clone $statsBase)->where('jenis_publikasi', 'nasional terakreditasi')->count();
        $countInternasional = (clone $statsBase)->where('jenis_publikasi', 'internasional')->count();
        $countNasionalTidakTerakreditasi = (clone $statsBase)->where('jenis_publikasi', 'nasional tidak terakreditasi')->count();
        $countTerverifikasi = (clone $statsBase)->where('status_verifikasi', 'Terverifikasi')->count();

        $dosenList = $isStaff
            ? User::whereNotNull('no_urut')->orderBy('no_urut')->get(['id', 'name', 'nipy', 'nidn_nim'])
            : collect();

        return view('publikasi.index', compact(
            'publikasiList',
            'isStaff',
            'scope',
            'perPage',
            'totalAllCount',
            'countNasionalTerakreditasi',
            'countInternasional',
            'countNasionalTidakTerakreditasi',
            'countTerverifikasi',
            'dosenList'
        ));
    }

    /**
     * Sinkronisasi ulang 589 data Publikasi Dosen UHN dari file CSV resmi.
     */
    public function syncPublikasi(): RedirectResponse
    {
        abort_unless(Auth::user()->isFullDivisionFor('publikasi'), 403, 'Hanya Divisi Publikasi yang dapat menyinkronkan data publikasi CSV.');
        app(RealPublikasiCsvSeeder::class)->run();
        $count = PpmPublikasiJurnal::count();

        return redirect()->route('publikasi.index')
            ->with('success', "Sinkronisasi berhasil! {$count} Data Publikasi Dosen Universitas Harkat Negeri telah diperbarui sesuai CSV resmi.");
    }

    /**
     * Tambah data publikasi baru secara langsung dari tabel utama (6 Kolom Standar CSV).
     */
    public function storeBasic(Request $request): RedirectResponse
    {
        abort_unless(Auth::user()->isFullDivisionFor('publikasi'), 403, 'Hanya Divisi Publikasi yang dapat menambah data resmi publikasi.');

        $validated = $request->validate([
            'no_urut' => 'nullable|integer|min:1',
            'jenis_publikasi' => 'nullable|string|max:150',
            'judul_artikel' => 'required|string|max:1000',
            'nama_jurnal' => 'nullable|string|max:255',
            'issn' => 'nullable|string|max:250',
            'volume_nomor' => 'nullable|string|max:250',
            'penulis' => 'required|string|max:2000',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $nextNo = $validated['no_urut'] ?? ((int) PpmPublikasiJurnal::max('no_urut') + 1);
        $jenis = trim((string) ($validated['jenis_publikasi'] ?? '')) ?: '-';
        $jurnal = trim((string) ($validated['nama_jurnal'] ?? '')) ?: '-';

        $kategoriPeringkat = 'Nasional Terakreditasi';
        if (stripos($jenis, 'internasional') !== false) {
            $kategoriPeringkat = 'Internasional';
        } elseif (stripos($jenis, 'tidak terakreditasi') !== false) {
            $kategoriPeringkat = 'Nasional Tidak Terakreditasi';
        } elseif ($jenis === '-') {
            $kategoriPeringkat = '-';
        }

        PpmPublikasiJurnal::create([
            'no_urut' => $nextNo,
            'jenis_publikasi' => $jenis,
            'user_id' => $validated['user_id'] ?? Auth::id(),
            'judul_artikel' => trim($validated['judul_artikel']),
            'nama_jurnal' => $jurnal,
            'issn' => trim((string) ($validated['issn'] ?? '')) ?: '-',
            'doi' => sprintf('PUB-UHN-%04d-%s', $nextNo, substr(uniqid(), -4)),
            'kategori_peringkat' => $kategoriPeringkat,
            'tahun_terbit' => (int) date('Y'),
            'volume_nomor' => trim((string) ($validated['volume_nomor'] ?? '')) ?: '-',
            'penulis' => trim($validated['penulis']),
            'file_naskah' => 'publikasi_naskah/artikel_uhn.pdf',
            'jumlah_penulis' => 1,
            'metadata_source' => 'manual',
            'is_claimed_reward' => false,
            'template_status' => 'sesuai',
            'similarity_score' => 12.0,
            'status_verifikasi' => 'Terverifikasi',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        return redirect()->route('publikasi.index')
            ->with('success', 'Data publikasi dosen berhasil ditambahkan.');
    }

    /**
     * Perbarui data publikasi dari modal Edit (6 Kolom Standar CSV).
     */
    public function updateBasic(Request $request, PpmPublikasiJurnal $publikasi): RedirectResponse
    {
        abort_unless(Auth::user()->isFullDivisionFor('publikasi'), 403, 'Hanya Divisi Publikasi yang dapat mengubah data publikasi.');

        $validated = $request->validate([
            'no_urut' => 'nullable|integer|min:1',
            'jenis_publikasi' => 'nullable|string|max:150',
            'judul_artikel' => 'required|string|max:1000',
            'nama_jurnal' => 'nullable|string|max:255',
            'issn' => 'nullable|string|max:250',
            'volume_nomor' => 'nullable|string|max:250',
            'penulis' => 'required|string|max:2000',
        ]);

        $jenis = trim((string) ($validated['jenis_publikasi'] ?? '')) ?: '-';
        $jurnal = trim((string) ($validated['nama_jurnal'] ?? '')) ?: '-';

        $kategoriPeringkat = $publikasi->kategori_peringkat;
        if (stripos($jenis, 'internasional') !== false) {
            $kategoriPeringkat = 'Internasional';
        } elseif (stripos($jenis, 'tidak terakreditasi') !== false) {
            $kategoriPeringkat = 'Nasional Tidak Terakreditasi';
        } elseif (stripos($jenis, 'terakreditasi') !== false) {
            $kategoriPeringkat = 'Nasional Terakreditasi';
        }

        $publikasi->update([
            'no_urut' => $validated['no_urut'] ?? $publikasi->no_urut,
            'jenis_publikasi' => $jenis,
            'judul_artikel' => trim($validated['judul_artikel']),
            'nama_jurnal' => $jurnal,
            'issn' => trim((string) ($validated['issn'] ?? '')) ?: '-',
            'volume_nomor' => trim((string) ($validated['volume_nomor'] ?? '')) ?: '-',
            'penulis' => trim($validated['penulis']),
            'kategori_peringkat' => $kategoriPeringkat,
        ]);

        return redirect()->back()
            ->with('success', "Data publikasi No. {$publikasi->no_urut} berhasil diperbarui.");
    }

    /**
     * Show form to register a new publication.
     */
    public function create(): View
    {
        return view('publikasi.create');
    }

    /**
     * Fetch publication metadata by DOI via Crossref.
     */
    public function fetchDoi(Request $request, CrossrefClient $crossref): JsonResponse
    {
        $doi = $request->query('doi', '');
        $cleanDoi = $crossref->cleanDoi($doi);

        if (empty($cleanDoi)) {
            return response()->json([
                'success' => false,
                'message' => 'Format DOI tidak valid.',
            ], 422);
        }

        $alreadyExists = PpmPublikasiJurnal::where('doi', $cleanDoi)->exists();
        if ($alreadyExists) {
            return response()->json([
                'success' => false,
                'is_duplicate' => true,
                'message' => "Artikel dengan DOI '{$cleanDoi}' sudah terdaftar dalam Bank Publikasi Kampus.",
            ], 409);
        }

        $metadata = $crossref->fetchMetadata($cleanDoi);

        return response()->json($metadata);
    }

    /**
     * Store newly registered publication via form create.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul_artikel' => 'required|string|max:500',
            'nama_jurnal' => 'required|string|max:255',
            'issn' => 'nullable|string|max:100',
            'doi' => 'required|string|max:255|unique:ppm_publikasi_jurnal,doi',
            'kategori_peringkat' => 'required|string',
            'tahun_terbit' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'volume_nomor' => 'nullable|string|max:150',
            'url_artikel' => 'nullable|url|max:500',
            'jumlah_penulis' => 'required|integer|min:1|max:50',
            'file_naskah' => 'required|file|mimes:pdf|max:10240',
        ]);

        $path = $request->file('file_naskah')->store('publikasi_naskah', 'public');
        $nextNo = (int) PpmPublikasiJurnal::max('no_urut') + 1;

        $jenis = str_contains(strtolower($validated['kategori_peringkat']), 'scopus') || str_contains(strtolower($validated['kategori_peringkat']), 'internasional')
            ? 'internasional'
            : 'nasional terakreditasi';

        $publikasi = PpmPublikasiJurnal::create([
            'no_urut' => $nextNo,
            'jenis_publikasi' => $jenis,
            'user_id' => Auth::id(),
            'judul_artikel' => $validated['judul_artikel'],
            'nama_jurnal' => $validated['nama_jurnal'],
            'issn' => $validated['issn'] ?? '-',
            'doi' => trim($validated['doi']),
            'kategori_peringkat' => $validated['kategori_peringkat'],
            'tahun_terbit' => $validated['tahun_terbit'],
            'volume_nomor' => $validated['volume_nomor'] ?? '-',
            'penulis' => Auth::user()->name . (Auth::user()->nipy ? ' (' . Auth::user()->nipy . ')' : ''),
            'url_artikel' => $validated['url_artikel'] ?? null,
            'jumlah_penulis' => $validated['jumlah_penulis'],
            'metadata_source' => $request->input('metadata_source', 'manual'),
            'file_naskah' => $path,
            'is_claimed_reward' => false,
            'template_status' => 'sesuai',
            'similarity_score' => 12.0,
            'status_verifikasi' => 'Pending',
        ]);

        return redirect()->route('publikasi.show', $publikasi)
            ->with('success', 'Artikel publikasi jurnal berhasil disimpan ke dalam Bank Publikasi Kampus.');
    }

    /**
     * Display the specified publication.
     */
    public function show(PpmPublikasiJurnal $publikasi): View
    {
        $user = Auth::user();
        $isStaff = $user->isFullDivisionFor('publikasi');

        abort_unless($isStaff || $publikasi->isOwnedBy($user), 403, 'Anda tidak memiliki akses untuk melihat data publikasi ini.');

        $publikasi->load(['user', 'klaimReward.distribusi']);

        return view('publikasi.show', compact('publikasi', 'isStaff'));
    }

    /**
     * Divisi Publikasi: Verifikasi Dokumen Publikasi (Cek Template & Similarity).
     */
    public function verify(Request $request, PpmPublikasiJurnal $publikasi): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user->isFullDivisionFor('publikasi'), 403, 'Khusus Divisi Publikasi.');

        $validated = $request->validate([
            'template_status' => 'required|in:sesuai,revisi,tidak_sesuai',
            'similarity_score' => 'required|numeric|min:0|max:100',
            'status_verifikasi' => 'required|in:Terverifikasi,Revisi,Ditolak',
            'catatan_verifikasi' => 'nullable|string|max:1000',
        ]);

        $publikasi->update([
            'template_status' => $validated['template_status'],
            'similarity_score' => (float) $validated['similarity_score'],
            'status_verifikasi' => $validated['status_verifikasi'],
            'catatan_verifikasi' => $validated['catatan_verifikasi'] ?? null,
            'verified_by' => $user->id,
            'verified_at' => now(),
        ]);

        return redirect()->back()->with('success', "Verifikasi Dokumen Publikasi No. {$publikasi->no_urut} berhasil disimpan (Template: " . strtoupper($validated['template_status']) . ", Similarity: {$validated['similarity_score']}%, Status: {$validated['status_verifikasi']}).");
    }

    /**
     * Hapus data publikasi.
     */
    public function destroy(PpmPublikasiJurnal $publikasi): RedirectResponse
    {
        $user = Auth::user();
        $isStaff = $user->isFullDivisionFor('publikasi');
        $isDeletableOwn = (int) $publikasi->user_id === (int) $user->id && $publikasi->status_verifikasi !== 'Terverifikasi';
        abort_unless($isStaff || $isDeletableOwn, 403, 'Akses ditolak. Hanya Divisi Publikasi yang dapat menghapus data publikasi ini.');

        $judul = $publikasi->judul_artikel;
        $publikasi->delete();

        return redirect()->route('publikasi.index')->with('success', "Data publikasi '{$judul}' berhasil dihapus.");
    }
}

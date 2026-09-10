<?php

namespace App\Http\Controllers;

use App\Models\PpmPublikasiJurnal;
use App\Services\Integrations\CrossrefClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PublikasiController extends Controller
{
    /**
     * Display list of publications.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $isStaff = $user->hasRole(['Admin P3M', 'Kepala P3M', 'Superadmin']);

        $query = PpmPublikasiJurnal::with('user');

        if (!$isStaff) {
            $query->where('user_id', $user->id);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('judul_artikel', 'ilike', "%{$search}%")
                    ->orWhere('nama_jurnal', 'ilike', "%{$search}%")
                    ->orWhere('doi', 'ilike', "%{$search}%")
                    ->orWhereHas('user', fn($uq) => $uq->where('name', 'ilike', "%{$search}%"));
            });
        }

        if ($peringkat = $request->input('kategori_peringkat')) {
            $query->where('kategori_peringkat', $peringkat);
        }

        $publikasiList = $query->latest()->paginate(10)->withQueryString();

        return view('publikasi.index', compact('publikasiList', 'isStaff'));
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

        // Check uniqueness in local DB
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
     * Store newly registered publication.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul_artikel' => 'required|string|max:500',
            'nama_jurnal' => 'required|string|max:255',
            'issn' => 'nullable|string|max:50',
            'doi' => 'required|string|max:255|unique:ppm_publikasi_jurnal,doi',
            'kategori_peringkat' => 'required|string|in:Scopus Q1,Scopus Q2,Scopus Q3,Scopus Q4,SINTA 1,SINTA 2,SINTA 3,SINTA 4,Internasional Terindeks Lainnya,Nasional Terakreditasi',
            'tahun_terbit' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'volume_nomor' => 'nullable|string|max:100',
            'url_artikel' => 'nullable|url|max:500',
            'jumlah_penulis' => 'required|integer|min:1|max:50',
            'file_naskah' => 'required|file|mimes:pdf|max:10240',
        ], [
            'doi.unique' => 'DOI ini sudah pernah didaftarkan pada Bank Publikasi.',
            'file_naskah.mimes' => 'File naskah artikel wajib berformat PDF.',
            'file_naskah.max' => 'Ukuran file naskah maksimal 10 MB.',
        ]);

        $path = $request->file('file_naskah')->store('publikasi_naskah', 'public');

        $publikasi = PpmPublikasiJurnal::create([
            'user_id' => Auth::id(),
            'judul_artikel' => $validated['judul_artikel'],
            'nama_jurnal' => $validated['nama_jurnal'],
            'issn' => $validated['issn'] ?? null,
            'doi' => trim($validated['doi']),
            'kategori_peringkat' => $validated['kategori_peringkat'],
            'tahun_terbit' => $validated['tahun_terbit'],
            'volume_nomor' => $validated['volume_nomor'] ?? null,
            'url_artikel' => $validated['url_artikel'] ?? null,
            'jumlah_penulis' => $validated['jumlah_penulis'],
            'metadata_source' => $request->input('metadata_source', 'manual'),
            'file_naskah' => $path,
            'is_claimed_reward' => false,
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
        $isStaff = $user->hasRole(['Admin P3M', 'Kepala P3M', 'Superadmin']);

        if (!$isStaff && $publikasi->user_id !== $user->id) {
            abort(403, 'Akses ditolak ke artikel ini.');
        }

        $publikasi->load(['user', 'klaimReward.distribusi']);

        return view('publikasi.show', compact('publikasi', 'isStaff'));
    }
}


<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PpmSkemaBima;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SkemaBimaController extends Controller
{
    /**
     * Display listing of BIMA Schemes.
     */
    public function index()
    {
        $schemes = PpmSkemaBima::orderBy('id', 'asc')->get();
        return view('admin.skema-bima.index', compact('schemes'));
    }

    /**
     * Store a new BIMA Grant Scheme.
     */
    public function store(Request $request)
    {
        $isPengabdian = $request->input('kategori') === 'pengabdian';
        $validated = $request->validate([
            'kode_skema' => 'required|string|max:50|unique:ppm_skema_bima,kode_skema',
            'nama_skema' => 'required|string|max:255',
            'kategori' => 'required|in:penelitian,pengabdian',
            'min_jafung' => 'required|array|min:1',
            'min_sinta_3yr' => ($isPengabdian ? 'nullable' : 'required') . '|numeric|min:0',
            'min_sinta_overall' => ($isPengabdian ? 'required' : 'nullable') . '|numeric|min:0',
            'min_tkt' => ($isPengabdian ? 'nullable' : 'required') . '|integer|min:1|max:9',
            'max_tkt' => ($isPengabdian ? 'nullable' : 'required') . '|integer|min:1|max:9|gte:min_tkt',
            'min_sdgs' => ($isPengabdian ? 'required' : 'nullable') . '|integer|min:1|max:17',
            'plafon_dana' => 'required|numeric|min:0',
        ]);

        $scheme = PpmSkemaBima::create([
            'kode_skema' => strtoupper(trim($validated['kode_skema'])),
            'nama_skema' => trim($validated['nama_skema']),
            'kategori' => $validated['kategori'],
            'min_jafung' => $validated['min_jafung'],
            'min_sinta_3yr' => $validated['min_sinta_3yr'] ?? 0,
            'min_sinta_overall' => $validated['min_sinta_overall'] ?? 0,
            'min_tkt' => $validated['min_tkt'] ?? null,
            'max_tkt' => $validated['max_tkt'] ?? null,
            'min_sdgs' => $validated['min_sdgs'] ?? 0,
            'plafon_dana' => $validated['plafon_dana'],
            'is_active' => true,
            'rubrik_penilaian' => [
                [
                    'id' => 'c1',
                    'kriteria' => 'Kualifikasi & Rekam Jejak Pengusul',
                    'bobot' => 30,
                    'deskripsi' => 'Rekam jejak publikasi SINTA/Scopus & kepakaran.',
                    'skala' => ['1_2' => 'Kurang', '3_4' => 'Cukup', '5_6' => 'Baik', '7' => 'Sangat Baik']
                ],
                [
                    'id' => 'c2',
                    'kriteria' => 'Kebaharuan & Metodologi Riset',
                    'bobot' => 40,
                    'deskripsi' => 'State of the art & rancangan penelitian.',
                    'skala' => ['1_2' => 'Kurang', '3_4' => 'Cukup', '5_6' => 'Baik', '7' => 'Sangat Baik']
                ],
                [
                    'id' => 'c3',
                    'kriteria' => 'Luaran & Kelayakan RAB SBM',
                    'bobot' => 30,
                    'deskripsi' => 'Target luaran wajib & kewajaran RAB SBM.',
                    'skala' => ['1_2' => 'Kurang', '3_4' => 'Cukup', '5_6' => 'Baik', '7' => 'Sangat Baik']
                ]
            ],
        ]);

        AuditLogService::log('BIMA_SCHEME_CREATED', null, [
            'admin_id' => Auth::id(),
            'kode_skema' => $scheme->kode_skema,
            'plafon_dana' => $scheme->plafon_dana,
        ]);

        return redirect()->route('admin.skema-bima.index')
                         ->with('success', "Skema BIMA '{$scheme->nama_skema}' berhasil dibuat!");
    }

    /**
     * Update an existing BIMA Grant Scheme configuration.
     */
    public function update(Request $request, PpmSkemaBima $skemaBima)
    {
        $isPengabdian = $request->input('kategori') === 'pengabdian';
        $validated = $request->validate([
            'nama_skema' => 'required|string|max:255',
            'kategori' => 'required|in:penelitian,pengabdian',
            'min_jafung' => 'required|array|min:1',
            'min_sinta_3yr' => ($isPengabdian ? 'nullable' : 'required') . '|numeric|min:0',
            'min_sinta_overall' => ($isPengabdian ? 'required' : 'nullable') . '|numeric|min:0',
            'min_tkt' => ($isPengabdian ? 'nullable' : 'required') . '|integer|min:1|max:9',
            'max_tkt' => ($isPengabdian ? 'nullable' : 'required') . '|integer|min:1|max:9|gte:min_tkt',
            'min_sdgs' => ($isPengabdian ? 'required' : 'nullable') . '|integer|min:1|max:17',
            'plafon_dana' => 'required|numeric|min:0',
        ]);

        $skemaBima->update([
            ...$validated,
            'min_sinta_3yr' => $validated['min_sinta_3yr'] ?? 0,
            'min_sinta_overall' => $validated['min_sinta_overall'] ?? 0,
            'min_tkt' => $validated['min_tkt'] ?? null,
            'max_tkt' => $validated['max_tkt'] ?? null,
            'min_sdgs' => $validated['min_sdgs'] ?? 0,
        ]);

        AuditLogService::log('BIMA_SCHEME_UPDATED', null, [
            'admin_id' => Auth::id(),
            'kode_skema' => $skemaBima->kode_skema,
            'plafon_dana' => $skemaBima->plafon_dana,
        ]);

        return redirect()->route('admin.skema-bima.index')
                         ->with('success', "Konfigurasi Skema BIMA '{$skemaBima->nama_skema}' berhasil diperbarui!");
    }

    /**
     * Toggle active/inactive status of a scheme sewaktu-waktu (US-04.1).
     */
    public function toggleActive(PpmSkemaBima $skemaBima)
    {
        $skemaBima->update([
            'is_active' => !$skemaBima->is_active,
        ]);

        $statusStr = $skemaBima->is_active ? 'DIFUNGSIKAN (AKTIF)' : 'DINONAKTIFKAN';

        AuditLogService::log('BIMA_SCHEME_TOGGLED', null, [
            'admin_id' => Auth::id(),
            'kode_skema' => $skemaBima->kode_skema,
            'is_active' => $skemaBima->is_active,
        ]);

        return redirect()->route('admin.skema-bima.index')
                         ->with('success', "Status Skema '{$skemaBima->nama_skema}' berhasil diubah menjadi {$statusStr}.");
    }

    /**
     * Show Reviewer Assessment Rubric Editor (JSONB - US-04.3).
     */
    public function showRubrik(PpmSkemaBima $skemaBima)
    {
        $rubrikList = $skemaBima->rubrik_penilaian ?? [];
        $totalBobot = array_sum(array_column($rubrikList, 'bobot'));

        return view('admin.skema-bima.rubrik', compact('skemaBima', 'rubrikList', 'totalBobot'));
    }

    /**
     * Update Reviewer Assessment Rubric JSONB with 100% Weight Accumulation Validation (US-04.3).
     */
    public function updateRubrik(Request $request, PpmSkemaBima $skemaBima)
    {
        $validated = $request->validate([
            'rubrik' => 'required|array|min:1',
            'rubrik.*.kriteria' => 'required|string|max:255',
            'rubrik.*.bobot' => 'required|numeric|min:1|max:100',
            'rubrik.*.deskripsi' => 'nullable|string',
            'rubrik.*.skala_1_2' => 'nullable|string',
            'rubrik.*.skala_3_4' => 'nullable|string',
            'rubrik.*.skala_5_6' => 'nullable|string',
            'rubrik.*.skala_7' => 'nullable|string',
        ]);

        // Calculate total weight sum
        $totalWeight = 0;
        $formattedRubrik = [];

        foreach ($validated['rubrik'] as $index => $item) {
            $bobot = (float) $item['bobot'];
            $totalWeight += $bobot;

            $formattedRubrik[] = [
                'id' => 'c' . ($index + 1),
                'kriteria' => trim($item['kriteria']),
                'bobot' => $bobot,
                'deskripsi' => trim($item['deskripsi'] ?? ''),
                'skala' => [
                    '1_2' => trim($item['skala_1_2'] ?? 'Sangat Kurang'),
                    '3_4' => trim($item['skala_3_4'] ?? 'Cukup'),
                    '5_6' => trim($item['skala_5_6'] ?? 'Baik'),
                    '7' => trim($item['skala_7'] ?? 'Sangat Baik / Sangat Layak'),
                ]
            ];
        }

        // Validate exact 100% weight accumulation requirement
        if (abs($totalWeight - 100.0) > 0.01) {
            return redirect()->back()
                             ->withInput()
                             ->with('error', "GAGAL MANDATORI! Total akumulasi bobot rubrik harus tepat 100%! (Total akumulasi bobot saat ini: {$totalWeight}%)");
        }

        $skemaBima->update([
            'rubrik_penilaian' => $formattedRubrik,
        ]);

        AuditLogService::log('BIMA_RUBRIK_UPDATED', null, [
            'admin_id' => Auth::id(),
            'kode_skema' => $skemaBima->kode_skema,
            'total_criteria' => count($formattedRubrik),
            'total_weight' => $totalWeight,
        ]);

        return redirect()->route('admin.skema-bima.index')
                         ->with('success', "BERHASIL! Rubrik penilaian reviewer 1-7 untuk skema '{$skemaBima->nama_skema}' telah diperbarui (Total Bobot: 100%).");
    }

    /**
     * Delete a scheme.
     */
    public function destroy(PpmSkemaBima $skemaBima)
    {
        $name = $skemaBima->nama_skema;
        $skemaBima->delete();

        return redirect()->route('admin.skema-bima.index')
                         ->with('success', "Skema '{$name}' telah dihapus dari sistem.");
    }
}


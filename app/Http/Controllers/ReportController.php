<?php

namespace App\Http\Controllers;

use App\Exports\AkreditasiMultiSheetExport;
use App\Models\RefFakultas;
use App\Models\RefProgramStudi;
use App\Services\AccreditationReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    /**
     * Preview accreditation data tables (US-12.3).
     */
    public function index(Request $request, AccreditationReportService $reportService): View
    {
        $user = Auth::user();
        abort_unless(
            $user->hasRole(['Admin P3M', 'Kepala P3M', 'Dekanat', 'Superadmin', 'Kaprodi']),
            403,
            'Akses ditolak: Khusus Pengelola Akreditasi, P3M, atau Pimpinan Unit.'
        );

        $filters = $this->resolveFilters($request, $user);
        $tables = $reportService->getAllTables($filters['fakultas_id'], $filters['prodi_id'], $filters['tahun']);

        $fakultasList = RefFakultas::with('programStudi')->get();
        $prodiList = $filters['fakultas_id']
            ? RefProgramStudi::where('id_fakultas', $filters['fakultas_id'])->get()
            : RefProgramStudi::all();

        $tab = $request->query('tab', 'penelitian');

        return view('laporan.akreditasi', compact('tables', 'fakultasList', 'prodiList', 'filters', 'tab'));
    }

    /**
     * Export accreditation tables to Excel (.xlsx) using maatwebsite/excel (US-12.3).
     */
    public function exportExcel(Request $request, AccreditationReportService $reportService): BinaryFileResponse
    {
        $user = Auth::user();
        abort_unless(
            $user->hasRole(['Admin P3M', 'Kepala P3M', 'Dekanat', 'Superadmin', 'Kaprodi']),
            403,
            'Akses ditolak: Khusus Pengelola Akreditasi.'
        );

        $filters = $this->resolveFilters($request, $user);
        $tables = $reportService->getAllTables($filters['fakultas_id'], $filters['prodi_id'], $filters['tahun']);

        $fileName = 'LKPS_BANPT_LAM_UHN_' . date('Ymd_His') . '.xlsx';

        return Excel::download(new AkreditasiMultiSheetExport($tables), $fileName);
    }

    /**
     * Export accreditation tables to print-ready PDF using dompdf (US-12.3).
     */
    public function exportPdf(Request $request, AccreditationReportService $reportService): Response
    {
        $user = Auth::user();
        abort_unless(
            $user->hasRole(['Admin P3M', 'Kepala P3M', 'Dekanat', 'Superadmin', 'Kaprodi']),
            403,
            'Akses ditolak: Khusus Pengelola Akreditasi.'
        );

        $filters = $this->resolveFilters($request, $user);
        $tables = $reportService->getAllTables($filters['fakultas_id'], $filters['prodi_id'], $filters['tahun']);

        $fakultasName = $filters['fakultas_id'] ? RefFakultas::find($filters['fakultas_id'])?->nama_fakultas : 'Seluruh Universitas';
        $prodiName = $filters['prodi_id'] ? RefProgramStudi::find($filters['prodi_id'])?->nama_prodi : 'Semua Program Studi';

        $pdf = Pdf::loadView('laporan.akreditasi-pdf', [
            'tables' => $tables,
            'fakultasName' => $fakultasName,
            'prodiName' => $prodiName,
            'tahun' => $filters['tahun'] ?: date('Y'),
            'generatedAt' => now()->translatedFormat('d F Y H:i:s'),
            'generatedBy' => $user->name,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('Instrumen_Akreditasi_UHN_' . date('Ymd_His') . '.pdf');
    }

    /**
     * Helper to resolve tenant-isolated filters.
     */
    private function resolveFilters(Request $request, $user): array
    {
        $isPrivileged = $user->hasRole(['Superadmin', 'Kepala P3M', 'Admin P3M']);

        $fakultasId = $request->input('fakultas_id') ? (int) $request->input('fakultas_id') : null;
        $prodiId = $request->input('prodi_id') ? (int) $request->input('prodi_id') : null;
        $tahun = $request->input('tahun') ? (int) $request->input('tahun') : null;

        if (!$isPrivileged) {
            if ($user->hasRole('Dekanat')) {
                $fakultasId = $user->id_fakultas;
            } elseif ($user->hasRole('Kaprodi')) {
                $prodiId = $user->id_prodi;
                $fakultasId = $user->id_fakultas;
            }
        }

        return [
            'fakultas_id' => $fakultasId,
            'prodi_id' => $prodiId,
            'tahun' => $tahun,
        ];
    }
}


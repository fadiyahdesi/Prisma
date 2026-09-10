<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Integrations\SiakadClient;
use App\Services\Integrations\DjkiClient;
use App\Services\Integrations\StorageSecurityService;
use App\Services\SintaService;
use App\Models\User;

class IntegrationsController extends Controller
{
    /**
     * Display Integrations Hub Dashboard.
     */
    public function index()
    {
        $user = auth()->user();

        $integrationsStatus = [
            'siakad' => [
                'name' => 'SIAKAD Cloud UHN',
                'protocol' => 'REST API / OAuth2 OIDC',
                'status' => config('services.siakad.enabled', true) ? 'Active' : 'Standby',
                'last_sync' => $user->email_verified_at ? $user->email_verified_at->format('d M Y H:i') : 'Saat Login',
                'resilience' => 'Snapshot lokal di tabel users & Fallback Kredensial OTP',
            ],
            'sinta' => [
                'name' => 'SINTA Kemdiktisaintek',
                'protocol' => 'REST API JSON & Live Web Parser',
                'status' => 'Active',
                'sinta_id' => $user->sinta_id ?? 'Belum terlink',
                'sinta_score_3yr' => $user->sinta_score_3yr ?? 0,
                'last_sync' => $user->last_sinta_sync_at ? $user->last_sinta_sync_at->format('d M Y H:i') : 'Belum Pernah',
                'resilience' => 'Cache Redis TTL 7 Hari & Fallback Form Screenshot',
            ],
            'djki' => [
                'name' => 'Pangkalan Data DJKI',
                'protocol' => 'REST API / Web Parser PDKI',
                'status' => 'Active',
                'resilience' => 'Verifikasi Manual Staf Sentra HKI UHN jika pemeliharaan',
            ],
            'minio' => [
                'name' => 'MinIO Object Storage',
                'protocol' => 'S3 API Protocol (AES-256)',
                'status' => config('services.minio.enabled', false) ? 'MinIO S3 Connected' : 'Local Public Storage (Simulated S3)',
                'resilience' => 'Batas 5MB (Proposal) / 15MB (Monev), MIME application/pdf, ClamAV Antivirus',
            ],
        ];

        return view('integrations.index', compact('integrationsStatus', 'user'));
    }

    /**
     * Search & Verify DJKI HKI registration number.
     */
    public function verifyDjki(Request $request, DjkiClient $djkiClient)
    {
        $validated = $request->validate([
            'nomor_permohonan' => 'required|string|max:100',
        ]);

        $result = $djkiClient->verifyHki($validated['nomor_permohonan']);

        return response()->json($result);
    }

    /**
     * Test secure storage file upload (MinIO / Public Disk).
     */
    public function uploadTestFile(Request $request)
    {
        $request->validate([
            'file_document' => 'required|file|mimes:pdf|max:15360',
            'doc_type' => 'required|in:proposal,report',
        ]);

        $file = $request->file('file_document');
        $docType = $request->input('doc_type', 'proposal');

        $result = StorageSecurityService::validateAndStore($file, 'integrations_test', $docType);

        if (!$result['success']) {
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->back()->with('success', $result['message']);
    }
}


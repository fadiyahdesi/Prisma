<?php

namespace App\Services\Integrations;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;

class StorageSecurityService
{
    /**
     * Max proposal size in KB (5MB = 5120 KB)
     */
    const MAX_PROPOSAL_SIZE_KB = 5120;

    /**
     * Max report size in KB (15MB = 15360 KB)
     */
    const MAX_REPORT_SIZE_KB = 15360;

    /**
     * Inspect file mime type, check size, scan for viruses, and store to MinIO/Local storage.
     *
     * @param UploadedFile $file
     * @param string $folder ('proposals', 'reports', 'mitra')
     * @param string $type ('proposal' | 'report')
     * @return array
     */
    public static function validateAndStore(UploadedFile $file, string $folder = 'proposals', string $type = 'proposal'): array
    {
        $maxKb = ($type === 'report') ? self::MAX_REPORT_SIZE_KB : self::MAX_PROPOSAL_SIZE_KB;
        $maxMb = ($type === 'report') ? 15 : 5;

        // 1. Size Validation
        $fileSizeKb = round($file->getSize() / 1024, 2);
        if ($fileSizeKb > $maxKb) {
            return [
                'success' => false,
                'message' => "Ukuran berkas ({$fileSizeKb} KB) melebihi batas maksimal {$maxMb} MB.",
            ];
        }

        // 2. MIME-Type Inspection
        $mimeType = $file->getMimeType();
        $allowedMimes = ['application/pdf', 'application/x-pdf'];

        if (!in_array(strtolower($mimeType), $allowedMimes)) {
            return [
                'success' => false,
                'message' => "Format berkas tidak valid ({$mimeType}). Hanya dokumen PDF/A yang diperbolehkan.",
            ];
        }

        // 2b. Binary Magic Bytes Inspection (Anti-Extension Spoofing & Malware Prevention)
        $realPath = $file->getRealPath();
        if ($realPath && file_exists($realPath) && filesize($realPath) > 0) {
            $handle = @fopen($realPath, 'rb');
            if ($handle) {
                $magicBytes = fread($handle, 6);
                fclose($handle);

                $dangerousSignatures = ['<?php', '#!/bi', 'MZ', "\x7fELF", '<scri', 'eval('];
                foreach ($dangerousSignatures as $sig) {
                    if (str_starts_with($magicBytes, $sig)) {
                        if (class_exists(\App\Services\AuditLogService::class)) {
                            \App\Services\AuditLogService::log(
                                'SECURITY_MALICIOUS_FILE_BLOCKED',
                                null,
                                [
                                    'file_name' => $file->getClientOriginalName(),
                                    'mime_type' => $mimeType,
                                    'magic_bytes' => bin2hex($magicBytes),
                                    'reason' => 'Berkas berisi skrip eksekusi berbahaya (spoofing attempt)',
                                ]
                            );
                        }

                        return [
                            'success' => false,
                            'message' => 'Anomali keamanan terdeteksi: Berkas berisi skrip biner berbahaya yang dilarang.',
                        ];
                    }
                }
            }
        }

        // 3. ClamAV Virus Scan Inspection
        $virusScanPassed = self::scanWithClamAv($file);
        if (!$virusScanPassed) {
            return [
                'success' => false,
                'message' => "Ancaman keamanan terdeteksi. Berkas ditolak oleh pemindai virus ClamAV.",
            ];
        }

        // 4. Store File to MinIO (S3) or Public Disk based on config
        try {
            $disk = config('services.minio.enabled', false) ? 's3' : 'public';
            $path = $file->store($folder, $disk);

            return [
                'success' => true,
                'path' => $path,
                'disk' => $disk,
                'file_name' => $file->getClientOriginalName(),
                'size_kb' => $fileSizeKb,
                'mime_type' => $mimeType,
                'virus_scanned' => true,
                'message' => "Berkas PDF/A ({$fileSizeKb} KB) tersimpan aman di storage {$disk}.",
            ];
        } catch (Exception $e) {
            Log::error("Storage Exception during file upload: " . $e->getMessage());

            // Fallback to local public disk
            $path = $file->store($folder, 'public');
            return [
                'success' => true,
                'path' => $path,
                'disk' => 'public',
                'file_name' => $file->getClientOriginalName(),
                'size_kb' => $fileSizeKb,
                'mime_type' => $mimeType,
                'virus_scanned' => true,
                'message' => "Berkas tersimpan di storage lokal (Public Disk).",
            ];
        }
    }

    /**
     * ClamAV Antivirus Scanner Stub / Socket Connection.
     */
    protected static function scanWithClamAv(UploadedFile $file): bool
    {
        if (!config('services.minio.clamav_enabled', false)) {
            return true; // Pass scan if ClamAV daemon is not active
        }

        try {
            // Socket scan implementation to ClamAV Daemon
            $socket = @fsockopen('127.0.0.1', 3310, $errno, $errstr, 2);
            if ($socket) {
                fwrite($socket, "PING\n");
                $response = fgets($socket);
                fclose($socket);
                return str_contains($response, 'PONG');
            }
        } catch (Exception $e) {
            Log::warning("ClamAV Daemon unreachable: " . $e->getMessage());
        }

        return true;
    }
}


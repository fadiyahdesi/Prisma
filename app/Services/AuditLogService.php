<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogService
{
    /**
     * Record an audit log entry into ppm_audit_logs.
     */
    public static function log(string $action, ?array $before = null, ?array $after = null, ?int $userId = null): AuditLog
    {
        return AuditLog::create([
            'id_user' => $userId ?? Auth::id(),
            'action' => $action,
            'ip_address' => Request::ip() ?? '127.0.0.1',
            'user_agent' => Request::userAgent(),
            'payload_sebelum' => $before,
            'payload_sesudah' => $after,
            'created_at' => now(),
        ]);
    }
}


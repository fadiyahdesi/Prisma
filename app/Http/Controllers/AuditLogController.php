<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AuditLogController extends Controller
{
    /**
     * Display paginated Audit Trail Logs. Restricted to Superadmin only.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Otorisasi: Khusus Superadmin
        if (!$user->hasRole('Superadmin')) {
            return redirect()->route('dashboard')->with('error', 'Akses Ditolak: Halaman Audit Trail Logging hanya dapat diakses oleh Superadmin.');
        }

        $query = AuditLog::with('user')->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        $logs = $query->paginate(15)->withQueryString();
        $actionTypes = AuditLog::select('action')->distinct()->pluck('action');

        return view('admin.audit-logs', compact('logs', 'actionTypes'));
    }
}


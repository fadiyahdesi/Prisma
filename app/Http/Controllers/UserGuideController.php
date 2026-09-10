<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserGuideController extends Controller
{
    /**
     * Show interactive user guide categorized by role and workflow lifecycle.
     */
    public function index(Request $request)
    {
        $currentRole = session('active_role', auth()->user()->roles()->first()->name ?? 'Dosen / Pengusul');
        $activeTab = $request->query('role', $this->mapDefaultTab($currentRole));

        return view('panduan.index', compact('activeTab', 'currentRole'));
    }

    protected function mapDefaultTab(string $role): string
    {
        return match($role) {
            'Reviewer' => 'reviewer',
            'Kaprodi' => 'kaprodi',
            'Dekanat' => 'dekanat',
            'Keuangan' => 'keuangan',
            'Admin P3M', 'Kepala P3M', 'Superadmin' => 'admin',
            'Dosen / Mahasiswa Anggota' => 'anggota',
            default => 'dosen',
        };
    }
}


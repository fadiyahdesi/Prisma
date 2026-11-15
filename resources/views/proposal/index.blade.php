@extends('layouts.app')

@section('title', 'Data Penelitian Dosen UHN')

@section('styles')
<style>
    .filter-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 1.15rem 1.35rem;
        margin-bottom: 1.25rem;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }
    .tab-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.55rem 1rem;
        border-radius: 10px;
        font-size: 0.84rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.15s ease;
        border: 1px solid transparent;
    }
    .tab-pill.active {
        background: #1B365D;
        color: #ffffff;
        box-shadow: 0 2px 6px rgba(27, 54, 93, 0.2);
    }
    .tab-pill.inactive {
        background: #f8fafc;
        color: #475569;
        border-color: #e2e8f0;
    }
    .tab-pill.inactive:hover {
        background: #f1f5f9;
        color: #0f172a;
    }
    .tab-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.12rem 0.5rem;
        border-radius: 999px;
        font-size: 0.73rem;
        font-weight: 800;
    }
    .tab-pill.active .tab-badge {
        background: rgba(255, 255, 255, 0.2);
        color: #ffffff;
    }
    .tab-pill.inactive .tab-badge {
        background: #e2e8f0;
        color: #334155;
    }
    .penelitian-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .penelitian-table thead th {
        background: #f8fafc;
        color: #334155;
        font-size: 0.74rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 0.85rem 0.9rem;
        border-bottom: 2px solid #e2e8f0;
        white-space: nowrap;
    }
    .penelitian-table tbody td {
        padding: 0.85rem 0.9rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: top;
        font-size: 0.84rem;
        color: #1e293b;
    }
    .penelitian-table tbody tr:hover {
        background-color: #f8fafc;
    }
    .semester-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.28rem 0.65rem;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 700;
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
        white-space: nowrap;
    }
    .no-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 34px;
        height: 28px;
        padding: 0 0.45rem;
        border-radius: 7px;
        background: #f1f5f9;
        color: #334155;
        font-weight: 800;
        font-size: 0.78rem;
        border: 1px solid #e2e8f0;
    }
    .nidn-code {
        font-family: 'JetBrains Mono', ui-monospace, SFMono-Regular, Consolas, monospace;
        font-size: 0.79rem;
        font-weight: 600;
        color: #0f172a;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 0.22rem 0.5rem;
        border-radius: 6px;
        display: inline-block;
        white-space: nowrap;
    }
    .prodi-badge {
        display: inline-block;
        padding: 0.25rem 0.6rem;
        border-radius: 7px;
        font-size: 0.76rem;
        font-weight: 700;
        background: #f0fdf4;
        color: #166534;
        border: 1px solid #bbf7d0;
        line-height: 1.3;
    }
    .nominal-text {
        font-family: 'JetBrains Mono', ui-monospace, SFMono-Regular, Consolas, monospace;
        font-weight: 700;
        font-size: 0.82rem;
        color: #0f172a;
        white-space: nowrap;
    }
    .action-group {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        white-space: nowrap;
    }
    .btn-act {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.3rem;
        height: 30px;
        padding: 0 0.65rem;
        border-radius: 7px;
        font-size: 0.75rem;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.15s ease;
        border: 1px solid transparent;
    }
    .btn-act-edit {
        background: #eff6ff;
        color: #1d4ed8;
        border-color: #bfdbfe;
    }
    .btn-act-edit:hover {
        background: #dbeafe;
        color: #1e40af;
    }
    .btn-act-view {
        background: #f8fafc;
        color: #334155;
        border-color: #cbd5e1;
    }
    .btn-act-view:hover {
        background: #f1f5f9;
        color: #0f172a;
    }
    .btn-act-del {
        background: #fef2f2;
        color: #dc2626;
        border-color: #fecaca;
    }
    .btn-act-del:hover {
        background: #fee2e2;
        color: #b91c1c;
    }
    /* Modal Edit */
    .modal-backdrop-custom {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.55);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    .modal-backdrop-custom.show {
        display: flex;
    }
    .modal-card-custom {
        background: #ffffff;
        border-radius: 16px;
        width: 100%;
        max-width: 680px;
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.2);
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }
</style>
@endsection

@section('content')
@php
    $isAdmin = auth()->user()->hasRole(['Superadmin', 'Admin P3M', 'Kepala P3M']);
@endphp

<div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-start; gap: 1rem; margin-bottom: 1.25rem;">
    <div>
        <div style="display: flex; align-items: center; gap: 0.65rem; flex-wrap: wrap;">
            <h2 style="font-size: 1.5rem; font-weight: 800; color: #0f172a; margin: 0;">Data Penelitian Dosen UHN</h2>
            <span style="background: #dcfce7; color: #166534; border: 1px solid #86efac; font-size: 0.76rem; font-weight: 800; padding: 0.22rem 0.65rem; border-radius: 999px;">
                {{ $totalAllCount }} Data Penelitian Resmi
            </span>
        </div>
        <p style="color: #64748b; margin: 0.25rem 0 0 0; font-size: 0.88rem;">
            Basis data penelitian dosen Universitas Harkat Negeri sesuai kolom standar: <strong>Semester, No, Judul, Ketua, NIDN / NUPN, Anggota, Prodi, Nominal</strong>.
        </p>
    </div>

    <div style="display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap;">
        <form action="{{ route('usulan.sync-penelitian') }}" method="POST" style="margin: 0;" onsubmit="return confirm('Sinkronkan ulang 438 Data Penelitian Dosen dari CSV resmi dan bersihkan data dummy?');">
            @csrf
            <button type="submit" class="btn" style="background: #0f766e; color: #ffffff; border: none; padding: 0.58rem 1rem; border-radius: 10px; font-size: 0.82rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.45rem; cursor: pointer;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 2v6h-6"></path><path d="M3 12a9 9 0 0 1 15-6.7L21 8"></path><path d="M3 22v-6h6"></path><path d="M21 12a9 9 0 0 1-15 6.7L3 16"></path></svg>
                Sinkronkan 438 Penelitian CSV
            </button>
        </form>

        <a href="{{ route('sinta.profile') }}" class="btn btn-primary" style="padding: 0.58rem 1rem; border-radius: 10px; font-size: 0.82rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.4rem; text-decoration: none;">
            + Ajukan Proposal Baru
        </a>
    </div>
</div>

@if(session('success'))
    <div style="background: #f0fdf4; border: 1px solid #86efac; color: #166534; padding: 0.85rem 1.1rem; border-radius: 12px; margin-bottom: 1.1rem; font-size: 0.86rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 0.85rem 1.1rem; border-radius: 12px; margin-bottom: 1.1rem; font-size: 0.86rem; font-weight: 600;">
        {{ session('error') }}
    </div>
@endif

<!-- Scope Tabs & Summary -->
<div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
        <a href="{{ route('usulan.index', array_merge(request()->except('page'), ['scope' => 'all'])) }}"
           class="tab-pill {{ ($scope ?? 'all') === 'all' ? 'active' : 'inactive' }}">
            <span>Semua Penelitian Dosen UHN</span>
            <span class="tab-badge">{{ $totalAllCount }}</span>
        </a>
        <a href="{{ route('usulan.index', array_merge(request()->except('page'), ['scope' => 'mine'])) }}"
           class="tab-pill {{ ($scope ?? 'all') === 'mine' ? 'active' : 'inactive' }}">
            <span>Penelitian Saya (Ketua / Anggota)</span>
            <span class="tab-badge">{{ $totalMineCount }}</span>
        </a>
    </div>

    <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.45rem 0.85rem; font-size: 0.8rem; color: #334155;">
            Menampilkan: <strong style="color: #0f172a;">{{ $proposals->count() }}</strong> dari <strong style="color: #0f172a;">{{ $proposals->total() }}</strong> penelitian
        </div>
        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 0.45rem 0.85rem; font-size: 0.8rem; color: #166534;">
            Total Nominal: <strong>Rp. {{ number_format($totalNominal, 0, '.', ',') }}</strong>
        </div>
    </div>
</div>

<!-- Filter Bar -->
<div class="filter-card">
    <form method="GET" action="{{ route('usulan.index') }}" style="display: grid; grid-template-columns: 2fr 1.2fr 1.4fr 0.9fr auto; gap: 0.75rem; align-items: end;">
        <input type="hidden" name="scope" value="{{ $scope ?? 'all' }}">

        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;">Cari Judul / Ketua / NIDN / Anggota</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik judul, nama ketua, NIDN, atau anggota..."
                   style="width: 100%; height: 38px; padding: 0 0.75rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.83rem;">
        </div>

        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;">Semester</label>
            <select name="semester" style="width: 100%; height: 38px; padding: 0 0.65rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.83rem; background: #fff;">
                <option value="">Semua Semester ({{ $semesterList->count() }})</option>
                @foreach($semesterList as $sem)
                    <option value="{{ $sem }}" {{ request('semester') === $sem ? 'selected' : '' }}>{{ $sem }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;">Program Studi (Prodi)</label>
            <select name="prodi" style="width: 100%; height: 38px; padding: 0 0.65rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.83rem; background: #fff;">
                <option value="">Semua Prodi ({{ $prodiList->count() }})</option>
                @foreach($prodiList as $pr)
                    <option value="{{ $pr }}" {{ request('prodi') === $pr ? 'selected' : '' }}>{{ $pr }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;">Baris / Halaman</label>
            <select name="per_page" onchange="this.form.submit()" style="width: 100%; height: 38px; padding: 0 0.65rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.83rem; background: #fff;">
                <option value="500" {{ ($perPage ?? 500) == 500 ? 'selected' : '' }}>Semua (438)</option>
                <option value="250" {{ ($perPage ?? 500) == 250 ? 'selected' : '' }}>250 Baris</option>
                <option value="100" {{ ($perPage ?? 500) == 100 ? 'selected' : '' }}>100 Baris</option>
                <option value="50" {{ ($perPage ?? 500) == 50 ? 'selected' : '' }}>50 Baris</option>
                <option value="25" {{ ($perPage ?? 500) == 25 ? 'selected' : '' }}>25 Baris</option>
            </select>
        </div>

        <div style="display: flex; gap: 0.45rem;">
            <button type="submit" style="height: 38px; padding: 0 1rem; background: #1B365D; color: #fff; border: none; border-radius: 8px; font-size: 0.82rem; font-weight: 700; cursor: pointer;">
                Filter
            </button>
            @if(request()->hasAny(['search', 'semester', 'prodi']))
                <a href="{{ route('usulan.index', ['scope' => $scope ?? 'all', 'per_page' => $perPage ?? 500]) }}"
                   style="height: 38px; padding: 0 0.85rem; background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.82rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center;">
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Main Table: 8 Columns (Semester | No | Judul | Ketua | NIDN / NUPN | Anggota | Prodi | Nominal) -->
<div class="card" style="padding: 0; overflow: hidden; border: 1px solid #e2e8f0; border-radius: 14px; background: #ffffff;">
    @if($proposals->isEmpty())
        <div style="text-align: center; padding: 3.5rem 2rem;">
            <h3 style="font-size: 1.05rem; font-weight: 700; color: #334155; margin-bottom: 0.4rem;">Tidak Ada Data Penelitian Ditemukan</h3>
            <p style="color: #64748b; max-width: 460px; margin: 0 auto 1.25rem; font-size: 0.86rem;">
                Tidak ada data penelitian dosen yang cocok dengan filter pencarian saat ini.
            </p>
            <a href="{{ route('usulan.index', ['scope' => 'all']) }}" class="btn btn-primary" style="text-decoration: none; font-size: 0.82rem;">
                Tampilkan Semua 438 Penelitian Dosen
            </a>
        </div>
    @else
        <div style="overflow-x: auto;">
            <table class="penelitian-table">
                <thead>
                    <tr>
                        <th style="width: 130px;">Semester</th>
                        <th style="width: 55px; text-align: center;">No</th>
                        <th style="min-width: 280px;">Judul</th>
                        <th style="min-width: 190px;">Ketua</th>
                        <th style="width: 130px;">NIDN / NUPN</th>
                        <th style="min-width: 200px;">Anggota</th>
                        <th style="min-width: 170px;">Prodi</th>
                        <th style="width: 130px; text-align: right;">Nominal</th>
                        <th style="width: 155px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($proposals as $idx => $p)
                        @php
                            $semesterVal = $p->semester ?: ($p->periode ? trim($p->periode->tahun_akademik . ' ' . $p->periode->semester) : '-');
                            $noUrutVal = $p->no_urut ?? ($proposals->firstItem() + $idx);
                            $ketuaVal = $p->nama_ketua ?: ($p->pengusul?->name ?? '-');
                            $nidnVal = $p->nidn_ketua ?: ($p->pengusul?->nidn_nim ?? '-');
                            $anggotaVal = $p->nama_anggota;
                            if (empty($anggotaVal) || $anggotaVal === '-') {
                                $anggotaNames = $p->anggota?->pluck('nama')->filter()->values()->all() ?? [];
                                $anggotaVal = !empty($anggotaNames) ? implode('; ', $anggotaNames) : '-';
                            }
                            $prodiVal = $p->nama_prodi ?: ($p->prodi?->nama_prodi ?? ($p->pengusul?->prodi?->nama_prodi ?? '-'));
                            $nominalVal = (float) ($p->total_rab ?? 0);
                            $canManage = $isAdmin || $p->id_pengusul === auth()->id();
                        @endphp
                        <tr>
                            <td>
                                <span class="semester-badge">{{ $semesterVal }}</span>
                            </td>
                            <td style="text-align: center;">
                                <span class="no-badge">{{ $noUrutVal }}</span>
                            </td>
                            <td>
                                <div style="font-weight: 700; color: #0f172a; line-height: 1.45;">
                                    {{ $p->judul_usulan ?: '(Judul Belum Diisi)' }}
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 700; color: #1e293b; line-height: 1.35;">
                                    {{ $ketuaVal }}
                                </div>
                            </td>
                            <td>
                                <span class="nidn-code">{{ $nidnVal }}</span>
                            </td>
                            <td>
                                <div style="color: #334155; line-height: 1.4; font-size: 0.81rem;">
                                    {{ $anggotaVal }}
                                </div>
                            </td>
                            <td>
                                <span class="prodi-badge">{{ $prodiVal }}</span>
                            </td>
                            <td style="text-align: right;">
                                <span class="nominal-text">Rp. {{ number_format($nominalVal, 0, '.', ',') }}</span>
                            </td>
                            <td style="text-align: center;">
                                <div class="action-group">
                                    @if($canManage)
                                        <button type="button"
                                                class="btn-act btn-act-edit"
                                                onclick="openEditPenelitianModal({{ json_encode([
                                                    'id' => $p->id,
                                                    'update_url' => route('usulan.update-basic', $p->id),
                                                    'semester' => $semesterVal,
                                                    'no_urut' => $noUrutVal,
                                                    'judul_usulan' => $p->judul_usulan,
                                                    'nama_ketua' => $ketuaVal,
                                                    'nidn_ketua' => $nidnVal,
                                                    'nama_anggota' => $anggotaVal,
                                                    'nama_prodi' => $prodiVal,
                                                    'total_rab' => $nominalVal,
                                                ]) }})"
                                                title="Edit Data Penelitian">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                            Edit
                                        </button>
                                    @endif

                                    <a href="{{ route('usulan.step', ['usulan' => $p->id, 'step' => ($p->status === 'Draft' ? 1 : 6)]) }}"
                                       class="btn-act btn-act-view"
                                       title="Lihat Detail">
                                        Detail
                                    </a>

                                    @if($canManage)
                                        <form action="{{ route('usulan.destroy', $p->id) }}" method="POST" style="margin: 0; display: inline;"
                                              onsubmit="return confirm('Hapus data penelitian No. {{ $noUrutVal }} ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-act btn-act-del" title="Hapus Data Penelitian">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path><path d="M10 11v6"></path><path d="M14 11v6"></path></svg>
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($proposals->hasPages())
            <div style="padding: 1rem 1.25rem; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
                <div style="font-size: 0.8rem; color: #64748b;">
                    Menampilkan baris <strong>{{ $proposals->firstItem() }}</strong> – <strong>{{ $proposals->lastItem() }}</strong> dari total <strong>{{ $proposals->total() }}</strong> penelitian
                </div>
                <div>
                    {{ $proposals->links() }}
                </div>
            </div>
        @endif
    @endif
</div>

<!-- Modal Edit 8 Kolom Dasar Penelitian -->
<div id="editPenelitianModal" class="modal-backdrop-custom">
    <div class="modal-card-custom">
        <div style="padding: 1.1rem 1.4rem; background: #1B365D; color: #ffffff; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1rem; font-weight: 800;">Edit Data Penelitian Dosen</h3>
            <button type="button" onclick="closeEditPenelitianModal()" style="background: transparent; border: none; color: #ffffff; font-size: 1.25rem; cursor: pointer; line-height: 1;">&times;</button>
        </div>

        <form id="editPenelitianForm" method="POST" action="" style="padding: 1.35rem 1.4rem;">
            @csrf
            @method('PUT')

            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 0.85rem; margin-bottom: 0.85rem;">
                <div>
                    <label style="display: block; font-size: 0.76rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">No</label>
                    <input type="number" name="no_urut" id="edit_no_urut" required
                           style="width: 100%; height: 38px; padding: 0 0.75rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.84rem;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.76rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Semester</label>
                    <input type="text" name="semester" id="edit_semester" required placeholder="Contoh: 2024 2025 Genap"
                           style="width: 100%; height: 38px; padding: 0 0.75rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.84rem;">
                </div>
            </div>

            <div style="margin-bottom: 0.85rem;">
                <label style="display: block; font-size: 0.76rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Judul Penelitian</label>
                <textarea name="judul_usulan" id="edit_judul_usulan" rows="3" required
                          style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.84rem; font-family: inherit;"></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 0.85rem; margin-bottom: 0.85rem;">
                <div>
                    <label style="display: block; font-size: 0.76rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Ketua</label>
                    <input type="text" name="nama_ketua" id="edit_nama_ketua" required
                           style="width: 100%; height: 38px; padding: 0 0.75rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.84rem;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.76rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">NIDN / NUPN</label>
                    <input type="text" name="nidn_ketua" id="edit_nidn_ketua"
                           style="width: 100%; height: 38px; padding: 0 0.75rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.84rem;">
                </div>
            </div>

            <div style="margin-bottom: 0.85rem;">
                <label style="display: block; font-size: 0.76rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Anggota</label>
                <input type="text" name="nama_anggota" id="edit_nama_anggota"
                       style="width: 100%; height: 38px; padding: 0 0.75rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.84rem;">
            </div>

            <div style="display: grid; grid-template-columns: 1.4fr 1fr; gap: 0.85rem; margin-bottom: 1.25rem;">
                <div>
                    <label style="display: block; font-size: 0.76rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Prodi</label>
                    <input type="text" name="nama_prodi" id="edit_nama_prodi" required
                           style="width: 100%; height: 38px; padding: 0 0.75rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.84rem;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.76rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Nominal (Rp)</label>
                    <input type="number" step="1" min="0" name="total_rab" id="edit_total_rab" required
                           style="width: 100%; height: 38px; padding: 0 0.75rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.84rem;">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.6rem; border-top: 1px solid #e2e8f0; padding-top: 1rem;">
                <button type="button" onclick="closeEditPenelitianModal()"
                        style="height: 38px; padding: 0 1rem; background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.82rem; font-weight: 700; cursor: pointer;">
                    Batal
                </button>
                <button type="submit"
                        style="height: 38px; padding: 0 1.2rem; background: #1B365D; color: #ffffff; border: none; border-radius: 8px; font-size: 0.82rem; font-weight: 700; cursor: pointer;">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditPenelitianModal(data) {
        const modal = document.getElementById('editPenelitianModal');
        const form = document.getElementById('editPenelitianForm');
        form.action = data.update_url;
        document.getElementById('edit_no_urut').value = data.no_urut || '';
        document.getElementById('edit_semester').value = data.semester || '';
        document.getElementById('edit_judul_usulan').value = data.judul_usulan || '';
        document.getElementById('edit_nama_ketua').value = data.nama_ketua || '';
        document.getElementById('edit_nidn_ketua').value = data.nidn_ketua || '-';
        document.getElementById('edit_nama_anggota').value = data.nama_anggota || '-';
        document.getElementById('edit_nama_prodi').value = data.nama_prodi || '';
        document.getElementById('edit_total_rab').value = Math.round(data.total_rab || 0);
        modal.classList.add('show');
    }

    function closeEditPenelitianModal() {
        document.getElementById('editPenelitianModal').classList.remove('show');
    }
</script>
@endsection

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class PpmUsulan extends Model
{
    use HasFactory;

    protected $table = 'ppm_usulan';

    protected $fillable = [
        'no_urut',
        'semester',
        'kategori_usulan',
        'id_pengusul',
        'id_skema_bima',
        'id_periode_hibah',
        'kode_usulan',
        'judul_usulan',
        'nama_ketua',
        'nidn_ketua',
        'nama_anggota',
        'nama_prodi',
        'id_prodi',
        'rumpun_ilmu_level_1',
        'rumpun_ilmu_level_2',
        'rumpun_ilmu_level_3',
        'fokus_rirn',
        'target_tkt',
        'jawaban_instrumen_tkt',
        'nama_mitra',
        'mitra_lat',
        'mitra_long',
        'mitra_jarak_km',
        'mitra_surat_kesediaan_path',
        'ringkasan_substansi',
        'file_proposal_path',
        'total_rab',
        'total_honorarium',
        'status',
        'verification_notes',
        'template_status',
        'similarity_score',
        'similarity_status',
        'verified_by',
        'verified_at',
        'kaprodi_alignment_status',
        'kaprodi_recommendation',
        'kaprodi_id',
        'kaprodi_reviewed_at',
        'submitted_at',
        'skor_reviewer_1',
        'skor_reviewer_2',
        'skor_reviewer_3',
        'skor_akhir',
        'is_disparity',
        'adjudication_notes',
    ];

    protected $casts = [
        'target_tkt' => 'integer',
        'jawaban_instrumen_tkt' => 'array',
        'mitra_lat' => 'float',
        'mitra_long' => 'float',
        'mitra_jarak_km' => 'float',
        'total_rab' => 'float',
        'total_honorarium' => 'float',
        'submitted_at' => 'datetime',
        'verified_at' => 'datetime',
        'kaprodi_reviewed_at' => 'datetime',
        'skor_reviewer_1' => 'float',
        'skor_reviewer_2' => 'float',
        'skor_reviewer_3' => 'float',
        'skor_akhir' => 'float',
        'is_disparity' => 'boolean',
    ];

    public function pengusul(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_pengusul')->withDefault(function (User $user, PpmUsulan $usulan) {
            $user->name = $usulan->nama_ketua ?? 'Dosen Pengusul';
            $user->nidn_nim = $usulan->nidn_ketua ?? '-';
            $user->id_prodi = $usulan->id_prodi;
            $user->email = '-';
        });
    }

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(RefProgramStudi::class, 'id_prodi', 'id_prodi');
    }

    public function skema(): BelongsTo
    {
        return $this->belongsTo(PpmSkemaBima::class, 'id_skema_bima');
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(PpmPeriodeHibah::class, 'id_periode_hibah');
    }

    public function anggota(): HasMany
    {
        return $this->hasMany(PpmUsulanAnggota::class, 'id_usulan');
    }

    public function rab(): HasMany
    {
        return $this->hasMany(PpmUsulanRab::class, 'id_usulan');
    }

    public function luaran(): HasMany
    {
        return $this->hasMany(PpmUsulanLuaran::class, 'id_usulan');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function kaprodi(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kaprodi_id');
    }

    public function penugasanReviewer(): HasMany
    {
        return $this->hasMany(PpmPenugasanReviewer::class, 'id_usulan');
    }

    public function kontrak(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PpmKontrak::class, 'id_usulan');
    }

    public function logbook(): HasMany
    {
        return $this->hasMany(PpmLogbook::class, 'id_usulan')->orderBy('tanggal', 'asc');
    }

    public function monevKemajuan(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PpmMonevKemajuan::class, 'id_usulan');
    }

    public function seminarHasil(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PpmSeminarHasil::class, 'id_usulan');
    }

    public function laporanAkhir(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PpmLaporanAkhir::class, 'id_usulan');
    }

    /**
     * Scope query to records created by or involving the given user (as Ketua or Anggota).
     */
    public function scopeOwnedByUser(Builder $query, User $user): Builder
    {
        $likeOp = DB::getDriverName() === 'pgsql' ? 'ilike' : 'like';
        $baseName = $user->cleanedBaseName();
        $isLecturerLike = !$user->hasRole(['Superadmin', 'Admin P3M']) && $user->nidn_nim !== 'ADM-P3M-01';
        $nidnDigits = preg_replace('/\D+/', '', (string) $user->nidn_nim);

        return $query->where(function (Builder $q) use ($user, $likeOp, $baseName, $isLecturerLike, $nidnDigits) {
            $q->where('id_pengusul', $user->id)
              ->orWhereHas('anggota', fn (Builder $aq) => $aq->where('user_id', $user->id));

            if ($isLecturerLike && strlen($nidnDigits) >= 5) {
                $q->orWhere('nidn_ketua', $user->nidn_nim);
            }

            if ($isLecturerLike && strlen($baseName) >= 5) {
                $q->orWhere('nama_ketua', $likeOp, '%' . $baseName . '%')
                  ->orWhere('nama_anggota', $likeOp, '%' . $baseName . '%');
            }
        });
    }

    /**
     * Check if a specific user owns or participates in this record.
     */
    public function isOwnedBy(User $user): bool
    {
        if ((int) $this->id_pengusul === (int) $user->id) {
            return true;
        }

        if ($this->anggota()->where('user_id', $user->id)->exists()) {
            return true;
        }

        $isLecturerLike = !$user->hasRole(['Superadmin', 'Admin P3M']) && $user->nidn_nim !== 'ADM-P3M-01';
        if ($isLecturerLike) {
            $nidnDigits = preg_replace('/\D+/', '', (string) $user->nidn_nim);
            if (strlen($nidnDigits) >= 5 && trim((string) $this->nidn_ketua) === trim((string) $user->nidn_nim)) {
                return true;
            }

            $baseName = $user->cleanedBaseName();
            if (strlen($baseName) >= 5) {
                $haystack = strtolower(($this->nama_ketua ?? '') . ' ' . ($this->nama_anggota ?? ''));
                if (str_contains($haystack, strtolower($baseName))) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if all team members have consented/approved.
     */
    public function isAllMembersApproved(): bool
    {
        return !$this->anggota()->where('status_persetujuan', '!=', 'approved')->exists();
    }
}


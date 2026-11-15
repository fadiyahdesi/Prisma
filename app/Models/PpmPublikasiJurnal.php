<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class PpmPublikasiJurnal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ppm_publikasi_jurnal';

    protected $fillable = [
        'no_urut',
        'semester',
        'jenis_publikasi',
        'user_id',
        'judul_artikel',
        'nama_jurnal',
        'issn',
        'doi',
        'kategori_peringkat',
        'tahun_terbit',
        'volume_nomor',
        'penulis',
        'url_artikel',
        'file_naskah',
        'jumlah_penulis',
        'peran_penulis',
        'metadata_source',
        'is_claimed_reward',
        'template_status',
        'similarity_score',
        'status_verifikasi',
        'catatan_verifikasi',
        'verified_by',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'tahun_terbit' => 'integer',
            'jumlah_penulis' => 'integer',
            'is_claimed_reward' => 'boolean',
            'similarity_score' => 'float',
            'verified_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function klaimReward(): HasOne
    {
        return $this->hasOne(PpmKlaimReward::class, 'id_publikasi');
    }

    /**
     * Scope query to publications authored or co-authored by the given user.
     */
    public function scopeOwnedByUser(Builder $query, User $user): Builder
    {
        $likeOp = DB::getDriverName() === 'pgsql' ? 'ilike' : 'like';
        $baseName = $user->cleanedBaseName();
        $isLecturerLike = !$user->hasRole(['Superadmin', 'Admin P3M']) && $user->nidn_nim !== 'ADM-P3M-01';

        return $query->where(function (Builder $q) use ($user, $likeOp, $baseName, $isLecturerLike) {
            $q->where('user_id', $user->id);

            if (!empty($user->nipy)) {
                $q->orWhere('penulis', $likeOp, '%' . $user->nipy . '%');
            }

            if ($isLecturerLike && strlen($baseName) >= 5) {
                $q->orWhere('penulis', $likeOp, '%' . $baseName . '%');
            }
        });
    }

    /**
     * Check whether the given user is an owner or co-author of this publication.
     */
    public function isOwnedBy(User $user): bool
    {
        if ((int) $this->user_id === (int) $user->id) {
            return true;
        }

        $penulis = (string) ($this->penulis ?? '');
        if (!empty($user->nipy) && str_contains(strtolower($penulis), strtolower($user->nipy))) {
            return true;
        }

        $isLecturerLike = !$user->hasRole(['Superadmin', 'Admin P3M']) && $user->nidn_nim !== 'ADM-P3M-01';
        if ($isLecturerLike) {
            $baseName = $user->cleanedBaseName();
            if (strlen($baseName) >= 5 && str_contains(strtolower($penulis), strtolower($baseName))) {
                return true;
            }
        }

        return false;
    }
}


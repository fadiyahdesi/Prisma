<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class PpmPublikasiJurnal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ppm_publikasi_jurnal';

    protected $fillable = [
        'user_id',
        'judul_artikel',
        'nama_jurnal',
        'issn',
        'doi',
        'kategori_peringkat',
        'tahun_terbit',
        'volume_nomor',
        'url_artikel',
        'file_naskah',
        'jumlah_penulis',
        'metadata_source',
        'is_claimed_reward',
    ];

    protected function casts(): array
    {
        return [
            'tahun_terbit' => 'integer',
            'jumlah_penulis' => 'integer',
            'is_claimed_reward' => 'boolean',
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
}


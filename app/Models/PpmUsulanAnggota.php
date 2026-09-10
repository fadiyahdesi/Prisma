<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PpmUsulanAnggota extends Model
{
    use HasFactory;

    protected $table = 'ppm_usulan_anggota';

    protected $fillable = [
        'id_usulan',
        'user_id',
        'jenis_anggota',
        'nama',
        'identifier',
        'peran_anggota',
        'status_persetujuan',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function usulan(): BelongsTo
    {
        return $this->belongsTo(PpmUsulan::class, 'id_usulan');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PpmPenugasanReviewer extends Model
{
    use HasFactory;

    protected $table = 'ppm_penugasan_reviewer';

    protected $fillable = [
        'id_usulan',
        'id_reviewer',
        'peran_reviewer',
        'status_penugasan',
        'assigned_by',
        'assigned_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function usulan()
    {
        return $this->belongsTo(PpmUsulan::class, 'id_usulan');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'id_reviewer');
    }

    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function penilaian()
    {
        return $this->hasOne(PpmPenilaianReviewer::class, 'id_penugasan');
    }
}


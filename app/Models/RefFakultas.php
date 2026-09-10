<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefFakultas extends Model
{
    protected $table = 'ref_fakultas';
    protected $primaryKey = 'id_fakultas';

    protected $fillable = [
        'kode_fakultas',
        'nama_fakultas',
        'dekan_nama',
        'dekan_nip',
        'is_active',
    ];

    public function programStudi()
    {
        return $this->hasMany(RefProgramStudi::class, 'id_fakultas', 'id_fakultas');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'id_fakultas', 'id_fakultas');
    }
}


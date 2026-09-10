<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefProgramStudi extends Model
{
    protected $table = 'ref_program_studi';
    protected $primaryKey = 'id_prodi';

    protected $fillable = [
        'id_fakultas',
        'kode_prodi',
        'nama_prodi',
        'jenjang',
        'kaprodi_nama',
        'kaprodi_nip',
        'is_active',
    ];

    public function fakultas()
    {
        return $this->belongsTo(RefFakultas::class, 'id_fakultas', 'id_fakultas');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'id_prodi', 'id_prodi');
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PpmKontrak extends Model
{
    use HasFactory;

    protected $table = 'ppm_kontrak';

    protected $fillable = [
        'id_usulan',
        'nomor_sk',
        'nomor_kontrak',
        'tanggal_sk',
        'tanggal_kontrak',
        'pagu_disetujui',
        'dana_termin_1',
        'dana_termin_2',
        'nama_bank',
        'nomor_rekening',
        'nama_pemilik_rekening',
        'file_buku_tabungan',
        'rekening_verified_by',
        'rekening_verified_at',
        'signed_by_kepala',
        'signed_by_kepala_at',
        'signed_by_pengusul',
        'signed_by_pengusul_at',
        'verification_token',
        'document_hash',
        'file_spk_path',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_sk' => 'date',
            'tanggal_kontrak' => 'date',
            'pagu_disetujui' => 'decimal:2',
            'dana_termin_1' => 'decimal:2',
            'dana_termin_2' => 'decimal:2',
            'signed_by_kepala' => 'boolean',
            'signed_by_kepala_at' => 'datetime',
            'signed_by_pengusul' => 'boolean',
            'signed_by_pengusul_at' => 'datetime',
            'rekening_verified_at' => 'datetime',
        ];
    }

    public function usulan(): BelongsTo
    {
        return $this->belongsTo(PpmUsulan::class, 'id_usulan');
    }

    public function pencairan(): HasMany
    {
        return $this->hasMany(PpmPencairanDana::class, 'id_kontrak');
    }

    public function rekeningVerifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rekening_verified_by');
    }

    public function isFullySigned(): bool
    {
        return (bool) ($this->signed_by_kepala && $this->signed_by_pengusul);
    }

    public function hasVerifiedRekening(): bool
    {
        return !empty($this->nomor_rekening) && !is_null($this->rekening_verified_at);
    }

    public function isTermin1Disbursed(): bool
    {
        return $this->pencairan()
            ->where('termin', 1)
            ->where('status_pencairan', 'transferred')
            ->exists();
    }
}


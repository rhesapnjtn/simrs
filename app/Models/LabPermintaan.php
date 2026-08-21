<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LabPermintaan extends Model
{
    use HasFactory;

    protected $table = 'lab_permintaan';

    protected $fillable = [
        'no_lab',
        'pendaftaran_id',
        'dokter_id',
        'tanggal_permintaan',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_permintaan' => 'date',
    ];

    /**
     * Relasi ke Pendaftaran
     */
    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(
            Pendaftaran::class,
            'pendaftaran_id'
        );
    }

    /**
     * Relasi ke Dokter
     */
    public function dokter(): BelongsTo
    {
        return $this->belongsTo(
            Dokter::class,
            'dokter_id'
        );
    }

    /**
     * Relasi ke Detail Pemeriksaan
     */
    public function details(): HasMany
    {
        return $this->hasMany(
            LabPermintaanDetail::class,
            'lab_permintaan_id'
        );
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LabPermintaanDetail extends Model
{
    use HasFactory;

    protected $table = 'lab_permintaan_details';

    protected $fillable = [
        'lab_permintaan_id',
        'lab_pemeriksaan_id',
    ];

    /**
     * Relasi ke Lab Permintaan
     */
    public function labPermintaan(): BelongsTo
    {
        return $this->belongsTo(
            LabPermintaan::class,
            'lab_permintaan_id'
        );
    }

    /**
     * Relasi ke Master Pemeriksaan Lab
     */
    public function labPemeriksaan(): BelongsTo
    {
        return $this->belongsTo(
            LabPemeriksaan::class,
            'lab_pemeriksaan_id'
        );
    }

    /**
     * Relasi ke Hasil Lab
     */
    public function hasil(): HasOne
    {
        return $this->hasOne(
            LabHasil::class,
            'lab_permintaan_detail_id'
        );
    }
}
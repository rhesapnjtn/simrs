<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pemeriksaan extends Model
{
    protected $fillable = [
        'pendaftaran_id',
        'tekanan_darah',
        'suhu',
        'berat_badan',
        'tinggi_badan',
        'nadi',
        'respirasi',
        'diagnosis',
        'tindakan',
        'catatan',
    ];

    protected $casts = [
        'suhu' => 'decimal:1',
        'berat_badan' => 'decimal:2',
        'tinggi_badan' => 'decimal:2',
    ];

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(
            Pendaftaran::class,
            'pendaftaran_id'
        );
    }
}
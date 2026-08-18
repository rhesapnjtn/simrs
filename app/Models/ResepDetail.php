<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResepDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'resep_id',
        'obat_id',
        'jumlah',
        'dosis',
        'aturan_pakai',
        'catatan',
    ];

    protected $casts = [
        'jumlah' => 'integer',
    ];

    public function resep(): BelongsTo
    {
        return $this->belongsTo(
            Resep::class,
            'resep_id'
        );
    }

    public function obat(): BelongsTo
    {
        return $this->belongsTo(
            Obat::class,
            'obat_id'
        );
    }
}
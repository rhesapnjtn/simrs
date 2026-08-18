<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resep extends Model
{
    use HasFactory;

    protected $fillable = [
        'pendaftaran_id',
        'dokter_id',
        'tanggal_resep',
        'no_resep',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_resep' => 'date',
    ];

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(
            Pendaftaran::class,
            'pendaftaran_id'
        );
    }

    public function dokter(): BelongsTo
    {
        return $this->belongsTo(
            Dokter::class,
            'dokter_id'
        );
    }

    public function details(): HasMany
    {
        return $this->hasMany(
            ResepDetail::class,
            'resep_id'
        );
    }
}
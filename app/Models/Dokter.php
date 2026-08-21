<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Dokter extends Model
{
    protected $fillable = [
        'user_id',
        'poli_id',
        'nomor_str',
        'nama',
        'spesialisasi',
        'no_telepon',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Poli
     */
    public function poli(): BelongsTo
    {
        return $this->belongsTo(Poli::class);
    }
    public function pendaftarans()
{
    return $this->hasMany(Pendaftaran::class);
}
public function labPermintaans(): HasMany
{
    return $this->hasMany(
        LabPermintaan::class,
        'dokter_id'
    );
}
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pendaftaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'pasien_id',
        'poli_id',
        'dokter_id',
        'tanggal_kunjungan',
        'no_antrian',
        'status',
        'keluhan',
    ];

    protected $casts = [
        'tanggal_kunjungan' => 'date',
    ];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

    public function poli()
    {
        return $this->belongsTo(Poli::class);
    }

    public function dokter()
    {
        return $this->belongsTo(Dokter::class);
    }
    

    public function pemeriksaan(): HasOne
    {
        return $this->hasOne(
            Pemeriksaan::class,
            'pendaftaran_id'
        );
    }
    public function labPermintaans(): HasMany
{
    return $this->hasMany(
        LabPermintaan::class,
        'pendaftaran_id'
    );
}
    
}
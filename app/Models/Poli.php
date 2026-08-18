<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poli extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'nama',
        'prefix',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
    public function dokters()
{
    return $this->hasMany(Dokter::class);
}
public function pendaftarans()
{
    return $this->hasMany(Pendaftaran::class);
}
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LabPemeriksaan extends Model
{
    protected $table = 'lab_pemeriksaans';

    protected $fillable = [
        'kode',
        'nama',
        'kategori',
        'satuan',
        'nilai_rujukan',
        'harga',
        'is_active',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function details(): HasMany
    {
        return $this->hasMany(
            LabPermintaanDetail::class,
            'lab_pemeriksaan_id'
        );
    }
}
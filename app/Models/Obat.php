<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Obat extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_obat',
        'nama_obat',
        'jenis',
        'satuan',
        'stok',
        'harga',
        'is_active',
    ];

    protected $casts = [
        'stok' => 'integer',
        'harga' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function resepDetails(): HasMany
    {
        return $this->hasMany(
            ResepDetail::class,
            'obat_id'
        );
    }
}
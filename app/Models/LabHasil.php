<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabHasil extends Model
{
    use HasFactory;

    protected $table = 'lab_hasil';

    protected $fillable = [
        'lab_permintaan_detail_id',
        'hasil',
        'flag',
        'catatan',
        'tanggal_pemeriksaan',
        'tanggal_verifikasi',
        'verified_by',
    ];

    protected $casts = [
        'tanggal_pemeriksaan' => 'datetime',
        'tanggal_verifikasi' => 'datetime',
    ];

    /**
     * Relasi ke detail permintaan lab
     */
    public function detail(): BelongsTo
    {
        return $this->belongsTo(
            LabPermintaanDetail::class,
            'lab_permintaan_detail_id'
        );
    }

    /**
     * User yang melakukan verifikasi
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'verified_by'
        );
    }
}
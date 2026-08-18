<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use App\Models\Pemeriksaan;
use Illuminate\Http\Request;

class PemeriksaanController extends Controller
{
    /**
     * Ambil data pemeriksaan berdasarkan pendaftaran.
     */
    public function show($pendaftaranId)
    {
        $pendaftaran = Pendaftaran::with([
            'pasien',
            'poli',
            'dokter',
            'pemeriksaan',
        ])->find($pendaftaranId);

        if (!$pendaftaran) {
            return response()->json([
                'message' => 'Data pendaftaran tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'pendaftaran' => $pendaftaran,
            'pemeriksaan' => $pendaftaran->pemeriksaan,
        ]);
    }


    /**
     * Simpan hasil pemeriksaan pasien.
     */
    public function store(Request $request, $pendaftaranId)
    {
        $pendaftaran = Pendaftaran::find($pendaftaranId);

        if (!$pendaftaran) {
            return response()->json([
                'message' => 'Data pendaftaran tidak ditemukan.'
            ], 404);
        }

        $validated = $request->validate([

            'tekanan_darah' => [
                'nullable',
                'string',
                'max:20',
            ],

            'suhu' => [
                'nullable',
                'numeric',
                'between:25,45',
            ],

            'berat_badan' => [
                'nullable',
                'numeric',
                'between:1,500',
            ],

            'tinggi_badan' => [
                'nullable',
                'numeric',
                'between:30,250',
            ],

            'nadi' => [
                'nullable',
                'integer',
                'between:20,250',
            ],

            'respirasi' => [
                'nullable',
                'integer',
                'between:5,100',
            ],

            'diagnosis' => [
                'nullable',
                'string',
            ],

            'tindakan' => [
                'nullable',
                'string',
            ],

            'catatan' => [
                'nullable',
                'string',
            ],

        ]);

        $pemeriksaan = Pemeriksaan::updateOrCreate(
            [
                'pendaftaran_id' => $pendaftaran->id,
            ],
            $validated
        );

        $pendaftaran->update([
            'status' => 'DIPERIKSA',
        ]);

        return response()->json([
            'message' => 'Pemeriksaan berhasil disimpan.',
            'pemeriksaan' => $pemeriksaan,
            'pendaftaran' => $pendaftaran,
        ]);
    }


    /**
     * Riwayat pemeriksaan satu pasien.
     */
    public function riwayatPasien($pasienId)
    {
        $pemeriksaans = Pemeriksaan::with([
            'pendaftaran.pasien',
            'pendaftaran.poli',
            'pendaftaran.dokter',
        ])
            ->whereHas('pendaftaran', function ($query) use ($pasienId) {
                $query->where('pasien_id', $pasienId);
            })
            ->latest()
            ->get();

        return response()->json([
            'pemeriksaans' => $pemeriksaans,
        ]);
    }


    /**
     * Semua pasien yang pernah diperiksa
     * oleh dokter yang sedang login.
     */
    public function pasienDokter()
{
    $user = auth()->user();

    if (!$user) {
        return response()->json([
            'message' => 'User tidak terautentikasi.'
        ], 401);
    }

    /*
    |--------------------------------------------------------------------------
    | SUPER ADMIN
    |--------------------------------------------------------------------------
    |
    | Super Admin dapat melihat seluruh riwayat pemeriksaan.
    |
    */

    if ($user->hasRole('SUPER_ADMIN')) {

        $pemeriksaans = Pemeriksaan::with([
            'pendaftaran.pasien',
            'pendaftaran.poli',
            'pendaftaran.dokter',
        ])
            ->latest()
            ->get();

        return response()->json([
            'pemeriksaans' => $pemeriksaans,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | DOKTER
    |--------------------------------------------------------------------------
    |
    | Dokter hanya dapat melihat pemeriksaan
    | yang dilakukan oleh dirinya sendiri.
    |
    */

    if ($user->hasRole('DOKTER')) {

        if (!$user->dokter) {
            return response()->json([
                'message' => 'User tidak memiliki data dokter.'
            ], 403);
        }

        $dokterId = $user->dokter->id;

        $pemeriksaans = Pemeriksaan::with([
            'pendaftaran.pasien',
            'pendaftaran.poli',
            'pendaftaran.dokter',
        ])
            ->whereHas('pendaftaran', function ($query) use ($dokterId) {
                $query->where('dokter_id', $dokterId);
            })
            ->latest()
            ->get();

        return response()->json([
            'pemeriksaans' => $pemeriksaans,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | ROLE LAIN
    |--------------------------------------------------------------------------
    */

    return response()->json([
        'message' => 'Anda tidak memiliki akses ke riwayat pemeriksaan.'
    ], 403);
}
}
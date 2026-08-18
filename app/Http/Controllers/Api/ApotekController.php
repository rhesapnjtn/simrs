<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Resep;
use Illuminate\Http\Request;

class ApotekController extends Controller
{
    /**
     * Menampilkan daftar resep untuk apotek.
     */
    public function index(Request $request)
    {
        $query = Resep::with([
            'pendaftaran.pasien',
            'dokter',
            'details.obat',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Filter Status
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where(
                    'no_resep',
                    'like',
                    "%{$search}%"
                )

                ->orWhereHas(
                    'pendaftaran.pasien',
                    function ($pasien) use ($search) {

                        $pasien->where(
                            'nama',
                            'like',
                            "%{$search}%"
                        );
                    }
                );

            });
        }

        $reseps = $query
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data resep apotek berhasil diambil',
            'data' => $reseps,
        ]);
    }


    /**
     * Menampilkan detail resep untuk apotek.
     */
    public function show($id)
    {
        $resep = Resep::with([
            'pendaftaran.pasien',
            'dokter',
            'details.obat',
        ])->find($id);

        if (!$resep) {

            return response()->json([
                'success' => false,
                'message' => 'Resep tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail resep berhasil diambil',
            'data' => $resep,
        ]);
    }


    /**
 * Proses resep
 */
public function proses($id)
{
    $resep = Resep::find($id);

    if (!$resep) {
        return response()->json([
            'success' => false,
            'message' => 'Resep tidak ditemukan',
        ], 404);
    }

    $resep->update([
        'status' => 'DIPROSES',
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Resep berhasil diproses.',
        'data' => $resep,
    ]);
}


/**
 * Menyelesaikan resep
 */
public function selesai($id)
{
    $resep = Resep::find($id);

    if (!$resep) {
        return response()->json([
            'success' => false,
            'message' => 'Resep tidak ditemukan',
        ], 404);
    }

    $resep->update([
        'status' => 'SELESAI',
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Resep berhasil diselesaikan.',
        'data' => $resep,
    ]);
}
}
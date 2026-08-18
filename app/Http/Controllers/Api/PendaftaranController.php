<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dokter;
use App\Models\Pendaftaran;
use App\Models\Pasien;
use App\Models\Poli;
use Illuminate\Http\Request;

class PendaftaranController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    |
    | Mengambil data pendaftaran.
    |
    | Default dari frontend adalah tanggal hari ini,
    | tetapi endpoint ini tetap bisa menerima tanggal lain
    | untuk melihat data/riwayat berdasarkan tanggal.
    |
    */

    public function index(Request $request)
{
    $query = Pendaftaran::with([
        'pasien',
        'poli',
        'dokter',
        'pemeriksaan',
    ])->latest();


    /*
    |--------------------------------------------------------------------------
    | Filter tanggal
    |--------------------------------------------------------------------------
    */

    if ($request->filled('tanggal')) {

        $query->whereDate(
            'tanggal_kunjungan',
            $request->tanggal
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Filter poli
    |--------------------------------------------------------------------------
    */

    if ($request->filled('poli_id')) {

        $query->where(
            'poli_id',
            $request->poli_id
        );
    }


    /*
    |--------------------------------------------------------------------------
    | FILTER DOKTER YANG LOGIN
    |--------------------------------------------------------------------------
    |
    | Jika user adalah DOKTER:
    | hanya tampilkan pasien yang terdaftar
    | kepada dokter tersebut.
    |
    */

    $user = auth()->user();

    if ($user->hasRole('DOKTER')) {

        /*
        | Ambil data dokter berdasarkan
        | user_id yang sedang login.
        */

        $dokter = Dokter::where(
            'user_id',
            $user->id
        )->first();


        /*
        | Jika akun dokter belum terhubung
        | dengan data dokter
        */

        if (!$dokter) {

            return response()->json([
                'message' =>
                    'Akun dokter belum terhubung dengan data dokter.',
                'pendaftarans' => [],
            ]);
        }


        /*
        | Hanya ambil pasien milik dokter login
        */

        $query->where(
            'dokter_id',
            $dokter->id
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Ambil data
    |--------------------------------------------------------------------------
    */

    $pendaftarans = $query->get();


    /*
    |--------------------------------------------------------------------------
    | Response
    |--------------------------------------------------------------------------
    */

    return response()->json([
        'pendaftarans' => $pendaftarans,
    ]);
}


    /*
    |--------------------------------------------------------------------------
    | Form Data
    |--------------------------------------------------------------------------
    */

    public function form()
    {
        return response()->json([

            /*
            |--------------------------------------------------------------------------
            | Pasien aktif
            |--------------------------------------------------------------------------
            */

            'pasiens' => Pasien::where(
                'is_active',
                true
            )
                ->orderBy('nama')
                ->get(),

            /*
            |--------------------------------------------------------------------------
            | Poli aktif
            |--------------------------------------------------------------------------
            */

            'polis' => Poli::where(
                'is_active',
                true
            )
                ->orderBy('nama')
                ->get(),

            /*
            |--------------------------------------------------------------------------
            | Dokter aktif
            |--------------------------------------------------------------------------
            */

            'dokters' => Dokter::with('poli')
                ->where('is_active', true)
                ->orderBy('nama')
                ->get(),

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Store Pendaftaran
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Validasi
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([

            'pasien_id' =>
                'required|exists:pasiens,id',

            'poli_id' =>
                'required|exists:polis,id',

            'dokter_id' =>
                'required|exists:dokters,id',

            'tanggal_kunjungan' =>
                'required|date',

            'keluhan' =>
                'nullable|string',

        ]);


        /*
        |--------------------------------------------------------------------------
        | Ambil dokter
        |--------------------------------------------------------------------------
        */

        $dokter = Dokter::findOrFail(
            $validated['dokter_id']
        );


        /*
        |--------------------------------------------------------------------------
        | Pastikan dokter sesuai poli
        |--------------------------------------------------------------------------
        */

        if (
            $dokter->poli_id !=
            $validated['poli_id']
        ) {

            return response()->json([
                'message' =>
                    'Dokter tidak terdaftar pada poli yang dipilih.',
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Ambil poli
        |--------------------------------------------------------------------------
        */

        $poli = Poli::findOrFail(
            $validated['poli_id']
        );


        /*
        |--------------------------------------------------------------------------
        | Cari nomor antrean terakhir
        |--------------------------------------------------------------------------
        |
        | Nomor antrean dihitung berdasarkan:
        |
        | - Poli
        | - Tanggal kunjungan
        |
        */

        $lastNumber = Pendaftaran::where(
            'poli_id',
            $validated['poli_id']
        )
            ->whereDate(
                'tanggal_kunjungan',
                $validated['tanggal_kunjungan']
            )
            ->orderByDesc('id')
            ->value('no_antrian');


        /*
        |--------------------------------------------------------------------------
        | Tentukan nomor berikutnya
        |--------------------------------------------------------------------------
        */

        if ($lastNumber) {

            /*
            | Contoh:
            |
            | PG-001 → 001
            | PG-002 → 002
            | PD-005 → 005
            |
            */

            $number = (int) preg_replace(
                '/\D/',
                '',
                $lastNumber
            ) + 1;

        } else {

            $number = 1;
        }


        /*
        |--------------------------------------------------------------------------
        | Buat nomor antrean
        |--------------------------------------------------------------------------
        |
        | Contoh:
        |
        | PG-001
        | PG-002
        | PD-001
        | U-001
        |
        */

        $validated['no_antrian'] =
            $poli->prefix . '-' .
            str_pad(
                $number,
                3,
                '0',
                STR_PAD_LEFT
            );


        /*
        |--------------------------------------------------------------------------
        | Status awal
        |--------------------------------------------------------------------------
        */

        $validated['status'] = 'MENUNGGU';


        /*
        |--------------------------------------------------------------------------
        | Simpan pendaftaran
        |--------------------------------------------------------------------------
        */

        $pendaftaran = Pendaftaran::create(
            $validated
        );


        /*
        |--------------------------------------------------------------------------
        | Load relationship
        |--------------------------------------------------------------------------
        */

        $pendaftaran->load([
            'pasien',
            'poli',
            'dokter',
            'pemeriksaan',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'message' =>
                'Pendaftaran berhasil dibuat.',

            'pendaftaran' =>
                $pendaftaran,
        ], 201);
    }


    /*
    |--------------------------------------------------------------------------
    | Show
    |--------------------------------------------------------------------------
    */

    public function show(
        Pendaftaran $pendaftaran
    ) {

        /*
        |--------------------------------------------------------------------------
        | Load relationship
        |--------------------------------------------------------------------------
        */

        $pendaftaran->load([
            'pasien',
            'poli',
            'dokter',
            'pemeriksaan',
        ]);


        return response()->json([
            'pendaftaran' =>
                $pendaftaran,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Update Status
    |--------------------------------------------------------------------------
    */

    public function updateStatus(
        Request $request,
        Pendaftaran $pendaftaran
    ) {

        /*
        |--------------------------------------------------------------------------
        | Validasi status
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([

            'status' =>
                'required|in:MENUNGGU,DIPANGGIL,DIPERIKSA,SELESAI,BATAL',

        ]);


        /*
        |--------------------------------------------------------------------------
        | Update status
        |--------------------------------------------------------------------------
        */

        $pendaftaran->update([
            'status' =>
                $validated['status'],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Load relationship terbaru
        |--------------------------------------------------------------------------
        */

        $pendaftaran->load([
            'pasien',
            'poli',
            'dokter',
            'pemeriksaan',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'message' =>
                'Status pendaftaran berhasil diperbarui.',

            'pendaftaran' =>
                $pendaftaran,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\Resep;
use App\Models\ResepDetail;
use App\Models\Dokter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ResepController extends Controller
{
    /**
     * ============================================================
     * INDEX
     * ============================================================
     *
     * Menampilkan daftar resep.
     *
     * Dokter:
     *   - Hanya melihat resep miliknya sendiri.
     *
     * Super Admin / Apoteker:
     *   - Bisa melihat semua resep.
     */
    public function index(Request $request)
    {
        $query = Resep::with([
            'pendaftaran.pasien',
            'pendaftaran.poli',
            'dokter',
            'details.obat',
        ])
            ->latest();


        /*
        |--------------------------------------------------------------------------
        | USER LOGIN
        |--------------------------------------------------------------------------
        */

        $user = $request->user();


        /*
        |--------------------------------------------------------------------------
        | FILTER BERDASARKAN ROLE
        |--------------------------------------------------------------------------
        |
        | Dokter hanya boleh melihat resep miliknya.
        |
        | Super Admin dan Apoteker boleh melihat semua resep.
        |
        */

        if ($user) {

            /*
            |--------------------------------------------------------------------------
            | CEK APAKAH USER ADALAH DOKTER
            |--------------------------------------------------------------------------
            */

            $dokter = Dokter::where(
                'user_id',
                $user->id
            )->first();


            /*
            |--------------------------------------------------------------------------
            | JIKA USER ADALAH DOKTER
            |--------------------------------------------------------------------------
            */

            if (
                $dokter &&
                $user->hasRole('Dokter')
            ) {

                $query->where(
                    'dokter_id',
                    $dokter->id
                );
            }


            /*
            |--------------------------------------------------------------------------
            | JIKA SUPER ADMIN
            |--------------------------------------------------------------------------
            |
            | Tidak diberi filter dokter.
            |
            | Artinya:
            |
            | Super Admin → melihat semua resep.
            |
            */


            /*
            |--------------------------------------------------------------------------
            | JIKA APOTEKER
            |--------------------------------------------------------------------------
            |
            | Tidak diberi filter dokter.
            |
            | Artinya:
            |
            | Apoteker → melihat semua resep.
            |
            */

        }


        /*
        |--------------------------------------------------------------------------
        | FILTER TANGGAL
        |--------------------------------------------------------------------------
        */

        if ($request->filled('tanggal')) {

            $query->whereDate(
                'tanggal_resep',
                $request->tanggal
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTER DOKTER
        |--------------------------------------------------------------------------
        |
        | Bisa digunakan oleh Super Admin / Apoteker
        | jika frontend mengirim dokter_id.
        |
        */

        if ($request->filled('dokter_id')) {

            $query->where(
                'dokter_id',
                $request->dokter_id
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTER STATUS
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            $query->where(
                'status',
                $request->status
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTER POLI
        |--------------------------------------------------------------------------
        |
        | Karena poli berada di tabel pendaftaran.
        |
        */

        if ($request->filled('poli_id')) {

            $query->whereHas(
                'pendaftaran',
                function ($q) use ($request) {

                    $q->where(
                        'poli_id',
                        $request->poli_id
                    );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | AMBIL DATA
        |--------------------------------------------------------------------------
        */

        $reseps = $query->get();


        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,
            'message' => 'Data resep berhasil diambil',
            'data' => $reseps,
        ]);
    }


    /**
     * ============================================================
     * SHOW
     * ============================================================
     *
     * Menampilkan satu resep beserta detail obat.
     */
    public function show($id)
    {
        $resep = Resep::with([
            'pendaftaran.pasien',
            'pendaftaran.poli',
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
     * ============================================================
     * STORE
     * ============================================================
     *
     * Membuat resep baru beserta detail obat.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([

            'pendaftaran_id' => [
                'required',
                'exists:pendaftarans,id',
            ],

            'dokter_id' => [
                'required',
                'exists:dokters,id',
            ],

            'tanggal_resep' => [
                'nullable',
                'date',
            ],

            'catatan' => [
                'nullable',
                'string',
            ],

            'details' => [
                'required',
                'array',
                'min:1',
            ],

            'details.*.obat_id' => [
                'required',
                'exists:obats,id',
            ],

            'details.*.jumlah' => [
                'required',
                'integer',
                'min:1',
            ],

            'details.*.dosis' => [
                'required',
                'string',
                'max:255',
            ],

            'details.*.aturan_pakai' => [
                'nullable',
                'string',
                'max:255',
            ],

            'details.*.catatan' => [
                'nullable',
                'string',
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | TRANSACTION
        |--------------------------------------------------------------------------
        */

        DB::beginTransaction();


        try {

            /*
            |--------------------------------------------------------------------------
            | GENERATE NOMOR RESEP
            |--------------------------------------------------------------------------
            |
            | Contoh:
            | RSP-20260813-0001
            |
            */

            $tanggal = now()->format('Ymd');


            $lastResep = Resep::whereDate(
                'created_at',
                now()->toDateString()
            )
                ->latest('id')
                ->first();


            if (
                $lastResep &&
                $lastResep->no_resep
            ) {

                $lastNumber = (int) substr(
                    $lastResep->no_resep,
                    -4
                );


                $number = $lastNumber + 1;

            } else {

                $number = 1;
            }


            $noResep =
                'RSP-' .
                $tanggal .
                '-' .
                str_pad(
                    $number,
                    4,
                    '0',
                    STR_PAD_LEFT
                );


            /*
            |--------------------------------------------------------------------------
            | VALIDASI PENDAFTARAN
            |--------------------------------------------------------------------------
            */

            $pendaftaran = \App\Models\Pendaftaran::findOrFail(
                $validated['pendaftaran_id']
            );


            /*
            |--------------------------------------------------------------------------
            | PASTIKAN DOKTER SESUAI DENGAN PENDAFTARAN
            |--------------------------------------------------------------------------
            */

            if (
                (int) $pendaftaran->dokter_id !==
                (int) $validated['dokter_id']
            ) {

                return response()->json([
                    'success' => false,
                    'message' =>
                        'Dokter resep tidak sesuai dengan dokter pada pendaftaran.',
                ], 422);
            }


            /*
            |--------------------------------------------------------------------------
            | BUAT RESEP
            |--------------------------------------------------------------------------
            */

            $resep = Resep::create([

                'pendaftaran_id' =>
                    $validated['pendaftaran_id'],

                'dokter_id' =>
                    $validated['dokter_id'],

                'tanggal_resep' =>
                    $validated['tanggal_resep']
                    ?? now()->toDateString(),

                'no_resep' =>
                    $noResep,

                'status' =>
                    'MENUNGGU',

                'catatan' =>
                    $validated['catatan'] ?? null,

            ]);


            /*
            |--------------------------------------------------------------------------
            | SIMPAN DETAIL OBAT
            |--------------------------------------------------------------------------
            */

            foreach (
                $validated['details']
                as $detail
            ) {

                /*
                |--------------------------------------------------------------------------
                | LOCK OBAT
                |--------------------------------------------------------------------------
                */

                $obat = Obat::lockForUpdate()
                    ->findOrFail(
                        $detail['obat_id']
                    );


                /*
                |--------------------------------------------------------------------------
                | CEK STOK
                |--------------------------------------------------------------------------
                |
                | Stok belum dikurangi.
                |
                | Stok akan dikurangi ketika:
                |
                | MENUNGGU → DIPROSES
                |
                */

                if (
                    $obat->stok <
                    $detail['jumlah']
                ) {

                    throw ValidationException::withMessages([

                        'details' => [

                            "Stok {$obat->nama_obat} tidak mencukupi. " .
                            "Stok tersedia: {$obat->stok}.",

                        ],

                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | SIMPAN DETAIL
                |--------------------------------------------------------------------------
                */

                ResepDetail::create([

                    'resep_id' =>
                        $resep->id,

                    'obat_id' =>
                        $detail['obat_id'],

                    'jumlah' =>
                        $detail['jumlah'],

                    'dosis' =>
                        $detail['dosis'],

                    'aturan_pakai' =>
                        $detail['aturan_pakai']
                        ?? null,

                    'catatan' =>
                        $detail['catatan']
                        ?? null,

                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | COMMIT
            |--------------------------------------------------------------------------
            */

            DB::commit();


            /*
            |--------------------------------------------------------------------------
            | LOAD RELATIONSHIP
            |--------------------------------------------------------------------------
            */

            $resep->load([
                'pendaftaran.pasien',
                'pendaftaran.poli',
                'dokter',
                'details.obat',
            ]);


            /*
            |--------------------------------------------------------------------------
            | RESPONSE
            |--------------------------------------------------------------------------
            */

            return response()->json([

                'success' => true,

                'message' =>
                    'Resep berhasil dibuat',

                'data' =>
                    $resep,

            ], 201);


        } catch (ValidationException $e) {

            DB::rollBack();

            throw $e;


        } catch (\Throwable $e) {

            DB::rollBack();


            return response()->json([

                'success' => false,

                'message' =>
                    'Gagal membuat resep',

                'error' =>
                    $e->getMessage(),

            ], 500);
        }
    }


    /**
     * ============================================================
     * UPDATE STATUS
     * ============================================================
     *
     * Update status resep.
     *
     * MENUNGGU → DIPROSES
     *
     * Pada saat DIPROSES:
     * stok obat dikurangi.
     */
    public function updateStatus(
        Request $request,
        $id
    ) {

        $validated = $request->validate([

            'status' => [

                'required',

                'in:MENUNGGU,DIPROSES,SELESAI,BATAL',

            ],

        ]);


        DB::beginTransaction();


        try {

            $resep = Resep::with(
                'details.obat'
            )
                ->lockForUpdate()
                ->find($id);


            if (!$resep) {

                DB::rollBack();


                return response()->json([

                    'success' => false,

                    'message' =>
                        'Resep tidak ditemukan',

                ], 404);
            }


            $statusBaru =
                $validated['status'];


            /*
            |--------------------------------------------------------------------------
            | MENUNGGU → DIPROSES
            |--------------------------------------------------------------------------
            |
            | Kurangi stok hanya sekali.
            |
            */

            if (

                $resep->status ===
                'MENUNGGU'

                &&

                $statusBaru ===
                'DIPROSES'

            ) {

                foreach (
                    $resep->details
                    as $detail
                ) {

                    $obat = Obat::lockForUpdate()
                        ->find(
                            $detail->obat_id
                        );


                    if (!$obat) {

                        throw ValidationException::withMessages([

                            'obat' => [

                                'Obat tidak ditemukan.',

                            ],

                        ]);
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | CEK STOK
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $obat->stok <
                        $detail->jumlah
                    ) {

                        throw ValidationException::withMessages([

                            'stok' => [

                                "Stok {$obat->nama_obat} tidak mencukupi. " .
                                "Stok tersedia: {$obat->stok}, " .
                                "dibutuhkan: {$detail->jumlah}.",

                            ],

                        ]);
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | KURANGI STOK
                    |--------------------------------------------------------------------------
                    */

                    $obat->decrement(
                        'stok',
                        $detail->jumlah
                    );
                }
            }


            /*
            |--------------------------------------------------------------------------
            | UPDATE STATUS
            |--------------------------------------------------------------------------
            */

            $resep->update([

                'status' =>
                    $statusBaru,

            ]);


            DB::commit();


            /*
            |--------------------------------------------------------------------------
            | LOAD DATA
            |--------------------------------------------------------------------------
            */

            $resep->load([

                'pendaftaran.pasien',
                'pendaftaran.poli',
                'dokter',
                'details.obat',

            ]);


            return response()->json([

                'success' => true,

                'message' =>
                    'Status resep berhasil diperbarui',

                'data' =>
                    $resep,

            ]);


        } catch (ValidationException $e) {

            DB::rollBack();

            throw $e;


        } catch (\Throwable $e) {

            DB::rollBack();


            return response()->json([

                'success' => false,

                'message' =>
                    'Gagal memperbarui status resep',

                'error' =>
                    $e->getMessage(),

            ], 500);
        }
    }


    /**
     * ============================================================
     * DESTROY
     * ============================================================
     *
     * Menghapus resep.
     */
    public function destroy($id)
    {
        $resep = Resep::find($id);


        if (!$resep) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Resep tidak ditemukan',

            ], 404);
        }


        $resep->delete();


        return response()->json([

            'success' => true,

            'message' =>
                'Resep berhasil dihapus',

        ]);
    }
}
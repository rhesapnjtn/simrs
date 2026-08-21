<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LabHasil;
use App\Models\LabPermintaan;
use App\Models\LabPermintaanDetail;
use App\Models\LabPemeriksaan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Throwable;

class LabPermintaanController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    | Menampilkan daftar permintaan laboratorium.
    |
    */

    public function index(Request $request): JsonResponse
    {
        try {
            $query = LabPermintaan::with([
                'pendaftaran.pasien',
                'pendaftaran.poli',
                'dokter',
                'details.labPemeriksaan',
                'details.hasil',
            ]);

            /*
            |--------------------------------------------------------------------------
            | FILTER STATUS
            |--------------------------------------------------------------------------
            */

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            /*
            |--------------------------------------------------------------------------
            | FILTER PENDAFTARAN
            |--------------------------------------------------------------------------
            */

            if ($request->filled('pendaftaran_id')) {
                $query->where(
                    'pendaftaran_id',
                    $request->pendaftaran_id
                );
            }

            /*
            |--------------------------------------------------------------------------
            | FILTER DOKTER
            |--------------------------------------------------------------------------
            */

            if ($request->filled('dokter_id')) {
                $query->where(
                    'dokter_id',
                    $request->dokter_id
                );
            }

            /*
            |--------------------------------------------------------------------------
            | SEARCH NO LAB
            |--------------------------------------------------------------------------
            */

            if ($request->filled('search')) {
                $search = $request->search;

                $query->where(
                    'no_lab',
                    'like',
                    "%{$search}%"
                );
            }

            $labPermintaans = $query
                ->latest('id')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Data permintaan laboratorium berhasil diambil.',
                'data' => $labPermintaans,
            ]);

        } catch (Throwable $e) {

            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data permintaan laboratorium.',
            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    | Dokter membuat permintaan laboratorium.
    |
    | Request:
    |
    | {
    |     "pendaftaran_id": 1,
    |     "dokter_id": 1,
    |     "tanggal_permintaan": "2026-08-19",
    |     "catatan": "Pemeriksaan rutin",
    |     "pemeriksaan_ids": [1, 2, 3]
    | }
    |
    */

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pendaftaran_id' => [
                'required',
                'integer',
                'exists:pendaftarans,id',
            ],

            'dokter_id' => [
                'required',
                'integer',
                'exists:dokters,id',
            ],

            'tanggal_permintaan' => [
                'required',
                'date',
            ],

            'catatan' => [
                'nullable',
                'string',
            ],

            'pemeriksaan_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'pemeriksaan_ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:lab_pemeriksaans,id',
            ],
        ]);

        try {

            $labPermintaan = DB::transaction(function () use ($validated) {

                /*
                |--------------------------------------------------------------------------
                | Generate No. Lab
                |--------------------------------------------------------------------------
                */

                $noLab = $this->generateNoLab();

                /*
                |--------------------------------------------------------------------------
                | Buat Permintaan
                |--------------------------------------------------------------------------
                */

                $labPermintaan = LabPermintaan::create([
                    'no_lab' => $noLab,
                    'pendaftaran_id' => $validated['pendaftaran_id'],
                    'dokter_id' => $validated['dokter_id'],
                    'tanggal_permintaan' => $validated['tanggal_permintaan'],
                    'status' => 'MENUNGGU',
                    'catatan' => $validated['catatan'] ?? null,
                ]);

                /*
                |--------------------------------------------------------------------------
                | Buat Detail Pemeriksaan
                |--------------------------------------------------------------------------
                */

                foreach ($validated['pemeriksaan_ids'] as $pemeriksaanId) {

                    LabPermintaanDetail::create([
                        'lab_permintaan_id' => $labPermintaan->id,
                        'lab_pemeriksaan_id' => $pemeriksaanId,
                    ]);
                }

                return $labPermintaan;
            });

            /*
            |--------------------------------------------------------------------------
            | Load Relasi
            |--------------------------------------------------------------------------
            */

            $labPermintaan->load([
                'pendaftaran.pasien',
                'pendaftaran.poli',
                'dokter',
                'details.labPemeriksaan',
                'details.hasil',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permintaan laboratorium berhasil dibuat.',
                'data' => $labPermintaan,
            ], 201);

        } catch (Throwable $e) {

            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat permintaan laboratorium.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : null,
            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    | Menampilkan detail satu permintaan laboratorium.
    |
    */

    public function show(
        LabPermintaan $labPermintaan
    ): JsonResponse {

        try {

            $labPermintaan->load([
                'pendaftaran.pasien',
                'pendaftaran.poli',
                'pendaftaran.dokter',
                'dokter',
                'details.labPemeriksaan',
                'details.hasil.verifiedBy',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Detail permintaan laboratorium berhasil diambil.',
                'data' => $labPermintaan,
            ]);

        } catch (Throwable $e) {

            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail permintaan laboratorium.',
            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    | Mengubah data permintaan laboratorium.
    |
    | Status tidak diubah melalui method ini.
    | Status menggunakan endpoint updateStatus().
    |
    */

    public function update(
        Request $request,
        LabPermintaan $labPermintaan
    ): JsonResponse {

        /*
        |--------------------------------------------------------------------------
        | Jangan edit jika sudah selesai / diverifikasi
        |--------------------------------------------------------------------------
        */

        if (in_array(
            $labPermintaan->status,
            ['SELESAI', 'DIVERIFIKASI']
        )) {
            return response()->json([
                'success' => false,
                'message' => 'Permintaan laboratorium yang sudah selesai atau diverifikasi tidak dapat diubah.',
            ], 422);
        }

        $validated = $request->validate([
            'pendaftaran_id' => [
                'required',
                'integer',
                'exists:pendaftarans,id',
            ],

            'dokter_id' => [
                'required',
                'integer',
                'exists:dokters,id',
            ],

            'tanggal_permintaan' => [
                'required',
                'date',
            ],

            'catatan' => [
                'nullable',
                'string',
            ],

            'pemeriksaan_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'pemeriksaan_ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:lab_pemeriksaans,id',
            ],
        ]);

        try {

            DB::transaction(function () use (
                $validated,
                $labPermintaan
            ) {

                /*
                |--------------------------------------------------------------------------
                | Update Header
                |--------------------------------------------------------------------------
                */

                $labPermintaan->update([
                    'pendaftaran_id' => $validated['pendaftaran_id'],
                    'dokter_id' => $validated['dokter_id'],
                    'tanggal_permintaan' => $validated['tanggal_permintaan'],
                    'catatan' => $validated['catatan'] ?? null,
                ]);

                /*
                |--------------------------------------------------------------------------
                | Hapus Detail Lama
                |--------------------------------------------------------------------------
                */

                $labPermintaan->details()->delete();

                /*
                |--------------------------------------------------------------------------
                | Buat Detail Baru
                |--------------------------------------------------------------------------
                */

                foreach ($validated['pemeriksaan_ids'] as $pemeriksaanId) {

                    LabPermintaanDetail::create([
                        'lab_permintaan_id' => $labPermintaan->id,
                        'lab_pemeriksaan_id' => $pemeriksaanId,
                    ]);
                }
            });

            $labPermintaan->load([
                'pendaftaran.pasien',
                'pendaftaran.poli',
                'dokter',
                'details.labPemeriksaan',
                'details.hasil',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permintaan laboratorium berhasil diperbarui.',
                'data' => $labPermintaan,
            ]);

        } catch (Throwable $e) {

            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui permintaan laboratorium.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : null,
            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE STATUS
    |--------------------------------------------------------------------------
    |
    | Status yang tersedia:
    |
    | MENUNGGU
    | SAMPEL_DIAMBIL
    | DIPROSES
    | SELESAI
    | DIVERIFIKASI
    | BATAL
    |
    */

    public function updateStatus(
        Request $request,
        LabPermintaan $labPermintaan
    ): JsonResponse {

        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in([
                    'MENUNGGU',
                    'SAMPEL_DIAMBIL',
                    'DIPROSES',
                    'SELESAI',
                    'DIVERIFIKASI',
                    'BATAL',
                ]),
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Status tidak boleh diubah setelah diverifikasi
        |--------------------------------------------------------------------------
        */

        if ($labPermintaan->status === 'DIVERIFIKASI') {

            return response()->json([
                'success' => false,
                'message' => 'Permintaan laboratorium yang sudah diverifikasi tidak dapat diubah statusnya.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Validasi Alur Status
        |--------------------------------------------------------------------------
        */

        $allowedTransitions = [

            'MENUNGGU' => [
                'SAMPEL_DIAMBIL',
                'BATAL',
            ],

            'SAMPEL_DIAMBIL' => [
                'DIPROSES',
                'BATAL',
            ],

            'DIPROSES' => [
                'SELESAI',
                'BATAL',
            ],

            'SELESAI' => [
                'DIVERIFIKASI',
            ],

            'BATAL' => [],

            'DIVERIFIKASI' => [],
        ];

        $currentStatus = $labPermintaan->status;
        $newStatus = $validated['status'];

        /*
        |--------------------------------------------------------------------------
        | Jika status sama
        |--------------------------------------------------------------------------
        */

        if ($currentStatus === $newStatus) {

            return response()->json([
                'success' => true,
                'message' => 'Status laboratorium tidak berubah.',
                'data' => $labPermintaan,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Cek transisi
        |--------------------------------------------------------------------------
        */

        if (
            !isset($allowedTransitions[$currentStatus]) ||
            !in_array(
                $newStatus,
                $allowedTransitions[$currentStatus]
            )
        ) {

            return response()->json([
                'success' => false,
                'message' => "Status tidak dapat diubah dari {$currentStatus} menjadi {$newStatus}.",
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */

        $labPermintaan->update([
            'status' => $newStatus,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status permintaan laboratorium berhasil diperbarui.',
            'data' => $labPermintaan->fresh(),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    */

    public function destroy(
        LabPermintaan $labPermintaan
    ): JsonResponse {

        /*
        |--------------------------------------------------------------------------
        | Jangan hapus yang sudah diproses
        |--------------------------------------------------------------------------
        */

        if (in_array(
            $labPermintaan->status,
            [
                'SAMPEL_DIAMBIL',
                'DIPROSES',
                'SELESAI',
                'DIVERIFIKASI',
            ]
        )) {

            return response()->json([
                'success' => false,
                'message' => 'Permintaan laboratorium yang sudah diproses tidak dapat dihapus.',
            ], 422);
        }

        try {

            $labPermintaan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Permintaan laboratorium berhasil dihapus.',
            ]);

        } catch (Throwable $e) {

            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus permintaan laboratorium.',
            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | STORE HASIL
    |--------------------------------------------------------------------------
    | Input hasil untuk satu pemeriksaan.
    |
    */

    public function storeHasil(
        Request $request,
        LabPermintaanDetail $detail
    ): JsonResponse {

        /*
        |--------------------------------------------------------------------------
        | Pastikan permintaan masih bisa diisi hasil
        |--------------------------------------------------------------------------
        */

        $labPermintaan = $detail->labPermintaan;

        if (!$labPermintaan) {

            return response()->json([
                'success' => false,
                'message' => 'Permintaan laboratorium tidak ditemukan.',
            ], 404);
        }

        if (in_array(
            $labPermintaan->status,
            ['MENUNGGU', 'BATAL', 'DIVERIFIKASI']
        )) {

            return response()->json([
                'success' => false,
                'message' => 'Hasil belum dapat dimasukkan pada status saat ini.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Satu detail hanya boleh memiliki satu hasil
        |--------------------------------------------------------------------------
        */

        if ($detail->hasil()->exists()) {

            return response()->json([
                'success' => false,
                'message' => 'Hasil untuk pemeriksaan ini sudah ada. Gunakan endpoint update hasil.',
            ], 422);
        }

        $validated = $request->validate([
            'hasil' => [
                'required',
                'string',
            ],

            'flag' => [
                'nullable',
                Rule::in([
                    'NORMAL',
                    'TINGGI',
                    'RENDAH',
                    'KRITIS',
                ]),
            ],

            'catatan' => [
                'nullable',
                'string',
            ],

            'tanggal_pemeriksaan' => [
                'nullable',
                'date',
            ],
        ]);

        try {

            $hasil = LabHasil::create([
                'lab_permintaan_detail_id' =>
                    $detail->id,

                'hasil' =>
                    $validated['hasil'],

                'flag' =>
                    $validated['flag'] ?? null,

                'catatan' =>
                    $validated['catatan'] ?? null,

                'tanggal_pemeriksaan' =>
                    $validated['tanggal_pemeriksaan']
                    ?? now(),
            ]);

            $hasil->load([
                'detail.labPemeriksaan',
                'detail.labPermintaan',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Hasil laboratorium berhasil disimpan.',
                'data' => $hasil,
            ], 201);

        } catch (Throwable $e) {

            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan hasil laboratorium.',
            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE HASIL
    |--------------------------------------------------------------------------
    */

    public function updateHasil(
        Request $request,
        LabHasil $hasil
    ): JsonResponse {

        /*
        |--------------------------------------------------------------------------
        | Tidak boleh mengubah hasil yang sudah diverifikasi
        |--------------------------------------------------------------------------
        */

        if ($hasil->tanggal_verifikasi !== null) {

            return response()->json([
                'success' => false,
                'message' => 'Hasil yang sudah diverifikasi tidak dapat diubah.',
            ], 422);
        }

        $validated = $request->validate([
            'hasil' => [
                'required',
                'string',
            ],

            'flag' => [
                'nullable',
                Rule::in([
                    'NORMAL',
                    'TINGGI',
                    'RENDAH',
                    'KRITIS',
                ]),
            ],

            'catatan' => [
                'nullable',
                'string',
            ],

            'tanggal_pemeriksaan' => [
                'nullable',
                'date',
            ],
        ]);

        try {

            $hasil->update([
                'hasil' =>
                    $validated['hasil'],

                'flag' =>
                    $validated['flag'] ?? null,

                'catatan' =>
                    $validated['catatan'] ?? null,

                'tanggal_pemeriksaan' =>
                    $validated['tanggal_pemeriksaan']
                    ?? $hasil->tanggal_pemeriksaan
                    ?? now(),
            ]);

            $hasil->load([
                'detail.labPemeriksaan',
                'detail.labPermintaan',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Hasil laboratorium berhasil diperbarui.',
                'data' => $hasil,
            ]);

        } catch (Throwable $e) {

            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui hasil laboratorium.',
            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | VERIFIKASI HASIL
    |--------------------------------------------------------------------------
    */

    public function verifikasiHasil(
        Request $request,
        LabHasil $hasil
    ): JsonResponse {

        /*
        |--------------------------------------------------------------------------
        | Validasi
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'catatan' => [
                'nullable',
                'string',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Cek hasil
        |--------------------------------------------------------------------------
        */

        if (empty($hasil->hasil)) {

            return response()->json([
                'success' => false,
                'message' => 'Hasil pemeriksaan belum diisi.',
            ], 422);
        }

        if ($hasil->tanggal_verifikasi !== null) {

            return response()->json([
                'success' => false,
                'message' => 'Hasil ini sudah diverifikasi.',
            ], 422);
        }

        try {

            DB::transaction(function () use (
                $hasil,
                $validated,
                $request
            ) {

                /*
                |--------------------------------------------------------------------------
                | Verifikasi hasil
                |--------------------------------------------------------------------------
                */

                $hasil->update([
                    'catatan' =>
                        $validated['catatan']
                        ?? $hasil->catatan,

                    'tanggal_verifikasi' =>
                        now(),

                    'verified_by' =>
                        $request->user()->id,
                ]);

                /*
                |--------------------------------------------------------------------------
                | Cek apakah semua detail sudah punya hasil
                |--------------------------------------------------------------------------
                */

                $detail = $hasil->detail;

                $labPermintaan = $detail->labPermintaan;

                $totalDetail =
                    $labPermintaan
                        ->details()
                        ->count();

                $totalHasil =
                    $labPermintaan
                        ->details()
                        ->whereHas('hasil')
                        ->count();

                /*
                |--------------------------------------------------------------------------
                | Jika semua hasil sudah ada dan diverifikasi
                |--------------------------------------------------------------------------
                */

                $semuaTerverifikasi =
                    $labPermintaan
                        ->details()
                        ->whereDoesntHave(
                            'hasil',
                            function ($query) {
                                $query->whereNotNull(
                                    'tanggal_verifikasi'
                                );
                            }
                        )
                        ->doesntExist();

                if (
                    $totalDetail > 0 &&
                    $totalHasil === $totalDetail &&
                    $semuaTerverifikasi
                ) {

                    $labPermintaan->update([
                        'status' => 'DIVERIFIKASI',
                    ]);
                }
            });

            $hasil->load([
                'detail.labPemeriksaan',
                'detail.labPermintaan',
                'verifiedBy',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Hasil laboratorium berhasil diverifikasi.',
                'data' => $hasil,
            ]);

        } catch (Throwable $e) {

            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memverifikasi hasil laboratorium.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : null,
            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | GENERATE NO LAB
    |--------------------------------------------------------------------------
    |
    | Format:
    |
    | LAB-20260819-0001
    |
    */

    private function generateNoLab(): string
    {
        $date = now()->format('Ymd');

        $prefix = "LAB-{$date}-";

        $lastLab = LabPermintaan::where(
            'no_lab',
            'like',
            "{$prefix}%"
        )
            ->orderByDesc('id')
            ->first();

        if (!$lastLab) {
            return $prefix . '0001';
        }

        $lastNumber = (int) str_replace(
            $prefix,
            '',
            $lastLab->no_lab
        );

        return $prefix .
            str_pad(
                $lastNumber + 1,
                4,
                '0',
                STR_PAD_LEFT
            );
    }
}
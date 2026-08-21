<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LabPemeriksaan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LabPemeriksaanController extends Controller
{
    /**
     * Display a listing of all lab examinations.
     */
    public function index()
    {
        $labPemeriksaans = LabPemeriksaan::orderBy('nama', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data pemeriksaan laboratorium berhasil diambil.',
            'data' => $labPemeriksaans,
        ]);
    }


    /**
     * Display only active lab examinations.
     *
     * Digunakan ketika dokter/petugas memilih
     * jenis pemeriksaan laboratorium.
     */
    public function active()
    {
        $labPemeriksaans = LabPemeriksaan::where('is_active', true)
            ->orderBy('kategori', 'asc')
            ->orderBy('nama', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data pemeriksaan laboratorium aktif berhasil diambil.',
            'data' => $labPemeriksaans,
        ]);
    }


    /**
     * Store a newly created lab examination.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => [
                'required',
                'string',
                'max:255',
                'unique:lab_pemeriksaans,kode',
            ],

            'nama' => [
                'required',
                'string',
                'max:255',
            ],

            'kategori' => [
                'nullable',
                'string',
                'max:255',
            ],

            'satuan' => [
                'nullable',
                'string',
                'max:255',
            ],

            'nilai_rujukan' => [
                'nullable',
                'string',
                'max:255',
            ],

            'harga' => [
                'required',
                'numeric',
                'min:0',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Default Active
        |--------------------------------------------------------------------------
        */

        $validated['is_active'] =
            $validated['is_active'] ?? true;


        /*
        |--------------------------------------------------------------------------
        | Create
        |--------------------------------------------------------------------------
        */

        $labPemeriksaan =
            LabPemeriksaan::create($validated);


        return response()->json([
            'success' => true,
            'message' =>
                'Pemeriksaan laboratorium berhasil ditambahkan.',
            'data' => $labPemeriksaan,
        ], 201);
    }


    /**
     * Display the specified lab examination.
     */
    public function show(
        LabPemeriksaan $labPemeriksaan
    ) {
        return response()->json([
            'success' => true,
            'message' =>
                'Detail pemeriksaan laboratorium berhasil diambil.',
            'data' => $labPemeriksaan,
        ]);
    }


    /**
     * Update the specified lab examination.
     */
    public function update(
        Request $request,
        LabPemeriksaan $labPemeriksaan
    ) {
        $validated = $request->validate([
            'kode' => [
                'required',
                'string',
                'max:255',

                Rule::unique(
                    'lab_pemeriksaans',
                    'kode'
                )->ignore($labPemeriksaan->id),
            ],

            'nama' => [
                'required',
                'string',
                'max:255',
            ],

            'kategori' => [
                'nullable',
                'string',
                'max:255',
            ],

            'satuan' => [
                'nullable',
                'string',
                'max:255',
            ],

            'nilai_rujukan' => [
                'nullable',
                'string',
                'max:255',
            ],

            'harga' => [
                'required',
                'numeric',
                'min:0',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Pertahankan status jika tidak dikirim
        |--------------------------------------------------------------------------
        */

        if (!array_key_exists(
            'is_active',
            $validated
        )) {
            $validated['is_active'] =
                $labPemeriksaan->is_active;
        }


        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */

        $labPemeriksaan->update(
            $validated
        );


        return response()->json([
            'success' => true,
            'message' =>
                'Pemeriksaan laboratorium berhasil diperbarui.',
            'data' =>
                $labPemeriksaan->fresh(),
        ]);
    }


    /**
     * Remove the specified lab examination.
     */
    public function destroy(
        LabPemeriksaan $labPemeriksaan
    ) {
        /*
        |--------------------------------------------------------------------------
        | Cek apakah sudah digunakan
        |--------------------------------------------------------------------------
        */

        if (
            $labPemeriksaan
                ->details()
                ->exists()
        ) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Pemeriksaan laboratorium tidak dapat dihapus karena sudah digunakan dalam permintaan laboratorium.',
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Delete
        |--------------------------------------------------------------------------
        */

        $labPemeriksaan->delete();


        return response()->json([
            'success' => true,
            'message' =>
                'Pemeriksaan laboratorium berhasil dihapus.',
        ]);
    }
}
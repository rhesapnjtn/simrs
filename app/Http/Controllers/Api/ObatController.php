<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Obat;
use Illuminate\Http\Request;

class ObatController extends Controller
{
    /**
     * Menampilkan daftar obat.
     */
    public function index(Request $request)
    {
        $query = Obat::query();

        // Search kode atau nama obat
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where(
                    'kode_obat',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'nama_obat',
                    'like',
                    "%{$search}%"
                );
            });
        }

        // Filter obat aktif
        if ($request->has('is_active')) {
            $query->where(
                'is_active',
                filter_var(
                    $request->is_active,
                    FILTER_VALIDATE_BOOLEAN
                )
            );
        }

        $obats = $query
            ->orderBy('nama_obat')
            ->get();

        return response()->json([
            'obats' => $obats,
        ]);
    }


    /**
     * Menambahkan obat baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_obat' => [
                'required',
                'string',
                'max:50',
                'unique:obats,kode_obat',
            ],

            'nama_obat' => [
                'required',
                'string',
                'max:255',
            ],

            'jenis' => [
                'nullable',
                'string',
                'max:100',
            ],

            'satuan' => [
                'required',
                'string',
                'max:50',
            ],

            'stok' => [
                'required',
                'integer',
                'min:0',
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

        // Default aktif
        if (!isset($validated['is_active'])) {
            $validated['is_active'] = true;
        }

        $obat = Obat::create($validated);

        return response()->json([
            'message' => 'Obat berhasil ditambahkan.',
            'obat' => $obat,
        ], 201);
    }


    /**
     * Menampilkan satu obat.
     */
    public function show(Obat $obat)
    {
        return response()->json([
            'obat' => $obat,
        ]);
    }


    /**
     * Mengubah data obat.
     */
    public function update(
        Request $request,
        Obat $obat
    ) {
        $validated = $request->validate([
            'kode_obat' => [
                'required',
                'string',
                'max:50',
                'unique:obats,kode_obat,' . $obat->id,
            ],

            'nama_obat' => [
                'required',
                'string',
                'max:255',
            ],

            'jenis' => [
                'nullable',
                'string',
                'max:100',
            ],

            'satuan' => [
                'required',
                'string',
                'max:50',
            ],

            'stok' => [
                'required',
                'integer',
                'min:0',
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

        $obat->update($validated);

        return response()->json([
            'message' => 'Obat berhasil diperbarui.',
            'obat' => $obat->fresh(),
        ]);
    }


    /**
     * Menghapus obat.
     */
    public function destroy(Obat $obat)
    {
        $obat->delete();

        return response()->json([
            'message' => 'Obat berhasil dihapus.',
        ]);
    }
}
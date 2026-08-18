<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Poli;
use Illuminate\Http\Request;

class PoliController extends Controller
{
    public function index()
    {
        return response()->json([
            'polis' => Poli::latest()->get(),
        ]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:20|unique:polis,kode',
            'nama' => 'required|string|max:255',

            // Prefix nomor antrean
            'prefix' => 'required|string|max:10|unique:polis,prefix',

            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean',
        ]);


        $poli = Poli::create($validated);


        return response()->json([
            'message' => 'Poli berhasil dibuat.',
            'poli' => $poli,
        ], 201);
    }


    public function show(Poli $poli)
    {
        return response()->json([
            'poli' => $poli,
        ]);
    }


    public function update(Request $request, Poli $poli)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:20|unique:polis,kode,' . $poli->id,
            'nama' => 'required|string|max:255',

            // Prefix nomor antrean
            'prefix' => 'required|string|max:10|unique:polis,prefix,' . $poli->id,

            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean',
        ]);


        $poli->update($validated);


        return response()->json([
            'message' => 'Poli berhasil diperbarui.',
            'poli' => $poli,
        ]);
    }


    public function destroy(Poli $poli)
    {
        $poli->delete();


        return response()->json([
            'message' => 'Poli berhasil dihapus.',
        ]);
    }
}
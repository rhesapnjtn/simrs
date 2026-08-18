<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pasien;
use Illuminate\Http\Request;

class PasienController extends Controller
{
    public function index()
    {
        $pasiens = Pasien::latest()->get();

        return response()->json([
            'pasiens' => $pasiens
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik' => 'nullable|string|max:20|unique:pasiens,nik',
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'nullable|string',
            'no_telepon' => 'nullable|string|max:30',
            'golongan_darah' => 'nullable|string|max:5',
            'kontak_darurat' => 'nullable|string|max:100',
            'no_telepon_darurat' => 'nullable|string|max:30',
            'is_active' => 'boolean',
        ]);

        // Generate nomor rekam medis
        $lastPasien = Pasien::latest('id')->first();

        $nextNumber = $lastPasien
            ? $lastPasien->id + 1
            : 1;

        $validated['no_rm'] =
            'RM-' . date('Y') . '-' . str_pad(
                $nextNumber,
                6,
                '0',
                STR_PAD_LEFT
            );

        $validated['is_active'] =
            $validated['is_active'] ?? true;

        $pasien = Pasien::create($validated);

        return response()->json([
            'message' => 'Pasien berhasil ditambahkan.',
            'pasien' => $pasien
        ], 201);
    }

    public function show(Pasien $pasien)
    {
        return response()->json([
            'pasien' => $pasien
        ]);
    }

    public function update(Request $request, Pasien $pasien)
    {
        $validated = $request->validate([
            'nik' => 'nullable|string|max:20|unique:pasiens,nik,' . $pasien->id,
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'nullable|string',
            'no_telepon' => 'nullable|string|max:30',
            'golongan_darah' => 'nullable|string|max:5',
            'kontak_darurat' => 'nullable|string|max:100',
            'no_telepon_darurat' => 'nullable|string|max:30',
            'is_active' => 'boolean',
        ]);

        $pasien->update($validated);

        return response()->json([
            'message' => 'Data pasien berhasil diperbarui.',
            'pasien' => $pasien
        ]);
    }

    public function destroy(Pasien $pasien)
    {
        $pasien->delete();

        return response()->json([
            'message' => 'Pasien berhasil dihapus.'
        ]);
    }
}
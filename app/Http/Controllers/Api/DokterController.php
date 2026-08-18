<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dokter;
use App\Models\Poli;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DokterController extends Controller
{
    /**
     * Menampilkan semua dokter.
     */
    public function index()
    {
        $dokters = Dokter::with(['user', 'poli'])
            ->latest()
            ->get();

        return response()->json([
            'dokters' => $dokters,
        ]);
    }


    /**
     * Menyimpan dokter baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([

            'user_id' => [
                'nullable',
                'exists:users,id',
                Rule::unique('dokters', 'user_id'),
            ],

            'poli_id' => [
                'required',
                'exists:polis,id',
            ],

            'nomor_str' => [
                'nullable',
                'string',
                'max:100',
                'unique:dokters,nomor_str',
            ],

            'nama' => [
                'required',
                'string',
                'max:255',
            ],

            'spesialisasi' => [
                'nullable',
                'string',
                'max:255',
            ],

            'no_telepon' => [
                'nullable',
                'string',
                'max:30',
            ],

            'is_active' => [
                'boolean',
            ],

        ]);

        $dokter = Dokter::create($validated);

        $dokter->load(['user', 'poli']);

        return response()->json([
            'message' => 'Dokter berhasil dibuat.',
            'dokter' => $dokter,
        ], 201);
    }


    /**
     * Menampilkan satu dokter.
     */
    public function show(Dokter $dokter)
    {
        $dokter->load(['user', 'poli']);

        return response()->json([
            'dokter' => $dokter,
        ]);
    }


    /**
     * Memperbarui dokter.
     */
    public function update(Request $request, Dokter $dokter)
    {
        $validated = $request->validate([

            'user_id' => [
                'nullable',
                'exists:users,id',
                Rule::unique('dokters', 'user_id')
                    ->ignore($dokter->id),
            ],

            'poli_id' => [
                'required',
                'exists:polis,id',
            ],

            'nomor_str' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('dokters', 'nomor_str')
                    ->ignore($dokter->id),
            ],

            'nama' => [
                'required',
                'string',
                'max:255',
            ],

            'spesialisasi' => [
                'nullable',
                'string',
                'max:255',
            ],

            'no_telepon' => [
                'nullable',
                'string',
                'max:30',
            ],

            'is_active' => [
                'boolean',
            ],

        ]);

        $dokter->update($validated);

        $dokter->load(['user', 'poli']);

        return response()->json([
            'message' => 'Dokter berhasil diperbarui.',
            'dokter' => $dokter,
        ]);
    }


    /**
     * Menghapus dokter.
     */
    public function destroy(Dokter $dokter)
    {
        $dokter->delete();

        return response()->json([
            'message' => 'Dokter berhasil dihapus.',
        ]);
    }


    /**
     * Mengambil user dengan role DOKTER
     * yang belum mempunyai data dokter.
     */
    public function users()
    {
        $users = User::role('DOKTER')
            ->whereDoesntHave('dokter')
            ->get();

        return response()->json([
            'users' => $users,
        ]);
    }


    /**
     * Mengambil semua poli aktif.
     */
    public function polis()
    {
        $polis = Poli::where('is_active', true)
            ->orderBy('nama')
            ->get();

        return response()->json([
            'polis' => $polis,
        ]);
    }
}
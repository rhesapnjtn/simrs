<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Menampilkan semua user.
     */
    public function index()
    {
        $users = User::with('roles')
            ->latest()
            ->get();

        return response()->json([
            'users' => $users,
        ]);
    }

    /**
     * Membuat user baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'exists:roles,name'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole($validated['role']);

        return response()->json([
            'message' => 'User berhasil dibuat.',
            'user' => $user->load('roles'),
        ], 201);
    }

    /**
     * Menampilkan satu user.
     */
    public function show(User $user)
    {
        return response()->json([
            'user' => $user->load('roles'),
        ]);
    }

    /**
     * Update user.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'unique:users,email,' . $user->id,
            ],
            'role' => ['required', 'exists:roles,name'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        $user->syncRoles([$validated['role']]);

        return response()->json([
            'message' => 'User berhasil diperbarui.',
            'user' => $user->load('roles'),
        ]);
    }

    /**
     * Hapus user.
     */
    public function destroy(User $user)
    {
        if ($user->hasRole('SUPER_ADMIN')) {
            return response()->json([
                'message' => 'Super Admin tidak dapat dihapus.',
            ], 403);
        }

        $user->delete();

        return response()->json([
            'message' => 'User berhasil dihapus.',
        ]);
    }

    /**
     * Daftar role.
     */
    public function roles()
    {
        return response()->json([
            'roles' => Role::orderBy('name')->get(),
        ]);
    }
}
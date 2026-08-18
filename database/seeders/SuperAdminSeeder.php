<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            [
                'email' => 'superadmin@simrs.test',
            ],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password123'),
            ]
        );

        $user->assignRole('SUPER_ADMIN');
    }
}
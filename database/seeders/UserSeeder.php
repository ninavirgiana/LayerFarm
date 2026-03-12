<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ADMIN UTAMA
        User::updateOrCreate(
            [
                'email' => 'vrgnina001@gmail.com',
            ],
            [
                'name' => 'Mashari',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // USER UNTUK KARYAWAN
        User::updateOrCreate(
            [
                'email' => 'ninavirgiana931@gmail.com',
            ],
            [
                'name' => 'Misno',
                'password' => Hash::make('password'),
                'role' => 'karyawan',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}

<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Database\Seeder;

class KaryawanSeeder extends Seeder
{
    public function run(): void
    {
        $userMisno = User::where('email', 'ninavirgiana931@gmail.com')->first();

        Karyawan::updateOrCreate(
            [
                'nama_karyawan' => 'Misno'
            ],
            [
                'user_id' => $userMisno ? $userMisno->id : null,
                'nama_karyawan' => 'Misno',
                'no_hp' => '085859716566',
                'alamat' => 'Tanjungkalang',
                'status_karyawan' => 'aktif'
            ]
        );
    }
}

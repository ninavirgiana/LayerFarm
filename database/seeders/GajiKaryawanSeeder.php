<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GajiKaryawan;

class GajiKaryawanSeeder extends Seeder
{
    public function run(): void
    {
        GajiKaryawan::create([
            'karyawan_id' => 1,
            'bulan' => 1,
            'tahun' => 2026,
            'gaji_pokok' => 2500000,
            'tanggal_bayar' => '2026-01-31',
            'status_bayar' => 'dibayar'
        ]);
    }
}

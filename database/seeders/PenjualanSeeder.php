<?php

namespace Database\Seeders;

use App\Models\Penjualan;
use Illuminate\Database\Seeder;

class PenjualanSeeder extends Seeder
{
    public function run(): void
    {
        // Penjualan 1
        Penjualan::create([
            'user_id' => 1,
            'jenis_produk' => 'telur',
            'berat_terjual' => 50,
            'harga_satuan' => 27500,
            'total_penjualan' => 1375000,
            'tanggal_penjualan' => now()
        ]);

        // Penjualan 2
        Penjualan::create([
            'user_id' => 1,
            'jenis_produk' => 'telur',
            'berat_terjual' => 80,
            'harga_satuan' => 27500,
            'total_penjualan' => 2200000,
            'tanggal_penjualan' => now()
        ]);
    }
}

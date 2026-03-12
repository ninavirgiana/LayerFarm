<?php

namespace Database\Seeders;

use App\Models\InventoriKandang;
use Illuminate\Database\Seeder;

class InventoriKandangSeeder extends Seeder
{
    public function run(): void
    {
        // Transaksi Masuk
        InventoriKandang::create([
            'user_id' => 1,
            'tanggal' => now(),
            'nama_barang' => 'Konsentrat',
            'jenis_transaksi' => 'masuk',
            'jumlah' => 15,
            'satuan' => 'karung',
            'harga_satuan' => 300000,
            'total_harga' => 4500000
        ]);

        // Transaksi Keluar
        InventoriKandang::create([
            'user_id' => 1,
            'tanggal' => now(),
            'nama_barang' => 'Konsentrat',
            'jenis_transaksi' => 'keluar',
            'jumlah' => 5,
            'satuan' => 'karung',
            'harga_satuan' => null,
            'total_harga' => null
        ]);
    }
}

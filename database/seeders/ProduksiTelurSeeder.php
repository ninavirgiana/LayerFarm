<?php

namespace Database\Seeders;

use App\Models\ProduksiTelur;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProduksiTelurSeeder extends Seeder
{
    public function run(): void
    {
        

        // Shift Pagi
        ProduksiTelur::create([
            'kandang_id' => 2,
            'user_id' => 1,
            'shift' => 'pagi',
            'berat_telur_layak' => 150,
            'berat_telur_rusak' => 0,
            'berat_telur_total' => 150,
            'tanggal_produksi' => now(),
        ]);

        // Shift Sore
        ProduksiTelur::create([
            'kandang_id' => 2,
            'user_id' => 1,
            'shift' => 'sore',
            'berat_telur_layak' => 80,
            'berat_telur_rusak' => 0,
            'berat_telur_total' => 80,
            'tanggal_produksi' => now(),
        ]);
    }
}

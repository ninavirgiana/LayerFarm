<?php

namespace Database\Seeders;

use App\Models\Kandang;
use Illuminate\Database\Seeder;

class KandangSeeder extends Seeder
{
    public function run(): void
    {
        Kandang::create([
            'nama_kandang' => 'Kandang Pembesaran',
            'jenis_kandang' => 'Pembesaran'
        ]);

        Kandang::create([
            'nama_kandang' => 'Kandang Produksi',
            'jenis_kandang' => 'Produksi'
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MutasiAyam;

class MutasiAyamSeeder extends Seeder
{
    public function run(): void
    {
        MutasiAyam::create([
            'kandang_id' => 1,
            'user_id' => 1,
            'jenis_mutasi' => 'masuk',
            'jumlah' => 7000,
            'tanggal' => now(),
            'keterangan' => 'DOC baru'
        ]);
    }
}

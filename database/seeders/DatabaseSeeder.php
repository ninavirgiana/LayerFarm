<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            KandangSeeder::class,
            KaryawanSeeder::class,
            GajiKaryawanSeeder::class,
            InventoriKandangSeeder::class,
            MutasiAyamSeeder::class,
            ProduksiTelurSeeder::class,
            PenjualanSeeder::class,
        ]);
    }
}

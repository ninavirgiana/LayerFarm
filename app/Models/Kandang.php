<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kandang extends Model
{
    use HasFactory;

    protected $table = 'kandang';

    protected $fillable = [
        'nama_kandang',
        'jenis_kandang'
    ];

    public function mutasiAyam()
    {
        return $this->hasMany(MutasiAyam::class);
    }

    public function produksiTelur()
    {
        return $this->hasMany(ProduksiTelur::class);
    }
}

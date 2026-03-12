<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    protected $table = 'penjualan';

    protected $fillable = [
        'user_id',
        'jenis_produk',
        'berat_terjual',
        'harga_satuan',
        'total_penjualan',
        'tanggal_penjualan',
    ];

    // Relasi ke User (penjual)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


}

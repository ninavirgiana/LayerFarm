<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventoriKandang extends Model
{
    use HasFactory;

    protected $table = 'inventori_kandang';

    protected $fillable = [
        'user_id',
        'tanggal',
        'nama_barang',
        'jenis_transaksi',
        'jumlah',
        'satuan',
        'harga_satuan',
        'total_harga'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProduksiTelur extends Model
{
    use HasFactory;

    protected $table = 'produksi_telur';


    protected $fillable = [
        'kandang_id',
        'user_id',
        'shift',
        'berat_telur_layak',
        'berat_telur_rusak',
        'berat_telur_total',
        'tanggal_produksi',
    ];




    public function kandang()
    {
        return $this->belongsTo(Kandang::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

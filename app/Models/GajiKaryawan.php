<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GajiKaryawan extends Model
{
    use HasFactory;

    protected $table = 'gaji_karyawan';

    protected $fillable = [
        'karyawan_id',
        'bulan',
        'tahun',
        'gaji_pokok',
        'tanggal_bayar',
        'status_bayar' 
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
}

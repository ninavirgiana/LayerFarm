<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MutasiAyam extends Model
{
    use HasFactory;

    protected $table = 'mutasi_ayam';

    protected $fillable = [
        'kandang_id',
        'user_id',
        'jenis_mutasi',
        'jumlah',
        'tanggal',
        'keterangan',
    ];

    public $timestamps = true;

    // Relasi ke Kandang
    public function kandang()
    {
        return $this->belongsTo(Kandang::class, 'kandang_id');
    }

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

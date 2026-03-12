<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'karyawan';

    protected $fillable = [
        'user_id',
        'nama_karyawan',
        'no_hp',
        'alamat',
        'status_karyawan'
    ];



    /*  RELASI  */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function gaji()
    {
        return $this->hasMany(GajiKaryawan::class);
    }
    public function isAktif()
    {
        return $this->status_karyawan === 'aktif';
    }
}

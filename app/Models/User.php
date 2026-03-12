<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable; 
use App\Notifications\ResetPasswordNotification;


class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /* ========== ROLE CHECK ========== */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isKaryawan(): bool
    {
        return $this->role === 'karyawan';
    }


    /* RELASI */
    public function inventoriKandang()
    {
        return $this->hasMany(InventoriKandang::class);
    }

    public function penjualan()
    {
        return $this->hasMany(Penjualan::class);
    }

    public function karyawan()
    {
        return $this->hasOne(Karyawan::class);
    }

    public function mutasiAyam()
    {
        return $this->hasMany(MutasiAyam::class);
    }

    public function produksiTelur()
    {
        return $this->hasMany(ProduksiTelur::class);
    }
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}

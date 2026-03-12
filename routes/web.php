<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\{
    HomeController,
    KaryawanController,
    GajiController,
    PakanController,
    AyamController,
    HasilController,
    KeuanganController,
    LaporanController,
    ProfilController,
    LaporanExportController
};
use App\Http\Middleware\CheckRole;

require __DIR__ . '/auth.php';

// ROOT
// Route::get('/', function () {
//     return redirect()->route('login');
// });

Route::get('/', action: fn() => Auth::check() ? redirect()->route('beranda') : redirect()->route('login'));
// Route::get('/', function () {
//     return view('welcome');
// });

// Semua route harus login
Route::middleware('auth')->group(function () {

    /*
       SEMUA USER LOGIN
    */
    Route::get('/beranda', [HomeController::class, 'index'])->name('beranda');

    Route::get('/profil', [ProfilController::class, 'index'])->name('profil');
    Route::patch('/profil/{id}', [ProfilController::class, 'update'])->name('profil.update');
    Route::patch('/profil/{id}/password', [ProfilController::class, 'updatePassword'])
        ->name('profil.password.update');


    /*
       ADMIN + KARYAWAN
       (tanpa delete)
     */
    Route::middleware([CheckRole::class . ':admin,karyawan'])->group(function () {

        Route::prefix('pakan')->group(function () {
            Route::get('/', [PakanController::class, 'index'])->name('pakan');
            Route::post('/store', [PakanController::class, 'store'])->name('pakan.store');
            Route::put('/{id}', [PakanController::class, 'update'])->name('pakan.update');
        });

        Route::prefix('ayam')->group(function () {
            Route::get('/', [AyamController::class, 'index'])->name('ayam');
            Route::post('/store', [AyamController::class, 'store'])->name('ayam.store');
            Route::put('/{id}', [AyamController::class, 'update'])->name('ayam.update');
        });

        Route::prefix('hasil')->group(function () {
            Route::get('/', [HasilController::class, 'index'])->name('hasil');
            Route::post('/', [HasilController::class, 'store'])->name('hasil.store');
            Route::put('/{id}', [HasilController::class, 'update'])->name('hasil.update');
        });

        Route::get('/keuangan', [KeuanganController::class, 'index'])->name('keuangan.index');
        Route::post('/keuangan', [KeuanganController::class, 'store'])->name('keuangan.store');
        Route::put('/keuangan/{id}', [KeuanganController::class, 'update'])->name('keuangan.update');
    });


    /*
       ADMIN ONLY
       (boleh delete + master)
   */
    Route::middleware([CheckRole::class . ':admin'])->group(function () {

        // DELETE data operasional
        Route::delete('/pakan/{id}', [PakanController::class, 'destroy'])->name('pakan.delete');
        Route::delete('/ayam/{id}', [AyamController::class, 'destroy'])->name('ayam.delete');
        Route::delete('/hasil/{id}', [HasilController::class, 'destroy'])->name('hasil.destroy');
        Route::delete('/keuangan/{id}', [KeuanganController::class, 'destroy'])->name('keuangan.delete');

        // Manajemen karyawan
        Route::get('/karyawan', [KaryawanController::class, 'index'])->name('karyawan');
        Route::post('/karyawan', [KaryawanController::class, 'store'])->name('karyawan.store');
        Route::put('/karyawan/{id}', [KaryawanController::class, 'update'])->name('karyawan.update');
        Route::patch('/karyawan/{id}/nonaktif', [KaryawanController::class, 'nonaktifkan'])->name('karyawan.nonaktifkan');
        Route::patch('/karyawan/{id}/aktif', [KaryawanController::class, 'aktifkan'])->name('karyawan.aktifkan');

        // Gaji & laporan
        Route::get('/gaji', [GajiController::class, 'index'])->name('gaji');
        Route::post('/gaji', [GajiController::class, 'store'])->name('gaji.store');

        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');
        Route::get('/laporan/export', [LaporanController::class, 'export'])->name('laporan.export');
    });
});

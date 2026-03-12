<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('populasi_ayam');
    }

    public function down(): void
    {
        Schema::create('populasi_ayam', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kandang_id')->constrained('kandang');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->integer('jumlah_ayam');
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }
};

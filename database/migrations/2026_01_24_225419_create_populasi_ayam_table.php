<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('populasi_ayam', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kandang_id')->constrained('kandang'); // foreign key ke tabel kandang
            $table->foreignId('user_id')->nullable()->constrained('users'); // foreign key ke users, nullable
            $table->integer('jumlah_ayam');
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('populasi_ayam');
    }
};

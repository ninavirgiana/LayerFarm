<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::dropIfExists('detail_penjualan');
    }

    public function down()
    {
        Schema::create('detail_penjualan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penjualan_id')->constrained()->cascadeOnDelete();
            $table->string('produk_id'); // sesuaikan dengan kolom lama
            $table->integer('jumlah');
            $table->decimal('harga_satuan', 12, 2);
            $table->timestamps();
        });
    }
};

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
        // Hapus tabel produk
        Schema::dropIfExists('produk');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan tabel produk jika rollback
        Schema::create('produk', function (Blueprint $table) {
            $table->id(); // id primary key auto increment
            $table->string('nama_produk');
            $table->string('jenis_produk');
            $table->string('satuan');
            $table->timestamps(); // created_at & updated_at
        });
    }
};

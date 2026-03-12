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
        Schema::create('inventori_kandang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users'); // FK ke users
            $table->string('nama_barang');
            $table->enum('jenis_transaksi', ['masuk', 'keluar']);
            $table->decimal('jumlah', 10, 2);
            $table->string('satuan');
            $table->decimal('harga_satuan', 12, 2)->nullable();
            $table->decimal('total_harga', 14, 2)->nullable();
            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventori_kandang');
    }
};

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
        Schema::create('produksi_telur', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kandang_id')->constrained('kandang'); // FK ke tabel kandang
            $table->foreignId('user_id')->nullable()->constrained('users'); // FK ke users, nullable
            $table->enum('shift', ['pagi', 'sore']);
            $table->decimal('berat_telur_layak', 8, 2);
            $table->decimal('berat_telur_rusak', 8, 2);
            $table->decimal('berat_telur_total', 8, 2);
            $table->date('tanggal_produksi');
            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produksi_telur');
    }
};

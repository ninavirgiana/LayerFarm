<?php



use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mutasi_ayam', function (Blueprint $table) {
            $table->id();

            // kolom foreign key ke kandang
            $table->unsignedBigInteger('kandang_id');
            $table->foreign('kandang_id')->references('id')->on('kandang')->cascadeOnDelete();

            // kolom foreign key ke users
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();

            // kolom lainnya
            $table->enum('jenis_mutasi', ['masuk','keluar','mati','afkir','pindah']);
            $table->integer('jumlah');
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mutasi_ayam');
    }
};

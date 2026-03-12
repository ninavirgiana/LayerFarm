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
        Schema::create('karyawan', function (Blueprint $table) {
            $table->id(); // id primary key auto increment
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // jika relasi ke user ada
            $table->string('nama_karyawan');
            $table->string('no_hp');
            $table->string('alamat')->nullable();
            $table->enum('status_karyawan', ['aktif', 'nonaktif']);
            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawan');
    }
};

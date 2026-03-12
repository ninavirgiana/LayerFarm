<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produksi_telur', function (Blueprint $table) {
            $table->decimal('berat_telur_layak', 8, 2)->change();
            $table->decimal('berat_telur_rusak', 8, 2)->change();
            $table->decimal('berat_telur_total', 8, 2)->change();
        });
    }

    public function down(): void
    {
        Schema::table('produksi_telur', function (Blueprint $table) {
            $table->integer('berat_telur_layak')->change();
            $table->integer('berat_telur_rusak')->change();
            $table->integer('berat_telur_total')->change();
        });
    }
};

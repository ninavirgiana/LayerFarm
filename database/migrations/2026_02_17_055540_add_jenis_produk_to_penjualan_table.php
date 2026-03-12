<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('penjualan', function (Blueprint $table) {

            if (!Schema::hasColumn('penjualan', 'jenis_produk')) {
                $table->enum('jenis_produk', ['telur', 'afkir'])
                    ->after('user_id');
            }
        });
    }

    public function down()
    {
        Schema::table('penjualan', function (Blueprint $table) {

            if (Schema::hasColumn('penjualan', 'jenis_produk')) {
                $table->dropColumn('jenis_produk');
            }
        });
    }
};

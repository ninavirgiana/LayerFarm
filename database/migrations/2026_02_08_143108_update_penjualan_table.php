<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('penjualan', function (Blueprint $table) {
            // berat_terjual sudah ada → skip kalau perlu
            if (!Schema::hasColumn('penjualan', 'berat_terjual')) {
                $table->decimal('berat_terjual', 8, 2)->after('user_id');
            }

            // harga_satuan belum ada → tambah
            if (!Schema::hasColumn('penjualan', 'harga_satuan')) {
                $table->decimal('harga_satuan', 12, 2)->after('berat_terjual');
            }

            // total_penjualan sudah ada → lewati

            // tanggal_penjualan baru → gunakan nullable supaya data lama aman
            if (!Schema::hasColumn('penjualan', 'tanggal_penjualan')) {
                $table->date('tanggal_penjualan')->nullable()->after('total_penjualan');
            }
        });
    }

    public function down()
    {
        Schema::table('penjualan', function (Blueprint $table) {
            if (Schema::hasColumn('penjualan', 'berat_terjual')) {
                $table->dropColumn('berat_terjual');
            }
            if (Schema::hasColumn('penjualan', 'harga_satuan')) {
                $table->dropColumn('harga_satuan');
            }
            if (Schema::hasColumn('penjualan', 'tanggal_penjualan')) {
                $table->dropColumn('tanggal_penjualan');
            }
        });
    }
};

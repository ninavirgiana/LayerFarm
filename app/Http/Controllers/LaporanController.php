<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\LaporanExportService;


class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->get('tahun', now()->year);

        return view('laporan', [
            'tahun'      => $tahun,
            'produksi'   => $this->laporanProduksi($tahun),
            'inventori'  => $this->laporanInventori($tahun),
            'keuangan'   => $this->laporanKeuangan($tahun),
        ]);
    }


    private function laporanProduksi(int $tahun)
    {
        return DB::table('produksi_telur')
            ->selectRaw('
            MONTH(tanggal_produksi) AS bulan,
            CEIL(DAY(tanggal_produksi) / 7) AS minggu,
            SUM(berat_telur_total) AS total_produksi
        ')
            ->whereYear('tanggal_produksi', $tahun)
            ->groupBy('bulan', 'minggu')
            ->orderBy('bulan')
            ->orderBy('minggu')
            ->get();
    }

    private function laporanInventori(int $tahun)
    {
        return DB::table('inventori_kandang')
            ->selectRaw('
            MONTH(created_at) AS bulan,
            CEIL(DAY(created_at) / 7) AS minggu,
            SUM(CASE WHEN jenis_transaksi = "masuk" THEN jumlah ELSE 0 END) AS masuk,
            SUM(CASE WHEN jenis_transaksi = "keluar" THEN jumlah ELSE 0 END) AS keluar
        ')
            ->whereYear('created_at', $tahun)
            ->groupBy('bulan', 'minggu')
            ->orderBy('bulan')
            ->orderBy('minggu')
            ->get();
    }

    /**
     * LAPORAN KEUANGAN (BULANAN)
     */
    private function laporanKeuangan(int $tahun)
    {
        // PEMASUKAN (PENJUALAN)
        // sumber: tabel penjualan
        $pemasukan = DB::table('penjualan')
            ->selectRaw('
            MONTH(tanggal_penjualan) AS bulan,
            SUM(total_penjualan) AS total
        ')
            ->whereYear('tanggal_penjualan', $tahun)
            ->groupBy('bulan')
            ->get();

        // PENGELUARAN INVENTORI
        $pengeluaranInventori = DB::table('inventori_kandang')
            ->selectRaw('
            MONTH(created_at) AS bulan,
            SUM(total_harga) AS total
        ')
            ->whereYear('created_at', $tahun)
            ->where('jenis_transaksi', 'masuk')
            ->groupBy('bulan')
            ->get();

        // GAJI KARYAWAN
        $gaji = DB::table('gaji_karyawan')
            ->selectRaw('bulan, SUM(gaji_pokok) AS total')
            ->where('tahun', $tahun)
            ->groupBy('bulan')
            ->get();

        return [
            'pemasukan' => $pemasukan,
            'inventori' => $pengeluaranInventori,
            'gaji'      => $gaji,
        ];
    }

    public function export(Request $request)
    {
        $tahun = $request->get('tahun', now()->year);
        return app(LaporanExportService::class)->export($tahun);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\ProduksiTelur;
use App\Models\InventoriKandang;
use App\Models\Penjualan;
use App\Models\MutasiAyam;

use Illuminate\Support\Facades\Auth;


class HomeController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $today = Carbon::today();

        /*  STAT UMUM */

        $jumlahMasuk = MutasiAyam::where('jenis_mutasi', 'masuk')->sum('jumlah');
        $jumlahKeluar = MutasiAyam::whereIn('jenis_mutasi', ['keluar', 'mati', 'afkir', 'pindah'])->sum('jumlah');
        $jumlahAyam = $jumlahMasuk - $jumlahKeluar;

        $produksiHariIni = ProduksiTelur::whereDate('tanggal_produksi', $today)
            ->sum(DB::raw('berat_telur_layak + berat_telur_rusak'));

        // total stok pakan global (yang lama)
        $stokPakan = InventoriKandang::where('jenis_transaksi', 'masuk')->sum('jumlah')
            - InventoriKandang::where('jenis_transaksi', 'keluar')->sum('jumlah');

        // stok per barang + deteksi stok menipis
        $stok = InventoriKandang::selectRaw("
    nama_barang,
    SUM(CASE WHEN jenis_transaksi = 'masuk' THEN jumlah ELSE 0 END)
    -
    SUM(CASE WHEN jenis_transaksi = 'keluar' THEN jumlah ELSE 0 END)
    AS stok
")
            ->groupBy('nama_barang')
            ->get();

        $batasMinimum = [
            'Jagung'        => 5,
            'Katul'         => 5,
            'Konsentrat'    => 5,
            'Pakan Layer'   => 5,
            'Pakan Starter' => 5,
        ];

        $stokMenipis = $stok->filter(function ($item) use ($batasMinimum) {
            $min = $batasMinimum[$item->nama_barang] ?? 5;
            return $item->stok <= $min;
        });
        // untuk Admin & Karyawan
        $pendapatanHariIni = Penjualan::whereDate('tanggal_penjualan', $today)
            ->sum('total_penjualan');
        $bulan = request('bulan', now()->month);
        $minggu = request('minggu', ceil(now()->day / 7));
        $tahun = request('tahun', now()->year);

        $query = ProduksiTelur::select(
            DB::raw('DATE(tanggal_produksi) as tanggal'),
            DB::raw('SUM(berat_telur_layak + berat_telur_rusak) as total')
        )
            ->whereYear('tanggal_produksi', $tahun)
            ->whereMonth('tanggal_produksi', $bulan);

        if ($minggu) {
            $query->whereRaw('CEIL(DAY(tanggal_produksi) / 7) = ?', [$minggu]);
        }

        $produksiMingguan = $query
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        $chartProduksiLabels = $produksiMingguan->pluck('tanggal')
            ->map(fn($tgl) => Carbon::parse($tgl)->format('d M'));

        $chartProduksiData = $produksiMingguan->pluck('total');


        /*  CHART KEUANGAN (ADMIN ONLY)*/
        if ($user->isAdmin()) {

            $finance = DB::table('penjualan')
                ->selectRaw('
        CEIL(DAY(tanggal_penjualan)/7) as minggu,
        SUM(total_penjualan) as pemasukan
    ')
                ->whereYear('tanggal_penjualan', $tahun)
                ->whereMonth('tanggal_penjualan', $bulan)
                ->groupBy('minggu')
                ->orderBy('minggu')
                ->get();


            /* PENGELUARAN INVENTORI */
            $pengeluaranInventori = DB::table('inventori_kandang')
                ->selectRaw('
        CEIL(DAY(created_at)/7) as minggu,
        SUM(total_harga) as total
    ')
                ->whereYear('created_at', $tahun)
                ->whereMonth('created_at', $bulan)
                ->where('jenis_transaksi', 'masuk')
                ->groupBy('minggu')
                ->pluck('total', 'minggu');


            /* GAJI KARYAWAN (dibagi rata per minggu) */
            $gajiBulanan = DB::table('gaji_karyawan')
                ->where('tahun', $tahun)
                ->where('bulan', $bulan)
                ->sum('gaji_pokok');

            $gajiMingguan = $gajiBulanan / 4;


            $chartFinanceLabels = [];
            $chartFinancePemasukan = [];
            $chartFinancePengeluaran = [];


            foreach ($finance as $row) {

                $minggu = $row->minggu;

                $chartFinanceLabels[] = 'Minggu ' . $minggu;

                $chartFinancePemasukan[] = $row->pemasukan;

                $pengeluaranInventoriMinggu = $pengeluaranInventori[$minggu] ?? 0;

                $chartFinancePengeluaran[] =
                    $pengeluaranInventoriMinggu + $gajiMingguan;
            }
        } else {
            $chartFinanceLabels = [];
            $chartFinancePemasukan = [];
            $chartFinancePengeluaran = [];
        }


        return view('home', compact(
            'jumlahAyam',
            'produksiHariIni',
            'stokPakan',
            'stokMenipis',

            'pendapatanHariIni',
            'chartProduksiLabels',
            'chartProduksiData',
            'chartFinanceLabels',
            'chartFinancePemasukan',
            'chartFinancePengeluaran'
        ));
    }
}

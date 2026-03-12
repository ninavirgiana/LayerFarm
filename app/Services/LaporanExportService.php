<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanExportService
{
    public function export(int $tahun): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();

        /*
        | SHEET 1 — PRODUKSI (MINGGUAN)
        */
        $sheetProduksi = $spreadsheet->getActiveSheet();
        $sheetProduksi->setTitle('Produksi');

        $sheetProduksi->setCellValue('A1', 'LAPORAN PETERNAKAN AYAM PETELUR PAK HARI');
        $sheetProduksi->setCellValue('A2', 'TAHUN ' . $tahun);

        $sheetProduksi->setCellValue('A4', 'Bulan');
        $sheetProduksi->setCellValue('B4', 'Minggu');
        $sheetProduksi->setCellValue('C4', 'Total Produksi (Kg)');

        $produksi = DB::table('produksi_telur')
            ->selectRaw('
                MONTH(tanggal_produksi) AS bulan,
                CEIL(DAY(tanggal_produksi) / 7) AS minggu,
                SUM(berat_telur_total) AS total
            ')
            ->whereYear('tanggal_produksi', $tahun)
            ->groupBy('bulan', 'minggu')
            ->orderBy('bulan')
            ->orderBy('minggu')
            ->get();

        $row = 5;
        foreach ($produksi as $item) {
            $sheetProduksi->setCellValue(
                "A{$row}",
                \Carbon\Carbon::createFromDate(null, $item->bulan, 1)
                    ->locale('id')
                    ->translatedFormat('F')
            );
            $sheetProduksi->setCellValue("B{$row}", 'Minggu ' . $item->minggu);
            $sheetProduksi->setCellValue("C{$row}", $item->total);
            $row++;
        }

        $totalProduksi = $produksi->sum('total');
        $jumlahMinggu  = $produksi->count();
        $rataProduksi  = $jumlahMinggu > 0 ? $totalProduksi / $jumlahMinggu : 0;

        $sheetProduksi->setCellValue("A{$row}", 'TOTAL PRODUKSI TAHUNAN');
        $sheetProduksi->setCellValue("C{$row}", $totalProduksi);
        $row++;

        $sheetProduksi->setCellValue("A{$row}", 'RATA-RATA PRODUKSI MINGGUAN');
        $sheetProduksi->setCellValue("C{$row}", round($rataProduksi, 1));
        $row++;

        $this->autoSize($sheetProduksi);

        /*
        | SHEET 2 — INVENTORI (MINGGUAN)
        */
        $sheetInventori = $spreadsheet->createSheet();
        $sheetInventori->setTitle('Pakan');

        $sheetInventori->setCellValue('A1', 'LAPORAN PETERNAKAN AYAM PETELUR PAK HARI');
        $sheetInventori->setCellValue('A2', 'TAHUN ' . $tahun);

        $sheetInventori->setCellValue('A4', 'Bulan');
        $sheetInventori->setCellValue('B4', 'Minggu');
        $sheetInventori->setCellValue('C4', 'Pakan Masuk (Kg)');
        $sheetInventori->setCellValue('D4', 'Pakan Keluar (Kg)');

        $inventori = DB::table('inventori_kandang')
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

        $row = 5;
        foreach ($inventori as $item) {
            $sheetInventori->setCellValue(
                "A{$row}",
                \Carbon\Carbon::createFromDate(null, $item->bulan, 1)
                    ->locale('id')
                    ->translatedFormat('F')
            );
            $sheetInventori->setCellValue("B{$row}", 'Minggu ' . $item->minggu);
            $sheetInventori->setCellValue("C{$row}", $item->masuk);
            $sheetInventori->setCellValue("D{$row}", $item->keluar);
            $row++;
        }

        $totalMasuk  = $inventori->sum('masuk');
        $totalKeluar = $inventori->sum('keluar');

        $sheetInventori->setCellValue("A{$row}", 'TOTAL MASUK PAKAN TAHUNAN');
        $sheetInventori->setCellValue("C{$row}", $totalMasuk);
        $row++;

        $sheetInventori->setCellValue("A{$row}", 'TOTAL KELUAR PAKAN TAHUNAN');
        $sheetInventori->setCellValue("D{$row}", $totalKeluar);
        $row++;

        $this->autoSize($sheetInventori);

        /*
        | SHEET 3 — KEUANGAN (BULANAN)
        */
        $sheetKeuangan = $spreadsheet->createSheet();
        $sheetKeuangan->setTitle('Keuangan');

        $sheetKeuangan->setCellValue('A1', 'LAPORAN PETERNAKAN AYAM PETELUR PAK HARI');
        $sheetKeuangan->setCellValue('A2', 'TAHUN ' . $tahun);

        $sheetKeuangan->setCellValue('A4', 'Bulan');
        $sheetKeuangan->setCellValue('B4', 'Pemasukan');
        $sheetKeuangan->setCellValue('C4', 'Pembelian Pakan');
        $sheetKeuangan->setCellValue('D4', 'Gaji');
        $sheetKeuangan->setCellValue('E4', 'Laba / Rugi');

        $pemasukan = DB::table('penjualan')
            ->selectRaw('
        MONTH(tanggal_penjualan) AS bulan,
        SUM(total_penjualan) AS total
    ')
            ->whereYear('tanggal_penjualan', $tahun)
            ->groupBy('bulan')
            ->get()
            ->keyBy('bulan');


        $inventoriMasuk = DB::table('inventori_kandang')
            ->selectRaw('MONTH(created_at) AS bulan, SUM(total_harga) AS total')
            ->whereYear('created_at', $tahun)
            ->where('jenis_transaksi', 'masuk')
            ->groupBy('bulan')
            ->get()
            ->keyBy('bulan');

        $gaji = DB::table('gaji_karyawan')
            ->selectRaw('bulan, SUM(gaji_pokok) AS total')
            ->where('tahun', $tahun)
            ->groupBy('bulan')
            ->get()
            ->keyBy('bulan');

        $row = 5;
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $p = $pemasukan[$bulan]->total ?? 0;
            $i = $inventoriMasuk[$bulan]->total ?? 0;
            $g = $gaji[$bulan]->total ?? 0;
            $laba = $p - ($i + $g);

            $sheetKeuangan->setCellValue(
                "A{$row}",
                \Carbon\Carbon::createFromDate(null, $bulan, 1)
                    ->locale('id')
                    ->translatedFormat('F')
            );
            $sheetKeuangan->setCellValue("B{$row}", $p);
            $sheetKeuangan->setCellValue("C{$row}", $i);
            $sheetKeuangan->setCellValue("D{$row}", $g);
            $sheetKeuangan->setCellValue("E{$row}", $laba);
            $row++;
        }

        $totalPemasukan = $pemasukan->sum('total');
        $totalInventori = $inventoriMasuk->sum('total');
        $totalGaji      = $gaji->sum('total');
        $labaTahunan    = $totalPemasukan - ($totalInventori + $totalGaji);

        $sheetKeuangan->setCellValue("A{$row}", 'LABA BERSIH TAHUNAN');
        $sheetKeuangan->setCellValue("E{$row}", $labaTahunan);

        $this->autoSize($sheetKeuangan);

        /*
        | DOWNLOAD
        */
        $filename = "Laporan-Tahunan-{$tahun}.xlsx";

        return new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    private function autoSize($sheet): void
    {
        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
}

<?php
// TIDAK DIPAKAI
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

// contoh model, sesuaikan
use App\Models\ProduksiTelur;

class LaporanExportController extends Controller
{
    public function export(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        // ambil data (contoh)
        $data = ProduksiTelur::whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // HEADER
        $sheet->setCellValue('A1', 'Tanggal');
        $sheet->setCellValue('B1', 'Kandang');
        $sheet->setCellValue('C1', 'Jumlah Telur');

        $row = 2;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item->tanggal);
            $sheet->setCellValue('B' . $row, $item->kandang->nama ?? '-');
            $sheet->setCellValue('C' . $row, $item->jumlah);
            $row++;
        }

        $filename = "laporan-{$bulan}-{$tahun}.xlsx";

        return new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            "Content-Type" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            "Content-Disposition" => "attachment; filename=\"$filename\"",
        ]);
    }
}

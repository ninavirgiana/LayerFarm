<?php

namespace App\Http\Controllers;

use App\Models\GajiKaryawan;
use App\Models\Karyawan;
use Illuminate\Http\Request;

class GajiController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->perPage ?? 10;

        $query = GajiKaryawan::with('karyawan');

        // SEARCH GLOBAL
        if ($request->search) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                // Cari nama karyawan
                $q->whereHas('karyawan', function ($sub) use ($search) {
                    $sub->where('nama_karyawan', 'like', '%' . $search . '%');
                })

                    // Cari bulan
                    ->orWhere('bulan', 'like', '%' . $search . '%')
                    // Cari tahun
                    ->orWhere('tahun', 'like', '%' . $search . '%')
                    // Cari gaji
                    ->orWhere('gaji_pokok', 'like', '%' . $search . '%')
                    // Cari tanggal bayar
                    ->orWhere('tanggal_bayar', 'like', '%' . $search . '%');
            });
        }


        // FILTER BULAN
        if ($request->bulan) {
            $query->where('bulan', $request->bulan);
        }

        // FILTER TAHUN
        if ($request->tahun) {
            $query->where('tahun', $request->tahun);
        }

        $gaji = $query
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        $karyawan = Karyawan::where('status_karyawan', 'aktif')
            ->orderBy('nama_karyawan')
            ->get();

        return view('gaji', compact('gaji', 'karyawan', 'perPage'));
    }

    public function store(Request $request)
    {
        //  Bersihkan format rupiah dulu sebelum validasi
        $request->merge([
            'gaji_pokok' => preg_replace('/[^0-9]/', '', $request->gaji_pokok)
        ]);

        //  Validasi
        $request->validate(
            [
                'karyawan_id'   => 'required|exists:karyawan,id',
                'bulan'         => 'required|integer|min:1|max:12',
                'tahun'         => 'required|integer|min:2000|max:' . date('Y'),
                'gaji_pokok' => 'required|numeric|min:100000|max:9999999',
                'tanggal_bayar' => 'required|date|before_or_equal:today'
            ],
            [
                'karyawan_id.required' => 'Karyawan wajib dipilih',
                'karyawan_id.exists'   => 'Karyawan tidak valid',

                'bulan.required' => 'Bulan wajib dipilih',
                'bulan.min'      => 'Bulan tidak valid',
                'bulan.max'      => 'Bulan tidak valid',

                'tahun.required' => 'Tahun wajib diisi',
                'tahun.integer'  => 'Tahun harus berupa angka',
                'tahun.max'      => 'Tahun tidak boleh melebihi tahun sekarang',
                'tahun.min'      => 'Tahun tidak valid',

                'gaji_pokok.required' => 'Gaji pokok wajib diisi',
                'gaji_pokok.numeric'  => 'Gaji pokok harus berupa angka',
                'gaji_pokok.max' => 'Gaji pokok maksimal Rp 9.999.999',
                'gaji_pokok.min' => 'Gaji pokok minimal Rp 100.000',

                'tanggal_bayar.required' => 'Tanggal pembayaran wajib diisi',
                'tanggal_bayar.date'     => 'Format tanggal tidak valid',
                'tanggal_bayar.before_or_equal' => 'Tanggal pembayaran tidak boleh melebihi hari ini.',
            ]
        );

        // Cegah gaji dobel
        $cek = GajiKaryawan::where('karyawan_id', $request->karyawan_id)
            ->where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->exists();

        if ($cek) {
            return back()
                ->withErrors([
                    'bulan' => 'Gaji untuk bulan dan tahun ini sudah dicatat'
                ])
                ->withInput();
        }

        // Simpan
        GajiKaryawan::create([
            'karyawan_id'   => $request->karyawan_id,
            'bulan'         => $request->bulan,
            'tahun'         => $request->tahun,
            'gaji_pokok'    => $request->gaji_pokok,
            'tanggal_bayar' => $request->tanggal_bayar,
            'status_bayar'  => 'dibayar',
        ]);

        return redirect()->route('gaji')
            ->with('success', 'Pembayaran gaji berhasil dicatat');
    }
}

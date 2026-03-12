<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;


class KeuanganController extends Controller
{

    public function index(Request $request)
    {
        // $query = Penjualan::orderBy('tanggal_penjualan', 'desc');
        $query = Penjualan::orderByDesc('tanggal_penjualan')
            ->orderByDesc('created_at');
        /** @var \App\Models\User $user */

        $user = Auth::user();

        if ($user->isKaryawan()) {
            $query->where('user_id', $user->id);
        }

        //  FILTER PRODUK AKTIF
        if ($request->produk) {
            $query->where('jenis_produk', $request->produk);
        }

        //  SEARCH GLOBAL
        if ($request->search) {

            $s = $request->search;

            $query->where(function ($q) use ($s) {

                // cari jenis produk (telur / afkir)
                $q->where('jenis_produk', 'like', "%$s%")
                    ->orWhere('berat_terjual', 'like', "%$s%")
                    ->orWhere('harga_satuan', 'like', "%$s%")
                    ->orWhere('total_penjualan', 'like', "%$s%")
                    ->orWhere('tanggal_penjualan', 'like', "%$s%")
                    ->orWhereRaw("DATE_FORMAT(tanggal_penjualan, '%Y-%m-%d') like ?", ["%$s%"]);
            });
        }


        if ($request->bulan) {
            $query->whereMonth('tanggal_penjualan', $request->bulan);
        }

        if ($request->tahun) {
            $query->whereYear('tanggal_penjualan', $request->tahun);
        }

        return view('produksi-keuangan', [
            'penjualans' => $query->paginate($request->perPage ?? 10)->withQueryString(),
            'produks' => [
                'telur' => 'Telur Ayam',
                'afkir' => 'Ayam Afkir',
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'tanggal' => 'required|date|before_or_equal:today',
                'jenis_produk' => 'required|in:telur,afkir',
                'jumlah' => 'required|numeric|min:0.1|max:9999',
                'harga_satuan' => 'required|numeric|min:1|max:99999999',
            ],
            [
                'tanggal.required' => 'Tanggal penjualan wajib diisi.',
                'tanggal.date' => 'Tanggal penjualan tidak valid.',
                'jenis_produk.required' => 'Jenis produk wajib diisi.',
                'jumlah.required' => 'Jumlah penjualan wajib diisi.',
                'jumlah.numeric' => 'Jumlah harus berupa angka.',
                'jumlah.min' => 'Jumlah minimal 0,1 Kg.',
                'harga_satuan.required' => 'Harga satuan wajib diisi.',
                'harga_satuan.numeric' => 'Harga satuan harus berupa angka.',
                'harga_satuan.min' => 'Harga satuan minimal Rp 1.',
            ]
        );


        Penjualan::create([
            'user_id' => Auth::id(),
            'jenis_produk' => $request->jenis_produk,
            'berat_terjual' => $request->jumlah,
            'harga_satuan' => $request->harga_satuan,
            'total_penjualan' => $request->jumlah * $request->harga_satuan,
            'tanggal_penjualan' => $request->tanggal,
        ]);

        return back()->with('success', 'Penjualan berhasil ditambahkan.');
    }
    public function update(Request $request, $id)
    {
        $penjualan = Penjualan::findOrFail($id);

        $validated = $request->validate(
            [
                'tanggal' => 'required|date|before_or_equal:today',
                'jenis_produk' => 'required|in:telur,afkir',
                'jumlah' => 'required|numeric|min:0.1',
                'harga_satuan' => 'required|numeric|min:1',
            ],
            [
                'tanggal.required' => 'Tanggal penjualan wajib diisi.',
                'tanggal.date' => 'Tanggal penjualan tidak valid.',
                'jenis_produk.required' => 'Jenis produk wajib diisi.',
                'jumlah.required' => 'Jumlah penjualan wajib diisi.',
                'jumlah.numeric' => 'Jumlah harus berupa angka.',
                'jumlah.min' => 'Jumlah minimal 0,1 Kg.',
                'harga_satuan.required' => 'Harga satuan wajib diisi.',
                'harga_satuan.numeric' => 'Harga satuan harus berupa angka.',
                'harga_satuan.min' => 'Harga satuan minimal Rp 1.',
            ]
        );

        $penjualan->update([
            'berat_terjual'     => $validated['jumlah'],
            'jenis_produk'     => $validated['jenis_produk'],
            'harga_satuan'      => $validated['harga_satuan'],
            'total_penjualan'   => $validated['jumlah'] * $validated['harga_satuan'],
            'tanggal_penjualan' => $validated['tanggal'],
        ]);

        return redirect()->back()->with('success', 'Penjualan berhasil diupdate.');
    }


    public function destroy($id)
    {
        /** @var \App\Models\User $user */

        $user = Auth::user();

        if ($user->isKaryawan()) {
            abort(403);
        }

        Penjualan::findOrFail($id)->delete();

        return back()->with('success', 'Penjualan berhasil dihapus');
    }
}

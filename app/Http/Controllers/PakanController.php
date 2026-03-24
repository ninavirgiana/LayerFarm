<?php

namespace App\Http\Controllers;

use App\Models\InventoriKandang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PakanController extends Controller
{

    public function index(Request $request)
    {
        $query = InventoriKandang::query();

        /** @var User $user */
        $user = Auth::user();

        // FILTER ROLE
        if ($user->isKaryawan()) {
            $query->where('user_id', $user->id);
        }

        /*  SEARCH  */
        if ($request->search) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama_barang', 'like', "%$s%")
                    ->orWhere('jenis_transaksi', 'like', "%$s%")
                    ->orWhereRaw("CAST(jumlah AS CHAR) LIKE ?", ["%$s%"]);
            });
        }

        /*  FILTER  */
        if ($request->jenis) {
            $query->where('jenis_transaksi', $request->jenis);
        }

        if ($request->bulan) {
            $query->whereMonth('tanggal', $request->bulan);
        }

        if ($request->tahun) {
            $query->whereYear('tanggal', $request->tahun);
        }

        $perPage = $request->perPage ?? 10;

        //  DATA TABEL
        $data = $query
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        //  HITUNG STOK (GLOBAL)
        $stok = InventoriKandang::selectRaw("
        nama_barang,
        SUM(CASE WHEN jenis_transaksi = 'masuk' THEN jumlah ELSE 0 END)
        -
        SUM(CASE WHEN jenis_transaksi = 'keluar' THEN jumlah ELSE 0 END)
        AS stok
    ")
            ->groupBy('nama_barang')
            ->get();

        //  BATAS MINIMUM
        $batasMinimum = [
            'Jagung'      => 5,
            'Katul'       => 5,
            'Konsentrat'  => 5,
            'Pakan Layer' => 5,
            'Pakan Starter' => 5,
        ];

        $stokMenipis = $stok->filter(function ($item) use ($batasMinimum) {
            $min = $batasMinimum[$item->nama_barang] ?? 5;
            return $item->stok <= $min;
        });

        return view('inventori-pakan', compact('data', 'stokMenipis'));
    }

    public function store(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $request->merge([
            'harga_satuan' => $request->harga_satuan
                ? str_replace('.', '', $request->harga_satuan)
                : null
        ]);

        // karyawan TIDAK BOLEH input barang masuk
        if ($user->isKaryawan() && $request->jenis_transaksi === 'masuk') {
            abort(403, 'Karyawan tidak diizinkan mencatat barang masuk.');
        }
        $namaBarang = ucfirst(strtolower(trim($request->nama_barang)));

        $request->validate([
            'tanggal' => 'required|date|before_or_equal:today',
            'nama_barang'     => 'required|string|max:100',
            'jenis_transaksi' => 'required|in:masuk,keluar',
            'jumlah'          => 'required|numeric|min:1|max:999999',
            // 'satuan'          => 'required|string|max:50|in:karung',
            'harga_satuan'    => $request->jenis_transaksi === 'masuk'
                ? 'required|numeric|min:1|max:999999999'
                : 'nullable',
        ], [
            'tanggal.required' => 'Tanggal wajib diisi.',
            'tanggal.date' => 'Format tanggal tidak valid.',

            'nama_barang.required'     => 'Nama barang wajib diisi.',
            'nama_barang.string'       => 'Nama barang harus berupa teks.',
            'nama_barang.max'          => 'Nama barang maksimal 100 karakter.',

            'jenis_transaksi.required' => 'Jenis transaksi wajib dipilih.',
            'jenis_transaksi.in'       => 'Jenis transaksi tidak valid.',

            'jumlah.required'          => 'Jumlah wajib diisi.',
            'jumlah.numeric'           => 'Jumlah harus berupa angka.',
            'jumlah.min'               => 'Jumlah minimal 1.',
            'jumlah.max'               => 'Jumlah terlalu besar.',

            // 'satuan.required'          => 'Satuan wajib diisi.',
            // 'satuan.string'            => 'Satuan harus berupa teks.',
            // 'satuan.max'               => 'Satuan maksimal 50 karakter.',

            'harga_satuan.required'    => 'Harga satuan wajib diisi untuk transaksi masuk.',
            'harga_satuan.numeric'     => 'Harga satuan harus berupa angka.',
            'harga_satuan.min'         => 'Harga satuan minimal 1.',
            'harga_satuan.max'         => 'Harga satuan terlalu besar.',
        ]);


        $total = null;

        if ($request->jenis_transaksi === 'masuk') {
            $total = $request->jumlah * $request->harga_satuan;
        }
        //  CEK STOK JIKA BARANG KELUAR
        if ($request->jenis_transaksi === 'keluar') {

            $stokSekarang = InventoriKandang::where('nama_barang', $namaBarang)
                ->selectRaw("
            SUM(CASE WHEN jenis_transaksi = 'masuk' THEN jumlah ELSE 0 END)
            -
            SUM(CASE WHEN jenis_transaksi = 'keluar' THEN jumlah ELSE 0 END)
            AS stok
        ")
                ->value('stok') ?? 0;

            if ($request->jumlah > $stokSekarang) {
                return back()->withErrors([
                    'jumlah' => 'Stok ' . $namaBarang . ' tidak mencukupi. Sisa stok: ' . $stokSekarang
                ])->withInput();
            }
        }

        InventoriKandang::create([
            'user_id'         => $user->id,
            'tanggal' => $request->tanggal,
            'nama_barang'     => $namaBarang,
            'jenis_transaksi' => $request->jenis_transaksi,
            'jumlah'          => $request->jumlah,
            'satuan' => 'karung',
            'harga_satuan'    => $request->jenis_transaksi === 'masuk' ? $request->harga_satuan : null,
            'total_harga'     => $total,
        ]);

        return redirect()->route('pakan')->with('success', 'Transaksi berhasil disimpan');
    }



    public function update(Request $request, $id)
    {
        /** @var User $user */
        $user = Auth::user();
        $request->merge([
            'harga_satuan' => $request->harga_satuan
                ? str_replace('.', '', $request->harga_satuan)
                : null
        ]);
        if ($user->isKaryawan()) {
            abort(403, 'Anda tidak memiliki izin mengedit data.');
        }

        $namaBarang = ucfirst(strtolower(trim($request->nama_barang)));

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'tanggal' => 'required|date|before_or_equal:today',
            'nama_barang' => 'required|string|max:100',
            'jenis_transaksi' => 'required|in:masuk,keluar',
            'jumlah' => 'required|numeric|min:1|max:999999',
            'harga_satuan' => $request->jenis_transaksi === 'masuk'
                ? 'required|numeric|min:1|max:999999999'
                : 'nullable',
        ], [
            'tanggal.required' => 'Tanggal wajib diisi.',
            'tanggal.date' => 'Format tanggal tidak valid.',

            'nama_barang.required' => 'Nama barang wajib diisi.',
            'nama_barang.string' => 'Nama barang harus berupa teks.',
            'nama_barang.max' => 'Nama barang maksimal 100 karakter.',

            'jenis_transaksi.required' => 'Jenis transaksi wajib dipilih.',
            'jenis_transaksi.in' => 'Jenis transaksi tidak valid.',

            'jumlah.required' => 'Jumlah wajib diisi.',
            'jumlah.numeric' => 'Jumlah harus berupa angka.',
            'jumlah.min' => 'Jumlah minimal 1.',
            'jumlah.max' => 'Jumlah terlalu besar.',

            'harga_satuan.required' => 'Harga satuan wajib diisi untuk transaksi masuk.',
            'harga_satuan.numeric' => 'Harga satuan harus berupa angka.',
            'harga_satuan.min' => 'Harga satuan minimal 1.',
            'harga_satuan.max' => 'Harga satuan terlalu besar.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator, 'edit')
                ->withInput()
                ->with('edit_id', $id);
        }

        try {

            DB::transaction(function () use ($request, $id, $namaBarang) {

                $item = InventoriKandang::findOrFail($id);

                $total = null;

                if ($request->jenis_transaksi === 'masuk') {
                    $total = $request->jumlah * $request->harga_satuan;
                }

                // CEK STOK JIKA KELUAR
                if ($request->jenis_transaksi === 'keluar') {

                    $stokSekarang = InventoriKandang::where('nama_barang', $namaBarang)
                        ->where('id', '!=', $id)
                        ->selectRaw("
                    SUM(CASE WHEN jenis_transaksi = 'masuk' THEN jumlah ELSE 0 END)
                    -
                    SUM(CASE WHEN jenis_transaksi = 'keluar' THEN jumlah ELSE 0 END)
                    AS stok
                ")
                        ->value('stok') ?? 0;

                    if ($request->jumlah > $stokSekarang) {
                        throw new \Exception('Stok tidak mencukupi. Sisa stok: ' . $stokSekarang);
                    }
                }

                $item->update([
                    'tanggal' => $request->tanggal,
                    'nama_barang' => $namaBarang,
                    'jenis_transaksi' => $request->jenis_transaksi,
                    'jumlah' => $request->jumlah,
                    'satuan' => 'karung',
                    'harga_satuan' => $request->jenis_transaksi === 'masuk'
                        ? $request->harga_satuan
                        : null,
                    'total_harga' => $total,
                ]);
            });
        } catch (\Exception $e) {

            return back()
                ->withErrors([
                    'jumlah' => $e->getMessage()
                ], 'edit')
                ->withInput()
                ->with('edit_id', $id);
        }

        return redirect()->route('pakan')->with('success', 'Transaksi berhasil diupdate');
    }



    public function destroy($id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // hanya admin boleh hapus
        if ($user->isKaryawan()) {
            abort(403, 'Anda tidak memiliki izin menghapus data.');
        }

        $item = InventoriKandang::findOrFail($id);

        // Cek jika yang dihapus adalah transaksi masuk
        if ($item->jenis_transaksi === 'masuk') {

            $stokSekarang = InventoriKandang::where('nama_barang', $item->nama_barang)
                ->selectRaw("
                SUM(CASE WHEN jenis_transaksi = 'masuk' THEN jumlah ELSE 0 END)
                -
                SUM(CASE WHEN jenis_transaksi = 'keluar' THEN jumlah ELSE 0 END)
                AS stok
            ")
                ->value('stok') ?? 0;

            // jika setelah dihapus stok jadi minus
            if (($stokSekarang - $item->jumlah) < 0) {
                return redirect()
                    ->route('pakan')
                    ->withErrors([
                        'delete' => 'Transaksi tidak bisa dihapus karena akan menyebabkan stok menjadi minus.'
                    ]);
            }
        }

        $item->delete();

        return redirect()
            ->route('pakan')
            ->with('success', 'Transaksi berhasil dihapus');
    }
}

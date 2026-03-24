<?php

namespace App\Http\Controllers;

use App\Models\MutasiAyam;
use App\Models\Kandang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AyamController extends Controller
{
    public function index(Request $request)
    {
        $query = MutasiAyam::with('kandang');

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->isKaryawan()) {
            $query->where('user_id', $user->id);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('kandang', function ($k) use ($request) {
                    $k->where('nama_kandang', 'like', '%' . $request->search . '%');
                })->orWhere('keterangan', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->kandang) $query->where('kandang_id', $request->kandang);
        if ($request->bulan) $query->whereMonth('tanggal', $request->bulan);
        if ($request->tahun) $query->whereYear('tanggal', $request->tahun);

        $perPage = $request->perPage ?? 10;
        $data = $query
            ->orderBy('tanggal', 'desc')     // tanggal terbaru dulu
            ->orderBy('id', 'desc')          // jika tanggal sama, yang terakhir ditambah tetap di atas
            ->paginate($perPage)
            ->withQueryString();

        // jumlah ayam terakhir per kandang
        $kandang = Kandang::query()
            ->select('id', 'nama_kandang')
            ->selectSub(function ($q) {
                $q->from('mutasi_ayam')
                    ->whereColumn('mutasi_ayam.kandang_id', 'kandang.id')
                    ->selectRaw("
                COALESCE(SUM(
                    CASE
                        WHEN jenis_mutasi = 'masuk' THEN jumlah
                        WHEN jenis_mutasi IN ('mati','afkir','pindah') THEN -jumlah
                        ELSE 0
                    END
                ), 0)
            ");
            }, 'jumlah_ayam_terakhir')
            ->get();

        return view('inventori-ayam', compact('data', 'kandang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_mutasi' => 'required|in:masuk,mati,afkir,pindah',
            'jumlah' => 'required|integer|min:1|max:9999',
            'tanggal' => 'required|date|before_or_equal:today',
            'keterangan' => 'required_if:jenis_mutasi,masuk,mati,afkir|string|max:255',
        ], [
            'jenis_mutasi.required' => 'Jenis mutasi wajib dipilih.',
            'jenis_mutasi.in'       => 'Jenis mutasi tidak valid.',

            'jumlah.required' => 'Jumlah ayam wajib diisi.',
            'jumlah.integer'  => 'Jumlah ayam harus berupa angka.',
            'jumlah.min'      => 'Jumlah ayam minimal 1 ekor.',

            'tanggal.required' => 'Tanggal wajib diisi.',
            'tanggal.date'     => 'Format tanggal tidak valid.',
            'tanggal.before_or_equal' => 'Tanggal tidak boleh melebihi hari ini.',

            'keterangan.required_if' => 'Keterangan wajib diisi.',
            'keterangan.string'   => 'Keterangan harus berupa teks.',
            'keterangan.max'      => 'Keterangan maksimal 255 karakter.',
        ]);

        DB::transaction(function () use ($request) {

            //  PINDAH
            if ($request->jenis_mutasi === 'pindah') {

                $request->validate([
                    'kandang_asal_id'   => 'required|exists:kandang,id|different:kandang_tujuan_id',
                    'kandang_tujuan_id' => 'required|exists:kandang,id',
                ], [
                    'kandang_asal_id.required'   => 'Kandang asal wajib dipilih.',
                    'kandang_asal_id.different'  => 'Kandang asal dan tujuan tidak boleh sama.',
                    'kandang_tujuan_id.required' => 'Kandang tujuan wajib dipilih.',
                ]);

                $kandangAsal = Kandang::findOrFail($request->kandang_asal_id);
                $kandangTujuan = Kandang::findOrFail($request->kandang_tujuan_id);

                $stokAsal = $kandangAsal->mutasiAyam()
                    ->selectRaw("
COALESCE(SUM(
CASE
WHEN jenis_mutasi='masuk' THEN jumlah
WHEN jenis_mutasi IN ('mati','afkir','pindah') THEN -jumlah
ELSE 0 END
),0) as stok
")->value('stok');

                if ($stokAsal <= 0) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'jumlah' => 'Stok kandang asal sudah habis.'
                    ]);
                }

                if ($request->jumlah > $stokAsal) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'jumlah' => "Stok kandang asal hanya $stokAsal ekor."
                    ]);
                }

                MutasiAyam::create([
                    'kandang_id'   => $request->kandang_asal_id,
                    'user_id'      => Auth::id(),
                    'jenis_mutasi' => 'pindah',
                    'jumlah'       => $request->jumlah,
                    'tanggal'      => $request->tanggal,
                    'keterangan'   => 'Pindah ke ' . $kandangTujuan->nama_kandang,
                ]);

                MutasiAyam::create([
                    'kandang_id'   => $request->kandang_tujuan_id,
                    'user_id'      => Auth::id(),
                    'jenis_mutasi' => 'masuk',
                    'jumlah'       => $request->jumlah,
                    'tanggal'      => $request->tanggal,
                    'keterangan'   => 'Pindahan dari ' . $kandangAsal->nama_kandang,
                ]);
            }

            //  MUTASI BIASA
            else {

                $request->validate([
                    'kandang_id' => 'required|exists:kandang,id',
                ], [
                    'kandang_id.required' => 'Nama kandang wajib dipilih.',
                    'kandang_id.exists'   => 'Kandang tidak ditemukan.',
                ]);

                $kandang = Kandang::findOrFail($request->kandang_id);

                $stok = $kandang->mutasiAyam()->selectRaw("
COALESCE(SUM(
CASE
WHEN jenis_mutasi='masuk' THEN jumlah
WHEN jenis_mutasi IN ('mati','afkir','pindah') THEN -jumlah
ELSE 0 END
),0) as stok
")->value('stok');

                // Kalau mutasi keluar/mati/afkir → cek stok
                if (in_array($request->jenis_mutasi, ['mati', 'afkir'])) {

                    if ($stok <= 0) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'jumlah' => 'Stok ayam di kandang ini sudah habis.'
                        ]);
                    }

                    if ($request->jumlah > $stok) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'jumlah' => "Stok hanya tersisa $stok ekor."
                        ]);
                    }
                }

                MutasiAyam::create([
                    'kandang_id'   => $request->kandang_id,
                    'user_id'      => Auth::id(),
                    'jenis_mutasi' => $request->jenis_mutasi,
                    'jumlah'       => $request->jumlah,
                    'tanggal'      => $request->tanggal,
                    'keterangan'   => $request->keterangan,
                ]);
            }
        });

        return redirect()->route('ayam')
            ->with('success', 'Data mutasi berhasil disimpan.');
    }

    public function update(Request $request, $id)
    {
        $mutasi = MutasiAyam::findOrFail($id);
        //  CEGAH EDIT DATA PINDAH
        if (
            $mutasi->jenis_mutasi === 'pindah' ||
            str_contains($mutasi->keterangan, 'Pindahan dari')
        ) {
            abort(403, 'Data hasil pindahan tidak boleh diedit.');
        }
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->isKaryawan() && $mutasi->user_id !== $user->id) {
            abort(403, 'Tidak diizinkan mengedit data ini.');
        }
        //  VALIDASI MANUAL
        $validator = Validator::make($request->all(), [
            'kandang_id'   => 'required|exists:kandang,id',
            'jenis_mutasi' => 'required|in:masuk,mati,afkir',
            'jumlah'       => 'required|integer|min:1|max:9999',
            'tanggal'      => 'required|date|before_or_equal:today',
            'keterangan' => 'required_if:jenis_mutasi,masuk,mati,afkir|string|max:255',

        ], [
            'kandang_id.required'   => 'Nama kandang wajib dipilih.',
            'kandang_id.exists'     => 'Kandang tidak ditemukan.',
            'jenis_mutasi.required' => 'Jenis mutasi wajib dipilih.',
            'jenis_mutasi.in'       => 'Jenis mutasi tidak valid.',
            'jumlah.required'       => 'Jumlah ayam wajib diisi.',
            'jumlah.integer'        => 'Jumlah ayam harus berupa angka.',
            'jumlah.min'            => 'Jumlah ayam minimal 1 ekor.',
            'tanggal.required'      => 'Tanggal wajib diisi.',
            'tanggal.date'          => 'Format tanggal tidak valid.',
            'tanggal.before_or_equal' => 'Tanggal tidak boleh melebihi hari ini.',
            'keterangan.required_if' => 'Keterangan wajib diisi.',
            'keterangan.string'   => 'Keterangan harus berupa teks.',
            'keterangan.max'      => 'Keterangan maksimal 255 karakter.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('edit_id', $mutasi->id);
        }

        // HITUNG STOK
        $kandang = Kandang::findOrFail($request->kandang_id);

        $stok = $kandang->mutasiAyam()
            ->where('id', '!=', $mutasi->id)
            ->selectRaw("
COALESCE(SUM(
CASE
WHEN jenis_mutasi='masuk' THEN jumlah
WHEN jenis_mutasi IN ('mati','afkir','pindah') THEN -jumlah
ELSE 0 END
),0) as stok
")->value('stok');

        // kembalikan stok lama jika mutasi lama adalah pengurang
        if (in_array($mutasi->jenis_mutasi, ['mati', 'afkir', 'pindah'])) {
            $stok += $mutasi->jumlah;
        }
        //  CEK STOK
        if (in_array($request->jenis_mutasi, ['mati', 'afkir'])) {

            if ($stok <= 0) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'jumlah' => 'Stok ayam di kandang ini sudah habis.'
                ]);
            }

            if ($request->jumlah > $stok) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'jumlah' => "Stok hanya tersisa $stok ekor."
                ]);
            }
        }

        //  UPDATE DATA
        $mutasi->update([
            'kandang_id'   => $request->kandang_id,
            'jenis_mutasi' => $request->jenis_mutasi,
            'jumlah'       => $request->jumlah,
            'tanggal'      => $request->tanggal,
            'keterangan'   => $request->keterangan,
        ]);

        return redirect()->route('ayam')
            ->with('success', 'Data berhasil diperbarui.');
    }
    public function destroy($id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->isKaryawan()) {
            abort(403, 'Karyawan tidak boleh menghapus data.');
        }

        $mutasi = MutasiAyam::findOrFail($id);

        if (
            $mutasi->jenis_mutasi === 'pindah' ||
            str_contains($mutasi->keterangan, 'Pindahan dari')
        ) {
            abort(403, 'Data hasil pindahan tidak boleh dihapus.');
        }

        $mutasi->delete();
        return redirect()->route('ayam')
            ->with('success', 'Data berhasil dihapus.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\ProduksiTelur;
use App\Models\Kandang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class HasilController extends Controller
{

    public function index(Request $request)
    {

        $query = ProduksiTelur::with('kandang')
            ->whereHas(
                'kandang',
                fn($q) =>
                $q->where('jenis_kandang', 'Produksi')
            );

        /** @var User $user */
        $user = Auth::user();

        //  FILTER ROLE
        if ($user->isKaryawan()) {
            $query->where('user_id', $user->id);
        }

        //  SEARCH GLOBAL
        if ($request->search) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->whereHas('kandang', function ($sub) use ($search) {
                    $sub->where('nama_kandang', 'like', '%' . $search . '%');
                })
                    ->orWhere('shift', 'like', '%' . $search . '%')
                    ->orWhere('tanggal_produksi', 'like', '%' . $search . '%')
                    ->orWhere('berat_telur_layak', 'like', '%' . $search . '%')
                    ->orWhere('berat_telur_rusak', 'like', '%' . $search . '%')
                    ->orWhereRaw("CAST(berat_telur_total AS CHAR) LIKE ?", ["%$search%"]);
            });
        }

        if ($request->kandang) {
            $query->where('kandang_id', $request->kandang);
        }

        if ($request->bulan) {
            $query->whereMonth('tanggal_produksi', $request->bulan);
        }

        if ($request->tahun) {
            $query->whereYear('tanggal_produksi', $request->tahun);
        }
        $perPage = $request->perPage ?? 10;


        $data = $query
            ->orderByDesc('tanggal_produksi')
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();


        $kandang = Kandang::where('jenis_kandang', 'Produksi')->get();
        $tahunList = ProduksiTelur::selectRaw('YEAR(tanggal_produksi) as tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        return view('produksi-hasil', compact('data', 'kandang', 'tahunList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kandang_id' => 'required|exists:kandang,id',
            'shift' => 'required|in:pagi,sore',
            'berat_telur_layak' => 'required|regex:/^\d{1,6}(\.\d{1,2})?$/',
            'berat_telur_rusak' => 'required|regex:/^\d{1,6}(\.\d{1,2})?$/',
            'tanggal_produksi' => [
                'required',
                'date_format:Y-m-d',
                'after_or_equal:2000-01-01',
                'before_or_equal:today',
            ],
        ], [
            'kandang_id.required' => 'Kandang wajib dipilih.',
            'shift.required' => 'Shift wajib dipilih.',
            'berat_telur_layak.required' => 'Berat telur layak wajib diisi.',
            'berat_telur_layak.regex' => 'Berat telur layak maksimal 6 digit dan hanya boleh angka desimal.',
            'berat_telur_rusak.required' => 'Berat telur rusak wajib diisi.',
            'berat_telur_rusak.regex' => 'Berat telur rusak maksimal 6 digit dan hanya boleh angka desimal.',
            'tanggal_produksi.required' => 'Tanggal produksi wajib diisi.',
            'tanggal_produksi.date_format' => 'Format tanggal harus YYYY-MM-DD.',
            'tanggal_produksi.after_or_equal' => 'Tanggal terlalu lama dan tidak valid.',
            'tanggal_produksi.before_or_equal' => 'Tanggal tidak boleh melebihi hari ini.',
        ]);


        $total = $request->berat_telur_layak + $request->berat_telur_rusak;

        ProduksiTelur::create([
            'kandang_id' => $request->kandang_id,
            'user_id' => Auth::id(),
            'shift' => $request->shift,
            'berat_telur_layak' => $request->berat_telur_layak,
            'berat_telur_rusak' => $request->berat_telur_rusak,
            'berat_telur_total' => $total,
            'tanggal_produksi' => $request->tanggal_produksi,
        ]);

        return redirect()->route('hasil')
            ->with('success', 'Data produksi berhasil ditambahkan.');
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'kandang_id' => 'required|exists:kandang,id',
            'shift' => 'required|in:pagi,sore',
            'berat_telur_layak' => 'required|regex:/^\d{1,6}(\.\d{1,2})?$/',
            'berat_telur_rusak' => 'required|regex:/^\d{1,6}(\.\d{1,2})?$/',
            'tanggal_produksi' => [
                'required',
                'date_format:Y-m-d',
                'after_or_equal:2000-01-01',
                'before_or_equal:today',
            ],
        ], [
            'kandang_id.required' => 'Kandang wajib dipilih.',
            'kandang_id.exists' => 'Kandang tidak ditemukan.',
            'shift.required' => 'Shift wajib dipilih.',
            'shift.in' => 'Shift harus pagi atau sore.',
            'berat_telur_layak.required' => 'Berat telur layak wajib diisi.',
            'berat_telur_layak.regex' => 'Berat telur layak maksimal 6 digit dan hanya boleh angka desimal.',
            'berat_telur_rusak.required' => 'Berat telur rusak wajib diisi.',
            'berat_telur_rusak.regex' => 'Berat telur rusak maksimal 6 digit dan hanya boleh angka desimal.',
            'tanggal_produksi.required' => 'Tanggal produksi wajib diisi.',
            'tanggal_produksi.date' => 'Tanggal produksi tidak valid.',
            'tanggal_produksi.date_format' => 'Format tanggal harus YYYY-MM-DD.',
            'tanggal_produksi.after_or_equal' => 'Tanggal terlalu lama dan tidak valid.',
            'tanggal_produksi.before_or_equal' => 'Tanggal tidak boleh melebihi hari ini.',
        ]);


        $total = $request->berat_telur_layak + $request->berat_telur_rusak;

        ProduksiTelur::findOrFail($id)->update([
            'kandang_id' => $request->kandang_id,
            'shift' => $request->shift,
            'berat_telur_layak' => $request->berat_telur_layak,
            'berat_telur_rusak' => $request->berat_telur_rusak,
            'berat_telur_total' => $total,
            'tanggal_produksi' => $request->tanggal_produksi,
        ]);

        return redirect()->route('hasil')
            ->with('success', 'Data produksi berhasil diperbarui.');
    }


    public function destroy($id)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->isKaryawan()) {
            abort(403, 'Anda tidak memiliki izin menghapus data.');
        }

        ProduksiTelur::findOrFail($id)->delete();

        return back()->with('success', 'Data produksi berhasil dihapus.');
    }
}

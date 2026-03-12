<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        $query = Karyawan::with('user');

        //  SEARCH NAMA / HP / ALAMAT
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_karyawan', 'like', '%' . $request->search . '%')
                    ->orWhere('no_hp', 'like', '%' . $request->search . '%')
                    ->orWhere('alamat', 'like', '%' . $request->search . '%'); // ✅ TAMBAHAN
            });
        }

        //  FILTER STATUS
        if ($request->status) {
            $query->where('status_karyawan', $request->status);
        }

        $perPage = in_array($request->perPage, [10, 25, 50]) ? $request->perPage : 10;
        
        $data = $query
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return view('karyawan', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'nama_karyawan'    => 'required|string|min:3|max:100',
                'no_hp'            => ['required', 'regex:/^08[0-9]{8,11}$/'],
                'alamat'           => 'required|string|max:255',
                'status_karyawan'  => 'required|in:aktif,nonaktif',
            ],
            [
                'nama_karyawan.required' => 'Nama karyawan wajib diisi',
                'nama_karyawan.string'   => 'Nama karyawan harus berupa teks',
                'nama_karyawan.min'      => 'Nama karyawan minimal 3 karakter',
                'nama_karyawan.max'      => 'Nama karyawan maksimal 100 karakter',

                'no_hp.required' => 'Nomor HP wajib diisi',
                'no_hp.regex'    => 'Nomor HP harus 10–13 digit, diawali 08, dan hanya angka.',

                'alamat.required' => 'Alamat wajib diisi',
                'alamat.string'   => 'Alamat harus berupa teks',
                'alamat.max'      => 'Alamat maksimal 255 karakter',

                'status_karyawan.required' => 'Status karyawan wajib dipilih',
                'status_karyawan.in'       => 'Status karyawan tidak valid',
            ]
        );

        $userId = null;

        if ($request->has_login) {
            $request->validate(
                [
                    'email'    => 'required|email|unique:users,email',
                    'password' => 'required|string|min:6|max:20',
                ],
                [
                    'email.required' => 'Email wajib diisi',
                    'email.email'    => 'Format email tidak valid',
                    'email.unique'   => 'Email sudah terdaftar',

                    'password.required' => 'Password wajib diisi',
                    'password.string'   => 'Password harus berupa teks',
                    'password.min'      => 'Password minimal 6 karakter',
                    'password.max'      => 'Password maksimal 20 karakter',
                ]
            );

            $user = User::create([
                'name'      => $request->nama_karyawan,
                'email'     => $request->email,
                'no_hp'     => $request->no_hp,
                'alamat'    => $request->alamat,
                'password'  => Hash::make($request->password),
                'is_active' => true,
            ]);

            $userId = $user->id;
        }

        Karyawan::create([
            'user_id'         => $userId,
            'nama_karyawan'   => $request->nama_karyawan,
            'no_hp'           => $request->no_hp,
            'alamat'          => $request->alamat,
            'status_karyawan' => $request->status_karyawan,
        ]);

        return redirect()->route('karyawan')
            ->with('success', 'Karyawan berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate(
            [
                'nama_karyawan' => 'required|string|min:3|max:100',
                'no_hp'         => ['required', 'regex:/^08[0-9]{8,11}$/'],
                'alamat'        => 'required|string|max:255',
            ],
            [
                'nama_karyawan.required' => 'Nama karyawan wajib diisi',
                'nama_karyawan.string'   => 'Nama karyawan harus berupa teks',
                'nama_karyawan.min'      => 'Nama karyawan minimal 3 karakter',
                'nama_karyawan.max'      => 'Nama karyawan maksimal 100 karakter',

                'no_hp.required' => 'Nomor HP wajib diisi',
                'no_hp.regex'    => 'Nomor HP harus 10–13 digit, diawali 08, dan hanya angka.',

                'alamat.required' => 'Alamat wajib diisi',
                'alamat.string'   => 'Alamat harus berupa teks',
                'alamat.max'      => 'Alamat maksimal 255 karakter',
            ]
        );

        $karyawan = Karyawan::findOrFail($id);

        $karyawan->update([
            'nama_karyawan' => $request->nama_karyawan,
            'no_hp'         => $request->no_hp,
            'alamat'        => $request->alamat,
        ]);

        return redirect()->route('karyawan')
            ->with('success', 'Data karyawan berhasil diperbarui');
    }


    public function nonaktifkan($id)
    {
        $karyawan = Karyawan::with('user')->findOrFail($id);

        $karyawan->update([
            'status_karyawan' => 'nonaktif'
        ]);

        // OPTIONAL: kalau punya akun, sekalian matikan akses login
        if ($karyawan->user) {
            $karyawan->user->update([
                'is_active' => false
            ]);
        }

        return redirect()->route('karyawan')
            ->with('success', 'Karyawan berhasil dinonaktifkan');
    }

    public function aktifkan($id)
    {
        $karyawan = Karyawan::with('user')->findOrFail($id);

        $karyawan->update([
            'status_karyawan' => 'aktif'
        ]);

        // OPTIONAL
        if ($karyawan->user) {
            $karyawan->user->update([
                'is_active' => true
            ]);
        }

        return redirect()->route('karyawan')
            ->with('success', 'Karyawan berhasil diaktifkan kembali');
    }
}

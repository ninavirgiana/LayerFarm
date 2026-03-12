<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Karyawan;

class ProfilController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $karyawan = $user->isKaryawan() ? $user->karyawan : null;

        return view('profil', compact('user', 'karyawan'));
    }


    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $request->session()->flash('active_tab', 'editProfil');

        $rules = [
            'name'  => ['required', 'string', 'min:3', 'max:100'],
        ];

        if ($user->isKaryawan()) {
            $rules['no_hp'] = ['required', 'regex:/^08[0-9]{8,11}$/'];
            $rules['alamat'] = [
                'required',
                'string',
                'min:5',
                'max:255',
                'regex:/[A-Za-z]/'
            ];
        }

        $messages = [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.string'   => 'Nama lengkap harus berupa teks.',
            'name.min'      => 'Nama lengkap minimal 3 karakter.',
            'name.max'      => 'Nama lengkap maksimal 100 karakter.',

            'email.required' => 'Alamat email wajib diisi.',
            'email.email'    => 'Format alamat email tidak valid.',
            'email.max'      => 'Alamat email maksimal 100 karakter.',
            'email.unique'   => 'Alamat email sudah digunakan oleh pengguna lain.',

            'no_hp.required' => 'Nomor telepon wajib diisi.',
            'no_hp.regex'    => 'Nomor telepon harus diawali dengan 08 dan terdiri dari 10 sampai 13 digit angka.',

            'alamat.required' => 'Alamat wajib diisi.',
            'alamat.string' => 'Alamat harus berupa teks.',
            'alamat.max'    => 'Alamat maksimal 255 karakter.',
            'alamat.min' => 'Alamat minimal 5 karakter.',
            'alamat.regex' => 'Alamat harus mengandung huruf (tidak boleh angka saja).',

        ];

        $validated = $request->validate($rules, $messages);

        $user->update([
            'name'  => $validated['name'],
        ]);

        if ($user->isKaryawan()) {
            $user->karyawan()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nama_karyawan'   => $validated['name'],
                    'no_hp'           => $validated['no_hp'],
                    'alamat'          => $validated['alamat'] ?? null,
                    'status_karyawan' => 'aktif',
                ]
            );
        }

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $request->session()->flash('active_tab', 'ubahPassword');

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:20',
                'confirmed',
                'regex:/^(?=.*[A-Za-z])(?=.*\d).+$/'
            ],
        ], [
            'current_password.required' => 'Password lama wajib diisi.',
            'current_password.current_password' => 'Password lama yang Anda masukkan tidak sesuai.',

            'password.required'  => 'Password baru wajib diisi.',
            'password.string'    => 'Password baru harus berupa teks.',
            'password.min'       => 'Password baru minimal terdiri dari 8 karakter.',
            'password.max'       => 'Password baru maksimal terdiri dari 20 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'password.regex'     => 'Password baru harus mengandung minimal satu huruf dan satu angka.',
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }
}

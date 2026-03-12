<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:6', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',

            'password.required' => 'Kata sandi wajib diisi.',
            'password.min'      => 'Kata sandi minimal 6 karakter.',
            'password.max'      => 'Kata sandi maksimal 20 karakter.',
        ];
    }

    public function authenticate(): void
    {
        if (! Auth::attempt([
            'email' => $this->email,
            'password' => $this->password,
            'is_active' => 1
        ], $this->boolean('remember'))) {

            throw \Illuminate\Validation\ValidationException::withMessages([
                'email' => 'Email, kata sandi salah atau akun sudah dinonaktifkan oleh admin.',
            ]);
        }
    }
}

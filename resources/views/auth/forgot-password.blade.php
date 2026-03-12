<x-guest-layout title="Lupa Kata Sandi">

    <div class="mb-4 text-center">
        <h4 class="fw-bold">Lupa Kata Sandi</h4>
        <p class="text-muted">
Masukkan alamat email terdaftar, dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi ke email tersebut.        </p>
    </div>

    {{-- Session Status --}}
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" novalidate>
        @csrf

        {{-- Email --}}
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email"
                   name="email"
                   value="{{ old('email') }}"
                   class="form-control @error('email') is-invalid @enderror"
                   required autofocus>

            @error('email')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Button --}}
        <button type="submit" class="btn btn-primary w-100">
            Kirim Tautan Reset Password
        </button>

        {{-- Back to Login --}}
        <div class="mt-3 text-center">
            <a href="{{ route('login') }}" class="text-decoration-none">
                Kembali ke Login
            </a>
        </div>

    </form>

</x-guest-layout>

<x-guest-layout title="Reset Kata Sandi">

    <div class="mb-4 text-center">
        <h4 class="fw-bold">Reset Kata Sandi</h4>
        <p class="text-muted">
            Silakan masukkan email dan kata sandi baru Anda.
        </p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" novalidate>
        @csrf

        {{-- Token --}}
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        {{-- Email --}}
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email"
                   name="email"
                   value="{{ old('email', $request->email) }}"
                   class="form-control @error('email') is-invalid @enderror"
                   required autofocus>

            @error('email')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Password Baru --}}
        <div class="mb-3">
            <label class="form-label">Kata Sandi Baru</label>
            <input type="password"
                   name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   required autocomplete="new-password">

            @error('password')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Konfirmasi Password --}}
        <div class="mb-3">
            <label class="form-label">Konfirmasi Kata Sandi</label>
            <input type="password"
                   name="password_confirmation"
                   class="form-control @error('password_confirmation') is-invalid @enderror"
                   required autocomplete="new-password">

            @error('password_confirmation')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Button --}}
        <button type="submit" class="btn btn-primary w-100">
            Reset Password
        </button>

        {{-- Back to Login --}}
        <div class="mt-3 text-center">
            <a href="{{ route('login') }}" class="text-decoration-none">
                Kembali ke Login
            </a>
        </div>

    </form>

</x-guest-layout>

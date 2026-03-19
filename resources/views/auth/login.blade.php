<x-guest-layout title="Login">    {{-- Logo --}}
    <div class="mb-4 text-center">
        <img src="{{ asset('assets/img/ayam.png') }}"
             alt="Logo Ayam"
             class="img-fluid"
             style="max-width: 320px; height: auto;">
    </div>
    <div class="mb-4 text-center">
        <h4 class="fw-bold">Login</h4>
        <p class="text-muted">Silakan login ke akun Anda</p>
    </div>

    {{-- Session Status --}}
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" novalidate>
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

        {{-- Password --}}
        <div class="mb-3">
            <label class="form-label">Kata Sandi</label>
            <input type="password"
                   name="password"
                   id="password"
                   class="form-control @error('password') is-invalid @enderror"
                   minlength="6"
                   maxlength="20"
                   required>

            @error('password')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Remember + Forgot --}}
        <div class="mb-3 d-flex justify-content-between align-items-center">
            {{-- <div class="form-check">
                <input class="form-check-input"
                       type="checkbox"
                       name="remember"
                       id="remember">
                <label class="form-check-label" for="remember">
                    Ingat saya
                </label>
            </div> --}}

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-decoration-none">
                    Lupa kata sandi?
                </a>
            @endif
        </div>

        {{-- Button --}}
        <button type="submit" class="btn btn-primary w-100">
            Masuk
        </button>
    </form>

</x-guest-layout>

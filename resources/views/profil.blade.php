@extends('template')
@section('title', 'Profil')

@section('content')
    <div class="main-panel">
        <div class="content">
            <div class="container-fluid">
                <div class="mb-4">
                    <h4 class="mb-1 fw-bold">Profil</h4>
                </div>
                <div class="shadow-sm card">
                    <div class="bg-white card-header border-bottom">
                        <h6 class="mb-0 fw-bold text-dark">
                            <i class="la la-user-circle text-primary"></i> Informasi Profil
                        </h6>
                    </div>
                    <div class="card-body">
                        <!--  NAV TAB -->
                        <ul class="nav nav-tabs fw-bold">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#detailProfil">
                                    <i class="la la-user"></i> Detail Profil
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#editProfil">
                                    <i class="la la-edit"></i> Edit Profil
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#ubahPassword">
                                    <i class="la la-lock"></i> Ubah Password
                                </a>
                            </li>
                        </ul>

                        <div class="pt-4 tab-content">

                            <!-- DETAIL PROFIL -->
                            <div class="tab-pane fade show active" id="detailProfil">
                                <div class="row">
                                    <!-- AVATAR DEFAULT SISTEM -->
                                    <div class="mb-4 text-center col-md-3">
                                        <div class="d-flex justify-content-center">
                                            <div class="shadow-sm d-flex align-items-center justify-content-center rounded-circle bg-light"
                                                style="width:120px;height:120px;">
                                                <i class="la la-user text-secondary" style="font-size:48px;"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- INFORMASI AKUN -->
                                    <div class="col-md-9">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <small class="text-muted">Nama Lengkap</small>
                                                <div class="fw-bold">
                                                    {{ $user->isKaryawan() ? $karyawan?->nama_karyawan : $user->name }}
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <small class="text-muted">Email</small>
                                                <div class="fw-bold">{{ $user->email }}</div>
                                            </div>
                                            @if ($user->isKaryawan())
                                                <div class="col-md-6">
                                                    <small class="text-muted">No. Telepon</small>
                                                    <div class="fw-bold">{{ $karyawan?->no_hp ?? '-' }}</div>
                                                </div>

                                                <div class="col-md-6">
                                                    <small class="text-muted">Alamat</small>
                                                    <div class="fw-bold">{{ $karyawan?->alamat ?? '-' }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--  EDIT PROFIL -->
                            <div class="tab-pane fade" id="editProfil">
                                <form method="POST" action="{{ route('profil.update', $user->id) }}">
                                    @csrf
                                    @method('PATCH')

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="fw-bold">Nama Lengkap</label>
                                                <input type="text" name="name"
                                                    class="form-control @error('name') is-invalid @enderror"
                                                    value="{{ old('name', $user->name) }}">
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="fw-bold">Email</label>
                                                <input type="email" name="email" class="form-control"
                                                    value="{{ old('email', $user->email) }}" readonly>

                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    @if ($user->isKaryawan())
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="fw-bold">No. Telepon</label>
                                                    <input type="text" name="no_hp" maxlength="13" inputmode="numeric"
                                                        pattern="[0-9]*"
                                                        oninput="this.value = this.value.replace(/[^0-9]/g,'')"
                                                        class="form-control @error('no_hp') is-invalid @enderror"
                                                        value="{{ old('no_hp', $karyawan?->no_hp) }}">

                                                    @error('no_hp')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="fw-bold">Alamat</label>

                                                    <input type="text" name="alamat"
                                                        class="form-control @error('alamat') is-invalid @enderror"
                                                        value="{{ old('alamat', $karyawan?->alamat) }}">


                                                    @error('alamat')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    @endif


                                    <button class="px-4 mt-2 btn btn-primary">
                                        <i class="la la-save"></i> Simpan Perubahan
                                    </button>
                                </form>
                            </div>

                            <!--  UBAH PASSWORD  -->
                            <div class="tab-pane fade" id="ubahPassword">
                                <form method="POST" action="{{ route('profil.password.update', $user->id) }}"
                                    style="max-width:500px;">
                                    @csrf
                                    @method('PATCH')

                                    <div class="form-group">
                                        <label class="fw-bold">Password Lama</label>
                                        <div class="input-group">
                                            <input type="password" name="current_password" id="current_password"
                                                maxlength="20"
                                                class="form-control @error('current_password') is-invalid @enderror">

                                            <button type="button" class="btn btn-outline-secondary toggle-password"
                                                data-target="current_password">
                                                <i class="la la-eye"></i>
                                            </button>

                                            @error('current_password')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <label class="fw-bold">Password Baru</label>
                                        <div class="input-group">
                                            <input type="password" name="password" id="password" maxlength="20"
                                                class="form-control @error('password') is-invalid @enderror">


                                            <button type="button" class="btn btn-outline-secondary toggle-password"
                                                data-target="password">
                                                <i class="la la-eye"></i>
                                            </button>

                                            @error('password')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <label class="fw-bold">Konfirmasi Password Baru</label>
                                        <div class="input-group">
                                            <input type="password" name="password_confirmation"
                                                id="password_confirmation" maxlength="20" class="form-control">


                                            <button type="button" class="btn btn-outline-secondary toggle-password"
                                                data-target="password_confirmation">
                                                <i class="la la-eye"></i>
                                            </button>
                                        </div>
                                    </div>


                                    <button class="px-4 mt-2 btn btn-primary">
                                        <i class="la la-lock"></i> Update Password
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
@section('scripts')

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            //  SWEET ALERT SUCCESS
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 2000
                });
            @endif


            //  AUTO TAB
            @if (session('active_tab'))
                var triggerTab = document.querySelector('a[href="#{{ session('active_tab') }}"]');
                if (triggerTab) {
                    new bootstrap.Tab(triggerTab).show();
                }
            @endif

            // Jika ada error password
            @if ($errors->has('current_password') || $errors->has('password'))
                var triggerTab = document.querySelector('a[href="#ubahPassword"]');
                if (triggerTab) {
                    new bootstrap.Tab(triggerTab).show();
                }
            @endif

            // Jika ada error profil
            @if ($errors->has('name') || $errors->has('email') || $errors->has('no_hp') || $errors->has('alamat'))
                var triggerTab = document.querySelector('a[href="#editProfil"]');
                if (triggerTab) {
                    new bootstrap.Tab(triggerTab).show();
                }
            @endif
            //  TOGGLE PASSWORD
            document.querySelectorAll('.toggle-password').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    let targetId = this.getAttribute('data-target');
                    let input = document.getElementById(targetId);
                    let icon = this.querySelector('i');

                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.remove('la-eye');
                        icon.classList.add('la-eye-slash');
                    } else {
                        input.type = 'password';
                        icon.classList.remove('la-eye-slash');
                        icon.classList.add('la-eye');
                    }
                });
            });

        });
    </script>
@endsection

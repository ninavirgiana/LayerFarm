@extends('template')
@section('title', 'Data Karyawan')

@section('content')
    <div class="main-panel">
        <div class="content">
            <div class="container-fluid">
                <div class="mb-4">
                    <h4 class="mb-1 fw-bold">Data Karyawan</h4>
                </div>
                <div class="shadow-sm card">
                    <!--  FILTER  -->
                    <form method="GET" action="{{ route('karyawan') }}" id="formFilter">
                        <div class="card-body border-bottom">
                            <div class="row g-2 align-items-end">
                                <!-- SEARCH -->
                                <div class="col-md-4">
                                    <label class="small text-muted">Cari</label>
                                    <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                                        class="form-control form-control-sm" placeholder="Cari data...">
                                </div>
                                <!-- STATUS -->
                                <div class="col-md-3">
                                    <label class="small text-muted">Status</label>
                                    <select name="status" id="filterStatus" class="form-control form-control-sm">
                                        <option value="">Semua</option>
                                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>
                                            Aktif
                                        </option>
                                        <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>
                                            Non Aktif
                                        </option>
                                    </select>
                                </div>
                                <!-- PER PAGE -->
                                <div class="col-md-2">
                                    <label class="small text-muted">Tampilkan</label>
                                    <select name="perPage" onchange="this.form.submit()"
                                        class="form-control form-control-sm">
                                        <option value="10" {{ request('perPage') == 10 ? 'selected' : '' }}>10</option>
                                        <option value="25" {{ request('perPage') == 25 ? 'selected' : '' }}>25</option>
                                        <option value="50" {{ request('perPage') == 50 ? 'selected' : '' }}>50</option>
                                    </select>
                                </div>
                                <!-- BUTTON -->
                                <div class="col-md-3 text-end">
                                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                                        data-target="#modalTambahKaryawan">
                                        <i class="la la-plus"></i> Tambah
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- TABEL -->
                    <div class="p-0 card-body">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="50">No</th>
                                        <th>Nama Karyawan</th>
                                        <th>No Hp</th>
                                        <th>Alamat</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center" width="120">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>
@foreach ($data as $item)
<tr>
    <td class="text-center">
        {{ $data->firstItem() + $loop->index }}
    </td>

    <td>
        <strong>{{ $item->nama_karyawan }}</strong><br>

        @if ($item->user)
            <small class="text-success">
                <i class="la la-check-circle"></i> Memiliki Akun
            </small>
        @else
            <small class="text-muted">
                <i class="la la-times-circle"></i> Tidak Memiliki Akun
            </small>
        @endif
    </td>

    <td>{{ $item->no_hp }}</td>
    <td>{{ $item->alamat ?? '-' }}</td>

    <td class="text-center">
        @if ($item->status_karyawan === 'aktif')
            <span class="badge badge-success">Aktif</span>
        @else
            <span class="badge badge-secondary">Non Aktif</span>
        @endif
    </td>

    <td class="text-center">
        <!-- EDIT -->
        <button class="text-white btn btn-warning btn-sm"
            data-toggle="modal"
            data-target="#modalEditKaryawan{{ $item->id }}">
            <i class="la la-edit"></i>
        </button>

        <!-- AKTIF / NONAKTIF -->
        @if ($item->status_karyawan === 'aktif')
            <button class="btn btn-secondary btn-sm"
                data-toggle="modal"
                data-target="#modalNonaktifKaryawan{{ $item->id }}">
                <i class="la la-ban"></i>
            </button>
        @else
            <form action="{{ route('karyawan.aktifkan', $item->id) }}"
                method="POST"
                class="d-inline">
                @csrf
                @method('PATCH')

                <button class="btn btn-success btn-sm">
                    <i class="la la-check"></i>
                </button>
            </form>
        @endif
    </td>
</tr>
@endforeach

@if ($data->count() == 0)
<tr>
    <td colspan="6" class="py-4 text-center text-muted">
        Data karyawan belum tersedia
    </td>
</tr>
@endif
</tbody>
                            </table>
                        </div>
                    </div>
                    <!-- FOOTER  -->
                    <div class="gap-2 card-body d-flex flex-column flex-md-row justify-content-between align-items-center">
                        <small class="text-muted">
                            @if ($data->count())
                                Menampilkan {{ $data->firstItem() }} – {{ $data->lastItem() }}
                                dari {{ $data->total() }} data
                            @endif
                        </small>
                        @if ($data->hasPages())
                            {{ $data->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--  MODAL TAMBAH  -->
    <div class="modal fade" id="modalTambahKaryawan" tabindex="-1">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header modal-header-clean">
                    <h6 class="mb-0 modal-title">
                        <i class="mr-1 la la-plus-circle text-muted"></i>
                        Tambah Karyawan
                    </h6>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <form method="POST" action="{{ route('karyawan.store') }}">
                        @csrf

                        <input type="hidden" name="status_karyawan" value="aktif">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Lengkap</label>
                                    <input type="text" name="nama_karyawan" value="{{ old('nama_karyawan') }}"
                                        class="form-control @error('nama_karyawan') is-invalid @enderror">

                                    @error('nama_karyawan')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>No HP</label>
                                    <input type="text" name="no_hp" value="{{ old('no_hp') }}" maxlength="13"
                                        inputmode="numeric" pattern="[0-9]{10,13}"
                                        oninput="this.value = this.value.replace(/[^0-9]/g,'').slice(0,13)"
                                        class="form-control @error('no_hp') is-invalid @enderror" {{-- placeholder="Contoh: 081234567890" --}}>

                                    @error('no_hp')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                        </div>
                        <div class="form-group">
                            <label>Alamat</label>
                            <input type="text" name="alamat" value="{{ old('alamat') }}"
                                class="form-control @error('alamat') is-invalid @enderror">

                            @error('alamat')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <hr>

                        <label class="gap-2 d-flex align-items-center">
                            <input type="checkbox" id="toggleLogin" name="has_login"
                                {{ old('has_login') ? 'checked' : '' }}>
                            Berikan akses login ke sistem
                        </label>

                        <div id="formAkun" style="{{ old('has_login') ? '' : 'display:none;' }}">
                            <h6 class="mb-2 fw-bold">Akun Login</h6>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" name="email" value="{{ old('email') }}"
                                            class="form-control @error('email') is-invalid @enderror">

                                        @error('email')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Password</label>

                                        <div class="input-group">
                                            <input type="password" id="passwordInput" name="password" maxlength="20"
                                                minlength="6" oninput="this.value = this.value.slice(0,20)"
                                                class="form-control @error('password') is-invalid @enderror">

                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-secondary"
                                                    id="togglePassword">
                                                    <i class="la la-eye"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <small class="text-muted">
                                            Password minimal 6 dan maksimal 20 karakter
                                        </small>

                                        @error('password')
                                            <small class="text-danger d-block">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button class="btn btn-primary">
                                <i class="la la-save"></i> Simpan
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>

    <!--  MODAL EDIT & NONAKTIF  -->
    @foreach ($data as $item)
        <!-- MODAL EDIT -->
        <div class="modal fade" id="modalEditKaryawan{{ $item->id }}" tabindex="-1">
            <div class="modal-dialog modal-md modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header modal-header-clean">
                        <h5 class="modal-title">
                            <i class="la la-edit"></i> Edit Karyawan
                        </h5>
                        <button class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">
                        <form method="POST" action="{{ route('karyawan.update', $item->id) }}">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="id" value="{{ $item->id }}">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nama Lengkap</label>
                                        <input type="text" name="nama_karyawan"
                                            value="{{ old('nama_karyawan', $item->nama_karyawan) }}"
                                            class="form-control @error('nama_karyawan') is-invalid @enderror">

                                        @error('nama_karyawan')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>No HP</label>
                                        <input type="text" name="no_hp" value="{{ old('no_hp', $item->no_hp) }}"
                                            maxlength="13" inputmode="numeric" pattern="[0-9]{10,13}"
                                            oninput="this.value = this.value.replace(/[^0-9]/g,'').slice(0,13)"
                                            class="form-control @error('no_hp') is-invalid @enderror">

                                        @error('no_hp')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Alamat</label>
                                <input type="text" name="alamat" value="{{ old('alamat', $item->alamat) }}"
                                    class="form-control @error('alamat') is-invalid @enderror">

                                @error('alamat')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                <button type="submit" class="text-white btn btn-warning">
                                    <i class="la la-save"></i> Update
                                </button>
                            </div>

                        </form>
                    </div>

                </div>
            </div>
        </div>


        <!-- MODAL NONAKTIF -->
        <div class="modal fade" id="modalNonaktifKaryawan{{ $item->id }}" tabindex="-1">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header modal-header-clean">
                        <h5 class="modal-title">
                            <i class="la la-ban"></i> Nonaktifkan Karyawan
                        </h5>
                        <button class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <form method="POST" action="{{ route('karyawan.nonaktifkan', $item->id) }}">
                        @csrf
                        @method('PATCH')

                        <div class="text-center modal-body">
                            <p class="mb-0">
                                Yakin ingin menonaktifkan
                                <strong>{{ $item->nama_karyawan }}</strong>?
                            </p>
                        </div>

                        <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="text-white btn btn-warning">
                                Nonaktifkan
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    @endforeach
@endsection

@section('scripts')

    <script>
        $('#toggleLogin').on('change', function() {
            $('#formAkun').toggle(this.checked);
        });
    </script>

    <script>
        $(document).on('click', '#togglePassword', function() {
            let input = $('#passwordInput');
            let icon = $(this).find('i');

            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('la-eye').addClass('la-eye-slash');
            } else {
                input.attr('type', 'password');
                icon.removeClass('la-eye-slash').addClass('la-eye');
            }
        });
    </script>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 1800
            });
        </script>
    @endif


    @if ($errors->any())
        <script>
            $(document).ready(function() {

                @if (old('_method') == 'PUT' && old('id'))
                    $('#modalEditKaryawan{{ old('id') }}').modal('show');
                @else
                    $('#modalTambahKaryawan').modal('show');
                @endif

            });
        </script>
    @endif


    <script>
        let timer;

        $('#searchInput').on('keyup', function() {
            clearTimeout(timer);
            timer = setTimeout(() => {
                $('#formFilter').submit();
            }, 400);
        });

        $('#filterStatus').on('change', function() {
            $('#formFilter').submit();
        });
    </script>
@endsection

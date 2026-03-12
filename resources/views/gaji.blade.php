@extends('template')
@section('title', 'Gaji Karyawan')

@section('content')
    <div class="main-panel">
        <div class="content">
            <div class="container-fluid">
                <div class="mb-4">
                    <h4 class="mb-1 fw-bold">Pencatatan Gaji Karyawan</h4>
                </div>
                <div class="shadow-sm card table-card">

                    <!--  FILTER BAR  -->
                    <form method="GET" action="{{ route('gaji') }}" id="formFilter">
                        <div class="card-body border-bottom filter-bar">
                            <div class="row align-items-end g-2">

                                <div class="col-md-3 col-12">
                                    <label class="small text-muted">Cari</label>
                                    <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                                        class="form-control form-control-sm" placeholder="Cari data...">
                                </div>

                                <div class="col-md-2 col-6">
                                    <label class="small text-muted">Bulan</label>
                                    <select name="bulan" id="filterBulan" class="form-control form-control-sm">
                                        <option value="">Semua</option>
                                        @for ($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}"
                                                {{ request('bulan') == $i ? 'selected' : '' }}>
                                                {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>

                                <div class="col-md-2 col-6">
                                    <label class="small text-muted">Tahun</label>
                                    <select name="tahun" id="filterTahun" class="form-control form-control-sm">
                                        <option value="">Semua</option>
                                        @foreach ([2024, 2025, 2026] as $th)
                                            <option value="{{ $th }}"
                                                {{ request('tahun') == $th ? 'selected' : '' }}>
                                                {{ $th }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2 col-6">
                                    <label class="small text-muted">Tampilkan</label>
                                    <select name="perPage" onchange="this.form.submit()"
                                        class="form-control form-control-sm">
                                        <option value="10" {{ request('perPage') == 10 ? 'selected' : '' }}>10</option>
                                        <option value="25" {{ request('perPage') == 25 ? 'selected' : '' }}>25</option>
                                        <option value="50" {{ request('perPage') == 50 ? 'selected' : '' }}>50</option>
                                    </select>
                                </div>

                                <div class="col-md-3 col-12 text-md-end">
                                    <button type="button" class="btn btn-primary btn-sm w-100 w-md-auto"
                                        data-toggle="modal" data-target="#modalTambahGaji">
                                        <i class="la la-plus"></i> Tambah
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- TABEL  -->
                    <div class="p-0 card-body">
                        <div class="table-responsive">
                            <table id="tabelGaji" class="table mb-0 table-standard">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="50">No</th>
                                        <th>Nama Karyawan</th>
                                        <th>Bulan</th>
                                        <th>Tahun</th>
                                        <th>Gaji Pokok</th>
                                        <th>Tanggal Bayar</th>
                                        <th class="text-center">Keterangan</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($gaji as $item)
                                        <tr data-nama="{{ $item->karyawan->nama_karyawan }}"
                                            data-bulan="{{ $item->bulan }}" data-tahun="{{ $item->tahun }}">

                                            <td class="text-center">
                                                {{ $gaji->firstItem() + $loop->index }}
                                            </td>

                                            <td>{{ $item->karyawan->nama_karyawan }}</td>

                                            <td>
                                                {{ \Carbon\Carbon::create()->month($item->bulan)->translatedFormat('F') }}
                                            </td>

                                            <td>{{ $item->tahun }}</td>

                                            <td>Rp {{ number_format($item->gaji_pokok, 0, ',', '.') }}</td>

                                            <td>
                                                {{ \Carbon\Carbon::parse($item->tanggal_bayar)->translatedFormat('d F Y') }}
                                            </td>

                                            <td class="text-center">
                                                <span class="badge badge-success">Dibayar</span>
                                            </td>

                                        </tr>
                                    @endforeach

                                    @if ($gaji->count() == 0)
                                        <tr>
                                            <td colspan="7" class="py-4 text-center text-muted">
                                                Data gaji karyawan belum tersedia
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!--  FOOTER / PAGINATION-->
                    <div class="gap-2 card-body d-flex flex-column flex-md-row justify-content-between align-items-center">

                        <small class="text-muted">
                            @if ($gaji->count())
                                Menampilkan {{ $gaji->firstItem() }} – {{ $gaji->lastItem() }}
                                dari {{ $gaji->total() }} data
                            @else
                                Tidak ada data gaji
                            @endif
                        </small>

                        @if ($gaji->hasPages())
                            {{ $gaji->links() }}
                        @endif

                    </div>



                </div>

            </div>
        </div>
    </div>

    {{--  MODAL TAMBAH GAJI  --}}
    <div class="modal fade" id="modalTambahGaji" tabindex="-1">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">


                <div class="modal-header modal-header-clean">
                    <h6 class="mb-0 modal-title">
                        <i class="mr-1 la la-plus-circle text-muted"></i>
                        Tambah Data Gaji
                    </h6>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <form action="{{ route('gaji.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">

                        <div class="form-group">
                            <label>Nama Karyawan</label>
                            <select name="karyawan_id" class="form-control @error('karyawan_id') is-invalid @enderror">
                                <option value="">-- Pilih Karyawan --</option>
                                @foreach ($karyawan as $row)
                                    <option value="{{ $row->id }}"
                                        {{ old('karyawan_id') == $row->id ? 'selected' : '' }}>
                                        {{ $row->nama_karyawan }}
                                    </option>
                                @endforeach
                            </select>
                            @error('karyawan_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <label>Bulan</label>
                                <select name="bulan" class="form-control @error('bulan') is-invalid @enderror">
                                    <option value="">-- Bulan --</option>
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ old('bulan') == $i ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                                        </option>
                                    @endfor
                                </select>
                                @error('bulan')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label>Tahun</label>
                                <input type="number" name="tahun" min="2000" max="{{ date('Y') }}"
                                    value="{{ old('tahun', date('Y')) }}"
                                    class="form-control @error('tahun') is-invalid @enderror">
                                @error('tahun')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label>Gaji</label>
                                <input type="text" id="gaji_pokok" name="gaji_pokok" value="{{ old('gaji_pokok') }}"
                                    class="form-control @error('gaji_pokok') is-invalid @enderror">
                                @error('gaji_pokok')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-2">
                            <label>Tanggal Pembayaran</label>
                            <input type="date" name="tanggal_bayar" max="{{ date('Y-m-d') }}"
                                value="{{ old('tanggal_bayar') }}"
                                class="form-control @error('tanggal_bayar') is-invalid @enderror">
                            @error('tanggal_bayar')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
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
@endsection



@section('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            /*
                FILTER OTOMATIS
             */
            const form = document.getElementById("formFilter");
            const search = document.getElementById("searchInput");
            const bulan = document.getElementById("filterBulan");
            const tahun = document.getElementById("filterTahun");

            let timer;

            if (search) {
                search.addEventListener("keyup", function() {
                    clearTimeout(timer);
                    timer = setTimeout(() => form.submit(), 500);
                });
            }

            if (bulan) {
                bulan.addEventListener("change", function() {
                    form.submit();
                });
            }

            if (tahun) {
                tahun.addEventListener("change", function() {
                    form.submit();
                });
            }


            /*
                FORMAT RUPIAH GAJI
             */
            const gajiInput = document.getElementById("gaji_pokok");

            if (gajiInput) {
                gajiInput.addEventListener("input", function() {

                    let angka = this.value.replace(/[^0-9]/g, '');

                    // BATAS MAKSIMAL 9.999.999
                    let max = 9999999;

                    if (angka) {
                        if (parseInt(angka) > max) {
                            angka = max.toString();
                        }

                        this.value = "Rp " + angka.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                    } else {
                        this.value = "";
                    }
                });
            }
        });
    </script>


    @if ($errors->any())
        <script>
            $(document).ready(function() {
                $('#modalTambahGaji').modal('show');
            });
        </script>
    @endif


    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 2000
            });
        </script>
    @endif

@endsection

@extends('template')
@section('title', 'Produksi Telur')

@section('content')
    <div class="main-panel">
        <div class="content">
            <div class="container-fluid">
                <div class="mb-4">
                    <h4 class="mb-1 fw-bold">Produksi Telur</h4>
                </div>
                <div class="shadow-sm card table-card">
                    <!-- FILTER BAR   -->
                    <form method="GET" action="{{ route('hasil') }}" id="formFilter">
                        <div class="card-body border-bottom">
                            <div class="row g-3 align-items-end">

                                <div class="col-md-4">
                                    <label class="small text-muted">Cari</label>
                                    <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                                        class="form-control form-control-sm" placeholder="Cari data...">
                                </div>

                                <div class="col-md-2">
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

                                <div class="col-md-2">
                                    <label class="small text-muted">Tahun</label>
                                    <select name="tahun" id="filterTahun" class="form-control form-control-sm">
                                        <option value="">Semua</option>
                                        @foreach ($tahunList as $th)
                                            <option value="{{ $th }}"
                                                {{ request('tahun') == $th ? 'selected' : '' }}>
                                                {{ $th }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="small text-muted">Data</label>
                                    <select name="perPage" class="form-control form-control-sm"
                                        onchange="this.form.submit()">
                                        <option value="10" {{ request('perPage') == 10 ? 'selected' : '' }}>10</option>
                                        <option value="25" {{ request('perPage') == 25 ? 'selected' : '' }}>25</option>
                                        <option value="50" {{ request('perPage') == 50 ? 'selected' : '' }}>50</option>
                                    </select>
                                </div>

                                <div class="col-md-2 d-flex justify-content-end">
                                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                                        data-target="#modalTambahProduksi">
                                        <i class="la la-plus"></i> Tambah
                                    </button>
                                </div>

                            </div>

                        </div>
                    </form>
                    <!--  TABEL -->
                    <div class="p-0 card-body">
                        <div class="table-responsive">
                            <table class="table mb-0 table-standard">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="50">No</th>
                                        <th>Kandang</th>
                                        <th class="text-center">Shift</th>
                                        <th class="text-center">Telur Baik</th>
                                        <th class="text-center">Telur Rusak</th>
                                        <th class="text-center">Total Telur</th>
                                        <th>Tanggal Produksi</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center" width="120">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $item)
                                        <tr>
                                            <td class="text-center">{{ $data->firstItem() + $loop->index }}
                                            </td>
                                            <td>{{ optional($item->kandang)->nama_kandang ?? '-' }}</td>
                                            <td class="text-center">{{ ucfirst($item->shift) }}</td>
                                            <td class="text-center">
                                                {{ rtrim(rtrim(number_format($item->berat_telur_layak, 2, ',', '.'), '0'), ',') }}
                                                kg
                                            </td>

                                            <td class="text-center">
                                                {{ rtrim(rtrim(number_format($item->berat_telur_rusak, 2, ',', '.'), '0'), ',') }}
                                                kg
                                            </td>

                                            <td class="text-center fw-bold">
                                                {{ rtrim(rtrim(number_format($item->berat_telur_total, 2, ',', '.'), '0'), ',') }}
                                                kg
                                            </td>


                                            <td>{{ \Carbon\Carbon::parse($item->tanggal_produksi)->translatedFormat('d F Y') }}
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-success">Selesai</span>
                                            </td>
                                            <td class="text-center">
                                                <button class="text-white btn btn-warning btn-sm btn-action"
                                                    data-toggle="modal" data-target="#modalEditProduksi"
                                                    data-id="{{ $item->id }}" data-kandang="{{ $item->kandang_id }}"
                                                    data-shift="{{ $item->shift }}"
                                                    data-layak="{{ $item->berat_telur_layak }}"
                                                    data-rusak="{{ $item->berat_telur_rusak }}"
                                                    data-tanggal="{{ $item->tanggal_produksi }}">
                                                    <i class="la la-edit"></i>
                                                </button>

                                                @auth
                                                    @if (auth()->user()->isAdmin())
                                                        <button class="btn btn-danger btn-sm btn-action" data-toggle="modal"
                                                            data-target="#modalHapusProduksi" data-id="{{ $item->id }}">
                                                            <i class="la la-trash"></i>
                                                        </button>
                                                    @endif
                                                @endauth

                                            </td>
                                        </tr>
                                    @endforeach
                                    @if ($data->count() == 0)
                                        <tr>
                                            <td colspan="8" class="py-4 text-center text-muted">
                                                Data produksi belum tersedia
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- FOOTER -->
                    <div class="card-body table-footer d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            @if ($data->count())
                                Menampilkan {{ $data->firstItem() }} - {{ $data->lastItem() }}
                                dari {{ $data->total() }} data
                            @else
                                Belum ada data
                            @endif
                        </small>

                        <div>
                            {{ $data->links() }}
                        </div>
                    </div>



                </div>
            </div>
        </div>
    </div>

    <!--  MODAL TAMBAH  -->
    <div class="modal fade" id="modalTambahProduksi" tabindex="-1">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('hasil.store') }}" method="POST">
                    @csrf

                    <div class="modal-header modal-header-clean">
                        <h6 class="mb-0 modal-title">
                            <i class="mr-1 la la-plus-circle text-muted"></i>
                            Tambah Produksi Telur
                        </h6>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <div class="form-group">
                            <label>Kandang</label>
                            <select name="kandang_id" class="form-control @error('kandang_id') is-invalid @enderror">
                                <option value="">-- Pilih Kandang --</option>
                                @foreach ($kandang as $k)
                                    <option value="{{ $k->id }}"
                                        {{ old('kandang_id') == $k->id ? 'selected' : '' }}>
                                        {{ $k->nama_kandang }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kandang_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Shift</label>
                            <select name="shift" class="form-control @error('shift') is-invalid @enderror">
                                <option value="">-- Pilih Shift --</option>
                                <option value="pagi" {{ old('shift') == 'pagi' ? 'selected' : '' }}>Pagi</option>
                                <option value="sore" {{ old('shift') == 'sore' ? 'selected' : '' }}>Sore</option>
                            </select>
                            @error('shift')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Berat Telur Layak (Kg)</label>
                                    <input type="text" name="berat_telur_layak" maxlength="6" inputmode="decimal"
                                        class="form-control @error('berat_telur_layak') is-invalid @enderror"
                                        value="{{ old('berat_telur_layak') }}" oninput="validasiBerat(this)">

                                    @error('berat_telur_layak')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Berat Telur Rusak (Kg)</label>
                                    <input type="text" name="berat_telur_rusak" maxlength="6" inputmode="decimal"
                                        class="form-control @error('berat_telur_rusak') is-invalid @enderror"
                                        value="{{ old('berat_telur_rusak') }}" oninput="validasiBerat(this)">

                                    @error('berat_telur_rusak')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Tanggal Produksi</label>
                            <input type="date" name="tanggal_produksi" min="2000-01-01" max="{{ date('Y-m-d') }}"
                                class="form-control @error('tanggal_produksi') is-invalid @enderror"
                                value="{{ old('tanggal_produksi') }}">

                            @error('tanggal_produksi')
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

    <!--  MODAL EDIT  -->
    <div class="modal fade" id="modalEditProduksi" tabindex="-1">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" id="formEdit">
                    @csrf
                    @method('PUT')

                    <div class="modal-header modal-header-clean">
                        <h5 class="modal-title">
                            <i class="la la-edit"></i> Edit Produksi Telur
                        </h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">

                        <div class="form-group">
                            <label>Kandang</label>
                            <select name="kandang_id" class="form-control">
                                @foreach ($kandang as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama_kandang }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Shift</label>
                            <select name="shift" class="form-control">
                                <option value="pagi">Pagi</option>
                                <option value="sore">Sore</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Berat Telur Layak (Kg)</label>
                                    <input type="text" name="berat_telur_layak" class="form-control" maxlength="6" inputmode="decimal"
                                        oninput="validasiBerat(this)">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Berat Telur Rusak (Kg)</label>
                                    <input type="text" name="berat_telur_rusak" class="form-control" maxlength="6" inputmode="decimal"
                                        oninput="validasiBerat(this)">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Tanggal Produksi</label>
                            <input type="date" name="tanggal_produksi" min="2000-01-01" max="{{ date('Y-m-d') }}"
                                class="form-control">
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button class="btn btn-primary">
                            <i class="la la-save"></i> Update
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!--  MODAL HAPUS  -->
    <div class="modal fade" id="modalHapusProduksi" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" id="formHapus">
                    @csrf
                    @method('DELETE')

                    <div class="modal-header modal-header-clean">
                        <h6 class="modal-title">
                            <i class="la la-trash"></i> Hapus Produksi Telur
                        </h6>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="text-center modal-body">
                        <p class="mb-0">Yakin ingin menghapus data produksi ini?</p>
                    </div>

                    <div class="modal-footer justify-content-center">
                        <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button class="btn btn-danger">
                            <i class="la la-trash"></i> Hapus
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        /*  AUTO FILTER */
        let timer;

        function autoSubmit() {
            clearTimeout(timer);
            timer = setTimeout(() => {
                $('#formFilter').submit();
            }, 500);
        }

        $('#searchInput').on('keyup', autoSubmit);
        $('#filterKandang, #filterBulan, #filterTahun').on('change', function() {
            $('#formFilter').submit();
        });

function formatBerat(angka) {
    angka = parseFloat(angka);

    if (isNaN(angka)) return '';

    // jika nilainya 0 tetap tampil 0
    if (angka === 0) return '0';

    // hilangkan .00 atau nol di belakang
    return angka.toString().replace(/\.?0+$/, '');
}
        /*  MODAL EDIT  */
        const baseUrl = "{{ url('/hasil') }}";

        $('#modalEditProduksi').on('show.bs.modal', function(e) {
            let btn = $(e.relatedTarget);

            $('#formEdit').attr('action', baseUrl + '/' + btn.data('id'));
            $('#formEdit [name=kandang_id]').val(btn.data('kandang'));
            $('#formEdit [name=shift]').val(btn.data('shift'));
            $('#formEdit [name=berat_telur_layak]').val(formatBerat(btn.data('layak') ?? 0));
$('#formEdit [name=berat_telur_rusak]').val(formatBerat(btn.data('rusak') ?? 0));
            $('#formEdit [name=tanggal_produksi]').val(btn.data('tanggal'));
        });


        /*  MODAL HAPUS  */
        $('#modalHapusProduksi').on('show.bs.modal', function(e) {
            let btn = $(e.relatedTarget);
            $('#formHapus').attr('action', baseUrl + '/' + btn.data('id'));
        });


        /*AUTO OPEN MODAL JIKA ERROR */
        @if ($errors->any())
            $(document).ready(function() {
                $('#modalTambahProduksi').modal('show');
            });
        @endif


        /* SWEET ALERT SUCCESS */
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 2000
            });
        @endif
        function validasiBerat(input) {

            // Hanya izinkan angka, titik, dan koma
            input.value = input.value.replace(/[^0-9.,]/g, '');

            // Ubah koma menjadi titik
            input.value = input.value.replace(',', '.');

            // Pastikan hanya satu titik desimal
            let parts = input.value.split('.');
            if (parts.length > 2) {
                input.value = parts[0] + '.' + parts[1];
            }

            // Batasi maksimal 6 karakter
            if (input.value.length > 6) {
                input.value = input.value.slice(0, 6);
            }
        }
    </script>
@endsection

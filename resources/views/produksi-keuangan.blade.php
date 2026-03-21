@extends('template')
@section('title', 'Penjualan')

@section('content')
    <div class="main-panel">
        <div class="content">
            <div class="container-fluid">
                <div class="mb-4">
                    <h4 class="mb-1 fw-bold">Penjualan</h4>
                </div>
                <div class="shadow-sm card table-card">
                    <!--  FILTER BAR  -->
                    <form method="GET" id="formFilter">
                        <div class="card-body border-bottom">
                            <div class="row g-3 align-items-end">

                                <div class="col-md-2">
                                    <label class="small">Cari</label>
                                    <input type="text" name="search" placeholder="Cari data..."
                                        value="{{ request('search') }}" class="form-control form-control-sm">
                                </div>

                                <div class="col-md-2">
                                    <label class="small">Produk</label>
                                    <select name="produk" class="form-control form-control-sm">
                                        <option value="">Semua</option>
                                        @foreach ($produks as $key => $label)
                                            <option value="{{ $key }}"
                                                {{ request('produk') == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="small">Bulan</label>
                                    <select name="bulan" class="form-control form-control-sm">
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
                                    <label class="small">Tahun</label>
                                    <select name="tahun" class="form-control form-control-sm">
                                        <option value="">Semua</option>
@for($th = 2024; $th <= date('Y'); $th++)
    <option value="{{ $th }}"
        {{ request('tahun') == $th ? 'selected' : '' }}>
        {{ $th }}
    </option>
@endfor
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="small">Data</label>
                                    <select name="perPage" class="form-control form-control-sm">
                                        <option value="10" {{ request('perPage') == 10 ? 'selected' : '' }}>10</option>
                                        <option value="25" {{ request('perPage') == 25 ? 'selected' : '' }}>25</option>
                                        <option value="50" {{ request('perPage') == 50 ? 'selected' : '' }}>50</option>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="invisible small">Aksi</label>
                                    <button type="button" class="btn btn-primary btn-sm w-100" data-toggle="modal"
                                        data-target="#modalTambahPenjualan">

                                        <i class="mr-1 la la-plus"></i> Tambah
                                    </button>
                                </div>

                            </div>

                        </div>
                    </form>


                    <!-- TABEL -->
                    <div class="p-0 card-body">
                        <div class="table-responsive">
                            <table class="table mb-0 table-standard">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="50">No</th>
                                        <th>Tanggal</th>
                                        <th>Produk</th>
                                        <th class="text-center">Jumlah (Kg)</th>
                                        <th class="text-end">Harga (Kg)</th>
                                        <th class="text-end">Total</th>
                                        <th class="text-center" width="120">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($penjualans as $penjualan)
                                        <tr>
                                            <td class="text-center">
                                                {{ $penjualans->firstItem() + $loop->index }}
                                            </td>

                                            <td>
                                                {{ \Carbon\Carbon::parse($penjualan->tanggal_penjualan)->translatedFormat('d F Y') }}
                                            </td>
                                            <td>
                                                {{ $produks[$penjualan->jenis_produk] ?? '-' }}
                                            </td>

                                            <td class="text-center">
{{ rtrim(rtrim(number_format($penjualan->berat_terjual, 2, ',', '.'), '0'), ',') }} kg
                                            </td>

                                            <td class="text-end">
                                                Rp {{ number_format($penjualan->harga_satuan, 0, ',', '.') }}
                                            </td>

                                            <td class="text-end fw-bold">
                                                Rp {{ number_format($penjualan->total_penjualan, 0, ',', '.') }}
                                            </td>

                                            <td class="text-center">
                                                <button type="button" class="btn btn-warning btn-sm btn-action"
                                                    data-toggle="modal" data-target="#modalEditPenjualan"
                                                    data-id="{{ $penjualan->id }}"
                                                    data-tanggal="{{ $penjualan->tanggal_penjualan }}"
                                                    data-produk="{{ $penjualan->jenis_produk }}"
                                                    data-jumlah="{{ $penjualan->berat_terjual }}"
                                                    data-harga="{{ $penjualan->harga_satuan }}">
                                                    <i class="la la-edit"></i>
                                                </button>

                                                @auth
                                                    @if (auth()->user()->isAdmin())
                                                        <button type="button" class="btn btn-danger btn-sm btn-action"
                                                            data-toggle="modal" data-target="#modalHapusPenjualan"
                                                            data-id="{{ $penjualan->id }}"
                                                            data-info="Penjualan {{ \Carbon\Carbon::parse($penjualan->tanggal_penjualan)->translatedFormat('d F Y') }}">
                                                            <i class="la la-trash"></i>
                                                        </button>
                                                    @endif
                                                @endauth
                                            </td>
                                        </tr>

                                    @empty
                                        <tr>
                                            <td colspan="7" class="py-4 text-center text-muted">
                                                Data penjualan belum tersedia
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- FOOTER -->
                    <div class="card-body table-footer d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            @if ($penjualans->count())
                                Menampilkan {{ $penjualans->firstItem() }}
                                - {{ $penjualans->lastItem() }}
                                dari {{ $penjualans->total() }} data
                            @else
                                Belum ada data
                            @endif
                        </small>

                        <div>
                            {{ $penjualans->links() }}
                        </div>
                    </div>



                </div>
            </div>
        </div>
    </div>

    <!-- = MODAL TAMBAH = -->
    <div class="modal fade" id="modalTambahPenjualan" tabindex="-1">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('keuangan.store') }}" method="POST">
                    @csrf

                    <div class="modal-header modal-header-clean">
                        <h6 class="mb-0 modal-title">
                            <i class="mr-1 la la-plus-circle text-muted"></i>
                            Tambah Penjualan
                        </h6>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <div class="form-group">
                            <label>Tanggal Penjualan</label>
                            <input type="date" name="tanggal" max="{{ date('Y-m-d') }}"
                                class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal') }}">

                            @error('tanggal')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Produk</label>
                            <select name="jenis_produk" class="form-control @error('jenis_produk') is-invalid @enderror">
                                <option value="">-- Pilih Produk --</option>
                                <option value="telur" {{ old('jenis_produk') == 'telur' ? 'selected' : '' }}>Telur Ayam
                                </option>
                                <option value="afkir" {{ old('jenis_produk') == 'afkir' ? 'selected' : '' }}>Ayam Afkir
                                </option>
                            </select>

                            @error('jenis_produk')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror



                        </div>
                        <div class="form-group">
                            <label>Jumlah (Kg)</label>
                            <input type="number" name="jumlah" step="0.01" min="0.01" max="9999"
                                class="form-control @error('jumlah') is-invalid @enderror" value="{{ old('jumlah') }}">
                            @error('jumlah')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Harga (Kg)</label>
                            <input type="text" name="harga_satuan" id="hargaTambah"
class="form-control @error('harga_satuan') is-invalid @enderror"
value="{{ old('harga_satuan') }}">
                            @error('harga_satuan')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Total Penjualan</label>
                            <input type="text" id="totalPreview" class="form-control" readonly
                                placeholder="Total otomatis">
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

    <!-- = MODAL EDIT = -->
    <div class="modal fade" id="modalEditPenjualan" tabindex="-1">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" id="formEdit">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="id" id="edit_id">

                    <div class="modal-header modal-header-clean">
                        <h5 class="modal-title">
                            <i class="la la-edit"></i> Edit Penjualan
                        </h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">

                        <div class="form-group">
                            <label>Tanggal Penjualan</label>
                            <input type="date" name="tanggal" max="{{ date('Y-m-d') }}"
                                class="form-control @error('tanggal') is-invalid @enderror">


                            @error('tanggal')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Produk</label>
                            <select name="jenis_produk" class="form-control">
                                <option value="telur">Telur Ayam</option>
                                <option value="afkir">Ayam Afkir</option>
                            </select>
                        </div>


                        <div class="form-group">
                            <label>Jumlah (Kg)</label>
                            <input type="number" name="jumlah" step="0.01" min="0.01" max="9999"
                                class="form-control @error('jumlah') is-invalid @enderror" value="{{ old('jumlah') }}">
                            @error('jumlah')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Harga (Kg)</label>
                            <input type="text" name="harga_satuan" min="1" max="99999999"
                                class="form-control @error('harga_satuan') is-invalid @enderror"
                                value="{{ old('harga_satuan') }}">
                            @error('harga_satuan')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Total Penjualan</label>
                            <input type="text" id="totalPreviewEdit" class="form-control" readonly>
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

    <!-- = MODAL HAPUS = -->
    <div class="modal fade" id="modalHapusPenjualan" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" id="formHapus">
                    @csrf
                    @method('DELETE')

                    <div class="modal-header modal-header-clean">
                        <h6 class="modal-title">
                            <i class="la la-trash"></i> Hapus Penjualan
                        </h6>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="text-center modal-body">
                        <p class="mb-0">
                            Yakin ingin menghapus data<br>
                            <strong id="hapusInfo"></strong>?
                        </p>
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
        $(document).ready(function() {

            // ROUTE CONFIG (AMAN & DINAMIS)
            const updateRoute = "{{ route('keuangan.update', ':id') }}";
            const deleteRoute = "{{ route('keuangan.delete', ':id') }}";
function formatJumlah(angka) {
    angka = parseFloat(angka);

    if (isNaN(angka)) return '';

    // kalau bilangan bulat, tampilkan tanpa desimal
    if (Number.isInteger(angka)) {
        return angka.toString();
    }

    // kalau desimal, tampilkan normal (tanpa nol berlebih)
    return angka.toString().replace(/(\.\d*?[1-9])0+$/, '$1');
}

function formatRupiah(angka) {
    angka = parseInt(angka);

    if (isNaN(angka)) return '';

    return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}
            // MODAL EDIT
            $('#modalEditPenjualan').on('show.bs.modal', function(e) {

                let btn = $(e.relatedTarget);
                let id = btn.data('id');

                let url = updateRoute.replace(':id', id);
                $('#formEdit').attr('action', url);

                $('#edit_id').val(id);

                let oldTanggal = "{{ old('tanggal') }}";
                let oldJumlah = "{{ old('jumlah') }}";
                let oldHarga = "{{ old('harga_satuan') }}";
                let oldProduk = "{{ old('jenis_produk') }}";

                $('#formEdit [name=tanggal]').val(oldTanggal ? oldTanggal : btn.data('tanggal'));
                $('#formEdit [name=jenis_produk]').val(oldProduk ? oldProduk : btn.data('produk'));
                $('#formEdit [name=jumlah]').val(
    oldJumlah ? oldJumlah : formatJumlah(btn.data('jumlah'))
);

$('#formEdit [name=harga_satuan]').val(
    oldHarga ? oldHarga : formatRupiah(btn.data('harga'))
);

                hitungTotalEdit();
            });

            // RESET TOTAL MODAL TAMBAH
            $('#modalTambahPenjualan').on('show.bs.modal', function() {
                $('#totalPreview').val('');
            });

            // RESET TOTAL MODAL EDIT
            $('#modalEditPenjualan').on('hidden.bs.modal', function() {
                $('#totalPreviewEdit').val('');
            });

            // MODAL HAPUS
            $('#modalHapusPenjualan').on('show.bs.modal', function(e) {

                let btn = $(e.relatedTarget);
                let id = btn.data('id');

                let deleteUrl = deleteRoute.replace(':id', id);
                $('#formHapus').attr('action', deleteUrl);

                $('#hapusInfo').text(btn.data('info'));
            });

            // AUTO OPEN MODAL SAAT VALIDASI
            @if ($errors->any())
                @if (old('_method') == 'PUT')
                    $('#modalEditPenjualan').modal('show');
                @else
                    $('#modalTambahPenjualan').modal('show');
                @endif
            @endif

            // SEARCH DELAY 500ms
            let timer;

            $('input[name=search]').on('keyup', function() {
                clearTimeout(timer);
                timer = setTimeout(function() {
                    $('#formFilter').submit();
                }, 500);
            });

            // FILTER AUTO SUBMIT
            $('select[name=produk], select[name=bulan], select[name=tahun], select[name=perPage]')
                .on('change', function() {
                    $('#formFilter').submit();
                });

            // SWEET ALERT SUCCESS
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: "{{ session('success') }}",
                    timer: 2000,
                    showConfirmButton: false
                });
            @endif

            // PEMBATASAN INPUT JUMLAH
            $('input[name=jumlah]').on('input', function() {

                let value = parseFloat($(this).val());

                if (value > 9999) {
                    $(this).val(9999);
                }

                if (value < 0) {
                    $(this).val('');
                }

            });


            // TOTAL OTOMATIS MODAL TAMBAH
            function hitungTotalTambah() {

                let jumlah = parseFloat($('#modalTambahPenjualan input[name=jumlah]').val()) || 0;
let harga = $('#modalTambahPenjualan input[name=harga_satuan]').val().replace(/\./g,'');
harga = parseFloat(harga) || 0;
                let total = jumlah * harga;

                $('#totalPreview').val(
                    "Rp " + total.toLocaleString('id-ID')
                );
            }

            $('#modalTambahPenjualan input[name=jumlah], #modalTambahPenjualan input[name=harga_satuan]')
                .on('input', hitungTotalTambah);


            // TOTAL OTOMATIS MODAL EDIT
            function hitungTotalEdit() {

    let jumlah = parseFloat($('#modalEditPenjualan input[name=jumlah]').val()) || 0;

    let harga = $('#modalEditPenjualan input[name=harga_satuan]').val()
        .replace(/\./g, ''); // 🔥 hapus titik dulu

    harga = parseFloat(harga) || 0;

    let total = jumlah * harga;

    $('#totalPreviewEdit').val(
        "Rp " + total.toLocaleString('id-ID')
    );
}
$('#modalEditPenjualan input[name=harga_satuan]').on('input', function() {

    let angka = $(this).val().replace(/\D/g, '');

if (parseInt(angka || 0) > 99999999) {
    angka = '99999999';
}

    let rupiah = angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");

    $(this).val(rupiah);

    hitungTotalEdit();
});
$('#formEdit').on('submit', function() {

    let harga = $('#modalEditPenjualan input[name=harga_satuan]')
        .val()
        .replace(/\./g, '');

    $('#modalEditPenjualan input[name=harga_satuan]').val(harga);
});


// FORMAT RUPIAH + PEMBATASAN HARGA
$('#hargaTambah').on('input', function() {

    let angka = $(this).val().replace(/\D/g, '');

    // BATAS MAKSIMAL
if (parseInt(angka || 0) > 99999999) {
    angka = '99999999';
}

    // FORMAT RIBUAN
    let rupiah = angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");

    $(this).val(rupiah);

    hitungTotalTambah();
});

$('#modalTambahPenjualan form').on('submit', function() {
    let harga = $('#hargaTambah').val().replace(/\./g, '');
    $('#hargaTambah').val(harga);
});
        });
    </script>
@endsection

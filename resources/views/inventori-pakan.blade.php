@extends('template')
@section('title', 'Manajemen Pakan')

@section('content')
    <div class="main-panel">
        <div class="content">
            <div class="container-fluid">
                <div class="mb-4">
                    <h4 class="mb-1 fw-bold">Manajemen Pakan</h4>
                </div>
                <div class="shadow-sm card table-card operasional-page">
                    <!--  FILTER BAR  -->
                    <form method="GET" id="formFilter">
                        <div class="card-body border-bottom">
                            <div class="row g-3 align-items-end">

                                <div class="col-md-2">
                                    <label class="small">Cari</label>
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        class="form-control form-control-sm">
                                </div>

                                <div class="col-md-2">
                                    <label class="small">Jenis</label>
                                    <select name="jenis" class="form-control form-control-sm">
                                        <option value="">Semua</option>
                                        @if (auth()->user()->isAdmin())
                                            <option value="masuk" {{ request('jenis') == 'masuk' ? 'selected' : '' }}>Masuk
                                            </option>
                                        @endif
                                        <option value="keluar" {{ request('jenis') == 'keluar' ? 'selected' : '' }}>Keluar
                                        </option>
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
                                        @foreach ([2024, 2025, 2026] as $th)
                                            <option value="{{ $th }}"
                                                {{ request('tahun') == $th ? 'selected' : '' }}>
                                                {{ $th }}
                                            </option>
                                        @endforeach
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
                                    <label class="text-white small">Aksi</label>
                                    <button type="button" class="btn btn-primary btn-sm w-100" data-toggle="modal"
                                        data-target="#modalTransaksi">

                                        <i class="la la-plus"></i>
                                        @if (auth()->user()->isAdmin())
                                            Tambah
                                        @else
                                            Catat Pemakaian
                                        @endif

                                    </button>
                                </div>

                            </div>

                        </div>
                    </form>
                    @if (auth()->user()->isAdmin() && isset($stokMenipis) && $stokMenipis->count())
                        <div class="alert alert-warning">
                            <strong>⚠ Stok Menipis</strong>
                            <ul class="mb-0">
                                @foreach ($stokMenipis as $s)
                                    <li>
                                        {{ $s->nama_barang }} tersisa
                                        {{ number_format($s->stok, 0, ',', '.') }} karung
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif



                    <!--  TABEL -->
                    <div class="p-0 card-body">
                        <div class="table-responsive">
<table class="table mb-0" style="table-layout:fixed; width:100%;">                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" style="width:60px;">No</th>
                                        <th style="width:150px;">Tanggal</th>
                                        <th style="width:220px;">Nama Barang</th>
                                        <th class="text-center" style="width:110px;">Jenis</th>
                                        <th class="text-center" style="width:130px;">Jumlah</th>

                                        @if (auth()->user()->isAdmin())
                                            <th class="text-end" style="width:140px;">Harga (Rp)</th>
                                            <th class="text-end" style="width:150px;">Total (Rp)</th>
                                            <th class="text-center" style="width:110px;">Aksi</th>
                                        @endif
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse ($data as $item)
                                        <tr>

                                            <td class="text-center align-middle col-no">
                                                {{ $data->firstItem() ? $data->firstItem() + $loop->index : $loop->iteration }}
                                            </td>

                                            <td class="align-middle col-tanggal">
                                                {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}
                                            </td>

                                            <td class="align-middle"
                                                style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                                {{ $item->nama_barang }}
                                            </td>
                                            <td class="text-center align-middle col-jenis">
                                                <span
                                                    class="badge badge-{{ $item->jenis_transaksi == 'masuk' ? 'success' : 'danger' }}">
                                                    {{ ucfirst($item->jenis_transaksi) }}
                                                </span>
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ number_format($item->jumlah, 0, ',', '.') }} {{ $item->satuan }}
                                            </td>

                                            @if (auth()->user()->isAdmin())
                                                <td class="align-middle col-harga text-end">
                                                    {{ $item->harga_satuan ? 'Rp ' . number_format($item->harga_satuan, 0, ',', '.') : '-' }}
                                                </td>

                                                <td class="align-middle col-total text-end">
                                                    {{ $item->total_harga ? 'Rp ' . number_format($item->total_harga, 0, ',', '.') : '-' }}
                                                </td>

                                                <td class="text-center">
                                                    <div class="gap-1 d-flex justify-content-center">
                                                        <button class="btn btn-warning btn-sm" data-toggle="modal"
                                                            data-target="#modalEditTransaksi" data-id="{{ $item->id }}"
                                                            data-nama="{{ $item->nama_barang }}"
                                                            data-jenis="{{ $item->jenis_transaksi }}"
                                                            data-jumlah="{{ $item->jumlah }}"
                                                            data-satuan="{{ $item->satuan }}"
                                                            data-harga="{{ $item->harga_satuan }}"
                                                            data-tanggal="{{ $item->tanggal }}">
                                                            <i class="la la-edit"></i>
                                                        </button>

                                                        <button class="btn btn-danger btn-sm" data-toggle="modal"
                                                            data-target="#modalHapusTransaksi"
                                                            data-id="{{ $item->id }}">
                                                            <i class="la la-trash"></i>
                                                        </button>
                                                    </div>

                                                </td>
                                            @endif

                                        </tr>

                                    @empty
                                        <tr>
                                            <td colspan="{{ auth()->user()->isAdmin() ? 8 : 5 }}"
                                                class="py-4 text-center text-muted">
                                                Data manajemen pakan belum tersedia
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>



                            </table>
                        </div>
                    </div>

                    <!-- ===== FOOTER ===== -->
                    <div class="gap-2 card-body d-flex flex-column flex-md-row justify-content-between align-items-center">
                        <small class="text-muted">
                            @if ($data->count())
                                Menampilkan {{ $data->firstItem() }} – {{ $data->lastItem() }}
                                dari {{ $data->total() }} data
                            @else
                                Belum ada data
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

    <!--  MODAL TAMBAH TRANSAKSI -->
    <div class="modal fade" id="modalTransaksi" tabindex="-1">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">

                <form action="{{ route('pakan.store') }}" method="POST" novalidate>
                    @csrf

                    <div class="modal-header modal-header-clean">
                        <h6 class="mb-0 modal-title">
                            <i class="mr-1 la la-plus-circle text-muted"></i>

                            @if (auth()->user()->isAdmin())
                                Tambah Pakan
                            @else
                                Catat Pemakaian
                            @endif

                        </h6>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <div class="form-group">
                            <label>Jenis Transaksi</label>

                            <select name="jenis_transaksi" id="jenisTambah"
                                class="form-control @error('jenis_transaksi') is-invalid @enderror" required>

                                @if (auth()->user()->isAdmin())
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="masuk" {{ old('jenis_transaksi') == 'masuk' ? 'selected' : '' }}>
                                        Barang Masuk (Pembelian)
                                    </option>
                                    <option value="keluar" {{ old('jenis_transaksi') == 'keluar' ? 'selected' : '' }}>
                                        Barang Keluar (Pemakaian)
                                    </option>
                                @else
                                    <option value="keluar" selected>Barang Keluar (Pemakaian)</option>
                                @endif

                            </select>

                            @error('jenis_transaksi')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Tanggal</label>
                            <input type="date" name="tanggal" class="form-control"
                                value="{{ old('tanggal', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Nama Barang</label>

                            <select name="nama_barang" class="form-control @error('nama_barang') is-invalid @enderror"
                                required>

                                <option value="">-- Pilih Pakan --</option>

                                <option value="Jagung" {{ old('nama_barang') == 'Jagung' ? 'selected' : '' }}>
                                    Jagung
                                </option>

                                <option value="Katul" {{ old('nama_barang') == 'Katul' ? 'selected' : '' }}>
                                    Katul
                                </option>

                                <option value="Konsentrat" {{ old('nama_barang') == 'Konsentrat' ? 'selected' : '' }}>
                                    Konsentrat
                                </option>

                                <option value="Pakan Layer" {{ old('nama_barang') == 'Pakan Layer' ? 'selected' : '' }}>
                                    Pakan Layer
                                </option>

                                <option value="Pakan Starter"
                                    {{ old('nama_barang') == 'Pakan Starter' ? 'selected' : '' }}>
                                    Pakan Starter
                                </option>

                            </select>

                            @error('nama_barang')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>


                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Jumlah</label>
                                    <input type="number" name="jumlah" id="jumlahTambah" max="999999"
                                        min="1" inputmode="numeric" oninput="this.value=this.value.slice(0,6)"
                                        class="form-control @error('jumlah') is-invalid @enderror"
                                        value="{{ old('jumlah') }}" required>

                                    @error('jumlah')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror

                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Satuan</label>
                                    <input type="text" class="form-control" value="karung" readonly>
                                </div>

                                <input type="hidden" name="satuan" value="karung">
                            </div>
                            @if (auth()->user()->isAdmin())
                                <div class="col-md-4 harga-tambah">
                                    <div class="form-group">
                                        <label>Harga (Rp)</label>

                                        <input type="text" name="harga_satuan" id="hargaTambah"
                                            class="form-control @error('harga_satuan') is-invalid @enderror"
                                            inputmode="numeric" maxlength="12" value="{{ old('harga_satuan') }}">
                                        @error('harga_satuan')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            @endif

                        </div>

                        @if (auth()->user()->isAdmin())
                            <div class="form-group harga-tambah">
                                <label>Total Harga (Rp)</label>
                                <input type="text" id="totalTambah" class="form-control" readonly>
                            </div>
                        @endif


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

    <!--  MODAL EDIT TRANSAKSI  -->
    <div class="modal fade" id="modalEditTransaksi" tabindex="-1">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">

                <form method="POST" id="formEditTransaksi" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="modal-header modal-header-clean">
                        <h5 class="modal-title">
                            <i class="la la-edit"></i> Edit Pakan
                        </h5>
                        <button class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">

                        <div class="form-group">
                            <label>Jenis Transaksi</label>

                            <select name="jenis_transaksi" id="jenisEdit"
                                class="form-control @error('jenis_transaksi') is-invalid @enderror" required>

                                <option value="masuk">Barang Masuk (Pembelian)</option>
                                <option value="keluar">Barang Keluar (Pemakaian)</option>

                            </select>

                            @error('jenis_transaksi')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Tanggal</label>
                            <input type="date" name="tanggal" id="tanggalEdit" class="form-control"
                                max="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Nama Barang</label>
                            <select name="nama_barang" id="namaEdit" class="form-control" required>

                                <option value="Jagung">Jagung</option>
                                <option value="Katul">Katul</option>
                                <option value="Konsentrat">Konsentrat</option>
                                <option value="Pakan Layer">Pakan Layer</option>
                                <option value="Pakan Starter">Pakan Starter</option>

                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Jumlah</label>
                                    <input type="number" name="jumlah" id="jumlahEdit" max="999999" min="1"
                                        inputmode="numeric" oninput="this.value=this.value.slice(0,6)"
                                        class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Satuan</label>
                                    <input type="text" class="form-control" value="karung" readonly>
                                </div>

                                <input type="hidden" name="satuan" value="karung">
                            </div>
                            <div class="col-md-4 harga-edit">
                                <div class="form-group">
                                    <label>Harga Satuan (Rp)</label>
                                    <input type="text" name="harga_satuan" id="hargaEdit" class="form-control"
                                        inputmode="numeric" maxlength="12">
                                </div>
                            </div>
                        </div>

                        <div class="form-group harga-edit">
                            <label>Total Harga (Rp)</label>
                            <input type="text" id="totalEdit" class="form-control" readonly>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button class="text-white btn btn-warning">
                            <i class="la la-save"></i> Update
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <!--  MODAL HAPUS TRANSAKSI  -->
    <div class="modal fade" id="modalHapusTransaksi" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">

                <form method="POST" id="formHapusTransaksi">
                    @csrf
                    @method('DELETE')

                    <div class="modal-header modal-header-clean">
                        <h5 class="modal-title">
                            <i class="la la-trash"></i> Hapus Pakan
                        </h5>
                        <button class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="text-center modal-body">
                        <p class="mb-0">Yakin ingin menghapus transaksi ini?</p>
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
        const baseUrl = "{{ url('/pakan') }}";

        /* FORMAT RUPIAH  */
        function formatRupiah(angka) {

            angka = angka.replace(/[^,\d]/g, '').toString();
            let split = angka.split(',');
            let sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            return rupiah;

        }
function formatJumlah(angka){

    angka = parseFloat(angka);

    if(isNaN(angka)) return '';

    if(angka === 0) return '0';

    return angka.toString().replace(/\.?0+$/, '');

}
        /* TAMBAH  */
        function hitungTambah() {

            let j = $('#jumlahTambah').val() || 0;
            let h = $('#hargaTambah').val().replace(/\./g, '') || 0;

            $('#totalTambah').val(
                'Rp ' + (j * h).toLocaleString('id-ID')
            );

        }

        $('#jumlahTambah, #hargaTambah').on('input', hitungTambah);

        /* format harga tambah */
        $('#hargaTambah').on('keyup', function() {

            let angka = this.value.replace(/\./g, '');

            if (angka.length > 9) {
                angka = angka.slice(0, 9);
            }

            this.value = formatRupiah(angka);
            hitungTambah();

        });

        $('#jenisTambah').on('change', function() {

            if (this.value === 'keluar') {
                $('.harga-tambah').hide();
                $('#hargaTambah,#totalTambah').val('');
            } else {
                $('.harga-tambah').show();
            }

        });

        /* EDIT  */
        function hitungEdit() {

            let j = $('#jumlahEdit').val() || 0;
            let h = $('#hargaEdit').val().replace(/\./g, '') || 0;

            $('#totalEdit').val(
                'Rp ' + (j * h).toLocaleString('id-ID')
            );

        }

        /* realtime hitung */
        $('#jumlahEdit, #hargaEdit').on('input', hitungEdit);

        /* format harga edit */
        $('#hargaEdit').on('keyup', function() {

            let angka = this.value.replace(/\./g, '');

            if (angka.length > 9) {
                angka = angka.slice(0, 9);
            }

            this.value = formatRupiah(angka);
            hitungEdit();

        });

        /* perubahan jenis transaksi edit */
        $('#jenisEdit').on('change', function() {

            if (this.value === 'keluar') {
                $('.harga-edit').hide();
                $('#hargaEdit,#totalEdit').val('');
            } else {
                $('.harga-edit').show();
                hitungEdit();
            }

        });

        /* modal edit dibuka */
        $('#modalEditTransaksi').on('show.bs.modal', function(e) {

            let btn = $(e.relatedTarget);

            $('#formEditTransaksi').attr('action', baseUrl + '/' + btn.data('id'));
            $('#formEditTransaksi [name=nama_barang]').val(btn.data('nama'));
            $('#formEditTransaksi [name=jenis_transaksi]').val(btn.data('jenis'));
$('#formEditTransaksi [name=jumlah]').val(
    formatJumlah(btn.data('jumlah'))
);
let harga = btn.data('harga');

if (harga !== null && harga !== undefined) {
    harga = formatRupiah(harga.toString());
} else {
    harga = '';
}

$('#formEditTransaksi [name=harga_satuan]').val(harga);
            $('#formEditTransaksi [name=tanggal]').val(btn.data('tanggal'));

            $('#jenisEdit').trigger('change');
            hitungEdit();

        });

        /*  HAPUS  */
        $('#modalHapusTransaksi').on('show.bs.modal', function(e) {

            let btn = $(e.relatedTarget);
            $('#formHapusTransaksi').attr('action', baseUrl + '/' + btn.data('id'));

        });

        /*  FILTER  */
        let timer;

        $('input[name=search]').on('keyup', function() {

            clearTimeout(timer);

            timer = setTimeout(function() {
                $('#formFilter').submit();
            }, 500);

        });

        $('select[name=jenis],select[name=bulan],select[name=tahun],select[name=perPage]')
            .on('change', function() {

                $('#formFilter').submit();

            });
    </script>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: "{{ session('success') }}",
                timer: 2000,
                showConfirmButton: false
            });
        </script>
    @endif


    @if ($errors->any())
        <script>
            $(document).ready(function() {

                $('#modalTransaksi').modal('show');
                hitungTambah();

            });
        </script>
    @endif

@endsection

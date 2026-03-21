@extends('template')
@section('title', 'Populasi Ayam')

@section('content')
    <div class="main-panel">
        <div class="content">
            <div class="container-fluid">

                <!-- ===== TITLE ===== -->
                <div class="mb-4">
                    <h4 class="mb-1 fw-bold">Populasi Ayam</h4>
                </div>
                <div class="mb-0 row">
    @foreach ($kandang as $k)
        <div class="mb-2 col-md-3 col-sm-6">
            <div class="border-0 shadow-sm card">
                <div class="py-2 text-center card-body">

                    <div class="mb-1 fw-semibold text-dark">
                        {{ $k->nama_kandang }}
                    </div>



                    <div class="mt-1 h6 fw-bold text-dark">
                        {{ number_format($k->jumlah_ayam_terakhir, 0, ',', '.') }}
                    </div>

                </div>
            </div>
        </div>
    @endforeach
</div>
                <!-- ===== CARD ===== -->
                <div class="shadow-sm card table-card">

                    <!-- ===== FILTER BAR ===== -->
                    <form method="GET" action="{{ route('ayam') }}" id="formFilter">
                        <div class="card-body border-bottom">
                            <div class="row g-2 align-items-end">

                                <!-- SEARCH -->
                                <div class="col-md-3">
                                    <label class="small text-muted">Cari</label>
                                    <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                                        class="form-control form-control-sm" placeholder="Cari kandang/keterangan">
                                </div>

                                <!-- KANDANG -->
                                <div class="col-md-2">
                                    <label class="small text-muted">Kandang</label>
                                    <select name="kandang" id="filterKandang" class="form-control form-control-sm">
                                        <option value="">Semua</option>
                                        @foreach ($kandang as $k)
                                            <option value="{{ $k->id }}"
                                                {{ request('kandang') == $k->id ? 'selected' : '' }}>
                                                {{ $k->nama_kandang }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- BULAN -->
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

                                <!-- TAHUN -->
                                <div class="col-md-2">
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

                                <!-- PER PAGE -->
                                <div class="col-md-1">
                                    <label class="small text-muted">Data</label>
                                    <select name="perPage" onchange="this.form.submit()"
                                        class="form-control form-control-sm">
                                        <option value="10" {{ request('perPage') == 10 ? 'selected' : '' }}>10</option>
                                        <option value="25" {{ request('perPage') == 25 ? 'selected' : '' }}>25</option>
                                        <option value="50" {{ request('perPage') == 50 ? 'selected' : '' }}>50</option>
                                    </select>
                                </div>

                                <!-- TAMBAH -->
                                <div class="col-md-2 d-flex align-items-end justify-content-end">
                                    <button type="button" class="btn btn-primary btn-sm w-100" data-toggle="modal"
                                        data-target="#modalTambah">
                                        <i class="la la-plus"></i> Tambah
                                    </button>
                                </div>

                            </div>
                        </div>
                    </form>




                    <!-- ===== TABLE ===== -->
                    <div class="p-0 card-body">
                        <div class="table-responsive">
                            <table class="table mb-0 table-standard">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="50">No</th>
                                        <th>Nama Kandang</th>
                                        <th>Jumlah</th>
                                        <th>Jenis Mutasi</th>
                                        <th>Keterangan</th>
                                        <th>Tanggal Update</th>
                                        <th class="text-center" width="120">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($data as $item)
                                        <tr>
                                            <td class="text-center">
                                                {{ $data->firstItem() + $loop->index }}
                                            </td>

                                            <td>{{ $item->kandang->nama_kandang ?? '-' }}</td>

                                            <td>{{ number_format($item->jumlah, 0, ',', '.') }}</td>

                                    <td>
    {{ ucfirst($item->jenis_mutasi ?? '-') }}
</td>

                                            <td>{{ $item->keterangan ?? '-' }}</td>

                                            <td>
                                                {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}
                                            </td>

                                            <td class="text-center">
                                                <button class="btn btn-warning btn-sm" data-toggle="modal"
                                                    data-target="#modalEdit" data-id="{{ $item->id }}"
                                                    data-kandang="{{ $item->kandang_id }}"
                                                    data-jumlah="{{ $item->jumlah }}"
                                                    data-jenis="{{ $item->jenis_mutasi }}"
                                                    data-keterangan="{{ $item->keterangan }}"
                                                    data-tanggal="{{ $item->tanggal }}">
                                                    <i class="la la-edit"></i>
                                                </button>

                                                @auth
                                                    @if (auth()->user()->isAdmin())
                                                        <button class="btn btn-danger btn-sm" data-toggle="modal"
                                                            data-target="#modalHapus" data-id="{{ $item->id }}">
                                                            <i class="la la-trash"></i>
                                                        </button>
                                                    @endif
                                                @endauth
                                            </td>
                                        </tr>

                                    @empty
                                        <tr>
                                            <td colspan="7" class="py-4 text-center text-muted">
                                                Data populasi ayam belum tersedia
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ===== FOOTER / PAGINATION ===== -->
                    <div class="gap-2 card-body d-flex flex-column flex-md-row justify-content-between align-items-center">

                        <small class="text-muted">
                            @if ($data->count())
                                Menampilkan {{ $data->firstItem() }} – {{ $data->lastItem() }}
                                dari {{ $data->total() }} data
                            @else
                                Tidak ada data populasi ayam
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

    <!-- ================= MODAL TAMBAH ================= -->
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('ayam.store') }}" method="POST">
                    @csrf

                    <div class="modal-header modal-header-clean">
                        <h6 class="mb-0 modal-title">
                            <i class="mr-1 la la-plus-circle text-muted"></i>
                            Tambah Data Ayam
                        </h6>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        {{-- ================= JENIS MUTASI ================= --}}
                        <div class="p-3 mb-3 border rounded bg-light">
                            <label class="fw-bold">1. Pilih Jenis Mutasi</label>
                            <select name="jenis_mutasi" id="jenisMutasi"
                                class="form-control mt-2 @error('jenis_mutasi') is-invalid @enderror">
                                <option value="">-- Pilih Jenis Mutasi --</option>
                                <option value="masuk">Masuk (DOC)</option>
                                <option value="mati">Mati</option>
                                <option value="afkir">Afkir</option>
                                <option value="pindah">Pindah Kandang</option>
                            </select>
                        </div>


                        {{-- ================= MUTASI BIASA ================= --}}
                        <div id="groupKandang" class="p-3 mb-3 border rounded">
                            <label class="fw-bold">2. Detail Mutasi</label>

                            <div class="mt-2 form-group">
                                <label>Nama Kandang</label>
                                <select name="kandang_id" class="form-control">
                                    <option value="">-- Pilih Kandang --</option>
                                    @foreach ($kandang as $k)
                                        <option value="{{ $k->id }}">
                                            {{ $k->nama_kandang }}
                                            (Stok: {{ $k->jumlah_ayam_terakhir }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        {{-- ================= PINDAH ================= --}}
                        <div id="groupPindah" class="p-3 mb-4 border rounded-3 bg-light" style="display:none;">
                            <label class="fw-bold">2. Detail Pindah Kandang</label>

                            <div class="mt-2 form-group">
                                <label>Kandang Asal</label>
                                <select name="kandang_asal_id" class="form-control">
                                    <option value="">-- Pilih Kandang Asal --</option>
                                    @foreach ($kandang as $k)
                                        <option value="{{ $k->id }}">
                                            {{ $k->nama_kandang }}
                                            (Stok: {{ $k->jumlah_ayam_terakhir }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Kandang Tujuan</label>
                                <select name="kandang_tujuan_id" class="form-control">
                                    <option value="">-- Pilih Kandang Tujuan --</option>
                                    @foreach ($kandang as $k)
                                        <option value="{{ $k->id }}">{{ $k->nama_kandang }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        {{-- ================= JUMLAH & TANGGAL ================= --}}
                        <div class="p-3 mb-3 border rounded">
                            <label class="fw-bold">3. Informasi Tambahan</label>

                            <div class="mt-2 form-group">
                                <label>Jumlah Ayam</label>
                                {{-- <input type="number" name="jumlah" class="form-control"> --}}
                                <input type="number" name="jumlah" max="9999" min="1" inputmode="numeric"
                                    oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,4)"
                                    class="form-control @error('jumlah') is-invalid @enderror"
                                    value="{{ old('jumlah') }}">
                            </div>

                            <div class="form-group">
                                <label>Tanggal</label>
                                <input type="date" name="tanggal" class="form-control" max="{{ date('Y-m-d') }}">
                            </div>

                            <div class="form-group">
                                <label>Keterangan</label>
                                <textarea name="keterangan" class="form-control" rows="2"></textarea>
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

    <!-- ================= MODAL EDIT ================= -->
    <div class="modal fade" id="modalEdit" tabindex="-1">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" id="formEdit">
                    @csrf
                    @method('PUT')

                    <div class="modal-header modal-header-clean">
                        <h5 class="modal-title">
                            <i class="la la-edit"></i> Edit Data Ayam
                        </h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Kandang</label>
                            <select name="kandang_id" class="form-control @error('kandang_id') is-invalid @enderror">
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
                            <label>Jumlah Ayam</label>
                            {{-- <input type="number" name="jumlah"
       value="{{ old('jumlah') }}"
       class="form-control @error('jumlah') is-invalid @enderror"> --}}
                            <input type="number" name="jumlah" max="9999" min="1" inputmode="numeric"
                                oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,4)"
                                value="{{ old('jumlah') }}" class="form-control @error('jumlah') is-invalid @enderror">

                            @error('jumlah')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Jenis Mutasi</label>
                            <select name="jenis_mutasi" class="form-control @error('jenis_mutasi') is-invalid @enderror">

                                <option value="masuk" {{ old('jenis_mutasi') == 'masuk' ? 'selected' : '' }}>Masuk</option>
                                <option value="mati" {{ old('jenis_mutasi') == 'mati' ? 'selected' : '' }}>Mati</option>
                                <option value="afkir" {{ old('jenis_mutasi') == 'afkir' ? 'selected' : '' }}>Afkir</option>
                            </select>

                            @error('jenis_mutasi')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Tanggal Update</label>
                            <input type="date" name="tanggal" value="{{ old('tanggal') }}"
                                max="{{ date('Y-m-d') }}" class="form-control @error('tanggal') is-invalid @enderror">

                            @error('tanggal')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Keterangan</label>
                            <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror">{{ old('keterangan') }}</textarea>

                            @error('keterangan')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
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

    <!-- ================= MODAL HAPUS ================= -->
    <div class="modal fade" id="modalHapus" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" id="formHapus">
                    @csrf
                    @method('DELETE')

                    <div class="modal-header modal-header-clean">
                        <h6 class="modal-title">
                            <i class="la la-trash"></i> Hapus Data Ayam
                        </h6>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="text-center modal-body">
                        <p class="mb-0">Yakin ingin menghapus data populasi ayam ini?</p>
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
const baseUrl = "{{ route('ayam') }}";
        $(document).ready(function() {
            /* =============================
       HANDLE MODAL EDIT NORMAL
    ============================= */
            $('#modalEdit').on('show.bs.modal', function(e) {

                // Jika ada error validation (mode old input)
                @if (session('edit_id'))
                    return; // hentikan supaya tidak ditimpa data tombol
                @endif

                let btn = $(e.relatedTarget);
                let id = btn.data('id');

                $('#formEdit').attr('action', baseUrl + '/' + id);

                $('#formEdit [name=kandang_id]').val(btn.data('kandang'));
                $('#formEdit [name=jumlah]').val(btn.data('jumlah'));
                $('#formEdit [name=jenis_mutasi]').val(btn.data('jenis'));
                $('#formEdit [name=tanggal]').val(btn.data('tanggal'));
                $('#formEdit [name=keterangan]').val(btn.data('keterangan'));
            });

            /* =============================
               HANDLE EDIT MODAL (JIKA ERROR)
            ============================== */
            @if (session('edit_id'))

                $('#formEdit').attr('action', baseUrl + '/{{ session('edit_id') }}');

                $('#formEdit [name=kandang_id]').val("{{ old('kandang_id') }}");
                $('#formEdit [name=jumlah]').val("{{ old('jumlah') }}");
                $('#formEdit [name=jenis_mutasi]').val("{{ old('jenis_mutasi') }}");
                $('#formEdit [name=tanggal]').val("{{ old('tanggal') }}");
                $('#formEdit [name=keterangan]').val(@json(old('keterangan')));

                $('#modalEdit').modal('show');
            @elseif ($errors->any())

                $('#modalTambah').modal('show');
            @endif


            /* =============================
               HANDLE MODAL HAPUS
            ============================== */
            $('#modalHapus').on('show.bs.modal', function(e) {
                let btn = $(e.relatedTarget);
                $('#formHapus').attr('action', baseUrl + '/' + btn.data('id'));
            });


            /* =============================
               AUTO FILTER
            ============================== */
            let timer;

            $('#searchInput').on('keyup', function() {
                clearTimeout(timer);
                timer = setTimeout(() => {
                    $('#formFilter').submit();
                }, 400);
            });

            $('#filterKandang, #filterBulan, #filterTahun').on('change', function() {
                $('#formFilter').submit();
            });


            /* =============================
               TOGGLE PINDAH
            ============================== */
            if ($('#jenisMutasi').length) {
                $('#jenisMutasi').on('change', function() {

                    if ($(this).val() === 'pindah') {
                        $('#groupKandang').slideUp(200);
                        $('#groupPindah').slideDown(200);
                    } else {
                        $('#groupPindah').slideUp(200);
                        $('#groupKandang').slideDown(200);
                    }

                });
            }

        });
    </script>


    {{-- SWEET ALERT SUCCESS --}}
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

@endsection

@extends('template')
@section('title', 'Laporan')

@section('content')
    <div class="main-panel">
        <div class="content">
            <div class="container-fluid">
                <div class="mb-4">
                    <h4 class="mb-1 fw-bold">Laporan Tahunan</h4>
                </div>
                <div class="shadow-sm card">
                    <!--  FILTER TAHUN  -->
                    <div class="card-body border-bottom">
                        <form method="GET">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h6 class="mb-0 fw-bold">Tahun Laporan</h6>
                                    <select name="tahun" class="w-auto mt-1 form-select form-select-sm"
                                        onchange="this.form.submit()">
                                        @for ($t = now()->year - 3; $t <= now()->year + 1; $t++)
                                            <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>
                                                {{ $t }}
                                            </option>
                                        @endfor
                                    </select>
                                    <small class="mt-1 text-muted d-block">
                                        Menampilkan data laporan tahun {{ $tahun }}
                                    </small>
                                </div>
                                <div class="mt-3 col-md-6 text-end mt-md-0">
                                    <a href="{{ route('laporan.export', ['tahun' => $tahun]) }}"
                                        class="btn btn-primary btn-sm">
                                        <i class="la la-download"></i> Unduh Laporan
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!--  TAB NAV  -->
                    <ul class="px-3 pt-3 nav nav-tabs fw-semibold">
                        <li class="nav-item">
                            <a class="nav-link {{ request('tab', 'produksi') === 'produksi' ? 'active' : '' }}"
                                data-toggle="tab" href="#lapProduksi">
                                Produksi Telur
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request('tab') === 'inventori' ? 'active' : '' }}" data-toggle="tab"
                                href="#lapOperasional">
                                Pakan
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request('tab') === 'keuangan' ? 'active' : '' }}" data-toggle="tab"
                                href="#lapKeuangan">
                                Keuangan
                            </a>
                        </li>
                    </ul>


                    <div class="tab-content">

                        <!--  PRODUKSI -->
                        <div class="tab-pane fade {{ request('tab', 'produksi') === 'produksi' ? 'show active' : '' }}"
                            id="lapProduksi">
                            <div class="card-body">
                                @php
                                    $bulanProduksi = request('bulan_produksi', 1);
                                @endphp

                                <form method="GET" class="mb-3">
                                    <input type="hidden" name="tahun" value="{{ $tahun }}">
                                    <input type="hidden" name="tab" value="produksi">
                                    <div class="row align-items-center">
                                        <div class="col-md-4">
                                            <label class="fw-semibold">Bulan</label>
                                            <select name="bulan_produksi" class="form-select form-select-sm"
                                                onchange="this.form.submit()">
                                                @for ($b = 1; $b <= 12; $b++)
                                                    <option value="{{ $b }}"
                                                        {{ $bulanProduksi == $b ? 'selected' : '' }}>
                                                        {{ \Carbon\Carbon::createFromDate(null, $b, 1)->translatedFormat('F') }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                </form>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Minggu</th>
                                                <th class="text-end">Total Produksi (Kg)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $dataProduksi = $produksi->where('bulan', $bulanProduksi);
                                                $totalProduksi = 0;
                                            @endphp

                                            @foreach ($dataProduksi as $row)
                                                <tr class="{{ $loop->odd ? 'table-secondary' : '' }}">
                                                    <td>Minggu {{ $row->minggu }}</td>
                                                    <td class="text-end">{{ number_format($row->total_produksi) }}</td>
                                                </tr>
                                                @php $totalProduksi += $row->total_produksi; @endphp
                                            @endforeach

                                            <tr class="fw-bold table-light">
                                                <td>
                                                    Total
                                                    {{ \Carbon\Carbon::createFromDate(null, $bulanProduksi, 1)->translatedFormat('F') }}
                                                </td>
                                                <td class="text-end">{{ number_format($totalProduksi) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <div class="mt-3">
                                        <small class="text-muted d-block">
                                            Total Produksi Bulan:
                                            <strong>{{ number_format($totalProduksi, 0, ',', '.') }} Kg</strong>
                                        </small>

                                        <small class="text-muted d-block">
                                            Rata-rata Produksi Mingguan:
                                            <strong>
                                                {{ $dataProduksi->count() > 0 ? number_format($totalProduksi / $dataProduksi->count(), 1, ',', '.') : 0 }}
                                                Kg
                                            </strong>
                                        </small>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <!--  INVENTORI -->
                        <div class="tab-pane fade {{ request('tab') === 'inventori' ? 'show active' : '' }}"
                            id="lapOperasional">
                            <div class="card-body">
                                @php
                                    $bulanInventori = request('bulan_inventori', 1);
                                @endphp

                                <form method="GET" class="mb-3">
                                    <input type="hidden" name="tahun" value="{{ $tahun }}">
                                    <input type="hidden" name="tab" value="inventori">
                                    <div class="row align-items-center">
                                        <div class="col-md-4">
                                            <label class="fw-semibold">Bulan</label>
                                            <select name="bulan_inventori" class="form-select form-select-sm"
                                                onchange="this.form.submit()">
                                                @for ($b = 1; $b <= 12; $b++)
                                                    <option value="{{ $b }}"
                                                        {{ $bulanInventori == $b ? 'selected' : '' }}>
                                                        {{ \Carbon\Carbon::createFromDate(null, $b, 1)->translatedFormat('F') }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                </form>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Minggu</th>
                                                <th class="text-end">Masuk (Kg)</th>
                                                <th class="text-end">Keluar (Kg)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $dataInventori = $inventori->where('bulan', $bulanInventori);
                                                $totalMasuk = 0;
                                                $totalKeluar = 0;
                                            @endphp

                                            @foreach ($dataInventori as $row)
                                                <tr class="{{ $loop->odd ? 'table-secondary' : '' }}">
                                                    <td>Minggu {{ $row->minggu }}</td>
                                                    <td class="text-end">{{ number_format($row->masuk) }}</td>
                                                    <td class="text-end">{{ number_format($row->keluar) }}</td>
                                                </tr>
                                                @php
                                                    $totalMasuk += $row->masuk;
                                                    $totalKeluar += $row->keluar;
                                                @endphp
                                            @endforeach

                                            <tr class="fw-bold table-light">
                                                <td>
                                                    Total
                                                    {{ \Carbon\Carbon::createFromDate(null, $bulanInventori, 1)->translatedFormat('F') }}
                                                </td>
                                                <td class="text-end">{{ number_format($totalMasuk) }}</td>
                                                <td class="text-end">{{ number_format($totalKeluar) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mt-3">
                                    <small class="text-muted d-block">
                                        Total Masuk Bulan:
                                        <strong>{{ number_format($totalMasuk) }} Kg</strong>
                                    </small>

                                    <small class="text-muted d-block">
                                        Total Keluar Bulan:
                                        <strong>{{ number_format($totalKeluar) }} Kg</strong>
                                    </small>

                                    <small class="text-muted d-block">
                                        Rata-rata Masuk Mingguan:
                                        <strong>
                                            {{ $dataInventori->count() > 0 ? number_format($totalMasuk / $dataInventori->count(), 1, ',', '.') : 0 }}
                                            Kg
                                        </strong>
                                    </small>

                                    <small class="text-muted d-block">
                                        Rata-rata Keluar Mingguan:
                                        <strong>
                                            {{ $dataInventori->count() > 0 ? number_format($totalKeluar / $dataInventori->count(), 1, ',', '.') : 0 }}
                                            Kg
                                        </strong>
                                    </small>

                                </div>

                            </div>
                        </div>


                        <!--  KEUANGAN -->
                        <div class="tab-pane fade {{ request('tab') === 'keuangan' ? 'show active' : '' }}"
                            id="lapKeuangan">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Bulan</th>
                                                <th class="text-end">Pemasukan</th>
                                                <th class="text-end">Pembelian</th>
                                                <th class="text-end">Gaji</th>
                                                <th class="text-end">Laba / Rugi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @for ($b = 1; $b <= 12; $b++)
                                                @php
                                                    $pemasukan =
                                                        $keuangan['pemasukan']->firstWhere('bulan', $b)->total ?? 0;
                                                    $inventori =
                                                        $keuangan['inventori']->firstWhere('bulan', $b)->total ?? 0;
                                                    $gaji = $keuangan['gaji']->firstWhere('bulan', $b)->total ?? 0;
                                                    $laba = $pemasukan - ($inventori + $gaji);
                                                @endphp
                                                <tr class="{{ $b % 2 == 1 ? 'table-secondary' : '' }}">
                                                    <td>
                                                        {{ \Carbon\Carbon::createFromDate(null, $b, 1)->locale('id')->translatedFormat('F') }}
                                                    </td>
                                                    <td class="text-end">
                                                        Rp {{ number_format($pemasukan, 0, ',', '.') }}
                                                    </td>
                                                    <td class="text-end">
                                                        Rp {{ number_format($inventori, 0, ',', '.') }}
                                                    </td>
                                                    <td class="text-end">
                                                        Rp {{ number_format($gaji, 0, ',', '.') }}
                                                    </td>
                                                    <td class="text-end fw-semibold {{ $laba < 0 ? 'text-danger' : '' }}">
                                                        Rp {{ number_format($laba, 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                            @endfor
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

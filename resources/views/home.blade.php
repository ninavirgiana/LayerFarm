@extends('template')
@section('title', 'Beranda')

@section('content')
    <div class="main-panel">
        <div class="content">
            <div class="container-fluid">
                <div class="mb-4">
                    <h4 class="mb-1 fw-semibold">
                        Hi, selamat datang kembali
                    </h4>
                    <small class="text-muted">
                        {{ now()->translatedFormat('l, d F Y') }} —
                        Login sebagai {{ auth()->user()->isAdmin() ? 'Admin' : 'Karyawan' }}
                    </small>
                </div>
                <div class="section">
                    <div class="row g-4">

                        <!--  Jumlah Ayam  -->
                        <div class="col-xl-3 col-lg-6 col-md-6">
                            <div class="border-0 shadow-sm card rounded-3 h-100">
                                <div class="card-body d-flex flex-column justify-content-between">

                                    <div class="gap-3 d-flex align-items-center">
                                        <div class="flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center"
                                            style="width:52px;height:52px;background:#fef3c7;">
                                            <i class="la la-paw fs-4 text-warning"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">Jumlah Ayam</small>
                                            <div class="fs-5 fw-semibold">
                                                {{ number_format($jumlahAyam ?? 0, 0, ',', '.') }} Ekor
                                            </div>
                                        </div>
                                    </div>

                                    <small class="text-muted" style="min-height:20px">
                                        Total populasi ayam aktif
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!--  Produksi Hari Ini  -->
                        <div class="col-xl-3 col-lg-6 col-md-6">
                            <div class="border-0 shadow-sm card rounded-3 h-100">
                                <div class="card-body d-flex flex-column justify-content-between">

                                    <div class="gap-3 d-flex align-items-center">
                                        <div class="flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center"
                                            style="width:52px;height:52px;background:#fef3c7;">
                                            <i class="la la-egg fs-4 text-warning"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">Produksi Hari Ini</small>
                                            <div class="fs-5 fw-semibold">
                                                {{ number_format($produksiHariIni ?? 0, 1, ',', '.') }} Kg
                                            </div>
                                        </div>
                                    </div>

                                    <small class="text-muted" style="min-height:20px">
                                        Akumulasi produksi hari ini
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!--  Stok Pakan  -->
                        <div class="col-xl-3 col-lg-6 col-md-6">
                            <div class="border-0 shadow-sm card rounded-3 h-100">
                                <div class="card-body d-flex flex-column justify-content-between">

                                    <div class="gap-3 d-flex align-items-center">
                                        <div class="flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center"
                                            style="width:52px;height:52px;background:#fef3c7;">
                                            <i class="la la-cubes fs-4 text-warning"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">Stok Pakan</small>
                                            <div class="fs-5 fw-semibold">
                                                {{ number_format($stokPakan ?? 0, 1, ',', '.') }} Karung
                                            </div>
                                        </div>
                                    </div>

                                    <small style="min-height:20px"
                                        class="{{ isset($stokMenipis) && $stokMenipis->count() ? 'text-warning' : 'text-muted' }}">
                                        @if (isset($stokMenipis) && $stokMenipis->count())
                                            ⚠ Stok menipis: {{ $stokMenipis->pluck('nama_barang')->join(', ') }}
                                        @else
                                            Stok dalam batas aman
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!--  Pendapatan -->
                        <div class="col-xl-3 col-lg-6 col-md-6">
                            <div class="border-0 shadow-sm card rounded-3 h-100">
                                <div class="card-body d-flex flex-column justify-content-between">

                                    <div class="gap-3 d-flex align-items-center">
                                        <div class="flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center"
                                            style="width:52px;height:52px;background:#fef3c7;">
                                            <i class="la la-wallet fs-4 text-warning"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">Pendapatan Hari Ini</small>
                                            <div class="fs-5 fw-semibold">
                                                Rp {{ number_format($pendapatanHariIni ?? 0, 0, ',', '.') }}
                                            </div>
                                        </div>
                                    </div>

                                    <small class="text-muted" style="min-height:20px">
                                        Total transaksi hari ini
                                    </small>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!--  CHART SECTION  -->
                <div class="section">
                    <div class="row g-4">
                        <!-- Produksi Telur Mingguan -->
                        <div class="{{ auth()->user()->isAdmin() ? 'col-lg-6' : 'col-lg-8 mx-auto' }}">
                            <div class="border-0 shadow-sm card rounded-1 h-100">
                                <div class="card-body">
                                    <div class="mb-3 d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0 fw-semibold">Produksi Telur</h6>
                                            <small class="text-muted">Harian dalam periode terpilih</small>
                                        </div>

                                        <form method="GET" class="gap-2 d-flex align-items-center">
                                            <select name="bulan" class="border-0 shadow-sm form-select form-select-sm"
                                                style="min-width:110px" onchange="this.form.submit()">
                                                @for ($i = 1; $i <= 12; $i++)
                                                    <option value="{{ $i }}"
                                                        {{ request('bulan', now()->month) == $i ? 'selected' : '' }}>
                                                        {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                                                    </option>
                                                @endfor
                                            </select>

                                            <select name="minggu" class="border-0 shadow-sm form-select form-select-sm"
                                                onchange="this.form.submit()">
                                                <option value="">All</option>
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <option value="{{ $i }}"
                                                        {{ request('minggu') == $i ? 'selected' : '' }}>
                                                        W{{ $i }}
                                                    </option>
                                                @endfor
                                            </select>

                                            <input type="hidden" name="tahun"
                                                value="{{ request('tahun', now()->year) }}">
                                        </form>
                                    </div>

                                    <!-- CHART -->
                                    <div style="height:260px">
                                        <canvas id="eggChart"></canvas>
                                    </div>

                                </div>
                            </div>
                        </div>


                        <!-- Ringkasan Keuangan -->
                        @auth
                            @if (auth()->user()->isAdmin())
                                <div class="col-lg-6">
                                    <div class="border-0 shadow-sm card rounded-1 h-100">
                                        <div class="card-body">

                                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-0 fw-semibold">Total Keuangan</h6>
                                                    <small class="text-muted">Mingguan</small>
                                                </div>

                                                <form method="GET">
                                                    <select name="bulan" class="border-0 shadow-sm form-select form-select-sm"
                                                        onchange="this.form.submit()">
                                                        @for ($i = 1; $i <= 12; $i++)
                                                            <option value="{{ $i }}"
                                                                {{ request('bulan', now()->month) == $i ? 'selected' : '' }}>
                                                                {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                                                            </option>
                                                        @endfor
                                                    </select>
                                                </form>
                                            </div>

                                            <!-- CHART -->
                                            <div style="height:260px">
                                                <canvas id="financeChart"></canvas>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            @endif


                        @endauth


                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('scripts')

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        /* PRODUKSI TELUR */
        new Chart(document.getElementById('eggChart'), {
            type: 'line',
            data: {
                labels: @json($chartProduksiLabels ?? []),
                datasets: [{
                    data: @json($chartProduksiData ?? []),
                    borderColor: 'rgba(59,130,246,1)',
                    backgroundColor: 'rgba(59,130,246,0.08)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });


        /* KEUANGAN */
        @if (auth()->user()->isAdmin())

            new Chart(document.getElementById('financeChart'), {
                type: 'line',
                data: {
                    labels: @json($chartFinanceLabels ?? []),
                    datasets: [{
                            label: 'Pemasukan',
                            data: @json($chartFinancePemasukan ?? []),
                            borderColor: 'rgba(34,197,94,1)',
                            backgroundColor: 'rgba(34,197,94,0.08)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Pengeluaran',
                            data: @json($chartFinancePengeluaran ?? []),
                            borderColor: 'rgba(239,68,68,1)',
                            backgroundColor: 'rgba(239,68,68,0.08)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        @endif
    </script>

@endsection

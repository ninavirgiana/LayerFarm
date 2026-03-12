<html>

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<title>LayerFarm | @yield('title')</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no'
        name='viewport' />
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/ready.css">
    <link rel="stylesheet" href="assets/css/demo.css">
    <link rel="stylesheet" href="assets/css/custom.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/line-awesome/1.3.0/line-awesome/css/line-awesome.min.css">

</head>

<body>
    <div class="wrapper">
        <div class="main-header">
            <div class="logo-header">
                {{-- <a href="{{ route('beranda') }}" class="logo">
                    Ready Dashboard
                </a> --}}
                <a href="{{ route('beranda') }}" class="logo d-flex align-items-center">
    <img src="{{ asset('assets/img/ayam.png') }}" class="dashboard-logo" alt="Logo">
</a>

                <button class="ml-auto navbar-toggler sidenav-toggler" type="button" data-toggle="collapse"
                    data-target="collapse" aria-controls="sidebar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <button class="topbar-toggler more"><i class="la la-ellipsis-v"></i></button>
            </div>
            <nav class="navbar navbar-header navbar-expand-lg">
                <div class="container-fluid">



                    <ul class="navbar-nav topbar-nav ml-md-auto align-items-center">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="{{ route('profil') }}"
                                id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <div class="mr-2 border shadow-sm d-flex align-items-center justify-content-center rounded-circle bg-light"
                                    style="width:36px;height:36px;">
                                    <i class="la la-user text-secondary" style="font-size:18px;"></i>

                                </div>

                                <span
                                    class="d-none d-sm-inline font-weight-bold text-dark">{{ auth()->user()->name }}</span>
                            </a>
                            <div class="shadow dropdown-menu dropdown-menu-right animated--fade-in"
                                aria-labelledby="userDropdown">

                                <!-- Profil → sesuai route CRUD baru -->
                                <a class="dropdown-item" href="{{ route('profil') }}">
                                    <i class="mr-2 ti-user text-primary"></i> Profil
                                </a>

                                <div class="dropdown-divider"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger"
                                        style="background: none; border: none; width: 100%; text-align: left;">
                                        <i class="mr-2 fa fa-power-off"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </li>
                    </ul>


                </div>
            </nav>
        </div>

        <div class="sidebar">
            <div class="sidebar-wrapper">
                <ul class="nav">
                    {{-- Beranda, semua user --}}
                    <li class="nav-item {{ Route::is('beranda') ? 'active' : '' }}">
                        <a href="{{ route('beranda') }}">
                            <i class="la la-home"></i>
                            <p>Beranda</p>
                        </a>
                    </li>

                    {{-- Menu Admin --}}
                    @if (auth()->user()->role === 'admin')
                        {{-- Manajemen Karyawan --}}
                        <li class="nav-item">
                            <a data-toggle="collapse" href="#karyawanMenu" aria-expanded="false">
                                <i class="la la-user-friends"></i>
                                <p>Karyawan</p>
                                <span class="caret"></span>
                            </a>
                            <div class="collapse" id="karyawanMenu">
                                <ul class="nav nav-collapse">
                                    <li class="{{ Route::is('karyawan') ? 'active' : '' }}">
                                        <a href="{{ route('karyawan') }}">
                                            <span class="sub-item">Data Karyawan</span>
                                        </a>
                                    </li>
                                    <li class="{{ Route::is('gaji') ? 'active' : '' }}">
                                        <a href="{{ route('gaji') }}">
                                            <span class="sub-item">Gaji Karyawan</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @endif

                    {{-- Operasional (semua user) --}}
                    <li class="nav-item">
                        <a data-toggle="collapse" href="#inventoriMenu" aria-expanded="false">
                            <i class="la la-box"></i>
                            <p>Manajemen Kandang</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse" id="inventoriMenu">
                            <ul class="nav nav-collapse">
                                <li>
                                    <a href="{{ route('pakan') }}">
                                        <span class="sub-item">Manajemen Pakan</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('ayam') }}">
                                        <span class="sub-item">Populasi Ayam</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    {{-- <li class="nav-item {{ request()->routeIs('pakan') ? 'active' : '' }}">
    <a href="{{ route('pakan') }}">
        <i class="la la-box"></i>
        <p>Gudang</p>
    </a>
</li> --}}


                    {{-- Manajemen Produksi (semua user) --}}
                    <li class="nav-item {{ Route::is('hasil') ? 'active' : '' }}">
                        <a href="{{ route('hasil') }}">
                            <i class="la la-chart-bar"></i>
                            <p>Produksi Telur</p>
                        </a>
                    </li>

                    {{-- Manajemen Penjualan --}}
                    @if (auth()->user()->role === 'admin')
                        <li class="nav-item {{ Route::is('keuangan.index') ? 'active' : '' }}">
                            <a href="{{ route('keuangan.index') }}">
                                <i class="la la-shopping-cart"></i>
                                <p>Penjualan</p>
                            </a>
                        </li>
                    @elseif(auth()->user()->role === 'karyawan')
                        <li class="nav-item {{ Route::is('keuangan.index') ? 'active' : '' }}">
                            <a href="{{ route('keuangan.index') }}">
                                <i class="la la-shopping-cart"></i>
                                <p>Penjualan</p>
                            </a>
                        </li>
                    @endif

                    {{-- Laporan (Admin only) --}}
                    @if (auth()->user()->role === 'admin')
                        <li class="nav-item {{ Route::is('laporan') ? 'active' : '' }}">
                            <a href="{{ route('laporan') }}">
                                <i class="la la-file-alt"></i>
                                <p>Laporan</p>
                            </a>
                        </li>
                    @endif
                    {{-- <li class="nav-item nav-logout">
                        <a href="#"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="la la-sign-out"></i>
                            <p>Logout</p>
                        </a>

                        <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display:none;">
                            @csrf
                        </form>
                    </li> --}}


<li class="nav-item nav-item-logout">
    <a href="#"
        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="la la-sign-out"></i>
        <p>Logout</p>
    </a>

    <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display:none;">
        @csrf
    </form>
</li>
                </ul>
                {{-- <li class="nav-item nav-item-logout">
    <a href="#"
        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="la la-sign-out"></i>
        <p>Logout</p>
    </a>

    <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display:none;">
        @csrf
    </form>
</li> --}}




            </div>
        </div>

        @yield('content')

        <script src="assets/js/core/jquery.3.2.1.min.js"></script>
        <script src="assets/js/core/popper.min.js"></script>
        <script src="assets/js/core/bootstrap.min.js"></script>
        <script src="assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
        <script src="assets/js/plugin/chartist/chartist.min.js"></script>
        <script src="assets/js/plugin/chartist/plugin/chartist-plugin-tooltip.min.js"></script>
        <script src="assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>
        <script src="assets/js/plugin/bootstrap-toggle/bootstrap-toggle.min.js"></script>
        <script src="assets/js/plugin/jquery-mapael/jquery.mapael.min.js"></script>
        <script src="assets/js/plugin/jquery-mapael/maps/world_countries.min.js"></script>
        <script src="assets/js/plugin/chart-circle/circles.min.js"></script>
        <script src="assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
        <script src="assets/js/ready.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


        @yield('scripts')

</body>

</html>

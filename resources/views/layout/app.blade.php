<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Disperindag - @yield('title')</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    {{-- Favicons --}}
    {{-- <link href="{{ asset('assets/img/Kerawang.png') }}" rel="icon">
    <link href="{{ asset('assets/img/Kerawang.png') }}" rel="apple-touch-icon"> --}}

    {{-- Google Fonts --}}
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    {{-- Vendor CSS Files --}}
    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/quill/quill.snow.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/quill/quill.bubble.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/simple-datatables/style.css') }}" rel="stylesheet">

    {{-- Template Main CSS File --}}
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
</head>

<body>

    {{-- ======= Header ======= --}}
    <header id="header" class="header fixed-top d-flex align-items-center">
        <div class="d-flex align-items-center justify-content-between">
            <a href="{{ url('/') }}" class="logo d-flex align-items-center">
                <img src="{{ asset('assets/img/Kerawang.png') }}" alt="">
                <span class="d-none d-lg-block">Disperindag</span>
            </a>
            <i class="bi bi-list toggle-sidebar-btn"></i>
        </div>

        <nav class="header-nav ms-auto">
            <ul class="d-flex align-items-center">

                <li class="nav-item d-block d-lg-none">
                    <a class="nav-link nav-icon search-bar-toggle " href="#">
                        <i class="bi bi-search"></i>
                    </a>
                </li>

                {{-- ======= Profile Nav ======= --}}
                <li class="nav-item dropdown pe-3">
                    <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                        <span class="d-none d-md-block dropdown-toggle ps-2">{{ Auth::user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                        <li class="dropdown-header">
                            <h6>{{ Auth::user()->name }}</h6>
                            <span>{{ Auth::user()->role }}</span>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        {{-- Tautan untuk Pengaturan Akun, disesuaikan untuk peran user --}}
                        @if(Auth::user()->role === 'admin')
                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.settings.index') }}">
                                    <i class="bi bi-person-gear"></i>
                                    <span>Pengaturan Akun</span>
                                </a>
                            </li>
                        @elseif(Auth::user()->role === 'divisi')
                           
                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="{{ route('divisi.settings.index') }}">
                                    <i class="bi bi-person-gear"></i>
                                    <span>Pengaturan Akun</span>
                                </a>
                            </li>
                            
                        @endif
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Keluar</span>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </header>

    {{-- ======= Sidebar ======= --}}
    <aside id="sidebar" class="sidebar">
        <ul class="sidebar-nav" id="sidebar-nav">
            @if(Auth::user()->role === 'admin')
                <li class="nav-item">
                    <a class="nav-link collapsed" href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-grid"></i>
                        <span>Dashboard Admin</span>
                    </a>
                </li>

                <li class="nav-heading">MANAJEMEN GUDANG</li>
                <li class="nav-item">
                    <a class="nav-link collapsed" href="{{ route('barang.index') }}">
                        <i class="bi bi-box"></i>
                        <span>Barang</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link collapsed" href="{{ route('barang-masuk.index') }}">
                        <i class="bi bi-box-arrow-in-left"></i>
                        <span>Barang Masuk</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link collapsed" href="{{ route('admin.barang-keluar.index') }}">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Barang Keluar</span>
                    </a>
                </li>

                <li class="nav-heading">TRANSAKSI</li>
                <li class="nav-item">
                    <a class="nav-link collapsed" href="{{ route('supplier.index') }}">
                        <i class="bi bi-truck"></i>
                        <span>Supplier</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link collapsed" href="{{ route('payment.index') }}">
                        <i class="bi bi-currency-dollar"></i>
                        <span>Pembayaran</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link collapsed" href="{{ route('pengadaan.index') }}">
                        <i class="bi bi-clipboard-check"></i>
                        <span>Pengadaan Barang</span>
                    </a>
                </li>

                <li class="nav-heading">PENGGUNA & PERMINTAAN</li>
                <li class="nav-item">
                    <a class="nav-link collapsed" href="{{ route('admin.divisi.index') }}">
                        <i class="bi bi-people"></i>
                        <span>Manajemen Divisi</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link collapsed" href="{{ route('admin.permintaan.index') }}">
                        <i class="bi bi-file-earmark-text"></i>
                        <span>Daftar Permintaan</span>
                    </a>
                </li>

            @elseif(Auth::user()->role === 'divisi')
                <li class="nav-item">
                    <a class="nav-link collapsed" href="{{ route('divisi.dashboard') }}">
                        <i class="bi bi-grid"></i>
                        <span>Dashboard Divisi</span>
                    </a>
                </li>
                {{-- Tambahkan menu divisi lainnya di sini --}}
                <li class="nav-item">
                    <a class="nav-link collapsed" href="{{ route('divisi.permintaan-barang.index') }}">
                        <i class="bi bi-basket"></i>
                        <span>Permintaan Barang</span>
                    </a>
                </li>
            @endif
        </ul>
    </aside>

    <main id="main" class="main">
        @yield('content')
    </main>

    {{-- ======= Footer ======= --}}
    <footer id="footer" class="footer">
        <div class="copyright">
            &copy; Copyright <strong><span>Disperindag</span></strong>. All Rights Reserved
        </div>
        <div class="credits">
            Designed by <a href="#">BootstrapMade</a>
        </div>
    </footer>

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    {{-- Vendor JS Files --}}
    <script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/chart.js/chart.umd.js') }}"></script>
    <script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/quill/quill.js') }}"></script>
    <script src="{{ asset('assets/vendor/simple-datatables/simple-datatables.js') }}"></script>
    <script src="{{ asset('assets/vendor/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/php-email-form/validate.js') }}"></script>

    {{-- Template Main JS File --}}
    <script src="{{ asset('assets/js/main.js') }}"></script>
    {{-- Ini adalah baris yang hilang --}}
    @stack('scripts')

</body>
</html>
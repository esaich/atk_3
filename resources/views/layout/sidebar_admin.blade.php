<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? '' : 'collapsed' }}" href="{{ route('admin.dashboard') }}">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>
        </li><!-- End Dashboard Nav -->

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('supplier.*') ? '' : 'collapsed' }}" href="{{ route('supplier.index') }}">
                <i class="bi bi-truck"></i>
                <span>Supplier</span>
            </a>
        </li><!-- End Supplier Nav -->

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('pengadaan.*') ? '' : 'collapsed' }}" href="{{ route('pengadaan.index') }}">
                <i class="bi bi-cart-plus"></i>
                <span>Pengadaan Barang</span>
            </a>
        </li><!-- End Pengadaan Barang Nav -->

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('barang.*') ? '' : 'collapsed' }}" href="{{ route('barang.index') }}">
                <i class="bi bi-box-seam"></i>
                <span>Barang</span>
            </a>
        </li><!-- End Barang Nav -->

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('barang-masuk.*') ? '' : 'collapsed' }}" href="{{ route('barang-masuk.index') }}">
                <i class="bi bi-box-arrow-in-right"></i>
                <span>Barang Masuk</span>
            </a>
        </li><!-- End Barang Masuk Nav -->

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.barang-keluar.*') ? '' : 'collapsed' }}" href="{{ route('admin.barang-keluar.index') }}">
                <i class="bi bi-box-arrow-left"></i>
                <span>Barang Keluar</span>
            </a>
        </li><!-- End Barang Keluar Nav -->

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('payment.*') ? '' : 'collapsed' }}" href="{{ route('payment.index') }}">
                <i class="bi bi-cash-coin"></i>
                <span>Pembayaran</span>
            </a>
        </li><!-- End Pembayaran Nav -->

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.permintaan.*') ? '' : 'collapsed' }}" href="{{ route('admin.permintaan.index') }}">
                <i class="bi bi-card-checklist"></i>
                <span>Permintaan Barang</span>
            </a>
        </li><!-- End Permintaan Barang Nav -->

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.divisi.*') ? '' : 'collapsed' }}" href="{{ route('admin.divisi.index') }}">
                <i class="bi bi-people"></i>
                <span>Manajemen Divisi</span>
            </a>
        </li><!-- End Manajemen Divisi Nav -->

    </ul>
</aside><!-- End Sidebar-->
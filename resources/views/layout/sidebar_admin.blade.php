<aside id="sidebar" class="sidebar">
  <ul class="sidebar-nav" id="sidebar-nav">
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('admin.dashboard') ? '' : 'collapsed' }}" href="{{ route('admin.dashboard') }}">
        <i class="bi bi-grid"></i>
        <span>Dashboard</span>
      </a>
    </li>
    <!-- Menu Admin lain -->
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('supplier.*') ? '' : 'collapsed' }}" href="{{ route('supplier.index') }}">
        <i class="bi bi-box"></i>
        <span>Supplier</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('payment.*') ? '' : 'collapsed' }}" href="{{ route('payment.index') }}">
        <i class="bi bi-wallet2"></i>
        <span>Payment</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('barang.*') ? '' : 'collapsed' }}" href="{{ route('barang.index') }}">
        <i class="bi bi-bag"></i>
        <span>Barang</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('barang-masuk.*') ? '' : 'collapsed' }}" href="{{ route('barang-masuk.index') }}">
        <i class="bi bi-bag-plus"></i>
        <span>Barang Masuk</span>
      </a>
    </li>
    <li class="nav-item">
      {{-- Perbaikan: Mengubah nama rute menjadi admin.barang-keluar.index --}}
      <a class="nav-link {{ request()->routeIs('admin.barang-keluar.index') ? '' : 'collapsed' }}" href="{{ route('admin.barang-keluar.index') }}">
        <i class="bi bi-bag-x"></i>
        <span>Barang Keluar</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('admin.permintaan.*') ? '' : 'collapsed' }}" href="{{ route('admin.permintaan.index') }}">
        <i class="bi bi-card-checklist"></i>
        <span>Permintaan</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('admin.divisi.*') ? '' : 'collapsed' }}" href="{{ route('admin.divisi.index') }}">
        <i class="bi bi-person-lines-fill"></i>
        <span>User Divisi</span>
      </a>
    </li>

    <!-- Logout -->
    <li class="nav-item">
      <a class="nav-link" href="#"
         onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="bi bi-box-arrow-right"></i>
        <span>Logout</span>
      </a>
      <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
    </li>
  </ul>
</aside>

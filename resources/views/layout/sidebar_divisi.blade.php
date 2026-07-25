<aside id="sidebar" class="sidebar">
  <ul class="sidebar-nav" id="sidebar-nav">

    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('divisi.dashboard') ? '' : 'collapsed' }}" href="{{ route('divisi.dashboard') }}">
        <i class="bi bi-speedometer2"></i>
        <span>Dashboard</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('divisi.permintaan-barang.*') ? '' : 'collapsed' }}" href="{{ route('divisi.permintaan-barang.index') }}">
        <i class="bi bi-card-checklist"></i>
        <span>Permintaan Barang</span>
      </a>
    </li>

  </ul>
</aside>
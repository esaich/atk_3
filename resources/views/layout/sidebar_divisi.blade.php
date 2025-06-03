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

<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item nav-profile">
      <a href="#" class="nav-link">
        <div class="nav-profile-image">
          <img src="../assets/images/faces/face1.jpg" alt="profile" />
          <span class="login-status online"></span>
        </div>
        <div class="nav-profile-text d-flex flex-column">
          <span class="font-weight-bold mb-2">{{ Auth::user()->name }}</span>
          <span class="text-secondary text-small">User</span>
        </div>
        <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ Route::is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
        <span class="menu-title">Dashboard</span>
        <i class="mdi mdi-home menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ Route::is('kategori.index') || Route::is('kategori.create') || Route::is('kategori.edit') ? 'active' : '' }}" href="{{ route('kategori.index') }}">
        <span class="menu-title">Kategori</span>
        <i class="mdi mdi-folder menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ Route::is('buku.index') || Route::is('buku.create') || Route::is('buku.edit') ? 'active' : '' }}" href="{{ route('buku.index') }}">
        <span class="menu-title">Buku</span>
        <i class="mdi mdi-book-open-page-variant menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ Route::is('barang.index') ? 'active' : '' }}" href="{{ route('barang.index') }}">
        <span class="menu-title">Tag Harga</span>
        <i class="mdi mdi-tag-multiple menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ Route::is('pdf.index') ? 'active' : '' }}" href="{{ route('pdf.index') }}">
        <span class="menu-title">PDF</span>
        <i class="mdi mdi-file-document menu-icon"></i>
      </a>
    </li>
    <li class="nav-item {{ Route::is('js.*') ? 'active' : '' }}">
      <a class="nav-link" data-bs-toggle="collapse" href="#jsStudiMenu" aria-expanded="{{ Route::is('js.*') ? 'true' : 'false' }}">
        <span class="menu-title">JS Studi Kasus</span>
        <i class="mdi mdi-code-folder menu-icon"></i>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse {{ Route::is('js.*') ? 'show' : '' }}" id="jsStudiMenu">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item">
            <a class="nav-link {{ Route::is('js.studi1') ? 'active' : '' }}" href="{{ route('js.studi1') }}">
              Studi 1 – Button Spinner
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ Route::is('js.studi2_plain') ? 'active' : '' }}" href="{{ route('js.studi2_plain') }}">
              Studi 2&3 – Tabel Biasa
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ Route::is('js.studi3_dt') ? 'active' : '' }}" href="{{ route('js.studi3_dt') }}">
              Studi 2&3 – DataTables
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ Route::is('js.studi4') ? 'active' : '' }}" href="{{ route('js.studi4') }}">
              Studi 4 – Select & Select2
            </a>
          </li>
        </ul>
      </div>
    </li>
    <li class="nav-item {{ Route::is('ajax.*') ? 'active' : '' }}">
      <a class="nav-link" data-bs-toggle="collapse" href="#ajaxStudiMenu"
        aria-expanded="{{ Route::is('ajax.*') ? 'true' : 'false' }}">
        <span class="menu-title">AJAX Studi Kasus</span>
        <i class="mdi mdi-code-folder menu-icon"></i>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse {{ Route::is('ajax.*') ? 'show' : '' }}" id="ajaxStudiMenu">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item">
            <a class="nav-link {{ Route::is('ajax.wilayah') ? 'active' : '' }}" href="{{ route('ajax.wilayah') }}">
              SK 1 – Cascading Wilayah
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ Route::is('ajax.pos') ? 'active' : '' }}" href="{{ route('ajax.pos') }}">
              SK 2 – Point of Sales
            </a>
          </li>
        </ul>
      </div>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{ route('kantin.index') }}">
        <span class="menu-title">Kantin Online</span>
        <i class="mdi mdi-food menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{ route('kantor.login') }}">
        <span class="menu-title">Vendor Panel</span>
        <i class="mdi mdi-store menu-icon"></i>
      </a>
    </li>
    <li class="nav-item {{ Route::is('customer.*') ? 'active' : '' }}">
      <a class="nav-link" data-bs-toggle="collapse" href="#customerMenu"
        aria-expanded="{{ Route::is('customer.*') ? 'true' : 'false' }}">
        <span class="menu-title">Customer</span>
        <i class="mdi mdi-account-group menu-icon"></i>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse {{ Route::is('customer.*') ? 'show' : '' }}" id="customerMenu">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item">
            <a class="nav-link {{ Route::is('customer.index') ? 'active' : '' }}"
              href="{{ route('customer.index') }}">Data Customer</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ Route::is('customer.create.blob') ? 'active' : '' }}"
              href="{{ route('customer.create.blob') }}">Tambah Customer 1</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ Route::is('customer.create.file') ? 'active' : '' }}"
              href="{{ route('customer.create.file') }}">Tambah Customer 2</a>
          </li>
        </ul>
      </div>
    </li>
  </ul>
</nav>
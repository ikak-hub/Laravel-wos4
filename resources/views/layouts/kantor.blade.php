<!doctype html>
<html lang="id">
<head>
    <style>
    .navbar {
        background: linear-gradient(to right, #da8cff, #9a55ff) !important; /* Warna khas Purple Admin */
    }
    .page-body-wrapper {
        padding-top: 70px; /* Sesuaikan dengan tinggi navbar kamu */
    }
</style>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Vendor Panel — Kantin</title>
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body>
<div class="container-scroller">

    {{-- Navbar --}}
    <nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
        <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
            <span class="text-white fw-bold ps-3">
                <i class="mdi mdi-store me-1"></i> Vendor Panel
            </span>
        </div>
        <div class="navbar-menu-wrapper d-flex align-items-stretch">
            <ul class="navbar-nav navbar-nav-right">
                <li class="nav-item">
                    <span class="nav-link text-white">
                        <i class="mdi mdi-account-circle me-1"></i>{{ session('vendor_name') }}
                    </span>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('kantor.logout') }}">
                        <i class="mdi mdi-logout me-1"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container-fluid page-body-wrapper">

        {{-- Sidebar --}}
        <nav class="sidebar sidebar-offcanvas" id="sidebar">
            <ul class="nav">
                <li class="nav-item nav-profile">
                    <a href="#" class="nav-link">
                        <div class="nav-profile-text d-flex flex-column">
                            <span class="font-weight-bold mb-2">{{ session('vendor_name') }}</span>
                            <span class="text-secondary text-small">Vendor</span>
                        </div>
                    </a>
                </li>
                <li class="nav-item {{ Route::is('kantor.dashboard') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('kantor.dashboard') }}">
                        <span class="menu-title">Dashboard</span>
                        <i class="mdi mdi-home menu-icon"></i>
                    </a>
                </li>
                <li class="nav-item {{ Route::is('kantor.menu') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('kantor.menu') }}">
                        <span class="menu-title">Master Menu</span>
                        <i class="mdi mdi-food menu-icon"></i>
                    </a>
                </li>
                <li class="nav-item {{ Route::is('kantor.orders') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('kantor.orders') }}">
                        <span class="menu-title">Pesanan Lunas</span>
                        <i class="mdi mdi-receipt menu-icon"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('kantin.index') }}" target="_blank">
                        <span class="menu-title">Halaman Kantin ↗</span>
                        <i class="mdi mdi-open-in-new menu-icon"></i>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="main-panel">
            <div class="content-wrapper">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="mdi mdi-check-circle me-1"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @yield('content')
            </div>
            <footer class="footer">
                <div class="text-center text-muted">Vendor Panel — Kantin Online</div>
            </footer>
        </div>

    </div>
</div>
<script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
<script src="{{ asset('assets/js/off-canvas.js') }}"></script>
<script src="{{ asset('assets/js/misc.js') }}"></script>
@stack('scripts')
</body>
</html>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>🍽️ Kantin Online</title>
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        body { background: #f0f2f5; }
        .kantin-nav {
            background: linear-gradient(135deg, #4B49AC, #7978E9);
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky; top: 0; z-index: 999;
            box-shadow: 0 2px 8px rgba(0,0,0,.15);
        }
    </style>
</head>
<body>
    <div class="kantin-nav">
        <span class="text-white fw-bold fs-5">
            <i class="mdi mdi-food me-2"></i>Kantin Online
        </span>
        <a href="{{ route('kantor.login') }}" class="btn btn-sm btn-light">
            <i class="mdi mdi-store me-1"></i> Login Vendor
        </a>
    </div>

    <div class="container-fluid py-4 px-4">
        @yield('content')
    </div>

    <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- Midtrans Snap.js --}}
    @if(config('midtrans.is_production'))
        <script src="https://app.midtrans.com/snap/snap.js"
                data-client-key="{{ config('midtrans.client_key') }}"></script>
    @else
        <script src="https://app.sandbox.midtrans.com/snap/snap.js"
                data-client-key="{{ config('midtrans.client_key') }}"></script>
    @endif
    @stack('scripts')
</body>
</html>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Vendor</title>
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body>
<div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth">
            <div class="row flex-grow">
                <div class="col-lg-4 mx-auto">
                    <div class="auth-form-light text-left p-5">
                        <div class="brand-logo text-center mb-3">
                            <i class="mdi mdi-store mdi-48px text-primary d-block mb-2"></i>
                            <h4 class="fw-bold">Login Vendor</h4>
                            <p class="text-muted small">Masuk ke panel manajemen kantin</p>
                        </div>

                        @if(session('error'))
                            <div class="alert alert-danger">
                                <i class="mdi mdi-alert-circle me-1"></i> {{ session('error') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('kantor.login.post') }}">
                            @csrf
                            <div class="form-group mb-3">
                                <input type="text" name="username"
                                       class="form-control form-control-lg"
                                       placeholder="Username"
                                       value="{{ old('username') }}"
                                       required autofocus>
                            </div>
                            <div class="form-group mb-4">
                                <input type="password" name="password"
                                       class="form-control form-control-lg"
                                       placeholder="Password" required>
                            </div>
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-gradient-primary btn-lg">
                                    <i class="mdi mdi-login me-1"></i> Masuk
                                </button>
                            </div>
                            <div class="text-center">
                                <a href="{{ route('kantin.index') }}" class="text-muted small">
                                    ← Kembali ke Halaman Kantin
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
</body>
</html>
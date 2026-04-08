@extends('layouts.kantor')

@section('content')
<div class="page-header">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
            <i class="mdi mdi-home"></i>
        </span> Dashboard
    </h3>
</div>

<div class="row">
    <div class="col-md-4 stretch-card grid-margin">
        <div class="card bg-gradient-info card-img-holder text-white">
            <div class="card-body">
                <img src="{{ asset('assets/images/dashboard/circle.svg') }}"
                     class="card-img-absolute" alt="bg">
                <h4 class="font-weight-normal mb-3">
                    Total Menu
                    <i class="mdi mdi-food mdi-24px float-end"></i>
                </h4>
                <h2 class="mb-5">{{ $totalMenu }}</h2>
                <h6 class="card-text">Menu Tersedia di Kantin</h6>
            </div>
        </div>
    </div>
    <div class="col-md-4 stretch-card grid-margin">
        <div class="card bg-gradient-success card-img-holder text-white">
            <div class="card-body">
                <img src="{{ asset('assets/images/dashboard/circle.svg') }}"
                     class="card-img-absolute" alt="bg">
                <h4 class="font-weight-normal mb-3">
                    Pesanan Lunas
                    <i class="mdi mdi-check-circle mdi-24px float-end"></i>
                </h4>
                <h2 class="mb-5">{{ $totalOrders }}</h2>
                <h6 class="card-text">Total Pesanan Terbayar</h6>
            </div>
        </div>
    </div>
    <div class="col-md-4 stretch-card grid-margin">
        <div class="card bg-gradient-danger card-img-holder text-white">
            <div class="card-body">
                <img src="{{ asset('assets/images/dashboard/circle.svg') }}"
                     class="card-img-absolute" alt="bg">
                <h4 class="font-weight-normal mb-3">
                    Pendapatan
                    <i class="mdi mdi-currency-usd mdi-24px float-end"></i>
                </h4>
                <h2 class="mb-5" style="font-size:1.4rem;">
                    Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                </h2>
                <h6 class="card-text">Total Pendapatan Lunas</h6>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">
                    <i class="mdi mdi-store text-primary me-2"></i>
                    Selamat datang, <strong>{{ $vendor->nama_vendor }}</strong>!
                </h4>
                <p class="text-muted">
                    Gunakan menu di sidebar untuk mengelola menu kantin dan melihat pesanan yang sudah lunas.
                </p>
                <div class="d-flex gap-3 mt-3">
                    <a href="{{ route('kantor.menu') }}" class="btn btn-gradient-primary">
                        <i class="mdi mdi-food me-1"></i> Kelola Menu
                    </a>
                    <a href="{{ route('kantor.orders') }}" class="btn btn-gradient-success">
                        <i class="mdi mdi-receipt me-1"></i> Lihat Pesanan Lunas
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
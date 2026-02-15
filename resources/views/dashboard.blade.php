@extends('layouts.app')

@section('content')
@include('layouts.header', ['title' => 'Dashboard', 'icon' => 'mdi-view-dashboard'])

<!-- Statistics Cards -->
<div class="row">
    <div class="col-md-4 stretch-card grid-margin">
        <div class="card bg-gradient-danger card-img-holder text-white">
            <div class="card-body">
                <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
                <h4 class="font-weight-normal mb-3">Total Kategori <i class="mdi mdi-chart-line mdi-24px float-end"></i>
                </h4>
                <h2 class="mb-5">{{ $totalKategori }}</h2>
                <h6 class="card-text">Kategori Buku Tersedia</h6>
            </div>
        </div>
    </div>
    <div class="col-md-4 stretch-card grid-margin">
        <div class="card bg-gradient-info card-img-holder text-white">
            <div class="card-body">
                <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
                <h4 class="font-weight-normal mb-3">Total Buku <i class="mdi mdi-bookmark-outline mdi-24px float-end"></i>
                </h4>
                <h2 class="mb-5">{{ $totalBuku }}</h2>
                <h6 class="card-text">Koleksi Buku Tersimpan</h6>
            </div>
        </div>
    </div>
    <div class="col-md-4 stretch-card grid-margin">
        <div class="card bg-gradient-success card-img-holder text-white">
            <div class="card-body">
                <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
                <h4 class="font-weight-normal mb-3">Total User <i class="mdi mdi-diamond mdi-24px float-end"></i>
                </h4>
                <h2 class="mb-5">{{ $totalUser }}</h2>
                <h6 class="card-text">Pengguna Terdaftar</h6>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Buku per Kategori Chart -->
    <div class="col-md-7 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Statistik Buku per Kategori</h4>
                <canvas id="bukuPerKategoriChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Kategori List -->
    <div class="col-md-5 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Kategori Buku</h4>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Kategori</th>
                                <th>Jumlah Buku</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bukuPerKategori as $kategori)
                            <tr>
                                <td>
                                    <span class="badge badge-gradient-primary">{{ $kategori->nama_kategori }}</span>
                                </td>
                                <td>
                                    <label class="badge badge-gradient-info">{{ $kategori->bukus_count }} buku</label>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-center">Belum ada kategori</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Latest Books -->
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Buku Terbaru</h4>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Judul</th>
                                <th>Pengarang</th>
                                <th>Kategori</th>
                                <th>Tanggal Ditambahkan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestBukus as $buku)
                            <tr>
                                <td><span class="badge badge-gradient-info">{{ $buku->kode }}</span></td>
                                <td>{{ $buku->judul }}</td>
                                <td>{{ $buku->pengarang }}</td>
                                <td><span class="badge badge-gradient-success">{{ $buku->kategori->nama_kategori }}</span></td>
                                <td>{{ $buku->created_at->format('d M Y, H:i') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">Belum ada buku</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/vendors/chart.js/chart.umd.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Data dari Laravel
    var kategoris = @json($bukuPerKategori->pluck('nama_kategori'));
    var counts = @json($bukuPerKategori->pluck('bukus_count'));
    
    // Chart Configuration
    var ctx = document.getElementById('bukuPerKategoriChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: kategoris,
                datasets: [{
                    label: 'Jumlah Buku',
                    data: counts,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });
    }
});
</script>
@endpush
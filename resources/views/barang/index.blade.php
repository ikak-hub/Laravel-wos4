@extends('layouts.app')

@section('content')
@include('layouts.header', ['title' => 'Tag Harga Barang', 'icon' => 'mdi-tag-multiple'])

{{-- ── Alert ── --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="mdi mdi-check-circle me-1"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">

                {{-- ── Toolbar ── --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">Daftar Barang</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('barang.cetak.form') }}"
                            class="btn btn-gradient-success btn-sm">
                            <i class="mdi mdi-printer me-1"></i> Cetak Tag Harga
                        </a>
                        <a href="{{ route('barang.create') }}" class="btn btn-primary btn-sm">
                            Tambah Barang
                        </a>
                    </div>
                </div>

                {{-- ── DataTable ── --}}
                <div class="table-responsive">
                    <table id="tblBarang" class="table table-hover table-bordered w-100">
                        <thead class="table-dark">
                            <tr>
                                <th>ID Barang</th>
                                <th>Nama Barang</th>
                                <th>Harga</th>
                                <th>Tanggal Input</th>
                                <th class="text-center" style="width:110px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($barang as $barang)
                            <tr>
                                <td>
                                    <span class="badge badge-gradient-info font-monospace">
                                        {{ $barang->id_barang }}
                                    </span>
                                </td>
                                <td>{{ $barang->nama }}</td>
                                <td>Rp {{ number_format($barang->harga, 0, ',', '.') }}</td>
                                <td>{{ \Carbon\Carbon::parse($barang->timestamp)->format('d M Y, H:i') }}</td>
                                <td class="text-center">

                                    {{-- Tombol edit --}}
                                    <a href="{{ route('barang.edit', $barang->id_barang) }}"
                                        class="btn btn-gradient-warning btn-sm px-2" title="Edit">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>

                                    {{-- Tombol Hapus --}}
                                    <form action="{{ route('barang.destroy', $barang->id_barang) }}"
                                        method="POST" class="d-inline"
                                        onsubmit="return confirm('Hapus {{ $barang->nama }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-gradient-danger btn-sm px-2" title="Hapus">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="mdi mdi-package-variant-closed mdi-24px d-block mb-1"></i>
                                    Belum ada data barang. Tambah barang terlebih dahulu.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>{{-- /.table-responsive --}}

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet"
    href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(function() {
        $('#tblBarang').DataTable({
            language: {
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data',
                zeroRecords: 'Data tidak ditemukan',
                paginate: {
                    first: 'Pertama',
                    last: 'Terakhir',
                    next: 'Selanjutnya',
                    previous: 'Sebelumnya',
                }
            },
            order: [
                [3, 'desc']
            ], // urutkan kolom Tanggal Input descending
            columnDefs: [{
                orderable: false,
                targets: 4
            }] // kolom Aksi tidak sortable
        });
    });
</script>
@endpush
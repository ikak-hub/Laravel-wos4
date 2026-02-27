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
                        <button type="button"
                            class="btn btn-gradient-primary btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#modalTambah">
                            <i class="mdi mdi-plus me-1"></i> Tambah Barang
                        </button>
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
                            @forelse($barang as $b)
                            <tr>
                                <td>
                                    <span class="badge badge-gradient-info font-monospace">
                                        {{ $b->id_barang }}
                                    </span>
                                </td>
                                <td>{{ $b->nama }}</td>
                                <td>Rp {{ number_format($b->harga, 0, ',', '.') }}</td>
                                <td>{{ \Carbon\Carbon::parse($b->timestamp)->format('d M Y, H:i') }}</td>
                                <td class="text-center">

                                    {{-- Tombol Edit --}}
                                    <button type="button"
                                        class="btn btn-gradient-warning btn-sm px-2"
                                        title="Edit"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEdit{{ $b->id_barang }}">
                                        <i class="mdi mdi-pencil"></i>
                                    </button>

                                    {{-- Tombol Hapus --}}
                                    <form action="{{ route('barang.destroy', $b->id_barang) }}"
                                        method="POST" class="d-inline"
                                        onsubmit="return confirm('Hapus barang {{ $b->nama }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="btn btn-gradient-danger btn-sm px-2"
                                            title="Hapus">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </form>

                                </td>
                            </tr>

                            {{-- ── Modal Edit (per baris) ── --}}
                            <div class="modal fade" id="modalEdit{{ $b->id_barang }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-gradient-warning text-white">
                                            <h5 class="modal-title">
                                                <i class="mdi mdi-pencil me-1"></i> Edit Barang
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white"
                                                data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('barang.update', $b->id_barang) }}"
                                            method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">ID Barang</label>
                                                    <input type="text" class="form-control bg-light"
                                                        value="{{ $b->id_barang }}" readonly>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Nama Barang</label>
                                                    <input type="text" name="nama" class="form-control"
                                                        value="{{ $b->nama }}" required maxlength="50">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Harga (Rp)</label>
                                                    <input type="number" name="harga" class="form-control"
                                                        value="{{ $b->harga }}" required min="0">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light"
                                                    data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-gradient-primary">
                                                    <i class="mdi mdi-content-save me-1"></i> Simpan
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            {{-- ── End Modal Edit ── --}}

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

{{-- ── Modal Tambah Barang ── --}}
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title">
                    <i class="mdi mdi-plus-circle me-1"></i> Tambah Barang Baru
                </h5>
                <button type="button" class="btn-close btn-close-white"
                    data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('barang.store') }}" method="POST">
                @csrf
                <div class="modal-body">

                    <div class="alert alert-info py-2 small mb-3">
                        <i class="mdi mdi-information-outline me-1"></i>
                        <strong>ID Barang</strong> digenerate otomatis oleh sistem
                        (format <code>YYMMDDSQ</code>).
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" name="nama"
                            class="form-control @error('nama') is-invalid @enderror"
                            placeholder="Contoh: Pensil 2B"
                            required maxlength="50"
                            value="{{ old('nama') }}">
                        @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Harga (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="harga"
                            class="form-control @error('harga') is-invalid @enderror"
                            placeholder="Contoh: 5000"
                            required min="0"
                            value="{{ old('harga') }}">
                        @error('harga')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-gradient-primary">
                        <i class="mdi mdi-content-save me-1"></i> Simpan
                    </button>
                </div>
            </form>
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

        // Buka kembali modal Tambah jika ada error validasi
        @if($errors - > any())
            (new bootstrap.Modal(document.getElementById('modalTambah'))).show();
        @endif
    });
</script>
@endpush
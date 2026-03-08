@extends('layouts.app')
@section('title', 'Tambah Barang')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Tambah Barang Baru</h4>
                <p class="card-description">Masukkan detail barang baru</p>

                <form action="{{ route('barang.store') }}" method="POST" class="forms-sample">
                    @csrf

                    <div class="form-group">
                        <label>Nama Barang</label>
                        <input type="text"
                               name="nama"
                               class="form-control @error('nama') is-invalid @enderror"
                               placeholder="Masukkan nama barang"
                               value="{{ old('nama') }}" required>
                        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label>Harga (Rp)</label>
                        <input type="number"
                               name="harga"
                               class="form-control @error('harga') is-invalid @enderror"
                               placeholder="Masukkan harga"
                               value="{{ old('harga') }}" min="0" required>
                        @error('harga')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <button type="submit" class="btn btn-primary me-2">Simpan</button>
                    <a href="{{ route('barang.index') }}" class="btn btn-light">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
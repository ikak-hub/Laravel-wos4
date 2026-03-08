@extends('layouts.app')
@section('title', 'Edit Barang')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Edit Barang</h4>
                <p class="card-description">Ubah detail barang</p>

                <form action="{{ route('barang.update', $barang->id_barang) }}" method="POST" class="forms-sample">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label>ID Barang</label>
                        <input type="text" class="form-control bg-light"
                               value="{{ $barang->id_barang }}" disabled>
                    </div>

                    <div class="form-group">
                        <label>Nama Barang</label>
                        <input type="text"
                               name="nama"
                               class="form-control @error('nama') is-invalid @enderror"
                               value="{{ old('nama', $barang->nama) }}" required>
                        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label>Harga (Rp)</label>
                        <input type="number"
                               name="harga"
                               class="form-control @error('harga') is-invalid @enderror"
                               value="{{ old('harga', $barang->harga) }}" min="0" required>
                        @error('harga')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <button type="submit" class="btn btn-primary me-2">Update</button>
                    <a href="{{ route('barang.index') }}" class="btn btn-light">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
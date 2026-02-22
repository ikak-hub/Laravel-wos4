@extends('layouts.app')

@section('content')
@include('layouts.header', ['title' => 'kategori', 'icon' => 'mdi-folder-multiple-image'])


<?php
session()->put('current_page', 'buku');
?>
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">Daftar Kategori</h4>
                    <button type="button" class="btn btn-gradient-primary btn-sm" data-bs-toggle="modal" data-bs-target="#tambahKategoriModal">
                        <i class="mdi mdi-plus"></i> Tambah Kategori
                    </button>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <!-- <th>ID</th> -->
                                <th>Nama Kategori</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kategoris as $index => $kategori)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <!-- <td>{{ $kategori->idkategori }}</td> -->
                                <td>{{ $kategori->nama_kategori }}</td>
                                <td>
                                    <button type="button" class="btn btn-gradient-info btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editKategoriModal{{ $kategori->idkategori }}">
                                        <i class="mdi mdi-pencil"></i> Edit
                                    </button>
                                    <form action="{{ route('kategori.destroy', $kategori->idkategori) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-gradient-danger btn-sm">
                                            <i class="mdi mdi-delete"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editKategoriModal{{ $kategori->idkategori }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Kategori</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('kategori.update', $kategori->idkategori) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Nama Kategori</label>
                                                    <input type="text" name="nama_kategori" class="form-control" 
                                                           value="{{ $kategori->nama_kategori }}" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-gradient-primary">Update</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">Belum ada data kategori</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tambah Modal -->
<div class="modal fade" id="tambahKategoriModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kategori Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('kategori.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Kategori</label>
                        <input type="text" name="nama_kategori" class="form-control @error('nama_kategori') is-invalid @enderror" 
                               placeholder="Masukkan nama kategori" required>
                        @error('nama_kategori')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-gradient-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
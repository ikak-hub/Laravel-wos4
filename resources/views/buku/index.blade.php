@extends('layouts.app')
@section('content')
@include('layouts.header', ['title' => 'Daftar Buku', 'icon' => 'mdi-book-open-variant'])


<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">Daftar Buku</h4>
                    <button type="button" class="btn btn-gradient-primary btn-sm" data-bs-toggle="modal" data-bs-target="#tambahBukuModal">
                        <i class="mdi mdi-plus"></i> Tambah Buku
                    </button>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Judul</th>
                                <th>Pengarang</th>
                                <th>Kategori</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bukus as $index => $buku)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><span class="badge badge-gradient-info">{{ $buku->kode }}</span></td>
                                <td>{{ $buku->judul }}</td>
                                <td>{{ $buku->pengarang }}</td>
                                <td><span class="badge badge-gradient-success">{{ $buku->kategori->nama_kategori }}</span></td>
                                <td>
                                    <button type="button" class="btn btn-gradient-info btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editBukuModal{{ $buku->idbuku }}">
                                        <i class="mdi mdi-pencil"></i> Edit
                                    </button>
                                    <form action="{{ route('buku.destroy', $buku->idbuku) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus buku ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-gradient-danger btn-sm">
                                            <i class="mdi mdi-delete"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editBukuModal{{ $buku->idbuku }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Buku</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('buku.update', $buku->idbuku) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Kode Buku</label>
                                                    <input type="text" name="kode" class="form-control" 
                                                           value="{{ $buku->kode }}" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Judul Buku</label>
                                                    <input type="text" name="judul" class="form-control" 
                                                           value="{{ $buku->judul }}" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Pengarang</label>
                                                    <input type="text" name="pengarang" class="form-control" 
                                                           value="{{ $buku->pengarang }}" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Kategori</label>
                                                    <select name="idkategori" class="form-control" required>
                                                        <option value="">Pilih Kategori</option>
                                                        @foreach($kategoris as $kategori)
                                                        <option value="{{ $kategori->idkategori }}" 
                                                                {{ $buku->idkategori == $kategori->idkategori ? 'selected' : '' }}>
                                                            {{ $kategori->nama_kategori }}
                                                        </option>
                                                        @endforeach
                                                    </select>
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
                                <td colspan="6" class="text-center">Belum ada data buku</td>
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
<div class="modal fade" id="tambahBukuModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Buku Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('buku.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Kode Buku</label>
                        <input type="text" name="kode" class="form-control @error('kode') is-invalid @enderror" 
                               placeholder="Contoh: NV-01" required>
                        @error('kode')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Judul Buku</label>
                        <input type="text" name="judul" class="form-control @error('judul') is-invalid @enderror" 
                               placeholder="Masukkan judul buku" required>
                        @error('judul')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Pengarang</label>
                        <input type="text" name="pengarang" class="form-control @error('pengarang') is-invalid @enderror" 
                               placeholder="Masukkan nama pengarang" required>
                        @error('pengarang')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="idkategori" class="form-control @error('idkategori') is-invalid @enderror" required>
                            <option value="">Pilih Kategori</option>
                            @foreach($kategoris as $kategori)
                            <option value="{{ $kategori->idkategori }}">{{ $kategori->nama_kategori }}</option>
                            @endforeach
                        </select>
                        @error('idkategori')
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
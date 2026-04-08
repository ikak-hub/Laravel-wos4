@extends('layouts.kantor')

@section('content')
<div class="page-header">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
            <i class="mdi mdi-food"></i>
        </span> Master Menu
    </h3>
</div>

<div class="row">

    {{-- ── Form Tambah Menu ──────────────────────────────────── --}}
    <div class="col-lg-4 grid-margin stretch-card">
        <div class="card">
            <div class="card-header bg-gradient-primary text-white py-2">
                <i class="mdi mdi-plus-circle me-1"></i> Tambah Menu Baru
            </div>
            <div class="card-body">
                <form action="{{ route('kantor.menu.store') }}" method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="form-group mb-3">
                        <label class="fw-semibold">Nama Menu <span class="text-danger">*</span></label>
                        <input type="text" name="nama_menu" class="form-control"
                               required placeholder="Contoh: Nasi Goreng Spesial"
                               value="{{ old('nama_menu') }}">
                        @error('nama_menu')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label class="fw-semibold">Harga (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="harga" class="form-control"
                               required min="0" placeholder="Contoh: 15000"
                               value="{{ old('harga') }}">
                        @error('harga')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group mb-4">
                        <label class="fw-semibold">Foto Menu <small class="text-muted">(opsional)</small></label>
                        <input type="file" name="gambar" class="form-control" accept="image/*">
                        <small class="text-muted">Maks. 2MB, format JPG/PNG/WebP</small>
                        @error('gambar')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-gradient-primary w-100">
                        <i class="mdi mdi-content-save me-1"></i> Simpan Menu
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Daftar Menu ────────────────────────────────────────── --}}
    <div class="col-lg-8 grid-margin stretch-card">
        <div class="card">
            <div class="card-header bg-gradient-dark text-white py-2">
                <i class="mdi mdi-food me-1"></i>
                Daftar Menu — <strong>{{ $vendor->nama_vendor }}</strong>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th width="64">Foto</th>
                                <th>Nama Menu</th>
                                <th>Harga</th>
                                <th class="text-center" width="110">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($menus as $menu)
                            <tr>
                                <td>
                                    @if($menu->path_gambar)
                                        <img src="{{ asset('storage/' . $menu->path_gambar) }}"
                                             alt="{{ $menu->nama_menu }}"
                                             style="width:52px;height:52px;object-fit:cover;
                                                    border-radius:8px;border:1px solid #dee2e6;">
                                    @else
                                        <div style="width:52px;height:52px;background:#f0f2f5;
                                                    border-radius:8px;display:flex;align-items:center;
                                                    justify-content:center;border:1px solid #dee2e6;">
                                            <i class="mdi mdi-image-off text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="align-middle fw-semibold">{{ $menu->nama_menu }}</td>
                                <td class="align-middle">
                                    Rp {{ number_format($menu->harga, 0, ',', '.') }}
                                </td>
                                <td class="text-center align-middle">
                                    <button class="btn btn-gradient-warning btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editModal{{ $menu->idmenu }}"
                                            title="Edit">
                                        <i class="mdi mdi-pencil"></i>
                                    </button>
                                    <form action="{{ route('kantor.menu.destroy', $menu->idmenu) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Hapus menu {{ $menu->nama_menu }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-gradient-danger btn-sm" title="Hapus">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            {{-- Modal Edit --}}
                            <div class="modal fade" id="editModal{{ $menu->idmenu }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                <i class="mdi mdi-pencil me-1"></i> Edit Menu
                                            </h5>
                                            <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('kantor.menu.update', $menu->idmenu) }}"
                                              method="POST" enctype="multipart/form-data">
                                            @csrf @method('PUT')
                                            <div class="modal-body">
                                                <div class="form-group mb-3">
                                                    <label>Nama Menu</label>
                                                    <input type="text" name="nama_menu"
                                                           class="form-control"
                                                           value="{{ $menu->nama_menu }}" required>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label>Harga (Rp)</label>
                                                    <input type="number" name="harga"
                                                           class="form-control"
                                                           value="{{ $menu->harga }}" required>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label>Foto baru (opsional, kosongkan jika tidak ingin ganti)</label>
                                                    <input type="file" name="gambar"
                                                           class="form-control" accept="image/*">
                                                    @if($menu->path_gambar)
                                                        <div class="mt-2">
                                                            <small class="text-muted">Foto saat ini:</small><br>
                                                            <img src="{{ asset('storage/' . $menu->path_gambar) }}"
                                                                 style="height:60px;border-radius:6px;margin-top:4px;">
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light"
                                                        data-bs-dismiss="modal">Batal</button>
                                                <button type="submit"
                                                        class="btn btn-gradient-primary">Update</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">
                                    <i class="mdi mdi-food-off mdi-36px d-block mb-2"></i>
                                    Belum ada menu. Tambah menu menggunakan form di kiri.
                                </td>
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
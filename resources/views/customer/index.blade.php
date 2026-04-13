@extends('layouts.app')
@section('content')
@include('layouts.header', ['title' => 'Data Customer', 'icon' => 'mdi-account-group'])

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between mb-3">
            <h4 class="card-title mb-0">Daftar Customer</h4>
            <div class="d-flex gap-2">
                <a href="{{ route('customer.create.blob') }}" class="btn btn-gradient-info btn-sm">
                    <i class="mdi mdi-camera me-1"></i> Tambah (Blob)
                </a>
                <a href="{{ route('customer.create.file') }}" class="btn btn-gradient-primary btn-sm">
                    <i class="mdi mdi-camera-plus me-1"></i> Tambah (File)
                </a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Foto</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Tipe Foto</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $c)
                    <tr>
                        <td>
                            @if($c->foto_blob)
                                <img src="{{ $c->foto_blob }}" 
                                     style="width:52px;height:52px;object-fit:cover;border-radius:8px;">
                            @elseif($c->foto_path)
                                <img src="{{ asset('storage/' . $c->foto_path) }}"
                                     style="width:52px;height:52px;object-fit:cover;border-radius:8px;">
                            @else
                                <div style="width:52px;height:52px;background:#f0f2f5;border-radius:8px;
                                            display:flex;align-items:center;justify-content:center;">
                                    <i class="mdi mdi-account text-muted"></i>
                                </div>
                            @endif
                        </td>
                        <td class="fw-semibold">{{ $c->nama }}</td>
                        <td>{{ $c->email ?? '-' }}</td>
                        <td>
                            @if($c->foto_blob)
                                <span class="badge badge-gradient-info">Blob (DB)</span>
                            @elseif($c->foto_path)
                                <span class="badge badge-gradient-primary">File (Storage)</span>
                            @endif
                        </td>
                        <td>{{ $c->created_at->format('d M Y, H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Belum ada data customer.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
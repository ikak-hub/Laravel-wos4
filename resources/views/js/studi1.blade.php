@extends('layouts.app')

@section('content')
@include('layouts.header', ['title' => 'Studi Kasus 1 – Button Spinner', 'icon' => 'mdi-loading'])

<div class="row justify-content-center">
    <div class="col-md-7 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-1">Tambah Buku <small class="text-muted fs-6">(dengan spinner)</small></h4>
                <p class="text-muted small mb-4">
                    Klik <strong>Simpan</strong> — jika ada input kosong akan muncul validasi HTML5.
                    Jika semua terisi, tombol berubah jadi <em>spinner</em> selama proses berjalan.
                </p>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                {{-- ── Form — button HARUS di luar <form> agar bisa dikontrol JS ── --}}
                <form id="frmBuku" action="{{ route('buku.store') }}" method="POST">
                    @csrf

                    <div class="form-group mb-3">
                        <label class="fw-semibold">Kode Buku <span class="text-danger">*</span></label>
                        <input type="text" name="kode" id="kode"
                               class="form-control @error('kode') is-invalid @enderror"
                               placeholder="Contoh: NV-01" required maxlength="20"
                               value="{{ old('kode') }}">
                        @error('kode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group mb-3">
                        <label class="fw-semibold">Judul Buku <span class="text-danger">*</span></label>
                        <input type="text" name="judul" id="judul"
                               class="form-control @error('judul') is-invalid @enderror"
                               placeholder="Masukkan judul buku" required maxlength="500"
                               value="{{ old('judul') }}">
                        @error('judul')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group mb-3">
                        <label class="fw-semibold">Pengarang <span class="text-danger">*</span></label>
                        <input type="text" name="pengarang" id="pengarang"
                               class="form-control @error('pengarang') is-invalid @enderror"
                               placeholder="Nama pengarang" required maxlength="200"
                               value="{{ old('pengarang') }}">
                        @error('pengarang')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group mb-3">
                        <label class="fw-semibold">Kategori <span class="text-danger">*</span></label>
                        <select name="idkategori" id="idkategori"
                                class="form-control @error('idkategori') is-invalid @enderror" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategoris as $k)
                                <option value="{{ $k->idkategori }}" {{ old('idkategori') == $k->idkategori ? 'selected' : '' }}>
                                    {{ $k->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                        @error('idkategori')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </form>

                {{-- Button di LUAR form — submit dipicu via JS --}}
                <div class="d-flex gap-2 mt-4">
                    <button type="button" id="btnSimpan" class="btn btn-gradient-primary px-4">
                        <span id="btnIcon"><i class="mdi mdi-content-save me-1"></i></span>
                        <span id="btnText">Simpan</span>
                    </button>
                    <a href="{{ route('buku.index') }}" class="btn btn-light px-4">Batal</a>
                </div>

            </div>
        </div>
    </div>

    {{-- ── Penjelasan kode ── --}}
    <div class="col-md-5 grid-margin stretch-card">
        <div class="card">
            <div class="card-header bg-dark text-white py-2">
                <i class="mdi mdi-code-tags me-1"></i> Cara Kerja
            </div>
            <div class="card-body small">
                <ol class="ps-3" style="line-height: 1.9">
                    <li>Button <code>type="button"</code> berada <strong>di luar</strong> <code>&lt;form&gt;</code></li>
                    <li>Klik → JS panggil <code>form.checkValidity()</code></li>
                    <li>Jika invalid → <code>form.reportValidity()</code> tampilkan tooltip HTML5</li>
                    <li>Jika valid → icon diganti <em>spinner</em>, button di-disable, kemudian <code>form.submit()</code></li>
                    <li>Tidak ada double-submit karena button langsung di-disable</li>
                </ol>
                <hr>
                <p class="text-muted mb-0">
                    Teknik ini berlaku untuk <strong>semua</strong> form CRUD (Tambah / Edit / Hapus).
                    Cukup terapkan pola yang sama: button di luar form + trigger submit via JS.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {

    $('#btnSimpan').on('click', function () {
        const form = document.getElementById('frmBuku');

        // ── 1. Cek validitas semua input required ──
        if (!form.checkValidity()) {
            form.reportValidity();   // tampilkan tooltip HTML5 native
            return;
        }

        // ── 2. Semua valid → ubah button jadi spinner ──
        const $btn  = $(this);
        const $icon = $('#btnIcon');
        const $text = $('#btnText');

        $btn.prop('disabled', true);
        $icon.html('<span class="spinner-border spinner-border-sm me-1" role="status"></span>');
        $text.text('Menyimpan...');

        // ── 3. Submit form ──
        form.submit();
    });

});
</script>
@endpush
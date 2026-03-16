@extends('layouts.app')

@section('content')
@include('layouts.header', ['title' => 'Studi Kasus 1 – Button Spinner', 'icon' => 'mdi-loading'])

<div class="row justify-content-center">
    <div class="col-md-7 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-1">Tambah Buku <small class="text-muted fs-6">(dengan spinner)</small></h4>
                <p class="text-muted small mb-4">
                    Klik <strong>Simpan</strong> — jika ada input kosong akan muncul validasi.
                    Jika semua terisi, tombol berubah jadi <em>spinner</em> selama proses berjalan.
                </p>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                {{--
                    novalidate = matikan validasi native browser,
                    kita handle sendiri via JS agar spinner bisa muncul dulu
                --}}
                <form id="frmBuku" action="{{ route('buku.store') }}" method="POST" novalidate>
                    @csrf

                    <div class="form-group mb-3">
                        <label class="fw-semibold">Kode Buku <span class="text-danger">*</span></label>
                        <input type="text" name="kode" id="kode"
                               class="form-control @error('kode') is-invalid @enderror"
                               placeholder="Contoh: NV-01" required maxlength="20"
                               value="{{ old('kode') }}">
                        @error('kode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        {{-- div ini HARUS ada di HTML — diisi JS saat validasi client-side gagal --}}
                        <div id="err-kode" class="invalid-feedback"></div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="fw-semibold">Judul Buku <span class="text-danger">*</span></label>
                        <input type="text" name="judul" id="judul"
                               class="form-control @error('judul') is-invalid @enderror"
                               placeholder="Masukkan judul buku" required maxlength="500"
                               value="{{ old('judul') }}">
                        @error('judul')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div id="err-judul" class="invalid-feedback"></div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="fw-semibold">Pengarang <span class="text-danger">*</span></label>
                        <input type="text" name="pengarang" id="pengarang"
                               class="form-control @error('pengarang') is-invalid @enderror"
                               placeholder="Nama pengarang" required maxlength="200"
                               value="{{ old('pengarang') }}">
                        @error('pengarang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div id="err-pengarang" class="invalid-feedback"></div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="fw-semibold">Kategori <span class="text-danger">*</span></label>
                        <select name="idkategori" id="idkategori"
                                class="form-control @error('idkategori') is-invalid @enderror" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategoris as $k)
                                <option value="{{ $k->idkategori }}"
                                    {{ old('idkategori') == $k->idkategori ? 'selected' : '' }}>
                                    {{ $k->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                        @error('idkategori')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div id="err-idkategori" class="invalid-feedback"></div>
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

    {{-- Penjelasan kode --}}
    <div class="col-md-5 grid-margin stretch-card">
        <div class="card">
            <div class="card-header bg-dark text-white py-2">
                <i class="mdi mdi-code-tags me-1"></i> Cara Kerja
            </div>
            <div class="card-body small">
                <ol class="ps-3" style="line-height: 1.9">
                    <li>Button <code>type="button"</code> berada <strong>di luar</strong> <code>&lt;form&gt;</code></li>
                    <li>Form pakai <code>novalidate</code> — validasi dikontrol penuh oleh JS</li>
                    <li>Klik → JS validasi tiap field, tampilkan pesan merah jika kosong</li>
                    <li>Jika valid → icon diganti <em>spinner</em>, button di-disable</li>
                    <li>Setelah 500ms (spinner terrender) → <code>form.submit()</code></li>
                </ol>
                <hr>
                <p class="text-muted mb-0">
                    Teknik ini berlaku untuk <strong>semua</strong> form CRUD.
                    Pola: button di luar form + trigger submit via JS.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {

    // ── Konfigurasi field: id input → id div error → pesan ──
    const fields = [
        { id: 'kode',       errId: 'err-kode',       msg: 'Kode buku wajib diisi.' },
        { id: 'judul',      errId: 'err-judul',       msg: 'Judul buku wajib diisi.' },
        { id: 'pengarang',  errId: 'err-pengarang',   msg: 'Nama pengarang wajib diisi.' },
        { id: 'idkategori', errId: 'err-idkategori',  msg: 'Kategori wajib dipilih.' },
    ];

    // ── Tampilkan error satu field ──
    function showError(f) {
        $('#' + f.id).addClass('is-invalid');
        $('#' + f.errId).text(f.msg).css('display', 'block');
    }

    // ── Hapus error satu field ──
    function clearError(f) {
        $('#' + f.id).removeClass('is-invalid');
        $('#' + f.errId).text('').css('display', 'none');
    }

    // ── Validasi semua field, return true jika semua valid ──
    function validateAll() {
        let valid = true;
        fields.forEach(function (f) {
            if ($('#' + f.id).val().trim() === '') {
                showError(f);
                valid = false;
            } else {
                clearError(f);
            }
        });
        return valid;
    }

    // ── Live: hapus error saat user mengisi field ──
    fields.forEach(function (f) {
        $('#' + f.id).on('input change', function () {
            if ($(this).val().trim() !== '') {
                clearError(f);
            }
        });
    });

    // ── Klik Simpan ──
    $('#btnSimpan').on('click', function () {

        // 1. Validasi — jika ada yang kosong, tampilkan error dan berhenti
        if (!validateAll()) {
            return;
        }

        // 2. Semua valid → ubah button jadi spinner
        $('#btnSimpan').prop('disabled', true);
        $('#btnIcon').html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>');
        $('#btnText').text('Menyimpan...');

        // 3. Tunggu 500ms agar browser sempat RENDER spinner, baru submit
        setTimeout(function () {
            document.getElementById('frmBuku').submit();
        }, 500);
    });

});
</script>
@endpush
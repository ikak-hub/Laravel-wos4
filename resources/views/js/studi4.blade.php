@extends('layouts.app')

@section('content')
@include('layouts.header', ['title' => 'Studi Kasus 4 – Select & Select2', 'icon' => 'mdi-form-select'])

<div class="row">

    {{-- ════════════════════════════════════
         Card 1: Select Biasa
    ════════════════════════════════════ --}}
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-header bg-gradient-primary text-white">
                <i class="mdi mdi-cursor-default-click-outline me-1"></i> Select
            </div>
            <div class="card-body">

                {{-- Input tambah kota --}}
                <div class="input-group mb-3">
                    <input type="text" id="inpKota1" class="form-control"
                           placeholder="Nama kota baru..." maxlength="50">
                    <button type="button" id="btnTambahKota1" class="btn btn-gradient-primary">
                        <i class="mdi mdi-plus"></i> Tambah
                    </button>
                </div>

                {{-- Select biasa --}}
                <div class="form-group mb-3">
                    <label class="fw-semibold">Form Select Kota</label>
                    <select id="selKota1" class="form-select" size="1">
                        <option value="">-- Pilih Kota --</option>
                        <option value="Surabaya">Surabaya</option>
                        <option value="Jakarta">Jakarta</option>
                        <option value="Bandung">Bandung</option>
                    </select>
                </div>

                {{-- Kota terpilih --}}
                <div class="p-3 bg-light rounded border">
                    <p class="mb-1 fw-semibold text-muted small">Kota Terpilih:</p>
                    <p id="kotaTerpilih1" class="mb-0 fs-5 fw-bold text-primary">
                        <em class="text-muted fw-normal small">Belum ada</em>
                    </p>
                </div>

            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════
         Card 2: Select2
    ════════════════════════════════════ --}}
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-header bg-gradient-info text-white">
                <i class="mdi mdi-cursor-default-click-outline me-1"></i> Select 2
            </div>
            <div class="card-body">

                {{-- Input tambah kota --}}
                <div class="input-group mb-3">
                    <input type="text" id="inpKota2" class="form-control"
                           placeholder="Nama kota baru..." maxlength="50">
                    <button type="button" id="btnTambahKota2" class="btn btn-gradient-info">
                        <i class="mdi mdi-plus"></i> Tambah
                    </button>
                </div>

                {{-- Select2 --}}
                <div class="form-group mb-3">
                    <label class="fw-semibold">Form Select Kota (Select2)</label>
                    <select id="selKota2" class="form-select" style="width:100%">
                        <option value="">-- Pilih Kota --</option>
                        <option value="Surabaya">Surabaya</option>
                        <option value="Jakarta">Jakarta</option>
                        <option value="Bandung">Bandung</option>
                    </select>
                </div>

                {{-- Kota terpilih --}}
                <div class="p-3 bg-light rounded border">
                    <p class="mb-1 fw-semibold text-muted small">Kota Terpilih:</p>
                    <p id="kotaTerpilih2" class="mb-0 fs-5 fw-bold text-info">
                        <em class="text-muted fw-normal small">Belum ada</em>
                    </p>
                </div>

            </div>
        </div>
    </div>

</div>

{{-- Perbedaan singkat --}}
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-header py-2">
                <i class="mdi mdi-information-outline me-1"></i> Perbedaan Select Biasa vs Select2
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-6 border-end">
                        <h6 class="text-primary">Select Biasa</h6>
                        <ul class="text-start small ps-3">
                            <li>Native HTML <code>&lt;select&gt;</code></li>
                            <li>Tidak bisa di-search</li>
                            <li>Tampilan default browser</li>
                            <li>Event: <code>onchange</code> / <code>.on('change')</code></li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-info">Select2</h6>
                        <ul class="text-start small ps-3">
                            <li>Library jQuery — tampilan lebih modern</li>
                            <li>Ada fitur <strong>search / filter</strong></li>
                            <li>Mendukung multiple select, tag, dll</li>
                            <li>Event: <code>.on('select2:select', ...)</code> atau <code>.on('change', ...)</code></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
{{-- Select2 CSS & JS --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(function () {

    /* ─────────────────────────────────────────
       Inisialisasi Select2 pada #selKota2
    ───────────────────────────────────────── */
    $('#selKota2').select2({
        theme: 'bootstrap-5',
        placeholder: '-- Pilih Kota --',
        allowClear: true,
        width: '100%'
    });

    /* ─────────────────────────────────────────
       CARD 1 — Select Biasa
    ───────────────────────────────────────── */

    // Tambah opsi kota baru ke select biasa
    $('#btnTambahKota1').on('click', function () {
        const kota = $('#inpKota1').val().trim();
        if (!kota) { alert('Masukkan nama kota!'); return; }

        // Cek duplikat
        let exists = false;
        $('#selKota1 option').each(function () {
            if ($(this).val() === kota) { exists = true; return false; }
        });
        if (exists) { alert(`Kota "${kota}" sudah ada!`); return; }

        // Tambah option — value = nama kota (sesuai ketentuan)
        $('#selKota1').append(`<option value="${kota}">${kota}</option>`);
        $('#inpKota1').val('');
    });

    // Tampilkan kota terpilih saat select berubah
    $('#selKota1').on('change', function () {
        const val = $(this).val();
        if (val) {
            $('#kotaTerpilih1').html(`<i class="mdi mdi-map-marker me-1"></i>${val}`);
        } else {
            $('#kotaTerpilih1').html('<em class="text-muted fw-normal small">Belum ada</em>');
        }
    });

    /* ─────────────────────────────────────────
       CARD 2 — Select2
    ───────────────────────────────────────── */

    // Tambah opsi kota baru ke select2
    $('#btnTambahKota2').on('click', function () {
        const kota = $('#inpKota2').val().trim();
        if (!kota) { alert('Masukkan nama kota!'); return; }

        let exists = false;
        $('#selKota2 option').each(function () {
            if ($(this).val() === kota) { exists = true; return false; }
        });
        if (exists) { alert(`Kota "${kota}" sudah ada!`); return; }

        // Untuk select2: buat option baru lalu trigger change agar select2 refresh
        const $option = new Option(kota, kota, false, false);
        $('#selKota2').append($option).trigger('change');
        $('#inpKota2').val('');
    });

    // Tampilkan kota terpilih — gunakan event 'change' (kompatibel dengan select2)
    $('#selKota2').on('change', function () {
        const val = $(this).val();
        if (val) {
            $('#kotaTerpilih2').html(`<i class="mdi mdi-map-marker me-1"></i>${val}`);
        } else {
            $('#kotaTerpilih2').html('<em class="text-muted fw-normal small">Belum ada</em>');
        }
    });

});
</script>
@endpush
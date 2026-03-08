@extends('layouts.app')

@section('content')
@include('layouts.header', ['title' => 'Studi Kasus 2 & 3 – Tabel HTML Biasa', 'icon' => 'mdi-table'])

{{-- ── Alert area ── --}}
<div id="alertArea"></div>

<div class="row">
    {{-- ── Form Tambah ── --}}
    <div class="col-lg-4 grid-margin stretch-card">
        <div class="card">
            <div class="card-header bg-gradient-primary text-white py-2">
                <i class="mdi mdi-plus-circle me-1"></i> Tambah Barang
            </div>
            <div class="card-body">
                <p class="text-muted small">
                    Data hanya ditampilkan di tabel (tidak disimpan ke database).
                </p>

                <form id="frmTambah">
                    <div class="form-group mb-3">
                        <label class="fw-semibold">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" id="inpNama" class="form-control"
                               placeholder="Contoh: Pensil 2B" required maxlength="50">
                    </div>
                    <div class="form-group mb-3">
                        <label class="fw-semibold">Harga (Rp) <span class="text-danger">*</span></label>
                        <input type="number" id="inpHarga" class="form-control"
                               placeholder="Contoh: 5000" required min="0">
                    </div>
                </form>

                {{-- Button di luar form ──────────────────── --}}
                <button type="button" id="btnTambah" class="btn btn-gradient-primary w-100 mt-2">
                    <span id="tambahIcon"><i class="mdi mdi-content-save me-1"></i></span>
                    <span id="tambahText">Simpan ke Tabel</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ── Tabel ── --}}
    <div class="col-lg-8 grid-margin stretch-card">
        <div class="card">
            <div class="card-header bg-gradient-success text-white py-2 d-flex justify-content-between align-items-center">
                <span><i class="mdi mdi-table me-1"></i> Daftar Barang</span>
                <a href="{{ route('js.studi3_dt') }}" class="btn btn-sm btn-light">
                    <i class="mdi mdi-swap-horizontal me-1"></i> Versi DataTables
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="tblBarang">
                        <thead class="table-dark">
                            <tr>
                                <th style="width:120px">ID Barang</th>
                                <th>Nama Barang</th>
                                <th>Harga</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyBarang">
                            <tr id="emptyRow">
                                <td colspan="3" class="text-center text-muted py-4">
                                    <i class="mdi mdi-inbox mdi-24px d-block mb-1"></i>
                                    Belum ada data. Tambahkan barang lewat form di kiri.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer text-muted small">
                    Klik baris untuk edit / hapus.
                    <span class="float-end" id="infoCount"></span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════
     Modal Edit / Hapus (Studi Kasus 3)
═══════════════════════════════════════════ --}}
<div class="modal fade" id="modalEditHapus" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-gradient-warning text-white">
                <h5 class="modal-title"><i class="mdi mdi-pencil me-1"></i> Detail Barang</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="frmEdit">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="fw-semibold">ID Barang</label>
                        <input type="text" id="editId" class="form-control bg-light" readonly>
                    </div>
                    <div class="form-group mb-3">
                        <label class="fw-semibold">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" id="editNama" class="form-control" required maxlength="50">
                    </div>
                    <div class="form-group mb-3">
                        <label class="fw-semibold">Harga (Rp) <span class="text-danger">*</span></label>
                        <input type="number" id="editHarga" class="form-control" required min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="btnHapus"
                            class="btn btn-gradient-danger">
                        <span id="hapusIcon"><i class="mdi mdi-delete me-1"></i></span>
                        <span id="hapusText">Hapus</span>
                    </button>
                    <button type="button" id="btnUbah"
                            class="btn btn-gradient-primary">
                        <span id="ubahIcon"><i class="mdi mdi-content-save me-1"></i></span>
                        <span id="ubahText">Ubah</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(function () {

    let rowCount  = 0;   // counter untuk ID auto-increment
    let $activeRow = null; // row yang sedang diklik

    /* ────────────────────────────────────────
       Helper: format rupiah
    ──────────────────────────────────────── */
    function formatRupiah(angka) {
        return 'Rp ' + parseInt(angka).toLocaleString('id-ID');
    }

    /* ────────────────────────────────────────
       Helper: spinner on/off
    ──────────────────────────────────────── */
    function setSpinner($btn, $icon, $text, label, spinning) {
        $btn.prop('disabled', spinning);
        if (spinning) {
            $icon.html('<span class="spinner-border spinner-border-sm me-1" role="status"></span>');
            $text.text(label);
        } else {
            // dikembalikan saat modal tutup, bukan di sini
        }
    }

    /* ────────────────────────────────────────
       Helper: tampilkan alert
    ──────────────────────────────────────── */
    function showAlert(msg, type = 'success') {
        const html = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                <i class="mdi mdi-check-circle me-1"></i> ${msg}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>`;
        $('#alertArea').html(html);
        setTimeout(() => $('#alertArea .alert').alert('close'), 3000);
    }

    /* ────────────────────────────────────────
       Helper: update info count
    ──────────────────────────────────────── */
    function updateCount() {
        const n = $('#tbodyBarang tr.data-row').length;
        $('#infoCount').text(n > 0 ? `${n} barang` : '');
    }

    /* ════════════════════════════════════════
       TAMBAH BARANG
    ════════════════════════════════════════ */
    $('#btnTambah').on('click', function () {
        const form = document.getElementById('frmTambah');

        // Validasi HTML5
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const nama   = $('#inpNama').val().trim();
        const harga  = parseInt($('#inpHarga').val());

        // Spinner
        setSpinner($('#btnTambah'), $('#tambahIcon'), $('#tambahText'), 'Menyimpan...', true);

        // Simulasi delay proses (bisa diganti AJAX)
        setTimeout(function () {
            rowCount++;
            const id = 'BRG' + String(rowCount).padStart(4, '0');

            // Hapus baris kosong jika ada
            $('#emptyRow').remove();

            // Append row baru
            const $row = $(`
                <tr class="data-row" style="cursor:pointer;" data-id="${id}" data-nama="${nama}" data-harga="${harga}">
                    <td><span class="badge badge-gradient-info font-monospace">${id}</span></td>
                    <td>${nama}</td>
                    <td>${formatRupiah(harga)}</td>
                </tr>
            `);

            $('#tbodyBarang').append($row);
            bindRowClick($row);

            // Reset form
            $('#inpNama').val('');
            $('#inpHarga').val('');

            updateCount();
            showAlert(`Barang "<strong>${nama}</strong>" berhasil ditambahkan.`);

            // Kembalikan button
            $('#tambahIcon').html('<i class="mdi mdi-content-save me-1"></i>');
            $('#tambahText').text('Simpan ke Tabel');
            $('#btnTambah').prop('disabled', false);

        }, 600);
    });

    /* ════════════════════════════════════════
       ROW CLICK → buka modal (Studi Kasus 3)
    ════════════════════════════════════════ */
    function bindRowClick($row) {
        $row.on('mouseenter', function () { $(this).css('cursor', 'pointer'); })
            .on('click', function () {
                $activeRow = $(this);

                $('#editId').val($activeRow.data('id'));
                $('#editNama').val($activeRow.data('nama'));
                $('#editHarga').val($activeRow.data('harga'));

                // Reset tombol modal
                $('#ubahIcon').html('<i class="mdi mdi-content-save me-1"></i>');
                $('#ubahText').text('Ubah');
                $('#btnUbah').prop('disabled', false);
                $('#hapusIcon').html('<i class="mdi mdi-delete me-1"></i>');
                $('#hapusText').text('Hapus');
                $('#btnHapus').prop('disabled', false);

                new bootstrap.Modal(document.getElementById('modalEditHapus')).show();
            });
    }

    /* ════════════════════════════════════════
       UBAH (Update) dalam modal
    ════════════════════════════════════════ */
    $('#btnUbah').on('click', function () {
        const form = document.getElementById('frmEdit');

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const namaBaru  = $('#editNama').val().trim();
        const hargaBaru = parseInt($('#editHarga').val());

        setSpinner($('#btnUbah'), $('#ubahIcon'), $('#ubahText'), 'Menyimpan...', true);

        setTimeout(function () {
            // Update data attribute & tampilan
            $activeRow
                .data('nama',  namaBaru)
                .data('harga', hargaBaru);

            $activeRow.find('td:eq(1)').text(namaBaru);
            $activeRow.find('td:eq(2)').text(formatRupiah(hargaBaru));

            bootstrap.Modal.getInstance(document.getElementById('modalEditHapus')).hide();
            showAlert(`Barang berhasil diubah menjadi "<strong>${namaBaru}</strong>".`);
        }, 500);
    });

    /* ════════════════════════════════════════
       HAPUS (Delete) dalam modal
    ════════════════════════════════════════ */
    $('#btnHapus').on('click', function () {
        setSpinner($('#btnHapus'), $('#hapusIcon'), $('#hapusText'), 'Menghapus...', true);

        setTimeout(function () {
            const nama = $activeRow.data('nama');
            $activeRow.remove();

            // Tampilkan baris kosong jika tabel sudah habis
            if ($('#tbodyBarang tr.data-row').length === 0) {
                $('#tbodyBarang').html(`
                    <tr id="emptyRow">
                        <td colspan="3" class="text-center text-muted py-4">
                            <i class="mdi mdi-inbox mdi-24px d-block mb-1"></i>
                            Belum ada data. Tambahkan barang lewat form di kiri.
                        </td>
                    </tr>`);
            }

            updateCount();
            bootstrap.Modal.getInstance(document.getElementById('modalEditHapus')).hide();
            showAlert(`Barang "<strong>${nama}</strong>" berhasil dihapus.`, 'danger');
        }, 500);
    });

});
</script>
@endpush
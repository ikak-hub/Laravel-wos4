@extends('layouts.app')

@section('content')
@include('layouts.header', ['title' => 'Studi Kasus 2 & 3 – DataTables', 'icon' => 'mdi-table-large'])

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
                <button type="button" id="btnTambah" class="btn btn-gradient-primary w-100 mt-2">
                    <span id="tambahIcon"><i class="mdi mdi-content-save me-1"></i></span>
                    <span id="tambahText">Simpan ke Tabel</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ── Tabel DataTables ── --}}
    <div class="col-lg-8 grid-margin stretch-card">
        <div class="card">
            <div class="card-header bg-gradient-info text-white py-2 d-flex justify-content-between align-items-center">
                <span><i class="mdi mdi-table-large me-1"></i> Daftar Barang (DataTables)</span>
                <a href="{{ route('js.studi2_plain') }}" class="btn btn-sm btn-light">
                    <i class="mdi mdi-swap-horizontal me-1"></i> Versi Tabel Biasa
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover w-100" id="tblBarang">
                        <thead class="table-dark">
                            <tr>
                                <th>ID Barang</th>
                                <th>Nama Barang</th>
                                <th>Harga</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <p class="text-muted small mt-2">Klik baris untuk edit / hapus.</p>
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
                    <button type="button" id="btnHapus" class="btn btn-gradient-danger">
                        <span id="hapusIcon"><i class="mdi mdi-delete me-1"></i></span>
                        <span id="hapusText">Hapus</span>
                    </button>
                    <button type="button" id="btnUbah" class="btn btn-gradient-primary">
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
{{-- DataTables CSS & JS --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
$(function () {

    let rowCount    = 0;
    let activeRowId = null; // ID barang dari row yang di-klik
    let dtData      = [];   // array data untuk DataTables

    /* ── Inisialisasi DataTable kosong ── */
    const dt = $('#tblBarang').DataTable({
        data: dtData,
        columns: [
            { data: 'id',    render: d => `<span class="badge badge-gradient-info font-monospace">${d}</span>` },
            { data: 'nama' },
            { data: 'harga', render: d => 'Rp ' + parseInt(d).toLocaleString('id-ID') }
        ],
        language: {
            emptyTable:  'Belum ada data. Tambahkan barang lewat form di kiri.',
            search:      'Cari:',
            lengthMenu:  'Tampilkan _MENU_ data',
            info:        'Menampilkan _START_–_END_ dari _TOTAL_ data',
            infoEmpty:   'Tidak ada data',
            zeroRecords: 'Data tidak ditemukan',
            paginate: { next: 'Selanjutnya', previous: 'Sebelumnya' }
        },
        order: [[0, 'asc']],
        createdRow: function (row) {
            // Studi Kasus 3: pointer cursor + click handler
            $(row).css('cursor', 'pointer').on('click', function () {
                const rowData = dt.row(this).data();
                activeRowId = rowData.id;

                $('#editId').val(rowData.id);
                $('#editNama').val(rowData.nama);
                $('#editHarga').val(rowData.harga);

                // Reset tombol
                $('#ubahIcon').html('<i class="mdi mdi-content-save me-1"></i>');
                $('#ubahText').text('Ubah');
                $('#btnUbah').prop('disabled', false);
                $('#hapusIcon').html('<i class="mdi mdi-delete me-1"></i>');
                $('#hapusText').text('Hapus');
                $('#btnHapus').prop('disabled', false);

                new bootstrap.Modal(document.getElementById('modalEditHapus')).show();
            });
        }
    });

    /* ── Helper: alert ── */
    function showAlert(msg, type = 'success') {
        $('#alertArea').html(`
            <div class="alert alert-${type} alert-dismissible fade show">
                <i class="mdi mdi-check-circle me-1"></i> ${msg}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>`);
        setTimeout(() => $('#alertArea .alert').alert('close'), 3000);
    }

    /* ════════════════
       TAMBAH BARANG
    ════════════════ */
    $('#btnTambah').on('click', function () {
        const form = document.getElementById('frmTambah');
        if (!form.checkValidity()) { form.reportValidity(); return; }

        const nama  = $('#inpNama').val().trim();
        const harga = parseInt($('#inpHarga').val());

        // Spinner
        $('#btnTambah').prop('disabled', true);
        $('#tambahIcon').html('<span class="spinner-border spinner-border-sm me-1"></span>');
        $('#tambahText').text('Menyimpan...');

        setTimeout(function () {
            rowCount++;
            const id = 'BRG' + String(rowCount).padStart(4, '0');

            // Tambah ke DataTable
            dt.row.add({ id, nama, harga }).draw();

            // Reset form
            $('#inpNama').val('');
            $('#inpHarga').val('');

            showAlert(`Barang "<strong>${nama}</strong>" berhasil ditambahkan.`);

            // Kembalikan button
            $('#tambahIcon').html('<i class="mdi mdi-content-save me-1"></i>');
            $('#tambahText').text('Simpan ke Tabel');
            $('#btnTambah').prop('disabled', false);
        }, 600);
    });

    /* ════════════════
       UBAH
    ════════════════ */
    $('#btnUbah').on('click', function () {
        const form = document.getElementById('frmEdit');
        if (!form.checkValidity()) { form.reportValidity(); return; }

        const namaBaru  = $('#editNama').val().trim();
        const hargaBaru = parseInt($('#editHarga').val());

        $('#btnUbah').prop('disabled', true);
        $('#ubahIcon').html('<span class="spinner-border spinner-border-sm me-1"></span>');
        $('#ubahText').text('Menyimpan...');

        setTimeout(function () {
            // Cari row berdasarkan ID dan update
            dt.rows().every(function () {
                if (this.data().id === activeRowId) {
                    this.data({ id: activeRowId, nama: namaBaru, harga: hargaBaru }).draw();
                    return false;
                }
            });

            bootstrap.Modal.getInstance(document.getElementById('modalEditHapus')).hide();
            showAlert(`Barang berhasil diubah menjadi "<strong>${namaBaru}</strong>".`);
        }, 500);
    });

    /* ════════════════
       HAPUS
    ════════════════ */
    $('#btnHapus').on('click', function () {
        $('#btnHapus').prop('disabled', true);
        $('#hapusIcon').html('<span class="spinner-border spinner-border-sm me-1"></span>');
        $('#hapusText').text('Menghapus...');

        setTimeout(function () {
            let namaHapus = '';
            dt.rows().every(function () {
                if (this.data().id === activeRowId) {
                    namaHapus = this.data().nama;
                    this.remove();
                    return false;
                }
            });
            dt.draw();

            bootstrap.Modal.getInstance(document.getElementById('modalEditHapus')).hide();
            showAlert(`Barang "<strong>${namaHapus}</strong>" berhasil dihapus.`, 'danger');
        }, 500);
    });

});
</script>
@endpush
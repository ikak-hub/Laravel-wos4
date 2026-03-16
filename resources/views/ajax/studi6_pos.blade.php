@extends('layouts.app')

@section('content')
@include('layouts.header', ['title' => 'AJAX – Point of Sales (Kasir)', 'icon' => 'mdi-cash-register'])

<ul class="nav nav-tabs mb-4" id="posTab" role="tablist">
    <li class="nav-item">
        <button class="nav-link active" id="pos-ajax-tab" data-bs-toggle="tab" data-bs-target="#pos-ajax">
            <i class="mdi mdi-jquery me-1"></i> jQuery Ajax
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" id="pos-axios-tab" data-bs-toggle="tab" data-bs-target="#pos-axios">
            <i class="mdi mdi-lightning-bolt me-1"></i> Axios
        </button>
    </li>
</ul>

<div class="tab-content" id="posTabContent">

{{-- ══════════════════ TAB 1: jQuery Ajax ══════════════════ --}}
<div class="tab-pane fade show active" id="pos-ajax">
<div class="row">

    {{-- ── Form input barang ── --}}
    <div class="col-lg-4 grid-margin stretch-card">
        <div class="card">
            <div class="card-header bg-gradient-primary text-white py-2">
                <i class="mdi mdi-barcode-scan me-1"></i> Input Barang
            </div>
            <div class="card-body">
                <p class="text-muted small">
                    Ketik kode barang → tekan <kbd>Enter</kbd>.
                    Nama & harga akan terisi otomatis.
                </p>

                <div class="form-group mb-3">
                    <label class="fw-semibold">Kode Barang</label>
                    <div class="input-group">
                        <input type="text" id="aj-kode" class="form-control font-monospace"
                               placeholder="Contoh: BRG0001" autocomplete="off">
                        <span id="aj-search-spinner" class="input-group-text d-none">
                            <span class="spinner-border spinner-border-sm"></span>
                        </span>
                    </div>
                    <div id="aj-kode-msg" class="small mt-1"></div>
                </div>

                <div class="form-group mb-3">
                    <label class="fw-semibold">Nama Barang</label>
                    <input type="text" id="aj-nama" class="form-control"
                           placeholder="— otomatis —" readonly
                           style="background:#fff0f0;">
                </div>
                <div class="form-group mb-3">
                    <label class="fw-semibold">Harga Barang (Rp)</label>
                    <input type="text" id="aj-harga-display" class="form-control"
                           placeholder="— otomatis —" readonly
                           style="background:#fff0f0;">
                    {{-- hidden: nilai numerik --}}
                    <input type="hidden" id="aj-harga">
                </div>
                <div class="form-group mb-4">
                    <label class="fw-semibold">Jumlah</label>
                    <input type="number" id="aj-jumlah" class="form-control"
                           value="1" min="1" placeholder="1">
                </div>

                {{-- Button Tambahkan: disabled sampai barang ditemukan --}}
                <button type="button" id="aj-btnTambah"
                        class="btn btn-gradient-success w-100"
                        disabled>
                    <span id="aj-tambahIcon"><i class="mdi mdi-plus-circle me-1"></i></span>
                    <span id="aj-tambahText">Tambahkan</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ── Tabel keranjang + total + bayar ── --}}
    <div class="col-lg-8 grid-margin stretch-card">
        <div class="card">
            <div class="card-header bg-gradient-dark text-white py-2">
                <i class="mdi mdi-cart me-1"></i> Keranjang Belanja
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="aj-tblKeranjang">
                        <thead class="table-dark">
                            <tr>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th class="text-end">Harga</th>
                                <th class="text-center" style="width:100px">Jumlah</th>
                                <th class="text-end">Subtotal</th>
                                <th class="text-center" style="width:50px"></th>
                            </tr>
                        </thead>
                        <tbody id="aj-tbodyKeranjang">
                            <tr id="aj-emptyRow">
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="mdi mdi-cart-off mdi-24px d-block mb-1"></i>
                                    Belum ada barang. Masukkan kode barang di kiri.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Total --}}
                <div class="card-footer d-flex justify-content-between align-items-center py-3 bg-light">
                    <span class="fw-bold fs-5">TOTAL</span>
                    <span class="fw-bold fs-4 text-success" id="aj-total">Rp 0</span>
                </div>
            </div>

            {{-- Tombol Bayar --}}
            <div class="card-footer text-end">
                <button type="button" id="aj-btnBayar"
                        class="btn btn-gradient-success btn-lg px-5"
                        disabled>
                    <span id="aj-bayarIcon"><i class="mdi mdi-cash me-1"></i></span>
                    <span id="aj-bayarText">Bayar</span>
                </button>
            </div>
        </div>
    </div>

</div>
</div>{{-- /tab ajax --}}

{{-- ══════════════════ TAB 2: Axios ══════════════════ --}}
<div class="tab-pane fade" id="pos-axios">
<div class="row">

    <div class="col-lg-4 grid-margin stretch-card">
        <div class="card">
            <div class="card-header bg-gradient-info text-white py-2">
                <i class="mdi mdi-barcode-scan me-1"></i> Input Barang
            </div>
            <div class="card-body">
                <p class="text-muted small">
                    Versi Axios — fungsi identik, konstruksi request berbeda.
                </p>

                <div class="form-group mb-3">
                    <label class="fw-semibold">Kode Barang</label>
                    <div class="input-group">
                        <input type="text" id="ax-kode" class="form-control font-monospace"
                               placeholder="Contoh: BRG0001" autocomplete="off">
                        <span id="ax-search-spinner" class="input-group-text d-none">
                            <span class="spinner-border spinner-border-sm"></span>
                        </span>
                    </div>
                    <div id="ax-kode-msg" class="small mt-1"></div>
                </div>
                <div class="form-group mb-3">
                    <label class="fw-semibold">Nama Barang</label>
                    <input type="text" id="ax-nama" class="form-control"
                           placeholder="— otomatis —" readonly style="background:#f0f8ff;">
                </div>
                <div class="form-group mb-3">
                    <label class="fw-semibold">Harga Barang (Rp)</label>
                    <input type="text" id="ax-harga-display" class="form-control"
                           placeholder="— otomatis —" readonly style="background:#f0f8ff;">
                    <input type="hidden" id="ax-harga">
                </div>
                <div class="form-group mb-4">
                    <label class="fw-semibold">Jumlah</label>
                    <input type="number" id="ax-jumlah" class="form-control"
                           value="1" min="1">
                </div>

                <button type="button" id="ax-btnTambah"
                        class="btn btn-gradient-info w-100"
                        disabled>
                    <span id="ax-tambahIcon"><i class="mdi mdi-plus-circle me-1"></i></span>
                    <span id="ax-tambahText">Tambahkan</span>
                </button>
            </div>
        </div>
    </div>

    <div class="col-lg-8 grid-margin stretch-card">
        <div class="card">
            <div class="card-header bg-gradient-dark text-white py-2">
                <i class="mdi mdi-cart me-1"></i> Keranjang Belanja
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th class="text-end">Harga</th>
                                <th class="text-center" style="width:100px">Jumlah</th>
                                <th class="text-end">Subtotal</th>
                                <th class="text-center" style="width:50px"></th>
                            </tr>
                        </thead>
                        <tbody id="ax-tbodyKeranjang">
                            <tr id="ax-emptyRow">
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="mdi mdi-cart-off mdi-24px d-block mb-1"></i>
                                    Belum ada barang.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center py-3 bg-light">
                    <span class="fw-bold fs-5">TOTAL</span>
                    <span class="fw-bold fs-4 text-info" id="ax-total">Rp 0</span>
                </div>
            </div>
            <div class="card-footer text-end">
                <button type="button" id="ax-btnBayar"
                        class="btn btn-gradient-info btn-lg px-5"
                        disabled>
                    <span id="ax-bayarIcon"><i class="mdi mdi-cash me-1"></i></span>
                    <span id="ax-bayarText">Bayar</span>
                </button>
            </div>
        </div>
    </div>

</div>
</div>{{-- /tab axios --}}

</div>{{-- /tab-content --}}
@endsection

@push('scripts')
{{-- SweetAlert2 + Axios --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
/* ════════════════════════════════════════════════════════════════════
   SHARED HELPERS
══════════════════════════════════════════════════════════════════════ */
function formatRupiah(angka) {
    return 'Rp ' + parseInt(angka).toLocaleString('id-ID');
}

/* ════════════════════════════════════════════════════════════════════
   ▌TAB 1 — jQuery Ajax POS
══════════════════════════════════════════════════════════════════════ */
$(function () {

    let ajKeranjang = {}; // { id_barang: { id_barang, nama, harga, jumlah } }

    // ── Hitung & tampilkan total ────────────────────────────────────
    function ajHitungTotal() {
        let total = 0;
        Object.values(ajKeranjang).forEach(function (item) {
            total += item.harga * item.jumlah;
        });
        $('#aj-total').text(formatRupiah(total));
        $('#aj-btnBayar').prop('disabled', Object.keys(ajKeranjang).length === 0);
    }

    // ── Render ulang tabel ──────────────────────────────────────────
    function ajRenderTabel() {
        const $tbody = $('#aj-tbodyKeranjang');
        $tbody.empty();

        if (Object.keys(ajKeranjang).length === 0) {
            $tbody.html(`<tr id="aj-emptyRow">
                <td colspan="6" class="text-center text-muted py-4">
                    <i class="mdi mdi-cart-off mdi-24px d-block mb-1"></i>
                    Belum ada barang.
                </td></tr>`);
            ajHitungTotal();
            return;
        }

        Object.values(ajKeranjang).forEach(function (item) {
            const subtotal = item.harga * item.jumlah;
            const $row = $(`
                <tr data-id="${item.id_barang}">
                    <td><span class="badge badge-gradient-info font-monospace">${item.id_barang}</span></td>
                    <td>${item.nama}</td>
                    <td class="text-end">${formatRupiah(item.harga)}</td>
                    <td class="text-center">
                        <input type="number" class="form-control form-control-sm text-center aj-qty"
                               value="${item.jumlah}" min="1" style="width:70px;margin:auto;">
                    </td>
                    <td class="text-end aj-subtotal">${formatRupiah(subtotal)}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-gradient-danger aj-hapus" title="Hapus">
                            <i class="mdi mdi-delete"></i>
                        </button>
                    </td>
                </tr>
            `);
            $tbody.append($row);
        });

        // Event: ubah jumlah di tabel
        $('#aj-tbodyKeranjang').find('.aj-qty').on('change', function () {
            const $row = $(this).closest('tr');
            const id   = $row.data('id');
            let qty    = parseInt($(this).val());
            if (isNaN(qty) || qty < 1) { qty = 1; $(this).val(1); }

            ajKeranjang[id].jumlah = qty;
            const sub = ajKeranjang[id].harga * qty;
            $row.find('.aj-subtotal').text(formatRupiah(sub));
            ajHitungTotal();
        });

        // Event: hapus baris
        $('#aj-tbodyKeranjang').find('.aj-hapus').on('click', function () {
            const id = $(this).closest('tr').data('id');
            delete ajKeranjang[id];
            ajRenderTabel();
        });

        ajHitungTotal();
    }

    // ── Cari barang saat Enter di input kode ────────────────────────
    $('#aj-kode').on('keypress', function (e) {
        if (e.which !== 13) return; // hanya Enter
        const kode = $(this).val().trim();
        if (!kode) return;

        // Reset fields
        $('#aj-nama').val('');
        $('#aj-harga-display').val('');
        $('#aj-harga').val('');
        $('#aj-jumlah').val(1);
        $('#aj-btnTambah').prop('disabled', true);
        $('#aj-kode-msg').text('').removeClass('text-danger text-success');

        // Spinner ON
        $('#aj-search-spinner').removeClass('d-none');

        $.ajax({
            url: "{{ url('/ajax/pos/cari') }}/" + encodeURIComponent(kode),
            type: "GET",
            success: function (response) {
                $('#aj-search-spinner').addClass('d-none');
                console.log(response); // debugging

                if (response.status === 'success') {
                    const b = response.data;
                    $('#aj-nama').val(b.nama);
                    $('#aj-harga').val(b.harga);
                    $('#aj-harga-display').val(formatRupiah(b.harga));
                    $('#aj-jumlah').val(1);
                    $('#aj-btnTambah').prop('disabled', false);
                    $('#aj-kode-msg')
                        .text('✓ ' + response.message)
                        .addClass('text-success');
                }
            },
            error: function (xhr) {
                $('#aj-search-spinner').addClass('d-none');
                const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan.';
                $('#aj-kode-msg').text('✗ ' + msg).addClass('text-danger');
                console.log('Error cari barang:', xhr);
            }
        });
    });

    // ── Jumlah berubah → non-aktifkan Tambah jika jumlah ≤ 0 ───────
    $('#aj-jumlah').on('input', function () {
        const hasBrg  = $('#aj-harga').val() !== '';
        const qtyOk   = parseInt($(this).val()) > 0;
        $('#aj-btnTambah').prop('disabled', !(hasBrg && qtyOk));
    });

    // ── Klik Tambahkan ──────────────────────────────────────────────
    $('#aj-btnTambah').on('click', function () {
        const id    = $('#aj-kode').val().trim();
        const nama  = $('#aj-nama').val();
        const harga = parseInt($('#aj-harga').val());
        const qty   = parseInt($('#aj-jumlah').val());

        // Spinner
        $('#aj-btnTambah').prop('disabled', true);
        $('#aj-tambahIcon').html('<span class="spinner-border spinner-border-sm me-1"></span>');
        $('#aj-tambahText').text('Menambahkan...');

        setTimeout(function () {
            if (ajKeranjang[id]) {
                // Barang sudah ada: update jumlah + subtotal
                ajKeranjang[id].jumlah += qty;
            } else {
                ajKeranjang[id] = { id_barang: id, nama, harga, jumlah: qty };
            }

            ajRenderTabel();

            // Reset input form
            $('#aj-kode').val('').focus();
            $('#aj-nama').val('');
            $('#aj-harga').val('');
            $('#aj-harga-display').val('');
            $('#aj-jumlah').val(1);
            $('#aj-kode-msg').text('');

            // Kembalikan button
            $('#aj-tambahIcon').html('<i class="mdi mdi-plus-circle me-1"></i>');
            $('#aj-tambahText').text('Tambahkan');
            // Tetap disabled — harus scan ulang
        }, 400);
    });

    // ── Klik Bayar ─────────────────────────────────────────────────
    $('#aj-btnBayar').on('click', function () {
        $('#aj-btnBayar').prop('disabled', true);
        $('#aj-bayarIcon').html('<span class="spinner-border spinner-border-sm me-1"></span>');
        $('#aj-bayarText').text('Memproses...');

        // Kumpulkan data
        const items = Object.values(ajKeranjang).map(function (item) {
            return {
                id_barang: item.id_barang,
                jumlah:    item.jumlah,
                subtotal:  item.harga * item.jumlah,
            };
        });
        let total = items.reduce(function (s, i) { return s + i.subtotal; }, 0);

        $.ajax({
            url: "{{ route('ajax.pos.bayar') }}",
            type: "POST",
            data: JSON.stringify({ total, items }),
            contentType: "application/json",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (response) {
                console.log(response);
                $('#aj-bayarIcon').html('<i class="mdi mdi-cash me-1"></i>');
                $('#aj-bayarText').text('Bayar');

                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Transaksi Berhasil!',
                        text: response.message,
                        confirmButtonColor: '#4B49AC',
                    }).then(function () {
                        // Kosongkan halaman
                        ajKeranjang = {};
                        ajRenderTabel();
                    });
                }
            },
            error: function (xhr) {
                $('#aj-bayarIcon').html('<i class="mdi mdi-cash me-1"></i>');
                $('#aj-bayarText').text('Bayar');
                $('#aj-btnBayar').prop('disabled', false);

                const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan.';
                Swal.fire({ icon: 'error', title: 'Gagal!', text: msg });
            }
        });
    });

});

/* ════════════════════════════════════════════════════════════════════
   ▌TAB 2 — Axios POS
══════════════════════════════════════════════════════════════════════ */
(function () {

    // Set CSRF header global
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    // Content-Type untuk POST JSON
    axios.defaults.headers.post['Content-Type'] = 'application/json';

    let axKeranjang = {};

    function axHitungTotal() {
        let total = 0;
        Object.values(axKeranjang).forEach(function (item) {
            total += item.harga * item.jumlah;
        });
        document.getElementById('ax-total').textContent = formatRupiah(total);
        document.getElementById('ax-btnBayar').disabled = Object.keys(axKeranjang).length === 0;
    }

    function axRenderTabel() {
        const tbody = document.getElementById('ax-tbodyKeranjang');
        tbody.innerHTML = '';

        if (Object.keys(axKeranjang).length === 0) {
            tbody.innerHTML = `<tr id="ax-emptyRow">
                <td colspan="6" class="text-center text-muted py-4">
                    <i class="mdi mdi-cart-off mdi-24px d-block mb-1"></i>Belum ada barang.</td></tr>`;
            axHitungTotal();
            return;
        }

        Object.values(axKeranjang).forEach(function (item) {
            const sub = item.harga * item.jumlah;
            const tr = document.createElement('tr');
            tr.dataset.id = item.id_barang;
            tr.innerHTML = `
                <td><span class="badge badge-gradient-info font-monospace">${item.id_barang}</span></td>
                <td>${item.nama}</td>
                <td class="text-end">${formatRupiah(item.harga)}</td>
                <td class="text-center">
                    <input type="number" class="form-control form-control-sm text-center ax-qty"
                           value="${item.jumlah}" min="1" style="width:70px;margin:auto;">
                </td>
                <td class="text-end ax-subtotal">${formatRupiah(sub)}</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-gradient-danger ax-hapus">
                        <i class="mdi mdi-delete"></i>
                    </button>
                </td>`;
            tbody.appendChild(tr);
        });

        // Events
        tbody.querySelectorAll('.ax-qty').forEach(function (inp) {
            inp.addEventListener('change', function () {
                const id  = this.closest('tr').dataset.id;
                let qty   = parseInt(this.value);
                if (isNaN(qty) || qty < 1) { qty = 1; this.value = 1; }
                axKeranjang[id].jumlah = qty;
                this.closest('tr').querySelector('.ax-subtotal').textContent =
                    formatRupiah(axKeranjang[id].harga * qty);
                axHitungTotal();
            });
        });

        tbody.querySelectorAll('.ax-hapus').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const id = this.closest('tr').dataset.id;
                delete axKeranjang[id];
                axRenderTabel();
            });
        });

        axHitungTotal();
    }

    // ── Enter di input kode ─────────────────────────────────────────
    document.getElementById('ax-kode').addEventListener('keypress', function (e) {
        if (e.key !== 'Enter') return;
        const kode = this.value.trim();
        if (!kode) return;

        document.getElementById('ax-nama').value = '';
        document.getElementById('ax-harga-display').value = '';
        document.getElementById('ax-harga').value = '';
        document.getElementById('ax-jumlah').value = 1;
        document.getElementById('ax-btnTambah').disabled = true;
        const msgEl = document.getElementById('ax-kode-msg');
        msgEl.textContent = '';
        msgEl.className = 'small mt-1';

        document.getElementById('ax-search-spinner').classList.remove('d-none');

        axios.get('/ajax/pos/cari/' + encodeURIComponent(kode))
        .then(function (response) {
            document.getElementById('ax-search-spinner').classList.add('d-none');
            console.log(response.data); // debugging

            if (response.data.status === 'success') {
                const b = response.data.data;
                document.getElementById('ax-nama').value         = b.nama;
                document.getElementById('ax-harga').value        = b.harga;
                document.getElementById('ax-harga-display').value = formatRupiah(b.harga);
                document.getElementById('ax-jumlah').value       = 1;
                document.getElementById('ax-btnTambah').disabled = false;
                msgEl.textContent = '✓ ' + response.data.message;
                msgEl.classList.add('text-success');
            }
        })
        .catch(function (error) {
            document.getElementById('ax-search-spinner').classList.add('d-none');
            const msg = error.response && error.response.data ? error.response.data.message : 'Terjadi kesalahan.';
            msgEl.textContent = '✗ ' + msg;
            msgEl.classList.add('text-danger');
            console.log('Error Axios cari barang:', error);
        });
    });

    // ── Jumlah berubah ──────────────────────────────────────────────
    document.getElementById('ax-jumlah').addEventListener('input', function () {
        const hasBrg = document.getElementById('ax-harga').value !== '';
        const qtyOk  = parseInt(this.value) > 0;
        document.getElementById('ax-btnTambah').disabled = !(hasBrg && qtyOk);
    });

    // ── Tambahkan ───────────────────────────────────────────────────
    document.getElementById('ax-btnTambah').addEventListener('click', function () {
        const id    = document.getElementById('ax-kode').value.trim();
        const nama  = document.getElementById('ax-nama').value;
        const harga = parseInt(document.getElementById('ax-harga').value);
        const qty   = parseInt(document.getElementById('ax-jumlah').value);

        this.disabled = true;
        document.getElementById('ax-tambahIcon').innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>';
        document.getElementById('ax-tambahText').textContent = 'Menambahkan...';

        setTimeout(function () {
            if (axKeranjang[id]) {
                axKeranjang[id].jumlah += qty;
            } else {
                axKeranjang[id] = { id_barang: id, nama, harga, jumlah: qty };
            }

            axRenderTabel();

            document.getElementById('ax-kode').value          = '';
            document.getElementById('ax-nama').value          = '';
            document.getElementById('ax-harga').value         = '';
            document.getElementById('ax-harga-display').value = '';
            document.getElementById('ax-jumlah').value        = 1;
            document.getElementById('ax-kode-msg').textContent = '';
            document.getElementById('ax-kode').focus();

            document.getElementById('ax-tambahIcon').innerHTML = '<i class="mdi mdi-plus-circle me-1"></i>';
            document.getElementById('ax-tambahText').textContent = 'Tambahkan';
        }, 400);
    });

    // ── Bayar ───────────────────────────────────────────────────────
    document.getElementById('ax-btnBayar').addEventListener('click', function () {
        this.disabled = true;
        document.getElementById('ax-bayarIcon').innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>';
        document.getElementById('ax-bayarText').textContent = 'Memproses...';

        const items = Object.values(axKeranjang).map(function (item) {
            return { id_barang: item.id_barang, jumlah: item.jumlah, subtotal: item.harga * item.jumlah };
        });
        const total = items.reduce(function (s, i) { return s + i.subtotal; }, 0);

        // Axios POST — data dikirim sebagai JSON
        axios.post("{{ route('ajax.pos.bayar') }}", { total, items })
        .then(function (response) {
            console.log(response.data);
            document.getElementById('ax-bayarIcon').innerHTML = '<i class="mdi mdi-cash me-1"></i>';
            document.getElementById('ax-bayarText').textContent = 'Bayar';

            Swal.fire({
                icon: 'success',
                title: 'Transaksi Berhasil!',
                text: response.data.message,
                confirmButtonColor: '#0090E7',
            }).then(function () {
                axKeranjang = {};
                axRenderTabel();
            });
        })
        .catch(function (error) {
            document.getElementById('ax-bayarIcon').innerHTML = '<i class="mdi mdi-cash me-1"></i>';
            document.getElementById('ax-bayarText').textContent = 'Bayar';
            document.getElementById('ax-btnBayar').disabled = false;

            const msg = error.response && error.response.data
                        ? error.response.data.message : 'Terjadi kesalahan.';
            Swal.fire({ icon: 'error', title: 'Gagal!', text: msg });
            console.log('Error Axios bayar:', error);
        });
    });

})();
</script>
@endpush
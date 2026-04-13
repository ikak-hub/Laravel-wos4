@extends('layouts.kantin')

@section('content')
<div class="row">

    {{-- ── Kiri: Form Pilih Menu ──────────────────────────────── --}}
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header bg-gradient-primary text-white py-2">
                <i class="mdi mdi-food me-1"></i> Pilih Menu
            </div>
            <div class="card-body">

                {{-- Select Vendor --}}
                <div class="form-group mb-3">
                    <label class="fw-semibold">Pilih Kantin / Vendor</label>
                    <select id="selVendor" class="form-select">
                        <option value="">-- Pilih Kantin --</option>
                        @foreach($vendors as $v)
                        <option value="{{ $v->idvendor }}">{{ $v->nama_vendor }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Select Menu (cascading) --}}
                <div class="form-group mb-3">
                    <label class="fw-semibold">Pilih Menu</label>
                    <select id="selMenu" class="form-select" disabled>
                        <option value="">-- Pilih Menu --</option>
                    </select>
                    <div id="loadMenuSpinner" class="text-muted small mt-1 d-none">
                        <span class="spinner-border spinner-border-sm me-1"></span> Memuat menu...
                    </div>
                </div>

                {{-- Harga --}}
                <div class="form-group mb-3">
                    <label class="fw-semibold">Harga</label>
                    <input type="text" id="inpHargaDisp" class="form-control"
                        placeholder="— otomatis —" readonly style="background:#fff0f0;">
                    <input type="hidden" id="inpHargaVal">
                    <input type="hidden" id="inpMenuId">
                    <input type="hidden" id="inpMenuNama">
                </div>

                {{-- Jumlah --}}
                <div class="form-group mb-3">
                    <label class="fw-semibold">Jumlah</label>
                    <input type="number" id="inpJumlah" class="form-control" value="1" min="1">
                </div>

                {{-- Catatan --}}
                <div class="form-group mb-4">
                    <label class="fw-semibold">Catatan <small class="text-muted">(opsional)</small></label>
                    <input type="text" id="inpCatatan" class="form-control"
                        placeholder="Contoh: tidak pedas, tambah nasi">
                </div>

                <button type="button" id="btnTambah"
                    class="btn btn-gradient-success w-100" disabled>
                    <i class="mdi mdi-plus-circle me-1"></i> Tambah ke Keranjang
                </button>
            </div>
        </div>
    </div>

    {{-- ── Kanan: Keranjang ────────────────────────────────────── --}}
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header bg-gradient-dark text-white py-2">
                <i class="mdi mdi-cart me-1"></i> Keranjang Pesanan
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Menu</th>
                                <th class="text-end">Harga</th>
                                <th class="text-center" style="width:90px;">Qty</th>
                                <th class="text-end">Subtotal</th>
                                <th style="width:48px;"></th>
                            </tr>
                        </thead>
                        <tbody id="tbodyKeranjang">
                            <tr id="emptyRow">
                                <td colspan="5" class="text-center text-muted py-5">
                                    <i class="mdi mdi-cart-off mdi-36px d-block mb-2"></i>
                                    Keranjang masih kosong.<br>
                                    <small>Pilih vendor & menu di sebelah kiri.</small>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center px-3 py-3 bg-light border-top">
                    <span class="fw-bold fs-5">TOTAL</span>
                    <span class="fw-bold fs-4 text-success" id="totalDisplay">Rp 0</span>
                </div>
            </div>
            <div class="card-footer text-end">
                <button type="button" id="btnBayar"
                    class="btn btn-gradient-primary btn-lg px-5" disabled>
                    <i class="mdi mdi-credit-card me-1"></i> Bayar Sekarang
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ── Modal Sukses ──────────────────────────────────────────── --}}
<div class="modal fade" id="modalSukses" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center border-0 shadow-lg">
            <div class="modal-body py-5 px-4">
                <div class="mb-3" style="font-size:64px;">✅</div>
                <h4 class="fw-bold mb-2">Pembayaran Berhasil!</h4>
                <p class="text-muted mb-1">Pesanan anda tercatat sebagai:</p>
                <h3 class="fw-bold text-primary mb-1" id="guestNameDisplay">—</h3>

                <div id="qrCodeContainer" class="my-3" style="display:none;">
                    <p class="text-muted small mb-2">QR Code Pesanan:</p>
                    <img id="qrCodeImg" src="" alt="QR Code"
                        style="width:160px;height:160px;border:1px solid #dee2e6;border-radius:8px;">
                </div>

                <p class="text-muted small mb-4">Tunjukkan nama ini kepada kasir untuk mengambil pesanan.</p>
                <button type="button" class="btn btn-gradient-primary px-5"
                    onclick="location.reload()">
                    <i class="mdi mdi-refresh me-1"></i> Pesan Lagi
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {

        let keranjang = {}; // { key: { key, idmenu, nama, harga, qty, subtotal, catatan } }
        let vendorId = null;

        /* ─── Format Rupiah ──────────────────────────────────── */
        function formatRp(n) {
            return 'Rp ' + parseInt(n).toLocaleString('id-ID');
        }

        /* ─── Hitung & update total ─────────────────────────── */
        function hitungTotal() {
            let total = 0;
            Object.values(keranjang).forEach(i => total += i.subtotal);
            $('#totalDisplay').text(formatRp(total));
            $('#btnBayar').prop('disabled', Object.keys(keranjang).length === 0);
            return total;
        }

        /* ─── Render keranjang ──────────────────────────────── */
        function renderKeranjang() {
            const $tbody = $('#tbodyKeranjang');
            $tbody.empty();

            if (Object.keys(keranjang).length === 0) {
                $tbody.html(`<tr id="emptyRow">
                <td colspan="5" class="text-center text-muted py-5">
                    <i class="mdi mdi-cart-off mdi-36px d-block mb-2"></i>
                    Keranjang masih kosong.
                </td></tr>`);
                hitungTotal();
                return;
            }

            Object.values(keranjang).forEach(item => {
                $tbody.append(`
                <tr data-key="${item.key}">
                    <td>
                        <div class="fw-semibold">${item.nama}</div>
                        ${item.catatan ? `<small class="text-muted"><i class="mdi mdi-note-text-outline me-1"></i>${item.catatan}</small>` : ''}
                    </td>
                    <td class="text-end">${formatRp(item.harga)}</td>
                    <td class="text-center">
                        <input type="number" class="form-control form-control-sm text-center qty-input"
                               value="${item.qty}" min="1"
                               style="width:65px;margin:auto;" data-key="${item.key}">
                    </td>
                    <td class="text-end fw-semibold">${formatRp(item.subtotal)}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-gradient-danger btn-hapus" data-key="${item.key}">
                            <i class="mdi mdi-delete"></i>
                        </button>
                    </td>
                </tr>`);
            });

            // Event: ubah qty
            $tbody.find('.qty-input').on('change', function() {
                const key = $(this).data('key');
                let qty = parseInt($(this).val()) || 1;
                if (qty < 1) {
                    qty = 1;
                    $(this).val(1);
                }
                keranjang[key].qty = qty;
                keranjang[key].subtotal = keranjang[key].harga * qty;
                renderKeranjang();
            });

            // Event: hapus item
            $tbody.find('.btn-hapus').on('click', function() {
                delete keranjang[$(this).data('key')];
                renderKeranjang();
            });

            hitungTotal();
        }

        /* ─── Vendor berubah → load menus ──────────────────── */
        $('#selVendor').on('change', function() {
            vendorId = $(this).val();
            $('#selMenu').html('<option value="">-- Pilih Menu --</option>').prop('disabled', true);
            $('#inpHargaDisp').val('');
            $('#inpHargaVal').val('');
            $('#inpMenuId').val('');
            $('#inpMenuNama').val('');
            $('#btnTambah').prop('disabled', true);

            // Reset keranjang saat ganti vendor
            if (Object.keys(keranjang).length > 0) {
                keranjang = {};
                renderKeranjang();
            }

            if (!vendorId) return;

            $('#loadMenuSpinner').removeClass('d-none');
            $.ajax({
                url: "{{ url('/kantin/menu') }}/" + vendorId,
                type: 'GET',
                success: function(res) {
                    $('#loadMenuSpinner').addClass('d-none');
                    if (res.status === 'success' && res.data.length > 0) {
                        $('#selMenu').prop('disabled', false);
                        res.data.forEach(m => {
                            $('#selMenu').append(
                                `<option value="${m.idmenu}"
                                     data-harga="${m.harga}"
                                     data-nama="${m.nama_menu}">
                                ${m.nama_menu} — ${formatRp(m.harga)}
                            </option>`
                            );
                        });
                    } else {
                        $('#selMenu').html('<option value="">Belum ada menu</option>');
                    }
                },
                error: function() {
                    $('#loadMenuSpinner').addClass('d-none');
                }
            });
        });

        /* ─── Menu berubah → tampilkan harga ───────────────── */
        $('#selMenu').on('change', function() {
            const opt = $(this).find(':selected');
            const id = $(this).val();
            const harga = opt.data('harga');
            const nama = opt.data('nama');

            if (id) {
                $('#inpHargaDisp').val(formatRp(harga));
                $('#inpHargaVal').val(harga);
                $('#inpMenuId').val(id);
                $('#inpMenuNama').val(nama);
                $('#btnTambah').prop('disabled', false);
            } else {
                $('#inpHargaDisp').val('');
                $('#inpHargaVal').val('');
                $('#inpMenuId').val('');
                $('#inpMenuNama').val('');
                $('#btnTambah').prop('disabled', true);
            }
        });

        /* ─── Tambah ke keranjang ──────────────────────────── */
        $('#btnTambah').on('click', function() {
            const id = $('#inpMenuId').val();
            const nama = $('#inpMenuNama').val();
            const harga = parseInt($('#inpHargaVal').val());
            const qty = parseInt($('#inpJumlah').val()) || 1;
            const catatan = $('#inpCatatan').val().trim();

            // Key unik: idmenu + catatan (supaya item sama beda catatan bisa dipisah)
            const key = id + '||' + catatan;

            if (keranjang[key]) {
                keranjang[key].qty += qty;
                keranjang[key].subtotal = keranjang[key].harga * keranjang[key].qty;
            } else {
                keranjang[key] = {
                    key,
                    idmenu: id,
                    nama,
                    harga,
                    qty,
                    subtotal: harga * qty,
                    catatan
                };
            }

            renderKeranjang();

            // Reset input
            $('#selMenu').val('').change();
            $('#inpJumlah').val(1);
            $('#inpCatatan').val('');
        });

        /* ─── Bayar Sekarang ────────────────────────────────── */
        $('#btnBayar').on('click', function() {
            if (!vendorId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih kantin dulu!',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }

            const items = Object.values(keranjang).map(i => ({
                idmenu: i.idmenu,
                jumlah: i.qty,
                harga: i.harga,
                subtotal: i.subtotal,
                catatan: i.catatan || '',
            }));
            const total = items.reduce((s, i) => s + i.subtotal, 0);

            $('#btnBayar').prop('disabled', true)
                .html('<span class="spinner-border spinner-border-sm me-1"></span> Memproses...');

            $.ajax({
                url: "{{ route('kantin.order') }}",
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    idvendor: vendorId,
                    total,
                    items
                }),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    $('#btnBayar').prop('disabled', false)
                        .html('<i class="mdi mdi-credit-card me-1"></i> Bayar Sekarang');

                    if (res.status === 'success') {
                        // ── Buka Midtrans Snap ─────────────────────
                        snap.pay(res.snap_token, {
                            onSuccess: function() {
                                $('#guestNameDisplay').text(res.guest_name);

                                if (res.qr_code) {
                                    $('#qrCodeImg').attr('src', res.qr_code);
                                    $('#qrCodeContainer').show();
                                }
                                new bootstrap.Modal(document.getElementById('modalSukses')).show();
                                keranjang = {};
                                renderKeranjang();
                            },
                            onPending: function() {
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Menunggu Pembayaran',
                                    html: `Pesanan anda: <strong>${res.guest_name}</strong><br>
                                        Selesaikan pembayaran sesuai instruksi yang diberikan.`,
                                });
                                keranjang = {};
                                renderKeranjang();
                            },
                            onError: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Pembayaran Gagal',
                                    text: 'Silakan coba lagi.'
                                });
                            },
                            onClose: function() {
                                // Customer menutup popup tanpa bayar — tidak melakukan apa-apa
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: res.message
                        });
                    }
                },
                error: function(xhr) {
                    $('#btnBayar').prop('disabled', false)
                        .html('<i class="mdi mdi-credit-card me-1"></i> Bayar Sekarang');
                    const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan server.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: msg
                    });
                }
            });
        });

    });
</script>
@endpush
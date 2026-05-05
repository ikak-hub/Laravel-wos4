@extends('layouts.kantin')

@section('content')

<div class="container-fluid px-2">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-1">
                        <i class="mdi mdi-qrcode me-2 text-primary"></i>QR Code Pesanan Saya
                    </h4>
                    <p class="text-muted small mb-0">
                        Tunjukkan QR Code ini kepada vendor untuk mengambil pesanan Anda.
                    </p>
                </div>
                <a href="{{ route('kantin.index') }}" class="btn btn-gradient-primary btn-sm">
                    <i class="mdi mdi-plus me-1"></i> Pesan Lagi
                </a>
            </div>

            {{-- Placeholder: tidak ada pesanan --}}
            <div id="noPesanan" class="card" style="display:none;">
                <div class="card-body text-center py-5">
                    <i class="mdi mdi-receipt-text-remove mdi-48px text-muted d-block mb-3"></i>
                    <h5 class="text-muted">Belum ada pesanan tersimpan</h5>
                    <p class="text-muted small mb-4">
                        QR Code akan tersimpan otomatis setelah Anda berhasil melakukan pembayaran.
                    </p>
                    <a href="{{ route('kantin.index') }}" class="btn btn-gradient-primary">
                        <i class="mdi mdi-food me-1"></i> Pesan Sekarang
                    </a>
                </div>
            </div>

            {{-- Daftar pesanan (diisi oleh JS) --}}
            <div id="daftarPesanan"></div>

            {{-- Tombol hapus riwayat --}}
            <div id="clearSection" class="text-end mt-3" style="display:none;">
                <button type="button" id="btnClearAll" class="btn btn-outline-danger btn-sm">
                    <i class="mdi mdi-delete-sweep me-1"></i> Hapus Semua Riwayat
                </button>
            </div>

        </div>
    </div>
</div>

{{-- Template card pesanan (hidden, di-clone JS) --}}
<template id="tmplPesanan">
    <div class="card mb-4 order-card">
        <div class="card-header d-flex justify-content-between align-items-center py-2" style="background:linear-gradient(135deg,#4B49AC,#7978E9);">
            <span class="text-white fw-bold">
                <i class="mdi mdi-store me-1"></i>
                <span class="order-guest-name">—</span>
            </span>
            <span class="badge bg-light text-dark order-time">—</span>
        </div>
        <div class="card-body">
            <div class="row align-items-center">

                {{-- QR Code --}}
                <div class="col-md-4 text-center mb-3 mb-md-0">
                    <img class="order-qr-img" src=""
                         style="width:180px;height:180px;border:2px solid #dee2e6;border-radius:12px;padding:6px;background:#fff;">
                    <p class="small text-muted mt-2 mb-0">Tunjukkan ke vendor</p>
                </div>

                {{-- Detail --}}
                <div class="col-md-8">
                    <div class="mb-2">
                        <span class="text-muted small">ID Pesanan</span>
                        <p class="fw-bold font-monospace mb-1 order-id-text">—</p>
                    </div>

                    <div class="mb-3">
                        <span class="text-muted small">Status Pembayaran</span><br>
                        <span class="badge badge-gradient-success order-status">✓ Lunas</span>
                    </div>

                    <div class="mb-3">
                        <span class="text-muted small">Total</span>
                        <p class="fw-bold fs-5 text-success mb-0 order-total">—</p>
                    </div>

                    {{-- Items --}}
                    <div>
                        <span class="text-muted small">Item Pesanan</span>
                        <ul class="list-unstyled mb-0 mt-1 order-items-list"></ul>
                    </div>
                </div>

            </div>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center py-2 bg-light">
            <small class="text-muted"><i class="mdi mdi-information-outline me-1"></i>QR Code aktif selama pesanan belum selesai</small>
            <button type="button" class="btn btn-sm btn-outline-danger btn-hapus-order">
                <i class="mdi mdi-delete"></i>
            </button>
        </div>
    </div>
</template>
@endsection

@push('scripts')
<script>
function formatRupiah(n) {
    return 'Rp ' + parseInt(n).toLocaleString('id-ID');
}

function loadPesanan() {
    const savedOrders = JSON.parse(localStorage.getItem('kantin_orders') || '[]');
    const container = document.getElementById('daftarPesanan');
    const noPesanan = document.getElementById('noPesanan');
    const clearSection = document.getElementById('clearSection');

    container.innerHTML = '';

    if (savedOrders.length === 0) {
        noPesanan.style.display = 'block';
        clearSection.style.display = 'none';
        return;
    }

    noPesanan.style.display = 'none';
    clearSection.style.display = 'block';

    savedOrders.forEach((order, index) => {
        const tmpl = document.getElementById('tmplPesanan');
        const clone = tmpl.content.cloneNode(true);

        // Guest name
        clone.querySelector('.order-guest-name').textContent = order.guestName || '—';

        // Time
        const tgl = order.timestamp
            ? new Date(order.timestamp).toLocaleDateString('id-ID', {
                day: '2-digit', month: 'short', year: 'numeric',
                hour: '2-digit', minute: '2-digit'
              })
            : '—';
        clone.querySelector('.order-time').textContent = tgl;

        // QR Code
        if (order.qrCode) {
            clone.querySelector('.order-qr-img').src = order.qrCode;
        }

        // Order ID
        clone.querySelector('.order-id-text').textContent = order.orderId || order.idpesanan || '—';

        // Total
        clone.querySelector('.order-total').textContent = formatRupiah(order.total || 0);

        // Items
        const itemsList = clone.querySelector('.order-items-list');
        if (order.items && order.items.length > 0) {
            order.items.forEach(item => {
                const li = document.createElement('li');
                li.className = 'small text-muted d-flex justify-content-between';
                li.innerHTML = `
                    <span><i class="mdi mdi-circle-small"></i>${item.nama || ('Menu #' + item.idmenu || '?')}</span>
                    <span class="ms-2">×${item.jumlah} = ${formatRupiah(item.subtotal)}</span>
                `;
                itemsList.appendChild(li);
            });
        } else {
            const li = document.createElement('li');
            li.className = 'small text-muted';
            li.textContent = '(detail tidak tersedia)';
            itemsList.appendChild(li);
        }

        // Hapus tombol
        const btnHapus = clone.querySelector('.btn-hapus-order');
        btnHapus.addEventListener('click', () => {
            if (confirm('Hapus QR code pesanan ini?')) {
                const orders = JSON.parse(localStorage.getItem('kantin_orders') || '[]');
                orders.splice(index, 1);
                localStorage.setItem('kantin_orders', JSON.stringify(orders));
                loadPesanan();
            }
        });

        container.appendChild(clone);
    });
}

// Hapus semua
document.getElementById('btnClearAll').addEventListener('click', function () {
    if (confirm('Hapus semua riwayat pesanan?')) {
        localStorage.removeItem('kantin_orders');
        loadPesanan();
    }
});

// Load saat halaman dibuka
loadPesanan();
</script>
@endpush
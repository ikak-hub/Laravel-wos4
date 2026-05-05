@extends('layouts.kantor')

@section('content')
<div class="page-header">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-success text-white me-2">
            <i class="mdi mdi-qrcode-scan"></i>
        </span> Scan QR Pesanan Customer
    </h3>
</div>

<div class="row">

    {{-- ── Kiri: Scanner ──────────────────────────────────────── --}}
    <div class="col-lg-5 grid-margin stretch-card">
        <div class="card">
            <div class="card-header bg-gradient-primary text-white py-2">
                <i class="mdi mdi-qrcode-scan me-1"></i> Arahkan ke QR Code Customer
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">
                    Arahkan kamera ke QR Code yang ditunjukkan customer untuk melihat detail pesanan.
                </p>

                {{-- Pilih kamera (tampil jika ada lebih dari 1) --}}
                <div class="form-group mb-3" id="cameraSelectWrap" style="display:none;">
                    <label class="fw-semibold small">Pilih Kamera</label>
                    <select id="cameraSelect" class="form-select form-select-sm"></select>
                </div>

                {{-- Status --}}
                <div id="scannerStatus" class="alert alert-info d-flex align-items-center mb-3">
                    <span class="spinner-border spinner-border-sm me-2"></span>
                    <span>Memulai kamera...</span>
                </div>

                {{-- Reader area --}}
                <div id="vendorReader" style="width:100%;border-radius:8px;overflow:hidden;"></div>

                {{-- Tombol --}}
                <div class="d-flex gap-2 mt-3">
                    <button type="button" id="btnStartScan"
                            class="btn btn-gradient-primary flex-fill" style="display:none;">
                        <i class="mdi mdi-refresh me-1"></i> Scan Ulang
                    </button>
                    <button type="button" id="btnStopScan"
                            class="btn btn-gradient-danger flex-fill" style="display:none;">
                        <i class="mdi mdi-stop me-1"></i> Stop Scanner
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Kanan: Hasil ───────────────────────────────────────── --}}
    <div class="col-lg-7 grid-margin stretch-card">
        <div class="card">
            <div class="card-header bg-gradient-dark text-white py-2">
                <i class="mdi mdi-receipt me-1"></i> Detail Pesanan
            </div>
            <div class="card-body">

                {{-- Placeholder --}}
                <div id="resultPlaceholder" class="text-center text-muted py-5">
                    <i class="mdi mdi-qrcode mdi-48px d-block mb-3 text-secondary"></i>
                    <p class="fw-semibold mb-1">Belum ada QR yang dipindai</p>
                    <p class="small">Scan QR Code customer untuk menampilkan detail pesanan</p>
                </div>

                {{-- Loading --}}
                <div id="resultLoading" class="text-center py-4" style="display:none;">
                    <span class="spinner-border text-primary me-2"></span>
                    <span class="text-muted">Mengambil data pesanan...</span>
                </div>

                {{-- Hasil --}}
                <div id="resultSuccess" style="display:none;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <p class="text-muted small mb-1">Nama Customer</p>
                            <h5 class="fw-bold mb-0" id="resNama">—</h5>
                        </div>
                        <div id="resBadgeStatus"></div>
                    </div>
                    <hr>
                    <p class="fw-semibold mb-2 text-muted small">Item Pesanan:</p>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Menu</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="resItems"></tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center p-3 rounded"
                         style="background:#f0fff4;border:1px solid #b7ebc9;">
                        <span class="fw-bold fs-5">TOTAL</span>
                        <span class="fw-bold fs-4 text-success" id="resTotal">—</span>
                    </div>
                    <div class="mt-2 text-muted small text-end">
                        ID: <span id="resOrderId" class="font-monospace">—</span>
                    </div>
                </div>

                {{-- Error --}}
                <div id="resultError" style="display:none;" class="text-center py-4">
                    <i class="mdi mdi-alert-circle mdi-48px text-danger d-block mb-2"></i>
                    <p class="fw-semibold text-danger mb-1">Pesanan tidak ditemukan</p>
                    <p class="small text-muted" id="errorMsg"></p>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
/* ── Beep via Web Audio API ── */
function playBeep(freq = 1050, dur = 120, vol = 0.4) {
    try {
        const ctx  = new (window.AudioContext || window.webkitAudioContext)();
        const osc  = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.type = 'square';
        osc.frequency.value = freq;
        gain.gain.setValueAtTime(vol, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + dur / 1000);
        osc.start(ctx.currentTime);
        osc.stop(ctx.currentTime + dur / 1000);
    } catch (e) {}
}

function formatRupiah(n) {
    return 'Rp ' + parseInt(n).toLocaleString('id-ID');
}

function setStatus(type, html) {
    document.getElementById('scannerStatus').className = 'alert alert-' + type + ' d-flex align-items-center mb-3';
    document.getElementById('scannerStatus').innerHTML = html;
}

$(function () {

    let html5QrCode   = null;
    let isScanning    = false;
    let lastScanned   = '';
    let selectedCamId = null;

    /* ── Konfigurasi QR — area kotak (bukan persegi panjang seperti barcode) ── */
    const qrConfig = {
        fps: 20,
        qrbox: function (w, h) {
            const size = Math.min(w, h) * 0.7;
            return { width: Math.round(size), height: Math.round(size) };
        },
        formatsToSupport: [ Html5QrcodeSupportedFormats.QR_CODE ],
        rememberLastUsedCamera: false,
        showTorchButtonIfSupported: false,
    };

    /* ── Ambil daftar kamera — KUNCI FIX ── */
    Html5Qrcode.getCameras()
    .then(function (cameras) {

        if (!cameras || cameras.length === 0) {
            setStatus('danger', '<i class="mdi mdi-alert me-2"></i> Tidak ada kamera terdeteksi.');
            return;
        }

        if (cameras.length > 1) {
            cameras.forEach(function (cam) {
                $('#cameraSelect').append(
                    `<option value="${cam.id}">${cam.label || 'Kamera ' + cam.id}</option>`
                );
            });
            $('#cameraSelectWrap').show();

            $('#cameraSelect').on('change', function () {
                selectedCamId = $(this).val();
                if (isScanning) {
                    stopScanner().then(function () { startScanner(); });
                }
            });
        }

        // ★ Pakai deviceId langsung — BUKAN { facingMode: "environment" }
        selectedCamId = cameras[0].id;
        startScanner();
    })
    .catch(function (err) {
        setStatus('danger', '<i class="mdi mdi-alert me-2"></i> Gagal akses kamera: ' + err);
        console.error(err);
    });

    /* ── Start scanner ── */
    function startScanner() {
        if (isScanning) return Promise.resolve();

        html5QrCode = new Html5Qrcode("vendorReader");
        lastScanned = '';

        setStatus('info', '<span class="spinner-border spinner-border-sm me-2"></span> Membuka kamera...');

        // ★ selectedCamId (string) bukan { facingMode }
        return html5QrCode.start(
            selectedCamId,
            qrConfig,
            onScanSuccess,
            function () {}  // abaikan error per-frame
        )
        .then(function () {
            isScanning = true;
            setStatus('info', '<i class="mdi mdi-camera-outline me-2"></i> Scanner aktif — arahkan ke QR code customer...');
            $('#btnStopScan').show();
            $('#btnStartScan').hide();
        })
        .catch(function (err) {
            setStatus('danger', '<i class="mdi mdi-alert me-2"></i> Gagal buka kamera: ' + err);
            console.error('Start error:', err);
        });
    }

    /* ── Stop scanner ── */
    function stopScanner() {
        if (!html5QrCode || !isScanning) return Promise.resolve();
        return html5QrCode.stop()
        .then(function () {
            isScanning = false;
            $('#btnStopScan').hide();
            $('#btnStartScan').show();
        })
        .catch(function (err) { console.warn('Stop error:', err); });
    }

    /* ── Berhasil scan ── */
    function onScanSuccess(decodedText) {
        if (decodedText === lastScanned) return;
        lastScanned = decodedText;

        // 1. Beep
        playBeep();

        // 2. Stop scanner
        stopScanner();

        // 3. Update UI
        setStatus('success', '<i class="mdi mdi-qrcode-scan me-2"></i> QR terbaca! Mengambil data...');
        $('#resultPlaceholder, #resultSuccess, #resultError').hide();
        $('#resultLoading').show();

        // 4. AJAX
        $.ajax({
            url:  "{{ url('/kantor/scan-result') }}/" + encodeURIComponent(decodedText),
            type: 'GET',
            success: function (res) {
                $('#resultLoading').hide();

                if (res.status === 'success') {
                    const d = res.data;

                    $('#resNama').text(d.nama);
                    $('#resOrderId').text(d.midtrans_order_id || d.idpesanan);

                    // Badge status
                    if (d.status_bayar == 1) {
                        $('#resBadgeStatus').html('<span class="badge badge-gradient-success fs-6">✓ Lunas</span>');
                    } else {
                        $('#resBadgeStatus').html('<span class="badge badge-gradient-danger fs-6">✗ Belum Bayar</span>');
                    }

                    // Items
                    const $tbody = $('#resItems').empty();
                    if (d.details && d.details.length > 0) {
                        d.details.forEach(function (item) {
                            $tbody.append(`
                                <tr>
                                    <td>
                                        <span class="fw-semibold">${item.nama_menu}</span>
                                        ${item.catatan ? `<br><small class="text-muted"><i class="mdi mdi-note-text-outline me-1"></i>${item.catatan}</small>` : ''}
                                    </td>
                                    <td class="text-center">×${item.jumlah}</td>
                                    <td class="text-end">${formatRupiah(item.subtotal)}</td>
                                </tr>
                            `);
                        });
                    } else {
                        $tbody.append('<tr><td colspan="3" class="text-muted small text-center">Tidak ada detail item.</td></tr>');
                    }

                    $('#resTotal').text(formatRupiah(d.total));
                    $('#resultSuccess').show();

                    setStatus('success',
                        '<i class="mdi mdi-check-circle me-2"></i> Data tampil. Klik "Scan Ulang" untuk customer berikutnya.');
                } else {
                    showError(res.message);
                }
            },
            error: function (xhr) {
                $('#resultLoading').hide();
                const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan server.';
                showError(msg);
            }
        });
    }

    function showError(msg) {
        $('#resultSuccess, #resultPlaceholder, #resultLoading').hide();
        $('#resultError').show();
        $('#errorMsg').text(msg);
        setStatus('warning', '<i class="mdi mdi-alert me-2"></i> ' + msg + ' — Klik "Scan Ulang" untuk coba lagi.');
    }

    /* ── Tombol ── */
    $('#btnStartScan').on('click', function () {
        $('#resultPlaceholder').show();
        $('#resultSuccess, #resultError').hide();
        startScanner();
    });

    $('#btnStopScan').on('click', function () {
        stopScanner();
        setStatus('warning', '<i class="mdi mdi-pause me-2"></i> Scanner dihentikan. Klik "Scan Ulang" untuk lanjut.');
    });

});
</script>
@endpush
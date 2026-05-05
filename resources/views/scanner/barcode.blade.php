@extends('layouts.app')

@section('content')
@include('layouts.header', ['title' => 'Barcode Scanner – Tag Harga', 'icon' => 'mdi-barcode-scan'])

<div class="row justify-content-center">

    {{-- ── Kolom Scanner ── --}}
    <div class="col-lg-5 grid-margin stretch-card">
        <div class="card">
            <div class="card-header bg-gradient-primary text-white py-2">
                <i class="mdi mdi-barcode-scan me-1"></i> Arahkan Kamera ke Barcode
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">
                    Arahkan kamera ke barcode <strong>Code 128</strong> yang ada di label harga.
                    Sistem akan otomatis membaca dan berhenti setelah berhasil.
                </p>

                {{-- Pilih kamera (tampil jika ada > 1) --}}
                <div class="form-group mb-3" id="cameraSelectWrap" style="display:none;">
                    <label class="fw-semibold small">Pilih Kamera</label>
                    <select id="cameraSelect" class="form-select form-select-sm"></select>
                </div>

                {{-- Status --}}
                <div id="scannerStatus" class="alert alert-info d-flex align-items-center mb-3">
                    <span class="spinner-border spinner-border-sm me-2"></span>
                    <span>Memulai kamera...</span>
                </div>

                {{-- Area reader --}}
                <div id="reader" style="width:100%; border-radius:8px; overflow:hidden;"></div>

                {{-- Tombol kontrol --}}
                <div class="d-flex gap-2 mt-3">
                    <button type="button" id="btnStartScan"
                            class="btn btn-gradient-primary flex-fill" style="display:none;">
                        <i class="mdi mdi-camera me-1"></i> Scan Ulang
                    </button>
                    <button type="button" id="btnStopScan"
                            class="btn btn-gradient-danger flex-fill" style="display:none;">
                        <i class="mdi mdi-stop me-1"></i> Stop Scanner
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Kolom Hasil ── --}}
    <div class="col-lg-7 grid-margin stretch-card">
        <div class="card">
            <div class="card-header bg-gradient-success text-white py-2">
                <i class="mdi mdi-package-variant me-1"></i> Informasi Barang
            </div>
            <div class="card-body">

                <div id="resultPlaceholder" class="text-center text-muted py-5">
                    <i class="mdi mdi-barcode mdi-48px d-block mb-3 text-secondary"></i>
                    <p class="mb-1 fw-semibold">Belum ada barcode terbaca</p>
                    <p class="small">Scan barcode di kiri untuk melihat informasi barang</p>
                </div>

                <div id="resultCard" style="display:none;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <small class="text-muted">Barcode terbaca:</small>
                        <span id="rawBarcode" class="badge badge-gradient-info font-monospace fs-6"></span>
                    </div>

                    <div class="p-3 rounded border" style="background:#f0fff4;">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <p class="text-muted small mb-1">ID Barang</p>
                                <h5 class="fw-bold mb-0 font-monospace" id="resIdBarang">—</h5>
                            </div>
                            <i class="mdi mdi-check-circle mdi-36px text-success"></i>
                        </div>
                        <div class="row">
                            <div class="col-8">
                                <p class="text-muted small mb-1">Nama Barang</p>
                                <h4 class="fw-bold mb-0" id="resNama">—</h4>
                            </div>
                            <div class="col-4 text-end">
                                <p class="text-muted small mb-1">Harga</p>
                                <h4 class="fw-bold text-success mb-0" id="resHarga">—</h4>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 text-center">
                        <small class="text-muted">
                            <i class="mdi mdi-clock-outline me-1"></i>
                            Dibaca pada: <span id="resScanTime">—</span>
                        </small>
                    </div>
                </div>

                <div id="resultError" style="display:none;" class="text-center py-4">
                    <i class="mdi mdi-alert-circle mdi-48px text-danger d-block mb-2"></i>
                    <p class="fw-semibold text-danger mb-1">Barang tidak ditemukan</p>
                    <p class="small text-muted" id="errorMsg"></p>
                </div>

            </div>
        </div>

        {{-- Riwayat --}}
        <div class="card mt-0">
            <div class="card-header bg-dark text-white py-2">
                <i class="mdi mdi-history me-1"></i> Riwayat Scan (sesi ini)
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>ID Barang</th>
                                <th>Nama</th>
                                <th class="text-end">Harga</th>
                                <th style="width:80px;" class="text-muted small">Waktu</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyRiwayat">
                            <tr id="noRiwayat">
                                <td colspan="4" class="text-center text-muted py-3 small">
                                    Belum ada riwayat scan.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
// Beep via Web Audio API (karena HTML5 Audio sering delay dan gak bisa diputar otomatis tapi alasan kedua karena laptop saya penuh pak😭🙏)
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
    $('#scannerStatus')
        .removeClass('alert-info alert-success alert-danger alert-warning')
        .addClass('alert-' + type)
        .html(html);
}

$(function () {

    let html5QrCode    = null;
    let isScanning     = false;
    let lastScanned    = '';
    let selectedCamId  = null;

    // Konfigurasi scanner
    const scanConfig = {
        fps: 20,
        // Area deteksi: lebar penuh, tinggi 35% — pas untuk barcode horizontal
        qrbox: function (w, h) {
            return {
                width:  Math.min(Math.round(w * 0.88), 480),
                height: Math.round(Math.min(w, h) * 0.35),
            };
        },
        formatsToSupport: [
            Html5QrcodeSupportedFormats.CODE_128,
            Html5QrcodeSupportedFormats.CODE_39,
            Html5QrcodeSupportedFormats.QR_CODE,
            Html5QrcodeSupportedFormats.EAN_13,
            Html5QrcodeSupportedFormats.EAN_8,
        ],
        // Jangan set aspectRatio atau facingMode — biarin browser pilih sendiri
        rememberLastUsedCamera: false,
        showTorchButtonIfSupported: false,
    };

    // Ambil daftar kamera
    Html5Qrcode.getCameras()
    .then(function (cameras) {

        if (!cameras || cameras.length === 0) {
            setStatus('danger', '<i class="mdi mdi-alert me-2"></i> Tidak ada kamera yang terdeteksi.');
            return;
        }

        if (cameras.length > 1) {
            // Isi dropdown pilih kamera
            cameras.forEach(function (cam) {
                $('#cameraSelect').append(
                    `<option value="${cam.id}">${cam.label || 'Kamera ' + cam.id}</option>`
                );
            });
            $('#cameraSelectWrap').show();

            // Ganti kamera saat dipilih
            $('#cameraSelect').on('change', function () {
                selectedCamId = $(this).val();
                if (isScanning) {
                    stopScanner().then(function () { startScanner(); });
                }
            });
        }

        // FIX: pakai deviceId kamera pertama, BUKAN facingMode
        selectedCamId = cameras[0].id;
        startScanner();
    })
    .catch(function (err) {
        setStatus('danger', '<i class="mdi mdi-alert me-2"></i> Gagal akses kamera: ' + err);
        console.error(err);
    });

    // Start scanner 
    function startScanner() {
        if (isScanning) return Promise.resolve();

        html5QrCode = new Html5Qrcode("reader");
        lastScanned = '';

        setStatus('info',
            '<span class="spinner-border spinner-border-sm me-2"></span> Membuka kamera...');

        // Pakai selectedCamId
        return html5QrCode.start(
            selectedCamId,
            scanConfig,
            onScanSuccess,
            function () {}   // abaikan error per-frame
        )
        .then(function () {
            isScanning = true;
            setStatus('info',
                '<i class="mdi mdi-camera-outline me-2"></i> Scanner aktif — arahkan ke barcode...');
            $('#btnStopScan').show();
            $('#btnStartScan').hide();
        })
        .catch(function (err) {
            setStatus('danger', '<i class="mdi mdi-alert me-2"></i> Gagal buka kamera: ' + err);
            console.error('Start error:', err);
        });
    }

    // Stop scanner
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

    // Callback: berhasil scan
    function onScanSuccess(decodedText) {
        if (decodedText === lastScanned) return;
        lastScanned = decodedText;

        // 1. Beep Beep
        playBeep();

        // 2. Stop scanner
        stopScanner();

        // 3. Update status & UI
        setStatus('success',
            '<i class="mdi mdi-barcode-scan me-2"></i> Barcode terbaca! Mencari data...');
        $('#rawBarcode').text(decodedText);
        $('#resultPlaceholder, #resultCard, #resultError').hide();

        // 4. AJAX cari barang
        $.ajax({
            url:  "{{ url('/ajax/pos/cari') }}/" + encodeURIComponent(decodedText),
            type: 'GET',
            success: function (res) {
                if (res.status === 'success') {
                    const b   = res.data;
                    const now = new Date().toLocaleTimeString('id-ID');

                    $('#resIdBarang').text(b.id_barang);
                    $('#resNama').text(b.nama);
                    $('#resHarga').text(formatRupiah(b.harga));
                    $('#resScanTime').text(now);
                    $('#resultCard').show();

                    addRiwayat(b, now);
                    setStatus('success',
                        '<i class="mdi mdi-check-circle me-2"></i> Ditemukan! Klik "Scan Ulang" untuk lanjut.');
                } else {
                    showError(res.message);
                }
            },
            error: function (xhr) {
                const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Barang tidak ditemukan.';
                showError(msg);
            }
        });
    }

    function showError(msg) {
        $('#resultCard, #resultPlaceholder').hide();
        $('#resultError').show();
        $('#errorMsg').text(msg);
        setStatus('warning', '<i class="mdi mdi-alert me-2"></i> ' + msg + ' — Klik "Scan Ulang" untuk coba lagi.');
    }

    function addRiwayat(b, waktu) {
        $('#noRiwayat').remove();
        $('#tbodyRiwayat').prepend(`
            <tr>
                <td><span class="badge badge-gradient-info font-monospace">${b.id_barang}</span></td>
                <td>${b.nama}</td>
                <td class="text-end">${formatRupiah(b.harga)}</td>
                <td class="text-muted small">${waktu}</td>
            </tr>
        `);
    }

    // Tombol
    $('#btnStartScan').on('click', function () {
        $('#resultPlaceholder').show();
        $('#resultCard, #resultError').hide();
        startScanner();
    });

    $('#btnStopScan').on('click', function () {
        stopScanner();
        setStatus('warning',
            '<i class="mdi mdi-pause me-2"></i> Scanner dihentikan. Klik "Scan Ulang" untuk lanjut.');
    });

});
</script>
@endpush
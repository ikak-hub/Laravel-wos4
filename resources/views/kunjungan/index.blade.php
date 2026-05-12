@extends('layouts.app')

@section('title', 'Kunjungan Toko')

@push('styles')
<style>
    /* Layout */
    .section-card {
        border: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .10);
        border-radius: .75rem;
    }

    .section-card .card-header {
        border-radius: .75rem .75rem 0 0 !important;
        font-weight: 600;
    }

    /* Scanner area */
    #reader {
        width: 100%;
        border-radius: .5rem;
        overflow: hidden;
    }

    #reader video {
        border-radius: .5rem;
    }

    /* Accuracy progress bar */
    .acc-bar-wrap {
        height: 8px;
        background: #e9ecef;
        border-radius: 4px;
        overflow: hidden;
    }

    .acc-bar {
        height: 100%;
        transition: width .4s, background .4s;
    }

    .badge-xl {
        font-size: 1.1rem;
        padding: .5rem 1.2rem;
        border-radius: .6rem;
    }

    #qrModalImg {
        max-width: 220px;
        display: block;
        margin: 0 auto;
    }

    .tbl-toko td,
    .tbl-toko th {
        vertical-align: middle;
        font-size: .85rem;
    }

    .coord-box {
        background: #f8f9fa;
        border-radius: .5rem;
        padding: .6rem 1rem;
        font-size: .85rem;
    }

    .coord-box span.label {
        color: #6c757d;
        font-size: .75rem;
        display: block;
    }

    .coord-box span.val {
        font-weight: 600;
        font-size: .95rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-3">

    <div class="d-flex align-items-center mb-4 gap-2">
        <i class="mdi mdi-map-marker-radius fs-3 text-primary" style="font-size:2rem"></i>
        <div>
            <h4 class="mb-0 fw-bold">Kunjungan Toko</h4>
            <small class="text-muted">Validasi posisi sales terhadap titik toko</small>
        </div>
    </div>

    <div class="row g-3">

        <div class="col-lg-6">

            {{-- ── Form Input Titik Awal Toko ─────────────────── --}}
            <div class="card section-card mb-3">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-plus-circle me-1"></i> Input Titik Awal Toko
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Toko</label>
                        <input type="text" id="inp_nama_toko" class="form-control"
                            placeholder="Contoh: Toko Maju Jaya">
                    </div>

                    {{-- Koordinat (diisi via tombol Ambil Lokasi) --}}
                    <div class="row g-2 mb-3">
                        <div class="col-4">
                            <label class="form-label fw-semibold">Latitude</label>
                            <input type="text" id="inp_lat" class="form-control" readonly placeholder="–">
                        </div>
                        <div class="col-4">
                            <label class="form-label fw-semibold">Longitude</label>
                            <input type="text" id="inp_lng" class="form-control" readonly placeholder="–">
                        </div>
                        <div class="col-4">
                            <label class="form-label fw-semibold">Accuracy (m)</label>
                            <input type="text" id="inp_acc" class="form-control" readonly placeholder="–">
                        </div>
                    </div>

                    {{-- Ambil Lokasi Toko --}}
                    <button class="btn btn-outline-secondary btn-sm me-2" id="btn_ambil_lokasi_toko"
                        onclick="ambilLokasiToko()">
                        <span class="spinner-border spinner-border-sm d-none me-1" id="spin_toko"></span>
                        <i class="bi bi-crosshair" id="ico_toko"></i> Ambil Lokasi
                    </button>
                    <span id="status_lokasi_toko" class="text-muted small"></span>

                    <hr>
                    <button class="btn btn-primary" id="btn_simpan_toko" onclick="simpanToko()">
                        <i class="bi bi-floppy me-1"></i> Simpan Toko
                    </button>
                </div>
            </div>

            {{-- ── List Toko ──────────────────────────────────── --}}
            <div class="card section-card">
                <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                    <span><i class="mdi mdi-table me-1"></i> List Toko</span>
                    <span class="badge bg-light text-dark" id="badge_total">{{ $tokos->count() }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 tbl-toko" id="tbl_toko">
                            <thead class="table-light">
                                <tr>
                                    <th>Barcode</th>
                                    <th>Nama Toko</th>
                                    <th>Lat</th>
                                    <th>Lng</th>
                                    <th>Acc(m)</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_toko">
                                @forelse($tokos as $t)
                                <tr id="row_{{ $t->barcode }}">
                                    <td><code>{{ $t->barcode }}</code></td>
                                    <td>{{ $t->nama_toko }}</td>
                                    <td>{{ number_format($t->latitude, 6) }}</td>
                                    <td>{{ number_format($t->longitude, 6) }}</td>
                                    <td>{{ $t->accuracy }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-info" title="Cetak Barcode"
                                            onclick="cetakBarcode('{{ $t->barcode }}', '{{ addslashes($t->nama_toko) }}')">
                                            <i class="mdi mdi-qrcode"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" title="Hapus"
                                            onclick="hapusToko('{{ $t->barcode }}')">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr id="row_empty">
                                    <td colspan="6" class="text-center text-muted py-3">
                                        Belum ada data toko.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>{{-- /col-left --}}


        {{-- ════════════════════════════════════════════════════════
             KOLOM KANAN  –  Titik Kunjungan
        ════════════════════════════════════════════════════════ --}}
        <div class="col-lg-6">

            {{-- ── Barcode Scanner ─────────────────────────────── --}}
            <div class="card section-card mb-3">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <span><i class="mdi mdi-qrcode-scan me-1"></i> Scan Barcode Toko</span>
                    <button class="btn btn-sm btn-outline-light" id="btn_toggle_scanner" onclick="toggleScanner()">
                        <i class="mdi mdi-camera me-1"></i> Mulai Scan
                    </button>
                </div>
                <div class="card-body">
                    <div id="reader" class="mb-2"></div>
                    <div id="scan_result_box" class="d-none">
                        <div class="alert alert-info mb-2 py-2">
                            <i class="mdi mdi-check-circle me-1"></i>
                            QR terbaca: <strong id="scan_raw_text"></strong>
                        </div>
                        <div id="scan_toko_info" class="coord-box">
                            <div class="row g-2">
                                <div class="col-6">
                                    <span class="label">Nama Toko</span>
                                    <span class="val" id="s_nama_toko">–</span>
                                </div>
                                <div class="col-6">
                                    <span class="label">Barcode</span>
                                    <span class="val" id="s_barcode">–</span>
                                </div>
                                <div class="col-4">
                                    <span class="label">Latitude</span>
                                    <span class="val" id="s_lat">–</span>
                                </div>
                                <div class="col-4">
                                    <span class="label">Longitude</span>
                                    <span class="val" id="s_lng">–</span>
                                </div>
                                <div class="col-4">
                                    <span class="label">Accuracy (m)</span>
                                    <span class="val" id="s_acc">–</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="scan_error_box" class="alert alert-danger d-none py-2">
                        <i class="mdi mdi-alert-circle me-1"></i>
                        <span id="scan_error_msg"></span>
                    </div>
                </div>
            </div>

            {{-- ── Posisi Sales ─────────────────────────────────── --}}
            <div class="card section-card mb-3">
                <div class="card-header bg-success text-white">
                    <i class="mdi mdi-walk me-1"></i> Posisi Sales (Titik Kunjungan)
                </div>
                <div class="card-body">
                    <button class="btn btn-success btn-sm mb-3" id="btn_ambil_lokasi_sales"
                        onclick="ambilLokasiSales()">
                        <span class="spinner-border spinner-border-sm d-none me-1" id="spin_sales"></span>
                        <i class="mdi mdi-crosshairs-gps" id="ico_sales"></i> Ambil Lokasi Saya
                    </button>

                    <div id="sales_lokasi_box" class="coord-box d-none">
                        <div class="row g-2">
                            <div class="col-4">
                                <span class="label">Latitude</span>
                                <span class="val" id="sales_lat">–</span>
                            </div>
                            <div class="col-4">
                                <span class="label">Longitude</span>
                                <span class="val" id="sales_lng">–</span>
                            </div>
                            <div class="col-4">
                                <span class="label">Accuracy (m)</span>
                                <span class="val" id="sales_acc">–</span>
                            </div>
                        </div>

                        {{-- Accuracy bar (makin kecil akurasi = makin bagus) --}}
                        <div class="mt-2">
                            <div class="d-flex justify-content-between mb-1">
                                <small class="text-muted">Kualitas sinyal GPS</small>
                                <small id="acc_label_text"></small>
                            </div>
                            <div class="acc-bar-wrap">
                                <div class="acc-bar" id="acc_bar"></div>
                            </div>
                        </div>
                    </div>
                    <div id="sales_lokasi_error" class="text-danger small d-none">
                        <i class="mdi mdi-alert me-1"></i>
                        <span id="sales_error_msg"></span>
                    </div>
                </div>
            </div>

            {{-- ── Validasi Kunjungan ───────────────────────────── --}}
            <div class="card section-card">
                <div class="card-header bg-warning text-dark">
                    <i class="mdi mdi-radar me-1"></i> Hasil Validasi Kunjungan
                </div>
                <div class="card-body">
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Threshold Jarak (m)</label>
                            <input type="number" id="inp_threshold" class="form-control form-control-sm"
                                value="300" min="1" max="10000">
                        </div>
                    </div>

                    <button class="btn btn-warning fw-semibold" onclick="validasiKunjungan()">
                        <i class="mdi mdi-check-circle-outline me-1"></i> Validasi
                    </button>

                    <hr>

                    <div id="validasi_result" class="d-none">
                        {{-- Detail perhitungan --}}
                        <div class="coord-box mb-3">
                            <div class="row g-2">
                                <div class="col-6">
                                    <span class="label">Jarak Aktual (pusat ke pusat)</span>
                                    <span class="val" id="r_jarak">–</span>
                                </div>
                                <div class="col-6">
                                    <span class="label">Threshold Efektif</span>
                                    <span class="val" id="r_threshold_eff">–</span>
                                </div>
                                <div class="col-4">
                                    <span class="label">Acc Toko</span>
                                    <span class="val" id="r_acc_toko">–</span>
                                </div>
                                <div class="col-4">
                                    <span class="label">Acc Sales</span>
                                    <span class="val" id="r_acc_sales">–</span>
                                </div>
                                <div class="col-4">
                                    <span class="label">Threshold Dasar</span>
                                    <span class="val" id="r_threshold_base">–</span>
                                </div>
                            </div>
                        </div>

                        {{-- Rumus visual --}}
                        <div class="text-center small text-muted mb-3" id="r_formula"></div>

                        {{-- Status Diterima / Ditolak --}}
                        <div class="text-center">
                            <span id="badge_status" class="badge badge-xl"></span>
                        </div>
                    </div>

                    <div id="validasi_error" class="alert alert-warning d-none py-2 mt-2">
                        <i class="mdi mdi-alert me-1"></i>
                        <span id="validasi_error_msg"></span>
                    </div>
                </div>
            </div>

        </div>{{-- /col-right --}}

    </div>{{-- /row --}}
</div>{{-- /container --}}


{{-- ── Modal QR Code ──────────────────────────────────────────── --}}
<div class="modal fade" id="qrModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="bi bi-qr-code me-1"></i> Barcode Toko</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <p class="text-muted small mb-1">Barcode: <strong id="qr_barcode_text"></strong></p>
                <p class="fw-semibold mb-2" id="qr_nama_toko_text"></p>
                <img id="qrModalImg" src="" alt="QR Code" class="img-fluid mb-2">
            </div>
            <div class="modal-footer justify-content-center">
                <button class="btn btn-outline-primary btn-sm" onclick="printQr()">
                    <i class="mdi mdi-printer me-1"></i> Cetak
                </button>
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection


@push('scripts')
{{-- html5-qrcode untuk scan kamera --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>

<script>
    "use strict";

    // CSRF token 
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    // State
    let html5QrcodeScanner = null;
    let scannerRunning = false;
    let tokoData = null; // hasil scan
    let salesCoords = null; // posisi sales

    //  1. AMBIL LOKASI  (gunakan getAccuratePosition dari lampiran 1)
    
    // Terus watchPosition sampai accuracy ≤ targetAccuracy atau timeout.
    // Mirip share-location WhatsApp – selalu simpan posisi terbaik.
    
    function getAccuratePosition(targetAccuracy = 50, maxWait = 20000) {
        return new Promise((resolve, reject) => {
            if (!navigator.geolocation) {
                return reject(new Error('Browser tidak mendukung Geolocation.'));
            }

            let bestResult = null;
            const startTime = Date.now();

            const watchId = navigator.geolocation.watchPosition(
                (position) => {
                    const acc = position.coords.accuracy;

                    // Simpan hasil terbaik (accuracy terkecil = terbaik)
                    if (!bestResult || acc < bestResult.coords.accuracy) {
                        bestResult = position;
                    }

                    // Sudah cukup akurat → berhenti
                    if (acc <= targetAccuracy) {
                        navigator.geolocation.clearWatch(watchId);
                        resolve(bestResult);
                        return;
                    }

                    // Timeout → pakai yang terbaik sejauh ini
                    if (Date.now() - startTime >= maxWait) {
                        navigator.geolocation.clearWatch(watchId);
                        if (bestResult) resolve(bestResult);
                        else reject(new Error('Timeout, tidak dapat posisi GPS.'));
                    }
                },
                (error) => reject(error), {
                    enableHighAccuracy: true,
                    maximumAge: 0,
                    timeout: maxWait
                }
            );
        });
    }

    // Ambil lokasi untuk form Input Toko
    async function ambilLokasiToko() {
        const spin = document.getElementById('spin_toko');
        const ico = document.getElementById('ico_toko');
        const status = document.getElementById('status_lokasi_toko');

        spin.classList.remove('d-none');
        ico.classList.add('d-none');
        status.textContent = 'Mengambil lokasi terbaik…';
        status.className = 'text-muted small';

        try {
            const pos = await getAccuratePosition(50, 20000);
            document.getElementById('inp_lat').value = pos.coords.latitude.toFixed(8);
            document.getElementById('inp_lng').value = pos.coords.longitude.toFixed(8);
            document.getElementById('inp_acc').value = pos.coords.accuracy.toFixed(2);
            status.textContent = `✓ Lokasi didapat (acc: ${pos.coords.accuracy.toFixed(1)}m)`;
            status.className = 'text-success small';
        } catch (e) {
            status.textContent = `✗ Gagal: ${e.message}`;
            status.className = 'text-danger small';
        } finally {
            spin.classList.add('d-none');
            ico.classList.remove('d-none');
        }
    }

    // Ambil lokasi untuk sales (kunjungan) 
    async function ambilLokasiSales() {
        const spin = document.getElementById('spin_sales');
        const ico = document.getElementById('ico_sales');
        const box = document.getElementById('sales_lokasi_box');
        const errEl = document.getElementById('sales_lokasi_error');
        const errMsg = document.getElementById('sales_error_msg');

        spin.classList.remove('d-none');
        ico.classList.add('d-none');
        box.classList.add('d-none');
        errEl.classList.add('d-none');

        try {
            const pos = await getAccuratePosition(50, 20000);
            salesCoords = {
                lat: pos.coords.latitude,
                lng: pos.coords.longitude,
                acc: pos.coords.accuracy,
            };

            document.getElementById('sales_lat').textContent = salesCoords.lat.toFixed(8);
            document.getElementById('sales_lng').textContent = salesCoords.lng.toFixed(8);
            document.getElementById('sales_acc').textContent = salesCoords.acc.toFixed(2) + ' m';
            box.classList.remove('d-none');
            updateAccBar(salesCoords.acc);
        } catch (e) {
            errMsg.textContent = e.message;
            errEl.classList.remove('d-none');
        } finally {
            spin.classList.add('d-none');
            ico.classList.remove('d-none');
        }
    }

    // Akurasi bar (hijau=bagus, kuning=sedang, merah=buruk)
    function updateAccBar(acc) {
        const bar = document.getElementById('acc_bar');
        const label = document.getElementById('acc_label_text');

        // 0–20m = excellent, 20–50 = baik, 50–100 = sedang, >100 = buruk
        let pct, color, text;
        if (acc <= 20) {
            pct = 100;
            color = '#198754';
            text = 'Sangat Baik';
        } else if (acc <= 50) {
            pct = 75;
            color = '#20c997';
            text = 'Baik';
        } else if (acc <= 100) {
            pct = 50;
            color = '#ffc107';
            text = 'Sedang';
        } else {
            pct = 25;
            color = '#dc3545';
            text = 'Buruk';
        }

        bar.style.width = pct + '%';
        bar.style.background = color;
        label.textContent = `${text} (${acc.toFixed(1)}m)`;
        label.style.color = color;
    }

    //  2. MENYIMPAN TOKO
    async function simpanToko() {
        const nama = document.getElementById('inp_nama_toko').value.trim();
        const lat = document.getElementById('inp_lat').value;
        const lng = document.getElementById('inp_lng').value;
        const acc = document.getElementById('inp_acc').value;

        if (!nama) return alert('Nama toko wajib diisi.');
        if (!lat || !lng || !acc) return alert('Ambil lokasi terlebih dahulu.');

        const btn = document.getElementById('btn_simpan_toko');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan…';

        try {
            const resp = await fetch('/kunjungan/toko', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                },
                body: JSON.stringify({
                    nama_toko: nama,
                    latitude: lat,
                    longitude: lng,
                    accuracy: acc
                }),
            });

            const json = await resp.json();
            if (json.status !== 'success') throw new Error(json.message);

            // Tambah baris ke tabel tanpa reload
            tambahBarisTabel({
                barcode: json.data.barcode,
                nama_toko: nama,
                latitude: lat,
                longitude: lng,
                accuracy: acc
            });

            // Reset form
            ['inp_nama_toko', 'inp_lat', 'inp_lng', 'inp_acc'].forEach(id => {
                document.getElementById(id).value = '';
            });
            document.getElementById('status_lokasi_toko').textContent = '';
            alert(`Toko berhasil disimpan! Barcode: ${json.data.barcode}`);
        } catch (e) {
            alert('Gagal menyimpan: ' + e.message);
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-floppy me-1"></i> Simpan Toko';
        }
    }

    function tambahBarisTabel(t) {
        // Hapus row kosong jika ada
        document.getElementById('row_empty')?.remove();

        const tbody = document.getElementById('tbody_toko');
        const badgeEl = document.getElementById('badge_total');
        const row = document.createElement('tr');
        row.id = `row_${t.barcode}`;
        row.innerHTML = `
        <td><code>${t.barcode}</code></td>
        <td>${t.nama_toko}</td>
        <td>${parseFloat(t.latitude).toFixed(6)}</td>
        <td>${parseFloat(t.longitude).toFixed(6)}</td>
        <td>${parseFloat(t.accuracy).toFixed(2)}</td>
        <td class="text-center">
            <button class="btn btn-sm btn-outline-info" onclick="cetakBarcode('${t.barcode}','${t.nama_toko.replace(/'/g,"\\'")}')">
                <i class="mdi mdi-qr-code"></i>
            </button>
            <button class="btn btn-sm btn-outline-danger" onclick="hapusToko('${t.barcode}')">
                <i class="mdi mdi-trash"></i>
            </button>
        </td>`;
        tbody.prepend(row);
        badgeEl.textContent = parseInt(badgeEl.textContent || '0') + 1;
    }

    //  3. MENGHAPUS TOKO
    async function hapusToko(barcode) {
        if (!confirm(`Hapus toko ${barcode}?`)) return;

        const resp = await fetch(`/kunjungan/toko/${barcode}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': CSRF
            },
        });
        const json = await resp.json();
        if (json.status !== 'success') return alert(json.message);

        document.getElementById(`row_${barcode}`)?.remove();
        const badgeEl = document.getElementById('badge_total');
        badgeEl.textContent = Math.max(0, parseInt(badgeEl.textContent) - 1);

        if (!document.querySelector('#tbody_toko tr')) {
            document.getElementById('tbody_toko').innerHTML =
                `<tr id="row_empty"><td colspan="6" class="text-center text-muted py-3">Belum ada data toko.</td></tr>`;
        }
    }

    //  4. CETAK BARCODE (tampil modal QR)
    function cetakBarcode(barcode, namaToko) {
        document.getElementById('qr_barcode_text').textContent = barcode;
        document.getElementById('qr_nama_toko_text').textContent = namaToko;
        document.getElementById('qrModalImg').src = `/kunjungan/qrcode/${barcode}`;

        const modal = new bootstrap.Modal(document.getElementById('qrModal'));
        modal.show();
    }

    function printQr() {
        const img = document.getElementById('qrModalImg').src;
        const nama = document.getElementById('qr_nama_toko_text').textContent;
        const kode = document.getElementById('qr_barcode_text').textContent;

        const win = window.open('', '_blank', 'width=400,height=500');
        win.document.write(`
        <html><head><title>Print QR – ${kode}</title>
        <style>body{font-family:sans-serif;text-align:center;padding:20px}
        img{width:200px}h3{margin:10px 0 4px}p{margin:0;font-size:.9rem;color:#555}</style>
        </head><body onload="window.print();window.close()">
        <h3>${nama}</h3><p>${kode}</p>
        <img src="${img}">
        </body></html>`);
        win.document.close();
    }

    //  5. BARCODE SCANNER
    function toggleScanner() {
        if (scannerRunning) {
            stopScanner();
        } else {
            startScanner();
        }
    }

    function startScanner() {
        const btn = document.getElementById('btn_toggle_scanner');
        btn.innerHTML = '<i class="bi bi-stop-circle me-1"></i> Stop Scan';
        btn.classList.replace('btn-outline-light', 'btn-danger');

        html5QrcodeScanner = new Html5QrcodeScanner(
            'reader', {
                fps: 10,
                qrbox: {
                    width: 250,
                    height: 250
                }
            },
            false
        );

        html5QrcodeScanner.render(onScanSuccess, onScanError);
        scannerRunning = true;
    }

    function stopScanner() {
        if (html5QrcodeScanner) {
            html5QrcodeScanner.clear().catch(() => {});
            html5QrcodeScanner = null;
        }
        scannerRunning = false;
        const btn = document.getElementById('btn_toggle_scanner');
        btn.innerHTML = '<i class="bi bi-camera-video me-1"></i> Mulai Scan';
        btn.classList.replace('btn-danger', 'btn-outline-light');
    }

    async function onScanSuccess(decodedText) {
        stopScanner();

        document.getElementById('scan_raw_text').textContent = decodedText;
        document.getElementById('scan_result_box').classList.remove('d-none');
        document.getElementById('scan_error_box').classList.add('d-none');

        // Fetch data toko dari server
        try {
            const resp = await fetch(`/kunjungan/toko/${encodeURIComponent(decodedText)}`);
            const json = await resp.json();

            if (json.status !== 'success') throw new Error(json.message);

            const t = json.data;
            tokoData = t;

            document.getElementById('s_nama_toko').textContent = t.nama_toko;
            document.getElementById('s_barcode').textContent = t.barcode;
            document.getElementById('s_lat').textContent = parseFloat(t.latitude).toFixed(8);
            document.getElementById('s_lng').textContent = parseFloat(t.longitude).toFixed(8);
            document.getElementById('s_acc').textContent = parseFloat(t.accuracy).toFixed(2);
        } catch (e) {
            tokoData = null;
            document.getElementById('scan_error_msg').textContent = e.message;
            document.getElementById('scan_error_box').classList.remove('d-none');
        }
    }

    function onScanError() {
        /* abaikan error per-frame */ }

    //  6. VALIDASI KUNJUNGAN  (Haversine + threshold efektif)

    /**
     * Haversine distance (meters) – dari Lampiran 2
     */
    function haversine(lat1, lng1, lat2, lng2) {
        const R = 6371000; // radius bumi dalam meter
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLng = (lng2 - lng1) * Math.PI / 180;
        const a = Math.sin(dLat / 2) ** 2 +
            Math.cos(lat1 * Math.PI / 180) *
            Math.cos(lat2 * Math.PI / 180) *
            Math.sin(dLng / 2) ** 2;
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
    }

    function validasiKunjungan() {
        const resBox = document.getElementById('validasi_result');
        const errBox = document.getElementById('validasi_error');
        const errMsg = document.getElementById('validasi_error_msg');

        resBox.classList.add('d-none');
        errBox.classList.add('d-none');

        if (!tokoData) {
            errMsg.textContent = 'Scan barcode toko terlebih dahulu.';
            return errBox.classList.remove('d-none');
        }
        if (!salesCoords) {
            errMsg.textContent = 'Ambil lokasi sales terlebih dahulu.';
            return errBox.classList.remove('d-none');
        }

        const threshold = parseFloat(document.getElementById('inp_threshold').value) || 300;

        const jarak = haversine(
            parseFloat(tokoData.latitude), parseFloat(tokoData.longitude),
            salesCoords.lat, salesCoords.lng
        );
        const accToko = parseFloat(tokoData.accuracy);
        const accSales = salesCoords.acc;
        const thresholdEff = threshold + accToko + accSales; // lampiran 3
        const diterima = jarak <= thresholdEff;

        // Tampilkan detail
        document.getElementById('r_jarak').textContent = jarak.toFixed(2) + ' m';
        document.getElementById('r_threshold_eff').textContent = thresholdEff.toFixed(2) + ' m';
        document.getElementById('r_acc_toko').textContent = accToko.toFixed(2) + ' m';
        document.getElementById('r_acc_sales').textContent = accSales.toFixed(2) + ' m';
        document.getElementById('r_threshold_base').textContent = threshold + ' m';

        // Rumus visual
        document.getElementById('r_formula').innerHTML =
            `threshold_efektif = ${threshold} + ${accToko.toFixed(1)} + ${accSales.toFixed(1)} = <strong>${thresholdEff.toFixed(1)}m</strong><br>` +
            `${jarak.toFixed(1)}m ${diterima ? '≤' : '>'} ${thresholdEff.toFixed(1)}m`;

        // Badge status
        const badge = document.getElementById('badge_status');
        if (diterima) {
            badge.className = 'badge badge-xl bg-success';
            badge.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> DITERIMA ✓';
        } else {
            badge.className = 'badge badge-xl bg-danger';
            badge.innerHTML = '<i class="bi bi-x-circle-fill me-1"></i> DITOLAK ✗';
        }

        resBox.classList.remove('d-none');
    }
</script>
@endpush
@extends('layouts.app')
@section('content')
@include('layouts.header', ['title' => 'Tambah Customer 1 — Blob', 'icon' => 'mdi-camera'])

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-gradient-info text-white py-2">
                <i class="mdi mdi-camera me-1"></i> Ambil Foto Customer (Simpan sebagai Blob)
            </div>
            <div class="card-body">
                <form action="{{ route('customer.store.blob') }}" method="POST" id="frmBlob">
                    @csrf
                    <div class="form-group mb-3">
                        <label class="fw-semibold">Nama <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" required placeholder="Nama customer">
                    </div>
                    <div class="form-group mb-3">
                        <label class="fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Email (opsional)">
                    </div>

                    {{-- Kamera --}}
                    <div class="form-group mb-3">
                        <label class="fw-semibold">Foto Customer <span class="text-danger">*</span></label>
                        <div class="border rounded p-3 text-center" style="background:#f8f9fa;">
                            <video id="videoBlob" autoplay playsinline
                                   style="width:100%;max-width:320px;border-radius:8px;display:none;"></video>
                            <canvas id="canvasBlob" style="display:none;width:100%;max-width:320px;border-radius:8px;"></canvas>
                            <img id="previewBlob" style="display:none;width:100%;max-width:320px;border-radius:8px;">
                            <div id="placeholderBlob" class="text-muted py-4">
                                <i class="mdi mdi-camera mdi-48px d-block mb-2"></i>
                                Klik "Buka Kamera" untuk mengambil foto
                            </div>
                        </div>
                        <div class="d-flex gap-2 mt-2">
                            <button type="button" id="btnBukaKameraBlob" class="btn btn-gradient-info flex-fill">
                                <i class="mdi mdi-camera me-1"></i> Buka Kamera
                            </button>
                            <button type="button" id="btnAmbilFotoBlob" class="btn btn-gradient-success flex-fill" disabled>
                                <i class="mdi mdi-camera-iris me-1"></i> Ambil Foto
                            </button>
                            <button type="button" id="btnRetakeBlob" class="btn btn-gradient-warning flex-fill" style="display:none;">
                                <i class="mdi mdi-refresh me-1"></i> Ulangi
                            </button>
                        </div>
                    </div>

                    {{-- Hidden input untuk base64 --}}
                    <input type="hidden" name="foto_blob" id="fotoBlobInput">

                    <button type="submit" id="btnSimpanBlob" class="btn btn-gradient-primary w-100" disabled>
                        <i class="mdi mdi-content-save me-1"></i> Simpan Customer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let streamBlob = null;

// Buka kamera
document.getElementById('btnBukaKameraBlob').addEventListener('click', async function() {
    try {
        streamBlob = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
        const video = document.getElementById('videoBlob');
        video.srcObject = streamBlob;
        video.style.display = 'block';
        document.getElementById('placeholderBlob').style.display = 'none';
        document.getElementById('previewBlob').style.display = 'none';
        document.getElementById('btnAmbilFotoBlob').disabled = false;
        document.getElementById('btnRetakeBlob').style.display = 'none';
        document.getElementById('btnSimpanBlob').disabled = true;
        document.getElementById('fotoBlobInput').value = '';
        this.disabled = true;
    } catch(e) {
        alert('Tidak bisa mengakses kamera: ' + e.message);
    }
});

// Ambil foto → simpan ke canvas → convert ke base64
document.getElementById('btnAmbilFotoBlob').addEventListener('click', function() {
    const video = document.getElementById('videoBlob');
    const canvas = document.getElementById('canvasBlob');
    canvas.width  = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);

    const base64 = canvas.toDataURL('image/jpeg', 0.8);
    document.getElementById('fotoBlobInput').value = base64;

    // Tampilkan preview
    const preview = document.getElementById('previewBlob');
    preview.src = base64;
    preview.style.display = 'block';
    video.style.display = 'none';

    // Hentikan kamera
    if (streamBlob) streamBlob.getTracks().forEach(t => t.stop());
    streamBlob = null;

    document.getElementById('btnAmbilFotoBlob').disabled = true;
    document.getElementById('btnRetakeBlob').style.display = 'inline-block';
    document.getElementById('btnBukaKameraBlob').disabled  = false;
    document.getElementById('btnSimpanBlob').disabled = false;
});

// Ulangi foto
document.getElementById('btnRetakeBlob').addEventListener('click', function() {
    document.getElementById('btnBukaKameraBlob').click();
});
</script>
@endpush
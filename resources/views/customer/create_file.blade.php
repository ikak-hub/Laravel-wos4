@extends('layouts.app')
@section('content')
@include('layouts.header', ['title' => 'Tambah Customer 2 — File', 'icon' => 'mdi-camera-plus'])

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-gradient-primary text-white py-2">
                <i class="mdi mdi-camera-plus me-1"></i> Ambil Foto Customer (Simpan sebagai File)
            </div>
            <div class="card-body">
                {{-- Struktur HTML sama persis dengan create_blob.blade.php --}}
                {{-- Bedanya hanya: name="foto" dan route store.file --}}
                <form action="{{ route('customer.store.file') }}" method="POST" id="frmFile">
                    @csrf
                    <div class="form-group mb-3">
                        <label class="fw-semibold">Nama <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" required placeholder="Nama customer">
                    </div>
                    <div class="form-group mb-3">
                        <label class="fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Email (opsional)">
                    </div>
                    <div class="form-group mb-3">
                        <label class="fw-semibold">Foto Customer <span class="text-danger">*</span></label>
                        <div class="border rounded p-3 text-center" style="background:#f8f9fa;">
                            <video id="videoFile" autoplay playsinline
                                   style="width:100%;max-width:320px;border-radius:8px;display:none;"></video>
                            <img id="previewFile" style="display:none;width:100%;max-width:320px;border-radius:8px;">
                            <div id="placeholderFile" class="text-muted py-4">
                                <i class="mdi mdi-camera mdi-48px d-block mb-2"></i>
                                Klik "Buka Kamera" untuk mengambil foto
                            </div>
                        </div>
                        <div class="d-flex gap-2 mt-2">
                            <button type="button" id="btnBukaKameraFile" class="btn btn-gradient-primary flex-fill">
                                <i class="mdi mdi-camera me-1"></i> Buka Kamera
                            </button>
                            <button type="button" id="btnAmbilFotoFile" class="btn btn-gradient-success flex-fill" disabled>
                                <i class="mdi mdi-camera-iris me-1"></i> Ambil Foto
                            </button>
                            <button type="button" id="btnRetakeFile" class="btn btn-gradient-warning flex-fill" style="display:none;">
                                <i class="mdi mdi-refresh me-1"></i> Ulangi
                            </button>
                        </div>
                    </div>
                    <input type="hidden" name="foto" id="fotoFileInput">
                    <button type="submit" id="btnSimpanFile" class="btn btn-gradient-primary w-100" disabled>
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
let streamFile = null;

document.getElementById('btnBukaKameraFile').addEventListener('click', async function() {
    try {
        streamFile = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
        const video = document.getElementById('videoFile');
        video.srcObject = streamFile;
        video.style.display = 'block';
        document.getElementById('placeholderFile').style.display = 'none';
        document.getElementById('previewFile').style.display = 'none';
        document.getElementById('btnAmbilFotoFile').disabled = false;
        document.getElementById('btnRetakeFile').style.display = 'none';
        document.getElementById('btnSimpanFile').disabled = true;
        this.disabled = true;
    } catch(e) {
        alert('Tidak bisa mengakses kamera: ' + e.message);
    }
});

document.getElementById('btnAmbilFotoFile').addEventListener('click', function() {
    const video = document.getElementById('videoFile');
    const canvas = document.createElement('canvas');
    canvas.width  = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);

    const base64 = canvas.toDataURL('image/jpeg', 0.8);
    document.getElementById('fotoFileInput').value = base64;

    const preview = document.getElementById('previewFile');
    preview.src = base64;
    preview.style.display = 'block';
    video.style.display = 'none';

    if (streamFile) streamFile.getTracks().forEach(t => t.stop());
    streamFile = null;

    document.getElementById('btnAmbilFotoFile').disabled = true;
    document.getElementById('btnRetakeFile').style.display = 'inline-block';
    document.getElementById('btnBukaKameraFile').disabled  = false;
    document.getElementById('btnSimpanFile').disabled = false;
});

document.getElementById('btnRetakeFile').addEventListener('click', function() {
    document.getElementById('btnBukaKameraFile').click();
});
</script>
@endpush
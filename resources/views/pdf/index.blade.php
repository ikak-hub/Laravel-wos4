@extends('layouts.app')

@section('content')
@include('layouts.header', ['title' => 'Generate PDF', 'icon' => 'mdi-file-pdf'])

<div class="row">
    <!-- PDF 1: Sertifikat Landscape -->
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body text-center">
                <i class="mdi mdi-certificate mdi-48px text-warning mb-3"></i>
                <h4 class="card-title">Sertifikat Mahasiswa</h4>
                <p class="card-description text-muted">
                    Format <strong>Landscape A4</strong><br>
                    Sertifikat penghargaan mahasiswa berprestasi
                    dengan desain profesional.
                </p>
                <a href="{{ route('pdf.sertifikat') }}"
                   class="btn btn-gradient-warning btn-lg mt-3">
                    <i class="mdi mdi-download me-1"></i> Download Sertifikat PDF
                </a>
            </div>
        </div>
    </div>

    <!-- PDF 2: Undangan Portrait -->
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body text-center">
                <i class="mdi mdi-email-outline mdi-48px text-primary mb-3"></i>
                <h4 class="card-title">Undangan / Pengumuman Fakultas</h4>
                <p class="card-description text-muted">
                    Format <strong>Portrait A4</strong> dengan header institusi.<br>
                    Undangan resmi seminar atau pengumuman dari Fakultas.
                </p>
                <a href="{{ route('pdf.undangan') }}"
                   class="btn btn-gradient-primary btn-lg mt-3">
                    <i class="mdi mdi-download me-1"></i> Download Undangan PDF
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
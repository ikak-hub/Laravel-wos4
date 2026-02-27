@extends('layouts.app')

@section('content')
@include('layouts.header', ['title' => 'Cetak Tag Harga', 'icon' => 'mdi-printer'])

<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">
                        <i class="mdi mdi-printer text-primary me-2"></i>Cetak Tag Harga
                    </h4>
                    <a href="{{ route('barang.index') }}" class="btn btn-secondary btn-sm">
                        <i class="mdi mdi-arrow-left me-1"></i> Kembali
                    </a>
                </div>

                @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- Form dikirim ke route cetak pdf, buka PDF di tab baru --}}
                <form id="frmCetak"
                    action="{{ route('barang.cetak.pdf') }}"
                    method="POST"
                    target="_blank">
                    @csrf

                    <div class="row g-4">

                        {{-- ── Kiri: daftar barang ── --}}
                        <div class="col-lg-8">
                            <div class="card border shadow-sm">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold">
                                        <i class="mdi mdi-checkbox-multiple-marked-outline me-1"></i>
                                        Pilih Barang yang Akan Dicetak
                                    </span>
                                    <div class="d-flex gap-2">
                                        <button type="button" id="btnPilihSemua"
                                            class="btn btn-outline-primary btn-sm">
                                            Pilih Semua
                                        </button>
                                        <button type="button" id="btnBatalSemua"
                                            class="btn btn-outline-secondary btn-sm">
                                            Batal Semua
                                        </button>
                                    </div>
                                </div>

                                <div class="card-body p-0">
                                    <div style="max-height:430px; overflow-y:auto;">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-light sticky-top">
                                                <tr>
                                                    <th width="42">
                                                        <input type="checkbox" id="chkAll" class="form-check-input">
                                                    </th>
                                                    <th>ID Barang</th>
                                                    <th>Nama Barang</th>
                                                    <th>Harga</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($barang as $b)
                                                <tr class="baris-barang" style="cursor:pointer;">
                                                    <td>
                                                        <input type="checkbox"
                                                            name="ids[]"
                                                            value="{{ $b->id_barang }}"
                                                            class="form-check-input chk-barang">
                                                    </td>
                                                    <td><code>{{ $b->id_barang }}</code></td>
                                                    <td>{{ $b->nama }}</td>
                                                    <td>Rp {{ number_format($b->harga, 0, ',', '.') }}</td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted py-3">
                                                        Belum ada data barang.
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="card-footer bg-light">
                                    <small id="infoTerpilih" class="text-muted">
                                        <i class="mdi mdi-information-outline me-1"></i>
                                        Belum ada barang dipilih.
                                    </small>
                                </div>
                            </div>
                        </div>{{-- /col kiri --}}

                        {{-- ── Kanan: koordinat + preview grid ── --}}
                        <div class="col-lg-4">

                            {{-- Koordinat --}}
                            <div class="card border shadow-sm mb-3">
                                <div class="card-header bg-light fw-semibold">
                                    <i class="mdi mdi-crosshairs-gps me-1"></i>
                                    Posisi Awal Cetak
                                </div>
                                <div class="card-body">
                                    <p class="text-muted small mb-3">
                                        Kertas <strong>TnJ No. 108</strong> memiliki
                                        <strong>5 kolom × 8 baris = 40 label</strong>.<br>
                                        Masukkan koordinat label pertama yang akan diisi,
                                        sehingga label yang sudah terpakai bisa dilewati.
                                    </p>

                                    <div class="row g-3">
                                        <div class="col-6">
                                            <label class="form-label fw-semibold">Kolom (X)</label>
                                            <input type="number" name="start_x" id="inpX"
                                                class="form-control form-control-lg text-center"
                                                value="1" min="1" max="5">
                                            <small class="text-muted">1 = kiri, 5 = kanan</small>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label fw-semibold">Baris (Y)</label>
                                            <input type="number" name="start_y" id="inpY"
                                                class="form-control form-control-lg text-center"
                                                value="1" min="1" max="8">
                                            <small class="text-muted">1 = atas, 8 = bawah</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Preview grid 5×8 --}}
                            <div class="card border shadow-sm mb-3">
                                <div class="card-header bg-light fw-semibold">
                                    <i class="mdi mdi-grid me-1"></i>
                                    Preview Kertas Label
                                </div>
                                <div class="card-body p-2">
                                    <div id="divGrid"></div>
                                    <div class="mt-2 d-flex gap-2 flex-wrap">
                                        <span>
                                            <span class="badge"
                                                style="background:#e74c3c;padding:4px 8px;">&nbsp;</span>
                                            Posisi awal
                                        </span>
                                        <span>
                                            <span class="badge"
                                                style="background:#27ae60;padding:4px 8px;">&nbsp;</span>
                                            Label terisi
                                        </span>
                                        <span>
                                            <span class="badge bg-secondary"
                                                style="padding:4px 8px;">&nbsp;</span>
                                            Sudah terpakai
                                        </span>
                                        <span>
                                            <span class="badge"
                                                style="background:#ecf0f1;border:1px solid #bdc3c7;padding:4px 8px;">&nbsp;</span>
                                            Kosong
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Tombol Generate --}}
                            <div class="d-grid">
                                <button type="submit" id="btnGenerate"
                                    class="btn btn-gradient-success btn-lg">
                                    <i class="mdi mdi-file-pdf me-2"></i>
                                    Generate PDF
                                </button>
                            </div>

                        </div>{{-- /col kanan --}}

                    </div>{{-- /row --}}
                </form>

            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(function() {

        $('#chkAll').on('change', function() {
            $('.chk-barang').prop('checked', this.checked);
            updateInfo();
        });

        $('#btnPilihSemua').on('click', function() {
            $('.chk-barang, #chkAll').prop('checked', true);
            updateInfo();
        });

        $('#btnBatalSemua').on('click', function() {
            $('.chk-barang, #chkAll').prop('checked', false);
            updateInfo();
        });

        $('.baris-barang').on('click', function(e) {
            if (!$(e.target).is('input')) {
                var cb = $(this).find('.chk-barang');
                cb.prop('checked', !cb.prop('checked'));
                updateInfo();
            }
        });

        $('.chk-barang').on('change', updateInfo);

        function updateInfo() {
            var n = $('.chk-barang:checked').length;
            var txt = n === 0 ?
                '<i class="mdi mdi-information-outline me-1"></i>Belum ada barang dipilih.' :
                '<i class="mdi mdi-check-circle text-success me-1"></i><strong>' + n + '</strong> barang dipilih.';
            $('#infoTerpilih').html(txt);
            renderGrid();
        }

        // ── Grid preview ───
        function renderGrid() {
            var x = parseInt($('#inpX').val()) || 1;
            var y = parseInt($('#inpY').val()) || 1;
            var sel = $('.chk-barang:checked').length;

            // 0-based linear start index
            var startIdx = (y - 1) * 5 + (x - 1);

            var html = '<table style="width:100%;border-collapse:collapse;">';
            for (var r = 0; r < 8; r++) {
                html += '<tr>';
                for (var c = 0; c < 5; c++) {
                    var idx = r * 5 + c;
                    var bg, title, txt = '';

                    if (idx < startIdx) {
                        bg = '#bdc3c7'; // sudah terpakai
                        title = 'Sudah terpakai';
                    } else if (idx === startIdx) {
                        bg = '#e74c3c'; // posisi awal
                        txt = '<span style="color:#fff;font-size:8px;font-weight:bold;">START</span>';
                        title = 'Posisi awal cetak (X=' + x + ', Y=' + y + ')';
                    } else if (idx <= startIdx + sel - 1) {
                        bg = '#27ae60'; // akan diisi
                        txt = '<span style="color:#fff;font-size:8px;">✓</span>';
                        title = 'Label ke-' + (idx - startIdx + 1);
                    } else {
                        bg = '#ecf0f1'; // kosong
                        title = 'Kosong';
                    }

                    html += '<td title="' + title + '" ' +
                        'style="background:' + bg + ';' +
                        'border:1px solid #ccc;' +
                        'width:20%;height:22px;' +
                        'text-align:center;vertical-align:middle;">' +
                        txt + '</td>';
                }
                html += '</tr>';
            }
            html += '</table>';
            $('#divGrid').html(html);
        }

        $('#inpX, #inpY').on('input change', renderGrid);

        // Validasi sebelum submit
        $('#frmCetak').on('submit', function(e) {
            if ($('.chk-barang:checked').length === 0) {
                e.preventDefault();
                alert('Pilih minimal 1 barang untuk dicetak!');
            }
        });

        // Render awal
        renderGrid();
    });
</script>
@endpush
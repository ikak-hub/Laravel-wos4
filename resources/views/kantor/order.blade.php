@extends('layouts.kantor')

@section('content')
<div class="page-header">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-success text-white me-2">
            <i class="mdi mdi-receipt"></i>
        </span> Pesanan Lunas
    </h3>
</div>

<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">
                        Pesanan Lunas — {{ $vendor->nama_vendor }}
                    </h4>
                    <span class="badge badge-gradient-success fs-6">
                        {{ $orders->count() }} pesanan
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover" id="tblOrders">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama Guest</th>
                                <th>Waktu Pesan</th>
                                <th>Metode Bayar</th>
                                <th class="text-end">Total</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $i => $order)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>
                                    <span class="badge badge-gradient-info font-monospace fs-6">
                                        {{ $order->nama }}
                                    </span>
                                </td>
                                <td>{{ $order->created_at->format('d M Y, H:i') }}</td>
                                <td>
                                    @if($order->metode_bayar)
                                    <span class="badge badge-gradient-primary">
                                        {{ strtoupper(str_replace('_', ' ', $order->metode_bayar)) }}
                                    </span>
                                    @else
                                    <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-end fw-semibold">
                                    Rp {{ number_format($order->total, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-gradient-success">✓ Lunas</span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-gradient-info"
                                        data-bs-toggle="modal"
                                        data-bs-target="#detailModal{{ $order->idpesanan }}">
                                        <i class="mdi mdi-eye me-1"></i> Detail
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="mdi mdi-inbox mdi-36px d-block mb-2"></i>
                                    Belum ada pesanan yang lunas.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- ===================== MODALS (di luar table) ===================== --}}
@foreach($orders as $order)
<div class="modal fade" id="detailModal{{ $order->idpesanan }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-info text-white">
                <h5 class="modal-title">
                    <i class="mdi mdi-receipt me-1"></i>
                    Detail Pesanan — {{ $order->nama }}
                </h5>
                <button type="button" class="btn-close btn-close-white"
                    data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Menu</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Harga</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->details as $det)
                            <tr>
                                <td>
                                    {{ $det->menu->nama_menu ?? '(menu dihapus)' }}
                                    @if($det->catatan)
                                    <br><small class="text-muted">
                                        <i class="mdi mdi-note-text-outline me-1"></i>
                                        {{ $det->catatan }}
                                    </small>
                                    @endif
                                </td>
                                <td class="text-center">{{ $det->jumlah }}</td>
                                <td class="text-end">
                                    Rp {{ number_format($det->harga, 0, ',', '.') }}
                                </td>
                                <td class="text-end">
                                    Rp {{ number_format($det->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold table-light">
                                <td colspan="3" class="text-end">TOTAL:</td>
                                <td class="text-end text-success">
                                    Rp {{ number_format($order->total, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="mt-2 text-muted small">
                    <i class="mdi mdi-clock-outline me-1"></i>
                    Dibayar: {{ $order->updated_at->format('d M Y, H:i') }}
                    &nbsp;|&nbsp;
                    <i class="mdi mdi-credit-card-outline me-1"></i>
                    {{ strtoupper(str_replace('_', ' ', $order->metode_bayar ?? '-')) }}
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach
{{-- ================================================================= --}}

@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
$(function() {
    @if($orders->isNotEmpty())
    $('#tblOrders').DataTable({
        language: {
            search:     'Cari:',
            lengthMenu: 'Tampilkan _MENU_ data',
            info:       'Menampilkan _START_–_END_ dari _TOTAL_ pesanan',
            paginate:   { next: 'Selanjutnya', previous: 'Sebelumnya' }
        },
        order: [[0, 'desc']],
        columnDefs: [{ orderable: false, targets: [6] }]
    });
    @endif
});
</script>
@endpush
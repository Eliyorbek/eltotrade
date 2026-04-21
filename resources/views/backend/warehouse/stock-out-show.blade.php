@extends('backend.inc.app')
@section('title', 'Mahsulot Chiqim Tafsilotlari')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">
                    <i class="fas fa-sign-out-alt me-2"></i>
                    Mahsulot Chiqim Tafsilotlari
                </h4>
                <small class="text-muted">{{ $stockOut->reference_number }}</small>
            </div>
            <a href="{{ route('warehouse.stockOutHistory') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Orqaba
            </a>
        </div>

        {{-- Header Card --}}
        <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white;">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <h6 style="opacity: 0.9;">Chiqim Raqami</h6>
                        <h4 class="fw-bold">{{ $stockOut->reference_number }}</h4>
                    </div>
                    <div class="col-md-3">
                        <h6 style="opacity: 0.9;">Sababu</h6>
                        <h4 class="fw-bold">{{ $stockOut->reason_label }}</h4>
                    </div>
                    <div class="col-md-3">
                        <h6 style="opacity: 0.9;">Chiqim Sanasi</h6>
                        <h4 class="fw-bold">{{ $stockOut->issued_date->format('d.m.Y') }}</h4>
                    </div>
                    <div class="col-md-3">
                        <h6 style="opacity: 0.9;">Jami Xarajat</h6>
                        <h4 class="fw-bold">{{ number_format($stockOut->total_amount, 0) }} so'm</h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- Chiqim Mahsulotlari --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">🛍️ Chiqim Mahsulotlari ({{ $stockOut->items->count() }} ta)</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                        <tr>
                            <th class="ps-3">#</th>
                            <th>Mahsulot Nomi</th>
                            <th>Miqdor</th>
                            <th>Narxi (Cost)</th>
                            <th>Jami Xarajat</th>
                            <th class="text-end pe-3">Seri #</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($stockOut->items as $index => $item)
                            <tr>
                                <td class="ps-3">{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $item->product->name }}</strong><br>
                                    <small class="text-muted">{{ $item->product->sku }}</small>
                                </td>
                                <td>{{ $item->quantity }} {{ $item->product->unit }}</td>
                                <td>{{ number_format($item->unit_price, 0) }}</td>
                                <td class="fw-bold text-danger">
                                    -{{ number_format($item->total, 0) }} so'm
                                </td>
                                <td class="text-end pe-3">{{ $item->batch_number ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Mahsulot yo'q</td>
                            </tr>
                        @endforelse
                        </tbody>
                        <tfoot class="table-light">
                        <tr>
                            <th colspan="4" class="ps-3">JAMI XARAJAT</th>
                            <th class="fw-bold text-danger">-{{ number_format($stockOut->total_amount, 0) }} so'm</th>
                            <th></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Izohlar --}}
        @if($stockOut->notes)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">📝 Izohlar</h5>
                </div>
                <div class="card-body">
                    {{ nl2br($stockOut->notes) }}
                </div>
            </div>
        @endif

        {{-- Info --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p>
                            <strong>Qayd qilgan:</strong> {{ $stockOut->user->name }}<br>
                            <strong>Vaqti:</strong> {{ $stockOut->created_at->format('d.m.Y H:i') }}<br>
                            <strong>Status:</strong>
                            <span class="badge bg-success">{{ $stockOut->status_label }}</span>
                        </p>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="{{ route('warehouse.stockOutHistory') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-list me-1"></i> Chiqim Tarixiga
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

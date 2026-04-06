@extends('backend.inc.app')
@section('title', 'Sotuv Tafsilotlari')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">{{ $sale->sale_number }}</h4>
                <small class="text-muted">{{ $sale->created_at->format('d.m.Y H:i') }}</small>
            </div>
            <div>
                <a href="{{ route('sales.printReceipt', $sale) }}" class="btn btn-primary" target="_blank">
                    <i class="fas fa-print me-1"></i> Print
                </a>
                <a href="{{ route('sales.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Orqaga
                </a>
            </div>
        </div>

        <div class="row">
            {{-- Main Info --}}
            <div class="col-md-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Sotuv Tafsilotlari</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Sotuv Raqami:</strong>
                                <div>{{ $sale->sale_number }}</div>
                            </div>
                            <div class="col-md-6">
                                <strong>Status:</strong>
                                <div>
                                    @if($sale->status === 'completed')
                                        <span class="badge bg-success">Raqomlandi</span>
                                    @else
                                        <span class="badge bg-warning">Kutilmoqda</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Vaqti:</strong>
                                <div>{{ $sale->created_at->format('d.m.Y H:i:s') }}</div>
                            </div>
                            <div class="col-md-6">
                                <strong>Kassir:</strong>
                                <div>{{ $sale->user->name }}</div>
                            </div>
                        </div>

                        @if($sale->customer_name)
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Mijoz Ismi:</strong>
                                    <div>{{ $sale->customer_name }}</div>
                                </div>
                                @if($sale->customer_phone)
                                    <div class="col-md-6">
                                        <strong>Telefon:</strong>
                                        <div>{{ $sale->customer_phone }}</div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <strong>To'lov Usuli:</strong>
                                <div>
                                    @if($sale->payment_method === 'cash')
                                        Naqd pul
                                    @elseif($sale->payment_method === 'card')
                                        Plastik karta
                                    @else
                                        Bank transferi
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <strong>Qo'shimcha Izohlar:</strong>
                                <div>{{ $sale->notes ?? '—' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Items Table --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Mahsulotlar ({{ $sale->items->count() }} ta)</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Mahsulot</th>
                                    <th>Narxi</th>
                                    <th>Miqdori</th>
                                    <th>Jami</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($sale->items as $item)
                                    <tr>
                                        <td class="ps-3">
                                            <strong>{{ $item->product->name }}</strong>
                                            <br>
                                            <small class="text-muted">SKU: {{ $item->product->sku }}</small>
                                        </td>
                                        <td>{{ number_format($item->unit_price, 0) }}</td>
                                        <td>{{ $item->quantity }} {{ $item->product->unit }}</td>
                                        <td class="fw-bold">{{ number_format($item->total, 0) }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Summary --}}
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Xulosa</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Jami:</span>
                            <strong>{{ number_format($sale->total_amount, 0) }}</strong>
                        </div>

                        @if($sale->discount_amount > 0)
                            <div class="d-flex justify-content-between mb-2">
                                <span>Chegirma:</span>
                                <strong class="text-danger">
                                    -{{ number_format($sale->discount_amount, 0) }}
                                </strong>
                            </div>
                        @endif

                        <div style="border-top: 2px solid #ddd; padding-top: 10px;" class="mt-2">
                            <div class="d-flex justify-content-between">
                                <span style="font-size: 16px; font-weight: bold;">Yakuniy:</span>
                                <strong style="font-size: 16px; color: #28a745;">
                                    {{ number_format($sale->final_amount, 0) }}
                                </strong>
                            </div>
                        </div>

                        @if($sale->discount_amount > 0)
                            <div class="alert alert-info mt-3 mb-0" style="font-size: 12px;">
                                <strong>Chegirma:</strong>
                                @if($sale->discount_type === 'percent')
                                    {{ $sale->discount_value }}%
                                    ({{ number_format($sale->discount_amount, 0) }})
                                @else
                                    {{ number_format($sale->discount_value, 0) }}
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Actions --}}
                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-body">
                        <a href="{{ route('sales.printReceipt', $sale) }}"
                           class="btn btn-primary w-100 mb-2" target="_blank">
                            <i class="fas fa-print me-1"></i> Chekni Print Qilish
                        </a>
                        <a href="{{ route('sales.create') }}"
                           class="btn btn-success w-100 mb-2">
                            <i class="fas fa-plus me-1"></i> Yangi Sotuv
                        </a>
                        <a href="{{ route('sales.index') }}"
                           class="btn btn-outline-secondary w-100">
                            <i class="fas fa-list me-1"></i> Sotuvlar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('backend.inc.app')
@section('title', 'Mahsulot Kirim Tafsilotlari')
    <style>
        .receipt-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .receipt-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .receipt-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .receipt-info-item {
            background: rgba(255,255,255,0.1);
            padding: 10px;
            border-radius: 6px;
        }

        .receipt-info-label {
            font-size: 12px;
            opacity: 0.8;
            margin-bottom: 5px;
        }

        .receipt-info-value {
            font-weight: bold;
            font-size: 14px;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 10px;
        }

        .status-received {
            background: #d4edda;
            color: #155724;
        }
    </style>
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">
                    <i class="fas fa-inbox me-2"></i>
                    Mahsulot Kirim Tafsilotlari
                </h4>
                <small class="text-muted">{{ $stockIn->reference_number }}</small>
            </div>
            <div>
                <a href="{{ route('warehouse.stockInHistory') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Orqaba
                </a>
            </div>
        </div>

        {{-- Header --}}
        <div class="receipt-header">
            <div class="receipt-title">{{ $stockIn->reference_number }}</div>
            <div class="receipt-info">
                <div class="receipt-info-item">
                    <div class="receipt-info-label">📑 Ta'minotchi</div>
                    <div class="receipt-info-value">{{ $stockIn->supplier->name }}</div>
                </div>
                <div class="receipt-info-item">
                    <div class="receipt-info-label">📅 Kirim Sanasi</div>
                    <div class="receipt-info-value">{{ $stockIn->received_date->format('d.m.Y') }}</div>
                </div>
                <div class="receipt-info-item">
                    <div class="receipt-info-label">👤 Qayd qilgan</div>
                    <div class="receipt-info-value">{{ $stockIn->user->name }}</div>
                </div>
                <div class="receipt-info-item">
                    <div class="receipt-info-label">💰 Jami Summa</div>
                    <div class="receipt-info-value">{{ number_format($stockIn->total_amount, 0) }} so'm</div>
                </div>
            </div>
            <div>
            <span class="status-badge status-received">
                <i class="fas fa-check-circle me-1"></i>
                {{ $stockIn->status_label }}
            </span>
            </div>
        </div>

        {{-- Ta'minotchi Ma'lumotlari --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">📑 Ta'minotchi Ma'lumotlari</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p>
                            <strong>{{ $stockIn->supplier->name }}</strong><br>
                            <small class="text-muted">
                                @if($stockIn->supplier->contact_person)
                                    Kontakt: {{ $stockIn->supplier->contact_person }}<br>
                                @endif
                                @if($stockIn->supplier->phone)
                                    Tel: {{ $stockIn->supplier->phone }}<br>
                                @endif
                                @if($stockIn->supplier->email)
                                    Email: {{ $stockIn->supplier->email }}<br>
                                @endif
                                @if($stockIn->supplier->address)
                                    Manzil: {{ $stockIn->supplier->address }}
                                @endif
                            </small>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p>
                            <strong>To'lov Shartlari:</strong>
                            {{ $stockIn->supplier->payment_terms ?? 'Noma\'lum' }}<br>
                            <strong>Yetkazib Berish Vaqti:</strong>
                            {{ $stockIn->supplier->delivery_time ?? '—' }} kun
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kirim Mahsulotlari --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">🛍️ Kirim Mahsulotlari ({{ $stockIn->items->count() }} ta)</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                        <tr>
                            <th class="ps-3">#</th>
                            <th>Mahsulot Nomi</th>
                            <th>Miqdor</th>
                            <th>Narx</th>
                            <th>Jami</th>
                            <th>Muddati</th>
                            <th>Seri #</th>
                            <th class="text-end pe-3">Joylashtirish</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($stockIn->items as $index => $item)
                            <tr>
                                <td class="ps-3">{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $item->product->name }}</strong><br>
                                    <small class="text-muted">{{ $item->product->sku }}</small>
                                </td>
                                <td>{{ $item->quantity }} {{ $item->product->unit }}</td>
                                <td>{{ number_format($item->unit_price, 0) }}</td>
                                <td class="fw-bold text-success">{{ number_format($item->total, 0) }}</td>
                                <td>
                                    @if($item->expiry_date)
                                        {{ $item->expiry_date->format('d.m.Y') }}
                                        @if($item->isExpired())
                                            <br><span class="badge bg-danger">O'tgan</span>
                                        @elseif($item->isExpiringSoon())
                                            <br><span class="badge bg-warning">Birov o'tib ketadi</span>
                                        @endif
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>{{ $item->batch_number ?? '—' }}</td>
                                <td class="text-end pe-3">{{ $item->location ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Mahsulot yo'q</td>
                            </tr>
                        @endforelse
                        </tbody>
                        <tfoot class="table-light">
                        <tr>
                            <th colspan="4" class="ps-3">JAMI</th>
                            <th class="fw-bold text-success">{{ number_format($stockIn->total_amount, 0) }} so'm</th>
                            <th colspan="3"></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Izohlar --}}
        @if($stockIn->notes)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">📝 Izohlar</h5>
                </div>
                <div class="card-body">
                    {{ nl2br($stockIn->notes) }}
                </div>
            </div>
        @endif

        {{-- Amallar --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <a href="{{ route('warehouse.stockInHistory') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-list me-1"></i> Kirim Tarixiga
                </a>
                <a href="{{ route('warehouse.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-warehouse me-1"></i> Ombor Dashboardi
                </a>
            </div>
        </div>
    </div>
@endsection

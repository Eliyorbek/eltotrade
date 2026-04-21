@extends('backend.inc.app')
@section('title', 'Kunlik Hisoboti')

    <style>
        .report-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .report-date {
            font-size: 14px;
            opacity: 0.9;
        }

        .report-title {
            font-size: 32px;
            font-weight: bold;
            margin: 10px 0;
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .metric {
            background: rgba(255,255,255,0.1);
            padding: 15px;
            border-radius: 8px;
            backdrop-filter: blur(10px);
        }

        .metric-label {
            font-size: 12px;
            opacity: 0.8;
            margin-bottom: 5px;
        }

        .metric-value {
            font-size: 20px;
            font-weight: bold;
        }

        .date-picker {
            margin-bottom: 20px;
        }
    </style>


@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('reports.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Orqaga
            </a>
            <div>
                <input type="date" id="dateInput" class="form-control form-control-sm"
                       style="width: 150px; display: inline-block;"
                       value="{{ $date }}"
                       onchange="window.location.href='{{ route('reports.daily') }}?date=' + this.value">
            </div>
        </div>

        {{-- Header --}}
        <div class="report-header">
            <div class="report-date">📅 {{ \Carbon\Carbon::parse($date)->format('d.m.Y - l') }}</div>
            <div class="report-title">{{ $report['total_sales'] }} ta Sotuv</div>
            <div class="metrics-grid">
                <div class="metric">
                    <div class="metric-label">Jami Summa</div>
                    <div class="metric-value">{{ number_format($report['total_amount'], 0) }}</div>
                </div>
                <div class="metric">
                    <div class="metric-label">Chegirma</div>
                    <div class="metric-value">-{{ number_format($report['total_discount'], 0) }}</div>
                </div>
                <div class="metric">
                    <div class="metric-label">Yakuniy</div>
                    <div class="metric-value">{{ number_format($report['final_amount'], 0) }}</div>
                </div>
                <div class="metric">
                    <div class="metric-label">O'rtacha Chek</div>
                    <div class="metric-value">
                        @php
                            $avg = $report['total_sales'] > 0 ? $report['final_amount'] / $report['total_sales'] : 0;
                        @endphp
                        {{ number_format($avg, 0) }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Payment Methods --}}
        <div class="row mb-4">
            @foreach($report['payment_methods'] as $method => $data)
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="text-muted">
                                @if($method === 'cash')
                                    💰 Naqd Pul
                                @elseif($method === 'card')
                                    🏧 Plastik Karta
                                @else
                                    🔄 Transfer
                                @endif
                            </h6>
                            <h4 class="fw-bold">{{ $data['count'] }} ta</h4>
                            <small class="text-muted">{{ number_format($data['amount'], 0) }} so'm</small>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Sales Table --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">📋 Bu Kunning Sotuvlari ({{ $sales->total() }} ta)</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                        <tr>
                            <th class="ps-3">Sotuv #</th>
                            <th>Vaqti</th>
                            <th>Mahsulotlar</th>
                            <th>Jami</th>
                            <th>Chegirma</th>
                            <th>Yakuniy</th>
                            <th>To'lov</th>
                            <th class="text-end pe-3">Amallar</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($sales as $sale)
                            <tr>
                                <td class="ps-3"><strong>{{ $sale->sale_number }}</strong></td>
                                <td>{{ $sale->created_at->format('H:i') }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $sale->items->count() }} ta</span>
                                </td>
                                <td>{{ number_format($sale->total_amount, 0) }}</td>
                                <td>
                                    @if($sale->discount_amount > 0)
                                        <span class="text-danger">-{{ number_format($sale->discount_amount, 0) }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="fw-bold text-success">{{ number_format($sale->final_amount, 0) }}</td>
                                <td>
                                    @if($sale->payment_method === 'cash')
                                        💰
                                    @elseif($sale->payment_method === 'card')
                                        🏧
                                    @else
                                        🔄
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    Bu kun sotuv yo'q
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($sales->hasPages())
                <div class="card-footer bg-white">
                    {{ $sales->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

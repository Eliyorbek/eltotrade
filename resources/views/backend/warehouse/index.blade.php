@extends('backend.inc.app')
@section('title', 'Ombor Boshqaruvi')
    <style>
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .stat-card h6 {
            margin: 0 0 10px 0;
            font-size: 13px;
            opacity: 0.9;
            text-transform: uppercase;
        }

        .stat-card .amount {
            font-size: 28px;
            font-weight: bold;
        }

        .stat-card:nth-child(2) {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .stat-card:nth-child(3) {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .stat-card:nth-child(4) {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        .stat-card:nth-child(5) {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }

        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
            margin-bottom: 30px;
        }

        .action-btn {
            text-align: center;
            padding: 15px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            text-decoration: none;
            color: inherit;
            transition: all 0.3s;
        }

        .action-btn:hover {
            border-color: #007bff;
            box-shadow: 0 4px 12px rgba(0,123,255,0.15);
            transform: translateY(-2px);
        }

        .action-icon {
            font-size: 24px;
            margin-bottom: 8px;
        }

        .action-title {
            font-weight: 600;
            font-size: 12px;
        }

        .warning-table {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .low-stock-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            border-bottom: 1px solid #eee;
        }

        .low-stock-row:last-child {
            border-bottom: none;
        }

        .stock-bar {
            width: 100px;
            height: 6px;
            background: #ddd;
            border-radius: 3px;
            overflow: hidden;
        }

        .stock-bar-fill {
            height: 100%;
            background: #dc3545;
        }

        .expiry-alert {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
    </style>
@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">
                    <i class="fas fa-warehouse me-2"></i>
                    Ombor Boshqaruvi
                </h4>
                <small class="text-muted">Mahsulot kirim, chiqim va ta'minotchi</small>
            </div>
        </div>

        {{-- Statistics Cards --}}
        <div class="row">
            <div class="col-md-6 col-lg-3">
                <div class="stat-card">
                    <h6>BUGUN KIRIM</h6>
                    <div class="amount">{{ $stockInStats['today'] }}</div>
                    <small>ta kirim</small>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="stat-card">
                    <h6>OYLIK KIRIM</h6>
                    <div class="amount">{{ number_format($stockInStats['month'], 0) }}</div>
                    <small>so'm</small>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="stat-card">
                    <h6>OYLIK CHIQIM</h6>
                    <div class="amount">{{ number_format($stockOutStats['month'], 0) }}</div>
                    <small>so'm xarajat</small>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="stat-card">
                    <h6>PAST STOCK</h6>
                    <div class="amount">{{ $lowStockProducts->count() }}</div>
                    <small>ta mahsulot</small>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="action-buttons">
            <a href="{{ route('warehouse.stockInCreate') }}" class="action-btn">
                <div class="action-icon">➕</div>
                <div class="action-title">Yangi Kirim</div>
            </a>
            <a href="{{ route('warehouse.stockOutCreate') }}" class="action-btn">
                <div class="action-icon">📤</div>
                <div class="action-title">Yangi Chiqim</div>
            </a>
            <a href="{{ route('warehouse.stockInHistory') }}" class="action-btn">
                <div class="action-icon">📋</div>
                <div class="action-title">Kirim Tarixlari</div>
            </a>
            <a href="{{ route('warehouse.stockOutHistory') }}" class="action-btn">
                <div class="action-icon">📑</div>
                <div class="action-title">Chiqim Tarixlari</div>
            </a>
            <a href="{{ route('warehouse.suppliers') }}" class="action-btn">
                <div class="action-icon">📑</div>
                <div class="action-title">Ta'minotchilar</div>
            </a>
        </div>

        {{-- Expiring Products Alert --}}
        @if($expiringProducts->count() > 0)
            <div class="expiry-alert">
                <h6 class="mb-2">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    ⚠️ {{ $expiringProducts->count() }} ta mahsulotning muddati birov o'tib ketadi!
                </h6>
                <small>
                    Ushbu mahsulotlarni 30 kun ichida sotishni yoki chiqimni tavsiya qilishdi.
                </small>
            </div>
        @endif

        {{-- Low Stock Products --}}
        @if($lowStockProducts->count() > 0)
            <div class="warning-table">
                <h5 class="mb-3">📉 Past Stock Mahsulotlar</h5>
                @foreach($lowStockProducts as $product)
                    <div class="low-stock-row">
                        <div style="flex: 1;">
                            <strong>{{ $product->name }}</strong>
                            <br>
                            <small class="text-muted">SKU: {{ $product->sku }}</small>
                        </div>
                        <div style="width: 120px;">
                            <div class="stock-bar">
                                <div class="stock-bar-fill" style="width: {{ min($product->stock / 20 * 100, 100) }}%"></div>
                            </div>
                        </div>
                        <div style="text-align: right; min-width: 80px;">
                            <span class="badge bg-danger">{{ $product->stock }} dona</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Recent Stock In --}}
        <div class="row">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">📥 Oxirgi Kirirmilar</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Raqami</th>
                                    <th>Ta'minotchi</th>
                                    <th>Summa</th>
                                    <th>Sana</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse(\App\Models\StockIn::latest()->limit(5)->get() as $in)
                                    <tr>
                                        <td class="ps-3">
                                            <a href="{{ route('warehouse.stockInShow', $in) }}">
                                                {{ $in->reference_number }}
                                            </a>
                                        </td>
                                        <td>{{ $in->supplier->name }}</td>
                                        <td class="fw-bold text-success">{{ number_format($in->total_amount, 0) }}</td>
                                        <td>{{ $in->received_date->format('d.m.Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">Hali kirim yo'q</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">📤 Oxirgi Chiqimlar</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Raqami</th>
                                    <th>Sababu</th>
                                    <th>Summa</th>
                                    <th>Sana</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse(\App\Models\StockOut::latest()->limit(5)->get() as $out)
                                    <tr>
                                        <td class="ps-3">
                                            <a href="{{ route('warehouse.stockOutShow', $out) }}">
                                                {{ $out->reference_number }}
                                            </a>
                                        </td>
                                        <td>
                                        <span class="badge bg-{{
                                            $out->reason === 'damage' ? 'danger' :
                                            ($out->reason === 'expiry' ? 'warning' : 'info')
                                        }}">
                                            @switch($out->reason)
                                                @case('damage')
                                                    ❌ Shikastlanish
                                                    @break
                                                @case('expiry')
                                                    ⏰ Muddati
                                                    @break
                                                @case('return')
                                                    ↩️ Qaytarish
                                                    @break
                                                @case('adjustment')
                                                    ⚖️ Tekshiruv
                                                    @break
                                                @default
                                                    Boshqa
                                            @endswitch
                                        </span>
                                        </td>
                                        <td class="fw-bold text-danger">-{{ number_format($out->total_amount, 0) }}</td>
                                        <td>{{ $out->issued_date->format('d.m.Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">Hali chiqim yo'q</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

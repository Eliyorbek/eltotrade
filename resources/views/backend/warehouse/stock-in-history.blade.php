@extends('backend.inc.app')
@section('title', 'Mahsulot Kirim Tarixlari')
    <style>
        .filter-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .filter-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            align-items: end;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-box {
            background: white;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .stat-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }

        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }

        .status-received {
            background: #d4edda;
            color: #155724;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-completed {
            background: #d1ecf1;
            color: #0c5460;
        }
    </style>
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">
                    <i class="fas fa-history me-2"></i>
                    Mahsulot Kirim Tarixlari
                </h4>
                <small class="text-muted">Barcha kirim raqordlari</small>
            </div>
            <a href="{{ route('warehouse.stockInCreate') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-1"></i> Yangi Kirim
            </a>
        </div>

        {{-- Statistics --}}
        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-label">JAMI KIRIRMILAR</div>
                <div class="stat-value">{{ $stockIns->total() }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">BUGUN KIRIM</div>
                <div class="stat-value">
                    @php
                        $todayCount = \App\Models\StockIn::whereDate('created_at', today())->count();
                    @endphp
                    {{ $todayCount }}
                </div>
            </div>
            <div class="stat-box">
                <div class="stat-label">OYLIK SUMMA</div>
                <div class="stat-value">
                    @php
                        $monthTotal = \App\Models\StockIn::whereMonth('created_at', now()->month)->sum('total_amount');
                    @endphp
                    {{ number_format($monthTotal, 0) }}
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="filter-card">
            <form method="GET" action="{{ route('warehouse.stockInHistory') }}" class="filter-form">
                <div>
                    <label class="form-label">Ta'minotchi</label>
                    <select name="supplier_id" class="form-select form-select-sm">
                        <option value="">-- Hammasini ko'rsat --</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">Boshlang'ich Sana</label>
                    <input type="date" name="date_from" class="form-control form-control-sm"
                           value="{{ request('date_from') }}">
                </div>

                <div>
                    <label class="form-label">Tugash Sanasi</label>
                    <input type="date" name="date_to" class="form-control form-control-sm"
                           value="{{ request('date_to') }}">
                </div>

                <div>
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-search me-1"></i> Qidirish
                    </button>
                </div>

                @if(request()->filled('supplier_id') || request()->filled('date_from') || request()->filled('date_to'))
                    <div>
                        <a href="{{ route('warehouse.stockInHistory') }}" class="btn btn-secondary btn-sm w-100">
                            <i class="fas fa-redo me-1"></i> Qayta Yuklash
                        </a>
                    </div>
                @endif
            </form>
        </div>

        {{-- Kirirmilar Table --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">📋 Kirim Ro'yxati</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                        <tr>
                            <th class="ps-3">Kirim Raqami</th>
                            <th>Ta'minotchi</th>
                            <th>Sanasi</th>
                            <th>Mahsulotlar</th>
                            <th>Jami Summa</th>
                            <th>Status</th>
                            <th>Qayd qilgan</th>
                            <th class="text-end pe-3">Amallar</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($stockIns as $in)
                            <tr>
                                <td class="ps-3">
                                    <strong>{{ $in->reference_number }}</strong>
                                </td>
                                <td>{{ $in->supplier->name }}</td>
                                <td>{{ $in->received_date->format('d.m.Y') }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $in->items->count() }} ta</span>
                                </td>
                                <td class="fw-bold text-success">
                                    {{ number_format($in->total_amount, 0) }} so'm
                                </td>
                                <td>
                                <span class="status-badge status-{{ $in->status }}">
                                    {{ $in->status_label }}
                                </span>
                                </td>
                                <td>
                                    <small>{{ $in->user->name }}</small><br>
                                    <small class="text-muted">{{ $in->created_at->format('d.m.Y H:i') }}</small>
                                </td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('warehouse.stockInShow', $in) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <i class="fas fa-inbox fa-3x mb-3"></i><br>
                                    Kirim tarixlari yo'q
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($stockIns->hasPages())
                <div class="card-footer bg-white">
                    {{ $stockIns->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

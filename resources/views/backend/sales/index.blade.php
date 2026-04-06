@extends('backend.inc.app')
@section('title', 'Sotuvlar')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">
                    <i class="fas fa-receipt me-2"></i>
                    Sotuvlar
                </h4>
                <small class="text-muted">Barcha sotuv operatsiyalari</small>
            </div>
            <a href="{{ route('sales.create') }}" class="btn btn-success btn-lg">
                <i class="fas fa-plus me-1"></i> Yangi Sotuv (POS)
            </a>
        </div>

        {{-- Statistics Cards --}}
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <small class="text-muted d-block">Bugun sotuvlar</small>
                        <h3 class="fw-bold text-primary mt-2">{{ $stats['today_sales'] ?? 0 }}</h3>
                        <small class="text-success">
                            {{ number_format($stats['today_amount'] ?? 0, 0) }}
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <small class="text-muted d-block">Bu oyning sotuvlar</small>
                        <h3 class="fw-bold text-success mt-2">
                            {{ number_format($stats['month_amount'] ?? 0, 0) }}
                        </h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <small class="text-muted d-block">O'rtacha chek</small>
                        <h3 class="fw-bold text-info mt-2">
                            @php
                                $avgCheck = $stats['today_sales'] > 0 ? $stats['today_amount'] / $stats['today_sales'] : 0;
                            @endphp
                            {{ number_format($avgCheck, 0) }}
                        </h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <small class="text-muted d-block">Jami sotuvlar</small>
                        <h3 class="fw-bold text-warning mt-2">
                            {{ $sales->total() ?? 0 }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('sales.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" placeholder="Sotuv raqami yoki mijoz"
                               value="{{ request('search') }}">
                    </div>

                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">— Barcha —</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>
                                ✓ Tayyor
                            </option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>
                                ✕ Bekor
                            </option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <input type="date" name="date_from" class="form-control"
                               value="{{ request('date_from') }}">
                    </div>

                    <div class="col-md-2">
                        <input type="date" name="date_to" class="form-control"
                               value="{{ request('date_to') }}">
                    </div>

                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i> Qidirish
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Sales Table --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                        <tr>
                            <th class="ps-3">Sotuv Raqami</th>
                            <th>Vaqti</th>
                            <th>Mijoz</th>
                            <th>Mahsulotlar</th>
                            <th>Jami</th>
                            <th>Chegirma</th>
                            <th>Yakuniy</th>
                            <th>To'lov</th>
                            <th>Status</th>
                            <th class="text-end pe-3">Amallar</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($sales as $sale)
                            <tr>
                                <td class="ps-3">
                                    <strong>{{ $sale->sale_number }}</strong>
                                </td>
                                <td>
                                    <small>{{ $sale->created_at->format('d.m.Y H:i') }}</small>
                                </td>
                                <td>
                                    @if($sale->customer_name)
                                        <strong>{{ $sale->customer_name }}</strong>
                                        @if($sale->customer_phone)
                                            <br><small class="text-muted">{{ $sale->customer_phone }}</small>
                                        @endif
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $sale->items->count() }} ta</span>
                                </td>
                                <td>
                                    {{ number_format($sale->total_amount, 0) }}
                                </td>
                                <td>
                                    @if($sale->discount_amount > 0)
                                        <span class="text-danger">
                                            -{{ number_format($sale->discount_amount, 0) }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="fw-bold text-success">
                                    {{ number_format($sale->final_amount, 0) }}
                                </td>
                                <td>
                                    @if($sale->payment_method === 'cash')
                                        Naqd
                                    @elseif($sale->payment_method === 'card')
                                        Karta
                                    @else
                                        Transfer
                                    @endif
                                </td>
                                <td>
                                    @if($sale->status === 'completed')
                                        <span class="badge bg-success">Tayyor</span>
                                    @else
                                        <span class="badge bg-warning">Kutilmoqda</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('sales.show', $sale) }}"
                                       class="btn btn-sm btn-outline-info me-1">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('sales.printReceipt', $sale) }}"
                                       class="btn btn-sm btn-outline-primary me-1"
                                       target="_blank">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    @if($sale->status !== 'completed')
                                        <button class="btn btn-sm btn-outline-danger"
                                                onclick="if(confirm('Sotuvni o\'chirishni tasdiqlaysizmi?')) {
                                                    document.getElementById('deleteForm{{ $sale->id }}').submit();
                                                }">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <form action="{{ route('sales.destroy', $sale) }}"
                                              method="POST"
                                              id="deleteForm{{ $sale->id }}"
                                              style="display: none;">
                                            @csrf @method('DELETE')
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-5">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block opacity-25"></i>
                                    Sotuvlar yo'q
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

@extends('backend.inc.app')
@section('title', 'Zararli Mahsulotlar')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">⚠️ Zararli Mahsulotlar</h4>
            <a href="{{ route('reports.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Orqaba
            </a>
        </div>

        {{-- Warning Alert --}}
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Ixtiyor!</strong> {{ $report['count'] }} ta mahsulot zararlang sotilmoqda.
            Narx ko'tarilishi yoki o'chirilishi kerak.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        {{-- Filters --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('reports.lowProfit') }}" class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Minimal Foyda Chegarasi (%)</label>
                        <input type="number" name="min_profit" class="form-control"
                               value="{{ $minProfit }}" min="0" max="100">
                        <small class="text-muted">Bu qiymatdan pastroq foiza ega mahsulotlar ko'rsatiladi</small>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i> Qidirish
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Table --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">Zararlang Sotilgan Mahsulotlar ({{ $report['count'] }} ta)</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                        <tr>
                            <th class="ps-3">Mahsulot Nomi</th>
                            <th>Kategoriya</th>
                            <th>Sotish Narxi</th>
                            <th>Kelish Narxi</th>
                            <th>Foyda (Dona)</th>
                            <th>Foyda %</th>
                            <th class="text-center">Status</th>
                            <th class="text-end pe-3">Tavsiya</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($report['low_profit_products'] as $item)
                            <tr class="{{ $item['unit_profit'] < 0 ? 'table-danger' : '' }}">
                                <td class="ps-3">
                                    <strong>{{ $item['product']->name }}</strong>
                                    <br>
                                    <small class="text-muted">SKU: {{ $item['product']->sku }}</small>
                                </td>
                                <td>{{ $item['product']->category->name }}</td>
                                <td>{{ number_format($item['product']->sale_price, 0) }}</td>
                                <td>{{ number_format($item['product']->purchase_price, 0) }}</td>
                                <td class="fw-bold {{ $item['unit_profit'] >= 0 ? 'text-warning' : 'text-danger' }}">
                                    {{ number_format($item['unit_profit'], 0) }}
                                </td>
                                <td>
                                <span class="badge bg-{{ $item['unit_profit'] < 0 ? 'danger' : 'warning' }}">
                                    {{ $item['profit_margin'] }}%
                                </span>
                                </td>
                                <td class="text-center">
                                    @if($item['unit_profit'] < 0)
                                        <span class="badge bg-danger">ZARARLANG</span>
                                    @else
                                        <span class="badge bg-warning">PAST FOYDA</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <span class="badge bg-info">{{ $item['recommendation'] }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    ✓ Barcha mahsulotlar foydali!
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Recommendations --}}
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">💡 Tavsiyalar</h5>
            </div>
            <div class="card-body">
                <h6 class="mb-3">Zararli mahsulotlarni hal qilish usullari:</h6>
                <ol class="mb-0">
                    <li class="mb-2">
                        <strong>Narx ko'tarish</strong> - Sotish narxini kelish narxiga yaqin qilib narxini ko'tarish
                    </li>
                    <li class="mb-2">
                        <strong>Chegirma qo'shish</strong> - Kelish narxini pasaytirish uchun supplier bilan munosabat qilish
                    </li>
                    <li class="mb-2">
                        <strong>Kombinatsiya satish</strong> - Zararli mahsulotni foydali mahsulot bilan birgalikda satish
                    </li>
                    <li class="mb-0">
                        <strong>O'chirish</strong> - Agar zararli mahsulotning tayyorlash imkoni bo'lmasa, assorting'dan chiqarish
                    </li>
                </ol>
            </div>
        </div>
    </div>
@endsection

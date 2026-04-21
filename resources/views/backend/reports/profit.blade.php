@extends('backend.inc.app')
@section('title', 'Foyda Tahlili')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">💰 Foyda Tahlili</h4>
            <a href="{{ route('reports.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Orqaba
            </a>
        </div>

        {{-- Filters --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('reports.profit') }}" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Boshlang'ich Sana</label>
                        <input type="date" name="date_from" class="form-control" value="{{ $dateFrom->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tugash Sanasi</label>
                        <input type="date" name="date_to" class="form-control" value="{{ $dateTo->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i> Qidirish
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted">JAMI DAROMAD</h6>
                        <h4 class="fw-bold text-primary">{{ number_format($report['total_revenue'], 0) }}</h4>
                        <small class="text-muted">so'm</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted">JAMI XARIJ</h6>
                        <h4 class="fw-bold text-warning">{{ number_format($report['total_cost'], 0) }}</h4>
                        <small class="text-muted">so'm</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted">JAMI FOYDA</h6>
                        <h4 class="fw-bold text-success">{{ number_format($report['total_profit'], 0) }}</h4>
                        <small>{{ $report['profit_margin'] }}% foiz</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Chart --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h5 class="mb-4">Top 10 Foydali Mahsulotlar</h5>
                <canvas id="profitChart" height="80"></canvas>
            </div>
        </div>

        {{-- Detailed Table --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">Mahsulot Bo'yicha Foyda</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                        <tr>
                            <th class="ps-3">Mahsulot Nomi</th>
                            <th>Miqdor</th>
                            <th>Daromad</th>
                            <th>Xarij</th>
                            <th>Foyda</th>
                            <th>Foiz %</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($sortedProducts as $item)
                            <tr>
                                <td class="ps-3"><strong>{{ $item['product']->name }}</strong></td>
                                <td>{{ $item['quantity'] }}</td>
                                <td>{{ number_format($item['revenue'], 0) }}</td>
                                <td>{{ number_format($item['cost'], 0) }}</td>
                                <td class="fw-bold text-success">{{ number_format($item['profit'], 0) }}</td>
                                <td>
                                <span class="badge bg-success">
                                    {{ round(($item['profit'] / $item['revenue']) * 100, 1) }}%
                                </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">Ma'lumot yo'q</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script>
        const data = {!! json_encode($sortedProducts->map(fn($p) => [
    'name' => substr($p['product']->name, 0, 20),
    'profit' => $p['profit']
])) !!};

        const ctx = document.getElementById('profitChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(d => d.name),
                datasets: [{
                    label: 'Foyda (so\'m)',
                    data: data.map(d => d.profit),
                    backgroundColor: '#43e97b'
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    </script>
@endsection

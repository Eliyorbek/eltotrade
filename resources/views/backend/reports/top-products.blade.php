@extends('backend.inc.app')
@section('title', 'Eng Ko\'p Sotilgan Mahsulotlar')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">🏆 Eng Ko'p Sotilgan Mahsulotlar</h4>
            <a href="{{ route('reports.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Orqaba
            </a>
        </div>

        {{-- Filters --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('reports.topProducts') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Boshlang'ich Sana</label>
                        <input type="date" name="date_from" class="form-control" value="{{ $dateFrom->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tugash Sanasi</label>
                        <input type="date" name="date_to" class="form-control" value="{{ $dateTo->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Chegarasi</label>
                        <select name="limit" class="form-select">
                            <option value="5">Top 5</option>
                            <option value="10" selected>Top 10</option>
                            <option value="20">Top 20</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i> Qidirish
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Chart --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h5 class="mb-4">Sotilgan Miqdori</h5>
                <canvas id="topProductsChart" height="80"></canvas>
            </div>
        </div>

        {{-- Table --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">Mahsulotlar</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                        <tr>
                            <th class="ps-3">Reyting</th>
                            <th>Mahsulot Nomi</th>
                            <th>Kategoriya</th>
                            <th>Sotilgan Miqdor</th>
                            <th>Jami Summa</th>
                            <th>Foyda %</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($report['products'] as $index => $item)
                            <tr>
                                <td class="ps-3">
                                    <h5 class="mb-0">
                                        @if($index === 0)
                                            🥇
                                        @elseif($index === 1)
                                            🥈
                                        @elseif($index === 2)
                                            🥉
                                        @else
                                            {{ $index + 1 }}
                                        @endif
                                    </h5>
                                </td>
                                <td><strong>{{ $item['product']->name }}</strong></td>
                                <td>{{ $item['product']->category->name }}</td>
                                <td>{{ $item['quantity'] }} {{ $item['product']->unit }}</td>
                                <td>{{ number_format($item['amount'], 0) }} so'm</td>
                                <td>
                                <span class="badge bg-{{ $item['profit_percent'] > 30 ? 'success' : 'warning' }}">
                                    {{ $item['profit_percent'] }}%
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
        const data = {!! json_encode($report['products']->map(fn($p) => [
    'name' => substr($p['product']->name, 0, 20),
    'quantity' => $p['quantity']
])) !!};

        const ctx = document.getElementById('topProductsChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(d => d.name),
                datasets: [{
                    label: 'Sotilgan Miqdor',
                    data: data.map(d => d.quantity),
                    backgroundColor: [
                        '#667eea', '#764ba2', '#f093fb', '#f5576c', '#00f2fe',
                        '#43e97b', '#fa709a', '#fee140', '#30b0fe', '#4facfe'
                    ]
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

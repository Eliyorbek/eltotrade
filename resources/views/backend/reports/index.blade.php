@extends('backend.inc.app')
@section('title', 'Hisobotlar')

    <style>
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200px;
            height: 200px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
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
            margin-bottom: 10px;
        }

        .stat-card .detail {
            font-size: 12px;
            opacity: 0.8;
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

        .chart-container {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .chart-container h5 {
            margin-bottom: 20px;
            font-weight: 600;
        }

        .table-wrapper {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .quick-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
            margin-bottom: 20px;
        }

        .quick-link {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            transition: all 0.3s;
            text-decoration: none;
            color: inherit;
        }

        .quick-link:hover {
            border-color: #007bff;
            box-shadow: 0 4px 12px rgba(0,123,255,0.15);
            transform: translateY(-2px);
        }

        .quick-link-icon {
            font-size: 24px;
            margin-bottom: 8px;
        }

        .quick-link-text {
            font-size: 12px;
            font-weight: 500;
        }
    </style>

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">
                    <i class="fas fa-chart-line me-2"></i>
                    Hisobotlar
                </h4>
                <small class="text-muted">Admin uchun ishchi tahlillar</small>
            </div>
{{--            <div>--}}
{{--                <button class="btn btn-outline-primary me-2">--}}
{{--                    <i class="fas fa-download me-1"></i> Excel--}}
{{--                </button>--}}
{{--                <button class="btn btn-outline-danger">--}}
{{--                    <i class="fas fa-file-pdf me-1"></i> PDF--}}
{{--                </button>--}}
{{--            </div>--}}
        </div>

        {{-- Quick Links --}}
        <div class="quick-links">
            <a href="{{ route('reports.daily') }}" class="quick-link">
                <div class="quick-link-icon">📅</div>
                <div class="quick-link-text">Kunlik</div>
            </a>
            <a href="{{ route('reports.monthly') }}" class="quick-link">
                <div class="quick-link-icon">📊</div>
                <div class="quick-link-text">Oylik</div>
            </a>
            <a href="{{ route('reports.topProducts') }}" class="quick-link">
                <div class="quick-link-icon">🏆</div>
                <div class="quick-link-text">Top Mahsulotlar</div>
            </a>
            <a href="{{ route('reports.profit') }}" class="quick-link">
                <div class="quick-link-icon">💰</div>
                <div class="quick-link-text">Foyda</div>
            </a>
            <a href="{{ route('reports.lowProfit') }}" class="quick-link">
                <div class="quick-link-icon">⚠️</div>
                <div class="quick-link-text">Zararli</div>
            </a>
        </div>

        {{-- Statistics Cards --}}
        <div class="row">
            <div class="col-md-6 col-lg-3">
                <div class="stat-card">
                    <h6>BUGUN SOTUVLAR</h6>
                    <div class="amount">{{ $today['total_sales'] }}</div>
                    <div class="detail">{{ number_format($today['final_amount'], 0) }} so'm</div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="stat-card">
                    <h6>OYLIK SOTUVLAR</h6>
                    <div class="amount">{{ $thisMonth['total_sales'] }}</div>
                    <div class="detail">{{ number_format($thisMonth['final_amount'], 0) }} so'm</div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="stat-card">
                    <h6>O'RTACHA CHEK</h6>
                    <div class="amount">
                        @php
                            $avgCheck = $today['total_sales'] > 0
                                ? $today['final_amount'] / $today['total_sales']
                                : 0;
                        @endphp
                        {{ number_format($avgCheck, 0) }}
                    </div>
                    <div class="detail">so'm per chek</div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="stat-card">
                    <h6>OYLIK FOYDA</h6>
                    <div class="amount">{{ number_format($profitReport['total_profit'], 0) }}</div>
                    <div class="detail">{{ $profitReport['profit_margin'] }}% foiz</div>
                </div>
            </div>
        </div>

        {{-- Charts Row --}}
        <div class="row mt-4">
            {{-- 7 Kunlik Trend --}}
            <div class="col-lg-6">
                <div class="chart-container">
                    <h5>Oxirgi 7 Kunlik Trend</h5>
                    <canvas id="dailyChart" height="80"></canvas>
                </div>
            </div>

            {{-- Top Products --}}
            <div class="col-lg-6">
                <div class="chart-container">
                    <h5>Top 5 Mahsulotlar</h5>
                    <canvas id="topProductsChart" height="80"></canvas>
                </div>
            </div>
        </div>

        {{-- Top Products Table --}}
        <div class="table-wrapper">
            <h5 class="mb-3">Eng Ko'p Sotilgan Mahsulotlar (30 kun)</h5>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Mahsulot Nomi</th>
                        <th>Kategoriya</th>
                        <th>Miqdor</th>
                        <th>Summa</th>
                        <th>Foyda %</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($topProducts['products'] as $index => $item)
                        <tr>
                            <td><strong>{{ $index + 1 }}</strong></td>
                            <td>{{ $item['product']->name }}</td>
                            <td>{{ $item['product']->category->name }}</td>
                            <td>{{ $item['quantity'] }} {{ $item['product']->unit }}</td>
                            <td>{{ number_format($item['amount'], 0) }}</td>
                            <td>
                            <span class="badge bg-{{ $item['profit_percent'] > 30 ? 'success' : 'warning' }}">
                                {{ $item['profit_percent'] }}%
                            </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Ma'lumot yo'q</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Low Profit Products --}}
        @if($lowProfitProducts['count'] > 0)
            <div class="table-wrapper">
                <h5 class="mb-3">Zararli Mahsulotlar</h5>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>{{ $lowProfitProducts['count'] }} ta</strong> mahsulot narxi ko'tarilishi kerak!
                </div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                        <tr>
                            <th>Mahsulot</th>
                            <th>Narx (Sotish)</th>
                            <th>Xarij (Kelish)</th>
                            <th>Foyda</th>
                            <th>Foiz</th>
                            <th>Tavsiya</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($lowProfitProducts['low_profit_products'] as $item)
                            <tr>
                                <td>{{ $item['product']->name }}</td>
                                <td>{{ number_format($item['product']->sale_price, 0) }}</td>
                                <td>{{ number_format($item['product']->purchase_price, 0) }}</td>
                                <td class="{{ $item['unit_profit'] > 0 ? 'text-danger' : 'text-danger fw-bold' }}">
                                    {{ number_format($item['unit_profit'], 0) }}
                                </td>
                                <td>{{ $item['profit_margin'] }}%</td>
                                <td>
                                <span class="badge bg-{{ $item['unit_profit'] < 0 ? 'danger' : 'warning' }}">
                                    {{ $item['recommendation'] }}
                                </span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script>
        // Kunlik sotuv trend (7 kun)
        fetch('{{ route("reports.chartData", ["type" => "daily"]) }}')
            .then(r => r.json())
            .then(data => {
                const ctx = document.getElementById('dailyChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.map(d => d.date),
                        datasets: [{
                            label: 'Sotuv Summasi',
                            data: data.map(d => d.amount),
                            borderColor: '#667eea',
                            backgroundColor: 'rgba(102, 126, 234, 0.1)',
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#667eea',
                            pointRadius: 5,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            });

        // Top mahsulotlar
        fetch('{{ route("reports.chartData", ["type" => "top_products"]) }}')
            .then(r => r.json())
            .then(data => {
                const ctx = document.getElementById('topProductsChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.map(d => d.name.substring(0, 15)),
                        datasets: [{
                            label: 'Sotilgan Miqdor',
                            data: data.map(d => d.quantity),
                            backgroundColor: [
                                '#667eea', '#764ba2', '#f093fb', '#f5576c', '#00f2fe'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            });
    </script>
@endsection

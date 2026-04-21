@extends('backend.inc.app')
@section('title', 'Oylik Hisoboti')

    <style>
        .month-selector {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            align-items: center;
        }

        .month-selector select {
            width: auto;
        }

        .daily-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 10px;
            margin-bottom: 20px;
        }

        .daily-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 12px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .daily-card:hover {
            border-color: #007bff;
            box-shadow: 0 4px 12px rgba(0,123,255,0.15);
        }

        .daily-card-date {
            font-weight: 600;
            font-size: 12px;
            margin-bottom: 5px;
        }

        .daily-card-amount {
            font-size: 14px;
            font-weight: bold;
            color: #28a745;
        }

        .daily-card-count {
            font-size: 11px;
            color: #6c757d;
        }
    </style>

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">📊 Oylik Hisoboti</h4>
            <a href="{{ route('reports.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Orqaba
            </a>
        </div>

        {{-- Month Selector --}}
        <div class="month-selector">
            <select id="yearSelect" class="form-select" style="width: 100px;"
                    onchange="changeMonth()">
                @for($y = now()->year - 2; $y <= now()->year; $y++)
                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>

            <select id="monthSelect" class="form-select" style="width: 150px;"
                    onchange="changeMonth()">
                @foreach(['01' => 'Yanvar', '02' => 'Fevral', '03' => 'Mart', '04' => 'Aprel',
                          '05' => 'May', '06' => 'Iyun', '07' => 'Iyul', '08' => 'Avgust',
                          '09' => 'Sentabr', '10' => 'Oktabr', '11' => 'Noyabr', '12' => 'Dekabr'] as $m => $name)
                    <option value="{{ $m }}" {{ $m == sprintf('%02d', $month) ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Summary Stats --}}
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted">OYLIK SOTUVLAR</h6>
                        <h4 class="fw-bold">{{ $report['total_sales'] }}</h4>
                        <small>ta sotuv</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted">JAMI SUMMA</h6>
                        <h4 class="fw-bold">{{ number_format($report['final_amount'], 0) }}</h4>
                        <small>so'm</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted">O'RTACHA KUN</h6>
                        <h4 class="fw-bold">{{ number_format($report['average_per_day'], 0) }}</h4>
                        <small>ta sotuv/kun</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted">CHEGIRMA</h6>
                        <h4 class="fw-bold text-danger">{{ number_format($report['total_discount'], 0) }}</h4>
                        <small>so'm</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Trend Chart --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h5 class="mb-4">📈 Kunlik Trend</h5>
                <canvas id="trendChart" height="80"></canvas>
            </div>
        </div>

        {{-- Daily Breakdown --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">📅 Kunlik Tafsilotlar</h5>
            </div>
            <div class="card-body">
                <div class="daily-cards">
                    @foreach($report['days'] as $date => $dayData)
                        <div class="daily-card" onclick="location.href='{{ route('reports.daily') }}?date={{ $date }}'">
                            <div class="daily-card-date">{{ \Carbon\Carbon::parse($date)->format('d M') }}</div>
                            <div class="daily-card-amount">{{ number_format($dayData['final_amount'], 0) }}</div>
                            <div class="daily-card-count">{{ $dayData['total_sales'] }} ta</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script>
        function changeMonth() {
            const year = document.getElementById('yearSelect').value;
            const month = document.getElementById('monthSelect').value;
            window.location.href = `{{ route('reports.monthly') }}?year=${year}&month=${month}`;
        }

        const chartData = {!! json_encode($chartData) !!};
        const ctx = document.getElementById('trendChart').getContext('2d');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.map(d => d.date),
                datasets: [{
                    label: 'Sotuv Summasi',
                    data: chartData.map(d => d.amount),
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#667eea',
                    pointRadius: 5,
                    pointHoverRadius: 7,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
@endsection

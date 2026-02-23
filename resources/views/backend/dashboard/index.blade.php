@extends('backend.inc.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid">

        {{-- Page Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">📊 Dashboard</h4>
            <span class="text-muted">{{ now()->format('d.m.Y') }}</span>
        </div>

        {{-- Stat Cards --}}
        <div class="row g-3 mb-4">

            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm text-white bg-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="mb-1 small">Mijozlar</p>
                                <h4 class="fw-bold">{{ $total_customers }}</h4>
                            </div>
                            <i class="fas fa-users fa-2x opacity-75"></i>
                        </div>
                        <a href="{{ route('backend.customers.index') }}" class="text-white small">Ko'rish →</a>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm text-white bg-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="mb-1 small">Buyurtmalar</p>
                                <h4 class="fw-bold">{{ $total_orders }}</h4>
                            </div>
                            <i class="fas fa-shopping-cart fa-2x opacity-75"></i>
                        </div>
                        <a href="{{ route('backend.orders.index') }}" class="text-white small">Ko'rish →</a>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm text-white bg-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="mb-1 small">Mahsulotlar</p>
                                <h4 class="fw-bold">{{ $total_products }}</h4>
                            </div>
                            <i class="fas fa-box fa-2x opacity-75"></i>
                        </div>
                        <a href="{{ route('backend.products.index') }}" class="text-white small">Ko'rish →</a>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm text-white bg-info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="mb-1 small">Xodimlar</p>
                                <h4 class="fw-bold">{{ $total_employees }}</h4>
                            </div>
                            <i class="fas fa-user-tie fa-2x opacity-75"></i>
                        </div>
                        <a href="{{ route('backend.employees.index') }}" class="text-white small">Ko'rish →</a>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-8 col-sm-12">
                <div class="card border-0 shadow-sm text-white bg-danger">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="mb-1 small">Umumiy Daromad</p>
                                <h4 class="fw-bold">{{ number_format($total_revenue, 2) }} UZS</h4>
                            </div>
                            <i class="fas fa-money-bill-wave fa-2x opacity-75"></i>
                        </div>
                        <a href="{{ route('backend.transactions.index') }}" class="text-white small">Ko'rish →</a>
                    </div>
                </div>
            </div>

        </div>

        {{-- Recent Orders & Customers --}}
        <div class="row g-3">

            {{-- Recent Orders --}}
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white fw-semibold">
                        🛒 So'nggi Buyurtmalar
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Mijoz</th>
                                <th>Summa</th>
                                <th>Status</th>
                                <th>Sana</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($recent_orders as $order)
                                <tr>
                                    <td>{{ $order->id }}</td>
                                    <td>{{ $order->customer->name ?? '—' }}</td>
                                    <td>{{ number_format($order->total_amount, 2) }}</td>
                                    <td>
                                    <span class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'pending' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                    </td>
                                    <td>{{ $order->created_at->format('d.m.Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">Buyurtma yo'q</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Recent Customers --}}
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white fw-semibold">
                        👥 So'nggi Mijozlar
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                            <tr>
                                <th>Ism</th>
                                <th>Telefon</th>
                                <th>Sana</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($recent_customers as $customer)
                                <tr>
                                    <td>{{ $customer->name }}</td>
                                    <td>{{ $customer->phone ?? '—' }}</td>
                                    <td>{{ $customer->created_at->format('d.m.Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3">Mijoz yo'q</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection

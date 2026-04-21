@extends('backend.inc.app')
@section('title', 'Ta\'minotchilar')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">
                    <i class="fas fa-users me-2"></i>
                    Ta'minotchilar
                </h4>
                <small class="text-muted">Mahsulot yetkazib beruvchilar</small>
            </div>
            <a href="{{ route('warehouse.supplierCreate') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-1"></i> Yangi Ta'minotchi
            </a>
        </div>

        {{-- Suppliers Table --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                        <tr>
                            <th class="ps-3">Nomi</th>
                            <th>Kontakt Odam</th>
                            <th>Telefon</th>
                            <th>Email</th>
                            <th>Shahar</th>
                            <th>To'lov Shartlari</th>
                            <th>Yetkazib Berish</th>
                            <th>Status</th>
                            <th class="text-end pe-3">Amallar</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($suppliers as $supplier)
                            <tr>
                                <td class="ps-3">
                                    <strong>{{ $supplier->name }}</strong><br>
                                    <small class="text-muted">TIN: {{ $supplier->tin ?? '—' }}</small>
                                </td>
                                <td>{{ $supplier->contact_person ?? '—' }}</td>
                                <td>
                                    @if($supplier->phone)
                                        <a href="tel:{{ $supplier->phone }}">{{ $supplier->phone }}</a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    @if($supplier->email)
                                        <a href="mailto:{{ $supplier->email }}">{{ $supplier->email }}</a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>{{ $supplier->city ?? '—' }}</td>
                                <td>{{ $supplier->payment_terms ?? '—' }}</td>
                                <td>
                                    @if($supplier->delivery_time)
                                        {{ $supplier->delivery_time }} kun
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                <span class="badge bg-{{ $supplier->status === 'active' ? 'success' : 'danger' }}">
                                    {{ $supplier->status_label }}
                                </span>
                                </td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('warehouse.supplierEdit', $supplier) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">
                                    <i class="fas fa-users fa-3x mb-3"></i><br>
                                    Ta'minotchilar yo'q
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($suppliers->hasPages())
                <div class="card-footer bg-white">
                    {{ $suppliers->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

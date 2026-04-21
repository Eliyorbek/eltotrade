@extends('backend.inc.app')
@section('title', 'Mahsulot Chiqim Tarixlari')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">
                    <i class="fas fa-history me-2"></i>
                    Mahsulot Chiqim Tarixlari
                </h4>
                <small class="text-muted">Barcha chiqim raqordlari</small>
            </div>
            <a href="{{ route('warehouse.stockOutCreate') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-1"></i> Yangi Chiqim
            </a>
        </div>

        {{-- Filters --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('warehouse.stockOutHistory') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Sabab</label>
                        <select name="reason" class="form-select form-select-sm">
                            <option value="">-- Hammasini ko'rsat --</option>
                            <option value="damage" {{ request('reason') == 'damage' ? 'selected' : '' }}>❌ Shikastlanish</option>
                            <option value="expiry" {{ request('reason') == 'expiry' ? 'selected' : '' }}>⏰ Muddati O'tgani</option>
                            <option value="return" {{ request('reason') == 'return' ? 'selected' : '' }}>↩️ Qaytarish</option>
                            <option value="adjustment" {{ request('reason') == 'adjustment' ? 'selected' : '' }}>⚖️ Tekshiruv</option>
                            <option value="loss" {{ request('reason') == 'loss' ? 'selected' : '' }}>🚨 Yo'qolish</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Boshlang'ich Sana</label>
                        <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Tugash Sanasi</label>
                        <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                    </div>

                    <div class="col-md-3 d-flex gap-2 align-items-end">
                        <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                            <i class="fas fa-search me-1"></i> Qidirish
                        </button>
                        @if(request()->filled('reason') || request()->filled('date_from') || request()->filled('date_to'))
                            <a href="{{ route('warehouse.stockOutHistory') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-redo"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- Chiqimlar Table --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">📋 Chiqim Ro'yxati</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                        <tr>
                            <th class="ps-3">Chiqim Raqami</th>
                            <th>Sababu</th>
                            <th>Sanasi</th>
                            <th>Mahsulotlar</th>
                            <th>Jami Xarajat</th>
                            <th>Status</th>
                            <th>Qayd qilgan</th>
                            <th class="text-end pe-3">Amallar</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($stockOuts as $out)
                            <tr>
                                <td class="ps-3">
                                    <strong>{{ $out->reference_number }}</strong>
                                </td>
                                <td>
                                <span class="badge bg-{{
                                    $out->reason === 'damage' ? 'danger' :
                                    ($out->reason === 'expiry' ? 'warning' : 'info')
                                }}">
                                    {{ $out->reason_label }}
                                </span>
                                </td>
                                <td>{{ $out->issued_date->format('d.m.Y') }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $out->items->count() }} ta</span>
                                </td>
                                <td class="fw-bold text-danger">
                                    -{{ number_format($out->total_amount, 0) }} so'm
                                </td>
                                <td>
                                    <span class="badge bg-success">{{ $out->status_label }}</span>
                                </td>
                                <td>
                                    <small>{{ $out->user->name }}</small><br>
                                    <small class="text-muted">{{ $out->created_at->format('d.m.Y H:i') }}</small>
                                </td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('warehouse.stockOutShow', $out) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <i class="fas fa-sign-out-alt fa-3x mb-3"></i><br>
                                    Chiqim tarixlari yo'q
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($stockOuts->hasPages())
                <div class="card-footer bg-white">
                    {{ $stockOuts->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

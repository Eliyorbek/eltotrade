@extends('backend.inc.app')
@section('title', 'Mahsulotlar')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">Mahsulotlar</h4>
                <small class="text-muted">Jami: {{ $products->total() }} ta mahsulot</small>
            </div>
            @can('products.create')
                <a href="{{ route('products.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Yangi Mahsulot
                </a>
            @endcan
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-times-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th class="ps-3">#</th>
                        <th>Mahsulot</th>
                        <th>SKU / Barcode</th>
                        <th>Kategoriya</th>
                        <th>Narx</th>
                        <th>Ombor</th>
                        <th>Status</th>
                        @canany(['products.edit', 'products.delete'])
                            <th class="text-end pe-3">Amallar</th>
                        @endcanany
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td class="ps-3">{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($product->image)
                                        <img src="{{ Storage::url($product->image) }}"
                                             class="rounded" width="40" height="40"
                                             style="object-fit:cover">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                             style="width:40px;height:40px">
                                            <i class="fas fa-box text-muted"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-semibold">{{ $product->name }}</div>
                                        <small class="text-muted">{{ $product->profit_margin }}% foyda</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <small class="d-block text-muted">SKU: <code>{{ $product->sku }}</code></small>
                                <small class="d-block text-muted">Barcode: <code>{{ $product->barcode }}</code></small>
                            </td>
                            <td>{{ $product->category->name }}</td>
                            <td>
                                <small class="d-block text-muted">Kelish: {{ number_format($product->purchase_price, 0) }}</small>
                                <small class="d-block fw-semibold text-success">Sotish: {{ number_format($product->sale_price, 0) }}</small>
                            </td>
                            <td>
                                @if($product->isLowStock())
                                    <span class="badge bg-danger">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    {{ $product->stock }} {{ $product->unit }}
                                </span>
                                @else
                                    <span class="badge bg-success">
                                    {{ $product->stock }} {{ $product->unit }}
                                </span>
                                @endif
                            </td>
                            <td>
                            <span class="badge bg-{{ $product->status === 'active' ? 'success' : 'secondary' }}">
                                {{ $product->status === 'active' ? 'Faol' : 'Nofaol' }}
                            </span>
                            </td>
                            @canany(['products.edit', 'products.delete'])
                                <td class="text-end pe-3">
                                    <a href="{{ route('products.show', $product) }}"
                                       class="btn btn-sm btn-outline-info me-1">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @can('products.edit')
                                        <a href="{{ route('products.edit', $product) }}"
                                           class="btn btn-sm btn-outline-primary me-1">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endcan
                                    @can('products.delete')
                                        <form action="{{ route('products.destroy', $product) }}"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('{{ $product->name }} ni o\'chirishni tasdiqlaysizmi?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            @endcanany
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="fas fa-box-open fa-2x mb-2 d-block opacity-25"></i>
                                Mahsulotlar yo'q
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            @if($products->hasPages())
                <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        {{ $products->firstItem() }}–{{ $products->lastItem() }} / {{ $products->total() }}
                    </small>
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

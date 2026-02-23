@extends('backend.inc.app')
@section('title', $product->name)

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">📦 {{ $product->name }}</h4>
                <small class="text-muted">{{ $product->category->name }}</small>
            </div>
            <div class="d-flex gap-2">
                @can('products.edit')
                    <a href="{{ route('products.edit', $product) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-1"></i> Tahrirlash
                    </a>
                @endcan
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Orqaga
                </a>
            </div>
        </div>

        <div class="row g-3">

            {{-- Chap panel --}}
            <div class="col-lg-8">

                {{-- Asosiy ma'lumotlar --}}
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white fw-semibold">
                        <i class="fas fa-info-circle me-2"></i>Asosiy Ma'lumotlar
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <small class="text-muted d-block">Nomi</small>
                                <span class="fw-semibold">{{ $product->name }}</span>
                            </div>

                            <div class="col-md-6">
                                <small class="text-muted d-block">Kategoriya</small>
                                <span class="badge bg-primary">{{ $product->category->name }}</span>
                            </div>

                            <div class="col-md-6">
                                <small class="text-muted d-block">Slug</small>
                                <code>{{ $product->slug }}</code>
                            </div>

                            <div class="col-md-6">
                                <small class="text-muted d-block">Status</small>
                                <span class="badge bg-{{ $product->status === 'active' ? 'success' : 'secondary' }}">
                                {{ $product->status === 'active' ? 'Faol' : 'Nofaol' }}
                            </span>
                            </div>

                            @if($product->description)
                                <div class="col-12">
                                    <small class="text-muted d-block">Tavsif</small>
                                    <p class="mb-0">{{ $product->description }}</p>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>

                {{-- Narxlar --}}
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white fw-semibold">
                        <i class="fas fa-tag me-2"></i>Narxlar
                    </div>
                    <div class="card-body">
                        <div class="row g-3 text-center">

                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded">
                                    <small class="text-muted d-block mb-1">Kelish Narxi</small>
                                    <h5 class="fw-bold text-dark mb-0">
                                        {{ number_format($product->purchase_price, 0) }}
                                        <small class="text-muted fs-6">UZS</small>
                                    </h5>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="p-3 bg-success bg-opacity-10 rounded">
                                    <small class="text-muted d-block mb-1">Sotish Narxi</small>
                                    <h5 class="fw-bold text-white mb-0">
                                        {{ number_format($product->sale_price, 0) }}
                                        <small class="text-muted fs-6">UZS</small>
                                    </h5>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="p-3 bg-info bg-opacity-10 rounded">
                                    <small class="text-muted d-block mb-1">Ulgurji Narxi</small>
                                    <h5 class="fw-bold text-white mb-0">
                                        {{ number_format($product->wholesale_price, 0) }}
                                        <small class="text-muted fs-6">UZS</small>
                                    </h5>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="alert alert-{{ $product->profit_margin >= 0 ? 'success' : 'danger' }} py-2 mb-0">
                                    <i class="fas fa-chart-line me-1"></i>
                                    Foyda: <strong>{{ number_format($product->sale_price - $product->purchase_price, 0) }}</strong> UZS
                                    (<strong>{{ $product->profit_margin }}%</strong>)
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Ombor --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white fw-semibold">
                        <i class="fas fa-warehouse me-2"></i>Ombor Holati
                    </div>
                    <div class="card-body">
                        <div class="row g-3 text-center">

                            <div class="col-md-4">
                                <div class="p-3 {{ $product->isLowStock() ? 'bg-danger' : 'bg-success' }} bg-opacity-10 rounded">
                                    <small class="text-muted d-block mb-1">Joriy Qoldiq</small>
                                    <h4 class="fw-bold {{ $product->isLowStock() ? 'text-white' : 'text-white' }} mb-0">
                                        {{ $product->stock }}
                                        <small class="fs-6">{{ $product->unit }}</small>
                                    </h4>
                                    @if($product->isLowStock())
                                        <small class="text-danger">
                                            <i class="fas fa-exclamation-triangle"></i> Kam qoldi!
                                        </small>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="p-3 bg-warning bg-opacity-10 rounded">
                                    <small class="text-muted d-block mb-1">Minimum Qoldiq</small>
                                    <h4 class="fw-bold text-white mb-0">
                                        {{ $product->min_stock }}
                                        <small class="fs-6">{{ $product->unit }}</small>
                                    </h4>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded">
                                    <small class="text-muted d-block mb-1">Ombor Qiymati</small>
                                    <h4 class="fw-bold text-dark mb-0">
                                        {{ number_format($product->stock * $product->purchase_price, 0) }}
                                        <small class="fs-6 text-muted">UZS</small>
                                    </h4>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            {{-- O'ng panel --}}
            <div class="col-lg-4">

                {{-- Rasm --}}
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body text-center p-3">
                        @if($product->image)
                            <img src="{{ Storage::url($product->image) }}"
                                 class="rounded w-100" style="max-height:200px;object-fit:cover">
                        @else
                            <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                 style="height:200px">
                                <div class="text-center text-muted">
                                    <i class="fas fa-image fa-3x mb-2 d-block opacity-25"></i>
                                    Rasm yo'q
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Barcode --}}
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white fw-semibold">
                        <i class="fas fa-barcode me-2"></i>Barcode & SKU
                    </div>
                    <div class="card-body">

                        <div class="mb-3">
                            <small class="text-muted d-block">SKU</small>
                            <code class="fs-6">{{ $product->sku }}</code>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block">Barcode</small>
                            <code class="fs-6">{{ $product->barcode }}</code>
                        </div>

                        @if($product->barcode)
                            <div class="text-center p-2 bg-light rounded">
                                <svg id="barcode_svg"></svg>
                            </div>
                            <button onclick="printBarcode()" class="btn btn-outline-secondary btn-sm w-100 mt-2">
                                <i class="fas fa-print me-1"></i> Barcodni Chop Etish
                            </button>
                        @endif

                    </div>
                </div>

                {{-- Meta --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <small class="text-muted d-block">Qo'shilgan sana</small>
                        <span>{{ $product->created_at->format('d.m.Y H:i') }}</span>
                        <small class="text-muted d-block mt-2">Oxirgi yangilanish</small>
                        <span>{{ $product->updated_at->format('d.m.Y H:i') }}</span>
                    </div>
                </div>

            </div>
        </div>
    </div>

        <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
        <script>
            @if($product->barcode)
            JsBarcode('#barcode_svg', '{{ $product->barcode }}', {
                format: 'EAN13',
                width: 2,
                height: 70,
                displayValue: true,
                fontSize: 14,
            });
            @endif

            function printBarcode() {
                const svg = document.getElementById('barcode_svg').outerHTML;
                const win = window.open('', '_blank');
                win.document.write(`
            <html><body style="text-align:center;padding:20px">
                <h4>{{ $product->name }}</h4>
                ${svg}
                <p>{{ $product->barcode }}</p>
                <script>window.print();window.close();<\/script>
            </body></html>
        `);
                win.document.close();
            }
        </script>
@endsection

@extends('backend.inc.app')
@section('title', 'Mahsulotni Tahrirlash')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">Mahsulotni Tahrirlash</h4>
                <small class="text-muted">{{ $product->name }}</small>
            </div>
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Orqaga
            </a>
        </div>

        <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="row g-3">

                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-white fw-semibold">
                            <i class="fas fa-info-circle me-2"></i>Asosiy Ma'lumotlar
                        </div>
                        <div class="card-body">
                            <div class="row g-3">

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nomi <span class="text-danger">*</span></label>
                                    <input type="text" name="name"
                                           class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name', $product->name) }}">
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Kategoriya <span class="text-danger">*</span></label>
                                    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Tavsif</label>
                                    <textarea name="description" class="form-control" rows="3">{{ old('description', $product->description) }}</textarea>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-white fw-semibold">
                            <i class="fas fa-tag me-2"></i>Narxlar
                        </div>
                        <div class="card-body">
                            <div class="row g-3">

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Kelish Narxi <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" name="purchase_price" id="purchase_price"
                                               class="form-control"
                                               value="{{ old('purchase_price', $product->purchase_price) }}"
                                               min="0" step="0.01" oninput="calcMargin()">
                                        <span class="input-group-text">UZS</span>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Sotish Narxi <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" name="sale_price" id="sale_price"
                                               class="form-control"
                                               value="{{ old('sale_price', $product->sale_price) }}"
                                               min="0" step="0.01" oninput="calcMargin()">
                                        <span class="input-group-text">UZS</span>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Ulgurji Narxi</label>
                                    <div class="input-group">
                                        <input type="number" name="wholesale_price"
                                               class="form-control"
                                               value="{{ old('wholesale_price', $product->wholesale_price) }}"
                                               min="0" step="0.01">
                                        <span class="input-group-text">UZS</span>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="alert alert-success py-2 mb-0" id="margin_info">
                                        <i class="fas fa-chart-line me-1"></i>
                                        Foyda: <strong id="margin_amount">{{ number_format($product->sale_price - $product->purchase_price, 0) }}</strong> UZS
                                        (<strong id="margin_percent">{{ $product->profit_margin }}</strong>%)
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white fw-semibold">
                            <i class="fas fa-warehouse me-2"></i>Ombor
                        </div>
                        <div class="card-body">
                            <div class="row g-3">

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Qoldiq <span class="text-danger">*</span></label>
                                    <input type="number" name="stock" class="form-control"
                                           value="{{ old('stock', $product->stock) }}" min="0">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Minimum Qoldiq</label>
                                    <input type="number" name="min_stock" class="form-control"
                                           value="{{ old('min_stock', $product->min_stock) }}" min="0">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">O'lchov Birligi <span class="text-danger">*</span></label>
                                    <select name="unit" class="form-select">
                                        @foreach($units as $unit)
                                            <option value="{{ $unit }}"
                                                {{ old('unit', $product->unit) == $unit ? 'selected' : '' }}>
                                                {{ $unit }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">

                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-white fw-semibold">
                            <i class="fas fa-barcode me-2"></i>Barcode & SKU
                        </div>
                        <div class="card-body">

                            <div class="mb-3">
                                <label class="form-label fw-semibold">SKU</label>
                                <input type="text" name="sku" class="form-control"
                                       value="{{ old('sku', $product->sku) }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Barcode</label>
                                <input type="text" name="barcode" id="barcode_input"
                                       class="form-control"
                                       value="{{ old('barcode', $product->barcode) }}">
                            </div>

                            @if($product->barcode)
                                <div class="text-center p-2 bg-light rounded">
                                    <svg id="barcode_svg"></svg>
                                </div>
                            @endif

                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-white fw-semibold">
                            <i class="fas fa-image me-2"></i>Rasm
                        </div>
                        <div class="card-body">
                            @if($product->image)
                                <img src="{{ Storage::url($product->image) }}"
                                     class="rounded mb-2 w-100" style="max-height:150px;object-fit:cover">
                            @endif
                            <input type="file" name="image" class="form-control"
                                   accept="image/*" onchange="previewImage(this)">
                            <img id="image_preview" src="#" class="rounded mt-2 w-100"
                                 style="max-height:150px;object-fit:cover;display:none">
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white fw-semibold">
                            <i class="fas fa-toggle-on me-2"></i>Status
                        </div>
                        <div class="card-body">
                            <select name="status" class="form-select">
                                <option value="active" {{ $product->status === 'active' ? 'selected' : '' }}>✅ Faol</option>
                                <option value="inactive" {{ $product->status === 'inactive' ? 'selected' : '' }}>❌ Nofaol</option>
                            </select>
                        </div>
                    </div>

                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="fas fa-save me-1"></i> Yangilash
                    </button>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary ms-2">
                        Bekor qilish
                    </a>
                </div>

            </div>
        </form>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
        <script>
            // Edit sahifada barcode render
            @if($product->barcode)
            JsBarcode('#barcode_svg', '{{ $product->barcode }}', {
                format: 'EAN13',
                width: 2,
                height: 60,
                displayValue: true,
                fontSize: 12,
            });
            @endif

            function calcMargin() {
                const purchase = parseFloat(document.getElementById('purchase_price').value) || 0;
                const sale = parseFloat(document.getElementById('sale_price').value) || 0;
                const amount = sale - purchase;
                const percent = purchase > 0 ? ((amount / purchase) * 100).toFixed(2) : 0;
                document.getElementById('margin_amount').textContent = amount.toLocaleString();
                document.getElementById('margin_percent').textContent = percent;
                document.getElementById('margin_info').className =
                    'alert py-2 mb-0 ' + (amount >= 0 ? 'alert-success' : 'alert-danger');
            }

            function previewImage(input) {
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = e => {
                        const img = document.getElementById('image_preview');
                        img.src = e.target.result;
                        img.style.display = 'block';
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            }
        </script>
    @endpush
@endsection

@extends('backend.inc.app')
@section('title', 'Yangi Mahsulot')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">Yangi Mahsulot</h4>
                <small class="text-muted">Yangi mahsulot qo'shish</small>
            </div>
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Orqaga
            </a>
        </div>

        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">

                {{-- Asosiy ma'lumotlar --}}
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
                                           value="{{ old('name') }}" placeholder="Mahsulot nomi">
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Kategoriya <span class="text-danger">*</span></label>
                                    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                                        <option value="">— Tanlang —</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Tavsif</label>
                                    <textarea name="description" class="form-control" rows="3"
                                              placeholder="Mahsulot haqida...">{{ old('description') }}</textarea>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- Narxlar --}}
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
                                               class="form-control @error('purchase_price') is-invalid @enderror"
                                               value="{{ old('purchase_price', 0) }}" min="0" step="0.01"
                                               oninput="calcMargin()">
                                        <span class="input-group-text">UZS</span>
                                    </div>
                                    @error('purchase_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Sotish Narxi <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" name="sale_price" id="sale_price"
                                               class="form-control @error('sale_price') is-invalid @enderror"
                                               value="{{ old('sale_price', 0) }}" min="0" step="0.01"
                                               oninput="calcMargin()">
                                        <span class="input-group-text">UZS</span>
                                    </div>
                                    @error('sale_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Ulgurji Narxi</label>
                                    <div class="input-group">
                                        <input type="number" name="wholesale_price"
                                               class="form-control"
                                               value="{{ old('wholesale_price', 0) }}" min="0" step="0.01">
                                        <span class="input-group-text">UZS</span>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="alert alert-info py-2 mb-0" id="margin_info">
                                        <i class="fas fa-chart-line me-1"></i>
                                        Foyda: <strong id="margin_amount">0</strong> UZS
                                        (<strong id="margin_percent">0</strong>%)
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- Ombor --}}
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white fw-semibold">
                            <i class="fas fa-warehouse me-2"></i>Ombor
                        </div>
                        <div class="card-body">
                            <div class="row g-3">

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Boshlang'ich Qoldiq <span class="text-danger">*</span></label>
                                    <input type="number" name="stock"
                                           class="form-control @error('stock') is-invalid @enderror"
                                           value="{{ old('stock', 0) }}" min="0">
                                    @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Minimum Qoldiq</label>
                                    <input type="number" name="min_stock" class="form-control"
                                           value="{{ old('min_stock', 5) }}" min="0">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">O'lchov Birligi <span class="text-danger">*</span></label>
                                    <select name="unit" class="form-select @error('unit') is-invalid @enderror">
                                        @foreach($units as $unit)
                                            <option value="{{ $unit }}"
                                                {{ old('unit', 'dona') == $unit ? 'selected' : '' }}>
                                                {{ $unit }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                {{-- O'ng panel --}}
                <div class="col-lg-4">

                    {{-- Barcode / SKU --}}
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-white fw-semibold">
                            <i class="fas fa-barcode me-2"></i>Barcode & SKU
                        </div>
                        <div class="card-body">

                            <div class="mb-3">
                                <label class="form-label fw-semibold">SKU</label>
                                <div class="input-group">
                                    <input type="text" name="sku" id="sku_input"
                                           class="form-control @error('sku') is-invalid @enderror"
                                           value="{{ old('sku') }}" placeholder="Avtomatik">
                                    <button type="button" class="btn btn-outline-secondary"
                                            onclick="generateSku()">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                                @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Barcode</label>
                                <div class="input-group">
                                    <input type="text" name="barcode" id="barcode_input"
                                           class="form-control @error('barcode') is-invalid @enderror"
                                           value="{{ old('barcode') }}"
                                           placeholder="Skanerlang yoki kiriting">
                                    <button type="button" class="btn btn-outline-secondary"
                                            onclick="document.getElementById('barcode_input').focus()">
                                        <i class="fas fa-barcode"></i>
                                    </button>
                                </div>
                                @error('barcode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <small class="text-muted">Bo'sh qoldirsangiz avtomatik yaratiladi</small>
                            </div>

                            {{-- Barcode preview --}}
                            <div id="barcode_preview" class="text-center p-2 bg-light rounded" style="display:none">
                                <svg id="barcode_svg"></svg>
                            </div>

                        </div>
                    </div>

                    {{-- Rasm --}}
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-white fw-semibold">
                            <i class="fas fa-image me-2"></i>Rasm
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-2">
                                <img id="image_preview" src="#" alt="Preview"
                                     class="rounded mb-2" style="max-width:100%;max-height:150px;display:none">
                            </div>
                            <input type="file" name="image" class="form-control"
                                   accept="image/*" onchange="previewImage(this)">
                            <small class="text-muted">JPG, PNG, WEBP. Max 2MB</small>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white fw-semibold">
                            <i class="fas fa-toggle-on me-2"></i>Status
                        </div>
                        <div class="card-body">
                            <select name="status" class="form-select">
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>
                                    ✅ Faol
                                </option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                    ❌ Nofaol
                                </option>
                            </select>
                        </div>
                    </div>

                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> Saqlash
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
            // Foyda hisoblash
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

            // Barcode preview
            document.getElementById('barcode_input').addEventListener('input', function() {
                const val = this.value.trim();
                if (val.length >= 8) {
                    document.getElementById('barcode_preview').style.display = 'block';
                    JsBarcode('#barcode_svg', val, {
                        format: 'EAN13',
                        width: 2,
                        height: 60,
                        displayValue: true,
                        fontSize: 12,
                    });
                } else {
                    document.getElementById('barcode_preview').style.display = 'none';
                }
            });

            // Rasm preview
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

            // SKU generate
            function generateSku() {
                const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                let sku = 'PRD-';
                for (let i = 0; i < 6; i++) {
                    sku += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                document.getElementById('sku_input').value = sku;
            }
        </script>
    @endpush
@endsection

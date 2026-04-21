@extends('backend.inc.app')
@section('title', 'Yangi Mahsulot Kirim')
    <style>
        .stock-in-container {
            max-width: 1200px;
        }

        .form-section {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .form-section h5 {
            font-weight: 600;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }

        .item-row {
            background: #f9f9f9;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid #667eea;
        }

        .item-row.empty {
            background: #fff3cd;
            border-left-color: #ffc107;
        }

        .remove-item-btn {
            cursor: pointer;
            color: #dc3545;
            font-weight: bold;
        }

        .add-item-btn {
            background: #667eea;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 10px;
        }

        .add-item-btn:hover {
            background: #5568d3;
        }

        .total-amount {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
        }

        .required {
            color: #dc3545;
        }
    </style>
@section('content')
    <div class="container-fluid stock-in-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">
                    <i class="fas fa-plus-circle me-2"></i>
                    Yangi Mahsulot Kirim
                </h4>
                <small class="text-muted">Omborda yangi mahsulot qabul qilish</small>
            </div>
            <a href="{{ route('warehouse.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Orqaba
            </a>
        </div>

        <form action="{{ route('warehouse.stockInStore') }}" method="POST" id="stockInForm">
            @csrf

            {{-- Ta'minotchi va Sana --}}
            <div class="form-section">
                <h5>📋 Kirim Ma'lumotlari</h5>

                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Ta'minotchi <span class="required">*</span></label>
                        <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
                            <option value="">-- Ta'minotchini tanlang --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Kirim Sanasi <span class="required">*</span></label>
                        <input type="date" name="received_date" class="form-control @error('received_date') is-invalid @enderror"
                               value="{{ old('received_date', date('Y-m-d')) }}" required>
                        @error('received_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Mahsulotlar --}}
            <div class="form-section">
                <h5>🛍️ Kirim Mahsulotlari</h5>

                <div id="itemsContainer">
                    {{-- Items dinamik qo'shilib chiqadi --}}
                </div>

                <button type="button" class="add-item-btn" onclick="addItem()">
                    <i class="fas fa-plus me-1"></i> Mahsulot Qo'shish
                </button>
            </div>

            {{-- Jami Summa --}}
            <div class="form-section">
                <div class="row">
                    <div class="col-md-6"></div>
                    <div class="col-md-6">
                        <div class="card border-0" style="background: #f0f0f0;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>JAMI SUMMA:</span>
                                    <span class="total-amount" id="totalAmount">0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Izohlar --}}
            <div class="form-section">
                <h5>📝 Qo'shimcha Ma'lumotlar</h5>

                <label class="form-label">Izohlar</label>
                <textarea name="notes" class="form-control" rows="3"
                          placeholder="Kirim haqida qo'shimcha ma'lumot...">{{ old('notes') }}</textarea>
            </div>

            {{-- Tugmalar --}}
            <div class="form-section">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fas fa-save me-1"></i> Kirimni Saqlash
                </button>
                <a href="{{ route('warehouse.index') }}" class="btn btn-outline-secondary btn-lg">
                    <i class="fas fa-times me-1"></i> Bekor Qilish
                </a>
            </div>
        </form>
    </div>

    <script>
        let itemCount = 0;
        const products = {!! json_encode($products->pluck('name', 'id')) !!};

        function addItem() {
            const container = document.getElementById('itemsContainer');
            const itemHtml = `
        <div class="item-row" id="item-${itemCount}">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Mahsulot <span class="text-danger">*</span></label>
                    <select name="items[${itemCount}][product_id]" class="form-select" required onchange="updateTotal()">
                        <option value="">-- Mahsulotni tanlang --</option>
                        @foreach($products as $product)
            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label">Miqdor <span class="text-danger">*</span></label>
            <input type="number" name="items[${itemCount}][quantity]" class="form-control"
                           placeholder="0" min="1" required onchange="updateTotal()">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Narx <span class="text-danger">*</span></label>
                    <input type="number" name="items[${itemCount}][unit_price]" class="form-control"
                           placeholder="0" min="0" step="0.01" required onchange="updateTotal()">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Muddati</label>
                    <input type="date" name="items[${itemCount}][expiry_date]" class="form-control">
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-sm btn-danger w-100" onclick="removeItem(${itemCount})">
                        <i class="fas fa-trash me-1"></i> O'chirish
                    </button>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">Seri Raqami</label>
                    <input type="text" name="items[${itemCount}][batch_number]" class="form-control"
                           placeholder="Seri #">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Joylashtirish</label>
                    <input type="text" name="items[${itemCount}][location]" class="form-control"
                           placeholder="Omborda joylashtirish (A1, B2 vb)">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Izohlar</label>
                    <input type="text" name="items[${itemCount}][notes]" class="form-control"
                           placeholder="Qo'shimcha izohlar">
                </div>
            </div>
        </div>
    `;
            container.insertAdjacentHTML('beforeend', itemHtml);
            itemCount++;
        }

        function removeItem(index) {
            const item = document.getElementById(`item-${index}`);
            if (item) {
                item.remove();
                updateTotal();
            }
        }

        function updateTotal() {
            let total = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                const quantity = row.querySelector('input[name*="quantity"]')?.value || 0;
                const price = row.querySelector('input[name*="unit_price"]')?.value || 0;
                total += parseInt(quantity) * parseFloat(price);
            });
            document.getElementById('totalAmount').textContent = new Intl.NumberFormat('uz-UZ').format(total);
        }

        // Sahifa yuklanganda birinchi item qo'shish
        window.addEventListener('DOMContentLoaded', function() {
            addItem();
        });
    </script>
@endsection

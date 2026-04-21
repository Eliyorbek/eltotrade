@extends('backend.inc.app')
@section('title', 'Yangi Mahsulot Chiqimi')
@section('content')
    <div class="container-fluid" style="max-width: 1000px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">
                    <i class="fas fa-sign-out-alt me-2"></i>
                    Yangi Mahsulot Chiqimi
                </h4>
                <small class="text-muted">Ombor chiqim qayd qilish</small>
            </div>
            <a href="{{ route('warehouse.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Orqaba
            </a>
        </div>

        <form action="{{ route('warehouse.stockOutStore') }}" method="POST" id="stockOutForm">
            @csrf

            {{-- Chiqim Ma'lumotlari --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">📋 Chiqim Ma'lumotlari</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Chiqim Sababu <span class="text-danger">*</span></label>
                            <select name="reason" class="form-select @error('reason') is-invalid @enderror" required>
                                <option value="">-- Sababi tanlang --</option>
                                <option value="damage" {{ old('reason') == 'damage' ? 'selected' : '' }}>
                                    ❌ Shikastlanish
                                </option>
                                <option value="expiry" {{ old('reason') == 'expiry' ? 'selected' : '' }}>
                                    ⏰ Muddati O'tgani
                                </option>
                                <option value="return" {{ old('reason') == 'return' ? 'selected' : '' }}>
                                    ↩️ Qaytarish
                                </option>
                                <option value="adjustment" {{ old('reason') == 'adjustment' ? 'selected' : '' }}>
                                    ⚖️ Tekshiruv / Ayirma
                                </option>
                                <option value="loss" {{ old('reason') == 'loss' ? 'selected' : '' }}>
                                    🚨 Yo'qolish
                                </option>
                            </select>
                            @error('reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Chiqim Sanasi <span class="text-danger">*</span></label>
                            <input type="date" name="issued_date" class="form-control @error('issued_date') is-invalid @enderror"
                                   value="{{ old('issued_date', date('Y-m-d')) }}" required>
                            @error('issued_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Chiqim Mahsulotlari --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">🛍️ Chiqim Mahsulotlari</h5>
                </div>
                <div class="card-body">
                    <div id="itemsContainer"></div>

                    <button type="button" class="btn btn-primary mt-3" onclick="addItem()">
                        <i class="fas fa-plus me-1"></i> Mahsulot Qo'shish
                    </button>
                </div>
            </div>

            {{-- Jami Xarajat --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6"></div>
                        <div class="col-md-6">
                            <div style="background: #f0f0f0; padding: 15px; border-radius: 8px;">
                                <div class="d-flex justify-content-between">
                                    <span>JAMI XARAJAT:</span>
                                    <span style="font-size: 24px; font-weight: bold; color: #dc3545;" id="totalAmount">0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Izohlar --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">📝 Izohlar</h5>
                </div>
                <div class="card-body">
                <textarea name="notes" class="form-control" rows="3"
                          placeholder="Chiqim haqida qo'shimcha ma'lumot...">{{ old('notes') }}</textarea>
                </div>
            </div>

            {{-- Tugmalar --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-save me-1"></i> Chiqimni Saqlash
                    </button>
                    <a href="{{ route('warehouse.index') }}" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-times me-1"></i> Bekor Qilish
                    </a>
                </div>
            </div>
        </form>
    </div>

    <script>
        let itemCount = 0;

        function addItem() {
            const container = document.getElementById('itemsContainer');
            const itemHtml = `
        <div style="background: #f9f9f9; border-radius: 8px; padding: 15px; margin-bottom: 15px; border-left: 4px solid #dc3545;" id="item-${itemCount}">
            <div class="row mb-3">
                <div class="col-md-5">
                    <label class="form-label">Mahsulot <span class="text-danger">*</span></label>
                    <select name="items[${itemCount}][product_id]" class="form-select" required onchange="updateTotal()">
                        <option value="">-- Mahsulotni tanlang --</option>
                        @foreach($products as $product)
            <option value="{{ $product->id }}">
                                {{ $product->name }} (Stock: {{ $product->stock }})
                            </option>
                        @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Miqdor <span class="text-danger">*</span></label>
            <input type="number" name="items[${itemCount}][quantity]" class="form-control"
                           placeholder="0" min="1" required onchange="updateTotal()">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Narx <span class="text-danger">*</span></label>
                    <input type="number" name="items[${itemCount}][unit_price]" class="form-control"
                           placeholder="0" min="0" step="0.01" required onchange="updateTotal()">
                </div>

                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-sm btn-danger w-100" onclick="removeItem(${itemCount})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">Seri Raqami</label>
                    <input type="text" name="items[${itemCount}][batch_number]" class="form-control"
                           placeholder="Seri #">
                </div>

                <div class="col-md-6">
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
            document.querySelectorAll('[id^="item-"]').forEach(row => {
                const quantity = row.querySelector('input[name*="quantity"]')?.value || 0;
                const price = row.querySelector('input[name*="unit_price"]')?.value || 0;
                total += parseInt(quantity) * parseFloat(price);
            });
            document.getElementById('totalAmount').textContent = new Intl.NumberFormat('uz-UZ').format(total);
        }

        window.addEventListener('DOMContentLoaded', function() {
            addItem();
        });
    </script>
@endsection

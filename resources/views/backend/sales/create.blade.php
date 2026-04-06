@extends('backend.inc.app')
@section('title', 'Yangi Sotuv (POS)')
    <style>
        .pos-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            height: calc(100vh - 200px);
        }

        @media (max-width: 1200px) {
            .pos-container {
                grid-template-columns: 1fr;
                height: auto;
            }
        }

        .products-area {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            overflow-y: auto;
        }

        .cart-area {
            background: white;
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 15px;
            display: flex;
            flex-direction: column;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }

        .product-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 10px;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
        }

        .product-card:hover {
            border-color: #007bff;
            box-shadow: 0 2px 8px rgba(0,123,255,0.2);
            transform: translateY(-2px);
        }

        .product-card.active {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }

        .product-name {
            font-size: 12px;
            font-weight: 600;
            margin: 5px 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .product-price {
            font-size: 14px;
            font-weight: bold;
            color: #28a745;
            margin: 5px 0;
        }

        .product-stock {
            font-size: 11px;
            color: #6c757d;
        }

        .cart-items {
            flex: 1;
            overflow-y: auto;
            margin-bottom: 15px;
        }

        .cart-item {
            display: grid;
            grid-template-columns: auto 1fr auto auto auto;
            gap: 10px;
            align-items: center;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 4px;
            margin-bottom: 8px;
            font-size: 13px;
        }

        .cart-item-name {
            font-weight: 600;
        }

        .cart-item-qty {
            width: 50px;
            padding: 4px;
            text-align: center;
        }

        .cart-item-total {
            font-weight: bold;
            min-width: 80px;
            text-align: right;
        }

        .remove-item {
            color: #dc3545;
            cursor: pointer;
            font-weight: bold;
        }

        .summary-section {
            border-top: 2px solid #ddd;
            padding-top: 15px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            font-size: 14px;
        }

        .summary-row.total {
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
            padding-top: 8px;
            border-top: 1px solid #ddd;
        }

        .discount-input {
            display: flex;
            gap: 5px;
            margin: 10px 0;
        }

        .discount-input input {
            flex: 1;
        }

        .discount-input select {
            width: 100px;
        }

        .payment-method {
            display: flex;
            gap: 5px;
            margin: 10px 0;
        }

        .payment-method label {
            flex: 1;
            margin: 0;
        }

        .payment-method input {
            margin-right: 3px;
        }

        .action-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-top: 10px;
        }

        .search-box {
            display: flex;
            gap: 5px;
            margin-bottom: 10px;
        }

        .search-box input {
            flex: 1;
        }

        .category-tabs {
            display: flex;
            gap: 5px;
            margin-bottom: 10px;
            overflow-x: auto;
            padding-bottom: 5px;
        }

        .category-btn {
            padding: 6px 12px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            white-space: nowrap;
            transition: all 0.2s;
        }

        .category-btn.active {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
    </style>

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">
                    <i class="fas fa-cash-register me-2"></i>
                    Yangi Sotuv (POS)
                </h4>
                <small class="text-muted">Mahsulot tanlang va sotuvni raqomlang</small>
            </div>
            <a href="{{ route('sales.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Sotuvlar
            </a>
        </div>

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>Xato!</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('sales.store') }}" method="POST" id="salesForm">
            @csrf

            <div class="pos-container">
                {{-- MAHSULOT TANLASH QISMI --}}
                <div class="products-area">
                    <h5 class="fw-bold mb-2">Mahsulotlar</h5>

                    {{-- Qidirish --}}
                    <div class="search-box">
                        <input type="text" id="productSearch" class="form-control form-control-sm"
                               placeholder="Nomi, SKU yoki barcode bo'yicha qidiring...">
                    </div>

                    {{-- Kategoriya filtri --}}
                    <div class="category-tabs">
                        <button type="button" class="category-btn active" data-category="all">
                            Barcha
                        </button>
                        @foreach($categories as $category)
                            <button type="button" class="category-btn" data-category="{{ $category->id }}">
                                {{ $category->name }}
                            </button>
                        @endforeach
                    </div>

                    {{-- Mahsulot siyohati --}}
                    <div class="product-grid" id="productGrid">
                        @foreach($products as $product)
                            <div class="product-card" data-product-id="{{ $product->id }}"
                                 data-category="{{ $product->category_id }}"
                                 data-name="{{ $product->name }}"
                                 data-price="{{ $product->sale_price }}"
                                 data-stock="{{ $product->stock }}">

                                <div class="product-name">{{ $product->name }}</div>
                                <div class="product-price">{{ number_format($product->sale_price, 0) }}</div>
                                <div class="product-stock">
                                    📦 {{ $product->stock }} {{ $product->unit }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- SAVAT VA HISOBLASH QISMI --}}
                <div class="cart-area">
                    <h5 class="fw-bold mb-2">🛒 Savat</h5>

                    {{-- Savat mahsulotlari --}}
                    <div class="cart-items" id="cartItems">
                        <div class="text-muted text-center py-4">
                            <i class="fas fa-shopping-cart fa-2x opacity-25 d-block mb-2"></i>
                            Mahsulot tanlanmagan
                        </div>
                    </div>

                    {{-- Xulosa --}}
                    <div class="summary-section">
                        <div class="summary-row">
                            <span>Jami:</span>
                            <span id="totalAmount">0</span>
                        </div>

                        {{-- Chegirma --}}
                        <div class="discount-input">
                            <select name="discount_type" id="discountType" class="form-select form-select-sm">
                                <option value="fixed">Soʻmda</option>
                                <option value="percent">Foizda</option>
                            </select>
                            <input type="number" name="discount_value" id="discountValue"
                                   class="form-control form-control-sm" placeholder="0" min="0">
                        </div>

                        <div class="summary-row">
                            <span>Chegirma:</span>
                            <span id="discountAmount">0</span>
                        </div>

                        <div class="summary-row total">
                            <span>Yakuniy:</span>
                            <span id="finalAmount">0</span>
                        </div>
                    </div>

                    {{-- Mijoz ma'lumotlari --}}
                    <div class="mt-3">
                        <small class="text-muted d-block mb-2">Mijoz (Ixtiyoriy)</small>
                        <input type="text" name="customer_name" class="form-control form-control-sm mb-2"
                               placeholder="Ismi">
                        <input type="tel" name="customer_phone" class="form-control form-control-sm"
                               placeholder="Telefon">
                    </div>

                    {{-- To'lov usuli --}}
                    <div class="payment-method mt-3">
                        <label>
                            <input type="radio" name="payment_method" value="cash" checked> Naqd
                        </label>
                        <label>
                            <input type="radio" name="payment_method" value="card"> Karta
                        </label>
                        <label>
                            <input type="radio" name="payment_method" value="transfer"> Transfer
                        </label>
                    </div>

                    {{-- Tugmalar --}}
                    <div class="action-buttons">
                        <button type="submit" class="btn btn-success btn-sm" id="completeSaleBtn" disabled>
                            <i class="fas fa-check me-1"></i> Raqomlash
                        </button>
                        <button type="reset" class="btn btn-warning btn-sm">
                            <i class="fas fa-redo me-1"></i> Tozalash
                        </button>
                    </div>
                </div>
            </div>

            {{-- Dynamic hidden inputs for items --}}
            <div id="itemsContainer"></div>
        </form>
    </div>

    <script>
        let cartItems = [];

        // Mahsulot qidirish (real-time)
        document.getElementById('productSearch').addEventListener('input', filterProducts);

        // Kategoriya filtri
        document.querySelectorAll('.category-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                filterProducts();
            });
        });

        function filterProducts() {
            const searchValue = document.getElementById('productSearch').value.toLowerCase();
            const categoryValue = document.querySelector('.category-btn.active').dataset.category;

            document.querySelectorAll('.product-card').forEach(card => {
                const name = card.dataset.name.toLowerCase();
                const category = card.dataset.category;

                const matchSearch = name.includes(searchValue) || searchValue === '';
                const matchCategory = categoryValue === 'all' || category === categoryValue;

                card.style.display = matchSearch && matchCategory ? '' : 'none';
            });
        }

        // Mahsulot tanlash
        document.querySelectorAll('.product-card').forEach(card => {
            card.addEventListener('click', function() {
                addToCart({
                    product_id: this.dataset.productId,
                    name: this.dataset.name,
                    price: parseFloat(this.dataset.price),
                    stock: parseInt(this.dataset.stock)
                });
            });
        });

        function addToCart(product) {
            const existing = cartItems.find(item => item.product_id === product.product_id);

            if (existing) {
                if (existing.quantity < product.stock) {
                    existing.quantity++;
                } else {
                    alert('Omborda etarli mahsulot yo\'q!');
                    return;
                }
            } else {
                cartItems.push({
                    product_id: product.product_id,
                    name: product.name,
                    unit_price: product.price,
                    quantity: 1,
                    discount_percent: 0
                });
            }

            updateCart();
        }

        function removeFromCart(index) {
            cartItems.splice(index, 1);
            updateCart();
        }

        function updateCart() {
            const cartHtml = cartItems.length === 0
                ? '<div class="text-muted text-center py-4"><i class="fas fa-shopping-cart fa-2x opacity-25 d-block mb-2"></i>Mahsulot tanlanmagan</div>'
                : cartItems.map((item, index) => `
            <div class="cart-item">
                <span>${index + 1}.</span>
                <span class="cart-item-name">${item.name}</span>
                <input type="number" class="cart-item-qty" value="${item.quantity}"
                       min="1" onchange="updateQuantity(${index}, this.value)">
                <span class="cart-item-total">${(item.quantity * item.unit_price).toLocaleString('en-US')}</span>
                <span class="remove-item" onclick="removeFromCart(${index})">✕</span>
            </div>
        `).join('');

            document.getElementById('cartItems').innerHTML = cartHtml;

            // Hisoblash
            calculateTotals();

            // Dynamic hidden inputs yaratish
            createHiddenInputs();

            // Tugmani enable/disable qilish
            document.getElementById('completeSaleBtn').disabled = cartItems.length === 0;
        }

        function updateQuantity(index, newQty) {
            cartItems[index].quantity = parseInt(newQty) || 1;
            updateCart();
        }

        function calculateTotals() {
            const totalAmount = cartItems.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0);
            const discountType = document.getElementById('discountType').value;
            const discountValue = parseFloat(document.getElementById('discountValue').value) || 0;

            let discountAmount = 0;
            if (discountType === 'percent') {
                discountAmount = (totalAmount * discountValue) / 100;
            } else {
                discountAmount = discountValue;
            }

            const finalAmount = totalAmount - discountAmount;

            document.getElementById('totalAmount').textContent = totalAmount.toLocaleString('en-US');
            document.getElementById('discountAmount').textContent = discountAmount.toLocaleString('en-US');
            document.getElementById('finalAmount').textContent = finalAmount.toLocaleString('en-US');
        }

        function createHiddenInputs() {
            const container = document.getElementById('itemsContainer');
            container.innerHTML = ''; // Eski inputlarni tozalash

            cartItems.forEach((item, index) => {
                // Product ID
                const productIdInput = document.createElement('input');
                productIdInput.type = 'hidden';
                productIdInput.name = `items[${index}][product_id]`;
                productIdInput.value = item.product_id;
                container.appendChild(productIdInput);

                // Quantity
                const qtyInput = document.createElement('input');
                qtyInput.type = 'hidden';
                qtyInput.name = `items[${index}][quantity]`;
                qtyInput.value = item.quantity;
                container.appendChild(qtyInput);

                // Unit Price
                const priceInput = document.createElement('input');
                priceInput.type = 'hidden';
                priceInput.name = `items[${index}][unit_price]`;
                priceInput.value = item.unit_price;
                container.appendChild(priceInput);

                // Discount Percent
                const discountInput = document.createElement('input');
                discountInput.type = 'hidden';
                discountInput.name = `items[${index}][discount_percent]`;
                discountInput.value = item.discount_percent || 0;
                container.appendChild(discountInput);
            });
        }

        // Chegirma o'zgarishi
        document.getElementById('discountType').addEventListener('change', calculateTotals);
        document.getElementById('discountValue').addEventListener('input', calculateTotals);

        // Form submit
        document.getElementById('salesForm').addEventListener('submit', function(e) {
            if (cartItems.length === 0) {
                e.preventDefault();
                alert('Iltimos, mahsulot tanlang!');
            }
        });
    </script>
@endsection

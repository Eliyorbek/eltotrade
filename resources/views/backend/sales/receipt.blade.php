@extends('backend.inc.app')
@section('title', 'Sotuv Cheki')
@push('styles')
    <style>
        .receipt-container {
            max-width: 400px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border: 1px solid #ddd;
            font-family: 'Courier New', monospace;
        }

        .receipt-header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px dashed #ccc;
            padding-bottom: 10px;
        }

        .receipt-logo {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .receipt-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .receipt-date {
            font-size: 11px;
            color: #666;
        }

        .receipt-number {
            font-size: 12px;
            font-weight: bold;
            margin-top: 5px;
        }

        .receipt-items {
            margin: 15px 0;
            border-bottom: 1px dashed #ccc;
            padding-bottom: 10px;
        }

        .item-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin-bottom: 5px;
        }

        .item-name {
            flex: 1;
        }

        .item-qty {
            width: 30px;
            text-align: center;
        }

        .item-price {
            width: 50px;
            text-align: right;
        }

        .separator {
            border-bottom: 1px dashed #ccc;
            margin: 10px 0;
        }

        .total-section {
            margin: 10px 0;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin-bottom: 5px;
        }

        .total-row.grand-total {
            font-size: 14px;
            font-weight: bold;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 5px 0;
        }

        .receipt-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 11px;
            color: #666;
            border-top: 1px dashed #ccc;
            padding-top: 10px;
        }

        .payment-info {
            text-align: center;
            font-size: 12px;
            margin: 10px 0;
        }

        .cashier-info {
            text-align: center;
            font-size: 11px;
            margin: 10px 0;
            color: #666;
        }

        @media print {
            body {
                background: white;
            }

            .receipt-container {
                border: none;
                box-shadow: none;
                margin: 0;
                padding: 0;
            }

            .btn, .d-flex {
                display: none !important;
            }

            .receipt-container {
                width: 80mm;
            }
        }

        .button-group {
            text-align: center;
            margin-top: 20px;
        }

        .button-group .btn {
            margin: 0 5px;
        }
    </style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <i class="fas fa-receipt me-2"></i>
            Sotuv Cheki
        </h4>
        <a href="{{ route('sales.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Sotuvlar
        </a>
    </div>

    <div class="receipt-container">
        {{-- Header --}}
        <div class="receipt-header">
            <div class="receipt-logo"></div>
            <div class="receipt-title">SOTUV CHEKI</div>
            <div class="receipt-date">
                {{ $sale->created_at->format('d.m.Y H:i') }}
            </div>
            <div class="receipt-number">
                Raqam: <strong>{{ $sale->sale_number }}</strong>
            </div>
        </div>

        {{-- Mijoz ma'lumotlari (agar bo'lsa) --}}
        @if($sale->customer_name)
            <div style="font-size: 12px; margin-bottom: 10px;">
                <strong>Mijoz:</strong> {{ $sale->customer_name }}
                @if($sale->customer_phone)
                    <br>Tel: {{ $sale->customer_phone }}
                @endif
            </div>
        @endif

        {{-- Mahsulotlar --}}
        <div class="receipt-items">
            <div style="margin-bottom: 5px;">
                <strong style="font-size: 12px;">Mahsulot        Miqdor    Narx</strong>
            </div>
            <div class="separator"></div>

            @foreach($sale->items as $item)
                <div class="item-row">
                    <span class="item-name">{{ $item->product->name }}</span>
                    <span class="item-qty">{{ $item->quantity }}x</span>
                    <span class="item-price">{{ number_format($item->total, 0) }}</span>
                </div>
            @endforeach
        </div>

        {{-- Xulosa --}}
        <div class="total-section">
            <div class="total-row">
                <span>Jami:</span>
                <span>{{ number_format($sale->total_amount, 0) }}</span>
            </div>

            @if($sale->discount_amount > 0)
                <div class="total-row">
                    <span>Chegirma:</span>
                    <span>-{{ number_format($sale->discount_amount, 0) }}</span>
                </div>
            @endif

            <div class="total-row grand-total">
                <span>YAKUNIY:</span>
                <span>{{ number_format($sale->final_amount, 0) }}</span>
            </div>
        </div>

        {{-- To'lov usuli --}}
        <div class="payment-info">
            <strong>To'lov usuli:</strong>
            @if($sale->payment_method === 'cash')
                Naqd
            @elseif($sale->payment_method === 'card')
                Plastik Karta
            @else
                Transfer
            @endif
        </div>

        {{-- Cashier ma'lumotlari --}}
        <div class="cashier-info">
            <div>Kassir: {{ $sale->user->name }}</div>
            <div>ID: {{ $sale->id }}</div>
        </div>

        {{-- Footer --}}
        <div class="receipt-footer">
            <div style="margin-bottom: 10px;">
                ✦ RAHMAT, QAYTA KELING! ✦
            </div>
            <div>
                Sotuv raqomlandi: {{ $sale->created_at->format('Y-m-d H:i:s') }}
            </div>
        </div>
    </div>

    {{-- Tugmalar --}}
    <div class="button-group">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print me-1"></i> Print
        </button>
        <button onclick="downloadPDF()" class="btn btn-info">
            <i class="fas fa-file-pdf me-1"></i> PDF
        </button>
        <a href="{{ route('sales.create') }}" class="btn btn-success">
            <i class="fas fa-plus me-1"></i> Yangi Sotuv
        </a>
        <a href="{{ route('sales.index') }}" class="btn btn-secondary">
            <i class="fas fa-list me-1"></i> Sotuvlar
        </a>
    </div>

    <script>
        function downloadPDF() {
            const element = document.querySelector('.receipt-container');
            const opt = {
                margin: 5,
                filename: '{{ $sale->sale_number }}.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { orientation: 'portrait', unit: 'mm', format: 'a4' }
            };

            // CDN orqali html2pdf load qilish
            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js';
            script.onload = function() {
                html2pdf().set(opt).from(element).save();
            };
            document.head.appendChild(script);
        }
    </script>
@endsection

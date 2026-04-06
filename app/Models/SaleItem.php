<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'unit_price',           // Shu vaqtdagi narxi
        'discount_percent',     // Item bo'yicha chegirma (ixtiyoriy)
        'subtotal',            // quantity * unit_price
        'total',               // subtotal - chegirma
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Sotuv bilan bog'lanish
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Mahsulot bilan bog'lanish
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Total hisoblash
     */
    public function calculateTotal(): float
    {
        $discount = 0;

        if ($this->discount_percent > 0) {
            $discount = ($this->subtotal * $this->discount_percent) / 100;
        }

        return $this->subtotal - $discount;
    }

    /**
     * Format summalar
     */
    public function getFormattedPrices(): array
    {
        return [
            'unit_price' => number_format($this->unit_price, 0),
            'subtotal' => number_format($this->subtotal, 0),
            'total' => number_format($this->total, 0),
        ];
    }
}

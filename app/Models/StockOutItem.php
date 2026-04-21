<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOutItem extends Model
{
    protected $fillable = [
        'stock_out_id',
        'product_id',
        'quantity',          // Chiqim qilingan miqdor
        'unit_price',        // Qiymat (cost)
        'batch_number',      // Seri raqami
        'notes',            // Izohlar
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Chiqim bilan bog'lanish
     */
    public function stockOut(): BelongsTo
    {
        return $this->belongsTo(StockOut::class);
    }

    /**
     * Mahsulot bilan bog'lanish
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Jami xarajat hisoblash
     */
    public function getTotalAttribute()
    {
        return $this->quantity * $this->unit_price;
    }
}

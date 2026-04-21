<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockInItem extends Model
{
    protected $fillable = [
        'stock_in_id',
        'product_id',
        'quantity',          // Kirim qilingan miqdor
        'unit_price',        // Kelish narxi
        'expiry_date',       // Muddati
        'batch_number',      // Seri raqami
        'location',          // Ombordagi joylashtirish
        'notes',            // Izohlar
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'expiry_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Kirim bilan bog'lanish
     */
    public function stockIn(): BelongsTo
    {
        return $this->belongsTo(StockIn::class);
    }

    /**
     * Mahsulot bilan bog'lanish
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Jami summa hisoblash
     */
    public function getTotalAttribute()
    {
        return $this->quantity * $this->unit_price;
    }

    /**
     * Muddati o'tganmi
     */
    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    /**
     * Muddati birov o'tib ketibdi
     */
    public function isExpiringSoon(): bool
    {
        if (!$this->expiry_date) return false;
        return $this->expiry_date->diffInDays(now()) <= 30;
    }
}

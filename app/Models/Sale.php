<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    protected $fillable = [
        'sale_number',      // Sotuv raqami (unique)
        'user_id',          // Kim sattadi (cashier)
        'total_amount',     // Jami summa (chegirmasiz)
        'discount_type',    // Chegirma turi: fixed, percent
        'discount_value',   // Chegirma miqdori
        'discount_amount',  // Chegirma summa (hisoblanadi)
        'final_amount',     // Yakuniy narx (chegirma bilan)
        'payment_method',   // To'lov usuli: cash, card, transfer
        'status',           // Status: completed, pending, cancelled
        'notes',            // Izohlar
        'customer_name',    // Mijoz ismi (ixtiyoriy)
        'customer_phone',   // Mijoz telefoni (ixtiyoriy)
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Sotuv detallari bilan bog'lanish
     */
    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Cashier bilan bog'lanish
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Sotuv raqamini auto-generate qilish
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->sale_number) {
                $lastSale = self::latest('id')->first();
                $nextId = ($lastSale?->id ?? 0) + 1;
                $model->sale_number = 'SAL-' . date('Ymd') . '-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Chegirma summasini hisoblash
     */
    public function calculateDiscount(): float
    {
        if ($this->discount_type === 'percent') {
            return ($this->total_amount * $this->discount_value) / 100;
        }

        return $this->discount_value ?? 0;
    }

    /**
     * Yakuniy narxni hisoblash
     */
    public function calculateFinalAmount(): float
    {
        return $this->total_amount - $this->discount_amount;
    }

    /**
     * Sotuv bo'ldi sertifikati
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Sotuv summasi format
     */
    public function getFormattedAmounts(): array
    {
        return [
            'total' => number_format($this->total_amount, 0),
            'discount' => number_format($this->discount_amount, 0),
            'final' => number_format($this->final_amount, 0),
        ];
    }
}

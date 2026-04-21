<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockIn extends Model
{
    protected $fillable = [
        'reference_number',  // Kirim raqami (PRI-20240315-001)
        'supplier_id',
        'user_id',          // Kim qayd qildi
        'received_date',    // Kirim qilingan sana
        'total_amount',     // Jami summa
        'status',           // pending, received, completed
        'notes',            // Izohlar
    ];

    protected $casts = [
        'received_date' => 'date',
        'total_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Ta'minotchi bilan bog'lanish
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Qayd qilgan user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Kirim detallari
     */
    public function items(): HasMany
    {
        return $this->hasMany(StockInItem::class);
    }

    /**
     * Auto-generate reference number
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->reference_number) {
                $lastId = self::latest('id')->first()?->id ?? 0;
                $nextId = $lastId + 1;
                $model->reference_number = 'PRI-' . date('Ymd') . '-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Kirim miqdorini hisoblash
     */
    public function getTotalItemsAttribute()
    {
        return $this->items()->sum('quantity');
    }

    /**
     * Status label
     */
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'pending' => 'Kutilmoqda',
            'received' => 'Qabul qilindi',
            'completed' => 'Tayyor',
            default => 'Noma\'lum',
        };
    }
}

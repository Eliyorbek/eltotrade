<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockOut extends Model
{
    protected $fillable = [
        'reference_number',   // Chiqim raqami (OUT-20240315-001)
        'user_id',           // Kim qayd qildi
        'reason',            // Sababu: damage, expiry, return, adjustment
        'issued_date',       // Chiqim sanasi
        'total_amount',      // Jami summa (xarajat)
        'status',            // pending, completed
        'notes',             // Izohlar
    ];

    protected $casts = [
        'issued_date' => 'date',
        'total_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Qayd qilgan user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Chiqim detallari
     */
    public function items(): HasMany
    {
        return $this->hasMany(StockOutItem::class);
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
                $model->reference_number = 'OUT-' . date('Ymd') . '-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Sabab label
     */
    public function getReasonLabelAttribute()
    {
        return match ($this->reason) {
            'damage' => '❌ Shikastlanish',
            'expiry' => '⏰ Muddati o\'tish',
            'return' => '↩️ Qaytarish',
            'adjustment' => '⚖️ Tekshiruv',
            'loss' => '🚨 Yo\'qolish',
            default => 'Boshqa',
        };
    }

    /**
     * Status label
     */
    public function getStatusLabelAttribute()
    {
        return $this->status === 'completed' ? '✓ Tayyor' : '⏳ Kutilmoqda';
    }

    /**
     * Jami chiqim miqdori
     */
    public function getTotalItemsAttribute()
    {
        return $this->items()->sum('quantity');
    }
}

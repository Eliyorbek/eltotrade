<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = [
        'name',              // Ta'minotchi nomi
        'contact_person',    // Kontakt odam
        'phone',            // Telefon
        'email',            // Email
        'address',          // Manzili
        'city',             // Shahar
        'country',          // Mamlakat
        'bank_account',     // Bank hisob
        'tin',              // TIN (Vergi ID)
        'payment_terms',    // To'lov shartlari (cash, credit, etc)
        'delivery_time',    // Yetkazib berish vaqti (kunlar)
        'status',           // active, inactive
        'notes',            // Izohlar
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Ushbu ta'minotchidan mahsulot kirishlari
     */
    public function stockInRecords(): HasMany
    {
        return $this->hasMany(StockIn::class);
    }

    /**
     * Ta'minotchiga buyurtmalar
     */
    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    /**
     * Status label
     */
    public function getStatusLabelAttribute()
    {
        return $this->status === 'active' ? '✓ Faol' : '✗ Bekor';
    }

    /**
     * Formatlashtirilgan telefon
     */
    public function getFormattedPhoneAttribute()
    {
        return $this->phone ? '+998 ' . substr($this->phone, -9) : '';
    }

    /**
     * Oxirgi kirim sanasi
     */
    public function getLastStockInDateAttribute()
    {
        return $this->stockInRecords()
            ->latest()
            ->first()
            ?->created_at
            ->format('d.m.Y');
    }

    /**
     * Jami kirim miqdori
     */
    public function getTotalStockInAttribute()
    {
        return $this->stockInRecords()
            ->sum('total_amount');
    }
}

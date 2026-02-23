<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'barcode',
        'description',
        'purchase_price',
        'sale_price',
        'wholesale_price',
        'stock',
        'min_stock',
        'unit',
        'image',
        'status',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            $product->slug = Str::slug($product->name);
            if (!$product->sku) {
                $product->sku = self::generateSku();
            }
            if (!$product->barcode) {
                $product->barcode = self::generateBarcode();
            }
        });

        static::updating(function ($product) {
            $product->slug = Str::slug($product->name);
        });
    }

    // Unique SKU generator
    public static function generateSku(): string
    {
        do {
            $sku = 'PRD-' . strtoupper(Str::random(6));
        } while (self::where('sku', $sku)->exists());

        return $sku;
    }

    // EAN-13 barcode generator
    public static function generateBarcode(): string
    {
        do {
            $barcode = '200' . str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT);
            // EAN-13 check digit
            $sum = 0;
            for ($i = 0; $i < 12; $i++) {
                $sum += $barcode[$i] * ($i % 2 === 0 ? 1 : 3);
            }
            $checkDigit = (10 - ($sum % 10)) % 10;
            $barcode .= $checkDigit;
        } while (self::where('barcode', $barcode)->exists());

        return $barcode;
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function isLowStock(): bool
    {
        return $this->stock <= $this->min_stock;
    }

    // Foyda foizi
    public function getProfitMarginAttribute(): float
    {
        if ($this->purchase_price == 0) return 0;
        return round((($this->sale_price - $this->purchase_price) / $this->purchase_price) * 100, 2);
    }
}

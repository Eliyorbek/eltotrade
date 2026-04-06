<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();

            // Foreign Keys
            $table->foreignId('sale_id')->constrained('sales')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict');

            // Mahsulot ma'lumotlari
            $table->integer('quantity');
            $table->decimal('unit_price', 12, 2);              // Shu vaqtdagi narxi
            $table->decimal('discount_percent', 8, 2)->default(0);

            // Summalar
            $table->decimal('subtotal', 12, 2);                // quantity * unit_price
            $table->decimal('total', 12, 2);                   // subtotal - chegirma

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index('sale_id');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};

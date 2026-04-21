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
        Schema::create('stock_in_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('stock_in_id')->constrained('stock_ins')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict');

            $table->integer('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->date('expiry_date')->nullable();
            $table->string('batch_number')->nullable();
            $table->string('location')->nullable();      // Omborda joylashtirish
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->index('stock_in_id');
            $table->index('product_id');
            $table->index('expiry_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_in_items');
    }
};

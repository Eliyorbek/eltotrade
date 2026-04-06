<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Sotuvlar jadvali
        Schema::create('sales', function (Blueprint $table) {
            $table->id();

            // Sotuv raqami
            $table->string('sale_number')->unique(); // SAL-20240315-00001

            // User (Kassir)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Narxlar
            $table->decimal('total_amount', 12, 2)->default(0);      // Chegirmasiz jami
            $table->enum('discount_type', ['fixed', 'percent'])->default('fixed');
            $table->decimal('discount_value', 12, 2)->default(0);    // Chegirma miqdori
            $table->decimal('discount_amount', 12, 2)->default(0);   // Hisoblangan chegirma
            $table->decimal('final_amount', 12, 2)->default(0);      // Yakuniy narx

            // To'lov
            $table->enum('payment_method', ['cash', 'card', 'transfer'])->default('cash');

            // Status
            $table->enum('status', ['completed', 'pending', 'cancelled'])->default('completed');

            // Mijoz ma'lumotlari
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();

            // Izohlar
            $table->text('notes')->nullable();

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('sale_number');
            $table->index('status');
            $table->index('created_at');
            $table->index('payment_method');
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};

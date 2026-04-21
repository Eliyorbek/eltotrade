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
        Schema::create('stock_outs', function (Blueprint $table) {
            $table->id();

            $table->string('reference_number')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->enum('reason', ['damage', 'expiry', 'return', 'adjustment', 'loss'])->default('adjustment');
            $table->date('issued_date');
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->index('reason');
            $table->index('issued_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_outs');
    }
};

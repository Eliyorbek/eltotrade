<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Suppliers jadvali
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();

            $table->string('name')->unique();           // Ta'minotchi nomi
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('tin')->nullable();           // Vergi ID
            $table->string('payment_terms')->nullable(); // cash, credit, etc
            $table->integer('delivery_time')->nullable();// kunlar
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->index('status');
        });

    }

    public function down(): void
    {

        Schema::dropIfExists('suppliers');
    }
};

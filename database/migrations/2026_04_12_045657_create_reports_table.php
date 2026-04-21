<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Reports jadvali (opsional - hisobotlarni cache qilish uchun)
        Schema::create('reports', function (Blueprint $table) {
            $table->id();

            $table->string('type');                 // daily, monthly, yearly, top_products, profit, low_profit
            $table->date('date_from');
            $table->date('date_to');

            $table->string('title')->nullable();
            $table->text('description')->nullable();

            $table->json('data');                   // Report ma'lumotlari JSON format'da

            $table->timestamps();

            $table->index('type');
            $table->index('date_from');
            $table->index('date_to');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};

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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug')->unique();
            $table->string('database')->unique();

            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();

            $table->string('logo')->nullable();

            $table->enum('status', ['active', 'inactive', 'suspended'])
                ->default('active');

            $table->date('subscription_start')->nullable();
            $table->date('subscription_end')->nullable();

            $table->json('settings')->nullable();

            $table->foreignId('created_by')
                ->constrained('super_admins')
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};

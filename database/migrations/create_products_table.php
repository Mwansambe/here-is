<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();

            $table->string('name');
            $table->string('slug')->unique();
            $table->string('brand')->nullable();

            $table->text('description')->nullable();
            $table->jsonb('specifications')->nullable(); // PostgreSQL jsonb
            $table->decimal('price', 12, 2);
            $table->decimal('discount_price', 12, 2)->nullable();

            $table->unsignedInteger('stock')->default(0);
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['category_id', 'is_active']);
            $table->index(['is_popular', 'is_featured']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
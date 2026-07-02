<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('order_number')->unique();
            $table->decimal('subtotal', 12, 2);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('shipping_fee', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('total', 12, 2);

            $table->string('payment_method')->nullable(); // mpesa, paypal, card...
            $table->string('payment_status', 20)->default('pending'); // pending, paid, failed
            $table->string('order_status', 20)->default('pending');   // pending, shipped, delivered

            $table->string('shipping_name');
            $table->string('shipping_phone', 20);
            $table->string('shipping_city');
            $table->string('shipping_region')->nullable();
            $table->string('shipping_address');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
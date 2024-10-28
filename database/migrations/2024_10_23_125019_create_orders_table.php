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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->json('cart');
            $table->decimal('subtotal_price', 10, 2);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('total_price', 10, 2);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('user_email');
            $table->string('user_phone_number');
            $table->text('user_address');
            $table->string('region');
            $table->string('reference')->unique();
            $table->enum('status', ['pending', 'in-transit', 'delivered'])->default('pending');
            $table->string('order_number')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

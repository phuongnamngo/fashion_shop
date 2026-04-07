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
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('order_code')->unique();
            $table->decimal('total_amount', 15, 2);
            $table->enum('status', ['pending', 'processing', 'shipped', 'completed', 'cancelled']);
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded']);
            $table->enum('payment_method', ['cod', 'momo', 'zalopay']);
            $table->text('shipping_address');
            $table->text('note')->nullable();
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

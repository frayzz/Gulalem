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
            $table->string('user_phone');
            $table->string('recipient_phone')->nullable();
            $table->string('address');
            $table->string('selected_time');
            $table->timestamp('custom_time')->nullable();
            $table->string('card_message')->nullable();
            $table->string('payment_method');
            $table->string('status'); // Статус заказа
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

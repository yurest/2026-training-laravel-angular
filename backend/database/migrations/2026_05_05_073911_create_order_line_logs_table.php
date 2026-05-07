<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_line_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->foreignId('restaurant_id')->constrained('restaurants');
            $table->foreignId('order_id')->constrained('orders');
            $table->uuid('order_line_id')->nullable();
            $table->foreignId('user_id')->constrained('users');

            $table->string('action');

            $table->integer('old_quantity')->nullable();
            $table->integer('new_quantity')->nullable();

            $table->integer('old_price')->nullable();
            $table->integer('new_price')->nullable();

            $table->string('reason')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_line_logs');
    }
};

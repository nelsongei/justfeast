<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('runner_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('pickup_time')->nullable();
            $table->timestamp('delivered_time')->nullable();
            $table->string('verification_pin');
            $table->string('status')->default('pending'); // pending, picked_up, delivered
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};

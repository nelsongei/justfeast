<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->foreignId('runner_id')->nullable()->constrained('users')->onDelete('set null');
            $table->json('seat_location'); // section, row, seat
            $table->decimal('total_amount', 8, 2);
            $table->string('payment_status')->default('pending'); // pending, paid, failed
            $table->string('order_status')->default('created'); // created, accepted, preparing, ready, runner_assigned, en_route, delivered
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

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
        Schema::create('loop_payments', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('merchant_reference', 64)->unique();
            $table->string('idempotency_key', 100)->unique();
            $table->string('paybill_number', 20);
            $table->string('account_reference', 100);
            $table->decimal('amount', 14, 2);
            $table->char('currency', 3)->default('KES');
            $table->string('narration', 140)->nullable();
            $table->string('status', 30)->default('created');
            $table->string('provider_transaction_id', 100)->nullable()->index();
            $table->string('provider_request_id', 100)->nullable()->index();
            $table->string('provider_receipt', 100)->nullable()->index();
            $table->string('provider_code', 50)->nullable();
            $table->text('provider_message')->nullable();
            $table->unsignedInteger('inquiry_attempts')->default(0);
            $table->timestamp('initiated_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('last_inquired_at')->nullable();
            $table->json('request_snapshot')->nullable();
            $table->json('response_snapshot')->nullable();
            $table->json('callback_snapshot')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loop_payments');
    }
};

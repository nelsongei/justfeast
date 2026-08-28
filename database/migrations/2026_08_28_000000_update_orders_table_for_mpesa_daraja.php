<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds Safaricom M-Pesa Daraja fields to orders table and drops legacy IntaSend fields.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'intasend_invoice_id')) {
                $table->dropColumn(['intasend_invoice_id', 'intasend_ref']);
            }
            $table->string('mpesa_checkout_request_id')->nullable()->unique()->after('payment_status');
            $table->string('mpesa_merchant_request_id')->nullable()->after('mpesa_checkout_request_id');
            $table->string('mpesa_receipt_number')->nullable()->after('mpesa_merchant_request_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['mpesa_checkout_request_id', 'mpesa_merchant_request_id', 'mpesa_receipt_number']);
            $table->string('intasend_invoice_id')->nullable()->after('payment_status');
            $table->string('intasend_ref')->nullable()->after('intasend_invoice_id');
        });
    }
};

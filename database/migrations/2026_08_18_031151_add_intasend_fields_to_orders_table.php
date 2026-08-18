<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds IntaSend tracking fields to the orders table.
     * - intasend_invoice_id: the invoice_id returned by IntaSend STK Push (used for status checks & webhook matching)
     * - intasend_ref: the api_ref we send to IntaSend (format: "order-{id}") used to match webhook callbacks
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('intasend_invoice_id')->nullable()->after('payment_status');
            $table->string('intasend_ref')->nullable()->after('intasend_invoice_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['intasend_invoice_id', 'intasend_ref']);
        });
    }
};

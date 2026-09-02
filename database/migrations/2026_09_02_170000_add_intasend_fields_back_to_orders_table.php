<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds IntaSend tracking fields back to the orders table alongside existing payment fields.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'intasend_invoice_id')) {
                $table->string('intasend_invoice_id')->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('orders', 'intasend_ref')) {
                $table->string('intasend_ref')->nullable()->after('intasend_invoice_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'intasend_invoice_id') || Schema::hasColumn('orders', 'intasend_ref')) {
                $table->dropColumn(array_filter(['intasend_invoice_id', 'intasend_ref'], fn($col) => Schema::hasColumn('orders', $col)));
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $hasIndex = function ($table, $name) {
            $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$name]);
            return count($indexes) > 0;
        };

        Schema::table('vendors', function (Blueprint $table) use ($hasIndex) {
            if (!$hasIndex('vendors', 'vendors_event_status_index')) {
                $table->index(['event_id', 'status'], 'vendors_event_status_index');
            }
        });

        Schema::table('orders', function (Blueprint $table) use ($hasIndex) {
            if (!$hasIndex('orders', 'orders_vendor_payment_created_index')) {
                $table->index(['vendor_id', 'payment_status', 'created_at'], 'orders_vendor_payment_created_index');
            }
            if (!$hasIndex('orders', 'orders_user_status_created_index')) {
                $table->index(['user_id', 'order_status', 'created_at'], 'orders_user_status_created_index');
            }
            if (!$hasIndex('orders', 'orders_payment_created_index')) {
                $table->index(['payment_status', 'created_at'], 'orders_payment_created_index');
            }
            if (!$hasIndex('orders', 'orders_intasend_invoice_id_unique')) {
                $table->unique('intasend_invoice_id', 'orders_intasend_invoice_id_unique');
            }
        });

        Schema::table('deliveries', function (Blueprint $table) use ($hasIndex) {
            if (!$hasIndex('deliveries', 'deliveries_runner_status_created_index')) {
                $table->index(['runner_id', 'status', 'created_at'], 'deliveries_runner_status_created_index');
            }
            if (!$hasIndex('deliveries', 'deliveries_order_status_index')) {
                $table->index(['order_id', 'status'], 'deliveries_order_status_index');
            }
            if (!$hasIndex('deliveries', 'deliveries_order_id_unique')) {
                $table->unique('order_id', 'deliveries_order_id_unique');
            }
        });
    }

    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropIndex('vendors_event_status_index');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_vendor_payment_created_index');
            $table->dropIndex('orders_user_status_created_index');
            $table->dropIndex('orders_payment_created_index');
            $table->dropUnique('orders_intasend_invoice_id_unique');
        });

        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropIndex('deliveries_runner_status_created_index');
            $table->dropIndex('deliveries_order_status_index');
            $table->dropUnique('deliveries_order_id_unique');
        });
    }
};

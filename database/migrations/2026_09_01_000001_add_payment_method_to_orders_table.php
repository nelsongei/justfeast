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
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'payment_method')) {
                $table->string('payment_method', 30)->default('mpesa')->index()->after('payment_status');
            }
            if (!Schema::hasColumn('orders', 'loop_payment_id')) {
                $table->foreignId('loop_payment_id')->nullable()->after('payment_method')->constrained('loop_payments')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'loop_payment_id')) {
                $table->dropForeign(['loop_payment_id']);
                $table->dropColumn('loop_payment_id');
            }
            if (Schema::hasColumn('orders', 'payment_method')) {
                $table->dropColumn('payment_method');
            }
        });
    }
};

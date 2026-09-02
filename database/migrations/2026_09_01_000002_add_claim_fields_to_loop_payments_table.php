<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loop_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('loop_payments', 'submitted_receipt')) {
                $table->string('submitted_receipt', 50)->nullable()->index()->after('provider_receipt');
            }
            if (!Schema::hasColumn('loop_payments', 'customer_claimed_at')) {
                $table->timestamp('customer_claimed_at')->nullable()->after('initiated_at');
            }
            if (!Schema::hasColumn('loop_payments', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('customer_claimed_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('loop_payments', function (Blueprint $table) {
            $table->dropColumn(['submitted_receipt', 'customer_claimed_at', 'expires_at']);
        });
    }
};

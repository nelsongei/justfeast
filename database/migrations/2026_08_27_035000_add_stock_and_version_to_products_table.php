<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('stock_quantity')->default(100)->after('stock_status');
            $table->integer('reserved_quantity')->default(0)->after('stock_quantity');
            $table->unsignedInteger('version')->default(1)->after('reserved_quantity');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['stock_quantity', 'reserved_quantity', 'version']);
        });
    }
};

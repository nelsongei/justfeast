<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->string('verification_pin_hash')->nullable()->after('verification_pin');
            $table->integer('verification_attempts')->default(0)->after('verification_pin_hash');
            $table->timestamp('pin_expires_at')->nullable()->after('verification_attempts');
        });
    }

    public function down(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropColumn(['verification_pin_hash', 'verification_attempts', 'pin_expires_at']);
        });
    }
};

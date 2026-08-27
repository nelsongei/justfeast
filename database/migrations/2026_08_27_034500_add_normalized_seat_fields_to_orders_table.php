<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('seat_type')->nullable()->after('runner_id');
            $table->string('seat_section')->nullable()->index()->after('seat_type');
            $table->string('seat_row')->nullable()->after('seat_section');
            $table->string('seat_number')->nullable()->after('seat_row');
            $table->decimal('latitude', 10, 7)->nullable()->after('seat_number');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
        });

        // Backfill existing order rows from seat_location JSON
        DB::table('orders')->get()->each(function ($order) {
            if (!empty($order->seat_location)) {
                $loc = is_string($order->seat_location) ? json_decode($order->seat_location, true) : (array) $order->seat_location;
                if (is_array($loc)) {
                    DB::table('orders')->where('id', $order->id)->update([
                        'seat_type'    => $loc['type'] ?? 'seat',
                        'seat_section' => $loc['section'] ?? null,
                        'seat_row'     => $loc['row'] ?? null,
                        'seat_number'  => $loc['seat'] ?? null,
                        'latitude'     => isset($loc['latitude']) ? floatval($loc['latitude']) : null,
                        'longitude'    => isset($loc['longitude']) ? floatval($loc['longitude']) : null,
                    ]);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['seat_section']);
            $table->dropColumn(['seat_type', 'seat_section', 'seat_row', 'seat_number', 'latitude', 'longitude']);
        });
    }
};

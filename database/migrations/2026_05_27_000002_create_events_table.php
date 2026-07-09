<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('venue_id')->constrained('venues')->onDelete('cascade');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('status')->default('active'); // active, completed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};

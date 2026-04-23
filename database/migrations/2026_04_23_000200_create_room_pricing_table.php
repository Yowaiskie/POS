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
        Schema::create('room_pricing', function (Blueprint $table) {
            $table->id();
            $table->decimal('base_rate_per_hour', 8, 2);
            $table->unsignedInteger('billing_unit_minutes')->default(30);
            $table->unsignedInteger('grace_period_minutes')->default(10);
            $table->boolean('per_room_rate')->default(false);
            $table->unsignedInteger('version')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_pricing');
    }
};

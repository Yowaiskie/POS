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
        Schema::table('room_pricing', function (Blueprint $table) {
            $table->decimal('price_30_min', 10, 2)->default(100);
            $table->decimal('price_60_min', 10, 2)->default(350);
            $table->integer('overtime_unit_minutes')->default(10);
            $table->decimal('overtime_unit_price', 10, 2)->default(50);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_pricing', function (Blueprint $table) {
            $table->dropColumn(['price_30_min', 'price_60_min', 'overtime_unit_minutes', 'overtime_unit_price']);
        });
    }
};

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
        Schema::table('room_sessions', function (Blueprint $table) {
            $table->decimal('room_charge', 10, 2)->nullable()->after('status');
            $table->json('billing_breakdown')->nullable()->after('room_charge');
            $table->json('pricing_snapshot')->nullable()->after('billing_breakdown');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_sessions', function (Blueprint $table) {
            $table->dropColumn(['room_charge', 'billing_breakdown', 'pricing_snapshot']);
        });
    }
};

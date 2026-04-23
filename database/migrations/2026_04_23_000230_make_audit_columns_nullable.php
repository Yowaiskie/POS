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
        Schema::table('room_billing_audit', function (Blueprint $table) {
            $table->json('pricing_snapshot')->nullable()->change();
            $table->json('billing_breakdown')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_billing_audit', function (Blueprint $table) {
            $table->json('pricing_snapshot')->nullable(false)->change();
            $table->json('billing_breakdown')->nullable(false)->change();
        });
    }
};

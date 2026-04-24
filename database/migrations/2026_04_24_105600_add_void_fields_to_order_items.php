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
        Schema::table('order_items', function (Blueprint $table) {
            $table->boolean('is_voided')->default(false)->after('is_stock_deducted');
            $table->timestamp('voided_at')->nullable()->after('is_voided');
            $table->foreignId('voided_by')->nullable()->after('voided_at')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['voided_by']);
            $table->dropColumn(['is_voided', 'voided_at', 'voided_by']);
        });
    }
};

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
            $table->float('promo_duration_hours')->default(0)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_sessions', function (Blueprint $table) {
            $table->dropColumn('promo_duration_hours');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\QueryException;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $indexes = ['created_at', 'shift_id'];

        foreach ($indexes as $index) {
            try {
                Schema::table('orders', function (Blueprint $table) use ($index) {
                    $table->index($index);
                });
            } catch (QueryException $e) {
                // Index might already exist
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $indexes = ['created_at', 'shift_id'];

        foreach ($indexes as $index) {
            try {
                Schema::table('orders', function (Blueprint $table) use ($index) {
                    $table->dropIndex([$index]);
                });
            } catch (QueryException $e) {
                //
            }
        }
    }
};

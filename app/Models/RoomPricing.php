<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomPricing extends Model
{
    use HasFactory;

    // Explicit table name to match migration
    protected $table = 'room_pricing';

    protected $fillable = [
        'base_rate_per_hour',
        'billing_unit_minutes',
        'grace_period_minutes',
        'per_room_rate',
        'version',
        'price_30_min',
        'price_60_min',
        'overtime_unit_minutes',
        'overtime_unit_price',
    ];
}

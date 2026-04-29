<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'menu_item_id',
        'name',
        'unit_price',
        'quantity',
        'is_stock_deducted',
        'is_voided',
        'voided_at',
        'voided_by',
        'void_shift_id',
        'kitchen_status',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'quantity' => 'integer',
        'is_stock_deducted' => 'boolean',
        'is_voided' => 'boolean',
        'voided_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function voidShift(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'void_shift_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'price',
        'is_active',
        'stock_quantity',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'stock_quantity' => 'integer',
    ];

    /**
     * Determine if the item is out of stock.
     * null = unlimited (never out of stock), 0 = out of stock.
     */
    public function isOutOfStock(): bool
    {
        return $this->stock_quantity !== null && $this->stock_quantity <= 0;
    }

    /**
     * Get a human-readable stock status label.
     */
    public function stockStatus(): string
    {
        if ($this->stock_quantity === null) {
            return 'unlimited';
        }
        if ($this->stock_quantity === 0) {
            return 'out_of_stock';
        }
        if ($this->stock_quantity <= 5) {
            return 'low';
        }
        return 'in_stock';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(MenuCategory::class, 'category_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}

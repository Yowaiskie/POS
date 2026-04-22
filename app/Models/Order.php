<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'order_number',
        'order_type',
        'room_session_id',
        'user_id',
        'status',
        'payment_method',
        'amount_received',
        'reference_number',
        'closed_at',
        'promo_name',
        'promo_price',
    ];

    protected $casts = [
        'closed_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function roomSession(): BelongsTo
    {
        return $this->belongsTo(RoomSession::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTotalAmountAttribute(): float
    {
        return $this->items->sum(fn (OrderItem $item) => $item->unit_price * $item->quantity);
    }

    public function getItemsCountAttribute(): int
    {
        return $this->items->sum('quantity');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoSet extends Model
{
    protected $fillable = ['name', 'price', 'duration_hours', 'is_active'];

    public function items()
    {
        return $this->hasMany(PromoSetItem::class);
    }
}

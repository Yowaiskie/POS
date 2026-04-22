<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoSetItem extends Model
{
    protected $fillable = ['promo_set_id', 'menu_item_id', 'quantity'];

    public function promoSet()
    {
        return $this->belongsTo(PromoSet::class);
    }

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }
}

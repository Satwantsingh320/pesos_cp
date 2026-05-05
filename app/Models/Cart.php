<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'customer_id',
        'session_id',
        'coupon_id',
        'subtotal',
        'shipping_amount',
        'discount_amount',
        'tax_amount',
        'grand_total',
        'coupon_code',
    ];
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
}

<?php
// app/Models/VipProductPrice.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VipProductPrice extends Model
{
    protected $table = 'vip_product_prices';

    protected $fillable = [
        'customer_id',
        'product_id',
        'product_variant_id',
        'vip_price'
    ];

    protected $casts = [
        'vip_price' => 'decimal:2'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}

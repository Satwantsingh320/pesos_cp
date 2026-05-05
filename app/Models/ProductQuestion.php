<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ProductQuestion extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'question',
        'is_answered',
        'is_approved'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(Customer::class);
    }

    public function answers()
    {
        return $this->hasMany(ProductAnswer::class);
    }
}

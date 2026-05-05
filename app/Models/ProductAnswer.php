<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ProductAnswer extends Model
{
    protected $fillable = [
        'product_question_id',
        'user_id',
        'answer',
        'is_approved'
    ];

    public function question()
    {
        return $this->belongsTo(ProductQuestion::class, 'product_question_id');
    }

    public function user()
    {
        return $this->belongsTo(Customer::class);
    }
}

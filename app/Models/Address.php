<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Address extends Model
{
    protected $fillable = ['customer_id', 'name', 'phone', 'dial_code', 'address', 'country', 'state', 'city', 'postcode', 'type', 'status', 'is_default'];
    public function user()
    {
        return $this->belongsTo(Customer::class);
    }
}

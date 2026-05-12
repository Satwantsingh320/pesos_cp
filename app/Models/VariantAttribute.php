<?php

namespace App\Models;
use App\Models\VariantAttributeValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VariantAttribute extends Model
{
    protected $table = 'variant_attributes';

    protected $fillable = [
        'name',
        'display_name',
        'type',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    /**
     * Get the values for this attribute
     */
    public function values(): HasMany
    {
        return $this->hasMany(VariantAttributeValue::class, 'attribute_id')->orderBy('position');
    }

    /**
     * Get active values
     */
    public function activeValues()
    {
        return $this->values()->where('status', 1)->orderBy('position');
    }
}

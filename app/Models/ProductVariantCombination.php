<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariantCombination extends Model
{
    protected $table = 'product_variant_combinations';

    protected $fillable = [
        'product_variant_id',
        'attribute_id',
        'attribute_value_id'
    ];

    /**
     * Get the variant
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /**
     * Get the attribute
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(VariantAttribute::class, 'attribute_id');
    }

    /**
     * Get the attribute value
     */
    public function attributeValue(): BelongsTo
    {
        return $this->belongsTo(VariantAttributeValue::class, 'attribute_value_id');
    }
}

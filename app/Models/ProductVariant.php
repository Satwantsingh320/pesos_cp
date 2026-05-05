<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductVariant extends Model
{

    protected $fillable = [
        'product_id',
        'sku',
        'barcode',
        'price',
        'offer_price',
        'quantity',
        'low_stock_threshold',
        'image',
        'status',
        'position'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'offer_price' => 'decimal:2',
        'quantity' => 'integer',
        'status' => 'boolean',
        'position' => 'integer'
    ];

    /**
     * Get the product that owns the variant
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Get the attributes for this variant
     */
    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(
            VariantAttribute::class,
            'product_variant_combinations',
            'product_variant_id',
            'attribute_id'
        )->withPivot('attribute_value_id');
    }

    /**
     * Get the attribute values for this variant
     */
    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(
            VariantAttributeValue::class,
            'product_variant_combinations',
            'product_variant_id',
            'attribute_value_id'
        );
    }

    /**
     * Get combinations for this variant
     */
    public function combinations()
    {
        return $this->hasMany(ProductVariantCombination::class, 'product_variant_id');
    }

    /**
     * Get active price (offer price if available)
     */
    public function getActivePriceAttribute()
    {
        return $this->offer_price && $this->offer_price < $this->price
            ? $this->offer_price
            : $this->price;
    }

    /**
     * Check if variant is in stock
     */
    public function getInStockAttribute()
    {
        return $this->quantity > 0;
    }

    /**
     * Check if variant is low stock
     */
    public function getIsLowStockAttribute()
    {
        return $this->quantity <= $this->low_stock_threshold && $this->quantity > 0;
    }
}

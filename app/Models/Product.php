<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Helpers\WebsiteHelper;
use App\Services\VipPricingService;

class Product extends Model
{
    protected $guarded = [];
    public $sortOrder = 'asc';
    public $sortEntity = 'products.id';

    protected $fillable = [
        'category_id',
        'subcategory_id',
        'brand_id',
        'name',
        'description',
        'cover_image',
        'price',
        'offer_price',
        'quantity',
        'sku_number',
        'barcode_number',
        'min_price',
        'max_price',
        'estimated_delivery_time',
        'status',
        'shipping_fee',
        'created_at',
        'updated_at',
        'is_special_offer',
        'low_stock_threshold',
        'is_clearance',
        'return_days',
        'slug',
        'is_featured',
        'total_sold',
        'type',
        'has_variants'
    ];

    public $vip_customer = null;
    protected $vip_prices = null;

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically set VIP customer when retrieving products
        static::retrieved(function ($product) {
            if (auth('customer')->check()) {
                $product->vip_customer = auth('customer')->user();
                $product->loadVipPricesForCustomer($product->vip_customer->id);
            }
        });
    }

    /**
     * Load VIP prices for a specific customer
     */
    public function loadVipPricesForCustomer($customerId)
    {
        $vipPrices = VipProductPrice::where('customer_id', $customerId)
            ->where('product_id', $this->id)
            ->get()
            ->keyBy(function ($item) {
                return $item->product_variant_id ?? 'base';
            });

        $this->vip_prices = $vipPrices;
        return $this;
    }

    /**
     * Get VIP price from loaded data
     */
    public function getVipPriceFromLoaded($customerId, $variantId = null)
    {
        if ($this->vip_prices !== null) {
            $key = $variantId ?? 'base';
            if (isset($this->vip_prices[$key]) && $this->vip_prices[$key]->vip_price > 0) {
                return (float) $this->vip_prices[$key]->vip_price;
            }
        }

        // Fallback to database query
        $query = VipProductPrice::where('customer_id', $customerId)
            ->where('product_id', $this->id);

        if ($variantId) {
            $query->where('product_variant_id', $variantId);
        } else {
            $query->whereNull('product_variant_id');
        }

        $vipPrice = $query->first();
        return $vipPrice && $vipPrice->vip_price > 0 ? (float) $vipPrice->vip_price : null;
    }

    public function gallery()
    {
        return $this->hasMany(ProductGallery::class, 'product_id', 'id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'product_id', 'id');
    }

    public function offer()
    {
        return $this->hasMany(Offer::class, 'product_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class)->withDefault();
    }

    public function inventory()
    {
        return $this->hasMany(Inventory::class, 'product_id', 'id')->orderBy('id', 'desc');
    }

    public function subcategory()
    {
        return $this->belongsTo(SubCategory::class)->withDefault();
    }

    public function brands()
    {
        return $this->belongsTo(Brand::class, 'brand_id')->withDefault();
    }

    public function scopeActive($query)
    {
        return $query->where('products.status', 1);
    }

    public function pagination(Request $request)
    {
        $filter = 1;
        $perPage = 10;
        $sortOrder = $this->sortOrder;
        $sortEntity = $this->sortEntity;

        $query = Product::with('category', 'subcategory', 'brands');
        if ($request->has('perPage') && $request->get('perPage') != '') {
            $perPage = $request->get('perPage');
        }
        if ($request->has('keyword') && $request->get('keyword') != '') {
            $filter .= " and (
                products.name like '%" . addslashes($request->get('keyword')) . "%'
                or products.sku_number like '%" . addslashes($request->get('keyword')) . "%'
                or products.barcode_number like '%" . addslashes($request->get('keyword')) . "%'
                or sub_categories.name like '%" . addslashes($request->get('keyword')) . "%'
                or categories.name like '%" . addslashes($request->get('keyword')) . "%')";
        }

        if ($request->has('status') && $request->get('status') != '') {
            $filter .= " and products.status = '" . addslashes($request->get('status')) . "'";
        }

        if ($request->has('sortEntity') && $request->get('sortEntity') != '') {
            $sortEntity = $request->get('sortEntity');
        }

        if ($request->has('sortOrder') && $request->get('sortOrder') != '') {
            $sortOrder = $request->get('sortOrder');
        }

        $query->addSelect('products.*')
            ->leftJoin('categories', 'categories.id', 'products.category_id')
            ->leftJoin('sub_categories', 'sub_categories.id', 'products.subcategory_id')
            ->leftJoin('brands', 'brands.id', 'products.brand_id')
            ->whereRaw($filter)
            ->orderBy($sortEntity, $sortOrder);
        Cache::put(env('EXPORT_CACHE_KEY'), $request->all());
        $data = $query->paginate($perPage);
        return $data;
    }

    public function toggleStatus($status, $ids = [])
    {
        if (isset($ids) && count($ids) > 0) {
            return $this->whereIn('products.id', $ids)->update(['status' => $status]);
        }
    }

    public function service($heading = true, $title = '-Select-', $search = [])
    {
        $filter = 1;
        if (isset($search) && count($search) > 0) {
            $f1 = (isset($search['subcategory_id']) && $search['subcategory_id'] != '') ?
                ' and products.subcategory_id = "' . addslashes($search['subcategory_id']) . '"' : '';
            $filter .= $f1;
        }

        $result = $this
            ->whereRaw($filter)
            ->active()
            ->get(['id', 'name']);

        $service = [];
        if ($heading) {
            $service[''] = $title;
        }

        if (isset($result) && count($result) > 0) {
            foreach ($result as $row) {
                $service[$row->id] = $row->name;
            }
        }
        return $service;
    }

    // ============ VIP CUSTOMER PRICING FUNCTIONS ============

    /**
     * Get discounted price for VIP customer using VipPricingService
     *
     * @param Customer|null $customer
     * @param ProductVariant|null $variant
     * @return float
     */
    public function getPriceForCustomer($customer = null, $variant = null)
    {
        // Get base price
        $basePrice = $variant
            ? ($variant->offer_price ?? $variant->price)
            : ($this->offer_price ?? $this->price);

        // If no customer, return base price
        if (!$customer) {
            return (float) $basePrice;
        }

        // Get VIP service and calculate discounted price
        $vipService = app(VipPricingService::class);
        $vipPrice = $vipService->getVipPrice($customer, $this, $variant);

        // Return VIP price if available, otherwise base price
        return $vipPrice !== null ? (float) $vipPrice : (float) $basePrice;
    }

    /**
     * Apply VIP discount to a given price using VipPricingService
     *
     * @param float $originalPrice
     * @param Customer|null $customer
     * @return float
     */
    public function applyVipDiscount($originalPrice, $customer = null)
    {
        // Use stored customer if none provided
        if ($customer === null && $this->vip_customer !== null) {
            $customer = $this->vip_customer;
        }

        // If no customer, return original price
        if (!$customer) {
            return $originalPrice;
        }

        // Check expiry
        if ($customer->vip_expiry_date && $customer->vip_expiry_date->isPast()) {
            return $originalPrice;
        }

        // Check for manual price from loaded vip_prices
        $manualPrice = $this->getVipPriceFromLoaded($customer->id);

        if ($manualPrice !== null) {
            return $manualPrice;
        }

        // For selected products only, check if product is selected
        if ($customer->vip_apply_to === 'selected_products') {
            $hasPrice = VipProductPrice::where('customer_id', $customer->id)
                ->where('product_id', $this->id)
                ->exists();

            if (!$hasPrice) {
                return $originalPrice;
            }
        }

        // Apply percentage discount
        if ($customer->vip_discount_type === 'percentage' && $customer->vip_discount_value > 0) {
            return round($originalPrice * (1 - $customer->vip_discount_value / 100), 2);
        }

        // Apply fixed discount
        if ($customer->vip_discount_type === 'fixed' && $customer->vip_discount_value > 0) {
            return max(0, round($originalPrice - $customer->vip_discount_value, 2));
        }

        return $originalPrice;
    }

    public function applyVipDiscountMM($originalPrice, $customer = null, $type)
    {
        // Use stored customer if none provided
        if ($customer === null && $this->vip_customer !== null) {
            $customer = $this->vip_customer;
        }

        // If no customer, return original price
        if (!$customer) {
            return $originalPrice;
        }

        // Check expiry
        if ($customer->vip_expiry_date && $customer->vip_expiry_date->isPast()) {
            return $originalPrice;
        }

        // Check for manual price from loaded vip_prices
        $manualPrice = $this->getVipPriceFromLoaded($customer->id);

        if ($manualPrice !== null) {
            return $manualPrice;
        }


        $query = VipProductPrice::where('customer_id', $customer->id)
            ->where('product_id', $this->id);

        if ($type === 'min') {
            $hasPrice = $query->orderBy('vip_price', 'asc')->first();
        } elseif ($type === 'max') {
            $hasPrice = $query->orderBy('vip_price', 'desc')->first();
        } else {
            $hasPrice = $query->first();
        }

        if ($hasPrice) {
            return $hasPrice->vip_price;
        }


        // Apply percentage discount
        if ($customer->vip_discount_type === 'percentage' && $customer->vip_discount_value > 0) {
            return round($originalPrice * (1 - $customer->vip_discount_value / 100), 2);
        }

        // Apply fixed discount
        if ($customer->vip_discount_type === 'fixed' && $customer->vip_discount_value > 0) {
            return max(0, round($originalPrice - $customer->vip_discount_value, 2));
        }

        return $originalPrice;
    }

    // ============ END VIP FUNCTIONS ============

    public function getCoverImageUrlAttribute(): string
    {
        if (!$this->cover_image) {
            return asset('assets/images/no-image.jpg');
        }
        $path = PRODUCTS_PATH . $this->cover_image;

        return file_exists(public_path($path))
            ? asset($path)
            : asset('assets/images/no-image.jpg');
    }

    public function getIsOnSaleAttribute(): bool
    {
        return (bool) $this->is_special_offer;
    }

    public function getDisplayPriceAttribute(): float
    {
        $price = null;

        if (
            !is_null($this->offer_price) &&
            $this->offer_price > 0 &&
            $this->offer_price < $this->price
        ) {
            $price = (float) $this->offer_price;
        } else {
            $price = (float) $this->price;
        }

        // Apply VIP discount if customer is set
        if ($this->vip_customer) {
            return $this->applyVipDiscount($price, $this->vip_customer);
        }

        return $price;
    }

    public function getOriginalPriceAttribute(): ?float
    {
        if (
            !is_null($this->offer_price) &&
            $this->offer_price > 0 &&
            $this->offer_price < $this->price
        ) {
            return (float) $this->price;
        }

        return null;
    }


    public function questions()
    {
        return $this->hasMany(ProductQuestion::class, 'product_id');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id')->orderBy('position');
    }

    public function activeVariants()
    {
        return $this->variants()->where('status', 1);
    }

    public function getMainImageAttribute()
    {
        return $this->cover_image ? asset(PRODUCTS_PATH . $this->cover_image) : null;
    }

    public function getPriceRangeAttribute()
    {
        if (!$this->has_variants == 1) {
            return null;
        }

        if ($this->min_price == $this->max_price) {
            return WebsiteHelper::formatPrice($this->min_price);
        }

        return WebsiteHelper::formatPrice($this->min_price) . ' - ' . WebsiteHelper::formatPrice($this->max_price);
    }

    public function getTotalQuantityAttribute()
    {
        if ($this->has_variants == 1) {
            return $this->variants()->sum('quantity');
        }
        return $this->quantity ?? 0;
    }

    public function getLowThresholdAttribute()
    {
        if ($this->has_variants == 1) {
            return $this->variants()->sum('low_stock_threshold');
        }
        return $this->low_stock_threshold ?? 0;
    }

    public function getMinPriceAttribute()
    {
        $price = null;

        if ($this->has_variants == 1) {
            $price = $this->variants()->min('price');
        } else {
            $price = $this->price;
        }

        if ($this->vip_customer) {
            return $this->applyVipDiscount($price, $this->vip_customer);
        }

        return $price;
    }

    public function getMaxPriceAttribute()
    {
        $price = null;

        if ($this->has_variants == 1) {
            $price = $this->variants()->max('price');
        } else {
            $price = $this->price;
        }

        if ($this->vip_customer) {
            return $this->applyVipDiscount($price, $this->vip_customer);
        }

        return $price;
    }

    public function updatePriceRange()
    {
        $minPrice = $this->variants()->min('price');
        $maxPrice = $this->variants()->max('price');

        $this->min_price = $minPrice;
        $this->max_price = $maxPrice;
        $this->save();
    }

    public function getPriceDisplayAttribute()
    {
        if ($this->has_variants == 1 && $this->variants && $this->variants->count() > 0) {
            $variantPrices = $this->variants->pluck('price')->toArray();
            $variantOfferPrices = $this->variants->pluck('offer_price')->filter()->toArray();

            $pricesToShow = !empty($variantOfferPrices) ? $variantOfferPrices : $variantPrices;

            if (empty($pricesToShow)) {
                return (object) ['type' => 'single', 'price' => 0];
            }

            $minPrice = min($pricesToShow);
            $maxPrice = max($pricesToShow);

            if ($this->vip_customer) {
                $minPrice = $this->applyVipDiscountMM($minPrice, $this->vip_customer ?? '', 'min');
                $maxPrice = $this->applyVipDiscountMM($maxPrice, $this->vip_customer ?? '', 'max');
            }

            if ($minPrice == $maxPrice) {
                return (object) [
                    'type' => 'single',
                    'price' => $minPrice,
                    'original_price' => $this->getOriginalPriceForVariants($variantPrices, $minPrice, $this->variants)
                ];
            } else {
                return (object) [
                    'type' => 'range',
                    'min_price' => $minPrice,
                    'max_price' => $maxPrice,
                    'original_price' => $this->getOriginalPriceForVariants($variantPrices, $minPrice, $this->variants)
                ];
            }
        } else {
            $displayPrice = $this->offer_price ?? $this->price;
            $originalPrice = ($this->offer_price && $this->offer_price < $this->price) ? $this->price : null;

            if ($this->vip_customer) {
                $displayPrice = $this->applyVipDiscount($displayPrice, $this->vip_customer);
            }

            return (object) [
                'type' => 'single',
                'price' => $displayPrice,
                'original_price' => $originalPrice
            ];
        }
    }

    private function getOriginalPriceForVariants($variantPrices, $currentMinPrice, $variant)
    {
        $minOriginal = min($variantPrices);
        return ($minOriginal != $currentMinPrice) ? $minOriginal : null;
    }
}

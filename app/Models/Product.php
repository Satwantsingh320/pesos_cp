<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Helpers\WebsiteHelper;

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
        'is_clearance',
        'return_days',
        'slug',
        'is_featured',
        'total_sold',
        'type',
        'has_variants'
    ];

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

    /* Product belongs to Subcategory */
    public function subcategory()
    {
        return $this->belongsTo(SubCategory::class)->withDefault();
    }
    public function brands()
    {
        return $this->belongsTo(Brand::class, 'brand_id')->withDefault();
        ;
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


        $query = Product::with('category', 'subcategory', 'brands'); // relation loaded
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
    // product image get acessor
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
    //display sale tag or not
    public function getIsOnSaleAttribute(): bool
    {
        return (bool) $this->is_special_offer;
    }
    //display price or offer price
    public function getDisplayPriceAttribute(): float
    {
        if (
            !is_null($this->offer_price) &&
            $this->offer_price > 0 &&
            $this->offer_price < $this->price
        ) {
            return (float) $this->offer_price;
        }

        return (float) $this->price;
    }
    //cut on original price
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
    //price final
    public function getFinalPriceAttribute()
    {
        return (!(is_null($this->offer_price)) && $this->offer_price > 0) ? $this->offer_price : $this->price;
    }

    public function questions()
    {
        return $this->hasMany(ProductQuestion::class, 'product_id');
    }

    /**
     * Get the variants for this product
     */
    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id')->orderBy('position');
    }

    /**
     * Get active variants
     */
    public function activeVariants()
    {
        return $this->variants()->where('status', 1);
    }

    /**
     * Get the main product image (cover)
     */
    public function getMainImageAttribute()
    {
        return $this->cover_image ? asset(PRODUCTS_PATH . $this->cover_image) : null;
    }

    /**
     * Get price range as string
     */
    public function getPriceRangeAttribute()
    {
        if (!$this->has_variants) {
            return null;
        }

        if ($this->min_price == $this->max_price) {
            return WebsiteHelper::formatPrice($this->min_price);
        }

        return WebsiteHelper::formatPrice($this->min_price) . ' - ' . WebsiteHelper::formatPrice($this->max_price);
    }

    // Accessor to get total quantity (for simple products or sum of variants)
    public function getTotalQuantityAttribute()
    {
        if ($this->has_variants) {
            return $this->variants()->sum('quantity');
        }
        return $this->quantity ?? 0;
    }

    // Accessor to get min price
    public function getMinPriceAttribute()
    {
        if ($this->has_variants) {
            return $this->variants()->min('price');
        }
        return $this->price;
    }

    // Accessor to get max price
    public function getMaxPriceAttribute()
    {
        if ($this->has_variants) {
            return $this->variants()->max('price');
        }
        return $this->price;
    }

    /**
     * Update price range from variants
     */
    public function updatePriceRange()
    {
        $minPrice = $this->variants()->min('price');
        $maxPrice = $this->variants()->max('price');
        $offerPrice = $this->variants()->min('offer_price');

        $this->min_price = $minPrice;
        $this->max_price = $maxPrice;
        $this->save();
    }
}

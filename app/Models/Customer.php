<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Cashier\Billable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\VipProductPrice;

class Customer extends Authenticatable
{
    use Billable;
    use Notifiable;
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $guard = 'customer';
    protected $guarded = [];
    public $sortOrder = 'asc';
    public $sortEntity = 'customers.id';
    protected $fillable = [
        'name',
        'email',
        'dial_code_iso',
        'dial_code',
        'phone',
        'rfc_number',
        'password',
        'image',
        'status',
        'stripe_id',
        'is_vip',
        'vip_discount_type',
        'vip_discount_value',
        'vip_apply_to',
        'vip_expiry_date',
    ];
    protected $casts = [
        'is_vip' => 'boolean',
        'vip_discount_value' => 'decimal:2',
        'vip_expiry_date' => 'date'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id', 'id');
    }


    public function scopeActive($query)
    {
        return $query->where('customers.status', 1);
    }
    public function pagination(Request $request)
    {
        $filter = 1;
        $perPage = 10;
        $sortOrder = $this->sortOrder;
        $sortEntity = $this->sortEntity;

        if ($request->has('perPage') && $request->get('perPage') != '') {
            $perPage = $request->get('perPage');
        }
        if ($request->has('keyword') && $request->get('keyword') != '') {
            $filter .= " and (
                customers.name like '%" . addslashes($request->get('keyword')) . "%'
                or customers.email like '%" . addslashes($request->get('keyword')) . "%'
                or customers.phone like '%" . addslashes($request->get('keyword')) . "%')";
        }

        if ($request->has('status') && $request->get('status') != '') {
            $filter .= " and customers.status = '" . addslashes($request->get('status')) . "'";
        }

        if ($request->has('sortEntity') && $request->get('sortEntity') != '') {
            $sortEntity = $request->get('sortEntity');
        }

        if ($request->has('sortOrder') && $request->get('sortOrder') != '') {
            $sortOrder = $request->get('sortOrder');
        }

        $query = $this->addSelect('customers.*')
            ->whereRaw($filter)
            ->orderBy($sortEntity, $sortOrder);
        Cache::put(env('EXPORT_CACHE_KEY'), $request->all());
        $data = $query->paginate($perPage);
        return $data;
    }
    public function toggleStatus($status, $ids = [])
    {
        if (isset($ids) && count($ids) > 0) {
            return $this->whereIn('customers.id', $ids)->update(['status' => $status]);
        }
    }
    public function service($heading = true, $title = '-Select-')
    {
        $result = $this
            ->active()
            ->get(['id', 'name']);

        $service = [];
        // if ($heading) {
        //     $service[''] = $title;
        // }

        if (isset($result) && count($result) > 0) {
            foreach ($result as $row) {
                $service[$row->id] = $row->name;
            }
        }
        return $service;
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function vipPrices()
    {
        return $this->hasMany(VipProductPrice::class);
    }

    /**
     * Get VIP price for a specific product/variant
     */
    public function getVipPrice($product, $variant = null)
    {
        // Check if customer is VIP and not expired
        if (!$this->is_vip) {   //|| ($this->vip_expiry_date && $this->vip_expiry_date < now())
            return null;
        }

        $productId = $product->id;
        $variantId = $variant ? $variant->id : null;

        // 1. Check for manual price override in vip_product_prices table
        $manualPrice = VipProductPrice::where('customer_id', $this->id)
            ->where('product_id', $productId)
            ->when($variantId, function ($query) use ($variantId) {
                return $query->where('product_variant_id', $variantId);
            })
            ->first();

        if ($manualPrice) {
            return $manualPrice->vip_price;
        }

        // 2. Apply percentage or fixed discount if applicable
        if ($this->vip_discount_type && $this->vip_discount_value) {
            // Check if discount applies to this product
            if ($this->vip_apply_to == 'selected_products') {
                // Check if this product has a manual price (meaning it's selected)
                $hasManualPrice = VipProductPrice::where('customer_id', $this->id)
                    ->where('product_id', $productId)
                    ->exists();

                if (!$hasManualPrice) {
                    return null; // Product not selected for discount
                }
            }

            // Get base price
            $basePrice = $variant
                ? ($variant->offer_price ?? $variant->price)
                : ($product->offer_price ?? $product->price);

            // Apply discount
            if ($this->vip_discount_type == 'percentage') {
                return $basePrice * (1 - $this->vip_discount_value / 100);
            } else { // fixed discount
                return max(0, $basePrice - $this->vip_discount_value);
            }
        }

        return null;
    }

    /**
     * Get all VIP prices for this customer (including calculated ones)
     */
    public function getAllVipPrices()
    {
        $prices = [];

        // Get manual prices
        $manualPrices = $this->vipPrices()->with('product', 'variant')->get();

        foreach ($manualPrices as $manualPrice) {
            $key = $manualPrice->product_id . '_' . ($manualPrice->product_variant_id ?? '0');
            $prices[$key] = [
                'type' => 'manual',
                'product_id' => $manualPrice->product_id,
                'variant_id' => $manualPrice->product_variant_id,
                'price' => $manualPrice->vip_price,
                'product_name' => $manualPrice->product->name,
                'variant_sku' => $manualPrice->variant->sku ?? null
            ];
        }

        return $prices;
    }

    /**
     * Assign VIP status with percentage discount for selected products
     */
    public function assignVipWithPercentage($percentage, $productIds = [], $expiryDate = null)
    {
        $this->update([
            'is_vip' => true,
            'vip_discount_type' => 'percentage',
            'vip_discount_value' => $percentage,
        ]);

        // If selected products, create entries in vip_product_prices as markers
        if (!empty($productIds) && $this->vip_apply_to == 'selected_products') {
            foreach ($productIds as $productId) {
                VipProductPrice::updateOrCreate(
                    [
                        'customer_id' => $this->id,
                        'product_id' => $productId,
                        'product_variant_id' => null
                    ],
                    [
                        'vip_price' => 0 // Placeholder, actual price calculated on the fly
                    ]
                );
            }
        }
    }

    /**
     * Assign VIP status with fixed discount for selected products
     */
    public function assignVipWithFixedDiscount($discountAmount, $productIds = [], $expiryDate = null)
    {
        $this->update([
            'is_vip' => true,
            'vip_discount_type' => 'fixed',
            'vip_discount_value' => $discountAmount,
        ]);

        // If selected products, create entries in vip_product_prices as markers
        if (!empty($productIds) && $this->vip_apply_to == 'selected_products') {
            foreach ($productIds as $productId) {
                VipProductPrice::updateOrCreate(
                    [
                        'customer_id' => $this->id,
                        'product_id' => $productId,
                        'product_variant_id' => null
                    ],
                    [
                        'vip_price' => 0 // Placeholder, actual price calculated on the fly
                    ]
                );
            }
        }
    }

    /**
     * Remove VIP status
     */
    public function removeVip()
    {
        // Delete all manual prices
        $this->vipPrices()->delete();

        // Reset VIP fields
        $this->update([
            'is_vip' => false,
            'vip_discount_type' => null,
            'vip_discount_value' => null
        ]);
    }

}

<?php

namespace App\Services;

use App\Models\VipProductPrice;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class VipPricingService
{
    protected const CACHE_TTL = 3600;

    /**
     * Get VIP price for customer
     */
    public function getVipPrice($customer, $product, $variant = null)
    {

        if (!$customer || !$customer->is_vip) {
            return null;
        }

        // Check expiry
        if ($customer->vip_expiry_date && $customer->vip_expiry_date->isPast()) {
            return null;
        }

        $cacheKey = $this->generateCacheKey($customer->id, $product->id, $variant->id ?? null);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($customer, $product, $variant) {
            return $this->calculateVipPrice($customer, $product, $variant);
        });
    }

    /**
     * Calculate VIP price
     */
    protected function calculateVipPrice($customer, $product, $variant = null)
    {
        // 1. Check for manual price in vip_product_prices table
        $manualPrice = $this->getManualPrice($customer->id, $product->id, $variant->id ?? null);

        if ($manualPrice !== null && $manualPrice > 0) {
            return $manualPrice;
        }

        // 2. Check if discount applies to this product
        if (!$this->doesDiscountApply($customer, $product)) {
            return null;
        }

        // 3. Get base price
        $basePrice = $this->getBasePrice($product, $variant);

        // 4. Apply discount
        return $this->applyDiscountToPrice($customer, $basePrice);
    }

    /**
     * Get manual price from database
     */
    public function getManualPrice($customerId, $productId, $variantId = null)
    {
        // First try to get variant-specific price
        if ($variantId) {
            $variantPrice = VipProductPrice::where('customer_id', $customerId)
                ->where('product_id', $productId)
                ->where('product_variant_id', $variantId)
                ->first();

            if ($variantPrice && $variantPrice->vip_price > 0) {
                return (float) $variantPrice->vip_price;
            }
        }

        // Fallback to default product price (where variant_id is null)
        $defaultPrice = VipProductPrice::where('customer_id', $customerId)
            ->where('product_id', $productId)
            ->whereNull('product_variant_id')
            ->first();

        return $defaultPrice && $defaultPrice->vip_price > 0 ? (float) $defaultPrice->vip_price : null;
    }

    /**
     * Check if discount applies to this product
     */
    protected function doesDiscountApply($customer, $product)
    {
        // If all products, always apply
        if ($customer->vip_apply_to === 'all') {
            return true;
        }

        // If selected products, check if product has manual price entry
        if ($customer->vip_apply_to === 'selected_products') {
            return true;
        }

        // For manual_only, always true as prices are set manually
        if ($customer->vip_apply_to === 'manual_only') {
            return VipProductPrice::where('customer_id', $customer->id)
                ->where('product_id', $product->id)
                ->exists();
        }

        return false;
    }

    /**
     * Get base price from product or variant
     */
    protected function getBasePrice($product, $variant = null)
    {
        if ($variant) {
            return (float) ($variant->offer_price ?? $variant->price);
        }

        return (float) ($product->offer_price ?? $product->price);
    }

    /**
     * Apply discount to price
     */
    protected function applyDiscountToPrice($customer, $price)
    {
        if (!$customer->vip_discount_value) {
            return $price;
        }

        if ($customer->vip_discount_type === 'percentage') {
            return round($price * (1 - $customer->vip_discount_value / 100), 2);
        }

        if ($customer->vip_discount_type === 'fixed') {
            return max(0, round($price - $customer->vip_discount_value, 2));
        }

        return $price;
    }

    /**
     * Generate cache key
     */
    protected function generateCacheKey($customerId, $productId, $variantId = null)
    {
        return "vip_price_{$customerId}_{$productId}_" . ($variantId ?? 'base');
    }

    /**
     * Clear VIP cache for customer
     */
    public function clearCustomerCache($customerId)
    {
        // Since Laravel doesn't have pattern-based cache deletion by default,
        // we'll use a tagged cache approach or simple flush for the pattern
        Cache::flush(); // Or implement more specific pattern deletion
    }

    /**
     * Set manual VIP price for a product
     */
    public function setManualPrice($customerId, $productId, $price, $variantId = null)
    {
        DB::beginTransaction();

        try {
            $vipPrice = VipProductPrice::updateOrCreate(
                [
                    'customer_id' => $customerId,
                    'product_id' => $productId,
                    'product_variant_id' => $variantId
                ],
                ['vip_price' => $price]
            );

            $this->clearCustomerCache($customerId);

            DB::commit();

            return $vipPrice;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete manual VIP price
     */
    public function deleteManualPrice($customerId, $productId, $variantId = null)
    {
        DB::beginTransaction();

        try {
            $deleted = VipProductPrice::where('customer_id', $customerId)
                ->where('product_id', $productId)
                ->when($variantId, function ($q) use ($variantId) {
                    return $q->where('product_variant_id', $variantId);
                })
                ->delete();

            $this->clearCustomerCache($customerId);

            DB::commit();

            return $deleted > 0;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get all manual prices for a customer
     */
    public function getManualPrices($customerId)
    {
        return VipProductPrice::where('customer_id', $customerId)
            ->with(['product', 'variant'])
            ->get();
    }

    /**
     * Bulk assign products to VIP customer
     */
    public function bulkAssignProducts($customerId, array $productIds, $defaultPrice = 0)
    {
        DB::beginTransaction();

        try {
            // Delete existing
            VipProductPrice::where('customer_id', $customerId)
                ->whereNull('product_variant_id')
                ->delete();

            // Create new entries
            $insertData = [];
            foreach ($productIds as $productId) {
                $insertData[] = [
                    'customer_id' => $customerId,
                    'product_id' => $productId,
                    'product_variant_id' => null,
                    'vip_price' => $defaultPrice,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            if (!empty($insertData)) {
                VipProductPrice::insert($insertData);
            }

            $this->clearCustomerCache($customerId);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function applyVipPricingToCart($cart, $customer)
    {
        if (!$customer || !$customer->is_vip) {
            return $cart;
        }
        foreach ($cart->items as $item) {
            $vipPrice = $this->getVipPrice($customer, $item->product, $item->variant);

            if ($vipPrice !== null) {
                $item->price_at_time = $vipPrice;
                $item->total = $vipPrice * $item->quantity;
                $item->save();
            }
        }

        return $cart;
    }

}

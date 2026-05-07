<?php
// app/Services/VipPricingService.php
namespace App\Services;

use App\Models\VipProductPrice;


class VipPricingService
{
    /**
     * Get VIP price for customer
     */
    public function getVipPrice($customer, $product, $variant = null)
    {
        if (!$customer || !$customer->is_vip) {
            return null;
        }

        return $customer->getVipPrice($product, $variant);
    }

    /**
     * Set manual VIP price for a product
     */
    public function setManualPrice($customerId, $productId, $price, $variantId = null)
    {
        return VipProductPrice::updateOrCreate(
            [
                'customer_id' => $customerId,
                'product_id' => $productId,
                'product_variant_id' => $variantId
            ],
            ['vip_price' => $price]
        );
    }

    /**
     * Delete manual VIP price
     */
    public function deleteManualPrice($customerId, $productId, $variantId = null)
    {
        return VipProductPrice::where('customer_id', $customerId)
            ->where('product_id', $productId)
            ->when($variantId, function ($q) use ($variantId) {
                return $q->where('product_variant_id', $variantId);
            })
            ->delete();
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
     * Apply VIP pricing to cart
     */
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

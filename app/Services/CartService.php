<?php

namespace App\Services;

use App\Helpers\WebsiteHelper;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Exception;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\VipPricingService;


class CartService
{
    protected $vipPricingService;

    public function __construct(VipPricingService $vipPricingService)
    {
        $this->vipPricingService = $vipPricingService;
    }
    /**
     * Create or get guest user token
     */
    public function getGuestToken()
    {
        $token = request()->cookie('guest_cart_token');

        if (!$token) {
            $token = (string) Str::uuid();
            Cookie::queue('guest_cart_token', $token, 60 * 24 * 30);
        }

        return $token;
    }

    /**
     * Get cart or create if not exist
     */
    public function getCart()
    {
        if (Auth::guard('customer')->check()) {
            return Cart::firstOrCreate([
                'customer_id' => Auth::guard('customer')->id()
            ]);
        }

        $guestToken = $this->getGuestToken();

        return Cart::firstOrCreate([
            'session_id' => $guestToken
        ]);
    }

    /**
     * Add to cart with variant support
     */
    public function addToCart($productId, $qty, $variantId = null)
    {
        try {
            DB::beginTransaction();

            $product = Product::where('id', $productId)->active()->first();
            if (!$product) {
                throw new \Exception('Product not available', 400);
            }

            $variant = null;
            $currentStock = 0;
            $price = 0;
            $shipping = $product->shipping_fee ?? 0;

            // Handle variant product
            if ($product->has_variants == 1) {
                if (!$variantId) {
                    throw new \Exception('Please select a variant', 400);
                }

                $variant = ProductVariant::with('combinations.attributeValue')
                    ->where('id', $variantId)
                    ->where('product_id', $productId)
                    ->first();

                if (!$variant) {
                    throw new \Exception('Variant not available', 400);
                }

                $currentStock = $variant->quantity;
                $price = $variant->offer_price ?? $variant->price;

                // Stock validation for variant
                if ($currentStock <= 0) {
                    throw new \Exception('Variant out of stock', 400);
                }

                if ($qty > $currentStock) {
                    throw new \Exception("Only {$currentStock} items available for this variant", 400);
                }
            } else {
                // Handle simple product
                $currentStock = $product->quantity ?? 0;

                if ($currentStock <= 0) {
                    throw new \Exception('Product out of stock', 400);
                }

                if ($qty > $currentStock) {
                    throw new \Exception("Only {$currentStock} items available", 400);
                }

                $price = $product->offer_price ?? $product->price;
            }


            $customer = Auth::guard('customer')->user();
            $vipPrice = null;
            if ($customer && $customer->is_vip) {
                $vipPrice = $this->vipPricingService->getVipPrice($customer, $product, $variant);
            }
            $price = $vipPrice ?? ($variant ? ($variant->offer_price ?? $variant->price) : ($product->offer_price ?? $product->price));



            $cart = $this->getCart();
            // Check if item already exists in cart
            $item = $cart->items()
                ->where('product_id', $productId)
                ->where('variant_id', $variantId)
                ->first();

            $finalQty = $item ? $item->quantity + $qty : $qty;

            // Validate final quantity against stock
            if ($finalQty > $currentStock) {
                throw new \Exception('Quantity exceeds available stock', 400);
            }

            // Create or update cart item
            $cartItem = $cart->items()->updateOrCreate(
                [
                    'product_id' => $productId,
                    'variant_id' => $variantId
                ],
                [
                    'price_at_time' => $price,
                    'shipping_fee_at_time' => $shipping,
                    'quantity' => $finalQty,
                    'total' => $finalQty * $price
                ]
            );

            // Store variant attributes for display if needed
            if ($variant && $cartItem) {
                $attributeText = [];
                foreach ($variant->combinations as $combo) {
                    if ($combo->attributeValue) {
                        $attributeText[] = $combo->attributeValue->value;
                    }
                }
                $cartItem->variant_attributes = implode(', ', $attributeText);
                $cartItem->save();
            }

            $this->recalculateCart($cart);

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage(), 400);
        }
    }

    /**
     * Update cart item quantity
     */
    public function updateQuantity($itemId, $quantity)
    {
        try {
            DB::beginTransaction();

            $cart = $this->getCart();
            $item = $cart->items()->find($itemId);

            if (!$item) {
                throw new \Exception('Item not found in cart', 404);
            }

            $product = Product::find($item->product_id);
            if (!$product) {
                $item->delete();
                throw new \Exception('Product not available', 400);
            }

            // Check stock based on product type
            if ($product->has_variants == 1 && $item->variant_id) {
                $variant = ProductVariant::find($item->variant_id);
                if (!$variant || $variant->quantity < $quantity) {
                    $availableStock = $variant ? $variant->quantity : 0;
                    throw new \Exception("Only {$availableStock} items available for this variant", 400);
                }
            } else {
                if ($product->quantity < $quantity) {
                    throw new \Exception("Only {$product->quantity} items available", 400);
                }
            }

            if ($quantity <= 0) {
                $item->delete();
            } else {
                $item->quantity = $quantity;
                $item->total = $item->price_at_time * $quantity;
                $item->save();
            }

            $this->recalculateCart($cart);

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage(), 400);
        }
    }

    /**
     * Remove item from cart
     */
    public function removeItem($itemId)
    {
        try {
            $cart = $this->getCart();
            $item = $cart->items()->find($itemId);

            if ($item) {
                $item->delete();
                $this->recalculateCart($cart);
            }

            return true;

        } catch (\Exception $e) {
            throw new \Exception('Failed to remove item', 400);
        }
    }

    /**
     * Apply cart validations
     */
    public function validateCart()
    {

        function getVipPrice($product, $variant, $customer)
        {
            if (!$customer || !$customer->is_vip) {
                return null;
            }
            return app(VipPricingService::class)->getVipPrice($customer, $product, $variant);
        }

        $cart = $this->getCart();
        $messages = [];

        if (!$cart || $cart->items->isEmpty()) {
            return $messages;
        }

        foreach ($cart->items as $item) {
            $product = Product::find($item->product_id);

            // If product deleted or inactive
            if (!$product || $product->status == 0) {
                $item->delete();
                $messages[] = "{$item->product_name} removed (not available)";
                continue;
            }

            $currentStock = 0;
            $actualPrice = 0;

            // Handle variant product
            if ($product->has_variants == 1 && $item->variant_id) {
                $variant = ProductVariant::find($item->variant_id);

                if (!$variant) {
                    $item->delete();
                    $messages[] = "{$product->name} variant removed (not available)";
                    continue;
                }
                $customer = auth('customer')->user();
                $regularPrice = $variant->offer_price ?? $variant->price;
                $vipPrice = getVipPrice($product, $variant, $customer);
                $actualPrice = $vipPrice ?? $regularPrice;
                $currentStock = $variant->quantity;

                // Check if variant is out of stock
                if ($currentStock <= 0) {
                    $item->delete();
                    $messages[] = "{$product->name} variant removed (out of stock)";
                    continue;
                }

                // Update variant attributes text
                $attributeText = [];
                foreach ($variant->combinations as $combo) {
                    if ($combo->attributeValue) {
                        $attributeText[] = $combo->attributeValue->value;
                    }
                }
                $item->variant_attributes = implode(', ', $attributeText);
            } else {
                // Handle simple product
                $customer = auth('customer')->user();
                $regularPrice = $product->offer_price ?? $product->price;
                $vipPrice = getVipPrice($product, null, $customer);
                $actualPrice = $vipPrice ?? $regularPrice;
                $currentStock = $product->quantity ?? 0;
                //add here

                if ($currentStock <= 0) {
                    $item->delete();
                    $messages[] = "{$product->name} removed (out of stock)";
                    continue;
                }
            }

            // Validate quantity against stock
            if ($item->quantity > $currentStock) {
                $item->quantity = $currentStock;
                $item->total = $item->price_at_time * $currentStock;
                $messages[] = "Available stock for {$product->name} is: {$currentStock}";
            }

            // Check if price changed
            $storedPrice = (int) (round((float) $item->price_at_time, 2) * 100);
            $currentPrice = (int) (round((float) $actualPrice, 2) * 100);

            if ($storedPrice !== $currentPrice) {
                $item->price_at_time = $actualPrice;
                $item->total = $actualPrice * $item->quantity;
                $messages[] = "Price has changed for {$product->name}";
            }

            // Check if shipping changed
            if ($item->shipping_fee_at_time != $product->shipping_fee) {
                $item->shipping_fee_at_time = $product->shipping_fee ?? 0;
                $messages[] = "Shipping price has changed for {$product->name}";
            }

            $item->save();
        }

        $this->recalculateCart($cart);
        return $messages;
    }

    /**
     * Recalculate cart totals
     */
    public function recalculateCart($cart)
    {
        $customer = Auth::guard('customer')->user();

        if ($customer && $customer->is_vip) {
            $cart = $this->vipPricingService->applyVipPricingToCart($cart, $customer);
        }

        if (!$cart || $cart->items->isEmpty()) {
            $cart->subtotal = 0;
            $cart->shipping_amount = 0;
            $cart->discount_amount = 0;
            $cart->grand_total = 0;
            $cart->tax_amount = 0;
            $cart->save();
            return;
        }

        $subtotal = 0;
        $shippingFee = 0;

        foreach ($cart->items as $item) {
            $subtotal += $item->total;
            $shippingFee += $item->shipping_fee_at_time;
        }

        // Calculate tax
        $taxPercent = WebsiteHelper::getTax();
        $taxAmount = $subtotal - $subtotal * (100 / (100 + $taxPercent));
        $subtotal = $subtotal - $taxAmount;
        $cart->subtotal = $subtotal;

        // Calculate free shipping threshold
        $freeShippingThreshold = WebsiteHelper::getShippingFree();
        $finalShippingFee = ($subtotal + $taxAmount) > $freeShippingThreshold ? 0 : $shippingFee;

        // Update cart
        $cart->subtotal = $subtotal;
        $cart->shipping_amount = $finalShippingFee;
        $cart->tax_amount = $taxAmount;
        $cart->discount_amount = $cart->discount_amount ?? 0;
        $cart->grand_total = $subtotal + $finalShippingFee + $taxAmount - ($cart->discount_amount ?? 0);

        $cart->save();
    }

    /**
     * Get cart count
     */
    public function cartCount(): int
    {
        if (Auth::guard('customer')->check()) {
            $cart = Cart::where('customer_id', Auth::guard('customer')->id())->first();
            if ($cart) {
                return $cart->items->sum('quantity');
            }
            return 0;
        }

        if (Cookie::has('guest_cart_token')) {
            $cart = Cart::where('session_id', Cookie::get('guest_cart_token'))->first();
            if ($cart) {
                return $cart->items->sum('quantity');
            }
            return 0;
        }

        return 0;
    }

    /**
     * Get cart items with all necessary data
     */
    public function getCartItems()
    {
        $cart = $this->getCart();

        if (!$cart || $cart->items->isEmpty()) {
            return collect();
        }

        // Load relationships for all items
        $cart->load(['items.product', 'items.variant.combinations.attributeValue']);

        // Enhance items with additional data
        foreach ($cart->items as $item) {
            $item->product_name = $item->product->name ?? 'N/A';
            $item->product_image = $item->product->CoverImageUrl ?? null;

            if ($item->variant) {
                $attributes = [];
                foreach ($item->variant->combinations as $combo) {
                    if ($combo->attributeValue) {
                        $attributes[] = $combo->attributeValue->value;
                    }
                }
                $item->variant_display = implode(', ', $attributes);
            }
        }

        return $cart->items;
    }

    /**
     * Get cart summary
     */
    public function getCartSummary()
    {
        $cart = $this->getCart();

        return [
            'subtotal' => $cart->subtotal ?? 0,
            'shipping' => $cart->shipping_amount ?? 0,
            'tax' => $cart->tax_amount ?? 0,
            'discount' => $cart->discount_amount ?? 0,
            'grand_total' => $cart->grand_total ?? 0,
            'item_count' => $cart->items->sum('quantity') ?? 0,
            'items' => $this->getCartItems()
        ];
    }

    /**
     * Clear entire cart
     */
    public function clear()
    {
        try {
            $cart = $this->getCart();
            if ($cart) {
                $cart->items()->delete();
                $cart->delete();
            }
            return true;
        } catch (\Exception $e) {
            Log::error('Cart clear error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Apply coupon/discount to cart
     */
    public function applyDiscount($code)
    {
        // Implement discount logic here
        // This is a placeholder for discount functionality
        return false;
    }
}

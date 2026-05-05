<?php

namespace App\Services;

use App\Helpers\WebsiteHelper;
use App\Models\Cart;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;

class CartService
{
    //create guest user token
    public function getGuestToken()
    {
        $token = request()->cookie('guest_cart_token');

        if (!$token) {
            $token = (string) Str::uuid();
            Cookie::queue('guest_cart_token', $token, 60 * 24 * 30);
        }

        return $token;
    }
    //get cart or create if not exist
    public function getCart()
    {
        if (Auth::guard('customer')->check()) {
            return Cart::firstOrCreate([
                'customer_id' => auth::guard('customer')->id()
            ]);
        }

        $gusetToken = $this->getGuestToken();

        return Cart::firstOrCreate([
            'session_id' => $gusetToken
        ]);
    }
    //addToCart
    public function addToCart($productId, $qty)
    {
        $product = Product::where('id', $productId)->active()->first();
        if (!$product) {
            throw new \Exception('Product not available', 400);
        }
        //inline vaildation
        if ($product->no_of_pieces_available <= 0) {
            throw new \Exception('Product out of stock', 400);
        }
        if ($qty > $product->no_of_pieces_available) {
            throw new \Exception("Only {$product->no_of_pieces_available} items available", 400);
        }
        $cart = $this->getCart();

        $item = $cart->items()->where('product_id', $productId)->first();
        $finalQty = $item ? $item->quantity + $qty : $qty;

        if ($finalQty > $product->no_of_pieces_available) {
            throw new \Exception('Quantity exceeds stock', 400);
        }
        $price = $product->offer_price;
        $shipping = $product->shipping_fee;

        //create or update cart items
        $cart->items()->updateOrCreate(
            ['product_id' => $productId],
            [
                'price_at_time' => $price,
                'shipping_fee_at_time' => $shipping,
                'quantity' => $finalQty,
                'total' => $finalQty * $price
            ]
        );
        $this->recalculateCart($cart);
        return true;
    }
    //apply cart validations
    public function ValidateCart()
    {
        $cart = $this->getCart();
        $messages = [];

        foreach ($cart->items as $item) {

            $product = Product::find($item->product_id);

            //if product delete or inactive
            if (!$product || $product->status == 0) {
                $item->delete();
                $messages[] = "{$product->name} removed (not available)";
                continue;
            }

            //out of  stock
            if ($product->no_of_pieces_available <= 0) {
                $item->delete();
                $messages[] = "{$product->name} removed (out of stock)";
            }

            //quantity exceeds
            if ($item->quantity > $product->no_of_pieces_available) {
                $item->quantity = $product->no_of_pieces_available;
                $messages[] = "Available stock for the {$product->name} is : {$product->no_of_pieces_available}";
            }

            //price change
            // Convert both to cents/centavos as integers to be 100% sure

            $storedPrice = (int) (round((float) $item->price_at_time, 2) * 100);
            $actualPrice = (int) (round((float) $product->offer_price, 2) * 100);
            if ($storedPrice !== $actualPrice) {
                $item->price_at_time = $product->offer_price;
                $messages[] = "Price has changed for {$product->name}";
            }

            //shipping chnaged
            if ($item->shipping_fee_at_time != $product->shipping_fee) {
                $item->shipping_fee_at_time = $product->shipping_fee;
                $messages[] = "Shipping price has chnaged for {$product->name}";
            }
            $item->total = $item->price_at_time * $item->quantity;
            $item->save();
        }
        $this->recalculateCart($cart);
        return $messages;
    }
    public function recalculateCart($cart)
    {
        $subtotal = 0;
        $shippingFee = 0;
        if ($cart->items->isEmpty()) {
            $cart->subtotal = 0;
            $cart->shipping_amount = 0;
            $cart->discount_amount = 0;
            $cart->grand_total = 0;
            $cart->tax_amount = 0;
        }
        foreach ($cart->items as $item) {
            $subtotal += $item->total;
            $shippingFee += $item->shipping_fee_at_time;
        }
        $percent = WebsiteHelper::getTax();
        $taxAmount = $subtotal - $subtotal * (100 / (100 + $percent));
        $subtotal = $subtotal - $taxAmount;
        $cart->subtotal = $subtotal;
        //tax
        $freeShipping = WebsiteHelper::getShippingFree();
        $shippingFee = ($subtotal + $taxAmount) > $freeShipping ? 0 : $shippingFee;
        $cart->shipping_amount = $shippingFee;
        $cart->tax_amount = round($taxAmount, 2);
        $cart->grand_total = $subtotal + $shippingFee + $cart->tax_amount;
        $cart->save();
    }
    public function cartCount(): int
    {
        if (Auth::guard('customer')->check()) {
            return cart::where('customer_id', Auth::guard('customer')->id())
                ->withSum('items', 'quantity')
                ->value('items_sum_quantity') ?? 0;
        }
        if (Cookie::has('guest_cart_token')) {
            return cart::where('session_id', Cookie::get('guest_cart_token'))
                ->withSum('items', 'quantity')
                ->value('items_sum_quantity') ?? 0;
        }
        return 0;
    }

    public function clear()
    {
        $cart = $this->getCart();
        if ($cart) {
            $cart->items()->delete();
            $cart->delete();
        }
    }
}

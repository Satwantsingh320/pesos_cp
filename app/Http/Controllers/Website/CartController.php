<?php

namespace App\Http\Controllers\Website;

use App\Helpers\WebsiteHelper;
use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Address;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use App\Services\CartService;
use Illuminate\Http\Request;
use App\Mail\OrderInvoiceMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function addToCart(Request $request, CartService $cartService)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
                'variant_id' => 'nullable|exists:product_variants,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            if ($request->buy_now) {
                $cartService->clear();
            }
            $cartService->addToCart($request->product_id, $request->quantity, $request->variant_id);
            $cartcount = $cartService->cartCount();
            if ($request->buy_now) {
                return response()->json([
                    'status' => true,
                    'redirect' => route('website.checkout')
                ]);
            }
            return WebsiteHelper::WebsiteApiResponse(
                true,
                __('admin.Product added to cart'),
                ['cart_count' => $cartcount],
                200,
            );
        } catch (\Exception $e) {
            if (in_array($e->getCode(), [400])) {
                return WebsiteHelper::WebsiteApiResponse(
                    false,
                    $e->getMessage(),
                    [],
                    400
                );
            }
            return WebsiteHelper::WebsiteInternalErrorResponse($e);
        }
    }
    public function viewCart(CartService $cartService)
    {
        $cart = $cartService->getCart();
        $cartItems = $cart->items;
        return view('website.cart', compact('cart', 'cartItems'));
    }
    // public function getCart(CartService $cartService)
    // {
    //     $cartService->getCart();
    // }

    public function applyCoupon(Request $request, CartService $cartService)
    {
        try {
            $code = trim($request->code);
            $cart = $cartService->getCart();
            if ($cart->coupon_code) {
                return WebsiteHelper::WebsiteApiResponse(
                    false,
                    __('admin.A coupon is already applied'),
                    [],
                    400
                );
            }
            $coupon = PromoCode::where('code', $code)
                ->where('status', 1)
                ->first();
            if (!$coupon) {
                return WebsiteHelper::WebsiteApiResponse(
                    false,
                    __('admin.Invalid coupon'),
                    [],
                    400
                );
            }
            if ($cart->subtotal < $coupon->min_order_amount) {
                return WebsiteHelper::WebsiteApiResponse(
                    false,
                    __('admin.Cart total too low for this coupon'),
                    [],
                    400
                );
            }
            $discount = $coupon->type == 1
                ? ($cart->subtotal * $coupon->code_amount) / 100
                : $coupon->code_amount;
            $cart->discount_amount = min($discount, $cart->subtotal);
            $cart->coupon_code = $code;
            $cartService->recalculateCart($cart);
            $data = [
                'subtotal' => number_format($cart->subtotal, 2),
                'discount' => number_format($cart->discount_amount, 2),
                'tax' => number_format($cart->tax_amount, 2),
                'shipping' => number_format($cart->shipping_amount, 2),
                'grand_total' => number_format($cart->grand_total - $cart->discount_amount, 2)
            ];
            $extra['redirect'] = 'checkout';
            return WebsiteHelper::WebsiteApiResponse(
                true,
                __('admin.Coupon applied successfully'),
                $data,
                200,
                $extra,

            );
        } catch (\Exception $e) {
            return WebsiteHelper::WebsiteInternalErrorResponse($e);
        }
    }

    public function removeCoupon()
    {
        $cart = app(CartService::class)->getCart();

        if ($cart) {
            $cart->update([
                'coupon_code' => '',
                'discount_amount' => 0,
            ]);
        }

        return redirect()->back()->with('success', __('admin.Coupon removed successfully.'));
    }
    public function updateQuantity(Request $request, CartService $cartService)
    {
        $request->validate([
            'cart_item_id' => 'required|exists:cart_items,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = $cartService->getCart();
        $item = $cart->items()->findOrFail($request->cart_item_id);

        // Get product and variant info
        $product = $item->product;
        $maxStock = 0;

        // Check if item has variant
        if ($item->variant_id && $item->variant) {
            $variant = $item->variant;
            $maxStock = $variant->quantity;

            // Stock validation for variant
            if ($request->quantity > $maxStock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only ' . $maxStock . ' items available for this variant',
                    'max_stock' => $maxStock
                ], 400);
            }
        } else {
            // Simple product stock validation
            $maxStock = $product->no_of_pieces_available ?? 0;

            if ($request->quantity > $maxStock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only ' . $maxStock . ' items available',
                    'max_stock' => $maxStock
                ], 400);
            }
        }

        // Update quantity
        $item->quantity = $request->quantity;
        $item->total = $item->quantity * $item->price_at_time;
        $item->save();

        // Recalculate cart totals
        $cartService->recalculateCart($cart);
        $cartCount = $cartService->cartCount();

        // Calculate grand total correctly
        $grandTotal = $cart->subtotal + $cart->tax_amount + $cart->shipping_amount - ($cart->discount_amount ?? 0);

        // Return data directly without nesting
        return response()->json([
            'success' => true,
            'message' => __('admin.Cart updated'),
            'item_total' => '{{CURRENCY}} ' . number_format($item->total, 2),
            'subtotal' => '{{CURRENCY}} ' . number_format($cart->subtotal, 2),
            'discount' => '{{CURRENCY}} ' . number_format($cart->discount_amount ?? 0, 2),
            'tax' => '{{CURRENCY}} ' . number_format($cart->tax_amount, 2),
            'shipping' => '{{CURRENCY}} ' . number_format($cart->shipping_amount, 2),
            'grand_total' => '{{CURRENCY}} ' . number_format($grandTotal, 2),
            'cart_count' => $cartCount,
            'item_quantity' => $item->quantity,
            'max_stock' => $maxStock
        ], 200);
    }
    //delete cart item
    public function removeItem(Request $request, CartService $cartService)
    {
        $cart = $cartService->getCart();
        $item = $cart->items()->findOrFail($request->cart_item_id);
        $item->delete();
        //recalulate billing summary again (carts table)
        $cartService->recalculateCart($cart);
        $cartcount = $cartService->cartCount();
        return WebsiteHelper::WebsiteApiResponse(
            true,
            __('admin.Item Deleted'),
            [
                'item_total' => CURRENCY . number_format($item->total, 2),
                'subtotal' => CURRENCY . number_format($cart->subtotal, 2),
                'discount' => CURRENCY . number_format($cart->discount_amount, 2),
                'tax' => CURRENCY . number_format($cart->tax_amount, 2),
                'shipping' => CURRENCY . number_format($cart->shipping_amount, 2),
                'grand_total' => CURRENCY . number_format($cart->grand_total - $cart->discount_amount, 2),
                'cart_count' => $cartcount,
            ],
            200
        );
    }

    public function viewCheckout(CartService $cartService)
    {
        $cart = $cartService->getCart();
        $cartItems = $cart->items;
        if (count($cartItems) == 0) {
            return redirect('/cart');
        }

        // 1. Check if user is logged in
        $isLoggedIn = auth('customer')->check();

        // 2. Fetch saved addresses if authenticated, otherwise empty array
        $savedAddresses = $isLoggedIn
            ? auth('customer')->user()->addresses // Assumes a 'hasMany' relationship with an Address model
            : collect([]);

        return view('website.checkout', compact('cart', 'cartItems', 'isLoggedIn', 'savedAddresses'));
    }

    public function proceedPayment(Request $request, CartService $cartService)
    {
        if (!auth('customer')->check()) {
            return redirect()->route('login.post');
        }
        $validationErrors = $cartService->ValidateCart();
        if (!empty($validationErrors)) {
            return redirect()->route('website.cart')
                ->with('error', $validationErrors);
        }
        $user = auth('customer')->user();
        $cart = $cartService->getCart();

        //dd($request);
        // Calculate your values (example logic)
        $subTotal = $cart->subtotal; // Ensure your service has these methods
        $tax = $cart->tax_amount ?? 0;
        $shippingFee = $cart->shipping_amount ?? 0;
        $discount = $cart->discount_amount ?? 0;
        $couponId = $cart->coupon_code ?? null;
        // 1. Logic for Billing Address
        $shippingData = [];
        if ($request->saved_billing_id) {
            $shippingData['id'] = $request->saved_billing_id;
        } else {
            $shippingData = [
                'name' => $request->shipping_name,
                'phone' => $request->shipping_dial_code . $request->shipping_phone,
                'address' => $request->shipping_address,
                'colonia' => $request->shipping_colonia,
                'city' => $request->shipping_city,
                'state' => $request->shipping_state,
                'postcode' => $request->shipping_postcode,
                'type' => $request->shipping_type,
            ];
        }

        // 2. Logic for Shipping Address
        $billingData = [];
        if ($request->has('same_as_shipping')) {
            $billingData = ['type' => 'same_as_shipping'];
        } else {
            $billingData = [
                'name' => $request->billing_name,
                'phone' => $request->billing_dial_code . $request->billing_phone,
                'address' => $request->billing_address,
                'colonia' => $request->billing_colonia,
                'city' => $request->billing_city,
                'state' => $request->billing_state,
                'postcode' => $request->billing_postcode,
                'type' => $request->billing_type,
            ];
        }

        // Prepare line items for Stripe from your cart
        $lineItems = [];
        foreach ($cart->items as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'mxn',
                    'unit_amount' => $item->price_at_time * 100, // Stripe uses cents ($10.00 = 1000)
                    'product_data' => [
                        'name' => $item->product?->name,
                    ],
                ],
                'quantity' => $item->quantity,
            ];
        }

        // 1. Add Shipping as a Line Item
        if ($shippingFee > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'mxn',
                    'unit_amount' => $shippingFee * 100,
                    'product_data' => [
                        'name' => 'Shipping Fee',
                    ],
                ],
                'quantity' => 1,
            ];
        }

        // 2. Add Tax as a Line Item
        if ($tax > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'mxn',
                    'unit_amount' => $tax * 100,
                    'product_data' => [
                        'name' => 'Tax',
                    ],
                ],
                'quantity' => 1,
            ];
        }

        // 3. Handle Discounts
        $discounts = [];
        if ($discount > 0) {
            // 1. Initialize Stripe Client (uses your secret key from services.php or .env)
            $stripe = new \Stripe\StripeClient(config('services.stripe.STRIPE_SECRET'));
            // 2. Create a temporary coupon in Stripe based on your database calculation
            $stripeCoupon = $stripe->coupons->create([
                'amount_off' => $discount * 100, // Convert your DB discount to cents
                'currency' => 'mxn',           // Match your currency
                'duration' => 'once',          // Only valid for this session
                'name' => 'Coupon: ' . ($couponId ?? 'Discount'), // Shows up in Stripe UI
            ]);
            // 3. Add to the array to be passed to checkout
            $discounts[] = ['coupon' => $stripeCoupon->id];
        }

        // Redirect to Stripe Checkout
        return $user->checkout($lineItems, [
            'success_url' => route('website.checkout-success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('website.checkout-cancel'),
            'discounts' => $discounts, // <--- Apply the discount here
            'metadata' => [
                'billing_json' => json_encode($billingData),
                'shipping_json' => json_encode($shippingData),
                'order_notes' => $request->notes,
                'sub_total' => $subTotal,
                'tax' => $tax,
                'shipping' => $shippingFee,
                'discount' => $discount,
                'coupon_id' => $couponId,
            ],
        ]);
    }

    public function checkoutSuccess(Request $request)
    {
        $sessionId = $request->get('session_id');
        if (!$sessionId) {
            return redirect()->route('customer.dashboard.index');
        }
        $check = Order::where('stripe_id', $sessionId)->first();
        if (!empty($check)) {
            return redirect()->route('customer.dashboard.index');
        }

        // Fetch the session from Stripe to verify payment status
        $session = auth('customer')->user()->stripe()->checkout->sessions->retrieve($sessionId);
        if ($session->payment_status === 'paid') {
            // Decode metadata
            $billingMeta = json_decode($session->metadata->billing_json, true);
            $shippingMeta = json_decode($session->metadata->shipping_json, true);

            // Fetch billing details from DB or Metadata
            if (isset($shippingMeta['id'])) {
                $addr = Address::find($shippingMeta['id']);
                $shippingFinal = [
                    'address' => $addr->address,
                    'city' => $addr->city,
                    'state' => $addr->state,
                    'postcode' => $addr->postcode,
                    'type' => $addr->type,
                    'phone' => $addr->dial_code . $addr->phone,
                ];
            } else {
                $shippingFinal = $shippingMeta;
            }

            // Handle Billing
            if (isset($billingMeta['type']) && $billingMeta['type'] === 'same_as_shipping') {
                $billingFinal = $shippingFinal;
            } else {
                $billingFinal = $billingMeta;
            }
            // Save to Orders table as a single JSON blob
            $order = Ordercreate([
                'customer_id' => auth('customer')->id(),
                'stripe_id' => $session->id,
                'payment_intent_id' => $session->payment_intent,
                'price' => $session->amount_total / 100,
                'sub_total' => $session->metadata->sub_total ?? 0,
                'tax' => $session->metadata->tax ?? 0,
                'shipping' => $session->metadata->shipping ?? 0,
                'discount' => $session->metadata->discount ?? 0,
                'coupon_id' => $session->metadata->coupon_id ?? null,
                'payment_status' => 'Paid',
                'payment_type' => 'Online',
                'order_status' => 1,
                'address' => [
                    'billing' => $billingFinal,
                    'shipping' => $shippingFinal
                ],
                'txn_details' => json_encode($session),
                'order_number' => 'ORD-' . date('ymd') . strtoupper(uniqid()),
            ]);
            $order->order_number = 'ORD-' . now()->format('ymd') . '' . str_pad($order->id, 6, '0', STR_PAD_LEFT);
            $order->save();
            $cart = app(CartService::class)->getCart();
            foreach ($cart->items as $item) {
                $orderItem = new OrderItem;
                $orderItem->orders_id = $order->id;
                $orderItem->product_id = $item->product_id;
                $orderItem->name = $item->product?->name ?? '';
                $orderItem->quantity = $item->quantity;
                $orderItem->price = $item->price_at_time;
                $orderItem->shipping_fee = $item->shipping_fee_at_time;
                $orderItem->save();
            }
            app(CartService::class)->clear();
            // Reload order with items
            $order->load('items');
            $admins = User::all();
            foreach ($admins as $admin) {
                $admin->notify(new NewOrderNotification($order));
            }
            try {
                // Send to Customer
                Mail::to(auth('customer')->user()->email)
                    ->send(new OrderInvoiceMail($order));   //use queue in place of send for fast sending

                // Send to Admin
                Mail::to(config('mail.admin_email'))
                    ->send(new OrderInvoiceMail($order, true));
            } catch (\Exception $e) {
                \Log::error('Mail Queue Failed: ' . $e->getMessage());
            }

            return view('website.thanks', ['order_id' => $order->order_number]);
        }
        return redirect()->route('checkout-cancel')->with('error', __('admin.Payment failed.'));
    }

}

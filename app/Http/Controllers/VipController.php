<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\VipProductPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VipController extends Controller
{
    /**
     * Display VIP management page
     */
    public function index()
    {
        $vipCustomers = Customer::where('is_vip', true)
            ->with('vipPrices.product', 'vipPrices.variant')
            ->orderBy('created_at', 'desc')
            ->get();

        $regularCustomers = Customer::where('is_vip', false)
            ->orderBy('name')
            ->get();

        $products = Product::where('status', 1)
            ->orderBy('name')
            ->get();

        return view('admin.vip.index', compact('vipCustomers', 'regularCustomers', 'products'));
    }

    /**
     * Assign VIP to customer (Manual Prices Only)
     */
    public function assignVip(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'manual_product_ids' => 'required|array|min:1',
            'manual_product_ids.*' => 'exists:products,id',
            'expiry_date' => 'nullable|date|after:today'
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $customer = Customer::findOrFail($request->customer_id);

        try {
            DB::beginTransaction();

            // Update customer VIP settings (NO discount type/value)
            $customer->update([
                'is_vip' => true,
                'vip_discount_type' => null,
                'vip_discount_value' => null,
                'vip_apply_to' => 'manual_only',
                'vip_expiry_date' => $request->expiry_date
            ]);

            // Clear existing manual prices
            VipProductPrice::where('customer_id', $customer->id)->delete();

            // Create placeholder entries for selected products
            foreach ($request->manual_product_ids as $productId) {
                VipProductPrice::create([
                    'customer_id' => $customer->id,
                    'product_id' => $productId,
                    'product_variant_id' => null,
                    'vip_price' => 0 // Will be updated with manual price
                ]);
            }

            DB::commit();

            $message = __('admin.vip_assigned_successfully', [
                'name' => $customer->name
            ]);

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => $message]);
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => __('admin.Failed to assign VIP: ') . $e->getMessage()], 500);
            }

            return redirect()->back()->with('error', __('admin.Failed to assign VIP: ') . $e->getMessage());
        }
    }

    /**
     * Get customer edit data (AJAX)
     */
    public function getEditData($id)
    {
        try {
            $customer = Customer::findOrFail($id);
            $productIds = [];

            // Get products for both manual_only AND selected_products
            if ($customer->vip_apply_to == 'manual_only' || $customer->vip_apply_to == 'selected_products') {
                $productIds = VipProductPrice::where('customer_id', $customer->id)
                    ->whereNull('product_variant_id')
                    ->pluck('product_id')
                    ->unique() // Remove duplicates
                    ->values() // Reindex array
                    ->toArray();

            }

            return response()->json([
                'success' => true,
                'customer' => $customer,
                'productIds' => $productIds
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update VIP customer products (AJAX)
     */
    public function updateVip(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'exists:products,id',
            'expiry_date' => 'nullable|date'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $customer = Customer::findOrFail($id);

        try {
            DB::beginTransaction();

            $customer->update([
                'vip_expiry_date' => $request->expiry_date
            ]);

            // Clear existing product selections
            VipProductPrice::where('customer_id', $customer->id)->whereNull('product_variant_id')->delete();

            // Create placeholder entries for selected products
            foreach ($request->product_ids as $productId) {
                VipProductPrice::create([
                    'customer_id' => $customer->id,
                    'product_id' => $productId,
                    'product_variant_id' => null,
                    'vip_price' => 0
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', __('admin.VIP products updated successfully'));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Set manual price for product (AJAX)
     */
    public function setManualPrice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'product_id' => 'required|exists:products,id',
            'vip_price' => 'required|numeric|min:0',
            'variant_id' => 'nullable|exists:product_variants,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            // Check if manual price already exists
            $existingPrice = VipProductPrice::where('customer_id', $request->customer_id)
                ->where('product_id', $request->product_id)
                ->where('product_variant_id', $request->variant_id)
                ->first();

            if ($existingPrice) {
                $existingPrice->update(['vip_price' => $request->vip_price]);
                $price = $existingPrice;
            } else {
                $price = VipProductPrice::create([
                    'customer_id' => $request->customer_id,
                    'product_id' => $request->product_id,
                    'product_variant_id' => $request->variant_id,
                    'vip_price' => $request->vip_price
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => __('admin.Manual price set successfully'),
                'data' => $price
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get product regular price (AJAX) - No automatic discount
     */
    public function getProductPrice(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);
            $customerId = $request->get('customer_id');

            // Get base price
            $price = $product->offer_price ?? $product->price;

            // Check if there's a manual price for this customer
            if ($customerId) {
                $manualPrice = VipProductPrice::where('customer_id', $customerId)
                    ->where('product_id', $id)
                    ->whereNull('product_variant_id')
                    ->first();

                if ($manualPrice && $manualPrice->vip_price > 0) {
                    $price = $manualPrice->vip_price;
                }
            }

            return response()->json([
                'success' => true,
                'price' => number_format($price, 2),
                'original_price' => number_format($product->price, 2),
                'has_offer' => !is_null($product->offer_price),
                'offer_price' => $product->offer_price ? number_format($product->offer_price, 2) : null,
                'is_manual' => ($manualPrice && $manualPrice->vip_price > 0) ?? false
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get customer VIP prices (AJAX)
     */
    public function getCustomerPrices($id)
    {
        try {
            $customer = Customer::findOrFail($id);

            // Get all VIP prices for this customer
            $prices = VipProductPrice::where('customer_id', $customer->id)
                ->with(['product', 'variant'])
                ->where('vip_price', '>', 0) // Only get prices that are set
                ->get()
                ->map(function ($price) {
                    $regularPrice = $price->variant
                        ? ($price->variant->offer_price ?? $price->variant->price)
                        : ($price->product->offer_price ?? $price->product->price);

                    return [
                        'id' => $price->id,
                        'product_name' => $price->product->name,
                        'variant_sku' => $price->variant ? $price->variant->sku : null,
                        'vip_price' => number_format($price->vip_price, 2),
                        'regular_price' => number_format($regularPrice, 2),
                        'savings' => number_format($regularPrice - $price->vip_price, 2),
                        'savings_percentage' => round((($regularPrice - $price->vip_price) / $regularPrice) * 100)
                    ];
                });

            // Get products without prices (need to set prices)
            $productsWithoutPrices = VipProductPrice::where('customer_id', $customer->id)
                ->with(['product', 'variant'])
                ->where('vip_price', 0)
                ->get()
                ->map(function ($price) {
                    return [
                        'id' => $price->id,
                        'product_name' => $price->product->name,
                        'variant_sku' => $price->variant ? $price->variant->sku : null,
                        'regular_price' => number_format($price->variant
                            ? ($price->variant->offer_price ?? $price->variant->price)
                            : ($price->product->offer_price ?? $price->product->price), 2),
                        'status' => 'pending'
                    ];
                });

            return response()->json([
                'success' => true,
                'prices' => $prices,
                'pending_prices' => $productsWithoutPrices,
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'expiry_date' => $customer->vip_expiry_date
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove VIP from customer
     */
    public function removeVip($id)
    {

        $customer = Customer::findOrFail($id);

        try {
            DB::beginTransaction();

            $customer->update([
                'is_vip' => false,
                'vip_discount_type' => null,
                'vip_discount_value' => null,
                'vip_apply_to' => 'selected_products',
                'vip_expiry_date' => null
            ]);

            VipProductPrice::where('customer_id', $customer->id)->delete();

            DB::commit();

            return redirect()->back()->with('success', __('admin.VIP removed successfully from ') . $customer->name);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', __('admin.Failed to remove VIP: ') . $e->getMessage());
        }
    }

    /**
     * Get product variants (AJAX)
     */
    public function getProductVariants($id)
    {
        try {
            $product = Product::findOrFail($id);

            if ($product->has_variants) {
                $variants = $product->variants()
                    ->with('combinations.attributeValue')
                    ->where('status', 1)
                    ->get();

                $variantsList = $variants->map(function ($variant) {
                    $attributes = [];
                    foreach ($variant->combinations as $combo) {
                        if ($combo->attributeValue) {
                            $attributes[] = $combo->attributeValue->value;
                        }
                    }
                    $attributeText = implode(', ', $attributes);
                    $price = $variant->offer_price ?? $variant->price;

                    return [
                        'id' => $variant->id,
                        'text' => $variant->sku . ' - ' . ($attributeText ?: 'Default'),
                        'price' => number_format($price, 2),
                        'stock' => $variant->quantity,
                        'sku' => $variant->sku
                    ];
                });

                return response()->json(['success' => true, 'variants' => $variantsList]);
            }

            return response()->json(['success' => true, 'variants' => []]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete manual price (AJAX)
     */
    public function deleteManualPrice($id)
    {
        try {
            $price = VipProductPrice::findOrFail($id);
            $price->delete();

            return response()->json(['success' => true, 'message' => __('admin.Manual price deleted successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get customer products for manual pricing (based on apply_to)
     */
    /**
     * Get customer products for manual pricing (based on apply_to)
     */
    public function getCustomerProducts($id)
    {
        try {
            $customer = Customer::findOrFail($id);
            $products = [];

            if ($customer->vip_apply_to == 'manual_only') {
                // Show only products that have been assigned for manual pricing
                $productIds = VipProductPrice::where('customer_id', $customer->id)
                    ->whereNull('product_variant_id')
                    ->pluck('product_id')
                    ->unique(); // Ensure unique product IDs

                $products = Product::whereIn('id', $productIds)
                    ->where('status', 1)
                    ->orderBy('name')
                    ->get(['id', 'name', 'sku_number as sku']);
            } elseif ($customer->vip_apply_to == 'all') {
                // Show all active products
                $products = Product::where('status', 1)
                    ->orderBy('name')
                    ->get(['id', 'name', 'sku_number as sku']);
            } elseif ($customer->vip_apply_to == 'selected_products') {
                // Show only selected products from VIP configuration
                $productIds = VipProductPrice::where('customer_id', $customer->id)
                    ->whereNull('product_variant_id')
                    ->pluck('product_id')
                    ->unique();

                $products = Product::whereIn('id', $productIds)
                    ->where('status', 1)
                    ->orderBy('name')
                    ->get(['id', 'name', 'sku_number as sku']);
            }

            return response()->json([
                'success' => true,
                'products' => $products,
                'apply_to' => $customer->vip_apply_to
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}

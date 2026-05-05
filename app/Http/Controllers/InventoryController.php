<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class InventoryController extends Controller
{
    /**
     * Display a listing of the inventory.
     */
    public function index(Request $request)
    {
        $sortEntity = (new Inventory())->sortEntity;
        $sortOrder = (new Inventory())->sortOrder;

        $result = null;
        if ($request->ajax()) {
            $sortEntity = $request->sortEntity;
            $sortOrder = $request->sortOrder;

            $result = (new Inventory)->pagination($request);

            return view('admin.inventory.pagination', compact('result', 'sortOrder', 'sortEntity'));
        }
        $url = url()->full();
        return view('admin.inventory.index', compact('url', 'result', 'sortOrder', 'sortEntity'));
    }

    /**
     * Show form to add inventory (simple product)
     */
    public function create()
    {
        // Get simple products (has_variants = 0)
        $simpleProducts = Product::where('status', 1)
            ->where('has_variants', 0)
            ->get();

        // Get variant products (has_variants = 1)
        $variantProducts = Product::where('status', 1)
            ->where('has_variants', 1)
            ->get();

        return view('admin.inventory.create', compact('simpleProducts', 'variantProducts'));
    }
    /**
     * Store inventory transaction
     */
    public function store(Request $request)
    {
        $rules = [
            'stock_type' => 'required|in:in,out',
            'quantity' => 'required|integer|min:1'
        ];

        // Validate based on product type
        if ($request->has('product_id') && $request->product_id) {
            $rules['product_id'] = 'required|exists:products,id';
            $product = Product::find($request->product_id);

            if ($product->has_variants) {
                return back()->with('error', 'This product has variants. Please select a specific variant.');
            }
        } elseif ($request->has('variant_id') && $request->variant_id) {
            $rules['variant_id'] = 'required|exists:product_variants,id';
        } else {
            return back()->with('error', 'Please select either a product or a variant.');
        }

        $request->validate($rules);

        try {
            DB::beginTransaction();

            $currentStock = 0;
            $updatedStock = 0;
            $productId = null;
            $variantId = null;

            // Process simple product
            if ($request->has('product_id') && $request->product_id) {
                $product = Product::find($request->product_id);
                $currentStock = $product->quantity ?? 0;
                $productId = $product->id;

                if ($request->stock_type == 'in') {
                    $updatedStock = $currentStock + $request->quantity;
                } else {
                    if ($currentStock < $request->quantity) {
                        throw new \Exception('Insufficient stock available. Current stock: ' . $currentStock);
                    }
                    $updatedStock = $currentStock - $request->quantity;
                }

                // Update product stock
                $product->quantity = $updatedStock;
                $product->save();
            }

            // Process variant
            if ($request->has('variant_id') && $request->variant_id) {
                $variant = ProductVariant::find($request->variant_id);
                $currentStock = $variant->quantity;
                $variantId = $variant->id;
                $productId = $variant->product_id;

                if ($request->stock_type == 'in') {
                    $updatedStock = $currentStock + $request->quantity;
                } else {
                    if ($currentStock < $request->quantity) {
                        throw new \Exception('Insufficient stock available for variant ' . $variant->sku . '. Current stock: ' . $currentStock);
                    }
                    $updatedStock = $currentStock - $request->quantity;
                }

                // Update variant stock
                $variant->quantity = $updatedStock;
                $variant->save();

                // Update product price range
                $variant->product->updatePriceRange();
            }

            // Create inventory record
            Inventory::create([
                'product_id' => $productId,
                'product_variant_id' => $variantId,
                'stock_type' => $request->stock_type,
                'available_stock' => $currentStock,
                'quantity' => $request->quantity,
                'updated_stock' => $updatedStock,
                'notes' => $request->notes,
                'reference_type' => $request->reference_type,
                'reference_id' => $request->reference_id
            ]);

            DB::commit();

            return redirect()->route('inventory.create')->with('success', 'Inventory updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show inventory for a specific product/variant
     */
    public function show($id)
    {
        $inventory = Inventory::with(['product', 'variant.product'])->findOrFail($id);
        return view('admin.inventory.show', compact('inventory'));
    }

    /**
     * Get product variants for AJAX
     */
    public function getVariants(Request $request)
    {
        $productId = $request->product_id;
        $product = Product::find($productId);

        if (!$product || !$product->has_variants) {
            return response()->json([
                'success' => false,
                'message' => 'No variants found for this product'
            ]);
        }

        $variants = ProductVariant::where('product_id', $productId)
            ->with('combinations.attributeValue')
            ->get();

        if ($variants->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No variants found'
            ]);
        }

        $options = '<option value="">Select a variant</option>';
        foreach ($variants as $variant) {
            $attributes = [];
            foreach ($variant->combinations as $combination) {
                $attributes[] = $combination->attributeValue->value;
            }
            $attributeText = !empty($attributes) ? ' (' . implode(', ', $attributes) . ')' : '';
            $stockStatus = $variant->quantity > 0 ? 'In Stock: ' . $variant->quantity : 'Out of Stock';

            $options .= '<option value="' . $variant->id . '" data-stock="' . $variant->quantity . '" data-price="' . $variant->price . '">';
            $options .= $variant->sku . $attributeText . ' - ' . $stockStatus;
            $options .= '</option>';
        }

        return response()->json([
            'success' => true,
            'options' => $options,
            'variants_count' => $variants->count()
        ]);
    }

    /**
     * Get stock info for AJAX
     */
    public function getStockInfo(Request $request)
    {
        if ($request->has('variant_id') && $request->variant_id) {
            $variant = ProductVariant::find($request->variant_id);
            if ($variant) {
                return response()->json([
                    'success' => true,
                    'current_stock' => $variant->quantity,
                    'price' => $variant->price,
                    'sku' => $variant->sku
                ]);
            }
        }

        if ($request->has('product_id') && $request->product_id) {
            $product = Product::find($request->product_id);
            if ($product) {
                // For simple products, return the quantity from products table
                // For variant products, return the sum of all variant quantities
                if ($product->has_variants) {
                    $totalStock = $product->variants()->sum('quantity');
                    return response()->json([
                        'success' => true,
                        'current_stock' => $totalStock,
                        'price' => $product->min_price,
                        'sku' => 'VARIOUS',
                        'has_variants' => true
                    ]);
                } else {
                    return response()->json([
                        'success' => true,
                        'current_stock' => $product->quantity ?? 0,
                        'price' => $product->price,
                        'sku' => $product->sku_number,
                        'has_variants' => false
                    ]);
                }
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Product or variant not found'
        ]);
    }
}

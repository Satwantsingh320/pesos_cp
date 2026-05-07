<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

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
     * Show form for multiple upload inventory
     */
    public function createMultiple()
    {
        // Get simple products (has_variants = 0)
        $simpleProducts = Product::where('status', 1)
            ->where('has_variants', 0)
            ->get();

        // Get variant products (has_variants = 1)
        $variantProducts = Product::where('status', 1)
            ->where('has_variants', 1)
            ->get();

        return view('admin.inventory.create-multiple', compact('simpleProducts', 'variantProducts'));
    }

    /**
     * Store multiple inventory transactions
     */
    public function storeMultiple(Request $request)
    {
        $rules = [
            'inventory_items' => 'required|array|min:1',
            'inventory_items.*.stock_type' => 'required|in:in,out',
            'inventory_items.*.quantity' => 'required|integer|min:1'
        ];

        $request->validate($rules);

        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            foreach ($request->inventory_items as $index => $item) {
                try {
                    // Validate item type
                    if (isset($item['product_id']) && $item['product_id']) {
                        $product = Product::find($item['product_id']);
                        if (!$product) {
                            throw new \Exception("Product not found for item #" . ($index + 1));
                        }

                        if ($product->has_variants) {
                            throw new \Exception("Item #" . ($index + 1) . ": This product has variants. Please select a specific variant.");
                        }

                        $currentStock = $product->quantity ?? 0;
                        $productId = $product->id;
                        $variantId = null;

                        if ($item['stock_type'] == 'in') {
                            $updatedStock = $currentStock + $item['quantity'];
                        } else {
                            if ($currentStock < $item['quantity']) {
                                throw new \Exception("Item #" . ($index + 1) . ": Insufficient stock. Current stock: " . $currentStock);
                            }
                            $updatedStock = $currentStock - $item['quantity'];
                        }

                        // Update product stock
                        $product->quantity = $updatedStock;
                        $product->save();

                    } elseif (isset($item['variant_id']) && $item['variant_id']) {
                        $variant = ProductVariant::find($item['variant_id']);
                        if (!$variant) {
                            throw new \Exception("Variant not found for item #" . ($index + 1));
                        }

                        $currentStock = $variant->quantity;
                        $productId = $variant->product_id;
                        $variantId = $variant->id;

                        if ($item['stock_type'] == 'in') {
                            $updatedStock = $currentStock + $item['quantity'];
                        } else {
                            if ($currentStock < $item['quantity']) {
                                throw new \Exception("Item #" . ($index + 1) . ": Insufficient stock for variant. Current stock: " . $currentStock);
                            }
                            $updatedStock = $currentStock - $item['quantity'];
                        }

                        // Update variant stock
                        $variant->quantity = $updatedStock;
                        $variant->save();

                        // Update product price range
                        $variant->product->updatePriceRange();

                    } else {
                        throw new \Exception("Item #" . ($index + 1) . ": Please select either a product or a variant.");
                    }

                    // Create inventory record
                    Inventory::create([
                        'product_id' => $productId,
                        'product_variant_id' => $variantId,
                        'stock_type' => $item['stock_type'],
                        'available_stock' => $currentStock,
                        'quantity' => $item['quantity'],
                        'updated_stock' => $updatedStock,
                        'notes' => $item['notes'] ?? null,
                        'reference_type' => 'bulk_upload',
                        'reference_id' => null
                    ]);

                    $successCount++;

                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = $e->getMessage();
                }
            }

            DB::commit();

            $message = "Successfully processed {$successCount} inventory transactions.";
            if ($errorCount > 0) {
                $message .= " Failed: {$errorCount}. " . implode("; ", $errors);
                return redirect()->route('inventory.create.multiple')->with('warning', $message);
            }

            return redirect()->route('inventory.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to process inventory: ' . $e->getMessage());
        }
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

    /**
     * Get variant details for bulk upload
     */
    public function getVariantDetails(Request $request)
    {
        $variantId = $request->variant_id;
        $variant = ProductVariant::with('combinations.attributeValue', 'product')->find($variantId);

        if (!$variant) {
            return response()->json([
                'success' => false,
                'message' => 'Variant not found'
            ]);
        }

        $attributes = [];
        foreach ($variant->combinations as $combination) {
            $attributes[] = $combination->attributeValue->value;
        }

        return response()->json([
            'success' => true,
            'variant' => [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'current_stock' => $variant->quantity,
                'price' => $variant->price,
                'attributes' => implode(', ', $attributes),
                'product_name' => $variant->product->name
            ]
        ]);
    }

    /**
     * Parse CSV file for bulk upload
     */
    public function parseCSV(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getPathname(), 'r');

        $data = [];
        $headers = fgetcsv($handle); // Skip headers

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 5) {
                $data[] = [
                    'product_type' => trim($row[0]),
                    'product_id' => trim($row[1]),
                    'variant_id' => trim($row[2]),
                    'stock_type' => trim($row[3]),
                    'quantity' => (int) trim($row[4]),
                    'notes' => isset($row[5]) ? trim($row[5]) : null
                ];
            }
        }

        fclose($handle);

        return response()->json([
            'success' => true,
            'data' => $data,
            'count' => count($data)
        ]);
    }
}

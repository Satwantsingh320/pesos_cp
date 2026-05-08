<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\ProductGallery;
use App\Models\ProductVariant;
use App\Models\Inventory;
use App\Models\ProductVariantCombination;
use App\Models\Product;
use App\Models\SubCategory;
use App\Models\VariantAttribute;
use App\Models\VariantAttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sortEntity = (new Product())->sortEntity;
        $sortOrder = (new Product())->sortOrder;
        $result = null;
        if ($request->ajax()) {
            $sortEntity = $request->sortEntity;
            $sortOrder = $request->sortOrder;

            $result = (new Product)->pagination($request);

            return view('admin.products.pagination', compact('result', 'sortOrder', 'sortEntity'));
        }
        $url = url()->full();
        return view('admin.products.index', compact('url', 'result', 'sortOrder', 'sortEntity'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = (new Category())->service();
        $brands = (new Brand())->service();
        $attributes = VariantAttribute::where('status', 1)->with('values')->get();

        return view('admin.products.create', compact('categories', 'brands', 'attributes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $inputs = $request->all();
        $rules = [
            'category' => 'required|integer|exists:categories,id',
            'subcategory' => 'required|integer|exists:sub_categories,id',
            'brand' => 'required|integer|exists:brands,id',
            'name' => 'required|string',
            'shipping_fee' => 'required|numeric|min:0',
            'return_days' => 'required|numeric|min:0',
            'description' => 'nullable',
            'type' => 'required',
            'estimated_delivery_time' => 'required|numeric',
            'status' => 'in:0,1',
            'cover_image' => 'required|mimes:jpg,jpeg,png,svg',
            'gallery_images' => 'required|array|min:1',
            'gallery_images.*' => 'mimes:jpg,jpeg,png,svg',
            'is_special_offer' => 'sometimes|boolean',
            'is_clearance' => 'sometimes|boolean',
            'is_featured' => 'sometimes|boolean',
            'has_variants' => 'sometimes|boolean',
            'low_stock_threshold' => 'nullable',
        ];

        // Variant validation if product has variants
        if ($request->has('has_variants')) {
            $rules['variants'] = 'required|array|min:1';
            $rules['variants.*.sku'] = 'required|string|distinct';
            $rules['variants.*.price'] = 'required|numeric|min:0';
            $rules['variants.*.quantity'] = 'required|integer|min:0';
            $rules['variants.*.image'] = 'nullable|mimes:jpg,jpeg,png,svg';
        } else {
            // Simple product validation
            $rules['price'] = 'required|numeric|min:1';
            //$rules['offer_price'] = 'nullable|numeric|lt:price';
            $rules['sku_number'] = 'required|unique:products,sku_number';
            $rules['barcode_number'] = 'required';
            $rules['pieces_available'] = 'required|numeric|min:0';
        }

        $validation = validator($inputs, $rules);
        if ($validation->fails()) {
            return back()->withErrors($validation->getMessageBag())->withInput();
        }

        try {
            DB::beginTransaction();

            // Upload cover image
            $coverImg = null;
            $path = public_path(PRODUCTS_PATH);
            if ($request->hasFile('cover_image')) {
                $file = $request->file('cover_image');
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($path, $fileName);
                $coverImg = $fileName;
            }

            // Create product
            $productData = [
                'category_id' => $inputs['category'],
                'subcategory_id' => $inputs['subcategory'],
                'brand_id' => $inputs['brand'],
                'name' => $inputs['name'],
                'shipping_fee' => $inputs['shipping_fee'],
                'return_days' => $inputs['return_days'],
                'description' => $inputs['description'],
                'estimated_delivery_time' => $inputs['estimated_delivery_time'],
                'status' => $inputs['status'],
                'cover_image' => $coverImg,
                'is_special_offer' => $request->has('is_special_offer'),
                'is_clearance' => $request->has('is_clearance'),
                'low_stock_threshold' => $request->has('low_stock_threshold'),
                'is_featured' => $request->has('is_featured'),
                'slug' => $inputs['slug'],
                'type' => $inputs['type'],
                'has_variants' => $request->has('has_variants'),
            ];

            // Handle simple product data
            if (!$request->has('has_variants')) {
                $productData['price'] = $inputs['price'];
                $productData['offer_price'] = $inputs['offer_price'] ?? $inputs['price'];
                $productData['quantity'] = $inputs['pieces_available'];
                $productData['sku_number'] = $inputs['sku_number'];
                $productData['barcode_number'] = $inputs['barcode_number'];
            }

            $product = Product::create($productData);

            // Create variants if product has variants
            if ($request->has('has_variants') && $request->has('variants')) {
                $this->createVariants($product, $request->variants);
                // Update price range
                $product->updatePriceRange();
            }

            // Gallery Images
            if ($request->hasFile('gallery_images')) {
                foreach ($request->file('gallery_images') as $image) {
                    $file = $image;
                    $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move($path, $fileName);
                    ProductGallery::create([
                        'product_id' => $product->id,
                        'image' => $fileName,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('products.index')->with('success', __('admin.product_created_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Create variants for a product with images
     */
    private function createVariants(Product $product, array $variants)
    {
        $variantPath = public_path(PRODUCTS_PATH . 'variants/');

        // Create variants directory if not exists
        if (!File::exists($variantPath)) {
            File::makeDirectory($variantPath, 0777, true);
        }

        foreach ($variants as $index => $variantData) {
            // Handle variant image upload
            $variantImage = null;
            if (isset($variantData['image']) && $variantData['image'] instanceof \Illuminate\Http\UploadedFile) {
                $file = $variantData['image'];
                $fileName = time() . '_' . uniqid() . '_variant.' . $file->getClientOriginalExtension();
                $file->move($variantPath, $fileName);
                $variantImage = 'variants/' . $fileName;
            }

            $variant = ProductVariant::create([
                'product_id' => $product->id,
                'sku' => $variantData['sku'],
                'barcode' => $variantData['barcode'] ?? null,
                'price' => $variantData['price'],
                'offer_price' => $variantData['offer_price'] ?? null,
                'quantity' => $variantData['quantity'],
                'low_stock_threshold' => $variantData['low_stock_threshold'] ?? 5,
                'status' => $variantData['status'] ?? 1,
                'position' => $index,
                'image' => $variantImage,
            ]);

            // Save variant combinations (attributes)
            if (isset($variantData['attributes']) && is_array($variantData['attributes'])) {
                foreach ($variantData['attributes'] as $attributeId => $valueId) {
                    ProductVariantCombination::create([
                        'product_variant_id' => $variant->id,
                        'attribute_id' => $attributeId,
                        'attribute_value_id' => $valueId,
                    ]);
                }
            }
        }
    }

    /**
     * Show the specified resource.
     */
    public function show(Product $product)
    {
        $product->load('gallery', 'category', 'subcategory', 'brands', 'inventory', 'variants.combinations.attributeValue');

        // Get simple products for dropdown (if needed)
        $simpleProducts = Product::where('status', 1)
            ->where('has_variants', 0)
            ->get();

        // Get variant products for dropdown (if needed)
        $variantProducts = Product::where('status', 1)
            ->where('has_variants', 1)
            ->get();

        $categories = (new Category())->service();

        // Get recent inventory activities for this product
        $recentInventory = Inventory::where('product_id', $product->id)
            ->with('variant')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Calculate total stock for product (including variants if applicable)
        $totalStock = $product->has_variants
            ? $product->variants->sum('quantity')
            : $product->quantity;

        // Get low stock variants (for variant products)
        $lowStockVariants = collect();
        if ($product->has_variants) {
            $lowStockVariants = $product->variants()
                ->where('quantity', '<=', 10)
                ->with('combinations.attributeValue')
                ->get();
        }

        return view('admin.products.show', compact(
            'product',
            'categories',
            'simpleProducts',
            'variantProducts',
            'recentInventory',
            'totalStock',
            'lowStockVariants'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $product->load('category', 'subcategory', 'brands', 'gallery', 'variants.combinations.attributeValue', 'variants');
        $categories = (new Category())->service();
        $brands = (new Brand())->service();
        $attributes = VariantAttribute::where('status', 1)->with('values')->get();

        return view('admin.products.edit', compact('product', 'categories', 'brands', 'attributes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $rules = [
            'category' => 'required|integer|exists:categories,id',
            'subcategory' => 'required|integer|exists:sub_categories,id',
            'brand' => 'required|integer|exists:brands,id',
            'name' => 'required|string',
            'shipping_fee' => 'required|numeric|min:0',
            'description' => 'nullable',
            'estimated_delivery_time' => 'required|numeric',
            'status' => 'required|in:0,1',
            'cover_image' => 'nullable|mimes:jpg,jpeg,png,svg',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'mimes:jpg,jpeg,png,svg',
            'is_special_offer' => 'sometimes|boolean',
            'is_clearance' => 'sometimes|boolean',
            'is_featured' => 'sometimes|boolean',
            'has_variants' => 'sometimes',
            'low_stock_threshold' => 'nullable',
        ];

        // Variant validation
        if ($request->has('has_variants') && $request->has_variants == '1') {
            $rules['variants'] = 'required|array|min:1';
            $rules['variants.*.sku'] = 'required|string|distinct';
            $rules['variants.*.price'] = 'required|numeric|min:0';
            $rules['variants.*.quantity'] = 'required|integer|min:0';
            $rules['variants.*.image'] = 'nullable|mimes:jpg,jpeg,png,svg';
        } else {
            $rules['price'] = 'required|numeric|min:1';
            //$rules['offer_price'] = 'nullable|numeric|lt:price';
            $rules['sku_number'] = 'required|unique:products,sku_number,' . $product->id;
            $rules['barcode_number'] = 'required';
            $rules['pieces_available'] = 'required|numeric|min:0';
        }

        $request->validate($rules);

        try {
            DB::beginTransaction();

            // Update product basic info
            $product->category_id = $request->category;
            $product->subcategory_id = $request->subcategory;
            $product->brand_id = $request->brand;
            $product->name = $request->name;
            $product->shipping_fee = $request->shipping_fee;
            $product->description = $request->description;
            $product->estimated_delivery_time = $request->estimated_delivery_time;
            $product->status = $request->status;
            $product->low_stock_threshold = $request->low_stock_threshold;
            $product->is_special_offer = $request->has('is_special_offer');
            $product->is_clearance = $request->has('is_clearance');
            $product->is_featured = $request->has('is_featured');
            $product->slug = $request->slug;
            $product->type = $request->type;
            $product->has_variants = $request->has_variants;

            // Handle simple product data
            if ($request->has_variants == 0) {
                $product->price = $request->price;
                $product->offer_price = $request->offer_price ?? $request->price;
                $product->quantity = $request->pieces_available;
                $product->sku_number = $request->sku_number;
                $product->barcode_number = $request->barcode_number;
            } else {
                // Clear simple product fields if switching to variant product
                $product->price = null;
                $product->offer_price = null;
                $product->quantity = null;
                $product->sku_number = null;
                $product->barcode_number = null;
            }

            // Cover image
            if ($request->hasFile('cover_image')) {
                $path = public_path(PRODUCTS_PATH);
                $file = $request->file('cover_image');
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($path, $fileName);

                // Delete old cover
                if ($product->cover_image && file_exists($path . $product->cover_image)) {
                    unlink($path . $product->cover_image);
                }
                $product->cover_image = $fileName;
            }

            $product->save();

            // Handle variants
            if ($request->has_variants == 1) {
                $this->updateVariants($product, $request->variants);
            } else {
                // Delete all variants if product is no longer variant type
                $this->deleteAllVariants($product);
                $product->min_price = null;
                $product->max_price = null;
                $product->save();
            }

            // Remove gallery images
            if ($request->removed_gallery_images) {
                $ids = explode(',', $request->removed_gallery_images);
                ProductGallery::whereIn('id', $ids)->delete();
            }

            // Add new gallery images
            if ($request->hasFile('gallery_images')) {
                foreach ($request->file('gallery_images') as $image) {
                    $fileName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path(PRODUCTS_PATH), $fileName);

                    ProductGallery::create([
                        'product_id' => $product->id,
                        'image' => $fileName,
                    ]);
                }
            }

            DB::commit();

            return redirect()->back()->with('success', __('admin.product_updated_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update variants with image handling
     */
    private function updateVariants(Product $product, array $variants)
    {
        $variantPath = public_path(PRODUCTS_PATH . 'variants/');

        // Create variants directory if not exists
        if (!File::exists($variantPath)) {
            File::makeDirectory($variantPath, 0777, true);
        }

        // Get existing variant IDs
        $existingVariantIds = $product->variants->pluck('id')->toArray();
        $updatedVariantIds = [];

        foreach ($variants as $index => $variantData) {
            // Handle variant image upload
            $variantImage = null;

            // Check if new image is uploaded
            if (isset($variantData['image']) && $variantData['image'] instanceof \Illuminate\Http\UploadedFile) {
                $file = $variantData['image'];
                $fileName = time() . '_' . uniqid() . '_variant.' . $file->getClientOriginalExtension();
                $file->move($variantPath, $fileName);
                $variantImage = 'variants/' . $fileName;
            }
            // Check if existing image is kept
            elseif (isset($variantData['existing_image']) && $variantData['existing_image']) {
                $variantImage = $variantData['existing_image'];
            }
            // Check if image should be removed
            elseif (isset($variantData['remove_image']) && $variantData['remove_image'] == '1') {
                $variantImage = null;
            }

            if (isset($variantData['id']) && $variantData['id']) {
                // Update existing variant
                $variant = ProductVariant::find($variantData['id']);
                if ($variant && $variant->product_id == $product->id) {
                    // Delete old image if new image is uploaded
                    if ($variantImage && $variant->image && $variantImage != $variant->image) {
                        $oldImagePath = public_path(PRODUCTS_PATH . $variant->image);
                        if (File::exists($oldImagePath)) {
                            File::delete($oldImagePath);
                        }
                    }

                    $variant->update([
                        'sku' => $variantData['sku'],
                        'barcode' => $variantData['barcode'] ?? null,
                        'price' => $variantData['price'],
                        'offer_price' => $variantData['offer_price'] ?? null,
                        'quantity' => $variantData['quantity'],
                        'low_stock_threshold' => $variantData['low_stock_threshold'] ?? 5,
                        'status' => $variantData['status'] ?? 1,
                        'position' => $index,
                        'image' => $variantImage,
                    ]);
                    $updatedVariantIds[] = $variant->id;

                    // Update combinations
                    ProductVariantCombination::where('product_variant_id', $variant->id)->delete();
                    if (isset($variantData['attributes']) && is_array($variantData['attributes'])) {
                        foreach ($variantData['attributes'] as $attributeId => $valueId) {
                            ProductVariantCombination::create([
                                'product_variant_id' => $variant->id,
                                'attribute_id' => $attributeId,
                                'attribute_value_id' => $valueId,
                            ]);
                        }
                    }
                }
            } else {
                // Create new variant
                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => $variantData['sku'],
                    'barcode' => $variantData['barcode'] ?? null,
                    'price' => $variantData['price'],
                    'offer_price' => $variantData['offer_price'] ?? null,
                    'quantity' => $variantData['quantity'],
                    'low_stock_threshold' => $variantData['low_stock_threshold'] ?? 5,
                    'status' => $variantData['status'] ?? 1,
                    'position' => $index,
                    'image' => $variantImage,
                ]);
                $updatedVariantIds[] = $variant->id;

                if (isset($variantData['attributes']) && is_array($variantData['attributes'])) {
                    foreach ($variantData['attributes'] as $attributeId => $valueId) {
                        ProductVariantCombination::create([
                            'product_variant_id' => $variant->id,
                            'attribute_id' => $attributeId,
                            'attribute_value_id' => $valueId,
                        ]);
                    }
                }
            }
        }

        // Delete variants that were removed
        $variantsToDelete = array_diff($existingVariantIds, $updatedVariantIds);
        if (!empty($variantsToDelete)) {
            foreach ($variantsToDelete as $variantId) {
                $variant = ProductVariant::find($variantId);
                if ($variant) {
                    // Delete variant image
                    if ($variant->image) {
                        $imagePath = public_path(PRODUCTS_PATH . $variant->image);
                        if (File::exists($imagePath)) {
                            File::delete($imagePath);
                        }
                    }
                    $variant->delete();
                }
            }
        }

        // Update price range
        $product->updatePriceRange();
    }

    /**
     * Delete all variants and their images
     */
    private function deleteAllVariants(Product $product)
    {
        foreach ($product->variants as $variant) {
            // Delete variant image
            if ($variant->image) {
                $imagePath = public_path(PRODUCTS_PATH . $variant->image);
                if (File::exists($imagePath)) {
                    File::delete($imagePath);
                }
            }
            $variant->delete();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            DB::beginTransaction();

            // Delete cover image
            if ($product->cover_image) {
                $coverPath = public_path(PRODUCTS_PATH . $product->cover_image);
                if (File::exists($coverPath)) {
                    File::delete($coverPath);
                }
            }

            // Delete variant images
            foreach ($product->variants as $variant) {
                if ($variant->image) {
                    $variantImagePath = public_path(PRODUCTS_PATH . $variant->image);
                    if (File::exists($variantImagePath)) {
                        File::delete($variantImagePath);
                    }
                }
            }

            // Delete gallery images
            if ($product->gallery) {
                foreach ($product->gallery as $gallery) {
                    $galleryPath = public_path(PRODUCTS_PATH . $gallery->image);
                    if (File::exists($galleryPath)) {
                        File::delete($galleryPath);
                    }
                }
                $product->gallery()->delete();
            }

            // Variants will be deleted automatically due to cascade
            $product->inventory()->delete();
            $product->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'status' => 201,
                'message' => __('admin.product_deleted_successfully'),
                'extra' => ['redirect' => route('products.index')]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Variant management methods
     */
    public function getVariantCombinations(Request $request)
    {
        $attributes = $request->get('attributes', []);
        $combinations = $this->generateCombinations($attributes);

        return response()->json(['combinations' => $combinations]);
    }

    private function generateCombinations($attributes)
    {
        $combinations = [[]];

        foreach ($attributes as $attributeId => $values) {
            $newCombinations = [];
            foreach ($combinations as $combination) {
                foreach ($values as $valueId) {
                    $newCombinations[] = array_merge($combination, [
                        $attributeId => $valueId
                    ]);
                }
            }
            $combinations = $newCombinations;
        }

        return $combinations;
    }

    /**
     * Get variant details by SKU (for AJAX)
     */
    public function getVariantBySku(Request $request)
    {
        $sku = $request->get('sku');
        $variant = ProductVariant::where('sku', $sku)->with('product')->first();

        if ($variant) {
            return response()->json([
                'success' => true,
                'data' => [
                    'price' => $variant->price,
                    'offer_price' => $variant->offer_price,
                    'quantity' => $variant->quantity,
                    'product_name' => $variant->product->name,
                    'image' => $variant->image ? asset(PRODUCTS_PATH . $variant->image) : null
                ]
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Variant not found']);
    }

    public function toggleAllStatus($status, Request $request)
    {
        $inputs = $request->only('ids');

        try {
            DB::beginTransaction();
            (new Product())->toggleStatus($status, $inputs['ids']);
            DB::commit();
            return response()->json(['success' => true, 'status' => 201, 'message' => __('admin.status_updated_successfully'), 'extra' => ['redirect' => route('products.index')]]);
        } catch (\Exception $e) {
            DB::rollBack();
            return jsonResponse(false, 207, __('admin.server_error'));
        }
    }

    public function status($id)
    {
        $result = Product::findorFail($id);
        if (!$result) {
            $message = __('admin.invalid_detail');
            return jsonResponse(false, 207, $message);
        }

        try {
            DB::beginTransaction();
            $result->update(['status' => !$result->status]);
            DB::commit();
            return response()->json(['success' => true, 'status' => 201, 'message' => __('admin.status_updated_successfully'), 'extra' => ['redirect' => route('products.index')]]);
        } catch (\Exception $e) {
            DB::rollBack();
            return jsonResponse(false, 207, __('admin.server_error'));
        }
    }

    public function service(Request $request)
    {
        $inputs = $request->all();
        $rules = [
            'subcategory_id' => 'required|numeric|min:1'
        ];
        $validation = validator($inputs, $rules);
        if ($validation->fails()) {
            return jsonResponse(false, 206, $validation->getMessageBag());
        }

        $title = $inputs['title'] ?? __('admin.select');
        $options = '<option value="" selected disabled>' . __('admin.select') . '</option>';

        $result = (new Product())->service(false, $title, ['subcategory_id' => $inputs['subcategory_id']]);
        if (isset($result) && count($result) > 0) {
            foreach ($result as $key => $option) {
                $options .= '<option value="' . $key . '">' . $option . '</option>';
            }
        }

        return response()->json(['success' => true, 'options' => $options]);
    }

    public function getPrice(Request $request)
    {
        $inputs = $request->all();
        $rules = [
            'product_id' => 'required|numeric|min:1'
        ];
        $validation = validator($inputs, $rules);
        if ($validation->fails()) {
            return jsonResponse(false, 206, $validation->getMessageBag());
        }

        $productId = $inputs['product_id'];
        $product = Product::find($productId);

        if ($product->has_variants) {
            $minPrice = $product->variants()->min('price');
            $maxPrice = $product->variants()->max('price');
            $price = $minPrice == $maxPrice ? $minPrice : $minPrice . ' - ' . $maxPrice;
        } else {
            $price = $product->price;
        }

        return response()->json(['success' => true, 'price' => $price]);
    }

    public function updateInventory(Request $request)
    {
        $inputs = $request->all();
        $rules = [
            'item_type' => 'required|in:simple,variant',
            'stock_type' => 'required|in:in,out',
            'quantity' => 'required|numeric|min:1'
        ];

        // Add conditional validation based on item type
        if ($inputs['item_type'] == 'simple') {
            $rules['product_id'] = 'required|numeric|min:1|exists:products,id';
        } else {
            $rules['variant_id'] = 'required|numeric|min:1|exists:product_variants,id';
        }

        $validation = validator($inputs, $rules);
        if ($validation->fails()) {
            return jsonResponse(false, 206, $validation->getMessageBag());
        }

        try {
            DB::beginTransaction();

            $updatedStock = 0;
            $currentStock = 0;
            $productId = null;
            $variantId = null;

            // Handle Simple Product
            if ($inputs['item_type'] == 'simple') {
                $productId = $inputs['product_id'];
                $product = Product::find($productId);

                // Check if product has variants
                if ($product->has_variants) {
                    throw new \Exception('This product has variants. Please manage inventory through variants.');
                }

                $currentStock = $product->quantity ?? 0;

                if ($inputs['stock_type'] == 'in') {
                    $updatedStock = $currentStock + $inputs['quantity'];
                } else {
                    if ($currentStock < $inputs['quantity']) {
                        throw new \Exception('Insufficient stock available. Current stock: ' . $currentStock);
                    }
                    $updatedStock = $currentStock - $inputs['quantity'];
                }

                // Update product stock
                $product->quantity = $updatedStock;
                $product->save();

            }
            // Handle Variant Product
            else {
                $variantId = $inputs['variant_id'];
                $variant = ProductVariant::find($variantId);
                $productId = $variant->product_id;
                $currentStock = $variant->quantity ?? 0;

                if ($inputs['stock_type'] == 'in') {
                    $updatedStock = $currentStock + $inputs['quantity'];
                } else {
                    if ($currentStock < $inputs['quantity']) {
                        throw new \Exception('Insufficient stock available for variant. Current stock: ' . $currentStock);
                    }
                    $updatedStock = $currentStock - $inputs['quantity'];
                }

                // Update variant stock
                $variant->quantity = $updatedStock;
                $variant->save();

                // Update parent product price range
                $variant->product->updatePriceRange();
            }

            // Create inventory record
            Inventory::create([
                'product_id' => $productId,
                'product_variant_id' => $variantId,
                'stock_type' => $inputs['stock_type'],
                'available_stock' => $currentStock,
                'quantity' => $inputs['quantity'],
                'updated_stock' => $updatedStock,
                'notes' => $inputs['notes'] ?? null,
                'reference_type' => 'manual',
                'reference_id' => null
            ]);

            DB::commit();

            // Return different responses based on request type
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => __('admin.inventory_updated_successfully'),
                    'updated_stock' => $updatedStock,
                    'current_stock' => $updatedStock
                ]);
            }

            return redirect()->back()->with('success', __('admin.inventory_updated_successfully'));

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Delete variant image
     */
    public function deleteVariantImage($variantId)
    {
        try {
            $variant = ProductVariant::findOrFail($variantId);

            if ($variant->image) {
                $imagePath = public_path(PRODUCTS_PATH . $variant->image);
                if (File::exists($imagePath)) {
                    File::delete($imagePath);
                }
                $variant->image = null;
                $variant->save();

                return response()->json([
                    'success' => true,
                    'message' => __('admin.image_deleted_successfully')
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => __('admin.image_not_found')
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

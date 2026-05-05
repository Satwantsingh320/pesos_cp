<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\ProductQuestion;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request, ProductService $productService)
    {
        return $this->renderProducts($productService, $request);
    }
    //display category products
    public function category($slug, Request $request, ProductService $productService)
    {
        return $this->renderProducts($productService, $request, ['category_slug' => $slug]);
    }
    //brand products
    public function brand($slug, Request $request, ProductService $productService)
    {
        return $this->renderProducts($productService, $request, ['brand_slug' => $slug]);
    }
    protected function renderProducts(
        ProductService $service,
        Request $request,
        array $context = []
    ) {
        $products = $service->getProducts($request->all(), $context);
        if ($request->ajax()) {
            return view('website.partials.product-list', compact('products'))->render();
        }
        $categories = Category::active()->get();
        $brands = Brand::active()->get();
        $activeCategoryId = null;
        if (!empty($context['category_slug'])) {
            $activeCategoryId = Category::where('slug', $context['category_slug'])->value('id');
        }
        return view('website.products', compact('categories', 'brands', 'products', 'activeCategoryId'));
    }
    //display search result
    public function search(Request $request, ProductService $service)
    {
        $products = $service->getProducts([], [], $request->keyword);
        //when requested search is not empty
        $keyword = $request->keyword;
        $total = $products->total();
        return view('website.search', compact('products', 'keyword', 'total'));
    }
    //product detail
    public function productDetail(string $slug)
    {
        $product = Product::with([
            'questions' => function ($q) {
                $q->where('is_approved', 1)
                    ->with([
                        'user:id,name',
                        'answers.user:id,name'
                    ])
                    ->latest();
            }
        ])
            ->where('slug', $slug)->firstOrFail();
        return view('website.product-detail', compact('product'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'question' => ['required', 'string', 'max:1000'],
        ]);

        $userId = auth('customer')->id();

        if (!empty($userId)) {
            // Check if user already has a pending question
            $hasPendingQuestion = ProductQuestion::where('product_id', $validated['product_id'])
                ->where('user_id', $userId)
                ->where('is_approved', false)
                ->exists();

            if ($hasPendingQuestion) {
                return back()
                    ->withInput()
                    ->with('error', __('admin.You already asked a question for this product. Please wait until it is approved.'));
            }
        }


        ProductQuestion::create([
            'product_id' => $validated['product_id'],
            'user_id' => $userId,
            'question' => $validated['question'],
            'is_approved' => false,
        ]);

        return back()->with('success', __('admin.Question submitted successfully'));
    }

}

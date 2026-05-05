<?php

namespace App\Services;

use App\Models\Product;

class ProductService
{
    public function getProducts(array $filters = [], array $context = [], ?string $keyword = null)
    {
        $query = Product::query()
            ->active();
        //seo routes context filters - category
        if (!empty($context['category_slug'])) {
            $query->whereHas(
                'category',
                fn($q) =>
                $q->where('slug', $context['category_slug'])
            );
        }
        //seo routes context filters - brand
        if (!empty($context['brand_slug'])) {
            $query->whereHas(
                'brands',
                fn($q) =>
                $q->where('slug', $context['brand_slug'])
            );
        }

        //category filter
        if (!empty($filters['categories'])) {
            $query->whereIn('category_id', (array) $filters['categories']);
        }
        //brand filter
        if (!empty($filters['brands'])) {
            $query->whereIn('brand_id', (array) $filters['brands']);
        }
        //special offers filter
        if (!empty($filters['speacial_offers'])) {
            $query->where('is_special_offer', 1);
        }
        //price filter
        if (!empty($filters['price_range'])) {
            match ($filters['price_range']) {
                '0-200' => $query->whereBetween('price', [0, 200]),
                '200-800' => $query->whereBetween('price', [200, 800]),
                '800-2000' => $query->whereBetween('price', [800, 2000]),
                '2000-10000' => $query->whereBetween('price', [2000, 10000]),
                '10000-above' => $query->where('price', '>=', 10000),
            };
        }
        //sorting
        if (!empty($filters['sort'])) {
            match ($filters['sort']) {
                'new_arrivals' => $query->orderBy('created_at', 'desc'),
                'best_seller' => $query->orderByDesc('total_Sold'),
                'sale' => $query->where('is_special_offer', 1),
                'featured' => $query->where('is_featured', 1),
                default => $query->latest(), //all products
            };
        }
        //search
        //when requested search is not empty
        //only when search not empty
        $query->when($keyword, function ($q) use ($keyword) {
            $q->where(function ($query) use ($keyword) {
                $query->where('name', 'like', "%{$keyword}%")
                    ->orWhereHas('category', function ($cat) use ($keyword) {
                        $cat->where('name', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('subcategory', function ($subcat) use ($keyword) {
                        $subcat->where('name', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('brands', function ($brand) use ($keyword) {
                        $brand->where('name', 'like', "%{$keyword}%");
                    });
            });
        });
        $query->latest();
        return $query->paginate(12)->withQueryString();
    }
}

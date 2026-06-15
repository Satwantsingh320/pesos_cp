<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Category;
use App\Services\CartService;
use App\Services\WishlistService;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        Paginator::useBootstrapFive();
        //display menu as categories
        View::composer('website.layouts.*', function ($view) {
            $categories = Category::active()
                ->orderBy('name', 'asc')
                ->get(['id', 'name', 'slug']);
            //get cart count
            $cartCount = app(CartService::class)->cartCount();
            $wishlistCount = app(WishlistService::class)->wishlistCount();
            $view->with([
                'menuCategories' => $categories,
                'cartCount' => $cartCount > 0 ? $cartCount : '',
                'wishlistCount' => $wishlistCount > 0 ? $wishlistCount : '',
            ]);
        });
        View::composer('layouts.*', function ($view) {
            if (auth('web')->check()) {
                $admin = auth('web')->user();

                $view->with('unreadNotifications', $admin->unreadNotifications()->latest()->limit(5)->get());
                $view->with('unreadCount', $admin->unreadNotifications()->count());
            }
        });
    }
}

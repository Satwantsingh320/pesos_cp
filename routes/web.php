<?php

use App\Helpers\WebsiteHelper;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\VariantAttributeController;

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\RefundRequestController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\ProductQuestionController;
use App\Http\Controllers\VipController;
use App\Http\Controllers\Website\CartController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Website\HomeController as WebsiteHomeController;
use App\Http\Controllers\Website\ProductController as WebsiteProductController;
use App\Http\Controllers\Website\WishlistController;



Route::get('clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    dd('cleared');
});

Route::get('migrate-fresh', function () {
    Artisan::call('migrate:fresh');
    dd('migration fresh');
});

Route::get('migrate-up', function () {
    Artisan::call('migrate');
    dd('migration done');
});



Route::prefix('admin')->group(function () {
    Route::get('', [AuthController::class, 'showLoginForm'])->name('admin.login');

    Route::middleware('guest:web')->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm']);
        Route::post('/login', [AuthController::class, 'login'])->name('admin.login.post');
    });

    Route::middleware('auth:web')->group(function () {
        Route::get('/change-language/{lang}', function ($lang) {
            if (!in_array($lang, config('app.locales'))) {
                abort(400);
            }
            session()->put('locale', $lang);
            return redirect()->back();
        })->name('change.language');

        Route::get('/product-questions', [ProductQuestionController::class, 'index'])->name('admin.product.questions');
        Route::post('/product-questions/answer', [ProductQuestionController::class, 'answer'])->name('admin.product.question.answer');
        Route::post('/product-questions/toggle-status', [ProductQuestionController::class, 'toggleStatus'])->name('admin.product.question.toggle-status');

        Route::get('export/{page}', [ExportController::class, 'export'])->name('export');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
        Route::get('/dashboard/orders-graph', [DashboardController::class, 'ordersGraph']);

        Route::get('/profile', [SettingController::class, 'profile'])->name('profile');
        Route::post("/profile/update", [SettingController::class, 'updateProfile'])->name('profile.update');
        Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

        //Category
        Route::resource('category', CategoryController::class);
        Route::post('category/status/{id?}', [CategoryController::class, 'status'])->name('category.status');
        Route::post('category/toggle-all-status/{status?}', [CategoryController::class, 'toggleAllStatus'])->name('category.toggle-all-status');

        //Subcategory
        Route::resource('subcategory', SubCategoryController::class);
        Route::post('subcategory/service', [SubCategoryController::class, 'service'])->name('subcategory.service');
        Route::post('subcategory/status/{id?}', [SubCategoryController::class, 'status'])->name('subcategory.status');
        Route::post('subcategory/toggle-all-status/{status?}', [SubCategoryController::class, 'toggleAllStatus'])->name('subcategory.toggle-all-status');

        //Brand
        Route::resource('brands', BrandController::class);
        Route::post('brands/status/{id?}', [BrandController::class, 'status'])->name('brands.status');
        Route::post('brands/toggle-all-status/{status?}', [BrandController::class, 'toggleAllStatus'])->name('brands.toggle-all-status');

        //Products
        Route::resource('products', ProductController::class);
        Route::post('products/status/{id?}', [ProductController::class, 'status'])->name('products.status');
        Route::post('products/toggle-all-status/{status?}', [ProductController::class, 'toggleAllStatus'])->name('products.toggle-all-status');
        Route::post('products/service', [ProductController::class, 'service'])->name('products.service');
        Route::post('products/get-price', [ProductController::class, 'getPrice'])->name('products.get-price');


        //Customers
        Route::resource('customers', CustomerController::class);
        Route::post('customers/status/{id?}', [CustomerController::class, 'status'])->name('customers.status');
        Route::post('customers/toggle-all-status/{status?}', [CustomerController::class, 'toggleAllStatus'])->name('customers.toggle-all-status');

        //Orders
        Route::resource('orders', OrderController::class);
        Route::post('/orders/update-tracking', [OrderController::class, 'updateTracking'])->name('orders.updateTracking');
        Route::post('orders/update-order-status', [OrderController::class, 'updateOrderStatus'])
            ->name('orders.update-order-status');
        //Settings
        Route::get('settings', [SettingController::class, 'getSettings'])->name('settings.get-settings');
        Route::post('settings/update/{id}', [SettingController::class, 'updateSettings'])->name('settings.update');

        //Special Offers
        Route::resource('banners', OfferController::class);
        Route::post('offers/status/{id?}', [OfferController::class, 'status'])->name('offers.status');
        Route::post('offers/toggle-all-status/{status?}', [OfferController::class, 'toggleAllStatus'])->name('offers.toggle-all-status');

        //Inventory
        Route::post('update-inventory', [ProductController::class, 'updateInventory'])->name('update-inventory');
        Route::prefix('inventory')->name('inventory.')->group(function () {
            Route::get('/', [InventoryController::class, 'index'])->name('index');
            Route::get('/create', [InventoryController::class, 'create'])->name('create');
            Route::post('/store', [InventoryController::class, 'store'])->name('store');
            Route::get('/create-multiple', [InventoryController::class, 'createMultiple'])->name('create.multiple');
            Route::post('/store-multiple', [InventoryController::class, 'storeMultiple'])->name('store.multiple');
            Route::post('/get-variants', [InventoryController::class, 'getVariants'])->name('getVariants');
            Route::post('/get-stock-info', [InventoryController::class, 'getStockInfo'])->name('getStockInfo');
            Route::post('/get-variant-details', [InventoryController::class, 'getVariantDetails'])->name('getVariantDetails');
            Route::post('/parse-csv', [InventoryController::class, 'parseCSV'])->name('parseCsv');
            Route::get('/{id}', [InventoryController::class, 'show'])->name('show');
        });

        // VIP Management Routes
        Route::prefix('vip')->name('admin.vip.')->group(function () {
            Route::get('/', [VipController::class, 'index'])->name('index');
            Route::post('/assign', [VipController::class, 'assignVip'])->name('assign');
            Route::put('/update/{id}', [VipController::class, 'updateVip'])->name('update');
            Route::delete('/remove/{id}', [VipController::class, 'removeVip'])->name('remove');
            Route::get('/{id}/edit', [VipController::class, 'getEditData'])->name('edit');
            Route::get('/{id}/prices', [VipController::class, 'getCustomerPrices'])->name('prices');
            Route::post('/manual-price', [VipController::class, 'setManualPrice'])->name('manual-price');
            Route::delete('/manual-price/{id}', [VipController::class, 'deleteManualPrice'])->name('manual-price.delete');
            Route::get('/product/{id}/variants', [VipController::class, 'getProductVariants'])->name('product.variants');
            Route::get('/product/{id}/price', [VipController::class, 'getProductPrice'])->name('product.price');
            Route::get('/vip/customer/{id}/products', [VipController::class, 'getCustomerProducts'])->name('customer.products');
        });

        Route::name('admin.')->group(function () {
            Route::get('variant-attributes', [VariantAttributeController::class, 'index'])->name('variant-attributes.index');
            Route::get('variant-attributes/create', [VariantAttributeController::class, 'create'])->name('variant-attributes.create');
            Route::post('variant-attributes', [VariantAttributeController::class, 'store'])->name('variant-attributes.store');
            Route::get('variant-attributes/{id}/edit', [VariantAttributeController::class, 'edit'])->name('variant-attributes.edit');
            Route::put('variant-attributes/{id}', [VariantAttributeController::class, 'update'])->name('variant-attributes.update');
            Route::delete('variant-attributes/{id}', [VariantAttributeController::class, 'destroy'])->name('variant-attributes.destroy');

            Route::post('variant-attributes/{attributeId}/values', [VariantAttributeController::class, 'storeValue'])->name('variant-attributes.values.store');
            Route::put('variant-attribute-values/{id}', [VariantAttributeController::class, 'updateValue'])->name('variant-attribute-values.update');
            Route::delete('variant-attribute-values/{id}', [VariantAttributeController::class, 'destroyValue'])->name('variant-attribute-values.destroy');
            Route::post('variant-attribute-values/reorder', [VariantAttributeController::class, 'reorderValues'])->name('variant-attribute-values.reorder');
        });

        //notifications
        Route::post('/notifications/read/{id}', [AuthController::class, 'markAsRead'])->name('admin.notifications.read');
        Route::get('/notifications', [AuthController::class, 'notifications'])->name('admin.notifications.index');
        Route::post('/notifications/read-all', [AuthController::class, 'markAllAsRead'])->name('admin.notifications.readAll');
    });
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginFormWebsite'])->name('login');
    Route::post('/login', [AuthController::class, 'loginWebsite'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');
    Route::post('/product/question/store', [WebsiteProductController::class, 'store'])->name('product.questions.store');
});

Route::name('website.')->group(function () {
    Route::get('/', [WebsiteHomeController::class, 'index'])->name('home');
    Route::get('search', [WebsiteProductController::class, 'search'])->name('products.search');
    Route::get('all-products', [WebsiteProductController::class, 'index'])->name('products');
    Route::get('/category-product/{slug}', [WebsiteProductController::class, 'category'])->name('category.products');
    Route::get('/brand/{slug}', [WebsiteProductController::class, 'brand'])->name('brand.products');
    Route::get('/product/{slug}', [WebsiteProductController::class, 'productDetail'])->name('product.show');
    Route::post('/add-to-cart', [CartController::class, 'addToCart'])->name('cart.addCart');
    Route::get('/cart', [CartController::class, 'viewCart'])->name('cart');
    Route::get('get-cart', [CartController::class, 'getCart']);
    Route::post('/cart/apply-coupon', [CartController::class, 'applyCoupon'])->name('cart.applyCoupon');
    Route::get('/cart/remove-coupon', [CartController::class, 'removeCoupon'])->name('cart.remove-coupon');
    Route::post('/cart/update-quantity', [CartController::class, 'updateQuantity'])->name('cart.updateQuantity');
    Route::post('/cart/remove', [CartController::class, 'removeItem'])->name('cart.removeItem');
    Route::get('/checkout', [CartController::class, 'viewCheckout'])->name('checkout');
    Route::post('/proceed-payment', [CartController::class, 'proceedPayment'])->name('proceed.payment');
    Route::get('/checkout/success', [CartController::class, 'checkoutSuccess'])->name('checkout-success');
    Route::get('/checkout/cancel', function () {
        return view('website.checkout-cancel');
    })->name('checkout-cancel');
    Route::get('contact-us', [WebsiteHomeController::class, 'contactUs'])->name('contact-us');
    Route::post('send-contact-ticket', [WebsiteHomeController::class, 'createTicket'])->name('createTicket');
    Route::post('/lang/switch', [WebsiteHomeController::class, 'switchLang'])->name('lang.switch');

});

Route::middleware('auth:customer')->group(function () {
    Route::get('customer/dashboard', [CustomerController::class, 'dashboard'])->name('customer.dashboard.index');
    Route::get('customer/logout', [CustomerController::class, 'logout'])->name('customer.logout');
    Route::post('/profile', [CustomerController::class, 'profile'])->name('customer.profile.update');
    Route::post('/address/store', [CustomerController::class, 'storeAddress'])->name('address.store');
    Route::get('/address/default/{id}', [CustomerController::class, 'setDefaultAddress'])->name('address.default');
    Route::delete('/address/delete/{id}', [CustomerController::class, 'deleteAddress'])->name('address.delete');
    Route::post('/profile/password', [AuthController::class, 'updatePassword'])->name('customer.password.update');
    Route::get('/order-details/{id}', [CustomerController::class, 'orderDetail'])->name('customer.order.details');
    Route::post('/refund/request', [RefundRequestController::class, 'store'])->name('customer.refund.request');
    Route::get('/refunds', [RefundRequestController::class, 'customerIndex'])->name('customer.refund.index');

});

Route::get('/terms', [AuthController::class, 'terms'])->name('terms');
Route::get('/privacy-policy', [AuthController::class, 'privacyPolicy'])->name('privacyPolicy');
Route::get('/return-policy', [AuthController::class, 'returnPolicy'])->name('returnPolicy');
Route::prefix('wishlist')->group(function () {
    Route::get('/', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/toggle/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
});

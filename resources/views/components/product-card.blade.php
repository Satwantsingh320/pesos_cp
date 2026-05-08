@php
    use App\Models\Wishlist;

    $owner = auth('customer')->check()
        ? ['customer_id' => auth('customer')->id()]
        : ['session_id' => session()->getId()];

    $isWishlisted = Wishlist::where('product_id', $product->id)
        ->where($owner)
        ->exists();

    $priceDisplay = $product->price_display;
    $totalStock = $product->total_quantity;
    $lowStockThreshold = $product->low_threshold;
@endphp


<style>
    .product-price {
        position: relative;
    }

    .stock-badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: bold;
        margin-top: 5px;
    }

    .stock-badge.out-of-stock {
        background-color: #dc3545;
        color: white;
    }

    .stock-badge.low-stock {
        background-color: #ffc107;
        color: #000;
    }

    .price-cut {
        text-decoration: line-through;
        font-size: 12px;
        color: #999;
        margin-left: 5px;
    }

    .current-price {
        font-weight: bold;
        color: #333;
    }
</style>

<div class="position-relative product-card-wrapper">

    {{-- Wishlist Button --}}
    <a class="wishlist-toggle position-absolute" style="top:10px;left:10px; z-index:10;"
        data-product="{{ $product->id }}" data-url="{{ route('wishlist.toggle', $product->id)}}">
        <i class="{{ $isWishlisted ? 'fa-solid fa-heart' : 'fa-regular fa-heart' }}" style="font-size:18px;color:black"
            aria-hidden="true"></i>
    </a>

    {{-- Sale Badge --}}
    @if($product->IsOnSale || ($priceDisplay->original_price && $priceDisplay->original_price > $priceDisplay->price))
        <div class="sale-tag">
            <div class="sale-text">
                <span>{{ __('website.product_offer') }}</span>
            </div>
        </div>
    @endif

    {{-- Product Link --}}
    <a href="{{ route('website.product.show', $product->slug) }}">
        <div class="main-item-box">
            <div class="product-box-list d-flex justify-content-center align-items-center">
                <div class="product-img">
                    <img src="{{ $product->CoverImageUrl }}" class="img-fluid" alt="{{ $product->name }}">
                </div>
            </div>
            <div class="product-details">
                <div class="product-title">
                    {{ $product->name }} -{{ $lowStockThreshold }}
                </div>

                {{-- Price Display --}}
                <div class="product-price">
                    @if($priceDisplay->type == 'range')
                        <span class="current-price">
                            {{ CURRENCY }} {{ number_format($priceDisplay->min_price, 2) }} - {{ CURRENCY }}
                            {{ number_format($priceDisplay->max_price, 2) }}
                        </span>
                        @if($priceDisplay->original_price)
                            <span class="price-cut">{{ CURRENCY }} {{ number_format($priceDisplay->original_price, 2) }}</span>
                        @endif
                    @else
                        <span class="current-price">
                            {{ CURRENCY }} {{ number_format($priceDisplay->price, 2) }}
                        </span>
                        @if($priceDisplay->original_price)
                            <span class="price-cut">{{ CURRENCY }} {{ number_format($priceDisplay->original_price, 2) }}</span>
                        @endif
                    @endif

                    {{-- Stock Status --}}
                    @if($totalStock <= 0)
                        <div class="stock-badge out-of-stock">
                            {{ __('website.product_out_of_stock') }}
                        </div>
                    @elseif($lowStockThreshold > 0 && $totalStock <= $lowStockThreshold && $totalStock > 0)
                        <div class="stock-badge low-stock">
                            {{ __('website.product_last_units') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </a>
</div>

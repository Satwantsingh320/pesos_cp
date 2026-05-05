@php
    use App\Models\Wishlist;

    $owner = auth('customer')->check()
        ? ['customers_id' => auth('customer')->id()]
        : ['session_id' => session()->getId()];

    $isWishlisted = Wishlist::where('product_id', $product->id)
        ->where($owner)
        ->exists();
@endphp

<div class="position-relative product-card-wrapper">

    {{-- Wishlist Button --}}
    <a class="wishlist-toggle  position-absolute" style="top:10px;left:10px; z-index:10;"
        data-product="{{ $product->id }}" data-url="{{ route('wishlist.toggle', $product->id)}}">
        <i class=" {{ $isWishlisted ? 'fa-solid fa-heart' : 'fa-regular fa-heart' }} "
            style="font-size:18px;color:black" aria-hidden="true"></i>
    </a>

    {{-- Product Link --}}
    <a href="{{ route('website.product.show', $product->slug) }}">
        <div class="main-item-box">
            @if ($product->IsOnSale)
                <div class="sale-tag">
                    <div class="sale-text">
                        <span>Oferta</span>
                    </div>
                </div>
            @endif

            <div class="product-box-list d-flex justify-content-center align-items-center">

                <div class="product-img">
                    <img src="{{ $product->CoverImageUrl }}" class="img-fluid" alt="product">
                </div>
            </div>
            <div class="product-details">
                <div class="product-title">
                    {{ $product->name }}
                </div>
                <p>
                    MX$ {{ number_format($product->DisplayPrice, 2) }}

                    @if ($product->OriginalPrice)
                        <span class="price-cut">MX$ {{ number_format($product->OriginalPrice, 2) }}</span>
                    @endif

                </p>
            </div>
        </div>
    </a>
</div>
@php
    $pageType = 'inner';
    $pageTitle = __('website.product_title');
    $breadcrumbTitlecurrent = __('website.product_title');
@endphp

@extends('website.layouts.layouts')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.0/dist/fancybox/fancybox.css" />
    <style>
        .vip-price {
            color: #28a745;
            font-weight: bold;
        }

        .vip-badge {
            background-color: #28a745;
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }

        .price-tag {
            font-size: 24px;
            font-weight: bold;
        }

        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        .variant-swatch.active {
            border: 2px solid black !important;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>

    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>

    <section id="product-time">
        <div class="container">
            @php
                use App\Models\Wishlist;
                use Illuminate\Support\Str;

                $owner = auth('customer')->check()
                    ? ['customer_id' => auth('customer')->id()]
                    : ['session_id' => session()->getId()];

                $isWishlisted = Wishlist::where('product_id', $product->id)->where($owner)->exists();

                // Set VIP customer for product
                $customer = auth('customer')->user();
                if ($customer) {
                    $product->vip_customer = $customer;
                }

                // Calculate price display for variant products
                $hasVariants = $product->has_variants == 1 && $product->variants && $product->variants->count() > 0;
                $variants = $hasVariants ? $product->variants()->with('combinations.attributeValue.attribute')->get() : collect();

                // Build variant lookup arrays
                $variantLookup = [];
                $attributeOptions = [];

                if ($hasVariants) {
                    foreach ($variants as $variant) {
                        $key = [];
                        foreach ($variant->combinations as $combo) {
                            if ($combo->attributeValue && $combo->attributeValue->attribute) {
                                $attrName = $combo->attributeValue->attribute->name;
                                $attrValue = $combo->attributeValue->value;
                                $key[$attrName] = $attrValue;

                                if (!isset($attributeOptions[$attrName])) {
                                    $attributeOptions[$attrName] = [];
                                }
                                if (!in_array($attrValue, $attributeOptions[$attrName])) {
                                    $attributeOptions[$attrName][] = $attrValue;
                                    // sort($attributeOptions[$attrName], SORT_NATURAL | SORT_FLAG_CASE);
                                }
                            }
                        }
                        $keyString = json_encode($key);
                        $variantLookup[$keyString] = $variant;
                    }
                }

                // Get selected variant from URL
                $selectedCombination = request()->get('variant');
                $selectedVariant = null;

                if ($selectedCombination && $hasVariants) {
                    $selectedVariant = $variants->firstWhere('id', $selectedCombination);
                }

                if (!$selectedVariant && $hasVariants && $variants->count() > 0) {
                    $selectedVariant = $variants->first();
                }

                $currentStock = $hasVariants
                    ? ($selectedVariant ? $selectedVariant->quantity : $variants->sum('quantity'))
                    : ($product->quantity ?? 0);

                // Get selected attributes for display
                $selectedAttributes = [];
                if ($selectedVariant) {
                    foreach ($selectedVariant->combinations as $combo) {
                        if ($combo->attributeValue && $combo->attributeValue->attribute) {
                            $selectedAttributes[$combo->attributeValue->attribute->name] = $combo->attributeValue->value;
                        }
                    }
                }

                // Helper function to get VIP price
                function getVipPrice($product, $variant, $customer)
                {
                    if (!$customer || !$customer->is_vip) {
                        return null;
                    }

                    $vipService = app(App\Services\VipPricingService::class);
                    return $vipService->getVipPrice($customer, $product, $variant);
                }
            @endphp

            <div class="row">
                <div class="col-lg-6 col-sm-6">
                    <div class="product-image-wrapper position-relative">
                        {{-- Wishlist --}}
                        <a class="wishlist-toggle position-absolute" style="top:15px; left:15px; z-index:20;"
                            data-url="{{ route('wishlist.toggle', $product->id) }}" role="button">
                            <i class="{{ $isWishlisted ? 'fa-solid fa-heart' : 'fa-regular fa-heart' }}"
                                style="font-size:22px; color:black;"></i>
                        </a>

                        {{-- Main Image --}}
                        <div class="main-image text-center">
                            <a data-fancybox="gallery" data-caption="{{ $product->name }}" id="mainImageLink"
                                href='{{ $selectedVariant && $selectedVariant->image ? asset('uploads/products/' . $selectedVariant->image) : $product->CoverImageUrl }}'>
                                <img id="mainProductImage"
                                    src="{{ $selectedVariant && $selectedVariant->image ? asset('uploads/products/' . $selectedVariant->image) : $product->CoverImageUrl }}"
                                    class="img-fluid variant-image" alt="{{ $product->name }}">
                            </a>
                        </div>

                        {{-- Thumbnails --}}
                        @if ($product->gallery->count())
                            <div class="thumbnail-wrapper mt-3 d-flex gap-2 flex-wrap">
                                @foreach ($product->gallery as $key => $image)

                                    <img src="{{ asset('uploads/products/' . $image->image) }}"
                                        class="img-thumbnail thumb-img @if ($key == 0) active-thumb @endif"
                                        style="width:80px; cursor:pointer;" onclick="changeImage(this)">

                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-lg-6 col-sm-6">
                    <div class="product-list-view">
                        <h4>{{ $product->name }}</h4>

                        <div class="product-price mb-3">
                            @if($hasVariants && $selectedVariant)
                                @php
                                    $regularPrice = $selectedVariant->offer_price ?? $selectedVariant->price;
                                    $vipPrice = getVipPrice($product, $selectedVariant, $customer);

                                    $displayPrice = $vipPrice ?? $regularPrice;
                                    $originalPrice = ($selectedVariant->offer_price && $selectedVariant->offer_price < $selectedVariant->price)
                                        ? $selectedVariant->price
                                        : null;
                                @endphp
                                <div class="d-flex align-items-center gap-3 flex-wrap">
                                    <div>
                                        @if($vipPrice)
                                            <span class="price-tag vip-price">{{CURRENCY}}
                                                {{ number_format($displayPrice, 2) }}</span>
                                            <small class="vip-badge ms-2">{{ __('VIP Price') }}</small>
                                            @if($regularPrice > $displayPrice)
                                                <div class="text-muted small">
                                                    <del>{{ __('Regular') }}: {{CURRENCY}} {{ number_format($regularPrice, 2) }}</del>
                                                </div>
                                            @endif
                                        @else
                                            <span class="price-tag">{{CURRENCY}} {{ number_format($displayPrice, 2) }}</span>
                                            @if($originalPrice)
                                                <span class="text-muted ms-2">
                                                    <del>{{CURRENCY}} {{ number_format($originalPrice, 2) }}</del>
                                                </span>
                                                <span class="badge bg-danger ms-2">
                                                    -{{ round((($originalPrice - $displayPrice) / $originalPrice) * 100) }}%
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            @else
                                @php
                                    $regularPrice = $product->offer_price ?? $product->price;
                                    $vipPrice = getVipPrice($product, null, $customer);
                                    $displayPrice = $vipPrice ?? $regularPrice;
                                    $originalPrice = ($product->offer_price && $product->offer_price < $product->price)
                                        ? $product->price
                                        : null;
                                @endphp
                                <div class="d-flex align-items-center gap-3 flex-wrap">
                                    <div>
                                        @if($vipPrice)
                                            <span class="price-tag vip-price">{{CURRENCY}}
                                                {{ number_format($displayPrice, 2) }} </span>
                                            <small class="vip-badge ms-2">{{ __('VIP Price') }}</small>
                                            @if($regularPrice > $displayPrice)
                                                <div class="text-muted small">
                                                    <del>{{ __('Regular') }}: {{CURRENCY}} {{ number_format($regularPrice, 2) }}</del>
                                                </div>
                                            @endif
                                        @else
                                            <span class="price-tag">{{CURRENCY}} {{ number_format($displayPrice, 2) }}</span>
                                            @if($originalPrice)
                                                <span class="text-muted ms-2">
                                                    <del>{{CURRENCY}} {{ number_format($originalPrice, 2) }}</del>
                                                </span>
                                                <span class="badge bg-danger ms-2">
                                                    -{{ round((($originalPrice - $displayPrice) / $originalPrice) * 100) }}%
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            @endif
                            <small class="text-danger d-block">{{ __('website.prices_include_tax') }}</small>
                        </div>

                        {{-- Modern Variant Selector --}}
                        @if($hasVariants && $variants->count() > 0)
                            <div class="variant-selector-modern">
                                <form id="variantForm" method="GET"
                                    action="{{ route('website.product.show', $product->slug) }}">
                                    @foreach($attributeOptions as $attributeName => $values)
                                        <div class="variant-group" data-attribute="{{ $attributeName }}">
                                            <span class="variant-group-title">
                                                @php
                                                    $attrKey = strtolower($attributeName);
                                                    $attributeLabel = match ($attrKey) {
                                                        'color' => __('website.color'),
                                                        'size' => __('website.size'),
                                                        'material' => __('website.material'),
                                                        default => $attributeName,
                                                    };
                                                @endphp
                                                {{ $attributeLabel }}:
                                            </span>
                                            <div class="variant-options">
                                                @foreach($values as $value)

                                                    @php
                                                        $matchingVariant = null;
                                                        foreach ($variants as $variant) {
                                                            $hasAttribute = $variant->combinations->contains(function ($combo) use ($attributeName, $value) {
                                                                return $combo->attributeValue &&
                                                                    $combo->attributeValue->attribute->name == $attributeName &&
                                                                    $combo->attributeValue->value == $value;
                                                            });
                                                            if ($hasAttribute) {
                                                                $matchingVariant = $variant;
                                                                break;
                                                            }
                                                        }

                                                        $isSelected = $selectedVariant && $selectedVariant->id == ($matchingVariant ? $matchingVariant->id : null);
                                                        $isAvailable = $matchingVariant && $matchingVariant->quantity > 0;
                                                        $isColor = strtolower($attributeName) == 'color';
                                                    @endphp

                                                    @if($isColor)
                                                        <div class="variant-swatch {{ $isSelected ? 'active' : '' }} {{ !$isAvailable ? 'disabled' : '' }}"
                                                            style="background-color: {{ strtolower($value) }};"
                                                            data-attribute="{{ $attributeName }}" data-value="{{ $value }}"
                                                            data-variant-id="{{ $matchingVariant ? $matchingVariant->id : '' }}"
                                                            onclick="selectVariant('{{ $matchingVariant ? $matchingVariant->id : '' }}')"
                                                            title="{{ $value }} {{ !$isAvailable ? '(' . __('website.out_of_stock_label') . ')' : '' }}">
                                                        </div>
                                                    @else
                                                        <div class="variant-option-btn {{ $isSelected ? 'active' : '' }} {{ !$isAvailable ? 'disabled' : '' }} {{ $matchingVariant->id }}"
                                                            data-attribute="{{ $attributeName }}" data-value="{{ $value }}"
                                                            data-variant-id="{{ $matchingVariant ? $matchingVariant->id : '' }}"
                                                            onclick="selectVariant('{{ $matchingVariant ? $matchingVariant->id : '' }}')">
                                                            {{ $value }}
                                                            @if(!$isAvailable)
                                                                <small>{{ __('website.out_of_stock_label') }}</small>
                                                            @endif
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                    <input type="hidden" name="variant" id="selectedVariantId"
                                        value="{{ $selectedVariant ? $selectedVariant->id : '' }}">
                                </form>

                                {{-- Selected Variant Info Card --}}
                                @if($selectedVariant)
                                    @php
                                        $variantRegularPrice = $selectedVariant->offer_price ?? $selectedVariant->price;
                                        $variantVipPrice = getVipPrice($product, $selectedVariant, $customer);
                                    @endphp
                                    <div class="selected-variant-card">
                                        <div class="row">
                                            <div class="col-4">
                                                <div class="label">{{ __('website.sku') }}</div>
                                                <div class="value">{{ $selectedVariant->sku }}</div>
                                            </div>
                                            <div class="col-4">
                                                <div class="label">{{ __('website.available_stock') }}</div>
                                                <div class="value">{{ $selectedVariant->quantity }} {{ __('website.units') }}</div>
                                            </div>
                                            <div class="col-4">
                                                <div class="label">{{ __('website.price') }}</div>
                                                <div class="value">
                                                    @if($variantVipPrice)
                                                        <span class="text-success">{{CURRENCY}}
                                                            {{ number_format($variantVipPrice, 2) }}</span>
                                                        <small class="vip-badge">VIP</small>
                                                        <div class="small text-secondary">
                                                            <del>{{CURRENCY}} {{ number_format($variantRegularPrice, 2) }}</del>
                                                        </div>
                                                    @else
                                                        {{CURRENCY}} {{ number_format($variantRegularPrice, 2) }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-12">
                                                <div class="label">{{ __('website.selected_variant') }}</div>
                                                <div class="value">
                                                    @foreach($selectedAttributes as $attrName => $attrValue)
                                                        @php
                                                            $attrKey = strtolower($attrName);
                                                            $attrLabel = match ($attrKey) {
                                                                'color' => __('website.color'),
                                                                'size' => __('website.size'),
                                                                'material' => __('website.material'),
                                                                default => $attrName,
                                                            };
                                                        @endphp
                                                        {{ $attrLabel }}: {{ $attrValue }}@if(!$loop->last) | @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="product-meta mt-0">
                            @if ($product->brand)
                                <p><strong>{{ __('website.brand') }}:</strong> {{ $product->brand->name }}</p>
                            @endif

                            @if ($product->category)
                                <p>
                                    <strong>{{ __('website.category') }}:</strong>
                                    {{ $product->category->name }}
                                    @if ($product->subcategory)
                                        {{ __('website.category_link') }} {{ $product->subcategory->name }}
                                    @endif
                                </p>
                            @endif

                            @if ($product->type)
                                <p><strong>{{ __('website.type') }}:</strong> {{ $product->type }}</p>
                            @endif

                            <p>
                                <strong>{{ __('website.shipping') }}:</strong>
                                @if ($product->shipping_fee > 0)
                                    {{CURRENCY}} {{ number_format($product->shipping_fee, 2) }}
                                @else
                                    {{ __('website.free_shipping') }}
                                @endif
                            </p>
                        </div>

                        {{-- Stock Status --}}
                        @if ($currentStock > 0)
                            <div class="mb-3">
                                <span class="stock-badge in-stock">
                                    <i class="fas fa-check-circle"></i> {{ __('website.in_stock') }}
                                </span>
                                @if($currentStock <= 10)
                                    <span class="stock-badge low-stock ms-2">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        {{ __('website.only_left', ['count' => $currentStock]) }}
                                    </span>
                                @endif
                            </div>
                        @else
                            <div class="mb-3">
                                <span class="stock-badge out-stock">
                                    <i class="fas fa-times-circle"></i> {{ __('website.out_of_stock') }}
                                </span>
                            </div>
                        @endif

                        {{-- Description Preview --}}
                        <p>
                            {!! Str::limit(strip_tags($product->description), 300) !!}
                            @if (Str::length(strip_tags($product->description)) > 300)
                                <a class="small link" href="#description">{{ __('website.see_more') }}</a>
                            @endif
                        </p>

                        {{-- Add to Cart Form --}}
                        <form class="ajax-form" data-type="productDetail" data-url="{{ route('website.cart.addCart') }}"
                            data-method="post">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="variant_id" id="variant_id"
                                value="{{ $selectedVariant ? $selectedVariant->id : '' }}">
                            <input type="hidden" name="buy_now" id="buy_now" value="0">

                            @if($currentStock > 0 && (!$hasVariants || ($hasVariants && $selectedVariant)))
                                <div class="qty-box mt-4">
                                    <div class="quantity" data-context="product">
                                        <a href="#" class="quantity__minus"><span><i class="fa-solid fa-minus"></i></span></a>
                                        <input name="quantity" type="number" class="quantity__input" value="1" min="1"
                                            max="{{ $currentStock }}" step="1">
                                        <a href="#" class="quantity__plus"><span><i class="fa-solid fa-plus"></i></span></a>
                                    </div>
                                </div>
                            @endif

                            <div class="qty-box-btn mt-3">
                                @if ($currentStock > 0 && (!$hasVariants || ($hasVariants && $selectedVariant)))
                                    <button class="btn btn-primary" type="submit"
                                        onclick="document.getElementById('buy_now').value=1">
                                        <i class="fas fa-bolt"></i> {{ __('website.buy_now') }}
                                    </button>
                                    <button class="btn btn-theme" type="submit"
                                        onclick="document.getElementById('buy_now').value=0">
                                        <i class="fas fa-cart-plus"></i> {{ __('website.add_to_cart') }}
                                    </button>
                                @else
                                    <button type="button" class="btn btn-secondary" disabled>
                                        <i class="fas fa-times-circle"></i> {{ __('website.out_of_stock') }}
                                    </button>
                                @endif
                            </div>
                        </form>

                        <div id="cartAlert" class="alert alert-success alert-dismissible fade d-none mt-2" role="alert">
                            <strong>{{ __('website.added_to_cart') }}</strong>
                            {{ __('website.product_added_successfully') }}
                            <a role="button" href="{{ route('website.cart') }}" class="view-cart">
                                {{ __('website.view_cart') }}
                            </a>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Q&A Section --}}
            <div class="mt-5 qa-section">
                <h4 class="mb-4">{{ __('website.customer_questions') }}</h4>
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('product.questions.store') }}" method="POST" class="d-flex gap-2 align-items-center">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <textarea name="question" class="form-control flex-grow-1" rows="1" style="height: 38px; resize: none;"
                        placeholder="{{ __('website.ask_question') }}" required></textarea>
                    <button class="btn btn-primary" type="submit"
                        style="height: 38px;">{{ __('website.submit_question') }}</button>
                </form>

                @if($product->questions->count() > 0)
                    <hr>
                    <div class="pe-2 qa-scroll-box">
                        @foreach($product->questions as $question)
                            <div class="mb-4">
                                <p><b>{{ $question->question }}</b></p>
                                @if($question->answers->count())
                                    <div class="ps-3 border-start">
                                        @foreach($question->answers as $answer)
                                            <div class="mb-2">
                                                <p class="mb-1">{{ $answer->answer }}</p>
                                                <small class="text-muted small">
                                                    {{ $answer->created_at->format('d-m-Y') }}
                                                </small>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Full Description --}}
            <div class="row mt-4" id="description">
                <div class="col-lg-12">
                    <div class="details-box">
                        <h4>{{ __('website.product_description') }}</h4>
                        {!! $product->description !!}
                    </div>
                </div>
            </div>
        </div>
    </section>


    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.0/dist/fancybox/fancybox.umd.js"></script>
    <script>
        Fancybox.bind("[data-fancybox='gallery']", {
            Thumbs: false,
            Toolbar: true
        });

        document.addEventListener("DOMContentLoaded", function () {
            const barcodeValue = "{{ $product->barcode_number }}";

            if (barcodeValue) {
                JsBarcode("#barcode", barcodeValue, {
                    format: "CODE128",
                    width: 2,
                    height: 60,
                    displayValue: true
                });
            }

            // Initialize variant selection
            initVariantSelection();
        });

        function changeImage(element) {

            const mainImage = document.getElementById('mainProductImage');
            const mainLink = document.getElementById('mainImageLink');

            if (!mainImage || !element) return;

            const imageUrl = element.dataset.full || element.src;

            // Change visible image
            mainImage.src = imageUrl;

            // Update fancybox target
            if (mainLink) {
                mainLink.href = imageUrl;
                mainLink.setAttribute('data-src', imageUrl);
            }

            // Active thumb
            document.querySelectorAll('.thumb-img').forEach(img => {
                img.classList.remove('active-thumb');
            });

            element.classList.add('active-thumb');
        }
        // Variant selection function
        function selectVariant(variantId) {
            if (!variantId) return;

            // Show loading overlay
            const loadingOverlay = document.getElementById('loadingOverlay');
            if (loadingOverlay) {
                loadingOverlay.style.display = 'flex';
            }

            // Update hidden inputs
            const selectedVariantId = document.getElementById('selectedVariantId');
            const variantIdInput = document.getElementById('variant_id');

            if (selectedVariantId) selectedVariantId.value = variantId;
            if (variantIdInput) variantIdInput.value = variantId;

            // Submit the form
            const variantForm = document.getElementById('variantForm');
            if (variantForm) {
                variantForm.submit();
            } else {
                // Fallback: reload page with variant parameter
                let url = new URL(window.location.href);
                url.searchParams.set('variant', variantId);
                window.location.href = url.toString();
            }
        }

        // Initialize variant selection listeners
        function initVariantSelection() {
            // For color swatches
            const swatches = document.querySelectorAll('.variant-swatch');
            swatches.forEach(swatch => {
                swatch.addEventListener('click', function (e) {
                    e.preventDefault();
                    if (this.classList.contains('disabled')) return;
                    const variantId = this.getAttribute('data-variant-id');
                    if (variantId) selectVariant(variantId);
                });
            });

            // For text variant buttons
            const buttons = document.querySelectorAll('.variant-option-btn');
            buttons.forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    if (this.classList.contains('disabled')) return;
                    const variantId = this.getAttribute('data-variant-id');
                    if (variantId) selectVariant(variantId);
                });
            });
        }

        // Optional: Update price dynamically when variant changes (AJAX version)
        async function updateVariantPrice(variantId) {
            try {
                const response = await fetch(`/get-variant-price/${variantId}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();

                if (data.success) {
                    // Update price display
                    const priceElement = document.querySelector('.price-tag');
                    if (priceElement) {
                        priceElement.innerHTML = '{{CURRENCY}} ' + data.price;
                    }

                    // Update stock display
                    const stockElement = document.querySelector('.stock-badge');
                    if (stockElement && data.stock !== undefined) {
                        if (data.stock > 0) {
                            stockElement.className = 'stock-badge in-stock';
                            stockElement.innerHTML = '<i class="fas fa-check-circle"></i> {{ __("website.in_stock") }}';
                            if (data.stock <= 10) {
                                // Add low stock warning
                            }
                        } else {
                            stockElement.className = 'stock-badge out-stock';
                            stockElement.innerHTML = '<i class="fas fa-times-circle"></i> {{ __("website.out_of_stock") }}';
                        }
                    }
                }
            } catch (error) {
                console.error('Error updating variant price:', error);
            }
        }
    </script>
@endsection

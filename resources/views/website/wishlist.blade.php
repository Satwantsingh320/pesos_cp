@php
    $pageType = 'inner';
    $pageTitle = __('website.wishlist_title');
    $breadcrumbTitlecurrent = __('website.wishlist_title');
@endphp

@extends('website.layouts.layouts')

@section('content')
    <!-- ========= Product Time ======= -->
    <section id="product-time" class="wishlist-section py-5">
        <div class="container">

            <div class="row">
                @forelse($items as $item)

                    @php $product = $item->product; @endphp

                    <div class="col-lg-4 col-md-6 mb-4 wishlist-item" id="wishlist-item-{{ $product->id }}">
                        <div class="wishlist-card position-relative">

                            @if($product->IsOnSale)
                                <span class="badge bg-danger sale-badge">{{ __('website.wishlist_sale') }}</span>
                            @endif

                            <div class="wishlist-image text-center">
                                <a href="{{ route('website.product.show', $product->slug) }}">
                                    <img src="{{ $product->CoverImageUrl }}" alt="{{ $product->name }}" class="img-fluid">
                                </a>
                            </div>

                            <div class="wishlist-content text-center">
                                <h5 class="mb-2">
                                    <a href="{{ route('website.product.show', $product->slug) }}"
                                        class="text-dark text-decoration-none">
                                        {{ $product->name }}
                                    </a>
                                </h5>

                                <p class="price mb-3">
                                    {{ CURRENCY }}{{ number_format($product->DisplayPrice, 2) }}
                                    @if($product->OriginalPrice)
                                        <span class="text-muted text-decoration-line-through ms-2">
                                            {{ CURRENCY }}{{ number_format($product->OriginalPrice, 2) }}
                                        </span>
                                    @endif
                                </p>

                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('website.product.show', $product->slug) }}"
                                        class="btn btn-outline-dark btn-sm">
                                        {{ __('website.wishlist_btn_view') }}
                                    </a>

                                    <form method="POST" action="{{ route('wishlist.toggle', $product->id) }}">
                                        @csrf
                                        <button type="button" class="btn btn-danger btn-sm wishlist-remove"
                                            data-url="{{ route('wishlist.toggle', $product->id) }}">
                                            <i class="fa-solid fa-heart"></i> {{ __('website.wishlist_btn_remove') }}
                                        </button>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>

                @empty

                    <div class="col-12 text-center py-5">
                        <i class="fa-regular fa-heart fa-3x mb-3 text-muted"></i>
                        <h4>{{ __('website.wishlist_empty') }}</h4>
                        <p class="text-muted">{{ __('website.wishlist_empty_desc') }}</p>
                        <a href="{{ route('website.home') }}" class="btn btn-dark mt-2">
                            {{ __('website.wishlist_btn_continue') }}
                        </a>
                    </div>

                @endforelse
            </div>

        </div>
    </section>
@endsection
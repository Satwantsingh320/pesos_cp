@php
    $pageType = 'home';
@endphp

@extends('website.layouts.layouts')

@section('content')
    <style>
        .carousel-item img {
            height: 800px;
            width: 100%;
            object-fit: cover;
        }
    </style>
    @php
        $sliders = \App\Models\Offer::where('status', 1)->whereNotNull('banner')->orderBy('id', 'desc')->take(5)->get();
      @endphp

    @if ($sliders->count())
        <section id="slider-box">
            <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">

                {{-- Indicators --}}
                <div class="carousel-indicators">
                    @foreach ($sliders as $key => $slider)
                        <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="{{ $key }}"
                            class="{{ $key == 0 ? 'active' : '' }}" aria-current="{{ $key == 0 ? 'true' : 'false' }}"
                            aria-label="{{ __('website.Slide') }} {{ $key + 1 }}">
                        </button>
                    @endforeach
                </div>

                {{-- Slides --}}
                <div class="carousel-inner">
                    @foreach ($sliders as $key => $slider)
                        <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                            <img src="{{ asset(OFFER_BANNERS_PATH . $slider->banner) }}" class="d-block img-fluid w-100"
                                alt="{{ $slider->title }}">

                            <div class="carousel-caption text-start start-1">
                                <h2>{{ $slider->title }}</h2>

                                @if ($slider->description)
                                    <p>{!! \Illuminate\Support\Str::limit($slider->description, 120) !!}</p>
                                @endif


                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Navigation Arrows --}}
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">{{ __('website.previous') }}</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">{{ __('website.next') }}</span>
                </button>
            </div>
        </section>
    @endif

    <!-- ========= Features Section ======= -->
    <section id="pesmos-list">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-sm-6 wow fadeInLeft">
                    <div class="feature-box mb-2">
                        <div class="inner-feature-box">
                            <div class="inner-feature-icon">
                                <i class="fa-solid fa-truck-fast"></i>
                            </div>
                            <div class="inner-feature-text">
                                <h5>{{ __('website.free_shipping') }}</h5>
                                <p>{{ __('website.free_shipping_desc') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 wow fadeInUp">
                    <div class="feature-box mb-2">
                        <div class="inner-feature-box">
                            <div class="inner-feature-icon">
                                <i class="fa-solid fa-headset"></i>
                            </div>
                            <div class="inner-feature-text">
                                <h5>{{ __('website.support_24_7') }}</h5>
                                <p>{{ __('website.support_24_7_desc') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 wow fadeInDown">
                    <div class="feature-box mb-2">
                        <div class="inner-feature-box">
                            <div class="inner-feature-icon">
                                <i class="fa-solid fa-shield-halved"></i>
                            </div>
                            <div class="inner-feature-text">
                                <h5>{{ __('website.secure_payment') }}</h5>
                                <p>{{ __('website.secure_payment_desc') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 wow fadeInRight">
                    <div class="feature-box mb-2">
                        <div class="inner-feature-box">
                            <div class="inner-feature-icon">
                                <i class="fa-solid fa-tags"></i>
                            </div>
                            <div class="inner-feature-text">
                                <h5>{{ __('website.daily_offers') }}</h5>
                                <p>{{ __('website.daily_offers_desc') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========= Featured Products Section ======= -->
    <section id="category-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 wow fadeInLeft">
                    <div class="category-title">
                        <h3>{{ __('website.featured_products') }}</h3>
                        <p>{{ __('website.featured_products_desc') }}</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 wow fadeInUp">
                    <div class="product-slide">
                        <div id="myCarousel" class="owl-carousel owl-theme">
                            @forelse ($featured as $featuredProduct)
                                <div class="item">
                                    <x-product-card :product="$featuredProduct" />
                                </div>
                            @empty
                                <div class="text-center py-5">
                                    <p>{{ __('website.no_products_found') }}</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========= Latest Products Section ======= -->
    <section id="cat-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 wow fadeInLeft">
                    <div class="category-title">
                        <h3>{{ __('website.latest_products') }}</h3>
                        <p>{{ __('website.latest_products_desc') }}</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 wow fadeInUp">
                    <div class="product-slide">
                        <div id="myCarousel1" class="owl-carousel owl-theme">
                            @forelse ($latest as $latestProduct)
                                <div class="item">
                                    <x-product-card :product="$latestProduct" />
                                </div>
                            @empty
                                <div class="text-center py-5">
                                    <p>{{ __('website.no_products_found') }}</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========= Special Offers Banner ======= -->
    <div class="bw-banner">
        <div class="bw-content">
            <span class="bw-label">{{ __('website.limited_time') }}</span>
            <h1>{{ __('website.special_offers') }}</h1>
            <p>{{ __('website.special_offers_desc') }}</p>
            <div class="bw-actions">
                <a href="{{ route('website.products') }}" class="bw-btn bw-btn-primary">{{ __('website.shop_now') }}</a>
                <a href="{{ route('website.products', ['sort' => 'sale']) }}"
                    class="bw-btn bw-btn-secondary">{{ __('website.view_offers') }}</a>
            </div>
        </div>
    </div>

    <!-- ========= Clearance Products Section ======= -->
   {{--  <section id="selling-products">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 wow fadeInLeft">
                    <div class="category-title">
                        <h3>{{ __('website.clearance_products') }}</h3>
                        <p>{{ __('website.clearance_products_desc') }}</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 wow fadeInUp">
                    <div class="product-slide">
                        <div id="sell-products" class="owl-carousel owl-theme">
                            @forelse ($clearence as $clearanceProduct)
                                <div class="item">
                                    <x-product-card :product="$clearanceProduct" />
                                </div>
                            @empty
                                <div class="text-center py-5">
                                    <p>{{ __('website.no_products_found') }}</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}
@endsection

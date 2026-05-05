@php
  $pageType = 'home';

@endphp
@extends('website.layouts.layouts')
@section('content')
  <!-- ========= Slider ======= -->
  {{-- <section id="slider-box">
                            <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">
                                <div class="carousel-indicators">
                                    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active"
                                        aria-current="true" aria-label="Slide 1"></button>
                                    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1"
                                        aria-label="Slide 2"></button>
                                    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2"
                                        aria-label="Slide 3"></button>
                                </div>
                                <div class="carousel-inner">
                                    <div class="carousel-item active">
                                        <img src="{{ asset('website-assets/img/main.png') }}" class="d-block img-fluid"
  alt="Banner">
  <div class="carousel-caption text-start start-1">
    <h2>Nueva Generación Relojes Inteligentes</h2>
    <p>Tecnología que funciona a la perfección en cualquier espacio.</p>
    <div class="banner-btn">
      <a href="#" type="button">EXPLORA AHORA</a>
    </div>
  </div>
  </div>
  <div class="carousel-item">
    <img src="{{ asset('website-assets/img/main.png') }}" class="d-block img-fluid" alt="Banner">
    <div class="carousel-caption text-start start-1">
      <h2>Nueva Generación Relojes Inteligentes</h2>
      <p>Tecnología que funciona a la perfección en cualquier espacio.</p>
      <div class="banner-btn">
        <a href="#" type="button">EXPLORA AHORA</a>
      </div>
    </div>
  </div>
  <div class="carousel-item">
    <img src="{{ asset('website-assets/img/main.png') }}" class="d-block img-fluid" alt="Banner">
    <div class="carousel-caption text-start start-1">
      <h2>Nueva Generación Relojes Inteligentes</h2>
      <p>Tecnología que funciona a la perfección en cualquier espacio.</p>
      <div class="banner-btn">
        <a href="#" type="button">EXPLORA AHORA</a>
      </div>
    </div>
  </div>
  </div>
  </div>
  </section> --}}
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
              class="{{ $key == 0 ? 'active' : '' }}" aria-current="{{ $key == 0 ? 'true' : 'false' }}">
            </button>
          @endforeach
        </div>

        {{-- Slides --}}
        <div class="carousel-inner">
          @foreach ($sliders as $key => $slider)
            <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">

              <img src="{{ asset(OFFER_BANNERS_PATH . $slider->banner) }}" class="d-block img-fluid"
                alt="{{ $slider->title }}">

              <div class="carousel-caption text-start start-1">
                <h2>{{ $slider->title }}</h2>

                @if ($slider->description)
                  <p>{!! \Illuminate\Support\Str::limit($slider->description, 120) !!}</p>
                @endif

                <div class="mb-2">
                  <span class="fw-bold text-danger fs-5">
                    MX${{ number_format($slider->offer_price, 2) }}
                  </span>

                  <span class="text-muted text-decoration-line-through ms-2">
                    MX${{ number_format($slider->price, 2) }}
                  </span>
                </div>

                <div class="banner-btn">
                  @php $product = \App\Models\Product::select('slug')->find($slider->product_id); @endphp
                  <a href="{{ url('product/' . $product->slug) }}">
                    EXPLORA AHORA
                  </a>
                </div>
              </div>

            </div>
          @endforeach
        </div>
      </div>
    </section>
  @endif
  <!-- ========= Pesmos Features ======= -->
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
                <h5>Envío gratis</h5>
                <p>Para todos los pedidos superiores a $1299.00</p>
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
                <h5>Soporte 24/7</h5>
                <p>+52 614 215 9366</p>
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
                <h5>Pago Seguro</h5>
                <p>Pago 100% seguro</p>
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
                <h5>Ofertas Diarias</h5>
                <p>Ahorre hasta un 25% de descuento</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>


  <!-- ========= Category ======= -->
  <section id="category-section">
    <div class="container">
      <div class="row">
        <div class="col-lg-12 wow fadeInLeft">
          <div class="category-title">
            <h3>Productos destacados</h3>
            <p>Una selección especial de productos elegidos por su calidad, valor y alta demanda.</p>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-12 wow fadeInUp">
          <div class="product-slide">
            <div id="myCarousel" class="owl-carousel owl-theme">
              @foreach ($featured as $featured)
                <div class="item">
                  {{-- //component --}}
                  <x-product-card :product="$featured" />
                </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>



  <!-- ========= Latest ======= -->
  <section id="cat-section">
    <div class="container">
      <div class="row">
        <div class="col-lg-12 wow fadeInLeft">
          <div class="category-title">
            <h3>Productos </h3>
            <p>Explora los productos más recientes</p>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-12 wow fadeInUp">
          <div class="product-slide">
            <div id="myCarousel1" class="owl-carousel owl-theme">
              @foreach ($latest as $latest)
                <div class="item">

                  <x-product-card :product="$latest" />

                </div>
              @endforeach


            </div>
          </div>
        </div>
      </div>
    </div>
  </section>



  <div class="bw-banner">
    <div class="bw-content">
      <span class="bw-label">TIEMPO LIMITADO</span>

      <h1>OFERTAS ESPECIALES</h1>

      <p>
        Ofertas exclusivas disponibles ahora.
        Compra productos premium a los mejores precios.
      </p>

      <div class="bw-actions">
        <a href="{{ route('website.products') }}" class="bw-btn bw-btn-primary">COMPRAR AHORA</a>
        <a href="{{ route('website.products', ['sort' => 'sale']) }}" class="bw-btn bw-btn-secondary">VER
          OFERTAS</a>
      </div>
    </div>
  </div>
  <!-- ========= Industrial ======= -->
  {{-- <section id="cat-section-industrial">
                                                                <div class="container">
                                                                    <div class="row">
                                                                        <div class="col-lg-12 wow fadeInLeft">
                                                                            <div class="category-title">
                                                                                <h3>Los más vendidos</h3>
                                                                                <p>Los productos favoritos de nuestros clientes con mayor número de ventas y confianza.</p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-lg-12 wow fadeInUp">
                                                                            <div class="product-slide">
                                                                                <div id="myCarousel2" class="owl-carousel owl-theme">
                                                                                    @foreach ($bestSelling as $bestSelling)
                                                                                        <div class="item">
                                                                                            <x-product-card :product="$bestSelling" />
                                                                                        </div>
                                                                                    @endforeach


                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </section> --}}




  <!-- ========= Hot Selling Products ======= -->
  <section id="selling-products">
    <div class="container">
      <div class="row">
        <div class="col-lg-12 wow fadeInLeft">
          <div class="category-title">
            <h3>Liquidación</h3>
            <p>Productos con grandes descuentos disponibles por tiempo limitado hasta agotar existencias.</p>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-12 wow fadeInUp">
          <div class="product-slide">
            <div id="sell-products" class="owl-carousel owl-theme">
              @foreach ($clearence as $clearence)
                <div class="item">
                  <x-product-card :product="$clearence" />
                </div>
              @endforeach


            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection

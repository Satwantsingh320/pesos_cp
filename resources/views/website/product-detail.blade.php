@php
    $pageType = 'inner';
    $pageTitle = 'Productos';
    $breadcrumbTitlecurrent = 'Productos';
@endphp
@extends('website.layouts.layouts')
@section('content')
    <style>
        .qa-section {
            background: #f8f9fa;
            /* very light grey */
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 25px;
        }

        .qa-scroll-box {
            max-height: 400px;
            overflow-y: auto;
            padding-right: 6px;
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.0/dist/fancybox/fancybox.css" />
    <!-- ========= Product Time ======= -->
    <section id="product-time">
        <div class="container">
            @php
                use App\Models\Wishlist;

                $owner = auth('customer')->check()
                    ? ['customers_id' => auth('customer')->id()]
                    : ['session_id' => session()->getId()];

                $isWishlisted = Wishlist::where('product_id', $product->id)->where($owner)->exists();
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

                        {{-- Sale Tag --}}
                        @if ($product->IsOnSale)
                            <div class="sale-tag">
                                <div class="sale-text">
                                    <span>Oferta</span>
                                </div>
                            </div>
                        @endif

                        {{-- Main Image --}}
                        <div class="main-image text-center">
                            <a data-fancybox="gallery" data-caption="{{ $product->name }}"
                                href='{{ $product->CoverImageUrl }}'><img id="mainProductImage"
                                    src="{{ $product->CoverImageUrl }}" class="img-fluid" alt="{{ $product->name }}"></a>
                        </div>

                        {{-- Thumbnails --}}
                        @if ($product->gallery->count())
                            <div class="thumbnail-wrapper mt-3 d-flex gap-2 flex-wrap">

                                @foreach ($product->gallery as $key => $image)
                                    <a data-fancybox="gallery" data-caption="{{ $product->name }}"
                                        href='{{ asset('uploads/products/' . $image->image) }}'><img
                                            src="{{ asset('uploads/products/' . $image->image) }}"
                                            class="img-thumbnail thumb-img @if ($key == 0) active-thumb @endif"
                                            style="width:80px; cursor:pointer;" onclick="changeImage(this)"></a>
                                @endforeach

                            </div>
                        @endif

                    </div>
                </div>
                <div class="col-lg-6 col-sm-6">
                    <div class="product-list-view">
                        <h4>{{ $product->name }}</h4>

                        <div class="product-price">
                            <h4 class="mb-0">
                                MX$ {{ number_format($product->DisplayPrice, 2) }}

                                @if ($product->OriginalPrice)
                                    <span class="price-cut">MX$ {{ number_format($product->OriginalPrice, 2) }}</span>
                                @endif

                            </h4>
                            <small class="text-danger">Precios incluyen IVA</small>
                        </div>

                        <div class="product-meta mt-0">

                            {{-- Barcode --}}
                            @if ($product->barcode_number)
                                <svg id="barcode"></svg>
                            @endif

                            {{-- Brand --}}
                            @if ($product->brand)
                                <p><strong>Marca:</strong> {{ $product->brand->name }}</p>
                            @endif

                            {{-- Category --}}
                            @if ($product->category)
                                <p>
                                    <strong>Categoría:</strong>
                                    {{ $product->category->name }}

                                    @if ($product->subcategory)
                                        → {{ $product->subcategory->name }}
                                    @endif
                                </p>
                            @endif

                            {{-- SKU --}}
                            @if ($product->sku_number)
                                <p><strong>SKU:</strong> {{ $product->sku_number }}</p>
                            @endif
                            @if ($product->type)
                                <p><strong>Tipo:</strong> {{ $product->type }}</p>
                            @endif
                            {{-- Shipping --}}
                            <p>
                                <strong>Envío:</strong>
                                @if ($product->shipping_fee > 0)
                                    MX$ {{ number_format($product->shipping_fee, 2) }}
                                @else
                                    Envío Gratis
                                @endif
                            </p>

                            {{-- Delivery Time --}}
                            @if ($product->estimated_delivery_time)
                                <p>
                                    <strong>Tiempo estimado de entrega:</strong>
                                    {{ $product->estimated_delivery_time }} días
                                </p>
                            @endif

                        </div>
                        @if ($product->no_of_pieces_available > 0)
                            <div class="avaliable-stock">
                                <h5>Disponible en stock</h5>
                            </div>
                        @else
                            <div class="out-stock">
                                <h5>Agotado</h5>
                            </div>
                        @endif
                        <p>
                            {!! Str::limit($product->description, 500) !!}
                            @if (Str::length(strip_tags($product->description)) > 500)
                                <a class="small link" href="#description">See More</a>
                            @endif
                        </p>

                        <form class="ajax-form" data-type="productDetail" data-url="{{ route('website.cart.addCart') }}"
                            data-method="post">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="buy_now" id="buy_now" value="0">
                            <div class="qty-box mt-4">
                                <div class="quantity" data-context="product">
                                    <a href="#" class="quantity__minus"><span><i class="fa-solid fa-minus"></i></span></a>
                                    <input name="quantity" type="text" class="quantity__input" value="1" min="1"
                                        max="{{ $product->no_of_pieces_available }}" readonly>
                                    <a href="#" class="quantity__plus"><span><i class="fa-solid fa-plus"></i></span></a>
                                </div>
                            </div>


                            <div class="qty-box-btn">
                                {{-- //add to cart verify out of stock condition --}}
                                @if ($product->no_of_pieces_available > 0)
                                    {{-- <a href="#" type="button">Añadir al carrito</a> --}}
                                    <button class="btn btn-primary" type="submit"
                                        onclick="document.getElementById('buy_now').value=1">
                                        Comprar ahora
                                    </button>
                                    <button class="btn btn-theme" type="submit"
                                        onclick="document.getElementById('buy_now').value=0">
                                        Añadir al carrito
                                    </button>


                                @else
                                    <button type="button" class="btn disabled" disabled="disabled">Agotado</button>
                                @endif

                            </div>
                        </form>
                        <div id="cartAlert" class="alert alert-success alert-dismissible fade d-none mt-2" role="alert">
                            <strong>¡Agregado al carrito!</strong> Este producto se ha añadido correctamente.
                            <a role="button" href="{{ route('website.cart') }}" class="view-cart">
                                Ver carrito
                            </a>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5 qa-section">

                <h4 class="mb-4">Preguntas de los clientes</h4>
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('product.questions.store') }}" method="POST" class="d-flex gap-2 align-items-center">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                    <textarea name="question" class="form-control flex-grow-1" rows="1" style="height: 38px; resize: none;"
                        placeholder="Haz una pregunta sobre este producto..." required></textarea>

                    <button class="btn btn-primary" type="submit" style="height: 38px;">Enviar pregunta</button>
                </form>
                @if(($product->questions->count() > 0))
                    <hr>
                    <div class="pe-2 qa-scroll-box">
                        @foreach($product->questions as $question)

                            <div class="mb-4">
                                <strong class="d-none">{{ $question?->user?->name ?? ''}}</strong>
                                <p><b>{{ $question->question }}</b></p>
                                @if($question->answers->count())
                                    <div class="ps-3 border-start">
                                        @foreach($question->answers as $answer)
                                            <div class="mb-2">
                                                <p class="mb-1">{{ $answer->answer }}</p>
                                                <small class="text-muted small">
                                                    {{ $answer->created_at->format('d-m-Y') }}
                                                    <span class="d-none"> By {{ $answer?->user->name }} </span>
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

            @if (Str::length(strip_tags($product->description)) > 500)
                <div class="row">
                    <div class="col-lg-12">
                        <div class="details-box" id="description">
                            <h4>Descripción del Producto</h4>
                            {!! $product->description !!}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.0/dist/fancybox/fancybox.umd.js"></script>
    <script>
        Fancybox.bind("[data-fancybox='gallery']", {
            Thumbs: false,
            Toolbar: true
        });
    </script>
    <script>
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
        });

        function changeImage(element) {
            const mainImage = document.getElementById('mainProductImage');
            mainImage.src = element.src;

            // Remove active class
            document.querySelectorAll('.thumb-img').forEach(img => {
                img.classList.remove('active-thumb');
            });

            element.classList.add('active-thumb');
        }

    </script>
@endsection
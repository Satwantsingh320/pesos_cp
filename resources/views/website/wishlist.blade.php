@php
    $pageType = 'inner';
    $pageTitle = 'Lista de Deseos';
    $breadcrumbTitlecurrent = 'Lista de Deseos';
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

                            {{-- Sale Badge --}}
                            @if($product->IsOnSale)
                                <span class="badge bg-danger sale-badge">Oferta</span>
                            @endif

                            {{-- Product Image --}}
                            <div class="wishlist-image text-center">
                                <a href="{{ route('website.product.show', $product->slug) }}">
                                    <img src="{{ $product->CoverImageUrl }}" alt="{{ $product->name }}" class="img-fluid">
                                </a>
                            </div>

                            {{-- Product Info --}}
                            <div class="wishlist-content text-center">
                                <h5 class="mb-2">
                                    <a href="{{ route('website.product.show', $product->slug) }}"
                                        class="text-dark text-decoration-none">
                                        {{ $product->name }}
                                    </a>
                                </h5>

                                <p class="price mb-3">
                                    ₹{{ number_format($product->DisplayPrice, 2) }}

                                    @if($product->OriginalPrice)
                                        <span class="text-muted text-decoration-line-through ms-2">
                                            ₹{{ number_format($product->OriginalPrice, 2) }}
                                        </span>
                                    @endif
                                </p>

                                <div class="d-flex justify-content-center gap-2">

                                    <a href="{{ route('website.product.show', $product->slug) }}"
                                        class="btn btn-outline-dark btn-sm">
                                        Ver
                                    </a>

                                    <form method="POST" action="{{ route('wishlist.toggle', $product->id) }}">
                                        @csrf
                                        <button type="button" class="btn btn-danger btn-sm wishlist-remove"
                                            data-url="{{ route('wishlist.toggle', $product->id) }}">
                                            <i class="fa-solid fa-heart"></i> Eliminar
                                        </button>
                                    </form>

                                </div>
                            </div>

                        </div>
                    </div>

                @empty

                    <div class="col-12 text-center py-5">
                        <i class="fa-regular fa-heart fa-3x mb-3 text-muted"></i>
                        <h4>Tu lista de deseos está vacía</h4>
                        <p class="text-muted">Explora productos y agrega tus favoritos.</p>
                        <a href="{{ route('website.home') }}" class="btn btn-dark mt-2">
                            Seguir Comprando
                        </a>
                    </div>

                @endforelse
            </div>

        </div>
    </section>
@endsection

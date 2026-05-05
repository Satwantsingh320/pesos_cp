@php
    $pageType = 'inner';
    $pageTitle = 'Productos';
    $breadcrumbTitlecurrent = 'Productos';
@endphp
@extends('website.layouts.layouts')
@section('content')
    <!-- ========= Product Time ======= -->
    <section id="product-time">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-sm-6">
                   <div class="d-flex justify-content-between align-items-center mb-2">
    <h4 class="mb-0">Filtros</h4>
    <button type="button" id="clearFilters" class="btn btn-link p-0 text-danger small">
        Limpiar todo
    </button>
</div>
                    <form id="filterForm" data-url="{{ route('website.products') }}">
                        <div class="categories-list">
                            <h5>Comprar por categorías</h5>

                            <div class="category-list">
                                @foreach ($categories as $category)
                                    <div class="form-check">
                                        <input class="form-check-input" name="categories[]" type="checkbox"
                                            value="{{ $category->id }}" id="category_{{ $category->id }}"
                                            {{ in_array(
                                                $category->id,
                                                array_merge((array) request()->input('categories'), (array) ($activeCategoryId ? [$activeCategoryId] : [])),
                                            )
                                                ? 'checked'
                                                : '' }}>
                                        <label class="form-check-label" for="category_{{ $category->id }}">
                                            {{ $category->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                        </div>
                        {{-- brands --}}
                        <div class="categories-list">
                           <h5>Marcas</h5>
                            <div class="category-list">
                                @foreach ($brands as $brand)
                                    <div class="form-check">
                                        <input class="form-check-input" name="brands[]" type="checkbox"
                                            value="{{ $brand->id }}" id="brand_{{ $brand->id }}"
                                            {{ in_array($brand->id, (array) request()->input('brands')) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="brand_{{ $brand->id }}">
                                            {{ $brand->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        {{-- <div class="categories-list">
                        <h5>Comprar por categorías</h5>
                        <div class="category-list">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="checkDefault">
                                <label class="form-check-label" for="checkDefault">
                                    Carpinteria
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="checkChecked">
                                <label class="form-check-label" for="checkChecked">
                                    Electronicos
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="checkChecked">
                                <label class="form-check-label" for="checkChecked">
                                    Oficina
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="checkChecked">
                                <label class="form-check-label" for="checkChecked">
                                    Industrial
                                </label>
                            </div>
                        </div>
                    </div> --}}
                        @if (request('sort'))
                            <input type="hidden" name="sort" value="{{ request('sort') }}">
                        @endif
                        <div class="categories-list">
                            <h5>Reflejos</h5>
                            <div class="category-list">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="sort" id="all_products"
                                        value="all_products" {{ request('sort') == null ? 'checked' : '' }}>
                                    <label class="form-check-label" for="all_products">
                                        Todos los productos
                                    </label>
                                </div>
                              {{--  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="sort" id="best_seller"
                                        value="best_seller" {{ request('sort') == 'best_seller' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="best_seller">
                                        Productos más vendidos
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="sort" id="new_arrivals"
                                        value="new_arrivals" {{ request('sort') == 'new_arrivals' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="new_arrivals">
                                        Recién llegado
                                    </label>
                                </div> --}}
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="sort" id="featured"
                                        value="featured" {{ request('sort') == 'featured' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="featured">
                                        Productos destacados
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="sort" id="sale"
                                        value="sale" {{ request('sort') == 'sale' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sale">
                                        Artículos en oferta
                                    </label>
                                </div>
                                {{-- <ul>
                                    <li><a href="#">Todos los productos</a></li>
                                    <li><a href="#">Productos más vendidos</a></li>
                                    <li><a href="#">Recién llegado</a></li>
                                    <li><a href="#">Artículos en oferta</a></li>
                                </ul> --}}
                            </div>
                        </div>

                        <div class="categories-list">
                            <h5>Gama de precios</h5>
                            <div class="category-list">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="price_range" value="0-200"
                                        id="price_0_200">
                                    <label class="form-check-label" for="price_0_200">
                                        MX$ 0 - 200
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="price_range" value="200-800"
                                        id="price_200_800">
                                    <label class="form-check-label" for="price_200_800">
                                        MX$ 200 - 800
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="price_range" value="800-2000"
                                        id="price_800_2000">
                                    <label class="form-check-label" for="price_800_2000">
                                        MX$ 800 - 2000
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="price_range" value="2000-10000"
                                        id="price_2000_10000">
                                    <label class="form-check-label" for="price_2000_10000">
                                        MX$ 2000 - 10000
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="price_range"
                                        value="10000-above" id="price_10000_above">
                                    <label class="form-check-label" for="price_10000_above">
                                        Superior a MX$ 10,000
                                    </label>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-lg-9 col-sm-6">
                    {{-- <h5>Showing All 9 Results</h5> --}}
                    <div class="product-list-view mb-3" id="product-list">
                        <div class="product-loader d-none" id="product-loader">
                            <img src="{{ asset('website-assets/img/loader.gif') }}" alt="">
                        </div>
                        @include('website.partials.product-list', ['products' => $products])

                    </div>
                </div>


            </div>
        </div>
    </section>
@endsection

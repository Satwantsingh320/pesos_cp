@php
    $pageType = 'inner';
    $pageTitle = 'Buscar';
    $breadcrumbTitlecurrent = 'Buscar';
@endphp
@extends('website.layouts.layouts')
@section('content')
    <!-- ========= Search products listing ======= -->
    <section id="cat-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 wow fadeInLeft">
                    @if ($keyword)
                        <div class="category-title mb-5">
                            @if ($total > 0)
                                <h3> {{ $total }} Resultados encontrados para "{{ $keyword }}"</h3>
                            @else
                                <h3> No se encontraron resultados "{{ $keyword }}"</h3>
                            @endif
                        </div>
                    @endif

                </div>
            </div>
            <div class="row">
                @foreach ($products as $product)
                    <div class="col-sm-6 col-md-4 col-lg-3 wow fadeInUp">
                        <x-product-card :product="$product" />
                    </div>
                @endforeach
            </div>
            @if ($products->hasPages())
                <div class="d-flex flex-column align-items-center mt-4 gap-2">

                    <p class="text-muted small mb-1">
                        Mostrando {{ $products->firstItem() }} a
                        {{ $products->lastItem() }} de
                        {{ $products->total() }} resultados
                    </p>

                    {{ $products->links('pagination::bootstrap-4') }}

                </div>
            @endif

        </div>
    </section>
@endsection

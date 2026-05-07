@php
    $pageType = 'inner';
    $pageTitle = __('webiste.search_title');
    $breadcrumbTitlecurrent = __('website.search_title');
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
                                <h3>{{ __('website.search_results_found', ['count' => $total, 'keyword' => $keyword]) }}</h3>
                            @else
                                <h3>{{ __('website.search_no_results', ['keyword' => $keyword]) }}</h3>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <div class="row">
                @forelse ($products as $product)
                    <div class="col-sm-6 col-md-4 col-lg-3 wow fadeInUp">
                        <x-product-card :product="$product" />
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="fa-solid fa-search fa-3x mb-3 text-muted"></i>
                        <h4>{{ __('website.search_no_results', ['keyword' => $keyword]) }}</h4>
                        <a href="{{ route('website.home') }}" class="btn btn-dark mt-2">
                            {{ __('website.Back to Home') }}
                        </a>
                    </div>
                @endforelse
            </div>

            @if ($products->hasPages())
                    <div class="d-flex flex-column align-items-center mt-4 gap-2">
                        <p class="text-muted small mb-1">
                            {{ __('website.search_showing_results', [
                    'first' => $products->firstItem(),
                    'last' => $products->lastItem(),
                    'total' => $products->total()
                ]) }}
                        </p>
                        {{ $products->links('pagination::bootstrap-4') }}
                    </div>
            @endif
        </div>
    </section>
@endsection
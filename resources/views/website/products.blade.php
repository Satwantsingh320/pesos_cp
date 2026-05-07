@php
    $pageType = 'inner';
    $pageTitle = __('website.products');
    $breadcrumbTitlecurrent = __('website.products');
@endphp
@extends('website.layouts.layouts')
@section('content')
    <!-- ========= Product Time ======= -->
    <section id="product-time">
        <div class="container">
            <div class="row">

                <div class="col-lg-12 col-sm-6">
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
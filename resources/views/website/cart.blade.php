@php
    $pageType = 'inner';
    $pageTitle = __('website.cart');
    $breadcrumbTitlecurrent = __('website.cart');
@endphp

@extends('website.layouts.layouts')

@section('content')
    <!-- ========= Product Time ======= -->
    <section id="product-time" data-update-url="{{ route('website.cart.updateQuantity') }}"
        data-remove-url="{{ route('website.cart.removeItem') }}">
        <div class="container">
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <div class="d-flex">
                        <div class="me-3">
                            <i class="bx bx-error-circle fs-4"></i>
                        </div>
                        <div>
                            <h6 class="alert-heading fw-bold">{{ __('website.cart_items_changed') }}</h6>
                            <ul class="mb-0 ps-3">
                                @if (is_array(session('error')))
                                    @foreach (session('error') as $message)
                                        <li>{{ $message }}</li>
                                    @endforeach
                                @else
                                    <li>{{ session('error') }}</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                        aria-label="{{ __('website.close') }}"></button>
                </div>
            @endif

            @if ($cartItems->isEmpty())
                <div class="empty-cart text-center">
                    <h3>{{ __('website.empty_cart_title') }}</h3>
                    <p>{{ __('website.empty_cart_message') }}</p>
                    <a href="{{ route('website.products') }}" class="btn btn-dark">
                        {{ __('website.continue_shopping') }}
                    </a>
                </div>
            @else
                <div class="row">
                    <div class="col-lg-12">
                        <div class="cart-title mb-4">
                            <h5>{{ __('website.cart_title', ['count' => $cartItems->count()]) }}</h5>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-8 col-sm-8">
                        <div class="product_information">
                            <div class="border rounded">
                                <table class="table align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('website.image') }}</th>
                                            <th>{{ __('website.product') }}</th>
                                            <th class="text-start">{{ __('website.quantity') }}</th>
                                            <th class="text-center">{{ __('website.price') }}</th>
                                            <th class="text-center">{{ __('website.total') }}</th>
                                            <th class="text-center">{{ __('website.remove') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($cartItems as $item)
                                            @php
                                                // Get variant attributes if exists
                                                $variantDisplay = '';
                                                if ($item->variant_id && $item->variant) {
                                                    $attributes = [];
                                                    if ($item->variant->combinations) {
                                                        foreach ($item->variant->combinations as $combo) {
                                                            if ($combo->attributeValue) {
                                                                $attributes[] = $combo->attributeValue->value;
                                                            }
                                                        }
                                                    }
                                                    $variantDisplay = implode(', ', $attributes);
                                                } elseif ($item->variant_attributes) {
                                                    $variantDisplay = $item->variant_attributes;
                                                }
                                              @endphp
                                            <tr id="cart-row-{{ $item->id }}">
                                                <td>
                                                    <div class="product-img-cart">
                                                        <img src="{{ $item->product->cover_image_url }}" class="img-fluid"
                                                            alt="{{ __('website.product') }}">
                                                    </div>
                                                </td>
                                                <td>
                                                    {{ $item->product->name }}
                                                    @if($variantDisplay)
                                                        <br>
                                                        <small class="text-muted">
                                                            <i class="fas fa-tag"></i> {{ $variantDisplay }}
                                                        </small>
                                                    @endif
                                                    @if($item->variant && $item->variant->sku)
                                                        <br>
                                                        <small class="text-muted">
                                                            {{ __('website.sku') }}: {{ $item->variant->sku }}
                                                        </small>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="quantity" data-context="cart" data-item-id="{{ $item->id }}">
                                                        <a href="#" class="quantity__minus"><span><i
                                                                    class="fa-solid fa-minus"></i></span></a>
                                                        <input name="quantity" type="text" class="quantity__input"
                                                            value="{{ $item->quantity }}" min="1"
                                                            max="{{ $item->product->quantity ?? $item->Quantity }}" readonly>
                                                        <a href="#" class="quantity__plus"><span><i
                                                                    class="fa-solid fa-plus"></i></span></a>
                                                    </div>
                                                </td>
                                                <td class="text-end">{{ CURRENCY }} {{ number_format($item->price_at_time, 2) }}
                                                </td>
                                                <td class="text-end" id="item-total-{{ $item->id }}">{{ CURRENCY }}
                                                    {{ number_format($item->total, 2) }}
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-sm btn-danger remove-cart-item"
                                                        data-id="{{ $item->id }}">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-sm-4">
                        <div class="billing-summary">
                            <h5>{{ __('website.billing_summary') }}</h5>
                            <table class="table mb-0">
                                <tbody>
                                    <tr>
                                        <td>{{ __('website.subtotal') }}:</td>
                                        <td class="text-end" id="subtotal">
                                            {{ CURRENCY }} {{ number_format($cart->subtotal, 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('website.tax') }}:</td>
                                        <td class="text-end" id="tax">
                                            {{ CURRENCY }} {{ number_format($cart->tax_amount, 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('website.shipping_charges') }}:</td>
                                        <td class="text-end" id="shipping">
                                            {{ CURRENCY }} {{ number_format($cart->shipping_amount, 2) }}
                                        </td>
                                    </tr>
                                    <tr id="discount-row" style="{{ $cart->discount_amount > 0 ? '' : 'display:none' }}">
                                        <td class="text-green">{{ __('website.discount') }}:</td>
                                        <td class="text-end text-green" id="discount">
                                            - {{ CURRENCY }} {{ number_format($cart->discount_amount, 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('website.grand_total') }}:</strong></td>
                                        <td class="text-end" id="grand_total">
                                            <strong>{{ CURRENCY }}
                                                {{ number_format($cart->grand_total - $cart->discount_amount, 2) }}</strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="proceed-checkout">
                            <a class="btn btn-dark"
                                href="{{ route('website.checkout') }}">{{ __('website.proceed_to_checkout') }}</a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection

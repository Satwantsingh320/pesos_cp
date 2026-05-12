@php
  $pageType = 'inner';
  $pageTitle = __('website.checkout');
  $breadcrumbTitlecurrent = __('website.checkout');
$states = [
    'K'  => __('Blekinge County'),
    'W'  => __('Dalarna County'),
    'I'  => __('Gotland County'),
    'X'  => __('Gävleborg County'),
    'N'  => __('Halland County'),
    'Z'  => __('Jämtland County'),
    'F'  => __('Jönköping County'),
    'H'  => __('Kalmar County'),
    'G'  => __('Kronoberg County'),
    'BD' => __('Norrbotten County'),
    'M'  => __('Skåne County'),
    'AB' => __('Stockholm County'),
    'D'  => __('Södermanland County'),
    'C'  => __('Uppsala County'),
    'S'  => __('Värmland County'),
    'AC' => __('Västerbotten County'),
    'Y'  => __('Västernorrland County'),
    'U'  => __('Västmanland County'),
    'O'  => __('Västra Götaland County'),
    'T'  => __('Örebro County'),
    'E'  => __('Östergötland County'),
];
@endphp

@extends('website.layouts.layouts')

@section('content')
  <section id="product-time">
    <div class="container">
      <div class="row">
        <div class="col-lg-8 col-sm-8">
          <form action="{{ route('website.proceed.payment') }}" method="POST" id="checkoutForm">
            @csrf
            <div class="accordion" id="checkoutAccordion">

              {{-- Shipping Address --}}
              <div class="accordion-item shadow-sm">
                <h2 class="accordion-header">
                  <button class="accordion-button" type="button" data-bs-toggle="collapse"
                    data-bs-target="#shippingCollapse">
                    {{ __('website.shipping_address') }}
                  </button>
                </h2>
                <div id="shippingCollapse" class="accordion-collapse collapse show" data-bs-parent="#checkoutAccordion">
                  <div class="accordion-body">

                    <div id="shipping-fields-wrapper">
                      @if ($isLoggedIn)
                        <div id="saved-addresses-container">
                          <label class="form-label fw-bold">{{ __('website.select_saved_address') }}</label>
                          @foreach ($savedAddresses as $index => $addr)
                            <div class="form-check border rounded p-3 mb-2 address-card">
                              <input class="form-check-input" type="radio" name="saved_billing_id"
                                id="addr_{{ $addr['id'] }}" value="{{ $addr['id'] }}"
                                {{ $addr['is_default'] == 1 ? 'checked' : '' }}>
                              <label class="form-check-label w-100" for="addr_{{ $addr['id'] }}">
                                <strong>{{ $addr['address'] }}</strong><br>
                                <small class="text-muted">{{ $addr['address'] }}, {{ $addr['colonia'] }},
                                  {{ $addr['state'] }},
                                  {{ $addr['city'] }} ({{ $addr['postcode'] }})</small>
                              </label>
                            </div>
                          @endforeach
                          <button type="button" class="btn btn-link btn-sm p-0 mb-3" id="toggleNewAddress"
                            data-mode="saved">
                            {{ __('website.add_new_address') }}
                          </button>
                        </div>
                        <button type="button" id="showSavedAddress" class="btn btn-link p-0 mt-2 d-none">
                          {{ __('website.use_saved_address') }}
                        </button>
                      @else
                        <button type="button" class="btn btn-link btn-sm p-0 mb-3" id="toggleNewAddress"
                          data-mode="manual"></button>
                      @endif

                      <div id="manual-billing-form" class="<?php echo $isLoggedIn ? 'd-none' : ''; ?>">
                        <div class="mb-3">
                          <label class="form-label">{{ __('website.recipient_name') }}</label>
                          <input type="text" name="shipping_name" id="shipping_name" class="form-control shadow-none">
                        </div>
                        <div class="row">
                          <div class="col-4 mb-3">
                            <label class="form-label">{{ __('website.area_code') }}</label>
                            <input type="text" name="shipping_dial_code" id="shipping_dial_code"
                              class="form-control shadow-none" placeholder="+46" value="+46">
                          </div>
                          <div class="col-8 mb-3">
                            <label class="form-label">{{ __('website.phone') }}</label>
                            <input type="number" name="shipping_phone" id="shipping_phone"
                              class="form-control shadow-none">
                          </div>
                        </div>
                        <div class="mb-3">
                          <label class="form-label">{{ __('website.shipping_address_label') }}</label>
                          <textarea name="shipping_address" id="shipping_address" class="form-control shadow-none" rows="2"></textarea>
                        </div>
                        <div class="row">
                          <div class="col-6 mb-3">
                            <label class="form-label">{{ __('website.colonia') }}</label>
                            <input type="text" name="shipping_colonia" id="shipping_colonia"
                              class="form-control shadow-none">
                          </div>
                          <div class="col-6 mb-3">
                            <label class="form-label">{{ __('website.city') }}</label>
                            <input type="text" name="shipping_city" id="shipping_city"
                              class="form-control shadow-none">
                          </div>
                          <div class="col-6 mb-3">
                            <label class="form-label">{{ __('website.state') }}</label>
                            <select name="shipping_state" id="shipping_state" class="form-control">
                              <option value="">{{ __('website.select_state') }}</option>
                              @foreach ($states as $code => $state)
                                <option value="{{ $code }}"
                                  {{ old('shipping_state') == $code ? 'selected' : '' }}>
                                  {{ $state }}
                                </option>
                              @endforeach
                            </select>
                          </div>
                          <div class="col-6 mb-3">
                            <label class="form-label">{{ __('website.postal_code') }}</label>
                            <input type="text" name="shipping_postcode" id="shipping_postcode"
                              class="form-control shadow-none">
                          </div>
                          <div class="col-6 mb-3">
                            <label class="form-label">{{ __('website.address_type') }}</label>
                            <select name="shipping_type" class="form-select">
                              <option value="Home">{{ __('website.home') }}</option>
                              <option value="Office">{{ __('website.office') }}</option>
                              <option value="Other">{{ __('website.other') }}</option>
                            </select>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              {{-- Billing Address --}}
              <div class="accordion-item shadow-sm mt-3">
                <h2 class="accordion-header">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#billingCollapse">
                    {{ __('website.billing_address') }}
                  </button>
                </h2>
                <div id="billingCollapse" class="accordion-collapse collapse" data-bs-parent="#checkoutAccordion">
                  <div class="accordion-body">
                    <input class="form-check-input" type="checkbox" id="sameAsShipping" name="same_as_shipping"
                      checked>
                    <label class="form-check-label fw-bold mb-3" for="sameAsShipping">
                      {{ __('website.billing_same_as_shipping') }}
                    </label>
                    <div id="billing-fields-wrapper">
                      <div class="mb-3">
                        <label class="form-label">{{ __('website.billing_name') }}</label>
                        <input type="text" name="billing_name" id="billing_name" class="form-control shadow-none"
                          data-required="true">
                      </div>
                      <div class="row">
                        <div class="col-4 mb-3">
                          <label class="form-label">{{ __('website.area_code') }}</label>
                          <input type="text" name="billing_dial_code" id="billing_dial_code"
                            class="form-control shadow-none" placeholder="+46" value="+46" data-required="true">
                        </div>
                        <div class="col-8 mb-3">
                          <label class="form-label">{{ __('website.phone') }}</label>
                          <input type="number" name="billing_phone" id="billing_phone"
                            class="form-control shadow-none" data-required="true">
                        </div>
                      </div>
                      <div class="mb-3">
                        <label class="form-label">{{ __('website.billing_address_label') }}</label>
                        <textarea name="billing_address" id="billing_address" class="form-control" rows="2" data-required="true"></textarea>
                      </div>
                      <div class="row">
                        <div class="col-6 mb-3">
                          <label class="form-label">{{ __('website.colonia') }}</label>
                          <input type="text" name="billing_colonia" id="billing_colonia"
                            class="form-control shadow-none">
                        </div>
                        <div class="col-6 mb-3">
                          <label class="form-label">{{ __('website.city') }}</label>
                          <input type="text" name="billing_city" id="billing_city" class="form-control"
                            data-required="true">
                        </div>
                        <div class="col-6 mb-3">
                          <label class="form-label">{{ __('website.state') }}</label>
                          <select name="billing_state" id="billing_state" class="form-control" data-required="true">
                            <option value="">{{ __('website.select_state') }}</option>
                            @foreach ($states as $code => $state)
                              <option value="{{ $code }}"
                                {{ old('billing_state') == $code ? 'selected' : '' }}>
                                {{ $state }}
                              </option>
                            @endforeach
                          </select>
                        </div>
                        <div class="col-6 mb-3">
                          <label class="form-label">{{ __('website.postal_code') }}</label>
                          <input type="text" name="billing_postcode" id="billing_postcode" class="form-control">
                        </div>
                        <div class="col-6 mb-3">
                          <label class="form-label">{{ __('website.address_type') }}</label>
                          <select name="billing_type" class="form-select" data-required="true">
                            <option value="Home">{{ __('website.home') }}</option>
                            <option value="Office">{{ __('website.office') }}</option>
                            <option value="Other">{{ __('website.other') }}</option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>

        {{-- Promo and Order Summary --}}
        <div class="col-lg-4 col-sm-4">
          <div class="promo-code-box mb-2 d-none">
            <h5>{{ __('website.promo_code') }}</h5>
            <form class="ajax-form" data-url="{{ route('website.cart.applyCoupon') }}" data-type="billingUpdate"
              data-method="post">
              @csrf
              <div class="input-group mb-2">
                <input type="text" class="form-control" placeholder="{{ __('website.enter_promo_code') }}"
                  name="code" aria-label="{{ __('website.promo_code') }}" aria-describedby="button-addon2" required
                  value="{{ $cart->coupon_code }}" {{ $cart->coupon_code ? 'readonly' : '' }}>

                @if ($cart->coupon_code)
                  <button class="btn btn-success" type="button" id="button-addon2" disabled>
                    <i class="bx bx-check"></i> {{ __('website.applied') }}
                  </button>
                  <a href="{{ route('website.cart.remove-coupon') }}" class="btn btn-danger">
                    <i class="fa fa-trash font-size-14 align-middle me-1"></i>
                  </a>
                @else
                  <button class="btn btn-outline-dark" type="submit" id="button-addon2">{{ __('website.apply') }}</button>
                @endif
              </div>
            </form>
          </div>

          <div class="order-summary">
            <h4>{{ __('website.order_summary') }}</h4>
            <table class="table">
              <tbody>
                @foreach ($cartItems as $item)
                  @php
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

                    $maxStock = $item->variant
                        ? $item->variant->quantity
                        : ($item->product->quantity ?? $item->quantity);
                  @endphp
                  <tr id="cart-row-{{ $item->id }}">
                    <td class="w-20">
                      <div class="product_img">
                        @if($variantDisplay && !empty($item->variant->image))
                                                            <img src="{{ asset('uploads/products') . '/' . $item->variant->image }}"
                                                                class="img-fluid" alt="{{ __('website.product') }}">
                                                        @else
                                                            <img src="{{ $item->product->cover_image_url }}" class="img-fluid"
                                                                alt="{{ __('website.product') }}">
                                                        @endif

                      </div>
                    </td>
                    <td>
                      <div class="product-info-div">
                        <a href="#">{{ $item->product->name }}</a>

                        @if($variantDisplay)
                          <div class="variant-info mt-1">
                            <small class="text-muted">
                              <i class="fas fa-tag"></i> {{ $variantDisplay }}
                            </small>
                            @if($item->variant && $item->variant->sku)
                              <br>
                              <small class="text-muted">
                                {{ __('website.sku') }}: {{ $item->variant->sku }}
                              </small>
                            @endif
                          </div>
                        @endif

                        <div class="price-list">
                          <div class="list-price-box">
                            <p>{{ CURRENCY }} {{ number_format($item->total, 2) }}</p>
                            <small class="text-muted d-block d-none">
                              ({{ CURRENCY }} {{ number_format($item->price_at_time, 2) }} {{ __('website.each') }})
                            </small>
                          </div>
                        </div>
                        <div class="quantity mt-2">
                          <input name="quantity" type="text" class="quantity__input" value="{{ $item->quantity }}"
                            min="1" max="{{ $maxStock }}" readonly>
                        </div>
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <div class="billing-summary">
            <h5>{{ __('website.billing_summary') }}</h5>
            <table class="table mb-0">
              <tbody>
                <tr>
                  <td>{{ __('website.subtotal') }}:</td>
                  <td class="text-end" id="subtotal">{{ CURRENCY }} {{ number_format($cart->subtotal, 2) }}</td>
                </tr>
                <tr>
                  <td>{{ __('website.tax') }}:</td>
                  <td class="text-end" id="tax">{{ CURRENCY }} {{ number_format($cart->tax_amount, 2) }}</td>
                </tr>
                <tr>
                  <td>{{ __('website.shipping_charges') }}:</td>
                  <td class="text-end" id="shipping">{{ CURRENCY }} {{ number_format($cart->shipping_amount, 2) }}</td>
                </tr>
                <tr id="discount-row" style="{{ $cart->discount_amount > 0 ? '' : 'display:none' }}">
                  <td class="text-green">{{ __('website.discount') }}:</td>
                  <td class="text-end text-green" id="discount">- {{ CURRENCY }} {{ number_format($cart->discount_amount, 2) }}</td>
                </tr>
                <tr>
                  <td><strong>{{ __('website.total') }}:</strong></td>
                  <td class="text-end" id="grand_total">
                    <strong>{{ CURRENCY }} {{ number_format($cart->grand_total - $cart->discount_amount, 2) }}</strong>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="proceed-checkout">
            @if ($isLoggedIn)
              <button type="submit" id="externalSubmitBtn" class="btn btn-dark btn-lg w-100 mt-4">
                {{ __('website.proceed_to_payment') }}
              </button>
            @else
              <a href="{{ route('login') }}" class="btn btn-dark btn-lg w-100 mt-4">
                {{ __('website.proceed_to_payment') }}
              </a>
            @endif
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection


@php
  $pageType = 'inner';
  $pageTitle = 'Pago';
  $breadcrumbTitlecurrent = 'Pago';
  $states = [
      'AGU' => 'Aguascalientes',
      'BCN' => 'Baja California',
      'BCS' => 'Baja California Sur',
      'CAM' => 'Campeche',
      'CHP' => 'Chiapas',
      'CHH' => 'Chihuahua',
      'CMX' => 'Ciudad de México',
      'COA' => 'Coahuila',
      'COL' => 'Colima',
      'DUR' => 'Durango',
      'GUA' => 'Guanajuato',
      'GRO' => 'Guerrero',
      'HID' => 'Hidalgo',
      'JAL' => 'Jalisco',
      'MEX' => 'Estado de México',
      'MIC' => 'Michoacán',
      'MOR' => 'Morelos',
      'NAY' => 'Nayarit',
      'NLE' => 'Nuevo León',
      'OAX' => 'Oaxaca',
      'PUE' => 'Puebla',
      'QUE' => 'Querétaro',
      'ROO' => 'Quintana Roo',
      'SLP' => 'San Luis Potosí',
      'SIN' => 'Sinaloa',
      'SON' => 'Sonora',
      'TAB' => 'Tabasco',
      'TAM' => 'Tamaulipas',
      'TLA' => 'Tlaxcala',
      'VER' => 'Veracruz',
      'YUC' => 'Yucatán',
      'ZAC' => 'Zacatecas',
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
                    1. Dirección de Envío
                  </button>
                </h2>
                <div id="shippingCollapse" class="accordion-collapse collapse show" data-bs-parent="#checkoutAccordion">
                  <div class="accordion-body">

                    <div id="shipping-fields-wrapper">
                      @if ($isLoggedIn)
                        <div id="saved-addresses-container">
                          <label class="form-label fw-bold">Selecciona una dirección guardada</label>
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
                            + Agregar una nueva dirección
                          </button>
                        </div>
                        <button type="button" id="showSavedAddress" class="btn btn-link p-0 mt-2 d-none"> ← Usar
                          dirección guardada
                        </button>
                      @else
                        <button type="button" class="btn btn-link btn-sm p-0 mb-3" id="toggleNewAddress"
                          data-mode="manual"> </button>
                      @endif
                      <div id="manual-billing-form" class="<?php echo $isLoggedIn ? 'd-none' : ''; ?>">
                        <div class="mb-3">
                          <label class="form-label">Nombre del destinatario</label>
                          <input type="text" name="shipping_name" id="shipping_name" class="form-control shadow-none">
                        </div>
                        <div class="row">
                          <div class="col-4 mb-3">
                            <label class="form-label">Código de Area</label>
                            <input type="text" name="shipping_dial_code" id="shipping_dial_code"
                              class="form-control shadow-none" placeholder="+52" value="+52">
                          </div>
                          <div class="col-8 mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="number" name="shipping_phone" id="shipping_phone"
                              class="form-control shadow-none">
                          </div>
                        </div>
                        <div class="mb-3">
                          <label class="form-label">Dirección de envío</label>
                          <textarea name="shipping_address" id="shipping_address" class="form-control shadow-none" rows="2"></textarea>
                        </div>
                        <div class="row">
                          <div class="col-6 mb-3">
                            <label class="form-label">Colonia</label>
                            <input type="text" name="shipping_colonia" id="shipping_colonia"
                              class="form-control shadow-none">
                          </div>
                          <div class="col-6 mb-3">
                            <label class="form-label">Ciudad</label>
                            <input type="text" name="shipping_city" id="shipping_city"
                              class="form-control shadow-none">
                          </div>
                          <div class="col-6 mb-3">
                            <label class="form-label">Estado</label>
                            <select name="shipping_state" id="shipping_state" class="form-control">
                              <option value="">Seleccionar Estado</option>
                              @foreach ($states as $code => $state)
                                <option value="{{ $code }}"
                                  {{ old('shipping_state') == $code ? 'selected' : '' }}>
                                  {{ $state }}
                                </option>
                              @endforeach
                            </select>
                          </div>
                          <div class="col-6 mb-3">
                            <label class="form-label">Código Postal</label>
                            <input type="text" name="shipping_postcode" id="shipping_postcode"
                              class="form-control shadow-none">
                          </div>
                          <div class="col-6 mb-3">
                            <label class="form-label">Tipo</label>
                            <select name="shipping_type" class="form-select">
                              <option value="Home">Casa</option>
                              <option value="Office">Oficina</option>
                              <option value="Other">Otro</option>
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
                    2. Dirección de Facturación
                  </button>
                </h2>
                <div id="billingCollapse" class="accordion-collapse collapse" data-bs-parent="#checkoutAccordion">
                  <div class="accordion-body">
                    <input class="form-check-input" type="checkbox" id="sameAsShipping" name="same_as_shipping"
                      checked>

                    <label class="form-check-label fw-bold mb-3" for="sameAsShipping">
                      La dirección de facturación es la misma que la de envío
                    </label>
                    <div id="billing-fields-wrapper">

                      <div class="mb-3">
                        <label class="form-label">Nombre del facturador</label>
                        <input type="text" name="billing_name" id="billing_name" class="form-control shadow-none"
                          data-required="true">
                      </div>
                      <div class="row">
                        <div class="col-4 mb-3">
                          <label class="form-label">Código de Area</label>
                          <input type="text" name="billing_dial_code" id="billing_dial_code"
                            class="form-control shadow-none" placeholder="+52" value="+52" data-required="true">
                        </div>
                        <div class="col-8 mb-3">
                          <label class="form-label">Teléfono</label>
                          <input type="number" name="billing_phone" id="billing_phone"
                            class="form-control shadow-none" data-required="true">
                        </div>
                      </div>
                      <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <textarea name="billing_address" id="billing_address" class="form-control" rows="2" data-required="true"></textarea>
                      </div>
                      <div class="row">
                        <div class="col-6 mb-3">
                          <label class="form-label">Colonia</label>
                          <input type="text" name="billing_colonia" id="billing_colonia"
                            class="form-control shadow-none">
                        </div>
                        <div class="col-6 mb-3">
                          <label class="form-label">Ciudad</label>
                          <input type="text" name="billing_city" id="billing_city" class="form-control"
                            data-required="true">
                        </div>
                        <div class="col-6 mb-3">
                          <label class="form-label">Estado</label>
                          <select name="billing_state" id="billing_state" class="form-control" data-required="true">
                            <option value="">Seleccionar Estado</option>
                            @foreach ($states as $code => $state)
                              <option value="{{ $code }}"
                                {{ old('billing_state') == $code ? 'selected' : '' }}>
                                {{ $state }}
                              </option>
                            @endforeach
                          </select>
                        </div>
                        <div class="col-6 mb-3">
                          <label class="form-label">Código Postal</label>
                          <input type="text" name="billing_postcode" id="billing_postcode" class="form-control">
                        </div>
                        <div class="col-6 mb-3">
                          <label class="form-label">Tipo</label>
                          <select name="billing_type" class="form-select" data-required="true">
                            <option value="Home">Casa</option>
                            <option value="Office">Oficina</option>
                            <option value="Other">Otro</option>
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
          <div class="promo-code-box mb-2">
            <h5>Código Promocional</h5>
            <form class="ajax-form" data-url="{{ route('website.cart.applyCoupon') }}" data-type="billingUpdate"
              data-method="post">
              @csrf
              <div class="input-group mb-2">
                <input type="text" class="form-control" placeholder="Ingresa el código promocional..."
                  name="code" aria-label="Código promocional" aria-describedby="button-addon2" required
                  value="{{ $cart->coupon_code }}" {{ $cart->coupon_code ? 'readonly' : '' }}>

                @if ($cart->coupon_code)
                  <button class="btn btn-success" type="button" id="button-addon2" disabled>
                    <i class="bx bx-check"></i> Aplicado
                  </button>
                  <a href="{{ route('website.cart.remove-coupon') }}" class="btn btn-danger"><i
                      class="fa fa-trash font-size-14 align-middle me-1"></i></a>
                @else
                  <button class="btn btn-outline-dark" type="submit" id="button-addon2">Aplicar</button>
                @endif
              </div>
            </form>
          </div>

          <div class="order-summary">
            <h4>Resumen del Pedido</h4>
            <table class="table">
              <tbody>
                @foreach ($cartItems as $item)
                  <tr id="cart-row-{{ $item->id }}">
                    <td class="w-20">
                      <div class="product_img">
                        <img src="{{ $item->product->cover_image_url }}" class="img-fluid" alt="producto">
                      </div>
                    </td>
                    <td>
                      <div class="product-info-div">
                        <a href="#">{{ $item->product->name }}</a>
                        <div class="price-list">
                          <div class="list-price-box">
                            <p>MX$ {{ number_format($item->total, 2) }}</p>
                          </div>
                        </div>
                        <div class="quantity mt-2">
                          <input name="quantity" type="text" class="quantity__input" value="{{ $item->quantity }}"
                            min="1" max="{{ $item->product->no_of_pieces_available ?? $item->Quantity }}"
                            readonly>
                        </div>
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <div class="billing-summary">
            <h5>Resumen de Facturación</h5>
            <table class="table mb-0">
              <tbody>
                <tr>
                  <td>Subtotal:</td>
                  <td class="text-end" id="subtotal">MX$ {{ number_format($cart->subtotal, 2) }}</td>
                </tr>

                <tr>
                  <td>Impuestos:</td>
                  <td class="text-end" id="tax">MX$ {{ number_format($cart->tax_amount, 2) }}</td>
                </tr>
                <tr>
                  <td>Cargos de envío:</td>
                  <td class="text-end" id="shipping">MX$ {{ number_format($cart->shipping_amount, 2) }}
                  </td>
                </tr>
                <tr id="discount-row" style="{{ $cart->discount_amount > 0 ? '' : 'display:none' }}">
                  <td class="text-green">Descuento:</td>
                  <td class="text-end text-green" id="discount">- MX$
                    {{ number_format($cart->discount_amount, 2) }}
                  </td>
                </tr>
                <tr>
                  <td><strong>Total:</strong></td>
                  <td class="text-end" id="grand_total"><strong>MX$
                      {{ number_format($cart->grand_total - $cart->discount_amount, 2) }}</strong>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="proceed-checkout">
            @if ($isLoggedIn)
              <button type="submit" id="externalSubmitBtn" class="btn btn-dark btn-lg w-100 mt-4">Proceder al
                Pago</button>
            @else
              <a href="{{ route('login') }}" class="btn btn-dark btn-lg w-100 mt-4">Proceder al
                Pago</a>
            @endif
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection

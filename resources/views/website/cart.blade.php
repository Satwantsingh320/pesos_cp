@php
  $pageType = 'inner';
  $pageTitle = 'Carrito';
  $breadcrumbTitlecurrent = 'Carrito';
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
              <h6 class="alert-heading fw-bold">Los artículos en tu carrito han cambiado:</h6>
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
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
      @endif

      @if ($cartItems->isEmpty())
        <div class="empty-cart text-center">
          <h3>Tu carrito está vacío</h3>
          <p>Agrega productos a tu carrito para verlos aquí.</p>
          <a href="{{ route('website.products') }}" class="btn btn-dark">
            Seguir comprando
          </a>
        </div>
      @else
        <div class="row">
          <div class="col-lg-12">
            <div class="cart-title mb-4">
              <h5>Mi Carrito ({{ $cartItems->count() }} artículos)</h5>
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
                      <th>Imagen</th>
                      <th>Producto</th>
                      <th class="text-start">Cantidad</th>
                      <th class="text-center">Precio</th>
                      <th class="text-center">Total</th>
                      <th class="text-center">Eliminar</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($cartItems as $item)
                      <tr id="cart-row-{{ $item->id }}">
                        <td>
                          <div class="product-img-cart">
                            <img src="{{ $item->product->cover_image_url }}" class="img-fluid" alt="producto">
                          </div>
                        </td>
                        <td>{{ $item->product->name }}</td>
                        <td class="text-center">
                          <div class="quantity" data-context="cart" data-item-id="{{ $item->id }}">
                            <a href="#" class="quantity__minus"><span><i class="fa-solid fa-minus"></i></span></a>
                            <input name="quantity" type="text" class="quantity__input" value="{{ $item->quantity }}"
                              min="1" max="{{ $item->product->no_of_pieces_available ?? $item->Quantity }}"
                              readonly>
                            <a href="#" class="quantity__plus"><span><i class="fa-solid fa-plus"></i></span></a>
                          </div>
                        </td>
                        <td class="text-end">MX$ {{ number_format($item->price_at_time, 2) }}</td>
                        <td class="text-end" id="item-total-{{ $item->id }}">MX$
                          {{ number_format($item->total, 2) }}
                        </td>
                        <td class="text-center">
                          <button class="btn btn-sm btn-danger remove-cart-item" data-id="{{ $item->id }}"><i
                              class="fa-solid fa-trash"></i></button>
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
              <h5>Resumen de Facturación</h5>
              <table class="table mb-0">
                <tbody>
                  <tr>
                    <td>Subtotal:</td>
                    <td class="text-end" id="subtotal">
                      MX$ {{ number_format($cart->subtotal, 2) }}
                    </td>
                  </tr>

                  <tr>
                    <td>Impuestos:</td>
                    <td class="text-end" id="tax">
                      MX$ {{ number_format($cart->tax_amount, 2) }}</td>
                  </tr>
                  <tr>
                    <td>Cargos de envío:</td>
                    <td class="text-end" id="shipping">
                      MX$ {{ number_format($cart->shipping_amount, 2) }}</td>
                  </tr>
                  <tr id="discount-row" style="{{ $cart->discount_amount > 0 ? '' : 'display:none' }}">
                    <td class="text-green">Descuento:</td>
                    <td class="text-end text-green" id="discount">
                      - MX$ {{ number_format($cart->discount_amount, 2) }}
                    </td>
                  </tr>
                  <tr>
                    <td><strong>Total:</strong></td>
                    <td class="text-end" id="grand_total">
                      <strong>MX$
                        {{ number_format($cart->grand_total - $cart->discount_amount, 2) }}</strong>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="proceed-checkout">
              <a class="btn btn-dark" href="{{ route('website.checkout') }}">Proceder al pago</a>
            </div>
          </div>
        </div>
      @endif
    </div>
  </section>
@endsection

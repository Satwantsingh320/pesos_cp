<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title>Factura de pedido</title>
</head>

<body style="margin:0; padding:0; background:#f4f6f8; font-family: Arial, Helvetica, sans-serif;">

  @php
    $billing = $order->address['billing'] ?? [];
    $shipping = $order->address['shipping'] ?? [];

    $shippingAddress = !empty($shipping['address']) ? $shipping : $billing;

    $subtotal = $order->sub_total ?? $order->price;
    $tax = $order->tax ?? 0;
    $shippingAmount = $order->shipping ?? 0;
    $discount = $order->discount ?? 0;
  @endphp

  <table width="100%" cellpadding="0" cellspacing="0" style="padding:30px 0;">
    <tr>
      <td align="center">

        <table width="650" cellpadding="0" cellspacing="0"
          style="background:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 3px 10px rgba(0,0,0,0.05);">

          <!-- Header -->
          <tr>
            <td style="background:#111827; color:#ffffff; padding:25px;">
              <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                  <!-- Left content -->
                  <td align="left" style="vertical-align:middle;">
                    <h2 style="margin:0;">
                      @if ($isAdmin ?? false)
                        🛒 Nuevo pedido recibido
                      @else
                        ¡Gracias por su pedido!
                      @endif
                    </h2>

                    <p style="margin:5px 0 0; font-size:14px; opacity:0.8;">
                      Orden #{{ $order->order_number }}
                    </p>
                  </td>

                  <!-- Right logo -->
                  <td align="right" style="vertical-align:middle;">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" height="50"
                      style="max-height:50px; display:block;">
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Order Summary -->
          <tr>
            <td style="padding:25px;">
              <table width="100%">
                <tr>
                  <td width="50%">
                    <strong>Fecha del pedido:</strong><br>
                    {{ $order->created_at->format('d M Y') }}
                  </td>
                  <td width="50%">
                    <strong>Tipo de pago:</strong><br>
                    {{ ucfirst($order->payment_type) }}
                  </td>
                </tr>
                <tr>
                  <td style="padding-top:10px;">
                    <strong>Estado de pago:</strong><br>
                    {{ ucfirst($order->payment_status) }}
                  </td>
                  <td style="padding-top:10px;">
                    <strong>Gran total:</strong><br>
                    MX${{ number_format($order->price, 2) }}
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Addresses -->
          <tr>
            <td style="padding:0 25px 20px;">
              <table width="100%">
                <tr>
                  <td width="50%" valign="top">
                    <h4 style="border-bottom:1px solid #e5e7eb; padding-bottom:5px;">Dirección de Envio
                    </h4>
                    <p style="margin:0; line-height:1.6;">
                      {{ $billing['address'] ?? '-' }}<br>
                      {{ $billing['city'] ?? '' }},
                      {{ $billing['state'] ?? '' }} -
                      {{ $billing['postcode'] ?? '' }}<br>
                      Phone: {{ $billing['phone'] ?? '-' }}<br>
                      Type: {{ $billing['type'] ?? '-' }}
                    </p>
                  </td>

                  <td width="50%" valign="top">
                    <h4 style="border-bottom:1px solid #e5e7eb; padding-bottom:5px;">Envío DIRECCIÓN</h4>
                    <p style="margin:0; line-height:1.6;">
                      {{ $shippingAddress['address'] ?? '-' }}<br>
                      {{ $shippingAddress['city'] ?? '' }},
                      {{ $shippingAddress['state'] ?? '' }} -
                      {{ $shippingAddress['postcode'] ?? '' }}<br>
                      Teléfono: {{ $shippingAddress['phone'] ?? '-' }}<br>
                      Tipo: {{ $shippingAddress['type'] ?? '-' }}
                    </p>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Items -->
          <tr>
            <td style="padding:0 25px 25px;">
              <h4 style="border-bottom:1px solid #e5e7eb; padding-bottom:5px;">Ordenar artículos</h4>

              <table width="100%" cellpadding="10" cellspacing="0" style="border-collapse:collapse; font-size:14px;">
                <tr style="background:#f9fafb;">
                  <th align="left" style="border-bottom:1px solid #ddd;">Producto</th>
                  <th align="center" style="border-bottom:1px solid #ddd;">Cantidad</th>
                  <th align="right" style="border-bottom:1px solid #ddd;">Precio</th>
                  <th align="right" style="border-bottom:1px solid #ddd;">Total</th>
                </tr>

                @foreach ($order->items as $item)
                  <tr>
                    <td style="border-bottom:1px solid #eee;">{{ $item->name }}</td>
                    <td align="center" style="border-bottom:1px solid #eee;">{{ $item->quantity }}</td>
                    <td align="right" style="border-bottom:1px solid #eee;">
                      MX${{ number_format($item->price, 2) }}
                    </td>
                    <td align="right" style="border-bottom:1px solid #eee;">
                      MX${{ number_format($item->price * $item->quantity, 2) }}
                    </td>
                  </tr>
                @endforeach
              </table>

              <!-- Totals -->
              <table width="100%" cellpadding="10" cellspacing="0" style="margin-top:15px;">
                <tr>
                  <td align="right">Total parcial:</td>
                  <td align="right" width="150">
                    MX${{ number_format($subtotal, 2) }}
                  </td>
                </tr>
                @if ($shippingAmount > 0)
                  <tr>
                    <td align="right">Envío:</td>
                    <td align="right">MX${{ number_format($shippingAmount, 2) }}</td>
                  </tr>
                @endif

                @if ($tax > 0)
                  <tr>
                    <td align="right">Impuesto:</td>
                    <td align="right">MX${{ number_format($tax, 2) }}</td>
                  </tr>
                @endif



                @if ($discount > 0)
                  <tr>
                    <td align="right">Descuento:</td>
                    <td align="right">- MX${{ number_format($discount, 2) }}</td>
                  </tr>
                @endif

                <tr>
                  <td align="right" style="font-size:16px; font-weight:bold;">
                    Gran total:
                  </td>
                  <td align="right" style="font-size:16px; font-weight:bold;">
                    MX${{ number_format($order->price, 2) }}
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td style="background:#f9fafb; padding:20px; text-align:center; font-size:12px; color:#6b7280;">
              © {{ date('Y') }} {{ config('app.name') }}. Reservados todos los derechos.
            </td>
          </tr>

        </table>

      </td>
    </tr>
  </table>

</body>

</html>

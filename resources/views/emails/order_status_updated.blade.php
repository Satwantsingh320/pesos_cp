<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title>Actualización del estado del pedido</title>
</head>

<body style="margin:0; padding:0; background:#f4f6f9; font-family: Arial, sans-serif;">

  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f9; padding:20px 0;">
    <tr>
      <td align="center">

        <!-- Main Container -->
        <table width="600" cellpadding="0" cellspacing="0"
          style="background:#ffffff; border-radius:8px; overflow:hidden;">

          <!-- Header -->
          <tr>
            <td style="background:#0d6efd; color:#ffffff; padding:20px;">
              <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                  <!-- Left content -->
                  <td align="left" style="vertical-align:middle;">
                    <h2 style="margin:0;">Actualización de pedido</h2>
                    <p style="margin:5px 0 0;">#{{ $order->order_number }}</p>
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

          <!-- Body -->
          <tr>
            <td style="padding:25px; color:#333333;">

              <p style="margin:0 0 10px;">
                Hola <strong>{{ $order->customer->name ?? 'Customer' }}</strong>,
              </p>

              <p style="margin:0 0 15px;">
                El estado de su pedido se ha actualizado correctamente.
              </p>

              <!-- Status Box -->
              <table width="100%" cellpadding="10" cellspacing="0"
                style="background:#f8f9fa; border-radius:6px; margin-bottom:15px;">
                <tr>
                  <td>
                    <strong>Estado:</strong>
                    <span style="color:#0d6efd; font-weight:bold;">
                      {{ $statusLabel }}
                    </span>
                  </td>
                </tr>
                <tr>
                  <td>
                    <strong>Fecha:</strong>
                    {{ now()->format('d M Y H:i') }}
                  </td>
                </tr>
              </table>

              <p style="margin:20px 0 0;">
                Gracias por comprar con nosotros.
              </p>

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

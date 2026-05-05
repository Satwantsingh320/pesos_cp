@php
  $pageType = 'Terms';

@endphp
@extends('website.layouts.layouts')

@section('content')
  <div class="account-pages my-1 pt-sm-1">
    <div class="container py-1">
      <div class=""> {{-- card shadow-sm p-5 --}}

        <h1 class="mb-4 text-center">Términos y Condiciones de Venta</h1>

        <p>Las presentes condiciones regulan la compra de productos ofrecidos a través del sitio web
          {{ config('app.name') }}, en cumplimiento de la normativa española vigente.
        </p>

        <hr>

        <h4>1. Identificación del titular</h4>
        <p>
          <strong>Nombre comercial:</strong> {{ config('app.name') }}<br>
          {{-- <strong>Razón social:</strong> [Nombre legal de la empresa]<br>
                            <strong>NIF/CIF:</strong> [Número fiscal]<br>
                            <strong>Domicilio:</strong> [Dirección completa]<br> --}}
          <strong>Email de contacto:</strong> contacto@pesos.mx<br>
        </p>

        <h4>2. Objeto</h4>
        <p>
          El presente documento regula las condiciones de contratación de productos a través de esta tienda
          online.
          La realización de un pedido implica la aceptación plena de estas condiciones.
        </p>

        <h4>3. Proceso de compra</h4>
        <ul>
          <li>Selección de productos y añadido al carrito.</li>
          <li>Introducción de datos de facturación y envío.</li>
          <li>Confirmación del pedido y aceptación de condiciones.</li>
          <li>Pago mediante métodos disponibles en la plataforma.</li>
          <li>Recepción de email de confirmación.</li>
        </ul>

        <h4>4. Precios</h4>
        <p>
          Todos los precios están expresados en euros (MX$) e incluyen los impuestos aplicables (IVA), salvo
          indicación contraria.
          Los gastos de envío se mostrarán antes de finalizar la compra.
        </p>

        <h4>5. Formas de pago</h4>
        <p>
          Se aceptan los métodos de pago indicados en el proceso de compra.
          Los pagos se realizan a través de plataformas seguras.
        </p>

        <h4>6. Envíos</h4>
        <ul>
          <li>Los pedidos se procesan en días laborables.</li>
          <li>El plazo estimado de entrega se indicará antes de finalizar la compra.</li>
          <li>No nos responsabilizamos de retrasos imputables a la empresa de transporte.</li>
        </ul>

        <h4>7. Derecho de desistimiento</h4>
        <p>
          Conforme a la legislación española, el cliente dispone de un plazo de <strong>14 días naturales</strong>
          desde la recepción del pedido para ejercer su derecho de desistimiento sin necesidad de justificación.
        </p>
        <p>
          Para ejercer este derecho, deberá comunicarlo por email a contacto@pesos.mx.
          El producto deberá devolverse en perfecto estado.
        </p>

        <h4>8. Devoluciones y reembolsos</h4>
        <p>
          El reembolso se realizará utilizando el mismo método de pago empleado por el cliente,
          en un plazo máximo de 14 días desde la recepción del producto devuelto.
        </p>

        <h4>9. Garantías</h4>
        <p>
          Todos los productos cuentan con la garantía legal de conformidad conforme a la normativa vigente.
        </p>

        <h4>10. Protección de datos</h4>
        <p>
          Los datos personales serán tratados conforme a nuestra Política de Privacidad y al Reglamento General de
          Protección de Datos (RGPD).
        </p>

        <h4>11. Propiedad intelectual</h4>
        <p>
          Todos los contenidos del sitio web (textos, imágenes, logotipos, diseño) son propiedad del titular
          y están protegidos por la normativa de propiedad intelectual.
        </p>

        <h4>12. Legislación aplicable y jurisdicción</h4>
        <p>
          Las presentes condiciones se rigen por la legislación española.
          En caso de conflicto, las partes se someterán a los juzgados y tribunales del domicilio del consumidor.
        </p>

        <hr>

        <p class="text-muted small">
          Última actualización: {{ now()->format('d/m/Y') }}
        </p>

      </div>
    </div>
  </div>
@endsection

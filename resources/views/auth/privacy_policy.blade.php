@php
  $pageType = 'Privacy Policy';

@endphp
@extends('website.layouts.layouts')

@section('content')
  <div class="account-pages my-1 pt-sm-1">
    <div class="container py-1">
      <div class=""> {{-- card shadow-sm p-5 --}}

        <h1 class="mb-4 text-center">Política de Privacidad</h1>

        <p>En cumplimiento del Reglamento (UE) 2016/679 (RGPD), la Ley Orgánica 3/2018 (LOPDGDD) y la Ley 34/2002
          (LSSI-CE), le informamos sobre el tratamiento de sus datos personales en este sitio web.</p>

        <hr>

        <h4>1. Responsable del tratamiento</h4>
        <p>
          <strong>Nombre comercial:</strong> {{ config('app.name') }}<br>
          {{-- <strong>Razón social:</strong> [Nombre legal de la empresa]<br>
                                    <strong>NIF/CIF:</strong> [Número fiscal]<br>
                                    <strong>Domicilio:</strong> [Dirección completa]<br> --}}
          <strong>Email:</strong> contacto@pesos.mx<br>
        </p>

        <h4>2. Datos que recopilamos</h4>
        <ul>
          <li>Datos identificativos: nombre, apellidos.</li>
          <li>Datos de contacto: email, teléfono, dirección postal.</li>
          <li>Datos de facturación.</li>
          <li>Datos de pago (gestionados a través de pasarelas seguras).</li>
          <li>Datos de navegación (cookies y tecnologías similares).</li>
        </ul>

        <h4>3. Finalidad del tratamiento</h4>
        <ul>
          <li>Gestionar pedidos y compras online.</li>
          <li>Procesar pagos y facturación.</li>
          <li>Enviar comunicaciones relacionadas con el pedido.</li>
          <li>Atender consultas y solicitudes.</li>
          <li>Enviar comunicaciones comerciales (si existe consentimiento).</li>
          <li>Cumplir obligaciones legales.</li>
        </ul>

        <h4>4. Base legal</h4>
        <ul>
          <li>Ejecución de un contrato de compraventa.</li>
          <li>Consentimiento del usuario.</li>
          <li>Cumplimiento de obligaciones legales.</li>
          <li>Interés legítimo del responsable.</li>
        </ul>

        <h4>5. Conservación de los datos</h4>
        <p>
          Los datos se conservarán mientras exista relación contractual y posteriormente durante los plazos
          exigidos por la legislación fiscal y mercantil española.
        </p>

        <h4>6. Destinatarios</h4>
        <p>
          Los datos podrán ser comunicados a:
        </p>
        <ul>
          <li>Entidades financieras para la gestión de pagos.</li>
          <li>Empresas de transporte para la entrega de pedidos.</li>
          <li>Proveedores tecnológicos que prestan servicios necesarios para la actividad.</li>
        </ul>

        <h4>7. Derechos del usuario</h4>
        <p>
          Usted tiene derecho a:
        </p>
        <ul>
          <li>Acceder a sus datos personales.</li>
          <li>Solicitar la rectificación o supresión.</li>
          <li>Solicitar la limitación del tratamiento.</li>
          <li>Oponerse al tratamiento.</li>
          <li>Solicitar la portabilidad de los datos.</li>
          <li>Retirar el consentimiento en cualquier momento.</li>
        </ul>

        <p>
          Puede ejercer sus derechos enviando una solicitud a contacto@pesos.mx, adjuntando copia de su
          documento de identidad.
        </p>

        <h4>8. Medidas de seguridad</h4>
        <p>
          Aplicamos medidas técnicas y organizativas adecuadas para garantizar la seguridad de los datos
          personales y evitar su alteración, pérdida o acceso no autorizado.
        </p>

        <h4>9. Cookies</h4>
        <p>
          Este sitio web utiliza cookies propias y de terceros. Puede consultar información detallada en nuestra
          Política de Cookies.
        </p>

        <h4>10. Autoridad de control</h4>
        <p>
          Si considera que sus derechos no han sido respetados, puede presentar una reclamación ante la
          Agencia Española de Protección de Datos (AEPD).
        </p>

        <hr>

        <p class="text-muted small">
          Última actualización: {{ now()->format('d/m/Y') }}
        </p>
        <div class="mt-4">

        </div>
      </div>
    </div>
  </div>
@endsection

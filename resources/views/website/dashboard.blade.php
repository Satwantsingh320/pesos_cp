@php
  $pageType = 'inner';
  $pageTitle = 'Panel de Control';
  $breadcrumbTitlecurrent = 'Panel de Control';
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
  <div class="container my-5">
    <div class="row">
      {{-- Validation Errors --}}
      @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>

          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

      {{-- Success Message --}}
      @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          {{ session('success') }}

          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

      <div class="col-lg-3 pt-3">
        <div class="card shadow-sm border-0">
          <div class="card-body text-center">
            <img src="{{ asset('uploads/customers/' . $customer->image ?? 'assets/images/default-user.png') }}"
              onerror="this.onerror=null;this.src='{{ asset('assets/images/default-user.png') }}';"
              class="rounded-circle mb-3" width="100">
            <h5>{{ $customer->name }}</h5>
            <p class="text-muted small">{{ $customer->email }}</p>
          </div>
          <div class="list-group list-group-flush px-2 pb-3">
            <a href="#orders"
              class="list-group-item list-group-item-action border-0 {{ !session('active_tab') || session('active_tab') == 'v-orders' ? 'active' : '' }}"
              data-bs-toggle="pill">
              <i class="mdi mdi-cart me-2"></i> Pedidos
            </a>
            <a href="#profile"
              class="list-group-item list-group-item-action border-0 {{ session('active_tab') == 'v-profile' ? 'show active' : '' }}"
              data-bs-toggle="pill">
              <i class="mdi mdi-account me-2"></i> Perfil
            </a>
            <a href="#address"
              class="list-group-item list-group-item-action border-0 {{ session('active_tab') == 'v-address' ? 'show active' : '' }}"
              data-bs-toggle="pill">
              <i class="mdi mdi-map me-2"></i> Direcciones
            </a>
            <a class="list-group-item list-group-item-action {{ session('active_tab') == 'v-password' ? 'show active' : '' }}"
              data-bs-toggle="pill" href="#v-password">
              <i class="mdi mdi-lock-outline me-2"></i> Cambiar Contraseña
            </a>
            <br>
            <a class="list-group-item list-group-item-action" href="{{ route('customer.refund.index') }}">
              <i class="mdi mdi-truck me-2"></i> <small>Solicitudes de Reembolso</small>
            </a>
          </div>
        </div>
      </div>

      <div class="col-lg-9 pt-3">
        <div class="tab-content">

          <div
            class="tab-pane fade show {{ !session('active_tab') || session('active_tab') == 'v-orders' ? 'active' : '' }}"
            id="orders">
            <div class="card border-0 shadow-sm">
              <div class="card-header bg-white"><b>Pedidos Recientes</b></div>
              <div class="card-body p-0">
                <table class="table table-hover mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Pedido #</th>
                      <th>Fecha</th>
                      <th>Total</th>
                      <th>Estado</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($orders as $order)
                      <tr>
                        <td>
                          <a href="{{ route('customer.order.details', $order->id) }}">
                            #{{ $order->order_number }}
                          </a>
                        </td>
                        <td>{{ $order->created_at->format('d M, Y') }}</td>
                        <td>MX${{ number_format($order->price, 2) }}</td>
                        <td>
                          @php
                            $statusBadge = [
                                0 => ['class' => 'bg-secondary', 'label' => 'Pendiente'],
                                1 => ['class' => 'bg-warning text-dark', 'label' => 'Ordenado'],
                                2 => ['class' => 'bg-primary', 'label' => 'Enviado'],
                                3 => ['class' => 'bg-success', 'label' => 'Entregado'],
                            ];
                            $currentStatus = $statusBadge[$order->order_status] ?? $statusBadge[0];
                          @endphp
                          <span class="badge {{ $currentStatus['class'] }} fs-6">
                            {{ $currentStatus['label'] }}
                          </span>
                          <a href="{{ route('customer.order.details', $order->id) }}" class="btn btn-info btn-sm">
                            <i class="bi bi-eye "> Vista</i>
                          </a>
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="4" class="text-center py-4">No se encontraron pedidos.</td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
                <div class="mt-3 ms-2 me-2">
                  {{ $orders->appends(['active_tab' => 'v-orders'])->links() }}
                </div>
              </div>
            </div>
          </div>

          <div class="tab-pane fade {{ session('active_tab') == 'v-address' ? 'show active' : '' }}" id="address">
            <div class="card border-0 shadow-sm">
              <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <b>Mis Direcciones</b>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                  + Añadir Nueva Dirección
                </button>
              </div>
              <div class="card-body">
                <div class="row">
                  @foreach ($addresses as $addr)
                    <div class="col-md-6 mb-3">
                      <div
                        class="border p-3 rounded @if ($addr->is_default) border-primary bg-primary-subtle @endif">
                        <div class="d-flex justify-content-between">
                          <span class="badge bg-secondary mb-2">{{ $addr->type }}</span>
                          @if ($addr->is_default)
                            <span class="badge bg-primary pt-1 mt-1">Predeterminada</span>
                          @endif
                        </div>
                        <h6>{{ $addr->name }} ({{ $addr->dial_code }} {{ $addr->phone }})</h6>
                        <p class="mb-1 small">{{ $addr->address }}</p>
                        <p class="text-muted small">{{ $addr->city }}, {{ $addr->state }}
                          {{ $addr->postcode }}
                        </p>
                        <div class="mt-2 d-flex gap-2">
                          @if (!$addr->is_default)
                            <a href="{{ route('address.default', $addr->id) }}"
                              class="btn btn-sm btn-outline-primary">Establecer Predeterminada</a>
                          @endif
                          <form action="{{ route('address.delete', $addr->id) }}" method="POST"
                            onsubmit="return confirm('¿Eliminar esta dirección?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                          </form>
                        </div>
                      </div>
                    </div>
                  @endforeach
                </div>
              </div>
            </div>
          </div>

          <div class="tab-pane fade {{ session('active_tab') == 'v-profile' ? 'show active' : '' }}" id="profile">
            <div class="card border-0 shadow-sm">
              <div class="card-header bg-white"><b>Detalles del Perfil</b></div>
              <div class="card-body">
                <form action="{{ route('customer.profile.update') }}" method="POST" enctype="multipart/form-data">
                  @csrf
                  <div class="row g-3">
                    <div class="col-md-6 mb-2">
                      <label class="form-label">Imagen de Perfil</label>
                      <input type="file" name="image" class="form-control">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Número de RFC</label>
                      <input type="text" name="rfc_number" class="form-control"
                        value="{{ $customer->rfc_number }}">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Nombre Completo *</label>
                      <input type="text" name="name" class="form-control" value="{{ $customer->name }}"
                        required>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Correo Electrónico *</label>
                      <input type="email" name="email" class="form-control" value="{{ $customer->email }}"
                        required>
                    </div>
                    <div class="col-md-4">
                      <label class="form-label">Código de Área *</label>
                      <input type="text" name="dial_code" class="form-control"
                        value="{{ $customer->dial_code ?? '+52' }}" placeholder="+1">
                    </div>
                    <div class="col-md-8">
                      <label class="form-label">Número de Teléfono *</label>
                      <input type="number" name="phone" class="form-control" value="{{ $customer->phone }}"
                        required>
                    </div>
                    <div class="col-12 mt-3">
                      <button type="submit" class="btn btn-primary">Actualizar Perfil</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <div class="tab-pane fade {{ session('active_tab') == 'v-password' ? 'show active' : '' }}" id="v-password">
            <div class="card border-0 shadow-sm">
              <div class="card-header bg-white"><b>Configuración de Seguridad</b></div>
              <div class="card-body">
                <form action="{{ route('customer.password.update') }}" method="POST">
                  @csrf
                  <div class="mb-3">
                    <label class="form-label">Contraseña Actual</label>
                    <input type="password" name="current_password" class="form-control" required>
                    @error('current_password')
                      <span class="text-danger small">{{ $message }}</span>
                    @enderror
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Nueva Contraseña</label>
                    <input type="password" name="new_password" class="form-control" required>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Confirmar Nueva Contraseña</label>
                    <input type="password" name="new_password_confirmation" class="form-control" required>
                  </div>
                  <button type="submit" class="btn btn-danger">Actualizar Contraseña</button>
                </form>
              </div>
            </div>
          </div>

          <div class="modal fade" id="addAddressModal" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content">
                <form action="{{ route('address.store') }}" method="POST">
                  @csrf
                  <div class="modal-header">
                    <h5 class="modal-title">Añadir Nueva Dirección</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <div class="mb-3">
                      <label>Nombre del Receptor</label>
                      <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="row">
                      <div class="col-4 mb-3">
                        <label>Código de Área</label>
                        <input type="text" name="dial_code" class="form-control" placeholder="+52" value="+52"
                          required>
                      </div>
                      <div class="col-8 mb-3">
                        <label>Teléfono</label>
                        <input type="number" name="phone" class="form-control" required>
                      </div>
                    </div>
                    <div class="mb-3">
                      <label>Dirección</label>
                      <textarea name="address" class="form-control" rows="2" required></textarea>
                    </div>
                    <div class="row">
                      <div class="col-6 mb-3"><label>Colonia</label><input type="text" name="colonia"
                          class="form-control" required></div>
                      <div class="col-6 mb-3"><label>Ciudad</label><input type="text" name="city"
                          class="form-control" required></div>
                      <div class="col-6 mb-3"><label>Estado</label>
                        <select name="state" id="state" class="form-control" required>
                          <option value="">Seleccionar Estado</option>
                          @foreach ($states as $code => $state)
                            <option value="{{ $code }}" {{ old('state') == $code ? 'selected' : '' }}>
                              {{ $state }}
                            </option>
                          @endforeach
                        </select>
                      </div>
                      <div class="col-6 mb-3"><label>Código Postal</label><input type="text" name="postcode"
                          class="form-control" required></div>
                      <div class="col-6 mb-3">
                        <label>Tipo</label>
                        <select name="type" class="form-select">
                          <option value="Home">Casa</option>
                          <option value="Office">Oficina</option>
                          <option value="Other">Otro</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="submit" class="btn btn-primary w-100">Guardar Dirección</button>
                  </div>
                </form>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
@endsection

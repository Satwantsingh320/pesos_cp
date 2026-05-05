@php
  $pageType = 'inner';
  $pageTitle = 'Order Details';
  $breadcrumbTitlecurrent = 'Order Details';
@endphp
@extends('website.layouts.layouts')
@section('content')
  <div class="container my-5">
    <!-- start page title -->
    <div class="row">
      <div class="col-4">
        <div class="page-title-box d-flex align-items-center">
          <a href="{{ route('customer.dashboard.index') }}" class="btn btn-dark btn-sm mx-2"><i class="bx bx-arrow-back"></i>
            {{ __('admin.back') }}</a>
          <h4 class="mb-sm-0 font-size-18">{{ __('admin.order_details') }}</h4>
        </div>
      </div>
    </div>
    <div class="container-fluid py-4">
      <div class="row mb-4">
        <div class="col-md-6">
          <h3 class="fw-bold">{{ __('admin.Order') }} #{{ $order->order_number }}</h3>
          <p class="text-muted">{{ __('admin.Placed on') }} {{ $order->created_at->format('M d, Y H:i') }}
          </p>
        </div>
        <div class="col-md-6 text-md-end">
          @php
            $statusBadge = [
                0 => ['class' => 'bg-secondary', 'label' => 'Pendiente'],
                1 => ['class' => 'bg-warning text-dark', 'label' => 'Pedido realizado'],
                2 => ['class' => 'bg-primary', 'label' => 'Enviado'],
                3 => ['class' => 'bg-success', 'label' => 'Entregado'],
            ];
            $currentStatus = $statusBadge[$order->order_status] ?? $statusBadge[0];
          @endphp
          <span class="badge {{ $currentStatus['class'] }} fs-6">{{ $currentStatus['label'] }}</span>
          <div class="mt-2 text-muted small">{{ __('admin.Payment') }}:
            <strong>{{ strtoupper($order->payment_status) }}</strong> via {{ $order->payment_type }}
            <br>
            {{ $order->stripe_id }}
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-8">
          <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
              <h5 class="card-title mb-0">{{ __('admin.Order Items') }} ({{ $order->items->count() }})
              </h5>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                  <thead class="table-light">
                    <tr>
                      <th class="ps-4">{{ __('admin.Product') }}</th>
                      <th>{{ __('admin.Price') }}</th>
                      <th>{{ __('admin.Qty') }}</th>
                      <th class="text-end pe-4">{{ __('admin.Total') }}</th>
                      <th class="text-end pe-4">{{ __('admin.Action') }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($order->items as $item)
                      <tr>
                        <td class="ps-4">
                          <div class="d-flex align-items-center">
                            <div>
                              @php
                                $daysLeft = $item->refundDaysLeft();
                              @endphp

                              <h6 class="mb-0">{{ $item->name }}</h6>
                              <small class="text-muted d-none">ID:
                                #{{ $item->product_id }}</small>
                              <small class="text-muted">
                                @if (!is_null($daysLeft))
                                  @if ($daysLeft > 0)
                                    <div class="small text-success">
                                      {{ $daysLeft }} día(s) restantes para el reembolso
                                    </div>
                                  @elseif($daysLeft === 0)
                                    <div class="small text-warning">
                                      Último día para solicitar el reembolso
                                    </div>
                                  @else
                                  @endif
                                @endif
                              </small>
                            </div>
                          </div>
                        </td>
                        <td>MX${{ number_format($item->price, 2) }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td class="text-end pe-4 fw-bold">
                          MX${{ number_format($item->price * $item->quantity, 2) }}</td>
                        {{-- ================= REFUND ACTION COLUMN ================= --}}
                        <td class="text-end pe-4">
                          @if (auth('customer')->check() && $order->order_status == 3 && $order->delivered_at && $item->isRefundEligible())
                            @if (!$item->refundRequest)
                              <button type="button" class="btn btn-sm btn-outline-danger p-0 open-refund-modal"
                                style="font-size: 12px;" data-item-id="{{ $item->id }}">
                                {{ __('admin.Request Refund') }}
                              </button>
                            @else
                              <span class="badge bg-warning">
                                {{ __('admin.Refund Requested') }}
                              </span>
                            @endif
                          @elseif($item->refundRequest)
                            {{-- If refund exists but not eligible anymore --}}
                            <span class="badge bg-info">
                              {{ $item->refundRequest->status->label() }}
                            </span>
                          @else
                            <span class="text-muted small">
                              —
                            </span>
                          @endif

                        </td>
                        {{-- ======================================================== --}}
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          @if ($order->tracking_number)
            <div class="card shadow-sm">
              <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="bi bi-truck me-2"></i>{{ __('admin.Tracking Information') }}</h6>
                <div class="row">
                  <div class="col-sm-6">
                    <p class="mb-1 text-muted">{{ __('admin.Carrier') }}</p>
                    <p class="fw-bold">{{ $order->tracking_company }}</p>
                  </div>
                  <div class="col-sm-6">
                    <p class="mb-1 text-muted">{{ __('admin.Tracking Number') }}</p>
                    <p class="fw-bold text-primary">{{ $order->tracking_number }}</p>
                  </div>
                </div>
              </div>
            </div>
          @endif
        </div>

        <div class="col-lg-4">
          <div class="card shadow-sm mb-4">
            <div class="card-body">
              <h6 class="fw-bold mb-3">{{ __('admin.Customer Details') }}</h6>
              <p class="mb-1"><strong>{{ $order->customer->name ?? 'Guest' }}</strong></p>
              <p class="mb-1 text-muted">{{ $order->customer->email ?? '' }}</p>
              <p class="mb-0 text-muted">{{ $order->customer->phone ?? '' }}</p>
            </div>
          </div>
          <div class="card shadow-sm mb-4">
            <div class="card-body">
              <h6 class="fw-bold mb-3">{{ __('admin.Customer Details') }}</h6>

              <p class="mb-1"><strong>{{ $order->customer->name ?? 'Guest' }}</strong></p>
              <p class="mb-3 text-muted small">{{ $order->customer->email ?? '' }}</p>

              <h6 class="fw-bold mb-2 small text-uppercase text-primary">{{ __('admin.Shipping') }}
              </h6>

              @php
                // Extract addresses
                $shipping = $order->address['shipping'] ?? [];
                $billing = $order->address['billing'] ?? [];

                // Logic: Use shipping address if the 'address' field exists,
                // otherwise fallback to billing address
                $displayAddress = !empty($shipping['address']) ? $shipping : $billing;
              @endphp

              @if (!empty($displayAddress['address']))
                <p class="mb-1">
                  <strong>{{ $displayAddress['name'] ?? ($order->customer->name ?? '') }}</strong>
                  @if (isset($displayAddress['type']))
                    <span class="badge bg-soft-info text-info small">{{ $displayAddress['type'] }}</span>
                  @endif
                </p>
                <p class="mb-1 text-muted">
                  {{ $displayAddress['address'] }}<br>
                  @if (isset($displayAddress['colonia']))
                    {{ $displayAddress['colonia'] }},
                  @endif {{ $displayAddress['city'] }}, {{ $displayAddress['state'] }}
                  {{ $displayAddress['postcode'] }}
                </p>
                <p class="mb-0 text-muted">{{ $displayAddress['phone'] ?? '' }}</p>
              @else
                <p class="text-danger small">No address information available.</p>
              @endif
            </div>
          </div>

          <div class="card shadow-sm">
            <div class="card-header bg-white">
              <h5 class="card-title mb-0">{{ __('admin.Order Summary') }}</h5>
            </div>
            <div class="card-body">
              <div class="d-flex justify-content-between mb-2">
                <span>{{ __('admin.Subtotal') }}</span>
                <span>MX${{ number_format($order->sub_total, 2) }}</span>
              </div>
              <div class="d-flex justify-content-between mb-2">
                <span>{{ __('admin.Shipping') }}</span>
                <span>MX${{ number_format($order->shipping, 2) }}</span>
              </div>
              <div class="d-flex justify-content-between mb-2">
                <span>{{ __('admin.Tax') }}</span>
                <span>MX${{ number_format($order->tax, 2) }}</span>
              </div>
              @if ($order->discount > 0)
                <div class="d-flex justify-content-between mb-2 text-danger">
                  <span>{{ __('admin.Discount') }}</span>
                  <span>-MX${{ number_format($order->discount, 2) }}</span>
                </div>
              @endif
              <hr>
              <div class="d-flex justify-content-between fw-bold fs-5">
                <span>{{ __('admin.Total') }}</span>
                <span class="text-primary">MX${{ number_format($order->price, 2) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>


  <div class="modal fade" id="refundModal" tabindex="-1">
    <div class="modal-dialog">
      <form method="POST" action="{{ route('customer.refund.request') }}" enctype="multipart/form-data"
        class="modal-content">
        @csrf

        <input type="hidden" name="order_item_id" id="refund_item_id">

        <div class="modal-header">
          <h5 class="modal-title">Solicitar reembolso</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          {{-- Message --}}
          <div class="mb-3">
            <label class="form-label">Razón *</label>
            <textarea name="reason" class="form-control" rows="2" required></textarea>
          </div>

          {{-- Image Upload --}}
          <div class="mb-3">
            <label class="form-label">Subir imagen (opcional)</label>
            <input type="file" name="image" class="form-control" accept="image/*">
          </div>

          {{-- Preview --}}
          <div class="text-center">
            <img id="refund-preview" src="" class="img-fluid rounded d-none" style="max-height: 150px;">
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            Cancelar
          </button>

          <button type="submit" class="btn btn-danger">

            Enviar solicitud
          </button>
        </div>

      </form>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {

      const modal = new bootstrap.Modal(document.getElementById('refundModal'));

      // Open modal + set item id
      document.querySelectorAll('.open-refund-modal').forEach(btn => {
        btn.addEventListener('click', function() {
          document.getElementById('refund_item_id').value = this.dataset.itemId;
          modal.show();
        });
      });

      // Image preview
      const fileInput = document.querySelector('input[name="image"]');
      const preview = document.getElementById('refund-preview');

      fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];

        if (!file) {
          preview.classList.add('d-none');
          return;
        }

        preview.src = URL.createObjectURL(file);
        preview.classList.remove('d-none');
      });

    });
  </script>
@endsection

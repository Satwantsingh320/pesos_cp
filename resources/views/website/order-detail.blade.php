@php
    $pageType = 'inner';
    $pageTitle = __('website.order_details');
    $breadcrumbTitlecurrent = __('website.order_details');
@endphp

@extends('website.layouts.layouts')

@section('content')
    <div class="container my-5">
        <!-- start page title -->
        <div class="row">
            <div class="col-4">
                <div class="page-title-box d-flex align-items-center">
                    <a href="{{ route('customer.dashboard.index') }}" class="btn btn-dark btn-sm mx-2">
                        <i class="bx bx-arrow-back"></i> {{ __('website.back') }}
                    </a>
                    <h4 class="mb-sm-0 font-size-18">{{ __('website.order_details') }}</h4>
                </div>
            </div>
        </div>

        <div class="container-fluid py-4">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h3 class="fw-bold">{{ __('website.order') }} #{{ $order->order_number }}</h3>
                    <p class="text-muted">{{ __('website.placed_on') }} {{ $order->created_at->format('M d, Y H:i') }}</p>
                </div>
                <div class="col-md-6 text-md-end">
                    @php
                        $statusBadge = [
                            0 => ['class' => 'bg-secondary', 'label' => __('status_pending')],
                            1 => ['class' => 'bg-warning text-dark', 'label' => __('status_ordered')],
                            2 => ['class' => 'bg-primary', 'label' => __('status_shipped')],
                            3 => ['class' => 'bg-success', 'label' => __('status_delivered')],
                        ];
                        $currentStatus = $statusBadge[$order->order_status] ?? $statusBadge[0];
                      @endphp
                    <span class="badge {{ $currentStatus['class'] }} fs-6">{{ $currentStatus['label'] }}</span>
                    <div class="mt-2 text-muted small">
                        {{ __('website.payment') }}: <strong>{{ strtoupper($order->payment_status) }}</strong> via
                        {{ $order->payment_type }}
                        <br>
                        {{ $order->stripe_id }}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title mb-0">{{ __('website.order_items') }} ({{ $order->items->count() }})</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">{{ __('website.product') }}</th>
                                            <th>{{ __('website.price') }}</th>
                                            <th>{{ __('website.qty') }}</th>
                                            <th class="text-end pe-4">{{ __('website.total') }}</th>
                                            <th class="text-end pe-4">{{ __('website.action') }}</th>
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
                                                                            {{ __('website.days_left_for_refund', ['days' => $daysLeft]) }}
                                                                        </div>
                                                                    @elseif($daysLeft === 0)
                                                                        <div class="small text-warning">
                                                                            {{ __('website.last_day_for_refund') }}
                                                                        </div>
                                                                    @endif
                                                                @endif
                                                            </small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ CURRENCY }}{{ number_format($item->price, 2) }}</td>
                                                <td>{{ $item->quantity }}</td>
                                                <td class="text-end pe-4 fw-bold">
                                                    {{ CURRENCY }}{{ number_format($item->price * $item->quantity, 2) }}
                                                </td>

                                                {{-- REFUND ACTION COLUMN --}}
                                                <td class="text-end pe-4">
                                                    @if (auth('customer')->check() && $order->order_status == 3 && $order->delivered_at && $item->isRefundEligible())
                                                        @if (!$item->refundRequest)
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-danger p-0 open-refund-modal"
                                                                style="font-size: 12px;" data-item-id="{{ $item->id }}">
                                                                {{ __('website.request_refund') }}
                                                            </button>
                                                        @else
                                                            <span class="badge bg-warning">
                                                                {{ __('website.refund_requested') }}
                                                            </span>
                                                        @endif
                                                    @elseif($item->refundRequest)
                                                        <span class="badge bg-info">
                                                            {{ $item->refundRequest->status->label() }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted small">—</span>
                                                    @endif
                                                </td>
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
                                <h6 class="fw-bold mb-3"><i
                                        class="bi bi-truck me-2"></i>{{ __('website.tracking_information') }}</h6>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <p class="mb-1 text-muted">{{ __('website.carrier') }}</p>
                                        <p class="fw-bold">{{ $order->tracking_company }}</p>
                                    </div>
                                    <div class="col-sm-6">
                                        <p class="mb-1 text-muted">{{ __('website.tracking_number') }}</p>
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
                            <h6 class="fw-bold mb-3">{{ __('website.customer_details') }}</h6>
                            <p class="mb-1"><strong>{{ $order->customer->name ?? __('guest') }}</strong></p>
                            <p class="mb-1 text-muted">{{ $order->customer->email ?? '' }}</p>
                            <p class="mb-0 text-muted">{{ $order->customer->phone ?? '' }}</p>
                        </div>
                    </div>

                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">{{ __('website.shipping') }}</h6>

                            @php
                                $shipping = $order->address['shipping'] ?? [];
                                $billing = $order->address['billing'] ?? [];
                                $displayAddress = !empty($shipping['address']) ? $shipping : $billing;
                              @endphp

                            @if (!empty($displayAddress['address']))
                                <p class="mb-1">
                                    <strong>{{ $displayAddress['name'] ?? ($order->customer->name ?? __('guest')) }}</strong>
                                    @if (isset($displayAddress['type']))
                                        <span class="badge bg-soft-info text-info small">
                                            @if($displayAddress['type'] == 'Home')
                                                {{ __('website.address_home') }}
                                            @elseif($displayAddress['type'] == 'Office')
                                                {{ __('website.address_office') }}
                                            @else
                                                {{ __('website.address_other') }}
                                            @endif
                                        </span>
                                    @endif
                                </p>
                                <p class="mb-1 text-muted">
                                    {{ $displayAddress['address'] }}<br>
                                    @if (isset($displayAddress['colonia']))
                                        {{ $displayAddress['colonia'] }},
                                    @endif
                                    {{ $displayAddress['city'] }}, {{ $displayAddress['state'] }}
                                    {{ $displayAddress['postcode'] }}
                                </p>
                                <p class="mb-0 text-muted">{{ $displayAddress['phone'] ?? '' }}</p>
                            @else
                                <p class="text-danger small">{{ __('website.no_address_info') }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">{{ __('website.order_summary') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>{{ __('website.subtotal') }}</span>
                                <span>{{ CURRENCY }}{{ number_format($order->sub_total, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>{{ __('website.shipping') }}</span>
                                <span>{{ CURRENCY }}{{ number_format($order->shipping, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>{{ __('website.tax') }}</span>
                                <span>{{ CURRENCY }}{{ number_format($order->tax, 2) }}</span>
                            </div>
                            @if ($order->discount > 0)
                                <div class="d-flex justify-content-between mb-2 text-danger">
                                    <span>{{ __('website.discount') }}</span>
                                    <span>-{{ CURRENCY }}{{ number_format($order->discount, 2) }}</span>
                                </div>
                            @endif
                            <hr>
                            <div class="d-flex justify-content-between fw-bold fs-5">
                                <span>{{ __('website.total') }}</span>
                                <span class="text-primary">{{ CURRENCY }}{{ number_format($order->price, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Refund Modal --}}
    <div class="modal fade" id="refundModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('customer.refund.request') }}" enctype="multipart/form-data"
                class="modal-content">
                @csrf
                <input type="hidden" name="order_item_id" id="refund_item_id">

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('website.refund_modal_title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="{{ __('website.cancel') }}"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('website.refund_reason') }}</label>
                        <textarea name="reason" class="form-control" rows="2"
                            placeholder="{{ __('website.refund_reason_placeholder') }}" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('website.upload_image') }}</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small class="text-muted">{{ __('website.supported_formats') }}</small>
                    </div>

                    <div class="text-center">
                        <img id="refund-preview" src="" class="img-fluid rounded d-none" style="max-height: 150px;"
                            alt="{{ __('website.image_preview') }}">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('website.cancel') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('website.submit_request') }}</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = new bootstrap.Modal(document.getElementById('refundModal'));

            // Translations for JavaScript
            const translations = {
                'en': {
                    'confirm_cancel': 'Are you sure you want to cancel?'
                },
                'sv': {
                    'confirm_cancel': 'Är du säker på att du vill avbryta?'
                }
            };

            const currentLocale = '{{ app()->getLocale() }}';
            const trans = translations[currentLocale] || translations['en'];

            // Open modal + set item id
            document.querySelectorAll('.open-refund-modal').forEach(btn => {
                btn.addEventListener('click', function () {
                    document.getElementById('refund_item_id').value = this.dataset.itemId;
                    modal.show();
                });
            });

            // Image preview
            const fileInput = document.querySelector('input[name="image"]');
            const preview = document.getElementById('refund-preview');

            if (fileInput) {
                fileInput.addEventListener('change', function (e) {
                    const file = e.target.files[0];
                    if (!file) {
                        preview.classList.add('d-none');
                        preview.src = '';
                        return;
                    }
                    preview.src = URL.createObjectURL(file);
                    preview.classList.remove('d-none');
                });
            }
        });
    </script>
@endsection

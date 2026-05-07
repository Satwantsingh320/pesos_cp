@extends('layouts.master')

@section('content')

    <style>
        @media print {

            body {
                background: #fff;
            }

            .no-print {
                display: none !important;
            }

            #invoice-area {
                width: 100%;
                padding: 20px;
            }

            .table {
                border: 1px solid #000;
            }

            .table th,
            .table td {
                border: 1px solid #000 !important;
                padding: 8px !important;
            }

            h2,
            h3,
            h4,
            h5 {
                margin-bottom: 10px;
            }

            .row {
                display: flex !important;
                flex-wrap: nowrap !important;
            }

            .col-md-6 {
                width: 50% !important;
                max-width: 50% !important;
                flex: 0 0 50% !important;
            }

            .text-md-end {
                text-align: right !important;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                <!-- Print Button -->
                <div class="text-end mb-2 no-print">
                    <button onclick="window.print()" class="btn btn-info btn-sm">
                        <i class="bx bx-printer"></i> {{ __('admin.print_invoice') }}
                    </button>
                </div>

                <!-- Invoice -->
                <div id="invoice-area" class="bg-white p-4">

                    <!-- HEADER -->
                    <div class="d-flex justify-content-between mb-4">
                        <div>
                            <h3 class="fw-bold mb-1">vaakgolvslip.se</h3>
                            <p class="mb-0 text-muted">
                                Campo 8,<br>
                                Km 29 Corredor Comercial Cuauhtémoc <br>
                                Chihuahua Cp: 31614<br>
                                +46 614 215 9366
                            </p>
                        </div>

                        <div class="text-end">
                            <h2 class="fw-bold">{{ __('admin.Order') }}</h2>
                            <p class="mb-1"><strong>#{{ $order->order_number }}</strong></p>
                            <p class="text-muted mb-0">
                                {{ __('admin.Placed on') }}<br>
                                {{ $order->created_at->format('M d, Y H:i') }}
                            </p>
                        </div>
                    </div>

                    <!-- STATUS + PAYMENT -->
                    @php
                        $statusBadge = [
                            0 => ['class' => 'bg-secondary', 'label' => 'Pending'],
                            1 => ['class' => 'bg-warning text-dark', 'label' => 'Ordered'],
                            2 => ['class' => 'bg-primary', 'label' => 'Shipped'],
                            3 => ['class' => 'bg-success', 'label' => 'Delivered'],
                            4 => ['class' => 'bg-danger', 'label' => 'Cancelled'],
                            5 => ['class' => 'bg-danger', 'label' => 'Returned'],
                        ];
                        $currentStatus = $statusBadge[$order->order_status] ?? $statusBadge[0];
                    @endphp

                    <div class="mb-3 no-print">
                        <span class="badge {{ $currentStatus['class'] }}">
                            {{ $currentStatus['label'] }}
                        </span>

                        <div class="mt-2 text-muted">
                            {{ __('admin.Payment') }}:
                            <strong>{{ strtoupper($order->payment_status) }}</strong>
                            {{ __('admin.via') ?? 'via' }} {{ $order->payment_type }}
                            <br>
                            {{ $order->stripe_id }}
                        </div>
                    </div>

                    <!-- CUSTOMER + SHIPPING -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold">{{ __('admin.Customer Details') }}</h6>
                            <p class="mb-0">
                                <strong>{{ $order->customer->name ?? 'Guest' }}</strong><br>
                                {{ $order->customer->email ?? '' }}<br>
                                {{ $order->customer->dial_code ?? '' }}{{ $order->customer->phone ?? '' }}
                            </p>
                        </div>

                        <div class="col-md-6 text-md-end">
                            <h6 class="fw-bold">{{ __('admin.Shipping') }}</h6>

                            @php
                                $shipping = $order->address['shipping'] ?? [];
                                $billing = $order->address['billing'] ?? [];
                                $displayAddress = (!empty($shipping['address'])) ? $shipping : $billing;
                            @endphp

                            @if(!empty($displayAddress['address']))
                                <p class="mb-0">
                                    <strong>{{ $displayAddress['name'] ?? $order->customer->name ?? '' }}</strong><br>
                                    {{ $displayAddress['address'] }}<br>
                                    {{ $displayAddress['city'] }}, {{ $displayAddress['state'] }}
                                    {{ $displayAddress['postcode'] }}<br>
                                    {{ $displayAddress['phone'] ?? '' }}
                                </p>
                            @else
                                <p class="text-danger small">No address information available.</p>
                            @endif
                        </div>
                    </div>

                    <!-- ITEMS -->
                    <h5 class="fw-bold mb-2">
                        {{ __('admin.Order Items') }} ({{ $order->items->count() }})
                    </h5>

                    <table class="table table-bordered mb-4">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('admin.Product') }}</th>
                                <th class="text-center">{{ __('admin.Qty') }}</th>
                                <th class="text-end">{{ __('admin.Price') }}</th>
                                <th class="text-end">{{ __('admin.Total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        {{ $item->name }}<br>
                                        <small class="text-muted">ID: #{{ $item->product_id }}</small>
                                    </td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end">{{CURRENCY}}{{ number_format($item->price, 2) }}</td>
                                    <td class="text-end fw-bold">
                                        {{CURRENCY}}{{ number_format($item->price * $item->quantity, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- SUMMARY -->
                    <div class="row justify-content-end">
                        <div class="col-md-4">

                            <div class="d-flex justify-content-between mb-2">
                                <span>{{ __('admin.Subtotal') }}</span>
                                <span>{{CURRENCY}}{{ number_format($order->sub_total, 2) }}</span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span>{{ __('admin.Shipping') }}</span>
                                <span>{{CURRENCY}}{{ number_format($order->shipping, 2) }}</span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span>{{ __('admin.Tax') }}</span>
                                <span>{{CURRENCY}}{{ number_format($order->tax, 2) }}</span>
                            </div>

                            @if($order->discount > 0)
                                <div class="d-flex justify-content-between mb-2 text-danger">
                                    <span>{{ __('admin.Discount') }}</span>
                                    <span>-{{CURRENCY}}{{ number_format($order->discount, 2) }}</span>
                                </div>
                            @endif

                            <hr>

                            <div class="d-flex justify-content-between fw-bold fs-5">
                                <span>{{ __('admin.Total') }}</span>
                                <span>{{CURRENCY}}{{ number_format($order->price, 2) }}</span>
                            </div>

                        </div>
                    </div>

                    <!-- TRACKING -->
                    @if($order->tracking_number)
                        <div class="mt-4">
                            <h6 class="fw-bold">{{ __('admin.Tracking Information') }}</h6>
                            <p class="mb-1">
                                <strong>{{ __('admin.Carrier') }}:</strong> {{ $order->tracking_company }}
                            </p>
                            <p class="mb-0">
                                <strong>{{ __('admin.Tracking Number') }}:</strong> {{ $order->tracking_number }}
                            </p>
                        </div>
                    @endif

                </div>

            </div>
        </div>
    </div>

@endsection
@section('js')
    <script>
        function printInvoice() {
            window.print();
        }

        // auto print when coming from list
        @if(request('print'))
            window.onload = function () {
                window.print();
            }
        @endif
    </script>
@endsection
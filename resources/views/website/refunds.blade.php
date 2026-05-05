@php
    $pageType = 'inner';
    $pageTitle = 'Detalles del reembolso';
    $breadcrumbTitlecurrent = 'Detalles del reembolso';
@endphp
@extends('website.layouts.layouts')
@section('content')
    <div class="container my-5">
        <!-- start page title -->
        <div class="row">
            <div class="col-4">
                <div class="page-title-box d-flex align-items-center">
                    <a href="{{ route('customer.dashboard.index') }}" class="btn btn-dark btn-sm mx-2">
                        <i class="bx bx-arrow-back"></i> {{ __('admin.back') }}
                    </a>
                    <h4 class="mb-sm-0 font-size-18">{{ __('admin.refund_details') }}</h4>
                </div>
            </div>
        </div>
        <div class="container-fluid py-4">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Reembolso #</th>
                        <th>Pedido</th>
                        <th>Monto</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($refunds as $refund)
                        <tr>
                            <td>{{ $refund->refund_number }}</td>
                            <td>
                                <a href="{{ route('customer.order.details', $refund->order->id) }}">
                                    #{{ $refund->order->order_number }}
                                </a>
                            </td>
                            <td>MX${{ number_format($refund->amount, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $refund->status_badge }}">
                                    {{ $refund->status_label }}
                                </span>
                            </td>
                            <td>{{ $refund->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No se encontraron solicitudes de reembolso.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $refunds->links() }}

        </div>
    </div>
@endsection

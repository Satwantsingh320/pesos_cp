@php
    $pageType = 'inner';
    $pageTitle = 'Pago';
    $breadcrumbTitlecurrent = 'Pago';
@endphp
@extends('website.layouts.layouts')
@section('content')
    <div class="container py-5 text-center">
        <div class="card shadow-sm p-5">
            <div class="mb-4">
                <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
            </div>
            <h1 class="display-5 fw-bold">¡Gracias!</h1>
            <p class="lead text-muted">Tu pedido ha sido realizado con éxito.</p>
            <hr class="my-4" style="max-width: 100px; margin: auto;">

            <p>Hemos enviado un correo de confirmación a tu bandeja de entrada. Tu ID de transacción es:<br>
                <span class="badge bg-light text-dark border p-2 mt-2">{{ $order_id ?? 'N/A' }}</span>
            </p>

            <div class="mt-4">
                <a href="{{ url('/') }}" class="btn btn-primary px-4 me-2">Continuar Comprando</a>
                <a href="{{ route('customer.dashboard.index') }}" class="btn btn-outline-secondary px-4">Ver Mis Pedidos</a>
            </div>
        </div>
    </div>
@endsection

@php
    $pageType = 'inner';
    $pageTitle = 'Pago Cancelado';
    $breadcrumbTitlecurrent = 'Pago Cancelado';
@endphp
@extends('website.layouts.layouts')
@section('content')
    <div class="container py-5 text-center">
        <div class="card shadow-sm p-5 border-warning">
            <div class="mb-4">
                <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size: 4rem;"></i>
            </div>
            <h1 class="display-6 fw-bold">Pago Cancelado</h1>
            <p class="lead">Parece que el proceso de pago fue interrumpido o cancelado.</p>

            <div class="alert alert-secondary d-inline-block mt-3">
                No te preocupes, tus artículos siguen seguros en tu carrito.
            </div>

            <div class="mt-4">
                <a href="{{ route('website.checkout') }}" class="btn btn-warning px-4 me-2">Volver al Pago</a>
                <a href="{{ url('/') }}" class="btn btn-link text-decoration-none text-muted">Volver al Inicio</a>
            </div>

            <p class="mt-5 mb-0 text-muted small">
                ¿Necesitas ayuda? Contacta a nuestro soporte en <span class="fw-bold">support@example.com</span>
            </p>
        </div>
    </div>
@endsection

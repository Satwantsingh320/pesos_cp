@php
    $pageType = 'inner';
    $pageTitle = __('website.payment_success_title');
    $breadcrumbTitlecurrent = __('website.payment_success_title');
@endphp

@extends('website.layouts.layouts')

@section('content')
    <div class="container py-5 text-center">
        <div class="card shadow-sm p-5">
            <div class="mb-4">
                <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
            </div>
            <h1 class="display-5 fw-bold">{{ __('website.thank_you') }}</h1>
            <p class="lead text-muted">{{ __('website.order_placed_successfully') }}</p>
            <hr class="my-4" style="max-width: 100px; margin: auto;">

            <p>{{ __('website.confirmation_email_sent') }} {{ __('website.your_transaction_id_is') }}<br>
                <span class="badge bg-light text-dark border p-2 mt-2">{{ $order_id ?? 'N/A' }}</span>
            </p>

            <div class="mt-4">
                <a href="{{ url('/') }}" class="btn btn-primary px-4 me-2">{{ __('website.continue_shopping') }}</a>
                <a href="{{ route('customer.dashboard.index') }}"
                    class="btn btn-outline-secondary px-4">{{ __('website.view_my_orders') }}</a>
            </div>
        </div>
    </div>
@endsection

@php
    $pageType = __('privacy.title');
    $pageTitle = __('privacy.title');
    $breadcrumbTitlecurrent = __('privacy.title');
@endphp

@extends('website.layouts.layouts')

@section('content')
    <div class="account-pages my-1 pt-sm-1">
        <div class="container py-1">
            <div class="">
                {!! __('privacy.content') !!}
                <div class="mt-4"></div>
            </div>
        </div>
    </div>
@endsection
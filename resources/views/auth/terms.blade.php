@php
    $pageType = __('terms.title');
    $pageTitle = __('terms.title');
    $breadcrumbTitlecurrent = __('terms.title');
@endphp

@extends('website.layouts.layouts')

@section('content')
    <div class="account-pages my-1 pt-sm-1">
        <div class="container py-1">
            <div class="">
                {!! __('terms.content') !!}
            </div>
        </div>
    </div>
@endsection
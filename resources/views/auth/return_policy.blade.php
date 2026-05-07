@php
    $pageType = __('website.Return Policy');

@endphp
@extends('website.layouts.layouts')

@section('content')
    <div class="account-pages my-1 pt-sm-1">
        <div class="container py-1">
            @php
                $returnPolicy = DB::table('settings')->value('return_policy');
              @endphp

            <div class=""> {{-- card shadow-sm p-5 --}}

                <h1 class="mb-4 text-center">{{ __('website.Return Policy') }}</h1>

                <div class="policy-content">
                    {!! $returnPolicy !!}
                </div>

            </div>
        </div>
    </div>
@endsection
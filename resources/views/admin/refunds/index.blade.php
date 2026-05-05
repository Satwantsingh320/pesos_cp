@extends('layouts.master')
@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                {{-- Page Title --}}
                <div class="row">
                    <div class="col-4">
                        <div class="page-title-box d-flex align-items-center">
                            <h4 class="mb-sm-0 font-size-18">{{ __('admin.refund_requests') }}</h4>
                        </div>
                    </div>
                    <div class="col-8"></div>
                </div>

                {{-- Filter --}}
                <div class="row">
                    <div class="col-lg-12">
                        <form action="{{ route('admin.refund.index') }}" method="get" class="mb-3">
                            <h5 class="card-title mb-3">{{ __('admin.filter_refunds') }}</h5>

                            <div class="row g-3 align-items-end">

                                {{-- Search --}}
                                <div class="col-lg-4 col-md-6">
                                    <label class="form-label">{{ __('admin.search') }}</label>
                                    <input type="text" name="keyword" class="form-control"
                                        placeholder="{{ __('admin.search_here') }}" value="{{ request('keyword') }}">
                                </div>

                                {{-- Status --}}
                                <div class="col-lg-2 col-md-6">
                                    <label class="form-label">{{ __('admin.status') }}</label>
                                    <select name="status" class="form-select">
                                        <option value="">{{ __('admin.all') }}</option>
                                        @foreach(\App\Enums\RefundStatus::cases() as $case)
                                            <option value="{{ $case->value }}" @selected(request('status') == $case->value)>
                                                {{ $case->label() }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Start Date --}}
                                <div class="col-lg-2 col-md-6">
                                    <label class="form-label">{{ __('admin.start_date') }}</label>
                                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                                        class="form-control">
                                </div>

                                {{-- End Date --}}
                                <div class="col-lg-2 col-md-6">
                                    <label class="form-label">{{ __('admin.end_date') }}</label>
                                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                                        class="form-control">
                                </div>

                                {{-- Per Page --}}
                                <div class="col-lg-1 col-md-6">
                                    <label class="form-label">{{ __('admin.per_page') }}</label>
                                    <select name="perPage" class="form-select">
                                        @foreach([10, 20, 30, 40, 50] as $size)
                                            <option value="{{ $size }}" @selected(request('perPage', 10) == $size)>
                                                {{ $size }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Buttons --}}
                                <div class="col-lg-3 col-md-12 d-flex gap-2">
                                    <button type="submit" class="btn btn-secondary w-100">
                                        {{ __('admin.submit') }}
                                    </button>
                                    <a href="{{ route('admin.refund.index') }}" class="btn btn-danger w-100">
                                        {{ __('admin.reset') }}
                                    </a>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>

                {{-- Table --}}
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body" id="pagination">
                                @include('admin.refunds.pagination')
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            document.querySelectorAll('.confirm-action').forEach(button => {
                button.addEventListener('click', function (e) {
                    const message = this.dataset.message || 'Are you sure?';

                    if (!confirm(message)) {
                        e.preventDefault();
                    }
                });
            });

        });
    </script>
@endsection
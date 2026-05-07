@extends('layouts.master')

@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <!-- start page title -->
                <div class="row">
                    <div class="col-6">
                        <div class="page-title-box d-flex align-items-center">
                            <h4 class="mb-sm-0 font-size-18">{{ __('admin.inventory') }}</h4>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('inventory.create.multiple') }}" class="btn btn-success btn-sm">
                                <i class="bx bx-layer-plus"></i> {{ __('admin.multiple_upload') }}
                            </a>
                            <a href="{{ route('inventory.create') }}" class="btn btn-primary btn-sm">
                                <i class="bx bx-plus"></i> {{ __('admin.add_inventory') }}
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Filter Form -->
                <div class="row">
                    <div class="col-lg-12">
                        <form class="row my-2" action="{{ route('inventory.index') }}" method="get" id="form-search">
                            @csrf
                            <h5 class="card-title mb-3">{{__('admin.filter_inventory')}}</h5>
                            <div class="row g-3 align-items-end">
                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label">{{ __('admin.search') }}</label>
                                    <input type="text" name="keyword" class="form-control"
                                        placeholder="Search by SKU, Product, or Type" value="{{ request('keyword') }}">
                                </div>

                                <div class="col-lg-2 col-md-6">
                                    <label class="form-label">{{ __('admin.stock_type') }}</label>
                                    <select name="stock_type" class="form-select">
                                        <option value="">{{ __('admin.all') }}</option>
                                        <option value="in" {{ request('stock_type') == 'in' ? 'selected' : '' }}>
                                            {{ __('admin.stock_in') }}
                                        </option>
                                        <option value="out" {{ request('stock_type') == 'out' ? 'selected' : '' }}>
                                            {{ __('admin.stock_out') }}
                                        </option>
                                    </select>
                                </div>

                                <div class="col-lg-2 col-md-6">
                                    <label class="form-label">{{ __('admin.start_date') }}</label>
                                    <input type="date" name="start_date" class="form-control"
                                        value="{{ request('start_date') }}">
                                </div>

                                <div class="col-lg-2 col-md-6">
                                    <label class="form-label">{{ __('admin.end_date') }}</label>
                                    <input type="date" name="end_date" class="form-control"
                                        value="{{ request('end_date') }}">
                                </div>

                                <div class="col-lg-1 col-md-6">
                                    <label class="form-label">{{ __('admin.per_page') }}</label>
                                    <select name="perPage" id="perPage" class="form-select">
                                        <option value="10" {{ request('perPage') == 10 ? 'selected' : '' }}>10</option>
                                        <option value="20" {{ request('perPage') == 20 ? 'selected' : '' }}>20</option>
                                        <option value="50" {{ request('perPage') == 50 ? 'selected' : '' }}>50</option>
                                        <option value="100" {{ request('perPage') == 100 ? 'selected' : '' }}>100</option>
                                    </select>
                                </div>

                                <div class="col-lg-2 col-md-12 d-flex gap-2">
                                    <button type="submit" class="btn btn-secondary">{{__('admin.submit')}}</button>
                                    <a href="{{ route('inventory.index') }}"
                                        class="btn btn-danger">{{__('admin.reset')}}</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Inventory Table -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body" id="pagination" data-url="{!! $url !!}">
                                @include('admin.inventory.pagination')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

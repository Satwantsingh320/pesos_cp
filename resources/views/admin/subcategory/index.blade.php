@extends('layouts.master')
@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-4">
                    <div class="page-title-box d-flex align-items-center">
                        <h4 class="mb-sm-0 font-size-18">{{ __('admin.subcategories') }}</h4>
                    </div>
                </div>
                <div class="col-8">
                    <div class="d-flex justify-content-end flex-wrap gap-2">
                        <a href="{{ route('subcategory.create') }}" class="btn btn-primary btn-sm">
                            <i class="bx bx-plus"></i> {{ __('admin.add_subcategory') }}
                        </a>
                        <!-- <a href="{{ route('export',['page' => 'category']) }}" class="btn btn-success btn-sm">
                            <i class="bx bx-download"></i> {{ __('admin.export_category') }}
                        </a> -->
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <form class="row my-2" action="{{ route('subcategory.index') }}" method="get" id="form-search">
                        @csrf
                        <h5 class="card-title">{{__('admin.filter_subcategories')}}</h5>
                        <div class="row">
                            <div class="col-sm-4">
                                <input class="form-control" id="keyword" value="" name="keyword" type="text" placeholder="{{__('admin.search_here')}}"
                                aria-label="search here...">
                            </div>
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <div class="input-group-text">{{__('admin.status')}}</div>
                                    <select name="status" class="form-select">
                                        <option value="" selected>{{__('admin.all')}}</option>
                                        <option value="1">{{__('admin.active')}}</option>
                                        <option value="0">{{__('admin.inactive')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="input-group">
                                    <div class="input-group-text">{{__('admin.per_page')}}</div>
                                    <select name="perPage" id="perPage" class="form-select">
                                        <option value="10" selected>10</option>
                                        <option value="20">20</option>
                                        <option value="30">30</option>
                                        <option value="40">40</option>
                                        <option value="50">50</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <button type="submit" class="btn btn-secondary mx-2">{{__('admin.submit')}}</button>
                                <a href="{{ route('subcategory.index') }}" class="btn btn-danger">{{__('admin.reset')}}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                         <div class="card-body" id="pagination" data-url="{!! $url !!}">
                            @include('admin.subcategory.pagination')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
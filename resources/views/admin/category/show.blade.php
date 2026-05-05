@extends('layouts.master')
@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-4">
                    <div class="page-title-box d-flex align-items-center">
                        <a href="{{ route('category.index') }}" class="btn btn-dark btn-sm mx-2"><i class="bx bx-arrow-back"></i> {{__('admin.back')}}</a>
                        <h4 class="mb-sm-0 font-size-18">{{__('admin.category_details')}}</h4>
                    </div>
                </div>
            </div>
           <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">

                                <!-- LEFT: Customer details -->
                                <div class="col-md-4 d-flex align-items-start">
                                    <div class="text-muted">
                                        <h5 class="mb-1">{{ $category->name }}</h5>
                                        @if($category->status)
                                            <span class="badge bg-success">{{ __('admin.active') }}</span>
                                        @else
                                            <span class="badge bg-danger">{{ __('admin.inactive') }}</span>
                                        @endif
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">{{__('admin.update_category_details')}} </h4>
                            <form method="POST" action="{{ route('category.update',$category->id) }}">
                                @method('put')
                                @csrf
                                <div class="row mb-4">
                                    <label for="horizontal-email-input" class="col-sm-3 col-form-label">{{__('admin.name')}}</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="name" placeholder="{{__('admin.enter_category_name')}}" value="{{ $category->name }}" class="form-control">
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <label for="horizontal-email-input" class="col-sm-3 col-form-label">{{__('admin.status')}}</label>
                                    <div class="col-sm-9">
                                        <select name="status" id="" class="form-select">
                                            <option value="1" @if($category->status == 1) selected @endif>{{__('admin.active')}}</option>
                                            <option value="0" @if($category->status == 0) selected @endif>{{__('admin.inactive')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row d-flex justify-content-center my-5">
                                    <div class="col-sm-2">
                                        <div>
                                            <button type="submit" class="btn btn-primary w-md">{{__('admin.submit')}}</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- end card body -->
                    </div>
                    <!-- end card -->
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
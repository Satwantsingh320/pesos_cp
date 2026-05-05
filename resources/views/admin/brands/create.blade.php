@extends('layouts.master')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center">
                        <a href="{{ route('brands.index') }}" class="btn btn-dark btn-sm mx-2"><i class="bx bx-arrow-back"></i> {{__('admin.back')}}</a>
                        <h4 class="mb-sm-0 font-size-18"> {{__('admin.brands')}}</h4>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-9">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">{{__('admin.add_brand')}} </h4>
                            <form method="POST" action="{{ route('brands.store') }}">
                                @csrf
                                <div class="row mb-4">
                                    <label for="horizontal-email-input" class="col-sm-3 col-form-label">{{__('admin.name')}}</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="name" placeholder="{{__('admin.enter_brand_name')}}" class="form-control" required value="{{ old('name') }}">
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <label for="horizontal-email-input" class="col-sm-3 col-form-label">{{__('admin.status')}}</label>
                                    <div class="col-sm-9">
                                        <select name="status" id="" class="form-select">
                                            <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>{{__('admin.active')}}</option>
                                            <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>{{__('admin.inactive')}}</option>
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
@extends('layouts.master')
@section('content')
<link rel="stylesheet" href="{{ asset('assets/iti/intlTelInput.css') }}">
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-4">
                    <div class="page-title-box d-flex align-items-center">
                        <a href="{{ route('subcategory.index') }}" class="btn btn-dark btn-sm mx-2"><i class="bx bx-arrow-back"></i> {{__('admin.back')}}</a>
                        <h4 class="mb-sm-0 font-size-18">{{__('admin.subcategory_details')}}</h4>
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
                                        <h5 class="mb-1">{{ $subcategory->name }}</h5>
                                        @if($subcategory->status)
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
                            <h4 class="card-title mb-4">{{__('admin.update_subcategory_details')}} </h4>
                            <form method="POST" action="{{ route('subcategory.update',$subcategory->id) }}">
                                @method('put')
                                @csrf
                                 <div class="row mb-4">
                                    <label for="horizontal-email-input" class="col-sm-3 col-form-label">{{__('admin.category')}}</label>
                                    <div class="col-sm-9">
                                      <select name="category" class="form-control select2" required>
                                        @foreach($categories as $key => $value)
                                            <option value="{{ $key }}" @if($subcategory->category_id == $key) selected @endif>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>

                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <label for="horizontal-email-input" class="col-sm-3 col-form-label">{{__('admin.name')}}</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="name" placeholder="{{__('admin.enter_category_name')}}" value="{{ $subcategory->name }}" class="form-control">
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <label for="horizontal-email-input" class="col-sm-3 col-form-label">{{__('admin.status')}}</label>
                                    <div class="col-sm-9">
                                        <select name="status" id="" class="form-select">
                                            <option value="1" @if($subcategory->status == 1) selected @endif>{{__('admin.active')}}</option>
                                            <option value="0" @if($subcategory->status == 0) selected @endif>{{__('admin.inactive')}}</option>
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

<!---- QR Code Modal ------->
<div class="modal fade" id="qrModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Customer QR Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="qrModalImage" src="" class="img-fluid">
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/iti/custom.js')}}"></script>
<script src="{{asset('assets/iti/intlTelInput.js')}}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const qrModal = document.getElementById('qrModal');
    const modalImage = document.getElementById('qrModalImage');

    qrModal.addEventListener('show.bs.modal', function (event) {
        const thumb = event.relatedTarget;
        modalImage.src = thumb.getAttribute('data-image');
    });
});
</script>
@endsection
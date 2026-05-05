@extends('layouts.master')

@section('css')
<link rel="stylesheet" href="{{asset('assets/libs/flags/intlTelInput.css')}}">
<style>
    .iti--allow-dropdown {
        width: 100% !important;
    }
</style>
@endsection
@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center">
                        <a href="{{ route('customers.index') }}" class="btn btn-dark btn-sm mx-2"><i class="bx bx-arrow-back"></i> {{__('admin.back')}}</a>
                        <h4 class="mb-sm-0 font-size-18"> {{__('admin.customers')}}</h4>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-9">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">{{__('admin.add_customer')}} </h4>
                            <form method="POST" action="{{ route('customers.store') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="row mb-4">
                                    <label for="horizontal-email-input" class="col-sm-3 col-form-label">{{__('admin.name')}}</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="name" placeholder="{{__('admin.enter_customer_name')}}" class="form-control" required value="{{ old('name') }}">
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <label for="horizontal-email-input" class="col-sm-3 col-form-label">{{__('admin.email')}}</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="email" placeholder="{{__('admin.enter_customer_email')}}" class="form-control" required value="{{ old('email') }}" autocomplete="off">
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <label for="horizontal-email-input" class="col-sm-3 col-form-label">{{__('admin.phone')}}</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="phone" id="phone" placeholder="{{__('admin.enter_customer_phone_number')}}" class="form-control" required value="{{ old('phone') }}">
                                        <input type="hidden" name="dial_code" id="dial_code" value="+52"/>
                                         <input type="hidden" id="dial_code_iso" name="dial_code_iso" value="MX">
                                    </div>
                                </div>
                                 <div class="row mb-4">
                                    <label for="horizontal-email-input" class="col-sm-3 col-form-label">{{__('admin.password')}}</label>
                                    <div class="col-sm-9">
                                        <input type="password" name="password" placeholder="{{__('admin.enter_customer_password')}}" class="form-control" required value="{{ old('password') }}" autocomplete="off">
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
                                  <div class="row mb-4">
                                    <label for="horizontal-email-input" class="col-sm-3 col-form-label">{{__('admin.profile_pic')}}</label>
                                    <div class="col-sm-9">
                                         <input type="file" name="image" class="form-control" accept="image/*" onchange="previewCoverImage(this)" required>
                                              <div class="mt-2">
                                                <img id="coverPreview" class="img-thumbnail d-none" width="150">
                                            </div>
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

@section('js')

<script src="{{asset('assets/libs/flags/intlTelInput.js')}}"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {

    const input = document.getElementById("phone");
    const dialCodeInput = document.getElementById("dial_code");
    const dialCodeIsoInput = document.getElementById("dial_code_iso");

    const iti = window.intlTelInput(input, {
        initialCountry: "mx",
        separateDialCode: true,
        utilsScript: "{{ asset('assets/libs/flags/utils.js') }}"
    });

    // ✅ Set initial values on page load
    const initialCountry = iti.getSelectedCountryData();
    dialCodeInput.value = '+' + initialCountry.dialCode;
    dialCodeIsoInput.value = initialCountry.iso2;

    // ✅ Update BOTH values when country changes
    input.addEventListener("countrychange", function () {
        const countryData = iti.getSelectedCountryData();
        dialCodeInput.value = '+' + countryData.dialCode;
        dialCodeIsoInput.value = countryData.iso2;
    });

});
     function previewCoverImage(input) {
        const preview = document.getElementById('coverPreview');
        const file = input.files[0];

        if (file) {
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('d-none');
        }
    }
</script>
@endsection
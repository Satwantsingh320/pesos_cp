@extends('layouts.master')

@section('css')
<style>
    .select2-container .select2-selection--single .select2-selection__rendered {
    color: var(--bs-emphasis-color);
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
                        <a href="{{ route('promo-codes.index') }}" class="btn btn-dark btn-sm mx-2"><i class="bx bx-arrow-back"></i> {{__('admin.back')}}</a>
                        <h4 class="mb-sm-0 font-size-18"> {{__('admin.promo_codes')}}</h4>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">{{__('admin.create_promo_code')}} </h4>
                                 <form method="POST" action="{{ route('promo-codes.store') }}">
                                @csrf
                                <div class="row mb-4">
                                    <label for="horizontal-email-input" class="col-sm-3 col-form-label">{{__('admin.promo_type')}}</label>
                                    <div class="col-sm-9">
                                         <select name="promo_type" class="form-control select2" id="promo_type" required>
                                                <option value="" selected disabled>{{__('admin.select_promo_type')}}</option>
                                                <option value="2">Flat</option>
                                                <option value="1">Percentage (%)</option>
                                            </select>
                                    </div>
                                </div>
                                 <div class="row mb-4">
                                    <label for="horizontal-email-input" class="col-sm-3 col-form-label">{{__('admin.promo_code_amount')}}</label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                              <span class="input-group-text" id="amountPrefix">
                                                {{ env('CURRENCY_SYMBOL') }}
                                            </span>
                                         <input type="text" name="promo_code_amount" id="promo_code_amount" class="form-control __numeric" required value="{{ old('promo_code_amount') }}">
                                          <span class="input-group-text d-none" id="amountSuffix">%</span>
                                        </div>
                                    </div>
                                </div>
                                  <div class="row mb-4">
                                    <label for="horizontal-email-input" class="col-sm-3 col-form-label">{{__('admin.minimum_order_amount')}}</label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                             <span class="input-group-text">{{env('CURRENCY_SYMBOL')}}</span>
                                          <input type="text" name="minimum_order_amount" placeholder="{{__('admin.enter_minimum_order_amount')}}" class="form-control __numeric" required value="{{ old('minimum_order_amount') }}">
                                        </div>
                                    </div>
                                </div>
                                   <div class="row mb-4">
                                    <label for="horizontal-email-input" class="col-sm-3 col-form-label">{{__('admin.applied_to')}}</label>
                                    <div class="col-sm-9">
                                           <input type="radio" name="apply_to" value="all" class="apply" id="promo_all" checked> All Users
                                           <input type="radio" name="apply_to" value="login_user" class="apply" id="promo_all"> Login Users
                                    </div>
                                </div>
                                 <div class="row mb-4">
                                    <label for="horizontal-email-input" class="col-sm-3 col-form-label">{{__('admin.promo_code')}}</label>
                                    <div class="col-sm-9">
                                           <input type="radio" name="code_option" id="auto" value="auto" checked="" onClick="generateRandomString()"> Auto
                                           <input type="radio" name="code_option" id="custom" value="custom" checked=""> Custom
                                           <input type="hidden" name="code_type" id="code_type">
                                            <input type="text" name="code" value="<?php if (isset($code)) echo $code ?>" maxlength="10" required="" id="promo" placeholder="Promo Code" class="form-control">
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <label for="horizontal-email-input" class="col-sm-3 col-form-label">{{__('admin.start_date')}}</label>
                                    <div class="col-sm-9">
                                          <input type="date" name="start_date" id="start_date" required="" class="form-control">
                                    </div>
                                </div>
                                 <div class="row mb-4">
                                    <label for="horizontal-email-input" class="col-sm-3 col-form-label">{{__('admin.expiry_date')}}</label>
                                    <div class="col-sm-9">
                                          <input type="date" name="expiry_date" id="expiry_date" required="" class="form-control">
                                    </div>
                                </div>
                                 <div class="row mb-4">
                                    <label for="horizontal-email-input" class="col-sm-3 col-form-label">{{__('admin.total_used')}}</label>
                                    <div class="col-sm-9">
                                          <input type="number" name="total_used" min="1" id="total_used" class="form-control" required>
                                    </div>
                                </div>
                                 <div class="row mb-4">
                                    <label for="horizontal-email-input" class="col-sm-3 col-form-label">{{__('admin.per_user_used')}}</label>
                                    <div class="col-sm-9">
                                         <input type="number" name="per_user_used" min="1" id="per_user_used" class="form-control">
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

@section('js')
<script>
    //Change Promo code amount input prefix/suffix based on type selection
    $('#promo_type').on('change', function () {
    const type = $(this).val();

    if (type == 2) { // Flat
        $('#amountPrefix').text('{{ env('CURRENCY_SYMBOL') }}').removeClass('d-none');
        $('#amountSuffix').addClass('d-none');
         $('#promo_code_amount').attr('placeholder', "{!! __('admin.enter_promo_code_amount') !!}");
    
    }

    if (type == 1) { // Percentage
        $('#amountPrefix').addClass('d-none');
        $('#amountSuffix').removeClass('d-none');
         $('#promo_code_amount').attr('placeholder', "{!! __('admin.enter_percentage') !!}");

    }

    $('#promo_code_amount').val('');
});

 function generateRandomString() 
 {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789@#&";

        for (var i = 0; i < 8; i++)
            text += possible.charAt(Math.floor(Math.random() * possible.length));
        $("#promo").val(text);
        $("#code_type").val("0");
        $("#promo").attr("readonly", true);
        //return text;
}
$("#custom").click(function() {
        $("#promo").val("");
        $("#code_type").val("1");
        $("#promo").attr("readonly", false);
});
</script>
@endsection
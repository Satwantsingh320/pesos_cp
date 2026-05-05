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
                <div class="col-4">
                    <div class="page-title-box d-flex align-items-center">
                        <a href="{{ route('offers.index') }}" class="btn btn-dark btn-sm mx-2"><i class="bx bx-arrow-back"></i> {{__('admin.back')}}</a>
                        <h4 class="mb-sm-0 font-size-18">{{__('admin.offer_details')}}</h4>
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
                                        <h5 class="mb-1">{{ $offer->title }}</h5>
                                        @if($offer->status)
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
                            <h4 class="card-title mb-4">{{__('admin.update_offer_details')}} </h4>
                               <form method="POST" action="{{ route('offers.update',$offer->id) }}" enctype="multipart/form-data">
                                @method('put')
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="horizontal-email-input" class="col-sm-6 col-form-label">{{__('admin.select_category')}}</label>
                                             <select name="category" class="form-control select2 __category" data-route="{{route('subcategory.service')}}" required>
                                                 @foreach($categories as $key => $value)
                                                <option value="{{ $key }}" @if($offer->category_id == $key) selected @endif>
                                                {{ $value }}
                                            </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                              <label for="horizontal-email-input" class="col-sm-6 col-form-label">{{__('admin.select_subcategory')}}</label>
                                              <select name="subcategory" class="form-control select2 __subcategory" data-route="{{route('products.service')}}"  data-selected="{{ $offer->subcategory_id }}" required>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                 <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="horizontal-email-input" class="col-sm-6 col-form-label">{{__('admin.select_product')}}</label>
                                            <select name="product" class="form-control select2 __product"  data-route="{{ route('products.service') }}" data-price-route="{{ route('products.get-price') }}" data-selected="{{ $offer->product_id }}" required>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="horizontal-email-input" class="col-sm-6 col-form-label">{{__('admin.title')}}</label>
                                             <input type="text" name="title" placeholder="{{__('admin.enter_title')}}" class="form-control" required value="{{ $offer->title }}">
                                        </div>
                                    </div>
                                   
                                </div>

                                <div class="row">
                                     <div class="col-md-6">
                                        <div class="mb-3">
                                              <label for="horizontal-email-input" class="col-sm-6 col-form-label">{{__('admin.price')}}</label>
                                              <div class="input-group">
                                                    <span class="input-group-text">{{env('CURRENCY_SYMBOL')}}</span>
                                                    <input type="text" name="price" placeholder="{{__('admin.enter_price')}}" class="form-control __numeric_decimal __price" required value="{{ $offer->price }}" readonly>
                                              </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="horizontal-email-input" class="col-sm-6 col-form-label">{{__('admin.offer_price')}}</label>
                                            <div class="input-group">
                                                    <span class="input-group-text">{{env('CURRENCY_SYMBOL')}}</span>
                                                    <input type="text" name="offer_price" placeholder="{{__('admin.enter_offer_price')}}" class="form-control __numeric_decimal" required value="{{ $offer->offer_price }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                     <div class="col-md-6">
                                        <div class="mb-3">
                                              <label for="horizontal-email-input" class="col-sm-6 col-form-label">{{__('admin.offer_description')}}</label>
                                             <textarea class="form-control"  id="descriptionEditor" name="description">{!! $offer->description !!}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="horizontal-email-input" class="col-sm-6 col-form-label">{{__('admin.status')}}</label>
                                             <select name="status" id="" class="form-select">
                                                <option value="1" {{ $offer->status == '1' ? 'selected' : '' }}>{{__('admin.active')}}</option>
                                                <option value="0" {{ $offer->status == '0' ? 'selected' : '' }}>{{__('admin.inactive')}}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                 <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="horizontal-email-input" class="col-sm-6 col-form-label">{{__('admin.banner')}}</label>
                                             <input type="file" name="banner" class="form-control" accept="image/*" onchange="previewCoverImage(this)">
                                              <div class="mt-2">
                                                <img id="coverPreview" class="img-thumbnail d-none" width="150">
                                            </div>
                                            @if($offer->banner)
                                                @php
                                                    $imagePath = (!empty($offer->banner) && file_exists(public_path(OFFER_BANNERS_PATH . $offer->banner)))
                                                        ? asset(OFFER_BANNERS_PATH . $offer->banner)
                                                        : asset('assets/images/no-image.jpg');
                                                @endphp
                                                <img src="{{$imagePath}}" class="img-thumbnail preview-image" height="150" width="150" style="cursor:pointer" data-bs-toggle="modal"  data-bs-target="#imagePreviewModal">
                                            @endif
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
<!-- Image Preview Modal -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-body p-0 text-center">
                <img id="imagePreviewModalImg" src="" class="img-fluid w-100">
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script src="https://cdn.ckeditor.com/ckeditor5/40.2.0/classic/ckeditor.js"></script>
<script>
    //Editor
     ClassicEditor
        .create(document.querySelector('#descriptionEditor'), {
            toolbar: [
                'heading','|',
                'bold','italic','underline','|',
                'bulletedList','numberedList','|',
                'link','blockQuote','|',
                'undo','redo'
            ]
        })
        .then(editor => {
        const editable = editor.ui.view.editable.element;
        editable.style.height = '120px';
        editable.style.overflowY = 'auto';
    })
        .catch(error => {
            console.error(error);
        });
    //Previw uploaded images
    document.addEventListener('click', function (e) {
    if (e.target.classList.contains('preview-image')) {
        document.getElementById('imagePreviewModalImg').src = e.target.src;
    }
});
$(document).ready(function () {

    const categoryId = $('.__category').val();
    const subcategoryId = $('.__subcategory').data('selected');
    const productId = $('.__product').data('selected');

    // Load subcategories
    if (categoryId && subcategoryId) {
        select2Change('.__subcategory', $('.__category').data('route'), {
            category_id: categoryId,
            selected: subcategoryId
        });
    }

    // Load products AFTER subcategory is loaded
    if (subcategoryId && productId) {
        select2Change('.__product', $('.__product').data('route'), {
            subcategory_id: subcategoryId,
            selected: productId
        });
    }
});

    $('body').on('change', '.__category', function (e) {
    var value = $(this).val();
    if ($('.__category').length > 0) {
        if (value != '') {
            let route = $(this).attr('data-route');
            console.log(route);
            let title = $(this).attr('data-title');
            if (title == undefined) {
                title = '-Select-';
            }

            let data = {
                category_id: value,
                title: title,
            };
            select2Change('.__subcategory', route, data);
        }
    }
});
$('body').on('change', '.__subcategory', function () {
    const subcategoryId = $(this).val();
    const route = $('.__product').data('route');

    if (subcategoryId) {
        select2Change('.__product', route, {
            subcategory_id: subcategoryId
        });
    }
});
//Get Product Price
$('body').on('change', '.__product', function (e) {
    
    var value = $(this).val();
    if ($('.__product').length > 0) {
        if (value != '') {
            let route = $(this).attr('data-price-route');

            let data = {
                product_id: value
            };
           
            //Get & Display price in input box
                 $.ajax({
                    type: 'POST',
                    url: route,
                    headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                     },
                    data: data,
                    beforeSend: function () {
                        // showLoader();
                    },
                    success: function (res) {
                        if (res.success) {
                           $('.__price').val(res.price);
                        }
                    },
                    error: function (data) {
                        console.log('An error occurred.');
                    }
                });
        }
    }
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
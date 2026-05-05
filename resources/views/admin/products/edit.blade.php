@extends('layouts.master')

@section('css')
    <style>
        .select2-container .select2-selection--single .select2-selection__rendered {
            color: var(--bs-emphasis-color);
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
                background-color: #0d6efd;
                color: white;
                border: none;
            }
            .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
                color: white;
                border-right: 1px solid rgba(255,255,255,0.3);
            }
            .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
                background-color: rgba(255,255,255,0.2);
                color: white;
            }
            .gallery-item {
                position: relative;
            }
            .remove-gallery-image {
                position: absolute;
                top: 6px;
                right: 10px;
                background: rgba(0, 0, 0, 0.7);
                color: #fff;
                font-size: 18px;
                line-height: 18px;
                width: 22px;
                height: 22px;
                border-radius: 50%;
                text-align: center;
                cursor: pointer;
                z-index: 10;
            }
            .remove-gallery-image:hover {
                background: red;
            }
            .variant-card {
                background: #f8f9fa;
                border-radius: 8px;
                margin-bottom: 15px;
                padding: 15px;
                border: 1px solid #e9ecef;
            }
            .combination-badge {
                display: inline-block;
                background: #6c757d;
                color: white;
                padding: 2px 8px;
                border-radius: 4px;
                font-size: 12px;
                margin-right: 5px;
                margin-bottom: 5px;
            }
            .attribute-group {
                background: white;
                padding: 10px;
                border-radius: 6px;
                margin-bottom: 10px;
                border: 1px solid #e9ecef;
            }
            .select2-container {
                width: 100% !important;
            }
            .existing-variant-row {
                background-color: #f8f9fa;
            }
            .variant-section {
                transition: all 0.3s ease;
            }
            .stock-info {
                font-size: 12px;
                color: #6c757d;
            }
        </style>
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endsection

@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <!-- start page title -->
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center">
                            <a href="{{ route('products.index') }}" class="btn btn-dark btn-sm mx-2">
                                <i class="bx bx-arrow-back"></i> {{ __('admin.back') }}
                            </a>
                            <h4 class="mb-sm-0 font-size-18">{{ __('admin.update_product_details') }}</h4>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title mb-4">{{ __('admin.update_product_details') }}</h4>

                                <form method="POST" action="{{ route('products.update', $product->id) }}" enctype="multipart/form-data" id="productForm">
                                    @method('put')
                                    @csrf

                                    <!-- Basic Information -->
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('admin.select_category') }}</label>
                                                <select name="category" class="form-control select2 single-select __category" data-route="{{ route('subcategory.service') }}" required>
                                                    @foreach ($categories as $key => $value)
                                                        <option value="{{ $key }}" {{ $product->category_id == $key ? 'selected' : '' }}>
                                                            {{ $value }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('admin.select_subcategory') }}</label>
                                                <select name="subcategory" class="form-control select2 single-select __subcategory" data-selected="{{ $product->subcategory_id }}" required>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('admin.choose_brand') }}</label>
                                                <select name="brand" class="form-control select2 single-select" required>
                                                    <option value="" selected disabled>{{ __('admin.select') }}</option>
                                                    @foreach ($brands as $key => $value)
                                                        <option value="{{ $key }}" {{ $product->brand_id == $key ? 'selected' : '' }}>
                                                            {{ $value }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('admin.name') }}</label>
                                                <input type="text" id="product_name" name="name" placeholder="{{ __('admin.enter_item_name') }}" class="form-control" required value="{{ $product->name }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Slug</label>
                                                <div class="d-flex gap-2">
                                                    <input type="text" id="product_slug" name="slug" class="form-control" required value="{{ $product->slug }}">
                                                    <button type="button" id="regenSlug" class="btn btn-outline-secondary">@lang('admin.generate')</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Product Type Selection -->
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="card border">
                                                <div class="card-body">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="product_type" id="simple_product" value="simple" {{ !$product->has_variants ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="simple_product">Simple Product (No Variants)</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="product_type" id="variant_product" value="variant" {{ $product->has_variants ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="variant_product">Variable Product (With Variants)</label>
                                                    </div>
                                                    <input type="hidden" name="has_variants" id="has_variants" value="{{ $product->has_variants ? 1 : 0 }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Simple Product Fields -->
                                    <div id="simple_product_fields" style="{{ $product->has_variants ? 'display: none;' : '' }}">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('admin.sku_number') }}</label>
                                                    <input type="text" name="sku_number" placeholder="{{ __('admin.enter_sku_number') }}" class="form-control" value="{{ $product->sku_number }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('admin.barcode_number') }}</label>
                                                    <input type="text" name="barcode_number" placeholder="{{ __('admin.enter_barcode_number') }}" class="form-control" value="{{ $product->barcode_number }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('admin.price') }}</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">{{ env('CURRENCY_SYMBOL') }}</span>
                                                        <input type="text" name="price" placeholder="{{ __('admin.enter_price') }}" class="form-control __numeric_decimal" value="{{ $product->price }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('admin.offer_price') }}</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">{{ env('CURRENCY_SYMBOL') }}</span>
                                                        <input type="text" name="offer_price" placeholder="{{ __('admin.enter_offer_price') }}" class="form-control __numeric_decimal" value="{{ $product->offer_price != $product->price ? $product->offer_price : '' }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('admin.no_of_pieces_available') }}</label>
                                                    <input type="text" name="pieces_available" placeholder="{{ __('admin.no_of_pieces_available') }}" class="form-control __numeric" value="{{ $product->no_of_pieces_available }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Variant Product Fields -->
                                    <div id="variant_product_fields" style="{{ $product->has_variants ? '' : 'display: none;' }}">
                                        <!-- Attribute Selection -->
                                        <div class="row mb-4">
                                            <div class="col-md-12">
                                                <h5>Product Attributes</h5>
                                                <div id="attribute_selection">
                                                    @foreach($attributes as $attribute)
                                                        @php
                                                            $existingAttributeValues = [];
                                                            if ($product->has_variants && $product->variants->count() > 0) {
                                                                foreach ($product->variants as $variant) {
                                                                    foreach ($variant->combinations as $combination) {
                                                                        if ($combination->attribute_id == $attribute->id) {
                                                                            $existingAttributeValues[] = $combination->attribute_value_id;
                                                                        }
                                                                    }
                                                                }
                                                                $existingAttributeValues = array_unique($existingAttributeValues);
                                                            }
                                                        @endphp
                                                        <div class="attribute-group">
                                                            <label class="form-label fw-bold">{{ $attribute->display_name }}</label>
                                                            <select class="form-control attribute-select select2-multiple"
                                                                    data-attribute-id="{{ $attribute->id }}"
                                                                    multiple="multiple">
                                                                @foreach($attribute->values as $value)
                                                                    <option value="{{ $value->id }}"
                                                                            {{ in_array($value->id, $existingAttributeValues) ? 'selected' : '' }}
                                                                            {{ $value->color_code ? 'data-color="' . $value->color_code . '"' : '' }}>
                                                                        {{ $value->value }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <button type="button" class="btn btn-primary mt-2" id="generateVariantsBtn">
                                                    <i class="bx bx-plus"></i> Generate / Update Variants
                                                </button>
                                                <small class="text-muted d-block mt-2">Note: Generating variants will replace existing variants. Make sure to save after generating.</small>
                                            </div>
                                        </div>

                                        <!-- Variants Table -->
                                        <div class="row mt-4">
                                            <div class="col-md-12">
                                                <h5>Product Variants</h5>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered" id="variantsTable">
                                                        <thead>
                                                            <tr>
                                                                <th width="15%">Combination</th>
                                                                <th width="15%">SKU</th>
                                                                <th width="12%">Barcode</th>
                                                                <th width="10%">Price</th>
                                                                <th width="10%">Offer Price</th>
                                                                <th width="8%">Quantity</th>
                                                                <th width="10%">Low Stock</th>
                                                                <th width="8%">Status</th>
                                                                <th width="5%"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="variantsTableBody">
                                                            @if($product->has_variants && $product->variants->count() > 0)
                                                                @foreach($product->variants as $index => $variant)
                                                                    @php
                                                                        $combinationHtml = '';
                                                                        $combinationArray = [];
                                                                        foreach ($variant->combinations as $combination) {
                                                                            $combinationHtml .= '<span class="combination-badge">' . $combination->attributeValue->value . '</span>';
                                                                            $combinationArray[$combination->attribute_id] = $combination->attribute_value_id;
                                                                        }
                                                                    @endphp
                                                                    <tr data-combination='@json($combinationArray)'>
                                                                        <td>{!! $combinationHtml !!}</td>
                                                                        <td>
                                                                            <input type="text" name="variants[{{ $index }}][id]" value="{{ $variant->id }}" hidden>
                                                                            <input type="text" name="variants[{{ $index }}][sku]" class="form-control variant-sku" value="{{ $variant->sku }}" required>
                                                                            @foreach($combinationArray as $attrId => $valueId)
                                                                                <input type="hidden" name="variants[{{ $index }}][attributes][{{ $attrId }}]" value="{{ $valueId }}">
                                                                            @endforeach
                                                                        </td>
                                                                        <td><input type="text" name="variants[{{ $index }}][barcode]" class="form-control" value="{{ $variant->barcode }}"></td>
                                                                        <td><input type="text" name="variants[{{ $index }}][price]" class="form-control variant-price __numeric_decimal" value="{{ $variant->price }}" required></td>
                                                                        <td><input type="text" name="variants[{{ $index }}][offer_price]" class="form-control variant-offer-price __numeric_decimal" value="{{ $variant->offer_price }}"></td>
                                                                        <td><input type="number" name="variants[{{ $index }}][quantity]" class="form-control variant-qty" value="{{ $variant->quantity }}" required></td>
                                                                        <td><input type="number" name="variants[{{ $index }}][low_stock_threshold]" class="form-control" value="{{ $variant->low_stock_threshold ?? 5 }}"></td>
                                                                        <td>
                                                                            <select name="variants[{{ $index }}][status]" class="form-select">
                                                                                <option value="1" {{ $variant->status == 1 ? 'selected' : '' }}>Active</option>
                                                                                <option value="0" {{ $variant->status == 0 ? 'selected' : '' }}>Inactive</option>
                                                                            </select>
                                                                        </td>
                                                                        <td><button type="button" class="btn btn-sm btn-danger remove-variant"><i class="bx bx-trash"></i></button></td>
                                                                    </tr>
                                                                @endforeach
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Common Fields -->
                                    <div class="row mt-4">
                                        <div class="col-md-6">
                                            <div class="product-visibility">
                                                <h6>@lang('admin.product_visibility') : </h6>
                                                <div class="form-check mb-1">
                                                    <input class="form-check-input" id="is_special_offer" type="checkbox" name="is_special_offer" value="1" {{ $product->is_special_offer == 1 ? 'checked' : '' }}>
                                                    <label for="is_special_offer" class="form-check-label">Special Offer</label>
                                                </div>
                                                <div class="form-check mb-1">
                                                    <input class="form-check-input" type="checkbox" name="is_clearance" id="is_clearance" value="1" {{ $product->is_clearance == 1 ? 'checked' : '' }}>
                                                    <label for="is_clearance" class="form-check-label">Clearance Item</label>
                                                </div>
                                                <div class="form-check mb-1">
                                                    <input id="is_featured" class="form-check-input" type="checkbox" name="is_featured" value="1" {{ $product->is_featured == 1 ? 'checked' : '' }}>
                                                    <label for="is_featured" class="form-check-label">Featured Product</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('admin.shipping_fee') }}</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">{{ env('CURRENCY_SYMBOL') }}</span>
                                                    <input type="text" name="shipping_fee" placeholder="{{ __('admin.enter_shipping_fee') }}" class="form-control __numeric_decimal" required value="{{ $product->shipping_fee }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('admin.return_days') }}</label>
                                                <input type="number" name="return_days" placeholder="{{ __('admin.enter_return_days') }}" class="form-control" required value="{{ $product->return_days }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('admin.estimated_delivery_time') }}</label>
                                                <div class="input-group">
                                                    <input type="text" name="estimated_delivery_time" placeholder="{{ __('admin.enter_no_of_days') }}" class="form-control __numeric" required value="{{ $product->estimated_delivery_time }}">
                                                    <span class="input-group-text">{{ __('admin.days') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('admin.cover_image') }}</label>
                                                <input type="file" name="cover_image" class="form-control" accept="image/*" onchange="previewCoverImage(this)">
                                                <div class="mt-2">
                                                    <img id="coverPreview" class="img-thumbnail d-none" width="150">
                                                </div>
                                                @if ($product->cover_image)
                                                    @php
                                                        $imagePath = !empty($product->cover_image) && file_exists(public_path(PRODUCTS_PATH . $product->cover_image))
                                                            ? asset(PRODUCTS_PATH . $product->cover_image)
                                                            : asset('assets/images/no-image.jpg');
                                                    @endphp
                                                    <img src="{{ $imagePath }}" class="img-thumbnail preview-image existing-cover" height="150" width="150" id="coverPreviewRemove" style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#imagePreviewModal">
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('admin.gallery_images') }}</label>
                                                <input type="file" name="gallery_images[]" class="form-control" accept="image/*" onchange="previewGalleryImages(this)" multiple>
                                                <input type="hidden" name="removed_gallery_images" id="removedGalleryImages">
                                                <div id="galleryPreview" class="row mt-2">
                                                    @if (!empty($product->gallery))
                                                        @foreach ($product->gallery as $key => $val)
                                                            @php
                                                                $gimage = !empty($val->image) && file_exists(public_path(PRODUCTS_PATH . $val->image))
                                                                    ? asset(PRODUCTS_PATH . $val->image)
                                                                    : asset('assets/images/no-image.jpg');
                                                            @endphp
                                                            <div class="col-2 mb-3 gallery-item position-relative">
                                                                <span class="remove-gallery-image" data-id="{{ $val->id }}">×</span>
                                                                <img src="{{ $gimage }}" class="img-thumbnail preview-image mb-2" height="150" width="150" style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#imagePreviewModal">
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('admin.item_description') }}</label>
                                                <textarea class="form-control" id="descriptionEditor" name="description">{{ $product->description }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('admin.status') }}</label>
                                                <select name="status" class="form-select">
                                                    <option value="1" {{ $product->status == '1' ? 'selected' : '' }}>{{ __('admin.active') }}</option>
                                                    <option value="0" {{ $product->status == '0' ? 'selected' : '' }}>{{ __('admin.inactive') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('admin.type') }}</label>
                                                <select name="type" class="form-select">
                                                    <option value="Nuevo" {{ $product->type == 'Nuevo' ? 'selected' : '' }}>{{ __('admin.New') }}</option>
                                                    <option value="Reacondicionado" {{ $product->type == 'Reacondicionado' ? 'selected' : '' }}>{{ __('admin.Refurbished') }}</option>
                                                    <option value="Usado" {{ $product->type == 'Usado' ? 'selected' : '' }}>{{ __('admin.Used') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row d-flex justify-content-center my-5">
                                        <div class="col-sm-2">
                                            <button type="submit" class="btn btn-primary w-md">{{ __('admin.submit') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize all Select2 single selects
            $('.single-select').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Select an option',
                allowClear: true
            });

            // Initialize all Select2 multiple selects
            $('.select2-multiple').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Select options',
                allowClear: true,
                closeOnSelect: false
            });
        });

        // CKEditor setup
        ClassicEditor
            .create(document.querySelector('#descriptionEditor'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'underline', '|', 'bulletedList', 'numberedList', '|', 'link', 'blockQuote', '|', 'undo', 'redo']
            })
            .then(editor => {
                const editable = editor.ui.view.editable.element;
                editable.style.height = '120px';
                editable.style.overflowY = 'auto';
            })
            .catch(error => console.error(error));

        // Product type toggle
        $('input[name="product_type"]').on('change', function() {
            if ($(this).val() == 'simple') {
                $('#simple_product_fields').slideDown();
                $('#variant_product_fields').slideUp();
                $('#has_variants').val(0);

                // Make simple product fields required
                $('input[name="sku_number"]').attr('required', true);
                $('input[name="barcode_number"]').attr('required', true);
                $('input[name="price"]').attr('required', true);
                $('input[name="pieces_available"]').attr('required', true);
            } else {
                $('#simple_product_fields').slideUp();
                $('#variant_product_fields').slideDown();
                $('#has_variants').val(1);

                // Remove required from simple product fields
                $('input[name="sku_number"]').removeAttr('required');
                $('input[name="barcode_number"]').removeAttr('required');
                $('input[name="price"]').removeAttr('required');
                $('input[name="pieces_available"]').removeAttr('required');
            }
        });

        // Generate SKU
        function generateSku(combination, index) {
            let skuBase = $('#product_name').val().replace(/\s+/g, '-').substring(0, 20);
            let combinationStr = Object.values(combination).join('-');
            return (skuBase + '-' + combinationStr + '-' + index).toUpperCase();
        }

        // Generate variants from selected attributes
      // Generate variants from selected attributes
$('#generateVariantsBtn').on('click', function() {
    let selectedAttributes = {};
    let hasAttributes = false;

    $('.attribute-select').each(function() {
        let attributeId = $(this).data('attribute-id');
        let selectedValues = $(this).val();
        if (selectedValues && selectedValues.length > 0) {
            selectedAttributes[attributeId] = selectedValues;
            hasAttributes = true;
        }
    });

    if (!hasAttributes) {
        toastr.warning('Please select at least one attribute value');
        return;
    }

    // Generate combinations
    let combinations = generateCombinations(selectedAttributes);

    // Clear existing table body
    $('#variantsTableBody').empty();

    // Build variants table rows
    combinations.forEach((combination, index) => {
        let combinationHtml = '';
        let attributeIds = Object.keys(combination);

        // Get attribute value names for display
        attributeIds.forEach(attrId => {
            let valueId = combination[attrId];
            let valueText = $(`.attribute-select[data-attribute-id="${attrId}"] option[value="${valueId}"]`).text();
            combinationHtml += `<span class="combination-badge">${valueText}</span>`;
        });

        // Create table row with proper structure
        let row = $('<tr>');

        // Combination column
        row.append($('<td>').html(combinationHtml));

        // SKU column
        let skuCell = $('<td>');
        let skuInput = $(`<input type="text" name="variants[${index}][sku]" class="form-control variant-sku" value="${generateSku(combination, index)}" required>`);
        skuCell.append(skuInput);

        // Add hidden attributes
        attributeIds.forEach(attrId => {
            let valueId = combination[attrId];
            skuCell.append(`<input type="hidden" name="variants[${index}][attributes][${attrId}]" value="${valueId}">`);
        });
        row.append(skuCell);

        // Barcode column
        let barcodeCell = $('<td>');
        barcodeCell.append(`<input type="text" name="variants[${index}][barcode]" class="form-control">`);
        row.append(barcodeCell);

        // Price column
        let priceCell = $('<td>');
        priceCell.append(`<input type="text" name="variants[${index}][price]" class="form-control variant-price __numeric_decimal" required>`);
        row.append(priceCell);

        // Offer Price column
        let offerPriceCell = $('<td>');
        offerPriceCell.append(`<input type="text" name="variants[${index}][offer_price]" class="form-control variant-offer-price __numeric_decimal">`);
        row.append(offerPriceCell);

        // Quantity column
        let qtyCell = $('<td>');
        qtyCell.append(`<input type="number" name="variants[${index}][quantity]" class="form-control variant-qty" value="0" required>`);
        row.append(qtyCell);

        // Low Stock Threshold column
        let lowStockCell = $('<td>');
        lowStockCell.append(`<input type="number" name="variants[${index}][low_stock_threshold]" class="form-control" value="5">`);
        row.append(lowStockCell);

        // Status column
        let statusCell = $('<td>');
        let statusSelect = $(`
            <select name="variants[${index}][status]" class="form-select">
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
        `);
        statusCell.append(statusSelect);
        row.append(statusCell);

        // Action column
        let actionCell = $('<td>');
        let removeBtn = $(`<button type="button" class="btn btn-sm btn-danger remove-variant"><i class="bx bx-trash"></i></button>`);
        actionCell.append(removeBtn);
        row.append(actionCell);

        // Add row to table
        $('#variantsTableBody').append(row);
    });

    bindVariantEvents();
    toastr.success(`${combinations.length} variants generated successfully`);
});

        function generateCombinations(attributes) {
            let combinations = [{}];

            for (let attrId in attributes) {
                let values = attributes[attrId];
                let newCombinations = [];
                for (let combo of combinations) {
                    for (let value of values) {
                        let newCombo = { ...combo, [attrId]: value };
                        newCombinations.push(newCombo);
                    }
                }
                combinations = newCombinations;
            }

            return combinations;
        }

        function bindVariantEvents() {
            // Remove variant
            $('.remove-variant').on('click', function() {
                if (confirm('Remove this variant?')) {
                    $(this).closest('tr').remove();
                }
            });

            // Price validation
            $('.variant-price').on('change', function() {
                let price = parseFloat($(this).val()) || 0;
                let offerPrice = $(this).closest('tr').find('.variant-offer-price').val();
                if (offerPrice && parseFloat(offerPrice) >= price) {
                    toastr.warning('Offer price must be less than regular price');
                    $(this).closest('tr').find('.variant-offer-price').val('');
                }
            });

            $('.variant-offer-price').on('change', function() {
                let offerPrice = parseFloat($(this).val()) || 0;
                let price = parseFloat($(this).closest('tr').find('.variant-price').val()) || 0;
                if (offerPrice >= price) {
                    toastr.warning('Offer price must be less than regular price');
                    $(this).val('');
                }
            });
        }

        // Auto load selected subcategory
        $(document).ready(function() {
            let categoryId = $('.__category').val();
            let selectedSubcategory = $('.__subcategory').data('selected');
            let route = $('.__category').data('route');

            if (categoryId && selectedSubcategory) {
                $.ajax({
                    url: route,
                    type: 'POST',
                    data: {
                        category_id: categoryId,
                        selected: selectedSubcategory,
                        title: '-Select-',
                         _token: '{{ csrf_token() }}'
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            $('.__subcategory').html(response.options);
                            $('.__subcategory').val(selectedSubcategory).trigger('change');
                            // Reinitialize select2
                            $('.__subcategory').select2({
                                theme: 'bootstrap-5',
                                width: '100%',
                                placeholder: 'Select an option',
                                allowClear: true
                            });
                        }
                    }
                });
            }
        });

        // Category change
        $('body').on('change', '.__category', function(e) {
            var value = $(this).val();
            if ($('.__category').length > 0 && value != '') {
                let route = $(this).attr('data-route');
                let data = {
                    category_id: value,
                    title: '-Select-',
                };

                $.ajax({
                    url: route,
                    type: 'POST',
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            $('.__subcategory').html(response.options);
                            $('.__subcategory').select2({
                                theme: 'bootstrap-5',
                                width: '100%',
                                placeholder: 'Select an option',
                                allowClear: true
                            });
                        }
                    }
                });
            }
        });

        // Preview functions
        function previewCoverImage(input) {
            const preview = document.getElementById('coverPreview');
            const existingCover = document.querySelector('.existing-cover');
            if (existingCover) {
                existingCover.classList.add('d-none');
            }
            const file = input.files[0];
            if (file) {
                preview.src = URL.createObjectURL(file);
                preview.classList.remove('d-none');
            }
        }

        function previewGalleryImages(input) {
            const preview = document.getElementById('galleryPreview');
            const file = input.files[0];
            if (file) {
                const col = document.createElement('div');
                col.className = 'col-3 mb-3 position-relative gallery-item';
                const removeBtn = document.createElement('span');
                removeBtn.innerHTML = '×';
                removeBtn.className = 'remove-gallery-image';
                removeBtn.onclick = () => col.remove();
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.className = 'img-thumbnail preview-image w-100';
                img.dataset.bsToggle = "modal";
                img.dataset.bsTarget = "#imagePreviewModal";
                col.appendChild(removeBtn);
                col.appendChild(img);
                preview.appendChild(col);
            }
        }

        // Image preview modal
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('preview-image')) {
                document.getElementById('imagePreviewModalImg').src = e.target.src;
            }
        });

        // Remove gallery images
        let removedGallery = [];

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-gallery-image')) {
                const imageId = e.target.dataset.id;
                if (confirm('Remove this image?')) {
                    if (imageId) {
                        removedGallery.push(imageId);
                        document.getElementById('removedGalleryImages').value = removedGallery.join(',');
                    }
                    e.target.closest('.gallery-item').remove();
                }
            }
        });

        // Slug generation
        $('#product_name').on('keyup', function() {
            let slug = $(this).val().toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
            $('#product_slug').val(slug);
        });

        $('#regenSlug').on('click', function() {
            let slug = $('#product_name').val().toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
            $('#product_slug').val(slug);
        });

        // Form submission validation
        $('#productForm').on('submit', function(e) {
            if ($('#has_variants').val() == '1') {
                let hasVariants = $('#variantsTableBody tr').length > 0;
                if (!hasVariants) {
                    e.preventDefault();
                    toastr.error('Please generate at least one variant for this product');
                    return false;
                }

                // Validate variant prices
                let valid = true;
                $('.variant-price').each(function() {
                    if (!$(this).val() || parseFloat($(this).val()) <= 0) {
                        valid = false;
                        toastr.error('Please enter valid price for all variants');
                        return false;
                    }
                });

                if (!valid) {
                    e.preventDefault();
                    return false;
                }
            }

            // Validate simple product fields
            if ($('#has_variants').val() == '0') {
                if (!$('input[name="sku_number"]').val()) {
                    e.preventDefault();
                    toastr.error('Please enter SKU number');
                    return false;
                }
                if (!$('input[name="price"]').val() || parseFloat($('input[name="price"]').val()) <= 0) {
                    e.preventDefault();
                    toastr.error('Please enter valid price');
                    return false;
                }
            }

            // Bind variant events
            bindVariantEvents();
        });
    </script>
@endsection

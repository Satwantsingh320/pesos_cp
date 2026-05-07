@extends('layouts.master')

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <style>
        .variant-image-preview {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
            cursor: pointer;
        }
        .variant-image-container {
            position: relative;
            display: inline-block;
        }
        .remove-variant-image {
            position: absolute;
            top: -8px;
            right: -8px;
            background: red;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 12px;
            text-align: center;
            cursor: pointer;
            line-height: 18px;
        }
        .combination-badge {
            display: inline-block;
            background: #6c757d;
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 12px;
            margin: 2px;
        }
        .gallery-item {
            position: relative;
        }
        .remove-gallery-image {
            position: absolute;
            top: -8px;
            right: -8px;
            background: red;
            color: white;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            text-align: center;
            cursor: pointer;
            z-index: 10;
            font-size: 14px;
            line-height: 20px;
        }
        .preview-image {
            cursor: pointer;
        }
    </style>
@endsection

@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
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
                                                <label class="form-label">{{ __('admin.slug') }}</label>
                                                <div class="d-flex gap-2">
                                                    <input type="text" id="product_slug" name="slug" class="form-control" required value="{{ $product->slug }}">
                                                    <button type="button" id="regenSlug" class="btn btn-outline-secondary">{{ __('admin.generate') }}</button>
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
                                                        <label class="form-check-label" for="simple_product">{{ __('admin.simple_product_no_variants') }}</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="product_type" id="variant_product" value="variant" {{ $product->has_variants ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="variant_product">{{ __('admin.variable_product_with_variants') }}</label>
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
                                                    <input type="text" name="pieces_available" placeholder="{{ __('admin.no_of_pieces_available') }}" class="form-control __numeric" value="{{ $product->quantity }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Variant Product Fields -->
                                    <div id="variant_product_fields" style="{{ $product->has_variants ? '' : 'display: none;' }}">
                                        <div class="row mb-4">
                                            <div class="col-md-12">
                                                <h5>{{ __('admin.product_attributes') }}</h5>
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
                                                        <div class="attribute-group mb-3">
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
                                                    <i class="bx bx-plus"></i> {{ __('admin.generate_variants') }}
                                                </button>
                                                <small class="text-muted d-block mt-2">{{ __('admin.generate_variants_note') }}</small>
                                            </div>
                                        </div>

                                        <div class="row mt-4">
                                            <div class="col-md-12">
                                                <h5>{{ __('admin.product_variants') }}</h5>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered" id="variantsTable">
                                                        <thead>
                                                            <tr>
                                                                <th width="15%">{{ __('admin.combination') }}</th>
                                                                <th width="10%">{{ __('admin.image') }}</th>
                                                                <th width="15%">{{ __('admin.sku') }}</th>
                                                                <th width="12%">{{ __('admin.barcode') }}</th>
                                                                <th width="10%">{{ __('admin.price') }}</th>
                                                                <th width="8%">{{ __('admin.quantity') }}</th>
                                                                <th width="10%">{{ __('admin.low_stock') }}</th>
                                                                <th width="8%">{{ __('admin.status') }}</th>
                                                                <th width="5%">{{ __('admin.actions') }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="variantsTableBody">
                                                            @if($product->has_variants && $product->variants->count() > 0)
                                                                @foreach($product->variants as $index => $variant)
                                                                    @php
                                                                        $combinationHtml = '';
                                                                        $combinationArray = [];
                                                                        foreach ($variant->combinations as $combination) {
                                                                            $combinationHtml .= '<span class="combination-badge">' . e($combination->attributeValue->value) . '</span>';
                                                                            $combinationArray[$combination->attribute_id] = $combination->attribute_value_id;
                                                                        }
                                                                        $variantImage = $variant->image ? asset(PRODUCTS_PATH . $variant->image) : null;
                                                                    @endphp
                                                                    <tr data-combination='@json($combinationArray)'>
                                                                        <td>{!! $combinationHtml !!}</td>
                                                                        <td>
                                                                            <div class="variant-image-container">
                                                                                @if($variantImage)
                                                                                    <img src="{{ $variantImage }}" class="variant-image-preview" alt="Variant Image">
                                                                                    <span class="remove-variant-image" data-variant-id="{{ $variant->id }}" data-image="{{ $variant->image }}">×</span>
                                                                                    <input type="hidden" name="variants[{{ $index }}][existing_image]" value="{{ $variant->image }}">
                                                                                @endif
                                                                                <input type="file" name="variants[{{ $index }}][image]" class="form-control mt-1 variant-image-input" accept="image/*" style="font-size: 12px;">
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant->id }}">
                                                                            <input type="text" name="variants[{{ $index }}][sku]" class="form-control variant-sku" value="{{ $variant->sku }}" required>
                                                                            @foreach($combinationArray as $attrId => $valueId)
                                                                                <input type="hidden" name="variants[{{ $index }}][attributes][{{ $attrId }}]" value="{{ $valueId }}">
                                                                            @endforeach
                                                                        </td>
                                                                        <td><input type="text" name="variants[{{ $index }}][barcode]" class="form-control" value="{{ $variant->barcode }}"></td>
                                                                        <td><input type="text" name="variants[{{ $index }}][price]" class="form-control variant-price __numeric_decimal" value="{{ $variant->price }}" required></td>
                                                                        <td><input type="number" name="variants[{{ $index }}][quantity]" class="form-control variant-qty" value="{{ $variant->quantity }}" required></td>
                                                                        <td><input type="number" name="variants[{{ $index }}][low_stock_threshold]" class="form-control" value="{{ $variant->low_stock_threshold ?? 5 }}"></td>
                                                                        <td>
                                                                            <select name="variants[{{ $index }}][status]" class="form-select">
                                                                                <option value="1" {{ $variant->status == 1 ? 'selected' : '' }}>{{ __('admin.active') }}</option>
                                                                                <option value="0" {{ $variant->status == 0 ? 'selected' : '' }}>{{ __('admin.inactive') }}</option>
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
                                                <h6>{{ __('admin.product_visibility') }}:</h6>
                                                <div class="form-check mb-1">
                                                    <input class="form-check-input" id="is_special_offer" type="checkbox" name="is_special_offer" value="1" {{ $product->is_special_offer == 1 ? 'checked' : '' }}>
                                                    <label for="is_special_offer" class="form-check-label">{{ __('admin.special_offer') }}</label>
                                                </div>
                                                <div class="form-check mb-1">
                                                    <input class="form-check-input" type="checkbox" name="is_clearance" id="is_clearance" value="1" {{ $product->is_clearance == 1 ? 'checked' : '' }}>
                                                    <label for="is_clearance" class="form-check-label">{{ __('admin.clearance_item') }}</label>
                                                </div>
                                                <div class="form-check mb-1">
                                                    <input id="is_featured" class="form-check-input" type="checkbox" name="is_featured" value="1" {{ $product->is_featured == 1 ? 'checked' : '' }}>
                                                    <label for="is_featured" class="form-check-label">{{ __('admin.featured_product') }}</label>
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
                                                    <img src="{{ asset(PRODUCTS_PATH . $product->cover_image) }}" class="img-thumbnail existing-cover" height="150" width="150">
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
                                                            <div class="col-2 mb-3 gallery-item position-relative">
                                                                <span class="remove-gallery-image" data-id="{{ $val->id }}">×</span>
                                                                <img src="{{ asset(PRODUCTS_PATH . $val->image) }}" class="img-thumbnail preview-image mb-2" height="150" width="150">
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
                                                <label class="form-label">{{ __('admin.type') }}</label>
                                                <select name="type" class="form-select">
                                                    <option value="Nuevo" {{ $product->type == 'Nuevo' ? 'selected' : '' }}>{{ __('admin.new') }}</option>
                                                    <option value="Reacondicionado" {{ $product->type == 'Reacondicionado' ? 'selected' : '' }}>{{ __('admin.refurbished') }}</option>
                                                    <option value="Usado" {{ $product->type == 'Usado' ? 'selected' : '' }}>{{ __('admin.used') }}</option>
                                                </select>
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
@endsection

@section('js')
    <script src="https://cdn.ckeditor.com/ckeditor5/40.2.0/classic/ckeditor.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.single-select').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: '{{ __("admin.select_an_option") }}',
                allowClear: true
            });

            $('.select2-multiple').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: '{{ __("admin.select_options") }}',
                allowClear: true,
                closeOnSelect: false
            });
        });

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

        $('input[name="product_type"]').on('change', function() {
            if ($(this).val() == 'simple') {
                $('#simple_product_fields').slideDown();
                $('#variant_product_fields').slideUp();
                $('#has_variants').val(0);
                $('input[name="sku_number"]').attr('required', true);
                $('input[name="barcode_number"]').attr('required', true);
                $('input[name="price"]').attr('required', true);
                $('input[name="pieces_available"]').attr('required', true);
            } else {
                $('#simple_product_fields').slideUp();
                $('#variant_product_fields').slideDown();
                $('#has_variants').val(1);
                $('input[name="sku_number"]').removeAttr('required');
                $('input[name="barcode_number"]').removeAttr('required');
                $('input[name="price"]').removeAttr('required');
                $('input[name="pieces_available"]').removeAttr('required');
            }
        });

        function generateSku(combination, index) {
            let skuBase = $('#product_name').val().replace(/\s+/g, '-').substring(0, 20);
            let combinationStr = Object.values(combination).join('-');
            return (skuBase + '-' + combinationStr + '-' + index).toUpperCase();
        }

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
                toastr.warning('{{ __("admin.please_select_at_least_one_attribute") }}');
                return;
            }

            let combinations = generateCombinations(selectedAttributes);
            $('#variantsTableBody').empty();

            combinations.forEach((combination, index) => {
                let combinationHtml = '';
                let attributeIds = Object.keys(combination);

                attributeIds.forEach(attrId => {
                    let valueId = combination[attrId];
                    let valueText = $(`.attribute-select[data-attribute-id="${attrId}"] option[value="${valueId}"]`).text();
                    combinationHtml += `<span class="combination-badge">${valueText}</span>`;
                });

                let row = $('<tr>');

                row.append($('<td>').html(combinationHtml));

                let skuCell = $('<td>');
                skuCell.append(`<input type="text" name="variants[${index}][sku]" class="form-control variant-sku" value="${generateSku(combination, index)}" required>`);
                attributeIds.forEach(attrId => {
                    let valueId = combination[attrId];
                    skuCell.append(`<input type="hidden" name="variants[${index}][attributes][${attrId}]" value="${valueId}">`);
                });
                row.append(skuCell);

                row.append(`<td><input type="text" name="variants[${index}][barcode]" class="form-control"></td>`);
                row.append(`<td><input type="text" name="variants[${index}][price]" class="form-control variant-price __numeric_decimal" required></td>`);
                row.append(`<td><input type="number" name="variants[${index}][quantity]" class="form-control variant-qty" value="0" required></td>`);
                row.append(`<td><input type="number" name="variants[${index}][low_stock_threshold]" class="form-control" value="5"></td>`);
                row.append(`<td><select name="variants[${index}][status]" class="form-select"><option value="1">{{ __("admin.active") }}</option><option value="0">{{ __("admin.inactive") }}</option></select></td>`);
                row.append(`<td><button type="button" class="btn btn-sm btn-danger remove-variant"><i class="bx bx-trash"></i></button></td>`);

                $('#variantsTableBody').append(row);
            });

            bindVariantEvents();
            toastr.success(combinations.length + ' {{ __("admin.variants_generated_successfully") }}');
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
            $('.remove-variant').off('click').on('click', function() {
                if (confirm('{{ __("admin.remove_variant_confirmation") }}')) {
                    $(this).closest('tr').remove();
                }
            });

            $('.remove-variant-image').off('click').on('click', function() {
                if (confirm('{{ __("admin.remove_variant_image_confirmation") }}')) {
                    let variantId = $(this).data('variant-id');
                    let imagePath = $(this).data('image');
                    $(this).closest('.variant-image-container').find('.variant-image-preview').remove();
                    $(this).remove();
                    $(this).closest('.variant-image-container').append(`<input type="hidden" name="remove_variant_image[${variantId}]" value="${imagePath}">`);
                }
            });
        }

        $(document).ready(function() {
            let categoryId = $('.__category').val();
            let selectedSubcategory = $('.__subcategory').data('selected');
            let route = $('.__category').data('route');

            if (categoryId && selectedSubcategory) {
                $.ajax({
                    url: route,
                    type: 'POST',
                    data: { category_id: categoryId, selected: selectedSubcategory, title: '-Select-', _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            $('.__subcategory').html(response.options);
                            $('.__subcategory').val(selectedSubcategory).trigger('change');
                            $('.__subcategory').select2({ theme: 'bootstrap-5', width: '100%', placeholder: '{{ __("admin.select_an_option") }}', allowClear: true });
                        }
                    }
                });
            }
        });

        $('body').on('change', '.__category', function(e) {
            var value = $(this).val();
            if ($('.__category').length > 0 && value != '') {
                let route = $(this).attr('data-route');
                $.ajax({
                    url: route,
                    type: 'POST',
                    data: { category_id: value, title: '-Select-', _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            $('.__subcategory').html(response.options);
                            $('.__subcategory').select2({ theme: 'bootstrap-5', width: '100%', placeholder: '{{ __("admin.select_an_option") }}', allowClear: true });
                        }
                    }
                });
            }
        });

        function previewCoverImage(input) {
            const preview = document.getElementById('coverPreview');
            const existingCover = document.querySelector('.existing-cover');
            if (existingCover) existingCover.style.display = 'none';
            if (input.files && input.files[0]) {
                preview.src = URL.createObjectURL(input.files[0]);
                preview.classList.remove('d-none');
            }
        }

        function previewGalleryImages(input) {
            const preview = document.getElementById('galleryPreview');
            if (input.files && input.files[0]) {
                for (let file of input.files) {
                    const col = document.createElement('div');
                    col.className = 'col-2 mb-3 position-relative gallery-item';
                    const removeBtn = document.createElement('span');
                    removeBtn.innerHTML = '×';
                    removeBtn.className = 'remove-gallery-image';
                    removeBtn.onclick = () => col.remove();
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.className = 'img-thumbnail preview-image w-100';
                    col.appendChild(removeBtn);
                    col.appendChild(img);
                    preview.appendChild(col);
                }
            }
        }

        let removedGallery = [];
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-gallery-image')) {
                const imageId = e.target.dataset.id;
                if (confirm('{{ __("admin.remove_gallery_image_confirmation") }}')) {
                    if (imageId) {
                        removedGallery.push(imageId);
                        document.getElementById('removedGalleryImages').value = removedGallery.join(',');
                    }
                    e.target.closest('.gallery-item').remove();
                }
            }
        });

        $('#product_name').on('keyup', function() {
            let slug = $(this).val().toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
            $('#product_slug').val(slug);
        });

        $('#regenSlug').on('click', function() {
            let slug = $('#product_name').val().toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
            $('#product_slug').val(slug);
        });

        $('#productForm').on('submit', function(e) {
            if ($('#has_variants').val() == '1') {
                if ($('#variantsTableBody tr').length === 0) {
                    e.preventDefault();
                    toastr.error('{{ __("admin.please_generate_at_least_one_variant") }}');
                    return false;
                }

                let valid = true;
                $('.variant-price').each(function() {
                    if (!$(this).val() || parseFloat($(this).val()) <= 0) {
                        valid = false;
                        toastr.error('{{ __("admin.please_enter_valid_price_for_all_variants") }}');
                        return false;
                    }
                });
                if (!valid) {
                    e.preventDefault();
                    return false;
                }
            }
            bindVariantEvents();
        });
    </script>
@endsection

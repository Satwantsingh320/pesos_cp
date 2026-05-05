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
        .variant-card {
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 15px;
            padding: 15px;
            border: 1px solid #e9ecef;
        }
        .variant-header {
            cursor: move;
            background: #e9ecef;
            margin: -15px -15px 15px -15px;
            padding: 10px 15px;
            border-radius: 8px 8px 0 0;
        }
        .remove-variant {
            float: right;
            cursor: pointer;
            color: #dc3545;
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
        }
        .select2-container {
            width: 100% !important;
        }
    </style>
    <!-- Include Select2 CSS -->
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
                            <h4 class="mb-sm-0 font-size-18">{{ __('admin.products') }}</h4>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title mb-4">{{ __('admin.add_product') }}</h4>

                                <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" id="productForm">
                                    @csrf

                                    <!-- Basic Information -->
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('admin.select_category') }}</label>
                                                <select name="category" class="form-control select2 single-select __category" data-route="{{ route('subcategory.service') }}" required>
                                                    <option value="" selected disabled>{{ __('admin.select') }}</option>
                                                    @foreach ($categories as $key => $value)
                                                        <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>
                                                            {{ $value }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('admin.select_subcategory') }}</label>
                                                <select name="subcategory" class="form-control select2 single-select __subcategory" required>
                                                    <option value="" selected disabled>{{ __('admin.select') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('admin.choose_brand') }}</label>
                                                <select name="brand" class="form-control select2 single-select" required>
                                                    <option value="" selected disabled>{{ __('admin.select') }}</option>
                                                    @foreach ($brands as $key => $value)
                                                        <option value="{{ $key }}" {{ old('brand') == $key ? 'selected' : '' }}>
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
                                                <input type="text" name="name" id="product_name" placeholder="{{ __('admin.enter_item_name') }}" class="form-control" required value="{{ old('name') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Slug</label>
                                                <div class="d-flex gap-2">
                                                    <input type="text" id="product_slug" name="slug" class="form-control" required value="{{ old('slug') }}">
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
                                                        <input class="form-check-input" type="radio" name="product_type" id="simple_product" value="simple" checked>
                                                        <label class="form-check-label" for="simple_product">Simple Product (No Variants)</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="product_type" id="variant_product" value="variant">
                                                        <label class="form-check-label" for="variant_product">Variable Product (With Variants)</label>
                                                    </div>
                                                    <input type="hidden" name="has_variants" id="has_variants" value="0">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Simple Product Fields -->
                                    <div id="simple_product_fields">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('admin.sku_number') }}</label>
                                                    <input type="text" name="sku_number" placeholder="{{ __('admin.enter_sku_number') }}" class="form-control" value="{{ old('sku_number') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('admin.barcode_number') }}</label>
                                                    <input type="text" name="barcode_number" placeholder="{{ __('admin.enter_barcode_number') }}" class="form-control" value="{{ old('barcode_number') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('admin.price') }}</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">{{ env('CURRENCY_SYMBOL') }}</span>
                                                        <input type="text" name="price" placeholder="{{ __('admin.enter_price') }}" class="form-control __numeric_decimal" value="{{ old('price') }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('admin.offer_price') }}</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">{{ env('CURRENCY_SYMBOL') }}</span>
                                                        <input type="text" name="offer_price" placeholder="{{ __('admin.enter_offer_price') }}" class="form-control __numeric_decimal" value="{{ old('offer_price') }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('admin.no_of_pieces_available') }}</label>
                                                    <input type="text" name="pieces_available" placeholder="{{ __('admin.no_of_pieces_available') }}" class="form-control __numeric" value="{{ old('pieces_available') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Variant Product Fields -->
                                    <div id="variant_product_fields" style="display: none;">
                                        <!-- Attribute Selection -->
                                        <div class="row mb-4">
                                            <div class="col-md-12">
                                                <h5>Product Attributes</h5>
                                                <div id="attribute_selection">
                                                    @foreach($attributes as $attribute)
                                                        <div class="attribute-group">
                                                            <label class="form-label fw-bold">{{ $attribute->display_name }}</label>
                                                            <select class="form-control attribute-select select2-multiple"
                                                                    data-attribute-id="{{ $attribute->id }}"
                                                                    multiple="multiple">
                                                                @foreach($attribute->values as $value)
                                                                    <option value="{{ $value->id }}"
                                                                            {{ $value->color_code ? 'data-color="'.$value->color_code.'"' : '' }}>
                                                                        {{ $value->value }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <button type="button" class="btn btn-primary mt-2" id="generateVariantsBtn">
                                                    <i class="bx bx-plus"></i> Generate Variants
                                                </button>
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
                                                                <th>Combination</th>
                                                                <th>SKU</th>
                                                                <th>Barcode</th>
                                                                <th>Price</th>
                                                                <th>Offer Price</th>
                                                                <th>Quantity</th>
                                                                <th>Low Stock</th>
                                                                <th>Status</th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="variantsTableBody">
                                                            <!-- Variants will be inserted here -->
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
                                                <h6>@lang('admin.product_visibility') :</h6>
                                                <div class="form-check mb-1">
                                                    <input class="form-check-input" id="is_special_offer" type="checkbox" name="is_special_offer" value="1" {{ old('is_special_offer') ? 'checked' : '' }}>
                                                    <label for="is_special_offer" class="form-check-label">Special Offer</label>
                                                </div>
                                                <div class="form-check mb-1">
                                                    <input class="form-check-input" type="checkbox" name="is_clearance" id="is_clearance" value="1" {{ old('is_clearance') ? 'checked' : '' }}>
                                                    <label for="is_clearance" class="form-check-label">Clearance Item</label>
                                                </div>
                                                <div class="form-check mb-1">
                                                    <input id="is_featured" class="form-check-input" type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                                                    <label for="is_featured" class="form-check-label">Featured Product</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('admin.shipping_fee') }}</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">{{ env('CURRENCY_SYMBOL') }}</span>
                                                    <input type="text" name="shipping_fee" placeholder="{{ __('admin.enter_shipping_fee') }}" class="form-control __numeric_decimal" required value="{{ old('shipping_fee', 0) }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('admin.return_days') }}</label>
                                                <input type="number" name="return_days" placeholder="{{ __('admin.enter_return_days') }}" class="form-control" required value="{{ old('return_days', 0) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('admin.estimated_delivery_time') }}</label>
                                                <div class="input-group">
                                                    <input type="text" name="estimated_delivery_time" placeholder="{{ __('admin.enter_no_of_days') }}" class="form-control __numeric" required value="{{ old('estimated_delivery_time', 0) }}">
                                                    <span class="input-group-text">{{ __('admin.days') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('admin.cover_image') }}</label>
                                                <input type="file" name="cover_image" class="form-control" accept="image/*" onchange="previewCoverImage(this)" required>
                                                <div class="mt-2">
                                                    <img id="coverPreview" class="img-thumbnail d-none" width="150">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('admin.gallery_images') }}</label>
                                                <input type="file" name="gallery_images[]" class="form-control" accept="image/*" onchange="previewGalleryImages(this)" required multiple>
                                                <div id="galleryPreview" class="row mt-2"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('admin.item_description') }}</label>
                                                <textarea class="form-control" id="descriptionEditor" name="description">{{ old('description') }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('admin.type') }}</label>
                                                <select name="type" class="form-select">
                                                    <option value="Nuevo" {{ old('type') == 'Nuevo' ? 'selected' : '' }}>{{ __('admin.New') }}</option>
                                                    <option value="Reacondicionado" {{ old('type') == 'Reacondicionado' ? 'selected' : '' }}>{{ __('admin.Refurbished') }}</option>
                                                    <option value="Usado" {{ old('type') == 'Usado' ? 'selected' : '' }}>{{ __('admin.Used') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('admin.status') }}</label>
                                                <select name="status" class="form-select">
                                                    <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>{{ __('admin.active') }}</option>
                                                    <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>{{ __('admin.inactive') }}</option>
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
    <!-- Include jQuery (if not already included) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include Select2 JS -->
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
                $('#simple_product_fields').show();
                $('#variant_product_fields').hide();
                $('#has_variants').val(0);

                // Make simple product fields required
                $('input[name="sku_number"]').attr('required', true);
                $('input[name="barcode_number"]').attr('required', true);
                $('input[name="price"]').attr('required', true);
                $('input[name="pieces_available"]').attr('required', true);
            } else {
                $('#simple_product_fields').hide();
                $('#variant_product_fields').show();
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

            // Build variants table
            let html = '';
            combinations.forEach((combination, index) => {
                let combinationHtml = '';
                let attributeIds = Object.keys(combination);

                // Get attribute value names
                attributeIds.forEach(attrId => {
                    let valueId = combination[attrId];
                    let valueText = $(`.attribute-select[data-attribute-id="${attrId}"] option[value="${valueId}"]`).text();
                    combinationHtml += `<span class="combination-badge">${valueText}</span>`;
                    html += `<input type="hidden" name="variants[${index}][attributes][${attrId}]" value="${valueId}">`;
                });

                html += `
                    <tr data-combination='${JSON.stringify(combination)}'>
                        <td>${combinationHtml}</td>
                        <td>
                            <input type="text" name="variants[${index}][sku]" class="form-control variant-sku"
                                   value="${generateSku(combination, index)}" required>
                        </td>
                        <td><input type="text" name="variants[${index}][barcode]" class="form-control"></td>
                        <td><input type="text" name="variants[${index}][price]" class="form-control variant-price __numeric_decimal" required></td>
                        <td><input type="text" name="variants[${index}][offer_price]" class="form-control variant-offer-price __numeric_decimal"></td>
                        <td><input type="number" name="variants[${index}][quantity]" class="form-control variant-qty" value="0" required></td>
                        <td><input type="number" name="variants[${index}][low_stock_threshold]" class="form-control" value="5"></td>
                        <td>
                            <select name="variants[${index}][status]" class="form-select">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                         </td>
                        <td><button type="button" class="btn btn-sm btn-danger remove-variant"><i class="bx bx-trash"></i></button></td>
                    </tr>
                `;
            });

            $('#variantsTableBody').html(html);
            bindVariantEvents();
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
                $(this).closest('tr').remove();
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

        // Category change
        $('body').on('change', '.__category', function(e) {
            var value = $(this).val();
            if ($('.__category').length > 0 && value != '') {
                let route = $(this).attr('data-route');
                let title = $(this).attr('data-title') || '-Select-';
                let data = { category_id: value, title: title,   _token: '{{ csrf_token() }}' };

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
                            // Reinitialize select2 for subcategory
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
            const file = input.files[0];
            if (file) {
                preview.src = URL.createObjectURL(file);
                preview.classList.remove('d-none');
            }
        }

        function previewGalleryImages(input) {
            const preview = document.getElementById('galleryPreview');
            preview.innerHTML = '';
            Array.from(input.files).forEach(file => {
                const col = document.createElement('div');
                col.className = 'col-3 mb-2';
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.className = 'img-thumbnail';
                img.style.width = '100%';
                col.appendChild(img);
                preview.appendChild(col);
            });
        }

        // Slug generation
        $('#product_name').on('keyup', function() {
            let slug = $(this).val().toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
            $('#product_slug').val(slug);
        });

        $('#regenSlug').on('click', function() {
            let slug = $('#product_name').val().toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
            $('#product_slug').val(slug);
        });

        // Form submission validation for variants
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
        });
    </script>
@endsection

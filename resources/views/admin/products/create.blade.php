@extends('layouts.master')

@section('css')
    <!-- Include Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <style>
        /* Attribute Selection Styles */
        .attribute-card {
            transition: all 0.3s ease;
            border: 2px solid #e9ecef;
            cursor: pointer;
            background: #fff;
            margin-bottom: 15px;
        }

        .attribute-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-color: #0d6efd;
        }

        .attribute-card.active {
            border-color: #0d6efd;
            background: linear-gradient(135deg, #f8f9ff 0%, #fff 100%);
            box-shadow: 0 3px 10px rgba(13,110,253,0.1);
        }

        .attribute-card .card-body {
            padding: 15px;
        }

        .attribute-checkbox {
            width: 20px;
            height: 20px;
            cursor: pointer;
            margin-right: 8px;
        }

        .attribute-name {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .attribute-count {
            font-size: 11px;
            color: #6c757d;
        }

        /* Dynamic Attribute Container */
        .dynamic-attribute-container {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid #0d6efd;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .attribute-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #dee2e6;
        }

        .attribute-title {
            font-size: 16px;
            font-weight: 600;
            color: #0d6efd;
            margin: 0;
        }

        .remove-attribute-btn {
            padding: 4px 12px;
            font-size: 12px;
        }

        .value-checkbox-group {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }

        .value-checkbox {
            display: flex;
            align-items: center;
            padding: 8px 12px;
            background: #fff;
            border-radius: 8px;
            transition: all 0.2s ease;
            cursor: pointer;
            border: 1px solid #e9ecef;
        }

        .value-checkbox:hover {
            background: #f8f9fa;
            border-color: #0d6efd;
        }

        .value-checkbox input {
            margin-right: 10px;
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .value-checkbox label {
            cursor: pointer;
            margin: 0;
            font-size: 14px;
            color: #495057;
        }

        .color-preview {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 10px;
            border: 1px solid #dee2e6;
        }

        /* Selected Attributes Summary */
        .selected-summary {
            background: #e7f1ff;
            border-radius: 10px;
            padding: 12px 15px;
            margin-bottom: 20px;
        }

        .selected-badge {
            display: inline-flex;
            align-items: center;
            background: #0d6efd;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 13px;
            margin: 0 8px 8px 0;
        }

        .selected-badge .remove-badge {
            margin-left: 8px;
            cursor: pointer;
            font-weight: bold;
            font-size: 16px;
        }

        .selected-badge .remove-badge:hover {
            color: #ff4444;
        }

        /* Variant Table Styles */
        .variant-table th {
            background: #f8f9fa;
            font-weight: 600;
        }

        .combination-badge {
            display: inline-block;
            background: #6c757d;
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            margin: 2px;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px;
            background: #f8f9fa;
            border-radius: 10px;
            color: #6c757d;
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
                            <a href="{{ route('products.index') }}" class="btn btn-dark btn-sm mx-2">
                                <i class="bx bx-arrow-back"></i> {{ __('admin.back') }}
                            </a>
                            <h4 class="mb-sm-0 font-size-18">{{ __('admin.add_product') }}</h4>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title mb-4">{{ __('admin.product_information') }}</h4>

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
                                                <label class="form-label">{{ __('admin.slug') }}</label>
                                                <div class="d-flex gap-2">
                                                    <input type="text" id="product_slug" name="slug" class="form-control" required value="{{ old('slug') }}">
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
                                                        <input class="form-check-input" type="radio" name="product_type" id="simple_product" value="simple" checked>
                                                        <label class="form-check-label" for="simple_product">{{ __('admin.simple_product_no_variants') }}</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="product_type" id="variant_product" value="variant">
                                                        <label class="form-check-label" for="variant_product">{{ __('admin.variable_product_with_variants') }}</label>
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
                                            <div class="col-md-4 d-none">
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
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('admin.low_stock') }}</label>
                                                    <input type="text" name="low_stock_threshold" placeholder="{{ __('admin.low_stock_threshold') }}" class="form-control __numeric" value="10">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Variant Product Fields -->
                                    <div id="variant_product_fields" style="display: none;">
                                        <!-- Modern Attribute Selection -->
                                        <div class="row mb-4">
                                            <div class="col-md-12">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <h5 class="mb-0">{{ __('admin.product_attributes') }}</h5>
                                                    <small class="text-muted">{{ __('admin.select_attributes_for_variants') }}</small>
                                                </div>

                                                <!-- Attribute Cards Grid -->
                                                <div class="row" id="attributeCardsContainer">
                                                    @foreach($attributes as $attribute)
                                                        <div class="col-md-3">
                                                            <div class="card attribute-card" data-attribute-id="{{ $attribute->id }}">
                                                                <div class="card-body text-center">
                                                                    <div class="form-check">
                                                                        <input class="form-check-input attribute-checkbox"
                                                                               type="checkbox"
                                                                               id="attr_{{ $attribute->id }}"
                                                                               data-attribute-id="{{ $attribute->id }}"
                                                                               data-attribute-name="{{ $attribute->display_name }}">
                                                                        <label class="form-check-label" for="attr_{{ $attribute->id }}">
                                                                            <div class="attribute-name">{{ $attribute->display_name }}</div>
                                                                            <div class="attribute-count">{{ $attribute->values->count() }} {{ __('admin.options') }}</div>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                <!-- Dynamic Attributes Container -->
                                                <div id="dynamicAttributesContainer" class="mt-4"></div>

                                                <!-- Selected Attributes Summary -->
                                                <div id="selectedSummaryContainer"></div>

                                                <!-- Generate Button -->
                                                <div class="text-center mt-3" id="generateBtnContainer" style="display: none;">
                                                    <button type="button" class="btn btn-primary btn-lg" id="generateVariantsBtn">
                                                        <i class="bx bx-plus-circle"></i> {{ __('admin.generate_variants') }}
                                                    </button>
                                                </div>

                                                <!-- Hidden Selects for Form Submission -->
                                                <div id="hiddenSelectsContainer" style="display: none;">
                                                    @foreach($attributes as $attribute)
                                                        <select class="attribute-select-hidden" data-attribute-id="{{ $attribute->id }}" multiple="multiple" name="temp_attributes[{{ $attribute->id }}][]"></select>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Variants Table -->
                                        <div class="row mt-4">
                                            <div class="col-md-12">
                                                <h5>{{ __('admin.product_variants') }}</h5>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered variant-table" id="variantsTable">
                                                        <thead>
                                                            <tr>
                                                                <th width="15%">{{ __('admin.combination') }}</th>
                                                                <th width="10%">{{ __('admin.image') }}</th>
                                                                <th width="15%">{{ __('admin.sku') }}</th>
                                                                <th width="12%">{{ __('admin.barcode') }}</th>
                                                                <th width="10%">{{ __('admin.price') }}</th>
                                                                <th width="8%">{{ __('admin.quantity') }}</th>
                                                                <th width="10%">{{ __('admin.low_stock') }}</th>
                                                                <th width="10%">{{ __('admin.status') }}</th>
                                                                <th width="5%">{{ __('admin.actions') }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="variantsTableBody">
                                                            <tr class="empty-state-row">
                                                                <td colspan="9">
                                                                    <div class="empty-state">
                                                                        <i class="bx bx-cube" style="font-size: 48px;"></i>
                                                                        <p class="mt-2 mb-0">{{ __('admin.no_variants_generated') }}</p>
                                                                        <small>{{ __('admin.select_attributes_and_generate_variants') }}</small>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Rest of the form (Common Fields) continues... -->
                                    <div class="row mt-4">
                                        <div class="col-md-6">
                                            <div class="product-visibility">
                                                <h6>{{ __('admin.product_visibility') }}:</h6>
                                                <div class="form-check mb-1">
                                                    <input class="form-check-input" id="is_special_offer" type="checkbox" name="is_special_offer" value="1" {{ old('is_special_offer') ? 'checked' : '' }}>
                                                    <label for="is_special_offer" class="form-check-label">{{ __('admin.special_offer') }}</label>
                                                </div>
                                                <div class="form-check mb-1">
                                                    <input class="form-check-input" type="checkbox" name="is_clearance" id="is_clearance" value="1" {{ old('is_clearance') ? 'checked' : '' }}>
                                                    <label for="is_clearance" class="form-check-label">{{ __('admin.clearance_item') }}</label>
                                                </div>
                                                <div class="form-check mb-1">
                                                    <input id="is_featured" class="form-check-input" type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                                                    <label for="is_featured" class="form-check-label">{{ __('admin.featured_product') }}</label>
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
                                                    <option value="Nuevo" {{ old('type') == 'Nuevo' ? 'selected' : '' }}>{{ __('admin.new') }}</option>
                                                    <option value="Reacondicionado" {{ old('type') == 'Reacondicionado' ? 'selected' : '' }}>{{ __('admin.refurbished') }}</option>
                                                    <option value="Usado" {{ old('type') == 'Usado' ? 'selected' : '' }}>{{ __('admin.used') }}</option>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/40.2.0/classic/ckeditor.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        // Initialize toastr
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "5000"
        };

        // Global variables
        let selectedAttributes = {};

        $(document).ready(function() {
            // Initialize Select2
            $('.single-select').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: '{{ __("admin.select_an_option") }}',
                allowClear: true
            });

            // Numeric validation
            $('.__numeric_decimal').on('keypress', function(e) {
                var charCode = (e.which) ? e.which : e.keyCode;
                if (charCode == 46) {
                    if ($(this).val().indexOf('.') !== -1) {
                        return false;
                    }
                    return true;
                }
                if (charCode < 48 || charCode > 57) {
                    return false;
                }
                return true;
            });

            $('.__numeric').on('keypress', function(e) {
                var charCode = (e.which) ? e.which : e.keyCode;
                if (charCode < 48 || charCode > 57) {
                    return false;
                }
                return true;
            });

            // Attribute card click handler
            $('.attribute-card').on('click', function(e) {
                if ($(e.target).is('input') || $(e.target).is('label')) {
                    return;
                }
                const checkbox = $(this).find('.attribute-checkbox');
                checkbox.prop('checked', !checkbox.prop('checked'));
                checkbox.trigger('change');
            });

            // Attribute checkbox change handler
            $('.attribute-checkbox').on('change', function() {
                const attributeId = $(this).data('attribute-id');
                const attributeName = $(this).data('attribute-name');
                const card = $(this).closest('.attribute-card');

                if ($(this).is(':checked')) {
                    if (!selectedAttributes[attributeId]) {
                        selectedAttributes[attributeId] = {
                            id: attributeId,
                            name: attributeName,
                            values: []
                        };
                    }
                    card.addClass('active');
                    renderAttributeSelector(attributeId, attributeName);
                } else {
                    delete selectedAttributes[attributeId];
                    card.removeClass('active');
                    $(`#attributeSelector_${attributeId}`).remove();
                }

                updateUI();
            });
        });

        // Render attribute selector with values
        function renderAttributeSelector(attributeId, attributeName) {
            // Get attribute values from the original data
            const originalSelect = $(`.attribute-select-hidden[data-attribute-id="${attributeId}"]`);
            let valuesHtml = '';

            // You'll need to pass attribute values from backend or fetch via AJAX
            // For now, let's assume you have the data in a global variable
            @foreach($attributes as $attribute)
                if ({{ $attribute->id }} == attributeId) {
                    const values = @json($attribute->values);
                    values.forEach(value => {
                        if (value.color_code) {
                            valuesHtml += `
                                <div class="value-checkbox">
                                    <input type="checkbox" class="attribute-value-checkbox"
                                           id="value_${attributeId}_${value.id}"
                                           value="${value.id}"
                                           data-attribute-id="${attributeId}"
                                           data-value-name="${value.value}">
                                    <div class="color-preview" style="background: ${value.color_code}"></div>
                                    <label for="value_${attributeId}_${value.id}">${value.value}</label>
                                </div>
                            `;
                        } else {
                            valuesHtml += `
                                <div class="value-checkbox">
                                    <input type="checkbox" class="attribute-value-checkbox"
                                           id="value_${attributeId}_${value.id}"
                                           value="${value.id}"
                                           data-attribute-id="${attributeId}"
                                           data-value-name="${value.value}">
                                    <label for="value_${attributeId}_${value.id}">${value.value}</label>
                                </div>
                            `;
                        }
                    });
                }
            @endforeach

            const selectorHtml = `
                <div id="attributeSelector_${attributeId}" class="dynamic-attribute-container">
                    <div class="attribute-header">
                        <h6 class="attribute-title">
                            <i class="bx bx-category"></i> ${attributeName}
                        </h6>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-attribute-btn" data-attribute-id="${attributeId}">
                            <i class="bx bx-trash"></i> Remove
                        </button>
                    </div>
                    <div class="value-checkbox-group" id="valuesContainer_${attributeId}">
                        ${valuesHtml}
                    </div>
                </div>
            `;

            $(`#attributeSelector_${attributeId}`).remove();
            $('#dynamicAttributesContainer').append(selectorHtml);

            // Bind value change events
            $(`#attributeSelector_${attributeId} .attribute-value-checkbox`).on('change', function() {
                const attrId = $(this).data('attribute-id');
                const valueId = $(this).val();

                if ($(this).is(':checked')) {
                    if (!selectedAttributes[attrId].values.includes(valueId)) {
                        selectedAttributes[attrId].values.push(valueId);
                    }
                } else {
                    const index = selectedAttributes[attrId].values.indexOf(valueId);
                    if (index > -1) {
                        selectedAttributes[attrId].values.splice(index, 1);
                    }
                }
                updateUI();
            });

            // Bind remove button
            $(`#attributeSelector_${attributeId} .remove-attribute-btn`).on('click', function() {
                const attrId = $(this).data('attribute-id');
                $(`#attr_${attrId}`).prop('checked', false).trigger('change');
            });
        }

        // Update UI based on selected attributes
        function updateUI() {
            // Update summary
            let summaryHtml = '';
            let hasValues = false;

            for (let attrId in selectedAttributes) {
                const attr = selectedAttributes[attrId];
                if (attr.values.length > 0) {
                    hasValues = true;
                    summaryHtml += `
                        <span class="selected-badge">
                            ${attr.name}: ${attr.values.length} selected
                            <span class="remove-badge" data-attribute-id="${attrId}">×</span>
                        </span>
                    `;
                }
            }

            if (summaryHtml) {
                $('#selectedSummaryContainer').html(`
                    <div class="selected-summary">
                        <strong><i class="bx bx-check-circle"></i> Selected Attributes:</strong>
                        <div class="mt-2">${summaryHtml}</div>
                    </div>
                `);

                $('.remove-badge').on('click', function() {
                    const attrId = $(this).data('attribute-id');
                    $(`#attr_${attrId}`).prop('checked', false).trigger('change');
                });
            } else {
                $('#selectedSummaryContainer').empty();
            }

            // Show/hide generate button
            if (hasValues) {
                $('#generateBtnContainer').show();
            } else {
                $('#generateBtnContainer').hide();
            }
        }

        // Generate SKU
        function generateSku(combination, index) {
            let skuBase = $('#product_name').val().replace(/\s+/g, '-').substring(0, 20);
            let combinationStr = Object.values(combination).join('-');
            return (skuBase + '-' + combinationStr + '-' + index).toUpperCase();
        }

        // Generate combinations
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

        // Preview variant image
        window.previewVariantImage = function(input, index) {
            const preview = document.getElementById(`variantImagePreview_${index}`);
            if (preview) {
                if (preview.src && preview.src.startsWith('blob:')) {
                    URL.revokeObjectURL(preview.src);
                }
                const file = input.files[0];
                if (file) {
                    preview.src = URL.createObjectURL(file);
                    preview.classList.remove('d-none');
                }
            }
        };

        // Bind variant events
        function bindVariantEvents() {
            $('.remove-variant').off('click').on('click', function() {
                $(this).closest('tr').remove();
                if ($('#variantsTableBody tr').length === 0 || $('#variantsTableBody tr').hasClass('empty-state-row')) {
                    $('#variantsTableBody').html(`
                        <tr class="empty-state-row">
                            <td colspan="9">
                                <div class="empty-state">
                                    <i class="bx bx-cube" style="font-size: 48px;"></i>
                                    <p class="mt-2 mb-0">{{ __('admin.no_variants_generated') }}</p>
                                </div>
                            </td>
                        </tr>
                    `);
                }
            });

            $('.variant-price').off('change').on('change', function() {
                let price = parseFloat($(this).val()) || 0;
                if (price <= 0) {
                    toastr.warning('{{ __("admin.please_enter_valid_price") }}');
                }
            });
        }

        // Generate variants button click
        $('#generateVariantsBtn').on('click', function() {
            let attributesForCombination = {};

            for (let attrId in selectedAttributes) {
                if (selectedAttributes[attrId].values.length > 0) {
                    attributesForCombination[attrId] = selectedAttributes[attrId].values;
                }
            }

            if (Object.keys(attributesForCombination).length === 0) {
                toastr.warning('{{ __("admin.please_select_at_least_one_attribute_value") }}');
                return;
            }

            let combinations = generateCombinations(attributesForCombination);

            if (combinations.length === 0) {
                toastr.warning('No combinations generated');
                return;
            }

            if (combinations.length > 100) {
                if (!confirm(`You are about to generate ${combinations.length} variants. This may take a moment. Continue?`)) {
                    return;
                }
            }

            // Build variants table
            let html = '';
            combinations.forEach((combination, index) => {
                let combinationHtml = '';
                let attributeIds = Object.keys(combination);
                let attributesHtml = '';

                attributeIds.forEach(attrId => {
                    let valueId = combination[attrId];
                    let attrName = selectedAttributes[attrId].name;
                    let valueText = '';

                    // Get value text from the checkbox label
                    $(`#valuesContainer_${attrId} .attribute-value-checkbox`).each(function() {
                        if ($(this).val() == valueId) {
                            valueText = $(this).data('value-name');
                        }
                    });

                    combinationHtml += `<span class="combination-badge">${attrName}: ${valueText}</span>`;
                    attributesHtml += `<input type="hidden" name="variants[${index}][attributes][${attrId}]" value="${valueId}">`;
                });

                html += `
                    <tr>
                        <td>${combinationHtml}</td>
                        <td>
                            <input type="file" name="variants[${index}][image]" class="form-control variant-image-input" accept="image/*" onchange="previewVariantImage(this, ${index})">
                            <div class="mt-2">
                                <img id="variantImagePreview_${index}" class="img-thumbnail d-none" width="50">
                            </div>
                            <input type="hidden" name="variants[${index}][existing_image]" value="">
                         </div>
                        <td>
                            <input type="text" name="variants[${index}][sku]" class="form-control variant-sku" value="${generateSku(combination, index)}" required>
                            ${attributesHtml}
                         </div>
                        <td><input type="text" name="variants[${index}][barcode]" class="form-control"></div>
                        <td><input type="text" name="variants[${index}][price]" class="form-control variant-price __numeric_decimal" required></div>
                        <td><input type="number" name="variants[${index}][quantity]" class="form-control variant-qty" value="0" required></div>
                        <td><input type="number" name="variants[${index}][low_stock_threshold]" class="form-control" value="5"></div>
                        <td>
                            <select name="variants[${index}][status]" class="form-select">
                                <option value="1">{{ __("admin.active") }}</option>
                                <option value="0">{{ __("admin.inactive") }}</option>
                            </select>
                         </div>
                        <td><button type="button" class="btn btn-sm btn-danger remove-variant"><i class="bx bx-trash"></i></button></div>
                    </div>
                `;
            });

            $('#variantsTableBody').html(html);
            bindVariantEvents();
            toastr.success(combinations.length + ' {{ __("admin.variants_generated_successfully") }}');
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

        // Category change
        $('body').on('change', '.__category', function(e) {
            var value = $(this).val();
            if ($('.__category').length > 0 && value != '') {
                let route = $(this).attr('data-route');
                let data = { category_id: value, _token: '{{ csrf_token() }}' };

                $.ajax({
                    url: route,
                    type: 'POST',
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            if ($('.__subcategory').data('select2')) {
                                $('.__subcategory').select2('destroy');
                            }
                            $('.__subcategory').html(response.options);
                            $('.__subcategory').select2({
                                theme: 'bootstrap-5',
                                width: '100%',
                                placeholder: '{{ __("admin.select_an_option") }}',
                                allowClear: true
                            });
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Error loading subcategories');
                    }
                });
            }
        });

        // Preview functions
        window.previewCoverImage = function(input) {
            const preview = document.getElementById('coverPreview');
            const existingCover = document.querySelector('.existing-cover');
            if (existingCover) existingCover.style.display = 'none';
            if (preview && preview.src && preview.src.startsWith('blob:')) {
                URL.revokeObjectURL(preview.src);
            }
            const file = input.files[0];
            if (file) {
                preview.src = URL.createObjectURL(file);
                preview.classList.remove('d-none');
            }
        };

        window.previewGalleryImages = function(input) {
            const preview = document.getElementById('galleryPreview');
            const oldImages = preview.querySelectorAll('img');
            oldImages.forEach(img => {
                if (img.src && img.src.startsWith('blob:')) {
                    URL.revokeObjectURL(img.src);
                }
            });

            preview.innerHTML = '';
            Array.from(input.files).forEach((file, index) => {
                const col = document.createElement('div');
                col.className = 'col-3 mb-2';
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.className = 'img-thumbnail';
                img.style.width = '100%';
                img.style.height = '100px';
                img.style.objectFit = 'cover';
                col.appendChild(img);
                preview.appendChild(col);
            });
        };

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
                let hasVariants = $('#variantsTableBody tr').length > 0 && !$('#variantsTableBody tr').hasClass('empty-state-row');
                if (!hasVariants) {
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

            let submitBtn = $(this).find('button[type="submit"]');
            submitBtn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin"></i> {{ __("admin.processing") }}');
        });

        // Cleanup
        window.addEventListener('beforeunload', function() {
            document.querySelectorAll('img[src^="blob:"]').forEach(img => {
                URL.revokeObjectURL(img.src);
            });
        });
    </script>
@endsection

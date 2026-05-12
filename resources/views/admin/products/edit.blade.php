@extends('layouts.master')

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
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
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-color: #0d6efd;
        }

        .attribute-card.active {
            border-color: #0d6efd;
            background: linear-gradient(135deg, #f8f9ff 0%, #fff 100%);
            box-shadow: 0 3px 10px rgba(13, 110, 253, 0.1);
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
            padding: 4px 10px;
            border-radius: 20px;
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

        .empty-state {
            text-align: center;
            padding: 40px;
            background: #f8f9fa;
            border-radius: 10px;
            color: #6c757d;
        }

        .variant-table th {
            background: #f8f9fa;
            font-weight: 600;
        }

        .simple-product-fields {
            animation: fadeIn 0.5s ease;
        }

        .variant-product-fields {
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Fix for footer positioning */
        .main-content {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .page-content {
            flex: 1;
        }

        .card {
            margin-bottom: 1.5rem;
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

                                <form method="POST" action="{{ route('products.update', $product->id) }}"
                                    enctype="multipart/form-data" id="productForm">
                                    @method('put')
                                    @csrf

                                    <!-- Basic Information -->
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('admin.select_category') }}</label>
                                                <select name="category"
                                                    class="form-control select2 single-select __category"
                                                    data-route="{{ route('subcategory.service') }}" required>
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
                                                <select name="subcategory"
                                                    class="form-control select2 single-select __subcategory"
                                                    data-selected="{{ $product->subcategory_id }}" required>
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
                                                <input type="text" id="product_name" name="name"
                                                    placeholder="{{ __('admin.enter_item_name') }}" class="form-control"
                                                    required value="{{ $product->name }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('admin.slug') }}</label>
                                                <div class="d-flex gap-2">
                                                    <input type="text" id="product_slug" name="slug" class="form-control"
                                                        required value="{{ $product->slug }}">
                                                    <button type="button" id="regenSlug"
                                                        class="btn btn-outline-secondary">{{ __('admin.generate') }}</button>
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
                                                        <input class="form-check-input" type="radio" name="product_type"
                                                            id="simple_product" value="simple" {{ $product->has_variants == 0 ? 'checked' : '' }}>
                                                        <label class="form-check-label"
                                                            for="simple_product">{{ __('admin.simple_product_no_variants') }}</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="product_type"
                                                            id="variant_product" value="variant" {{ $product->has_variants == 1 ? 'checked' : '' }}>
                                                        <label class="form-check-label"
                                                            for="variant_product">{{ __('admin.variable_product_with_variants') }}</label>
                                                    </div>
                                                    <input type="hidden" name="has_variants" id="has_variants"
                                                        value="{{ $product->has_variants == 1 ? 1 : 0 }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Simple Product Fields -->
                                    <div id="simple_product_fields" class="simple-product-fields"
                                        style="{{ $product->has_variants == 1 ? 'display: none;' : '' }}">
                                        <div class="card border-primary mb-4">
                                            <div class="card-header bg-primary text-white">
                                                <h5 class="mb-0">{{ __('admin.simple_product_information') }}</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">{{ __('admin.sku_number') }}</label>
                                                            <input type="text" name="sku_number"
                                                                placeholder="{{ __('admin.enter_sku_number') }}"
                                                                class="form-control" value="{{ $product->sku_number }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label
                                                                class="form-label">{{ __('admin.barcode_number') }}</label>
                                                            <input type="text" name="barcode_number"
                                                                placeholder="{{ __('admin.enter_barcode_number') }}"
                                                                class="form-control" value="{{ $product->barcode_number }}">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label class="form-label">{{ __('admin.price') }}</label>
                                                            <div class="input-group">
                                                                <span
                                                                    class="input-group-text">{{ env('CURRENCY_SYMBOL') }}</span>
                                                                <input type="text" name="price"
                                                                    placeholder="{{ __('admin.enter_price') }}"
                                                                    class="form-control __numeric_decimal"
                                                                    value="{{ $product->price }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label class="form-label">{{ __('admin.offer_price') }}</label>
                                                            <div class="input-group">
                                                                <span
                                                                    class="input-group-text">{{ env('CURRENCY_SYMBOL') }}</span>
                                                                <input type="text" name="offer_price"
                                                                    placeholder="{{ __('admin.enter_offer_price') }}"
                                                                    class="form-control __numeric_decimal"
                                                                    value="{{ $product->offer_price != $product->price ? $product->offer_price : '' }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label
                                                                class="form-label">{{ __('admin.no_of_pieces_available') }}</label>
                                                            <input type="text" name="pieces_available"
                                                                placeholder="{{ __('admin.no_of_pieces_available') }}"
                                                                class="form-control __numeric"
                                                                value="{{ $product->quantity }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label class="form-label">{{ __('admin.low_stock') }}</label>
                                                            <input type="text" name="low_stock_threshold"
                                                                placeholder="{{ __('admin.low_stock_threshold') }}"
                                                                class="form-control __numeric"
                                                                value="{{ $product->low_stock_threshold }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Variant Product Fields -->
                                    <div id="variant_product_fields" class="variant-product-fields"
                                        style="{{ $product->has_variants == 1 ? '' : 'display: none;' }}">
                                        <!-- Modern Attribute Selection -->
                                        <div class="card border-success mb-4">
                                            <div class="card-header bg-success text-white">
                                                <h5 class="mb-0">{{ __('admin.product_attributes') }}</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <small
                                                        class="text-muted">{{ __('admin.select_attributes_for_variants') }}</small>
                                                </div>

                                                <!-- Attribute Cards Grid -->
                                                <div class="row" id="attributeCardsContainer">
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
                                                        <div class="col-md-3">
                                                            <div class="card attribute-card"
                                                                data-attribute-id="{{ $attribute->id }}">
                                                                <div class="card-body text-center">
                                                                    <div class="form-check">
                                                                        <input class="form-check-input attribute-checkbox"
                                                                            type="checkbox" id="attr_{{ $attribute->id }}"
                                                                            data-attribute-id="{{ $attribute->id }}"
                                                                            data-attribute-name="{{ $attribute->display_name }}"
                                                                            {{ count($existingAttributeValues) > 0 ? 'checked' : '' }}>
                                                                        <label class="form-check-label"
                                                                            for="attr_{{ $attribute->id }}">
                                                                            <div class="attribute-name">
                                                                                {{ $attribute->display_name }}
                                                                            </div>
                                                                            <div class="attribute-count">
                                                                                {{ $attribute->values->count() }}
                                                                                {{ __('admin.options') }}
                                                                            </div>
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

                                                <!-- Generate Variants Button - Moved here -->
                                                <div class="text-center mt-4" id="generateBtnContainer"
                                                    style="display: {{ ($product->has_variants && $product->variants->count() > 0) ? 'block' : 'none' }};">
                                                    <button type="button" class="btn btn-primary btn-lg"
                                                        id="generateVariantsBtn">
                                                        <i class="bx bx-plus-circle"></i>
                                                        {{ __('admin.generate_variants') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Variants Table -->
                                        <div class="card border-info mb-4">
                                            <div class="card-header bg-info text-white">
                                                <h5 class="mb-0">{{ __('admin.product_variants') }}</h5>
                                            </div>
                                            <div class="card-body">
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
                                                                                    <img src="{{ $variantImage }}"
                                                                                        class="variant-image-preview"
                                                                                        alt="Variant Image">
                                                                                    <span class="remove-variant-image"
                                                                                        data-variant-id="{{ $variant->id }}"
                                                                                        data-image="{{ $variant->image }}">×</span>
                                                                                    <input type="hidden"
                                                                                        name="variants[{{ $index }}][existing_image]"
                                                                                        value="{{ $variant->image }}">
                                                                                @endif
                                                                                <input type="file"
                                                                                    name="variants[{{ $index }}][image]"
                                                                                    class="form-control mt-1 variant-image-input"
                                                                                    accept="image/*" style="font-size: 12px;">
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <input type="hidden" name="variants[{{ $index }}][id]"
                                                                                value="{{ $variant->id }}">
                                                                            <input type="text" name="variants[{{ $index }}][sku]"
                                                                                class="form-control variant-sku"
                                                                                value="{{ $variant->sku }}" required>
                                                                            @foreach($combinationArray as $attrId => $valueId)
                                                                                <input type="hidden"
                                                                                    name="variants[{{ $index }}][attributes][{{ $attrId }}]"
                                                                                    value="{{ $valueId }}">
                                                                            @endforeach
                                                                        </td>
                                                                        <td><input type="text"
                                                                                name="variants[{{ $index }}][barcode]"
                                                                                class="form-control"
                                                                                value="{{ $variant->barcode }}">
                                                                        </td>
                                                                        <td><input type="text" name="variants[{{ $index }}][price]"
                                                                                class="form-control variant-price __numeric_decimal"
                                                                                value="{{ $variant->price }}" required>
                                                                        </td>
                                                                        <td><input type="number"
                                                                                name="variants[{{ $index }}][quantity]"
                                                                                class="form-control variant-qty"
                                                                                value="{{ $variant->quantity }}" required>
                                                                        </td>
                                                                        <td><input type="number"
                                                                                name="variants[{{ $index }}][low_stock_threshold]"
                                                                                class="form-control"
                                                                                value="{{ $variant->low_stock_threshold ?? 5 }}">
                                                                        </td>
                                                                        <td>
                                                                            <select name="variants[{{ $index }}][status]"
                                                                                class="form-select">
                                                                                <option value="1" {{ $variant->status == 1 ? 'selected' : '' }}>{{ __('admin.active') }}
                                                                                </option>
                                                                                <option value="0" {{ $variant->status == 0 ? 'selected' : '' }}>{{ __('admin.inactive') }}
                                                                                </option>
                                                                            </select>
                                                                        </td>
                                                                        <td><button type="button"
                                                                                class="btn btn-sm btn-danger remove-variant"><i
                                                                                    class="bx bx-trash"></i></button>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            @else
                                                                <tr class="empty-state-row">
                                                                    <td colspan="9">
                                                                        <div class="empty-state">
                                                                            <i class="bx bx-cube" style="font-size: 48px;"></i>
                                                                            <p class="mt-2 mb-0">
                                                                                {{ __('admin.no_variants_generated') }}
                                                                            </p>
                                                                            <small>{{ __('admin.select_attributes_and_generate_variants') }}</small>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Common Fields -->
                                    <div class="card border-secondary mb-4">
                                        <div class="card-header bg-secondary text-white">
                                            <h5 class="mb-0">{{ __('admin.additional_information') }}</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="product-visibility">
                                                        <h6>{{ __('admin.product_visibility') }}:</h6>
                                                        <div class="form-check mb-1">
                                                            <input class="form-check-input" id="is_special_offer"
                                                                type="checkbox" name="is_special_offer" value="1" {{ $product->is_special_offer == 1 ? 'checked' : '' }}>
                                                            <label for="is_special_offer"
                                                                class="form-check-label">{{ __('admin.special_offer') }}</label>
                                                        </div>
                                                        <div class="form-check mb-1">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="is_clearance" id="is_clearance" value="1" {{ $product->is_clearance == 1 ? 'checked' : '' }}>
                                                            <label for="is_clearance"
                                                                class="form-check-label">{{ __('admin.clearance_item') }}</label>
                                                        </div>
                                                        <div class="form-check mb-1">
                                                            <input id="is_featured" class="form-check-input" type="checkbox"
                                                                name="is_featured" value="1" {{ $product->is_featured == 1 ? 'checked' : '' }}>
                                                            <label for="is_featured"
                                                                class="form-check-label">{{ __('admin.featured_product') }}</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">{{ __('admin.shipping_fee') }}</label>
                                                        <div class="input-group">
                                                            <span
                                                                class="input-group-text">{{ env('CURRENCY_SYMBOL') }}</span>
                                                            <input type="text" name="shipping_fee"
                                                                placeholder="{{ __('admin.enter_shipping_fee') }}"
                                                                class="form-control __numeric_decimal" required
                                                                value="{{ $product->shipping_fee }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">{{ __('admin.return_days') }}</label>
                                                        <input type="number" name="return_days"
                                                            placeholder="{{ __('admin.enter_return_days') }}"
                                                            class="form-control" required
                                                            value="{{ $product->return_days }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label
                                                            class="form-label">{{ __('admin.estimated_delivery_time') }}</label>
                                                        <div class="input-group">
                                                            <input type="text" name="estimated_delivery_time"
                                                                placeholder="{{ __('admin.enter_no_of_days') }}"
                                                                class="form-control __numeric" required
                                                                value="{{ $product->estimated_delivery_time }}">
                                                            <span class="input-group-text">{{ __('admin.days') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">{{ __('admin.cover_image') }}</label>
                                                        <input type="file" name="cover_image" class="form-control"
                                                            accept="image/*" onchange="previewCoverImage(this)">
                                                        <div class="mt-2">
                                                            <img id="coverPreview" class="img-thumbnail d-none" width="150">
                                                        </div>
                                                        @if ($product->cover_image)
                                                            <img src="{{ asset(PRODUCTS_PATH . $product->cover_image) }}"
                                                                class="img-thumbnail existing-cover mt-2" height="150"
                                                                width="150">
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">{{ __('admin.gallery_images') }}</label>
                                                        <input type="file" name="gallery_images[]" class="form-control"
                                                            accept="image/*" onchange="previewGalleryImages(this)" multiple>
                                                        <input type="hidden" name="removed_gallery_images"
                                                            id="removedGalleryImages">
                                                        <div id="galleryPreview" class="row mt-2">
                                                            @if (!empty($product->gallery))
                                                                @foreach ($product->gallery as $key => $val)
                                                                    <div
                                                                        class="col-md-2 col-sm-3 col-6 mb-3 gallery-item position-relative">
                                                                        <span class="remove-gallery-image"
                                                                            data-id="{{ $val->id }}">×</span>
                                                                        <img src="{{ asset(PRODUCTS_PATH . $val->image) }}"
                                                                            class="img-thumbnail preview-image mb-2"
                                                                            style="height: 100px; width: 100%; object-fit: cover;">
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
                                                        <textarea class="form-control" id="descriptionEditor"
                                                            name="description">{{ $product->description }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label class="form-label">{{ __('admin.type') }}</label>
                                                        <select name="type" class="form-select">
                                                            <option value="Nuevo" {{ $product->type == 'Nuevo' ? 'selected' : '' }}>
                                                                {{ __('admin.new') }}
                                                            </option>
                                                            <option value="Reacondicionado" {{ $product->type == 'Reacondicionado' ? 'selected' : '' }}>
                                                                {{ __('admin.refurbished') }}
                                                            </option>
                                                            <option value="Usado" {{ $product->type == 'Usado' ? 'selected' : '' }}>
                                                                {{ __('admin.used') }}
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label class="form-label">{{ __('admin.status') }}</label>
                                                        <select name="status" class="form-select">
                                                            <option value="1" {{ $product->status == '1' ? 'selected' : '' }}>
                                                                {{ __('admin.active') }}
                                                            </option>
                                                            <option value="0" {{ $product->status == '0' ? 'selected' : '' }}>
                                                                {{ __('admin.inactive') }}
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row d-flex justify-content-center my-5">
                                        <div class="col-sm-2">
                                            <button type="submit" id="submitBtn"
                                                class="btn btn-primary w-md btn-lg">{{ __('admin.update_product') }}</button>
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
        let attributeValuesData = {};
        let existingVariantsData = [];

        // Store attribute values from backend
        @foreach($attributes as $attribute)
            attributeValuesData[{{ $attribute->id }}] = {
                @foreach($attribute->values as $value)
                            {{ $value->id }}: {
                        id: {{ $value->id }},
                        value: "{{ $value->value }}",
                        color_code: "{{ $value->color_code }}"
                    },
                @endforeach
                };
        @endforeach

        // Store existing variants data from backend
        @if($product->has_variants && $product->variants->count() > 0)
            existingVariantsData = {!! json_encode($product->variants->map(function ($variant) {
                $combinations = [];
                foreach ($variant->combinations as $combination) {
                    $combinations[$combination->attribute_id] = $combination->attribute_value_id;
                }
                return [
                    'id' => $variant->id,
                    'sku' => $variant->sku,
                    'barcode' => $variant->barcode,
                    'price' => $variant->price,
                    'quantity' => $variant->quantity,
                    'low_stock_threshold' => $variant->low_stock_threshold,
                    'status' => $variant->status,
                    'image' => $variant->image,
                    'combinations' => $combinations,
                    'combinationKey' => json_encode($combinations)
                ];
            })->keyBy('combinationKey')) !!};
        @endif

        $(document).ready(function () {
            // Initialize Select2
            $('.single-select').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: '{{ __("admin.select_an_option") }}',
                allowClear: true
            });

            // Numeric validation
            $('.__numeric_decimal').on('keypress', function (e) {
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

            $('.__numeric').on('keypress', function (e) {
                var charCode = (e.which) ? e.which : e.keyCode;
                if (charCode < 48 || charCode > 57) {
                    return false;
                }
                return true;
            });

            // Load existing selected attributes for edit mode
            loadExistingAttributes();

            // Attribute card click handler
            $('.attribute-card').on('click', function (e) {
                if ($(e.target).is('input') || $(e.target).is('label')) {
                    return;
                }
                const checkbox = $(this).find('.attribute-checkbox');
                checkbox.prop('checked', !checkbox.prop('checked'));
                checkbox.trigger('change');
            });

            // Attribute checkbox change handler - UPDATED
            $('.attribute-checkbox').on('change', function () {
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
                    // REMOVE ATTRIBUTE - Delete all variants containing this attribute
                    if (confirm(`Removing "${attributeName}" will delete all variants that use this attribute. Continue?`)) {
                        delete selectedAttributes[attributeId];
                        card.removeClass('active');
                        $(`#attributeSelector_${attributeId}`).remove();

                        // Remove variants that contain this attribute
                        removeVariantsWithAttribute(attributeId);
                    } else {
                        // User cancelled, re-check the checkbox
                        $(this).prop('checked', true);
                        return;
                    }
                }

                updateUI();
            });
        });

        // Helper function to generate consistent combination key
        function generateCombinationKey(combination) {
            return JSON.stringify(Object.keys(combination).sort().reduce((obj, key) => {
                obj[key] = combination[key];
                return obj;
            }, {}));
        }

        // Remove variants that contain a specific attribute
        function removeVariantsWithAttribute(attributeId) {
            let variantsToRemove = [];

            $('#variantsTableBody tr').each(function () {
                const $row = $(this);
                if (!$row.hasClass('empty-state-row')) {
                    const combination = $row.data('combination');
                    if (combination && combination[attributeId]) {
                        // This variant contains the removed attribute
                        let variantId = $row.find('input[name*="[id]"]').val();
                        if (variantId) {
                            $row.append(`<input type="hidden" name="deleted_variants[]" value="${variantId}">`);
                        }
                        variantsToRemove.push($row);
                    }
                }
            });

            // Remove the variants
            variantsToRemove.forEach($row => $row.remove());

            if (variantsToRemove.length > 0) {
                toastr.info(`${variantsToRemove.length} variant(s) removed because they contained the removed attribute.`);
            }

            // Check if table is empty
            if ($('#variantsTableBody tr').length === 0 || ($('#variantsTableBody tr').length === 1 && $('#variantsTableBody tr').hasClass('empty-state-row'))) {
                $('#variantsTableBody').html(`
                    <tr class="empty-state-row">
                        <td colspan="9">
                            <div class="empty-state">
                                <i class="bx bx-cube" style="font-size: 48px;"></i>
                                <p class="mt-2 mb-0">{{ __('admin.no_variants_generated') }}</p>
                                <small>{{ __('admin.select_attributes_and_generate_variants') }}</small>
                            </div>
                        </td>
                    </tr>
                `);
            }
        }

        // When attribute values are changed, update variants accordingly
        function updateVariantsForAttributeValues(attributeId, selectedValues) {
            let variantsToRemove = [];

            $('#variantsTableBody tr').each(function () {
                const $row = $(this);
                if (!$row.hasClass('empty-state-row')) {
                    const combination = $row.data('combination');
                    if (combination && combination[attributeId]) {
                        const currentValue = combination[attributeId].toString();
                        if (!selectedValues.includes(currentValue)) {
                            // This variant uses a value that is no longer selected
                            let variantId = $row.find('input[name*="[id]"]').val();
                            if (variantId) {
                                $row.append(`<input type="hidden" name="deleted_variants[]" value="${variantId}">`);
                            }
                            variantsToRemove.push($row);
                        }
                    }
                }
            });

            variantsToRemove.forEach($row => $row.remove());

            if (variantsToRemove.length > 0) {
                toastr.info(`${variantsToRemove.length} variant(s) removed due to unselected attribute values.`);
            }

            // Check if table is empty
            if ($('#variantsTableBody tr').length === 0 || ($('#variantsTableBody tr').length === 1 && $('#variantsTableBody tr').hasClass('empty-state-row'))) {
                $('#variantsTableBody').html(`
                    <tr class="empty-state-row">
                        <td colspan="9">
                            <div class="empty-state">
                                <i class="bx bx-cube" style="font-size: 48px;"></i>
                                <p class="mt-2 mb-0">{{ __('admin.no_variants_generated') }}</p>
                                <small>{{ __('admin.select_attributes_and_generate_variants') }}</small>
                            </div>
                        </td>
                    </tr>
                `);
            }
        }

        // Load existing attributes for edit mode
        function loadExistingAttributes() {
            @if($product->has_variants && $product->variants->count() > 0)
                // First, mark attributes as checked based on existing variants
                let attributesToCheck = {};
                let attributeValuesToCheck = {};

                @foreach($product->variants as $variant)
                    @foreach($variant->combinations as $combination)
                        attributesToCheck[{{ $combination->attribute_id }}] = true;
                        if (!attributeValuesToCheck[{{ $combination->attribute_id }}]) {
                            attributeValuesToCheck[{{ $combination->attribute_id }}] = [];
                        }
                        if (!attributeValuesToCheck[{{ $combination->attribute_id }}].includes({{ $combination->attribute_value_id }})) {
                            attributeValuesToCheck[{{ $combination->attribute_id }}].push({{ $combination->attribute_value_id }});
                        }
                    @endforeach
                @endforeach

                    // Check the attributes
                    for (let attrId in attributesToCheck) {
                    setTimeout(function () {
                        $(`#attr_${attrId}`).prop('checked', true).trigger('change');
                    }, 100);
                }

                // Pre-select values after attributes are loaded
                setTimeout(function () {
                    for (let attrId in attributeValuesToCheck) {
                        for (let valueId of attributeValuesToCheck[attrId]) {
                            if ($(`#value_${attrId}_${valueId}`).length) {
                                $(`#value_${attrId}_${valueId}`).prop('checked', true);
                                if (selectedAttributes[attrId] && !selectedAttributes[attrId].values.includes(valueId.toString())) {
                                    selectedAttributes[attrId].values.push(valueId.toString());
                                }
                            }
                        }
                    }
                    updateUI();
                }, 300);
            @endif
        }

        // Render attribute selector with values
        function renderAttributeSelector(attributeId, attributeName) {
            let valuesHtml = '';
            const values = attributeValuesData[attributeId];

            for (let valueId in values) {
                const value = values[valueId];
                const isChecked = selectedAttributes[attributeId] && selectedAttributes[attributeId].values.includes(valueId);
                const checkedAttr = isChecked ? 'checked' : '';

                if (value.color_code) {
                    valuesHtml += `
                        <div class="value-checkbox">
                            <input type="checkbox" class="attribute-value-checkbox"
                                   id="value_${attributeId}_${value.id}"
                                   value="${value.id}"
                                   data-attribute-id="${attributeId}"
                                   data-value-name="${value.value}"
                                   ${checkedAttr}>
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
                                   data-value-name="${value.value}"
                                   ${checkedAttr}>
                            <label for="value_${attributeId}_${value.id}">${value.value}</label>
                        </div>
                    `;
                }
            }

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

            // Bind value change events - UPDATED
            $(`#attributeSelector_${attributeId} .attribute-value-checkbox`).on('change', function () {
                const attrId = $(this).data('attribute-id');
                const valId = $(this).val();

                if ($(this).is(':checked')) {
                    if (!selectedAttributes[attrId].values.includes(valId)) {
                        selectedAttributes[attrId].values.push(valId);
                    }
                } else {
                    const idx = selectedAttributes[attrId].values.indexOf(valId);
                    if (idx > -1) {
                        selectedAttributes[attrId].values.splice(idx, 1);
                    }
                    // Remove variants that use this unselected value
                    updateVariantsForAttributeValues(attrId, selectedAttributes[attrId].values);
                }
                updateUI();
            });

            // Bind remove button
            $(`#attributeSelector_${attributeId} .remove-attribute-btn`).on('click', function () {
                const attrId = $(this).data('attribute-id');
                $(`#attr_${attrId}`).prop('checked', false).trigger('change');
            });
        }

        // Update UI based on selected attributes
        function updateUI() {
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

                $('.remove-badge').on('click', function () {
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
            let combinationStr = Object.values(combination).sort().join('-');
            const timestamp = Date.now().toString().slice(-4);
            return (skuBase + '-' + combinationStr + '-' + timestamp + '-' + index).toUpperCase();
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

        // Generate variants button click - Generate ONLY missing variants
// Generate variants button click - Generate ONLY variants with NEW values
$('#generateVariantsBtn').on('click', function() {
    let attributesForCombination = {};
    let hasNewValues = false;
    let newValuesAdded = [];

    // Track which values are new (not previously selected)
    for (let attrId in selectedAttributes) {
        if (selectedAttributes[attrId].values.length > 0) {
            attributesForCombination[attrId] = selectedAttributes[attrId].values;

            // Check which values are already in existing table
            const existingValuesForAttr = new Set();
            $('#variantsTableBody tr:not(.empty-state-row)').each(function() {
                const combination = $(this).data('combination');
                if (combination && combination[attrId]) {
                    existingValuesForAttr.add(combination[attrId].toString());
                }
            });

            // Find new values for this attribute
            const newValues = selectedAttributes[attrId].values.filter(v => !existingValuesForAttr.has(v));
            if (newValues.length > 0) {
                hasNewValues = true;
                newValuesAdded.push({
                    attrId: attrId,
                    attrName: selectedAttributes[attrId].name,
                    values: newValues
                });
            }
        }
    }

    if (Object.keys(attributesForCombination).length === 0) {
        toastr.warning('{{ __("admin.please_select_at_least_one_attribute_value") }}');
        return;
    }

    // If no new values, suggest to add new values first
    if (!hasNewValues) {
        toastr.info('No new attribute values detected. To add new variants, please select additional attribute values first.');
        return;
    }

    // Show which new values will generate variants
    let newValuesMessage = 'New values detected:\n';
    newValuesAdded.forEach(item => {
        newValuesMessage += `\n• ${item.attrName}: ${item.values.length} new value(s)`;
    });
    newValuesMessage += '\n\nGenerate variants for these new values only?';

    if (!confirm(newValuesMessage)) {
        return;
    }

    // Get ALL EXISTING combinations
    const existingCombinations = [];
    $('#variantsTableBody tr:not(.empty-state-row)').each(function() {
        const combination = $(this).data('combination');
        if (combination) {
            existingCombinations.push(combination);
        }
    });

    // Generate ONLY new combinations that include at least one NEW value
    let newCombinationsToAdd = [];

    // For each new value, generate combinations
    newValuesAdded.forEach(newValueItem => {
        const attrId = newValueItem.attrId;

        newValueItem.values.forEach(newValueId => {
            // Get all other selected attributes (excluding this one)
            let otherAttributes = {};
            for (let otherAttrId in attributesForCombination) {
                if (otherAttrId != attrId) {
                    otherAttributes[otherAttrId] = attributesForCombination[otherAttrId];
                }
            }

            // Generate combinations for other attributes
            let otherCombinations = [{}];
            for (let oAttrId in otherAttributes) {
                let values = otherAttributes[oAttrId];
                let newOtherCombinations = [];
                for (let combo of otherCombinations) {
                    for (let value of values) {
                        let newCombo = { ...combo, [oAttrId]: value };
                        newOtherCombinations.push(newCombo);
                    }
                }
                otherCombinations = newOtherCombinations;
            }

            // Add the new value to each combination
            otherCombinations.forEach(combo => {
                const newCombo = { ...combo, [attrId]: newValueId };
                const combinationKey = generateCombinationKey(newCombo);

                // Check if this combination already exists
                let exists = false;
                for (let existing of existingCombinations) {
                    if (generateCombinationKey(existing) === combinationKey) {
                        exists = true;
                        break;
                    }
                }

                // Also check if we're already adding it in this batch
                for (let pending of newCombinationsToAdd) {
                    if (generateCombinationKey(pending) === combinationKey) {
                        exists = true;
                        break;
                    }
                }

                if (!exists) {
                    newCombinationsToAdd.push(newCombo);
                }
            });
        });
    });

    if (newCombinationsToAdd.length === 0) {
        toastr.info('No new variants to add. All combinations with new values already exist!');
        return;
    }

    // Confirm before adding
    if (!confirm(`Add ${newCombinationsToAdd.length} new variant(s) for the new attribute values? Existing variants will be preserved.`)) {
        return;
    }

    // Add new variants
    let currentVariantCount = $('#variantsTableBody tr:not(.empty-state-row)').length;
    let html = '';

    newCombinationsToAdd.forEach((combination, idx) => {
        let combinationHtml = '';
        let attributesHtml = '';
        const currentIndex = currentVariantCount + idx;
        const attributeIds = Object.keys(combination).sort();

        attributeIds.forEach(attrId => {
            let valueId = combination[attrId];
            let valueText = attributeValuesData[attrId] ? attributeValuesData[attrId][valueId]?.value : 'Unknown';

            combinationHtml += `<span class="combination-badge">${valueText}</span>`;
            attributesHtml += `<input type="hidden" name="variants[${currentIndex}][attributes][${attrId}]" value="${valueId}">`;
        });

        html += `
            <tr data-combination='${JSON.stringify(combination)}'>
                <td>${combinationHtml}</td>
                <td>
                    <div class="variant-image-container">
                        <input type="file" name="variants[${currentIndex}][image]" class="form-control mt-1 variant-image-input" accept="image/*" onchange="previewVariantImage(this, ${currentIndex})" style="font-size: 12px;">
                        <div class="mt-2">
                            <img id="variantImagePreview_${currentIndex}" class="img-thumbnail d-none" width="50">
                        </div>
                        <input type="hidden" name="variants[${currentIndex}][existing_image]" value="">
                    </div>
                 </div>
                <td>
                    <input type="text" name="variants[${currentIndex}][sku]" class="form-control variant-sku" value="${generateSku(combination, currentIndex)}" required>
                    ${attributesHtml}
                 </div>
                <td><input type="text" name="variants[${currentIndex}][barcode]" class="form-control"></div>
                <td><input type="text" name="variants[${currentIndex}][price]" class="form-control variant-price __numeric_decimal" required></div>
                <td><input type="number" name="variants[${currentIndex}][quantity]" class="form-control variant-qty" value="0" required></div>
                <td><input type="number" name="variants[${currentIndex}][low_stock_threshold]" class="form-control" value="5"></div>
                <td>
                    <select name="variants[${currentIndex}][status]" class="form-select">
                        <option value="1">{{ __("admin.active") }}</option>
                        <option value="0">{{ __("admin.inactive") }}</option>
                    </select>
                 </div>
                <td><button type="button" class="btn btn-sm btn-danger remove-variant"><i class="bx bx-trash"></i></button></div>
            </div>
        `;
    });

    // Append only new variants
    if (html) {
        if ($('#variantsTableBody tr').length === 1 && $('#variantsTableBody tr').hasClass('empty-state-row')) {
            $('#variantsTableBody').empty();
        }
        $('#variantsTableBody').append(html);
        bindVariantEvents();
        toastr.success(`${newCombinationsToAdd.length} new variant(s) added successfully for the new attribute values!`);
    }
});

        // Preview variant image
        window.previewVariantImage = function (input, index) {
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
            $('.remove-variant').off('click').on('click', function () {
                if (confirm('{{ __("admin.remove_variant_confirmation") }}')) {
                    let row = $(this).closest('tr');
                    let variantId = row.find('input[name*="[id]"]').val();
                    if (variantId) {
                        row.append(`<input type="hidden" name="deleted_variants[]" value="${variantId}">`);
                    }
                    row.remove();
                    if ($('#variantsTableBody tr').length === 0) {
                        $('#variantsTableBody').html(`
                            <tr class="empty-state-row">
                                <td colspan="9">
                                    <div class="empty-state">
                                        <i class="bx bx-cube" style="font-size: 48px;"></i>
                                        <p class="mt-2 mb-0">{{ __('admin.no_variants_generated') }}</p>
                                        <small>{{ __('admin.select_attributes_and_generate_variants') }}</small>
                                    </div>
                                </td>
                            </tr>
                        `);
                    }
                    toastr.success('{{ __("admin.variant_removed") }}');
                }
            });

            $('.remove-variant-image').off('click').on('click', function () {
                if (confirm('{{ __("admin.remove_variant_image_confirmation") }}')) {
                    let variantId = $(this).data('variant-id');
                    let imagePath = $(this).data('image');
                    let container = $(this).closest('.variant-image-container');
                    container.find('.variant-image-preview').remove();
                    $(this).remove();
                    container.append(`<input type="hidden" name="remove_variant_image[${variantId}]" value="${imagePath}">`);
                    toastr.success('{{ __("admin.variant_image_removed") }}');
                }
            });

            $('.variant-price').off('change').on('change', function () {
                let price = parseFloat($(this).val()) || 0;
                if (price <= 0) {
                    toastr.warning('{{ __("admin.please_enter_valid_price") }}');
                }
            });
        }

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
        $('input[name="product_type"]').on('change', function () {
            if ($(this).val() == 'simple') {
                $('#simple_product_fields').slideDown();
                $('#variant_product_fields').slideUp();
                $('#has_variants').val(0);
                $('input[name="sku_number"]').attr('required', true);
                $('input[name="barcode_number"]').attr('required', true);
                $('input[name="price"]').attr('required', true);
                $('input[name="pieces_available"]').attr('required', true);
                $('input[name="offer_price"]').attr('required', false);
            } else {
                $('#simple_product_fields').slideUp();
                $('#variant_product_fields').slideDown();
                $('#has_variants').val(1);
                $('input[name="sku_number"]').removeAttr('required');
                $('input[name="barcode_number"]').removeAttr('required');
                $('input[name="price"]').removeAttr('required');
                $('input[name="pieces_available"]').removeAttr('required');
                $('input[name="offer_price"]').removeAttr('required');
            }
        });

        // Category change
        $(document).ready(function () {
            let categoryId = $('.__category').val();
            let selectedSubcategory = $('.__subcategory').data('selected');
            let route = $('.__category').data('route');

            if (categoryId && selectedSubcategory) {
                $.ajax({
                    url: route,
                    type: 'POST',
                    data: { category_id: categoryId, selected: selectedSubcategory, _token: '{{ csrf_token() }}' },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        if (response.success) {
                            if ($('.__subcategory').data('select2')) {
                                $('.__subcategory').select2('destroy');
                            }
                            $('.__subcategory').html(response.options);
                            $('.__subcategory').val(selectedSubcategory).trigger('change');
                            $('.__subcategory').select2({
                                theme: 'bootstrap-5',
                                width: '100%',
                                placeholder: '{{ __("admin.select_an_option") }}',
                                allowClear: true
                            });
                        }
                    }
                });
            }
        });

        $('body').on('change', '.__category', function (e) {
            var value = $(this).val();
            if ($('.__category').length > 0 && value != '') {
                let route = $(this).attr('data-route');
                $.ajax({
                    url: route,
                    type: 'POST',
                    data: { category_id: value, _token: '{{ csrf_token() }}' },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
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
                    error: function (xhr) {
                        toastr.error('Error loading subcategories');
                    }
                });
            }
        });

        // Preview functions
        window.previewCoverImage = function (input) {
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

        window.previewGalleryImages = function (input) {
            const preview = document.getElementById('galleryPreview');
            if (input.files && input.files[0]) {
                for (let file of input.files) {
                    const col = document.createElement('div');
                    col.className = 'col-md-2 col-sm-3 col-6 mb-3 position-relative gallery-item';
                    const removeBtn = document.createElement('span');
                    removeBtn.innerHTML = '×';
                    removeBtn.className = 'remove-gallery-image';
                    removeBtn.onclick = () => col.remove();
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.className = 'img-thumbnail preview-image';
                    img.style.height = '100px';
                    img.style.width = '100%';
                    img.style.objectFit = 'cover';
                    col.appendChild(removeBtn);
                    col.appendChild(img);
                    preview.appendChild(col);
                }
            }
        };

        let removedGallery = [];
        document.addEventListener('click', function (e) {
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

        // Slug generation
        $('#product_name').on('keyup', function () {
            let slug = $(this).val().toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
            $('#product_slug').val(slug);
        });

        $('#regenSlug').on('click', function () {
            let slug = $('#product_name').val().toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
            $('#product_slug').val(slug);
        });

        // Form submission validation
        $('#productForm').on('submit', function (e) {
            if ($('#has_variants').val() == '1') {
                let hasVariants = $('#variantsTableBody tr').length > 0 && !$('#variantsTableBody tr').hasClass('empty-state-row');
                if (!hasVariants) {
                    e.preventDefault();
                    toastr.error('{{ __("admin.please_generate_at_least_one_variant") }}');
                    return false;
                }

                let valid = true;
                $('.variant-price').each(function () {
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

            let submitBtn = $('#submitBtn');
            submitBtn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin"></i> {{ __("admin.processing") }}');
        });

        // Clean up blob URLs
        window.addEventListener('beforeunload', function () {
            document.querySelectorAll('img[src^="blob:"]').forEach(img => {
                URL.revokeObjectURL(img.src);
            });
        });

        // Initialize bindings for existing variants
        bindVariantEvents();
    </script>
@endsection

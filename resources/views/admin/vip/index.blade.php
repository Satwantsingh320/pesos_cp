@extends('layouts.master')
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <style>
        /* Fix for Select2 in modals */
        .select2-container--bootstrap-5.select2-container--open {
            z-index: 9999 !important;
        }

        .select2-dropdown {
            z-index: 9999 !important;
        }

        .modal {
            z-index: 1050;
        }

        .modal-backdrop {
            z-index: 1040;
        }
    </style>
@endsection

@section('content')
    <!-- Your existing HTML content remains the same -->
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                {{-- Display Success Messages --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bx bx-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- Display Error Messages --}}
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bx bx-error-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- Display Validation Errors --}}
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong><i class="bx bx-error-circle me-2"></i>{{ __('admin.validation_errors') }}</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-flex align-items-center justify-content-between">
                            <h4 class="mb-sm-0">{{ __('admin.vip_customer_management') }}</h4>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Assign VIP Section -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">{{ __('admin.assign_vip_to_customer') }}</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.vip.assign') }}" method="POST" id="vipAssignForm">
                                    @csrf

                                    <!-- Pricing Type Selection -->
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('admin.pricing_type') }} <span
                                                class="text-danger">*</span></label>
                                        <select name="pricing_type" class="form-select" id="pricingType" required>
                                            <option value="manual">{{ __('admin.manual_prices_only') }}</option>
                                            <option value="discount">{{ __('admin.discount_based') }}</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">{{ __('admin.select_customer') }} <span
                                                class="text-danger">*</span></label>
                                        <select name="customer_id" class="form-select select2-single" required>
                                            <option value="">{{ __('admin.select_customer') }}</option>
                                            @foreach($regularCustomers as $customer)
                                                <option value="{{ $customer->id }}">{{ $customer->name }}
                                                    ({{ $customer->email }})</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Discount Fields (Hidden by default) -->
                                    <div id="discountFields" style="display: none;">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('admin.discount_type') }} <span
                                                            class="text-danger">*</span></label>
                                                    <select name="discount_type" class="form-select">
                                                        <option value="percentage">{{ __('admin.percentage_discount') }} (%)
                                                        </option>
                                                        <option value="fixed">{{ __('admin.fixed_discount') }}
                                                            ({{CURRENCY}})</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('admin.discount_value') }} <span
                                                            class="text-danger">*</span></label>
                                                    <input type="number" name="discount_value" class="form-control"
                                                        step="0.01" min="0">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">{{ __('admin.apply_to') }} <span
                                                    class="text-danger">*</span></label>
                                            <select name="apply_to" class="form-select" id="applyToSelect">
                                                <option value="all">{{ __('admin.all_products') }}</option>
                                                <option value="selected">{{ __('admin.selected_products_only') }}</option>
                                            </select>
                                        </div>

                                        <div class="mb-3 d-none" id="productSelectDiv">
                                            <label class="form-label">{{ __('admin.select_products') }}</label>
                                            <select name="manual_product_ids[]" class="form-select select2-multiple"
                                                multiple>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}">{{ $product->name }}
                                                        ({{ $product->sku_number ?? __('admin.no_sku') }})</option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted">{{ __('admin.select_products_note') }}</small>
                                        </div>
                                    </div>

                                    <!-- Manual Products Fields (Visible by default) -->
                                    <div id="manualFields">
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('admin.select_products') }} <span
                                                    class="text-danger">*</span></label>
                                            <select name="manual_product_ids[]" class="form-select select2-multiple"
                                                multiple required>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}">{{ $product->name }}
                                                        ({{ $product->sku_number ?? __('admin.no_sku') }})</option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted">{{ __('admin.manual_prices_note') }} -
                                                {{ __('admin.select_products_then_set_manual_prices') }}</small>
                                        </div>
                                    </div>

                                    <div class="mb-3 d-none">
                                        <label class="form-label">{{ __('admin.expiry_date') }}
                                            ({{ __('admin.optional') }})</label>
                                        <input type="date" name="expiry_date" class="form-control">
                                        <small class="text-muted">{{ __('admin.vip_expiry_date_note') }}</small>
                                    </div>

                                    <button type="submit" class="btn btn-primary">{{ __('admin.assign_vip') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- VIP Customers List -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">{{ __('admin.vip_customers') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>{{ __('admin.name') }}</th>
                                                <th>{{ __('admin.discount') }}</th>
                                                <th>{{ __('admin.apply_to') }}</th>
                                                <th>{{ __('admin.expiry') }}</th>
                                                <th>{{ __('admin.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <!-- VIP Customers List Table Body -->
                                        <tbody>
                                            @forelse($vipCustomers as $customer)
                                                <tr>
                                                    <td>{{ $customer->name }} <br><small>{{ $customer->email }}</small></td>
                                                    <td>
                                                        @if($customer->vip_apply_to == 'manual_only')
                                                            <span class="badge bg-primary">{{ __('admin.manual_pricing') }}</span>
                                                        @elseif($customer->vip_discount_type == 'percentage')
                                                            <span class="badge bg-success">{{ $customer->vip_discount_value }}%
                                                                {{ __('admin.off') }}</span>
                                                        @elseif($customer->vip_discount_type == 'fixed')
                                                            <span
                                                                class="badge bg-info">{{CURRENCY}}{{ number_format($customer->vip_discount_value, 2) }}
                                                                {{ __('admin.off') }}</span>
                                                        @else
                                                            <span class="badge bg-secondary">{{ __('admin.na') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($customer->vip_apply_to == 'manual_only')
                                                            <span class="badge bg-primary">{{ __('admin.manual_products') }}</span>
                                                            <button class="btn btn-sm btn-link p-0 ms-1"
                                                                onclick="viewProducts({{ $customer->id }})">{{ __('admin.view_prices') }}</button>
                                                        @elseif($customer->vip_apply_to == 'selected_products')
                                                            <span
                                                                class="badge bg-warning">{{ __('admin.selected_products') }}</span>
                                                            <button class="btn btn-sm btn-link p-0 ms-1"
                                                                onclick="viewProducts({{ $customer->id }})">{{ __('admin.view') }}</button>
                                                        @elseif($customer->vip_apply_to == 'all')
                                                            <span class="badge bg-info">{{ __('admin.all_products') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $customer->vip_expiry_date ? date('d M Y', strtotime($customer->vip_expiry_date)) : __('admin.never') }}
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            @if($customer->vip_apply_to != 'manual_only')
                                                                <button class="btn btn-sm btn-outline-warning"
                                                                    onclick="editVip({{ $customer->id }})"
                                                                    title="{{ __('admin.edit') }}">
                                                                    <i class="bx bx-edit"></i>
                                                                </button>
                                                            @endif
                                                            <form action="{{ route('admin.vip.remove', $customer->id) }}"
                                                                method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                                    onclick="return confirm('{{ __('admin.remove_vip_confirmation') }}')"
                                                                    title="{{ __('admin.remove') }}">
                                                                    <i class="bx bx-trash"></i>
                                                                </button>
                                                            </form>
                                                            <button class="btn btn-sm btn-outline-primary"
                                                                onclick="setManualPrice({{ $customer->id }})"
                                                                title="{{ __('admin.set_manual_price') }}">
                                                                <i class="bx bx-dollar"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center">{{ __('admin.no_vip_customers') }}</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Manual Prices Section -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">{{ __('admin.manual_product_prices') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="manualPricesTable">
                                        <thead>
                                            <tr>
                                                <th>{{ __('admin.customer') }}</th>
                                                <th>{{ __('admin.product') }}</th>
                                                <th>{{ __('admin.variant') }}</th>
                                                <th>{{ __('admin.regular_price') }}</th>
                                                <th>{{ __('admin.vip_price') }}</th>
                                                <th>{{ __('admin.savings') }}</th>
                                                <th>{{ __('admin.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($vipCustomers as $customer)
                                                @foreach($customer->vipPrices as $price)
                                                    @if($price->vip_price > 0)
                                                        @php
                                                            $regularPrice = $price->variant
                                                                ? ($price->variant->offer_price ?? $price->variant->price)
                                                                : ($price->product->offer_price ?? $price->product->price);
                                                            if (empty($regularPrice) && $price->product->has_variants == 1) {
                                                                $regularPrice = $price->product->raw_price;
                                                            }
                                                            $savings = $regularPrice - $price->vip_price;
                                                        @endphp
                                                        <tr>
                                                            <td>{{ $customer->name }}</td>
                                                            <td>{{ $price->product->name }}</td>
                                                            <td>{{ $price->variant->sku ?? __('admin.base_product') }}</td>
                                                            <td>{{CURRENCY}}{{ number_format($regularPrice, 2) }}</td>
                                                            <td><strong
                                                                    class="text-success">{{CURRENCY}}{{ number_format($price->vip_price, 2) }}</strong>
                                                            </td>
                                                            <td><span
                                                                    class="badge bg-success">{{CURRENCY}}{{ number_format($savings, 2) }}
                                                                    ({{ round(($savings / $regularPrice) * 100) }}%)</span></td>
                                                            <td>
                                                                <button class="btn btn-sm btn-danger"
                                                                    onclick="deleteManualPrice({{ $price->id }})"
                                                                    title="{{ __('admin.delete') }}">
                                                                    <i class="bx bx-trash"></i>
                                                                </button>
                                                            </td>
                                                        </tr>

                                                    @endif
                                                @endforeach
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center">{{ __('admin.no_manual_prices') }}</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals (same as before) -->
    <!-- Edit VIP Modal -->
    <div class="modal fade" id="editVipModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('admin.edit_vip_customer') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editVipForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" name="customer_id" id="edit_customer_id">
                        <div class="mb-3">
                            <label class="form-label">{{ __('admin.discount_type') }}</label>
                            <select name="discount_type" id="edit_discount_type" class="form-select" required>
                                <option value="percentage">{{ __('admin.percentage_discount') }} (%)</option>
                                <option value="fixed">{{ __('admin.fixed_discount') }}</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('admin.discount_value') }}</label>
                            <input type="number" name="discount_value" id="edit_discount_value" class="form-control"
                                step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('admin.apply_to') }}</label>
                            <select name="apply_to" id="edit_apply_to" class="form-select" required>
                                <option value="all">{{ __('admin.all_products') }}</option>
                                <option value="selected">{{ __('admin.selected_products_only') }}</option>
                            </select>
                        </div>
                        <div class="mb-3 d-none" id="edit_productSelectDiv">
                            <label class="form-label">{{ __('admin.select_products') }}</label>
                            <select name="product_ids[]" id="edit_product_ids" class="form-select select2-multiple"
                                multiple>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('admin.cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('admin.update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Set Manual Price Modal -->
    <div class="modal fade" id="manualPriceModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('admin.set_manual_price') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="manual_customer_id">
                    <div class="mb-3">
                        <label class="form-label">{{ __('admin.select_product') }}</label>
                        <select id="manual_product_id" class="form-select select2-single" style="width: 100%">
                            <option value="">{{ __('admin.select_product') }}</option>
                        </select>
                    </div>
                    <div class="mb-3 d-none" id="variantSelectDiv">
                        <label class="form-label">{{ __('admin.select_variant') }}</label>
                        <select id="manual_variant_id" class="form-select">
                            <option value="">{{ __('admin.select_variant') }}</option>
                        </select>
                    </div>
                    <div class="mb-3" id="priceInfoDiv">
                        <div class="alert alert-info">
                            <div class="d-flex justify-content-between">
                                <span>{{ __('admin.regular_price') }}:</span>
                                <strong id="regular_price_display">{{CURRENCY}}0.00</strong>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <span>{{ __('admin.vip_price') }}:</span>
                                <input type="number" id="vip_price_input" class="form-control" step="0.01"
                                    style="width: 150px">
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <span>{{ __('admin.savings') }}:</span>
                                <strong id="savings_display" class="text-success">{{CURRENCY}}0.00</strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('admin.cancel') }}</button>
                    <button type="button" class="btn btn-primary"
                        onclick="saveManualPrice()">{{ __('admin.save_price') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Product List Modal -->
    <div class="modal fade" id="productListModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('admin.vip_products') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body"></div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // Toggle between pricing types
        $('#pricingType').on('change', function () {
            if ($(this).val() == 'manual') {
                $('#discountFields').slideUp();
                $('#manualFields').slideDown();
                // Remove required attributes from discount fields
                $('#discountFields').find('input, select').removeAttr('required');
                // Add required attributes to manual fields
                $('#manualFields').find('select').attr('required', true);
            } else {
                $('#manualFields').slideUp();
                $('#discountFields').slideDown();
                // Remove required attributes from manual fields
                $('#manualFields').find('select').removeAttr('required');
                // Add required attributes to discount fields
                $('#discountFields').find('input, select').attr('required', true);
            }
        });

        // Initialize
        $('#pricingType').trigger('change');
        // Define routes as JavaScript variables
        const routes = {
            variants: @json(route('admin.vip.product.variants', ['id' => ':id'])),
            productPrice: @json(route('admin.vip.product.price', ['id' => ':id'])),
            edit: @json(route('admin.vip.edit', ['id' => ':id'])),
            update: @json(route('admin.vip.update', ['id' => ':id'])),
            prices: @json(route('admin.vip.prices', ['id' => ':id'])),
            manualPrice: @json(route('admin.vip.manual-price')),
            deleteManualPrice: @json(route('admin.vip.manual-price.delete', ['id' => ':id']))
        };

        $(document).ready(function () {
            // Initialize Select2
            function initSelect2() {
                $('.select2-single').each(function () {
                    if (!$(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2({
                            theme: 'bootstrap-5',
                            width: '100%',
                            placeholder: '{{ __("admin.select_an_option") }}',
                            allowClear: true
                        });
                    }
                });

                $('.select2-multiple').each(function () {
                    if (!$(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2({
                            theme: 'bootstrap-5',
                            width: '100%',
                            placeholder: '{{ __("admin.select_options") }}',
                            allowClear: true,
                            closeOnSelect: false
                        });
                    }
                });
            }

            initSelect2();

            // Modal events for Select2
            $('#editVipModal').on('shown.bs.modal', function () {
                $('#edit_product_ids').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: '{{ __("admin.select_options") }}',
                    allowClear: true,
                    closeOnSelect: false,
                    dropdownParent: $('#editVipModal')
                });
            });

            $('#manualPriceModal').on('shown.bs.modal', function () {
                $('#manual_product_id').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: '{{ __("admin.select_product") }}',
                    allowClear: true,
                    dropdownParent: $('#manualPriceModal')
                });
            });

            // Apply to select change
            $('#applyToSelect').on('change', function () {
                if ($(this).val() == 'selected') {
                    $('#productSelectDiv').removeClass('d-none');
                    $('.select2-multiple').select2({
                        theme: 'bootstrap-5',
                        width: '100%',
                        placeholder: '{{ __("admin.select_options") }}',
                        allowClear: true,
                        closeOnSelect: false
                    });
                } else {
                    $('#productSelectDiv').addClass('d-none');
                }
            });

            $('#edit_apply_to').on('change', function () {
                if ($(this).val() == 'selected') {
                    $('#edit_productSelectDiv').removeClass('d-none');
                    $('#edit_product_ids').select2({
                        theme: 'bootstrap-5',
                        width: '100%',
                        placeholder: '{{ __("admin.select_options") }}',
                        allowClear: true,
                        closeOnSelect: false,
                        dropdownParent: $('#editVipModal')
                    });
                } else {
                    $('#edit_productSelectDiv').addClass('d-none');
                }
            });

            // Load variants when product changes
            $('#manual_product_id').on('change', function () {
                var productId = $(this).val();
                var customerId = $('#manual_customer_id').val();

                if (productId) {
                    var variantsUrl = routes.variants.replace(':id', productId);

                    $.get(variantsUrl, function (response) {
                        if (response.success && response.variants.length > 0) {
                            $('#variantSelectDiv').removeClass('d-none');
                            var options = '<option value="">{{ __("admin.select_variant") }}</option>';
                            response.variants.forEach(function (variant) {
                                options += `<option value="${variant.id}" data-price="${variant.price}">${variant.text}</option>`;
                            });
                            $('#manual_variant_id').html(options);
                        } else {
                            $('#variantSelectDiv').addClass('d-none');
                            $('#manual_variant_id').val('');
                        }
                    });

                    var priceUrl = routes.productPrice.replace(':id', productId);

                    $.get(priceUrl, { customer_id: customerId }, function (response) {
                        if (response.success) {
                            $('#regular_price_display').text('{{ env("CURRENCY_SYMBOL", "$") }}' + response.price);
                            $('#vip_price_input').val(response.price);
                            $('#savings_display').text('{{ env("CURRENCY_SYMBOL", "$") }}0.00');
                        }
                    });
                }
            });

            $('#manual_variant_id').on('change', function () {
                var selectedOption = $(this).find('option:selected');
                var price = selectedOption.data('price');
                if (price) {
                    $('#regular_price_display').text('{{ env("CURRENCY_SYMBOL", "$") }}' + price);
                    $('#vip_price_input').val(price);
                    updateSavings();
                }
            });

            $('#vip_price_input').on('input', function () {
                updateSavings();
            });

            function updateSavings() {
                var regularPriceText = $('#regular_price_display').text();

                // Remove currency symbol and any non-numeric characters except decimal point and minus sign
                var cleanPrice = regularPriceText.replace(/[^0-9.-]/g, '');
                var regularPrice = parseFloat(cleanPrice);
                var vipPrice = parseFloat($('#vip_price_input').val());

                if (!isNaN(regularPrice) && !isNaN(vipPrice) && regularPrice > 0) {
                    var savings = regularPrice - vipPrice;
                    var percentage = (savings / regularPrice) * 100;
                    var currencySymbol = '{{ env("CURRENCY_SYMBOL", "$") }}';

                    $('#savings_display').text(currencySymbol + savings.toFixed(2) + ' (' + percentage.toFixed(0) + '%)');
                } else {
                    $('#savings_display').text('{{ env("CURRENCY_SYMBOL", "$") }}0.00');
                }
            }
        });

        function editVip(id) {
            var editUrl = routes.edit.replace(':id', id);

            $.get(editUrl, function (data) {

                $('#edit_customer_id').val(data.customer.id);
                $('#edit_discount_type').val(data.customer.vip_discount_type);
                $('#edit_discount_value').val(data.customer.vip_discount_value);
                $('#edit_apply_to').val(data.customer.vip_apply_to == 'selected_products' ? 'selected' : 'all');
                $('#edit_expiry_date').val(data.customer.vip_expiry_date);

                if (data.customer.vip_apply_to == 'selected_products' && data.productIds && data.productIds.length > 0) {
                    $('#edit_productSelectDiv').removeClass('d-none');

                    // IMPORTANT: Initialize Select2 first
                    $('#edit_product_ids').select2({
                        theme: 'bootstrap-5',
                        width: '100%',
                        dropdownParent: $('#editVipModal')
                    });

                    // Then set the values
                    $('#edit_product_ids').val(data.productIds).trigger('change');
                } else {
                    $('#edit_productSelectDiv').addClass('d-none');
                }

                var updateUrl = routes.update.replace(':id', id);
                $('#editVipForm').attr('action', updateUrl);
                $('#editVipModal').modal('show');
            });
        }

        function viewProducts(id) {
            var pricesUrl = routes.prices.replace(':id', id);

            $.get(pricesUrl, function (data) {
                let html = '<div class="list-group">';

                if (data.prices && data.prices.length > 0) {
                    data.prices.forEach(price => {
                        // Helper function to parse price strings
                        function parsePrice(priceValue) {
                            if (typeof priceValue === 'number') return priceValue;
                            // Remove commas, currency symbols, and convert to float
                            var cleaned = String(priceValue).replace(/[^0-9.-]/g, '');
                            return parseFloat(cleaned);
                        }

                        var regularPrice = parsePrice(price.regular_price);
                        var vipPrice = parsePrice(price.vip_price);

                        var savings = regularPrice - vipPrice;
                        var savingsPercentage = ((savings / regularPrice) * 100).toFixed(0);
                        var currencySymbol = '{{ env("CURRENCY_SYMBOL", "$") }}';

                        html += `
                                                                        <div class="list-group-item">
                                                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                                                <strong>${price.product_name}</strong>
                                                                                ${price.variant_sku ? `<small class="text-muted">SKU: ${price.variant_sku}</small>` : ''}
                                                                            </div>

                                                                            <div class="row mt-2">
                                                                                <div class="col-md-6">
                                                                                    <div class="text-muted small">{{ __('admin.regular_price') }}:</div>
                                                                                    <div class="text-decoration-line-through">${currencySymbol}${regularPrice.toFixed(2)}</div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="text-muted small">{{ __('admin.vip_price') }}:</div>
                                                                                    <div class="text-success fw-bold">${currencySymbol}${vipPrice.toFixed(2)}</div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="mt-2">
                                                                                <span class="badge bg-success">
                                                                                    {{ __('admin.savings') }}: ${currencySymbol}${savings.toFixed(2)} (${savingsPercentage}% {{ __('admin.off') }})
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    `;
                    });
                } else {
                    html += '<div class="list-group-item text-muted">No products found</div>';
                }

                html += '</div>';
                $('#productListModal .modal-body').html(html);
                $('#productListModal').modal('show');
            });
        }

        function setManualPrice(customerId) {
            $('#manual_customer_id').val(customerId);

            // First, load products for this customer
            var productsUrl = "{{ route('admin.vip.customer.products', ['id' => ':id']) }}".replace(':id', customerId);

            $.get(productsUrl, function (response) {
                if (response.success && response.products) {
                    var productSelect = $('#manual_product_id');
                    productSelect.empty();
                    productSelect.append('<option value="">{{ __("admin.select_product") }}</option>');
                    response.products.forEach(function (product) {
                        productSelect.append('<option value="' + product.id + '">' +
                            product.name + ' (' + (product.sku || '{{ __("admin.no_sku") }}') + ')</option>');
                    });

                    // Reinitialize Select2 after updating options
                    productSelect.select2({
                        theme: 'bootstrap-5',
                        width: '100%',
                        placeholder: '{{ __("admin.select_product") }}',
                        allowClear: true,
                        dropdownParent: $('#manualPriceModal')
                    });
                }
            });

            $('#manual_variant_id').val('');
            $('#variantSelectDiv').addClass('d-none');
            $('#vip_price_input').val('');
            $('#manualPriceModal').modal('show');
        }

        function saveManualPrice() {
            var customerId = $('#manual_customer_id').val();
            var productId = $('#manual_product_id').val();
            var variantId = $('#manual_variant_id').val();
            var vipPrice = $('#vip_price_input').val();

            if (!productId) {
                toastr.warning('{{ __("admin.please_select_product") }}');
                return;
            }
            if (!vipPrice || vipPrice <= 0) {
                toastr.warning('{{ __("admin.please_enter_valid_price") }}');
                return;
            }

            $.ajax({
                url: routes.manualPrice,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    customer_id: customerId,
                    product_id: productId,
                    variant_id: variantId || null,
                    vip_price: vipPrice
                },
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message);
                        location.reload();
                    } else {
                        toastr.warning(response.message);
                    }
                },
                error: function (xhr) {
                    toastr.error('{{ __("admin.error_saving_price") }}');
                }
            });
        }

        function deleteManualPrice(id) {
            if (confirm('{{ __("admin.delete_manual_price_confirmation") }}')) {
                var deleteUrl = routes.deleteManualPrice.replace(':id', id);

                $.ajax({
                    url: deleteUrl,
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    success: function (response) {
                        toastr.success(response.message);
                        location.reload();
                    },
                    error: function (xhr) {
                        toastr.error('Error deleting manual price');
                    }
                });
            }
        }
    </script>
@endsection

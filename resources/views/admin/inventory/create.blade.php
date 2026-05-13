@extends('layouts.master')

@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-flex align-items-center">
                            <a href="{{ route('inventory.index') }}" class="btn btn-dark btn-sm me-2">
                                <i class="bx bx-arrow-back"></i> {{ __('admin.back') }}
                            </a>
                            <h4 class="mb-sm-0 font-size-18">{{ __('admin.inventoryArray.add_inventory') }}</h4>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-body">
                                @if(session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ session('error') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                @endif

                                @if(session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <i class="fas fa-check-circle"></i>
                                        {{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                @endif

                                @if(session('warning'))
                                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        {{ session('warning') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                @endif

                                @if(session('info'))
                                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                                        <i class="fas fa-info-circle"></i>
                                        {{ session('info') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                @endif
                                <form method="POST" action="{{ route('inventory.store') }}" id="inventoryForm">
                                    @csrf

                                    <div class="mb-3">
                                        <label
                                            class="form-label">{{ __('admin.inventoryArray.select_product_type') }}</label>

                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="item_type"
                                                id="simple_product" value="simple" checked>
                                            <label class="form-check-label" for="simple_product">
                                                {{ __('admin.inventoryArray.simple_product') }}
                                            </label>
                                        </div>

                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="item_type"
                                                id="variant_product" value="variant">
                                            <label class="form-check-label" for="variant_product">
                                                {{ __('admin.inventoryArray.variant_product') }}
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Simple Product Section -->
                                    <div id="simple_product_section">
                                        <div class="mb-3">
                                            <label
                                                class="form-label">{{ __('admin.inventoryArray.select_product') }}</label>
                                            <select name="product_id" id="simple_product_id" class="form-select">
                                                <option value="">{{ __('admin.inventoryArray.select_product_placeholder') }}
                                                </option>
                                                @foreach($simpleProducts as $product)
                                                    <option value="{{ $product->id }}" data-sku="{{ $product->sku_number }}"
                                                        data-stock="{{ $product->quantity }}">
                                                        {{ $product->name }} (SKU: {{ $product->sku_number }}) - Stock:
                                                        {{ $product->quantity }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Variant Product Section -->
                                    <div id="variant_product_section" style="display: none;">
                                        <div class="mb-3">
                                            <label
                                                class="form-label">{{ __('admin.inventoryArray.select_product') }}</label>
                                            <select name="variant_product_id" id="variant_product_id" class="form-select">
                                                <option value="">{{ __('admin.inventoryArray.select_product_placeholder') }}
                                                </option>
                                                @foreach($variantProducts as $product)
                                                    <option value="{{ $product->id }}">
                                                        {{ $product->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label
                                                class="form-label">{{ __('admin.inventoryArray.select_variant') }}</label>
                                            <select name="variant_id" id="variant_id" class="form-select" disabled>
                                                <option value="">{{ __('admin.inventoryArray.select_product_first') }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">{{ __('admin.inventoryArray.stock_type') }}</label>
                                        <select name="stock_type" class="form-select" required>
                                            <option value="">{{ __('admin.inventoryArray.select_stock_type') }}</option>
                                            <option value="in">{{ __('admin.inventoryArray.stock_in') }}</option>
                                            <option value="out">{{ __('admin.inventoryArray.stock_out') }}</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">{{ __('admin.inventoryArray.quantity') }}</label>
                                        <input type="number" name="quantity" class="form-control" min="1" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">{{ __('admin.inventoryArray.current_stock') }}</label>
                                        <input type="text" id="current_stock_display" class="form-control" readonly
                                            disabled>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">{{ __('admin.inventoryArray.notes_optional') }}</label>
                                        <textarea name="notes" class="form-control" rows="3"
                                            placeholder="{{ __('admin.inventoryArray.notes_placeholder') }}"></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <button type="submit" class="btn btn-primary">
                                            {{ __('admin.inventoryArray.update_inventory') }}
                                        </button>
                                        <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
                                            {{ __('admin.cancel') }}
                                        </a>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">{{ __('admin.inventoryArray.recent_activity') }}</h5>

                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>{{ __('admin.date') }}</th>
                                                <th>{{ __('admin.product') }}</th>
                                                <th>{{ __('admin.qty') }}</th>
                                                <th>{{ __('admin.sku') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $recentInventory = App\Models\Inventory::with(['product', 'variant'])
                                                    ->orderBy('created_at', 'desc')
                                                    ->limit(10)
                                                    ->get();
                                            @endphp
                                            @foreach($recentInventory as $item)
                                                <tr>
                                                    <td>{{ $item->created_at->format('d M H:i') }}</td>

                                                    <td>{{ $item->product_name }}</td>
                                                    <td>
                                                        @if($item->stock_type == 'in')
                                                            <span class="badge bg-success">+{{ $item->quantity }}</span>
                                                        @else
                                                            <span class="badge bg-danger">-{{ $item->quantity }}</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $item->sku }}</td>
                                                </tr>
                                            @endforeach
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
@endsection
@section('js')
    <script>
        $(document).ready(function () {
            // DOM Elements
            const $itemType = $('input[name="item_type"]');
            const $simpleSection = $('#simple_product_section');
            const $variantSection = $('#variant_product_section');
            const $simpleProductSelect = $('#simple_product_id');
            const $variantProductSelect = $('#variant_product_id');
            const $variantSelect = $('#variant_id');
            const $currentStock = $('#current_stock_display');
            const $stockType = $('select[name="stock_type"]');
            const $quantity = $('input[name="quantity"]');

            // Toggle between simple and variant product selection
            $itemType.on('change', function () {
                if ($(this).val() == 'simple') {
                    $simpleSection.show();
                    $variantSection.hide();
                    $currentStock.val('');
                    // Reset variant selects
                    $variantProductSelect.val('');
                    $variantSelect.html('<option value="">Select product first</option>');
                    $variantSelect.prop('disabled', true);
                } else {
                    $simpleSection.hide();
                    $variantSection.show();
                    $currentStock.val('');
                    // Reset simple select
                    $simpleProductSelect.val('');
                }
            });

            // Get stock info for simple product
            $simpleProductSelect.on('change', function () {
                const selectedOption = $(this).find('option:selected');
                const stock = selectedOption.data('stock');

                if (stock !== undefined) {
                    $currentStock.val(stock);
                } else {
                    $currentStock.val('N/A');
                }
            });

            // Load variants when product is selected in variant mode
            $variantProductSelect.on('change', function () {
                const productId = $(this).val();
                const selectedOption = $(this).find('option:selected');

                if (!productId) {
                    $variantSelect.html('<option value="">Select product first</option>');
                    $variantSelect.prop('disabled', true);
                    $currentStock.val('');
                    return;
                }

                // Show loading state
                $variantSelect.html('<option value="">Loading variants...</option>');
                $variantSelect.prop('disabled', true);
                $currentStock.val('');

                // Load variants via AJAX
                $.ajax({
                    url: "{{ route('inventory.getVariants') }}",
                    type: "POST",
                    data: {
                        product_id: productId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        console.log('Variants loaded:', response);
                        if (response.success) {
                            $variantSelect.html(response.options);
                            $variantSelect.prop('disabled', false);
                        } else {
                            $variantSelect.html('<option value="">No variants found for this product</option>');
                            $variantSelect.prop('disabled', true);
                        }
                    },
                    error: function (xhr) {
                        console.error('AJAX Error:', xhr);
                        $variantSelect.html('<option value="">Error loading variants. Please try again.</option>');
                        $variantSelect.prop('disabled', true);
                    }
                });
            });

            // Get stock info when variant is selected
            $variantSelect.on('change', function () {
                const variantId = $(this).val();
                const selectedOption = $(this).find('option:selected');
                const stock = selectedOption.data('stock');

                if (variantId && stock !== undefined) {
                    $currentStock.val(stock);
                } else if (variantId) {
                    // If stock data not in option, fetch via AJAX
                    $.ajax({
                        url: "{{ route('inventory.getStockInfo') }}",
                        type: "POST",
                        data: {
                            variant_id: variantId,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function (response) {
                            console.log('Stock info response:', response);
                            if (response.success) {
                                $currentStock.val(response.current_stock);
                            } else {
                                $currentStock.val('Stock info not available');
                            }
                        },
                        error: function (xhr) {
                            console.error('Error:', xhr);
                            $currentStock.val('Error loading stock info');
                        }
                    });
                } else {
                    $currentStock.val('');
                }
            });

            // Validate stock out doesn't exceed current stock
            $stockType.add($quantity).on('change keyup', function () {
                if ($stockType.val() == 'out') {
                    const currentStock = parseInt($currentStock.val()) || 0;
                    const quantity = parseInt($quantity.val()) || 0;

                    if (quantity > currentStock) {
                        alert(`Cannot remove ${quantity} items. Only ${currentStock} items available in stock.`);
                        $quantity.val('');
                    }
                }
            });
        });
    </script>
@endsection

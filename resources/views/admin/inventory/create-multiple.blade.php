@extends('layouts.master')

@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <a href="{{ route('inventory.index') }}" class="btn btn-dark btn-sm me-2">
                                    <i class="bx bx-arrow-back"></i> {{ __('admin.back') }}
                                </a>
                                <h4 class="mb-sm-0 font-size-18">{{ __('admin.inventoryArray.multiple_upload_inventory') }}
                                </h4>
                            </div>
                            <div class="d-none">
                                <button type="button" class="btn btn-info btn-sm" id="downloadSample">
                                    <i class="bx bx-download"></i> Download Sample CSV
                                </button>
                                <button type="button" class="btn btn-success btn-sm" id="importCSV">
                                    <i class="bx bx-upload"></i> Import CSV
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="card-title mb-0">{{ __('admin.inventoryArray.bulk_inventory_upload') }}</h5>
                                    <button type="button" class="btn btn-primary btn-sm" id="addMoreItem">
                                        <i class="bx bx-plus"></i> {{ __('admin.inventoryArray.add_more_item') }}
                                    </button>
                                </div>

                                <form method="POST" action="{{ route('inventory.store.multiple') }}" id="bulkInventoryForm">
                                    @csrf
                                    <div id="inventoryItemsContainer">
                                        <!-- Inventory Item Template -->
                                        <div class="inventory-item card mb-3" id="itemTemplate" style="display: none;">
                                            <div class="card-header bg-light">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-0">{{ __('admin.inventoryArray.item') }} <span
                                                            class="item-number"></span></h6>
                                                    <button type="button" class="btn btn-danger btn-sm remove-item">
                                                        <i class="bx bx-trash"></i> {{ __('admin.inventoryArray.remove') }}
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label
                                                                class="form-label">{{ __('admin.inventoryArray.select_product_type') }}</label>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input item-type" type="radio"
                                                                    name="inventory_items[__INDEX__][item_type]"
                                                                    value="simple" checked>
                                                                <label
                                                                    class="form-check-label">{{ __('admin.inventoryArray.simple_product') }}</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input item-type" type="radio"
                                                                    name="inventory_items[__INDEX__][item_type]"
                                                                    value="variant">
                                                                <label
                                                                    class="form-check-label">{{ __('admin.inventoryArray.variant_product') }}</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Simple Product Section -->
                                                <div class="simple-section">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label">{{ __('admin.inventoryArray.select_product') }}</label>
                                                                <select name="inventory_items[__INDEX__][product_id]"
                                                                    class="form-select simple-product-select">
                                                                    <option value="">
                                                                        {{ __('admin.inventoryArray.select_product_placeholder') }}
                                                                    </option>
                                                                    @foreach($simpleProducts as $product)
                                                                        <option value="{{ $product->id }}"
                                                                            data-stock="{{ $product->quantity }}"
                                                                            data-sku="{{ $product->sku_number }}">
                                                                            {{ $product->name }} (SKU:
                                                                            {{ $product->sku_number }}) - Stock:
                                                                            {{ $product->quantity }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Variant Product Section -->
                                                <div class="variant-section" style="display: none;">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label">{{ __('admin.inventoryArray.select_product') }}</label>
                                                                <select
                                                                    name="inventory_items[__INDEX__][variant_product_id]"
                                                                    class="form-select variant-product-select">
                                                                    <option value="">
                                                                        {{ __('admin.inventoryArray.select_product_placeholder') }}
                                                                    </option>
                                                                    @foreach($variantProducts as $product)
                                                                        <option value="{{ $product->id }}">
                                                                            {{ $product->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label">{{ __('admin.inventoryArray.select_variant') }}</label>
                                                                <select name="inventory_items[__INDEX__][variant_id]"
                                                                    class="form-select variant-select" disabled>
                                                                    <option value="">
                                                                        {{ __('admin.inventoryArray.select_product_first') }}
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="mb-3">
                                                            <label
                                                                class="form-label">{{ __('admin.inventoryArray.stock_type') }}</label>
                                                            <select name="inventory_items[__INDEX__][stock_type]"
                                                                class="form-select stock-type" required>
                                                                <option value="">
                                                                    {{ __('admin.inventoryArray.select_stock_type') }}
                                                                </option>
                                                                <option value="in">{{ __('admin.inventoryArray.stock_in') }}
                                                                </option>
                                                                <option value="out">
                                                                    {{ __('admin.inventoryArray.stock_out') }}
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="mb-3">
                                                            <label
                                                                class="form-label">{{ __('admin.inventoryArray.quantity') }}</label>
                                                            <input type="number" name="inventory_items[__INDEX__][quantity]"
                                                                class="form-control quantity" min="1" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="mb-3">
                                                            <label
                                                                class="form-label">{{ __('admin.inventoryArray.current_stock') }}</label>
                                                            <input type="text" class="form-control current-stock-display"
                                                                readonly disabled>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="mb-3">
                                                            <label
                                                                class="form-label">{{ __('admin.inventoryArray.sku') }}</label>
                                                            <input type="text" class="form-control sku-display" readonly
                                                                disabled>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="mb-3">
                                                            <label
                                                                class="form-label">{{ __('admin.inventoryArray.notes_optional') }}</label>
                                                            <textarea name="inventory_items[__INDEX__][notes]"
                                                                class="form-control" rows="2"
                                                                placeholder="{{ __('admin.inventoryArray.notes_placeholder') }}"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bx bx-save"></i> {{ __('admin.inventoryArray.submit_all') }}
                                            </button>
                                            <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
                                                {{ __('admin.cancel') }}
                                            </a>
                                        </div>
                                    </div>
                                </form>

                                <!-- CSV Import Modal -->
                                <div class="modal fade" id="csvImportModal" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ __('admin.inventoryArray.import_csv') }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form id="csvImportForm" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="mb-3">
                                                        <label
                                                            class="form-label">{{ __('admin.inventoryArray.select_csv_file') }}</label>
                                                        <input type="file" class="form-control" name="csv_file"
                                                            accept=".csv" required>
                                                        <small class="text-muted">Format:
                                                            product_type,product_id/variant_id,stock_type,quantity,notes</small>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label
                                                            class="form-label">{{ __('admin.inventoryArray.example_format') }}</label>
                                                        <pre class="bg-light p-2">simple,1,in,10,Restocking
                variant,5,out,2,Sold to customer</pre>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">{{ __('admin.close') }}</button>
                                                <button type="button" class="btn btn-primary"
                                                    id="processCSV">{{ __('admin.process') }}</button>
                                            </div>
                                        </div>
                                    </div>
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
            let itemCounter = 1;

            // Function to add new item
            function addInventoryItem(data = null) {
                const template = $('#itemTemplate').clone();
                template.removeAttr('id');
                template.removeAttr('style');

                const newHtml = template[0].outerHTML.replace(/__INDEX__/g, itemCounter);
                const $newItem = $(newHtml);

                // Update item number
                $newItem.find('.item-number').text(itemCounter + 1);

                // If data is provided (from CSV import), populate the fields
                if (data) {
                    const itemType = data.product_type;
                    $newItem.find(`input.item-type[value="${itemType}"]`).prop('checked', true);

                    if (itemType === 'simple') {
                        $newItem.find('.simple-section').show();
                        $newItem.find('.variant-section').hide();
                        $newItem.find('.simple-product-select').val(data.product_id).trigger('change');
                    } else {
                        $newItem.find('.simple-section').hide();
                        $newItem.find('.variant-section').show();
                        $newItem.find('.variant-product-select').val(data.product_id).trigger('change');

                        // After variants load, set the variant
                        setTimeout(() => {
                            $newItem.find('.variant-select').val(data.variant_id).trigger('change');
                        }, 500);
                    }

                    $newItem.find('.stock-type').val(data.stock_type);
                    $newItem.find('.quantity').val(data.quantity);
                    $newItem.find('textarea[name*="[notes]"]').val(data.notes || '');
                }

                // Add event handlers
                attachItemEventHandlers($newItem);

                $('#inventoryItemsContainer').append($newItem);
                itemCounter++;
            }

            // Function to attach event handlers to an item
            function attachItemEventHandlers($item) {
                // Remove item button
                $item.find('.remove-item').on('click', function () {
                    if ($('#inventoryItemsContainer .inventory-item').length > 1) {
                        $item.remove();
                        updateItemNumbers();
                    } else {
                        alert('At least one inventory item is required');
                    }
                });

                // Toggle between simple and variant product selection
                $item.find('.item-type').on('change', function () {
                    const $thisItem = $(this).closest('.inventory-item');
                    if ($(this).val() == 'simple') {
                        $thisItem.find('.simple-section').show();
                        $thisItem.find('.variant-section').hide();
                        $thisItem.find('.current-stock-display').val('');
                        $thisItem.find('.sku-display').val('');
                    } else {
                        $thisItem.find('.simple-section').hide();
                        $thisItem.find('.variant-section').show();
                        $thisItem.find('.current-stock-display').val('');
                        $thisItem.find('.sku-display').val('');
                    }
                });

                // Simple product selection change
                $item.find('.simple-product-select').on('change', function () {
                    const $thisItem = $(this).closest('.inventory-item');
                    const selectedOption = $(this).find('option:selected');
                    const stock = selectedOption.data('stock');
                    const sku = selectedOption.data('sku');

                    $thisItem.find('.current-stock-display').val(stock !== undefined ? stock : 'N/A');
                    $thisItem.find('.sku-display').val(sku !== undefined ? sku : 'N/A');
                });

                // Variant product selection change (load variants)
                $item.find('.variant-product-select').on('change', function () {
                    const $thisItem = $(this).closest('.inventory-item');
                    const productId = $(this).val();
                    const $variantSelect = $thisItem.find('.variant-select');

                    if (!productId) {
                        $variantSelect.html('<option value="">Select product first</option>');
                        $variantSelect.prop('disabled', true);
                        $thisItem.find('.current-stock-display').val('');
                        $thisItem.find('.sku-display').val('');
                        return;
                    }

                    $variantSelect.html('<option value="">Loading variants...</option>');
                    $variantSelect.prop('disabled', true);

                    $.ajax({
                        url: "{{ route('inventory.getVariants') }}",
                        type: "POST",
                        data: {
                            product_id: productId,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function (response) {
                            if (response.success) {
                                $variantSelect.html(response.options);
                                $variantSelect.prop('disabled', false);
                            } else {
                                $variantSelect.html('<option value="">No variants found</option>');
                                $variantSelect.prop('disabled', true);
                            }
                        },
                        error: function () {
                            $variantSelect.html('<option value="">Error loading variants</option>');
                            $variantSelect.prop('disabled', true);
                        }
                    });
                });

                // Variant selection change
                $item.find('.variant-select').on('change', function () {
                    const $thisItem = $(this).closest('.inventory-item');
                    const variantId = $(this).val();
                    const selectedOption = $(this).find('option:selected');
                    const stock = selectedOption.data('stock');

                    if (variantId && stock !== undefined) {
                        $thisItem.find('.current-stock-display').val(stock);
                        $thisItem.find('.sku-display').val(selectedOption.text().split(' - ')[0]);
                    } else if (variantId) {
                        $.ajax({
                            url: "{{ route('inventory.getVariantDetails') }}",
                            type: "POST",
                            data: {
                                variant_id: variantId,
                                _token: "{{ csrf_token() }}"
                            },
                            success: function (response) {
                                if (response.success) {
                                    $thisItem.find('.current-stock-display').val(response.variant.current_stock);
                                    $thisItem.find('.sku-display').val(response.variant.sku);
                                }
                            }
                        });
                    } else {
                        $thisItem.find('.current-stock-display').val('');
                        $thisItem.find('.sku-display').val('');
                    }
                });

                // Validate stock out
                $item.find('.stock-type, .quantity').on('change keyup', function () {
                    const $thisItem = $(this).closest('.inventory-item');
                    const stockType = $thisItem.find('.stock-type').val();
                    const currentStock = parseInt($thisItem.find('.current-stock-display').val()) || 0;
                    const quantity = parseInt($thisItem.find('.quantity').val()) || 0;

                    if (stockType == 'out' && quantity > currentStock) {
                        alert(`Cannot remove ${quantity} items. Only ${currentStock} items available.`);
                        $thisItem.find('.quantity').val('');
                    }
                });
            }

            // Update item numbers
            function updateItemNumbers() {
                $('#inventoryItemsContainer .inventory-item').each(function (index) {
                    $(this).find('.item-number').text(index + 1);
                    // Update name attributes
                    $(this).find('[name*="inventory_items"]').each(function () {
                        const name = $(this).attr('name');
                        const newName = name.replace(/inventory_items\[\d+\]/, `inventory_items[${index}]`);
                        $(this).attr('name', newName);
                    });
                });
                itemCounter = $('#inventoryItemsContainer .inventory-item').length;
            }

            // Add first item by default
            addInventoryItem();

            // Add more item button
            $('#addMoreItem').on('click', function () {
                addInventoryItem();
            });

            // Download sample CSV
            $('#downloadSample').on('click', function () {
                const sampleData = [
                    ['product_type', 'product_id', 'variant_id', 'stock_type', 'quantity', 'notes'],
                    ['simple', '1', '', 'in', '10', 'Restocking sample product'],
                    ['variant', '5', '12', 'out', '2', 'Sold to customer'],
                    ['simple', '3', '', 'out', '1', 'Damaged product']
                ];

                let csvContent = sampleData.map(row => row.join(',')).join('\n');
                const blob = new Blob([csvContent], { type: 'text/csv' });
                const link = document.createElement('a');
                const url = URL.createObjectURL(blob);
                link.href = url;
                link.download = 'inventory_sample.csv';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                URL.revokeObjectURL(url);
            });

            // Import CSV modal
            $('#importCSV').on('click', function () {
                $('#csvImportModal').modal('show');
            });

            // Process CSV import
            $('#processCSV').on('click', function () {
                const formData = new FormData($('#csvImportForm')[0]);

                $.ajax({
                    url: "{{ route('inventory.parseCsv') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success) {
                            // Remove existing items except first
                            $('#inventoryItemsContainer .inventory-item').remove();
                            itemCounter = 0;

                            // Add items from CSV
                            response.data.forEach(item => {
                                addInventoryItem(item);
                            });

                            $('#csvImportModal').modal('hide');
                            toastr.success(`Imported ${response.data.length} items successfully`);
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function (xhr) {
                        alert('Error parsing CSV file');
                    }
                });
            });

            // Form submission validation
            $('#bulkInventoryForm').on('submit', function (e) {
                let isValid = true;

                $('#inventoryItemsContainer .inventory-item').each(function (index) {
                    const $item = $(this);
                    const stockType = $item.find('.stock-type').val();
                    const quantity = $item.find('.quantity').val();
                    const currentStock = parseInt($item.find('.current-stock-display').val()) || 0;

                    if (!stockType) {
                        alert(`Item ${index + 1}: Please select stock type`);
                        isValid = false;
                        return false;
                    }

                    if (!quantity || quantity < 1) {
                        alert(`Item ${index + 1}: Please enter valid quantity`);
                        isValid = false;
                        return false;
                    }

                    if (stockType === 'out' && quantity > currentStock) {
                        alert(`Item ${index + 1}: Insufficient stock. Cannot remove ${quantity} items. Only ${currentStock} available.`);
                        isValid = false;
                        return false;
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                }
            });
        });
    </script>
@endsection

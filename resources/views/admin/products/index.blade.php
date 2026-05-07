@extends('layouts.master')

@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <!-- start page title -->
                <div class="row">
                    <div class="col-4">
                        <div class="page-title-box d-flex align-items-center">
                            <h4 class="mb-sm-0 font-size-18">{{ __('admin.products') }}</h4>
                        </div>
                    </div>
                    <div class="col-8">
                        <div class="d-flex justify-content-end flex-wrap gap-2">
                            <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm">
                                <i class="bx bx-plus"></i> {{ __('admin.add_product') }}
                            </a>
                            <!-- <a href="{{ route('export', ['page' => 'category']) }}" class="btn btn-success btn-sm">
                                        <i class="bx bx-download"></i> {{ __('admin.export_category') }}
                                    </a> -->
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <form class="row my-2" action="{{ route('products.index') }}" method="get" id="form-search">
                            @csrf
                            <h5 class="card-title">{{ __('admin.filter_products') }}</h5>
                            <div class="row">
                                <div class="col-sm-4">
                                    <input class="form-control" id="keyword" value="" name="keyword" type="text"
                                        placeholder="{{ __('admin.search_here') }}" aria-label="search here...">
                                </div>
                                <div class="col-sm-3">
                                    <div class="input-group">
                                        <div class="input-group-text">{{ __('admin.status') }}</div>
                                        <select name="status" class="form-select">
                                            <option value="" selected>{{ __('admin.all') }}</option>
                                            <option value="1">{{ __('admin.active') }}</option>
                                            <option value="0">{{ __('admin.inactive') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="input-group">
                                        <div class="input-group-text">{{ __('admin.per_page') }}</div>
                                        <select name="perPage" id="perPage" class="form-select">
                                            <option value="10" selected>10</option>
                                            <option value="20">20</option>
                                            <option value="30">30</option>
                                            <option value="40">40</option>
                                            <option value="50">50</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <button type="submit" class="btn btn-secondary mx-2">{{ __('admin.submit') }}</button>
                                    <a href="{{ route('products.index') }}"
                                        class="btn btn-danger">{{ __('admin.reset') }}</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body" id="pagination" data-url="{!! $url !!}">
                                @include('admin.products.pagination')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.inventory.modal')
@endsection

@section('js')
    <script>
        $(document).ready(function () {
            let currentStock = 0;
            let currentItemType = '';
            let currentProductId = '';
            let currentVariantId = '';

            const $modal = $('#manageInventoryModal');
            const $available = $('#available_inventory');
            const $currentDisplay = $('#current_display');
            const $updated = $('#updated_inventory');
            const $changeDisplay = $('#change_display');
            const $qty = $('#stock_qty');
            const $type = $('#stock_type');
            const $submitBtn = $('#updateStockBtn');
            const $productNameSpan = $('#product_name');
            const $skuDisplay = $('#sku_display');

            // When modal opens
            $modal.on('show.bs.modal', function (e) {
                const button = $(e.relatedTarget);

                // Get data attributes
                currentItemType = button.data('item-type');
                currentProductId = button.data('product-id');
                currentVariantId = button.data('variant-id');
                const stock = parseInt(button.data('stock')) || 0;
                const sku = button.data('sku') || '';
                const productName = button.data('product-name') || '';

                // Set form values
                $('#item_type').val(currentItemType);

                if (currentItemType === 'simple') {
                    $('#product_id').val(currentProductId);
                    $('#variant_id').val('');
                } else {
                    $('#product_id').val('');
                    $('#variant_id').val(currentVariantId);
                }

                // Set display values
                currentStock = stock;

                $productNameSpan.text(productName);
                $skuDisplay.text(sku);
                $available.text(numberFormat(currentStock));
                $currentDisplay.text(numberFormat(currentStock));
                $updated.text(numberFormat(currentStock));
                $changeDisplay.text('0');

                // Reset form
                $qty.val('');
                $type.val('in');
                $submitBtn.prop('disabled', false);

                // Clear any previous error states
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();

                // Focus on quantity input
                setTimeout(() => {
                    $qty.focus();
                }, 500);
            });

            // Function to format numbers
            function numberFormat(num) {
                return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }

            // Calculate stock changes in real-time
            function calculateStock() {
                const qty = parseInt($qty.val()) || 0;
                const type = $type.val();
                let updatedStock = currentStock;
                let change = 0;

                if (type === 'in') {
                    updatedStock = currentStock + qty;
                    change = qty;
                } else {
                    updatedStock = currentStock - qty;
                    change = -qty;
                }

                // Update display with formatted numbers
                $updated.text(numberFormat(updatedStock));
                $changeDisplay.text(change > 0 ? '+' + numberFormat(change) : numberFormat(change));

                // Change color based on stock change
                if (change > 0) {
                    $changeDisplay.css('color', 'green');
                } else if (change < 0) {
                    $changeDisplay.css('color', 'red');
                } else {
                    $changeDisplay.css('color', 'black');
                }

                // Validate stock out
                let isValid = true;
                let errorMessage = '';

                if (type === 'out') {
                    if (updatedStock < 0) {
                        isValid = false;
                        errorMessage = '{{ __("admin.cannot_remove_more_than_available") }}';
                    } else if (qty > currentStock) {
                        isValid = false;
                        errorMessage = `{{ __("admin.insufficient_stock") }} Available: ${numberFormat(currentStock)}`;
                    }
                }

                // Enable/disable submit button
                $submitBtn.prop('disabled', !isValid || qty === 0);

                // Show/hide error message
                let $errorDiv = $qty.closest('.col-lg-6').find('.invalid-feedback');
                if (!isValid) {
                    $qty.addClass('is-invalid');
                    if ($errorDiv.length === 0) {
                        $qty.after(`<div class="invalid-feedback">${errorMessage}</div>`);
                    } else {
                        $errorDiv.text(errorMessage);
                    }
                } else {
                    $qty.removeClass('is-invalid');
                    $errorDiv.remove();
                }

                return isValid;
            }

            // Form submission handler
            $('#inventory-form').on('submit', function (e) {
                e.preventDefault();

                // Validate before submit
                if (!calculateStock()) {
                    return false;
                }

                const $form = $(this);
                const $submitButton = $form.find('button[type="submit"]');
                const originalText = $submitButton.html();

                // Disable button and show loading
                $submitButton.prop('disabled', true);
                $submitButton.html('<i class="bx bx-loader-alt bx-spin"></i> {{ __("admin.processing") }}...');

                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: $form.serialize(),
                    success: function (response) {
                        if (response.success) {
                            // Show success message
                            if (typeof toastr !== 'undefined') {
                                toastr.success(response.message);
                            } else {
                                alert(response.message);
                            }

                            // Update the stock display on the product list page
                            if (currentItemType === 'simple') {
                                // Update simple product stock badge
                                const $stockBadge = $(`.product-stock-badge[data-product-id="${currentProductId}"]`);
                                if ($stockBadge.length) {
                                    const updatedStock = response.updated_stock;
                                    const stockClass = updatedStock <= 0 ? 'danger' : (updatedStock <= 10 ? 'warning' : 'success');
                                    $stockBadge
                                        .removeClass('bg-danger bg-warning bg-success')
                                        .addClass('bg-' + stockClass)
                                        .text(numberFormat(updatedStock));
                                }
                            } else {
                                // Update variant stock in the product row
                                // Find the product row that contains this variant
                                const $variantButton = $(`button[data-variant-id="${currentVariantId}"]`);
                                if ($variantButton.length) {
                                    // Update the button text with new stock
                                    const newStockText = $variantButton.text().replace(/Stock: \d+/, `Stock: ${response.updated_stock}`);
                                    $variantButton.text(newStockText);
                                    $variantButton.data('stock', response.updated_stock);

                                    // Update the product total stock badge
                                    let totalStock = 0;
                                    const $productRow = $(`#product-row-${currentProductId}`);
                                    $productRow.find('.dropdown-item').each(function () {
                                        const stock = $(this).data('stock');
                                        if (stock) totalStock += parseInt(stock);
                                    });

                                    const $stockBadge = $productRow.find('.product-stock-badge');
                                    const stockClass = totalStock <= 0 ? 'danger' : (totalStock <= 10 ? 'warning' : 'success');
                                    $stockBadge
                                        .removeClass('bg-danger bg-warning bg-success')
                                        .addClass('bg-' + stockClass)
                                        .text(numberFormat(totalStock));
                                }
                            }

                            // Close modal
                            $modal.modal('hide');

                            // Optional: Update the row style if needed
                            setTimeout(() => {
                                // You can add additional UI updates here
                                location.reload(); // Uncomment if you want to reload the page
                            }, 1500);
                        } else {
                            if (typeof toastr !== 'undefined') {
                                toastr.error(response.message);
                            } else {
                                alert(response.message);
                            }
                        }
                    },
                    error: function (xhr) {
                        let errorMessage = '{{ __("admin.error_updating_inventory") }}';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        if (typeof toastr !== 'undefined') {
                            toastr.error(errorMessage);
                        } else {
                            alert(errorMessage);
                        }
                    },
                    complete: function () {
                        $submitButton.prop('disabled', false);
                        $submitButton.html(originalText);
                    }
                });
            });

            // Real-time calculation events
            $qty.on('input', calculateStock);
            $type.on('change', calculateStock);

            // Prevent negative quantity
            $qty.on('keyup', function () {
                if ($(this).val() < 1) {
                    $(this).val('');
                }
            });

            // Add keyboard shortcut (Enter key to submit)
            $qty.on('keypress', function (e) {
                if (e.which === 13 && !$submitBtn.prop('disabled')) {
                    e.preventDefault();
                    $('#inventory-form').submit();
                }
            });
        });
    </script>
@endsection
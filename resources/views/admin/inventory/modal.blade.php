<div class="modal fade" id="manageInventoryModal" tabindex="-1" aria-labelledby="manageInventoryModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="manageInventoryModalLabel">
                    <i class="bx bx-package"></i> {{ __('admin.manage_inventory') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Product/Variant Info -->
                <div class="alert alert-info">
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ __('admin.product') }}:</span>
                        <strong id="product_name" class="text-end"></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ __('admin.sku') }}:</span>
                        <strong id="sku_display"></strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>{{ __('admin.available_inventory') }}:</span>
                        <strong id="available_inventory" class="text-success">0</strong>
                    </div>
                </div>

                <form id="inventory-form" action="{{ route('update-inventory') }}" autocomplete="off" method="post">
                    @csrf
                    <input type="hidden" name="product_id" id="product_id" value="0">
                    <input type="hidden" name="variant_id" id="variant_id" value="0">
                    <input type="hidden" name="item_type" id="item_type" value="simple">

                    <div class="row">
                        <!-- Stock Type -->
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">{{ __('admin.stock_action') }}</label>
                            <select class="form-select" name="stock_type" id="stock_type" required>
                                <option value="in">{{ __('admin.stock_in') }} (+)</option>
                                <option value="out">{{ __('admin.stock_out') }} (-)</option>
                            </select>
                        </div>

                        <!-- Quantity -->
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">{{ __('admin.quantity') }}</label>
                            <input type="number" min="1" class="form-control" id="stock_qty"
                                placeholder="{{ __('admin.enter_quantity') }}" name="quantity" required>
                        </div>
                    </div>

                    <!-- Live Result -->
                    <div class="alert alert-secondary">
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('admin.current_stock') }}:</span>
                            <strong id="current_display">0</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('admin.after_update') }}:</span>
                            <strong id="updated_inventory" class="text-primary">0</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>{{ __('admin.change') }}:</span>
                            <strong id="change_display">0</strong>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            {{ __('admin.cancel') }}
                        </button>
                        <button type="submit" id="updateStockBtn" class="btn btn-success">
                            <i class="bx bx-check"></i> {{ __('admin.update_inventory') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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

                        // Update the stock display on the page
                        if (currentItemType === 'simple') {
                            // Update simple product stock
                            const $stockElement = $(`#product-stock-${currentProductId}`);
                            if ($stockElement.length) {
                                $stockElement.text(response.updated_stock);
                            }

                            // Also update the stock in the header
                            $('.text-success.text-uppercase').find('span').first().text(response.updated_stock);
                        } else {
                            // Update variant stock in the table
                            const $variantRow = $(`#variant-row-${currentVariantId}`);
                            if ($variantRow.length) {
                                const stockClass = response.updated_stock <= 0 ? 'danger' : (response.updated_stock <= 10 ? 'warning' : 'success');
                                $variantRow.find('.variant-stock')
                                    .removeClass('bg-danger bg-warning bg-success')
                                    .addClass('bg-' + stockClass)
                                    .text(numberFormat(response.updated_stock));
                            }

                            // Update total stock for variant products
                            let totalStock = 0;
                            $('.variant-stock').each(function () {
                                totalStock += parseInt($(this).data('stock')) || 0;
                            });
                            $('#total-stock').text(numberFormat(totalStock));
                        }

                        // Close modal
                        $modal.modal('hide');

                        // Reload the page after 1.5 seconds to show updated inventory list
                        setTimeout(() => {
                            location.reload();
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

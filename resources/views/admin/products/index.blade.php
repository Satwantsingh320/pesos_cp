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
    <!------ Manage Stock Modal --------------->
    <div class="modal fade" id="manageInventoryModal" tabindex="-1" aria-labelledby="newOrderModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newOrderModalLabel">{{ __('admin.manage_inventory') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class=" badge bg-success p-2" style="font-size:13px;">
                        {{ __('admin.available_inventory') }} :
                        <strong>
                            <span id="available_inventory">0</span>
                        </strong>
                    </div>
                    <form id="inventory-form" action="{{ route('update-inventory') }}" autocomplete="off" method="post">
                        @csrf
                        <input type="hidden" id="current_stock" value="0">
                        <input type="hidden" name="product_id" id="product_id" value="0">

                        <div class="row mt-3">
                            <!-- Stock Type -->
                            <div class="col-lg-6">
                                <label class="form-label">{{ __('admin.stock_action') }}</label>
                                <select class="form-select" name="stock_type" id="stock_type" required>
                                    <option value="in">{{ __('admin.stock_in') }}</option>
                                    <option value="out">{{ __('admin.stock_out') }}</option>
                                </select>
                            </div>

                            <!-- Quantity -->
                            <div class="col-lg-6">
                                <label class="form-label">{{ __('admin.quantity') }}</label>
                                <input type="number" min="1" class="form-control" id="stock_qty"
                                    placeholder="{!! __('admin.enter_quantity') !!}" name="quantity" required>
                            </div>
                        </div>

                        <!-- Live Result -->
                        <div class="mt-3">
                            <span class="badge bg-info p-2" style="font-size:13px;">
                                {{ __('admin.updated_inventory') }} :
                                <strong>
                                    <span id="updated_inventory">0</span>
                                </strong>
                            </span>
                        </div>

                        <div class="text-end mt-4">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                {{ __('admin.cancel') }}
                            </button>
                            <button type="submit" id="updateStockBtn" class="btn btn-success">
                                {{ __('admin.update_inventory') }}
                            </button>
                        </div>
                    </form>

                </div>
                <!-- end modal body -->
            </div>
            <!-- end modal-content -->
        </div>
        <!-- end modal-dialog -->
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {

            let currentStock = 0;

            const $modal = $('#manageInventoryModal');
            const $available = $('#available_inventory');
            const $updated = $('#updated_inventory');
            const $qty = $('#stock_qty');
            const $type = $('#stock_type');
            const $submitBtn = $('#updateStockBtn');

            // When modal opens
            $modal.on('show.bs.modal', function(e) {

                const button = $(e.relatedTarget);
                $('#product_id').val(button.data('product-id'));
                currentStock = parseInt(button.data('stock')) || 0;

                $available.text(currentStock);
                $updated.text(currentStock);

                $qty.val('');
                $type.val('in');
                $submitBtn.prop('disabled', false);
            });

            // Calculate stock
            function calculateStock() {
                const qty = parseInt($qty.val()) || 0;
                const type = $type.val();
                let updatedStock = currentStock;

                if (type === 'in') {
                    updatedStock = currentStock + qty;
                } else {
                    updatedStock = currentStock - qty;
                }

                $updated.text(updatedStock);

                // Prevent negative stock
                $submitBtn.prop('disabled', updatedStock < 0);
            }

            // Events
            $qty.on('input', calculateStock);
            $type.on('change', calculateStock);

        });
    </script>
@endsection

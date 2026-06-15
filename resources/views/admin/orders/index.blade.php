@extends('layouts.master')
@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <!-- start page title -->
                <div class="row">
                    <div class="col-4">
                        <div class="page-title-box d-flex align-items-center">
                            <h4 class="mb-sm-0 font-size-18">{{ __('admin.orders') }}</h4>
                        </div>
                    </div>
                    <div class="col-8">
                        <div class="d-flex justify-content-end flex-wrap gap-2">
                            <!-- <a href="{{ route('category.create') }}" class="btn btn-primary btn-sm">
                                                                        <i class="bx bx-plus"></i> {{ __('admin.create_order') }}
                                                                    </a> -->
                            <!-- <a href="{{ route('export',['page' => 'category']) }}" class="btn btn-success btn-sm">
                                                                        <i class="bx bx-download"></i> {{ __('admin.export_category') }}
                                                                    </a> -->
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <form action="{{ route('orders.index') }}" method="get" class="mb-3">
                            <h5 class="card-title mb-3">{{ __('admin.filter_orders') }}</h5>

                            <div class="row g-3 align-items-end">

                                <!-- Search -->
                                <div class="col-lg-4 col-md-6">
                                    <label class="form-label">{{ __('admin.search') }}</label>
                                    <input type="text" name="keyword" class="form-control"
                                        placeholder="{{ __('admin.search_here') }}" value="{{ request('keyword') }}">
                                </div>

                                <!-- Status -->
                                <div class="col-lg-2 col-md-6">
                                    <label class="form-label">{{ __('admin.status') }}</label>
                                    <select name="status" class="form-select">
                                        <option value="">{{ __('admin.all') }}</option>
                                        <option value="0" @selected(request('status') === '0')>{{ __('admin.pending') }}
                                        </option>
                                        <option value="1" @selected(request('status') === '1')>{{ __('admin.ordered') }}
                                        </option>
                                        <option value="2" @selected(request('status') === '2')>{{ __('admin.shipped') }}
                                        </option>
                                        <option value="3" @selected(request('status') === '3')>{{ __('admin.delivered') }}
                                        </option>
                                        <option value="4" @selected(request('status') === '4')>{{ __('admin.cancelled') }}
                                        </option>
                                        <option value="5" @selected(request('status') === '5')>{{ __('admin.returned') }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Start Date -->
                                <div class="col-lg-2 col-md-6">
                                    <label class="form-label">{{ __('admin.start_date') }}</label>
                                    <input type="date" name="start_date" class="form-control"
                                        value="{{ request('start_date') }}">
                                </div>

                                <!-- End Date -->
                                <div class="col-lg-2 col-md-6">
                                    <label class="form-label">{{ __('admin.end_date') }}</label>
                                    <input type="date" name="end_date" class="form-control"
                                        value="{{ request('end_date') }}">
                                </div>

                                <!-- Per Page -->
                                <div class="col-lg-1 col-md-6">
                                    <label class="form-label">{{ __('admin.per_page') }}</label>
                                    <select name="perPage" id="perPage" class="form-select">
                                        <option value="10" selected>10</option>
                                        <option value="20">20</option>
                                        <option value="30">30</option>
                                        <option value="40">40</option>
                                        <option value="50">50</option>
                                    </select>
                                </div>

                                <!-- Buttons -->
                                <div class="col-lg-3 col-md-12 d-flex gap-2">
                                    <button type="submit" class="btn btn-secondary w-100">
                                        {{ __('admin.submit') }}
                                    </button>
                                    <a href="{{ route('orders.index') }}" class="btn btn-danger w-100">
                                        {{ __('admin.reset') }}
                                    </a>
                                </div>

                            </div>
                        </form>

                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body" id="pagination" data-url="{!! $url !!}">
                                @include('admin.orders.pagination')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="trackingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="trackingForm">
                    @csrf
                    <input type="hidden" name="order_id" id="modal_order_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Tracking Information</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Courier Name</label>
                            <input type="text" name="carrier" class="form-control" required placeholder="e.g. FedEx, DHL">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tracking Number</label>
                            <input type="text" name="tracking_number" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            aria-label="Close">Close</button>
                        <button type="submit" class="btn btn-primary" id="saveTrackingBtn">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        function updateOrderStatus(orderId, status) {

            if (!orderId || status === '') return;

            fetch("{{ route('orders.update-order-status') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    order_id: orderId,
                    order_status: status
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastr.success(data.message);
                    } else {
                        alert("Failed to update status");
                    }
                })
                .catch(error => {
                    console.error(error);
                    alert("Something went wrong");
                });
        }
        $(document).ready(function () {
            // Pass Order ID to Modal
            $(document).on('click', '.add-tracking-btn', function () {
                // Get the ID from the data-id attribute
                const orderId = $(this).attr('data-id') || $(this).data('id');
                // Set the value in the hidden input
                $('#modal_order_id').val(orderId);
                // Manually trigger the modal if data-bs-toggle is missing
                const myModal = new bootstrap.Modal(document.getElementById('trackingModal'));
                myModal.show();
            });

            // Handle AJAX Submission
            $('#trackingForm').on('submit', function (e) {
                e.preventDefault();

                let formData = $(this).serialize();
                let submitBtn = $('#saveTrackingBtn');

                submitBtn.prop('disabled', true).text('Saving...');

                $.ajax({
                    url: "{{ route('orders.updateTracking') }}",
                    method: "POST",
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            location.reload(); // Reload to update the table UI
                        }
                    },
                    error: function (xhr) {
                        console.log(xhr);
                        alert('Something went wrong. Please try again.');
                        submitBtn.prop('disabled', false).text('Save Changes');
                    }
                });
            });
        });
        $('#trackingModal').on('hidden.bs.modal', function () {
            $('body').removeClass('modal-open');
            $('.modal-backdrop').remove();
        });
    </script>
@endsection
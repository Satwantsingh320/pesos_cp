@extends('layouts.master')
@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                <!-- start page title -->
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                            <h4 class="mb-sm-0 font-size-18">{{__('admin.dashboard')}}</h4>
                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item active">{{__('admin.dashboard')}}</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end page title -->

                <div class="row">
                    <div class="col-xl-12">
                        <!-- First Row - Order Statistics -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card mini-stats-wid">
                                    <a href="{{route('orders.index', ['status' => 0])}}">
                                        <div class="card-body">
                                            <div class="d-flex">
                                                <div class="flex-grow-1">
                                                    <p class="text-muted fw-medium">{!! __('admin.total_pending_orders') !!}
                                                    </p>
                                                    <h4 class="mb-0">{{$total_pending_orders}}</h4>
                                                </div>
                                                <div class="flex-shrink-0 align-self-center">
                                                    <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                                        <span class="avatar-title">
                                                            <i class="bx bx-copy-alt font-size-24"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card mini-stats-wid">
                                    <a href="{{route('orders.index', ['status' => [1, 2]])}}">
                                        <div class="card-body">
                                            <div class="d-flex">
                                                <div class="flex-grow-1">
                                                    <p class="text-muted fw-medium">
                                                        {!! __('admin.total_orders_in_process') !!}</p>
                                                    <h4 class="mb-0">{{$total_in_process_orders}}</h4>
                                                </div>
                                                <div class="flex-shrink-0 align-self-center">
                                                    <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                                        <span class="avatar-title">
                                                            <i class="bx bx-workflow-alt font-size-24"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card mini-stats-wid">
                                    <a href="{{route('orders.index', ['status' => 3])}}">
                                        <div class="card-body">
                                            <div class="d-flex">
                                                <div class="flex-grow-1">
                                                    <p class="text-muted fw-medium">
                                                        {!! __('admin.total_orders_delivered') !!}</p>
                                                    <h4 class="mb-0">{{$total_delivered_orders}}</h4>
                                                </div>
                                                <div class="flex-shrink-0 align-self-center">
                                                    <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                                        <span class="avatar-title">
                                                            <i class="bx bx-check-circle font-size-24"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Second Row - Product & Stock Statistics -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card mini-stats-wid">
                                    <a href="{{route('products.index')}}">
                                        <div class="card-body">
                                            <div class="d-flex">
                                                <div class="flex-grow-1">
                                                    <p class="text-muted fw-medium">{!! __('admin.total_products') !!}</p>
                                                    <h4 class="mb-0">{{$total_products}}</h4>
                                                </div>
                                                <div class="flex-shrink-0 align-self-center">
                                                    <div class="avatar-sm rounded-circle bg-primary mini-stat-icon">
                                                        <span class="avatar-title rounded-circle bg-primary">
                                                            <i class="bx bx-purchase-tag-alt font-size-24"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            @if(isset($total_variants))
                                <div class="col-md-4">
                                    <div class="card mini-stats-wid">
                                        <div class="card-body">
                                            <div class="d-flex">
                                                <div class="flex-grow-1">
                                                    <p class="text-muted fw-medium">Total Variants</p>
                                                    <h4 class="mb-0">{{$total_variants}}</h4>
                                                    <small class="text-muted">Across all products</small>
                                                </div>
                                                <div class="flex-shrink-0 align-self-center">
                                                    <div class="avatar-sm rounded-circle bg-info mini-stat-icon">
                                                        <span class="avatar-title rounded-circle bg-info">
                                                            <i class="bx bx-grid-alt font-size-24"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if(isset($total_out_of_stock_variants))
                                <div class="col-md-4">
                                    <div class="card mini-stats-wid">
                                        <div class="card-body">
                                            <div class="d-flex">
                                                <div class="flex-grow-1">
                                                    <p class="text-muted fw-medium">{!! __('admin.Out of Stock Variants') !!}</p>
                                                    <h4 class="mb-0 text-danger">{{$total_out_of_stock_variants}}</h4>
                                                    <small class="text-muted">{!! __('admin.Need restocking') !!}</small>
                                                </div>
                                                <div class="flex-shrink-0 align-self-center">
                                                    <div class="avatar-sm rounded-circle bg-danger mini-stat-icon">
                                                        <span class="avatar-title rounded-circle bg-danger">
                                                            <i class="bx bx-error-circle font-size-24"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Third Row - Stock Movement -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card mini-stats-wid">
                                    <div class="card-body">
                                        <div class="d-flex">
                                            <div class="flex-grow-1">
                                                <p class="text-muted fw-medium">{!! __('admin.stock_in_today') !!}</p>
                                                <h4 class="mb-0 text-success">{{$total_stock_in_today}}</h4>
                                            </div>
                                            <div class="flex-shrink-0 align-self-center">
                                                <div class="avatar-sm rounded-circle bg-success mini-stat-icon">
                                                    <span class="avatar-title rounded-circle bg-success">
                                                        <i class="bx bx-arrow-in-down-right-square font-size-24"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card mini-stats-wid">
                                    <div class="card-body">
                                        <div class="d-flex">
                                            <div class="flex-grow-1">
                                                <p class="text-muted fw-medium">{!! __('admin.stock_out_today') !!}</p>
                                                <h4 class="mb-0 text-warning">{{$total_stock_out_today}}</h4>
                                            </div>
                                            <div class="flex-shrink-0 align-self-center">
                                                <div class="avatar-sm rounded-circle bg-warning mini-stat-icon">
                                                    <span class="avatar-title rounded-circle bg-warning">
                                                        <i class="bx bx-arrow-out-up-left-square font-size-24"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end row -->

                        <!-- Orders Chart Card -->
                        <div class="card shadow-sm border-0 rounded-4 mb-4">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                                    <h5 class="fw-semibold mb-2">{!! __('admin.orders') !!}</h5>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-light filter-btn"
                                            data-filter="weekly">{!! __('admin.weekly') !!}</button>
                                        <button class="btn btn-sm btn-light filter-btn"
                                            data-filter="monthly">{!! __('admin.monthly') !!}</button>
                                        <button class="btn btn-sm btn-primary active filter-btn"
                                            data-filter="yearly">{!! __('admin.yearly') !!}</button>
                                    </div>
                                </div>
                                <div style="height:300px">
                                    <canvas id="ordersChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Inventory Chart Card -->
                        <div class="card shadow-sm border-0 rounded-4">
                            <div class="card-body p-4">
                                <h5 class="fw-semibold mb-3">{{ __('Inventory Status by Category')}}</h5>
                                <div style="height:350px">
                                    <canvas id="inventoryChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end row -->

            </div>
            <!-- container-fluid -->
        </div>
        <!-- End Page-content -->

    </div>
    <!-- end main content-->
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        /* ===========================
           ORDERS BAR CHART
        =========================== */
        let ordersChart = null;

        function loadOrdersChart(filter = 'yearly') {
            $.get("{{ url('/admin/dashboard/orders-graph') }}", { filter }, function (res) {
                if (ordersChart) {
                    ordersChart.destroy();
                }

                const ctx = document.getElementById('ordersChart').getContext('2d');

                ordersChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: res.labels,
                        datasets: [{
                            label: 'Orders',
                            data: res.data,
                            backgroundColor: '#0d6efd',
                            borderColor: '#0d6efd',
                            borderWidth: 1,
                            borderRadius: 4,
                            barPercentage: 0.6,
                            categoryPercentage: 0.8,
                            maxBarThickness: 40
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: "#111",
                                titleColor: "#fff",
                                bodyColor: "#fff",
                                padding: 12,
                                displayColors: false
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { color: "#6c757d" }
                            },
                            y: {
                                beginAtZero: true,
                                grid: { color: "rgba(0,0,0,0.05)" },
                                ticks: { color: "#6c757d" }
                            }
                        }
                    }
                });
            });
        }

        // Initial load
        loadOrdersChart('yearly');

        // Filter buttons
        $('.filter-btn').on('click', function () {
            $('.filter-btn')
                .removeClass('btn-primary active')
                .addClass('btn-light');

            $(this)
                .removeClass('btn-light')
                .addClass('btn-primary active');

            loadOrdersChart($(this).data('filter'));
        });

        /* ===========================
           INVENTORY BAR CHART
        =========================== */

        // Laravel Data
        const categoryLabels = @json($labels);
        const totalStockData = @json($total_stock);
        const lowStockData = @json($low_stock_count);

        if (categoryLabels.length > 0) {
            const inventoryCtx = document.getElementById('inventoryChart').getContext('2d');

            const inventoryChart = new Chart(inventoryCtx, {
                type: 'bar',
                data: {
                    labels: categoryLabels,
                    datasets: [
                        {
                            label: '{{ __('Total Items') }}',
                            data: totalStockData,
                            backgroundColor: "rgba(25,135,84,0.7)",
                            borderRadius: 8,
                            barThickness: 30
                        },
                        {
                            label: '{{ __('Low Stock') }}',
                            data: lowStockData,
                            backgroundColor: "rgba(220,53,69,0.7)",
                            borderRadius: 8,
                            barThickness: 30
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                boxWidth: 12,
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            backgroundColor: "#111",
                            titleColor: "#fff",
                            bodyColor: "#fff",
                            padding: 12,
                            callbacks: {
                                label: function (context) {
                                    let label = context.dataset.label || '';
                                    let value = context.raw || 0;
                                    return label + ': ' + value.toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: "#6c757d" }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: "rgba(0,0,0,0.05)"
                            },
                            ticks: { color: "#6c757d" },
                            title: {
                                display: true,
                                text: 'Quantity'
                            }
                        }
                    }
                }
            });
        } else {
            document.getElementById('inventoryChart').innerHTML = '<div class="text-center text-muted mt-5">No data available</div>';
        }
    </script>
@endsection

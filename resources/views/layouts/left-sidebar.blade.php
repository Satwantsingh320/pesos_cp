<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">

    <div data-simplebar class="h-100">
        @php
            $counts = Cache::remember('sidebar_counts', 10, function () {
                return DB::selectOne("
                                                                    SELECT
                                                                      (SELECT COUNT(*) FROM orders WHERE order_status IN (0,1))
                        AS orders_count,
                                                                        (SELECT COUNT(*) FROM product_questions WHERE is_answered = 0 AND is_approved = 0) AS questions_count,
                                                                        (SELECT COUNT(*) FROM refund_requests WHERE status = 0) AS refunds_count
                                                                ");
            });
        @endphp

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <!-- <li class="menu-title" key="t-menu">{{ __('admin.menu') }}</li> -->
                <li>
                    <a href="{{ route('dashboard.index') }}" class="waves-effect">
                        <i class="bx bx-home-circle"></i>
                        <span key="t-dashboards">{{ __('admin.dashboard') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('category.index') }}" class="waves-effect">
                        <i class="bxr bx-categories"></i>
                        <span key="t-vendors">{{ __('admin.categories') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('subcategory.index') }}" class="waves-effect">
                        <i class="bx bx-list"></i>
                        <span key="t-vendors">{{ __('admin.subcategories') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('brands.index') }}" class="waves-effect">
                        <i class="bxr bx-registered"></i>
                        <span key="t-vendors">{{ __('admin.brands') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('products.index') }}" class="waves-effect">
                        <i class="bx bx-package"></i>
                        <span key="t-vendors">{{ __('admin.products') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('inventory.index') }}" class="waves-effect">
                        <i class='bx  bx-warehouse'></i>
                        <span key="t-vendors">{{ __('admin.inventory') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('customers.index') }}" class="waves-effect">
                        <i class="bx bx-group"></i>
                        <span key="t-vendors">{{ __('admin.customers') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('orders.index') }}" class="waves-effect d-flex justify-content-between">
                        <span>
                            <i class="bx bx-detail"></i>
                            {{ __('admin.orders') }}
                        </span>
                        @if ($counts->orders_count > 0)
                            <span
                                class="badge bg-danger rounded-circle d-inline-flex align-items-center justify-content-center"
                                style="width: 20px; height: 20px; font-size: 11px;">{{ $counts->orders_count }}</span>
                        @endif
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.product.questions') }}"
                        class="waves-effect d-flex justify-content-between">
                        <span>
                            <i class="bx bx-detail"></i>
                            {{ __('admin.product_questions') }}
                        </span>
                        @if ($counts->questions_count > 0)
                            <span
                                class="badge bg-warning rounded-circle d-inline-flex align-items-center justify-content-center"
                                style="width: 20px; height: 20px; font-size: 11px;">{{ $counts->questions_count }}</span>
                        @endif
                    </a>
                </li>


                <li>
                    <a href="{{ route('offers.index') }}" class="waves-effect">
                        <i class="bxr bx-discount"></i>
                        <span key="t-vendors">{{ __('admin.special_offers') }}</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.vip.index') }}" class="waves-effect">
                        <i class="bxr bx-discount"></i>
                        <span key="t-vendors">{{ __('admin.VIP Customers') }}</span>
                    </a>
                </li>



                <li>
                    <a href="{{ route('settings.get-settings') }}" class="waves-effect">
                        <i class="bx bx-cog"></i>
                        <span key="t-vendors">{{ __('admin.settings') }}</span>
                    </a>
                </li>
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->

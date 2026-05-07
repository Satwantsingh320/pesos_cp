<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="{{ route('dashboard.index') }}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{asset('assets/images/logo.jpeg')}}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{asset('assets/images/logo.jpeg')}}" alt="" height="17">
                    </span>
                </a>

                <a href="{{ route('dashboard.index') }}" class="logo logo-light mt-1">
                    <span class="logo-sm">
                        <img src="{{asset('assets/images/logo.png')}}" alt="">
                    </span>
                    <span class="logo-lg">
                        <img src="{{asset('assets/images/logo.png')}}" alt="">
                    </span>
                    <!-- <h4 class="text-white">vaakgolvslip.se</h4> -->

                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect d-none"
                id="vertical-menu-btn">
                <i class="fa fa-fw fa-bars"></i>
            </button>
        </div>

        <div class="d-flex">
            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item waves-effect" data-bs-toggle="dropdown">

                    <img src="{{ asset('assets/images/flags/' . app()->getLocale() . '.png') }}" height="16"
                        class="me-1" alt="lang">

                    <i class="mdi mdi-chevron-down"></i>
                </button>

                <div class="dropdown-menu dropdown-menu-end">
                    <a href="{{ route('change.language', 'sv') }}"
                        class="dropdown-item {{ app()->getLocale() == 'sv' ? 'active' : '' }}">
                        <img src="{{ asset('assets/images/flags/sv.png') }}" height="16" class="me-2">
                        Swedish
                    </a>

                    <a href="{{ route('change.language', 'en') }}"
                        class="dropdown-item {{ app()->getLocale() == 'en' ? 'active' : '' }}">
                        <img src="{{ asset('assets/images/flags/en.png') }}" height="16" class="me-2">
                        English
                    </a>
                </div>
            </div>



            <div class="dropdown d-inline-block d-lg-none ms-2">
                <button type="button" class="btn header-item noti-icon waves-effect" id="page-header-search-dropdown"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="mdi mdi-magnify"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                    aria-labelledby="page-header-search-dropdown">
                    <form class="p-3">
                        <div class="form-group m-0">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search ..."
                                    aria-label="Recipient's username">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit"><i
                                            class="mdi mdi-magnify"></i></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item noti-icon waves-effect"
                    id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false">

                    <i class="bx bx-bell bx-tada"></i>

                    @if($unreadCount > 0)
                        <span class="badge bg-danger rounded-pill">
                            {{ $unreadCount }}
                        </span>
                    @endif
                </button>

                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                    aria-labelledby="page-header-notifications-dropdown">

                    <div class="p-3 border-bottom">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="m-0">{{ __('admin.notifications') }}</h6>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('admin.notifications.index') }}" class="small">
                                    {{ __('admin.view_all') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <div data-simplebar style="max-height: 230px;">

                        <ul class="list-unstyled mb-0">

                            @forelse($unreadNotifications as $notification)
                                <li class="border-bottom">
                                    <a href="{{ $notification->data['url'] ?? '#' }}"
                                        class="dropdown-item py-3 d-flex align-items-start notification-item"
                                        onclick="markAsRead(event, '{{ $notification->id }}', this.href)">

                                        <div class="flex-shrink-0 me-3">
                                            <div
                                                class="avatar-xs bg-primary rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="bx  bx-cart text-white font-size-14"></i>
                                            </div>
                                        </div>

                                        <div class="flex-grow-1 overflow-hidden">
                                            <h6 class="mb-1 text-wrap notification-text">
                                                {{ $notification->data['message'] ?? 'Notification' }}
                                            </h6>

                                            <small class="text-muted">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </small>
                                        </div>

                                    </a>
                                </li>
                            @empty
                                <li class="text-center text-muted py-3">
                                    {{ __('website.No new notifications') }}
                                </li>
                            @endforelse

                        </ul>

                    </div>

                    <div class="p-2 border-top d-grid">
                        <a class="btn btn-sm btn-link text-center" href="{{ route('admin.notifications.index') }}">
                            {{ __('admin.view_more') }}..
                        </a>
                    </div>
                </div>
            </div>

            <div class="dropdown d-inline-block">
                @php
                    $user = auth()->user();
                    $imagePath = (!empty($user->image) && file_exists(public_path(USER_PATH . $user->image)))
                        ? asset(USER_PATH . $user->image)
                        : asset('assets/images/default-user.png'); // fallback image
                @endphp

                <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="rounded-circle header-profile-user" src="{{ $imagePath }}" alt="Header Avatar">

                    <span class="d-none d-xl-inline-block ms-1" key="t-henry">{{ auth()->user()->name }}</span>
                    <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <!-- item-->
                    <a class="dropdown-item" href="{{ route('profile') }}"><i
                            class="bx bx-user font-size-16 align-middle me-1"></i> <span
                            key="t-profile">{{__('admin.profile')}}</span></a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="{{ route('logout') }}"><i
                            class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i> <span
                            key="t-logout">{{__('admin.logout')}}</span></a>
                </div>
            </div>
        </div>
    </div>
</header>
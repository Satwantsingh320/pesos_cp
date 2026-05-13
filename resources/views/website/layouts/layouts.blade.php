<!doctype html>
<html lang="{{ app()->getLocale() }}">

<head>
    <!-- ========= Meta Tags ======= -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ config('aap.name') }}">
    <meta name="keywords" content="HTML, CSS, JavaScript">
    <meta name="author" content="Intellisense Technology">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Language Switcher -->
    <meta name="language-switcher" content="{{ route('website.lang.switch') }}">

    <!-- ========= Main Css ======= -->
    <link href="{{ asset('website-assets/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- ========= Custom Css ======= -->
    <link href="{{ asset('website-assets/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('website-assets/css/custom.css') }}" rel="stylesheet">

    <!-- ========= Animate ======= -->
    <link href="{{ asset('website-assets/css/animate.css') }}" rel="stylesheet">

    <!-- ========= Responsive Css ======= -->
    <link href="{{ asset('website-assets/css/responsive.css') }}" rel="stylesheet">

    <!-- ========= Font Awesome ======= -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" rel="stylesheet">

    <!-- ========= Owl Css ======= -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.css" />
    <style>
        /* Hide mobile elements by default */
        .mobile-only {
            display: none;
        }

        .mobile-search {
            display: none;
        }

        /* Language Switcher Styles */
        .language-switcher {
            margin-left: 10px;
        }

        .language-switcher .dropdown-item {
            cursor: pointer;
        }

        .language-switcher img {
            width: 20px;
            margin-right: 5px;
        }

        /* Mobile behavior */
        @media (max-width: 768px) {
            .desktop-search {
                display: none;
            }

            .mobile-only {
                display: inline-block;
            }

            .mobile-search {
                position: absolute;
                top: 100%;
                right: 0;
                padding: 10px;
                z-index: 999;
                background: #fff;
                border-top: 1px solid #eee;
            }

            .mobile-search.active {
                display: block;
            }
        }
    </style>

    <!-- ========= Favicon ======= -->
    <link rel="icon" type="image/png" href="{{ asset('website-assets/img/favicon.ico') }}">

    <title>@yield('title', config('app.name'))</title>
</head>

<body>

    <!-- ========= Header Top Side ======= -->
    <section id="header-top-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-sm-6">
                    <div class="top-text">
                        <p>{{ __('website.best_special_offers') }}</p>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-6">
                    <div class="contact-box">
                        <ul>
                            <li>
                                <div class="contact-list">
                                    <i class="fa-solid fa-phone"></i> {{ __('website.phone_number') }}
                                </div>
                            </li>
                            <li>
                                <div class="contact-email">
                                    <i class="fa-solid fa-envelope"></i> {{ __('website.email_address') }}
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========= Header Logo ======= -->
    <header id="header-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-sm-6">
                    <div class="header-social-media">
                        <ul>
                            <li>
                                <div class="header-social">
                                    <a href="https://facebook.com" target="_blank" rel="noopener noreferrer">
                                        <i class="fa-brands fa-facebook-f"></i>
                                    </a>
                                </div>
                            </li>

                            <li>
                                <div class="header-social">
                                    <a href="https://instagram.com" target="_blank" rel="noopener noreferrer">
                                        <i class="fa-brands fa-instagram"></i>
                                    </a>
                                </div>
                            </li>

                            <li>
                                <div class="header-social">
                                    <a href="https://x.com" target="_blank" rel="noopener noreferrer">
                                        <i class="fa-brands fa-x-twitter"></i>
                                    </a>
                                </div>
                            </li>

                            <li>
                                <div class="header-social">
                                    <a href="https://youtube.com" target="_blank" rel="noopener noreferrer">
                                        <i class="fa-brands fa-youtube"></i>
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="website-logo">
                        <a href="{{ route('website.home') }}">
                            <img src="{{ asset('website-assets/img/logo.png') }}" class="img-fluid" alt="Logo"
                                style="height:60px">
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="website-operation-div">
                        <div class="website-list">
                            <div class="search-container">
                                <!-- Desktop Search (unchanged) -->
                                <div class="search-box desktop-search">
                                    <form id="searchForm" action="{{ route('website.products.search') }}">
                                        <div class="search-wrapper">
                                            <input type="text" name="keyword" class="search-input"
                                                placeholder="{{ __('website.search_product') }}">
                                            <button type="submit" class="search-btn">
                                                <i class="fa-solid fa-magnifying-glass"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Mobile Search Icon -->
                                <button type="button" class="search-toggle mobile-only" id="searchToggle"
                                    style="border: none; background: white;">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </button>
                            </div>

                            <!-- Mobile Search Dropdown -->
                            <div class="mobile-search" id="mobileSearch">
                                <form action="{{ route('website.products.search') }}">
                                    <div class="search-wrapper">
                                        <input type="text" name="keyword" class="search-input"
                                            placeholder="{{ __('website.search_product') }}">
                                        <button type="submit" class="search-btn">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <div class="user-account">
                                <div class="dropdown">
                                    <button class="btn btn-link p-0 border-0" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="fa-regular fa-user"></i>
                                    </button>

                                    <ul class="dropdown-menu p-0">
                                        @if (auth('customer')->check())
                                            <li>
                                                <a class="dropdown-item" href="{{ route('customer.dashboard.index') }}">
                                                    <i class="fa fa-home font-size-14 align-middle me-1"></i>
                                                    <span key="t-home">{{ __('website.dashboard') }}</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('customer.logout') }}">
                                                    <i class="fa fa-power-off font-size-14 align-middle me-1"></i>
                                                    <span key="t-logout">{{ __('website.logout') }}</span>
                                                </a>
                                            </li>
                                        @else
                                            <li>
                                                <a href="{{ route('login') }}">
                                                    <i class="fa fa-sign-in-alt me-1"></i> {{ __('website.login') }}
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('register') }}">
                                                    <i class="fa fa-user-plus me-1"></i> {{ __('website.register') }}
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>

                            <div class="user-cart">
                                <a href="{{ route('wishlist.index') }}">
                                    <div class="user-list-view">
                                        <i class="fa-solid fa-heart"></i>
                                        <span id="wishlist-count">{{ $wishlistCount ?? 0 }}</span>
                                    </div>
                                </a>
                            </div>

                            <div class="user-cart">
                                <a href="{{ route('website.cart') }}">
                                    <div class="user-list-view">
                                        <i class="fa-solid fa-bag-shopping"></i>
                                        <span id="cart-count">{{ $cartCount ?? 0 }}</span>
                                    </div>
                                </a>
                            </div>

                            <!-- Language Switcher -->
                            <div class="language-switcher">
                                <div class="dropdown">
                                    <button class="btn btn-link p-0 border-0 dropdown-toggle" data-bs-toggle="dropdown">
                                        <img src="{{ asset('assets/images/flags/' . (app()->getLocale() == 'en' ? 'en.png' : 'sv.png')) }}"
                                            alt="{{ strtoupper(app()->getLocale()) }}" width="24">
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item" href="#" onclick="switchLanguage('en')">
                                                <img src="{{ asset('assets/images/flags/en.png') }}" alt="EN"> English
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="#" onclick="switchLanguage('sv')">
                                                <img src="{{ asset('assets/images/flags/sv.png') }}" alt="SV">
                                                Svenska
                                            </a>
                                        </li>

                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- ========= Navigation ======= -->
    <section id="navigation-list">
        <nav class="navbar navbar-expand-lg p-0">
            <div class="container">
                <a class="navbar-brand d-none" href="#"><img src="{{ asset('website-assets/img/logo.png') }}"
                        class="img-fluid" alt="Logo"></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('website.home') ? 'active' : '' }}"
                                aria-current="page" href="{{ route('website.home') }}">{{ __('website.home') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('website.products') ? 'active' : '' }}"
                                href="{{ route('website.products') }}">{{ __('website.shop') }}</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('website.category.products') ? 'active' : '' }}"
                                href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ __('website.categories') }}
                            </a>
                            <ul class="dropdown-menu">
                                @foreach ($menuCategories ?? [] as $category)
                                    <li>
                                        <a class="dropdown-item"
                                            href="{{ route('website.category.products', $category->slug) }}">
                                            {{ $category->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('website.contact-us') ? 'active' : '' }}"
                                href="{{ route('website.contact-us') }}">{{ __('website.contact_us') }}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </section>

    @if (isset($pageType) && $pageType == 'inner')
        <!-- ========= Inner header ======= -->
        <section id="inner-header">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 col-sm-8">
                        <div class="breadcrumb-title">
                            <h3>{{ $pageTitle ?? '' }}</h3>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-4">
                        <div class="breadcrumb-links">
                            <ul>
                                <li><a href="{{ route('website.home') }}">{{ __('website.home_breadcrumb') }}</a></li>
                                <li>
                                    <p>{{ $breadcrumbTitlecurrent ?? '' }}</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    @yield('content')

    <!-- ========= Footer ======= -->
    <footer id="footer">
        <div id="footer-links">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-sm-6">
                        <div class="footer-logo">
                            <img src="{{ asset('website-assets/img/logo.png') }}" class="img-fluid" alt="Logo"
                                style="height:90px">
                            <p>{{ __('website.footer_description') }}</p>
                        </div>
                        <div class="header-social-media">
                            <ul>
                                <li>
                                    <div class="header-social">
                                        <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                                    </div>
                                </li>
                                <li>
                                    <div class="header-social">
                                        <a href="#"><i class="fa-brands fa-instagram"></i></a>
                                    </div>
                                </li>
                                <li>
                                    <div class="header-social">
                                        <a href="#"><i class="fa-brands fa-x-twitter"></i></a>
                                    </div>
                                </li>
                                <li>
                                    <div class="header-social">
                                        <a href="#"><i class="fa-brands fa-youtube"></i></a>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="link-footer">
                            <h3>{{ __('website.navigation') }}</h3>
                            <ul>
                                <li><a href="{{ route('website.home') }}">{{ __('website.home') }}</a></li>
                                <li><a href="{{ route('website.products') }}">{{ __('website.shop') }}</a></li>
                                <li><a href="{{ route('website.contact-us') }}">{{ __('website.contact_us') }}</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="link-footer">
                            <h3>{{ __('website.useful_links') }}</h3>
                            <ul>
                                <li><a href="{{ route('terms') }}">{{ __('website.terms_and_conditions') }}</a></li>
                                <li><a href="{{ route('privacyPolicy') }}">{{ __('website.privacy_policy') }}</a></li>
                                <li><a href="{{ route('shippingPolicy') }}">{{ __('website.return_policy') }}</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="link-footer-contact">
                            <h3>{{ __('website.get_in_touch') }}</h3>
                            <ul>
                                <li>
                                    <div class="contact-icon">
                                        <i class="fa-solid fa-location-dot"></i>
                                    </div>
                                    <div class="contact-text">
                                        <p>{{ __('website.address') }}</p>
                                    </div>
                                </li>
                                <li>
                                    <div class="contact-icon">
                                        <i class="fa-solid fa-phone"></i>
                                    </div>
                                    <div class="contact-text">
                                        <p>{{ __('website.phone_number') }}</p>
                                    </div>
                                </li>
                                <li>
                                    <div class="contact-icon">
                                        <i class="fa-solid fa-envelope"></i>
                                    </div>
                                    <div class="contact-text">
                                        <p>{{ __('website.email_address') }}</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-lg-7 col-sm-6">
                    <div class="footer-copyright">
                        <p>{{ __('website.copyright') }}</p>
                    </div>
                </div>
                <div class="col-lg-5 col-sm-6">
                    <div class="pay-box">
                        <img src="{{ asset('website-assets/img/pay_img.png') }}" class="img-fluid" alt="pay">
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- ========= Back To Top ======= -->
    <div class="scroll-top-wrapper">
        <span class="scroll-top-inner">
            <i class="fa fa-2x fa-arrow-circle-up"></i>
        </span>
    </div>

    <!-- ========= JQuery ======= -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- ========= Bootstrap ======= -->
    <script src="{{ asset('website-assets/js/bootstrap.bundle.min.js') }}"></script>

    <!-- ========= Animate ======= -->
    <script src="{{ asset('website-assets/js/wow.min.js') }}"></script>

    <!-- ========= Custom js ======= -->
    <script src="{{ asset('website-assets/js/custom.js') }}"></script>

    <!-- ========= dev js ======= -->
    <script src="{{ asset('website-assets/js/website-script.js') }}"></script>

    <!-- ========= slider ======= -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js"></script>

    <script>
        // Language Switcher Function
        function switchLanguage(locale) {
            $.ajax({
                url: '{{ route('website.lang.switch') }}',
                type: 'POST',
                data: {
                    locale: locale,
                    _token: '{{ csrf_token() }}'
                },
                success: function () {
                    location.reload();
                },
                error: function () {
                    console.error('Failed to switch language');
                }
            });
        }

        // Mobile Search Toggle
        const toggleBtn = document.getElementById('searchToggle');
        const mobileSearch = document.getElementById('mobileSearch');

        if (toggleBtn && mobileSearch) {
            toggleBtn.addEventListener('click', () => {
                mobileSearch.classList.toggle('active');
                if (mobileSearch.classList.contains('active')) {
                    mobileSearch.querySelector('input').focus();
                }
            });

            // Optional: close on outside click
            document.addEventListener('click', (e) => {
                if (!mobileSearch.contains(e.target) && !toggleBtn.contains(e.target)) {
                    mobileSearch.classList.remove('active');
                }
            });
        }
    </script>

</body>

</html>

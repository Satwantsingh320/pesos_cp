<!doctype html>
<html lang="en">

<head>
    <!-- ========= Meta Tags ======= -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Topsy">
    <meta name="keywords" content="HTML, CSS, JavaScript">
    <meta name="author" content="John Doe">
    <meta name="csrf-token" content="{{ csrf_token() }}">

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

        /* Mobile behavior */
        @media (max-width: 768px) {

            .desktop-search {
                display: none;
                /* hide original search */
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

    <title>Pesos Website | Homepage</title>
</head>

<body>

    <!-- ========= Header Top Side ======= -->
    <section id="header-top-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-sm-6">
                    <div class="top-text">
                        <p>¡Las mejores ofertas especiales! ¡40% de descuento!</p>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-6">
                    <div class="contact-box">
                        <ul>
                            <li>
                                <div class="contact-list">
                                    <i class="fa-solid fa-phone"></i> +52 614 215 9366
                                </div>
                            </li>
                            <li>
                                <div class="contact-email">
                                    <i class="fa-solid fa-envelope"></i> contacto@pesos.mx
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
                <div class="col-lg-4 col-sm-6">
                    <div class="website-logo">
                        <a href="{{ route('website.home') }}">
                            <img src="{{ asset('website-assets/img/logo.png') }}" class="img-fluid" alt="Logo">
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
                                                placeholder="Buscar un producto">
                                            <button type="submit" class="search-btn">
                                                <i class="fa-solid fa-magnifying-glass"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Mobile Search Icon -->
                                <button type="btn btn-link p-0 border-0" class="search-toggle mobile-only"
                                    id="searchToggle" style="border: none; background: white;"">
                                    <i class=" fa-solid fa-magnifying-glass"></i>
                                </button>

                            </div>

                            <!-- Mobile Search Dropdown -->
                            <div class="mobile-search" id="mobileSearch">
                                <form action="{{ route('website.products.search') }}">
                                    <div class="search-wrapper">
                                        <input type="text" name="keyword" class="search-input"
                                            placeholder="Buscar un producto">
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
                                                    <span key="t-home">{{ __('Tablero') }}</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('customer.logout') }}">
                                                    <i class="fa fa-power-off font-size-14 align-middle me-1"></i>
                                                    <span key="t-logout">{{ __('Cerrar Sesión') }}</span>
                                                </a>
                                            </li>
                                        @else
                                            <li>
                                                <a href="{{ route('login') }}">
                                                    <i class="fa fa-sign-in-alt me-1"></i> Iniciar Sesión
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('register') }}">
                                                    <i class="fa fa-user-plus me-1"></i> Registrarse
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
                                        <span id="wishlist-count">{{ $wishlistCount }}</span>
                                    </div>
                                </a>
                            </div>

                            <div class="user-cart">
                                <a href="{{ route('website.cart') }}">
                                    <div class="user-list-view">
                                        <i class="fa-solid fa-bag-shopping"></i>
                                        <span id="cart-count">{{ $cartCount }}</span>
                                    </div>
                                </a>
                            </div>
                            <div class="country-flag">
                                <img src="{{ asset('website-assets/img/mexico.webp') }}" alt="Mexico Flag Waving" />
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
                                aria-current="page" href="{{ route('website.home') }}">Hogar</a>
                        </li>
                        <li class="nav-item"><a
                                class="nav-link {{ request()->routeIs('website.products') ? 'active' : '' }}"
                                href="{{ route('website.products') }}">Comercio</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('website.category.products') ? 'active' : '' }}"
                                href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Categorías
                            </a>
                            <ul class="dropdown-menu">
                                @foreach ($menuCategories as $category)
                                    <li><a class="dropdown-item "
                                            href="{{ route('website.category.products', $category->slug) }}">
                                            {{ $category->name }}</a></li>
                                @endforeach


                            </ul>
                        </li>


                        <li class="nav-item"><a
                                class="nav-link {{ request()->routeIs('website.contact-us') ? 'active' : '' }}"
                                href="{{ route('website.contact-us') }}">Contacta con
                                nosotros</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </section>

    @if ($pageType == 'inner')
        <!-- ========= Inner header ======= -->
        <section id="inner-header">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 col-sm-8">
                        <div class="breadcrumb-title">
                            <h3>{{ $pageTitle }}</h3>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-4">
                        <div class="breadcrumb-links">
                            <ul>
                                <li><a href="{{ route('website.home') }}">Inicio</a></li>
                                <li>
                                    <p>{{ $breadcrumbTitlecurrent }}</p>
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
        {{-- <div class="container">
            <div class="row">
                <div class="col-lg-8 col-sm-6">
                    <div class="email-text">
                        <h3>CONSIGUE LAS ÚLTIMAS OFERTAS</h3>
                        <p>Regístrate y obtén las últimas ofertas y cupones</p>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="search-bar-footer">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Ingrese la dirección de correo...."
                                aria-label="Recipient’s username" aria-describedby="button-addon2">
                            <button class="btn btn-secondary" type="button" id="button-addon2">Suscribir</button>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
        <div id="footer-links">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-sm-6">
                        <div class="footer-logo">
                            <img src="{{ asset('website-assets/img/logo.png') }}" class="img-fluid" alt="Logo">
                            <p>Descubra productos electrónicos de alta calidad diseñados para ofrecer rendimiento,
                                confiabilidad e innovación.</p>
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
                            <h3>Navegación</h3>
                            <ul>
                                <li><a href="{{ route('website.home') }}">Hogar</a></li>
                                <li><a href="{{ route('website.products') }}">Comercio</a></li>
                                <li><a href="{{ route('website.contact-us') }}">Contacta con nosotros</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="link-footer">
                            <h3>Enlaces útiles</h3>
                            <ul>
                                <li><a href="{{ route('terms') }}">Términos y condiciones</a></li>
                                <li><a href="{{ route('privacyPolicy') }}">política de privacidad</a></li>
                                <li><a href="{{ route('returnPolicy') }}">Política de devoluciones</a></li>

                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="link-footer-contact">
                            <h3>Ponte en contacto</h3>
                            <ul>
                                <li>
                                    <div class="contact-icon">
                                        <i class="fa-solid fa-location-dot"></i>
                                    </div>
                                    <div class="contact-text">
                                        <p>Campo 8, Km 29 Corredor
                                            Comercial
                                            Cuauhtémoc, Chihuahua
                                            Cp: 31614</p>
                                    </div>
                                </li>
                                <li>
                                    <div class="contact-icon">
                                        <i class="fa-solid fa-phone"></i>
                                    </div>
                                    <div class="contact-text">
                                        <p>+52 614 215 9366</p>
                                    </div>
                                </li>
                                <li>
                                    <div class="contact-icon">
                                        <i class="fa-solid fa-envelope"></i>
                                    </div>
                                    <div class="contact-text">
                                        <p>contacto@pesos.mx</p>
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
                        <p>Copyright &copy; 2025 PESOS Reservados todos los derechos</p>
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

    <!-- ========= Back To Top  ======= -->

    <div class="scroll-top-wrapper ">
        <span class="scroll-top-inner">
            <i class="fa fa-2x fa-arrow-circle-up"></i>
        </span>
    </div>



    <!-- ========= JQuery ======= -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- ========= JQuery ======= -->
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
        const toggleBtn = document.getElementById('searchToggle');
        const mobileSearch = document.getElementById('mobileSearch');

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
    </script>

</body>

</html>
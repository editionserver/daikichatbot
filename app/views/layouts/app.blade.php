<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name') }}</title>

    <!-- Favicon -->
    @if(isset($settings['favicon']) && $settings['favicon'])
        <link rel="icon" href="{{ asset('storage/'.$settings['favicon']) }}" type="image/x-icon">
    @endif

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- CSS Files -->
    <link href="{{ asset('assets/css/material-kit.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet">

    <!-- Page Specific CSS -->
    @stack('styles')

    <!-- Google Analytics -->
    @if(isset($settings['enable_analytics']) && $settings['enable_analytics'] && isset($settings['analytics_id']))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $settings['analytics_id'] }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ $settings['analytics_id'] }}');
    </script>
    @endif
</head>
<body class="@yield('body-class')">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand" href="{{ route('home') }}">
                @if(isset($settings['logo']) && $settings['logo'])
                    <img src="{{ asset('storage/'.$settings['logo']) }}" 
                         alt="{{ config('app.name') }}" 
                         class="navbar-brand-img h-100">
                @else
                    {{ config('app.name') }}
                @endif
            </a>

            <!-- Mobile Toggle Button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navigation Links -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            Ana Sayfa
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('chat') ? 'active' : '' }}" href="{{ route('chat') }}">
                            Chatbot
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('plans') ? 'active' : '' }}" href="{{ route('plans') }}">
                            Planlar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">
                            Hakkımızda
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">
                            İletişim
                        </a>
                    </li>
                </ul>

                <!-- Authentication Links -->
                <ul class="navbar-nav">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Giriş Yap</a>
                        </li>
                        @if(isset($settings['enable_registration']) && $settings['enable_registration'])
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">Kayıt Ol</a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" 
                               href="#" 
                               id="navbarDropdown" 
                               role="button" 
                               data-bs-toggle="dropdown" 
                               aria-expanded="false">
                                @if(Auth::user()->profile_photo)
                                    <img src="{{ asset('storage/'.Auth::user()->profile_photo) }}" 
                                         class="avatar avatar-sm rounded-circle me-2" 
                                         alt="{{ Auth::user()->name }}">
                                @else
                                    <div class="avatar avatar-sm bg-gradient-primary rounded-circle me-2">
                                        {{ Auth::user()->getInitials() }}
                                    </div>
                                @endif
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                        <i class="material-icons">person</i>
                                        <span>Profilim</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.chats') }}">
                                        <i class="material-icons">chat</i>
                                        <span>Sohbetlerim</span>
                                        @if($unreadChatsCount > 0)
                                            <span class="badge bg-primary float-end">{{ $unreadChatsCount }}</span>
                                        @endif
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.plan') }}">
                                        <i class="material-icons">card_membership</i>
                                        <span>Üyelik Planım</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.settings') }}">
                                        <i class="material-icons">settings</i>
                                        <span>Ayarlar</span>
                                    </a>
                                </li>
                                @if(Auth::user()->isAdmin())
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                            <i class="material-icons">admin_panel_settings</i>
                                            <span>Yönetim Paneli</span>
                                        </a>
                                    </li>
                                @endif
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                           onclick="event.preventDefault(); this.closest('form').submit();">
                                            <i class="material-icons">logout</i>
                                            <span>Çıkış Yap</span>
                                        </a>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content mt-5 pt-5">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer mt-5 py-5 bg-light">
        <div class="container">
            <div class="row">
                <!-- Logo ve Açıklama -->
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <a href="{{ route('home') }}" class="mb-3 d-block">
                        @if(isset($settings['logo']) && $settings['logo'])
                            <img src="{{ asset('storage/'.$settings['logo']) }}" 
                                 alt="{{ config('app.name') }}" 
                                 class="img-fluid" 
                                 style="max-height: 50px;">
                        @else
                            <h5>{{ config('app.name') }}</h5>
                        @endif
                    </a>
                    <p class="text-muted">
                        Daikin klimaları hakkında merak ettiğiniz her şey için AI destekli chatbot asistanınız.
                    </p>
                    <div class="social-links">
                        @if(isset($settings['social_facebook']))
                            <a href="{{ $settings['social_facebook'] }}" 
                               class="btn btn-just-icon btn-link btn-facebook" 
                               target="_blank">
                                <i class="material-icons">facebook</i>
                            </a>
                        @endif
                        @if(isset($settings['social_twitter']))
                            <a href="{{ $settings['social_twitter'] }}" 
                               class="btn btn-just-icon btn-link btn-twitter" 
                               target="_blank">
                                <i class="material-icons">twitter</i>
                            </a>
                        @endif
                        @if(isset($settings['social_instagram']))
                            <a href="{{ $settings['social_instagram'] }}" 
                               class="btn btn-just-icon btn-link btn-instagram" 
                               target="_blank">
                                <i class="material-icons">instagram</i>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Hızlı Linkler -->
                <div class="col-lg-2 col-md-6 mb-4 mb-lg-0">
                    <h6 class="text-uppercase mb-4">Hızlı Linkler</h6>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a href="{{ route('home') }}" class="nav-link text-muted">Ana Sayfa</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('chat') }}" class="nav-link text-muted">Chatbot</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('plans') }}" class="nav-link text-muted">Planlar</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('about') }}" class="nav-link text-muted">Hakkımızda</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('contact') }}" class="nav-link text-muted">İletişim</a>
                        </li>
                    </ul>
                </div>

                <!-- Destek -->
                <div class="col-lg-2 col-md-6 mb-4 mb-lg-0">
                    <h6 class="text-uppercase mb-4">Destek</h6>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a href="{{ route('faq') }}" class="nav-link text-muted">SSS</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('docs') }}" class="nav-link text-muted">Dokümantasyon</a>
                        </li>
                        <li class="nav-item">
                        <a href="{{ route('terms') }}" class="nav-link text-muted">Kullanım Şartları</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('privacy') }}" class="nav-link text-muted">Gizlilik Politikası</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('cookies') }}" class="nav-link text-muted">Çerez Politikası</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <!-- Cookie Consent -->
    @if(isset($settings['show_cookie_warning']) && $settings['show_cookie_warning'])
    <div class="cookie-consent position-fixed bottom-0 w-100 bg-dark text-white py-3" 
         style="display: none; z-index: 9999;" 
         id="cookieConsent">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-9 mb-3 mb-md-0">
                    <p class="mb-0">
                        Bu web sitesi deneyiminizi geliştirmek için çerezler kullanır. 
                        <a href="{{ route('cookies') }}" class="text-info">Çerez Politikamız</a> hakkında daha fazla bilgi edinin.
                    </p>
                </div>
                <div class="col-md-3 text-md-end">
                    <button class="btn btn-primary btn-sm" id="cookieAccept">Kabul Et</button>
                    <button class="btn btn-outline-light btn-sm" id="cookieDecline">Reddet</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Loading Indicator -->
    <div class="loading-overlay" style="display: none;">
        <div class="loading-spinner"></div>
    </div>

    <!-- Core JS -->
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/material-kit.min.js') }}"></script>

    <!-- Custom Scripts -->
    <script src="{{ asset('assets/js/custom.js') }}"></script>

    <!-- Loading Indicator -->
    <script>
    function showLoader() {
        document.querySelector('.loading-overlay').style.display = 'flex';
    }

    function hideLoader() {
        document.querySelector('.loading-overlay').style.display = 'none';
    }
    </script>

    <!-- Cookie Consent -->
    <script>
    if (document.getElementById('cookieConsent')) {
        // Çerez tercihini kontrol et
        if (!localStorage.getItem('cookieConsent')) {
            document.getElementById('cookieConsent').style.display = 'block';
        }

        // Çerez kabul
        document.getElementById('cookieAccept').addEventListener('click', function() {
            localStorage.setItem('cookieConsent', 'accepted');
            document.getElementById('cookieConsent').style.display = 'none';
        });

        // Çerez red
        document.getElementById('cookieDecline').addEventListener('click', function() {
            localStorage.setItem('cookieConsent', 'declined');
            document.getElementById('cookieConsent').style.display = 'none';
            
            // Google Analytics'i devre dışı bırak
            window['ga-disable-{{ $settings["analytics_id"] ?? "" }}'] = true;
        });
    }
    </script>

    <!-- Mobile Navigation -->
    <script>
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('.navbar-collapse');

    if (navbarToggler && navbarCollapse) {
        navbarToggler.addEventListener('click', function() {
            document.body.classList.toggle('nav-open');
        });

        // Mobil menüdeki linklere tıklandığında menüyü kapat
        navbarCollapse.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                navbarCollapse.classList.remove('show');
                document.body.classList.remove('nav-open');
            });
        });
    }
    </script>

    <!-- Back to Top Button -->
    <script>
    const backToTop = document.createElement('button');
    backToTop.className = 'btn btn-primary btn-icon btn-round back-to-top';
    backToTop.innerHTML = '<i class="material-icons">keyboard_arrow_up</i>';
    document.body.appendChild(backToTop);

    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 100) {
            backToTop.classList.add('show');
        } else {
            backToTop.classList.remove('show');
        }
    });

    backToTop.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
    </script>

    <!-- Tooltips & Popovers -->
    <script>
    // Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Bootstrap popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    </script>

    <!-- Page Specific Scripts -->
    @stack('scripts')
</body>
</html>
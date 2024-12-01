<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name') }} Yönetim</title>

    <!-- Favicon -->
    @if(isset($settings['favicon']) && $settings['favicon'])
        <link rel="icon" href="{{ asset('storage/'.$settings['favicon']) }}" type="image/x-icon">
    @endif

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- CSS Files -->
    <link href="{{ asset('assets/css/material-dashboard.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/admin-custom.css') }}" rel="stylesheet">

    <!-- Page Specific CSS -->
    @stack('styles')
</head>

<body class="g-sidenav-show bg-gray-200">
    <!-- Sidebar -->
    <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-white" id="sidenav-main">
        <div class="sidenav-header">
            <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
            <a class="navbar-brand m-0" href="{{ route('admin.dashboard') }}">
                @if(isset($settings['logo']) && $settings['logo'])
                    <img src="{{ asset('storage/'.$settings['logo']) }}" class="navbar-brand-img h-100" alt="{{ config('app.name') }}">
                @else
                    <span class="ms-1 font-weight-bold">{{ config('app.name') }}</span>
                @endif
            </a>
        </div>

        <hr class="horizontal dark mt-0">

        <div class="collapse navbar-collapse w-auto h-auto" id="sidenav-collapse-main">
            <ul class="navbar-nav">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                       href="{{ route('admin.dashboard') }}">
                        <div class="text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">dashboard</i>
                        </div>
                        <span class="nav-link-text ms-1">Dashboard</span>
                    </a>
                </li>

                <!-- Kullanıcılar -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                       href="{{ route('admin.users.index') }}">
                        <div class="text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">people</i>
                        </div>
                        <span class="nav-link-text ms-1">Kullanıcılar</span>
                    </a>
                </li>

                <!-- Sohbetler -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.chats.*') ? 'active' : '' }}"
                       href="{{ route('admin.chats.index') }}">
                        <div class="text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">chat</i>
                        </div>
                        <span class="nav-link-text ms-1">Sohbetler</span>
                    </a>
                </li>

                <!-- Özel Yanıtlar -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.responses.*') ? 'active' : '' }}"
                       href="{{ route('admin.responses.index') }}">
                        <div class="text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">question_answer</i>
                        </div>
                        <span class="nav-link-text ms-1">Özel Yanıtlar</span>
                    </a>
                </li>

                <!-- Planlar -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.plans.*') ? 'active' : '' }}"
                       href="{{ route('admin.plans.index') }}">
                        <div class="text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">card_membership</i>
                        </div>
                        <span class="nav-link-text ms-1">Üyelik Planları</span>
                    </a>
                </li>

                <!-- Analitik -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}"
                       href="{{ route('admin.analytics.index') }}">
                        <div class="text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">analytics</i>
                        </div>
                        <span class="nav-link-text ms-1">Analitik</span>
                    </a>
                </li>

                <li class="nav-item mt-3">
                    <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Ayarlar</h6>
                </li>

                <!-- Sistem Ayarları -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}"
                       href="{{ route('admin.settings.index') }}">
                        <div class="text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">settings</i>
                        </div>
                        <span class="nav-link-text ms-1">Sistem Ayarları</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="sidenav-footer position-absolute w-100 bottom-0">
            <div class="mx-3">
                <a class="btn bg-gradient-primary mt-4 w-100" 
                   href="{{ route('home') }}" 
                   type="button">Siteye Git</a>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <!-- Navbar -->
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
            <div class="container-fluid py-1 px-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                        <li class="breadcrumb-item text-sm">
                            <a class="opacity-5 text-dark" href="{{ route('admin.dashboard') }}">Admin</a>
                        </li>
                        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">
                            @yield('title')
                        </li>
                    </ol>
                </nav>

                <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
                    <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                        <div class="input-group input-group-outline">
                            <label class="form-label">Ara...</label>
                            <input type="text" class="form-control" id="globalSearch">
                        </div>
                    </div>
                    <ul class="navbar-nav justify-content-end">
                        <!-- Bildirimler -->
                        <li class="nav-item dropdown pe-2 d-flex align-items-center">
                            <a href="javascript:;" class="nav-link text-body p-0" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="material-icons cursor-pointer">notifications</i>
                                @if($notificationCount > 0)
                                    <span class="position-absolute top-5 start-100 translate-middle badge rounded-pill bg-danger">
                                        {{ $notificationCount }}
                                    </span>
                                @endif
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end p-2 me-sm-n4" aria-labelledby="dropdownMenuButton">
                                @forelse($notifications as $notification)
                                    <li class="mb-2">
                                        <a class="dropdown-item border-radius-md" href="{{ $notification->link }}">
                                            <div class="d-flex py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="text-sm font-weight-normal mb-1">
                                                        <span class="font-weight-bold">{{ $notification->title }}</span>
                                                    </h6>
                                                    <p class="text-xs text-secondary mb-0">
                                                        <i class="fa fa-clock me-1"></i>
                                                        {{ $notification->created_at->diffForHumans() }}
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                @empty
                                    <li>
                                        <p class="dropdown-item text-center">Bildirim yok</p>
                                    </li>
                                @endforelse
                                @if($notifications->isNotEmpty())
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a class="dropdown-item text-center" href="{{ route('admin.notifications.index') }}">
                                            Tümünü Gör
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>

                        <!-- Kullanıcı Menüsü -->
                        <li class="nav-item dropdown pe-2 d-flex align-items-center">
                            <a href="javascript:;" class="nav-link text-body p-0" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                @if(Auth::user()->profile_photo)
                                    <img src="{{ asset('storage/'.Auth::user()->profile_photo) }}" 
                                         class="avatar avatar-sm" 
                                         alt="{{ Auth::user()->name }}">
                                @else
                                    <div class="avatar avatar-sm bg-gradient-primary">{{ Auth::user()->getInitials() }}</div>
                                @endif
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end p-2 me-sm-n4" aria-labelledby="dropdownMenuButton">
                                <li>
                                    <a class="dropdown-item border-radius-md" href="{{ route('admin.profile.edit') }}">
                                        <i class="material-icons">person</i>
                                        <span class="ms-2">Profilim</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item border-radius-md" href="{{ route('admin.settings.index') }}">
                                        <i class="material-icons">settings</i>
                                        <span class="ms-2">Ayarlar</span>
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <a class="dropdown-item border-radius-md" href="{{ route('logout') }}"
                                           onclick="event.preventDefault(); this.closest('form').submit();">
                                            <i class="material-icons">logout</i>
                                            <span class="ms-2">Çıkış Yap</span>
                                        </a>
                                    </form>
                                </li>
                            </ul>
                        </li>

                        <!-- Mobil Menü -->
                        <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                            <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                                <div class="sidenav-toggler-inner">
                                    <i class="sidenav-toggler-line"></i>
                                    <i class="sidenav-toggler-line"></i>
                                    <i class="sidenav-toggler-line"></i>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        @yield('content')

        <!-- Footer -->
        <footer class="footer py-4">
            <div class="container-fluid">
                <div class="row align-items-center justify-content-lg-between">
                    <div class="col-lg-6 mb-lg-0 mb-4">
                        <div class="copyright text-center text-sm text-muted text-lg-start">
                            © {{ date('Y') }} 
                            <a href="{{ config('app.url') }}" class="font-weight-bold" target="_blank">
                                {{ config('app.name') }}
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <ul class="nav nav-footer justify-content-center justify-content-lg-end">
                            <li class="nav-item">
                            <a href="#" class="nav-link text-muted" target="_blank">Yardım</a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link text-muted" target="_blank">Lisans</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>
    </main>

    <!-- Core JS -->
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/chartjs.min.js') }}"></script>
    <script src="{{ asset('assets/js/material-dashboard.min.js') }}"></script>

    <!-- Custom Scripts -->
    <script src="{{ asset('assets/js/admin-custom.js') }}"></script>

    <!-- Global Search -->
    <script>
    document.getElementById('globalSearch').addEventListener('input', debounce(function(e) {
        if (e.target.value.length >= 2) {
            fetch(`/admin/search?q=${encodeURIComponent(e.target.value)}`)
                .then(response => response.json())
                .then(data => showSearchResults(data))
                .catch(error => console.error('Arama hatası:', error));
        }
    }, 500));

    function showSearchResults(results) {
        // Arama sonuçları modalını göster
        const modal = new bootstrap.Modal(document.getElementById('searchResultsModal'));
        const modalBody = document.querySelector('#searchResultsModal .modal-body');
        
        let html = '<div class="list-group">';
        results.forEach(result => {
            html += `
                <a href="${result.url}" class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1">${result.title}</h6>
                        <small>${result.type}</small>
                    </div>
                    <p class="mb-1">${result.description}</p>
                </a>
            `;
        });
        html += '</div>';

        modalBody.innerHTML = html;
        modal.show();
    }

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    </script>

    <!-- Notifications -->
    <script>
    // Bildirim badge'ini güncelle
    function updateNotificationBadge(count) {
        const badge = document.querySelector('#dropdownMenuButton .badge');
        if (count > 0) {
            if (badge) {
                badge.textContent = count;
            } else {
                const newBadge = document.createElement('span');
                newBadge.className = 'position-absolute top-5 start-100 translate-middle badge rounded-pill bg-danger';
                newBadge.textContent = count;
                document.querySelector('#dropdownMenuButton').appendChild(newBadge);
            }
        } else if (badge) {
            badge.remove();
        }
    }

    // Bildirimleri işaretle
    function markNotificationsAsRead() {
        fetch('/admin/notifications/mark-as-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateNotificationBadge(0);
            }
        })
        .catch(error => console.error('Bildirim işaretleme hatası:', error));
    }

    // Bildirim menüsü açıldığında bildirimleri okundu olarak işaretle
    document.querySelector('#dropdownMenuButton').addEventListener('click', markNotificationsAsRead);
    </script>

    <!-- Sidebar Toggle -->
    <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
        var options = {
            damping: '0.5'
        }
        Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }

    // Mobil menü toggle
    document.querySelector('#iconNavbarSidenav').addEventListener('click', function() {
        document.querySelector('body').classList.toggle('g-sidenav-pinned');
        document.querySelector('body').classList.toggle('g-sidenav-hidden');
    });
    </script>

    <!-- Loading Indicator -->
    <script>
    function showLoader() {
        const loader = document.createElement('div');
        loader.className = 'loading-overlay';
        loader.innerHTML = '<div class="loading-spinner"></div>';
        document.body.appendChild(loader);
    }

    function hideLoader() {
        const loader = document.querySelector('.loading-overlay');
        if (loader) {
            loader.remove();
        }
    }
    </script>

    <!-- Dark Mode -->
    <script>
    const darkModeToggle = document.createElement('button');
    darkModeToggle.className = 'btn btn-link nav-link text-body p-0 fixed-plugin-button-nav';
    darkModeToggle.innerHTML = '<i class="material-icons">dark_mode</i>';
    document.querySelector('.navbar-nav').insertBefore(darkModeToggle, document.querySelector('.navbar-nav').firstChild);

    darkModeToggle.addEventListener('click', function() {
        document.body.classList.toggle('dark-version');
        localStorage.setItem('darkMode', document.body.classList.contains('dark-version'));

        // Chart renklerini güncelle
        const charts = Chart.instances;
        charts.forEach(chart => updateChartColors(chart));
    });

    // Kaydedilmiş dark mode tercihini yükle
    if (localStorage.getItem('darkMode') === 'true') {
        document.body.classList.add('dark-version');
    }

    function updateChartColors(chart) {
        const isDark = document.body.classList.contains('dark-version');
        
        if (chart.config.options.scales) {
            const scales = chart.config.options.scales;
            if (scales.y) {
                scales.y.grid.color = isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
            }
            if (scales.x) {
                scales.x.grid.color = isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
            }
        }

        if (chart.config.options.plugins && chart.config.options.plugins.legend) {
            chart.config.options.plugins.legend.labels.color = isDark ? '#fff' : '#666';
        }

        chart.update();
    }
    </script>

    <!-- Page Specific Scripts -->
    @stack('scripts')
</body>
</html>
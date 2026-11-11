<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>@yield('title') | {{ get_setting('app_name', config('app.name')) }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" type="image/x-icon"
        href="{{ get_setting('app_favicon') ? asset('storage/' . get_setting('app_favicon')) : asset('favicon.ico') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com/" />
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('assets/css/perfect-scrollbar.min.css') }}" />
    <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('assets/css/style.css') }}" />
    <link defer rel="stylesheet" type="text/css" media="screen" href="{{ asset('assets/css/animate.css') }}" />
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- jQuery (required) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Anti-FOUC for Sidebar Pinned/Mini State -->
    <script>
        (function() {
            try {
                var pinned = localStorage.getItem('sidebar_pinned');
                if (pinned === 'true' || pinned === '"true"') {
                    document.documentElement.classList.add('pinned-sidebar');
                } else {
                    document.documentElement.classList.add('mini-sidebar');
                }
            } catch(e) {
                document.documentElement.classList.add('mini-sidebar');
            }
        })();
    </script>
    @stack('styles')
    <style>
        [x-cloak] {
            display: none !important;
        }

        /* Fix for table action dropdowns: ensure active row always floats above subsequent rows */
        table tbody tr:focus-within,
        table tbody tr.dropdown-open,
        table tbody tr:hover:has(.dropdown) {
            position: relative !important;
            z-index: 40 !important;
        }

        .dropdown {
            position: relative;
        }

        .dropdown ul {
            z-index: 100 !important;
        }

        /* Prevent table containers from clipping open 3-dot dropdown menus */
        .table-responsive:has(.dropdown-open),
        .table-responsive:focus-within,
        .datatable:has(.dropdown-open),
        .datatable:focus-within {
            overflow: visible !important;
        }

        /* Ensure modals always cover the entire viewport including header and sidebar */
        #modal-portal {
            position: relative;
            z-index: 99999 !important;
        }

        .modal-overlay-full {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            z-index: 99999 !important;
            background-color: rgba(0, 0, 0, 0.6) !important;
            overflow-y: auto !important;
        }

        /* ============================================================
           MINI SIDEBAR & PINNED FULL SIDEBAR PROTOCOL
           ============================================================ */
        @media (min-width: 1024px) {
            /* 1. Base Mini Sidebar (Collapsed 70px) */
            html.mini-sidebar body:not(.pinned-sidebar) .sidebar,
            body.mini-sidebar:not(.pinned-sidebar) .sidebar {
                width: 70px !important;
                left: 0 !important;
                transform: none !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
                transition: width 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s ease !important;
                overflow-x: hidden !important;
            }

            html.mini-sidebar body:not(.pinned-sidebar) .main-content,
            body.mini-sidebar:not(.pinned-sidebar) .main-content {
                margin-left: 70px !important;
                width: calc(100% - 70px) !important;
                transition: margin-left 0.25s cubic-bezier(0.4, 0, 0.2, 1), width 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
            }

            [dir="rtl"] html.mini-sidebar body:not(.pinned-sidebar) .sidebar,
            [dir="rtl"] body.mini-sidebar:not(.pinned-sidebar) .sidebar {
                right: 0 !important;
                left: auto !important;
            }

            [dir="rtl"] html.mini-sidebar body:not(.pinned-sidebar) .main-content,
            [dir="rtl"] body.mini-sidebar:not(.pinned-sidebar) .main-content {
                margin-right: 70px !important;
                margin-left: 0 !important;
            }

            /* In Mini Collapsed Mode: Hide text, arrows, submenus, and pin button */
            html.mini-sidebar body:not(.pinned-sidebar) .sidebar:not(:hover) .main-logo span,
            body.mini-sidebar:not(.pinned-sidebar) .sidebar:not(:hover) .main-logo span,
            html.mini-sidebar body:not(.pinned-sidebar) .sidebar:not(:hover) .sidebar-pin-btn,
            body.mini-sidebar:not(.pinned-sidebar) .sidebar:not(:hover) .sidebar-pin-btn {
                opacity: 0 !important;
                display: none !important;
            }

            html.mini-sidebar body:not(.pinned-sidebar) .sidebar:not(:hover) .sidebar-header,
            body.mini-sidebar:not(.pinned-sidebar) .sidebar:not(:hover) .sidebar-header {
                justify-content: center !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            html.mini-sidebar body:not(.pinned-sidebar) .sidebar:not(:hover) .main-logo,
            body.mini-sidebar:not(.pinned-sidebar) .sidebar:not(:hover) .main-logo {
                margin: 0 auto !important;
            }

            html.mini-sidebar body:not(.pinned-sidebar) .sidebar:not(:hover) .main-logo img,
            body.mini-sidebar:not(.pinned-sidebar) .sidebar:not(:hover) .main-logo img {
                margin-left: 0 !important;
                margin-right: 0 !important;
            }

            html.mini-sidebar body:not(.pinned-sidebar) .sidebar:not(:hover) ul,
            body.mini-sidebar:not(.pinned-sidebar) .sidebar:not(:hover) ul {
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
            }

            html.mini-sidebar body:not(.pinned-sidebar) .sidebar:not(:hover) .nav-item > a,
            body.mini-sidebar:not(.pinned-sidebar) .sidebar:not(:hover) .nav-item > button {
                padding-left: 0 !important;
                padding-right: 0 !important;
                justify-content: center !important;
            }

            html.mini-sidebar body:not(.pinned-sidebar) .sidebar:not(:hover) .nav-item .flex.items-center,
            body.mini-sidebar:not(.pinned-sidebar) .sidebar:not(:hover) .nav-item .flex.items-center {
                justify-content: center !important;
                width: 100% !important;
            }

            html.mini-sidebar body:not(.pinned-sidebar) .sidebar:not(:hover) .nav-item span,
            body.mini-sidebar:not(.pinned-sidebar) .sidebar:not(:hover) .nav-item span {
                display: none !important;
                opacity: 0 !important;
            }

            html.mini-sidebar body:not(.pinned-sidebar) .sidebar:not(:hover) .nav-item svg.transition-transform,
            body.mini-sidebar:not(.pinned-sidebar) .sidebar:not(:hover) .nav-item svg.transition-transform,
            html.mini-sidebar body:not(.pinned-sidebar) .sidebar:not(:hover) ul[x-show="open"],
            body.mini-sidebar:not(.pinned-sidebar) .sidebar:not(:hover) ul[x-show="open"] {
                display: none !important;
            }

            /* 2. Hover to Expand (Floating Full 260px Sidebar) */
            html.mini-sidebar body:not(.pinned-sidebar) .sidebar:hover,
            body.mini-sidebar:not(.pinned-sidebar) .sidebar:hover {
                width: 260px !important;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2) !important;
                z-index: 60 !important;
                overflow-y: auto !important;
            }

            html.mini-sidebar body:not(.pinned-sidebar) .sidebar:hover .sidebar-header,
            body.mini-sidebar:not(.pinned-sidebar) .sidebar:hover .sidebar-header {
                justify-content: space-between !important;
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }

            html.mini-sidebar body:not(.pinned-sidebar) .sidebar:hover .main-logo,
            body.mini-sidebar:not(.pinned-sidebar) .sidebar:hover .main-logo {
                margin: 0 !important;
            }

            html.mini-sidebar body:not(.pinned-sidebar) .sidebar:hover .main-logo span,
            body.mini-sidebar:not(.pinned-sidebar) .sidebar:hover .main-logo span {
                opacity: 1 !important;
                display: inline-block !important;
            }

            html.mini-sidebar body:not(.pinned-sidebar) .sidebar:hover .sidebar-pin-btn,
            body.mini-sidebar:not(.pinned-sidebar) .sidebar:hover .sidebar-pin-btn {
                opacity: 1 !important;
                display: flex !important;
            }

            html.mini-sidebar body:not(.pinned-sidebar) .sidebar:hover ul,
            body.mini-sidebar:not(.pinned-sidebar) .sidebar:hover ul {
                padding-left: 0.75rem !important;
                padding-right: 0.75rem !important;
            }

            html.mini-sidebar body:not(.pinned-sidebar) .sidebar:hover .nav-item > a,
            body.mini-sidebar:not(.pinned-sidebar) .sidebar:hover .nav-item > button {
                padding-left: 0.625rem !important;
                padding-right: 0.625rem !important;
                justify-content: space-between !important;
            }

            html.mini-sidebar body:not(.pinned-sidebar) .sidebar:hover .nav-item .flex.items-center,
            body.mini-sidebar:not(.pinned-sidebar) .sidebar:hover .nav-item .flex.items-center {
                justify-content: flex-start !important;
                width: auto !important;
            }

            html.mini-sidebar body:not(.pinned-sidebar) .sidebar:hover .nav-item span,
            body.mini-sidebar:not(.pinned-sidebar) .sidebar:hover .nav-item span {
                opacity: 1 !important;
                display: inline-block !important;
            }

            html.mini-sidebar body:not(.pinned-sidebar) .sidebar:hover .nav-item svg.transition-transform,
            body.mini-sidebar:not(.pinned-sidebar) .sidebar:hover .nav-item svg.transition-transform {
                display: inline-block !important;
            }

            /* 3. Pinned Full Sidebar Mode */
            html.pinned-sidebar body .sidebar,
            body.pinned-sidebar .sidebar {
                width: 260px !important;
                left: 0 !important;
                transform: none !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
                transition: width 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
            }

            html.pinned-sidebar body .main-content,
            body.pinned-sidebar .main-content {
                margin-left: 260px !important;
                width: calc(100% - 260px) !important;
                transition: margin-left 0.25s cubic-bezier(0.4, 0, 0.2, 1), width 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
            }

            [dir="rtl"] html.pinned-sidebar body .sidebar,
            [dir="rtl"] body.pinned-sidebar .sidebar {
                right: 0 !important;
                left: auto !important;
            }

            [dir="rtl"] html.pinned-sidebar body .main-content,
            [dir="rtl"] body.pinned-sidebar .main-content {
                margin-right: 260px !important;
                margin-left: 0 !important;
            }

            html.pinned-sidebar body .sidebar-pin-btn,
            body.pinned-sidebar .sidebar-pin-btn {
                display: flex !important;
            }
        }
    </style>
</head>

<body x-data="main" class="relative overflow-x-hidden font-nunito text-sm font-normal antialiased"
    :class="[ $store.app.sidebar ? 'toggle-sidebar' : '', $store.app.sidebarPinned ? 'pinned-sidebar' : 'mini-sidebar', $store.app.theme === 'dark' || $store.app.isDarkMode ?  'dark' : '', $store.app.menu, $store.app.layout,$store.app.rtlClass]">

    @include('admin.layouts.partials.loader')
    @include('admin.layouts.partials.scroll-to-top')

    <div class="main-container min-h-screen text-black dark:text-white-dark" :class="[$store.app.navbar]">
        @include('admin.layouts.partials.customizer')
        @include('admin.layouts.sidebar')

        <div class="main-content flex min-h-screen flex-col">
            @include('admin.layouts.header')

            <div class="animate__animated p-6" :class="[$store.app.animation]">
                @include('admin.layouts.partials.flash-messages')
                @yield('content')
            </div>

            @include('admin.layouts.footer')
        </div>
    </div>

    @include('admin.layouts.scripts')
    @stack('scripts')

    @if(auth()->check())
    <audio id="notification-sound" preload="auto">
        <source src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" type="audio/mpeg">
    </audio>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let lastNotificationCount = {{ auth()->user()->unreadNotifications->count() }};
            let originalTitle = document.title;
            let notificationInterval = null;

            function playNotificationSound() {
                const sound = document.getElementById('notification-sound');
                if (sound) {
                    sound.play().catch(e => console.log('Sound play blocked by browser:', e));
                }
            }

            function updateTabTitle(count) {
                if (count > 0) {
                    if (!notificationInterval) {
                        notificationInterval = setInterval(() => {
                            document.title = document.title === originalTitle 
                                ? `(${count}) New Notification!` 
                                : originalTitle;
                        }, 1000);
                    }
                } else {
                    if (notificationInterval) {
                        clearInterval(notificationInterval);
                        notificationInterval = null;
                    }
                    document.title = originalTitle;
                }
            }

            // Initial check
            if (lastNotificationCount > 0) {
                updateTabTitle(lastNotificationCount);
            }

            // Poll for new notifications every 30 seconds
            setInterval(() => {
                fetch('{{ route("admin.notifications.count") }}')
                    .then(response => response.json())
                    .then(data => {
                        if (data.count > lastNotificationCount) {
                            playNotificationSound();
                        }
                        lastNotificationCount = data.count;
                        updateTabTitle(lastNotificationCount);
                    })
                    .catch(error => console.error('Error fetching notifications:', error));
            }, 30000);
        });
    </script>
    @endif
    <!-- Global Modal Teleport Container (Covers entire screen including header and sidebar) -->
    <div id="modal-portal"></div>
</body>

</html>
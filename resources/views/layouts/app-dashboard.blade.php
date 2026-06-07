<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@isset($title){{ $title }}@else Dashboard - Mercatino Libri @endisset</title>
    <meta name="description" content="Dashboard Mercatino Libri">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700|inter:400,500,600" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Icons (HeroIcons) -->
    <script src="https://cdn.jsdelivr.net/npm/lucide@latest"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Poppins', sans-serif; font-weight: 700; }
        html { scroll-behavior: smooth; }
        
        .sidebar-active {
            @apply bg-purple-50 border-l-4 border-purple-600 text-purple-600;
        }
        
        .dashboard-card {
            @apply bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300;
        }
        
        .stat-badge {
            @apply inline-flex items-center px-3 py-1 rounded-full text-sm font-medium;
        }
        
        .gradient-text {
            @apply bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent;
        }

        /* Mobile sidebar slide animation */
        .sidebar {
            transform: translateX(-100%) !important;
        }
        
        @media (min-width: 1024px) {
            .sidebar {
                transform: translateX(0) !important;
            }
        }
        
        .sidebar.active {
            transform: translateX(0) !important;
        }
        
        .sidebar-overlay {
            display: none !important;
            pointer-events: none !important;
        }
        
        .sidebar-overlay.active {
            display: block !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            background-color: rgba(0, 0, 0, 0.5) !important;
            z-index: 30 !important;
            pointer-events: auto !important;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Sidebar Overlay (Mobile Only) -->
    <div class="sidebar-overlay fixed inset-0 bg-black bg-opacity-50 z-30" id="sidebarOverlay"></div>

    <div class="flex h-screen overflow-hidden">
        <!-- Hamburger Menu Button (Mobile Only) -->
        <button type="button" id="sidebarToggle" class="fixed lg:hidden top-4 left-4 z-50 p-2 bg-white text-gray-700 border border-gray-200 rounded-lg shadow-md hover:shadow-lg hover:bg-gray-50 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        <!-- Sidebar -->
        <aside class="sidebar fixed lg:relative left-0 top-0 h-screen lg:h-auto w-64 bg-white border-r border-gray-200 flex flex-col transform transition-transform duration-300 ease-in-out z-40">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center space-x-2">
                    <span class="text-3xl">📚</span>
                    <div>
                        <span class="text-xl font-bold text-gray-900">Mercatino</span>
                        <p class="text-xs text-gray-500">Admin Dashboard</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 p-6 space-y-0 overflow-y-auto">
                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition @if(request()->is('admin') && !request()->is('admin/schools*') && !request()->is('admin/users*') && !request()->is('admin/books*') && !request()->is('admin/listings*') && !request()->is('log-viewer*')) bg-gray-100 text-gray-900 @endif">
                    Dashboard
                </a>

                <!-- Schools Management -->
                <a href="{{ route('admin.schools.index') }}" class="block px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition @if(request()->is('admin/schools*')) bg-gray-100 text-gray-900 @endif">
                    Gestione Scuole
                </a>

                <!-- Users Management -->
                <a href="{{ route('admin.users.index') }}" class="block px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition @if(request()->is('admin/users*')) bg-gray-100 text-gray-900 @endif">
                    Gestione Utenti
                </a>

                <!-- Books Catalog -->
                <a href="{{ route('admin.books.index') }}" class="block px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition @if(request()->is('admin/books*')) bg-gray-100 text-gray-900 @endif">
                    Catalogo Libri
                </a>

                <!-- Book Listings -->
                <a href="{{ route('admin.listings.index') }}" class="block px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition @if(request()->is('admin/listings*')) bg-gray-100 text-gray-900 @endif">
                    Gestione Annunci
                </a>
                
                <!-- Separator -->
                <div class="my-4 border-t border-gray-200"></div>
                
                <!-- Log Viewer -->
                <a href="/log-viewer" class="block px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition @if(request()->is('log-viewer*')) bg-gray-100 text-gray-900 @endif">
                    Visualizza log
                </a>

                <!-- Problems -->
                <a href="{{ route('admin.problems.index') }}" class="block px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition">
                    Problemi Segnalati
                </a>
            </nav>

            <div class="p-6 border-t border-gray-200 bg-white">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                        <span class="text-lg">👤</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Admin</p>
                        <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                    </div>
                </div>
                <form method="POST" action="/logout" class="mt-4">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition">
                        Esci
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content flex-1 overflow-auto w-full lg:w-auto">
            <div class="p-8">
                @yield('dashboard-content')
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            if (!sidebarToggle || !sidebar || !sidebarOverlay) {
                return;
            }

            function toggleSidebar() {
                sidebar.classList.toggle('active');
                sidebarOverlay.classList.toggle('active');
                document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
            }

            // Toggle on hamburger click
            sidebarToggle.addEventListener('click', toggleSidebar);

            // Close sidebar when overlay is clicked
            sidebarOverlay.addEventListener('click', toggleSidebar);

            // Close sidebar when a link is clicked
            document.querySelectorAll('.sidebar a').forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth < 1024) {
                        toggleSidebar();
                    }
                });
            });

            // Handle window resize
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1024) {
                    sidebar.classList.remove('active');
                    sidebarOverlay.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });
        });
    </script>

    <style>
        .sidebar-nav-item {
            @apply flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors duration-200;
        }
    </style>
</body>
</html>

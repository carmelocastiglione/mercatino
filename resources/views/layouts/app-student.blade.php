<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@isset($title){{ $title }}@else Dashboard - Mercatino Libri @endisset</title>
    <meta name="description" content="Dashboard Studente Mercatino Libri">
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
            @apply bg-blue-50 border-l-4 border-blue-600 text-blue-600;
        }
        
        .dashboard-card {
            @apply bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300;
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
                        <p class="text-xs text-gray-500">Studente</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 p-6 space-y-1 overflow-y-auto">
                <!-- Prenota Consegna - Highlighted -->
                <a href="{{ route('student.deliveries.create') }}" class="block mb-2 px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg hover:from-blue-700 hover:to-blue-800 transition shadow-md">
                    ✨ Prenota consegna
                </a>

                <!-- Prenota Libro - Highlighted -->
                <a href="{{ route('student.book-reservations.create') }}" class="block mb-4 px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-green-600 to-green-700 rounded-lg hover:from-green-700 hover:to-green-800 transition shadow-md">
                    📚 Prenota acquisto
                </a>
                
                <!-- Dashboard -->
                <a href="{{ route('student.dashboard') }}" class="block px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition @if(request()->is('student') && !request()->is('student/deliveries*')) bg-gray-100 text-gray-900 @endif">
                    Dashboard
                </a>

                <!-- Consegne -->
                <a href="{{ route('student.deliveries.index') }}" class="block px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition @if(request()->is('student/deliveries*')) bg-gray-100 text-gray-900 @endif">
                    Le mie consegne
                </a>

                <!-- Le Mie Prenotazioni -->
                <a href="{{ route('student.book-reservations.index') }}" class="block px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition @if(request()->is('student/book-reservations*')) bg-gray-100 text-gray-900 @endif">
                    Le mie prenotazioni
                </a>

                <!-- I Miei Libri -->
                <a href="{{ route('student.book-listings.index') }}" class="block px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition @if(request()->is('student/book-listings*')) bg-gray-100 text-gray-900 @endif">
                    I miei libri
                </a>

                <!-- Le Mie Vendite -->
                <a href="{{ route('student.sales.index') }}" class="block px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition @if(request()->is('student/sales*')) bg-gray-100 text-gray-900 @endif">
                    Le mie vendite
                </a>

                <!-- I Miei Acquisti -->
                <a href="{{ route('student.purchases.index') }}" class="block px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition @if(request()->is('student/purchases*')) bg-gray-100 text-gray-900 @endif">
                    I miei acquisti
                </a>

                <!-- Le Mie Riscossioni -->
                <a href="{{ route('student.withdrawals.index') }}" class="block px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition @if(request()->is('student/withdrawals*')) bg-gray-100 text-gray-900 @endif">
                    Le mie riscossioni
                </a>

                <!-- Notifiche -->
                <div class="mt-4 pt-4 border-t border-gray-200">
                    @php
                        $unreadNotifications = auth()->user()->notifications()->where('is_read', false)->count();
                    @endphp
                    <a href="{{ route('student.notifications.index') }}" class="flex items-center justify-between px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition @if(request()->is('student/notifications*')) bg-gray-100 text-gray-900 @endif">
                        <span>Notifiche</span>
                        @if($unreadNotifications > 0)
                            <span class="flex items-center justify-center bg-red-500 text-white text-xs font-bold rounded-full w-6 h-6 leading-none">{{ $unreadNotifications }}</span>
                        @endif
                    </a>
                </div>

                <!-- Segnala Problema -->
                <a href="{{ route('student.problems.create') }}" class="block px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition">
                    Segnala problema
                </a>
            </nav>

            <div class="p-6 border-t border-gray-200 bg-white">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                        <span class="text-lg">👤</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
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
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
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
</body>
</html>

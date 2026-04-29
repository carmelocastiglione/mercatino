<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@isset($title){{ $title }}@else Dashboard - Mercatino Libri @endisset</title>
    <meta name="description" content="Dashboard Mercatino Libri">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700|inter:400,500,600" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
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
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r border-gray-200 flex flex-col">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center space-x-2">
                    <span class="text-3xl">📚</span>
                    <div>
                        <span class="text-xl font-bold text-gray-900">Mercatino</span>
                        <p class="text-xs text-gray-500">Admin Dashboard</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 p-6 space-y-1 overflow-y-auto">
                <!-- Dashboard -->
                <a href="/admin" class="block px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition @if(request()->is('admin') && !request()->is('admin/schools*') && !request()->is('admin/users*') && !request()->is('admin/books*')) bg-gray-100 text-gray-900 @endif">
                    Dashboard
                </a>

                <!-- Schools Management -->
                <a href="/admin/schools" class="block px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition @if(request()->is('admin/schools*')) bg-gray-100 text-gray-900 @endif">
                    Gestione Scuole
                </a>

                <!-- Users Management -->
                <a href="/admin/users" class="block px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition @if(request()->is('admin/users*')) bg-gray-100 text-gray-900 @endif">
                    Gestione Utenti
                </a>

                <!-- Books Management -->
                <a href="/admin/books" class="block px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition @if(request()->is('admin/books*')) bg-gray-100 text-gray-900 @endif">
                    Gestione Libri
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
        <main class="flex-1 overflow-auto">
            <div class="p-8">
                @yield('dashboard-content')
            </div>
        </main>
    </div>

    <style>
        .sidebar-nav-item {
            @apply flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors duration-200;
        }
    </style>
</body>
</html>

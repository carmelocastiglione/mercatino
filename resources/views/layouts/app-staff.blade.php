<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@isset($title){{ $title }}@else Dashboard - Mercatino Libri @endisset</title>
    <meta name="description" content="Dashboard Staff Mercatino Libri">
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
            @apply bg-blue-50 border-l-4 border-blue-600 text-blue-600;
        }
        
        .dashboard-card {
            @apply bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300;
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
                        <p class="text-xs text-gray-500">Staff</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 p-6 space-y-1 overflow-y-auto">
                <!-- Dashboard -->
                <a href="{{ route('staff.dashboard') }}" class="block px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition @if(request()->is('staff') && !request()->is('staff/deliveries*')) bg-gray-100 text-gray-900 @endif">
                    Dashboard
                </a>

                <!-- Prenotazioni online -->
                <a href="{{ route('staff.deliveries.index') }}" class="block px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition @if(request()->is('staff/deliveries*')) bg-gray-100 text-gray-900 @endif">
                    Prenotazioni online
                </a>

                <!-- Libri disponibili -->
                <a href="{{ route('staff.book-listings.index') }}" class="block px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition @if(request()->is('staff/book-listings*')) bg-gray-100 text-gray-900 @endif">
                    Libri disponibili
                </a>

                <!-- Acquisizioni -->
                <a href="{{ route('staff.acquisitions.index') }}" class="block px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition @if(request()->is('staff/acquisitions*')) bg-gray-100 text-gray-900 @endif">
                    Acquisizioni
                </a>

                <!-- Vendite -->
                <a href="{{ route('staff.sales.index') }}" class="block px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition @if(request()->is('staff/sales*')) bg-gray-100 text-gray-900 @endif">
                    Vendite
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
        <main class="flex-1 overflow-auto">
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
</body>
</html>

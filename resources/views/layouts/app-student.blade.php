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
                        <p class="text-xs text-gray-500">Studente</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 p-6 space-y-1 overflow-y-auto">
                <!-- Prenota Consegna - Highlighted -->
                <a href="{{ route('student.deliveries.create') }}" class="block mb-4 px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg hover:from-blue-700 hover:to-blue-800 transition shadow-md">
                    ✨ Prenota consegna
                </a>
                
                <!-- Dashboard -->
                <a href="{{ route('student.dashboard') }}" class="block px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition @if(request()->is('student') && !request()->is('student/deliveries*')) bg-gray-100 text-gray-900 @endif">
                    Dashboard
                </a>

                <!-- Consegne -->
                <a href="{{ route('student.deliveries.index') }}" class="block px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition @if(request()->is('student/deliveries*')) bg-gray-100 text-gray-900 @endif">
                    Le mie consegne
                </a>

                <!-- Sottomenu Consegne -->
                <div class="ml-4 space-y-1">
                    <a href="{{ route('student.deliveries.pending') }}" class="block px-3 py-2 text-xs font-medium text-gray-600 rounded-lg hover:bg-gray-100 transition @if(request()->is('student/deliveries/status/pending')) bg-yellow-50 text-yellow-700 @endif">
                        In sospeso
                    </a>
                    <a href="{{ route('student.deliveries.approved') }}" class="block px-3 py-2 text-xs font-medium text-gray-600 rounded-lg hover:bg-gray-100 transition @if(request()->is('student/deliveries/status/approved')) bg-green-50 text-green-700 @endif">
                        Approvate
                    </a>
                    <a href="{{ route('student.deliveries.rejected') }}" class="block px-3 py-2 text-xs font-medium text-gray-600 rounded-lg hover:bg-gray-100 transition @if(request()->is('student/deliveries/status/rejected')) bg-red-50 text-red-700 @endif">
                        Rifiutate
                    </a>
                </div>

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
                    <a href="{{ route('student.notifications.index') }}" class="flex items-center justify-between px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition @if(request()->is('student/notifications*')) bg-gray-100 text-gray-900 @endif">
                        <span>Notifiche</span>
                        <span id="notification-badge" class="hidden flex items-center justify-center bg-red-500 text-white text-xs font-bold rounded-full w-6 h-6 leading-none">0</span>
                    </a>
                </div>
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

    <script>
        // Load notification badge count
        async function updateNotificationBadge() {
            try {
                const response = await fetch('{{ route("student.notifications.unread-count") }}');
                const data = await response.json();
                const badge = document.getElementById('notification-badge');
                
                if (data.unread_count > 0) {
                    badge.textContent = data.unread_count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            } catch (error) {
                console.error('Error fetching notification count:', error);
            }
        }

        // Initial load
        updateNotificationBadge();
        
        // Refresh every 30 seconds
        setInterval(updateNotificationBadge, 30000);
    </script>
</body>
</html>

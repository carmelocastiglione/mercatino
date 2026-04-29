<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Mercatino</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen bg-white">
        <!-- Sidebar -->
        <aside class="w-64 bg-white text-gray-900 flex flex-col border-r border-gray-200">
            <!-- Logo -->
            <div class="p-6 border-b border-gray-200">
                <a href="{{ route('staff.dashboard') }}" class="text-2xl font-bold text-blue-600">
                    📚 Mercatino
                </a>
                <p class="text-sm text-gray-600 mt-1">Staff</p>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 p-6 space-y-1 overflow-y-auto">
                <!-- Dashboard -->
                <a href="{{ route('staff.dashboard') }}" class="block px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition @if(request()->is('staff') && !request()->is('staff/deliveries*')) bg-gray-100 text-gray-900 @endif">
                    Dashboard
                </a>

                <!-- Consegne da Approvare -->
                <a href="{{ route('staff.deliveries.index') }}" class="block px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition @if(request()->is('staff/deliveries*')) bg-gray-100 text-gray-900 @endif">
                    Consegne da Approvare
                </a>
            </nav>

            <!-- User Info -->
            <div class="p-6 border-t border-gray-200">
                <div class="text-sm">
                    <p class="text-gray-600">Logged in as</p>
                    <p class="font-medium text-gray-900">{{ auth()->user()->name }}</p>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="mt-4">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 transition">
                        Esci
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-auto">
            <div class="p-8">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>

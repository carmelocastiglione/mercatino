<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@isset($title){{ $title }}@else Mercatino Libri @endisset</title>
    <meta name="description" content="@isset($description){{ $description }}@else Piattaforma per comprare e vendere libri scolastici usati @endisset">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700|inter:400,500,600" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Poppins', sans-serif; font-weight: 700; }
        html { scroll-behavior: smooth; }
        .gradient-hero { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .gradient-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .bg-gradient-primary { background: linear-gradient(135deg, #9333ea 0%, #7c3aed 100%); }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in-up { animation: fadeInUp 0.6s ease-out forwards; }
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
    </style>
</head>
<body class="antialiased">
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-2">
                    <span class="text-2xl">📚</span>
                    <span class="text-xl font-bold text-gray-900">Mercatino Libri</span>
                </div>
                <div class="hidden md:flex space-x-8">
                    <a href="#features" class="text-gray-600 hover:text-gray-900 transition">Caratteristiche</a>
                    <a href="#how-it-works" class="text-gray-600 hover:text-gray-900 transition">Come Funziona</a>
                    <a href="#faq" class="text-gray-600 hover:text-gray-900 transition">FAQ</a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="/login" class="text-gray-600 hover:text-gray-900 transition font-medium hidden md:inline">Accedi</a>
                </div>
                <button id="mobile-menu-btn" class="md:hidden text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
            <div id="mobile-menu" class="hidden md:hidden pb-4 border-t border-gray-200">
                <a href="#features" class="block px-4 py-2 text-gray-600 hover:bg-gray-50">Caratteristiche</a>
                <a href="#how-it-works" class="block px-4 py-2 text-gray-600 hover:bg-gray-50">Come Funziona</a>
                <a href="#faq" class="block px-4 py-2 text-gray-600 hover:bg-gray-50">FAQ</a>
                <a href="/login" class="block px-4 py-2 text-gray-600 hover:bg-gray-50">Accedi</a>
            </div>
        </div>
    </nav>

    @yield('content')

    <footer class="bg-gray-900 text-gray-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 text-center text-sm">
            <p>Made with ❤️ by Carmelo Castiglione</p>
        </div>
    </footer>

    <script>
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
        document.querySelectorAll('#mobile-menu a').forEach(function(link) {
            link.addEventListener('click', function() {
                document.getElementById('mobile-menu').classList.add('hidden');
            });
        });
    </script>
</body>
</html>

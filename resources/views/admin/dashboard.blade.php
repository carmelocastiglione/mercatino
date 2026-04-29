@extends('layouts.app-dashboard')

@section('dashboard-content')
    <!-- Header -->
    <div class="mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">Dashboard</h1>
        <p class="text-gray-600">Gestione utenti e libri della piattaforma</p>
    </div>

    <!-- Management Boxes Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <!-- Schools Management Box -->
        <a href="/admin/schools" class="bg-blue-600 rounded-xl p-8 text-white hover:shadow-lg transition">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-2">GESTIONE</p>
                    <h2 class="text-3xl font-bold mb-4">Scuole</h2>
                    <p class="text-blue-100 mb-6">Amministra le scuole della piattaforma</p>
                    <div class="flex items-center font-medium hover:underline">
                        Accedi alla gestione
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </div>
                <div class="text-5xl font-bold text-blue-200">{{ $totalSchools ?? 0 }}</div>
            </div>
        </a>

        <!-- Users Management Box -->
        <a href="/admin/users" class="bg-blue-600 rounded-xl p-8 text-white hover:shadow-lg transition">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-2">GESTIONE</p>
                    <h2 class="text-3xl font-bold mb-4">Utenti</h2>
                    <p class="text-blue-100 mb-6">Amministra gli utenti della piattaforma</p>
                    <div class="flex items-center font-medium hover:underline">
                        Accedi alla gestione
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </div>
                <div class="text-5xl font-bold text-blue-200">{{ $totalUsers ?? 0 }}</div>
            </div>
        </a>

        <!-- Books Management Box -->
        <a href="/admin/books" class="bg-blue-600 rounded-xl p-8 text-white hover:shadow-lg transition">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-2">GESTIONE</p>
                    <h2 class="text-3xl font-bold mb-4">Libri</h2>
                    <p class="text-blue-100 mb-6">Amministra i libri del catalogo</p>
                    <div class="flex items-center font-medium hover:underline">
                        Accedi alla gestione
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </div>
                <div class="text-5xl font-bold text-blue-200">{{ $totalBooks ?? 0 }}</div>
            </div>
        </a>
    </div>
@endsection

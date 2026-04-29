@extends('layouts.app-dashboard')

@section('dashboard-content')
    <!-- Header -->
    <div class="mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">Dashboard</h1>
        <p class="text-gray-600">Gestione utenti e libri della piattaforma</p>
    </div>

    <!-- Management Boxes Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Schools Management Box -->
        <x-dashboard-card 
            href="/admin/schools"
            title="Scuole"
            description="Amministra le scuole della piattaforma"
            count="{{ $totalSchools ?? 0 }}"
            bgColor="blue"
            label="GESTIONE"
        />

        <!-- Users Management Box -->
        <x-dashboard-card 
            href="/admin/users"
            title="Utenti"
            description="Amministra gli utenti della piattaforma"
            count="{{ $totalUsers ?? 0 }}"
            bgColor="blue"
            label="GESTIONE"
        />

        <!-- Books Catalog Box -->
        <x-dashboard-card 
            href="/admin/books"
            title="Libri"
            description="Gestisci il catalogo generale dei libri"
            count="{{ $totalBooks ?? 0 }}"
            bgColor="blue"
            label="CATALOGO"
        />

        <!-- Book Listings Box -->
        <x-dashboard-card 
            href="/admin/listings"
            title="Copie"
            description="Gestisci gli annunci e le copie in vendita"
            count="—"
            bgColor="blue"
            label="ANNUNCI"
        />
    </div>
@endsection

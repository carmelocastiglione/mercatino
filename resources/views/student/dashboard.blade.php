@extends('layouts.app-student')

@section('title', 'Dashboard Studente')

@section('content')
    <div class="mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">Dashboard</h1>
        <p class="text-gray-600">Gestisci le tue consegne di libri</p>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
        <x-stat-card 
            title="Totale Vendite"
            amount="{{ $totalSales }}"
            bgColor="green"
            accentColor="green"
        />

        <x-stat-card 
            title="Totale Prelevato"
            amount="{{ $totalWithdrawn }}"
            bgColor="blue"
            accentColor="blue"
        />
    </div>

    <!-- Management Boxes Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Le Mie Consegne -->
        <x-dashboard-card 
            href="{{ route('student.deliveries.index') }}"
            title="Le mie consegne"
            description="Prenota la consegna dei libri"
            count="{{ $totalDeliveries }}"
            bgColor="yellow"
            label="CONSEGNE"
        />

        <!-- Le Mie Prenotazioni -->
        <x-dashboard-card 
            href="{{ route('student.book-reservations.index') }}"
            title="Le mie prenotazioni"
            description="Prenota un libro da comprare"
            count="{{ $totalReservations ?? 0 }}"
            bgColor="pink"
            label="PRENOTAZIONI"
        />

        <!-- I Miei Libri -->
        <x-dashboard-card 
            href="{{ route('student.book-listings.index') }}"
            title="I miei libri"
            description="Visualizza tutti i tuoi libri in vendita al mercatino"
            count="{{ $totalBookListings ?? 0 }}"
            bgColor="teal"
            label="LIBRI"
        />

        <!-- Le Mie Vendite -->
        <x-dashboard-card 
            href="{{ route('student.sales.index') }}"
            title="Le mie vendite"
            description="Visualizza tutte le vendite effettuate"
            count="{{ $totalSalesCount }}"
            bgColor="indigo"
            label="VENDITE"
        />

        <!-- I Miei Acquisti -->
        <x-dashboard-card 
            href="{{ route('student.purchases.index') }}"
            title="I miei acquisti"
            description="Visualizza tutti i libri acquistati"
            count="{{ $totalPurchases }}"
            bgColor="purple"
            label="ACQUISTI"
        />

        <!-- Le Mie Riscossioni -->
        <x-dashboard-card 
            href="{{ route('student.withdrawals.index') }}"
            title="Le mie riscossioni"
            description="Visualizza tutte le riscossioni"
            count="{{ $totalReclaims }}"
            bgColor="orange"
            label="RISCOSSIONI"
        />
    </div>
@endsection

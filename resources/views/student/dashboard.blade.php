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
            description="Gestisci le consegne prenotate"
            count="{{ $totalDeliveries }}"
            bgColor="yellow"
            label="CONSEGNE"
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
    </div>
@endsection

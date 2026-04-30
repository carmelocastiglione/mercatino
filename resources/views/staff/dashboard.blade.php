@extends('layouts.app-staff')

@section('title', 'Dashboard Staff')

@section('content')
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900">Dashboard Staff</h1>
        <p class="text-gray-600 mt-2">Panoramica della gestione delle consegne</p>
    </div>

    <!-- Main Action Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
        <!-- Prenotazioni online -->
        <x-dashboard-card 
            href="{{ route('staff.deliveries.index') }}"
            title="Prenotazioni online"
            description="Esamina e approva le prenotazioni degli studenti"
            count="{{ $pendingDeliveries }}"
            bgColor="yellow"
            label="DA ESAMINARE"
        />

        <!-- Acquisizioni -->
        <x-dashboard-card 
            href="{{ route('staff.listings.index') }}"
            title="Acquisizioni"
            description="Lista dei libri attualmente disponibili nel catalogo"
            count="{{ $availableBooks ?? 0 }}"
            bgColor="blue"
            label="CATALOGO"
        />

        <!-- Vendite -->
        <x-dashboard-card 
            href="{{ route('staff.sales.index') }}"
            title="Vendite"
            description="Libri venduti al mercatino (totale)"
            count="{{ $totalSales ?? 0 }}"
            bgColor="green"
            label="INCASSO"
        />
    </div>
@endsection

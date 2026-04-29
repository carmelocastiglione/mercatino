@extends('layouts.app-student')

@section('title', 'Dashboard Studente')

@section('content')
    <div class="mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">Dashboard</h1>
        <p class="text-gray-600">Gestisci le tue consegne di libri</p>
    </div>

    <!-- Management Boxes Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Consegne in Sospeso -->
        <x-dashboard-card 
            href="{{ route('student.deliveries.index') }}"
            title="Consegne in Sospeso"
            description="Consegne in attesa di approvazione dello staff"
            count="{{ $pendingDeliveries }}"
            bgColor="yellow"
            label="IN ATTESA"
        />

        <!-- Consegne Approvate -->
        <x-dashboard-card 
            href="{{ route('student.deliveries.index') }}"
            title="Consegne Approvate"
            description="Libri approvati e pronti per il mercatino"
            count="{{ $approvedDeliveries }}"
            bgColor="green"
            label="APPROVATE"
        />
    </div>
@endsection

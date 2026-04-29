@extends('layouts.app-staff')

@section('title', 'Dashboard Staff')

@section('content')
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900">Dashboard Staff</h1>
        <p class="text-gray-600 mt-2">Panoramica della gestione delle consegne</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
        <!-- Consegne in Sospeso -->
        <div class="bg-yellow-50 rounded-lg shadow-sm border border-yellow-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-600 text-sm font-medium">IN ATTESA DI REVISIONE</p>
                    <p class="text-gray-600 text-sm mt-1">Consegne da approvare o rifiutare</p>
                </div>
                <div class="text-5xl font-bold text-yellow-600">{{ $pendingDeliveries }}</div>
            </div>
            <a href="{{ route('staff.deliveries.index') }}" class="mt-6 inline-block text-yellow-600 hover:text-yellow-800 font-medium text-sm">
                Vai alle consegne →
            </a>
        </div>

        <!-- Consegne Approvate -->
        <div class="bg-green-50 rounded-lg shadow-sm border border-green-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-600 text-sm font-medium">APPROVATE</p>
                    <p class="text-gray-600 text-sm mt-1">Consegne aggiunte al catalogo</p>
                </div>
                <div class="text-5xl font-bold text-green-600">{{ $approvedDeliveries }}</div>
            </div>
        </div>

        <!-- Consegne Rifiutate -->
        <div class="bg-red-50 rounded-lg shadow-sm border border-red-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-600 text-sm font-medium">RIFIUTATE</p>
                    <p class="text-gray-600 text-sm mt-1">Consegne non accettate</p>
                </div>
                <div class="text-5xl font-bold text-red-600">{{ $rejectedDeliveries }}</div>
            </div>
        </div>

        <!-- Totale Processate -->
        <div class="bg-blue-50 rounded-lg shadow-sm border border-blue-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-600 text-sm font-medium">TOTALE PROCESSATE</p>
                    <p class="text-gray-600 text-sm mt-1">Approvate + Rifiutate</p>
                </div>
                <div class="text-5xl font-bold text-blue-600">{{ $totalProcessed }}</div>
            </div>
        </div>
    </div>

    <!-- Info Box -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <p class="text-sm text-blue-900">
            <strong>Come funziona:</strong> Quando approvi una consegna, il libro viene automaticamente aggiunto al catalogo con lo stesso prezzo proposto dallo studente. Puoi cambiare il prezzo successivamente dal catalogo se necessario.
        </p>
    </div>
@endsection

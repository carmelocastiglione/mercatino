@extends('layouts.app-student')

@section('title', isset($statusLabel) ? $statusLabel : 'Le Mie Consegne')

@section('content')
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-bold text-gray-900 mb-2">
                @if(isset($statusLabel))
                    {{ $statusLabel }}
                @else
                    Le Mie Consegne
                @endif
            </h1>
            <p class="text-gray-600">
                @if(isset($statusLabel))
                    Gestisci le tue consegne {{ strtolower($statusLabel) }}
                @else
                    Gestisci le consegne che hai prenotato
                @endif
            </p>
        </div>
        <a href="{{ route('student.deliveries.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-medium">
            + Prenota Consegna
        </a>
    </div>

    <!-- Summary Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
        <!-- Consegne in Attesa -->
        <a href="{{ route('student.deliveries.pending') }}" class="hover:shadow-md transition">
            <x-stats-card label="In Attesa" :value="$pendingDeliveries" color="yellow" />
        </a>

        <!-- Consegne Valutate -->
        <a href="{{ route('student.deliveries.submitted') }}" class="hover:shadow-md transition">
            <x-stats-card label="Valutate" :value="$submittedDeliveries" color="green" />
        </a>
    </div>

    @if(isset($statusFilter))
        <div class="mb-6">
            <a href="{{ route('student.deliveries.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                ← Torna a tutte le consegne
            </a>
        </div>
    @endif

    @if($batches->count() > 0)
        <div class="space-y-6">
            @foreach($batches as $batch)
                <x-delivery-batch-card :batch="$batch" />
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $batches->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="text-gray-600 text-lg mb-6">
                @if(isset($statusFilter))
                    @switch($statusFilter)
                        @case('pending')
                            Nessuna consegna in attesa
                        @break
                        @case('submitted')
                            Nessuna consegna valutata
                        @break
                        @default
                            Nessuna consegna trovata
                    @endswitch
                @else
                    Non hai ancora prenotato nessuna consegna
                @endif
            </p>
            @if(!isset($statusFilter))
                <a href="{{ route('student.deliveries.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-medium inline-block">
                    Prenota la tua prima consegna
                </a>
            @else
                <a href="{{ route('student.deliveries.index') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-medium inline-block">
                    Torna a tutte le consegne
                </a>
            @endif
        </div>
    @endif
@endsection

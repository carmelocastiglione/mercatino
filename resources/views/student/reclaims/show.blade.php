@extends('layouts.app-student')

@section('title', 'Dettagli Riscossione')

@section('content')
    <div class="mb-8">
        <a href="{{ route('student.reclaims.index') }}" class="text-orange-600 hover:text-orange-900 font-semibold mb-4 inline-block">
            ← Torna alle riscossioni
        </a>
        <h1 class="text-4xl font-bold text-gray-900 mb-2">Dettagli Riscossione</h1>
    </div>

    <!-- Reclaim Details Card -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Book Info -->
        <div class="bg-white rounded-xl shadow-md p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Informazioni Libro</h2>
            
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-600 font-semibold">Titolo</p>
                    <p class="text-gray-900 text-lg">{{ $reclaim->bookListing->book->title }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-600 font-semibold">Autore</p>
                    <p class="text-gray-900">{{ $reclaim->bookListing->book->author }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-600 font-semibold">ISBN</p>
                    <p class="text-gray-900">{{ $reclaim->bookListing->book->isbn ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-600 font-semibold">Condizione</p>
                    <p class="text-gray-900">{{ ucfirst($reclaim->bookListing->condition) }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-600 font-semibold">Materia</p>
                    <p class="text-gray-900">{{ $reclaim->bookListing->book->subject ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Reclaim Info -->
        <div class="bg-white rounded-xl shadow-md p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Informazioni Riscossione</h2>
            
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-600 font-semibold">Status</p>
                    <span class="px-3 py-1 rounded-full text-sm font-medium mt-2 inline-block
                        @switch($reclaim->status)
                            @case('pending') bg-yellow-100 text-yellow-800 @break
                            @case('approved') bg-green-100 text-green-800 @break
                            @case('rejected') bg-red-100 text-red-800 @break
                        @endswitch
                    ">
                        @switch($reclaim->status)
                            @case('pending') In Sospeso @break
                            @case('approved') Approvata @break
                            @case('rejected') Rifiutata @break
                        @endswitch
                    </span>
                </div>

                <div class="pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-600 font-semibold">Data Riscossione</p>
                    <p class="text-gray-900">{{ $reclaim->created_at->format('d/m/Y H:i') }}</p>
                </div>

                @if($reclaim->rejection_reason)
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <p class="text-sm text-gray-600 font-semibold mb-2">Motivo Rifiuto</p>
                        <p class="text-red-700">{{ $reclaim->rejection_reason }}</p>
                    </div>
                @endif

                @if($reclaim->notes)
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <p class="text-sm text-gray-600 font-semibold mb-2">Note</p>
                        <p class="text-gray-700">{{ $reclaim->notes }}</p>
                    </div>
                @endif

                <div>
                    <p class="text-sm text-gray-600 font-semibold">ID Riscossione</p>
                    <p class="text-sm text-gray-500 font-mono">{{ $reclaim->id }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="mt-8">
        <a href="{{ route('student.reclaims.index') }}" class="bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
            Torna alle riscossioni
        </a>
    </div>
@endsection

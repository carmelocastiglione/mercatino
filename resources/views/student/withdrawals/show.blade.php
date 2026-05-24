@extends('layouts.app-student')

@section('title', 'Dettagli Ritiro')

@section('content')
    <div class="mb-8">
        <a href="{{ route('student.withdrawals.index') }}" class="text-orange-600 hover:text-orange-900 font-semibold mb-4 inline-block">
            ← Torna alle riscossioni
        </a>
        <h1 class="text-4xl font-bold text-gray-900 mb-2">Dettagli Ritiro</h1>
    </div>

    <!-- Withdrawal Details Card -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Book Info -->
        <div class="bg-white rounded-xl shadow-md p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Informazioni Libro</h2>
            
            <div class="space-y-4">
                @if($withdrawal->bookListing && $withdrawal->bookListing->book)
                    <div>
                        <p class="text-sm text-gray-600 font-semibold">Titolo</p>
                        <p class="text-gray-900 text-lg">{{ $withdrawal->bookListing->book->title }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600 font-semibold">Autore</p>
                        <p class="text-gray-900">{{ $withdrawal->bookListing->book->author }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600 font-semibold">ISBN</p>
                        <p class="text-gray-900">{{ $withdrawal->bookListing->book->isbn ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600 font-semibold">Condizione</p>
                        <p class="text-gray-900">{{ ucfirst($withdrawal->bookListing->condition) }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600 font-semibold">Materia</p>
                        <p class="text-gray-900">{{ $withdrawal->bookListing->book->subject ?? '-' }}</p>
                    </div>
                @else
                    <p class="text-gray-500">Nessun libro associato</p>
                @endif
            </div>
        </div>

        <!-- Withdrawal Info -->
        <div class="bg-white rounded-xl shadow-md p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Informazioni Ritiro</h2>
            
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-600 font-semibold">Importo</p>
                    <p class="text-3xl font-bold text-orange-600 mt-2">€ {{ number_format($withdrawal->amount, 2, ',', '.') }}</p>
                </div>

                <div class="pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-600 font-semibold">Data Ritiro</p>
                    <p class="text-gray-900">{{ $withdrawal->created_at->format('d/m/Y H:i') }}</p>
                </div>

                @if($withdrawal->notes)
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <p class="text-sm text-gray-600 font-semibold mb-2">Note</p>
                        <p class="text-gray-700">{{ $withdrawal->notes }}</p>
                    </div>
                @endif

                <div>
                    <p class="text-sm text-gray-600 font-semibold">ID Ritiro</p>
                    <p class="text-sm text-gray-500 font-mono">{{ $withdrawal->id }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="mt-8">
        <a href="{{ route('student.withdrawals.index') }}" class="bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
            Torna alle riscossioni
        </a>
    </div>
@endsection

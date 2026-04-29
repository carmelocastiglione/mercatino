@extends('layouts.app-staff')

@section('title', 'Rivedi Consegna')

@section('content')
    <div class="mb-8">
        <a href="{{ route('staff.deliveries.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium mb-4 inline-block">
            ← Torna alle consegne
        </a>
        <h1 class="text-4xl font-bold text-gray-900">Rivedi Consegna</h1>
        <p class="text-gray-600 mt-2">Esamina i dettagli e decidi se approvare o rifiutare</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Dettagli Consegna -->
        <div class="md:col-span-2 space-y-6">
            <!-- Info Studente -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informazioni Studente</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Nome</p>
                        <p class="font-medium text-gray-900">{{ $delivery->user->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Email</p>
                        <p class="font-medium text-gray-900">{{ $delivery->user->email }}</p>
                    </div>
                    @if($delivery->user->school)
                        <div>
                            <p class="text-sm text-gray-600">Scuola</p>
                            <p class="font-medium text-gray-900">{{ $delivery->user->school->name }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Info Libro -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informazioni Libro</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Titolo</p>
                        <p class="font-medium text-gray-900">{{ $delivery->book->title }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Autore</p>
                        <p class="font-medium text-gray-900">{{ $delivery->book->author ?? 'Non disponibile' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">ISBN</p>
                        <p class="font-medium text-gray-900">{{ $delivery->book->isbn ?? 'Non disponibile' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Materia</p>
                        <p class="font-medium text-gray-900">{{ $delivery->book->subject ?? 'Non disponibile' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Classe</p>
                        <p class="font-medium text-gray-900">{{ $delivery->book->school_class ?? 'Non disponibile' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Prezzo Originale</p>
                        <p class="font-medium text-gray-900">€ {{ number_format($delivery->book->original_price, 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Info Consegna -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Dettagli Consegna</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Condizioni del Libro</p>
                        <p class="mt-1">
                            <span class="px-3 py-1 rounded-full text-sm font-medium
                                @switch($delivery->condition)
                                    @case('like-new') bg-green-100 text-green-800 @break
                                    @case('good') bg-blue-100 text-blue-800 @break
                                    @case('fair') bg-yellow-100 text-yellow-800 @break
                                    @case('poor') bg-red-100 text-red-800 @break
                                @endswitch
                            ">
                                {{ ucfirst(str_replace('-', ' ', $delivery->condition)) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Prezzo Proposto</p>
                        <p class="font-medium text-gray-900 text-lg">€ {{ number_format($delivery->price, 0) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Data Prenotazione</p>
                        <p class="font-medium text-gray-900">{{ $delivery->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Azioni -->
        <div class="md:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-4 sticky top-8">
                <h2 class="text-lg font-semibold text-gray-900">Azioni</h2>

                <!-- Approva -->
                <form action="{{ route('staff.deliveries.approve', $delivery) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="w-full bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition font-medium">
                        ✓ Approva Consegna
                    </button>
                </form>

                <!-- Rifiuta -->
                <a href="{{ route('staff.deliveries.reject-form', $delivery) }}" class="block w-full bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition font-medium text-center">
                    ✗ Rifiuta Consegna
                </a>

                <!-- Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-6">
                    <p class="text-xs text-blue-900">
                        <strong>Nota:</strong> Se approvi, il libro sarà aggiunto automaticamente al catalogo con il prezzo proposto.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

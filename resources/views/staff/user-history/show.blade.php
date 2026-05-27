@extends('layouts.app-staff')

@section('title', 'Storico - ' . $user->name . ' ' . $user->surname)

@section('content')
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-bold text-gray-900">Storico di {{ $user->name }} {{ $user->surname }}</h1>
            <div class="flex items-center gap-4 mt-3">
                <p class="text-sm text-gray-600"><strong>Codice:</strong> {{ $user->code }}</p>
                <p class="text-sm text-gray-600"><strong>Email:</strong> {{ $user->email }}</p>
            </div>
        </div>
        <a href="{{ route('staff.user-history.index') }}" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition font-medium">
            ← Nuova ricerca
        </a>
    </div>

    @if (count($movements) === 0)
        <div class="bg-gray-50 border-2 border-gray-300 rounded-lg p-12 text-center">
            <p class="text-gray-600 font-medium">Nessun movimento trovato</p>
            <p class="text-sm text-gray-500 mt-1">Questo utente non ha ancora effettuato operazioni</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($movements as $movement)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
                    <div class="flex items-start gap-4">
                        <!-- Icon -->
                        <div class="text-3xl mt-1">
                            {{ $movement['icon'] }}
                        </div>

                        <!-- Content -->
                        <div class="flex-1">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <h3 class="font-semibold text-gray-900 text-lg">{{ $movement['title'] }}</h3>
                                    <p class="text-gray-600 mt-1">{{ $movement['description'] }}</p>
                                </div>
                                <p class="text-sm text-gray-500 whitespace-nowrap">
                                    {{ $movement['date']->format('d/m/Y H:i') }}
                                </p>
                            </div>

                            <!-- Type-specific details -->
                            @if ($movement['type'] === 'delivery_batch')
                                <div class="mt-3 pt-3 border-t border-gray-100 text-sm text-gray-600">
                                    <p>ID Batch: <strong>{{ $movement['data']->id }}</strong></p>
                                    @if ($movement['data']->scheduled_delivery_date_id)
                                        <p>Data consegna: <strong>{{ $movement['data']->scheduledDeliveryDate?->scheduled_date->format('d/m/Y') ?? 'N/A' }}</strong></p>
                                    @endif
                                </div>
                            @elseif ($movement['type'] === 'acquisition')
                                <div class="mt-3 pt-3 border-t border-gray-100 text-sm text-gray-600">
                                    <p>Totale acquisito: <strong>€{{ number_format($movement['data']->total_price ?? 0, 2) }}</strong></p>
                                    <p>Numero libri: <strong>{{ $movement['data']->bookListings->count() }}</strong></p>
                                    @if ($movement['data']->status)
                                        <p>Stato: <strong class="capitalize">{{ $movement['data']->status }}</strong></p>
                                    @endif
                                </div>
                            @elseif ($movement['type'] === 'purchase')
                                <div class="mt-3 pt-3 border-t border-gray-100 text-sm text-gray-600">
                                    <p>Prezzo pagato: <strong>€{{ number_format($movement['data']->bookListing->price ?? 0, 2) }}</strong></p>
                                </div>
                            @elseif ($movement['type'] === 'sale')
                                <div class="mt-3 pt-3 border-t border-gray-100 text-sm text-gray-600">
                                    <p>Prezzo vendita: <strong>€{{ number_format($movement['data']->bookListing->price ?? 0, 2) }}</strong></p>
                                    <p>Acquirente: <strong>{{ $movement['data']->buyer->name ?? 'N/A' }} {{ $movement['data']->buyer->surname ?? '' }}</strong></p>
                                </div>
                            @elseif ($movement['type'] === 'withdrawal')
                                <div class="mt-3 pt-3 border-t border-gray-100 text-sm text-gray-600">
                                    <p>Metodo: <strong>{{ $movement['data']->withdrawal_method === 'cash' ? 'Contante' : 'Bonifico' }}</strong></p>
                                    @if ($movement['data']->withdrawal_method === 'bank_transfer' && $movement['data']->bank_account)
                                        <p>Conto: <strong>{{ $movement['data']->bank_account }}</strong></p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection

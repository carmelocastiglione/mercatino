@extends('layouts.app-staff')

@section('title', 'Prenotazioni Libri')

@section('content')
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900">Prenotazioni Libri</h1>
        <p class="text-gray-600 mt-2">Gestisci le prenotazioni degli studenti dai libri acquisiti</p>
    </div>

    <!-- Filter by Status -->
    <div class="mb-6 border-b border-gray-200">
        <div class="flex space-x-8">
            <a href="{{ route('staff.book-reservations.index') }}" class="px-4 py-2 border-b-2 border-blue-600 text-blue-600 font-semibold">
                Tutte
            </a>
        </div>
    </div>

    <!-- Reservations List -->
    @if ($batches->isEmpty())
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Nessuna prenotazione</h3>
            <p class="text-gray-600">Non ci sono prenotazioni da gestire al momento.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($batches as $batch)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">
                                Prenotazione #{{ $batch->id }}
                            </h3>
                            <p class="text-sm text-gray-600">
                                Studente: <strong>{{ $batch->user->name }} {{ $batch->user->surname }}</strong> ({{ $batch->user->code }})
                            </p>
                            <p class="text-sm text-gray-600">
                                {{ $batch->created_at->format('d/m/Y \a\l\l\e H:i') }}
                            </p>
                        </div>
                        <div class="flex items-center space-x-3">
                            @if ($batch->isPending())
                                <span class="inline-block bg-yellow-100 text-yellow-800 text-xs font-semibold px-3 py-1 rounded-full">
                                    In Sospeso
                                </span>
                            @elseif ($batch->isConfirmed())
                                <span class="inline-block bg-green-100 text-green-800 text-xs font-semibold px-3 py-1 rounded-full">
                                    Confermata
                                </span>
                            @elseif ($batch->isRejected())
                                <span class="inline-block bg-red-100 text-red-800 text-xs font-semibold px-3 py-1 rounded-full">
                                    Rifiutata
                                </span>
                            @elseif ($batch->isCancelled())
                                <span class="inline-block bg-gray-100 text-gray-800 text-xs font-semibold px-3 py-1 rounded-full">
                                    Cancellata
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Books Summary -->
                    <div class="mb-4">
                        <div class="text-sm text-gray-600 mb-2">{{ $batch->total_items }} {{ $batch->total_items === 1 ? 'libro' : 'libri' }}</div>
                        <div class="space-y-1">
                            @foreach ($batch->bookReservations->take(3) as $reservation)
                                <div class="text-sm">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-900">
                                            {{ $reservation->bookListing->book->title }}
                                        </span>
                                        <span class="font-semibold text-gray-900">€{{ $reservation->bookListing->price }}</span>
                                    </div>
                                    <div class="text-xs text-gray-600">
                                        Condizione: 
                                        <span class="font-medium">
                                            @switch($reservation->bookListing->condition)
                                                @case('like-new')
                                                    Come Nuovo
                                                @break
                                                @case('good')
                                                    Buona
                                                @break
                                                @case('fair')
                                                    Discreta
                                                @break
                                                @case('poor')
                                                    Scarsa
                                                @break
                                            @endswitch
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                            @if ($batch->bookReservations->count() > 3)
                                <div class="text-sm text-gray-600 mt-2">
                                    ... e {{ $batch->bookReservations->count() - 3 }} altri
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Totale -->
                    <div class="flex justify-between items-center pt-4 border-t border-gray-200 mb-4">
                        <span class="font-bold text-gray-900">Totale:</span>
                        <span class="text-2xl font-bold text-blue-600">€{{ $batch->getTotalPrice() }}</span>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-between items-center">
                        <a href="{{ route('staff.book-reservations.show', $batch) }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                            Visualizza Dettagli
                        </a>
                        @if ($batch->isPending())
                            <div class="flex space-x-3">
                                <form method="POST" action="{{ route('staff.book-reservations.confirm', $batch) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-800 font-medium text-sm">
                                        ✓ Conferma
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('staff.book-reservations.reject', $batch) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-sm">
                                        ✕ Rifiuta
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $batches->links() }}
        </div>
    @endif
@endsection

@extends('layouts.app-student')

@section('title', 'Le Mie Prenotazioni')

@section('content')
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Le Mie Prenotazioni</h1>
            <p class="text-gray-600">Gestisci i libri che hai prenotato dal mercatino</p>
        </div>
        <a href="{{ route('student.book-reservations.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-medium">
            + Prenota Libro
        </a>
    </div>

    <!-- Summary Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <!-- Prenotazioni in Attesa -->
        <a href="{{ route('student.book-reservations.pending') }}" class="hover:shadow-md transition">
            <x-stats-card label="In Attesa" :value="$pendingCount" color="yellow" />
        </a>

        <!-- Prenotazioni Valutate -->
        <a href="{{ route('student.book-reservations.confirmed') }}" class="hover:shadow-md transition">
            <x-stats-card label="Valutate" :value="$confirmedCount" color="green" />
        </a>

        <!-- Prenotazioni Cancellate -->
        <a href="{{ route('student.book-reservations.cancelled') }}" class="hover:shadow-md transition">
            <x-stats-card label="Cancellate" :value="$cancelledCount" color="red" />
        </a>
    </div>

    @if(isset($statusFilter))
        <div class="mb-6">
            <a href="{{ route('student.book-reservations.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                ← Torna a tutte le prenotazioni
            </a>
        </div>
    @endif

    <!-- Reservations List -->
    @if ($batches->isEmpty())
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="text-gray-600 text-lg mb-6">
                @if(isset($statusFilter))
                    @switch($statusFilter)
                        @case('pending')
                            Nessuna prenotazione in attesa
                        @break
                        @case('confirmed')
                            Nessuna prenotazione valutata
                        @break
                        @case('cancelled')
                            Nessuna prenotazione cancellata
                        @break
                        @default
                            Nessuna prenotazione trovata
                    @endswitch
                @else
                    Non hai ancora prenotato nessun libro dal mercatino
                @endif
            </p>
            @if(!isset($statusFilter))
                <a href="{{ route('student.book-reservations.create') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                    Inizia una prenotazione →
                </a>
            @else
                <a href="{{ route('student.book-reservations.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                    Torna a tutte le prenotazioni →
                </a>
            @endif
        </div>
    @else
        <!-- Info Box -->
        <x-info-box 
            type="info"
            title="Stampa e porta la ricevuta al mercatino"
            message="Ricordati di stampare la ricevuta della tua prenotazione e portala al mercatino il giorno stabilito. Puoi ristampare la ricevuta in qualsiasi momento tramite il pulsante Visualizza."
        />

        <div class="space-y-6">
            @foreach ($batches as $batch)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <!-- Batch Header -->
                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 border-b-2 border-blue-300 px-6 py-4">
                        <div class="grid grid-cols-6 gap-4 items-center">
                            <!-- Transaction Code -->
                            <div>
                                <p class="text-xs text-gray-600 font-semibold uppercase">Codice Prenotazione</p>
                                <p class="font-mono text-lg font-bold text-blue-600">{{ $batch->ean13 }}</p>
                            </div>

                            <!-- Number of Books -->
                            <div>
                                <p class="text-xs text-gray-600 font-semibold uppercase">Libri</p>
                                <p class="text-lg font-bold text-gray-900">{{ $batch->bookReservations->count() }}</p>
                            </div>

                            <!-- Total Price -->
                            <div>
                                <p class="text-xs text-gray-600 font-semibold uppercase">Totale</p>
                                <p class="text-lg font-bold text-gray-900">€{{ number_format($batch->total_price, 2) }}</p>
                            </div>

                            <!-- Request Date -->
                            <div>
                                <p class="text-xs text-gray-600 font-semibold uppercase">Richiesta</p>
                                <p class="text-sm font-medium text-gray-900">{{ $batch->created_at->format('d/m/Y H:i') }}</p>
                            </div>

                            <!-- Status -->
                            <div>
                                <p class="text-xs text-gray-600 font-semibold uppercase">Stato</p>
                                <div class="mt-1">
                                    @if ($batch->isPending())
                                        <span class="inline-block bg-yellow-100 text-yellow-800 text-xs font-semibold px-3 py-1 rounded-full">
                                            In Attesa
                                        </span>
                                    @elseif ($batch->isConfirmed())
                                        <span class="inline-block bg-green-100 text-green-800 text-xs font-semibold px-3 py-1 rounded-full">
                                            Valutata
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

                            <!-- Actions -->
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('student.book-reservations.show', $batch) }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                                    Visualizza
                                </a>
                                @if ($batch->isPending())
                                    <form method="POST" action="{{ route('student.book-reservations.destroy', $batch) }}" style="display: inline;" onsubmit="return confirm('Annullare questa prenotazione?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition">
                                            Annulla
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Books List -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">#</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Titolo</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">ISBN</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Condizioni</th>
                                    <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900">Prezzo</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($batch->bookReservations as $index => $reservation)
                                    <tr class="hover:bg-gray-50 transition border-b border-gray-200">
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4">
                                            <p class="font-medium text-gray-900">{{ $reservation->bookListing->book->title }}</p>
                                        </td>
                                        <td class="px-6 py-4 text-sm font-mono text-gray-600">
                                            {{ $reservation->bookListing->book->isbn ?? '—' }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-3 py-1 rounded-full text-sm font-medium
                                                @switch($reservation->bookListing->condition)
                                                    @case('like-new') bg-green-100 text-green-800 @break
                                                    @case('good') bg-blue-100 text-blue-800 @break
                                                    @case('fair') bg-yellow-100 text-yellow-800 @break
                                                    @case('poor') bg-red-100 text-red-800 @break
                                                @endswitch
                                            ">
                                                @switch($reservation->bookListing->condition)
                                                    @case('like-new') Come Nuovo @break
                                                    @case('good') Buona @break
                                                    @case('fair') Discreta @break
                                                    @case('poor') Scarsa @break
                                                @endswitch
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 font-medium text-gray-900 text-right">€{{ number_format($reservation->bookListing->price_sell ?? $reservation->bookListing->price, 2) }}</td>
                                        <td class="px-6 py-4">
                                            <span class="px-3 py-1 rounded-full text-sm font-medium
                                                @switch($reservation->status)
                                                    @case('pending') bg-yellow-100 text-yellow-800 @break
                                                    @case('confirmed') bg-green-100 text-green-800 @break
                                                    @case('rejected') bg-red-100 text-red-800 @break
                                                    @case('cancelled') bg-gray-100 text-gray-800 @break
                                                @endswitch
                                            ">
                                                @switch($reservation->status)
                                                    @case('pending') In Attesa @break
                                                    @case('confirmed') Confermato @break
                                                    @case('rejected') Rifiutato @break
                                                    @case('cancelled') Cancellato @break
                                                @endswitch
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">Nessun libro</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $batches->links() }}
        </div>
    @endif
@endsection

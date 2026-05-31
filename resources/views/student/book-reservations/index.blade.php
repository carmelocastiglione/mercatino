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
        <!-- Prenotazioni in Sospeso -->
        <div class="hover:shadow-md transition">
            <x-stats-card label="In Sospeso" :value="$batches->where('status', 'pending')->count()" color="yellow" />
        </div>

        <!-- Prenotazioni Confermate -->
        <div class="hover:shadow-md transition">
            <x-stats-card label="Confermate" :value="$batches->where('status', 'confirmed')->count()" color="green" />
        </div>

        <!-- Prenotazioni Rifiutate/Cancellate -->
        <div class="hover:shadow-md transition">
            <x-stats-card label="Rifiutate" :value="$batches->where('status', 'rejected')->count() + $batches->where('status', 'cancelled')->count()" color="red" />
        </div>
    </div>

    <!-- Reservations List -->
    @if ($batches->isEmpty())
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Nessuna prenotazione</h3>
            <p class="text-gray-600 mb-6">Non hai ancora prenotato nessun libro dal mercatino.</p>
            <a href="{{ route('student.book-reservations.create') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                Inizia una prenotazione →
            </a>
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
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-900">
                                        {{ $reservation->bookListing->book->title }}
                                    </span>
                                    <span class="font-semibold text-gray-900">€{{ $reservation->bookListing->price }}</span>
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
                        <a href="{{ route('student.book-reservations.show', $batch) }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                            Visualizza Dettagli
                        </a>
                        @if ($batch->isPending())
                            <form method="POST" action="{{ route('student.book-reservations.destroy', $batch) }}" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-sm" onclick="return confirm('Annullare questa prenotazione?')">
                                    Annulla Prenotazione
                                </button>
                            </form>
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

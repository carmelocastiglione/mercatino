@extends('layouts.app-staff')

@section('title', 'Prenotazione #' . $batch->id)

@section('content')
    <div class="mb-8">
        <a href="{{ route('staff.book-reservations.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium mb-4 inline-block">
            ← Torna all'elenco
        </a>
        <h1 class="text-4xl font-bold text-gray-900">Prenotazione #{{ $batch->id }}</h1>
        <p class="text-gray-600 mt-2">{{ $batch->created_at->format('d/m/Y \a\l\l\e H:i') }}</p>
    </div>

    <!-- Student Info & Status -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <div class="lg:col-span-2">
            <!-- Student Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Informazioni Studente</h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Nome:</span>
                        <span class="font-semibold">{{ $batch->user->name }} {{ $batch->user->surname }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Codice:</span>
                        <span class="font-semibold">{{ $batch->user->code }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Email:</span>
                        <span class="font-semibold">{{ $batch->user->email }}</span>
                    </div>
                </div>
            </div>

            <!-- Books List -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Libri Prenotati</h2>
                <div class="space-y-4">
                    @foreach ($batch->bookReservations as $reservation)
                        <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-gray-900">
                                        {{ $reservation->bookListing->book->title }}
                                    </h3>
                                    <p class="text-gray-600 mt-1">
                                        Autore: <strong>{{ $reservation->bookListing->book->author }}</strong>
                                    </p>
                                    <p class="text-gray-600 text-sm mt-1">
                                        ISBN: {{ $reservation->bookListing->book->isbn }}
                                    </p>
                                    <p class="text-gray-600 text-sm mt-1">
                                        Condizione: <span class="font-semibold">
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
                                    </p>
                                </div>
                                <div class="text-right">
                                    <div class="text-3xl font-bold text-blue-600">
                                        €{{ $reservation->bookListing->price }}
                                    </div>
                                    @if ($reservation->isConfirmed())
                                        <span class="inline-block bg-green-100 text-green-800 text-xs font-semibold px-2 py-1 rounded mt-2">
                                            Confermato
                                        </span>
                                    @elseif ($reservation->isPending())
                                        <span class="inline-block bg-yellow-100 text-yellow-800 text-xs font-semibold px-2 py-1 rounded mt-2">
                                            In Sospeso
                                        </span>
                                    @elseif ($reservation->isRejected())
                                        <span class="inline-block bg-red-100 text-red-800 text-xs font-semibold px-2 py-1 rounded mt-2">
                                            Rifiutato
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div>
            <!-- Status Card -->
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-6 border border-blue-200 sticky top-4">
                <h3 class="font-bold text-lg text-gray-900 mb-4">Stato</h3>
                
                <!-- Status Badge -->
                <div class="mb-6">
                    @if ($batch->isPending())
                        <div class="inline-block bg-yellow-100 text-yellow-800 text-sm font-semibold px-3 py-1 rounded-full">
                            ⏳ In Sospeso
                        </div>
                    @elseif ($batch->isConfirmed())
                        <div class="inline-block bg-green-100 text-green-800 text-sm font-semibold px-3 py-1 rounded-full">
                            ✓ Confermata
                        </div>
                    @elseif ($batch->isRejected())
                        <div class="inline-block bg-red-100 text-red-800 text-sm font-semibold px-3 py-1 rounded-full">
                            ✕ Rifiutata
                        </div>
                    @elseif ($batch->isCancelled())
                        <div class="inline-block bg-gray-100 text-gray-800 text-sm font-semibold px-3 py-1 rounded-full">
                            ✕ Cancellata
                        </div>
                    @endif
                </div>

                <!-- Summary -->
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Numero libri:</span>
                        <span class="font-bold text-gray-900">{{ $batch->total_items }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center pt-3 border-t border-blue-200">
                        <span class="text-gray-600">Prezzo totale:</span>
                        <span class="font-bold text-2xl text-blue-600">€{{ $batch->getTotalPrice() }}</span>
                    </div>
                </div>

                <!-- Action Buttons -->
                @if ($batch->isPending())
                    <div class="space-y-3">
                        <form method="POST" action="{{ route('staff.book-reservations.confirm', $batch) }}">
                            @csrf
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 rounded-lg transition">
                                ✓ Conferma Prenotazione
                            </button>
                        </form>
                        <form method="POST" action="{{ route('staff.book-reservations.reject', $batch) }}">
                            @csrf
                            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 rounded-lg transition">
                                ✕ Rifiuta Prenotazione
                            </button>
                        </form>
                    </div>
                @endif

                <!-- Timeline -->
                <div class="border-t border-blue-200 pt-6 mt-6">
                    <h4 class="text-sm font-bold text-gray-900 mb-3">Cronologia</h4>
                    <div class="space-y-3 text-sm">
                        <div>
                            <div class="font-semibold text-gray-900">Prenotata il:</div>
                            <div class="text-gray-600">{{ $batch->reserved_at ? $batch->reserved_at->format('d/m/Y H:i') : 'N/A' }}</div>
                        </div>
                        @if ($batch->confirmed_at)
                            <div>
                                <div class="font-semibold text-green-900">Confermata il:</div>
                                <div class="text-green-700">{{ $batch->confirmed_at->format('d/m/Y H:i') }}</div>
                            </div>
                        @endif
                        @if ($batch->rejected_at)
                            <div>
                                <div class="font-semibold text-red-900">Rifiutata il:</div>
                                <div class="text-red-700">{{ $batch->rejected_at->format('d/m/Y H:i') }}</div>
                            </div>
                        @endif
                        @if ($batch->cancelled_at)
                            <div>
                                <div class="font-semibold text-gray-900">Cancellata il:</div>
                                <div class="text-gray-600">{{ $batch->cancelled_at->format('d/m/Y H:i') }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notes Section -->
    @if ($batch->notes)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-3">Note</h3>
            <p class="text-gray-600">{{ $batch->notes }}</p>
        </div>
    @endif
@endsection
